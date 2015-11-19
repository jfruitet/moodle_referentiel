<?php

    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    //This functions are used to copy any file or directory ($from_file)
    //to a new file or directory ($to_file). It works recursively and
    //mantains file perms.
    // copied from moodle/backup/lib.php
    //I've copied it from: http://www.php.net/manual/en/function.copy.php
    //Little modifications done

    function referentiel_copy_file ($from_file, $to_file) {
    // Moodle 1.9 file API
        global $CFG;

        if (is_file($from_file)) {
            //Debug
            // echo "<br />DEBUG :: lib_archive.php :: 19 :: Copying ".$from_file." to ".$to_file;
            $perms=fileperms($from_file);
            return copy($from_file,$to_file) && chmod($to_file,$perms);
            umask(0000);
            if (copy($from_file,$to_file)) {
                chmod($to_file,$CFG->directorypermissions);
                return true;
            }
            return false;
        }
        else if (is_dir($from_file)) {
            return referentiel_copy_dir($from_file,$to_file);
        }
        else{
            echo "<br />Error: not file or dir ".$from_file;               //Debug
            return false;
        }
    }

    function referentiel_copy_dir($from_file, $to_file) {

        global $CFG;

        $status = true; // Initialize this, next code will change its value if needed

        if (!is_dir($to_file)) {
            //echo "<br />Creating ".$to_file;                                //Debug
            umask(0000);
            $status = mkdir($to_file,$CFG->directorypermissions);
        }
        $dir = opendir($from_file);
        while (false !== ($file=readdir($dir))) {
            if ($file=="." || $file=="..") {
                continue;
            }
            $status = referentiel_copy_file ("$from_file/$file","$to_file/$file");
        }
        closedir($dir);
        return $status;
    }
    ///Ends copy file/dirs functions
    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    
/**
 * Function to check if a directory exists and optionally create it.
 *
 * @param string absolute directory path (must be under $CFG->dataroot)
 * @param boolean create directory if does not exist
 * @param boolean create directory recursively
 *
 * @return boolean true if directory exists or created
 */

    //Function to check and create the "documents_files" dir to
    //save all the user files we need from "documents"
    function referentiel_check_and_create_document_files_dir($referentiel_referentiel_id, $user_creator, $userid) {

        global $CFG;

        // Moodle 2.0
        // $status = check_dir_exists($CFG->dataroot."/temp/archive/".$referentiel_referentiel_id."/".$user_creator."/document_files/".$userid,true, true);
        // Moodle 22
        $path_temp =  cleardoubleslashes(get_string('archivetemp', 'referentiel').'/'.$referentiel_referentiel_id.'/'.$user_creator);
        // Moodle 2.2
        $temp_dir=make_temp_directory($path_temp);
        $status = check_dir_exists($temp_dir."/document_files/".$userid,true, true);

        return $status;
    }

    //This function copies all the needed files under the "user" directory to the "user_files"
    //directory under temp/archive
    function referentiel_copy_document_file_moodle_1_9_api ($referentiel_referentiel_id, $user_creator, $userid, $file_uri) {
    // $file_uri= 2/moddata/referentiel/1/3/arrete-C2i2eVDef.pdf
        global $CFG;

        $status = true;

        //First we check that "user_files" exists and create it if necessary
        //in temp/archive/$backup_code  dir
        if (referentiel_check_and_create_document_files_dir($referentiel_referentiel_id, $user_creator, $userid)){
            //first remove dirroot so we can split out the folders.

            $parts = explode('/', $file_uri);
            $status = false;
            if (is_array($parts)) {
                    $course_id = $parts[0];
                    $instance_id = $parts[3];
                    $user_id = $parts[4];
                    $file_name=$parts[5];
            }
            if (!empty($file_name)){
                // DEBUG
                // echo "<br />DEBUG :: lib_archive :: 107 :: referentiel_copy_document_file<br />\n";
                //echo "<br />URL : $file_uri\n";
                //print_r($parts);
                // URL :
                $file_url= $CFG->dataroot.'/'.$file_uri;
                // echo "<br />URI: $file_url\n";
                // echo "<br />FILENAME: $file_name\n";
                $file_path_name="document_files/".$userid."/".$file_name;
                $status = referentiel_copy_file($file_url, $CFG->dataroot."/temp/archive/".$referentiel_referentiel_id."/".$user_creator."/".$file_path_name);
            }
        }
        if ($status){
            return $file_path_name;
        }
        else{
            return '';
        }
    }
    

    //This function creates the zip file containing all the backup info
    //moodle.xml, moddata, user_files, course_files.
    //The zipped file is created in the backup directory and named with
    //the "oficial" name of the backup
    //It uses "pclzip" if available or system "zip" (unix only)
    function referentiel_backup_zip ($referentiel_referentiel_id, $user_creator, $archive_name) {

        global $CFG;

        $status = true;
        // DEBUG
        //Base dir where everything happens
        // Moodle 2
        // $basedir = cleardoubleslashes($CFG->dataroot."/temp/archive/".$referentiel_referentiel_id.'/'.$user_creator);
        // Moodle 22
        $path_temp =  cleardoubleslashes(get_string('archivetemp', 'referentiel').'/'.$referentiel_referentiel_id.'/'.$user_creator);
        // Moodle 2.2
        $basedir=make_temp_directory($path_temp);
        $name = $archive_name;
        // DEBUG
        // echo "<br />DEBUG :: lib_archive.php :: 160 :: BASEDIR : '$basedir' NAME : '$name' <br />\n";

        //List of files and directories
        $filelist = referentiel_list_directories_and_files ($basedir);
        // print_object($filelist);
        // exit;

        //Convert them to full paths
        $files = array();
        
        foreach ($filelist as $file) {
           $files[] = "$basedir/$file";
        }

        $status = zip_files($files, "$basedir/$name");

        //echo "<br/>Status: ".$status;                                     //Debug
        return $status;

    }

    //This function return the names of all directories under a give directory
    //Not recursive
    function referentiel_list_directories ($rootdir) {

        $results = null;

        if ($dir = opendir($rootdir)){
            while (false !== ($file=readdir($dir))) {
                if ($file=="." || $file=="..") {
                    continue;
                }
                if (is_dir($rootdir."/".$file)) {
                    $results[$file] = $file;
                }
            }
            closedir($dir);
        }
        return $results;
    }

    //This function return the names of all directories and files under a give directory
    //Not recursive
    function referentiel_list_directories_and_files ($rootdir) {

        $results = null;

        if ($dir = opendir($rootdir)){
            while (false !== ($file=readdir($dir))) {
                if ($file=="." || $file=="..") {
                    continue;
                }
                $results[$file] = $file;
            }
            closedir($dir);
        }
        return $results;
    }

    //
    //This function return the size and number of of all files attached to activities
    //
    function referentiel_get_size_data_moodle_1_9_api($records_users, $referentiel_referentiel_id){
    // API Moodle 1.9
        global $CFG;
        $o = new object();
        
        $o->size=0;
        $o->nfile=0;
        if (!empty($records_users)){
            // print_object($records_users);

            foreach ($records_users as $userid){
                if (is_object($userid)){
                    $userid=$userid->userid;
                }
                $r_activites=referentiel_get_all_activites_user($referentiel_referentiel_id, $userid );

                if (!empty($r_activites)){
                    foreach ($r_activites as $activite){
                        $r_documents=referentiel_get_documents($activite->id);
                        if (!empty($r_documents)){
                            foreach ($r_documents as $document){
                                // chercher la taille
                                $url_document = $document->url_document;
                                if (!empty($url_document) && !preg_match("/http/",$url_document)){
                                    // la taille du fichier est collectée

                                    $parts = explode('/', $url_document);
                                    $status = false;
                                    if (is_array($parts)) {
                                        $course_id = $parts[0];
                                        $instance_id = $parts[3];
                                        $user_id = $parts[4];
                                        $file_name=$parts[5];
                                    }
                                    // DEBUG
                                    // echo "<br />DEBUG :: lib_archive :: 225 :: referentiel_get_size_data<br />\n";
                                    //echo "<br />URL : $url_document<br />\n";
                                    // print_object($parts);
                                    // URL :
                                    $file_url= $CFG->dataroot.'/'.$url_document;
                                    //echo "<br />URI: $file_url\n";
                                    //echo "<br />FILENAME: $file_name\n";
                                    if (file_exists($file_url)){
                                        $o->size+=filesize($file_url);
                                        $o->nfile++;
                                    }
                                }
                            }
                        }
                    }
                }


            }

        }
        return($o);
    }

// MOODLE 2 file API

    //This function copies all the needed files under the "user" directory to the "user_files"
    //directory under temp/archive
    function referentiel_copy_document_file($referentiel_referentiel_id, $user_creator, $userid, $file_uri) {
    // Moodle 1.9 :: $file_uri= 2/moddata/referentiel/1/3/arrete-C2i2eVDef.pdf
    // Moodle 2.x :: $file_uri= /contextid/mod_referentiel/document/ID/arrete-C2i2eVDef.pdf
    // /153/mod_referentiel/document/4/referentiel-epc.csv
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');
        
        $status = 0;

        //First we check that "user_files" exists and create it if necessary
        //in temp/archive/$backup_code  dir
        if (referentiel_check_and_create_document_files_dir($referentiel_referentiel_id, $user_creator, $userid)){
            $fullpath=$file_uri;
            // Traitement de $fullpath
            if ($fullpath && preg_match('/\//', $fullpath)){
                $t_fullpath=explode('/',$fullpath,6);
                if (!empty($t_fullpath) && empty($t_fullpath[0])){
                    $garbage=array_shift($t_fullpath);
                }
                if (!empty($t_fullpath)){
                    list($contextid, $component, $filearea, $itemid, $path )  = $t_fullpath;
                    if ($path){
                        if (preg_match('/\//', $path)){
                            $filename=substr($path, strrpos($path, '/')+1);
                            $path='/'.substr($path, 0, strrpos($path, '/')+1);
                        }
                        else{
                            $filename=$path;
                            $path='/';
                        }
                    }
                }
            }

            // echo "<br />DEBUG :: lib.php :: Ligne 5918 ::<br /> $contextid, $component, $filearea, $itemid, $path, $filename\n";
            // devrait afficher cas 0  :: 0, mod_referentiel, referentiel, 0, /, jf44.png
            // devrait afficher cas 1  :: 30, mod_referentiel, referentiel, 0, /rep1/rep2/, jf44.png
            // devrait afficher cas 2  :: 51, mod_referentiel, referentiel, 12, /, jf44.png
            $fs = get_file_storage();
            // Get file
            $file = $fs->get_file($contextid, $component, $filearea, $itemid, $path, $filename);
            if ($file) {
                // DEBUG
                // echo "<br />DEBUG :: 220 :: $filename\n";
                // print_object($file);
                // echo "<br />CONTENU\n";
                $contents = $file->get_content();
                // echo htmlspecialchars($contents);
                // $filesize = $file->get_filesize();
                // $filename = $file->get_filename();
                // $mimetype = $file->get_mimetype();
                // $timecreated =  userdate($file->get_timecreated(),"%Y/%m/%d-%H:%M",99,false);
                // $timemodified = userdate($file->get_timemodified(),"%Y/%m/%d-%H:%M",99,false);
                // $link= new moodle_url($CFG->wwwroot.'/pluginfile.php/'.$contextid.'/mod_referentiel/'.$filearea.'/'.$itemid.'/'.$filename);
                // $url='<a href="'.$link.'" target="_blank">'.$filename.'</a><br />'."\n";

                $file_dest_path_name="document_files/".$userid."/".$filename;
                //exit;
                // Moodle 2.0
                // $f=fopen($CFG->dataroot."/temp/archive/".$referentiel_referentiel_id."/".$user_creator."/".$file_dest_path_name,"w");
                // Moodle 22
                $path_temp =  cleardoubleslashes(get_string('archivetemp', 'referentiel').'/'.$referentiel_referentiel_id.'/'.$user_creator);
                // Moodle 2.2
                $temp_dir=make_temp_directory($path_temp);
                $f=fopen(cleardoubleslashes($temp_dir."/".$file_dest_path_name),"w");

                $status=fwrite($f, $contents);
                fclose($f);
            }
        }
        if ($status){
            return $file_dest_path_name;
        }
        else{
            return '';
        }
    }

    //
    //This function return the size and number of all files attached to an activity
    //
    function referentiel_get_size_data($records_users, $referentiel_referentiel_id){
    // API Moodle 1.9
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $o = new object();

        $o->size=0;
        $o->nfile=0;
        if (!empty($records_users)){
            // print_object($records_users);

            foreach ($records_users as $userid){
                if (is_object($userid)){
                    $userid=$userid->userid;
                }
                $r_activites=referentiel_get_all_activites_user($referentiel_referentiel_id, $userid );

                if (!empty($r_activites)){
                    foreach ($r_activites as $activite){
                        $r_documents=referentiel_get_documents($activite->id);
                        if (!empty($r_documents)){
                            foreach ($r_documents as $document){
                                // chercher la taille
                                $url_document = $document->url_document;
                                if (!empty($url_document) && (!preg_match("/http/",$url_document) || !preg_match("/https/",$url_document))){
                                    // la taille du fichier est collectée
                                    $fullpath=$url_document;
                                    // Traitement de $fullpath
                                    if ($fullpath && preg_match('/\//', $fullpath)){
                                        // DEBUG
                                        //echo "<br />DEBUG :: lib_archive.php :: 367 :: FULLPATH : $fullpath<br />\n";

                                        $t_fullpath=explode('/',$fullpath,6);
                                        //print_r($t_fullpath);
                                        //echo "<br />\n";
                                        
                                        if (!empty($t_fullpath) && empty($t_fullpath[0])){
                                            $garbage=array_shift($t_fullpath);
                                        }
                                        if (!empty($t_fullpath)){
                                            @list($contextid, $component, $filearea, $itemid, $path )  = $t_fullpath;
                                            if ($path){
                                                if (preg_match('/\//', $path)){
                                                    $filename=substr($path, strrpos($path, '/')+1);
                                                    $path='/'.substr($path, 0, strrpos($path, '/')+1);
                                                }
                                                else{
                                                    $filename=$path;
                                                    $path='/';
                                                }
                                            }

                                            // echo "<br />DEBUG :: lib.php :: Ligne 5918 ::<br /> $contextid, $component, $filearea, $itemid, $path, $filename\n";
                                            // devrait afficher cas 0  :: 0, mod_referentiel, referentiel, 0, /, jf44.png
                                            // devrait afficher cas 1  :: 30, mod_referentiel, referentiel, 0, /rep1/rep2/, jf44.png
                                            // devrait afficher cas 2  :: 51, mod_referentiel, referentiel, 12, /, jf44.png
                                            $fs = get_file_storage();

                                            // Get file
                                            $file = $fs->get_file($contextid, $component, $filearea, $itemid, $path, $filename);
                                            if ($file) {
                                                // DEBUG
                                                // echo "<br />DEBUG :: 220 :: $filename\n";
                                                // print_object($file);
                                                // echo "<br />CONTENU\n";
                                                // $contents = $file->get_content();
                                                // echo htmlspecialchars($contents);
                                                $filesize = $file->get_filesize();
                                                // $filename = $file->get_filename();
                                                // $mimetype = $file->get_mimetype();
                                                // $timecreated =  userdate($file->get_timecreated(),"%Y/%m/%d-%H:%M",99,false);
                                                // $timemodified = userdate($file->get_timemodified(),"%Y/%m/%d-%H:%M",99,false);
                                                // $link= new moodle_url($CFG->wwwroot.'/pluginfile.php/'.$contextid.'/mod_referentiel/'.$filearea.'/'.$itemid.'/'.$filename);
                                                // $url='<a href="'.$link.'" target="_blank">'.$filename.'</a><br />'."\n";

                                                $o->size+=$filesize;
                                                $o->nfile++;
                                            }
                                        }
                                    }
                                }
                            }
                       }
                    }
                }
            }
        }
        return($o);
    }


/**
 *  copie d'un fichier avec la file api Moodle2
 * @param string path file source
 * @param string filename file dest
 * @param object $context
 * @return bool false if file not found, does not return if found - just create the file
 */
     
    function referentiel_copy_file_moodle2_api ($from_file, $to_path, $filename, $context) {
    // Moodle 2 file API

        global $CFG;
        require_once($CFG->libdir.'/filelib.php');
        
        if (is_file($from_file)) {
            //Debug
            // echo "<br />DEBUG :: lib_archive.php :: 473 :: Copying ".$from_file." to ".$filename;

            $fs=get_file_storage();
            // Prepare file record object
            $fileinfo = array(
                'contextid' => $context->id, // ID of context
                'component' => 'mod_referentiel',     // usually = table name
                'filearea' => 'archive',     // usually = table name
                'itemid' => 0,               // usually = ID of row in table
                'filepath' => $to_path,           // any path beginning and ending in /
                'filename' => $filename); // any filename
            $fs->create_file_from_pathname($fileinfo, $from_file);
            return true;
        }
        else{
            echo "<br />Error: not file or dir ".$from_file;               //Debug
            return false;
        }
    }

/**
 *  Affiche les fichiers d'archives avec la file api Moodle2
 * @param int contextid : contexte id
 * @param string titre : title
 * @param string appli : post action
 * @param int userid_filtre : user owner of file to display
 * @param int instanceid : instance id
 * @param bool ok_portfolio : portfolio supported
 * @param bool report : function called from admin/report/referentiel (switch for return page)
 * @return bool false if file not found, does not return if found - just create the file

 */
 
// ------------------
function referentiel_get_manage_archives($contextid, $titre, $appli, $userid_filtre=0,
    $instanceid=0, $ok_portfolio=false, $report=false, $export_format=''){
// retourne la liste des liens vers des fichiers stockes dans le filearea
// propose la suppression
global $CFG;
global $OUTPUT;
    // Archives older than REFERENTIEL_ARCHIVE_OBSOLETE days will be deleted.
    $delai_destruction = REFERENTIEL_ARCHIVE_OBSOLETE * 24 * 3600;
    $date_obsolete= time() - $delai_destruction;
    $msg= get_string('archive_deleted', 'referentiel', date ("Y-m-d H:i:s.", $date_obsolete));

    $s='';
    $total_size=0;
    $nfile=0;
    // fileareas autorisees
    $filearea='archive';
    $strauthor=get_string('auteur', 'referentiel');
    $strfilename=get_string('filename', 'referentiel');
    $strfilesize=get_string('filesize', 'referentiel');
    $strtimecreated=get_string('timecreated', 'referentiel');
    $strtimemodified=get_string('timemodified', 'referentiel');
    $strmimetype=get_string('mimetype', 'referentiel');
    $strmenu=get_string('delete');
    $strportfolio=get_string('publishporttfolio','referentiel');

    $strurl=get_string('url');

    // publication via potfolio
    if ($ok_portfolio) {
        require_once($CFG->libdir.'/portfoliolib.php');
    }

    $fs = get_file_storage();
    if ($files = $fs->get_area_files($contextid, 'mod_referentiel', $filearea, 0, "timemodified", false)) {
        $table = new html_table();
        if ($ok_portfolio){
            $table->head  = array ($strauthor, $strfilename, $strfilesize, $strtimecreated, $strtimemodified, $strmimetype, $strmenu, $strportfolio);
            $table->align = array ("right", "center", "left", "left", "left", "center", "center", "center");
        }
        else{
            $table->head  = array ($strauthor, $strfilename, $strfilesize, $strtimecreated, $strtimemodified, $strmimetype, $strmenu);
            $table->align = array ("right", "center", "left", "left", "left", "center", "center");
        }
        
        $ok=false; //drapeau d'affichage
        foreach ($files as $file) {
            // print_object($file);
            $filesize = $file->get_filesize();
            $filename = $file->get_filename();
            $mimetype = $file->get_mimetype();
            $filepath = $file->get_filepath();

            $iconimage = '<img src="'.$OUTPUT->pix_url(file_mimetype_icon($mimetype)).'" class="icon" alt="'.$mimetype.'" />';

            $userid=0;
            $author = '';
            $parts = explode('/', $filepath);
            if (is_array($parts)) {
                $refrefid = $parts[1];
                if (isset($parts[2])){
                    $userid = trim($parts[2]);
                    // echo "<br />USER:$userid\n";
                    $author='#'.$userid.' '.referentiel_get_user_info($userid);
                }
            }
            $fullpath ='/'.$contextid.'/mod_referentiel/'.$filearea.'/'.'0'.$filepath.$filename;
            // echo "<br />FULPATH :: $fullpath \n";
            $timecreated =  userdate($file->get_timecreated(),"%Y/%m/%d-%H:%M",99,false);
            $timemodified = userdate($file->get_timemodified(),"%Y/%m/%d-%H:%M",99,false);

            $link= new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);
            $url=' <a href="'.$link.'" target="_blank">'.$iconimage.$filename.'</a><br />'."\n";

            $delete_link='<input type="checkbox" name="deletefile[]"  value="'.$fullpath.'" />'."\n";


            if ($userid && (($userid_filtre==0) || ($userid==$userid_filtre))){
                // afficher ce fichier
                $ok=true;
                $output_button='';
                // Publish button
                if ($ok_portfolio) {
                    $params = array('instanceid' => $instanceid, 'attachment' => $file->get_id(), 'report' => $report, 'export_format' => $export_format);
                    // DEBUG
                    /*
                    echo "<br />DEBUG :: lib_archive.php :: 585 :: PARAM PORTFOLIO<br />\n";
                    print_object($params);
                    echo "<br />\n";
                    // exit;
                    */
                    $button = new portfolio_add_button();
// Version anterieure à Moodle 2.4
//                    $button->set_callback_options('referentiel_portfolio_caller',
//                        $params, '/mod/referentiel/portfolio/mahara/locallib_portfolio.php');
// Version Moodle 2.4
                    $button->set_callback_options('referentiel_portfolio_caller',
                        $params, 'mod_referentiel');
                    $button->set_format_by_file($file);
                    // $button->set_formats(array(PORTFOLIO_FORMAT_FILE));
                    // $output_button = "<a href=\"$path\">$iconimage</a> ";
                    $output_button .= $button->to_html(PORTFOLIO_ADD_ICON_LINK);

                    $table->data[] = array ($author, $url, display_size($filesize),
                        $timecreated,
                        $timemodified,
                        $mimetype,
                        $delete_link,
                        $output_button);
                }
                else{
                    $table->data[] = array ($author, $url, display_size($filesize),
                        $timecreated,
                        $timemodified,
                        $mimetype,
                        $delete_link);
                }
                $total_size+=$filesize;
                $nfile++;
            }
        }
        if ($ok){
            if ($ok_portfolio) {
                $table->data[] = array ('', get_string('nbfile', 'referentiel',$nfile), get_string('totalsize', 'referentiel', display_size($total_size)), '', '', '','');
            }
            else{
                $table->data[] = array ('',get_string('nbfile', 'referentiel',$nfile), get_string('totalsize', 'referentiel', display_size($total_size)),'','');
            }
            echo $OUTPUT->box_start('generalbox  boxaligncenter');
            echo '<div align="center">'."\n";
            echo '<h3>'.$titre.'</h3>'."\n";

            // message d'information
            echo '<p><i>'.$msg.'</i></p>'."\n";
            
            echo '<form method="post" action="'.$appli.'">'."\n";
            echo "\n".'<input type="hidden" name="sesskey" value="'.sesskey().'" />'."\n";

        echo '<div align="center">'."\n";
        echo '<input type="button" name="select_all_archive" id="select_tous_enseignants" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkall()" />'."\n";
//        echo '&nbsp; &nbsp; &nbsp; <input type="button" name="select_not_any_archive" id="select_aucun_enseignant" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckall()" />'."\n";
//        echo '<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
        echo '&nbsp; <input type="reset" value="'.get_string('select_not_any', 'referentiel').'" />'."\n";
        echo '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';
        echo '<input type="submit" name="save" value="'.get_string('deletefile', 'referentiel').'" />'."\n";
        echo '<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
        echo '</div>'."\n";

            echo html_writer::table($table);

        echo '<div align="center">'."\n";
        echo '<input type="button" name="select_all_archive" id="select_tous_enseignants" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkall()" />'."\n";
//        echo '&nbsp; &nbsp; &nbsp; <input type="button" name="select_not_any_archive" id="select_aucun_enseignant" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckall()" />'."\n";
//        echo '<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
        echo '&nbsp;  <input type="reset" value="'.get_string('select_not_any', 'referentiel').'" />'."\n";
        echo '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';
        echo '<input type="submit" name="save" value="'.get_string('deletefile', 'referentiel').'" />'."\n";
        echo '<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
        echo '</div>'."\n";

            echo '</form>'."\n";

            echo '</div>'."\n";
            echo $OUTPUT->box_end();
        }
    }
}


    /**
     * Retourne le volume et le nombre de fichiers
     * du dossier d'archive pour un cours et une occurrence de referentiel
     *
     * @input : context id,
     * @output : object
     */

function referentiel_get_how_many_files($contextid){
// retourne la taille des fichiers stockes dans le filearea
global $CFG;
    $files_info=new stdClass;
    $files_info->total_size=0;
    $files_info->nfile=0;

    // fileareas autorisees
    $filearea='archive';
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($contextid, 'mod_referentiel', $filearea, 0, 'timemodified', false)) {
        foreach ($files as $file) {
            // print_object($file);
            $filesize = $file->get_filesize();
            $filename = $file->get_filename();
            $mimetype = $file->get_mimetype();
            $filepath = $file->get_filepath();
            $fullpath ='/'.$contextid.'/mod_referentiel/'.$filearea.'/'.'0'.$filepath.$filename;
            // echo "<br />FULPATH :: $fullpath \n";
            $timecreated =  $file->get_timecreated();
            $timemodified = $file->get_timemodified();

            // $link= new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);
            $files_info->total_size+=$filesize;
            $files_info->nfile++;
        }
    }
    return $files_info;
}

    // suppression archives obsoletes
    function referentiel_purge_archives($contextid, $delai_destruction=0, $debug=false){
    // appelee par le cron
        global $CFG;
        $msg='';
        if ($contextid){
            // fileareas autorisees
            $filearea='archive';
            $fs = get_file_storage();
            if ($files = $fs->get_area_files($contextid, 'mod_referentiel', $filearea, 0, 'timemodified', false)) {
                foreach ($files as $file) {
                    // print_object($file);
                    $filesize = $file->get_filesize();
                    $filename = $file->get_filename();
                    $mimetype = $file->get_mimetype();
                    $filepath = $file->get_filepath();
                    $fullpath ='/'.$contextid.'/mod_referentiel/'.$filearea.'/'.'0'.$filepath.$filename;
                    // echo "<br />FULPATH :: $fullpath \n";
                    $timecreated =  $file->get_timecreated();
                    $timemodified = $file->get_timemodified();

                    // echo "<br />$file_time :: $file_url was last modified: " . date ("F d Y H:i:s.", $file_time)."<br />\n";
                    if ( (time() - $timemodified) > $delai_destruction){
                        referentiel_delete_a_file($fullpath);
                        if ($debug){
                            mtrace($fullpath. "deleted \n");
                        }
                    }
                    if ($debug){
                        mtrace(".");
                    }
                }
            }
        }
        return $msg;
    }


?>

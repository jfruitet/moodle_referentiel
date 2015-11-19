<?php  // $Id: format.php,v 1.0 2008/05/01 00:00:00 jfruitet Exp $ 
/**
 * Base class for referentiel import and export formats.
 * recupere de question/format.php
 *
 * @author Martin Dougiamas, Howard Miller, and many others.
 *         {@link http://moodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package referentiel
 */
 

class rformat_default {

    var $displayerrors = true;
	var $rinstance = NULL; // instance de referentiel 
	var $rreferentiel = NULL; // referentiel_referentiel
    var $coursemodule = NULL;	
    var $course = NULL;
    var $filename = '';
    var $importerrors = 0;
    var $stoponerror = true;
	var $override = false;
	var $returnpage = "";
	var $new_referentiel_id = ""; // id d'un referentiel_referentiel
    var $contents = "";
    var $context = NULL;
    
// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

// Accessor methods
    /**
     * set the context class variable
     * @param contexte object Moodle context variable
     */
    function setContext( $context ) {
        $this->context = $context;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setContents( $contents ) {
        $this->contents = $contents;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setIReferentiel( $referentiel ) {
        $this->rinstance = $referentiel;
    }

	    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setRReferentiel( $referentiel ) {
        $this->rreferentiel = $referentiel;
    }


    /**
     * set the referentiel
     * @param id referentiel the referentiel referentiel id
     */
	function setReferentielId( $id ) {
        $this->new_referentiel_id = $id;
    }

    /**
     * set the action 
     * @param string action 
     */
	function setAction( $action ) {
        $this->action = $action;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCourse( $course ) {
        $this->course = $course;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCoursemodule( $cm ) {
        $this->coursemodule = $cm;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setFilename( $filename ) {
        $this->filename = $filename;
    }


    /**
     * set returnpage
     * @param bool stoponerror stops database write if any errors reported
     */
    function setReturnpage( $returnpage ) {
        $this->returnpage = $returnpage;
    }

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    function setStoponerror( $stoponerror ) {
        $this->stoponerror = $stoponerror;
    }
	
    /**
     * set override
     * @param bool override database write 
     */
    function setOverride( $override ) {
        $this->override = $override;
    }
	
    /**
     * set newinstance
     * @param bool newinstance database write 
     */
    function setNewinstance( $newinstance ){
        $this->newinstance = $newinstance;
    }
	

/*******************
 * EXPORT FUNCTIONS
 *******************/

    /** 
     * Provide export functionality for plugin referentiel types
     * Do not override
     * @param name referentiel name
     * @param referentiel object data to export 
     * @param extra mixed any addition format specific data needed
     * @return string the data to append to export or false if error (or unhandled)
     */
    function try_exporting( $name, $referentiel, $extra=null ) {

        // work out the name of format in use
        $formatname = substr( get_class( $this ), strlen( 'rformat_' ));
        $methodname = "export_to_$formatname";

		if (method_exists( $methodname )) {
			if ($data = $methodname( $referentiel, $this, $extra )) {
				return $data;
            }
        }
        return false;
    }

    /**
     * Return the files extension appropriate for this type
     * override if you don't want .txt
     * @return string file extension
     */
    function export_file_extension() {
        return ".txt";
    }

    /**
     * Do any pre-processing that may be required
     * @param boolean success
     */
    function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    function presave_process( $content ) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return boolean success
     */
    function exportprocess() {
        global $CFG;

        //notify( get_string('exportingreferentiels', 'referentiel') );
        $count = 0;

        // results are first written into string (and then to a file)
        // so create/initialize the string here
        $expout = "";
        
        // export the item displaying message
        $count++;
        echo "<hr /><p><b>$count</b>. ".$this->rreferentiel->name."</p>";
        $expout .= $this->write_referentiel() . "\n";

        // continue path for following error checks
        $course = $this->course;
        $continuepath = "$CFG->wwwroot/mod/referentiel/export.php?d=".$this->rreferentiel->id; 

        // did we actually process anything
        if ($count==0) {
            $this->error( get_string('noreferentiels','referentiel',$continuepath),$filepath,'','export' );
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );
/*
// Moodle 1.9
        // write file
        $filepath = $path."/".$this->filename . $this->export_file_extension();
        if (!$fh=fopen($filepath,"w")) {
            $this->error( get_string('cannotopen','referentiel',$continuepath),$filepath,'','export' );
        }
		// DEBUG
		
		// echo "<br /> FORMAT : 218<br />$expout\n";
		
        if (!fwrite($fh, $expout, strlen($expout) )) {
            $this->error( get_string('cannotwrite','referentiel',$continuepath),$filepath,'','export' );
        }
        fclose($fh);
*/

        // Moodle 2.0
        $fs = get_file_storage();
        // Prepare file record object
        $fileinfo = array(
            'contextid' => $this->context->id, // ID of context
            'component' => 'mod_referentiel',     // usually = table name
            'filearea' => 'referentiel',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => $this->get_export_dir(),  // any path beginning and ending in /
            'filename' => $this->filename.$this->export_file_extension()); // any filename

        // Create file containing text
        $fs->create_file_from_string($fileinfo, $expout);

        return true;
    }

    /**
     * Do an post-processing that may be required
     * @return boolean success
     */
    function exportpostprocess() {
        return true;
    }

    /**
     * convert a single referentiel object into text output in the given
     * format.
     * This must be overriden
     * @param object referentiel referentiel object
     * @return mixed referentiel export text or null if not implemented
     */
    function write_referentiel() {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * get directory into which export is going 
     * @return string file path
     */
    function get_export_dir() {
		global $CFG;
        /*
        // Moodle 1.9
        $dirname = get_string('exportfilename', 'referentiel');
        $path =  $this->course->id.'/'.$CFG->moddata.'/'.$dirname; 
        return $path;
        */
        // Moodle 2.0
        return '/';
    }



/***********************
 * IMPORTING FUNCTIONS
 ***********************/

    /**
     * Handle parsing error
     */
    function error( $message, $text='', $name='', $type='' ) {
        if ($type=='import')
            $error = get_string('importerror', 'referentiel');
        else
            $error = get_string('exporterror', 'referentiel');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$error $name</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<i>$message</i>\n";
        echo "</div>";

         $this->importerrors++;
    }



    /** 
     * Import for referentieltype plugins
     * Do not override.
     * @param data mixed The segment of data containing the referentiel
     * @param referentiel object processed (so far) by standard import code if appropriate
     * @param extra mixed any additional format specific data that may be passed by the format
     * @return object referentiel object suitable for save_options() or false if cannot handle
     */
    function try_importing( $data, $referentiel=null, $extra=null ) {

        // work out what format we are using
        $formatname = substr( get_class( $this ), strlen('rformat_'));
        $methodname = "import_from_$formatname";

        // loop through installed referentieltypes checking for
        // function to handle this referentiel
        if (method_exists( $methodname)) {
        	if ($referentiel = $methodname( $data, $referentiel, $this, $extra )) {
            	return $referentiel;
            }
        }
        return false;   
    }

    /**
     * Perform any required pre-processing
     * @return boolean success
     */
    function importpreprocess() {
        return true;
    }

    /**
     * Process the file
     * This method should not normally be overidden
     * @return boolean success
     */
    function importprocess() {

       	// reset the timer in case file upload was slow
       	@set_time_limit();

       	// STAGE 1: Parse the file
       	// notify( get_string('parsing', 'referentiel') );
         
		// Moodle 1.9
        //if (! $lines = $this->readdata($this->filename)) {
        // Moodle 2.0
        if (! $lines = $this->readdata()) {
            notify( get_string('cannotread', 'referentiel') );
            return false;
        }
		$newly_imported_referentiel = new stdClass();
		
		// DEBUG
		// echo "<br />DEBUG :: ./mod/referentiel/format.php :: 390<br />EXIT<br />\n";
		// print_r($lines);
		// exit;
        if (! $newly_imported_referentiel = $this->lines_2_referentiel($lines)) {   // Extract the referentiel
            notify( get_string('noinfile', 'referentiel') );
            return false;
        }

        // STAGE 2: Write data to database
		// echo "<br />\n";
		// print_object($newly_imported_referentiel);
		// echo "<br />\n";
        // check for errors before we continue
        if ($this->stoponerror and ($this->importerrors>0)) {
            return false;
        }

		// notify( get_string('importdone', 'referentiel') );
		

		return true;
    }

    /**
     * Return complete file within an array, one item per line
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
    /*
    function readdata($filename) {
    // Moodle 1.9
        if (is_readable($filename)) {
            $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            if (ereg("\r", $filearray[0]) AND !ereg("\n", $filearray[0])) {
                return explode("\r", $filearray[0]);
            }
            else {
                return $filearray;
            }
        }
        return false;
    }
    */
    
    function readdata() {
    // MOODLE 2.0
        if (!empty($this->contents)) {
            // DEBUG
            // echo "<br />DEBUG :: ./mod/referentiel/format.php :: 439<br />EXIT<br />\n";
            // echo nl2br($this->contents)." NEWLINE\n";


            // $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            //if (ereg("\r", $this->contents) AND !ereg("\n", $this->contents)) {
            if (preg_match("/\r/", $this->contents) AND !preg_match("/\n/", $this->contents)) {
                $content=explode("\r", $this->contents);
            }
            else if (preg_match("/\r\n/", $this->contents)) {
                $content=explode("\r\n", $this->contents);
            }
            else {
                $content=explode("\n", $this->contents);
            }

            // print_r($content);
            // exit;
            return ($content);
        }
        return false;
    }

    /**
     * Parses an array of lines into a referentiel, 
     * where is a newly_imported_referentiel object as defined by 
     * readimportedreferentiel().
     *
     * @param array lines array of lines from readdata
     * @return array referentiel object
     */
    function lines_2_referentiel($lines) {
	// 
        $tline = array();
		if (is_array($lines)){
            foreach ($lines as $line) {
                $line = trim($line);
			
                if (!empty($line)) {
                    $tline[] = $line;
                }
            }
		    // echo "<br />DEBUG 3 : format.php :: ligne 453 :: fonction lines_2_referentiel()<br />\n";

	       	// echo "<br />\n";
		    // exit;
            if (!empty($tline)) {  // conversion
                $imported_referentiel = $this->read_import_referentiel($tline);
            }
        }
        else{
            $imported_referentiel = $this->read_import_referentiel($lines);
        }
        return $imported_referentiel ;
    }


    /**
     * return an "empty" referentiel
     * Somewhere to specify referentiel parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default referentiel
	*/      
    function defaultreferentiel() {
	// retourne un objet import_referentiel qui mime l'objet refrentiel
        $import_referentiel = new stdClass();
		$import_referentiel->name="";
		$import_referentiel->code_referentiel="";
		$import_referentiel->description_referentiel="";
		$import_referentiel->url_referentiel="";
		$import_referentiel->seuil_certificat="";
    	$import_referentiel->timemodified = time();
		$import_referentiel->nb_domains="";
		$import_referentiel->liste_codes_competence="";
		$import_referentiel->local=0;
    	$import_referentiel->id = 0;
        // this option in case the referentieltypes class wants
        // to know where the data came from
        $import_referentiel->export_process = true;
        $import_referentiel->import_process = true;
        return $import_referentiel;
    }

	function defaultprotocole() {
    // retourne un objet protocol
        $protocole = new stdClass();
    	// $protocole->id = 0;
		$protocole->ref_occurrence=0;
		$protocole->seuil_referentiel=0;
		$protocole->l_domaines_oblig='';
		$protocole->l_seuils_domaines='';
		$protocole->l_minimas_domaines='';
		$protocole->l_competences_oblig='';
        $protocole->l_seuils_competences='';
        $protocole->l_minimas_competences='';
        $protocole->l_items_oblig='';
		$protocole->timemodified=0;
		$protocole->actif=0;
        $protocole->commentaire='';
        return $protocole;
    }

    function defaultdomaine() {
        // retourne un objet domaine
        $domaine = new stdClass();
    	$domaine->id = 0;
		$domaine->code_domaine="";
		$domaine->description_domaine="";
		$domaine->num_domaine=0;
		$domaine->nb_competences=0;
		$domaine->ref_referentiel=0;
        return $domaine;
    }

    function defaultcompetence() {
	// retourne un objet competence	
        $competence = new stdClass();
    	$competence->id = 0;
		$competence->code_competence="";
		$competence->description_competence="";
		$competence->num_competence=0;
		$competence->nb_item_competences=0;
		$competence->ref_domaine=0;
        return $competence;
    }

    function defaultitem() {
	// retourne un objet item de competence
        $item = new stdClass();
    	$item->id = 0;
		$item->code_item="";
		$item->description_item="";
		$item->num_item=0;
		$item->type_item="";
		$item->poids_item=0;
		$item->ref_competence=0;
		$item->ref_referentiel=0;
        return $item;
    }

    /**
     * Given the data known to define a referentiel in 
     * this format, this function converts it into a referentiel 
     * object suitable for processing and insertion into Moodle.
     *
     * If your format does not use blank lines to delimit referentiels
     * (e.g. an XML format) you must override 'readreferentiels' too
     * @param $lines mixed data that represents referentiel
     * @return object referentiel object
     */
	function read_import_referentiel($lines) {

        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * Override if any post-processing is required
     * @return boolean success
     */
    function importpostprocess() {
        return true;
    }

    /**
     * Import an image file encoded in base64 format
     * @param string path path (in course data) to store picture
     * @param string base64 encoded picture
     * @return string filename (nb. collisions are handled)
     */
    function importimagefile( $path, $base64 ) {
        global $CFG;

        // all this to get the destination directory
        // and filename!
        $fullpath = "{$CFG->dataroot}/{$this->course->id}/$path";
        $path_parts = pathinfo( $fullpath );
        $destination = $path_parts['dirname'];
        $file = clean_filename( $path_parts['basename'] );

        // check if path exists
        check_dir_exists($destination, true, true );

        // detect and fix any filename collision - get unique filename
        $newfiles = resolve_filename_collisions( $destination, array($file) );        
        $newfile = $newfiles[0];

        // convert and save file contents
        if (!$content = base64_decode( $base64 )) {
            return '';
        }
        $newfullpath = "$destination/$newfile";
        if (!$fh = fopen( $newfullpath, 'w' )) {
            return '';
        }
        if (!fwrite( $fh, $content )) {
            return '';
        }
        fclose( $fh );

        // return the (possibly) new filename
        //$newfile = ereg_replace("{$CFG->dataroot}/{$this->course->id}/", '',$newfullpath);
        $newfile = preg_replace("/".$CFG->dataroot."\/".$this->course->id."\//", '',$newfullpath);
        return $newfile;
    }
}

/**************************************************************************

ACTIVITES

***************************************************************************/
class aformat_default {

    var $displayerrors = true;
	var $ireferentiel = NULL; // instance
	var $rreferentiel = NULL; // referentiel
	var $ref_referentiel = 0;
	var $coursemodule = NULL;	
    var $course = NULL;
    var $filename = '';
    var $importerrors = 0;
    var $stoponerror = true;
    var $context = NULL;
    var $contents='';
    
// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

// Accessor methods
    /**
     * set the context class variable
     * @param contexte object Moodle context variable
     */
    function setContext( $context ) {
        $this->context = $context;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setContents( $contents ) {
        $this->contents = $contents;
    }



    /**
     * set the referentiel
     * @param object referentiel the referentiel instance object
     */
	function setIReferentiel( $referentiel_instance ) {
        $this->ireferentiel = $referentiel_instance;
    }

	    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setRefInstance( $id ) {
        $this->ref_instance = $id;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel referentiel object
     */
	function setRReferentiel( $referentiel_referentiel ) {
        $this->rreferentiel = $referentiel_referentiel;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setRefReferentiel( $id ) {
        $this->ref_referentiel = $id;
    }

    /**
     * set the action 
     * @param string action 
     */
	function setAction( $action ) {
        $this->action = $action;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCourse( $course ) {
        $this->course = $course;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCoursemodule( $cm ) {
        $this->coursemodule = $cm;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setFilename( $filename ) {
        $this->filename = $filename;
    }

    /**
     * set returnpage
     * @param bool stoponerror stops database write if any errors reported
     */
    function setReturnpage( $returnpage ) {
        $this->returnpage = $returnpage;
    }

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    function setStoponerror( $stoponerror ) {
        $this->stoponerror = $stoponerror;
    }
	
    /**
     * set override
     * @param bool override database write 
     */
    function setOverride( $override ) {
        $this->override = $override;
    }
	
    function error( $message, $text='', $name='', $type='' ) {
        if ($type=='import')
            $error = get_string('importerror', 'referentiel');
        else
            $error = get_string('exporterror', 'referentiel');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$error $name</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<i>$message</i>\n";
        echo "</div>";

         $this->importerrors++;
    }


/*******************
 * EXPORT FUNCTIONS
 *******************/

    /** 
     * Provide export functionality for plugin referentiel types
     * Do not override
     * @param name referentiel name
     * @param referentiel object data to export 
     * @param extra mixed any addition format specific data needed
     * @return string the data to append to export or false if error (or unhandled)
     */
    function try_exporting( $name, $referentiel, $extra=null ) {

        // work out the name of format in use
        $formatname = substr( get_class( $this ), strlen( 'aformat_' ));
        $methodname = "export_to_$formatname";

		if (method_exists( $methodname )) {
			if ($data = $methodname( $referentiel, $this, $extra )) {
				return $data;
            }
        }
        return false;
    }

    /**
     * Return the files extension appropriate for this type
     * override if you don't want .txt
     * @return string file extension
     */
    function export_file_extension() {
        return ".txt";
    }

    /**
     * Do any pre-processing that may be required
     * @param boolean success
     */
    function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    function presave_process( $content ) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return boolean success
     */
    function exportprocess() {
        global $CFG;

        // notify( get_string('exportingactivites', 'referentiel') );
        $count = 0;

        // results are first written into string (and then to a file)
        // so create/initialize the string here
        $expout = "";
        
        // export the item displaying message
        $count++;
        echo "<hr /><p><b>$count</b>. ".$this->ireferentiel->name."</p>";
		
        $expout .= $this->write_liste_activites( $this->ireferentiel ) . "\n";

        // continue path for following error checks
        $coursemodule = $this->coursemodule;
        $continuepath = "$CFG->wwwroot/mod/referentiel/export_activite.php?id=$coursemodule->id"; 

        // did we actually process anything
        if ($count==0) {
           $this->error( get_string('noactivite', 'referentiel', $continuepath),$filepath,'','export' );
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );

        // Moodle 1.9
        /*
        // write file
        $filepath = $path."/".$this->filename . $this->export_file_extension();

        if (!$fh=fopen($filepath,"w")) {
            $this->error( get_string('cannotopen', 'referentiel' ,$continuepath), $filepath,'', 'export' );
        }
        if (!fwrite($fh, $expout, strlen($expout) )) {
           $this->error( get_string('cannotwrite', 'referentiel', $continuepath), $filepath, '','export' );
        }
        fclose($fh);
        */
        
        // Moodle 2.0
        $fs = get_file_storage();
        // Prepare file record object
        $fileinfo = array(
            'contextid' => $this->context->id, // ID of context
            'component' => 'mod_referentiel',     // usually = table name
            'filearea' => 'activite',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => $this->get_export_dir(),  // any path beginning and ending in /
            'filename' => $this->filename.$this->export_file_extension()); // any filename

        // Create file containing text
        $fs->create_file_from_string($fileinfo, $expout);


        return true;
    }

    /**
     * Do an post-processing that may be required
     * @return boolean success
     */
    function exportpostprocess() {
        return true;
    }

    /**
     * convert a single referentiel object into text output in the given
     * format.
     * This must be overriden
     * @param object referentiel referentiel object
     * @return mixed referentiel export text or null if not implemented
     */
    function write_liste_activites() {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";
        return NULL;
    }

    /**
     * get directory into which export is going 
     * @return string file path
     */
    function get_export_dir() {
    
		global $CFG;
    // Moodle 1.9
    /*
        $dirname = get_string('exportfilename', 'referentiel');
        $path =  $this->course->id.'/'.$CFG->moddata.'/'.$dirname; 
        return $path;
    */
    // Moodle 2.0
        return '/';
    }



/***********************
 * IMPORTING FUNCTIONS
 ***********************/
 // MODIF JF 2015/06/17

    /**
     * Handle parsing error
     */
    function error( $message, $text='', $name='', $type='' ) {
        if ($type=='import')
            $error = get_string('importerror', 'referentiel');
        else
            $error = get_string('exporterror', 'referentiel');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$error $name</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<i>$message</i>\n";
        echo "</div>";

         $this->importerrors++;
    }



    /**
     * Import for referentieltype plugins
     * Do not override.
     * @param data mixed The segment of data containing the referentiel
     * @param referentiel object processed (so far) by standard import code if appropriate
     * @param extra mixed any additional format specific data that may be passed by the format
     * @return object referentiel object suitable for save_options() or false if cannot handle
     */
    function try_importing( $data, $referentiel=null, $extra=null ) {

        // work out what format we are using
        $formatname = substr( get_class( $this ), strlen('rformat_'));
        $methodname = "import_activities_from_$formatname";

        // loop through installed referentieltypes checking for
        // function to handle this referentiel
        if (method_exists( $methodname)) {
        	if ($record_activities = $methodname( $data, $referentiel, $this, $extra )) {
            	return $record_activities;
            }
        }
        return false;
    }

    /**
     * Perform any required pre-processing
     * @return boolean success
     */
    function importpreprocess() {
        return true;
    }

    /**
     * Process the file
     * This method should not normally be overidden
     * @return boolean success
     */
    function importprocess() {

       	// reset the timer in case file upload was slow
       	@set_time_limit();

       	// STAGE 1: Parse the file
       	// notify( get_string('parsing', 'referentiel') );

		// Moodle 1.9
        //if (! $lines = $this->readdata($this->filename)) {
        // Moodle 2.0
        if (! $lines = $this->readdata()) {
            notify( get_string('cannotread', 'referentiel') );
            return false;
        }
		$newly_imported_referentiel = new stdClass();

		// DEBUG
		// echo "<br />DEBUG :: ./mod/referentiel/format.php :: 390<br />EXIT<br />\n";
		// print_r($lines);
		// exit;
        if (! $newly_imported_referentiel = $this->lines_2_referentiel($lines)) {   // Extract the referentiel
            notify( get_string('noinfile', 'referentiel') );
            return false;
        }

        // STAGE 2: Write data to database
		// echo "<br />\n";
		// print_object($newly_imported_referentiel);
		// echo "<br />\n";
        // check for errors before we continue
        if ($this->stoponerror and ($this->importerrors>0)) {
            return false;
        }

		// notify( get_string('importdone', 'referentiel') );


		return true;
    }

    /**
     * Return complete file within an array, one item per line
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
    /*
    function readdata($filename) {
    // Moodle 1.9
        if (is_readable($filename)) {
            $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            if (ereg("\r", $filearray[0]) AND !ereg("\n", $filearray[0])) {
                return explode("\r", $filearray[0]);
            }
            else {
                return $filearray;
            }
        }
        return false;
    }
    */

    function readdata() {
    // MOODLE 2.0
        if (!empty($this->contents)) {
            // DEBUG
            // echo "<br />DEBUG :: ./mod/referentiel/format.php :: 439<br />EXIT<br />\n";
            // echo nl2br($this->contents)." NEWLINE\n";


            // $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            //if (ereg("\r", $this->contents) AND !ereg("\n", $this->contents)) {
            if (preg_match("/\r/", $this->contents) AND !preg_match("/\n/", $this->contents)) {
                $content=explode("\r", $this->contents);
            }
            else if (preg_match("/\r\n/", $this->contents)) {
                $content=explode("\r\n", $this->contents);
            }
            else {
                $content=explode("\n", $this->contents);
            }

            // print_r($content);
            // exit;
            return ($content);
        }
        return false;
    }

    /**
     * Parses an array of lines into a referentiel,
     * where is a newly_imported_referentiel object as defined by
     * readimportedreferentiel().
     *
     * @param array lines array of lines from readdata
     * @return array referentiel object
     */
    function lines_2_activities($lines) {
	//
        $tline = array();
		if (is_array($lines)){
            foreach ($lines as $line) {
                $line = trim($line);

                if (!empty($line)) {
                    $tline[] = $line;
                }
            }
		    // echo "<br />DEBUG 3 : format.php :: ligne 453 :: fonction lines_2_referentiel()<br />\n";

	       	// echo "<br />\n";
		    // exit;
            if (!empty($tline)) {  // conversion
                $imported_activities = $this->read_import_activities($tline);
            }
        }
        else{
            $imported_activities = $this->read_import_activities($lines);
        }
        return $imported_activities ;
    }



    /**
     * return an "empty" occurrence object
     * Somewhere to specify referentiel parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default referentiel
	*/
    function defaultoccurrence() {
	// retourne un objet import_occurrence qui mime l'objet referentiel
        $import_occurrence = new stdClass();
		$import_occurrence->id = 0;
		$import_occurrence->name="";
		$import_occurrence->code_referentiel="";
		$import_occurrence->description_referentiel="";
		$import_occurrence->cle_referentiel="";
		$import_occurrence->liste_codes_competence="";

        // this option in case the referentieltypes class wants
        // to know where the data came from
        $import_occurrence->export_process = false;
        $import_occurrence->import_process = true;
        return $import_occurrence;
    }

    /**
     * return an "empty" occurrence object
     * Somewhere to specify referentiel parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default referentiel
	*/
    function defaultactivite() {
	// retourne un objet import_activite qui mime l'objet a   ctivite
	/*
    <id>1</id>
    <type_activite>Test</type_activite>
    <description_activite>
      <text>Cr�ation d'une activit�</text>
    </description_activite>
    <competences_activite>A.1-1/A.1-2/</competences_activite>
    <commentaire_activite>
      <text></text>
    </commentaire_activite>
    <ref_instance>1</ref_instance>
    <ref_referentiel>1</ref_referentiel>
    <ref_course>2</ref_course>
    <userid>2</userid>
    <lastname>USER</lastname>
    <firstname>Admin</firstname>
    <teacherid>0</teacherid>
    <teacher_lastname></teacher_lastname>
    <teacher_firstname></teacher_firstname>
    <date_creation>1399802663</date_creation>
    <date_modif_student>1430844089</date_modif_student>
    <date_modif>0</date_modif>
    <approved>0</approved>
    <!-- document: 3  -->

	*/
        $import_activite = new stdClass();
		$import_activite->id = 0;
		$import_activite->type_activite="";
		$import_activite->description_activite="";
		$import_activite->competences_activite="";
        $import_activite->commentaire_activite="";
    	$import_activite->ref_instance=0;
    	$import_activite->ref_referentiel=   0;
	    $import_activite->ref_course=   0;
	    $import_activite->userid=   0;
	    $import_activite->lastname="";
	    $import_activite->firstname=   "";
	    $import_activite->teacherid=   "";
	    $import_activite->teacher_lastname=   "";
	    $import_activite->teacher_firstname=   ""
	    $import_activite->date_creation=   "";
	    $import_activite->date_modif_student=   "";
	    $import_activite->date_modif="";
    	$import_activite->approved="";
        $import_activite->ref_task=0;
        $import_activite->mailed=0;
        $import_activite->mailnow=0;

        // this option in case the referentieltypes class wants
        // to know where the data came from
        $import_activite->export_process = false;
        $import_activite->import_process = true;
        return $import_activite;
    }


    /**
     * Given the data known to define a referentiel in
     * this format, this function converts it into a referentiel
     * object suitable for processing and insertion into Moodle.
     *
     * If your format does not use blank lines to delimit referentiels
     * (e.g. an XML format) you must override 'readreferentiels' too
     * @param $lines mixed data that represents referentiel
     * @return object referentiel object
     */
	function read_import_activities($lines) {

        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * Override if any post-processing is required
     * @return boolean success
     */
    function importpostprocess() {
        return true;
    }

    /**
     * Import an image file encoded in base64 format
     * @param string path path (in course data) to store picture
     * @param string base64 encoded picture
     * @return string filename (nb. collisions are handled)
     */
    function importimagefile( $path, $base64 ) {
        global $CFG;

        // all this to get the destination directory
        // and filename!
        $fullpath = "{$CFG->dataroot}/{$this->course->id}/$path";
        $path_parts = pathinfo( $fullpath );
        $destination = $path_parts['dirname'];
        $file = clean_filename( $path_parts['basename'] );

        // check if path exists
        check_dir_exists($destination, true, true );

        // detect and fix any filename collision - get unique filename
        $newfiles = resolve_filename_collisions( $destination, array($file) );
        $newfile = $newfiles[0];

        // convert and save file contents
        if (!$content = base64_decode( $base64 )) {
            return '';
        }
        $newfullpath = "$destination/$newfile";
        if (!$fh = fopen( $newfullpath, 'w' )) {
            return '';
        }
        if (!fwrite( $fh, $content )) {
            return '';
        }
        fclose( $fh );

        // return the (possibly) new filename
        //$newfile = ereg_replace("{$CFG->dataroot}/{$this->course->id}/", '',$newfullpath);
        $newfile = preg_replace("/".$CFG->dataroot."\/".$this->course->id."\//", '',$newfullpath);
        return $newfile;
    }


}


class cformat_default { // certificat
    var $records_certificats = NULL;
    var $displayerrors = true;
	var $ireferentiel = NULL;
	var $rreferentiel = NULL;
	var $ref_referentiel = 0;
	var $coursemodule = NULL;
    var $course = NULL;
    var $filename = '';
    var $importerrors = 0;
    var $stoponerror = true;
    var $format_condense=0;
    var $export_pedagos=0;
    var $import_activity=0;  // une activite speciale est creer quand des certificats sont importes
    var $context = NULL;
    var $contents = '';

// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

// Accessor methods
    /**
     * set the import_activity variable
     * @param context variable
     */
    function setImportActivity( $import){
        $this->import_activity=$import;
    }
    /**
     * set the context class variable
     * @param contexte object Moodle context variable
     */
    function setContext( $context ) {
        $this->context = $context;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setContents( $contents ) {
        $this->contents = $contents;
    }


    /**
     * set the export_pedagos
     * @param int
     */
	function setExportPedago($export_pedagos){
        $this->export_pedagos = $export_pedagos;
    }

    /**
     * set the records to exports
     * @param object records objets
     */
  	function setRCFormat($format_condense ){
        $this->format_condense =$format_condense ;
    }

    /**
     * set the records to exports
     * @param object records objets
     */
  	function setRCertificats($records_certificats ){
        $this->records_certificats=$records_certificats;
    }

    /**
     * set the referentiel instance
     * @param object referentiel the referentiel object
     */
	function setIReferentiel( $referentiel ) {
        $this->ireferentiel = $referentiel;
    }

    /**
     * set the referentiel referentiel
     * @param object referentiel the referentiel object
     */
	function setRReferentiel( $referentiel ) {
        $this->rreferentiel = $referentiel;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setRefReferentiel( $id ) {
        $this->ref_referentiel = $id;
    }

    /**
     * set the action 
     * @param string action 
     */
	function setAction( $action ) {
        $this->action = $action;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCourse( $course ) {
        $this->course = $course;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCoursemodule( $cm ) {
        $this->coursemodule = $cm;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setFilename( $filename ) {
        $this->filename = $filename;
    }

    /**
     * set returnpage
     * @param bool stoponerror stops database write if any errors reported
     */
    function setReturnpage( $returnpage ) {
        $this->returnpage = $returnpage;
    }

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    function setStoponerror( $stoponerror ) {
        $this->stoponerror = $stoponerror;
    }
	
    /**
     * set override
     * @param bool override database write 
     */
    function setOverride( $override ) {
        $this->override = $override;
    }
	

/*******************
 * EXPORT FUNCTIONS
 *******************/
    function error( $message, $text='', $name='', $type='' ) {
        if ($type=='import')
            $error = get_string('importerror', 'referentiel');
        else
            $error = get_string('exporterror', 'referentiel');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$error $name</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<i>$message</i>\n";
        echo "</div>";

         $this->importerrors++;
    }



    /** 
     * Provide export functionality for plugin referentiel types
     * Do not override
     * @param name referentiel name
     * @param referentiel object data to export 
     * @param extra mixed any addition format specific data needed
     * @return string the data to append to export or false if error (or unhandled)
     */
    function try_exporting( $name, $referentiel, $extra=null ) {

        // work out the name of format in use
        $formatname = substr( get_class( $this ), strlen( 'cformat_' ));
        $methodname = "export_to_$formatname";

		if (method_exists( $methodname )) {
			if ($data = $methodname( $referentiel, $this, $extra )) {
				return $data;
            }
        }
        return false;
    }

    /**
     * Return the files extension appropriate for this type
     * override if you don't want .txt
     * @return string file extension
     */
    function export_file_extension() {
        return ".txt";
    }

    /**
     * Do any pre-processing that may be required
     * @param boolean success
     */
    function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    function presave_process( $content ) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return boolean success
     */
    function exportprocess() {
        global $CFG;

        // notify( get_string('exportingcertificats', 'referentiel') );
        $count = 0;

        // results are first written into string (and then to a file)
        // so create/initialize the string here
        $expout = "";
        
        // export the item displaying message
        $count++;
        echo "<hr /><p><b>$count</b>. ".$this->rreferentiel->name."</p>";
		
        $expout .= $this->write_certification() . "\n"; // on passe l'instance 

        // continue path for following error checks
        $coursemodule = $this->coursemodule;
        $continuepath = "$CFG->wwwroot/mod/referentiel/export_certificat.php?id=$coursemodule->id"; 

        // did we actually process anything
        if ($count==0) {
           $this->error( get_string('nocertificat', 'referentiel', $continuepath), $filepath ,'','export' );
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );
        // Moodle 1.9
        /*
        // write file
        $filepath = $path."/".$this->filename . $this->export_file_extension();
        if (!$fh=fopen($filepath,"w")) {
            $this->error( get_string('cannotopen', 'referentiel' ,$continuepath), $filepath, '','export' );
        }
        if (!fwrite($fh, $expout, strlen($expout) )) {
            $this->error( get_string('cannotwrite', 'referentiel', $continuepath), $filepath, '','export' );
        }
        fclose($fh);
        */
        // Moodle 2.0
        $fs = get_file_storage();
        // Prepare file record object
        $fileinfo = array(
            'contextid' => $this->context->id, // ID of context
            'component' => 'mod_referentiel',     // usually = table name
            'filearea' => 'certificat',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => $this->get_export_dir(),  // any path beginning and ending in /
            'filename' => $this->filename.$this->export_file_extension()); // any filename

        // Create file containing text
        $fs->create_file_from_string($fileinfo, $expout);

        return true;
    }

    /**
     * Do an post-processing that may be required
     * @return boolean success
     */
    function exportpostprocess() {
        return true;
    }

    /**
     * convert a single referentiel object into text output in the given
     * format.
     * This must be overriden
     * @param object referentiel referentiel object
     * @return mixed referentiel export text or null if not implemented
     */
    function write_certification() {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";
        return NULL;
    }

    /**
     * get directory into which export is going 
     * @return string file path
     */
	function get_export_dir() {
		/*
		// Moodle 1.9
        global $CFG;
        $dirname = get_string('exportfilename', 'referentiel');
        $path =  $this->course->id.'/'.$CFG->moddata.'/'.$dirname; 
        return $path;
        */
        // Moodle 2.0
        return '/';
    }

    /**
     * Import for certif type plugins
     * Do not override.
     * @param data mixed The segment of data containing the referentiel
     * @param referentiel object processed (so far) by standard import code if appropriate
     * @param extra mixed any additional format specific data that may be passed by the format
     * @return object referentiel object suitable for save_options() or false if cannot handle
     */
    function try_importing( $data, $certifs=null, $extra=null ) {

        // work out what format we are using
        $formatname = substr( get_class( $this ), strlen('cformat_'));
        $methodname = "import_from_$formatname";

        // loop through installed referentieltypes checking for
        // function to handle this referentiel
        if (method_exists( $methodname)) {
        	if ($certifs = $methodname( $data, $certifs, $this, $extra )) {
            	return $certifs;
            }
        }
        return false;
    }

    /**
     * Perform any required pre-processing
     * @return boolean success
     */
    function importpreprocess() {
        return true;
    }

    /**
     * Process the file
     * This method should not normally be overidden
     * @return boolean success
     */
    function importprocess() {

       	// reset the timer in case file upload was slow
       	@set_time_limit();

       	// STAGE 1: Parse the file
       	// notify( get_string('parsing', 'referentiel') );

		// if (! $lines = $this->readdata($this->filename)) {
		if (! $lines = $this->readdata($this->contents)) {
            notify( get_string('cannotread', 'referentiel') );
            return false;
        }
		$newly_imported_certifs = new stdClass();
        if (! $newly_imported_certifs = $this->lines_2_certifs($lines)) {   // Extract the certificates
            notify( get_string('noinfile', 'referentiel') );
            return false;
        }

        // STAGE 2: Write data to database
		// echo "<br />\n";
		// print_object($newly_imported_referentiel);
		// echo "<br />\n";
		// notify( get_string('importdone', 'referentiel') );

        // check for errors before we continue
        if ($this->stoponerror and ($this->importerrors>0)) {
            return false;
        }

		return true;
    }

    /**
     * Return complete file within an array, one item per line
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
     /*
    function readdata($filename) {
    // moodle 1.9
        if (is_readable($filename)) {
            $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            if (ereg("\r", $filearray[0]) AND !ereg("\n", $filearray[0])) {
                return explode("\r", $filearray[0]);
            }
            else {
                return $filearray;
            }
        }
        return false;
    }
    */

    function readdata() {
    // MOODLE 2.0
        if (!empty($this->contents)) {
            // DEBUG
            //echo "<br />DEBUG :: ./mod/referentiel/format.php :: 1389<br />EXIT<br />\n";
            //echo nl2br($this->contents)." NEWLINE<br />\n";


            // $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            // if (ereg("\r", $this->contents) AND !ereg("\n", $this->contents)) {
            if (preg_match("/\r/", $this->contents) AND !preg_match("/\n/", $this->contents)) {
                $content=explode("\r", $this->contents);
            }
            else if (preg_match("/\r\n/", $this->contents)) {
                $content=explode("\r\n", $this->contents);
            }
            else {
                $content=explode("\n", $this->contents);
            }
            return ($content);
        }
        return false;
    }

    /**
     * Parses an array of lines into a students array,
     * where is a newly_imported_referentiel object as defined by
     * readimportedreferentiel().
     *
     * @param array lines array of lines from readdata
     * @return array referentiel object
     */
    function lines_2_certifs($lines) {
	//
        $tline = array();

		if (is_array($lines)){
            foreach ($lines as $line) {
                $line = trim($line);

                if (!empty($line)) {
                    $tline[] = $line;
                }
            }


            // echo "<br />DEBUG 3 : format.php :: ligne 1432:: fonction lines_2_referentiel()<br />\n";
            // print_r($tline);
            // echo "<br />\n";
            // exit;
            if (!empty($tline)) {  // conversion
                $imported_certifs = $this->read_import_certifs($tline);
            }
        }
        else{
            $imported_certifs = $this->read_import_certifs($lines);
        }
        return $imported_certifs ;
    }



    /**
     * Given the data known to define a referentiel in
     * this format, this function converts it into a referentiel
     * object suitable for processing and insertion into Moodle.
     *
     * If your format does not use blank lines to delimit referentiels
     * (e.g. an XML format) you must override 'readreferentiels' too
     * @param $lines mixed data that represents referentiel
     * @return object referentiel object
     */
	function read_import_certifs($lines) {

        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * Override if any post-processing is required
     * @return boolean success
     */
    function importpostprocess() {
        return true;
    }

}


// *********************************************************
// ETUDIANT

class eformat_default { // etudiants

    var $displayerrors = true;
	var $ireferentiel = NULL;	
	var $rreferentiel = NULL;
	var $ref_referentiel = 0;
	var $coursemodule = NULL;	
    var $course = NULL;
    var $filename = '';
    var $importerrors = 0;
    var $stoponerror = true;
    var $context = NULL;
    var $contents = '';
    
// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

// Accessor methods
    /**
     * set the context class variable
     * @param contexte object Moodle context variable
     */
    function setContext( $context ) {
        $this->context = $context;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setContents( $contents ) {
        $this->contents = $contents;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setIReferentiel( $referentiel ) {
        $this->ireferentiel = $referentiel;
    }
    /**
     * set the referentiel
     * @param object referentiel the referentiel_referentiel object
     */
	function setRReferentiel( $referentiel ) {
        $this->rreferentiel = $referentiel;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setRefReferentiel( $id ) {
        $this->ref_referentiel = $id;
    }

    /**
     * set the action 
     * @param string action 
     */
	function setAction( $action ) {
        $this->action = $action;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCourse( $course ) {
        $this->course = $course;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCoursemodule( $cm ) {
        $this->coursemodule = $cm;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setFilename( $filename ) {
        $this->filename = $filename;
    }

    /**
     * set returnpage
     * @param bool stoponerror stops database write if any errors reported
     */
    function setReturnpage( $returnpage ) {
        $this->returnpage = $returnpage;
    }

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    function setStoponerror( $stoponerror ) {
        $this->stoponerror = $stoponerror;
    }
	
    /**
     * set override
     * @param bool override database write 
     */
    function setOverride( $override ) {
        $this->override = $override;
    }
	

/*******************
 * EXPORT FUNCTIONS
 *******************/

    function error( $message, $text='', $name='', $type='' ) {
        if ($type=='import')
            $error = get_string('importerror', 'referentiel');
        else
            $error = get_string('exporterror', 'referentiel');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$error $name</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<i>$message</i>\n";
        echo "</div>";

         $this->importerrors++;
    }


    /** 
     * Provide export functionality for plugin referentiel types
     * Do not override
     * @param name referentiel name
     * @param referentiel object data to export 
     * @param extra mixed any addition format specific data needed
     * @return string the data to append to export or false if error (or unhandled)
     */
    function try_exporting( $name, $referentiel, $extra=null ) {

        // work out the name of format in use
        $formatname = substr( get_class( $this ), strlen( 'eformat_' ));
        $methodname = "export_to_$formatname";

		if (method_exists( $methodname )) {
			if ($data = $methodname( $referentiel, $this, $extra )) {
				return $data;
            }
        }
        return false;
    }

    /**
     * Return the files extension appropriate for this type
     * override if you don't want .txt
     * @return string file extension
     */
    function export_file_extension() {
        return ".txt";
    }

    /**
     * Do any pre-processing that may be required
     * @param boolean success
     */
    function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    function presave_process( $content ) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return boolean success
     */
    function exportprocess() {
        global $CFG;
        // notify( get_string('exportingetudiants', 'referentiel') );
        $count = 0;

        // results are first written into string (and then to a file)
        // so create/initialize the string here
        $expout = "";
        
        // export the item displaying message
        $count++;
        echo "<hr /><p><b>$count</b>. ".$this->rreferentiel->name."</p>";
		$expout .= $this->write_liste_etablissements() . "\n"; // Liste des etablissements
        $expout .= $this->write_liste_etudiants() . "\n"; // on passe l'instance 

        // continue path for following error checks
        $coursemodule = $this->coursemodule;
        $continuepath = "$CFG->wwwroot/mod/referentiel/export_etudiant.php?id=$coursemodule->id"; 

        // did we actually process anything
        if ($count==0) {
           $this->error( get_string('noetudiant', 'referentiel', $continuepath),'','','export' );
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );
       
        // write file
        /*
        // Moodle 1.9
        $filepath = $path."/".$this->filename . $this->export_file_extension();
        if (!$fh=fopen($filepath,"w")) {
            $this->error( get_string('cannotopen','referentiel',$continuepath),$filepath,'','export' );
        }
        if (!fwrite($fh, $expout, strlen($expout) )) {
            $this->error( get_string('cannotwrite','referentiel',$continuepath),$filepath,'','export' );
        }
        fclose($fh);
        */
        // Moodle 2.0
        $fs = get_file_storage();
        // Prepare file record object
        $fileinfo = array(
            'contextid' => $this->context->id, // ID of context
            'component' => 'mod_referentiel',     // usually = table name
            'filearea' => 'scolarite',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => $this->get_export_dir(),  // any path beginning and ending in /
            'filename' => $this->filename.$this->export_file_extension()); // any filename

        // Create file containing text
        $fs->create_file_from_string($fileinfo, $expout);

        return true;
    }

    /**
     * Do an post-processing that may be required
     * @return boolean success
     */
    function exportpostprocess() {
        return true;
    }

    /**
     * convert a single referentiel object into text output in the given
     * format.
     * This must be overriden
     * @param object referentiel referentiel object
     * @return mixed referentiel export text or null if not implemented
     */
    function write_liste_etudiants() {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";
        return NULL;
    }

    /**
     * get directory into which export is going 
     * @return string file path
     */
	function get_export_dir() {
		/*
        // Moodle 1.9
        global $CFG;
        $dirname = get_string('exportfilename', 'referentiel');
        $path =  $this->course->id.'/'.$CFG->moddata.'/'.$dirname; 
        return $path;
        */
        // Moodle 2.0
        return '/';
    }

/***********************
 * IMPORTING FUNCTIONS
 ***********************/


    /** 
     * Import for students type plugins
     * Do not override.
     * @param data mixed The segment of data containing the referentiel
     * @param referentiel object processed (so far) by standard import code if appropriate
     * @param extra mixed any additional format specific data that may be passed by the format
     * @return object referentiel object suitable for save_options() or false if cannot handle
     */
    function try_importing( $data, $students=null, $extra=null ) {

        // work out what format we are using
        $formatname = substr( get_class( $this ), strlen('eformat_'));
        $methodname = "import_from_$formatname";

        // loop through installed referentieltypes checking for
        // function to handle this referentiel
        if (method_exists( $methodname)) {
        	if ($students = $methodname( $data, $students, $this, $extra )) {
            	return $students;
            }
        }
        return false;   
    }

    /**
     * Perform any required pre-processing
     * @return boolean success
     */
    function importpreprocess() {
        return true;
    }

    /**
     * Process the file
     * This method should not normally be overidden
     * @return boolean success
     */
    function importprocess() {

       	// reset the timer in case file upload was slow
       	@set_time_limit();

       	// STAGE 1: Parse the file
       	// notify( get_string('parsing', 'referentiel') );
         
		// if (! $lines = $this->readdata($this->filename)) {
		if (! $lines = $this->readdata($this->contents)) {
            notify( get_string('cannotread', 'referentiel') );
            return false;
        }
		$newly_imported_students = new stdClass();
        if (! $newly_imported_students = $this->lines_2_students($lines)) {   // Extract the students
            notify( get_string('noinfile', 'referentiel') );
            return false;
        }

        // STAGE 2: Write data to database
		// echo "<br />\n";
		// print_object($newly_imported_referentiel);
		// echo "<br />\n";
		// notify( get_string('importdone', 'referentiel') );
		
        // check for errors before we continue
        if ($this->stoponerror and ($this->importerrors>0)) {
            return false;
        }
		
		return true;
    }

    /**
     * Return complete file within an array, one item per line
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
     /*
    function readdata($filename) {
    // moodle 1.9
        if (is_readable($filename)) {
            $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            if (ereg("\r", $filearray[0]) AND !ereg("\n", $filearray[0])) {
                return explode("\r", $filearray[0]);
            }
            else {
                return $filearray;
            }
        }
        return false;
    }
    */
    
    function readdata() {
    // MOODLE 2.0
        if (!empty($this->contents)) {
            // DEBUG
            // echo "<br />DEBUG :: ./mod/referentiel/format.php :: 439<br />EXIT<br />\n";
            // echo nl2br($this->contents)." NEWLINE\n";


            // $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            // if (ereg("\r", $this->contents) AND !ereg("\n", $this->contents)) {
            if (preg_match("/\r/", $this->contents) AND !preg_match("/\n/", $this->contents)) {
                $content=explode("\r", $this->contents);
            }
            else if (preg_match("/\r\n/", $this->contents)) {
                $content=explode("\r\n", $this->contents);
            }
            else {
                $content=explode("\n", $this->contents);
            }

            // print_r($content);
            // exit;
            return ($content);
        }
        return false;
    }

    /**
     * Parses an array of lines into a students array, 
     * where is a newly_imported_referentiel object as defined by 
     * readimportedreferentiel().
     *
     * @param array lines array of lines from readdata
     * @return array referentiel object
     */
    function lines_2_students($lines) {
	// 
        $tline = array();
		
		if (is_array($lines)){
            foreach ($lines as $line) {
                $line = trim($line);

                if (!empty($line)) {
                    $tline[] = $line;
                }
            }


            // echo "<br />DEBUG 3 : format.php :: ligne 453 :: fonction lines_2_referentiel()<br />\n";
            // print_r($treferentiel);
            // echo "<br />\n";
            // exit;
            if (!empty($tline)) {  // conversion
                $imported_students = $this->read_import_students($tline);
            }
        }
        else{
            $imported_students = $this->read_import_students($lines);
        }
        return $imported_students ;
    }


    /**
     * return an "empty" etablissement
     * Somewhere to specify referentiel parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default referentiel
	*/      
    function defaultetablissement() {
	// retourne un objet import_etablissement qui mime l'objet referentiel
        $import_etablissement = new stdClass();
		$import_etablissement->num_etablissement="";
		$import_etablissement->nom_etablissement="";
		$import_etablissement->adresse_etablissement="";
		$import_etablissement->logo_etablissement="";
    	$import_etablissement->id = 0;
        // this option in case the etablissement types class wants
        // to know where the data came from
        $import_etablissement->export_process = true;
        $import_etablissement->import_process = true;
        return $import_etablissement;
    }

    /**
     * return an "empty" etudiant
     * Somewhere to specify referentiel parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default referentiel
	*/      
    function defaultetudiant() {
	// retourne un objet import_etudiant qui mime l'objet referentiel
        $import_etudiant = new stdClass();
    	$import_etudiant->id = 0;
    	$import_etudiant->userid = 0;		
		$import_etudiant->num_etudiant="";
		$import_etudiant->ddn_etudiant="";
		$import_etudiant->lieu_naissance="";		
		$import_etudiant->departement_naissance="";
		$import_etudiant->adresse_etudiant="";
		$import_etudiant->ref_etablissement=0;		
        // this option in case the etudiant types class wants
        // to know where the data came from
        $import_etudiant->export_process = true;
        $import_etudiant->import_process = true;
        return $import_etudiant;
    }


    /**
     * Given the data known to define a referentiel in 
     * this format, this function converts it into a referentiel 
     * object suitable for processing and insertion into Moodle.
     *
     * If your format does not use blank lines to delimit referentiels
     * (e.g. an XML format) you must override 'readreferentiels' too
     * @param $lines mixed data that represents referentiel
     * @return object referentiel object
     */
	function read_import_students($lines) {

        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * Override if any post-processing is required
     * @return boolean success
     */
    function importpostprocess() {
        return true;
    }

    /**
     * Import an image file encoded in base64 format
     * @param string path path (in course data) to store picture
     * @param string base64 encoded picture
     * @return string filename (nb. collisions are handled)
     */
    function importimagefile( $path, $base64 ) {
        global $CFG;

        // all this to get the destination directory
        // and filename!
        $fullpath = "{$CFG->dataroot}/{$this->course->id}/$path";
        $path_parts = pathinfo( $fullpath );
        $destination = $path_parts['dirname'];
        $file = clean_filename( $path_parts['basename'] );

        // check if path exists
        check_dir_exists($destination, true, true );

        // detect and fix any filename collision - get unique filename
        $newfiles = resolve_filename_collisions( $destination, array($file) );        
        $newfile = $newfiles[0];

        // convert and save file contents
        if (!$content = base64_decode( $base64 )) {
            return '';
        }
        $newfullpath = "$destination/$newfile";
        if (!$fh = fopen( $newfullpath, 'w' )) {
            return '';
        }
        if (!fwrite( $fh, $content )) {
            return '';
        }
        fclose( $fh );

        // return the (possibly) new filename
        //$newfile = ereg_replace("{$CFG->dataroot}/{$this->course->id}/", '',$newfullpath);
        $newfile = preg_replace("/".$CFG->dataroot."\/".$this->course->id."\//", '',$newfullpath);
        return $newfile;
    }

}


/**************************************************************************

TACHES

***************************************************************************/
class tformat_default {

    var $displayerrors = true;
	var $ireferentiel = NULL; // instance
	var $rreferentiel = NULL; // referentiel
	var $ref_referentiel = 0;
	var $coursemodule = NULL;	
    var $course = NULL;
    var $filename = '';
    var $importerrors = 0;
    var $stoponerror = true;
    var $contents ='';
    var $context = NULL;

// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

// Accessor methods
    /**
     * set the context class variable
     * @param contexte object Moodle context variable
     */
    function setContext( $context ) {
        $this->context = $context;
    }

    /**
     * set the context class variable
     * @param contexte object Moodle context variable
     */
    function setContents( $contents ) {
        $this->contents = $contents;
    }


    /**
     * set the referentiel
     * @param object referentiel the referentiel instance object
     */
	function setIReferentiel( $referentiel_instance ) {
        $this->ireferentiel = $referentiel_instance;
    }

	/**
     * set the referentiel instance ID
     * @param object referentiel the referentiel object
     */
	function setRefInstance( $id ) {
        $this->ref_instance = $id;
    }

    /**
     * set the referentiel referentiel object
     * @param object referentiel the referentiel referentiel object
     */
	function setRReferentiel( $referentiel_referentiel ) {
        $this->rreferentiel = $referentiel_referentiel;
    }

    /**
     * set the referentiel referentiel ID
     * @param object referentiel the referentiel object
     */
	function setRefReferentiel( $id ) {
        $this->ref_referentiel = $id;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCourse( $course ) {
        $this->course = $course;
    }

    /**
     * set the CourseModule class variable
     * @param course object Moodle course variable
     */
    function setCoursemodule( $cm ) {
        $this->coursemodule = $cm;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setFilename( $filename ) {
        $this->filename = $filename;
    }

    /**
     * set returnpage
     * @param bool stoponerror stops database write if any errors reported
     */
    function setReturnpage( $returnpage ) {
        $this->returnpage = $returnpage;
    }

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    function setStoponerror( $stoponerror ) {
        $this->stoponerror = $stoponerror;
    }
	

    function error( $message, $text='', $name='', $type='' ) {
        if ($type=='import')
            $error = get_string('importerror', 'referentiel');
        else
            $error = get_string('exporterror', 'referentiel');
            
        echo "<div class=\"importerror\">\n";
        echo "<strong>$error $name</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<i>$message</i>\n";
        echo "</div>";

         $this->importerrors++;
    }

/*******************
 * EXPORT FUNCTIONS
 *******************/

    /** 
     * Provide export functionality for plugin referentiel types
     * Do not override
     * @param name referentiel name
     * @param referentiel object data to export 
     * @param extra mixed any addition format specific data needed
     * @return string the data to append to export or false if error (or unhandled)
     */
    function try_exporting( $name, $referentiel, $extra=null ) {

        // work out the name of format in use
        $formatname = substr( get_class( $this ), strlen( 'tformat_' ));
        $methodname = "export_to_$formatname";

		if (method_exists( $methodname )) {
			if ($data = $methodname( $referentiel, $this, $extra )) {
				return $data;
            }
        }
        return false;
    }

    /**
     * Return the files extension appropriate for this type
     * override if you don't want .txt
     * @return string file extension
     */
    function export_file_extension() {
        return ".txt";
    }

    /**
     * Do any pre-processing that may be required
     * @param boolean success
     */
    function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    function presave_process( $content ) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return boolean success
     */
    function exportprocess() {
        global $CFG;
        // notify( get_string('exportingtasks', 'referentiel') );
        $count = 0;

        // results are first written into string (and then to a file)
        // so create/initialize the string here
        $expout = "";
        
        // export the item displaying message
        $count++;
        echo "<hr /><p><b>$count</b>. ".$this->ireferentiel->name."</p>";
		
        $expout .= $this->write_liste_tasks() . "\n";

        // continue path for following error checks
        $coursemodule = $this->coursemodule;
        $continuepath = "$CFG->wwwroot/mod/referentiel/export_task.php?id=$coursemodule->id"; 

        // did we actually process anything
        if ($count==0) {
           $this->error( get_string('notask', 'referentiel', $continuepath),'','','export' );
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );
       
        // write file
        // Moodle 1.9
        /*
        $filepath = $path."/".$this->filename . $this->export_file_extension();
        if (!$fh=fopen($filepath,"w")) {
            $this->error( get_string('cannotopen','referentiel',$continuepath),$filepath,'','export' );
        }
        if (!fwrite($fh, $expout, strlen($expout) )) {
            $this->error( get_string('cannotwrite','referentiel',$continuepath),$filepath,'','export' );
        }
        fclose($fh);
        */
                // Moodle 2.0
        $fs = get_file_storage();
        // Prepare file record object
        $fileinfo = array(
            'contextid' => $this->context->id, // ID of context
            'component' => 'mod_referentiel',     // usually = table name
            'filearea' => 'task',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => $this->get_export_dir(),  // any path beginning and ending in /
            'filename' => $this->filename.$this->export_file_extension()); // any filename

        // Create file containing text
        $fs->create_file_from_string($fileinfo, $expout);

        return true;
    }

    /**
     * Do an post-processing that may be required
     * @return boolean success
     */
    function exportpostprocess() {
        return true;
    }

    /**
     * convert a single referentiel object into text output in the given
     * format.
     * This must be overriden
     * @param object referentiel referentiel object
     * @return mixed referentiel export text or null if not implemented
     */
    function write_liste_tasks() {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";
        return NULL;
    }

    /**
     * get directory into which export is going 
     * @return string file path
     */
    function get_export_dir() {
		// Moodle 1.9
        /*
        global $CFG;
        $dirname = get_string('exportfilename', 'referentiel');
        $path =  $this->course->id.'/'.$CFG->moddata.'/'.$dirname; 
        return $path;
        */
        return '/';
    }



/***********************
 * IMPORTING FUNCTIONS
 ***********************/


    /** 
     * Import for referentieltype plugins
     * Do not override.
     * @param data mixed The segment of data containing the referentiel
     * @param referentiel object processed (so far) by standard import code if appropriate
     * @param extra mixed any additional format specific data that may be passed by the format
     * @return object referentiel object suitable for save_options() or false if cannot handle
     */
    function try_importing( $data, $tasks=null, $extra=null ) {

        // work out what format we are using
        $formatname = substr( get_class( $this ), strlen('tformat_'));
        $methodname = "import_from_$formatname";

        // loop through installed referentieltypes checking for
        // function to handle this referentiel
        if (method_exists( $methodname)) {
        	if ($tasks = $methodname( $data, $tasks, $this, $extra )) {
            	return $tasks;
            }
        }
        return false;   
    }

    /**
     * Perform any required pre-processing
     * @return boolean success
     */
    function importpreprocess() {
        return true;
    }

    /**
     * Process the file
     * This method should not normally be overidden
     * @return boolean success
     */
    function importprocess() {

       	// reset the timer in case file upload was slow
       	@set_time_limit();

       	// STAGE 1: Parse the file
       	// notify( get_string('parsing', 'referentiel') );
         
		// Moodle 1.9
        //if (! $lines = $this->readdata($this->filename)) {
        // Moodle 2.0
        if (! $lines = $this->readdata()) {
            notify( get_string('cannotread', 'referentiel') );
            return false;
        }

		$newly_imported_tasks = new stdClass();
		$newly_imported_tasks = $this->lines_2_tasks($lines);
        if (empty($newly_imported_tasks)) {   // Extract the referentiel
            notify( get_string('noinfile', 'referentiel') );
            return false;
        }

        // STAGE 2: Write data to database
		// echo "<br />\n";
		// print_object($newly_imported_tasks);
		// echo "<br />\n";
		// notify( get_string('importdone', 'referentiel') );
		
        // check for errors before we continue
        if ($this->stoponerror and ($this->importerrors>0)) {
            return false;
        }
		
		return true;
    }

    /**
     * Return complete file within an array, one item per line
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
     /*
    function readdata($filename) {
    // Moodle 1.9
        if (is_readable($filename)) {
            $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            if (ereg("\r", $filearray[0]) AND !ereg("\n", $filearray[0])) {
                return explode("\r", $filearray[0]);
            }
            else {
                return $filearray;
            }
        }
        return false;
    }
    */

    function readdata() {
    // MOODLE 2.0
        if (!empty($this->contents)) {
            // DEBUG
            // echo "<br />DEBUG :: ./mod/referentiel/format.php :: 439<br />EXIT<br />\n";
            // echo nl2br($this->contents)." NEWLINE\n";


            // $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            //if (ereg("\r", $this->contents) AND !ereg("\n", $this->contents)) {
            if (preg_match("/\r/", $this->contents) AND !preg_match("/\n/", $this->contents)) {
                $content=explode("\r", $this->contents);
            }
            else if (preg_match("/\r\n/", $this->contents)) {
                $content=explode("\r\n", $this->contents);
            }
            else {
                $content=explode("\n", $this->contents);
            }

            // print_r($content);
            // exit;
            return ($content);
        }
        return false;
    }

    /**
     * Parses an array of lines into a referentiel, 
     * where is a $newly_imported_tasks object as defined by 
     * readimportedtask().
     *
     * @param array lines array of lines from readdata
     * @return array referentiel object
     */
    function lines_2_tasks($lines) {
	// 
        $tline = array();
		
        foreach ($lines as $line) {
            $line = trim($line);
			
            if (!empty($line)) {
                $tline[] = $line;
            }
        }
		// echo "<br />DEBUG 3 : format.php :: ligne 453 :: fonction lines_2_tasks()<br />\n";
		// print_r($treferentiel);
		// echo "<br />\n";
		// exit;

        if (!empty($tline)) {  // conversion
            return($this->read_import_tasks($tline));
        }
        else{
            return NULL;
        }
    }


    /**
     * return an "empty" referentiel
     * Somewhere to specify referentiel parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default referentiel
	*/      
    function defaultreferentiel_reduit() {
	// retourne un objet import_referentiel_reduit qui mime l'objet referentiel
        $import_referentiel_reduit = new stdClass();
		$import_referentiel_reduit->name="";
		$import_referentiel_reduit->code_referentiel_reduit="";
		$import_referentiel_reduit->description_referentiel_reduit="";
		$import_referentiel_reduit->cle_referentiel="";
		$import_referentiel_reduit->liste_codes_competence="";
		/*		
		$import_referentiel_reduit->url_referentiel_reduit="";
		$import_referentiel_reduit->seuil_certificat="";
    	$import_referentiel_reduit->timemodified = time();
		$import_referentiel_reduit->nb_domains="";
		$import_referentiel_reduit->liste_codes_competence="";
		$import_referentiel_reduit->local=0;
    	$import_referentiel_reduit->id = 0;
		*/
        // this option in case the referentieltypes class wants
        // to know where the data came from
        $import_referentiel_reduit->export_process = false;
        $import_referentiel_reduit->import_process = true;
        return $import_referentiel_reduit;
    }

	
    function defaulttask() {
        // retourne un objet task
		/*
CREATE TABLE mdl_referentiel_task (
  id bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  type_task varchar(80) NOT NULL DEFAULT '',
  description_task text NOT NULL,
  competences_task text NOT NULL,
  criteres_evaluation text NOT NULL,
  ref_instance bigint(10) unsigned NOT NULL DEFAULT '0',
  ref_referentiel bigint(10) unsigned NOT NULL DEFAULT '0',
  ref_course bigint(10) unsigned NOT NULL DEFAULT '0',
  auteurid bigint(10) unsigned NOT NULL,
  date_creation bigint(10) unsigned NOT NULL DEFAULT '0',
  date_modif bigint(10) unsigned NOT NULL DEFAULT '0',
  date_debut bigint(10) unsigned NOT NULL DEFAULT '0',
  date_fin bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='task';
		
		*/
        $task = new stdClass();
    	$task->id = 0;
		$task->type_task="";
		$task->description_task="";
		$task->competences_task="";
		$task->criteres_evaluation="";
		$task->ref_instance=0;
		$task->ref_referentiel=0;
		$task->ref_course=0;
		$task->auteurid=0;
		$task->date_creation=0;
		$task->date_modif=0;
		$task->date_debut=0;
		$task->date_fin=0;
		
        return $task;
    }

    function defaultconsigne() {
	// retourne un objet consigne	
	/*

CREATE TABLE mdl_referentiel_consigne (
  id bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  type_consigne varchar(20) NOT NULL DEFAULT '',
  description_consigne text NOT NULL,
  url_consigne varchar(255) NOT NULL DEFAULT '',
  ref_task bigint(10) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='consigne';	*/
        $consigne = new stdClass();
    	$consigne->id = 0;
		$consigne->type_consigne="";
		$consigne->description_consigne="";
		$consigne->url_consigne="";
		$consigne->ref_task=0;
        return $consigne;
    }


    /**
     * Given the data known to define a referentiel in 
     * this format, this function converts it into a referentiel 
     * object suitable for processing and insertion into Moodle.
     *
     * If your format does not use blank lines to delimit referentiels
     * (e.g. an XML format) you must override 'readreferentiels' too
     * @param $lines mixed data that represents referentiel
     * @return object referentiel object
     */
	function read_import_tasks($lines) {

        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * Override if any post-processing is required
     * @return boolean success
     */
    function importpostprocess() {
        return true;
    }

    /**
     * Import an image file encoded in base64 format
     * @param string path path (in course data) to store picture
     * @param string base64 encoded picture
     * @return string filename (nb. collisions are handled)
     */
    function importimagefile( $path, $base64 ) {
        global $CFG;

        // all this to get the destination directory
        // and filename!
        $fullpath = "{$CFG->dataroot}/{$this->course->id}/$path";
        $path_parts = pathinfo( $fullpath );
        $destination = $path_parts['dirname'];
        $file = clean_filename( $path_parts['basename'] );

        // check if path exists
        check_dir_exists($destination, true, true );

        // detect and fix any filename collision - get unique filename
        $newfiles = resolve_filename_collisions( $destination, array($file) );        
        $newfile = $newfiles[0];

        // convert and save file contents
        if (!$content = base64_decode( $base64 )) {
            return '';
        }
        $newfullpath = "$destination/$newfile";
        if (!$fh = fopen( $newfullpath, 'w' )) {
            return '';
        }
        if (!fwrite( $fh, $content )) {
            return '';
        }
        fclose( $fh );

        // return the (possibly) new filename
        //$newfile = ereg_replace("{$CFG->dataroot}/{$this->course->id}/", '',$newfullpath);
        $newfile = preg_replace("/".$CFG->dataroot."\/".$this->course->id."\//", '',$newfullpath);

        return $newfile;
    }


}


// *********************************************************
// PEDAGOGIE

class pformat_default { // pedagos

    var $displayerrors = true;
	var $ireferentiel = NULL;	
	var $rreferentiel = NULL;
	var $ref_referentiel = 0;
	var $coursemodule = NULL;	
    var $course = NULL;
    var $filename = '';
    var $importerrors = 0;
    var $stoponerror = true;
    var $context = NULL;
    var $contents = '';
    
// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

// Accessor methods
    /**
     * set the context class variable
     * @param contexte object Moodle context variable
     */
    function setContext( $context ) {
        $this->context = $context;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setContents( $contents ) {
        $this->contents = $contents;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel instance object
     */
	function setIReferentiel( $referentiel ) {
        $this->ireferentiel = $referentiel;
    }
    /**
     * set the referentiel
     * @param object referentiel the referentiel_referentiel object
     */
	function setRReferentiel( $referentiel ) {
        $this->rreferentiel = $referentiel;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setRefReferentiel( $id ) {
        $this->ref_referentiel = $id;
    }

    /**
     * set the action 
     * @param string action 
     */
	function setAction( $action ) {
        $this->action = $action;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCourse( $course ) {
        $this->course = $course;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCoursemodule( $cm ) {
        $this->coursemodule = $cm;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setFilename( $filename ) {
        $this->filename = $filename;
    }

    /**
     * set returnpage
     * @param bool stoponerror stops database write if any errors reported
     */
    function setReturnpage( $returnpage ) {
        $this->returnpage = $returnpage;
    }

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    function setStoponerror( $stoponerror ) {
        $this->stoponerror = $stoponerror;
    }
	
    /**
     * set override
     * @param bool override database write 
     */
    function setOverride( $override ) {
        $this->override = $override;
    }
	

/*******************
 * EXPORT FUNCTIONS
 *******************/
    function error( $message, $text='', $name='', $type='' ) {
        if ($type=='import')
            $error = get_string('importerror', 'referentiel');
        else
            $error = get_string('exporterror', 'referentiel');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$error $name</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<i>$message</i>\n";
        echo "</div>";

         $this->importerrors++;
    }


    /** 
     * Provide export functionality for plugin referentiel types
     * Do not override
     * @param name referentiel name
     * @param referentiel object data to export 
     * @param extra mixed any addition format specific data needed
     * @return string the data to append to export or false if error (or unhandled)
     */
    function try_exporting( $name, $referentiel, $extra=null ) {

        // work out the name of format in use
        $formatname = substr( get_class( $this ), strlen( 'pformat_' ));
        $methodname = "export_to_$formatname";

		if (method_exists( $methodname )) {
			if ($data = $methodname( $referentiel, $this, $extra )) {
				return $data;
            }
        }
        return false;
    }

    /**
     * Return the files extension appropriate for this type
     * override if you don't want .txt
     * @return string file extension
     */
    function export_file_extension() {
        return ".txt";
    }

    /**
     * Do any pre-processing that may be required
     * @param boolean success
     */
    function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    function presave_process( $content ) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return boolean success
     */
    function exportprocess() {
        global $CFG;

        // notify( get_string('exportingpedagos', 'referentiel') );
        $count = 0;

        // results are first written into string (and then to a file)
        // so create/initialize the string here
        $expout = "";
        
        // export the item displaying message
        $count++;
        echo "<hr /><p><b>$count</b>. ".$this->rreferentiel->name."</p>";
        $expout .= $this->write_liste_pedagos() . "\n"; // on passe l'instance 

        // continue path for following error checks
        $coursemodule = $this->coursemodule;
        $continuepath = "$CFG->wwwroot/mod/referentiel/export_pedago.php?id=$coursemodule->id"; 

        // did we actually process anything
        if ($count==0) {
           $this->error( get_string('nopedago', 'referentiel', $continuepath),'','','export'  );
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );
       
        // write file
        /*
        $filepath = $path."/".$this->filename . $this->export_file_extension();
        if (!$fh=fopen($filepath,"w")) {
            $this->error( get_string('cannotopen', 'referentiel' ,$continuepath), $filepath, '','export');
        }
        if (!fwrite($fh, $expout, strlen($expout) )) {
            $this->error( get_string('cannotwrite', 'referentiel', $continuepath), $filepath, '', 'export');
        }
        fclose($fh);
        */
        // Moodle 2.0
        $fs = get_file_storage();
        // Prepare file record object
        $fileinfo = array(
            'contextid' => $this->context->id, // ID of context
            'component' => 'mod_referentiel',     // usually = table name
            'filearea' => 'pedagogie',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => $this->get_export_dir(),  // any path beginning and ending in /
            'filename' => $this->filename.$this->export_file_extension()); // any filename

        // Create file containing text
        $fs->create_file_from_string($fileinfo, $expout);

        return true;
    }

    /**
     * Do an post-processing that may be required
     * @return boolean success
     */
    function exportpostprocess() {
        return true;
    }

    /**
     * convert a single referentiel object into text output in the given
     * format.
     * This must be overriden
     * @param object referentiel referentiel object
     * @return mixed referentiel export text or null if not implemented
     */
    function write_liste_pedagos() {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";
        return NULL;
    }

    /**
     * get directory into which export is going 
     * @return string file path
     */
	function get_export_dir() {
	// Moodle 1.9
	/*
		global $CFG;
        $dirname = get_string('exportfilename', 'referentiel');
        $path =  $this->course->id.'/'.$CFG->moddata.'/'.$dirname; 
        return $path;
        */
        return '/';
    }

/***********************
 * IMPORTING FUNCTIONS
 ***********************/


    /** 
     * Import for students type plugins
     * Do not override.
     * @param data mixed The segment of data containing the referentiel
     * @param referentiel object processed (so far) by standard import code if appropriate
     * @param extra mixed any additional format specific data that may be passed by the format
     * @return object referentiel object suitable for save_options() or false if cannot handle
     */
    function try_importing( $data, $pedagos=null, $extra=null ) {

        // work out what format we are using
        $formatname = substr( get_class( $this ), strlen('pformat_'));
        $methodname = "import_from_$formatname";

        // loop through installed referentieltypes checking for
        // function to handle this referentiel
        if (method_exists( $methodname)) {
        	if ($pedagos = $methodname( $data, $pedagos, $this, $extra )) {
            	return $pedagos;
            }
        }
        return false;   
    }

    /**
     * Perform any required pre-processing
     * @return boolean success
     */
    function importpreprocess() {
        return true;
    }

    /**
     * Process the file
     * This method should not normally be overidden
     * @return boolean success
     */
    function importprocess() {

       	// reset the timer in case file upload was slow
       	@set_time_limit();

       	// STAGE 1: Parse the file
       	// notify( get_string('parsing', 'referentiel') );
         
		// Moodle 1.9
        //if (! $lines = $this->readdata($this->filename)) {
        // Moodle 2.0
        if (! $lines = $this->readdata()) {
            notify( get_string('cannotread', 'referentiel') );
            return false;
        }

		$newly_imported_pedagos = new stdClass();
        if (! $newly_imported_pedagos = $this->lines_2_pedagos($lines)) {   // Extract the pedagos
            notify( get_string('noinfile', 'referentiel') );
            return false;
        }

        // STAGE 2: Write data to database
		// echo "<br />\n";
		// print_object($newly_imported_referentiel);
		// echo "<br />\n";
		// notify( get_string('importdone', 'referentiel') );
		
        // check for errors before we continue
        if ($this->stoponerror and ($this->importerrors>0)) {
            return false;
        }
		
		return true;
    }

    /**
     * Return complete file within an array, one item per line
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
    /*
    function readdata($filename) {
    // Moodle 1.9
        if (is_readable($filename)) {
            $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            if (ereg("\r", $filearray[0]) AND !ereg("\n", $filearray[0])) {
                return explode("\r", $filearray[0]);
            }
            else {
                return $filearray;
            }
        }
        return false;
    }
    */
    
    function readdata() {
    // MOODLE 2.0
        if (!empty($this->contents)) {
            // DEBUG
            // echo "<br />DEBUG :: ./mod/referentiel/format.php :: 439<br />EXIT<br />\n";
            // echo nl2br($this->contents)." NEWLINE\n";


            // $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            //if (ereg("\r", $this->contents) AND !ereg("\n", $this->contents)) {
            if (preg_match("/\r/", $this->contents) AND !preg_match("/\n/", $this->contents)) {
                $content=explode("\r", $this->contents);
            }
            else if (preg_match("/\r\n/", $this->contents)) {
                $content=explode("\r\n", $this->contents);
            }
            else {
                $content=explode("\n", $this->contents);
            }

            // print_r($content);
            // exit;
            return ($content);
        }
        return false;
    }

    /**
     * Parses an array of lines into a pedagos array, 
     * where is a newly_imported_referentiel object as defined by 
     * readimportedreferentiel().
     *
     * @param array lines array of lines from readdata
     * @return array referentiel object
     */
    function lines_2_pedagos($lines) {
	// 
        $tline = array();
		if (is_array($lines)){
            foreach ($lines as $line) {
                $line = trim($line);

                if (!empty($line)) {
                    $tline[] = $line;
                }
            }
    		// echo "<br />DEBUG 3 : format.php :: ligne 453 :: fonction lines_2_referentiel()<br />\n";
	       	// print_r($treferentiel);
    		// echo "<br />\n";
	       	// exit;
            if (!empty($tline)) {  // conversion
                $imported_pedagos = $this->read_import_pedagos($tline);
            }
        }
        else{
            $imported_pedagos = $this->read_import_pedagos($lines);
        }
        return $imported_pedagos ;
    }



    /**
     * return an "empty" pedago
     * Somewhere to specify referentiel parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default referentiel
	*/      
    function defaultpedago() {
	// retourne un objet import_pedago qui mime l'objet referentiel
        $import_pedago = new stdClass();
    	$import_pedago->id = 0;
    	$import_pedago->userid = 0;		
		$import_pedago->datedeb="";
		$import_pedago->datefin="";
		$import_pedago->promotion="";		
		$import_pedago->formation="";
		$import_pedago->pedagogie="";
		$import_pedago->site="";
		$import_pedago->refrefid=0;		
        // this option in case the pedago types class wants
        // to know where the data came from
        $import_pedago->export_process = true;
        $import_pedago->import_process = true;
        return $import_pedago;
    }


    /**
     * Given the data known to define a referentiel in 
     * this format, this function converts it into a referentiel 
     * object suitable for processing and insertion into Moodle.
     *
     * If your format does not use blank lines to delimit referentiels
     * (e.g. an XML format) you must override 'readreferentiels' too
     * @param $lines mixed data that represents referentiel
     * @return object referentiel object
     */
	function read_import_pedagos($lines) {

        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * Override if any post-processing is required
     * @return boolean success
     */
    function importpostprocess() {
        return true;
    }

    /**
     * Import an image file encoded in base64 format
     * @param string path path (in course data) to store picture
     * @param string base64 encoded picture
     * @return string filename (nb. collisions are handled)
     */
    function importimagefile( $path, $base64 ) {
        global $CFG;
// A REPRENDRE POUR MOODLE 2


// Moodle 1.9
        // and filename!
        $fullpath = "{$CFG->dataroot}/{$this->course->id}/$path";
        $path_parts = pathinfo( $fullpath );
        $destination = $path_parts['dirname'];
        $file = clean_filename( $path_parts['basename'] );

        // check if path exists
        check_dir_exists($destination, true, true );

        // detect and fix any filename collision - get unique filename
        $newfiles = resolve_filename_collisions( $destination, array($file) );        
        $newfile = $newfiles[0];

        // convert and save file contents
        if (!$content = base64_decode( $base64 )) {
            return '';
        }
        $newfullpath = "$destination/$newfile";
        if (!$fh = fopen( $newfullpath, 'w' )) {
            return '';
        }
        if (!fwrite( $fh, $content )) {
            return '';
        }
        fclose( $fh );

        // return the (possibly) new filename
        //$newfile = ereg_replace("{$CFG->dataroot}/{$this->course->id}/", '',$newfullpath);
        $newfile = preg_replace("/".$CFG->dataroot."\/".$this->course->id."\//", '',$newfullpath);

        return $newfile;
    }

}

// Archive zip format
class zformat_default {

    var $export_documents=0;
    var $records_certificats = NULL; // certoficates to export
    var $displayerrors = true;
	var $rinstance = NULL; // instance de referentiel
	var $rreferentiel = NULL; // referentiel_referentiel
    var $coursemodule = NULL;
    var $course = NULL;
    var $context = NULL;
    var $filename = '';
    var $importerrors = 0;
    var $stoponerror = true;
	var $override = false;
	var $returnpage = "";
	var $new_referentiel_id = ""; // id d'un referentiel_referentiel

    var $format_condense=0;
    var $export_pedagos=0;

    var $user_creator=0;      // celui qui cree l'archive
    var $user_filtre=0;  // celui pour lequel est cree l'archive
    var $records_users=NULL; // liste des utilisateurs Ã  archiver

    var $t_instances= array(); // liste des instances en relation avec l'occurrence referentiel
    var $t_etablissements= array(); // liste des etablissements enregistrÃ©s

// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

// Accessor methods
    /**
     * set the context class variable
     * @param contexte object Moodle context variable
     */
    function setContext( $context ) {
        $this->context = $context;
    }

   /**
     * set the records to exports
     * @param object records objets
     */
  	function setExportDocuments($export_documents){
        $this->export_documents=$export_documents;
    }

   /**
     * set the records to exports
     * @param object records objets
     */
  	function setRUsers($records_users){
        $this->records_users=$records_users;
    }

   /**
     * set the records to exports
     * @param object records objets
     */
  	function setRCertificats($records_certificats ){
        $this->records_certificats=$records_certificats;
    }

    /**
     * set the userid
     * @param int
     */
    function setUserCreator( $userid){
        $this->user_creator = $userid;
    }

    /**
     * set the userid
     * @param int
     */
    function setUserFiltre( $userid){
        $this->user_filtre = $userid;
    }

    /**
     * set the export_pedagos
     * @param int
     */
	function setExportPedago($export_pedagos){
        $this->export_pedagos = $export_pedagos;
    }

    /**
     * set the records to exports
     * @param object records objets
     */
  	function setRCFormat($format_condense ){
        $this->format_condense =$format_condense ;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setIReferentiel( $referentiel ) {
        $this->rinstance = $referentiel;
    }

	    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setRReferentiel( $referentiel_referentiel ) {
        $this->rreferentiel = $referentiel_referentiel;
    }


    /**
     * set the referentiel
     * @param id referentiel the referentiel referentiel id
     */
	function setReferentielId( $id ) {
        $this->new_referentiel_id = $id;
    }

    /**
     * set the action
     * @param string action
     */
	function setAction( $action ) {
        $this->action = $action;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCourse( $course ) {
        $this->course = $course;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCoursemodule( $cm ) {
        $this->coursemodule = $cm;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setFilename( $filename ) {
        $this->filename = $filename;
    }

    /**
     * set returnpage
     * @param bool stoponerror stops database write if any errors reported
     */
    function setReturnpage( $returnpage ) {
        $this->returnpage = $returnpage;
    }

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    function setStoponerror( $stoponerror ) {
        $this->stoponerror = $stoponerror;
    }

    /**
     * set override
     * @param bool override database write
     */
    function setOverride( $override ) {
        $this->override = $override;
    }

    /**
     * set newinstance
     * @param bool newinstance database write
     */
    function setNewinstance( $newinstance ){   // en realite new occurrence
        $this->newinstance = $newinstance;
    }


/*******************
 * EXPORT FUNCTIONS
 *******************/

  	// ----------------------------------
	function m_special_case($s, $cap=true, $encoding = 'utf-8'){
		if ($s){
			mb_internal_encoding($encoding);
            mb_regex_encoding($encoding);
            //echo "<br /> 2902 :: $s\n";
			$pattern=    array('Ä', 'Â', 'à','â','ä', 'ç', 'É', 'Ê', 'Ë', 'Ê', 'é','è','ë','ê', 'Ï', 'Î', 'ï','î', 'Ö', 'Ô', 'ö','ô','ø', 'Ü', 'Û', 'ù','ü','û' ) ;
			$replacement=array('A', 'A', 'a','a','a', 'c', 'E', 'E', 'E', 'E', 'e','e','e','e', 'I', 'I', 'i','i', 'O', 'O', 'o','o','o', 'U', 'U', 'u','u','u' ) ;
            for ($i=0; $i<sizeof($pattern); $i++) {
				$s= mb_ereg_replace($pattern[$i], $replacement[$i] , $s);
			}
			//echo "<br />  $s\n";
			if ($cap) $s= mb_strtoupper($s);
			else $s= mb_convert_case($s, MB_CASE_TITLE, $encoding);
            //echo "<br />  $s\n";
		}
		return  $s;
	}

    /**
     * Provide export functionality for plugin referentiel types
     * Do not override
     * @param name referentiel name
     * @param referentiel object data to export
     * @param extra mixed any addition format specific data needed
     * @return string the data to append to export or false if error (or unhandled)
     */
    function try_exporting( $name, $referentiel, $extra=null ) {

        // work out the name of format in use
        $formatname = substr( get_class( $this ), strlen( 'zformat_' ));
        $methodname = "export_to_$formatname";

		if (method_exists( $methodname )) {
			if ($data = $methodname( $referentiel, $this, $extra )) {
				return $data;
            }
        }
        return false;
    }

    /**
     * Return the files extension appropriate for this type
     * override if you don't want .txt
     * @return string file extension
     */
    function export_file_extension() {
        return ".zip";
    }

    function export_zip_extension() {
    // zipfile
        return ".zip";
    }

    /**
     * Do any pre-processing that may be required
     * @param boolean success
     */
    function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    function presave_process( $content ) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return boolean success
     */
    function exportprocess() {
        global $CFG;

        // create a directory for the exports (if not already existing)
        // Moodle 1.9
        /*
        if (! $export_dir = make_upload_directory($this->get_export_dir())) {
              $this->error( get_string('cannotcreatepath', 'referentiel', $export_dir),'','','export' );
        }
        $path = $CFG->dataroot.'/'.$this->get_export_dir();
        */
        
        // create a directory for temporary zip file (if not already existing)
        /*
        if (! $temp_dir = make_upload_directory($this->get_temp_dir())) {
            $this->error( get_string('cannotcreatepath', 'referentiel', $temp_dir),'','','export' );
        }
        */
        
        // Moodle 2.0 :: $temp_path = $CFG->dataroot.'/'.$this->get_temp_dir();
        // Moodle 2.3
        $temp_path = $this->get_temp_dir();
        // DEBUG
        // echo "<br />DEBUG :: format.php :: 3326 :: TEMPDIR : $temp_path\n";
        // suppression du contenu au prealable pour adresser le probleme de dossier temporaire non vide
		if (file_exists($temp_path)) {
            remove_dir($temp_path, true);
		}

        // notify( get_string('exportingreferentiels', 'referentiel') );
        $count = 0;

        // results are first written into string (and then to a file)
        // so create/initialize the string here
        $expout = "";

        // export the item displaying message
        $count++;
        echo "<hr /><p><b>$count</b>. ".$this->rreferentiel->name."</p>";
        
         // MODIF JF 2012/06/15
        // write index file
        // ici modification par rapport au traitement normal car
        // les données doivent être zippée et placées dans un dossier spécial
        // moodledata/temp/archive/referentiel_id/


        // $expout .= $this->write_archive() . "\n";

        // continue path for following error checks
        $course = $this->course;
        $continuepath = "$CFG->wwwroot/mod/referentiel/archive.php?d=".$this->rinstance->id;

        // did we actually process anything
        if ($count==0) {
            $this->error( get_string('noreferentiels','referentiel',$continuepath),$filepath,'','archive' );
        }

        // write index file
        $expout = $this->write_index();
        $filepath = $temp_path."/".$this->filename . '.html';
        if (!$fh=fopen($filepath,"w")) {
            $this->error( get_string('cannotopen','referentiel',$continuepath),$filepath,'','export' );
        }
		// DEBUG
		// echo "<br /> FORMAT : 3363<br />$expout\n";

        if (!fwrite($fh, $expout, strlen($expout) )) {
            $this->error( get_string('cannotwrite','referentiel',$continuepath),$filepath,'','export' );
        }
        fclose($fh);
        /*
			echo "<br />DEBUG format.php :: LIGNE 3370<br />\n";
			print_r($this->records_users);
            echo "<br />EXIT\n";
            exit;
        */
        // Pour chaque utilisateur
        $count=0;
        foreach ($this->records_users as $userid){
            $expout = $this->write_archive_user($userid) . "\n";
            $count++;
            // final pre-process on exported data
            $expout = $this->presave_process( $expout, $userid, $this->filename.$this->export_file_extension());
            // write file
            $filepath = $temp_path."/".$this->get_filename_by_userid($userid) . $this->export_file_extension();
            if (!$fh=fopen($filepath,"w")) {
                $this->error( get_string('cannotopen','referentiel',$continuepath),$filepath,'','export' );
            }
		    // DEBUG
            // echo "<br /> FORMAT : 3029<br />$expout\n";

            if (!fwrite($fh, $expout, strlen($expout) )) {
                $this->error( get_string('cannotwrite','referentiel',$continuepath),$filepath,'','export' );
            }
            fclose($fh);
        }

        return true;
        
    }

    /**
     * Do an post-processing that may be required
     * @return boolean success
     */
    function exportpostprocess($archive_name) {
        // ici realiser la compression ZIP
        global $CFG;
        $archive_name.='.zip';
        // Moodle 2 :
        $fullpath = cleardoubleslashes($CFG->dataroot.'/'.$this->context->id.'/mod_referentiel/archive/'.$this->get_export_dir().'/'.$archive_name);
        // echo "<br />DEBUG :: format.php/exportpostprocess :: 3281 :: FILENAME : $archive_name<br />FULNAME : $fullpath\n";

        if (referentiel_backup_zip ($this->rreferentiel->id, $this->user_creator, $archive_name)){
            // deplacer vers le dossier
            // Moodle 2
            // $from_file = cleardoubleslashes($CFG->dataroot.'/'.$this->get_temp_dir().'/'.$archive_name);
            $from_file = cleardoubleslashes($this->get_temp_dir().'/'.$archive_name);
            // echo "<br />DEBUG :: format.php/exportpostprocess :: 3288 :: FROM_FILE :$from_file\n";

            if (referentiel_copy_file_moodle2_api($from_file, $this->get_export_dir(), $archive_name, $this->context)){
                // supprimer le dossier temporaire
                // remove_dir($CFG->dataroot.'/'.$this->get_temp_dir(), true);
                remove_dir($this->get_temp_dir(), true);
                return true;
            }
        }
        return false;
    }



    /**
     * list of files of the archives
     * @param string processed output text
     */
    function write_index(){
        return '';
    }

    /**
     * filename
     * @param string processed output text
     */
    function get_filename_by_userid($userid){
    global $DB;
        if ($user= $DB->get_record("user", array("id" => $userid))){
			return $user->username.'_'.$this->m_special_case($user->lastname,true).'_'.$this->m_special_case($user->firstname, false);
        }
        return '';
    }


    /**
     * filename
     * @param string processed output text
     */
    function get_url_users(){
        return '';
    }


    /**
     * convert a single user object into text output in the given
     * format.
     * This must be overriden
     * @param object user object
     * @return mixed export text or null if not implemented
     */
    function write_archive_user($user) {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * convert a single referentiel object into text output in the given
     * format.
     * This must be overriden
     * @param object referentiel referentiel object
     * @return mixed referentiel export text or null if not implemented
     */
    function write_archive() {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * get directory into which export is going
     * @return string file path
     */

    function get_temp_dir() {
		global $CFG;
		/*
        $dirname = get_string('archivetemp', 'referentiel');
        $path =  $dirname.'/'.$this->rreferentiel->id.'/'.$this->user_creator;
        return $path;
        */
        $dirname = get_string('archivetemp', 'referentiel');
        $path =  $dirname.'/'.$this->rreferentiel->id.'/'.$this->user_creator;
        // Moodle 2.2
        return make_temp_directory($path);
    }

    /**
     * get directory into which export is going
     * @return string file path
     */
    function get_export_dir() {
        // Moodle 2
        global $CFG;
        $path =  '/'.$this->rreferentiel->id.'/'.$this->user_creator.'/';
        return $path;
    }


    /**
     * Handle parsing error
     */
    function error( $message, $text='', $name='', $type='' ) {
        if ($type=='import')
            $error = get_string('importerror', 'referentiel');
        else
            $error = get_string('exporterror', 'referentiel');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$error $name</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<i>$message</i>\n";
        echo "</div>";

         $this->importerrors++;
    }



/***********************
 * IMPORTING FUNCTIONS
 ***********************/
// pas d'import

}

//**************************************************************************



?>
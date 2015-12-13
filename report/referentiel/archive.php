<?php // $Id$
// JF 14/09/2011
    require_once(dirname(__FILE__).'/../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/mod/referentiel/lib.php');
    require_once($CFG->dirroot.'/mod/referentiel/locallib.php');
	require_once($CFG->dirroot.'/mod/referentiel/version.php');

    require_once($CFG->dirroot.'/mod/referentiel/lib_etab.php');     // scolarite
    require_once($CFG->dirroot.'/mod/referentiel/lib_archive.php');  // archivage
    require_once($CFG->dirroot.'/mod/referentiel/lib_certificat.php');
    require_once($CFG->dirroot.'/mod/referentiel/lib_pedagogie.php');
    require_once($CFG->dirroot.'/mod/referentiel/import_export_lib.php');	// IMPORT / EXPORT

/// Get all required strings

    $strreferentiels = get_string("modulenameplural", "referentiel");
    $strreferentiel  = get_string("modulename", "referentiel");
	$strmessage = get_string('archivereferentiel','referentiel');
    // $icon = $OUTPUT->pix_url('icon','referentiel');

    $base_url=$CFG->wwwroot.'/report/referentiel/';

    $i     = optional_param('i', 0, PARAM_INT);    // referentiel instance id
    if ($i){
        if (! $referentiel = $DB->get_record('referentiel', array('id' => $i))) {
            print_error('Referentiel instance ID is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record('referentiel_referentiel', array('id' => $referentiel->ref_referentiel))) {
            print_error('Referentiel id is incorrect');
        }
		if (! $course = $DB->get_record('course', array('id' => $referentiel->course))) {
	        print_error('Course is misconfigured');
    	}
		if (! $cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $course->id)) {
    	        print_error('Course Module ID is incorrect');
		}
        $context = context_module::instance($cm->id);
    }
    else{
		print_error(get_string('erreurscript','referentiel','Erreur : admin/report/referentiel/archive.php'));
		exit;
	}


	    
    $action = optional_param('action','', PARAM_ALPHA);
    $exportfilename = optional_param('exportfilename','',PARAM_FILE );
    $format = optional_param('format','', PARAM_FILE );
    $format_condense = optional_param('format_condense', 0, PARAM_INT);  // format compact
    $export_pedagos = optional_param('export_pedagos', 0, PARAM_INT);  // exportation des donnees de formation / pedagogie
    $export_documents= optional_param('export_documents', 0, PARAM_INT);  // exportation des documents attachés aux déclarations
    $cancel = optional_param('cancel', 0, PARAM_BOOL);
    
    // get display strings
    $txt = new object;

    $txt->choisir = get_string('choisir','referentiel');
    $txt->condense = get_string('format_condense','referentiel');
    $txt->pourcentage = get_string('format_pourcentage','referentiel');
    $txt->reduit1 = get_string('format_reduit1','referentiel');
    $txt->reduit2 = get_string('format_reduit2','referentiel');
    $txt->referentiel = get_string('referentiel','referentiel');
    $txt->download = get_string('download','referentiel');
    $txt->downloadextra = get_string('downloadextra','referentiel');
    $txt->exporterror = get_string('exporterror','referentiel');
    $txt->exportname = get_string('exportname','referentiel');
    $txt->exportreferentiel = get_string('exportreferentiel', 'referentiel');
    $txt->exportcertificat = get_string('exportcertificat', 'referentiel');
    $txt->fileformat = get_string('fileformat','referentiel');
    $txt->modulename = get_string('modulename','referentiel');
    $txt->modulenameplural = get_string('modulenameplural','referentiel');


    // Pedagogies
    if ($referentiel_referentiel->id){
        $existe_pedagos=referentiel_pedagogies_exists($referentiel_referentiel->id);
    }
    else{
        $existe_pedagos=0;
    }

    // RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    } else {
        $form = (object)$_POST;
    }

    if ($cancel) {
        redirect('index.php');
    }

    if (isset($action) && ($action=='delete') && confirm_sesskey()){
        // DEBUG
        // print_r($form);
        // exit;
        if (!empty($form->deletefile)){
            foreach ($form->deletefile as $fullpathfile) {   // supprimer
                referentiel_delete_a_file($fullpathfile);
            }
        }

        unset($form);
        unset($action);
        // exit;
    }


// Print the header & check permissions.
    admin_externalpage_setup('reportreferentiel');
    echo $OUTPUT->header();

    echo $OUTPUT->heading(get_string('adminreport', 'referentiel'));
    echo '<div align="center"><h3>'.$strmessage.' '.$OUTPUT->help_icon('archiveh','referentiel').'</h3></div>'."\n";



    $msg = '';
    $contextversionneeded = 2007101500;  // Moodle 1.9 branch
    // print_object($CFG);

    // exit;
    if ($CFG->version < $contextversionneeded){
        ///version issus
        $msg .= get_string('majmoodlesvp', 'referentiel', $contextversionneeded);
        $msg .= "<br />".get_string('moodleversion', 'referentiel',$CFG->version)."<br />\n";
    }
    else if (!empty($referentiel_referentiel) && !empty($referentiel) && !empty($course)){

        $records_users=referentiel_get_liste_users($referentiel, $course, $context, '', 0, NULL, 0, 0);
        $size_data=referentiel_get_size_data($records_users, $referentiel->id);

        if (!empty($format)) {   /// Filename et format d'exportation
            if (is_readable($CFG->dirroot.'/mod/referentiel/format/'.$format.'/format.php')) {

                // load parent class for import/export
                require($CFG->dirroot.'/mod/referentiel/format.php');
                // and then the class for the selected format
                require($CFG->dirroot.'/mod/referentiel/format/'.$format.'/format.php');

                $classname = "zformat_$format";
                $zformat = new $classname();
                $zformat->setCourse( $course );
                $zformat->setFilename( $exportfilename );
                $zformat->setIReferentiel( $referentiel);
                $zformat->setRReferentiel( $referentiel_referentiel);
                $zformat->setUserCreator( $USER->id);   // qui réalise l'archive
                $zformat->setUserFiltre(0);  // celui qu'on archive
                $zformat->setContext($context );
                // fournir la liste utilisateurs
                $zformat->setRUsers($records_users);

                if (isset($format_condense)){
                    $zformat->setRCFormat($format_condense);
                }
                if (isset($export_pedagos)){
                    $zformat->setExportPedago($export_pedagos);
	            }
                if (isset($export_documents)){
                    $zformat->setExportDocuments($export_documents);
                }

                // repertoire temporaire pour ziper l'archive
                // make_upload_directory( $zformat->get_temp_dir());

                if (! $zformat->exportpreprocess()) {   // Do anything before that we need to
                    print_error( $txt->exporterror,$base_url.'/archive.php?i='.$referentiel->id);
                }

                if (! $zformat->exportprocess()) {         // Process the export data
                    print_error( $txt->exporterror, $base_url.'/archive.php?i='.$referentiel->id);
                }

                if (! $zformat->exportpostprocess($exportfilename)) {                    // In case anything needs to be done after
                    print_error( $txt->exporterror, $base_url.'/archive.php?i='.$referentiel->id);
                }
                echo "<hr />";

                // link to download the finished file
                $file_ext = $zformat->export_zip_extension();

                // Moodle 2.0
                $fullpath = '/'.$context->id.'/mod_referentiel/archive/0'.$zformat->get_export_dir().$exportfilename.$file_ext;
                $efile = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);

                echo "<p><div class=\"boxaligncenter\"><a href=\"$efile\">$txt->download</a></div></p>";
                echo "<p><div class=\"boxaligncenter\"><font size=\"-1\">$txt->downloadextra</font></div></p>";
                // print_continue("$base_url/index.php");
                echo $OUTPUT->continue_button("$base_url/index.php");
                echo $OUTPUT->footer();
                die();
            }
        }
        else { // BOITES DE SELECTION
            // Display upload form
            // get valid formats to generate dropdown list
            $fileformatnames = referentiel_get_import_export_formats( 'archive' , 'zformat');

            // get filename
            if (empty($exportfilename)) {
                $exportfilename = referentiel_default_export_filename($course, $referentiel, 'archive');
            }
            echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');

?>
            <form enctype="multipart/form-data" method="post" action="archive.php?i=<?php echo $referentiel->id; ?>">
            <fieldset class="invisiblefieldset" style="display: block;">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
            <table cellpadding="5">
                <tr>
                    <td>
<?php
            echo $txt->fileformat.': ';
            echo html_writer::select($fileformatnames, 'format', 'html', false);
            echo $OUTPUT->help_icon('format_archiveh', 'referentiel');
?>
                    </td>
<?php
            if (!$format_condense){
                echo '<td>'.$txt->pourcentage.' ?<br /><input type="radio" name="format_condense" value="0" checked="checked"/>'.get_string('no').'
<input type="radio" name="format_condense" value="1"/>'.get_string('yes').'
';
            }
            else{
                echo '<td>'.$txt->pourcentage.' ?<br /><input type="radio" name="format_condense" value="0"/>'.get_string('no').'
<input type="radio" name="format_condense" value="1" checked="checked"/>'.get_string('yes').'
';
            }
            echo $OUTPUT->help_icon('format_certificath', 'referentiel');

            echo '</td>
</tr>
';

            // fichiers attachés ?
            echo '<tr><td>'.get_string('export_documents','referentiel', $size_data->nfile).' '. get_string('export_file_size','referentiel', display_size($size_data->size)).'</td><td>';
            if (!$export_documents){
                echo '<input type="radio" name="export_documents" value="0" checked="checked"/>'.get_string('export_url', 'referentiel').'
<input type="radio" name="export_documents" value="1"/>'.get_string('export_data', 'referentiel').'
</td></tr>'."\n";
            }
            else{
                echo '<input type="radio" name="export_documents" value="0"/>'.get_string('export_url', 'referentiel').'
<input type="radio" name="export_documents" value="1" checked="checked"/>'.get_string('export_data', 'referentiel').'
</td></tr>'."\n";
            }
            // Pégagogies
            if ($existe_pedagos){
                if ($export_pedagos){
                    echo '<tr><td><i>'.get_string('export_pedagos','referentiel').'</i>: </td>
<td>
    <input type="radio" name="export_pedagos" value="0"/>'.get_string('no').'
    <input type="radio" name="export_pedagos" value="1" checked="checked"/>'.get_string('yes').'
</td></tr>'."\n";
                }
                else{
                    echo '<tr><td><i>'.get_string('export_pedagos','referentiel').'</i>: </td>
<td>
    <input type="radio" name="export_pedagos" value="0" checked="checked"/>'.get_string('no').'
    <input type="radio" name="export_pedagos" value="1"/>'.get_string('yes').'
</td></tr>'."\n";
                }
            }
?>

                <tr>
                    <td><?php echo $txt->exportname; ?>:</td>
                    <td align="center" colspan="2">
                        <input type="text" size="60" name="exportfilename" value="<?php echo $exportfilename; ?>" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="3">
                        <input type="submit" name="save" value="<?php echo $txt->exportcertificat; ?>" />
                        <input type="submit" name="cancel" value="<?php print_string('quit', 'referentiel'); ?>" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
<?php

            echo $OUTPUT->box_end();
            // Gestion des fichiers d'archives
            referentiel_get_manage_archives($context->id, get_string('archives', 'referentiel'), "archive.php?i=$referentiel->id&amp;action=delete", 0, $referentiel->id, $CFG->enableportfolios, 1, 'file');  // report type = 1
            echo $OUTPUT->footer();
            die();
        }
    }
    if ($msg) {
        echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');
        echo $msg;
        echo $OUTPUT->box_end();
    }
    
    // Footer.
    echo $OUTPUT->footer();
    


?>

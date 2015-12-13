<?php  // $Id: export.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
* D'apres quiz/export.php 
* Export referentiel
*
* @version $Id: export.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
* @author Martin Dougiamas, Howard Miller, and many others.
*         {@link http://moodle.org}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package referentiel
*/

    require(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');

    require_once('import_export_lib.php');	// IMPORT / EXPORT

    $id    = optional_param('id', 0, PARAM_INT);    // course module id	
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel base id
	
    $mode  = optional_param('mode','', PARAM_ALPHANUMEXT);

    $exportfilename = optional_param('exportfilename','',PARAM_FILE );
    $format = optional_param('format','', PARAM_FILE );
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement


    // get display strings
    $txt = new object;
    $txt->referentiel = get_string('referentiel','referentiel');
    $txt->download = get_string('download','referentiel');
    $txt->downloadextra = get_string('downloadextra','referentiel');
    $txt->exporterror = get_string('exporterror','referentiel');
    $txt->exportname = get_string('exportname','referentiel');
    $txt->exportreferentiel = get_string('exportreferentiel', 'referentiel');
    $txt->fileformat = get_string('fileformat','referentiel');
    $txt->modulename = get_string('modulename','referentiel');
    $txt->modulenameplural = get_string('modulenameplural','referentiel');
    // $txt->tofile = get_string('tofile','referentiel');


    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/export.php');

	if ($d) {     // referentiel_referentiel_id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Réferentiel id is incorrect');
        }

		if (! $course = $DB->get_record("course", array("id" => "$referentiel->course"))) {
	            print_error('Course is misconfigured');
    	}

		if (! $cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $course->id)) {
    	        print_error('Course Module ID is incorrect');
		}
		$url->param('d', $d);
	}
	elseif ($id) {
        if (! $cm = get_coursemodule_from_id('referentiel', $id)) {
        	print_error('Course Module ID was incorrect');
        }
        if (! $course = $DB->get_record("course", array("id" => "$cm->course"))) {
            print_error('Course is misconfigured');
        }
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$cm->instance"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Referentiel is incorrect');
        }
        $url->param('id', $id);
    }
	else{
    // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : export.php'), 'referentiel');
	}

    $returnlink_ref = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));
    $returnlink_course = new moodle_url('/course/view.php', array('id'=>$course->id));
    $returnlink_add = new moodle_url('/mod/referentiel/add.php', array('d'=>$referentiel->id, 'sesskey'=>sesskey()));

    require_login($course->id, false, $cm);
    if (!isloggedin() || isguestuser()) {
        redirect($returnlink_course);
    }

    $context = context_module::instance($cm->id);

    require_capability('mod/referentiel:export', $context);


	// RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else {
        $form = (object)$_POST;
    }

    // Suppression des fichiers d'export
    if (!empty($form->deletefile) && confirm_sesskey()){
        foreach ($form->deletefile as $fullpathfile) {
            if ($fullpathfile){
                // echo "<br />DEBUG :: archive.php :: 252<br />\n";
                // echo "<br />$fullpathfile\n";
                referentiel_delete_a_file($fullpathfile);
            }
        }
        unset($form);
    }


    // ensure the files area exists for this course
    // Moolde 1.9
    // make_upload_directory( "$course->id/$CFG->moddata/referentiel" );

    

	/// Print the tabs
	$mode='export'; // un seul mode possible
	$url->param('mode', $mode);

    $currenttab = 'export';
	
    if ($referentiel->id) {
       	$editentry = true;  //used in tabs
    }

    /// Mark as viewed  ??????????? A COMMENTER
    $completion=new completion_info($course);
    $completion->set_module_viewed($cm);

    // AFFICHAGE DE LA PAGE Moodle 2
	/// Print the page header

	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
	$strpagename=get_string('exportreferentiel','referentiel');

    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->css('/mod/referentiel/jauge.css');

    $PAGE->set_title($pagetitle);
    $PAGE->navbar->add($strpagename);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }


    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();
    echo '<br />'."\n";
    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strpagename.' '.$OUTPUT->help_icon('exportreferentielh','referentiel').'</h2></div>'."\n";

	if ($mode=='listreferentiel'){
		referentiel_affiche_referentiel($cm, $referentiel->id, $referentiel->ref_referentiel);
	}

    if (!empty($format)) {   /// Filename et format d'exportation

        if (!confirm_sesskey()) {
            print_error( 'sesskey' );
        }

        if (! is_readable("format/$format/format.php")) {
            print_error( "Format not known ($format)" );  }

        // load parent class for import/export
        require("format.php");

        // and then the class for the selected format
        require("format/$format/format.php");

        $classname = "rformat_$format";
        $rformat = new $classname();

        // $rformat->setCategory( $category );
        $rformat->setContext( $context);
        $rformat->setCourse( $course );
        $rformat->setFilename( $exportfilename );
        $rformat->setIReferentiel( $referentiel);
        $rformat->setRReferentiel( $referentiel_referentiel);

        if (! $rformat->exportpreprocess()) {   // Do anything before that we need to
            print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/export.php?d='.$referentiel->id);
        }

        if (! $rformat->exportprocess()) {         // Process the export data
            print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/export.php?d='.$referentiel->id);
        }

        if (! $rformat->exportpostprocess()) {                    // In case anything needs to be done after
            print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/export.php?d='.$referentiel->id);
        }
        echo "<hr />";

        $file_ext = $rformat->export_file_extension();

        // Moodle 2.0
        $fullpath = '/'.$context->id.'/mod_referentiel/referentiel/0'.$rformat->get_export_dir().$exportfilename.$file_ext;
        $efile = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);

        echo "<p><div class=\"boxaligncenter\"><a href=\"$efile\">$txt->download</a></div></p>";
        echo "<p><div class=\"boxaligncenter\"><font size=\"-1\">$txt->downloadextra</font></div></p>";

        echo $OUTPUT->continue_button($returnlink_ref);
        echo $OUTPUT->footer();

        die();

    }

    /// Display upload form

    // get valid formats to generate dropdown list
    $fileformatnames = referentiel_get_import_export_formats( 'export' );

    // get filename
    if (empty($exportfilename)) {
        $exportfilename = referentiel_default_export_filename($course, $referentiel);
    }

    echo $OUTPUT->box_start('generalbox  boxaligncenter');
    echo "\n<div align=\"center\">\n";
?>

    <form enctype="multipart/form-data" method="post" action="export.php?d=<?php echo $referentiel->id; ?>">
        <fieldset class="invisiblefieldset" style="display: block;">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />

            <table cellpadding="5">
                <tr>
                    <td><?php echo $txt->fileformat; ?>:</td>
                    <td>
                        <?php
                        echo html_writer::select($fileformatnames, 'format', 'xml', false);
                        echo $OUTPUT->help_icon('formath', 'referentiel');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $txt->exportname; ?>:</td>
                </tr>
                <tr>					
                    <td colspan="2">
                        <input type="text" size="60" name="exportfilename" value="<?php echo $exportfilename; ?>" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <input type="submit" name="save" value="<?php echo $txt->exportreferentiel; ?>" />
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <?php
    echo "\n</div>\n";
    echo $OUTPUT->box_end();

    if (REFERENTIEL_OUTCOMES){
        echo '<div align="center"><h3><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('export_bareme','referentiel').' '.$OUTPUT->help_icon('exportoutcomesh','referentiel').'</h3>'."\n";
        echo $OUTPUT->box_start('generalbox  boxaligncenter');

        if (!empty($CFG->enableoutcomes)){
            echo '<a href="'.$CFG->wwwroot.'/mod/referentiel/grade/export_grade_outcomes.php?d='.$referentiel->id.'">'.get_string('export_outcomes','referentiel').'</a>';
        }
        else{
            print_string('activer_outcomes','referentiel');
        }
        echo '<br >'.get_string('help_outcomes','referentiel');

        echo $OUTPUT->box_end();
        echo '</div>'."\n";
    }

    // Liste de sauvegardes déjà enregistrées
    // Gestion des fichiers d'archives
    referentiel_get_manage_files($context->id, 'referentiel', 0, get_string('exportedreferentiel', 'referentiel'), "export.php?id=$cm->id");

    echo $OUTPUT->footer();
    die();
?>

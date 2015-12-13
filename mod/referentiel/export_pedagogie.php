<?php  // $Id: export_pedagogie..php,v 0.1 2011/03/07/ 00:00:00 jfruitet Exp $
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

/*
CREATE TABLE referentiel_a_user_scol (
    id BIGINT(10) unsigned NOT NULL auto_increment,
    userid BIGINT(10) unsigned NOT NULL DEFAULT 0,
    refrefid BIGINT(10) unsigned DEFAULT NULL,
    promotion VARCHAR(4) DEFAULT NULL,
    datedeb VARCHAR(14) DEFAULT NULL,
    datefin VARCHAR(14) DEFAULT NULL,
    timemodified BIGINT(10) unsigned NOT NULL,
    formation VARCHAR(40) DEFAULT NULL,
    pedagogie VARCHAR(40) DEFAULT NULL,
    site VARCHAR(40) DEFAULT NULL,
    username VARCHAR(100) DEFAULT NULL, 
CONSTRAINT  PRIMARY KEY (id)
);

*/
/**
* Exportation d'un fichier de pedagogie
* D'apres export_etudiant.php 
*
* @package referentiel
*/

    require(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');
	require_once('lib_pedagogie.php');
    require_once('import_export_lib.php');	// IMPORT / EXPORT	
    require_once($CFG->libdir . '/uploadlib.php');

    $exportfilename = optional_param('exportfilename','',PARAM_FILE );
    $format = optional_param('format','', PARAM_FILE );
	
    $id    = optional_param('id', 0, PARAM_INT);    // course module id    
	$d     = optional_param('d', 0, PARAM_INT);    // referentielbase id
	
    $pedago_id   = optional_param('pedago_id', 0, PARAM_INT);    //record pedago id

    $mode           = optional_param('mode','', PARAM_ALPHANUMEXT);
    $add           = optional_param('add','', PARAM_ALPHA);
    $update        = optional_param('update', 0, PARAM_INT);
    $delete        = optional_param('delete', 0, PARAM_INT);
    $approve        = optional_param('approve', 0, PARAM_INT);	
    $comment        = optional_param('comment', 0, PARAM_INT);		
    $course        = optional_param('course', 0, PARAM_INT);
    $groupmode     = optional_param('groupmode', -1, PARAM_INT);
    $cancel        = optional_param('cancel', 0, PARAM_BOOL);
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
	
    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/export_pedagogie.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : export_pedagogie.php'), 'referentiel');
	}

	if ($pedago_id) { // id pedago
        if (! $record = $DB->get_record('referentiel_a_user_scol', array("id" => "$pedago_id"))) {
            print_error('pedago ID is incorrect');
        }
	}


    // get display strings
    $txt = new object;
    $txt->referentiel = get_string('referentiel','referentiel');
    $txt->download = get_string('download','referentiel');
    $txt->downloadextra = get_string('downloadextra','referentiel');
    $txt->exporterror = get_string('exporterror','referentiel');
    $txt->exportname = get_string('exportname','referentiel');
    $txt->exportpedago = get_string('exportpedago', 'referentiel');
    $txt->fileformat = get_string('fileformat','referentiel');
    $txt->modulename = get_string('modulename','referentiel');
    $txt->modulenameplural = get_string('modulenameplural','referentiel');
    // $txt->tofile = get_string('tofile','referentiel');
	
	// PAS DE RSS
    // require_once("$CFG->libdir/rsslib.php");



    $returnlink_ref = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));
    $returnlink_course = new moodle_url('/course/view.php', array('id'=>$course->id));
    $returnlink_add = new moodle_url('/mod/referentiel/add.php', array('d'=>$referentiel->id, 'sesskey'=>sesskey()));

    require_login($course->id, false, $cm);
    if (!isloggedin() || isguestuser()) {
        redirect($returnlink_course);
    }

    $context = context_module::instance($cm->id);

    require_capability('mod/referentiel:export', $context);

    if ($pedago_id) {    // So do you have access?
        if (!(has_capability('mod/referentiel:writereferentiel', $context) 
			or referentiel_pedagogie_isowner($pedago_id, $USER->id)) or !confirm_sesskey() ) {
            print_error(get_string('noaccess','referentiel'));
        }
    }
	
	
	// RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else {
        $form = (object)$_POST;
    }

	if ($cancel) {
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        }
        else {
            redirect($CFG->wwwroot.'/mod/referentiel/pedagogie.php?d='.$referentiel->id);
        }
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

    
        
	/// Print the tabs
	if (!isset($mode) || ($mode=="")){
		$mode='exportpedago';
	}
	$currenttab = $mode;
	// Moodle 2
    $url->param('mode', $mode);

    /// Mark as viewed  ??????????? A COMMENTER
    $completion=new completion_info($course);
    $completion->set_module_viewed($cm);

// AFFICHAGE DE LA PAGE Moodle 2
	/// Print the page header
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
	$strmessage = get_string('exportpedagogies','referentiel');
	$strpagename=get_string('exportpedago','referentiel');

    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);

	/// RSS and CSS and JS meta
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->css('/mod/referentiel/jauge.css');
    $PAGE->requires->css('/mod/referentiel/referentiel.css');

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
    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('exportpedagoh','referentiel').'</h2></div>'."\n";
	
    if (!empty($format) && !empty($referentiel) && !empty($course)) {   
		/// Filename et format d'exportation
// DEBUG 
//echo "<br /> OK 1\n";
        if (!confirm_sesskey()) {
            print_error( 'sesskey' );
        }
// DEBUG 
//echo "<br /> OK 2\n";
        if (! is_readable("format/$format/format.php")) {
            print_error( "Format not known ($format)" );  
		}
// DEBUG 
//echo "<br /> OK 3\n";
        // load parent class for import/export
        require("format.php");
// DEBUG 
//echo "<br /> OK 4\n";
        // and then the class for the selected format
        require("format/$format/format.php");
// DEBUG 
//echo "<br /> OK 5\n";
        $classname = "pformat_$format";
        $pformat = new $classname();
// DEBUG 
// echo "<br /> OK 6\n";
        // $pformat->setCategory( $category );
		$pformat->setCoursemodule( $cm );
        $pformat->setContext( $context );
        $pformat->setCourse( $course );
        $pformat->setFilename( $exportfilename );
        $pformat->setIReferentiel( $referentiel);    
        $pformat->setRReferentiel( $referentiel_referentiel);
		$pformat->setRefReferentiel( $referentiel->ref_referentiel);
// DEBUG 
// echo "<br /> OK 7\n";

        if (! $pformat->exportpreprocess()) {   // Do anything before that we need to
            print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/export_pedagogie.php?id='.$cm->id);
        }
// echo "<br /> OK 8\n";
        if (! $pformat->exportprocess()) {         // Process the export data
            print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/export_pedagogie.php?id='.$cm->id);
        }
// echo "<br /> OK 9\n";
        if (! $pformat->exportpostprocess()) {                    // In case anything needs to be done after
            print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/export_pedagogie.php?id='.$cm->id);
        }
        echo "<hr />";
// echo "<br /> OK 10\n";

        // link to download the finished file
        $file_ext = $pformat->export_file_extension();
        // Moodle 1.9
        /*
        if ($CFG->slasharguments) {
          $efile = "{$CFG->wwwroot}/file.php/".$pformat->get_export_dir()."/$exportfilename".$file_ext."?forcedownload=1";
        }
        else {
          $efile = "{$CFG->wwwroot}/file.php?file=/".$pformat->get_export_dir()."/$exportfilename".$file_ext."&forcedownload=1";
        }
        */

        // Moodle 2.0
        $fullpath = '/'.$context->id.'/mod_referentiel/pedagogie/0'.$pformat->get_export_dir().$exportfilename.$file_ext;
        $efile = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);

        echo "<p><div class=\"boxaligncenter\"><a href=\"$efile\">$txt->download</a></div></p>";
        echo "<p><div class=\"boxaligncenter\"><font size=\"-1\">$txt->downloadextra</font></div></p>";

        echo $OUTPUT->continue_button($CFG->wwwroot.'/mod/referentiel/pedagogie.php?id='.$cm->id);
        echo $OUTPUT->footer();
        die();

    }

	
    /// Display upload form
    // get valid formats to generate dropdown list
  	$fileformatnames = referentiel_get_import_export_formats( 'export', 'pformat' );
    // get filename
    if (empty($exportfilename)) {
        $exportfilename = referentiel_default_export_filename($course, $referentiel, 'pedago');
    }

    // print_heading_with_help($txt->exportreferentiel, 'export', 'referentiel');
    echo $OUTPUT->box_start('generalbox  boxaligncenter');
    echo "\n<div align=\"center\">\n";
?>

    <form enctype="multipart/form-data" method="post" action="export_pedagogie.php?id=<?php echo $cm->id; ?>">
        <fieldset class="invisiblefieldset" style="display: block;">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />

            <table cellpadding="5">
                <tr>
                    <td><?php echo $txt->fileformat; ?>:</td>
                    <td>
                        <?php
                        // choose_from_menu($fileformatnames, 'format', 'csv', '');
                        // helpbutton('format', $txt->referentiel, 'referentiel');
                        echo html_writer::select($fileformatnames, 'format', 'csv', false);
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
                        <input type="submit" name="save" value="<?php echo $txt->exportpedago; ?>" />
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <?php

    echo "\n</div>\n";
    echo $OUTPUT->box_end();

    // Liste de sauvegardes déjà enregistrées
    // Gestion des fichiers d'archives
    referentiel_get_manage_files($context->id, 'pedagogie', 0, get_string('exportedpedago', 'referentiel'), "export_activite.php?id=$cm->id");

    echo $OUTPUT->footer();
    die();

?>

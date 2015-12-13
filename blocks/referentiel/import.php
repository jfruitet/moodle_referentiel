<?php
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
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set

/**
 * Interface.
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/../../config.php');
require_once('occurrence_class.php');
require_once('pass_form.php');
require_once('lib.php');
require_once('import_form.php'); // formulaires de choix de fichiers
require_once("$CFG->dirroot/repository/lib.php");
require_once('import_export_lib.php');	// IMPORT / EXPORT

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

// Next look for optional variables.
$occurrenceid = optional_param('occurrenceid', 0, PARAM_INT);
$mode  = optional_param('mode', 'add', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')
$action = optional_param('action','', PARAM_ALPHANUMEXT);

$format = optional_param('format','', PARAM_FILE );

// http://localhost/moodle25/blocks/referentiel/edit.php?blockid=65&courseid=2&occurrenceid=2&deleteid=217&action=modifieritem&delete=Supprimer&pass=&sesskey=pLZgTzHQUJ
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_referentiel', $courseid);
}

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

require_login($course);
$context = context_block::instance($blockid);
require_capability('mod/referentiel:import', $context);

// Traitements
// Occurrence
$params=array("blockid"=>$blockid, "courseid"=>$courseid);
$occurrence_object = new occurrence($params);
//print_object($occurrence_object->referentiel);
//exit;
$role=$occurrence_object->roles();
$isadmin=$role->is_admin;

// affichage
$icon = $OUTPUT->pix_url('icon','referentiel');

$pagetitle=get_string('import', 'block_referentiel');
$PAGE->set_url('/blocks/referentiel/import.php', array('blockid'=>$blockid, 'courseid' => $courseid));
$PAGE->requires->css('/mod/referentiel/referentiel.css');
// $PAGE->requires->js('/mod/referentiel/functions.js');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);
$PAGE->set_title($pagetitle);
$PAGE->navbar->add($occurrence_object->referentiel->code_referentiel);
//$settingsnode = $PAGE->settingsnav->add(get_string('displayoccurrence', 'block_referentiel'));
//$site = get_site();

$settingsnode = $PAGE->settingsnav->add(get_string('import', 'block_referentiel'));
$editurl = new moodle_url('/blocks/referentiel/import.php', array('blockid'=>$blockid, 'courseid'=>$courseid));
$editnode = $settingsnode->add(get_string('import', 'block_referentiel'), $editurl);
$editnode->make_active();

// import
if (!$role->can_edit){
    redirect($courseurl);
}
   // formulaire de saisie d'un fichier
    $fileformatnames = referentiel_get_import_export_formats('import', 'rformat');
    $options = array('subdirs'=>0, 'maxbytes'=>get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, 0), 'maxfiles'=>1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
    $mform = new block_referentiel_import_form(null, array('blockid'=>$blockid, 'courseid'=>$courseid, 'contextid'=>$context->id, 'filearea'=>'referentiel', 'fileformats' => $fileformatnames, 'override' => 0, 'stoponerror' => 1, 'newinstance' => 1, 'action' => 'importreferentiel', 'msg' =>  get_string('import', 'referentiel'), 'options'=>$options));

/*
    // mot de passe ?
	if (!empty($occurrence_object->referentiel->pass_referentiel)){

    	// Le referentiel est-il protege par mot de passe ?
        // RECUPERER LES FORMULAIRES
        if (isset($SESSION->modform)) {   // Variables are stored in the session
            $form = $SESSION->modform;
            unset($SESSION->modform);
        }
        else {
            $form = (object)$_POST;
        }


		if (!$pass && ($checkpass=='checkpass') && !empty($form->pass_referentiel)){
			$pass=referentiel_check_pass($occurrence_object->referentiel, $form->pass_referentiel);
			if (!$pass){
				// Abandonner
 				echo $OUTPUT->continue_button($courseurl);
      			exit;
			}
		}
		else{
			// saisie du mot de  passe
			if (isset($occurrence_object->referentiel->mail_auteur_referentiel) && ($occurrence_object->referentiel->mail_auteur_referentiel!='')
				&& (referentiel_get_user_mail($USER->id)!=$occurrence_object->referentiel->mail_auteur_referentiel)) {
				//
				echo $OUTPUT->header();
                echo $OUTPUT->box_start('generalbox  boxaligncenter');
    	    	// formulaires
				$appli_appelante="import.php";
				include_once("pass_inc.php");
                echo $OUTPUT->box_end();
                echo $OUTPUT->footer();
                die();
			}
		}
	}
*/
    // recuperer le fichier charge
    if ($mform->is_cancelled()) {
        redirect($courseurl);
    }
    else if ($mform->get_data()) {

        $returnlink = $courseurl;

        if ($formdata = $mform->get_data()) {
            // DEBUG
            // echo "<br />DEBUG :: import_instance.php :: 193 :: FORMDATA\n";
            // print_object($formdata);

            // documents activites et consignes des tâches
            $fileareas = array('referentiel');
            if (empty($formdata->filearea) || !in_array($formdata->filearea, $fileareas)) {
                return false;
            }


            $fs = get_file_storage();
            // suppression du fichier existant ?   NON
            // $fs->delete_area_files($formdata->contextid, 'block_referentiel', $formdata->filearea, 0);

            if ($newfilename= $mform->get_new_filename('referentiel_file')) {

				echo $OUTPUT->header();

                $contents = $mform->get_file_content('referentiel_file');
                if (!empty($contents)){
                    /*
                    $fullpath = "/$formdata->contextid/block_referentiel/$formdata->filearea/0/$newfilename";
                    $link = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);
                    // DEBUG
                    echo "<br />DEBUG :: 219 :: $link<br />\n";
                    */

                    $format=$formdata->format;

                    // echo "<br />DEBUG :: 235 :: $format<br />\n";
                    if (! is_readable("format/$format/format.php")) {
                        print_error( get_string('formatnotfound','referentiel', $format) );
                    }

                    require_once("format.php");  // Parent class
                    require_once("format/$format/format.php");
                    $classname = "rformat_$format";
                    $rformat = new $classname();
                    // load data into class
                    // $rformat->setReferentiel( $occurrence_object->referentiel ); // occurrence
                    $rformat->setCourse( $course );
                    $rformat->setContext( $context );
                    $rformat->setBlockId( $blockid);
                    $rformat->setContents( $contents );
                    $rformat->setStoponerror( $formdata->stoponerror );
                    $rformat->setOverride( $formdata->override );
                    $rformat->setNewinstance( $formdata->newinstance );
                    $rformat->setAction( $formdata->action );

                    // Do anything before that we need to
                    if (! $rformat->importpreprocess()) {
                        print_error( get_string('importerror','referentiel') , $returnlink);
                    }

                    // Process the uploaded file
                    if (! $rformat->importprocess() ) {
                        print_error( get_string('importerror','referentiel') , $returnlink);
                    }

                    // In case anything needs to be done after
                    if (! $rformat->importpostprocess()) {
                        print_error( get_string('importerror','referentiel') , $returnlink);
                    }

                    // Verify if referentiel is loaded
                    if (! $rformat->new_referentiel_id) {
                        print_error( get_string('importerror_referentiel_id','referentiel') , $returnlink);
                    }

                    echo "<hr />";
                    if (isset($rformat->returnpage) && ($rformat->returnpage!="")){
                        echo $OUTPUT->continue_button($rformat->returnpage);
                    }
                    else{
                        echo $OUTPUT->continue_button(new moodle_url('/blocks/referentiel/view.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$rformat->new_referentiel_id)));
                    }
                }
                else{
                    print_error( get_string('cannotread','referentiel') );
                }
                echo $OUTPUT->footer();
                die();
            }
        }
        redirect($returnlink);
    }
	else{
    	// afficher la page
	    echo $OUTPUT->header();
	    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" />'.get_string('importreferentiel','referentiel').' '.$OUTPUT->help_icon('importreferentielh','referentiel').'</h2></div>'."\n";
    	echo $OUTPUT->box_start('generalbox');
    	$mform->display();
    	echo $OUTPUT->box_end();
    	echo $OUTPUT->footer();
	}
	die();


?>
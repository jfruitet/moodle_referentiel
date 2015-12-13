<?php  // $Id: import_instance.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
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
* Importation d'un referentiel
* D'apres competency/import.php 
*
* @package referentiel
*/
    require_once('../../config.php');
    require_once('locallib.php');
    require_once('import_export_lib.php');	// IMPORT / EXPORT
    require_once('class/import_form.php'); // formulaires de choix de fichiers
    require_once("$CFG->dirroot/repository/lib.php");
    

    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel base id
	$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
    $checkpass = optional_param('checkpass','', PARAM_ALPHANUM); // mot de passe fourni

    $mode = optional_param('mode','', PARAM_ALPHANUMEXT);

    $format = optional_param('format','', PARAM_FILE );
    $courseid = optional_param('courseid', 0, PARAM_INT);
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
    
    

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/import.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : import.php'), 'referentiel');
	}


    require_login($course->id, false, $cm);
    if (!isloggedin() || isguestuser()) {
        redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
    }

    // check role capability
    $context = context_module::instance($cm->id);
    require_capability('mod/referentiel:import', $context);

    // ensure the files area exists for this course
	if (empty($mode)) {
        $mode='import'; // un seul mode possible
    }
    $url->param('mode', $mode);
    $action='importreferentiel'; // une seule action possible
    $url->param('action', $action);

	$currenttab = 'import';
    if ($referentiel->id) {
    	$editentry = true;  //used in tabs
    }
    
    

    // AFFICHAGE DE LA PAGE Moodle 2
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
	$strpagename=get_string('import','referentiel');
	$strmessage = get_string('importreferentiel','referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);

    $PAGE->set_title($pagetitle);
    $PAGE->navbar->add($strpagename);
    $PAGE->set_heading($course->fullname);

    // formulaire de saisie d'un fichier
    $fileformatnames = referentiel_get_import_export_formats( 'import', 'rformat' );
    $options = array('subdirs'=>0, 'maxbytes'=>get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, 0), 'maxfiles'=>1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
    $mform = new referentiel_import_form(null, array('d'=>$referentiel->id, 'contextid'=>$context->id, 'filearea'=>'referentiel', 'fileformats' => $fileformatnames, 'override' => 0, 'stoponerror' => 1, 'newinstance' => 1, 'action' => 'importreferentiel', 'msg' =>  get_string('import', 'referentiel'), 'options'=>$options));

    // mot de passe ?
	if ($referentiel_referentiel){
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
			$pass=referentiel_check_pass($referentiel_referentiel, $form->pass_referentiel);
			if (!$pass){
				// Abandonner
 				echo $OUTPUT->continue_button($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
      			exit;
			}
		}
		else{
			// saisie du mot de  passe
			if (isset($referentiel_referentiel->mail_auteur_referentiel) && ($referentiel_referentiel->mail_auteur_referentiel!='')
				&& (referentiel_get_user_mail($USER->id)!=$referentiel_referentiel->mail_auteur_referentiel)) {
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

    // recuperer le fichier charge
    if ($mform->is_cancelled()) {
        redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
    }
    else if ($mform->get_data()) {

        $returnlink = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));

        if ($formdata = $mform->get_data()) {
            // DEBUG
            // echo "<br />DEBUG :: import_instance.php :: 193 :: FORMDATA\n";
            // print_object($formdata);

            // documents activites et consignes des tâches
            $fileareas = array('referentiel', 'document', 'consigne', 'activite', 'task', 'certificat', 'scolarite', 'pedagogie');
            if (empty($formdata->filearea) || !in_array($formdata->filearea, $fileareas)) {
                return false;
            }


            $fs = get_file_storage();
            // suppression du fichier existant ?   NON
            // $fs->delete_area_files($formdata->contextid, 'mod_referentiel', $formdata->filearea, 0);

            if ($newfilename= $mform->get_new_filename('referentiel_file')) {

				echo $OUTPUT->header();

                $contents = $mform->get_file_content('referentiel_file');
                if (!empty($contents)){
                    /*
                    $fullpath = "/$formdata->contextid/mod_referentiel/$formdata->filearea/0/$newfilename";
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
                    $rformat->setIReferentiel( $referentiel ); // instance
                    // $rformat->setRReferentiel( $referentiel_referentiel ); // not yet
                    $rformat->setCourse( $course );
                    $rformat->setContext( $context );
                    $rformat->setCoursemodule( $cm);
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

                    // update instance
                    if (empty($formdata->newinstance)){
                        $DB->set_field ('referentiel','ref_referentiel',$rformat->new_referentiel_id, array("id" => "$referentiel->id"));
                    }
                    else{
                        // il faudrait créer une nouvelle instance mais ce n'est pas l'endroit
                        // on se contente de créer le référentiel_referentiel
                        // sans l'associer à l'instance courante
                    }

                    echo "<hr />";
                    if (isset($rformat->returnpage) && ($rformat->returnpage!="")){
                        echo $OUTPUT->continue_button($rformat->returnpage);
                    }
                    else{
                        echo $OUTPUT->continue_button($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
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
    
    // afficher la page
    echo $OUTPUT->header();

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();

    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('importreferentielh','referentiel').'</h2></div>'."\n";
    echo $OUTPUT->box_start('generalbox');
    $mform->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die();
?>

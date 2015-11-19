<?php  // $Id: upload.php,v 1.0 2008/05/03 00:00:00 jfruitet Exp $
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

    // require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
    require(dirname(__FILE__) . '/../../config.php');
    //require_once(dirname(__FILE__).'/class/upload_form.php');
    require_once('class/upload_simple_form.php');
    require_once("$CFG->dirroot/repository/lib.php");
    require_once('locallib.php');
	// require_once($CFG->libdir . '/uploadlib.php');	// pour charger un fichier
    require_once("print_lib_referentiel.php");	// AFFICHAGES 
	
	// PAS DE RSS
    // require_once("$CFG->libdir/rsslib.php");

    $id    = optional_param('id', 0, PARAM_INT);    // course module id    
	$d     = optional_param('d', 0, PARAM_INT);    // referentielbase id

    $mode       = optional_param('mode','', PARAM_ALPHANUMEXT);
    $add        = optional_param('add','', PARAM_ALPHA);
    $update     = optional_param('update', 0, PARAM_INT);
    $delete     = optional_param('delete', 0, PARAM_INT);
    $approve    = optional_param('approve', 0, PARAM_INT);	
    $comment    = optional_param('comment', 0, PARAM_INT);		
    $courseid     = optional_param('courseid', 0, PARAM_INT);
    $groupmode  = optional_param('groupmode', -1, PARAM_INT);
    $cancel     = optional_param('cancel', 0, PARAM_BOOL);
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
	
    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/upload_logo.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : upload_logo.php'), 'referentiel');
	}

    $returnlink = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));

    require_login($course->id, false, $cm);

    if (!isloggedin() or isguestuser()) {
        redirect($returnlink);
    }

    $contextcourse = context_course::instance($course->id);
    $context = context_module::instance($cm->id);

	/// If it's hidden then it's don't show anything.  :)
	/// Some capability checks.
    if (empty($cm->visible)
    && (
        !has_capability('moodle/course:viewhiddenactivities', $context)
            &&
        !has_capability('mod/referentiel:managecomments', $context)
        )

    ) {
        print_error(get_string("activityiscurrentlyhidden"),'error',"$CFG->wwwroot/course/view.php?id=$course->id");
    }

    if ($referentiel->id) {    // So do you have access?
        if (!has_capability('mod/referentiel:writereferentiel', $context) or !confirm_sesskey() ) {
            print_error(get_string('noaccess','referentiel'));
        }
    }

	// variable d'action
	if (isset($mode)){
		if ($mode == "delete"){
			// Suppression du logo 
			if (!empty($referentiel_referentiel)){ // referentiel
                // mettre a jour le referentiel
                $DB->set_field ('referentiel_referentiel','logo_referentiel', '', array("id" => "$referentiel_referentiel->id"));
				redirect($returnlink);
			}
		}
	}
	

    // ensure the files area exists for this course	
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
            redirect($returnlink);
        }
    }


// AFFICHAGE DE LA PAGE Moodle 2
	$strmessage = get_string('modifier_referentiel','referentiel');
	$strpagename=get_string('modifier_referentiel','referentiel');
    $strreferentiel = get_string('modulename', 'referentiel');
    $strreferentiels = get_string('modulenameplural', 'referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);
    $PAGE->set_context($context);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->navbar->add($strpagename);
    $PAGE->set_title($pagetitle);
    // $PAGE->set_heading($title);
    $PAGE->set_heading($course->fullname);

    // formulaire d'importation d'un fichier
    $options = array('subdirs'=>0, 'maxbytes'=>get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, 0), 'maxfiles'=>1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
    $mform = new referentiel_upload_simple_form(null, array('d'=>$referentiel->id, 'contextid'=>$context->id, 'filearea'=>'referentiel',  'action' => 'importreferentiel', 'msg' => get_string('logo', 'referentiel'), 'options'=>$options));

    if ($mform->is_cancelled()) {
        redirect($returnlink);
    }
    else if ($mform->get_data()) {
        // A TERMINER
        // referentiel_upload_document($mform, $referentiel->id);
        if ($formdata = $mform->get_data()) {
            // DEBUG
            // echo "<br />DEBUG :: lib.php :: 5682 :: referentiel_upload_document\n";
            // print_object($formdata);

            if (!empty($formdata->filearea) && ($formdata->filearea=='referentiel')){
                $fs = get_file_storage();
                // suppression du fichier existant ?   NON
                // $fs->delete_area_files($formdata->contextid, 'mod_referentiel', $formdata->filearea, 0);

                if ($newfilename = $mform->get_new_filename('referentiel_file')) {
                    $file = $mform->save_stored_file('referentiel_file', $formdata->contextid,
                        'mod_referentiel',$formdata->filearea,0,'/', $newfilename);
                    // DEBUG
                    // echo "<br />DEBUG :: lib.php 5730 :: $newfilename\n";
                    // print_object($file);
                    $fullpath = "/$formdata->contextid/mod_referentiel/$formdata->filearea/0/$newfilename";
                    // $link = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);
                    // DEBUG
                    // echo "<br />DEBUG :: 239 :: $link<br />\n";
                    // mettre a jour le referentiel
                    if ($fullpath&& $referentiel_referentiel){
                        $DB->set_field ('referentiel_referentiel','logo_referentiel',$fullpath, array("id" => "$referentiel_referentiel->id"));
                    }
                }
            }
        }
        redirect($returnlink);
        die();
    }
    // afficher la boite de saisie du fichier
    echo $OUTPUT->header();
    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets 
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, 'listreferentiel', $select_acc, NULL);
    $tab_onglets->display();
    echo $OUTPUT->box_start('generalbox');

    $mform->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die();


?>

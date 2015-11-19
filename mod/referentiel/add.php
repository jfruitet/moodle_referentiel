<?php  // $Id: add.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
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
* Creation d'une premiere version du referentiel
* association a l'instance
*
* @version $Id: add.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
* @author Martin Dougiamas, Howard Miller, and many others.
*         {@link http://moodle.org}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package referentiel
*/
	
    require_once('../../config.php');
    require_once('locallib.php');
	require_once('print_lib_referentiel.php');

    $id    = optional_param('id', 0, PARAM_INT);    // course module id	
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel instance id
	$action  			= optional_param('action','', PARAM_ALPHA); // pour distinguer differentes formes de creation de referentiel
    $mode  				= optional_param('mode','add', PARAM_ALPHANUMEXT);
	$name_instance		= optional_param('name_instance','', PARAM_ALPHANUMEXT);
	$description_instance		= optional_param('description_instance','', PARAM_ALPHANUMEXT);
	$label_domaine    = optional_param('label_domaine','', PARAM_ALPHANUMEXT);
	$label_competence = optional_param('label_competence','', PARAM_ALPHANUMEXT);
	$label_item= optional_param('label_item','', PARAM_ALPHANUMEXT);
    $sesskey     		= optional_param('sesskey', '', PARAM_ALPHANUM);
    $coursemodule     	= optional_param('coursemodule', 0, PARAM_INT);
    $section 			= optional_param('section', 0, PARAM_INT);	
    $module 			= optional_param('module', 0, PARAM_INT);
	$modulename     	= optional_param('modulename', '', PARAM_ALPHA);
	$instance 			= optional_param('instance', 0, PARAM_INT);
 	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
    $non_redirection = optional_param('non_redirection', 0, PARAM_INT);    // par defaut on redirige vers activite

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/add.php');

    if ($d) {     // referentiel_referentiel_id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }

		if (! $course = $DB->get_record("course", array("id" => "$referentiel->course"))) {
	            print_error('Course is misconfigured');
    	}

		if (! $cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $course->id)) {
    	        print_error('Course Module ID is incorrect');
		}
		// nouveaute Moodle 2
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
        // nouveaute Moodle 2
        $url->param('id', $id);
    }
	else{
        // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : add.php'));
	}
	
    if ($mode !== 'all') {
        $url->param('mode', $mode);
    }
    
    $url->param('non_redirection', $non_redirection);


	$returnlink_erreur=$CFG->wwwroot.'/course/view.php?id='.$course->id;
	$returnlink_suite=$CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1';
	$returnlink_modification=$CFG->wwwroot.'/mod/referentiel/edit.php?id='.$cm->id.'&amp;sesskey='.sesskey();
	
    require_login($course->id, false, $cm);   // pas d'autologin guest
    
    if (!isloggedin() || isguestuser()) {   // nouveaute Moodle 2
        redirect($returnlink_erreur);
    }

    // check role capability

    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}


    if ($referentiel->id) {    // So do you have access?
        if (!has_capability('mod/referentiel:writereferentiel', $context)
        or !confirm_sesskey() ) {
            print_error(get_string('noaccess','referentiel'));
        }
    }
	else{
		print_error('Referentiel instance is incorrect');
	}
	
	if (isset($referentiel->ref_referentiel) && ($referentiel->ref_referentiel>0)){
		redirect($returnlink_modification);
	}
	
	// RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else {
        $form = (object)$_POST;
    }
	
	// DEBUG
	// print_r($form);
	
	// Traitement des POST
	if (!empty($course) && !empty($referentiel) && !empty($form)) {    
		// select form submitted	
		// DEBUG
		// echo "<br /> MODE : $mode ACTION : $action<br />\n";
		// echo "<br />FORM<br />\n";		
		// print_r($form);

		// variable d'action
		if (!empty($form->cancel)){
			if ($form->cancel == get_string("quit", "referentiel")){
				// Abandonner
    		    redirect($returnlink_erreur);
       			exit;
			}
		}
		
		if (!empty($cancel)){
			if ($cancel == get_string("quit", "referentiel")){
				// Abandonner
    		    redirect($returnlink_erreur);
       			exit;
			}
		}

		if (!empty($action) && ($action=="modifierinstance")){
			// modifier l'instance du referentiel
			$return = referentiel_update_instance($form);
        	if (!$return) {
				print_error("ERROR 1 : Could not update the referentiel instance", $returnlink_erreur);
			}
			//  recharger le referentiel modifie
	        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$referentiel->id"))) {
    	        print_error('Certification instance '.$return.' is incorrect');
        	}
	        if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    	$params = array(
        	       	'contextid' => $context->id,
            	   	'objectid' => $referentiel->id,
            	);
            	$event = \mod_referentiel\event\course_module_updated::create($params);
	            $event->trigger();
    	    } else { // Before Moodle 2.7
	        	add_to_log($course->id, 'referentiel', 'update', "referentiel $referentiel->name of id $referentiel->id updated", "add.php?id=$cm->id", $cm->id);
           	}
			// pas de redirection car il faut peut �tre encore selectionner le referentiel
		}
		
		if (!empty($action) && (($action=="selectreferentiel") || ($action=="importreferentiel"))){
			// retour de selection ou d'importation : associer l'instance au referentiel
			// echo "<br />add.php :: 145 :: ACTION : $action ; FORM<br />\n";		
			// print_r($form);
			// MODIF JF 2012/03/07
			$return = referentiel_associe_referentiel_instance($form->instance, $form->new_referentiel_id);
        	if (!$return) {
				print_error("Error 2 : Could not update the referentiel instance", $returnlink_erreur);
			}
			//  recharger le referentiel modifie
	        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$referentiel->id")))
            {
    	        print_error('Certification instance '.$return.' is incorrect');
        	}
	        if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    	$params = array(
        	       	'contextid' => $context->id,
            	   	'objectid' => $referentiel->id,
            	);
            	$event = \mod_referentiel\event\course_module_updated::create($params);
	            $event->trigger();
    	    } else { // Before Moodle 2.7
                add_to_log($course->id, 'referentiel', 'update', "add.php?id=$cm->id", "$referentiel->name $referentiel->id");
			}
			// echo "<br />add.php :: 157 :: RETOUR : $returnlink_suite ; FORM<br />\n";					
			redirect($returnlink_suite);
	        exit;
		}
		
		if (!empty($action) && ($action=="modifierreferentiel")){
			// sauvegarder le referentiel
			$return_referentiel_id = referentiel_add_referentiel_domaines($form);
    	    if (!$return_referentiel_id) {
				print_error(get_string('erreur_creation','referentiel'), $returnlink_erreur);
			}
    	    if (is_string($return_referentiel_id)) {
        		print_error($return_referentiel_id, $returnlink_erreur);
	        }
	        if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    	$params = array(
        	       	'contextid' => $context->id,
            	   	'objectid' => $return_referentiel_id,
            	);
            	$event = \mod_referentiel\event\course_module_updated::create($params);
	            $event->trigger();
    	    } else { // Before Moodle 2.7
                add_to_log($course->id, "referentiel", "write", "add.php?id=$cm->id", "$form->name $return_referentiel_id");
			}

			// associer le referentiel
			$form->new_referentiel_id=$return_referentiel_id;
			$return = referentiel_associe_referentiel_instance($referentiel->id, $return_referentiel_id);
        	if (!$return) {
				print_error("Error 3 : Could not update the referentiel instance 3 ", $returnlink_erreur);
			}
			//  recharger le referentiel modifie
	        if (! $referentiel = $DB->get_record("referentiel", array("id"=>"$referentiel->id"))) {
    	        print_error('Referentiel instance '.$form->instance.' is incorrect');
        	}
	        if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    	$params = array(
        	       	'contextid' => $context->id,
            	   	'objectid' => $referentiel->id,
            	);
            	$event = \mod_referentiel\event\course_module_updated::create($params);
	            $event->trigger();
    	    } else { // Before Moodle 2.7
	    	    add_to_log($course->id, 'referentiel', 'update', "add.php?d=$referentiel->id", "$referentiel->name $referentiel->id");
			}
			redirect($returnlink_suite);
	        exit;
		}
	}
	
	/// RSS and CSS and JS meta
    $meta = '';

	/// Print the page header
    $PAGE->set_url($url);
    $PAGE->requires->js('/mod/referentiel/functions.js');
    $referentielinstance = new referentiel($cm->id, $referentiel, $cm, $course);
    /// Mark as viewed
    //$completion=new completion_info($course);
    //$completion->set_module_viewed($cm);
    $referentielinstance->add($non_redirection, $mode);   // Actually select the referentiel!

?>

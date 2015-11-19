<?php  // $Id: scolarite.php,v 1.0 2008/05/03 00:00:00 jfruitet Exp $
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

    require(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');
    require_once('lib_etab.php');
    require_once('print_lib_etablissement.php');	// AFFICHAGES 
	
	// PAS DE RSS
    // require_once("$CFG->libdir/rsslib.php");

    $id    = optional_param('id', 0, PARAM_INT);    // course module id    
	$d     = optional_param('d', 0, PARAM_INT);    // referentielbase id
	
    $etablissement_id   = optional_param('etablissement_id', 0, PARAM_INT);    //record etablissement id

    // $import   = optional_param('import', 0, PARAM_INT);    // show import form

    $action  	= optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements
    $mode       = optional_param('mode','', PARAM_ALPHA);
    $add        = optional_param('add','', PARAM_ALPHA);
    $update     = optional_param('update', 0, PARAM_INT);
    $delete     = optional_param('delete', 0, PARAM_INT);
    $approve    = optional_param('approve', 0, PARAM_INT);	
    $comment    = optional_param('comment', 0, PARAM_INT);		
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $groupmode  = optional_param('groupmode', -1, PARAM_INT);
    $cancel     = optional_param('cancel', 0, PARAM_BOOL);
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
	
    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/etablissement.php');

	if ($d) {     // referentiel_referentiel_id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('R�ferentiel id is incorrect');
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
		print_error(get_string('erreurscript','referentiel','Erreur01 : etablissement.php'), 'referentiel');
	}

	// Moodle 2

    $url->param('mode', $mode);
	
	if (isset($etablissement_id) && ($etablissement_id>0)) { 
		// id etablissement
        if (! $record_etab = $DB->get_record("referentiel_etablissement", array("id" => "$etablissement_id"))) {
            print_error('etablissement id is incorrect');
        }
	}

    require_login($course->id, false, $cm);   // pas d'autologin guest

    if (!isloggedin() or isguestuser()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
    }


    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

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

    if ($etablissement_id) {    // So do you have access?
        if (!(has_capability('mod/referentiel:managecertif', $context) 
			or ($USER->id==$etablissement_id)) or !confirm_sesskey() ) {
            print_error(get_string('noaccess','referentiel'));
        }
    }
	
	// selecteur
	$search="";
	
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
            redirect('etablissement.php?d='.$referentiel->id);
        }
    }
 	
	/// Delete any requested records
    if (isset($delete) && ($delete>0 )
			&& confirm_sesskey() 
			&& (has_capability('mod/referentiel:managecertif', $context) or referentiel_etablissement_isowner($delete))) {
        if ($confirm = optional_param('confirm',0,PARAM_INT)) {
            if (referentiel_delete_etablissement($delete)){
				// DEBUG
				// echo "<br /> etablissement REMIS A ZERO\n";
				// exit;
				add_to_log($course->id, 'referentiel', 'record delete', "etablissement.php?d=$referentiel->id", $delete, $cm->id);
                // notify(get_string('recorddeleted','referentiel'), 'notifysuccess');
            }
		} 
		$mode='listeetab';
    }
	

	if (!empty($referentiel) && !empty($course) 
		&& isset($form) && isset($form->mode)
		)
	{
		// update form submitted
		switch ($form->mode) {
			case "addetab":
				// echo "<br /> $form->mode\n";
				
				if (isset($form->name)) {
   		        	if (trim($form->name) == '') {
       		        	unset($form->name);
           		    }
               	}
    	    	$return = referentiel_add_etablissement($form);
   	    	    if (!$return) {
					/*
            		    if (file_exists($moderr)) {
                			$form = $form;
                    		include_once($moderr);
                        	die;
	                    }
					*/
    		       	print_error("Could not create etablissement  of the referentiel", "etablissement.php?d=$referentiel->id");
				}
		        if (is_string($return)) {
    		       	print_error($return, "etablissement.php?d=$referentiel->id");
	    	    }
	        	if (isset($form->redirect)) {
    	       		$SESSION->returnpage = $form->redirecturl;
				} 
				else {
        	   		$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/etablissement.php?d=$referentiel->id";
	            }
				add_to_log($course->id, "referentiel", "update",
            	           "mise a jour etablissement ",
                           "$return", "");
			break;
			
    		case "updateetab":
			
				// DEBUG
				// echo "<br /> $form->mode\n";
				
				if (isset($form->name)) {
   		        	if (trim($form->name) == '') {
       		        	unset($form->name);
           		    }
               	}
				
				// DEBUG
				// echo "<br /> UPDATE : 220 \n";
				// print_object($form);
				// exit;
	    	    	$return = referentiel_update_etablissement($form);
    	    	    if (!$return) {
					/*
            		    if (file_exists($moderr)) {
                			$form = $form;
                    		include_once($moderr);
                        	die;
	                    }
					*/
    	            	print_error("Could not update etablissement  of the referentiel", "etablissement.php?d=$referentiel->id");
					}
		            if (is_string($return)) {
    		        	print_error($return, "etablissement.php?d=$referentiel->id");
	    		    }
	        		if (isset($form->redirect)) {
    	        		$SESSION->returnpage = $form->redirecturl;
					} 
					else {
        	    		$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/etablissement.php?d=$referentiel->id";
	        	    }
					add_to_log($course->id, "referentiel", "update",
            	           "mise a jour etablissement ",
                           "$form->etablissement_id", "");
			break;
			
            
			default:
            	// print_error("No mode defined");
        }
		$mode='listeetab';
	}

	// afficher les formulaires

    unset($SESSION->modform); // Clear any old ones that may be hanging around.

    $modform = "etablissement.html";

    /// Check to see if groups are being used here
	/// find out current groups mode
	$groupmode = groups_get_activity_groupmode($cm);
    $currentgroup = groups_get_activity_group($cm, true);

   	/// Get all users that are allowed to submit activite
	$gusers=NULL;
    if ($gusers = get_users_by_capability($context, 'mod/referentiel:write', 'u.id', 'u.lastname', '', '', $currentgroup, '', false)) {
    	$gusers = array_keys($gusers);
    }
	// if groupmembersonly used, remove users who are not in any group
    if ($gusers and !empty($CFG->enablegroupings) and $cm->groupmembersonly) {
    	if ($groupingusers = groups_get_grouping_members($cm->groupingid, 'u.id', 'u.id')) {
       		$gusers = array_intersect($gusers, array_keys($groupingusers));
       	}
    }

    /// Print the tabs
	if (!isset($mode) || ($mode=="")){
		$mode='scolarite';
	}

	// DEBUG
	// echo "<br /> MODE : $mode\n";

	if (isset($mode) && (($mode=="deleteetab") || ($mode=="updateetab"))){
		$currenttab ='listeetab';
	}
	else{
		$currenttab = $mode;
	}
	if ($currenttab == 'addetab'){
			$currenttab = 'manageetab';
	}

    if ($etablissement_id) {
       	$editentry = true;  //used in tabs
    }
    
    //$completion=new completion_info($course);
    //$completion->set_module_viewed($cm);
    
	/// Print the page header
	$stretablissement = get_string('etablissement','referentiel');
	$strpagename=get_string('etablissements','referentiel');
	$stretudiant = get_string('etudiant','referentiel');

    $strreferentiel = get_string('modulename', 'referentiel');
    $strreferentiels = get_string('modulenameplural', 'referentiel');

    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->navbar->add($strpagename);
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

	groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/etablissement.php?d='.$referentiel->id.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc);

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();
    echo '<br />'."\n";
    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title=""  alt="" /> '.$strpagename.' '.$OUTPUT->help_icon('etablissementh','referentiel').'</h2></div>'."\n";

    // print_heading_with_help($stretablissement, 'etablissement', 'referentiel', $icon);
	if ($mode=='listeetab'){
		referentiel_print_liste_etablissements($mode, $referentiel, $search); 
	}
	else {
		echo $OUTPUT->box_start('generalbox  boxaligncenter');
    	
		// formulaires
		if ($mode=='updateetab'){
			// recuperer l'id du etablissement apr�s l'avoir genere automatiquement et mettre en place les competences
			
			if (!empty($etablissement_id)) { // id etablissement
    	   		if (!$record_etab){
                    if (!$record_etab = $DB->get_record("referentiel_etablissement", array("id" => "$etablissement_id"))) {
                        print_error('etablissement ID is incorrect');
                    }
    		    }
			}
			else{
				print_error('etablissement ID is incorrect');
			}
			$modform = "etablissement.html";
		}
		else if ($mode=='deleteetab'){
			// recuperer l'id du etablissement apr�s l'avoir genere automatiquement et mettre en place les competences
			
			if (!empty($etablissement_id)) { // id etablissement
    	   		if (!$record_etab){
                    if (!$record_etab = $DB->get_record("referentiel_etablissement", array("id" => "$etablissement_id"))) {
                        print_error('etablissement ID is incorrect');
                    }
    		    }
			}
			else{
				print_error('etablissement ID is incorrect');
			}
			$modform = "etablissement.html";
		}
		else if ($mode=='addetab'){
			// genere automatiquement
			if (!$etablissement_id){
				// confirmer la cr�ation d'un nouvel �tablissement ?
				$modform = "etablissement_add.html";
				// $etablissement_id=referentiel_genere_etablissement();
			}
			else { // id etablissement
    	   		if (! $record_etab = $DB->get_record("referentiel_etablissement", array("id" => "$etablissement_id"))) {
		            print_error('etablissement ID is incorrect');
    		    }
				$mode='updateetab';
				$modform = "etablissement.html";
			}
		}
	    if (file_exists($modform)) {
       	    $defaultformat = FORMAT_PLAIN;
            $editorfields = '';
		}
		else {
    	 	notice("ERREUR : No file found at : $modform)", "etablissement.php?d=$referentiel->id");
    	}
		
		include_once($modform);
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->footer();
    die();

?>

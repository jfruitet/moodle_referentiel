<?php  // $Id: certificat.php,v 1.0 2008/05/03 00:00:00 jfruitet Exp $
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

	require_once('print_lib_activite.php'); // AFFICHAGES ACTIVITES
    require_once('lib_task.php');
    require_once('print_lib_task.php');	// AFFICHAGES TACHES

    $id    = optional_param('id', 0, PARAM_INT);    // course module id    
	$d     = optional_param('d', 0, PARAM_INT);    // referentielbase id
    $mode  = optional_param('mode', '', PARAM_ALPHANUMEXT);    // Force the browse mode  ('single')
    $taskid   = optional_param('taskid', 0, PARAM_INT);    //record task id
    $action  	= optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements
    $mailnow    = optional_param('mailnow', 0, PARAM_INT);
    $add        = optional_param('add','', PARAM_ALPHA);
    $update     = optional_param('update', 0, PARAM_INT);
    $delete     = optional_param('delete', 0, PARAM_INT);
    $deleteall  = optional_param('deleteall', 0, PARAM_INT);
    $select     = optional_param('select', 0, PARAM_INT);
    $courseid   = optional_param('courseid', 0, PARAM_INT);
    $groupmode  = optional_param('groupmode', -1, PARAM_INT);
    $cancel     = optional_param('cancel', 0, PARAM_BOOL);
  	$approve    = optional_param('approve', 0, PARAM_INT);	
	$souscription    = optional_param('souscription', 0, PARAM_INT);
	$hide    = optional_param('hide', -1, PARAM_INT);
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
	
     // Filtres
    require_once('filtres.php'); // Ne pas deplacer

    $url = new moodle_url('/mod/referentiel/task.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : task.php'), 'referentiel');
	}


	if ($taskid) { // id task
        if (! $record = $DB->get_record('referentiel_task', array("id" => "$taskid"))) {
            print_error('task ID is incorrect');
        }
	}

    $context = context_module::instance($cm->id);

    require_login($course->id, false, $cm);   // pas d'autologin guest
    if (!isloggedin()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
    }

	/// If it's hidden then it's don't show anything.  :)
	/// Some capability checks.
    if (empty($cm->visible)
    && (
        !has_capability('moodle/course:viewhiddenactivities', $context)
            &&
        !has_capability('mod/referentiel:managecomments', $context)
        )

    ) {
        print_error(get_string("activityiscurrentlyhidden"),'error',$CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

	// selecteur
	$userid_filtre=0;
	
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
            redirect($CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc);
        }
    }

	/// selection d'utilisateurs
    if (isset($action) && ($action=='selectuser')
		&& isset($form->userid) && ($form->userid>0)
		&& confirm_sesskey() ){
		$userid_filtre=$form->userid;
		unset($form);
		unset($action);
		// exit;
    }

	/// Delete any requested records
    if (isset($delete) && ($delete>0 )
			&& confirm_sesskey() 
			&& (has_capability('mod/referentiel:addtask', $context) or referentiel_task_isowner($delete))) {
        if ($confirm = optional_param('confirm',0,PARAM_INT)) {
			// verifier que la tache existe
			if (referentiel_delete_task_record($delete)){
        		if ($CFG->version > 2014051200) { // Moodle 2.7+
            		$params = array(
                		'contextid' => $context->id,
                		'objectid' => $delete,
            		);
            		$event = \mod_referentiel\event\task_deleted::create($params);
            		$event->trigger();
        		} else { // Before Moodle 2.7
					add_to_log($course->id, 'referentiel', 'record delete', "task.php?id=$cm->id", $delete, $cm->id);
				}
            }
        } 
    }

    if (isset($deleteall) && ($deleteall>0 )
        && has_capability('mod/referentiel:addtask', $context)
		&& confirm_sesskey()){
    	/// Delete any requested records  and mapped activities
        if ($confirm = optional_param('confirm',0,PARAM_INT)) {
			// detruire la taches, les consignes et les activites associes
            if (referentiel_delete_task_and_activities($deleteall)){
        		if ($CFG->version > 2014051200) { // Moodle 2.7+
            		$params = array(
                		'contextid' => $context->id,
                		'objectid' => $deleteall,
						'other' => array('msg' => 'All activities linked to this task have been deleted too.'),
            		);
            		$event = \mod_referentiel\event\task_deleted::create($params);
            		$event->trigger();
        		} else { // Before Moodle 2.7
					add_to_log($course->id, 'referentiel', 'record delete', "task.php?id=$cm->id", $deleteall, $cm->id);
				}
            }
		}
    }

	/// Hide any requested records
    if (!empty($taskid) && has_capability('mod/referentiel:addtask', $context)) {
		if (isset($hide) && ($hide!=-1) && confirm_sesskey()){ // Masquer cette tache
            referentiel_mask_task($taskid, $hide);
        }
    }
		
	
	/// Approve any requested records
    if (isset($approve) && ($approve>0) && confirm_sesskey() 
		&& has_capability('mod/referentiel:approve', $context)) {
		// Valider toutes les activités qui pointent vers cette tache
        $confirm = optional_param('confirm',0,PARAM_INT);
        if ($confirm) {
			 referentiel_validation_activite_task($approve);
        }
    }
	
	/// Selection tache
    if (isset($select) && ($select>0) && confirm_sesskey()
		&& has_capability('mod/referentiel:selecttask', $context)) {
        // Rechercher le referent
        $a_referentid=0;
        $referentids=referentiel_get_accompagnements_user($referentiel->id, $course->id, $USER->id);

        if ($referentids){
            // print_object($referentids);
            // exit;
            foreach($referentids as $referentid){
                if ($referentid->userid){
                    $a_referentid=$referentid->userid;
                    break;
                }
            }
            // choisir le premier de la liste !
        }
		if (referentiel_association_user_task($USER->id, $select, $a_referentid, $mailnow, false)){
      		if ($CFG->version > 2014051200) { // Moodle 2.7+
           		$params = array(
               		'contextid' => $context->id,
               		'objectid' => $select,
           		);
           		$event = \mod_referentiel\event\task_linked::create($params);
           		$event->trigger();
       		} else { // Before Moodle 2.7
				add_to_log($course->id, 'referentiel', 'task', "task.php?id=$cm->id", $select, $cm->id);
			}
            redirect("$CFG->wwwroot/mod/referentiel/activite.php?id=$cm->id&amp;select_acc=$select_acc&amp;mode=listactivityall&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student");
		}
    }
	

	if (!empty($taskid) && ($mode=='imposetask')
        && has_capability('mod/referentiel:addtask', $context) ){
        redirect($CFG->wwwroot.'/mod/referentiel/souscription.php?d='.$referentiel->id.'&taskid='.$taskid.'&amp;select_acc='.$select_acc.'&amp;sesskey='.sesskey());
        exit;
	}

	
	if (!empty($referentiel) && !empty($course) 
		&& isset($form) 
		&& isset($form->mode)
		)
	{
        // pour eviter un warning
        if (!isset($select_all)){
            $select_all=0;
        }
        if (!isset($form->souscription_forcee)){
            $form->souscription_forcee=0;
        }

		// add, delete or update form submitted	
		$addfunction    = "referentiel_add_task";
        $updatefunction = "referentiel_update_task";
        $deletefunction = "referentiel_delete_task";

		switch ($form->mode) {
		    case "deletetaskall":
                if (isset($form->name)) {
                    if (trim($form->name) == '') {
       		           unset($form->name);
                    }
                }

                if (isset($form) && !empty($form->taskid) && !empty($form->t_activite)){
                    $select_sql='';
                    foreach ($form->t_activite as $activite_id){
                        referentiel_delete_activity_record($activite_id);
                    }
                    referentiel_delete_task_record($form->taskid);
                }
                break;


		    case "approve":
                if (isset($form->name)) {
                    if (trim($form->name) == '') {
       		           unset($form->name);
                    }
                }
		    
                if (isset($form) && !empty($form->taskid) && !empty($form->t_activite)){
                    $select_sql='';
                    foreach ($form->t_activite as $activite_id){
                        if (empty($select_sql)){
                            $select_sql.= ' AND ((ref_activite='.$activite_id.') ';
                        }
                        else{
                            $select_sql.= ' OR (ref_activite='.$activite_id.') ';
                        }
                    }
                    if (!empty($select_sql)){
                        $select_sql.= ') ';
                        // echo "<br />DEBUG :: $select_sql\n";
                        // exit;
                        referentiel_validation_activite_task($form->taskid, $select_sql);
                    }
                }
                break;
		
    		case "updatetask":
                if (isset($form->name)) {
                    if (trim($form->name) == '') {
                        unset($form->name);
                    }
                }

                if (isset($form->delete_all_task_associations) && ($form->delete_all_task_associations==get_string('delete_all_task_associations', 'referentiel'))){
                    // suppression de la tache et de toutes les activites associes

                    $return = referentiel_delete_task_and_activities($form->taskid);
                    if (!$return) {
                        print_error("Could not delete task $form->taskid of the referentiel", "task.php?id=$cm->id");
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
	            		$params = array(
    	            		'contextid' => $context->id,
        	        		'objectid' => $form->taskid,
							'other' => array('msg' => 'All activities linked to this task have been deleted too.'),
            			);
            			$event = \mod_referentiel\event\task_deleted::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
                        add_to_log($course->id, 'referentiel', 'delete', "task $form->taskid deleted", $cm->id, "");
					}
                }
                elseif (isset($form->delete) && ($form->delete==get_string('delete'))){
                    // suppression
					// echo "<br />SUPPRESSION\n";
                    $return = $deletefunction($form);
                    if (!$return) {
                        print_error("Could not delete task $taskid of the referentiel", "task.php?id=$cm->id");
                    }
                    if (is_string($return)) {
                        print_error($return, "task.php?id=$cm->id");
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
	            		$params = array(
    	            		'contextid' => $context->id,
        	        		'objectid' => $form->taskid,
            			);
            			$event = \mod_referentiel\event\task_deleted::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
                        add_to_log($course->id, "referentiel", "delete", "task $form->taskid deleted","$form->instance", "");
					}
                }
				else {
                    $return = $updatefunction($form);
                    if (!$return) {
                        print_error("Could not update task $form->id of the referentiel", "task.php?id=$cm->id");
					}
                    if (is_string($return)) {
                        print_error($return, "task.php?id=$cm->id");
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
	            		$params = array(
    	            		'contextid' => $context->id,
        	        		'objectid' => $form->taskid,
            			);
            			$event = \mod_referentiel\event\task_updated::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
						add_to_log($course->id, "referentiel", "update", "task $form->taskid updated", "$form->instance", "");
					}
					// depot de consigne ?
					if (isset($form->depot_consigne) && ($form->depot_consigne==get_string('yes'))){
						// APPELER le script
						if (isset($form->ref_task) && ($form->ref_task>0)){
							if (isset($form->consigne_id) && ($form->consigne_id>0)){
								redirect($CFG->wwwroot.'/mod/referentiel/upload_consigne.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&taskid='.$form->ref_task.'&consigne_id='.$form->consigne_id.'&amp;mode=updateconsigne&soucription='.$form->souscription_forcee.'&amp;sesskey='.sesskey());
								exit;
							}
							else{
								redirect($CFG->wwwroot.'/mod/referentiel/upload_consigne.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&taskid='.$form->ref_task.'&consigne_id=0&amp;mode=addconsigne&soucription='.$form->souscription_forcee.'&amp;sesskey='.sesskey());
								exit;
							}

						}
					}
					
					// souscription_forcee
					if (isset($form->souscription_forcee) && ($form->souscription_forcee=='1')){
                        if (isset($form->taskid) && ($form->taskid>0)){
							redirect($CFG->wwwroot.'/mod/referentiel/souscription.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&taskid='.$form->taskid.'&amp;sesskey='.sesskey());
							exit;
					   }
                    }

                }


                if (isset($form->redirect) and !empty($form->redirecturl)) {
                    $SESSION->returnpage = $form->redirecturl;
    		    }
                else {
                    $SESSION->returnpage = $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&amp;mode=listtasksingle';
                }

			    break;
			
			case "addtask":
				if (!isset($form->name) || trim($form->name) == '') {
        			$form->name = get_string("modulename", "referentiel");
                }
        
				$return = $addfunction($form);
				if (!$return) {
					print_error("Could not add a new task to the referentiel", "task.php?id=$cm->id");
				}
                if (is_string($return)) {
    	        	print_error($return, $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id);
				}
				// depot de consigne ?
				if (isset($form->depot_consigne) && ($form->depot_consigne==get_string('yes'))){
					// APPELER le script
						if ($return){
							redirect($CFG->wwwroot.'/mod/referentiel/upload_consigne.php?d='.$referentiel->id.'&taskid='.$return.'&amp;select_all='.$select_all.'&consigne_id=0&amp;mode=addconsigne&soucription='.$form->souscription_forcee.'&amp;sesskey='.sesskey());
							exit;
						}
				}
				// souscription_forcee
				if (isset($form->souscription_forcee) && ($form->souscription_forcee=='1')){
						if ($return){
							redirect($CFG->wwwroot.'/mod/referentiel/souscription.php?d='.$referentiel->id.'&amp;select_all='.$select_all.'&taskid='.$return.'&amp;sesskey='.sesskey());
							exit;
						}
                }

                if (isset($form->redirect) and !empty($form->redirecturl)) {
    	    		$SESSION->returnpage = $form->redirecturl;
				} 
				else {
					$SESSION->returnpage = $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id.'&amp;select_all='.$select_all.'&amp;mode=listtasksingle';
				}
	        	if ($CFG->version > 2014051200) { // Moodle 2.7+
	            	$params = array(
    	           		'contextid' => $context->id,
        	       		'objectid' => $form->taskid,
            		);
            		$event = \mod_referentiel\event\task_created::create($params);
	            	$event->trigger();
    	    	} else { // Before Moodle 2.7
					add_to_log($course->id, "referentiel", "add", "creation task $form->taskid ", "$form->instance", "");
				}
                break;
			
	        case "deletetask":
				if (! $deletefunction($form)) {
					print_error("Could not delete task of  the referentiel");
                }
				else{
	            	unset($SESSION->returnpage);
		        	if ($CFG->version > 2014051200) { // Moodle 2.7+
		            	$params = array(
    		           		'contextid' => $context->id,
        		       		'objectid' => $form->taskid,
            			);
            			$event = \mod_referentiel\event\task_deleted::create($params);
		            	$event->trigger();
    		    	} else { // Before Moodle 2.7
        	            add_to_log($course->id, "referentiel", "delete", "task $form->taskid deleted", "$form->instance", "");
					}
				}
				break;
            
			default:
            	// print_error("No mode defined");
        }

    	if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
	        unset($SESSION->returnpage);
    	    redirect($return);
        } 
		else {
	    	redirect($CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&amp;mode=listtasksingle');
    	}
		
        exit;
	}

	/// selection filtre
    if (empty($userid_filtre) || ($userid_filtre==$USER->id)
        || (isset($mode_select) && ($mode_select=='selectetab'))){
        set_filtres_sql();
    }

	// afficher les formulaires

    unset($SESSION->modform); // Clear any old ones that may be hanging around.
    $modform = "task_inc_html.php";

    if (($mode=='approvetask') || ($mode=='deletetaskactivites')){
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
    }
    

	/// Print the tabs
	if (!empty($taskid) && empty($mode)){
		$mode='listtasksingle';
	}

	if (empty($mode) || ($mode=="list")){
		$mode='listtask';
	}

	if (!empty($mode) && (($mode=="approvetask") || ($mode=="deletetaskactivites"))){
		$currenttab ='listtasksingle';
	}
	elseif (isset($mode) && (($mode=="deletetask") 
    	|| ($mode=="deletetaskall")
		|| ($mode=="approve")
        || ($mode=="commenttask"))){
		$currenttab ='updatetask';		
	}
	else if (isset($mode) && ($mode=='listtasksingle')){
		$currenttab ='listtasksingle';
	}
	else if (isset($mode) && ($mode=='selecttask')){
		$currenttab ='selecttask';
	}
	else if (isset($mode) && ($mode=='listtask')){
		$currenttab ='listtask';
	}
	else{
		$currenttab = $mode;
	}
	
	// Moodle 2
    $url->param('mode', $mode);

// AFFICHAGE DE LA PAGE Moodle 2
	/// RSS and CSS and JS meta
	/// Print the page header
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
	$strtask = get_string('task','referentiel');
    $strtasks = get_string('tasks','referentiel');
	$icon = $OUTPUT->pix_url('icon','referentiel');
	/// RSS and CSS and JS meta

	$strpagename=get_string('tasks','referentiel');

    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));

    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->css('/mod/referentiel/dhtmlgoodies_calendar.css');
    $PAGE->requires->js($OverlibJs);
    $PAGE->requires->js('/mod/referentiel/functions.js');

    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);
    $PAGE->navbar->add($strtasks);
    
    echo $OUTPUT->header();
    
    if (($mode=='approvetask') || ($mode=='deletetaskactivites')){
        groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/task.php?d='.$referentiel->id.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc);
    }
    
    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets 
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, $data_f, $mode);
    $tab_onglets->display();

    // print_heading_with_help($strtask, 'task', 'referentiel', $icon);
    echo '<div align="center"><h1><img src="'.$icon.'" border="0" title="referentiel" alt="referentiel" /> '.$strtask.' '.$OUTPUT->help_icon('taskh','referentiel').'</h1></div>'."\n";

    // DEBUG
    // echo "<br />MODE : $mode\n";

	if (($mode=='list') || ($mode=='listtask')){
		referentiel_print_liste_tasks($mode, $referentiel);
	}
	else if ($mode=='listtasksingle'){
		if (!empty($taskid)){
			referentiel_print_taskid($taskid, $referentiel);
		}
		else{
			referentiel_print_liste_tasks($mode, $referentiel); 
		}
	}
	else if ($mode=='approvetask'){
		if (isset($taskid) && ($taskid>0)){
			//Bareme
			$bareme=NULL;
			if ($CFG->referentiel_use_scale){
				require_once('lib_bareme.php');
				//echo "<br />OCCURRENCE<br />\n";
				//print_object($referentiel_referentiel);
				//echo "<br />\n";
                if ($rec_assoc=referentiel_get_assoc_bareme_occurrence($referentiel_referentiel->id)){
					// DEBUG
					//echo "<br />A BAREME OCCURRENCE<br />\n";
					//print_object($rec_assoc);
					//echo "<br />\n";
                    $bareme=referentiel_get_bareme($rec_assoc->refscaleid);
				}
			}

			referentiel_print_activities_task($bareme, $taskid, $referentiel,'approvetask', $userid_filtre, $gusers);
		}
		else{
			referentiel_print_liste_tasks($mode, $referentiel );
		}
	}
	else if ($mode=='deletetaskactivites'){
		if (isset($taskid) && ($taskid>0)){
			//Bareme
			$bareme=NULL;
			if ($CFG->referentiel_use_scale){
				require_once('lib_bareme.php');
				//echo "<br />OCCURRENCE<br />\n";
				//print_object($referentiel_referentiel);
				//echo "<br />\n";
                if ($rec_assoc=referentiel_get_assoc_bareme_occurrence($referentiel_referentiel->id)){
					// DEBUG
					//echo "<br />A BAREME OCCURRENCE<br />\n";
					//print_object($rec_assoc);
					//echo "<br />\n";
                    $bareme=referentiel_get_bareme($rec_assoc->refscaleid);
				}
			}

			referentiel_print_activities_task($bareme, $taskid, $referentiel,'deletetaskactivites', $userid_filtre, $gusers);
		}
		else{
			referentiel_print_liste_tasks($mode, $referentiel );
		}
	}
	else {
		//print_simple_box_start('center', '', '', 5, 'generalbox', $referentiel->name);
		echo $OUTPUT->box_start('generalbox  boxaligncenter');
		if ($mode=='updatetask'){
			// recuperer l'id de la tache
			if ($taskid) { // id 	task
    	    	if (! $record = $DB->get_record('referentiel_task', array("id" => "$taskid"))) {
			    	print_error('task ID is incorrect');
    			}
			}
			$modform = "task_inc_html.php";
		}
    	// formulaires
	    if (file_exists($modform)) {
       	    $defaultformat = FORMAT_PLAIN;
           	$editorfields = '';
        }
		else {
    	    notice("ERREUR : No file found at : $modform)", "task.php?id=$cm->id");
    	}

		include_once($modform);
		//   print_simple_box_end();
		echo $OUTPUT->box_end();
	} 
    echo $OUTPUT->footer();
    die();

?>

<?php  // $Id: souscription.php,v 1.0 2010/01/10 00:00:00 jfruitet Exp $
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
    $taskid   = required_param('taskid', PARAM_INT);    //record task id
    $mailnow    = optional_param('mailnow', 0, PARAM_INT);
    $action  	= optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements
    $mode       = optional_param('mode','', PARAM_ALPHA);	
    $add        = optional_param('add','', PARAM_ALPHA);
    $update     = optional_param('update', 0, PARAM_INT);
    $delete     = optional_param('delete', 0, PARAM_INT);
    $select    = optional_param('select', 0, PARAM_INT);
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $groupmode  = optional_param('groupmode', -1, PARAM_INT);
    $cancel     = optional_param('cancel', 0, PARAM_BOOL);
	$approve    = optional_param('approve', 0, PARAM_INT);
	$souscription    = optional_param('souscription', 0, PARAM_INT);
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement


     // Filtres
    require_once('filtres.php'); // Ne pas deplacer

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/souscription.php');
    
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
    }
	else{
      // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : souscription.php'));
	}
	
    $context = context_module::instance($cm->id);

	if ($taskid) { // id task
        if (! $record = $DB->get_record('referentiel_task', array("id" => "$taskid"))) {
            print_error('task ID is incorrect');
        }
	}
    require_login($course->id, false, $cm);


    if (!isloggedin() or isguestuser()) {
        redirect('view.php?id='.$cm->id.'non_redirection=1');
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
        print_error(get_string("activityiscurrentlyhidden"),'error',"$CFG->wwwroot/course/view.php?id=$course->id");
    }

    if ($taskid) {    // So do you have access?
    if (!(has_capability('mod/referentiel:write', $context) 
			or has_capability('mod/referentiel:selecttask', $context) 
			or referentiel_task_isowner($taskid)) 
			or !confirm_sesskey() ) {
         print_error(get_string('noaccess','referentiel'));
    }
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
            redirect('task.php?d='.$referentiel->id);
        }
    
  }
  
	/// Check to see if groups are being used here
	/// find out current groups mode
  $groupmode = groups_get_activity_groupmode($cm);
  $currentgroup = groups_get_activity_group($cm, true);
		
  /// Get all users that are allowed to submit or subscribe task
  $gusers=NULL;
  if ($gusers = get_users_by_capability($context, 'mod/referentiel:write', 'u.id', '', '', '', $currentgroup, '', false)) {
    	$gusers = array_keys($gusers);
  }
	// if groupmembersonly used, remove users who are not in any group
  if ($gusers and !empty($CFG->enablegroupings) and $cm->groupmembersonly) {
    	if ($groupingusers = groups_get_grouping_members($cm->groupingid, 'u.id', 'u.id')) {
        	$gusers = array_intersect($gusers, array_keys($groupingusers));
        }
  }

		

	if (!empty($referentiel) && !empty($course)
    && !empty($form->taskid) 
		&& isset($form) 
		&& isset($form->mode)
		)
	{
		
		if ($form->mode=="updatetask"){
				if (isset($form->name)) {
   		    if (trim($form->name) == '') {
       		   unset($form->name);
          }
        }

        // MODIF 31/01/2011
        $mailnow=0;
        if (isset($form->mailnow)){
            $mailnow=$form->mailnow;
        }

        if (!empty($form->tuserid)){
            foreach($form->tuserid as $ref_user){
                referentiel_association_user_task($ref_user, $form->taskid, $USER->id, $mailnow, true);
            }
          } 
    	   
    }
    if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
	        unset($SESSION->returnpage);
    	    redirect($return);
    } 
    else {
	    	redirect("task.php?d=$referentiel->id&amp;mode=listtasksingle");
    }
		
    exit;
  }

	$mode='updatetask';
   
	// afficher les formulaires

    unset($SESSION->modform); // Clear any old ones that may be hanging around.
    $modform = "";
	// Moodle 2
    $url->param('mode', $mode);
	/// Print the tabs
	$currenttab = $mode;

    /// Mark as viewed  ??????????? A COMMENTER
    $completion=new completion_info($course);
    $completion->set_module_viewed($cm);

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
    //if ($CFG->version < 2011120100) $PAGE->requires->js('/lib/overlib/overlib.js');  else
    $PAGE->requires->js($OverlibJs);
    $PAGE->requires->js('/mod/referentiel/functions.js');

    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);
    $PAGE->navbar->add($strtasks);

    echo $OUTPUT->header();

    groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/souscription.php?d='.$referentiel->id.'&amp;taskid='.$taskid.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc.'&amp;sesskey='.sesskey());

    require_once('onglets.php'); // menus sous forme d'onglets 
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, $data_f, $mode);
    $tab_onglets->display();

    // print_heading_with_help($strtask, 'task', 'referentiel', $icon);
    echo '<div align="center"><h1><img src="'.$icon.'" border="0" title=""  alt="" /> '.$strtask.' '.$OUTPUT->help_icon('taskh','referentiel').'</h1></div>'."\n";

	// recuperer l'id de la tache
	if ($taskid) { // id 	task
        if (! $record = $DB->get_record('referentiel_task', array("id" => "$taskid"))) {
            print_error('task ID is incorrect');
        }

        echo $OUTPUT->box_start('generalbox  boxaligncenter');
        referentiel_print_selection_user_tache($taskid, $mode, $referentiel, $userid_filtre, $gusers);
        echo $OUTPUT->box_end();
	}
    echo $OUTPUT->footer();
    die();

?>

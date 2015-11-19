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
    require_once('lib_pedagogie.php');

	// PAS DE RSS
    // require_once("$CFG->libdir/rsslib.php");

    $id    = optional_param('id', 0, PARAM_INT);    // course module id    
	$d     = optional_param('d', 0, PARAM_INT);    // referentielbase id
	
    $userid   = optional_param('userid', 0, PARAM_INT);    //record pedago id
	$pedago_id   = optional_param('pedago_id', 0, PARAM_INT);    //record pedago id

    $ligmin   = optional_param('ligmin', 0, PARAM_INT);    // index1 of user selection
    $ligmax   = optional_param('ligmax', 0, PARAM_INT);    // index2 of user selection

    // $import   = optional_param('import', 0, PARAM_INT);    // show import form

    $action  	= optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements
    $mode       = optional_param('mode','', PARAM_ALPHANUMEXT);
    $add        = optional_param('add','', PARAM_ALPHA);
    $update     = optional_param('update', 0, PARAM_INT);
    $updateasso     = optional_param('updateasso', 0, PARAM_INT);
    $delete     = optional_param('delete', 0, PARAM_INT);
    $deleteasso = optional_param('deleteasso', 0, PARAM_INT);
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $groupmode  = optional_param('groupmode', -1, PARAM_INT);
    $cancel     = optional_param('cancel', 0, PARAM_BOOL);

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/pedagogie.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : pedagogie.php'), 'referentiel');
	}

	// Moodle 2

    $url->param('mode', $mode);

    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

	if (!empty($userid)) {
		// id pedago
		$record=referentiel_get_pedagogie_user($userid, $referentiel->id);
	}
	
	if (!empty($pedago_id)) {
		// id pedago
        if (! $record = referentiel_get_pedagogie($pedago_id)) {
            print_error('pedago id is incorrect');
        }
	}

    require_login($course->id, false, $cm);   // pas d'autologin guest

    if (!isloggedin() or isguestuser()) {
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
        print_error(get_string("activityiscurrentlyhidden"),'error',"$CFG->wwwroot/course/view.php?id=$course->id");
    }

  if ($userid) {    // So do you have access?
        if (!(has_capability('mod/referentiel:write', $context) or ($USER->id==$userid)) 
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


  // utilisateur
  if (isset($action) && ($action=='selectuser')){
		  if (isset($form->userid) && ($form->userid>0)
			&& confirm_sesskey() ){
		  	$userid_filtre=$form->userid;
			 // DEBUG
		  }

		  if (isset($form->mode) && ($form->mode!='')){
			 $mode=$form->mode;
		  }
		  // echo "<br />ACTION : $action  SEARCH : $userid_filtre\n";
		  unset($form);
		  unset($action);
		  // exit;
  }

  if (isset($action) && ($action=='selectassociation')
      && isset($form->mode) && ($form->mode=='editasso')
			&& confirm_sesskey() ){

      if (!empty($form->pedagos_list)){
        $pedagoids=explode(',',$form->pedagos_list);
      }
      if (!empty($form->users_list)){
        $usersids=explode(',',$form->users_list);
      }
	  if (isset($form->mode) && ($form->mode!='')){
			 $mode=$form->mode;
      }
      
      foreach($pedagoids as $tid){
        // RAZ
        foreach ($usersids as $uid){
            // echo "<br />$uid::$tid\n";
            if (isset($form->t_user[$uid])){
                // echo "<br />Etudiant $uid :: Pedago ".$form->t_user[$uid]." ?? $tid\n";
                referentiel_delete_asso_user($tid, $uid, $referentiel_referentiel->id);
                if ($form->t_user[$uid]==$tid){
                    // echo "<br />$uid :: ".$form->t_user[$uid]." == $tid\n";
                    referentiel_set_association_user_pedago($uid, $referentiel_referentiel->id, $tid);
                }
            }
        }
      }
      unset($form);
      unset($action);
      // changer d'onglet;
      $mode='listasso';
		  // exit;
    }



	if ($cancel) {
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        }
        else {
            redirect('pedagogie.php?d='.$referentiel->id);
        }
    }

	/// selection d'utilisateurs
    if (isset($action) && ($action=='selectuser')
		&& isset($form->userid) && ($form->userid>0)
		&& confirm_sesskey() ){
		$userid_filtre=$form->userid;
		// DEBUG
		// echo "<br />DEBUG :: pedagogie.php :: Ligen 172 :: ACTION : $action  User: $userid_filtre\n";
		unset($form);
		unset($action);
		// exit;
    }

 	
	/// Delete any requested records
    if (!empty($delete)
		  && confirm_sesskey()
		  && (has_capability('mod/referentiel:managecertif', $context))) {
            if ($confirm = optional_param('confirm',0,PARAM_INT)) {
                if (referentiel_delete_pedago($delete)){
                    if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
                			'contextid' => $context->id,
		                	'objectid' => $delete,
        	    		);
            			$event = \mod_referentiel\event\pedagogie_deleted::create($params);
            			$event->trigger();
	        		} else { // Before Moodle 2.7
                        add_to_log($course->id, 'referentiel', 'pedagogy delete', "pedagogie.php?id=$cm->id", $delete, $cm->id);
					}
                    redirect("$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id");
                }
 		    }
    }


	/// Delete any requested records
    if (!empty($deleteasso)
		  && confirm_sesskey()
		  && (has_capability('mod/referentiel:managecertif', $context)
              or referentiel_pedagogie_isowner($deleteasso, $USER->id))) {
            if ($confirm = optional_param('confirm',0,PARAM_INT)) {
                if (referentiel_delete_asso_user($deleteasso, $userid, $referentiel->ref_referentiel)){
                    if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
                			'contextid' => $context->id,
		                	'objectid' => $delete,
							'other' => array('userid' => $userid, 'occurrenceid' => $referentiel->ref_referentiel),
        	    		);
            			$event = \mod_referentiel\event\pedagogie_association_deleted::create($params);
            			$event->trigger();
	        		} else { // Before Moodle 2.7
                    	add_to_log($course->id, 'referentiel', 'record delete', "pedagogie.php?id=$cm->id", $deleteasso, $cm->id);
					}
                    redirect("$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id");
                }
 		    }
    }

    if (!empty($referentiel) && !empty($course) 
        && isset($form) && isset($form->mode)
		)
    {
        // add, delete or update form submitted	
        $addfunction    = "referentiel_add_pedago";
        $updatefunction = "referentiel_update_pedago";
        $deletefunction = "referentiel_delete_pedago";
        // associations pedago / user
        // $updatefunctionasso = "referentiel_update_asso";
        $deletefunctionasso = "referentiel_delete_asso";

		switch ($form->mode) {
    		case "updatepedago":
			
				// DEBUG
				// echo "<br /> $form->mode\n";
				
				if (isset($form->name)) {
                    if (trim($form->name) == '') {
                        unset($form->name);
                    }
                }
				
				if (isset($form->delete) && ($form->delete==get_string('delete'))){
					// suppression 	
					// echo "<br />SUPPRESSION\n";
	    	        $return = $deletefunction($form->pedago_id);
    	    	    if (!$return) {
							/*
            	        	if (file_exists($moderr)) {
                	        	$form = $form;
	                   		    include_once($moderr);
    	                   		die;
	    	               	}
							*/
    	         	      	print_error("Could not update pedago $form->userid of the referentiel", "pedagogie.php?id=$cm->id");
        	    	}
	                if (is_string($return)) {
    	           	    print_error($return, "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id");
	    	        }
	        	    if (isset($form->redirect)) {
    	                $SESSION->returnpage = $form->redirecturl;
        	       	}
                       else {
            	       	$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id";
	               	}
                    if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
                			'contextid' => $context->id,
		                	'objectid' => $form->pedago_id,
        	    		);
            			$event = \mod_referentiel\event\pedagogie_deleted::create($params);
            			$event->trigger();
	        		} else { // Before Moodle 2.7
                        add_to_log($course->id, "referentiel", "delete", "mise a jour pedago $form->userid","$form->pedago_id", "");
					}
				}
				else {
				// DEBUG
				// echo "<br /> UPDATE\n";
				
	    	    	$return = $updatefunction($form);
    	    	    if (!$return) {
					/*
            		    if (file_exists($moderr)) {
                			$form = $form;
                    		include_once($moderr);
                        	die;
	                    }
					*/
    	            	print_error("Could not update pedago $form->pedago_id of the referentiel", "pedagogie.php?id=$cm->id");
					}
		            if (is_string($return)) {
    		        	print_error($return, "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id");
	    		    }
	        		if (isset($form->redirect)) {
    	        		$SESSION->returnpage = $form->redirecturl;
					} 
					else {
        	    		$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id";
	        	    }
                    if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
                			'contextid' => $context->id,
		                	'objectid' => $return,
        	    		);
            			$event = \mod_referentiel\event\pedagogie_updated::create($params);
            			$event->trigger();
	        		} else { // Before Moodle 2.7
                        	add_to_log($course->id, "referentiel", "update", "mise a jour pedago $return", "$return", "");
					}


    	    	}

			break;
			
			case "addpedago":
				if (!isset($form->name) || trim($form->name) == '') {
        			$form->name = get_string("modulename", "referentiel");
        		}
				$return = $addfunction($form);
				if (!$return) {
    	        	/*
					if (file_exists($moderr)) {
    	    	    	$form = $form;
        	    	    include_once($moderr);
            	    	die;
					}
	            	*/
					print_error("Could not add a new pedago to the referentiel", "pedagogie.php?id=$cm->id");
				}
	        	if (is_string($return)) {
    	        	print_error($return, "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id");
				}
				if (isset($form->redirect)) {
    	    		$SESSION->returnpage = $form->redirecturl;
				} 
				else {
					$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id";
				}
                if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
                			'contextid' => $context->id,
		                	'objectid' => $return,
        	    		);
            			$event = \mod_referentiel\event\pedagogie_created::create($params);
            			$event->trigger();
	        	} else { // Before Moodle 2.7
					add_to_log($course->id, "referentiel", "add", "creation pedago ", "", "");
				}
            break;
			
	        case "deletepedago":
				if (! $deletefunction($form->pedago_id)) {
	            	print_error("Could not delete pedago of  the referentiel");
                }
	            unset($SESSION->returnpage);
                if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
                			'contextid' => $context->id,
		                	'objectid' => $form->pedago_id,
        	    		);
            			$event = \mod_referentiel\event\pedagogie_deleted::create($params);
            			$event->trigger();
	        	} else { // Before Moodle 2.7
		            add_to_log($course->id, "referentiel", "delete", "suppression pedago $form->userid ", "$form->pedago_id", "");
				}
            break;

// ###########  ASSOCIATIONS
    		case "updateasso":

				// DEBUG
				// echo "<br /> $form->mode\n";

				if (isset($form->name)) {
                    if (trim($form->name) == '') {
                        unset($form->name);
                    }
                }

				if (isset($form->deleteasso) && ($form->deleteasso==get_string('delete'))){
					// suppression
					// echo "<br />SUPPRESSION\n";
	    	        $return = $deletefunctionasso($form);
    	    	    if (!$return) {
    	         	      	print_error("Could not update association $form->userid of the referentiel", "pedagogie.php?id=$cm->id");
        	    	}
	                if (is_string($return)) {
    	           	    print_error($return, "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id");
	    	        }
	        	    if (isset($form->redirect)) {
    	                $SESSION->returnpage = $form->redirecturl;
        	       	}
                    else {
            	       	$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id";
	               	}
                	if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
                			'contextid' => $context->id,
		                	'objectid' => $form->pedago_id,
							'other' => array('userid' => $form->userid, 'occurrenceid' => $referentiel_referentiel->id),
        	    		);
            			$event = \mod_referentiel\event\pedagogie_association_deleted::create($params);
            			$event->trigger();
	        		} else { // Before Moodle 2.7
	    	        	add_to_log($course->id, "referentiel", "delete", "mise a jour association $form->userid :: $form->pedago_id", "$form->pedago_id", "");
					}
				}
				else {
				// DEBUG
				// echo "<br /> UPDATE\n";

	    	    	$return = $updatefunctionasso($form);
    	    	    if (!$return) {

    	            	print_error("Could not update association $form->userid of the referentiel", "pedagogie.php?id=$cm->id");
					}
		            if (is_string($return)) {
    		        	print_error($return, "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id");
	    		    }
	        		if (isset($form->redirect)) {
    	        		$SESSION->returnpage = $form->redirecturl;
					}
					else {
        	    		$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id";
	        	    }
                    if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
                			'contextid' => $context->id,
		                	'objectid' => $form->pedago_id,
        	    		);
            			$event = \mod_referentiel\event\pedagogie_updated::create($params);
            			$event->trigger();
	        		} else { // Before Moodle 2.7
                        add_to_log($course->id, "referentiel", "update", "mise a jour association $form->userid :: $form->pedago_id", "$form->pedago_id", "");
					}
    	    	}

			break;

	        case "deleteasso":
				if (! $deletefunctionasso($form)) {
	            	print_error("Could not delete association of the referentiel");
                }
	            unset($SESSION->returnpage);
                if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
                			'contextid' => $context->id,
		                	'objectid' => $form->pedago_id,
							'other' => array('userid' => $form->userid, 'occurrenceid' => $referentiel_referentiel->id),
        	    		);
            			$event = \mod_referentiel\event\pedagogie_association_deleted::create($params);
            			$event->trigger();
	        	} else { // Before Moodle 2.7
	    	       	add_to_log($course->id, "referentiel", "delete", "mise a jour association $form->userid :: $form->pedago_id", "$form->pedago_id", "");
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
	    	redirect("$CFG->wwwroot/mod/referentiel/pedagogie.php?id=$cm->id");
    	}
		
        exit;
	}

	// afficher les formulaires

    unset($SESSION->modform); // Clear any old ones that may be hanging around.

    $modform = "pedagogie.html";

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
		$mode='listpedago';
	}
	if (isset($mode) && ($mode=="pedago")){
		$mode='listpedago';
	}

	// DEBUG
	//echo "<br /> DEBUG :: pedagogie.php :: 452 :: MODE : $mode\n";

	if (isset($mode) && (($mode=="deletepedago") || ($mode=="updatepedago"))){
		$currenttab ='editpedago';
	}
	else if (isset($mode) && (($mode=="selectasso") || ($mode=="editasso") || ($mode=="deleteasso") || ($mode=="updateasso"))){
		$currenttab ='editasso';
	}
	else{
		$currenttab = $mode;
	}

    if ($userid) {
       	$editentry = true;  //used in tabs
    }



    /// Mark as viewed  ??????????? A COMMENTER
    $completion=new completion_info($course);
    $completion->set_module_viewed($cm);

// AFFICHAGE DE LA PAGE Moodle 2
	/// Print the page header
	$strpedago = get_string('formation','referentiel');
	$strpagename=get_string('pedagos','referentiel');

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

	groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/pedagogie.php?d='.$referentiel->id.'&amp;mode='.$mode);

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }


    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, 0, NULL, $mode);
    $tab_onglets->display();
    echo '<br />'."\n";
    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strpagename.' '.$OUTPUT->help_icon('pedagoh','referentiel').'</h2></div>'."\n";

	if  ($mode=='editasso'){
        if (has_capability('mod/referentiel:managecertif', $context)) {
            if (!empty($userid)){
                $userid_filtre=$userid;
            }
            referentiel_select_associations($mode, $referentiel, $userid_filtre, $gusers);
        }
	}
	elseif (($mode=='pedago') || ($mode=='listpedago')){
		referentiel_print_liste_pedagogies($mode, $referentiel);
	}
	else if ($mode=='listasso'){
		referentiel_print_liste_associations($mode, $referentiel, $userid_filtre, $gusers);
	}
	else if ($mode=='selectasso'){
        // DEBUG
        // echo "<br />DEBUG :: pedagogie.php :: 598 \n";
        // echo "<br />LigMin: $ligmin, LigMax: $ligmax\n";
        // exit;
        echo referentiel_select_users_pedago_cache($course->id, $referentiel->id, $referentiel_referentiel->id, $mode, $ligmin, $ligmax);
	}
	else {
        //print_simple_box_start('center', '', '', 5, 'generalbox', $referentiel->name);
   	    echo $OUTPUT->box_start('generalbox  boxaligncenter');
		// formulaires
	    if (file_exists($modform)) {
   	        $defaultformat = FORMAT_PLAIN;
       	    $editorfields = '';
		}
		else {
    	    notice("ERREUR : No file found at : $modform)", "pedagogie.php?id=$cm->id");
    	}
		include_once($modform);
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->footer();
    die();

?>

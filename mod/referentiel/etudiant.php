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
    require_once('print_lib_etudiant.php');	// AFFICHAGES 

    $id    = optional_param('id', 0, PARAM_INT);    // course module id    
	$d     = optional_param('d', 0, PARAM_INT);    // referentielbase id

    $updateprofile = optional_param('updateprofile', 0, PARAM_INT); // forcer la mise à jour des numero d'etudiant
    $deleteid    = optional_param('deleteid', 0, PARAM_INT);    // id object to delete
    $delete = optional_param('delete','', PARAM_ALPHANUMEXT);
    $userid   = optional_param('userid', 0, PARAM_INT);    //record etudiant id
	$etudiant_id   = optional_param('etudiant_id', 0, PARAM_INT);    //record etudiant id
	$etablissement_id   = optional_param('etablissement_id', 0, PARAM_INT);    //record etablissement id

    $initiale     = optional_param('initiale','', PARAM_ALPHA); // selection par les initiales du nom
    $userids      = optional_param('userids','', PARAM_TEXT); // id user selectionnes par les initiales du nom

    // $import   = optional_param('import', 0, PARAM_INT);    // show import form

    $action  	= optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements
    $mode       = optional_param('mode','', PARAM_ALPHANUMEXT);
    $add        = optional_param('add','', PARAM_ALPHA);
    $update     = optional_param('update', 0, PARAM_INT);

    $approve    = optional_param('approve', 0, PARAM_INT);	
    $comment    = optional_param('comment', 0, PARAM_INT);		
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $groupmode  = optional_param('groupmode', -1, PARAM_INT);
    $cancel     = optional_param('cancel', 0, PARAM_BOOL);
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/etudiant.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : etudiant.php'), 'referentiel');
	}

	// Moodle 2

    $url->param('mode', $mode);
    $context = context_module::instance($cm->id);

	if (isset($userid) && ($userid>0)) {
		// userid record referentiel_etudiant
        if (! $record=$DB->get_record("referentiel_etudiant", array("userid" => "$userid"))){
            print_error('incorrect_userid','referentiel',$CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id,$user_id,"Update or delete function");
        }
	}

	if (isset($etudiant_id) && ($etudiant_id>0)) {
		// id record referentiel_etudiant
        if (!$record = $DB->get_record("referentiel_etudiant", array("id" => "$etudiant_id"))) {
            print_error('incorrect_id','referentiel',$CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id,$etudiant_id,"Update or delete function");
        }
	}

	if (isset($etablissement_id) && ($etablissement_id>0)) {
		// id etablissement record referentiel_etudiant
        if (! $record_etab = $DB->get_record("referentiel_etablissement", array("id" => "$etablissement_id"))) {
            print_error('incorrect_id','referentiel',$CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id,$user_id,"Etablissement");
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
        if (!(has_capability('mod/referentiel:write', $context) 
			or ($USER->id==$userid)) or !confirm_sesskey() ) {
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

	/// selection etablissement
   	if (isset($mode) && ($mode=='selectetab')
		&& isset($userid) && ($userid>0)
		&& isset($etablissement_id) && ($etablissement_id>0)
		&& confirm_sesskey() ){
		referentiel_etudiant_set_etablissement($userid, $etablissement_id);
		$mode='listetudiant';
    }

	if ($cancel) {
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        }
        else {
            redirect('etudiant.php?d='.$referentiel->id);
        }
    }

	/// selection d'utilisateurs
    if (isset($action) && ($action=='selectuser')
		&& isset($form->userid) && ($form->userid>0)
		&& confirm_sesskey() ){
		$userid_filtre=$form->userid;
		// DEBUG
		// echo "<br />DEBUG :: etudiant.php :: Ligen 172 :: ACTION : $action  User: $userid_filtre\n";
		unset($form);
		unset($action);
		// exit;
    }

 	
	/// Delete any requested records
    if (!empty($deleteid)
			&& confirm_sesskey()
			&& (has_capability('mod/referentiel:managecertif', $context)
            or referentiel_etudiant_isowner($deleteid))) {
        if ($confirm = optional_param('confirm',0,PARAM_INT)) {
            if (referentiel_delete_etudiant_user($deleteid)){
        		if ($CFG->version > 2014051200) { // Moodle 2.7+
            		$params = array(
                	'contextid' => $context->id,
                	'objectid' => $deleteid,
            		);
            		$event = \mod_referentiel\event\etudiant_deleted::create($params);
            		$event->trigger();
        		} else { // Before Moodle 2.7
					add_to_log($course->id, 'referentiel', 'delete', "etudiant.php?id=$cm->id", $deleteid, $cm->id);
				}
                // notify(get_string('recorddeleted','referentiel'), 'notifysuccess');
            }
            redirect("$CFG->wwwroot/mod/referentiel/etudiant.php?id=$cm->id&amp;sesskey=".sesskey());
            exit;
		}
    }

    // selections multiples
    if (isset($_POST['action']) && ($_POST['action']=='modifglobaletudiant')){
        // echo "<br />DEBUG :: etudiant.php :: 268 :: ACTION : ".$_POST['action']." \n";
		$form=$_POST;
		//print_object($form);
		// exit;
		if (!empty($form['tetudiant_id'])){
            //
            foreach ($form['tetudiant_id'] as $id_etudiant){
                    // echo "<br />ID :: ".$id_domaine."\n";
                    // exit;
                    $record= new Object();
                    $record->id=$id_etudiant;
        		    $record->num_etudiant=$form['num_etudiant_'.$id_etudiant];
        		    $record->ddn_etudiant=$form['ddn_etudiant_'.$id_etudiant];
        		    $record->lieu_naissance=$form['lieu_naissance_'.$id_etudiant];
        		    $record->departement_naissance=$form['departement_naissance_'.$id_etudiant];
					$record->adresse_etudiant=$form['adresse_etudiant_'.$id_etudiant];
        		    $record->ref_etablissement=$form['ref_etablissement_'.$id_etudiant];
                    $record->userid=$form['userid_'.$id_etudiant];
                    //print_object($record);
                    //echo "<br />\n";
					//exit;
					if (!$DB->update_record("referentiel_etudiant", $record)){
                        print_error("Could not update record $record->id ", "etudiant.php?id=".$cm->id.'&amp;sesskey='.sesskey());
                    }
        			if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
		                	'contextid' => $context->id,
        		        	'objectid' => $record->id,
            			);
	            		$event = \mod_referentiel\event\etudiant_updated::create($params);
    	        		$event->trigger();
        			} else { // Before Moodle 2.7
						add_to_log($course->id, 'referentiel', 'update', "etudiant.php?id=$cm->id", $record->id, $cm->id);
					}
			}
        }
        unset($form);
		redirect("$CFG->wwwroot/mod/referentiel/etudiant.php?id=$cm->id&amp;sesskey=".sesskey());
        exit;
    }



	if (!empty($form->mode)){  // add, delete or update form submitted
		$addfunction    = "referentiel_add_etudiant";
        $updatefunction = "referentiel_update_etudiant";
        $deletefunction = "referentiel_delete_etudiant";

		switch ($form->mode) {
    		case "updateetudiant":
				// DEBUG
				//echo "<br /> $form->mode<br />\n";
				//print_object($form);
				//exit;
				if (isset($form->delete) && ($form->delete==get_string('delete'))){
					// suppression
	    	        $return = $deletefunction($form->etudiant_id);
    	    	    if (!$return) {
    	         	    print_error('incorrect_id','referentiel',$CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id,$form->etudiant_id,"Delete function");
        	    	}
	        	    if (isset($form->redirect)) {
    	                $SESSION->returnpage = $form->redirecturl;
        	       	}
                    else {
            	       	$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/etudiant.php?id=$cm->id";
	               	}
       				if ($CFG->version > 2014051200) { // Moodle 2.7+
	            		$params = array(
    		            	'contextid' => $context->id,
            		    	'objectid' => $return,
            			);
	            		$event = \mod_referentiel\event\etudiant_deleted::create($params);
    	        		$event->trigger();
        			} else { // Before Moodle 2.7
						add_to_log($course->id, 'referentiel', 'delete', "etudiant.php?id=$cm->id", $return, $cm->id);
					}
				}
				else {
                    // update
	    	    	$return = $updatefunction($form);
    	    	    if (!$return) {
    	         	    print_error('incorrect_id','referentiel',$CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id,$form->etudiant_id,"Delete function");
					}
		            if (is_string($return)) {
    		        	print_error($return, "etudiant.php?id=$cm->id");
	    		    }
	        		if (isset($form->redirect)) {
    	        		$SESSION->returnpage = $form->redirecturl;
					}
					else {
        	    		$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/etudiant.php?id=$cm->id";
	        	    }
        			if ($CFG->version > 2014051200) { // Moodle 2.7+
            			$params = array(
		                	'contextid' => $context->id,
        		        	'objectid' => $form->etudiant_id,
            			);
	            		$event = \mod_referentiel\event\etudiant_updated::create($params);
    	        		$event->trigger();
        			} else { // Before Moodle 2.7
						add_to_log($course->id, 'referentiel', 'update', "etudiant.php?id=$cm->id", $form->etudiant_id, $cm->id);
					}
				}

			break;

			case "addetudiant":
				if (!isset($form->name) || trim($form->name) == '') {
        			$form->name = get_string("modulename", "referentiel");
        		}
				$return = $addfunction($form);
				if (!$return) {
					print_error("Could not add a new etudiant to the referentiel", "etudiant.php?id=$cm->id");
				}
	        	if (is_string($return)) {
    	        	print_error($return, "etudiant.php?id=$cm->id");
				}
				if (isset($form->redirect)) {
    	    		$SESSION->returnpage = $form->redirecturl;
				}
				else {
					$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/etudiant.php?id=$cm->id";
				}
        		if ($CFG->version > 2014051200) { // Moodle 2.7+
            		$params = array(
		               	'contextid' => $context->id,
        		       	'objectid' => $form->etudiant_id,
            		);
	            	$event = \mod_referentiel\event\etudiant_created::create($params);
    	        	$event->trigger();
        		} else { // Before Moodle 2.7
					add_to_log($course->id, 'referentiel', 'add', "etudiant.php?id=$cm->id", $form->etudiant_id, $cm->id);
				}

            break;

	        case "deleteetudiant":
				if (! $deletefunction($form->userid)) {
	            	print_error("Could not delete that student");
                }
	            unset($SESSION->returnpage);
	            add_to_log($course->id, referentiel, "add",
                           "suppression etudiant $form->userid ",
                           "$form->etudiant_id", "");
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
	    	redirect("etudiant.php?id=$cm->id");
    	}

        exit;
	}

	// afficher les formulaires

    unset($SESSION->modform); // Clear any old ones that may be hanging around.

    $modform = "etudiant_inc.php";

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

	/// Params for the tabs
	if (!isset($mode) || ($mode=="")){
		$mode='listetudiant';
	}
	if (isset($mode) && ($mode=="etudiant")){
		$mode='listetudiant';
	}

	// DEBUG
	// echo "<br /> MODE : $mode\n";

	if (isset($mode) && (($mode=="deleteetudiant") || ($mode=="updateetudiant"))){
		$currenttab ='editetudiant';
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
	$stretudiant = get_string('etudiant','referentiel');
	$strpagename=get_string('etudiants','referentiel');

    $strreferentiel = get_string('modulename', 'referentiel');
    $strreferentiels = get_string('modulenameplural', 'referentiel');

    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->js('/mod/referentiel/functions.js');
    $PAGE->navbar->add($strpagename);
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

	groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/etudiant.php?d='.$referentiel->id.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc);

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();
    echo '<br />'."\n";
    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strpagename.' '.$OUTPUT->help_icon('etudianth','referentiel').'</h2></div>'."\n";

	if (($mode=='scolarite') || ($mode=='listetudiant')){
		referentiel_print_liste_etudiants($initiale, $userids, $mode, $referentiel, $userid_filtre, $gusers, $select_acc);
	}
	else {
        //print_simple_box_start('center', '', '', 5, 'generalbox', $referentiel->name);
   	    echo $OUTPUT->box_start('generalbox  boxaligncenter');
		// formulaires
		if ($mode=='updateetudiant'){
            if (!has_capability('mod/referentiel:managecertif', $context) || !empty($userid)){
                // etudiant sélectionné
                $modform = "etudiant_edit_inc.php";
            }
            else{
                $modform = "etudiant_inc.php";
            }
		}
		else if ($mode=='deleteetudiant'){
			if (!empty($userid)){ // id etudiant
    			$modform = "etudiant_edit_inc.php";
    		}
			else{
                $modform = "etudiant_inc.php";
            }
		}

	    if (file_exists($modform)) {
  	        $defaultformat = FORMAT_PLAIN;
       	    $editorfields = '';
		}
		else {
    	    notice("ERREUR : No file found at : $modform)", "etudiant.php?id=$cm->id");
    	}
		include_once($modform);
        echo $OUTPUT->box_end();
    } 
    echo $OUTPUT->footer();
    die();

?>

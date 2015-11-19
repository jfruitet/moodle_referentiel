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
    require_once('onglets.php');
    include('lib_certificat.php');	// AFFICHAGES
    include('print_lib_certificat.php');	// AFFICHAGES 

    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentielbase id
    $certificat_id= optional_param('certificat_id', 0, PARAM_INT);    //record certificat id
    $action  	  = optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements
    $mode         = optional_param('mode','', PARAM_ALPHA);
    $add          = optional_param('add','', PARAM_ALPHA);
    $update       = optional_param('update', 0, PARAM_INT);
    $delete       = optional_param('delete', 0, PARAM_INT);
	$clore        = optional_param('clore', 0, PARAM_INT);
    $approve      = optional_param('approve', 0, PARAM_INT);
    $comment      = optional_param('comment', 0, PARAM_INT);
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $groupmode    = optional_param('groupmode', -1, PARAM_INT);
    $cancel       = optional_param('cancel', 0, PARAM_BOOL);
    $userid       = optional_param('userid', 0, PARAM_INT);
    $select_acc   = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
    $select_all   = optional_param('select_all', 0, PARAM_INT);      // accompagnement
    $list_userids = optional_param('list_userids', '',PARAM_TEXT);
    $initiale     = optional_param('initiale','', PARAM_ALPHA); // selection par les initiales du nom
    $userids      = optional_param('userids','', PARAM_TEXT); // id user selectionnes par les initiales du nom
    $mode_select  = optional_param('mode_select','', PARAM_ALPHA);

    // Filtres
    require_once('filtres.php'); // Ne pas deplacer

    $url = new moodle_url('/mod/referentiel/certificat.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : certificat.php'), 'referentiel');
	}

    $context = context_module::instance($cm->id);

    if ($certificat_id) { // id certificat
        if (! $record = $DB->get_record("referentiel_certificat", array("id" => "$certificat_id"))) {
            print_error('Certificat ID is incorrect');
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

    if ($certificat_id) {    // So do you have access?
        if (!(has_capability('mod/referentiel:viewrate', $context) 
			or referentiel_certificat_isowner($certificat_id)) or !confirm_sesskey() ) {
            print_error(get_string('noaccess_certificat','referentiel'));
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

  // accompagnement
	if (!isset($select_acc)){
    if (isset($form->select_acc)){
        $select_acc=$form->select_acc;
    }
    else{ 
      $select_acc=0 ;
    }
  }
	
	// selecteur
	$userid_filtre=0;
    if (!empty($userid)) {
        $userid_filtre=$userid;
    }


	/// selection filtre
    if (empty($userid_filtre) || ($userid_filtre==$USER->id)
        || (isset($mode_select) && ($mode_select=='selectetab'))){
        set_filtres_sql('certificat');
    }


	if ($cancel) {
	    if (isset($form->select_acc)){
          $select_acc=$form->select_acc;
      }

	    $mode ='list';
		  if (has_capability('mod/referentiel:managecertif', $context)){
         $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/certificat.php?id=$cm->id&amp;select_acc=$select_acc&amp;userid=0&amp;mode=$mode&";
		  }
		  else{
         $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/certificat.php?id=$cm->id&amp;select_acc=$select_acc&amp;userid=$userid&amp;mode=$mode";
		  }
	   
      if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
      }
      else {
            redirect("$CFG->wwwroot/mod/referentiel/certificat.php?id=$cm->id&amp;select_acc=$select_acc&amp;userid=$userid&amp;mode=$mode");
      }
       exit;
    }

	
	/// selection utilisateurs accompagnés
	if (isset($action) && ($action=='select_acc')){
		  if (isset($form->select_acc) && confirm_sesskey() ){
		  	$select_acc=$form->select_acc;
		  }
		  if (isset($form->mode) && ($form->mode!='')){
			 $mode=$form->mode;
		  }
		  unset($form);
		  unset($action);
		  // exit;
    }
    
    /// selection d'utilisateurs
    if (isset($action) && ($action=='selectuser')
		&& !empty($form->userid)
		&& confirm_sesskey() ){
		$userid_filtre=$form->userid;
		if (isset($form->select_acc)){
		  	$select_acc=$form->select_acc;
		}
		unset($form);
		unset($action);
		// exit;
    }
 	
	/// Delete any requested records
    if (!empty($delete)
			&& confirm_sesskey() 
			&& (has_capability('mod/referentiel:rate', $context) or referentiel_certificat_isowner($delete))) {
        if ($confirm = optional_param('confirm',0,PARAM_INT)) {
            if (referentiel_delete_certificat_record($delete)){
        		if ($CFG->version > 2014051200) { // Moodle 2.7+
            		$params = array(
                	'contextid' => $context->id,
                	'objectid' => $delete,
            		);
            		$event = \mod_referentiel\event\certificat_deleted::create($params);
            		$event->trigger();
        		} else { // Before Moodle 2.7
                    add_to_log($course->id, 'referentiel', 'certificate delete', "certificat.php?id=$cm->id", $delete, $cm->id);
        		}
            }
            
		}
        unset($form);
    }

	/// Clore dossier
    if (!empty($clore) && confirm_sesskey() && has_capability('mod/referentiel:rate', $context))
	{
        if ($approverecord = $DB->get_record("referentiel_certificat", array("id" => "$clore"))) {
            $confirm = optional_param('confirm',0,PARAM_INT);
            if ($confirm) {
                    $dclos = 1;
            }
            else{
                    $dclos = 0;
            }
            if ($dclos){
				$DB->set_field('referentiel_certificat','verrou',1,array("id" => "$clore"));
			}
            $DB->set_field('referentiel_certificat','valide',$dclos,array("id" => "$clore"));
            $DB->set_field('referentiel_certificat','teacherid',$USER->id,array("id" => "$clore"));
            //if (isset($userid) && ($userid>0)){
            //    $userid_filtre=$userid;
			//}
            $userid_filtre=0; // pour reafficher toutes les certificats
        }
	    unset($form);
	}

	/// Approve any requested records
    if (!empty($approve) && confirm_sesskey()
		&& has_capability('mod/referentiel:rate', $context)

        ) 
	{
        if ($approverecord = $DB->get_record("referentiel_certificat", array("id" => "$approve"))) {
            $confirm = optional_param('confirm',0,PARAM_INT);
            if ($confirm) {
                    $verrou = 1;
            }
                else{
                    $verrou = 0;
            }
            $DB->set_field('referentiel_certificat','verrou',$verrou,array("id" => "$approve"));
            $DB->set_field('referentiel_certificat','teacherid',$USER->id,array("id" => "$approve"));
            //if (isset($userid) && ($userid>0)){
            //    $userid_filtre=$userid;
			//}
            $userid_filtre=0; // pour reafficher toutes les certificats
        }
        unset($form);
    }
	
	/// Comment any requested records
    if (!empty($comment) && confirm_sesskey()
		&& has_capability('mod/referentiel:rate', $context)) 
	{
		if (isset($form) && isset($form->certificat_id) && ($form->certificat_id>0)){
			if ($approverecord = $DB->get_record("referentiel_certificat", array("id" => "$comment"))) {
				$approverecord->teacherid=$USER->id;
				$approverecord->commentaire_certificat=($form->commentaire_certificat);
				$approverecord->synthese_certificat=($form->synthese_certificat);
				$approverecord->competences_certificat=($approverecord->competences_certificat);
				$approverecord->decision_jury=($approverecord->decision_jury);

                if (isset($form->mailnow)){
                    $approverecord->mailnow=$form->mailnow;
                    if ($form->mailnow=='1'){ // renvoyer
                        $approverecord->mailed=0;   // annuler envoi precedent
                    }
                }
                else{
                    $approverecord->mailnow=0;
                }

				if (isset($form->userid) && ($form->userid>0)){
					$userid_filtre=$form->userid;
				} 

		        if ($DB->update_record('referentiel_certificat', $approverecord)) {
        		   	// notify(get_string('recordapproved','referentiel'), 'notifysuccess');
            	}
			}
			unset($form);
        }
    }


    if (!empty($referentiel) && !empty($course) && isset($form)) {
        /// modification globale

        if (isset($_POST['action']) && ($_POST['action']=='modifier_certificat_global')){
		    $form=$_POST;
	        // accompagnement
            if (isset($form['select_acc'])){
		    	$select_acc=$form['select_acc'];
		    }

 		    if (isset($form['tcertificat_id']) && ($form['tcertificat_id'])){

                foreach ($form['tcertificat_id'] as $id_certificat){
                    // echo "<br />DEBUG :: CERTIFICAT.PHP :: 422 <br />ID :: ".$id_certificat."\n";
                    $form2= new Object();
                    $form2->action='modifier_certificat';
                    $form2->certificat_id=$form['certificat_id_'.$id_certificat];
                    $form2->commentaire_certificat=$form['commentaire_certificat_'.$id_certificat];
                    $form2->competences_certificat=$form['competences_certificat_'.$id_certificat];
                    $form2->competences_activite=$form['competences_activite_'.$id_certificat];
                    $form2->synthese_certificat=$form['synthese_certificat_'.$id_certificat];
                    if (isset($form['decision_jury_sel_'.$id_certificat])){
                        $form2->decision_jury_sel=$form['decision_jury_sel_'.$id_certificat];
                    }
                    $form2->decision_jury=$form['decision_jury_'.$id_certificat];
                    $form2->decision_jury_old=$form['decision_jury_old_'.$id_certificat];
                    $form2->date_decision=$form['date_decision_'.$id_certificat];

                    $form2->ref_referentiel=$form['ref_referentiel_'.$id_certificat];
                    $form2->userid=$form['userid_'.$id_certificat];
                    $form2->teacherid=$form['teacherid_'.$id_certificat];

                    if (!empty($form['verrou_'.$id_certificat]))  {
                        $form2->verrou=$form['verrou_'.$id_certificat];
                    }
                    else {
                        $form2->verrou=0;
                    }
                    $form2->valide=$form['valide_'.$id_certificat];
                    $form2->evaluation=$form['evaluation_'.$id_certificat];
                    $form2->mailnow=$form['mailnow_'.$id_certificat];
                    $form2->instance=$form['instance_'.$id_certificat];

                    $return = referentiel_update_certificat($form2);
                    if (!$return) {
                        print_error("Could not update certificat $form->certificat_id of the referentiel", "certificat.php?id=$cm->id");
                    }
                    if (is_string($return)) {
                        print_error($return, "certificat.php?id=$cm->id");
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	        		$params = array(
        	        		'contextid' => $context->id,
            	    		'objectid' => $delete,
            			);
	            		$event = \mod_referentiel\event\certificat_updated::create($params);
    	        		$event->trigger();
        			} else { // Before Moodle 2.7
                        add_to_log($course->id, 'referentiel', 'update', "certificate $form2->certificat_id updated", $form2->instance, $cm->id);
					}
                }
            }
            unset($form);
    }

	elseif (!empty($form->mode)){

		// add, delete or update form submitted	
        $addfunction    = "referentiel_add_certificat";
        $updatefunction = "referentiel_update_certificat";
        $deletefunction = "referentiel_delete_certificat";

		switch ($form->mode) {
    		case "updatecertif":
			
				// DEBUG
				// echo "<br /> $form->mode\n";
				
				if (isset($form->name)) {
   		        	if (trim($form->name) == '') {
       		        	unset($form->name);
           		    }
               	}
				
				if (isset($form->delete) && ($form->delete==get_string('delete'))){
					// suppression 	
	    	        $return = $deletefunction($form);
    	    	    if (!$return) {
    	         	    print_error("Could not update certificat $certificat_id of the referentiel", "certificat.php?id=$cm->id");
        	    	}
	                if (is_string($return)) {
    	           	    print_error($return, "certificat.php?id=$cm->id");
	    	        }
	        	    if (isset($form->redirect)) {
    	                $SESSION->returnpage = $form->redirecturl;
        	       	} else {
            	       	$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/certificat.php?id=$cm->id";
	               	}
					
	    	        add_to_log($course->id, "referentiel", "delete",
            	          "mise a jour certificat $form->certificat_id",
                          "$form->instance", "");
					
				}
				else {
					if (isset($form->userid) && ($form->userid>0)){
						$userid_filtre=$form->userid;
					} 
					
	    	    	$return = $updatefunction($form);

    	    	    if (!$return) {
    	            	print_error("Could not update certificat $form->id of the referentiel", "certificat.php?id=$cm->id");
					}
		            if (is_string($return)) {
    		        	print_error($return, "certificat.php?id=$cm->id");
	    		    }
	        		if (isset($form->redirect)) {
    	        		$SESSION->returnpage = $form->redirecturl;
					} 
					else {
        	    		$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/certificat.php?id=$cm->id";
	        	    }
					add_to_log($course->id, "referentiel", "update",
            	           "mise a jour certificat $form->certificat_id",
                           "$form->instance", "");
    	    	}

			break;
			
			case "addcertif":
				if (!isset($form->name) || trim($form->name) == '') {
        			$form->name = get_string("modulename", "referentiel");
        		}
				$return = $addfunction($form);
				if (!$return) {
					print_error("Could not add a new certificat to the referentiel", "certificat.php?id=$cm->id");
				}
	        	if (is_string($return)) {
    	        	print_error($return, "certificat.php?id=$cm->id");
				}
				if (isset($form->redirect)) {
    	    		$SESSION->returnpage = $form->redirecturl;
				} 
				else {
					$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/certificat.php?id=$cm->id";
				}
				add_to_log($course->id, referentiel, "add",
                           "creation certificat $form->certificat_id ",
                           "$form->instance", "");
            break;
			
	        case "deletecertif":
				if (! $deletefunction($form)) {
	            	print_error("Could not delete certificat of  the referentiel");
                }
	            unset($SESSION->returnpage);
	            add_to_log($course->id, referentiel, "add",
                           "suppression certificat $form->certificat_id ",
                           "$form->instance", "");
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
	    	redirect("certificat.php?id=$cm->id");
    	}
		
        exit;
	   }
    }


	// afficher les formulaires

    unset($SESSION->modform); // Clear any old ones that may be hanging around.

    $modform = "certificat.html";

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
	if (empty($mode)){
		$mode='listcertif';
	}
	if (isset($mode) && ($mode=="certificat")){
		$mode='listcertif';
	}

    if (isset($mode) && (($mode=="deletecertif")
        || ($mode == "ouvrircertificat")
        || ($mode == "clorecertificat")
		|| ($mode=="updatecertif")
		|| ($mode=="approvecertif")
		|| ($mode=="deverrouiller")
		|| ($mode=="verrouiller")
		|| ($mode=="commentcertif"))){
		$currenttab ='editcertif';
	}
	else if (isset($mode) && ($mode=='listcertifsingle')){
		$currenttab ='listcertif';
	}
	else{
		$currenttab = $mode;
	}
    if ($certificat_id) {
       	$editentry = true;  //used in tabs
    }
    

    $url->param('mode', $mode);

	$strreferentiel = get_string('modulenameplural','referentiel');
	$strcertificat = get_string('certificat','referentiel');
	$strpagename=get_string('certificats','referentiel');
    $strreferentiels = get_string('modulenameplural', 'referentiel');
    $strsubmissions = get_string('submissions', 'referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/jauge.css');
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->js($OverlibJs);
    $PAGE->requires->js('/mod/referentiel/functions.js');

    $PAGE->set_title($pagetitle);
    $PAGE->navbar->add($strcertificat);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

	groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/certificat.php?id='.$cm->id.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc);

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets 
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, $data_f);
    $tab_onglets->display();
    echo '<br />'."\n";

    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="referentiel" alt="referentiel" /> '.$strcertificat.' '.$OUTPUT->help_icon('certificath','referentiel').'</h2></div>'."\n";

    if (($mode=='statcertif')){
         referentiel_print_graph_certificats($referentiel, $referentiel_referentiel, $gusers, $currentgroup);
    }
    elseif (($mode=='listcertifsingle') && ($certificat_id>0)){
        referentiel_print_un_certificat_detail($certificat_id, $referentiel, $userid_filtre, $select_acc);
	}
	elseif (($mode=='list') || ($mode=='listcertif')){
		referentiel_liste_certificats($initiale, $userids, $mode, $referentiel, $userid_filtre, $gusers, $sql_f_where, $sql_f_order, $data_f, $select_acc);
	}
	else {
		// formulaires
        if (($mode=='editcertif') && !$certificat_id && has_capability('mod/referentiel:managecertif', $context)){
            referentiel_evalue_global_liste_certificats($initiale, $userids, $mode, $referentiel, $userid_filtre, $gusers, $sql_f_where, $sql_f_order, $data_f, $select_acc);
        }
		else{

            echo $OUTPUT->box_start('generalbox  boxaligncenter');

            if ($mode=='editcertif') {

                if( $certificat_id) {
                    // id certificat : un certificat particulier
                    if (! $record = $DB->get_record("referentiel_certificat", array("id" => "$certificat_id"))) {
                        print_error('Certificat ID is incorrect');
                    }
                    $modform = "certificat_edit.html";
                }
                else {
                    $modform = "certificat.html";
                }
            }

    		else if ($mode=='updatecertif'){
    			// recuperer l'id du certificat après l'avoir genere automatiquement et mettre en place les competences
			
    			if ($certificat_id) { // id certificat
       	    		if (! $record = $DB->get_record("referentiel_certificat", array("id" => "$certificat_id"))) {
    		            print_error('Certificat ID is incorrect');
        		    }
    			}
    			else{
    				print_error('Certificat ID is incorrect');
    			}
    			$modform = "certificat_edit.html";
    		}
    		else if ($mode=='addcertif'){
    			// recuperer l'id du certificat après l'avoir genere automatiquement et mettre en place les competences
    			if (!$certificat_id){
    				$certificat_id=referentiel_genere_certificat($USER->id, $referentiel_referentiel->id);
    			}
    			if ($certificat_id) { // id certificat
            		if (! $record = $DB->get_record("referentiel_certificat", array("id" => "$certificat_id"))) {
    		            print_error('Certificat ID is incorrect');
        		    }
    			}
    			else{
    				print_error('Certificat ID is incorrect');
    			}
    			$modform = "certificat_add.html";
    		}
		
            if (file_exists($modform)) {
                $defaultformat = FORMAT_PLAIN;
            	$editorfields = '';
                include_once($modform);
            }
            echo $OUTPUT->box_end();
        }
    }

    echo $OUTPUT->footer();
    die();
?>

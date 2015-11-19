<?php  // $Id: activite.php,v 1.0 2014/03/25 00:00:00 jfruitet Exp $
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

    require_once('../../config.php');
    require_once('locallib.php');
    include('print_lib_activite.php');	// AFFICHAGES pagines des  acivites
    include('lib_etab.php');
    include('lib_task.php');
    include('print_lib_task.php');	// AFFICHAGES TACHES

//*********************************************************************
// Pagination
$pageNo = optional_param('pageNo', 1, PARAM_INT);    // Page en cours
$extraParam='';
$totalRecords=0;

$pageName="$CFG->wwwroot/mod/referentiel/list_activites_users.php";
$divid='lifeCycle';  // zone affichage du retour des requêtes
$perPage=1;
$totalPage=1;
$sql='';

//*********************************************************************
    $id = optional_param('id', 0, PARAM_INT);    // course module id
    $d  = optional_param('d', 0, PARAM_INT); // Referentiel ID
    $mode  = optional_param('mode', '', PARAM_ALPHANUMEXT);    // Force the browse mode  ('single')
    $group = optional_param('group', -1, PARAM_INT);   // choose the current group
    $activite_id   = optional_param('activite_id', 0, PARAM_INT);    //record activite id
    $mailnow      = optional_param('mailnow', 0, PARAM_INT); // pour afficher les destinataires
    $action  	= optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements
    $old_mode   = optional_param('old_mode','', PARAM_ALPHA); // mode anterieur
    $add        = optional_param('add','', PARAM_ALPHA);
    $update     = optional_param('update', 0, PARAM_INT);
    $delete     = optional_param('delete', 0, PARAM_INT);
    $approved   = optional_param('approved', 0, PARAM_INT);
    $comment    = optional_param('comment', 0, PARAM_INT);
    $courseid   = optional_param('courseid', 0, PARAM_INT);
    $groupmode  = optional_param('groupmode', -1, PARAM_INT);
    $cancel     = optional_param('cancel', 0, PARAM_BOOL);
    $userid     = optional_param('userid', 0, PARAM_INT);
    $initiale   = optional_param('initiale','', PARAM_ALPHA); // selection apr les initiales du nom
    $userids    = optional_param('userids','', PARAM_TEXT); // id user selectionnes par les initiales du nom

    $mode_select= optional_param('mode_select','', PARAM_ALPHANUMEXT);
    $select_acc = optional_param('select_acc', -1, PARAM_INT);      // accompagnement
    $order    	= optional_param('order', 0, PARAM_INT);
    $userbareme = optional_param('userbareme', 0, PARAM_INT); // si un bareme est utilise pour la saisie

	$pagination= optional_param('pagination', 1, PARAM_INT);

    // Filtres
    require_once('filtres.php'); // Ne pas deplacer

    $url = new moodle_url('/mod/referentiel/activite.php');

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
		//die();
		print_error(get_string('erreur1','referentiel','activite.php'), 'referentiel');
	}

	if (empty($mode)){
    	$mode='listactivityall';
	}
	if ($mode=='list'){
        $mode='listactivity';
	}
	// DEBUG
	//echo "<br /> DEBUG :: activite.php :: 68 :: MODE : $mode\n";
    if ($mode=='listactivity'){
        $modeaff=2;
	}
    elseif ($mode=='listactivityall'){
        $modeaff=1;
	}
 	elseif ($mode=='updateactivity'){
        $modeaff=0;
	}
	else{   // 'addactivity'
        $modeaff=-1;
	}

    $contextcourse = context_course::instance($course->id);
    $context = context_module::instance($cm->id);

	if ($activite_id) { // id activite
        if (! $record = $DB->get_record("referentiel_activite", array("id" => "$activite_id"))) {
            print_error('incorrect_activity_id', 'referentiel', $CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
        }
	}

    require_login($course->id, false, $cm);   // pas d'autologin guest

    if (!isloggedin() or isguestuser()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
    }

	/// If it's hidden then it's don't show anything.  :)
    if (empty($cm->visible)
    && (
        !has_capability('moodle/course:viewhiddenactivities', $context)
            &&
        !has_capability('mod/referentiel:managecomments', $context)
        )

    ) {
        print_error(get_string("activityiscurrentlyhidden"),'error',"$CFG->wwwroot/course/view.php?id=$course->id");
    }



    if ($activite_id) {    // So do you have access?
    	if (!(has_capability('mod/referentiel:write', $context) or referentiel_activite_isowner($activite_id)) ) {
			print_error(get_string('noaccess','referentiel'));
		}
    }

	// RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else{
      $form = (object)$_POST;
    }

    // selecteur
    $userid_filtre=0;
	if (isset($userid) && ($userid>0)){
		$userid_filtre=$userid;
	}
	// DEBUG
	//echo "<br>DEBUG :: activite.php :: 181 :$userid_filtre\n";
	//exit;

  // accompagnement
	if ($select_acc==-1){
        $select_acc=(referentiel_has_pupils($referentiel->id, $course->id, $USER->id)>0);
    }

	if ($cancel) {
		$mode ='listactivityall';
		if (has_capability('mod/referentiel:managecertif', $context)){
	           $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?id=$cm->id&amp;select_acc=$select_acc&amp;userid=0&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
		}
		else{
	           $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?id=$cm->id&amp;select_acc=$select_acc&amp;userid=$userid&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
		}
    	if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
	        unset($SESSION->returnpage);
    	    redirect($return);
        }
		else {
	       redirect("$CFG->wwwroot/mod/referentiel/activite.php?id=$cm->id&amp;select_acc=$select_acc&amp;userid=$userid&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student");
    	}
        exit;
    }

    // utilisateur
    if (isset($action) && ($action=='selectuser')){
		  if (!empty($userid) && confirm_sesskey() ){
		  	$userid_filtre=$userid;
		  }
		  unset($form);
		  unset($action);
    }


	/// Delete any requested records
    if (isset($delete) && ($delete>0 )
		&& confirm_sesskey()
		&& (has_capability('mod/referentiel:write', $context) or referentiel_activite_isowner($delete))) {

        if ($confirm = optional_param('confirm',0,PARAM_INT)) {
            // suppression
			if (referentiel_delete_activity_record($delete)){
        		if ($CFG->version > 2014051200) { // Moodle 2.7+
            		$params = array(
                	'contextid' => $context->id,
                	'objectid' => $delete,
            		);
            		$event = \mod_referentiel\event\activite_deleted::create($params);
            		$event->trigger();
        		} else { // Before Moodle 2.7
					add_to_log($course->id, 'referentiel', 'activity deleted', "activite.php?id=$cm->id", $delete, $cm->id);
        		}
                // notify(get_string('recorddeleted','referentiel'), 'notifysuccess');
            }
        }
        redirect("$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;select_acc=$select_acc&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student");
        exit;
    }

	/// Approve any requested records
    if (isset($approved) && ($approved>0) && confirm_sesskey()
		&& has_capability('mod/referentiel:approve', $context)) {
        if ($approvedrecord = $DB->get_record("referentiel_activite", array("id" => "$approved"))) {
	        $confirm = optional_param('confirm',0,PARAM_INT);
			if ($confirm) {
                $approvedrecord->approved = 1;
			}
			else{
				$approvedrecord->approved = 0;
			}
			$approvedrecord->teacherid=$USER->id;
			$approvedrecord->date_modif=time();
			$approvedrecord->type_activite=($approvedrecord->type_activite);
			$approvedrecord->description_activite=($approvedrecord->description_activite);
			$approvedrecord->commentaire_activite=($approvedrecord->commentaire_activite);

            if ($DB->update_record("referentiel_activite", $approvedrecord)) {
				if (($approvedrecord->userid>0) && ($approvedrecord->competences_activite!='')){
					// mise a jour du certificat
					if ($approvedrecord->approved){
						referentiel_mise_a_jour_competences_certificat_user('', $approvedrecord->competences_activite, $approvedrecord->userid, $approvedrecord->ref_referentiel,$approvedrecord->approved, false, true);
					}
					else{
						referentiel_mise_a_jour_competences_certificat_user($approvedrecord->competences_activite, '', $approvedrecord->userid, $approvedrecord->ref_referentiel,$approvedrecord->approved, false, true);
					}
				}
            }
			if (isset($userid) && ($userid>0)){
				$userid_filtre=$userid;
			}

			if (isset($old_mode) && ($old_mode!='')){
                $mode=$old_mode;
            }
        }
    }

	/// Comment any requested records
    if (isset($comment) && ($comment>0) && confirm_sesskey()
		&& has_capability('mod/referentiel:comment', $context))
    {
        if (!empty($activite_id)){
			if ($approvedrecord = $DB->get_record("referentiel_activite", array("id" => "$comment"))) {
				$approvedrecord->teacherid=$USER->id;
				$approvedrecord->date_modif=time();
				$approvedrecord->type_activite=($approvedrecord->type_activite);
				$approvedrecord->description_activite=($approvedrecord->description_activite);
				$approvedrecord->commentaire_activite=($form->commentaire_activite);
				if (isset($approved)) {
					$approvedrecord->approved=$approved;
				}
				if (isset($userid) && ($userid>0)){
					$userid_filtre=$userid;
				}
                if (isset($mailnow)){
                    $approvedrecord->mailnow=$mailnow;
                    if ($mailnow=='1'){ // renvoyer
                        $approvedrecord->mailed=0;   // annuler envoi precedent
                    }
                }
                else{
                    $approvedrecord->mailnow=0;
                }

                if ($DB->update_record('referentiel_activite', $approvedrecord)) {
					if (($approvedrecord->userid>0) && ($approvedrecord->competences_activite!='')){
						// mise a jour du certificat
						if ($approvedrecord->approved){
							referentiel_mise_a_jour_competences_certificat_user('', $approvedrecord->competences_activite, $approvedrecord->userid, $approvedrecord->ref_referentiel,$approvedrecord->approved, false, true);
						}
						else{
							referentiel_mise_a_jour_competences_certificat_user($approvedrecord->competences_activite, '', $approvedrecord->userid, $approvedrecord->ref_referentiel,$approvedrecord->approved, false, true);
						}
					}
				}
			}
			unset($form);

			// Relancer l'affichage de toutes les activites de l'utilisateur
			// en supprimant id de l'activite commentee
			$activite_id=0;

			if (isset($old_mode) && ($old_mode!='')){
                $mode=$old_mode;
            }
        }
    }


    if (!empty($referentiel) && !empty($course) && isset($form)) {
    	/// modification globale

        if (isset($_POST['action']) && ($_POST['action']=='modifier_activite_global')){
		    // echo "<br />DEBUG :: activite.php :: 274 :: ACTION : $action \n";
		    $form=$_POST;
			//echo "<br />activite.php :: 329 :: FORM<br />\n";
			//print_object($form);
			//exit;
            if (!empty($form['pageNo'])){
                $pageNo=$form['pageNo'];
				//echo "<br />DEBUG :: 359 :: PageNo:".$pageNo."\n";
				//exit;
			}
		    if (isset($form['tactivite_id']) && ($form['tactivite_id'])){
                //
                foreach ($form['tactivite_id'] as $id_activite){
                    //echo "<br />ID :: ".$id_activite."\n";
					// DEBUG
					//echo "<br />DEBUG : activite.php :: 375 UTILISE BAREME<br />FORMULAIRE INPUT<br />\n";
					//print_object($form);

                    $form2= new Object();
                    $form2->action='modifier_activite';
                    $form2->activite_id=$form['activite_id_'.$id_activite];
        		    $form2->type_activite=$form['type_activite_'.$id_activite];
		            $form2->old_liste_competences=$form['old_liste_competences_'.$id_activite];

                    $form2->description_activite=stripslashes($form['description_activite_'.$id_activite]);
                    $form2->commentaire_activite=stripslashes($form['commentaire_activite_'.$id_activite]);
                    $form2->instance=$form['ref_instance_'.$id_activite];
                    $form2->ref_referentiel=$form['ref_referentiel_'.$id_activite];
                    $form2->courseid=$form['ref_course_'.$id_activite];
                    $form2->date_creation=$form['date_creation_'.$id_activite];
                    $form2->date_modif_student=$form['date_modif_student_'.$id_activite];
                    $form2->date_modif=$form['date_modif_'.$id_activite];

                    if (!empty($form['approved_'.$id_activite]))  {
                        $form2->approved=$form['approved_'.$id_activite];
                    }
                    else {
                        $form2->approved=0;
                    }
                    $form2->userid=$form['userid_'.$id_activite];
                    $form2->teacherid=$form['teacherid_'.$id_activite];
                    $form2->mailnow=$form['mailnow_'.$id_activite];

					if ($userbareme){   // evaluation basee sur bareme

							if (!empty($form['baremeid']) && !empty($form['nbitems'])){
								$liste_evaluations='';
								for ($k=0; $k<$form['nbitems']; $k++){
									if (isset($form['code_item_'.$id_activite.'_'.$k])){
										if ($form['code_item_'.$id_activite.'_'.$k]>=$form['seuil']){
                                            $form['code_item_'.$id_activite][] = $form['code_code'][$k];  // astuce pour propager les competences validees
										}
										$liste_evaluations.=$form['code_code'][$k].':'.$form['code_item_'.$id_activite.'_'.$k].'/';
									}
								}
							}
							require_once('lib_bareme.php');
							referentiel_enregistrer_evaluation_activite($liste_evaluations, $id_activite, $form['baremeid']);
					}
                   	if (isset($form['code_item_'.$id_activite]) && is_array($form['code_item_'.$id_activite]) ){
     					$form2->competences_activite=reference_conversion_code_2_liste_competence('/', $form['code_item_'.$id_activite]);
        	        }
                    else if (isset($form['competences_activite_'.$id_activite])){
                    	$form2->competences_activite=$form['competences_activite_'.$id_activite];
                    }
                    else{
                    	$form2->competences_activite='';
                    }
                    //echo "<br />DEBUG : activite.php :: 431 FORMULAIRE OUTPUT<br />\n";
                    //print_object($form2);
                    //echo "<br />\n";
					//exit;
                    $return = referentiel_update_activity($form2);
                    if (!$return) {
                        print_error("Could not update activity $form->activite_id of the referentiel", "activite.php?d=$referentiel->id");
                    }
                    if (is_string($return)) {
                        print_error($return, "activite.php?d=$referentiel->id");
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	        		$params = array(
        	        	'contextid' => $context->id,
            	    	'objectid' => $form2->activite_id,
            			);
            			$event = \mod_referentiel\event\activite_updated::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
	                    add_to_log($course->id, 'referentiel', "activity $form2->activite_id  updated", $form2->instance, $cm->id);
        			}
                }
            }
            unset($form);
			if (!empty($userid_filtre)){
            	redirect("$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;userid=$userid_filtre&amp;select_acc=$select_acc&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student");
			}
			else{
            	redirect("$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student");
			}
            exit;
        }

        elseif (!empty($action) && (($action=='ajouter_activite') || ($action=='modifier_activite')  || ($action=='modifier_document'))
            && !empty($mode) &&
                (($mode=='modifactivity') or ($mode=='addactivity') or ($mode=='deleteactivity'))){
            // add, delete or update form submitted

            // Afficher la liste des destinataires ?
            if (!empty($form->mailnow)){
                $mailnow=1;
            }

            $addfunction    = "referentiel_add_activity";
            $updatefunction = "referentiel_update_activity";
            $deletefunction = "referentiel_delete_activity";

            switch ($mode) {
                case "modifactivity":
                    if (isset($form->name)) {
                        if (trim($form->name) == '') {
       		        	  unset($form->name);
                        }
                    }
                    if (isset($form->delete) && ($form->delete==get_string('delete'))){
                        // suppression
                        $return = $deletefunction($form);
                        if (!$return) {
    	         	      	print_error("Could not update activity $form->activite_id of the referentiel", "activite.php?d=$referentiel->id");
                        }
                        if (is_string($return)) {
                            print_error($return, 'error', "activite.php?d=$referentiel->id&amp;userid=$form->userid");
                        }
	        			if ($CFG->version > 2014051200) { // Moodle 2.7+
    	        			$params = array(
        	        			'contextid' => $context->id,
            	    			'objectid' => $form->activite_id,
            				);
            				$event = \mod_referentiel\event\activite_deleted::create($params);
	            			$event->trigger();
    	    			} else { // Before Moodle 2.7
	                        add_to_log($course->id, 'referentiel', 'delete', "mise a jour activite {$form->activite_id}", $form->instance, $cm->id);
        				}
                    }
                    else {
						if ($userbareme){   // evaluation basee sur bareme
		    				$form3=$_POST;
							// DEBUG
							//echo "<br />DEBUG : activite.php :: 496 UTILISE BAREME<br />FORMULAIR INPUT<br />\n";
							//print_object($form3);
							//exit;
							
							$liste_evaluations='';
                            if (!empty($form3['baremeid'])) {
                                // modification by a teacher
                                if (!empty($form3['nbitems'])){
                                    
                                    for ($k=0; $k<$form3['nbitems']; $k++){
								        if (isset($form3['code_item_'.$activite_id.'_'.$k])){
								            for ($k=0; $k<$form3['nbitems']; $k++){
                                                if (isset($form3['code_item_'.$activite_id.'_'.$k])){
										            if ($form3['code_item_'.$activite_id.'_'.$k]>=$form3['seuil']){
                                                        $form->code_item[]=$form3['code_code'][$k];
										            }
									       	        $liste_evaluations.=$form3['code_code'][$k].':'.$form3['code_item_'.$activite_id.'_'.$k].'/';
									            }
								            }
 						                 }   
                                    } 
                                } 
 						        // modification by a student
						        else if (isset($form3['code_item_'.$activite_id]) && isset($form3['threshold'])){
                                    if ($form3['threshold']>0) $value=$form3['threshold']-1; else $value=0;
						            $liste_evaluations=$form3['competencesbareme'];
                                    foreach ($form3['code_item_'.$activite_id] as $comp){
                                        if (!empty($comp)){
                                            // get the value attached to the competency code
                                            // comp A1-2:3  --> 3
                    				        /*
                                            if (!empty($form3['competencesbareme'])&& preg_match("/$comp:(\d+)\//",$form3['competencesbareme'], $matches)){
                                                //echo "<br />DEBUG : activite.php :: 520 <br />";
                                                //print_object($matches) ;                                                
                                                if (!empty($matches[1])){  // get anterior evaluation 
                                                    $liste_evaluations.=$comp.':'.$matches[1].'/';
                                                } 
                                                else{ // set treshold-1 evaluation
                                                    $liste_evaluations.=$comp.':'.$value.'/';       
                                                }	                    	                     
                    				        }
                                            else{
                                                $liste_evaluations.=$comp.':'.$value.'/';
					                        }
					                        */
					                        // fusionner avec la liste d'evaluation
					                        if (!empty($form3['competencesbareme']) && !preg_match("/$comp/",$form3['competencesbareme'])){
					                           $liste_evaluations.=$comp.':'.$value.'/';
					                        }                                    
                                        }
                                     }
                                  }        
                              }
						      //if (!empty($liste_evaluations)) {
 							         // DEBUG
							         // echo "<br />DEBUG : activite.php :: 525 <br />LISTE_BAREME $liste_evaluations<br />\n";
							         // exit;
							         // enregistrer les evaluations
							         require_once('lib_bareme.php');                             
                                     //if (!empty($form3['competencesbareme'])) {
            				         //   echo "<br />DEBUG : activite.php :: 530 <br />COMPETENCES_BAREME ".$form3['competencesbareme']."<br />\n";
			                            
                                     //}
                                     //exit;
							         referentiel_enregistrer_evaluation_activite($liste_evaluations, $activite_id, $form3['baremeid']);         
                              //}
                        }
                        
                        $return = $updatefunction($form);
                        if (!$return) {
                            print_error("Could not update activity $form->id of the referentiel", 'error', "activite.php?d=$referentiel->id");
                        }
                        if (is_string($return)) {
    	        		    print_error($return, "activite.php?d=$referentiel->id");
                        }
	        			if ($CFG->version > 2014051200) { // Moodle 2.7+
    	        			$params = array(
        	        			'contextid' => $context->id,
		            	    	'objectid' => $form->activite_id,
        	    			);
            				$event = \mod_referentiel\event\activite_updated::create($params);
	            			$event->trigger();
    	    			} else { // Before Moodle 2.7
	                    	add_to_log($course->id, 'referentiel', "activity $form->activite_id  updated", $form->instance, $cm->id);
	        			}

					   // depot de document ?
                        if (isset($form->depot_document) && ($form->depot_document==get_string('yes'))){
						    // APPELER le script upload moodle2.php
                            if (!empty($form->ref_activite) || !empty($form->activite_id) ){
                                if (empty($form->ref_activite)){
                                    $form->ref_activite=$form->activite_id;
                                }
                                if (!empty($form->document_id)){
								  redirect($CFG->wwwroot.'/mod/referentiel/upload_moodle2.php?d='.$referentiel->id.'&amp;userid='.$form->userid.'&amp;activite_id='.$form->ref_activite.'&amp;mailnow='.$mailnow.'&amp;select_acc='.$select_acc.'&amp;document_id='.$form->document_id.'&amp;mode=updatedocument&amp;f_auteur='.$data_f->f_auteur.'&amp;f_validation='.$data_f->f_validation.'&amp;f_referent='.$data_f->f_referent.'&amp;f_date_modif='.$data_f->f_date_modif.'&amp;f_date_modif_student='.$data_f->f_date_modif_student.'&amp;old_mode=listactivityall&amp;sesskey='.sesskey());
                                }
                                else{
								  redirect($CFG->wwwroot.'/mod/referentiel/upload_moodle2.php?d='.$referentiel->id.'&amp;userid='.$form->userid.'&amp;activite_id='.$form->ref_activite.'&amp;mailnow='.$mailnow.'&amp;select_acc='.$select_acc.'&amp;document_id=0&amp;mode=adddocument&amp;f_auteur='.$data_f->f_auteur.'&amp;f_validation='.$data_f->f_validation.'&amp;f_referent='.$data_f->f_referent.'&amp;f_date_modif='.$data_f->f_date_modif.'&amp;f_date_modif_student='.$data_f->f_date_modif_student.'&amp;old_mode=listactivityall&amp;sesskey='.sesskey());
                                }
                                exit;
                             }
                        }
                    }
                    $mode ='listactivityall';
					if (has_capability('mod/referentiel:managecertif', $context)){
	    	        	$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$form->userid&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
					}
					else{
                        if ($mailnow){
	            		     $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$form->userid&activite_id=$activite_id&mailnow=$mailnow&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
                        }
                        else{
	            		     $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$form->userid&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
                        }
					}
                break;

                case "addactivity":

                    if (!isset($form->name) || trim($form->name) == '') {
        			    $form->name = get_string("modulename", "referentiel");
                    }
                    $return = $addfunction($form);
		      		if (!$return) {
					     print_error("Could not add a new activity to the referentiel", 'error', "activite.php?d=$referentiel->id");
			     	}
                    if (is_string($return)) {
                        print_error($return, 'error', "activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$form->userid");
				    }

				    // depot de document ?
				    if (isset($form->depot_document) && ($form->depot_document==get_string('yes'))){
				        // APPELER le script
                        if ($return){
                            redirect($CFG->wwwroot.'/mod/referentiel/upload_moodle2.php?d='.$referentiel->id
                            .'&amp;userid='.$form->userid
                            .'&amp;activite_id='.$return
                            .'&amp;select_acc='.$select_acc
                            .'&amp;mailnow='.$mailnow
                            .'&amp;document_id=0&amp;mode=adddocument&amp;f_auteur='.$data_f->f_auteur.'&amp;f_validation='.$data_f->f_validation.'&amp;f_referent='.$data_f->f_referent.'&amp;f_date_modif='.$data_f->f_date_modif.'&amp;f_date_modif_student='.$data_f->f_date_modif_student
                            .'&amp;old_mode=listactivityall&amp;sesskey='.sesskey());
                            exit;
                        }
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	        		$params = array(
        	        		'contextid' => $context->id,
		            	    'objectid' => $form->activite_id,
        	    		);
            			$event = \mod_referentiel\event\activite_created::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
	                    add_to_log($course->id, 'referentiel', "add", "activity $form->activite_id  created", $form->instance, $cm->id);
	        		}

                    $mode ='listactivityall';
					if (has_capability('mod/referentiel:managecertif', $context)){
	    	                  $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$form->userid&mailnow=$mailnow&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
					}
					else{
                        if ($mailnow){
	            		     $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$form->userid&activite_id=$return&mailnow=$mailnow&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
                        }
                        else{
	            		     $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$form->userid&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
                        }
				    }
                break;

                case "deleteactivity":
                    if (! $deletefunction($form)) {
	            	    print_error("Could not delete activity of the referentiel module");
                    }
	                else{
                        unset($SESSION->returnpage);
		        		if ($CFG->version > 2014051200) { // Moodle 2.7+
        		    		$params = array(
                				'contextid' => $context->id,
			                	'objectid' => $form->activite_id,
            				);
            				$event = \mod_referentiel\event\activite_deleted::create($params);
		            		$event->trigger();
        				} else { // Before Moodle 2.7
							add_to_log($course->id, 'referentiel', 'activity deleted', "activite.php?id=$cm->id", $delete, $cm->id);
			        	}
				    }
      		        $mode ='listactivityall';
			   	    if (has_capability('mod/referentiel:managecertif', $context)){
                        $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$form->userid&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
				    }
				    else{
	            	    $SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$form->userid&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student";
				    }
                break;

			    default:
            	   // print_error("Incorrect mode defined");
            	   // echo "<br>571 :: MODE : $mode\n";
	               exit;
            }


            if (!empty($SESSION->returnpage)) {
                $return = $SESSION->returnpage;
                unset($SESSION->returnpage);
                redirect($return);
            }
		    else {
                redirect("$CFG->wwwroot/mod/referentiel/activite.php?d=$referentiel->id&amp;pageNo=$pageNo&amp;select_acc=$select_acc&amp;userid=$userid&amp;mode=$mode&amp;f_auteur=$data_f->f_auteur&amp;f_validation=$data_f->f_validation&amp;f_referent=$data_f->f_referent&amp;f_date_modif=$data_f->f_date_modif&amp;f_date_modif_student=$data_f->f_date_modif_student");
            }

            exit;

        }
    }
	/// selection filtre
    if (empty($userid_filtre) || ($userid_filtre==$USER->id) || (isset($mode_select) && ($mode_select=='selectetab'))){
        set_filtres_sql();
    }

	/// Check to see if groups are being used here
	/// find out current groups mode
	$groupmode = groups_get_activity_groupmode($cm);
    if (!empty($group) && ($group>-1)){
        $currentgroup = $group;
    }
    else{
        $currentgroup = groups_get_activity_group($cm, true);
    }

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

	// PAGINATION  #######################################################################################################
	// trouver le nombre d'enregistrements à afficher
	$iseditor = has_capability('mod/referentiel:writereferentiel', $context);
	$isteacher = has_capability('mod/referentiel:approve', $context)&& !$iseditor;
	$istutor = has_capability('mod/referentiel:comment', $context) && !$iseditor  && !$isteacher;
	$isauthor = has_capability('mod/referentiel:write', $context) && !$iseditor  && !$isteacher  && !$istutor;

    // DEBUG
	// echo "<br>DEBUG :: 633:: RECORD ID USERS :<br>MODE:$mode MODEAFF:$modeaff\n";

	if ($modeaff>=0){
    	// DEBUG
		// echo "<br>DEBUG :: 636:: RECORD ID USERS :<br>MODE:$mode MODEAFF:$modeaff\n";

    	$onclick='';
		$records_id_users=referentiel_get_liste_users_pagination($course, $referentiel, ($isteacher || $iseditor|| $istutor), $userids, $userid_filtre, $gusers, $select_acc);
		// afficher les activites des utilisateurs
        // DEBUG
		//echo "<br>DEBUG :: 642 :: RECORD ID USERS :<br>\n";
    	//print_object($records_id_users);

		if ($records_id_users){
        	// DEBUG
			//echo "<br>DEBUG :: 706:: RECORD ID USERS :<br>\n";
    	    //print_object($records_id_users);
	    	if (!empty($referentiel->ref_referentiel)){
				$params=array();
                if ($sql_f_order==''){
                    $sql_order=''; //'  userid ASC, date_creation DESC ';
                }
                else{
                    $sql_order=$sql_f_order;
                }
                if ($sql_f_select==''){
                    $sql_select=''; //'  userid, date_creation ';
                }
                else{
                    $sql_select=' , '.$sql_f_select;
                }
                $params[]=$referentiel->ref_referentiel;

                if (($modeaff>=0) && ($modeaff<3)) { // mode==listactivityall or mode==modifyactivity
					// $sql = 'SELECT * FROM {referentiel_activite} WHERE ref_referentiel=? AND ';
					$sql_count = 'SELECT COUNT(ra.id) as nb FROM {referentiel_activite} as ra WHERE ra.ref_referentiel=? AND ';
                }
				$sql2 = 'SELECT DISTINCT ra.userid AS userid, COUNT(ra.id) AS activitynumber '. $sql_select .'
 FROM {referentiel_activite} AS ra, {user} AS u
 WHERE ra.ref_referentiel=? AND ra.userid=u.id AND ';

				$sql_users='';
				if ($sql_order){
                	$sql_where_order= ' '.$sql_f_where.' ORDER BY '.$sql_order;
                }
				else{
                    $sql_where_order= ' '.$sql_f_where;
				}
                
				foreach ($records_id_users as $rec){
            		//print_r($rec);
                    $params[]=$rec->userid;

					if (empty($sql_users)){
                        $sql_users = " ((ra.userid=?) ";
                    }
                    else{
                        $sql_users .= " OR (ra.userid=?) ";
                    }
                }

                if (!empty($sql_users)){
                    $sql_users .=") ";
                    //$sql=addslashes($sql.$sql_users.$sql_where_order); // MODIF JF 2014/11/14
                    $sql=addslashes(trim($sql_where_order));
					$sql2=addslashes($sql2.$sql_users);
                    //$sql_count=$sql_count.$sql_users.$sql_where_order;
					$sql_count=$sql_count.$sql_users;
                    // DEBUG
                    //echo "<br>DEBUG :: 643 :: Params<br />\n";
					//print_object($params);

                    //echo "<br>DEBUG :: 786 :: SQL&gt; ".htmlspecialchars($sql_count)."\n";
                    //exit;
					if ($rec=$DB->get_record_sql($sql_count, $params)){
                    	// DEBUG
                    	// echo "<br>DEBUG :: 689 :: COUNT:<br>\n";
                    	// print_object($rec);
                    	$totalRecords=$rec->nb;
					}
                	if ($sql_f_select==''){
                    	$sql_group_by=''; //'  userid, date_creation ';
                	}
                	else{
                    	$sql_group_by=' , '.$sql_f_select. ' ';
                	}

					$sql2 .= ' GROUP BY ra.userid '. $sql_group_by . $sql_where_order;
                }
            }

			// Liste des bilans d'activite
			$sql2=stripslashes($sql2);
			//echo "<br />DEBUG :: 770 :: Length : ".htmlspecialchars($sql2)."\n";
            //print_object($params);
			//exit;
			if ($recs_bilans=$DB->get_records_sql($sql2, $params)){
       			$recs_bilans=referentiel_order_users_count($records_id_users, $recs_bilans, $order);
				//print_object($recs_bilans);
                $lparams=$params[0].'|';
                foreach($recs_bilans as $rcb){
					$lparams.= $rcb->userid.':'.$rcb->activitynumber.'|';
				}
                //echo "<br />DEBUG :: 779 :: LPARAMS : ".$lparams."\n";
				//exit;
			}
			
			if ($totalRecords >0){
	            if (empty($pagination)){
					$nombrePage = 1;
        	        $totalPage = 1;
            	    $perPage=$totalRecords;
            	}
				else{
					// nombre de pages à afficher
					$nombrePage = ceil((float)$totalRecords / (float) MAXPARPAGE);
        	        $totalPage = min(MAXPAGE, $nombrePage);
            	    $perPage=ceil((float)$totalRecords / (float)$totalPage);

					/*
	            	$totalPage=1;
    	        	$perPage=ceil((float)$totalRecords / (float)$totalPage);       // nombre d'enregistrements par page
        	    	while ($perPage>MAXPARPAGE){
            	    	$totalPage++;
                		$perPage=ceil((float)$totalRecords / (float)$totalPage);       // nombre d'enregistrements par page
	            	}
					*/
        	        //echo "<br />DEBUG :: 710 :: totalRecords:".$totalRecords." nombrePage:".$nombrePage." totalPage:".$totalPage." perPage:".$perPage." PageNo:".$pageNo."\n";

				}

				if ($pageNo>$totalPage){
                    $pageNo=1;
				}
                //echo "<br />DEBUG :: 689 :: PageNo:".$pageNo."\n";
				//exit;
		    	//echo "<br />DEBUG :: 820 :: ".$lparams."\n";
    			//echo "<br />DEBUG :: 821 :: ".htmlentities($sql)."\n";
				//exit;
		    	$sql = str_replace(">","&gt;",$sql);    // hack
		    	$sql = str_replace("<","&lt;",$sql);    // hack
		    	$sql = str_replace("\n","",$sql);    // hack
				// JavaScript Document
				//echo "<br />DEBUG :: 719 :: ".htmlentities($sql)."\n";
				//echo "<br />DEBUG :: 862 :: ".urlencode($sql)."\n";
				//exit;
			    //$sql = str_replace('>','&gt;',$sql);    // hack
			    //$sql = str_replace('<','&lt;',$sql);    // hack
				// MODIF JF 2014/11/14

				$ajaxvalue = "'".urlencode($pageName)."','".$pageNo."','".$referentiel->id."','".$sql."','".$lparams."','".$divid."','".$totalPage."','".$perPage."','".$select_acc."','".$modeaff."','".$userid_filtre."','".$order."','".$pagination."'";
				// $ajaxvalue = "'".$pageName."',1,".$referentiel->id.",'".$sql."','".$lparams."','".$divid."',".$totalPage.",".$perPage.",".$select_acc.",".$modeaff."";
		//        echo "<br>DEBUG :: 834 :: Ajax; <br />\n";
        //        echo $ajaxvalue;
		//      	exit;
				$onclick="javascript:ajaxPaging(".$ajaxvalue.");";
    			//$onclick="javascript:affiche2($value);";
			}
        }
	}

	// afficher les formulaires
   	unset($SESSION->modform); // Clear any old ones that may be hanging around.


/// Can't use this if there are no activite
/*
    if (has_capability('mod/referentiel:managetemplates', $context)) {
        if (!record_exists('referentiel_activite','ref_referentiel',$referentiel->id)) {      // Brand new referentielbase!
            redirect($CFG->wwwroot.'/mod/referentiel/activite.php?d='.$referentiel->id);  // Redirect to field entry
        }
    }
*/


// AFFICHAGE DE LA PAGE Moodle 2
    $stractivite = get_string('activite','referentiel');
    $strreferentiel = get_string('modulename', 'referentiel');
    $strreferentiels = get_string('modulenameplural', 'referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $url->param('mode', $mode);

    $PAGE->set_url($url);
    $PAGE->set_context($context);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->css('/mod/referentiel/pagination.css');
    $PAGE->requires->js($OverlibJs);
    $PAGE->requires->js('/mod/referentiel/functions.js');
     // Pagination
    $PAGE->requires->js('/mod/referentiel/ajax.js', true);
    if ($CFG->referentiel_use_scale){
    	$PAGE->requires->js('/mod/referentiel/bareme.js', true);
	}
	$PAGE->navbar->add($stractivite);
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();


	groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/activite.php?d='.$referentiel->id.'&amp;mode='.$mode.'&amp;pageNo='.$pageNo.'&amp;select_acc='.$select_acc);

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

	/// Print the tabs
	if (!empty($mode) && (($mode=="deleteactivity") || ($mode=="modifactivity")
		|| ($mode=="desapproveactivity") || ($mode=="approveactivity") || ($mode=="commentactivity") )){
		$currenttab ='updateactivity';
	}
	else{
    	$currenttab = $mode;
	}

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, $data_f, $mode);
    $tab_onglets->display();

    echo "\n".'<br />'."\n".'<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$stractivite.' '.$OUTPUT->help_icon('activiteh','referentiel').'</h2></div>'."\n";
    // JF 2011/11/29 - Affiche destinataires en cas de notification
    if ($mailnow && $activite_id) { // id 	activite
        $destinataires=referentiel_get_referents_notification($DB->get_record('referentiel_activite', array('id' => $activite_id)));
        if ($destinataires->nbdestinataires){
            echo '<div align="center"> <a class="overlib" href="javascript:void(0);" onmouseover="return overlib(\''.$destinataires->liste_destinataires.'\', WIDTH, 500, STICKY, MOUSEOFF, VAUTO, FGCOLOR, \'#DDEEFF\', CAPTION, \''.get_string('referents', 'referentiel').'\');" onmouseout="return nd();">'.get_string('destinataires_notification', 'referentiel').'</a></div>'."\n";
        }
    }
    // http://localhost/moodle253/mod/referentiel/activite.php?id=3&non_redirection=0&sesskey=asmZPzxox5&f_auteur=0&f_validation=0&f_referent=0&f_date_modif=0&f_date_modif_student=0&select_acc=0&mode=addactivity
	if (($mode=='addactivity') || ($mode=='modifactivity') || ($mode=="deleteactivity") || ($mode=="desapproveactivity") || ($mode=="approveactivity") || ($mode=="commentactivity")){
        echo $OUTPUT->box_start('generalbox  boxaligncenter');
		// recuperer l'id de l'activite
		if ($activite_id) {
            // page modification d'une activite
            if (! $record =  $DB->get_record("referentiel_activite", array("id" => "$activite_id"))) {
		    	print_error('Activite ID is incorrect');
			}
			$modform = "activite_edit_inc.php";
		}
		else {
            // saisie d'une nouvelle activite
			$modform = "activite_inc.php";
		}

    	// formulaires
	    if (file_exists($modform)) {
        	$defaultformat = FORMAT_PLAIN;
            $editorfields = '';
	    }
		else {
    	    notice("ERREUR : No file found at : $modform)", "activite.php?d=$referentiel->id");
    	}

		include_once($modform);
        echo $OUTPUT->box_end();
	}
	else {
		// boites de selection
		referentiel_boites_selections($context, $mode, $cm, $course, $referentiel, $initiale, $userids, $userid_filtre, $gusers, $data_f, $select_acc);

		echo '<div>'."\n";
		if (!empty($activite_id)){ // affichage de l'activite
            $bareme=NULL;
			if ($CFG->referentiel_use_scale){
				require_once('lib_bareme.php');
				if ($rec_assoc=referentiel_get_assoc_bareme_occurrence($referentiel_referentiel->id)){
 					$bareme=referentiel_get_bareme($rec_assoc->refscaleid);
				}
			}
    		referentiel_activite_id($context, $mode, $cm, $referentiel, $activite_id, $bareme, $select_acc, ($mode=='listactivityall'));
		}
		else{
			// Affichage des boites de selection et espace pour insertion Ajax
    		//referentiel_activites_paginees($context, $mode, $cm, $course, $referentiel, $initiale, $userids, $userid_filtre, $gusers, $records_id_users, $divid, $data_f, $select_acc);
			// afficher les activites
			if ($records_id_users){
				// Afficher
				// ESPACE DEDIE A L'INSERTION AJAX

				echo '<br />'."\n";
				echo '<div style="text-align:center;">'."\n";;

				if ($pagination){
					echo '<a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?id='.$cm->id.'&amp;pagination=0&amp;select_acc='.$select_acc.'&amp;userid='.$userid.'&amp;mode='.$mode.'&amp;f_auteur='.$data_f->f_auteur.'&amp;f_validation='.$data_f->f_validation.'&amp;f_referent='.$data_f->f_referent.'&amp;f_date_modif='.$data_f->f_date_modif.'&amp;f_date_modif_student='.$data_f->f_date_modif_student.'&amp;sesskey='.sesskey().'">'.get_string('pagination_no','referentiel').'</a> &nbsp; &nbsp;
<a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?id='.$cm->id.'&amp;pagination=1&amp;select_acc='.$select_acc.'&amp;userid='.$userid.'&amp;mode='.$mode.'&amp;f_auteur='.$data_f->f_auteur.'&amp;f_validation='.$data_f->f_validation.'&amp;f_referent='.$data_f->f_referent.'&amp;f_date_modif='.$data_f->f_date_modif.'&amp;f_date_modif_student='.$data_f->f_date_modif_student.'&amp;sesskey='.sesskey().'"><b>'.get_string('pagination_yes','referentiel').'</b></a>'."\n";
				}
				else{
					echo '<a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?id='.$cm->id.'&amp;pagination=0&amp;select_acc='.$select_acc.'&amp;userid='.$userid.'&amp;mode='.$mode.'&amp;f_auteur='.$data_f->f_auteur.'&amp;f_validation='.$data_f->f_validation.'&amp;f_referent='.$data_f->f_referent.'&amp;f_date_modif='.$data_f->f_date_modif.'&amp;f_date_modif_student='.$data_f->f_date_modif_student.'&amp;sesskey='.sesskey().'"><b>'.get_string('pagination_no','referentiel').'</b></a> &nbsp; &nbsp;
<a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?id='.$cm->id.'&amp;pagination=1&amp;select_acc='.$select_acc.'&amp;userid='.$userid.'&amp;mode='.$mode.'&amp;f_auteur='.$data_f->f_auteur.'&amp;f_validation='.$data_f->f_validation.'&amp;f_referent='.$data_f->f_referent.'&amp;f_date_modif='.$data_f->f_date_modif.'&amp;f_date_modif_student='.$data_f->f_date_modif_student.'&amp;sesskey='.sesskey().'">'.get_string('pagination_yes','referentiel').'</a>'."\n";
				}

				echo '</div>';
?>
				<!-- Espace insertion -->
<div id="pagin" class="pagination"></div>
<div id="<?php echo $divid;?>"></div>
<?php
				echo '<br /><br />'."\n";
				if (!empty($onclick)){
					echo '<!-- Espace chargement -->
<div id="loadin" align="center">
<!-- button id="clickme" onclick="'.$onclick.'">'.get_string('click_to_load','referentiel').'</button -->
<img src="'.$OUTPUT->pix_url('ajax-loader','referentiel').'" onload="'.$onclick.'">
</div>'."\n";
				}
			}
		}
		echo '</div>'."\n";
	}

    echo $OUTPUT->footer();
    die();

?>

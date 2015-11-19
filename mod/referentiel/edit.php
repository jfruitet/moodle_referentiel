<?php  // $Id: edit.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
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
* Modification du referentiel
* association a l'instance
*
* @version $Id: edit.php,v 2.0 2009/08/04/ 00:00:00 jfruitet Exp $
* @author Martin Dougiamas, Howard Miller, and many others.
*         {@link http://moodle.org}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package referentiel
*/


    require_once('../../config.php');
    require_once('locallib.php');
    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel instance id
	$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
    $checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni
  	$action  	= optional_param('action','', PARAM_ALPHANUMEXT);
  	$delete = optional_param('delete','', PARAM_ALPHANUMEXT);
    $deleteid    = optional_param('deleteid', 0, PARAM_INT);    // id object to delete
    $mode = optional_param('mode','all', PARAM_ALPHANUMEXT);
 	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/edit.php');

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
        $url->param('id', $id);
    }
	else{
		print_error(get_string('erreurscript','referentiel','Erreur01 : edit.php'));
	}

    if ($mode !== 'all') {
        $url->param('mode', $mode);
    }

    require_login($course->id, false, $cm);
    if (!isloggedin() || isguestuser()) {
        redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
    }

    // Contexte
    $context = context_module::instance($cm->id);

    if (!empty($referentiel->id)) {    // So do you have access?
        if (!has_capability('mod/referentiel:writereferentiel', $context)
        or !confirm_sesskey() ) {
            print_error(get_string('noaccess','referentiel'));
        }
    }
	else{
		print_error('Referentiel instance is incorrect');
	}

	// lien vers le referentiel lui-meme
	if (!empty($referentiel->ref_referentiel)){
	    if (! $referentiel_referentiel = $DB->get_record('referentiel_referentiel', array("id" => "$referentiel->ref_referentiel"))) {
    		print_error('Referentiel referentiel id is incorrect '.$referentiel->ref_referentiel);
    	}
    }
	else{
		// rediriger vers la creation du referentiel
		redirect(new moodle_url('/mod/referentiel/add.php', array('d'=>$referentiel->id, 'sesskey'=>sesskey())));
	}
	

	// RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    } 
	else {
        $form = (object)$_POST;
    }

    // Traitement des POST
	$msg="";

	if (!empty($course) && !empty($cm) && !empty($referentiel)  && !empty($referentiel_referentiel) && isset($form)) {

        $returnlink = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));

		// le mot de passe est-il actif ?
		// cette fonction est due au param�trage
		if ((!$pass) && ($checkpass=='checkpass')){
            if (!empty($form->pass_referentiel) && $referentiel_referentiel){
                if (!empty($form->force_pass)){  // forcer la sauvegarde sans verification
                    $pass=referentiel_set_pass($referentiel_referentiel->id, $form->pass_referentiel);
                }
                else{ // tester le mot de passe
                    $pass=referentiel_check_pass($referentiel_referentiel, $form->pass_referentiel);
                }
                if (!$pass){
                    // Abandonner
                    redirect($returnlink);
                    exit;
                }
            }
            else{    // mot de passe vide mais c'est un admin qui est connecte
                if (!empty($form->force_pass)){
                    $pass=1; // on passe... le mot de passe !
                }
            }
		}
		
		// variable d'action
		if (!empty($form->cancel)){
			if ($form->cancel == get_string("quit", "referentiel")){
				// Abandonner
    		    redirect($returnlink);
       			exit;
			}
		}
		
		// variable d'action
		if (!empty($delete)){
            if ($delete == get_string("delete")){
                if (!empty($deleteid) && (($action=="modifierdomaine") || ($action=="modifiercompetence") || ($action=="modifieritem"))){
                    $component='';
                    if ($action=="modifierdomaine"){
						// enregistre les modifications
							$return=referentiel_supprime_domaine($deleteid);
							$component='domaine';
							$msg=get_string("referentiel", "referentiel")." ".$referentiel_referentiel->id." ".get_string("domaine", "referentiel")." ".$deleteid;
					}
					else if ($action=="modifiercompetence"){
							$return=referentiel_supprime_competence($deleteid);
                            $component='competence';
							$msg=get_string("referentiel", "referentiel")." ".$referentiel_referentiel->id." ".get_string("competence", "referentiel")." ".$deleteid;
					}
					else if ($action=="modifieritem"){
							$return=referentiel_supprime_item($deleteid);
                            $component='item';
							$msg=get_string("referentiel", "referentiel")." ".$referentiel_referentiel->id." ".get_string("item", "referentiel")." ".$deleteid;
					}

					if (!isset($return) || (!$return)) {
    	    	    	print_error("Could not delete component $component : $msg", "$CFG->wwwroot/mod/referentiel/view.php?id=$cm->id&amp;non_redirection=1");
                    }
                    if (is_string($return)) {
                        print_error($return, "$CFG->wwwroot/mod/referentiel/view.php?id=$cm->id&amp;non_redirection=1");
                    }

			        if ($return) {
						// Mise a jour de la liste de competences dans le referentiel
						$liste_codes_competence=referentiel_new_liste_codes_competence($referentiel_referentiel->id);
						// echo "<br />LISTE_CODES_COMPETENCE : $form->liste_codes_competence\n";
						referentiel_set_liste_codes_competence($referentiel_referentiel->id, $liste_codes_competence);
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    			$params = array(
        	       			'contextid' => $context->id,
            	   			'objectid' => $referentiel->id,
							'other' => array('component' => $component, 'componentid' => $deleteid, 'occurrenceid' => $referentiel_referentiel->id, 'msg' => $msg),
            			);
            			$event = \mod_referentiel\event\occurrence_component_deleted::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
	                    add_to_log($course->id, 'referentiel', 'delete', "edit.php?id=".$cm->id, $msg, $cm->module);
					}
			     }

			     redirect("$CFG->wwwroot/mod/referentiel/edit.php?id=$cm->id&amp;pass=1&amp;sesskey=".sesskey());
			}
		}

        if (isset($_POST['action']) && ($_POST['action']=='modifierdomcompitems')){
		    		// echo "<br />DEBUG :: edit.php :: 235 :: ACTION : ".$_POST['action']." \n";
            $form=$_POST;
			// print_object($form);
			// exit;
			if (!empty($form['tdomaine_id'])){
                //
                foreach ($form['tdomaine_id'] as $id_domaine){
                    // echo "<br />ID :: ".$id_domaine."\n";
                    // exit;
                    $form2= new Object();
                    $form2->domaine_id=$id_domaine;
        		    $form2->type_domaine=$form['type_domaine_'.$id_domaine];
        		    $form2->minima_domaine=$form['minima_domaine_'.$id_domaine];
        		    $form2->seuil_domaine=$form['seuil_domaine_'.$id_domaine];
                    $form2->num_domaine=$form['num_domaine_'.$id_domaine];
                    $form2->nb_competences=$form['nb_competences_'.$id_domaine];
                    if (isset($form['code_domaine_'.$id_domaine])){
                        $form2->code_domaine=$form['code_domaine_'.$id_domaine];
                    }
                    else{
                      	$form2->code_domaine='';
										}
                    if (isset($form['description_domaine_'.$id_domaine])){
                        $form2->description_domaine=$form['description_domaine_'.$id_domaine];
                    }
                    else{
                        $form2->description_domaine='';
                    }
                    $form2->ref_referentiel=$referentiel_referentiel->id;
                    //echo "<br />DEBUG :: edit.php :: 262<br />\n";
                    //print_object($form2);
                    //echo "<br />\n";
					//exit;
					$return = referentiel_update_domaine($form2);
                    if (!$return) {
                        print_error("Could not update domain $form2->domaine_id of the referentiel", "view.php?id=".$cm->id);
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    			$params = array(
        	       			'contextid' => $context->id,
            	   			'objectid' => $form2->domaine_id,
                            'other' => array('occurrenceid'=>$referentiel_referentiel->id),
            			);
            			$event = \mod_referentiel\event\domaine_updated::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
	                    add_to_log($course->id, 'referentiel', 'domain update', "Domain {$form2->domaine_id} updated");
					}
                }
            }

			if (!empty($form['tcompetence_id'])){
                //
                foreach ($form['tcompetence_id'] as $id_competence){
                    // echo "<br />ID :: ".$id_competence."\n";
                    //
                    $form2= new Object();
                    $form2->competence_id=$id_competence;
        		    $form2->type_competence=$form['type_competence_'.$id_competence];
        		    $form2->minima_competence=$form['minima_competence_'.$id_competence];
        		    $form2->seuil_competence=$form['seuil_competence_'.$id_competence];
                    $form2->num_competence=$form['num_competence_'.$id_competence];
                    $form2->nb_item_competences=$form['nb_item_competences_'.$id_competence];
                    if (isset($form['code_competence_'.$id_competence])){
                        $form2->code_competence=$form['code_competence_'.$id_competence];
                    }
                    else{
                      	$form2->code_competence='';
					}
                    if (isset($form['description_competence_'.$id_competence])){
                       	$form2->description_competence=$form['description_competence_'.$id_competence];
                    }
                    else{
                       	$form2->description_competence='';
                    }
                    $form2->ref_domaine=$form['ref_domaine_'.$id_competence];
                    $form2->reference_id=$referentiel_referentiel->id;
                    //echo "<br />DEBUG :: edit.php :: 262<br />\n";
                    //print_object($form2);
                    //echo "<br />\n";
                    //exit;
                    $return = referentiel_update_competence($form2);
                    if (!$return) {
                       	print_error("Could not update skill $form2->competence_id of the referentiel", "view.php?id=".$cm->id);
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    			$params = array(
        	       			'contextid' => $context->id,
            	   			'objectid' => $form2->competence_id,
                            'other' => array('occurrenceid'=>$referentiel_referentiel->id),
            			);
            			$event = \mod_referentiel\event\competence_updated::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
	                    add_to_log($course->id, 'referentiel', 'competency update', "Competency {$form2->competence_id} updated");
					}
                }
            }

			if (!empty($form['titem_id'])){
                //
                foreach ($form['titem_id'] as $id_item){
                    // echo "<br />ID :: ".$id_item."\n";
                    //
                    $form2= new Object();
                    $form2->item_id=$id_item;
        		    $form2->type_item=$form['type_item_'.$id_item];
                    $form2->num_item=$form['num_item_'.$id_item];
                    $form2->ref_competence=$form['ref_competence_'.$id_item];
					$form2->poids_item=$form['poids_item_'.$id_item];
					$form2->empreinte_item=$form['empreinte_item_'.$id_item];
                    if (isset($form['code_item_'.$id_item])){
                        $form2->code_item=$form['code_item_'.$id_item];
                    }
                    else{
                      	$form2->code_item='';
					}
                    if (isset($form['description_item_'.$id_item])){
                        $form2->description_item=$form['description_item_'.$id_item];
                    }
                    else{
                        $form2->description_item='';
                    }
                    $form2->ref_referentiel=$referentiel_referentiel->id;
                    //echo "<br />DEBUG :: edit.php :: 332<br />\n";
                    //print_object($form2);
                    //echo "<br />\n";
                    $return = referentiel_update_item($form2);
                    if (!$return) {
                        print_error("Could not update Item $form2->item_id of the referentiel", "view.php?id=".$cm->id);
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    			$params = array(
        	       			'contextid' => $context->id,
            	   			'objectid' => $form2->competence_id,
                            'other' => array('occurrenceid'=>$referentiel_referentiel->id),
            			);
            			$event = \mod_referentiel\event\item_updated::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
	                    add_to_log($course->id, 'referentiel', 'item update', "Item {$form2->item_id} updated");
					}
                }
            }

			// NOUVEL ITEM
			if (!empty($form['tnewitem_id'])){
                foreach ($form['tnewitem_id'] as $index_item){
                    // echo "<br />ID :: ".$index_item."\n";
                    $form2= new Object();

                    $form2->new_type_item=$form['new_type_item_'.$index_item];
					$form2->new_ref_competence=$form['new_ref_competence_'.$index_item];
					$form2->new_poids_item=$form['new_poids_item_'.$index_item];
					$form2->new_empreinte_item=$form['new_empreinte_item_'.$index_item];
                    if (isset($form['new_code_item_'.$index_item])){
                        $form2->new_code_item=$form['new_code_item_'.$index_item];
                    }
                    else{
                      	$form2->new_code_item='';
										}
                    if (isset($form['new_description_item_'.$index_item])){
                        $form2->new_description_item=$form['new_description_item_'.$index_item];
                    }
                    else{
                        $form2->new_description_item='';
                    }
                    $form2->occurrence=$referentiel_referentiel->id;
                    $form2->new_num_item=$form['new_num_item_'.$index_item];
                    $form2->num_competence=$form['new_num_competence_'.$index_item];
                    $form2->num_domaine=$form['new_num_domaine_'.$index_item];

                    //echo "<br />DEBUG :: edit.php :: 370<br />\n";
                    //print_object($form2);
                    //echo "<br />\n";
                    $return = referentiel_add_item($form2);
                    if (!$return) {
                        print_error("Could not create Item", "view.php?id=".$cm->id);
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    			$params = array(
        	       			'contextid' => $context->id,
            	   			'objectid' => $return,
                            'other' => array('occurrenceid'=>$referentiel_referentiel->id),
            			);
            			$event = \mod_referentiel\event\item_created::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
	                    add_to_log($course->id, "referentiel", "add", "mise a jour item ".$return);
					}
                }
            }

			// NOUVELLE COMPETENCE
			if (!empty($form['tnewcompetence_id'])){
                foreach ($form['tnewcompetence_id'] as $index_competence){
                    // echo "<br />ID :: ".$index_competence."\n";
                    $form2= new Object();
                    if (isset($form['new_code_competence_'.$index_competence])){
                        $form2->new_code_competence=$form['new_code_competence_'.$index_competence];
                    }
                    else{
                      	$form2->new_code_competence='';
										}
                    if (isset($form['new_description_competence_'.$index_competence])){
                        $form2->new_description_competence=$form['new_description_competence_'.$index_competence];
                    }
                    else{
                        $form2->new_description_competence='';
                    }
        		    $form2->new_type_competence=$form['new_type_competence_'.$index_competence];
        		    $form2->new_minima_competence=$form['new_minima_competence_'.$index_competence];
        		    $form2->new_seuil_competence=$form['new_seuil_competence_'.$index_competence];
        		    $form2->new_num_competence=$form['new_num_competence_'.$index_competence];
                    $form2->new_nb_item_competences=$form['new_nb_item_competences_'.$index_competence];
                    $form2->occurrence=$referentiel_referentiel->id;
										$form2->new_ref_domaine=$form['new_ref_domaine_'.$index_competence];
                    $form2->num_domaine=$form['new_num_domaine_'.$index_competence];

                    // echo "<br />DEBUG :: edit.php :: 407<br />\n";
                    // print_object($form2);
                    // echo "<br />\n";
                    //exit;
                    $return = referentiel_add_competence($form2);
                    if (!$return) {
                        print_error("Could not create Competency", "view.php?id=".$cm->id);
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    			$params = array(
        	       			'contextid' => $context->id,
            	   			'objectid' => $return,
                            'other' => array('occurrenceid'=>$referentiel_referentiel->id),
            			);
            			$event = \mod_referentiel\event\competence_created::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
	                    add_to_log($course->id, "referentiel", "add", "mise a jour Competence ".$return);
					}
	            }
            }

            //NOUVEAU domaine
			if (!empty($form['tnewdomaine_id'])){
                foreach ($form['tnewdomaine_id'] as $index_domaine){
                    // echo "<br />ID :: ".$index_domaine."\n";
                    $form2= new Object();
                    if (isset($form['new_code_domaine_'.$index_domaine])){
                        $form2->new_code_domaine=$form['new_code_domaine_'.$index_domaine];
                    }
                    else{
                      	$form2->new_code_domaine='';
										}
                    if (isset($form['new_description_domaine_'.$index_domaine])){
                        $form2->new_description_domaine=$form['new_description_domaine_'.$index_domaine];
                    }
                    else{
                        $form2->new_description_domaine='';
                    }
        		    $form2->new_type_domaine=$form['new_type_domaine_'.$index_domaine];
        		    $form2->new_minima_domaine=$form['new_minima_domaine_'.$index_domaine];
        		    $form2->new_seuil_domaine=$form['new_seuil_domaine_'.$index_domaine];
        		    $form2->new_num_domaine=$form['new_num_domaine_'.$index_domaine];
                    $form2->new_nb_competences=$form['new_nb_competence_domaine_'.$index_domaine];
                    $form2->occurrence=$referentiel_referentiel->id;
                    $form2->num_domaine=$form['new_num_domaine_'.$index_domaine];

                    //echo "<br />DEBUG :: edit.php :: 443<br />\n";
                    //print_object($form2);
                    //echo "<br />\n";
                    //exit;
                    $return = referentiel_add_domaine($form2);
                    if (!$return) {
                        print_error("Could not create Domain", "view.php?id=".$cm->id);
                    }
	        		if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    			$params = array(
        	       			'contextid' => $context->id,
            	   			'objectid' => $return,
                            'other' => array('occurrenceid'=>$referentiel_referentiel->id),
            			);
            			$event = \mod_referentiel\event\domaine_created::create($params);
	            		$event->trigger();
    	    		} else { // Before Moodle 2.7
                        add_to_log($course->id, "referentiel", "add", "mise a jour domaine ".$return);
					}
                }
            }


            unset($form);
            //redirect("$CFG->wwwroot/mod/referentiel/view.php?id=$cm->id&amp;non_redirection=1");
						redirect("$CFG->wwwroot/mod/referentiel/edit.php?id=$cm->id&amp;pass=1&amp;sesskey=".sesskey());

            exit;
        }

		// variable d'action Enregistrer
		if (!empty($form->action)){
			if (isset($form->mode) && ($form->mode=="add")){
				// creer le referentiel
				if ($form->action=="modifierreferentiel"){
					// enregistre les modifications
					$return=referentiel_add_referentiel_domaines($form);
					$msg=get_string("referentiel", "referentiel")." ".$form->referentiel_id;
					$action="update";
				}
			}
			else if (isset($form->mode) && ($form->mode=="update")){
				if ($form->action=="modifierreferentiel"){
					// gestion du mot de passe
					if (isset($form->suppression_pass_referentiel) && ($form->suppression_pass_referentiel==1)){
						$form->old_pass_referentiel = '';
					}
					else if (isset($form->pass_referentiel) && ($form->pass_referentiel!='') 
						&& 
						(
							isset($form->old_pass_referentiel) && ($form->old_pass_referentiel!='') && ($form->old_pass_referentiel != md5($form->pass_referentiel))
							|| 
							isset($form->old_pass_referentiel) && ($form->old_pass_referentiel=='') 
						)
					){
						$form->old_pass_referentiel = md5($form->pass_referentiel);
					}
					
					$return=referentiel_update_referentiel_referentiel($form);
					
					$msg=get_string("referentiel", "referentiel")." ".$form->referentiel_id;
					$action="update";
					
				}
				else if ($form->action=="modifierdomaine"){
					// enregistre les modifications
					if (isset($form->domaine_id) && ($form->domaine_id>0)){
						$return=referentiel_update_domaine($form);
						$msg=get_string("referentiel", "referentiel")." ".$form->referentiel_id." ".get_string("domaine", "referentiel")." ".$form->domaine_id;
						$action="update";
					}
				}
				else if ($form->action=="modifiercompetence"){
					// enregistre les modifications
					if (isset($form->competence_id) && ($form->competence_id>0)){
						$return=referentiel_update_competence($form);
						$msg=get_string("referentiel", "referentiel")." ".$form->referentiel_id." ".get_string("competence", "referentiel")." ".$form->competence_id;
						$action="update";
					}
				}
				else if ($form->action=="modifieritem"){
					// enregistre le nouveau domaine
					$return=referentiel_update_item($form);
					$msg=get_string("referentiel", "referentiel")." ".$form->referentiel_id." ".get_string("item", "referentiel")." ".$form->item_id;
					$action="update";
				}
				else if ($form->action=="newdomaine"){
					// enregistre le nouveau domaine
					$return=referentiel_add_domaine($form);
					$msg=get_string("referentiel", "referentiel")." ".$form->referentiel_id." ".get_string("domaine", "referentiel")." ".$return;
					$action="add";
				}
				else if ($form->action=="newcompetence"){
					// enregistre le nouvel item
					$return=referentiel_add_competence($form);
					$msg=get_string("referentiel", "referentiel")." ".$form->referentiel_id." ".get_string("competence", "referentiel")." ".$return;
					$action="add";
				}
				else if ($form->action=="newitem"){
					// enregistre les modifications
					$return=referentiel_add_item($form);
					$msg=get_string("referentiel", "referentiel")." ".$form->referentiel_id." ".get_string("item", "referentiel")." ".$return;
					$action="add";
				}
				
				if (!$return) {
					print_error("Could not update instance $form->referentiel_id of referentiel", "view.php?id=$cm->id");
        		}
	        	if (is_string($return)) {
					print_error($return, $returnlink);
	    		}
				
		        if (isset($form->redirect)) {
    		    	$SESSION->returnpage = $form->redirecturl;
        		}
                else {
            		$SESSION->returnpage = $returnlink;
	        	}
			}
			
			if (isset($action)){
	        	if ($CFG->version > 2014051200) { // Moodle 2.7+
     					$params = array(
       					'contextid' => $context->id,
   	   					'objectid' => $referentiel->id,
						'other' => array('occurrenceid' => $referentiel_referentiel->id, 'msg' =>$msg),
	            	);
					if ($action=="update"){
						$event = \mod_referentiel\event\occurrence_updated::create($params);
					}
					else{
                        $event = \mod_referentiel\event\occurrence_created::create($params);
					}
                   	$event->trigger();
    	    	} else { // Before Moodle 2.7
					add_to_log($course->id, "referentiel", $action, "edit.php?d=".$referentiel->id, $msg, $cm->module);
				}
			}
		}
	}
	
	// afficher les formulaires
    //$modform = "edit.html";    // old
    $modform = "edit_inc.php";
    if (file_exists($modform)) {
		$defaultformat = FORMAT_PLAIN;
        $editorfields = '';
	}
	else {
        notice("ERREUR : No file found at : $modform)", "edit.php?d=$referentiel->id");
    }

    /// Mark as viewed  ???????????
    // $completion=new completion_info($course);
    // $completion->set_module_viewed($cm);

    // AFFICHAGE DE LA PAGE
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
	$strmessage = get_string('modifier_referentiel','referentiel');
	$strpagename=get_string('modifier_referentiel','referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

	// initialisation parametres affichage
	if (!isset($mode)){
		$mode='editreferentiel'; // un seul mode possible
	}
	$currenttab = 'editreferentiel';
    if ($referentiel->id) {
       	$editentry = true;  //used in tabs
    }
    // Administrateur ou Auteur ?
    $isadmin=referentiel_is_admin($USER->id,$course->id);
    $isreferentielauteur=referentiel_is_author($USER->id, $referentiel_referentiel);


    // affichage de la page
    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    //if ($CFG->version < 2011120100) $PAGE->requires->js('/lib/overlib/overlib.js');  else
    $PAGE->requires->js($OverlibJs);
    $PAGE->requires->js('/mod/referentiel/functions.js');

    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);


    echo $OUTPUT->header();

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets 
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();

    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('modifreferentielh','referentiel').'</h2></div>'."\n";

	if ($mode=='listreferentiel'){
		referentiel_affiche_referentiel($cm, $referentiel->id, $referentiel->ref_referentiel);
	}
	else {
        echo $OUTPUT->box_start('generalbox  boxaligncenter');
        // formulaires
		
        // verifer si le mot de passe est fourni
        if (!$pass  // si cest un admin il outrepasse car pass==1
		&&
		(
            (
                isset($referentiel_referentiel->pass_referentiel)
		        &&
		        ($referentiel_referentiel->pass_referentiel!='')
            )
            || $isreferentielauteur
            || $isadmin
        )
        ){
		// demander le mot de passe
            $appli_appelante="edit.php";
			include_once("pass_inc.php");
		}
		else{
			include_once($modform);
	    }
        echo $OUTPUT->box_end();
	}
	
    echo $OUTPUT->footer();
    die();
	
?>

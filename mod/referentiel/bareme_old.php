<?php  // $Id: bareme.php,v 1.0 2013/05/13/ 00:00:00 jfruitet Exp $
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
* association d'un barème
*
* @version $Id: bareme.php,v 1.0 2013/05/13/ 00:00:00 jfruitet Exp $
* @author Martin Dougiamas, Howard Miller, and many others.
*         {@link http://moodle.org}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package referentiel
*/


    require_once('../../config.php');
    require_once('locallib.php');
    require_once $CFG->dirroot.'/grade/lib.php';
    require_once $CFG->libdir.'/gradelib.php';
    require_once('lib_bareme.php');

    $id    = optional_param('id', 0, PARAM_INT);    // course module id	
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel instance id
	$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
    $checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni
	$action = optional_param('action','', PARAM_ALPHA);
    $mode = optional_param('mode','all', PARAM_ALPHANUMEXT);
    $scaleid    = optional_param('scaleid', 0, PARAM_INT);    // scale id
    $baremeid    = optional_param('baremeid', 0, PARAM_INT);    // scale id
    
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/bareme.php');

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
        // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : bareme.php'));
	}

    if ($mode !== 'all') {
        $url->param('mode', $mode);
    }
    $returnlink_ref = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));
    $returnlink_course = new moodle_url('/course/view.php', array('id'=>$course->id));
    $returnlink_add = new moodle_url('/mod/referentiel/add.php', array('d'=>$referentiel->id, 'sesskey'=>sesskey()));

    require_login($course->id, false, $cm);
    if (!isloggedin() || isguestuser()) {
        redirect($returnlink_course);
    }

    $context = context_course::instance($course->id);

    if (!empty($referentiel->id)) {    // So do you have access?
        if ( // !has_capability('mod/referentiel:writereferentiel', $context)or 
            !confirm_sesskey() ) {
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
        redirect($returnlink_add);
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

	// DEBUG
	//echo "<br />DEBUG : edit.php :: Ligne 122<br />\n";
	//print_r($form);

		// add, delete or update form submitted	
		
		// le mot de passe est-il actif ?
		// cette fonction est due au parametrage
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
                    redirect($returnlink_ref);
                    exit;
                }
            }
            else{    // mot de passe vide mais c'est un admin qui est connect�
                if (!empty($form->force_pass)){
                    $pass=1; // on passe... le mot de passe !
                }
            }
		}

		// variable d'action
		if (!empty($form->cancel)){
			if ($form->cancel == get_string("quit", "referentiel")){
				// Abandonner
    	    	if (isset($SESSION->returnpage) && !empty($SESSION->returnpage)) {
	            	$return = $SESSION->returnpage;
    		        unset($SESSION->returnpage);
   	        		redirect($return);
       			} 
				else {
                    redirect($returnlink_ref);
   	    		}
       			exit;
			}
		}
		
		// variable d'action
		if (!empty($form->action)){
				// MaJ
				//print_object($form);
				//exit;
			if ((($form->action == "modifbareme") || ($form->action == "editbareme")) && !empty($form->scaleid)){
        		referentiel_creation_modification_bareme($form);
    	    	if (isset($SESSION->returnpage) && !empty($SESSION->returnpage)) {
	            	$return = $SESSION->returnpage;
    		        unset($SESSION->returnpage);
   	        		redirect($return);
       			} 
				else {
                    redirect($returnlink_ref);
   	    		}
       			exit;        		
			}
			elseif (($form->action == "reeditbareme")  && !empty($form->baremeid)){
        		referentiel_modification_bareme($form);
    	    	if (isset($SESSION->returnpage) && !empty($SESSION->returnpage)) {
	            	$return = $SESSION->returnpage;
    		        unset($SESSION->returnpage);
   	        		redirect($return);
       			} 
				else {
                    redirect($returnlink_ref);
   	    		}
       			exit;        		
			}
		}
	}

	// afficher les formulaires

    // unset($SESSION->modform); // Clear any old ones that may be hanging around.

// AFFICHAGE DE LA PAGE Moodle 2
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
    $strmessage = get_string('newbaremes','referentiel');
    $strpagename=get_string('newbaremes','referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');


	/// Parametre des onglets
	if (!isset($mode)){
		$mode='bareme'; // un seul mode possible
	}
	$currenttab = 'bareme';
    if ($referentiel->id) {
       	$editentry = true;  //used in tabs
    }
    
    // affichage de la page
    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    // $PAGE->requires->js($OverlibJs);
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

    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('baremeh','referentiel').'</h2></div>'."\n";

    echo $OUTPUT->box_start('generalbox  boxaligncenter');

		// verifer si le mot de passe est fourni
		if (!$pass 
			&& 
			$referentiel
			&& 
			$referentiel_referentiel
			&& 
			isset($referentiel_referentiel->pass_referentiel)
			&&
			($referentiel_referentiel->pass_referentiel!='') 
			&& 
			isset($referentiel_referentiel->mail_auteur_referentiel)
			&&
			(referentiel_get_user_mail($USER->id)!=$referentiel_referentiel->mail_auteur_referentiel)){
			// demander le mot de passe
			$appli_appelante="bareme.php";
			include_once("pass_inc.php");
		}
		else{
			if (!empty($baremeid) && ($mode=='reeditbareme') &&  confirm_sesskey()){
                // DEBUG
                // echo "<br>DEBUG :: $scaleid selected\n";
				if ($rec_bareme=$DB->get_record('referentiel_scale', array('id'=>$baremeid))){
        				// print_object($rec_bareme);
            			referentiel_affiche_bareme($rec_bareme);
            			referentiel_modifier_bareme($mode, $course->id, $cm->id, $rec_bareme, $referentiel_referentiel->id);
					    echo $OUTPUT->box_end();
                    	echo $OUTPUT->footer();
    					die();                             							        	
    			}
    		}				 

            if (!empty($scaleid)){
				if (($mode=='editbareme') &&  confirm_sesskey()){
                	// DEBUG
                	// echo "<br>DEBUG :: $scaleid selected\n";
					if ($scale=$DB->get_record('scale', array('id'=>$scaleid))){
        				// print_object($scale);
        				if ($rec_bareme=referentiel_scale_2_bareme($scale)){
        					// print_object($rec_bareme);
            				referentiel_affiche_bareme($rec_bareme);
            				referentiel_modifier_bareme($mode, $course->id, $cm->id, $rec_bareme, $referentiel_referentiel->id);
					        echo $OUTPUT->box_end();
                    		echo $OUTPUT->footer();
    						die();                             				
			        	}
    				}
    			}
				else if (($mode=='echangebareme') && confirm_sesskey()){    			
    				if ($confirm = optional_param('confirm',0,PARAM_INT)) {
  						referentiel_echange_bareme_occurrence($scaleid, $referentiel_referentiel->id);
  					}
   		    	}
    			else if ($mode=='selectbareme'){
		            echo $OUTPUT->confirm(get_string('confirmexchange','referentiel').' '.$OUTPUT->help_icon('baremeechangeh','referentiel'),
	'bareme.php?id='.$cm->id.'&amp;scaleid='.$scaleid.'&amp;mode=echangebareme&amp;confirm=1&amp;sesskey='.sesskey(),
	'bareme.php?id='.$cm->id.'&amp;mode=bareme&amp;confirm=0&amp;sesskey='.sesskey());
					echo $OUTPUT->box_end();
                    echo $OUTPUT->footer();
    				die();                             							          
            	}
				else if (($mode=='confirmdeletebareme') && confirm_sesskey()){    			
    				if ($confirm = optional_param('confirm',0,PARAM_INT)) {
  						referentiel_delete_bareme_occurrence($scaleid, $referentiel_referentiel->id);
  					}
   		    	}
    			else if ($mode=='deletebareme'){
		            echo $OUTPUT->confirm(get_string('confirmdelete','referentiel').' '.$OUTPUT->help_icon('deletescaleh','referentiel'),
    'bareme.php?id='.$cm->id.'&amp;scaleid='.$scaleid.'&amp;mode=confirmdeletebareme&amp;confirm=1&amp;sesskey='.sesskey(),
	'bareme.php?id='.$cm->id.'&amp;mode=bareme&amp;confirm=0&amp;sesskey='.sesskey());
					echo $OUTPUT->box_end();
                    echo $OUTPUT->footer();
    				die();                             							          
            	}

            }
            $roles=referentiel_roles_in_instance($referentiel->id);
            $idbareme=referentiel_affiche_bareme_occurrence($referentiel->ref_referentiel, $course, $cm, $context, $roles);
	        echo "<br />\n";
            referentiel_liste_autres_baremes($course, $cm, $context, $roles, $idbareme, true);
            referentiel_print_scales($course, $cm, $context, $roles);            
	    }
        echo $OUTPUT->box_end();

    echo $OUTPUT->footer();
    die();
	
?>

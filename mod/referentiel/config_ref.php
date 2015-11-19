<?php  // $Id: config_ref.php,v 1.0 2010/10/19 00:00:00 jfruitet Exp $
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
 * Settings page of a referentiel
 * 
 * @author JF
 * @version $Id: config_ref.php,v 1.0 2010/10/19 00:00:00 jfruitet Exp $
 * @package referentiel
 **/



  require_once('../../config.php');
  require_once('locallib.php');

  $id    = optional_param('id', 0, PARAM_INT);    // course module id
  $d = optional_param('d', 0, PARAM_INT); // Referentiel ID
  $pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
  $checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni

  $mode  = optional_param('mode', '', PARAM_ALPHANUMEXT);    // Force the browse mode  ('single')
  $sesskey     		= optional_param('sesskey', '', PARAM_ALPHANUM);
  $coursemodule     = optional_param('coursemodule', 0, PARAM_INT);
  $section 			= optional_param('section', 0, PARAM_INT);
  $module 			= optional_param('module', 0, PARAM_INT);
  $modulename     	= optional_param('modulename', '', PARAM_ALPHA);
  $instance 		= optional_param('instance', 0, PARAM_INT);

  // MODIF JF 22/01/2010
  $non_redirection = optional_param('non_redirection', 0, PARAM_INT);    // par defaut on redirige vers activite
	
  $select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement


    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/config_ref.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : config_ref.php'));
	}

    $mode='configref';

    $url->param('mode', $mode);

    require_login($course->id, false, $cm);
    if (!isloggedin() || isguestuser()) {
        redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
    }

    $strreferentiels = get_string('modulenameplural', 'referentiel');
    $strreferentiel  = get_string('modulename', 'referentiel');
	
	// lien vers le referentiel lui-meme
	if (!empty($referentiel->ref_referentiel)){
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
    		print_error('Referentiel id is incorrect');
    	}
    }
	else{
		// rediriger vers la creation du referentiel
		redirect(new moodle_url('/mod/referentiel/add.php', array('d'=>$referentiel->id, 'sesskey'=>sesskey())));
	}

    // CONTEXTE
    $context = context_module::instance($cm->id);
    require_capability('mod/referentiel:writereferentiel', $context);

    /// Check further parameters that set browsing preferences
    if (!isset($SESSION->dataprefs)) {
        $SESSION->dataprefs = array();
    }
    if (!isset($SESSION->dataprefs[$referentiel->id])) {
        $SESSION->dataprefs[$referentiel->id] = array();
        $SESSION->dataprefs[$referentiel->id]['local'] = 0;
    }

    // RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else {
        $form = (object)$_POST;
    }

	$msg="";

	if (!empty($course) && !empty($cm) && !empty($referentiel_referentiel) && isset($form)) {

		// le mot de passe est-il actif ?
		if (!$pass && ($checkpass=='checkpass')){
            if (!empty($form->pass_referentiel) && $referentiel_referentiel){
                if (!empty($form->force_pass)){  // forcer la sauvegarde sans verification
                    $pass=referentiel_set_pass($referentiel_referentiel->id, $form->pass_referentiel);
                }
                else{ // tester le mot de passe
                    $pass=referentiel_check_pass($referentiel_referentiel, $form->pass_referentiel);
                }
                if (!$pass){
                    // Abandonner
                    print_error("error_pass","referentiel",
                        new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1')),
                        $referentiel_referentiel->mail_auteur_referentiel );
                    exit;
                }
            }
            else{    // mot de passe vide mais c'est un admin qui est connecté
                if (!empty($form->force_pass)){
                    $pass=1; // on passe... le mot de passe !
                }
            }
		}

        // Traitement des POST
        // variable d'action
		if (!empty($form->cancel)){
			if ($form->cancel == get_string("quit", "referentiel")){
                redirect(new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1')));
       			exit;
			}
		}
		// mise à jour de la configuration
		// variable d'action Enregistrer
		else if (!empty($form->action) && ($form->action=='modifierconfig') && !empty($form->mode) && ($form->mode=='configref')){
            // sauvegarder
            $config=referentiel_initialise_configuration($form,'config');
            referentiel_global_set_vecteur_config($config, $referentiel_referentiel->id);
		    $config_impression=referentiel_initialise_configuration($form,'config_impression');
            referentiel_global_set_vecteur_config_imp($config_impression, $referentiel_referentiel->id);
        	if ($CFG->version > 2014051200) { // Moodle 2.7+
            	$params = array(
            	   	'contextid' => $context->id,
               		'objectid' => $referentiel_referentiel->id,
            	);
            	$event = \mod_referentiel\event\config_updated::create($params);
            	$event->trigger();
        	} else { // Before Moodle 2.7
                add_to_log($course->id, 'referentiel', 'config', "config_ref.php?id=$cm->id", $course->id);
			}
	        if (isset($form->redirect)) {
                $SESSION->returnpage = $form->redirecturl;
        	}
			else {
                $SESSION->returnpage = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));

	        }
	        redirect($SESSION->returnpage);
		}
	}
	

    /// Mark as viewed  ??????????? A COMMENTER
    $completion=new completion_info($course);
    $completion->set_module_viewed($cm);

// AFFICHAGE DE LA PAGE Moodle 2
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
	$strmessage = get_string('configref','referentiel');
    $strpagename=get_string('configref','referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    /// Print the main part of the page

    /// Print the tabs
	if ($mode=='configref') {
        $currenttab = 'configref';
    }
    else {
		$currenttab = 'listreferentiel';
    }
    // Administrateur ou Auteur ?
    $isadmin=referentiel_is_admin($USER->id,$course->id);
    $isreferentielauteur=referentiel_is_author($USER->id, $referentiel_referentiel);

    // affichage de la page
    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    //if ($CFG->version < 2011120100) $PAGE->requires->js('/lib/overlib/overlib.js');  else
    $PAGE->requires->js($OverlibJs);
    // $PAGE->requires->js('/mod/referentiel/functions.js');

    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);


    echo $OUTPUT->header();

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets 
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL);
    $tab_onglets->display();

    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('configreferentielh','referentiel').'</h2></div>'."\n";

    // formulaires
    $modform = "config_ref.html";
	if (file_exists($modform)) {
        $defaultformat = FORMAT_PLAIN;
        $editorfields = '';
    }
	else {
        notice("ERREUR : No file found at : $modform)", "config_ref.php?d=$referentiel->id");
    }
	// verifer si le mot de passe est fourni
	if (!$pass
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
		$appli_appelante="config_ref.php";
		include_once("pass_inc.php");
	}
	else{
	   include_once($modform);
	}


/// Finish the page
    echo $OUTPUT->footer();
    die();
?>

<?php  // $Id: edit_protocole.php,v 1.0 2012/02/3 00:00:00 jfruitet Exp $
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
 * Settings page of a certification protocol
 *
 * @author JF
 * @version $Id: edit_protocole.php,v 1.0 2012/02/3 00:00:00 jfruitet Exp $
 * @package referentiel
 **/



    require(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');
    require_once("print_lib_protocole.php");
  
    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel instance id
	$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
    $checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni

    $mode = optional_param('mode','protocole', PARAM_ALPHANUMEXT);
    $action  = optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de creation de referentiel

    $select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    $sesskey = optional_param('sesskey', '', PARAM_ALPHA);
    $coursemodule     = optional_param('coursemodule', 0, PARAM_INT);
    $section 			= optional_param('section', 0, PARAM_INT);
    $module 			= optional_param('module', 0, PARAM_INT);
    $modulename     	= optional_param('modulename', '', PARAM_ALPHA);
    $instance 		= optional_param('instance', 0, PARAM_INT);

    $non_redirection = optional_param('non_redirection', 0, PARAM_INT);    // par defaut on redirige vers activite

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

    require_login($course->id, true, $cm);

    if (!isloggedin() || isguestuser()) {
        redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
    }

    $context = context_module::instance($cm->id);

    if (!empty($referentiel->id)) {    // So do you have access?
        if (!has_capability('mod/referentiel:writereferentiel', $context)
            or !confirm_sesskey() )
        {
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

	// Capacités
    require_capability('mod/referentiel:write', $context);

    // RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else {
        $form = (object)$_POST;
    }

	$msg="";

	if (!empty($course) && !empty($cm) && !empty($referentiel_referentiel)) {
        // le mot de passe est-il actif ?
		if (!$pass){
            if ($checkpass=='checkpass'){
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
		}

		// variable d'action
		if (!empty($form->cancel)){
			if ($form->cancel == get_string("quit", "referentiel")){
				// Abandonner
    		    redirect(new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1')));
       			exit;
			}
		}
		// mise à jour de la configuration
		// variable d'action Enregistrer
		else if (!empty($form->action) && ($form->action=='modifierprotocole')
            && !empty($form->mode) && ($form->mode=='protocole')){
            // sauvegarder
            $config=referentiel_set_protocole($referentiel_referentiel->id, $form);
	        if ($CFG->version > 2014051200) { // Moodle 2.7+
    	    	$params = array(
        			'contextid' => $context->id,
            		'objectid' => $referentiel_referentiel->id,
           			);
            	$event = \mod_referentiel\event\protocole_updated::create($params);
	            $event->trigger();
    	    } else { // Before Moodle 2.7
                add_to_log($course->id, 'referentiel', "config", "edit_protocole?id=$cm->id", "$course->id");
			}

            /*
	        if (isset($form->redirecturl)) {
                $SESSION->returnpage = $form->redirecturl;
        	}
			else {
                //$SESSION->returnpage = "$CFG->wwwroot/mod/referentiel/edit_protocole.php?id=$cm->id&amp;mode=$mode&amp;select_acc=$select_acc&amp;sesskey=".sesskey();
                $SESSION->returnpage = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));
	        }
	        redirect($SESSION->returnpage);
	        */
		}
	}

// AFFICHAGE DE LA PAGE Moodle
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');

    $strpagename=get_string('protocole','referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');


	// initialisation parametres affichage
    /// Print the tabs
	$mode='protocole';
	$currenttab = 'protocole';
	$url->param('mode', $mode);
	
    if ($referentiel->id) {
       	$editentry = true;  //used in tabs
    }

    // Administrateur ou Auteur ?
    $isadmin=referentiel_is_admin($USER->id,$course->id);
    $isreferentielauteur=referentiel_is_author($USER->id, $referentiel_referentiel);

    // affichage de la page
    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
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

    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strpagename.' '.$OUTPUT->help_icon('protocolereferentielh','referentiel').'</h2></div>'."\n";

    echo $OUTPUT->box_start('generalbox  boxaligncenter');
    // formulaires
    // verifer si le mot de passe est fourni
	if (!$pass && (
            (
                isset($referentiel_referentiel->pass_referentiel)
		        &&
		        ($referentiel_referentiel->pass_referentiel!='')
            )
            || $isreferentielauteur
            || $isadmin
            ))
    {
		  // demander le mot de passe
        $appli_appelante="edit_protocole.php";
		include_once("pass_inc.php");
	}
	else{
        // formulaires
        referentiel_edit_protocole($mode, $referentiel, $select_acc, $pass);
	}
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die();
?>

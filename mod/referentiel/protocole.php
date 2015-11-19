<?php  // $Id: protocole.php,v 1.0 2012/02/3 00:00:00 jfruitet Exp $
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
 * @version $Id: protocole.php,v 1.0 2012/02/3 00:00:00 jfruitet Exp $
 * @package referentiel
 **/



  require(dirname(__FILE__) . '/../../config.php');
  require_once('locallib.php');
  require_once("print_lib_protocole.php");
  
    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel instance id
	$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
    $checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni

    $mode = optional_param('mode','all', PARAM_ALPHANUMEXT);
    $select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
    $action  = optional_param('action','', PARAM_ALPHA); // pour distinguer differentes formes de creation de referentiel
    $sesskey     		= optional_param('sesskey', '', PARAM_ALPHA);
    $coursemodule     = optional_param('coursemodule', 0, PARAM_INT);
    $section 			= optional_param('section', 0, PARAM_INT);
    $module 			= optional_param('module', 0, PARAM_INT);
    $modulename     	= optional_param('modulename', '', PARAM_ALPHA);
    $instance 		= optional_param('instance', 0, PARAM_INT);

    // MODIF JF 22/01/2010
    $non_redirection = optional_param('non_redirection', 0, PARAM_INT);    // par defaut on redirige vers activite


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
        // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : edit.php'));
	}

	$returnlink="$CFG->wwwroot/course/view.php?id=$course->id";
    require_login($course->id, true, $cm);
/*
    if (!isloggedin() || isguestuser()) {   // nouveaute Moodle 2
        redirect($returnlink);
    }
*/
    $context = context_module::instance($cm->id);


    if (!empty($referentiel->id)) {    // So do you have access?
        if (!confirm_sesskey() ) {
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
		$returnlink_add="$CFG->wwwroot/mod/referentiel/add.php?d=$referentiel->id";
        redirect($returnlink_add);
	}



	// A MODIFIER / ADAPTER
    // require_capability('mod/referentiel:write', $context);


    /// Mark as viewed  ??????????? A COMMENTER
    //$completion=new completion_info($course);
    // $completion->set_module_viewed($cm);

// AFFICHAGE DE LA PAGE Moodle 2
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
    echo '<br />'."\n";
    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title=""  alt="" /> '.$strpagename.' '.$OUTPUT->help_icon('protocolereferentielh','referentiel').'</h2></div>'."\n";

    echo $OUTPUT->box_start('generalbox  boxaligncenter');
    referentiel_print_protocole($mode, $referentiel, $select_acc);
    echo $OUTPUT->box_end();


    echo $OUTPUT->footer();
    die();
?>

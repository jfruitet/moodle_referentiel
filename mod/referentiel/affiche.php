<?php  // $Id: view.php,v 1.0 2008/02/28 00:00:00 mark-nielsen jfruitet Exp $
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
 * This page prints a particular instance of referentiel in a special frame
 * 
 * @author JF
 * @version $Id: affiche.php,v 1.0 2008/02/28 00:00:00 mark-nielsen jfruitet Exp $
 * @package referentiel
 **/


    require(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');
    require_once("print_lib_referentiel.php");	// AFFICHAGES 
	
    $d = required_param('d', PARAM_INT); // Referentiel ID

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/affiche.php');

	if ($d) {     // referenteil_referentiel_id
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


    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

        require_login($course->id, false, $cm);   // pas d'autologin guest

        if (!isloggedin() or isguestuser()) {
            redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
        }
		$strreferentiels = get_string('modulenameplural', 'referentiel');
		$strreferentiel  = get_string('modulename', 'referentiel');
		$strpagename=get_string('listreferentiel','referentiel');

        $icon = $OUTPUT->pix_url('icon','referentiel');

        // AFFICHAGE DE LA PAGE Moodle 2
        $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
        $icon = $OUTPUT->pix_url('icon','referentiel');

        $PAGE->set_url($url);
        // $PAGE->set_context($context);
        $PAGE->requires->css('/mod/referentiel/referentiel.css');
        //if ($CFG->version < 2011120100) $PAGE->requires->js('/lib/overlib/overlib.js');  else
        $PAGE->requires->js($OverlibJs);
        $PAGE->set_title($pagetitle);
        $PAGE->set_heading($course->fullname);

        echo $OUTPUT->header();
        echo '<div align="center"><h2><img src="'.$icon.'" border="0" title=""  alt="" /> '.get_string('referentiel','referentiel').' '.$OUTPUT->help_icon('referentielh','referentiel').'</h2></div>'."\n";

        referentiel_affiche_referentiel_instance($referentiel->id);

        echo $OUTPUT->footer();
        die();
	}

?>

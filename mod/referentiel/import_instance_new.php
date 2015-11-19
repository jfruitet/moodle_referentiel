<?php  // $Id: import_instance.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
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
* Importation d'un referentiel
* D'apres competency/import.php 
*
* @package referentiel
*/
	
    require(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');
    require_once('import_export_lib.php');	// IMPORT / EXPORT	
    require_once($CFG->libdir . '/uploadlib.php');

    $id    = optional_param('id', 0, PARAM_INT);    // course module id	
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel instance id
    $action  = optional_param('action','', PARAM_ALPHA); // pour distinguer differentes formes de vcreatin de referentiel
    $mode    = optional_param('mode', 'add', PARAM_ALPHA);    // Force the browse mode  ('single')
    $format  = optional_param('format','', PARAM_FILE );
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/refrentiel/import_instance.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : import_instance.php'));
	}

	if (!isset($mode)){
		$mode='add'; // un seul mode possible
	}

    $url->param('mode', $mode);

    $PAGE->set_url($url);

    require_login($course, true, $cm);

	if (!isset($action) || empty($action)){
		$action='importreferentiel'; // une seule action possible
	}


    // ensure the files area exists for this course
    // OBSOLETE
    // make_upload_directory( "$course->id/$CFG->moddata/referentiel" );

    $PAGE->requires->js('/mod/referentiel/functions.js');

    $referentielinstance = new referentiel($cm->id, $referentiel, $cm, $course);

    /// Mark as viewed
    $completion=new completion_info($course);
    $completion->set_module_viewed($cm);

    $referentielinstance->load_referentiel($mode, $format, $action);   // Actually display the referentiel!

?>

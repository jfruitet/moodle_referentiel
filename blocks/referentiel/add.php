<?php
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
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set

/**
 * Interface.
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/../../config.php');
require_once('occurrence_class.php');
require_once('occurrence_form.php');
require_once('pass_form.php');
require_once('lib.php');

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

// Next look for optional variables.
$occurrenceid = optional_param('occurrenceid', 0, PARAM_INT);
$mode  = optional_param('mode', 'add', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')
$action = optional_param('action','', PARAM_ALPHANUMEXT);

// http://localhost/moodle25/blocks/referentiel/edit.php?blockid=65&courseid=2&occurrenceid=2&deleteid=217&action=modifieritem&delete=Supprimer&pass=&sesskey=pLZgTzHQUJ
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_referentiel', $courseid);
}

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

require_login($course);

$currenttab = 'edit';

// Traitements
	// Occurrence
	$params=array("blockid"=>$blockid, "courseid"=>$courseid);
	$occurrence_object = new occurrence($params);
	//print_object($occurrence_object->referentiel);
	//exit;
	$role=$occurrence_object->roles();
    $isadmin=$role->is_admin;

	// affichage

	$pagetitle=get_string('addoccurrence', 'block_referentiel');
	$PAGE->set_url('/blocks/referentiel/add.php', array('blockid'=>$blockid, 'courseid' => $courseid, 'occurrenceid' => $occurrenceid ));
	$PAGE->requires->css('/mod/referentiel/referentiel.css');
	$PAGE->requires->js('/mod/referentiel/functions.js');
	$PAGE->set_pagelayout('standard');
	$PAGE->set_heading($course->fullname);
	$PAGE->set_title($pagetitle);
	$PAGE->navbar->add($occurrence_object->referentiel->code_referentiel);
	//$settingsnode = $PAGE->settingsnav->add(get_string('displayoccurrence', 'block_referentiel'));
	//$site = get_site();

	$settingsnode = $PAGE->settingsnav->add(get_string('editoccurrence', 'block_referentiel'));
	$editurl = new moodle_url('/blocks/referentiel/add.php', array('blockid'=>$blockid, 'courseid'=>$courseid));
	$editnode = $settingsnode->add(get_string('editoccurrence', 'block_referentiel'), $editurl);
	$editnode->make_active();

	// imput occurrence / domains / competencies / items
	if ($role->can_edit){
		$options = array();
		if ($occurrence_form = new occurrence_form(null, array('blockid'=>$blockid, 'courseid'=>$courseid, 'userid'=>$USER->id, 'occurrence'=>$occurrence_object->referentiel, 'options'=>$options))){
			if($occurrence_form->is_cancelled()) {
    			// Cancelled forms redirect to the view main page.
    			redirect($courseurl);
            	die();
			} else if ($formdata=$occurrence_form->get_data()) {
    			// We need to add code to appropriately act on and store the submitted data
				//DEBUG
				// print_object($formdata);
	    		$occurrenceid=referentiel_set_occurrence($formdata);
				if ($occurrenceid){
                    $editurl = new moodle_url('/blocks/referentiel/edit.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>1));
                	redirect($editurl);
				}
				else{
                    redirect($courseurl);
				}
				die();
			}
			else {
            	echo $OUTPUT->header();
				// $occurrence_object->tabs($mode, $currenttab);
 				$occurrence_form->display();
				echo $OUTPUT->footer();
			}
		}
	}
	else{
    	redirect($courseurl);
        die();
	}

?>
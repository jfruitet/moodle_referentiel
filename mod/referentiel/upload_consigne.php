<?php  // $Id: upload.php,v 1.0 2008/05/03 00:00:00 jfruitet Exp $
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
    // require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
    require(dirname(__FILE__) . '/../../config.php');
    //require_once(dirname(__FILE__).'/class/upload_form.php');
    require_once('class/upload_form.php');
    require_once("$CFG->dirroot/repository/lib.php");
    require_once('locallib.php');
    require_once("print_lib_task.php");	// AFFICHAGES
	
    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentielbase id
	
    $taskid   = optional_param('taskid', 0, PARAM_INT);    //record task id
    $consigne_id   = optional_param('consigne_id', 0, PARAM_INT);    //record consigne id

    $action  	= optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements
    $mode       = optional_param('mode','', PARAM_ALPHANUMEXT);
    $add        = optional_param('add','', PARAM_ALPHA);
    $update     = optional_param('update', 0, PARAM_INT);
    $delete     = optional_param('delete', 0, PARAM_INT);
    $approve    = optional_param('approve', 0, PARAM_INT);
    $comment    = optional_param('comment', 0, PARAM_INT);
    $courseid     = optional_param('courseid', 0, PARAM_INT);
    $groupmode  = optional_param('groupmode', -1, PARAM_INT);
    $cancel     = optional_param('cancel', 0, PARAM_BOOL);
	$souscription    = optional_param('souscription', 0, PARAM_INT);
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
	
    $url = new moodle_url('/mod/referentiel/upload_consigne.php');

	if ($d) {     // referentiel_referentiel_id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('R�ferentiel id is incorrect');
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
		print_error(get_string('erreurscript','referentiel','Erreur01 : upload_consigne.php'), 'referentiel');
	}

    require_login($course->id, false, $cm);

    if (!isloggedin() or isguestuser()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
    }

    $contextcourse = context_course::instance($course->id);
    $context = context_module::instance($cm->id);

    
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


	if ($taskid) { // id task
        if (! $record = $DB->get_record('referentiel_task', array("id" => "$taskid"))) {
            print_error('task ID is incorrect');
        }
	}

    if ($taskid) {    // So do you have access?
        if (!(has_capability('mod/referentiel:writereferentiel', $context)
			or referentiel_task_isowner($taskid)) or !confirm_sesskey() ) {
            print_error(get_string('noaccess','referentiel'));
        }
    }
	if ($consigne_id) { // id task
        if (! $record_consigne = $DB->get_record('referentiel_consigne', array("id" => "$consigne_id"))) {
            print_error('consigne ID is incorrect');
        }
	}

	if ($cancel) {
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        }
        else {
        	 // souscription ?
            if ($souscription){
                redirect($CFG->wwwroot.'/mod/referentiel/souscription.php?d='.$referentiel->id.'&taskid='.$taskid.'&souscription='.$souscription.'&amp;sesskey='.sesskey());
            }
            redirect($CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id);
        }
    }

	/// Print the page header
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
	$strtask = get_string('depot_consigne','referentiel');
	$icon = '<img class="icon" src="'.$CFG->wwwroot.'/mod/referentiel/icon.gif" alt="'.get_string('modulename','referentiel').'"/>';
	/// RSS and CSS and JS meta
    $meta = '<link rel="stylesheet" type="text/css" href="referentiel.css" />';


$PAGE->set_url($url);
$PAGE->set_context($context);
$title = strip_tags($course->fullname.': '.get_string('modulename', 'referentiel').': '.format_string($referentiel->name,true));
$PAGE->set_title($title);
$PAGE->set_heading($title);

$options = array('subdirs'=>0, 'maxbytes'=>get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, 0), 'maxfiles'=>1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);

$mform = new mod_referentiel_upload_form(null, array('d'=>$referentiel->id, 'contextid'=>$context->id, 'userid'=>$USER->id, 'activiteid'=>$taskid, 'filearea'=>'consigne', 'msg' => get_string('depot_consigne','referentiel'), 'options'=>$options));

if ($mform->is_cancelled()) {
        redirect(new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id)));
} else if ($mform->get_data()) {
    referentiel_upload_document($mform, $referentiel->id);
    die();
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
die();


?>

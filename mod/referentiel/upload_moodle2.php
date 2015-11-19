<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package   mod-referentiel
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    // require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
    require(dirname(__FILE__) . '/../../config.php');
    //require_once(dirname(__FILE__).'/class/upload_form.php');
    require_once('class/upload_form.php');
    require_once("$CFG->dirroot/repository/lib.php");

    require_once('locallib.php');
    require_once('lib_etab.php');
    require_once("print_lib_activite.php");	// AFFICHAGES

    $id    = optional_param('id', 0, PARAM_INT);    // course module id
	$d     = optional_param('d', 0, PARAM_INT);    // referentielbase id

    $mailnow      = optional_param('mailnow', 0, PARAM_INT); // MODIF JF 2011/11/29 : pour afficher les destinataires
    $activite_id  = optional_param('activite_id', 0, PARAM_INT);    //record activite id
    $document_id   = optional_param('document_id', 0, PARAM_INT);    //record document id

    // $import   = optional_param('import', 0, PARAM_INT);    // show import form

    $old_mode   = optional_param('old_mode','', PARAM_ALPHA); // mode anterieur
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
	$userid = optional_param('userid', 0, PARAM_INT);
	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    $filtre_validation = optional_param('filtre_validation', 0, PARAM_INT);
    $filtre_referent = optional_param('filtre_referent', 0, PARAM_INT);
    $filtre_date_modif = optional_param('filtre_date_modif', 0, PARAM_INT);
    $filtre_date_modif_student = optional_param('filtre_date_modif_student', 0, PARAM_INT);
    $filtre_auteur = optional_param('filtre_auteur', 0, PARAM_INT);

	$data_filtre= new Object(); // parametres de filtrage
	if (isset($filtre_validation)){
			$data_filtre->filtre_validation=$filtre_validation;
	}
	else {
		$data_filtre->filtre_validation=0;
	}
	if (isset($filtre_referent)){
		$data_filtre->filtre_referent=$filtre_referent;
	}
	else{
		$data_filtre->filtre_referent=0;
	}
	if (isset($filtre_date_modif_student)){
		$data_filtre->filtre_date_modif_student=$filtre_date_modif_student;
	}
	else{
		$data_filtre->filtre_date_modif_student=0;
	}
	if (isset($filtre_date_modif)){
		$data_filtre->filtre_date_modif=$filtre_date_modif;
	}
	else{
		$data_filtre->filtre_date_modif=0;
	}
	if (isset($filtre_auteur)){
		$data_filtre->filtre_auteur=$filtre_auteur;
	}
	else{
		$data_filtre->filtre_auteur=0;
	}

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/upload_moodle2.php');

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
    // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : upload_moodle2.php'), 'referentiel');
	}

    $returnlink_ref = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));
    $returnlink_course = new moodle_url('/course/view.php', array('id'=>$course->id));

    require_login($course->id, false, $cm);
    if (!isloggedin() || isguestuser()) {
        redirect($returnlink_course);
    }

    $contextcourse = context_course::instance($course->id);
    $context = context_module::instance($cm->id);


	if ($activite_id) { // id activite
        if (! $record = $DB->get_record('referentiel_activite', array("id" => "$activite_id"))) {
            print_error('Activite ID is incorrect');
        }
	}
	if ($document_id) { // id activite
        if (! $record_document = $DB->get_record('referentiel_document', array("id" => "$document_id"))) {
            print_error('Document ID is incorrect');
        }
	}

    // Taille des telechargements
    if (isset($referentiel->maxbytes)){
        $maxbytes=$referentiel->maxbytes;
    }
    else{
        $maxbytes=0;
    }

    /// If it's hidden then it's don't show anything.  :)
	/// Some capability checks.
    if (empty($cm->visible)
    && (
        !has_capability('moodle/course:viewhiddenactivities', $context)
            &&
        !has_capability('mod/referentiel:managecomments', $context)
        )

    ) {
        print_error(get_string("activityiscurrentlyhidden"),'error', $returnlink_course);
    }


if ($cancel) {
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        }
        else {
            redirect(new moodle_url('/mod/referentiel/activite.php',
        array('id'=>$cm->id, 'userid'=>$userid, 'activite_id'=>$activite_id, 'mailnow'=>$mailnow,
'mode'=>'listactivityall', 'filtre_auteur'=>$data_filtre->filtre_auteur, 'filtre_validation'=>$data_filtre->filtre_validation,
'filtre_referent'=>$data_filtre->filtre_referent, 'filtre_date_modif'=>$data_filtre->filtre_date_modif,
'filtre_date_modif_student'=>$data_filtre->filtre_date_modif_student)));
        }
}



$PAGE->set_url($url);
$PAGE->set_context($context);
$title = strip_tags($course->fullname.': '.get_string('modulename', 'referentiel').': '.format_string($referentiel->name,true));
$PAGE->set_title($title);
$PAGE->set_heading($title);

// DEBUG
// echo "<br />upload_Mooddle2.php :: 151 :: activite_id:: $activite_id\n";
// exit;

$options = array('subdirs'=>0, 'maxbytes'=>get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, $maxbytes), 'maxfiles'=>1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);

$mform = new mod_referentiel_upload_form(null, array('d'=>$referentiel->id, 'contextid'=>$context->id, 'userid'=>$USER->id, 'activiteid'=>$activite_id, 'filearea'=>'document', 'msg' => get_string('document_associe', 'referentiel'), 'mailnow' => $mailnow, 'options'=>$options));

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/referentiel/activite.php',
        array('id'=>$cm->id, 'userid'=>$USER->id, 'activite_id'=>$activite_id, 'mailnow'=>$mailnow,
'mode'=>'listactivityall', 'filtre_auteur'=>$data_filtre->filtre_auteur, 'filtre_validation'=>$data_filtre->filtre_validation,
'filtre_referent'=>$data_filtre->filtre_referent, 'filtre_date_modif'=>$data_filtre->filtre_date_modif,
'filtre_date_modif_student'=>$data_filtre->filtre_date_modif_student)));
}
else if ($mform->get_data()) {
// A TERMINER
    referentiel_upload_document($mform, $referentiel->id);
    die();
//    redirect(new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id)));
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
die();

?>
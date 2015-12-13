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
* D'apres competency/import_etudiant.php 
*
* @package referentiel
*/

    require_once('../../config.php');
    require_once('class/import_form.php'); // formulaires de choix de fichiers
    // require_once($CFG->libdir . '/uploadlib.php'); // Moodle 1.9
    require_once("$CFG->dirroot/repository/lib.php"); // Moodle 2.0
    require_once('import_export_lib.php');	// IMPORT / EXPORT
    require_once('locallib.php');
	require_once('lib_etab.php');


    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel base id

    $mode           = optional_param('mode','', PARAM_ALPHANUMEXT);

    $format = optional_param('format','', PARAM_FILE );
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/import_etudiant.php');

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
		print_error(get_string('erreurscript','referentiel','Erreur01 : import_etudiant.php'));
	}

    require_login($course->id, false, $cm);   // pas d'autologin guest

    if (!isloggedin() or isguestuser()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
    }

    // check role capability
    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

    require_capability('mod/referentiel:import', $context);

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

    // ensure the files area exists for this course
    // make_upload_directory( "$course->id/$CFG->moddata/referentiel" );


   	$defaultformat = FORMAT_PLAIN;
    $editorfields = '';

	/// Print the tabs
	if (!isset($mode)){
		$mode='importetudiant'; // un seul mode possible
	}
	$currenttab = $mode;

    if ($referentiel->id) {
    	$editentry = true;  //used in tabs
    }


    $url->param('mode', $mode);
    $action='importetudiant'; // une seule action possible
    $url->param('action', $action);

    // AFFICHAGE DE LA PAGE Moodle 2
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
    $strmessage = get_string('importetudiants','referentiel');
    $strpagename = get_string('import_etudiant', 'referentiel');

    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);


    $PAGE->set_title($pagetitle);
    $PAGE->navbar->add($strpagename);
    $PAGE->set_heading($course->fullname);

    // formulaire de saisie d'un fichier
    $fileformatnames = referentiel_get_import_export_formats( 'import', 'eformat' );
    $options = array('subdirs'=>0, 'maxbytes'=>get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, 0), 'maxfiles'=>1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
    $mform = new referentiel_import_form(null, array('d'=>$referentiel->id, 'contextid'=>$context->id, 'filearea'=>'scolarite', 'fileformats' => $fileformatnames, 'stoponerror' => 1, 'msg' =>  get_string('importetudiants', 'referentiel'),  'options'=>$options));

    // recuperer le fichier charge
    if ($mform->is_cancelled()) {
        // redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
        redirect(new moodle_url('/mod/referentiel/etudiant.php', array('id'=>$cm->id, 'mode' => 'listetudiant', 'select_acc' => 0)));
    }
    else if ($mform->get_data()) {

        $returnlink=new moodle_url('/mod/referentiel/etudiant.php', array('id'=>$cm->id, 'mode' => 'listetudiant', 'select_acc' => 0));

        if ($formdata = $mform->get_data()) {
            // DEBUG
            // echo "<br />DEBUG :: import_instance.php :: 193 :: FORMDATA\n";
            // print_object($formdata);

            //
            $fileareas = array('referentiel', 'document', 'consigne', 'activite', 'task', 'certificat', 'scolarite', 'pedagogie');
            if (!empty($formdata->filearea) && ($formdata->filearea == 'scolarite')) {

                $fs = get_file_storage();
                // suppression du fichier existant ?   NON
                // $fs->delete_area_files($formdata->contextid, 'mod_referentiel', $formdata->filearea, 0);

                if ($newfilename= $mform->get_new_filename('referentiel_file')) {

				    echo $OUTPUT->header();
                    $contents = $mform->get_file_content('referentiel_file');
                    if (!empty($contents)){

                        $format=$formdata->format;

                        // echo "<br />DEBUG :: 235 :: $format<br />\n";

                        if (! is_readable("format/$format/format.php")) {
                            print_error( get_string('formatnotfound','referentiel', $format) );
                        }

                        require_once("format.php");  // Parent class
                        require_once("format/$format/format.php");
                        $classname = "eformat_$format";
                        // echo "<br />DEBUG :: 232 :: $classname<br />\n";

                        $eformat = new $classname();
                        // load data into class
                        $eformat->setIReferentiel( $referentiel ); // instance
                        // $eformat->setRefRerentiel( $referentiel->ref_referentiel );
                        $eformat->setRReferentiel( $referentiel_referentiel ); // referenteil_referentiel
                        $eformat->setCourse( $course );
                        $eformat->setContext( $context );
                        $eformat->setCoursemodule( $cm);
                        $eformat->setContents( $contents );
                        $eformat->setStoponerror( $formdata->stoponerror );

                        // Do anything before that we need to
                        if (! $eformat->importpreprocess()) {
                            print_error( get_string('importerror','referentiel') , $returnlink);
                        }

                        // Process the uploaded file
                        if (! $eformat->importprocess() ) {
                            print_error( get_string('importerror','referentiel') , $returnlink);
                        }

                        // In case anything needs to be done after
                        if (! $eformat->importpostprocess()) {
                            print_error( get_string('importerror','referentiel') , $returnlink);
                        }

                        echo "<hr />";
                        if (isset($eformat->returnpage) && ($eformat->returnpage!="")){
                            echo $OUTPUT->continue_button($eformat->returnpage);
                        }
                        else{
                            echo $OUTPUT->continue_button($CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id);
                        }
                    }
                    else{
                        print_error( get_string('cannotread','referentiel') );
                    }

                    echo $OUTPUT->footer();
                    die();
                }
            }
        }
        redirect($returnlink);
    }

    // afficher la page
    echo $OUTPUT->header();

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();
    echo '<br />'."\n";
    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title=""  alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('importetudianth','referentiel').'</h2></div>'."\n";

    // boite de saisie
    echo $OUTPUT->box_start('generalbox');
    $mform->display();
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();
    die();

?>

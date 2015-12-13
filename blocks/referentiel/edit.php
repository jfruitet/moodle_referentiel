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
$mode  = optional_param('mode', 'edit', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')
$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
$checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni
$action = optional_param('action','', PARAM_ALPHANUMEXT);
$edit = optional_param('edit', -1, PARAM_INT);
$approve = optional_param('approve', 0, PARAM_INT);    //approval recordid
$delete = optional_param('delete', '', PARAM_ALPHANUMEXT);    //delete action
$deleteid = optional_param('deleteid', 0, PARAM_INT);    // delete record id
// http://localhost/moodle25/blocks/referentiel/edit.php?blockid=65&courseid=2&occurrenceid=2&deleteid=217&action=modifieritem&delete=Supprimer&pass=&sesskey=pLZgTzHQUJ
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_referentiel', $courseid);
}

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
$viewurl = new moodle_url('/blocks/referentiel/view.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid));

require_login($course);
$currenttab = $mode;

// Traitements
	// Occurrence
	$params=array("blockid"=>$blockid, "courseid"=>$courseid, "occurrenceid"=>$occurrenceid);
	$occurrence_object = new occurrence($params);
/*
	// DEBUG
	echo "<br>DEBUG :: edit.php :: 75\n";
	print_object($occurrence_object->referentiel);
*/
	$role=$occurrence_object->roles();
    $isadmin=$role->is_admin;
    $isauthor=$occurrence_object->is_author();
/*
	if ($isauthor){
		echo "<br />DEBUG :: edit.php :: 80 :: ISAUTHOR : true <br />ROLES\n";
	}
	else{
		echo "<br />DEBUG :: edit.php :: 80 :: ISAUTHOR : false <br />ROLES\n";
	}

	print_object($role);
    echo "<br />\n";
	exit;
*/
	// variables d'action
	if (!empty($delete)){
    	if ($delete == get_string("delete")){
        	if (!empty($deleteid) && (($action=="modifierdomaine") || ($action=="modifiercompetence") || ($action=="modifieritem"))){

            	if ($action=="modifierdomaine"){
					// enregistre les modifications
					$ok=referentiel_supprime_domaine($deleteid);
				}
				else if ($action=="modifiercompetence"){
					$ok=referentiel_supprime_competence($deleteid);
				}
				else if ($action=="modifieritem"){
					$ok=referentiel_supprime_item($deleteid);
				}

			    if ($ok) {
					// Mise a jour de la liste de competences dans le referentiel
					$liste_codes_competence=referentiel_new_liste_codes_competence($occurrenceid);
					// echo "<br />LISTE_CODES_COMPETENCE : $form->liste_codes_competence\n";
					referentiel_set_liste_codes_competence($occurrenceid, $liste_codes_competence);
				}
			}
			$editurl = new moodle_url('/blocks/referentiel/edit.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$pass));
			redirect($editurl);
		}
	}

	// affichage

	$pagetitle=get_string('occurrence', 'block_referentiel', $occurrence_object->referentiel->code_referentiel);
	$PAGE->set_url('/blocks/referentiel/edit.php', array('blockid'=>$blockid, 'courseid' => $courseid, 'occurrenceid' => $occurrenceid ));
	$PAGE->requires->css('/mod/referentiel/referentiel.css');
	$PAGE->requires->js('/mod/referentiel/functions.js');
	$PAGE->set_pagelayout('standard');
	$PAGE->set_heading($course->fullname);
	$PAGE->set_title($pagetitle);
	$PAGE->navbar->add($occurrence_object->referentiel->code_referentiel);
	//$settingsnode = $PAGE->settingsnav->add(get_string('displayoccurrence', 'block_referentiel'));
	//$site = get_site();

	$settingsnode = $PAGE->settingsnav->add(get_string('editoccurrence', 'block_referentiel'));
	$editurl = new moodle_url('/blocks/referentiel/edit.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$pass));
	$editnode = $settingsnode->add(get_string('editoccurrence', 'block_referentiel'), $editurl);
	$editnode->make_active();

	// imput occurrence / domains / competencies / items
	if ($role->can_edit){
		$options = array('pass'=>$pass);
		if ($occurrence_form = new occurrence_form(null, array('blockid'=>$blockid, 'courseid'=>$courseid, 'userid'=>$USER->id, 'occurrence'=>$occurrence_object->referentiel, 'options'=>$options))){
			if ($occurrence_form->is_cancelled()) {
    			// Cancelled forms redirect to the view main page.
    			redirect($viewurl);
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
				// form didn't validate or this is the first display
    			// verifer si le mot de passe est fourni
			    if (!$pass  // si cest un admin il outrepasse car pass==1
					&& (( isset($occurrence_object->referentiel->pass_referentiel)
					&&
		   			($occurrence_object->referentiel->pass_referentiel!=''))
           			|| $isauthor
           			|| $isadmin
	        		)){
					// demander le mot de passe
					$options = array('pass'=>$occurrence_object->referentiel->pass_referentiel, 'isadmin'=>$isadmin, 'isauthor'=>$isauthor);
					if ($pass_form = new pass_form(null, array('occurrenceid'=>$occurrence_object->referentiel->id, 'blockid'=>$blockid, 'courseid'=>$courseid, 'options'=>$options))){
           				if($pass_form->is_cancelled()) {
		    				// Cancelled forms redirect to the view main page.
    						redirect($viewurl);
							die();
						} else if ($formdata=$pass_form->get_data()) {
    						// We need to add code to appropriately act on and store the submitted data
							// DEBUG
							// print_object($formdata);
							// exit;
							// le mot de passe est-il actif ?
							// cette fonction est due au parametrage
							if ($formdata->checkpass=='checkpass'){
		    				    if (!empty($formdata->pass_delete)){
									// supprimer
        		                    $pass=referentiel_delete_pass($occurrence_object->referentiel->id);
   									if (!$pass){
   										// Abandonner
              							redirect($viewurl);
		    							die();
						  			}
								}
								else if (!empty($formdata->pass_referentiel)){
           							if (!empty($formdata->force_pass)){  // force EDITION
	    	        					$pass=referentiel_set_pass($occurrence_object->referentiel->id, $formdata->pass_referentiel);
				        	    	}
	         		   				else{ // tester le mot de passe
    	       							$pass=referentiel_check_pass($occurrence_object->referentiel, $formdata->pass_referentiel);
        		    				}
		    	       				if (!$pass){
               							// Abandonner
	               						redirect($viewurl);
		    	   						die();
			   	        			}
        						}
        						else{
        							if (empty($formdata->force_pass)){  // empty password and not an admin or author connected
			               				// Abandonner
    	           						redirect($viewurl);
       									die();
									}
    							}
							}
						}
						else {
			   				// form didn't validate or this is the first display
							echo $OUTPUT->header();
 							$pass_form->display();
							echo $OUTPUT->footer();
							die();
						}
					}
				}
            	echo $OUTPUT->header();
				$occurrence_object->tabs($mode, $currenttab);
 				$occurrence_form->display();
				echo $OUTPUT->footer();
			}
		}
	}
	else{
    	redirect($viewurl);
        die();
	}

?>
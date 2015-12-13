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
require_once('pass_form.php');
require_once('lib.php');
require_once($CFG->dirroot.'/mod/referentiel/lib_config.php');

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);


// Next look for optional variables.
$occurrenceid = optional_param('occurrenceid', 0, PARAM_INT);
$mode  = optional_param('mode', 'delete', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')
$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
$pass_delete  = optional_param('pass_delete', 0, PARAM_INT);    // mot de passe a supprimer
$checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni
$action = optional_param('action','', PARAM_ALPHANUMEXT);
$delete = optional_param('delete', '', PARAM_ALPHANUMEXT);    //delete action
$cancel = optional_param('cancel', '', PARAM_ALPHANUMEXT);    //cancel action
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
// RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
	else {
        $form = (object)$_POST;
    }

    // Traitement des POST
	$msg="";
	if (!empty($occurrence_object->referentiel) && isset($form)) {
		// add, delete or update form submitted

		if (!empty($form->delete)){
			if ($form->delete == get_string("delete")){
				// Suppression instances
				if ($form->action=="supprimerinstances"){
					$msg='';
					// enregistre les modifications
					if (isset($form->t_ref_instance) && ($form->t_ref_instance) && is_array($form->t_ref_instance)){
						while (list($key, $val)=each($form->t_ref_instance)){
							if ($val){
								// suppression sans confirmation
								// REPRIS DE course/mod.php
								// DEBUG
								// print_object($form->t_ref_instance);
								// echo "<br />COURSEID:".$course->id." KEY:$key, VAL_InstanceId:$val\n";
								// exit;
								if (!$cm = get_coursemodule_from_instance('referentiel', $val, $course->id)) {
         							print_error('invalidmodule', 'block_referentiel', $val);
								}

								$params=array("module" => "$cm->module", "refid" => "$val");
								$sql = "SELECT * FROM {course_modules} WHERE module = :module AND instance=:refid ";
								$courses_modules = $DB->get_records_sql($sql, $params);

								if ($courses_modules){
  									foreach($courses_modules as $course_module){
										if (!empty($course_module)) {
            								if ($course_record = $DB->get_record("course", array("id" => "$course_module->course"))) {
    											require_login($course_module->course); // needed to setup proper $COURSE
											    $context_course = context_course::instance($course_module->course);
               									require_capability('moodle/course:manageactivities', $context_course);

			     								if ($that_instance = $DB->get_record("referentiel", array("id" => "$course_module->instance"))){
							     				    if (function_exists('course_delete_module')){  // Moodle v 2.5 et suivantes
                                                        if (course_delete_module($course_module->id)) {
                                                            if (delete_mod_from_section($course_module->id, "$course_module->section")) {
                                                                rebuild_course_cache($course_record->id);
		          				      						    $msg.=get_string('instance_deleted', 'referentiel').' '.$that_instance->name;
                                                            }
                                                        }
									                }
									                else{ // Moodle v 2.x
                                                        if (delete_course_module($course_module->id)) {
                                                            if (delete_mod_from_section($course_module->id, "$course_module->section")) {
                                                                rebuild_course_cache($course_record->id);
		          				      						    $msg.=get_string('instance_deleted', 'referentiel').' '.$that_instance->name;
				              			                    }
									                    }
                                                    }

                        							// Supprimer l'instance
								                	if (!referentiel_delete_instance($that_instance->id)) {
                                						$record_course = $DB->get_record('course', array('id'=> $that_instance->course));
	                                					$msg.= "<br />".get_string('instance','referentiel')." $that_instance->name (#$that_instance->id) ".get_string('course')." $record_course->fullname ($record_course->shortname) ".get_string('not_deleted', 'referentiel')."\n";
    	        			    		            }
												}
							            	}
						            	}
					            	}
								}
								else{
									// cette 'instance' n'existe dans aucun module, c'est juste un fantome, on peut la detruire
								    if (!referentiel_delete_instance($val)) {
		                                $record_instance = referentiel_get_referentiel($val);
                                		$record_course = $DB->get_record('course', array('id'=> $record_instance->course));
                                		$msg.= "<br />".get_string('instance','referentiel')." $record_instance->name (#$record_instance->id) ".get_string('course')." $record_course->fullname ($record_course->shortname) ".get_string('not_deleted', 'referentiel')."\n";
            			    		}
                                }
                            }
                        }
                    }

					if (!empty($form->referentiel_id)){
						$records_instance=referentiel_referentiel_list_of_instance($form->referentiel_id);
						if ($records_instance){
                            foreach($records_instance as $r_instance){
                                $record_instance = referentiel_get_referentiel($r_instance->id);
                                $record_course = $DB->get_record('course', array('id'=> $record_instance->course));
                                $msg.= "<br />".get_string('instance','referentiel')." $record_instance->name (#$record_instance->id) ".get_string('course')." $record_course->fullname ($record_course->shortname) ".get_string('not_deleted', 'referentiel')."\n";
                            }
							$msg.='<br />'.get_string("suppression_referentiel_impossible", "referentiel", $occurrence_object->referentiel->code_referentiel);
							redirect($viewurl, $msg);
						}
						else{
							// suppression du referentiel
							$return=referentiel_delete_referentiel_domaines($form->referentiel_id);
							if (isset($return) && !empty($return) && !is_string($return)){
                                // suppression des certificats
                                referentiel_delete_referentiel_certificats($form->referentiel_id);
                                $msg=get_string('deletereferentiel', 'referentiel').' '.$occurrence_object->referentiel->code_referentiel;
                                redirect($courseurl);
                            }
                            else{
   			                  	redirect($courseurl,"Could not delete #".$occurrence_object->referentiel->code_referentiel." occurrence...");
                            }
							exit;
						}
					}
				}
				// Suppression occurrence
				elseif ($form->action=="modifierreferentiel"){
					// enregistre les modifications
					if (!empty($form->referentiel_id)){
						$records_instance=referentiel_referentiel_list_of_instance($form->referentiel_id);
						if ($records_instance){
                            $msg='';
                            foreach($records_instance as $r_instance){
                                $record_instance = referentiel_get_referentiel($r_instance->id);
                                $record_course = $DB->get_record('course', array('id'=> $record_instance->course));
                                $msg.= "<br />".get_string('instance','referentiel')." $record_instance->name (#$record_instance->id) ".get_string('course')." $record_course->fullname ($record_course->shortname) ".get_string('not_deleted', 'referentiel')."\n";
                            }
							$msg.=get_string("suppression_referentiel_impossible", "referentiel")." ".$occurrence_object->referentiel->code_referentiel;
							redirect($viewurl,$msg);
						}
						else{
							// suppression du referentiel_referentiel
							$return=referentiel_delete_referentiel_domaines($form->referentiel_id);
							if (isset($return) && !empty($return) && !is_string($return)){
                                referentiel_delete_referentiel_certificats($form->referentiel_id);
							}
							$msg=get_string('deletereferentiel', 'referentiel').' '.$occurrence_object->referentiel->code_referentiel;
                            redirect($courseurl,$msg);
							exit;
						}
					}
				}
			}
		}
		if ($cancel){
            redirect($viewurl);
      		die();
		}

	}

	// affichage

$pagetitle=get_string('occurrence', 'block_referentiel', $occurrence_object->referentiel->code_referentiel);
$PAGE->set_url('/blocks/referentiel/delete.php', array('blockid'=>$blockid, 'courseid' => $courseid, 'occurrenceid' => $occurrenceid ));
$PAGE->requires->css('/mod/referentiel/referentiel.css');
$PAGE->requires->js('/mod/referentiel/functions.js');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);
$PAGE->set_title($pagetitle);
$PAGE->navbar->add($occurrence_object->referentiel->code_referentiel);
$icon = $OUTPUT->pix_url('icon','referentiel');
	//$settingsnode = $PAGE->settingsnav->add(get_string('displayoccurrence', 'block_referentiel'));
	//$site = get_site();

$settingsnode = $PAGE->settingsnav->add(get_string('deleteoccurrence', 'block_referentiel'));
$deleteurl = new moodle_url('/blocks/referentiel/delete.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid' => $occurrenceid ));
$deletenode = $settingsnode->add(get_string('deleteoccurrence', 'block_referentiel'), $deleteurl);
$deletenode->make_active();

if (!empty($occurrenceid)){
	if ($role->can_edit){
		if (!$pass  // si c'est un admin il outrepasse car pass==1
		&& (
			(
			!empty($occurrence_object->referentiel->pass_referentiel)
			)
   			|| $isauthor
   			|| $isadmin
			)
		){
			// demander le mot de passe
			$options = array('pass'=>$occurrence_object->referentiel->pass_referentiel, 'isadmin'=>$isadmin, 'isauthor'=>$isauthor);
			if ($pass_form = new pass_form(null, array('occurrenceid'=>$occurrence_object->referentiel->id, 'blockid'=>$blockid, 'courseid'=>$courseid, 'options'=>$options)))
			{
				if ($pass_form->is_cancelled()) {
					// Cancelled forms redirect to the view main page.
     				redirect($viewurl);
					die();
				}
				else if ($formdata=$pass_form->get_data()) {
					// DEBUG
					// print_object($formdata);
					// exit;
					// le mot de passe est-il actif ?
					// cette fonction est due au parametrage
					if ((!$pass) && ($checkpass=='checkpass')){
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
    	echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('supprimer_referentiel','referentiel').' '.$OUTPUT->help_icon('suppreferentielh','referentiel').'</h2></div>'."\n";
    	echo $OUTPUT->box_start('generalbox  boxaligncenter');
    	echo referentiel_select_delete($blockid, $course, $occurrence_object->referentiel, $mode, $pass);
    	echo $OUTPUT->box_end();
    	echo $OUTPUT->footer();
    	die();
	}
	else {
    	redirect($viewurl);
        die();
	}
}


// -------------------
function referentiel_select_delete($blockid, $course, $occurrence, $mode, $pass){
global $USER;
global $DB;
global $CFG;
	$s='';
    $email_user=referentiel_get_user_mail($USER->id);
	$interdire_creer_importer=referentiel_ref_get_item_config('creref', $occurrence->id);
    $old_pass_referentiel = $occurrence->pass_referentiel;
	/*
	// DEBUG
	echo "<br>DEBUG :: delete.php :: 196\n";
	print_object($occurrence);
    echo "<br>$email_user, \n";
	if ($interdire_creer_importer){
        echo "<br>NOT CREREF \n";
	} else{
        echo "<br>YES CREREF \n";
	}
	exit;
	*/
	if (!empty($interdire_creer_importer)){
	    $viewurl = new moodle_url('/blocks/referentiel/view.php', array('blockid'=>$blockid, 'course->id'=>$course->id, 'occurrenceid'=>$occurrence->id));
    	notice(get_string('suppression_non_autorisee','referentiel'), $viewurl);
	}
	else {
		$records_instance_id=referentiel_referentiel_list_of_instance($occurrence->id);
		$nbinstances=0;
		if ($records_instance_id){
			$s.='<h4 align="center">'.get_string("selection_instance_referentiel", "referentiel").'</h4>'."\n";
			$s.='<div>
<form name="form" method="post" action="delete.php">
<table cellpadding="5" bgcolor="#eeeeee">
';
			foreach ($records_instance_id  as $record_id){
				$record_instance = referentiel_get_referentiel($record_id->id);
				if ($record_instance){
                    $nbinstances++;
					$record_course = $DB->get_record("course", array("id"=>$record_instance->course));
					$s.='<tr valign="top">'."\n";
  					if ($record_course->id==$course->id){
						$s.='<td align="left"><input type="checkbox" name="t_ref_instance[]" value="'.$record_instance->id.'" checked="checked"  /></td>
<td align="left"><b>'.get_string('cours_courant','referentiel').' : </b></td>
<td align="left">'.$record_course->fullname.' ('.$record_course->shortname.')</td>'."\n";
					}
					else{
	      				$s.='<td align="left"><input type="checkbox" name="t_ref_instance[]" value="'.$record_instance->id.'"  /></td>
<td align="left"><b>'.get_string('cours_externe','referentiel').' : </b></td>
<td align="left"><a href="'.$CFG->wwwroot.'/course/view.php?id='.$record_course->id.'">'.$record_course->fullname.'</a> ('.$record_course->shortname.')</td>'."\n";
					}
					$s.='<td align="left"><b>'.get_string('name_instance','referentiel').':</b></td>
<td align="left">'.$record_instance->name.'</td><td align="left"><b>'.get_string('description_instance','referentiel').'</b> : </td>
<td align="left">'.strip_tags($record_instance->description_instance).'</td></tr>'."\n";
				}
			}
			$s.='</table>
<br />
<!-- These hidden variables are always the same -->
<input type="hidden" name="action" value="supprimerinstances" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="blockid" value="'.$blockid.'" />
<input type="hidden" name="courseid" value="'.$course->id.'" />
<input type="hidden" name="occurrenceid" value="'.$occurrence->id.'" />
<input type="hidden" name="pass" value="1" />
<input type="submit" name="delete" value="'.get_string("delete").'" />
<input type="reset"  value="'.get_string("cancel").'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel").'" />
</form>
</div>
';
		}
		else { // proposer la suppression de l'occurrence
			$s.='<div class="ref_aff0">'."\n";
            $s.='<form name="form" method="post" action="delete.php">
<p><b>'.get_string('name','referentiel').'</b> : '.stripslashes($occurrence->name).'
<br /><b>'.get_string('code','referentiel').'</b> : '.$occurrence->code_referentiel.'
';
			if (!empty($occurrence->mail_auteur_referentiel)){
				$s.='<br /><b>'.get_string('auteur','referentiel').' </b> : <i>'.$occurrence->mail_auteur_referentiel.'</i>
';
			}
			if (!empty($occurrence->pass_referentiel)){
				$s.='<br /><b>'.get_string('pass_referentiel','referentiel').' </b> : <i>'.get_string('pass_set','block_referentiel').'</i>
';
			}
			$s.='<br /><b>'.get_string('description','referentiel').'</b> : '.strip_tags($occurrence->description_referentiel).'
<br /><b>'.get_string('url','referentiel').'</b> : '.$occurrence->url_referentiel.'
<br /><b>'.get_string('logo','referentiel').'</b> : '.$occurrence->logo_referentiel;
			// $s.='<br /><b>'.get_string('seuil_certificat','referentiel').'</b> : '.$occurrence->seuil_certificat;
			$s.='<br /><b>'.get_string('referentiel_global','referentiel').'</b> : '."\n";
			if (!empty($occurrence->local)){
					$s.= get_string("no")."\n";
			}
			else{
				$s.= get_string("yes")."\n";
			}
/*
            $s.='<br /><b>'.get_string('nombre_domaines_supplementaires','referentiel').'</b> :
    '.$occurrence->nb_domaines;
*/
			$s.='
</p><p>
<input type="hidden" name="action" value="modifierreferentiel" />
<input type="hidden" name="referentiel_id"      value="'.$occurrence->id.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="mail_auteur_referentiel" value="'.$occurrence->mail_auteur_referentiel.'" />
<input type="hidden" name="old_pass_referentiel" value="'.$old_pass_referentiel.'" />
<input type="hidden" name="cle_referentiel" value="'.$occurrence->cle_referentiel.'" />
<input type="hidden" name="liste_codes_competence" value="'.$occurrence->liste_codes_competence.'" />
<input type="hidden" name="liste_empreintes_competence" value="'.$occurrence->liste_empreintes_competence.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="blockid" value="'.$blockid.'" />
<input type="hidden" name="courseid" value="'.$course->id.'" />
<input type="hidden" name="occurrenceid" value="'.$occurrence->id.'" />
<input type="hidden" name="pass" value="1" />
<input type="submit" name="delete" value="'.get_string("delete").'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel").'" />
</p>
</form>
</div>
';
		}
	}
	return $s;
}


?>
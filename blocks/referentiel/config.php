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
$mode  = optional_param('mode', 'config', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')
$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
$checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni
$action = optional_param('action','', PARAM_ALPHANUMEXT);
$edit = optional_param('edit', -1, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_referentiel', $courseid);
}

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
// url avec pass active
$viewurl = new moodle_url('/blocks/referentiel/view.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid));
$baseurl = new moodle_url('/blocks/referentiel/config.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$pass));

require_login($course);
$currenttab = $mode;
// Occurrence
$params=array("blockid"=>$blockid, "courseid"=>$courseid, "occurrenceid"=>$occurrenceid);
$occurrence_object = new occurrence($params);
//print_object($occurrence_object->referentiel);
$role=$occurrence_object->roles();
$isadmin=$role->is_admin;
$isauthor=$occurrence_object->is_author();

$form = (object)$_POST;
// variable d'action
if (!empty($form->cancel)&& ($form->cancel == get_string("quit", "referentiel"))){
	// Abandonner
	redirect($viewurl);
	exit;
}

// mise à jour de la configuration
if (!empty($form->action) && ($form->action=='modifierconfig') && !empty($form->mode) && ($form->mode=='config')){
	// sauvegarder
    $config=referentiel_initialise_configuration($form,'config');
    referentiel_global_set_vecteur_config($config, $occurrence_object->referentiel->id);
	$config_impression=referentiel_initialise_configuration($form,'config_impression');
    referentiel_global_set_vecteur_config_imp($config_impression, $occurrence_object->referentiel->id);
	redirect($viewurl);
	exit;
}

$strlastmodified = get_string('lastmodified');
$icon = $OUTPUT->pix_url('icon','referentiel');

// affichage
$pagetitle=get_string('occurrence', 'block_referentiel', $occurrence_object->referentiel->code_referentiel).' '.get_string('config','block_referentiel');
$PAGE->set_url('/blocks/referentiel/config.php', array('blockid'=>$blockid, 'courseid' => $courseid, 'occurrenceid' => $occurrenceid ));
$PAGE->requires->css('/mod/referentiel/referentiel.css');
$PAGE->requires->js('/mod/referentiel/functions.js');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);
$PAGE->set_title($pagetitle);
$PAGE->navbar->add($occurrence_object->referentiel->code_referentiel);
//$settingsnode = $PAGE->settingsnav->add(get_string('displayoccurrence', 'block_referentiel'));
//$site = get_site();

$settingsnode = $PAGE->settingsnav->add(get_string('config', 'block_referentiel'));
$editnode = $settingsnode->add(get_string('config', 'block_referentiel'), $baseurl);
$editnode->make_active();

/// Parametre des onglets
$currenttab = 'config';
$icon = $OUTPUT->pix_url('icon','referentiel');

if (!empty($occurrenceid)){
	if ($role->can_edit){
		if (!$pass  // si c est un admin il outrepasse car pass==1
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
				if($pass_form->is_cancelled()) {
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
    	echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('config','block_referentiel').' '.$OUTPUT->help_icon('configreferentielh','referentiel').'</h2></div>'."\n";
    	echo $OUTPUT->box_start('generalbox  boxaligncenter');
    	echo referentiel_select_config_occurrence($blockid, $courseid, $occurrence_object->referentiel, $mode, $pass);
    	echo $OUTPUT->box_end();
    	echo $OUTPUT->footer();
    	die();
	}
	else{
		echo $OUTPUT->header();
        $occurrence_object->tabs($mode, $currenttab);
    	echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('config','block_referentiel').' '.$OUTPUT->help_icon('configreferentielh','referentiel').'</h2></div>'."\n";
    	echo $OUTPUT->box_start('generalbox  boxaligncenter');
    	echo referentiel_affiche_config_occurrence($occurrence_object->referentiel);
    	echo $OUTPUT->box_end();
    	echo $OUTPUT->footer();
    	die();
	}
}



//--------------------
function referentiel_select_config_occurrence($blockid, $courseid, $occurrence, $mode, $pass){
global $OUTPUT;
    // configuration
	$s='<h3 align="center">'.get_string('configurer_referentiel','referentiel',$occurrence->code_referentiel).' '.$OUTPUT->help_icon('configrefglobalh','referentiel').'</h3>'."\n";
 	$s='<div class="ref_aff0">'."\n";
    $s.='<p><b>'.get_string('name','referentiel').'</b> '.$occurrence->name;
    $s.='<br /><b>'.get_string('code','referentiel').'</b> : '.$occurrence->code_referentiel."\n";
	$s.='<br /><b>'.get_string('description_instance','referentiel').'</b> : '.$occurrence->description_referentiel."\n";
	$s.='<br /><b>'.get_string('referentiel_config_global','referentiel').'</b><br /><i>'.get_string('aide_referentiel_config_global','referentiel').'</i></p>'."\n";
    $s.='<form name="form" method="post" action="config.php">'."\n";
    $s.=referentiel_selection_configuration($occurrence->config, 'config');
    if (referentiel_global_can_print_referentiel($occurrence->id)){
		$s.=' <!-- impression certificat -->
<br /><b>'.get_string('referentiel_config_global_impression','referentiel').'</b><br />'."\n";
		$s.=referentiel_selection_configuration($occurrence->config_impression, 'config_impression')."\n";
	}
    // validation
	$s.='<br />&nbsp; &nbsp; &nbsp; <input type="submit" value="'.get_string('savechanges').'" />'."\n";
	$s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
    $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";

    $s.='<input type="hidden" name="pass" value="1" />
<input type="hidden" name="action" value="modifierconfig" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="blockid" value="'.$blockid.'" />
<input type="hidden" name="courseid" value="'.$courseid.'" />
<input type="hidden" name="occurrenceid" value="'.$occurrence->id.'" />
<input type="hidden" name="sesskey" value="'.sesskey().'" />
<input type="hidden" name="mode" value="'.$mode.'" />'."\n";
    $s.='</form>'."\n";
    $s.='</div>'."\n";
    return $s;
}



//--------------------
function referentiel_affiche_config_occurrence($occurrence){
global $OUTPUT;
    // configuration
	$s='<h3 align="center">'.get_string('configurer_referentiel','referentiel',$code_referentiel).' '.$OUTPUT->help_icon('configglobalh','referentiel').'</h3>'."\n";
    $s.='<div><b>'.get_string('name','referentiel').' ('.get_string('code','referentiel').')</b> '.$occurrence->name.' ('.$occurrence->code_referentiel.')'."\n";
	$s.='<br /><b>'.get_string('description_instance','referentiel').'</b> :'.$occurrence->description_referentiel.   "\n";
	$s.='<br /><b>'.get_string('referentiel_config_global','referentiel').'<i>'.get_string('aide_referentiel_config_global','referentiel').'</i><br />'."\n";
    $s.=referentiel_affiche_configuration($occurrence->config, 'config');
    if (referentiel_global_can_print_referentiel($occurrence->id)){
		$s.=' <!-- impression certificat -->
<br /><b>'.get_string('referentiel_config_global_impression','referentiel').'</b>'."\n";
		$s.=referentiel_affcihe_configuration($occurrence->config_impression, 'config_impression')."\n";
	}

    return $s;
}
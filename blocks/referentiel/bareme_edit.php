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

require_once('../../config.php');
require_once('occurrence_class.php');
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';
require_once('pass_form.php');
require_once('lib.php');
require_once($CFG->dirroot.'/mod/referentiel/lib_bareme.php');


// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

// Next look for optional variables.
$occurrenceid = optional_param('occurrenceid', 0, PARAM_INT);
$mode  = optional_param('mode', 'edit', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')
$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
$checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni
$action = optional_param('action','', PARAM_ALPHANUMEXT);
$scaleid    = optional_param('scaleid', 0, PARAM_INT);    // scale id
$baremeid    = optional_param('baremeid', 0, PARAM_INT);    // scale id

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_referentiel', $courseid);
}

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
$viewurl = new moodle_url('/blocks/referentiel/view.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$pass));
$baseurl = new moodle_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$pass));

require_login($course);
// Traitements
	// Occurrence
	$params=array("blockid"=>$blockid, "courseid"=>$courseid, "occurrenceid"=>$occurrenceid);
	$occurrence_object = new occurrence($params);
	//print_object($occurrence_object->referentiel);
	$role=$occurrence_object->roles();
    $isadmin=$role->is_admin;
    $isauthor=$occurrence_object->is_author();

	// action

	// affichage

	$pagetitle=get_string('occurrence', 'block_referentiel', $occurrence_object->referentiel->code_referentiel);
	$PAGE->set_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid' => $courseid, 'occurrenceid' => $occurrenceid, , 'pass' => $pass  ));
	$PAGE->requires->css('/mod/referentiel/referentiel.css');
	// $PAGE->requires->js('/mod/referentiel/functions.js');
	$PAGE->set_pagelayout('standard');
	$PAGE->set_heading($course->fullname);
	$PAGE->set_title($pagetitle);
	$PAGE->navbar->add($occurrence_object->referentiel->code_referentiel);
	//$settingsnode = $PAGE->settingsnav->add(get_string('displayoccurrence', 'block_referentiel'));
	//$site = get_site();

	$settingsnode = $PAGE->settingsnav->add(get_string('bareme', 'block_referentiel'));
	$editnode = $settingsnode->add(get_string('editbareme', 'block_referentiel'), $baseurl);
	$editnode->make_active();

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
				if ((!$pass) && ($checkpass=='checkpass')){
			    	if (!empty($formdata->pass_referentiel)){
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
		/// Parametre des onglets
		if (!isset($mode)){
			$mode='bareme'; // un seul mode possible
		}
		$currenttab = 'bareme';
        $occurrence_object->tabs($mode, $currenttab);
        $icon = $OUTPUT->pix_url('icon','referentiel');
		echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('newbaremes','referentiel').' '.$OUTPUT->help_icon('baremeh','referentiel').'</h2></div>'."\n";
		echo $OUTPUT->box_start('generalbox  boxaligncenter');
		if (!empty($baremeid) && ($mode=='reeditbareme') &&  confirm_sesskey()){
			// DEBUG
		    // echo "<br>DEBUG :: $scaleid selected\n";
			if ($rec_bareme=$DB->get_record('referentiel_scale', array('id'=>$baremeid))){
        		// print_object($rec_bareme);
            	referentiel_affiche_bareme($rec_bareme);
            	// formulaire
                if ($bareme_form = new bareme_form(null, array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrence_object->referentiel->id, 'bareme'=>$rec_bareme, 'mode'=>$mode, 'options'=>array('pass'=>$pass,'details'=>1)))){
					if ($bareme_form->is_cancelled()) {
    					// Cancelled forms redirect to the view main page.
    					redirect($baseurl);
						die();
					} else if ($formdata=$bareme_form->get_data()) {
    					// We need to add code to appropriately act on and store the submitted data
						//DEBUG
						// print_object($formdata);
	    				referentiel_set_bareme($formdata);
						//exit;
						$baseurl = new moodle_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$formdata->pass));
	    				redirect($baseurl);
               			die();
					}
					else{
						echo $OUTPUT->header();
						$occurrence_object->tabs($mode, $currenttab);
						$bareme_form->display();
						echo $OUTPUT->footer();
					}
                }
			    echo $OUTPUT->box_end();
               	echo $OUTPUT->footer();
				die();
   			}

            	if (!empty($scaleid)){
							if (($mode=='editbareme') &&  confirm_sesskey()){
                				// DEBUG
                				// echo "<br>DEBUG :: $scaleid selected\n";
								if ($scale=$DB->get_record('scale', array('id'=>$scaleid))){
        							// print_object($scale);
        							if ($rec_bareme=referentiel_scale_2_bareme($scale)){
        								// print_object($rec_bareme);
            							referentiel_affiche_bareme($rec_bareme);
            							// A TERMINER
							 			referentiel_modifier_bareme($mode, $course->id, $cm->id, $rec_bareme, $occurrence_object->referentiel->id);
					        			echo $OUTPUT->box_end();
                    					echo $OUTPUT->footer();
    									die();
						        	}
    							}
			    			}
							else if (($mode=='echangebareme') && confirm_sesskey()){
			    				if ($confirm = optional_param('confirm',0,PARAM_INT)) {
  								referentiel_echange_bareme_occurrence($scaleid, $occurrence_object->referentiel->id);
  							}
		   		    	}
    					else if ($mode=='selectbareme'){
		        		    echo $OUTPUT->confirm(get_string('confirmexchange','referentiel').' '.$OUTPUT->help_icon('baremeechangeh','referentiel'),
	'bareme.php?id='.$cm->id.'&amp;scaleid='.$scaleid.'&amp;mode=echangebareme&amp;confirm=1&amp;sesskey='.sesskey(),
	'bareme.php?id='.$cm->id.'&amp;mode=bareme&amp;confirm=0&amp;sesskey='.sesskey());
							echo $OUTPUT->box_end();
                    		echo $OUTPUT->footer();
    						die();
            			}
						else if (($mode=='confirmdeletebareme') && confirm_sesskey()){
    						if ($confirm = optional_param('confirm',0,PARAM_INT)) {
  								referentiel_delete_bareme_occurrence($scaleid, $occurrence_object->referentiel->id);
		  					}
   				    	}
    					else if ($mode=='deletebareme'){
		            		echo $OUTPUT->confirm(get_string('confirmdelete','referentiel').' '.$OUTPUT->help_icon('deletescaleh','referentiel'),
    'bareme.php?id='.$cm->id.'&amp;scaleid='.$scaleid.'&amp;mode=confirmdeletebareme&amp;confirm=1&amp;sesskey='.sesskey(),
	'bareme.php?id='.$cm->id.'&amp;mode=bareme&amp;confirm=0&amp;sesskey='.sesskey());
							echo $OUTPUT->box_end();
        		            echo $OUTPUT->footer();
    						die();
            			}
					}
					echo $OUTPUT->footer();
				}
			}


?>
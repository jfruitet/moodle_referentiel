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
require_once('lib.php');
require_once('bareme_lib.php');
require_once('bareme_class.php');
require_once('bareme_form.php');

require_once($CFG->dirroot.'/mod/referentiel/lib_bareme.php');
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';
// require_once('pass_form.php');  // mot de passe pour acceder a l'edition


// <a href="http://localhost/moodle253/blocks/referentiel/bareme.php?blockid=18&amp;courseid=2&amp;occurrenceid=1&amp;scaleid=1&amp;mode=editbareme&amp;sesskey=zy5BMQOC1z">


// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

// Next look for optional variables.
$occurrenceid = optional_param('occurrenceid', 0, PARAM_INT);
$mode  = optional_param('mode', 'bareme', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')
// pass active : non
// $pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
// $checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni
$action = optional_param('action','', PARAM_ALPHANUMEXT);
$scaleid    = optional_param('scaleid', 0, PARAM_INT);    // scale id
$baremeid    = optional_param('baremeid', 0, PARAM_INT);    // scale id

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_referentiel', $courseid);
}

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
// url avec pass active
// $viewurl = new moodle_url('/blocks/referentiel/view.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$pass));
// $baseurl = new moodle_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$pass));
// url avec pass desactive
$viewurl = new moodle_url('/blocks/referentiel/view.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid));
$baseurl = new moodle_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid));

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
	//$PAGE->set_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid' => $courseid, 'occurrenceid' => $occurrenceid, 'pass' => $pass  ));
    $PAGE->set_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid' => $courseid, 'occurrenceid' => $occurrenceid));
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
	/// Parametre des onglets
	$currenttab = 'bareme';
    $icon = $OUTPUT->pix_url('icon','referentiel');

	if (!empty($baremeid)){
		$params=array("blockid"=>$blockid, "courseid"=>$courseid, "occurrenceid"=>$occurrenceid, "baremeid"=>$baremeid);
		$rec_bareme = new bareme_class($params);
		// DEBUG
		//echo "<br />DEBUG :: bareme.php :: 108 ::\n";
       	//print_object($rec_bareme);
		//exit;
		if (!empty($rec_bareme)){
			if ($mode=='reeditbareme'){
			// DEBUG
       		// print_object($rec_bareme);
           	// referentiel_affiche_bareme($rec_bareme);
			// verifer si le mot de passe est fourni

           	    // formulaire
                //if ($bareme_form = new bareme_form(null, array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrence_object->referentiel->id, 'bareme'=>$rec_bareme->bareme, 'mode'=>$mode, 'options'=>array('pass'=>$pass,'details'=>1)))){
                if ($bareme_form = new bareme_form(null, array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrence_object->referentiel->id, 'bareme'=>$rec_bareme->bareme, 'mode'=>$mode, 'options'=>array('details'=>1)))){
				    if ($bareme_form->is_cancelled()) {
   					    // Cancelled forms redirect to the view main page.
   					    redirect($baseurl);
					   die();
				    }
					else if ($formdata=$bareme_form->get_data()) {
                        referentiel_set_bareme($formdata);
                        //exit;
                        // $baseurl = new moodle_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$formdata->pass));
                        $baseurl = new moodle_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid));
                        redirect($baseurl);
                        die();
                    }
				    else{
                        echo $OUTPUT->header();
                        $occurrence_object->tabs($mode, $currenttab);
                        $rec_bareme->affiche();
                        $bareme_form->display();
                        echo $OUTPUT->footer();
                        die();
                    }
                }
            }
            else if ($mode=='selectbareme'){
				echo $OUTPUT->header();
    			$occurrence_object->tabs($mode, $currenttab);
				echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('newbaremes','referentiel').' '.$OUTPUT->help_icon('baremeh','referentiel').'</h2></div>'."\n";
				echo $OUTPUT->box_start('generalbox  boxaligncenter');
       		    echo $OUTPUT->confirm(get_string('confirmexchange','referentiel').' '.$OUTPUT->help_icon('baremeechangeh','referentiel'),
	'bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;baremeid='.$baremeid.'&amp;mode=echangebareme&amp;confirm=1&amp;sesskey='.sesskey(),
	'bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;mode=bareme&amp;confirm=0&amp;sesskey='.sesskey());
				echo $OUTPUT->box_end();
           		echo $OUTPUT->footer();
				die();
            }
            else if (($mode=='echangebareme') && confirm_sesskey()){
   				if ($confirm = optional_param('confirm',0,PARAM_INT)) {
					referentiel_echange_bareme_occurrence($baremeid, $occurrence_object->referentiel->id);
				}
            }
            else if (($mode=='confirmdeletebareme') && confirm_sesskey()){
				if ($confirm = optional_param('confirm',0,PARAM_INT)) {
					referentiel_delete_bareme_occurrence($baremeid, $occurrence_object->referentiel->id);
				}
            }
            else if ($mode=='deletebareme'){
				echo $OUTPUT->header();
    			$occurrence_object->tabs($mode, $currenttab);
				echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('newbaremes','referentiel').' '.$OUTPUT->help_icon('baremeh','referentiel').'</h2></div>'."\n";
				echo $OUTPUT->box_start('generalbox  boxaligncenter');
           		echo $OUTPUT->confirm(get_string('confirmdelete','referentiel').' '.$OUTPUT->help_icon('deletescaleh','referentiel'),
 'bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;baremeid='.$baremeid.'&amp;mode=confirmdeletebareme&amp;confirm=1&amp;sesskey='.sesskey(),
 'bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;mode=bareme&amp;confirm=0&amp;sesskey='.sesskey());
				echo $OUTPUT->box_end();
	            echo $OUTPUT->footer();
				die();
            }
        }
	}

	if (!empty($scaleid)){
    	$params=array("blockid"=>$blockid, "courseid"=>$courseid, "occurrenceid"=>$occurrenceid, "baremeid"=>0, "scaleid"=>$scaleid);
		$rec_bareme = new bareme_class($params);
		if (($mode=='editbareme') &&  confirm_sesskey()){
			// DEBUG
			//echo "<br />DEBUG :: bareme.php :: 251 :: COURSEID:$course->id, OccurrenceID: $occurrenceid, SCALEID:$scaleid, MODE:$mode<br />BAREM<br />\n";
            //print_object($rec_bareme);
/*
bareme_class Object
(
    [occurrenceid] => 1
    [courseid] => 2
    [blockid] => 18
    [baremeid] =>
    [bareme] => object Object
        (
            [scaleid] => 1
            [name] => LOMER
            [scale] => NA, EA, A, E
            [maxscale] => 3
            [threshold] => 2
            [description] => <p>NA : Non acquis</p>
<p>EA : En cours d'acquisition</p>
<p>A : Acquis</p>
<p>E : Excellent</p>
            [descriptionformat] => 1
            [icons] =>
            [labels] => NA, EA, A, E
            [timemodified] => 1396131366
        )

)
*/
        	if (!empty($rec_bareme)){
	            // $rec_bareme->affiche();
				//exit;
        	    // formulaire
        		$bareme_form = new bareme_form(null, array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrence_object->referentiel->id, 'bareme'=>$rec_bareme->bareme, 'mode'=>$mode, 'options'=>array('details'=>1)));
	            //echo "<br />DEBUG :: bareme.php :: 261 :: BAREME_FORM<br />\n";
    	        //print_object($bareme_form);
				//exit;
				if ($bareme_form){
					if ($bareme_form->is_cancelled()) {
   						// Cancelled forms redirect to the view main page.
   						redirect($baseurl);
            	        die();
					}
    	            else if ($formdata=$bareme_form->get_data()) {
        	        	//echo "<br />DEBUG :: bareme.php :: 271 :: BAREME FORMDATA<br />\n";
            	    	//print_object($formdata);
						//exit;
						referentiel_set_scale_2_bareme($formdata, $rec_bareme->bareme);
	                    // $baseurl = new moodle_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid, 'pass'=>$formdata->pass));
    	                $baseurl = new moodle_url('/blocks/referentiel/bareme.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid));
        	            redirect($baseurl);
           				die();
					}
	                else{
        			    // print_object($rec_bareme);
                        echo $OUTPUT->header();
					    $occurrence_object->tabs($mode, $currenttab);
					    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('newbaremes','referentiel').' '.$OUTPUT->help_icon('baremeh','referentiel').'</h2></div>'."\n";
			            echo $OUTPUT->box_start('generalbox  boxaligncenter');
                        $rec_bareme->affiche();
				        $bareme_form->display();
				        echo $OUTPUT->box_end();
				        echo $OUTPUT->footer();
        				die();
					}
	    		}
            }
   		}  			
	}

	echo $OUTPUT->header();
    $occurrence_object->tabs($mode, $currenttab);
	echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('newbaremes','referentiel').' '.$OUTPUT->help_icon('baremeh','referentiel').'</h2></div>'."\n";
	echo $OUTPUT->box_start('generalbox  boxaligncenter');
	$idbareme=referentiel_bareme_occurrence($blockid, $courseid, $occurrence_object->referentiel, $role);
	echo "<br />\n";
    referentiel_autres_baremes($blockid, $courseid, $occurrence_object->referentiel->id, $role, $idbareme, true);
    referentiel_display_scales($blockid, $courseid, $occurrence_object->referentiel->id, $role);
    echo $OUTPUT->box_end();
	echo $OUTPUT->footer();

?>
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

/**
 * Forms.
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once("{$CFG->libdir}/formslib.php");

class pass_form extends moodleform {
	var $occurrenceid;
	var $blockid;
	var $courseid;

    function definition() {
		global $USER;
		$mform = & $this->_form;
        $arguments = $this->_customdata;  // call new form

        // arguments
        if (isset($arguments['occurrenceid'])){
            $this->occurrenceid=$arguments['occurrenceid'];
        }
        else{
            $this->occurrenceid=0;
        }

        if (isset($arguments['blockid'])){
            $this->blockid=$arguments['blockid'];
        }
        else{
            $this->blockid=0;
        }

        if (isset($arguments['courseid'])){
            $this->courseid=$arguments['courseid'];
        }
        else{
            $this->courseid=0;
        }

		// Options
        if (isset($arguments['options'])){
            $options=$arguments['options'];
        }
        else{
            $option=NULL;
        }

		$pass=0;
        if (isset($options['pass'])){
            $pass=$options['pass'];
        }

        $isadmin=0;
        if (isset($options['isadmin'])){
            $isadmin=$options['isadmin'];
        }
        $isauthor=0;
        if (isset($options['isauthor'])){
            $isauthor=$options['isauthor'];
        }

        // add group for text areas
	    $mform->addElement('header','displayinfo', get_string('pass', 'block_referentiel'));
   		$mform->addHelpButton('displayinfo', 'passoccurrenceh','block_referentiel');

		if ($isadmin || $isauthor) {
                $mform->addElement('passwordunmask', 'pass_referentiel', get_string('pass_referentiel_admin','referentiel'),  array('size'=>'20'));
				$mform->setType('pass_referentiel', PARAM_TEXT);
				//$mform->addRule('pass_referentiel', null, 'alphanumeric', null, 'client');
				$mform->setDefault('pass_referentiel', '');
                $mform->addElement('html','<br /><i>'.get_string('existe_pass_referentiel','referentiel').'</i>');
                $mform->addElement('hidden', 'force_pass', $USER->id);
            	$mform->setType('force_pass', PARAM_INT);
// Ajout JF 20151212
                $mform->addElement('checkbox', 'pass_delete', get_string('passdelete','referentiel'));
				$mform->setType('pass_delete', PARAM_INT);
				$mform->setDefault('pass_delete', 0);
	    }
   		else{
                $mform->addElement('password', 'pass_referentiel', get_string('pass_referentiel', 'referentiel'), array('size'=>'20'));
    	    	$mform->setType('pass_referentiel', PARAM_TEXT);
				$mform->addRule('pass_referentiel', null, 'required', null, 'client');
				$mform->setDefault('pass_referentiel', '');
                $mform->addElement('html','<br /><i>'.get_string('check_pass_referentiel','referentiel').'</i>');
	    }

        $mform->addElement('hidden', 'old_pass_referentiel', $pass);
       	$mform->setType('old_pass_referentiel', PARAM_TEXT);
        $mform->addElement('hidden', 'checkpass','checkpass');
        $mform->setType('checkpass', PARAM_TEXT);
		$mform->addElement('hidden', 'blockid', $this->blockid);
        $mform->setType('blockid', PARAM_INT);
		$mform->addElement('hidden', 'courseid', $this->courseid);
        $mform->setType('courseid', PARAM_INT);
		$mform->addElement('hidden', 'occurrenceid', $this->occurrenceid);
        $mform->setType('occurrenceid', PARAM_INT);
//        $mform->addElement('hidden', 'sesskey', sesskey());
//        $mform->setType('sesskey', PARAM_TEXT);

		// buttons
       	$this->add_action_buttons(true);
	}
}




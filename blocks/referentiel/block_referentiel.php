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
 * Block class .
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (is_file($CFG->dirroot.'/mod/referentiel/lib.php')) {
    require_once($CFG->dirroot.'/mod/referentiel/lib.php');
    define('REFERENTIEL_BLOCK_LIB_IS_OK', true);
}

class block_referentiel extends block_list {
    function init() {
        $this->title = get_string('referentiel', 'block_referentiel');
    }

    function applicable_formats() {
        return array('site' => true, 'course' => true);
    }

	public function instance_allow_multiple() {
		return false;
	}

	public function has_config() {
		return false;
	}


	/**
	 * gets the referentiel instances from table referentiel.
	 * this is used to show the referentiel block
	 * all referentiels with the courseid are listed
 	 *
	 * @global object
	 * @param int $courseid
	 * @return array the referentiel instance-records
 	 */
	function get_occurrences() {
    global $DB;

    	// Get all global occurrences
    	$sql = "SELECT o.id AS id,
                   o.name AS name,
				   o.local as local,
                   o.code_referentiel AS code
            FROM {referentiel_referentiel} o  ORDER BY o.local ASC, o.code_referentiel ASC
";

    	if (!$occurrences = $DB->get_records_sql($sql, array())) {
        	$occurrences = array();
    	}

    	return $occurrences;

	}

	/**
	 * gets the referentiel instances from table referentiel.
	 * this is used to show the referentiel block
	 * all referentiels with the courseid are listed
 	 *
	 * @global object
	 * @param int $courseid
	 * @return array the referentiel instance-records
 	 */
	function get_instances_from_course($courseid) {
    global $DB;

    	// Get all instances listed with named courseid
    	$sql = "SELECT i.id AS id,
                   cm.id AS cmid,
                   i.name AS name,
                   i.date_instance AS date,
				   i.ref_referentiel AS refrefid
            FROM {referentiel} i, {course_modules} cm, {modules} m
            WHERE i.id = cm.instance
                   AND i.course =:courseid
                   AND m.id = cm.module
                   AND m.name = 'referentiel'
";

    	if (!$instances = $DB->get_records_sql($sql, array("courseid" => $courseid))) {
        	$instances = array();
    	}

    	return $instances;

	}


	// -------------------------
	function roles_in_block(){
		global $COURSE;
    	$role= new stdClass();
        $role->can_edit = false;
        $role->cal_list = true;
        if ($context = context_course::instance($COURSE->id)){
        	$role->can_edit = has_capability('mod/referentiel:writereferentiel', $context);
        	$role->can_list = has_capability('mod/referentiel:write', $context);
    	}
    	return $role;
	}


	// -------------------------
    function get_content() {
        global $CFG, $OUTPUT, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (!defined('REFERENTIEL_BLOCK_LIB_IS_OK')) {
            $this->content->items = array(get_string('missing_referentiel_module', 'block_referentiel'));
            return $this->content;
        }

        $icon = '<img src="'.$OUTPUT->pix_url('icon', 'referentiel') . '" class="icon" alt="Referentiel icon" title="Referentiel icon"/>';

        $courseid = $this->page->course->id;
        if ($courseid <= 0) {
            $courseid = SITEID;
        }

        $roles=$this->roles_in_block();

		if (empty($this->instance->pageid)) {
        	$this->instance->pageid = SITEID;
        }

        $occurrences = $this->get_occurrences();
        // $referentiels = $this->get_instances_from_course($courseid);
		if ($roles->can_list){
            $this->content->items[] = '<b>'.get_string('displayoccurrence', 'block_referentiel').'</b>'."\n";
			// instances de referentuiels du cours
        	if ($occurrences)
			{
            	$baseurl = new moodle_url('/blocks/referentiel/view.php');
            	foreach ($occurrences as $occurrence) {
                	$url = new moodle_url($baseurl);
      				$url->params(array('blockid'=>$this->instance->id, 'courseid'=>$COURSE->id, 'occurrenceid'=>$occurrence->id));
                	$this->content->items[] = '<a href="'.$url->out().'">'.$icon.$occurrence->code.'</a>';
  	            }
    	    }
		}

		if ($roles->can_edit){
            // $this->content->items[] = '<b>'.get_string('editoccurrence', 'block_referentiel').'</b>'."\n";
            $url = new moodle_url('/blocks/referentiel/add.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
    		$this->content->footer = html_writer::link($url, get_string('addoccurrence', 'block_referentiel'));
            $url = new moodle_url('/blocks/referentiel/import.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
    		$this->content->footer .= '<br />'.html_writer::link($url, get_string('import', 'block_referentiel'));
		}
		/*
		else{
            $this->content->items[] = '<b>'.get_string('instances', 'block_referentiel').'</b>'."\n";
			// instances de referentiels du cours
        	if ($referentiels)
			{
            	$baseurl = new moodle_url('/mod/referentiel/view.php');
            	foreach ($referentiels as $referentiel) {
                	$url = new moodle_url($baseurl);
                	$url->params(array('id'=>$referentiel->cmid, 'courseid'=>$courseid));
                	$this->content->items[] = '<a href="'.$url->out().'">'.$icon.$referentiel->name.'</a>';
	            }
    	    }
		}
		*/
        return $this->content;
    }
}

?>
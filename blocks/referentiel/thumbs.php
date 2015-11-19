<?php  // $Id: tabs.php,v 1.24.2.5 2007/09/24 17:15:31 skodak Exp $
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
 * Standard base class .
 *
 * @package   block-referentiel
 * @copyright 2011 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Thumbs {


var $courseid; // course id
var $occurrenceid; // occurrence id
var $currenttab; // active table
var $mode;
var $can_edit;

function Thumbs($occurrenceid, $blockid, $courseid, $currenttab='list', $mode='list', $can_edit=0) {

    if (empty($currenttab)) {
        $this->currenttab = 'referentiel';
    }
    else{
        $this->currenttab = $currenttab;
    }

    if (!empty($occurrenceid)) {$this->occurrenceid=$occurrenceid;}
    if (!empty($blockid)) {$this->blockid=$blockid;}
    if (!empty($courseid)) {$this->courseid=$courseid;}
    if (!empty($can_edit)) {$this->can_edit=$can_edit;}

    $this->mode=$mode;

}

// --------------------
function display(){
    global $USER;
    global $CFG;
    
    if ( !empty($this->courseid)) {

        $tabs = array();
        $row  = array();
        $inactive = NULL;
        $activetwo = NULL;

        $url_param=array('occurrenceid'=>$this->occurrenceid, 'blockid'=>$this->blockid, 'courseid'=>$this->courseid, 'sesskey'=>sesskey());

        // premier onglet
		$row[] = new tabobject('referentiel',  new moodle_url('/blocks/referentiel/view.php', $url_param), get_string('referentiel','referentiel'));

        // Sous onglets
        if (isset($this->currenttab) &&
				(
				($this->currenttab == 'config')
                || ($this->currenttab == 'bareme')
                || ($this->currenttab == 'protocole')
                || ($this->currenttab == 'referentiel') || ($this->currenttab == 'list')
                || ($this->currenttab == 'edit') || ($this->currenttab == 'delete')
                || ($this->currenttab == 'export')
				)
				)
        	{
		        $row  = array();
                $inactive[] = 'referentiel';
       			$url_param['mode']='listreferentiel';
                $row[] =new tabobject('list', new moodle_url('/blocks/referentiel/view.php', $url_param),  get_string('listreferentiel','referentiel'));
				if (!empty($CFG->referentiel_use_scale)){
					$url_param['mode']='bareme';
                    $row[] =new tabobject('bareme', new moodle_url('/blocks/referentiel/bareme.php', $url_param), get_string('scale'));
                }

                $url_param['mode']='protocole';
                $row[] =new tabobject('protocole', new moodle_url('/blocks/referentiel/protocole.php', $url_param), get_string('protocole','referentiel'));

                if ($this->can_edit){
					$url_param['mode']='config';
                    $row[] =new tabobject('config', new moodle_url('/blocks/referentiel/config.php', $url_param), get_string('config','block_referentiel'));
            	    $url_param['mode']='edit';
                    $row[] =new tabobject('edit', new moodle_url('/blocks/referentiel/edit.php', $url_param), get_string('editreferentiel','referentiel'));
    	    	    $url_param['mode']='delete';
                    $row[] =new tabobject('delete', new moodle_url('/blocks/referentiel/delete.php', $url_param), get_string('deletereferentiel','referentiel'));
		    	    $url_param['mode']='export';
    		        $row[] =new tabobject('export', new moodle_url('/blocks/referentiel/export.php', $url_param), get_string('export','referentiel'));
                }

        	    $tabs[] = $row;
		        $activetwo = array('referentiel');
        }
        /// Print out the tabs and continue!
        print_tabs($tabs, $this->currenttab, $inactive, $activetwo);
    }
}

}
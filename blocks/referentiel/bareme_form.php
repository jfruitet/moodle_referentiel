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
require_once($CFG->dirroot.'/blocks/referentiel/lib.php');
require_once($CFG->dirroot.'/mod/referentiel/lib_bareme.php');


class bareme_form extends moodleform {
	var $context;
	var $occurrenceid;
	var $blockid;
	var $courseid;
	var $pass;
	var $bareme;
	var $mode;

    function definition() {
        $mform = & $this->_form;
        $arguments = $this->_customdata;  // call new form

        // arguments
        if (isset($arguments['options'])){
            $options=$arguments['options'];
        }
        else{
            $option=NULL;
        }

		if (isset($options['pass'])){
			$this->pass=$options['pass'];
		}
		else{
            $this->pass=1;
		}

        if (isset($options['details'])){
			$details=$options['details'];
		}
		else{
            $details=0;
		}


        if (isset($arguments['context'])){
            $this->context=$arguments['context'];
        }
        else{
            $this->context=NULL;
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

        if (isset($arguments['userid'])){
            $userid=$arguments['userid'];
        }
        else{
            $userid=0;
        }

        if (isset($arguments['occurrenceid'])){
            $this->occurrenceid=$arguments['occurrenceid'];
        }
        else{
            $this->occurrenceid=0;
        }

        if (isset($arguments['bareme'])){
            $this->bareme=$arguments['bareme'];
        }
        else{
            $this->bareme=NULL;
        }

        if (isset($arguments['mode'])){
            $this->mode=$arguments['mode'];
        }
        else{
            $this->mode='editbareme';
        }
		$mform->addElement('hidden', 'blockid', $this->blockid);
		$mform->setType('blockid', PARAM_INT);
		$mform->addElement('hidden', 'courseid', $this->courseid);
        $mform->setType('courseid', PARAM_INT);
		$mform->addElement('hidden', 'occurrenceid', $this->occurrenceid);
        $mform->setType('occurrenceid', PARAM_INT);
        $mform->addElement('hidden', 'mode', $this->mode);
        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->setType('sesskey', PARAM_TEXT);
        $mform->addElement('hidden', 'mode',  $this->mode);
		$mform->setType('mode', PARAM_ALPHA);
    	$mform->addElement('hidden', 'pass',  $this->pass);
		$mform->setType('pass', PARAM_INT);

        $this->edit_bareme($details);

		// buttons
        //$this->add_action_buttons(true);
		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
		$buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('revert'));
		$buttonarray[] = &$mform->createElement('cancel');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$mform->closeHeaderBefore('buttonar');
    }


	// ---------------------
	function edit_bareme($detail=false){
		global $OUTPUT, $CFG;
    	$sform='';
		$mform = & $this->_form;

	    if (!empty($this->bareme)){
        	// DEBUG
        	//echo "<br />DEBUG :: lib_bareme.php :: 208 :: BAREME UTILISE<br />\n";
        	//print_object($this->bareme);
        	//echo "<br />\n";
			$tscales=explode(',',$this->bareme->scale);
        	$ticons=explode(',',$this->bareme->icons);
        	$tlabels=explode(',',$this->bareme->labels);

			// add group for text areas
			$mform->addElement('header','baremeinfo', get_string('editbareme', 'block_referentiel'));
            //$mform->setAdvanced('baremeinfo');

   			$mform->addHelpButton('baremeinfo', 'baremeh','referentiel');
			//$sform='<h5>'.get_string('modif_bareme','referentiel').'</h5>';
			$sform='<div class="ref_saisie1">'."\n";
		    $mform->addElement('html', $sform);
			// bareme name.
			$mform->addElement('text', 'name', get_string('name'), array('size'=>'80', 'maxlength'=>255));
    	    $mform->setType('name', PARAM_TEXT);
			$mform->addRule('name', null, 'required', null, 'client');
			$mform->setDefault('name', $this->bareme->name);

			if ($detail){
				$sform='<span class="bold">'.get_string('description','referentiel').'</span>
<div class="ref_aff1">'.$this->bareme->description.'</div>
<span class="bold">'.get_string('grades').'</span>'.$this->bareme->scale."<br />\n";
				$mform->addElement('html', $sform);
			}

            $options = array();
        	while (list($key, $val) = each($tscales)) {
            	// echo "$key => $val\n";
            	if (isset($val)){
                    $options[$key]=$val;
				}
            }
            $attributes=array('size'=>'4');
   			$select = $mform->addElement('select', 'seuilid', get_string('aide_saisie_seuil','referentiel'), $options, $attributes);
            $select->setSelected($this->bareme->threshold);

	        reset($tscales);
			$sform='<span class="bold">'.get_string('aide_saisie_icones','referentiel').'</span>'."\n";
			$mform->addElement('html', $sform);
 			$i=0;
        	while (list($key, $val) = each($tscales)) {
            	//$sform="$key => $val\n";
                //$mform->addElement('html', $sform);

            	if (isset($val)){
                	if (!empty($ticons[$key])){
						$mform->addElement('text', 'iconscale_'.$i, $val, array('size'=>'60', 'maxlength'=>255));
			    	    $mform->setType('iconscale_'.$i, PARAM_RAW);
						$mform->addRule('iconscale_'.$i, null, 'required', null, 'client');
						$mform->setDefault('iconscale_'.$i,  $ticons[$key]);
                	}
                	else{
                    	if ($key<$this->bareme->threshold){
							$mform->addElement('text', 'iconscale_'.$i, get_string('label_rouge','referentiel').' '.$val, array('size'=>'60', 'maxlength'=>255));
				    	    $mform->setType('iconscale_'.$i, PARAM_RAW);
							$mform->addRule('iconscale_'.$i, null, 'required', null, 'client');
							$mform->setDefault('iconscale_'.$i, get_string('label_rouge','referentiel'));
                    	}
                    	else{
	                  		$mform->addElement('text', 'iconscale_'.$i, get_string('label_vert','referentiel').' '.$val, array('size'=>'60', 'maxlength'=>255));
				    	    $mform->setType('iconscale_'.$i, PARAM_RAW);
							$mform->addRule('iconscale_'.$i, null, 'required', null, 'client');
							$mform->setDefault('iconscale_'.$i, get_string('label_vert','referentiel'));
                    	}
                	}
					$i++;
            	}
        	}


        	if (($this->mode=='modifbareme') || ($this->mode=='editbareme')) {
            	$mform->setType('scaleid', PARAM_INT);
            	$mform->addElement('hidden', 'scaleid', $this->bareme->scaleid);
			}
			elseif (isset($this->bareme->id)){
            	$mform->setType('baremeid', PARAM_INT);
            	$mform->addElement('hidden', 'baremeid', $this->bareme->id);
			}

		}
	}

}   // end class
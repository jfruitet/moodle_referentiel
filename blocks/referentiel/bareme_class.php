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
 * Standard base class for bareme table.
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitete {@link http://www.univ-nantes.fr/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot.'/mod/referentiel/lib_bareme.php');

class bareme_class {


    var $occurrenceid; // L'occurrence
	var $courseid;
	var $blockid;
    var $baremeid;

    /**
     * Constructor for the base occurrence class
     *
     *
     * @global object
     * @param int $referentiel_referentiel id
     */
    function bareme_class($params) {
        global $COURSE, $DB;
        global $CFG;
        if (!empty($params)){
			if (!empty($params['blockid'])) {
            	$this->blockid=$params['blockid'];
       		}
			if (!empty($params['courseid'])) {
            	$this->courseid=$params['courseid'];
       		}

 			if (!empty($params['occurrenceid'])) {
                $this->occurrenceid=$params['occurrenceid'];
        	} else {
        		$this->occurrenceid=0;
        	}

            if (!empty($params['baremeid'])) {
                $baremeid=$params['baremeid'];
        	} else {
        		$baremeid=0;
        	}

            if (!empty($params['scaleid'])) {
                $scaleid=$params['scaleid'];
        	} else {
        		$scaleid=0;
        	}

			if (!empty($baremeid)){
				$this->bareme = $DB->get_record('referentiel_scale', array('id'=>$baremeid));
			}
			elseif (!empty($scaleid)){
				if ($scale=$DB->get_record('scale', array('id'=>$scaleid))){
        			// print_object($scale);
        			$this->bareme = referentiel_scale_2_bareme($scale);
				}
			}
		}
    }

    // ---------------------
	function affiche($out=false){
	global $OUTPUT;
	    $strscales	= get_string('newbaremes','referentiel');
    	$strscale	= get_string('newbareme','referentiel');
	    $strname	= get_string('name');
    	$strdescription= get_string('description');
	    $strthreshold = get_string('seuil','referentiel');

    	$table = new html_table();
	    $table->head  = array($strscale, $strdescription, $strthreshold);
    	$table->size  = array('20%', '70%', '10%');
    	$table->align = array('left', 'left', 'center');
    	$table->attributes['class'] = 'scaletable localscales generaltable';

    	$heading = $strscales;

    	$data = array();
    	$line = array();

    	if (!empty($this->bareme)){
            //print_object($this->bareme);
            $tscales=array();
            $ticons=array();
            $tlabels=array();
            $thresholdlabel=0;
            if ($tscales=explode(',',$this->bareme->scale)){
        	   $thresholdlabel=$tscales[$this->bareme->threshold];
            }
            $strgrades='';
            $strgrades='<div class="scale_options">'.str_replace(",",", ",$this->bareme->scale).'</div>';

       	    if ($this->bareme->labels){
                $tlabels=explode(',',$this->bareme->labels);
            }

            $stricons='<b>'.get_string('grades').'</b><br />';
            // $stricons=str_replace(",",", ",$this->bareme->icons);

            if ($this->bareme->icons){
                if ($ticons=explode(',',$this->bareme->icons)){
				    while (list($key, $val) = each($ticons)) {
           			  if ($val){
               			if ($key>=$this->bareme->threshold){
                   			$stricons.='<span class="valide">';
	                   	}
                    	else{
							$stricons.='<span class="invalide">';
						}
           				if (!empty($tlabels[$key])){
							$stricons.=$tlabels[$key].' ';
						}
						else{
							$stricons.=$tscales[$key].' ';
       	    			}
           	       		$stricons.='</span>';
           				$stricons.=' &nbsp; '.$val;
                   		$stricons.='<br />';
					}
				}
			}
            }

            $stricons='<div class="scale_options">'.$stricons.'</div>'."\n";

       	    $line[] = '<b>'.format_string($this->bareme->name).'</b>'.$strgrades.$stricons;
            $line[] = $this->bareme->description;
		    $line[] = $thresholdlabel;

            $data[] = $line;

    	}

    	$table->data  = $data;

    	$s=$OUTPUT->heading($strscale, 3, 'main');
    	$s.=html_writer::table($table);
     	if ($out){
		 	return $s;
		}
		else{
			echo $s;
		}
	}


} // class

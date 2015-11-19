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
 * Library.
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/mod/referentiel/lib.php');
require_once($CFG->dirroot.'/mod/referentiel/lib_referentiel.php');


//------------------
function referentiel_bareme_occurrence($blockid, $courseid, $occurrence, $roles){
global $DB;
global $OUTPUT;
global $CFG;
$strscale          = get_string('bareme_utilise','referentiel');
$strname           = get_string('name');
$stroccurrence     = get_string('occurrence','referentiel');
$strcustomscales   = get_string('scalescustom');
$strdescription= get_string('description');
$strthreshold = get_string('seuil','referentiel');
$strmenu = get_string('editscale','referentiel');

$table = new html_table();

        $params= array('occurrenceid'=>$occurrence->id);
        $sql="SELECT s.* FROM {referentiel_a_scale_ref} AS a, {referentiel_scale} AS s
          WHERE a.refscaleid = s.id
          AND a.refrefid=:occurrenceid ";
        if ($bareme=$DB->get_record_sql($sql, $params)){
            // DEBUG
            //echo "<br />DEBUG :: lib_bareme.php :: 195 :: BAREME UTILISE";
            //print_object($bareme);
            $heading = $strscale;

            $data = array();
            $line = array();
        	//print_object($bareme);
        	$tscales=array();
			$ticons=array();
			$tlabels=array();
        	$thresholdlabel=0;
        	if ($tscales=explode(',',$bareme->scale)){
        		$thresholdlabel=$tscales[$bareme->threshold];
        	}
			$strgrades='';
			$strgrades='<div class="scale_options">'.str_replace(",",", ",$bareme->scale).'</div>';

        	if ($bareme->labels){
				$tlabels=explode(',',$bareme->labels);
        	}

			$stricons='<b>'.get_string('grades').'</b><br />';
			// $stricons=str_replace(",",", ",$bareme->icons);

        	if ($bareme->icons){
				if ($ticons=explode(',',$bareme->icons)){
					while (list($key, $val) = each($ticons)) {
            			if ($val){
                			if ($key>=$bareme->threshold){
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

        	$line[] = '<b>'.format_string($bareme->name).'</b>'.$strgrades.$stricons;
		    $line[] = $bareme->description;
			$line[] = $thresholdlabel;
			$menu = "";
        	if ($roles->can_edit) {
            	//$menu.= '<a href="'.$CFG->wwwroot.'/blocks/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrence->id.'&amp;baremeid='.$bareme->id.'&amp;pass=1&amp;mode=reeditbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('edit','referentiel').'" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
            	//$menu.= '<br />'.'<a href="'.$CFG->wwwroot.'/blocks/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrence->id.'&amp;baremeid='.$bareme->id.'&amp;pass=1&amp;mode=deletebareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('deleteall','referentiel').'" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
            	$menu.= '<a href="'.$CFG->wwwroot.'/blocks/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrence->id.'&amp;baremeid='.$bareme->id.'&amp;mode=reeditbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('edit','referentiel').'" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
            	$menu.= '<br />'.'<a href="'.$CFG->wwwroot.'/blocks/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrence->id.'&amp;baremeid='.$bareme->id.'&amp;mode=deletebareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('deleteall','referentiel').'" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';

				$menu.= '<br />'.$OUTPUT->help_icon('deletescaleh','referentiel');
				$line[] = $menu;
        	}

            $data[] = $line;
		    if (!empty($menu)){
				$table->head  = array($strscale, $strdescription, $strthreshold, $strmenu);
		    	$table->size  = array('30%', '50%', '5%', '15%');
		    	$table->align = array('left', 'left', 'center', 'center');
			}
			else{
				$table->head  = array($strscale, $strdescription, $strthreshold);
		    	$table->size  = array('30%', '60%', '10%');
		    	$table->align = array('left', 'left', 'center');
		    }
			$table->attributes['class'] = 'scaletable localscales generaltable';
            $table->data  = $data;

            echo $OUTPUT->heading($strscale, 3, 'main');
            echo html_writer::table($table);
            return $bareme->id;
        }
        else{
			echo '<div align="center">'.get_string('no_scale','referentiel',$occurrence->code_referentiel).'</div>'."\n";
		}

    return 0;
}

// -----------------------
function referentiel_autres_baremes($blockid, $courseid, $occurrenceid, $roles, $idbaremeexclus=0, $selection=0){
// idbaremeexclus : celui à ne pas afficher
global $DB;
global $OUTPUT;
global $CFG;
	$ok=0;
    $strscales     = get_string('newotherbaremes','referentiel');
    $strscale      = get_string('newbareme','referentiel');
    $strname       = get_string('name');
    $strdescription= get_string('description');
    $strthreshold  = get_string('seuil','referentiel');
    $strselect         = get_string('select');


    $table = new html_table();
	if ($selection){
    	$table->head  = array($strscale, $strdescription, $strthreshold, $strselect);
    	$table->size  = array('20%', '60%', '10%', '10%');
    	$table->align = array('left', 'left', 'center', 'center');
	}
	else{
    	$table->head  = array($strscale, $strdescription, $strthreshold);
    	$table->size  = array('20%', '70%', '10%');
    	$table->align = array('left', 'left', 'center');
    }
	$table->attributes['class'] = 'scaletable localscales generaltable';

    $heading = $strscales;

    $data = array();
    $line = array();

    if ($baremes=$DB->get_records('referentiel_scale',NULL)){
        foreach ($baremes as $bareme){
        	if (empty($idbaremeexclus) || (!empty($idbaremeexclus) && ($idbaremeexclus!=$bareme->id))){
                $line = array();
				$ok++;
        		//print_object($bareme);
        		$tscales=array();
				$ticons=array();
				$tlabels=array();
        		$thresholdlabel=0;
        		if ($tscales=explode(',',$bareme->scale)){
        			$thresholdlabel=$tscales[$bareme->threshold];
        		}

				$strgrades='';
				$strgrades='<div class="scale_options">'.str_replace(",",", ",$bareme->scale).'</div>';

        		if ($bareme->labels){
					$tlabels=explode(',',$bareme->labels);
        		}

				$stricons='<b>'.get_string('grades').'</b><br />';
				// $stricons=str_replace(",",", ",$bareme->icons);

        		if ($bareme->icons){
					if ($ticons=explode(',',$bareme->icons)){
						while (list($key, $val) = each($ticons)) {
            				if ($val){
                				if ($key>=$bareme->threshold){
                    				$stricons.='<span class="valide">';
	                    		}
    	                		else{
									$stricons.='<span class="invalide">';
								}
            					if (!empty($tlabels) && !empty($tlabels[$key])){
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

	        	$line[] = '<b>'.format_string($bareme->name).'</b>'.$strgrades.$stricons;
			    $line[] = $bareme->description;
				$line[] = $thresholdlabel;
				if ($selection){
			        $menu = "";
        			if ($roles->can_edit) {
            			//$menu.= '<a href="'.$CFG->wwwroot.'/blocks/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;baremeid='.$bareme->id.'&amp;pass=1&amp;mode=selectbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('square_small','referentiel').'" alt="'.get_string('select').'" title="'.get_string('select').'" /></a>';
            			$menu.= '<a href="'.$CFG->wwwroot.'/blocks/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;baremeid='.$bareme->id.'&amp;mode=selectbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('square_small','referentiel').'" alt="'.get_string('select').'" title="'.get_string('select').'" /></a>';
				        $line[] = $menu;
        			}
				}
        		$data[] = $line;
    	    }
    	}
		if ($ok){
	    	$table->data  = $data;
    		echo $OUTPUT->heading($strscales.' '.$OUTPUT->help_icon('baremeechangeh','referentiel'), 3, 'main');
    		echo html_writer::table($table);
    	}
	}
	else{
		echo '<div align="center">'.get_string('no_scales','referentiel').'</div>'."\n";
	}

}

// -----------------------
function referentiel_display_scales($blockid, $courseid, $occurrenceid, $roles){
global $DB;
global $OUTPUT;
global $CFG;
$strscale          = get_string('scale');
$strstandardscale  = get_string('scalesstandard');
$strcustomscales   = get_string('scalescustom');
$srtcreatenewscale = get_string('scalescustomcreate');
$strname           = get_string('name');
$strselect         = get_string('select');
$strused           = get_string('scaleused','referentiel');


$table = new html_table();
$table2 = new html_table();
$heading = '';

if ($courseid and $scales = grade_scale::fetch_all_local($courseid)) {
    $heading = $strcustomscales;

    $data = array();
    foreach($scales as $scale) {
        $line = array();
        $line[] = format_string($scale->name).'<div class="scale_options">'.str_replace(",",", ",$scale->scale).'</div>';

        $used = $scale->is_used();
        $line[] = $used ? get_string('yes') : get_string('no');

        $menu = "";
        if ($roles->can_edit) {
            //$menu.= '<a href="'.$CFG->wwwroot.'/blocks/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;scaleid='.$scale->id.'&amp;pass=1&amp;mode=editbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('square_small','referentiel').'" alt="'.get_string('select').'" title="'.get_string('select').'" /></a>';
            $menu.= '<a href="'.$CFG->wwwroot.'/blocks/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;scaleid='.$scale->id.'&amp;mode=editbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('square_small','referentiel').'" alt="'.get_string('select').'" title="'.get_string('select').'" /></a>';
        }
        $line[] = $menu;
        $data[] = $line;
    }
    $table->head  = array($strscale, $strused, $strselect);
    $table->size  = array('60%', '30%', '10%');
    $table->align = array('left', 'center', 'center');
    $table->attributes['class'] = 'scaletable localscales generaltable';
    $table->data  = $data;

    //print_grade_page_head($courseid, 'scale', 'scale', get_string('coursescales', 'grades'));

    echo $OUTPUT->heading($strcustomscales.' '.$OUTPUT->help_icon('scalelocalh','referentiel'), 3, 'main');
	echo html_writer::table($table);

	echo '<div align="center"><br />'."\n";
	if ($roles->is_admin){
		$link=new moodle_url('/grade/edit/scale/index.php', NULL);
	}
	else if ($roles->is_teacher){
		$link=new moodle_url('/grade/edit/scale/index.php?id='.$courseid, NULL);
	}
	else{
		$link='';
	}
	if ($link){
		echo '<a href="'.$link.'">'.get_string('create_a_scale','referentiel').'</a>'."\n";
	}
	echo '</div>'."\n";	
}
else{
	echo '<div align="center">'.get_string('no_custom_scale','referentiel').'<br />'."\n";
	if ($roles->is_admin){
		$link=new moodle_url('/grade/edit/scale/index.php', NULL);
	}
	else if ($roles->is_teacher){
		$link=new moodle_url('/grade/edit/scale/index.php?id='.$courseid, NULL);
	}
	else{
		$link='';
	}
	if ($link){
		echo '<a href="'.$link.'">'.get_string('create_a_scale','referentiel').'</a>'."\n";
	}
	echo '</div>'."\n";
}


if ($scales = grade_scale::fetch_all_global()) {
    $heading = $strstandardscale;

    $data = array();
    foreach($scales as $scale) {
        $line = array();
        $line[] = format_string($scale->name).'<div class="scale_options">'.str_replace(",",", ",$scale->scale).'</div>';

        $used = $scale->is_used();
        $line[] = $used ? get_string('yes') : get_string('no');

        $menu = "";
        if ($roles->can_edit) {
            //$menu.= '<a href="'.$CFG->wwwroot.'/mod/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;scaleid='.$scale->id.'&amp;pass=1&amp;mode=editbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('square_small','referentiel').'" alt="'.get_string('select').'" title="'.get_string('select').'" /></a>';
            $menu.= '<a href="'.$CFG->wwwroot.'/blocks/referentiel/bareme.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrenceid.'&amp;scaleid='.$scale->id.'&amp;mode=editbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('square_small','referentiel').'" alt="'.get_string('select').'" title="'.get_string('select').'" /></a>';
        }
        $line[] = $menu;
        $data[] = $line;
    }
    $table2->head  = array($strscale, $strused, $strselect);
    $table->attributes['class'] = 'scaletable globalscales generaltable';
    $table2->size  = array('60%', '30%', '10%');
    $table2->align = array('left', 'center', 'center');
    $table2->data  = $data;

	echo $OUTPUT->heading($strstandardscale.' '.$OUTPUT->help_icon('scaleglobalh','referentiel'), 3, 'main');
	echo html_writer::table($table2);
	
	if ($roles->is_admin){
		echo '<div align="center">'."\n";
		$link=new moodle_url('/grade/edit/scale/index.php', NULL);
		echo '<br /><a href="'.$link.'">'.get_string('create_global_scale','referentiel').'</a>'."\n";
        echo '</div>'."\n";
	}
}
else{
	echo '<div align="center">'.get_string('no_global_scale','referentiel')."\n";
	if ($roles->is_admin){
		$link=new moodle_url('/grade/edit/scale/index.php', NULL);
		echo '<br /><a href="'.$link.'">'.get_string('create_global_scale','referentiel').'</a>'."\n";
	}
	echo '</div>'."\n";
}
}

?>

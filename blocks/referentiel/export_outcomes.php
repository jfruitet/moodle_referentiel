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
 * Interface.
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require(dirname(__FILE__) . '/../../config.php');
require_once('occurrence_class.php');
require_once('import_export_lib.php');	// IMPORT / EXPORT
require_once($CFG->dirroot.'/mod/referentiel/lib_bareme.php');	// Scales management

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

// Next look for optional variables.
$occurrenceid = optional_param('occurrenceid', 0, PARAM_INT);
$mode  = optional_param('mode', 'export', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')
$exportfilename = optional_param('exportfilename','',PARAM_FILE );


if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_referentiel', $courseid);
}


$contextcourse = context_course::instance($course->id);
$context = context_block::instance($blockid);

//$contextcourse = get_context_instance(CONTEXT_COURSE, $course->id);
//$context = get_context_instance(CONTEXT_BLOCK, $blockid);

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
if (empty($occurrenceid)){
	redirect(new moodle_url('/blocks/referentiel/block_referentiel.php', array()));
}
$viewurl = new moodle_url('/blocks/referentiel/view.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid));
if (empty($CFG->enableoutcomes)) {
	redirect($viewurl);
}

require_login($course);

$params=array("blockid"=>$blockid, "courseid"=>$courseid, "occurrenceid"=>$occurrenceid);
$occurrence_object = new occurrence($params);

require_capability('mod/referentiel:export', $context);

if (empty($exportfilename)) {
	$exportfilename = "outcomes_".referentiel_default_export_filename($occurrence_object->referentiel->code_referentiel).'.csv';
}

// $systemcontext = get_context_instance(CONTEXT_SYSTEM);

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=$exportfilename");

// sending header with clear names, to make 'what is what' as easy as possible to understand
$header = array('outcome_name', 'outcome_shortname', 'outcome_description', 'scale_name', 'scale_items', 'scale_description');
echo format_csv($header, ';', '"');
$outcomes = array();
$outcomes = referentiel_get_outcomes($occurrence_object->referentiel);
// scale used with these outcomes
$scale_info = referentiel_get_scale_info($occurrence_object->referentiel->id);

/*
outcome_name;outcome_shortname;outcome_description;scale_name;scale_items;scale_description;
C2i2e A.1.1;A.1.1;A.1.1 : Identifier les personnes ressources TIC et leurs rôles respectifs dans l'école ou l'établissement, et en dehors (circonscription, bassin, Académie, niveau national...) ;Item référentiel;Non acquis,En cours d'acquisition,Acquis;Ce barème est destiné à évaluer (noter) les items de compétences du module référentiel.
C2i2e A.1.2 	A.1.2 	A.1.2 S'approprier les différentes composantes informatiques (lieux, outils...) de son environnement professionnel 	Item référentiel	Non acquis,En cours d'acquisition,Acquis	Ce barème est destiné à évaluer (noter) les items de compétences du module référentiel.
*/

foreach($outcomes as $outcome) {
        $line = array();
  		// purger les caracteres separateurs
        $line[] = str_replace(';',',',$outcome->name);
        $line[] = str_replace(';',',',$outcome->shortname);
        $line[] = str_replace(';',',',$outcome->description);
		$line[] = $scale_info->name;
        $line[] = $scale_info->grades;
		$line[] = str_replace(';',' ',$scale_info->description);

        echo format_csv($line, ';', '"');
}

die();



/**
 * Formats and returns a line of data, in CSV format. This code
 * is from http://au2.php.net/manual/en/function.fputcsv.php#77866
 *
 * @params array-of-string $fields data to be exported
 * @params char $delimiter char to be used to separate fields
 * @params char $enclosure char used to enclose strings that contains newlines, spaces, tabs or the delimiter char itself
 * @returns string one line of csv data
 */
function format_csv($fields = array(), $delimiter = ';', $enclosure = '"') {
    $str = '';
    $escape_char = '\\';
    foreach ($fields as $value) {
        if (strpos($value, $delimiter) !== false ||
			strpos($value, $enclosure) !== false ||
        	strpos($value, "\n") !== false ||
            strpos($value, "\r") !== false ||
            strpos($value, "\t") !== false ||
            strpos($value, ' ') !== false) {
            $str2 = $enclosure;
            $escaped = 0;
            $len = strlen($value);
            for ($i=0;$i<$len;$i++) {
                if ($value[$i] == $escape_char) {
                    $escaped = 1;
                } else if (!$escaped && $value[$i] == $enclosure) {
                    $str2 .= $enclosure;
                }
                else {
                    $escaped = 0;
                }
                $str2 .= $value[$i];
            }
            $str2 .= $enclosure;
            $str .= $str2.$delimiter;
        }
        else {
            $str .= $value.$delimiter;
        }
    }
    $str = substr($str,0,-1);
    $str .= "\n";

    return $str;
}

/**
 * Gets Pository items and returns an array of outcomes
 * @params referentiel_referentiel record
 * @returns array of outcome objects
 */

function referentiel_get_outcomes($occurrence){
// genere les outcomes (objectifs) pour le module grades (notes) a partir des items du r?f?rentiel
	$outcomes=array();
	if ($occurrence){
		$code_referentiel = stripslashes($occurrence->code_referentiel);

		// charger les domaines associes au referentiel courant
		if (isset($occurrence->id) && ($occurrence->id>0)){
			// AFFICHER LA LISTE DES DOMAINES
			$compteur_domaine=0;
			$records_domaine = referentiel_get_domaines($occurrence->id);
	    	if ($records_domaine){
    			// afficher
				// DEBUG
				// echo "<br/>DEBUG ::<br />\n";
				// print_r($records_domaine);
				foreach ($records_domaine as $record){
					$compteur_domaine++;
        			$domaine_id=$record->id;
					$nb_competences = $record->nb_competences;
					$code_domaine = stripslashes($record->code_domaine);
					$description_domaine = stripslashes($record->description_domaine);
					$num_domaine = $record->num_domaine;

					// LISTE DES COMPETENCES DE CE DOMAINE
					$compteur_competence=0;
					$records_competences = referentiel_get_competences($domaine_id);
			    	if ($records_competences){
						// DEBUG
						// echo "<br/>DEBUG :: COMPETENCES <br />\n";
						// print_r($records_competences);
						foreach ($records_competences as $record_c){
							$compteur_competence++;
        					$competence_id=$record_c->id;
							$nb_item_competences = $record_c->nb_item_competences;
							$code_competence = stripslashes($record_c->code_competence);
							$description_competence = stripslashes($record_c->description_competence);
							$num_competence = $record_c->num_competence;
							$ref_domaine = $record_c->ref_domaine;

							// ITEM
							$compteur_item=0;
							$records_items = referentiel_get_item_competences($competence_id);

                            if ($records_items){
								// DEBUG
								// echo "<br/>DEBUG :: ITEMS <br />\n";
								// print_r($records_items);

								foreach ($records_items as $record_i){
									$compteur_item++;
                                    $item_id=$record_i->id;
									$code_item = stripslashes($record_i->code_item);
									$description_item = stripslashes($record_i->description_item);
									$num_item = $record_i->num_item;
									$type_item = stripslashes($record_i->type_item);
									$poids_item = $record_i->poids_item;
									$empreinte_item = $record_i->empreinte_item;
									$ref_competence=$record_i->ref_competence;
									if (strlen($description_item)<=60){
                                        $desc_item=$description_item;
                                    }
                                    else{
                                        $desc_item=mb_substr($description_item,0,60);
                                        $desc_item=mb_substr($desc_item, 0, strrpos($desc_item," "));
                                        $desc_item.=' (...)';
                                    }
                                    $outcome= new object();
                                    $outcome->name=$code_referentiel.' '.$code_item.' :: '.$desc_item;
                                    $outcome->shortname=$code_item;
                                    $outcome->description=$description_item;

                                    $outcomes[]=$outcome;
								}
							}

						}
					}
				}
			}
		}
	}
	return $outcomes;
}


// -----------------------------
function referentiel_get_scale_info($occurrenceid){
global $CFG;
global $DB;
    $scale_info = new Object();
	// Default values
	$scale_info->name = get_string('nom_bareme','referentiel');
    $scale_info->grades = get_string('bareme','referentiel');
	$scale_info->description = get_string('description_bareme','referentiel');

	if ($CFG->referentiel_use_scale){
        if ($baremeid=referentiel_get_bareme_id_occurrence($occurrenceid)){
			if ($sbareme=$DB->get_record('referentiel_scale', array('id'=>$baremeid))){
				if ($scale=$DB->get_record('scale', array('id'=>$sbareme->scaleid))){
					// DEBUG
					// echo "<br />export_grade_outcomes.php :: 296 :: SCALE<br />\n";
					// print_object($scale);
					$scale_info->name = $scale->name;
			    	$scale_info->grades = $scale->scale;
					$scale_info->description = mb_substr(strip_tags($scale->description),0,60);
                    $scale_info->description = mb_substr($scale_info->description, 0, strrpos($scale_info->description," "));
                    $scale_info->description .= ' (...)';
				}
    		}
		}
	}
	return $scale_info;
}
?>
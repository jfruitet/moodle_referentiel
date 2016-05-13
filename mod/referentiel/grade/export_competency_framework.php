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
 * @package   mod_referentiel
 * @copyright 2016 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . "/../../../config.php");
require_once('../lib.php');
require_once('../import_export_lib.php');	// IMPORT / EXPORT
require_once('../lib_bareme.php');	// Scales management

// Check for all required variables.
$id = optional_param('id', 0, PARAM_INT);
$d = optional_param('d', 0, PARAM_INT);

// Next look for optional variables.
$occurrenceid = optional_param('occurrenceid', 0, PARAM_INT);
$mode  = optional_param('mode', 'export', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')
$exportfilename = optional_param('exportfilename','',PARAM_FILE );

$url = new moodle_url('/mod/referentiel/grade/export_competency_framewor.php');

if ($d) {     // referentiel->id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Occurrence id is incorrect');
        }

		if (! $course = $DB->get_record("course", array("id" => "$referentiel->course"))) {
	            print_error('Course is misconfigured');
    	}

		if (! $cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $course->id)) {
    	        print_error('Course Module ID is incorrect');
		}
		$url->param('d', $d);
}
elseif ($id) {
        if (! $cm = get_coursemodule_from_id('referentiel', $id)) {
        	print_error('Course Module ID was incorrect');
        }
        if (! $course = $DB->get_record("course", array("id" => "$cm->course"))) {
            print_error('Course is misconfigured');
        }
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$cm->instance"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Referentiel is incorrect');
        }
        $url->param('id', $id);
}
else{
    // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : grade/export_competency_framework.php'), 'referentiel');
}

require_login($course->id, false, $cm);

if (!isloggedin() or isguestuser()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
}


// check role capability
$context = context_module::instance($cm->id);
require_capability('mod/referentiel:export', $context);


if (empty($exportfilename)) {
	$exportfilename = "compfrwrk_".referentiel_export_filename($referentiel_referentiel->code_referentiel,'comma_separated').'.csv';
}


header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=$exportfilename");

// sending header with clear names, to make 'what is what' as easy as possible to understand
//$header = array('outcome_name', 'outcome_shortname', 'outcome_description', 'scale_name', 'scale_items', 'scale_description');
$header = array("Parent id number","Id number",'Shortname','Description',"Description format","Scale values","Scale configuration","Rule type (optional)","Rule outcome (optional)","Rule config (optional)","Cross referenced competency id numbers","Exported id (optional)","Is framework",'Taxonomy');

echo format_csv($header, ',', '"');

// scale used with these outcomes
$scale_info = referentiel_get_scale_info($referentiel_referentiel->id);

$outcomes = array();
$outcomes = referentiel_get_competencies($referentiel_referentiel, $scale_info);

/*
"Parent id number","Id number",Shortname,Description,"Description format","Scale values","Scale configuration","Rule type (optional)","Rule outcome (optional)","Rule config (optional)","Cross referenced competency id numbers","Exported id (optional)","Is framework",Taxonomy
,C2i0,C2i,"<p>Certification Internet<br></p>",1,"Non pertinent,Non validé,Validé","[{""scaleid"":""1""},{""id"":2,""scaledefault"":1,""proficient"":0},{""id"":3,""scaledefault"":0,""proficient"":1}]",,,,,,1,"domain,competency,skill,value"
,C2i0ET,"Environnement de travail","<p>Maîtrise de l'environnement de travail personnel<br /></p>",1,"Non pertinent,Non validé,Validé","[{""scaleid"":""1""},{""id"":2,""scaledefault"":1,""proficient"":0},{""id"":3,""scaledefault"":0,""proficient"":1}]","core_competency\competency_rule_points",2,"{""base"":{""points"":2},""competencies"":[{""id"":2,""points"":2,""required"":1}]}",,1,,
C2i0ET,C2i0ETPoste,"Gestion du poste de travail","<p>Je sais allumer, éteindre et entretenir mon poste de travail<br /></p>",1,,,"core_competency\competency_rule_points",2,"{""base"":{""points"":2},""competencies"":[{""id"":3,""points"":1,""required"":1},{""id"":4,""points"":1,""required"":1}]}",,2,,
C2i0ETPoste,C2i0ETPosteFichiers,"Gestion des fichiers","<p>Je sais organiser , déposer et retrouver mes données et fichiers<br></p>",1,,,,0,null,,3,,
C2i0ETPoste,C2i0ETGestionReseau,"Travail en réseau","<p>Je sais me connecter au réseau et échanger des fichiers<br></p>",1,,,,0,null,,4,,

*/

foreach($outcomes as $outcome) {
        $line = array();
  		// purger les caracteres separateurs qui seraint dans les textes

        $line[] = $outcome->parentid;          // 1
        $line[] = $outcome->idnumber;          // 2
        $line[] = $outcome->shortname;         // 3
        $line[] = $outcome->description;       // 4
        $line[] = 1; //  "Description format"     5
		$line[] = $outcome->scalevalue;        // 6
        $line[] = $outcome->scaleconfig; // scale configuration      7
        $line[] = ''; // Rule type (Optional)      8
		$line[] = ''; // Rule Outcome (Optional)  9
        $line[] = ''; // Rule config (optional) 10
		$line[] = ''; // Cross referenced id numbers 11
		$line[] = $outcome->exportedid; // Exported id (optional) 12
        $line[] = $outcome->isframework; // Is framework          13
        $line[] = $outcome->taxonomy;//  Taxonomy		14 trim(get_string($labeldomaine,"referentiel")).','.trim(get_string($labelcompetence,"referentiel")).','.trim(get_string($labelitem,"referentiel")); // Taxonomy

        echo format_csv($line, ',', '"');
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
function format_csv($fields = array(), $delimiter = ',', $enclosure = '"') {
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

function referentiel_get_competencies($occurrence, $scale_info){
// genere les outcomes (objectifs) pour le module competency a partir des domaine / competences / items du referentiel
global $labeldomaine;
$labeldomaine = "domaine";
global $labelcompetence;
$labelcompetence = "competence";
global $labelitem;
$labelitem = "item";

	$outcomes=array();
	if ($occurrence){
		if (!empty($occurrence->label_domaine)){ $labeldomaine = $occurrence->label_domaine;}
		if (!empty($occurrence->label_competence)) { $labelcompetence = $occurrence->label_competence;}
        if (!empty($occurrence->label_item)) { $labelitem = $occurrence->label_item;}

		$code_referentiel = stripslashes($occurrence->code_referentiel);
        $outcome= new stdClass();
        $outcome->parentid='';
        $outcome->idnumber=$code_referentiel;
        $outcome->shortname=$occurrence->name;
    	$outcome->description=$occurrence->description_referentiel;
        $outcome->scalevalue=$scale_info->grades;
        // $outcome->scaleconfig='[{""scaleid"":""1""},{""id"":3,""scaledefault"":1,""proficient"":1}]';
        $outcome->scaleconfig='[{"scaleid":"1"}';
		for ($i=0;$i<$scale_info->nbvalues; $i++){
            $index=$i+1;
			$outcome->scaleconfig.=',{"id":'.$index.',';
			if (!empty($scale_info->values[$i]->default)){
                $outcome->scaleconfig.='"scaledefault":1,';
			}
			else{
                $outcome->scaleconfig.='"scaledefault":0,';
			}
			if (!empty($scale_info->values[$i]->proficient)){
                $outcome->scaleconfig.='"proficient":1}';
			}
			else{
                $outcome->scaleconfig.='"proficient":0}';
			}
		}
        $outcome->scaleconfig.=']';

		$outcome->exportedid='';
        $outcome->isframework=1;
        $outcome->taxonomy='domain,competency,skill';
        $outcomes[]=$outcome;

        $exportedid=1;
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
					if (strlen($description_domaine)<=100){
                    	$desc_dom=$description_domaine;
                    }
                    else{
                            	$desc_dom=mb_substr($description_domaine,0,100);
                                $desc_dom=mb_substr($desc_dom, 0, strrpos($desc_dom," "));
                                $desc_dom.=' (...)';
                    }

                    $outcome= new stdClass();
                    $outcome->parentid='';
                    $outcome->idnumber=$code_domaine;
  					$outcome->shortname=$desc_dom;
                    $outcome->description=$description_domaine;
                	$outcome->scalevalue='';
        			$outcome->scaleconfig='';

                    $outcome->exportedid=$exportedid;
                    $outcome->isframework='';
                    $outcome->taxonomy='';
					$outcomes[]=$outcome;

                    $exportedid++;

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
							if (strlen($description_competence)<=100){
                            	$desc_comp=$description_competence;
                        	}
                            else{
                            	$desc_comp=mb_substr($description_competence,0,100);
                                $desc_comp=mb_substr($desc_comp, 0, strrpos($desc_comp," "));
                                $desc_comp.=' (...)';
                            }

                            $outcome= new stdClass();
                            $outcome->parentid=$code_domaine;
                            $outcome->idnumber=$code_competence;
  							$outcome->shortname=$desc_comp;
                            $outcome->description=$description_competence;
                			$outcome->scalevalue='';
        					$outcome->scaleconfig='';

                            $outcome->exportedid=$exportedid;
                            $outcome->isframework='';
                            $outcome->taxonomy='';
                            $outcomes[]=$outcome;

                            $exportedid++;
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
									if (strlen($description_item)<=100){
                                        $desc_item=$description_item;
                                    }
                                    else{
                                        $desc_item=mb_substr($description_item,0,100);
                                        $desc_item=mb_substr($desc_item, 0, strrpos($desc_item," "));
                                        $desc_item.=' (...)';
                                    }
                                    $outcome= new stdClass();
                                    $outcome->parentid=$code_competence;
                                    $outcome->idnumber=$code_item;
									$outcome->shortname=$desc_item;
                                    $outcome->description=$description_item;
                	$outcome->scalevalue='';
        			$outcome->scaleconfig='';

                                    $outcome->exportedid=$exportedid;
                                    $outcome->isframework='';
                                    $outcome->taxonomy='';
                                    $outcomes[]=$outcome;

                                    $exportedid++;
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

    $scale_info = new stdClass();

		// Default values
		$scale_info->name = get_string('nom_bareme','referentiel');
	    $scale_info->grades = get_string('bareme','referentiel');
		$scale_info->description = get_string('description_bareme','referentiel');

		$scale_info->nbvalues=3;
    	$scale_info->values= array();
		$agrade = new stdClass();
		$agrade->default=0;
	    $agrade->proficient=0;
    	$scale_info->values[]=$agrade;
	    $agrade = new stdClass();
		$agrade->default=0;
	    $agrade->proficient=0;
    	$scale_info->values[]=$agrade;
		$agrade = new stdClass();
		$agrade->default=1;                // la valeur par defaut en cas de validation automatique
	    $agrade->proficient=1;           // le seul qui valide la competence
    	$scale_info->values[]=$agrade;

	if (!$CFG->referentiel_use_scale){  // Bareme
    	if ($baremeid=referentiel_get_bareme_id_occurrence($occurrenceid)){
			if ($sbareme=$DB->get_record('referentiel_scale', array('id'=>$baremeid))){
				if ($scale=$DB->get_record('scale', array('id'=>$sbareme->scaleid))){
					$scale_info->name = $scale->name;
			    	$scale_info->grades = $scale->scale;
					$scale_info->description = mb_substr(strip_tags($scale->description),0,60);
                    $scale_info->description = mb_substr($scale_info->description, 0, strrpos($scale_info->description," "));
                    $scale_info->description .= ' (...)';

					// Competency Framework needs more information
					// print_object($sbareme);


    				$scale_info->values= array();
				 	if ($grades=explode( ',', $sbareme->scale)){
                    	$scale_info->nbvalues=count($grades);
						for($i=0; $i<$scale_info->nbvalues; $i++) {
				            $agrade = new stdClass();
							if ($i>=$sbareme->threshold){
        						if ($i==$sbareme->threshold){
									$agrade->default=1;            // la valeur par defaut en cas de validation automatique
    							}
								else{
                				    $agrade->default=0;
								}
								$agrade->proficient=1;
							}
							else{
                                $agrade->default=0;
                                $agrade->proficient=0;
							}
			                $scale_info->values[]=$agrade;     // le seuil qui valide la competence
						}
					}
				}
    		}
		}
	}
	return $scale_info;
}
?>
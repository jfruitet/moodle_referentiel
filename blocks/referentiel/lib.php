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
function referentiel_set_scale_2_bareme($form, $bareme){
global $DB;
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
            [timemodified] => 1396131625
        )

)

DEBUG :: bareme.php :: 271 :: BAREME FORMDATA
stdClass Object
(
    [blockid] => 18
    [courseid] => 2
    [occurrenceid] => 1
    [mode] => editbareme
    [pass] => 1
    [name] => LOMER
    [seuilid] => 2
    [iconscale_0] => <span style="color: red;">•</span>
    [iconscale_1] => <span style="color: red;">•</span>
    [iconscale_2] => <span style="color: green;">•</span>
    [iconscale_3] => <span style="color: green;">•</span>
    [scaleid] => 1
    [submitbutton] => Enregistrer
)
*/

	//DEBUG
    //echo "<br />DEBUG :: ./block/referentiel/lib.php :: 44 <br />\n";
	//print_object($form);
	//exit;

    if (!empty($form->scaleid) && !empty($bareme)){
    	$bareme->icons='';
		if ($tscales=explode(',',$bareme->scale)){
       		while (list($key, $val) = each($tscales)) {
            	//echo "$key => $val<br />\n";
                $s='iconscale_'.$key;
				if (!empty($form->$s)){
                   	$bareme->icons.=$form->$s.',';
				}
			}
		}
		// DEBUG
		//echo "<br /> ./blocks/referentiel/ lib.php :: 105\n";
		//print_object($bareme);
		//exit;
		if ($bareme->id=$DB->insert_record('referentiel_scale', $bareme) && !empty($form->occurrenceid)){
			referentiel_set_bareme_occurrence($bareme, $form->occurrenceid);
		}
	}
}

/*
 * return  borrowed from a scale record
 *
 * @param $scale data submitted
 * @param $occurrence_id as referentiel_referentiel id
 * @return Object
 */
 /*
// -----------------------
function referentiel_scale_2_bareme($scale){
    if (!empty($scale)){
        $bareme= new Object();
        $bareme->scaleid=$scale->id;
        $bareme->name=$scale->name;
        $bareme->scale=$scale->scale;
        $bareme->maxscale=0;
        $bareme->threshold=$bareme->maxscale;
        if ($ts=explode(',',$scale->scale)){
			$bareme->maxscale=count($ts)-1;
            if ($bareme->maxscale>2){
                $bareme->threshold= (int) (ceil($bareme->maxscale/2)) ;
            }
            else{
                $bareme->threshold=$bareme->maxscale;
            }
        }
        $bareme->description=$scale->description;
        $bareme->descriptionformat=$scale->descriptionformat;
        $bareme->icons='';
        $bareme->labels=$scale->scale;
        $bareme->timemodified=time();

        return $bareme;
    }
    return NULL;
}
*/
//------------------
function referentiel_set_bareme($form){
global $DB;
	//DEBUG
    //echo "<br />DEBUG :: ./block/referentiel/lib.php :: 155 <br />\n";
	//print_object($form);
	//exit;

    if (!empty($form->baremeid)){
	    if ($rec_bareme=$DB->get_record('referentiel_scale', array('id'=>$form->baremeid))){
        	$rec_bareme->name=$form->name;
			$rec_bareme->threshold=$form->seuilid;
    		$rec_bareme->icons='';
			if ($tscales=explode(',',$rec_bareme->scale)){
       			while (list($key, $val) = each($tscales)) {
            		//echo "$key => $val<br />\n";
                    $s='iconscale_'.$key;
					if (!empty($form->$s)){
                    	$rec_bareme->icons.=$form->$s.',';
					}
				}
				// DEBUG
				//echo "<br /> 390\n";
				//print_object($rec_bareme);
				if ($DB->update_record('referentiel_scale', $rec_bareme) && !empty($form->occurrenceid)){
					referentiel_set_bareme_occurrence($rec_bareme, $form->occurrenceid);
				}
			}
		}
	}

}



//------------------
function referentiel_set_occurrence($formdata){
// Traite le formulaire de saisie d'une occurrence
// mise a jour des tables referentiel_referentiel
// mise a jour des tables referentiel_domaine,
// referentiel_competence, referentiel_item et table referentiel_protocol
global $CFG, $USER, $DB;
    $occurrenceid=0;
    // DEBUG
    // echo "<br />DEBUG :: ./block/referentiel/lib.php :: 11 <br />\n";
    // print_object($mform);

    if (!empty($formdata)) {
        // DEBUG
        //echo "<br />DEBUG :: lib.php :: 416 <br />\n";
        //print_object($formdata);
        //echo "<br />EXIT<br />\n";
		//exit;
        $occurrence= new Object();
		$occurrence->name=$formdata->name;
    	$occurrence->code_referentiel=$formdata->code_referentiel;
    	$occurrence->description_referentiel=$formdata->description_referentiel;
    	$occurrence->url_referentiel=$formdata->url_referentiel;
    	$occurrence->logo_referentiel=$formdata->logo_referentiel;
        $occurrence->local=$formdata->local;
        $occurrence->nb_domaines=$formdata->nb_domaines;
    	$occurrence->liste_codes_competence=$formdata->liste_codes_competence;
    	$occurrence->liste_poids_competence=$formdata->liste_poids_competence;
        $occurrence->liste_empreintes_competence=$formdata->liste_empreintes_competence;
        if (!empty($formdata->label_domaine)){ $occurrence->label_domaine=$formdata->label_domaine; } else {$occurrence->label_domaine=get_string('label_domaine','referentiel');}
        if (!empty($formdata->label_competence)){ $occurrence->label_competence=$formdata->label_competence;} else {$occurrence->label_competence=get_string('label_competence','referentiel');}
        if (!empty($formdata->label_item)){ $occurrence->label_item=$formdata->label_item;} else {$occurrence->label_competence=get_string('label_item','referentiel');}
    	$occurrence->config=$formdata->config;
    	$occurrence->config_impression=$formdata->config_impression;
     	$occurrence->cle_referentiel=$formdata->cle_referentiel;
    	$occurrence->mail_auteur_referentiel=$formdata->mail_auteur_referentiel;
    	$occurrence->pass_referentiel=$formdata->pass_referentiel;
        $occurrence->timemodified = time();

        if (isset($formdata->occurrenceid) && ($formdata->occurrenceid==0)){
		 	$occurrenceid=$DB->insert_record("referentiel_referentiel", $occurrence);
			if ($occurrenceid){
            	$occurrence->cle_referentiel=referentiel_recalcule_cle_referentiel($occurrenceid);
			}
		}
		else{   // Update
            $occurrenceid=$formdata->occurrenceid;
			$occurrence->id=$formdata->occurrenceid;
		 	$DB->update_record("referentiel_referentiel", $occurrence);
		}

		// New Domains, Competencies , Items
		if (!empty($formdata->list_newdomains)){
            //echo "<br />LIST_NEWDOMAINS :$formdata->list_newdomains\n";
			if ($t_domainids=explode(',',$formdata->list_newdomains)){
				foreach($t_domainids as $D_id){
					if (!empty($D_id)){// && is_int($D_id)){
	                    $error=false;
						$rec=new Object();
                   	    $rec->occurrence=$occurrence->id;   // mandatory

                        $s='new_code_domaine_'.$D_id;
						if (isset($formdata->$s)){
                    	    $rec->new_code_domaine=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_description_domaine_'.$D_id;
						if (isset($formdata->$s)){
	                        $rec->new_description_domaine=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_type_domaine_'.$D_id;
						if (isset($formdata->$s)){
    	                    $rec->new_type_domaine=$formdata->$s;
						}
						else{
							$error=true;
						}
	                    $s='new_minima_domaine_'.$D_id;
						if (isset($formdata->$s)){
        	                $rec->new_minima_domaine=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_seuil_domaine_'.$D_id;
						if (isset($formdata->$s)){
            	            $rec->new_seuil_domaine=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_num_domaine_'.$D_id;
						if (isset($formdata->$s)){
                	        $rec->new_num_domaine=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_nb_competences_'.$D_id;
						if (isset($formdata->$s)){
                    	    $rec->new_nb_competences=$formdata->$s;
						}
						else{
							$error=true;
						}

						if (!$error){
							// exit;
							// Update + protocole update
		 					if (!referentiel_add_domaine($rec)){
								// DEBUG
                        		echo "ERROR ADD DOMAIN :: 518 lib.php :: :: DOMAIN<br />\n";
								print_object($rec);
								echo "<br>ERROR\n";
								exit;
							}
						}
					}
				}
			}
		}
		if (!empty($formdata->list_newcompetencies)){
			if ($t_competencyids=explode(',',$formdata->list_newcompetencies)){
				foreach($t_competencyids as $C_id){
					// echo "<br />COMPETENCY ID:$C_id\n";
					if (!empty($C_id)){// && is_int($C_id)){
	                    $error=false;
						$rec=new Object();
                        $rec->reference_id=$occurrence->id; // mandatory for protocole management
						$s='new_ref_domaine_'.$C_id;
						if (isset($formdata->$s)){
                    	    $rec->new_ref_domaine=$formdata->$s;
						}
						else{
							$error=true;
						}
            	        $s='new_code_competence_'.$C_id;
						if (isset($formdata->$s)){
                    	    $rec->new_code_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_description_competence_'.$C_id;
						if (isset($formdata->$s)){
	                        $rec->new_description_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_type_competence_'.$C_id;
						if (isset($formdata->$s)){
    	                    $rec->new_type_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
	                    $s='new_minima_competence_'.$C_id;
						if (isset($formdata->$s)){
        	                $rec->new_minima_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_seuil_competence_'.$C_id;
						if (isset($formdata->$s)){
            	            $rec->new_seuil_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_num_competence_'.$C_id;
						if (isset($formdata->$s)){
                	        $rec->new_num_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_nb_item_competences_'.$C_id;
						if (isset($formdata->$s)){
                    	    $rec->new_nb_item_competences=$formdata->$s;
						}
						else{
							$error=true;
						}
						if (!$error){
							// Update + protocole update
		 					if (!referentiel_add_competence($rec)){
                            	echo "<br>ERROR ADD COMPETENCY :: lib.php :: 595 :: COMPETENCY\n";
								print_object($rec);
                        		echo "<br />\n";
                                exit;
							}
						}
					}
				}
			}
		}
		if (!empty($formdata->list_newitems)){
			if ($t_itemids=explode(',',$formdata->list_newitems)){
				foreach($t_itemids as $I_id){
                    //echo "<br />ITEM ID:$I_id\n";
					if (!empty($I_id)){// && is_int($I_id)){
	                    $error=false;
						$rec=new Object();
                        $rec->occurrence=$occurrence->id; // mandatory for protocole management
						$s='new_ref_competence_'.$I_id;
						if (isset($formdata->$s)){
                    	    $rec->new_ref_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
            	        $s='new_code_item_'.$I_id;
						if (isset($formdata->$s)){
                    	    $rec->new_code_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_description_item_'.$I_id;
						if (isset($formdata->$s)){
	                        $rec->new_description_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_type_item_'.$I_id;
						if (isset($formdata->$s)){
    	                    $rec->new_type_item=$formdata->$s;
						}
						else{
							$error=true;
						}
	                    $s='new_poids_item_'.$I_id;
						if (isset($formdata->$s)){
        	                $rec->new_poids_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_empreinte_item_'.$I_id;
						if (isset($formdata->$s)){
            	            $rec->new_empreinte_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='new_num_item_'.$I_id;
						if (isset($formdata->$s)){
                	        $rec->new_num_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						if (!$error){
							// Update + protocole update
			 				if (!referentiel_add_item($rec)){
								echo "<br>ERROR ADD ITEM :: lib.php :: 265 :: ITEM\n";
								print_object($rec);
                        		echo "<br />\n";
                        		exit;
							}
						}
					}
				}
			}
		}

		// Domains, Competencies , Items
		if (!empty($formdata->list_domains)){
			if ($t_domainids=explode(',',$formdata->list_domains)){
				foreach($t_domainids as $D_id){
					if (!empty($D_id)){// && is_int($D_id)){
                    	//echo "<br />DOMAIN ID:$D_id\n";
                        $s='tdomain_id_'.$D_id;
						if (!empty($formdata->$s)){
							// Traiter cet objet
	                    	$error=false;
							$rec=new Object();
    	    	            $rec->domaine_id=$D_id;

            	            $s='ref_referentiel_'.$D_id;
							if (isset($formdata->$s)){
                    		    $rec->ref_referentiel=$formdata->$s;
							}
							else{
								$error=true;
							}
            	            $s='code_domaine_'.$D_id;
							if (isset($formdata->$s)){
                    		    $rec->code_domaine=$formdata->$s;
							}
							else{
								$error=true;
							}
							$s='description_domaine_'.$D_id;
							if (isset($formdata->$s)){
		                        $rec->description_domaine=$formdata->$s;
							}
							else{
								$error=true;
							}
							$s='type_domaine_'.$D_id;
							if (isset($formdata->$s)){
    		                    $rec->type_domaine=$formdata->$s;
							}
							else{
								$error=true;
							}
		                    $s='minima_domaine_'.$D_id;
							if (isset($formdata->$s)){
        		                $rec->minima_domaine=$formdata->$s;
							}
							else{
								$error=true;
							}
							$s='seuil_domaine_'.$D_id;
							if (isset($formdata->$s)){
        	    	            $rec->seuil_domaine=$formdata->$s;
							}
							else{
								$error=true;
							}
							$s='num_domaine_'.$D_id;
							if (isset($formdata->$s)){
                	        	$rec->num_domaine=$formdata->$s;
							}
							else{
								$error=true;
							}
							$s='nb_competences_'.$D_id;
							if (isset($formdata->$s)){
                    	    	$rec->nb_competences=$formdata->$s;;
							}
							else{
								$error=true;
							}

							if (!$error){
								// DEBUG
	                        	// echo "DEBUG :: 107 lib.php :: :: DOMAIN<br />\n";
								// print_object($rec);
        	                	// echo "<br />\n";
								// exit;
								// Update + protocole update
		 						referentiel_update_domaine($rec);
							}
						}
					}
				}
			}
		}
		if (!empty($formdata->list_competencies)){
			if ($t_competencyids=explode(',',$formdata->list_competencies)){
				foreach($t_competencyids as $C_id){
					//echo "<br />COMPETENCY ID:$C_id\n";
					if (!empty($C_id)){// && is_int($C_id)){
                    	$s='tcomp_id_'.$C_id;
						if (!empty($formdata->$s)){
							// Traiter cet objet

	                    $error=false;
						$rec=new Object();
        	            $rec->competence_id=$C_id;
                        $rec->reference_id=$occurrence->id; // mandatory for protocole management
						$s='ref_domaine_'.$C_id;
						if (isset($formdata->$s)){
                    	    $rec->ref_domaine=$formdata->$s;
						}
						else{
							$error=true;
						}
            	        $s='code_competence_'.$C_id;
						if (isset($formdata->$s)){
                    	    $rec->code_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='description_competence_'.$C_id;
						if (isset($formdata->$s)){
	                        $rec->description_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='type_competence_'.$C_id;
						if (isset($formdata->$s)){
    	                    $rec->type_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
	                    $s='minima_competence_'.$C_id;
						if (isset($formdata->$s)){
        	                $rec->minima_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='seuil_competence_'.$C_id;
						if (isset($formdata->$s)){
            	            $rec->seuil_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='num_competence_'.$C_id;
						if (isset($formdata->$s)){
                	        $rec->num_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='nb_item_competences_'.$C_id;
						if (isset($formdata->$s)){
                    	    $rec->nb_item_competences=$formdata->$s;
						}
						else{
							$error=true;
						}
						if (!$error){
							// Update + protocole update
		 					if (!referentiel_update_competence($rec)){
								// DEBUG
                        		echo "ERROR :: 833 lib.php :: :: COMPETENCY<br />\n";
								print_object($rec);
                        		echo "<br />\n";
								exit;
							}
						}
						}
					}
				}
			}
		}
		if (!empty($formdata->list_items)){
			if ($t_itemids=explode(',',$formdata->list_items)){
				foreach($t_itemids as $I_id){
                    //echo "<br />ITEM ID:$I_id\n";
					if (!empty($I_id)){// && is_int($I_id)){
                    	$s='titem_id_'.$I_id;
						if (!empty($formdata->$s)){
							// Traiter cet objet

	                    $error=false;
						$rec=new Object();
        	            $rec->item_id=$I_id;
						$s='ref_competence_'.$I_id;
						if (isset($formdata->$s)){
                    	    $rec->ref_competence=$formdata->$s;
						}
						else{
							$error=true;
						}
            	        $s='code_item_'.$I_id;
						if (isset($formdata->$s)){
                    	    $rec->code_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='description_item_'.$I_id;
						if (isset($formdata->$s)){
	                        $rec->description_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='type_item_'.$I_id;
						if (isset($formdata->$s)){
    	                    $rec->type_item=$formdata->$s;
						}
						else{
							$error=true;
						}
	                    $s='poids_item_'.$I_id;
						if (isset($formdata->$s)){
        	                $rec->poids_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='empreinte_item_'.$I_id;
						if (isset($formdata->$s)){
            	            $rec->empreinte_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						$s='num_item_'.$I_id;
						if (isset($formdata->$s)){
                	        $rec->num_item=$formdata->$s;
						}
						else{
							$error=true;
						}
						if (!$error){
							// DEBUG
                        	// echo "DEBUG :: 195 lib.php :: :: ITEM<br />\n";
							// print_object($rec);
                        	// echo "<br />\n";
							// exit;
							// Update + protocole update
		 					referentiel_update_item($rec);
						}
						}
					}
				}
			}
		}

    }
 	return $occurrenceid;
}

// ------------------
function referentiel_get_manage_block_files1($contextid, $itemid, $filename, $titre, $appli ){

$browser = get_file_browser();

$filename = null;
if ($fileinfo = $browser->get_file_info($contextid, 'block_referentiel', 'referentiel', $itemid, '/', $filename)) {
    // build a Breadcrumb trail
    $level = $fileinfo->get_parent();
    while ($level) {
        $path[] = array('name'=>$level->get_visible_name());
        $level = $level->get_parent();
    }
    $path = array_reverse($path);
    $children = $fileinfo->get_children();
    foreach ($children as $child) {
        if ($child->is_directory()) {
            echo $child->get_visible_name();
            // display contextid, itemid, component, filepath and filename
            var_dump($child->get_params());
        }
    }
}
}


/**
 * Lists all browsable file areas
 *
 * @package  mod_referentiel
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function block_referentiel_get_file_areas() {
    return array(
        'referentiel' => get_string('areareferentiel', 'referentiel'),
    );
}


/**
 * Serves documents and other files.
 * @package  block_referentiel
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function block_referentiel_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload=1, array $options=array()) {
    global $CFG, $DB;
    if ($context->contextlevel != CONTEXT_BLOCK) {
        return false;
    }
    require_login($course);

	// Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('mod/referentiel:view', $context)) {
        return false;
    }


    $areas = block_referentiel_get_file_areas();

    // filearea must contain a real area
    if (!isset($areas[$filearea])) {
        return false;
    }
    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.
    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.
    if ($filearea=='referentiel'){
        // an occurrence of referential exists
        if (! $occurrence = $DB->get_record("referentiel_referentiel", array("id" => $itemid ))) {
            return false;
        }
    }
    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.

    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }

    // Retrieve the file from the Files API.

    $fs = get_file_storage();

    $file = $fs->get_file($context->id, 'block_referentiel', $filearea, $itemid, $filepath, $filename);


    if (!$file) {
        return false; // The file does not exist.
    }
    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 0, 0, true); // download MUST be forced - security!
}




// ------------------
function block_referentiel_get_manage_block_files($contextid, $filearea, $docid, $titre, $appli){
// retourne la liste des liens vers des fichiers stockes dans le filearea
// propose la suppression
global $CFG;
global $OUTPUT;
    $total_size=0;
    $nfile=0;
    // fileareas autorisees
    $fileareas = array('referentiel');
    if (!in_array($filearea, $fileareas)) {
        return false;
    }
    $strfilepath='filepath';
    $strfilename=get_string('filename', 'referentiel');
    $strfilesize=get_string('filesize', 'referentiel');
    $strtimecreated=get_string('timecreated', 'referentiel');
    $strtimemodified=get_string('timemodified', 'referentiel');
    $strmimetype=get_string('mimetype', 'referentiel');
    $strmenu=get_string('delete');

    $strurl=get_string('url');


    $fs = get_file_storage();
    if ($files = $fs->get_area_files($contextid, 'block_referentiel', $filearea, $docid, "timemodified", false)) {
        // DEBUG
        //print_object($files);
        //exit;
        $table = new html_table();
	    $table->head  = array ($strfilename, $strfilesize, $strtimecreated, $strtimemodified, $strmimetype, $strmenu);
        $table->align = array ("center", "left", "left", "left", "center");

        foreach ($files as $file) {
            // print_object($file);
            $filesize = $file->get_filesize();
            $filename = $file->get_filename();
            $mimetype = $file->get_mimetype();
            $filepath = $file->get_filepath();
            $timecreated =  userdate($file->get_timecreated(),"%Y/%m/%d-%H:%M",99,false);
            $timemodified = userdate($file->get_timemodified(),"%Y/%m/%d-%H:%M",99,false);

            $fullpath = '/'.$contextid.'/block_referentiel/'.$filearea.'/'.$docid.$filepath.$filename;
            //echo "<br />FULLPATH : ".$fullpath."\n";
            //$link1 = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);
            //echo "<br />LINK1 : ".$link1."\n";

			$link = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
            // echo "<br />LINK : ".$link."\n";


            $url='<a href="'.$link.'">'.$filename.'</a><br />'."\n";
            $delete_link='<input type="checkbox" name="deletefile[]"  value="'.$fullpath.'" />'."\n";
            $table->data[] = array ($url, display_size($filesize), $timecreated, $timemodified, $mimetype, $delete_link);
            $total_size+=$filesize;
            $nfile++;
        }

        $table->data[] = array (get_string('nbfile', 'referentiel',$nfile), get_string('totalsize', 'referentiel', display_size($total_size)),'','','','');

        echo $OUTPUT->box_start('generalbox  boxaligncenter');
        echo '<div align="center">'."\n";
        echo '<h3>'.$titre.'</h3>'."\n";
        echo '<form method="post" action="'.$appli.'">'."\n";
        echo html_writer::table($table);
        echo "\n".'<input type="hidden" name="sesskey" value="'.sesskey().'" />'."\n";
        echo '<input type="submit" value="'.get_string('delete').'" />'."\n";
        echo '</form>'."\n";
        echo '</div>'."\n";
        echo $OUTPUT->box_end();
    }
}

?>
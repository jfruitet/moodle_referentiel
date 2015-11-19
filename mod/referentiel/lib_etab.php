<?php // $Id:  lib_etab.php,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
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
 * Library of functions and constants for module referentiel
 * 
 * @author jfruitet
 * @version $Id: lib.php,v 1.4 2006/08/28 16:41:20 mark-nielsen Exp $
 * @version $Id: lib.php,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
 * @package referentiel
 **/

 // ////////////////////////// ETUDIANT /////////////////////
//----------------------------------------------
function referentiel_nom_connu($s){
    if ($s=='l_inconnu') $s=get_string('l_inconnu', 'referentiel');
    return $s;
}

// ----------------------------------------------------------------
function referentiel_update_students_numbers($record_id_users) {
    // DEBUG
    // echo "<br />DEBUG :: lib_etab.php :: 39 :: Mise à jour des profils<br />\n";
    foreach ($record_id_users as $record) {   // traiter la liste d'utilisateurs
        if ($record->userid){
            referentiel_update_profile_student($record->userid);
    	}
	}
}

// ----------------------------------------------------------------
function referentiel_update_profile_student($userid){
global $DB;
$ok=false;
    if (!empty($userid)){
        $record=$DB->get_record("referentiel_etudiant", array("userid" => $userid));
        if ($record){
            $record->num_etudiant=referentiel_get_student_number($userid, true);
            $ok=$DB->set_field('referentiel_etudiant','num_etudiant', $record->num_etudiant, array("userid" => $userid));
            $record->num_etudiant=referentiel_get_student_ddn($userid, true);
            $ok=$ok & $DB->set_field('referentiel_etudiant','ddn_etudiant', $record->ddn_etudiant, array("userid" => $userid));
            $record->lieu_naissance=referentiel_get_student_ldn($userid, true);
            $ok=$ok & $DB->set_field('referentiel_etudiant','lieu_naissance', $record->lieu_naissance, array("userid" => $userid));
            $record->departement_naissance=referentiel_get_student_dpt($userid, true);
            $ok=$ok & $DB->set_field('referentiel_etudiant','departement_naissance', $record->departement_naissance, array("userid" => $userid));
            $record->adresse_etudiant=referentiel_get_student_adr($userid, true);
            $ok=$DB->set_field('referentiel_etudiant','adresse_etudiant', $record->adresse_etudiant, array("userid" => $userid));
			if ($ok){
                return $record;
            }
        }
    }
    return NULL;
}

// ----------------------------------------------------------------
function referentiel_get_student_number($userid, $updateprofil=0){
    global $CFG, $DB;
    $num_etudiant='';
    if (!empty($userid)){
        if ($student=$DB->get_record("referentiel_etudiant", array("userid" => $userid))){
            $num_etudiant=$student->num_etudiant;
        }
        if (empty($num_etudiant) || $updateprofil){
            if ($user=$DB->get_record("user", array("id" => $userid))){
                // profile table used ?
                if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_profilefield)){
                    $num_etudiant=referentiel_get_profile($CFG->ref_profilecategory, $CFG->ref_profilefield, $user->id);
                }
                if (!empty($num_etudiant)){
                    return $num_etudiant;
                }
                else{
                    if (!empty($user->idnumber)){
                        return $user->idnumber;
                    }
                    else{
                        return $user->username;
                    }
               }
            }
        }
    }
    return $num_etudiant;
}

// ----------------------------------------------------------------
function referentiel_get_student_ddn($userid, $updateprofil=0){
    global $CFG, $DB;
    $ddn_etudiant='';
    if (!empty($userid)){
        if ($student=$DB->get_record("referentiel_etudiant", array("userid" => $userid))){
            $ddn_etudiant=$student->ddn_etudiant;
        }
        if (empty($ddn_etudiant) || $updateprofil){
            if ($user=$DB->get_record("user", array("id" => $userid))){
                // profile table used ?
                if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_ddnfield)){
                    $ddn_etudiant=referentiel_get_profile($CFG->ref_profilecategory, $CFG->ref_ddnfield, $user->id);
                }
            }
        }
    }
    if (!empty($ddn_etudiant)){
    	return $ddn_etudiant;
    }
    else{
    	return 'l_inconnu';
    }
}

// ----------------------------------------------------------------
function referentiel_get_student_ldn($userid, $updateprofil=0){
    global $CFG, $DB;
    $ldn_etudiant='';
    if (!empty($userid)){
        if ($student=$DB->get_record("referentiel_etudiant", array("userid" => $userid))){
            $ldn_etudiant=$student->lieu_naissance;
        }
        if (empty($ldn_etudiant) || $updateprofil){
            if ($user=$DB->get_record("user", array("id" => $userid))){
                // profile table used ?
                if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_ldnfield)){
                    $ldn_etudiant=referentiel_get_profile($CFG->ref_profilecategory, $CFG->ref_ldnfield, $user->id);
                }
            }
        }
    }
    if (!empty($ldn_etudiant)){
    	return $ldn_etudiant;
    }
    else{
    	return 'l_inconnu';
    }
}

// ----------------------------------------------------------------
function referentiel_get_student_adr($userid, $updateprofil=0){
    global $CFG, $DB;
    $adr_etudiant='';
    if (!empty($userid)){
        if ($student=$DB->get_record("referentiel_etudiant", array("userid" => $userid))){
            $adr_etudiant=$student->adresse_etudiant;
        }
        if (empty($adr_etudiant) || $updateprofil){
            if ($user=$DB->get_record("user", array("id" => $userid))){
                // profile table used ?
                if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_adrfield)){
                    $adr_etudiant=referentiel_get_profile($CFG->ref_profilecategory, $CFG->ref_adrfield, $user->id);
                }
            }
        }
    }
    if (!empty($adr_etudiant)){
    	return $adr_etudiant;
    }
    else{
    	return 'l_inconnu';
    }
}
// ----------------------------------------------------------------
function referentiel_get_student_dpt($userid, $updateprofil=0){
    global $CFG, $DB;
    $dpt_etudiant='';
    if (!empty($userid)){
        if ($student=$DB->get_record("referentiel_etudiant", array("userid" => $userid))){
            $dpt_etudiant=$student->departement_naissance;
        }
        if (empty($dpt_etudiant) || $updateprofil){
            if ($user=$DB->get_record("user", array("id" => $userid))){
                // profile table used ?
                if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_dptfield)){
                    $dpt_etudiant=referentiel_get_profile($CFG->ref_profilecategory, $CFG->ref_dptfield, $user->id);
                }
            }
        }
    }
    if (!empty($dpt_etudiant)){
    	return $dpt_etudiant;
    }
    else{
    	return 'l_inconnu';
    }
}

// ----------------------------------------------------------------
function referentiel_get_ref_etablissement($userid, $updateprofil=0){
    global $CFG, $DB;
    $ref_etablissement=0;
    $name_etablissement='';
    $num_etablissement='';
    if (!empty($userid)){
        if ($student=$DB->get_record("referentiel_etudiant", array("userid" => $userid))){
            $ref_etablissement=$student->ref_etablissement;
        }
        if (empty($ref_etablissement) || $updateprofil){
            if ($user=$DB->get_record("user", array("id" => $userid))){
                // profile table used ?
                if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_etabfield)){
                    $name_etablissement=referentiel_get_profile($CFG->ref_profilecategory, $CFG->ref_etabfield, $user->id);
                }
                if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_numetabfield)){
                    $num_etablissement=referentiel_get_profile($CFG->ref_profilecategory, $CFG->ref_numetabfield, $user->id);
                }

                if (!empty($name_etablissement)){
					if ($etablissement= $DB->get_record("referentiel_etablissement", array("nom_etablissement" => $name_etablissement))){
                        $ref_etablissement=$etablissement->id;
					}
					else{ // creer l'etablissement
						$etablissement = new object();
                        $etablissement->nom_etablissement= $name_etablissement;
						if (!empty($num_etablissement)){
                            $etablissement->num_etablissement= $num_etablissement;
						}
						else{
                            $etablissement->num_etablissement= '';
						}
                        $etablissement->adresse_etablissement= '';
                        $etablissement->logo_etablissement= '';
      					$ref_etablissement=$DB->insert_record("referentiel_etablissement", $etablissement);
					}
                }
                elseif (!empty($num_etablissement)){
					if ($etablissement= $DB->get_record("referentiel_etablissement", array("num_etablissement" => $num_etablissement))){
                        $ref_etablissement=$etablissement->id;
					}
					else{ // creer l'etablissement
						$etablissement = new object();
                        if (!empty($name_etablissement)){
							$etablissement->nom_etablissement= $name_etablissement;
						}
                        $etablissement->num_etablissement= $num_etablissement;
                        $etablissement->adresse_etablissement= '';
                        $etablissement->logo_etablissement= '';
     					$ref_etablissement=$DB->insert_record("referentiel_etablissement", $etablissement);
					}
                }
				else{
                     $ref_etablissement=referentiel_get_min_etablissement();
				}
            }
        }
    }
    return $ref_etablissement;
}


// ----------------------------------------------------------------
function referentiel_get_profile($fieldcategory, $fieldname, $userid) {
    global $CFG, $USER, $DB;

    if ($category = $DB->get_record('user_info_category', array('name'=>$fieldcategory))) {
        if ($field = $DB->get_record('user_info_field', array('categoryid'=>$category->id, 'shortname'=>$fieldname))) {
            require_once($CFG->dirroot . '/user/profile/lib.php');
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id, $userid);
            if (!$formfield->is_empty()) {
                return $formfield->display_data();
            }
        }
    }
}


// ---------------------------------------------
function referentiel_add_etudiant_user($userid){
// retourne l'id cree
global $DB;
	if ($userid){
    	$record=new object();
        $record->userid=$userid;
        // $record->ddn_etudiant = 'l_inconnu';
        $record->ddn_etudiant = referentiel_get_student_ddn($userid);
        // $record->lieu_naissance = 'l_inconnu';
        $record->lieu_naissance = referentiel_get_student_ldn($userid);
        // $record->departement_naissance = 'l_inconnu';
        $record->departement_naissance = referentiel_get_student_dpt($userid);
        //$record->adresse_etudiant = 'l_inconnu';
        $record->adresse_etudiant = referentiel_get_student_adr($userid);
        // $record->ref_etablissement = referentiel_get_min_etablissement();
        $record->ref_etablissement = referentiel_get_ref_etablissement($userid);
        $record->num_etudiant=referentiel_get_student_number($userid);
	    // DEBUG
	    // echo "<br />DEBUG :: lib_etab.php :: 145\n";
	    // print_r($record);
        return ($DB->insert_record("referentiel_etudiant", $record));
    }
    return 0;
}

// ---------------------------------------------
function referentiel_etudiant_isowner($id){
global $DB;
global $USER;
	if (!empty($id)){
		$record=$DB->get_record("referentiel_etudiant", array("userid" => "$id"));
		// DEBUG
		// echo "<br >USERID : $USER->id ; OWNER : $record->userid\n";
		return ($USER->id == $record->userid);
	}
	else 
		return false; 
} 

// ---------------------------------------------
function referentiel_get_etudiant_id_by_userid($userid){
global $DB;
	if (!empty($userid)){
		$record=$DB->get_record("referentiel_etudiant", array("userid" => "$userid"));
		if ($record){
			return ($record->id);
		}
	}
	return 0;
}


/**
 * This function returns record from table referentiel_etudiant
 *
 * @param id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_etudiant_user($userid){
global $DB;
 if (!empty($userid)){
		return $DB->get_record_sql("SELECT * FROM {referentiel_etudiant} WHERE userid=:userid", array("userid" => "$userid"));
	}
	else 
		return 0; 
}

/**
 * This function returns record from table referentiel_etudiant
 *
 * @param id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_etudiant($id){
global $CFG;
	if (!empty($id)){
		return $DB->get_record_sql("SELECT * FROM {referentiel_etudiant} WHERE id=:id" , array("id" => "$id"));
	}
	else 
		return 0; 
}

/**
 * This function returns record from table referentiel_etudiant
 *
 * @param id
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_etudiants($params, $search=""){
global $DB;
	return $DB->get_records_sql("SELECT * FROM {referentiel_etudiant} WHERE $search ", $params);
}


/**
 * Given an object containing all the necessary referentiel, 
 * (defined by the form in mod.html) this function 
 * will create a new instance and return the id number 
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted referentiel record
 **/
function referentiel_add_etudiant($form) {
// creation certificat
global $DB;
global $USER;
$id=0;
	$record=new object();
	$record->num_etudiant = $form->num_etudiant;
	$record->ddn_etudiant = $form->ddn_etudiant ;
	$record->lieu_naissance = ($form->lieu_naissance);
	$record->departement_naissance = ($form->departement_naissance);
	$record->adresse_etudiant = ($form->adresse_etudiant);
	if ($form->ref_etablissement){
		$record->ref_etablissement = $form->ref_etablissement;
	}
	else{
		$record->ref_etablissement = referentiel_get_min_etablissement();
	}
	$record->userid = $form->userid;
	
	// controle
	if (($record->userid>0) && (($record->num_etudiant=='') || ($record->num_etudiant=='l_inconnu'))){
        $record->num_etudiant = referentiel_get_student_number($record->userid);
    }

	$id=$DB->insert_record("referentiel_etudiant", $record);
    return $id;
}


/**
 * Given an object containing all the necessary referentiel, 
 * (defined by the form in mod.html) this function 
 * will create a new instance and return the id number 
 * of the new instance.
 *
 * @param object $instance An object from the form in eturdiant.html
 * @return int The id of the newly inserted referentiel record
 **/
function referentiel_update_etudiant($form) {
// MAJ etudiant
global $DB;
    $ok=true;
    // DEBUG
    // echo "DEBUG : UPDATE ETUDIANT CALLED";
	// print_object($form);
    // echo "<br />";
	// certificat
    if (isset($form->action) && ($form->action=="modifier_etudiant")){
		$record=new object();
		$record->id = $form->etudiant_id;
		$record->num_etudiant = $form->num_etudiant;
		$record->ddn_etudiant = ($form->ddn_etudiant) ;
		$record->lieu_naissance = ($form->lieu_naissance);
		$record->departement_naissance = ($form->departement_naissance);
		$record->adresse_etudiant = ($form->adresse_etudiant);
		$record->ref_etablissement = $form->ref_etablissement;
		$record->userid = $form->userid;
	    // controle
	    if (($record->userid>0) && (($record->num_etudiant=='') || ($record->num_etudiant=='l_inconnu'))){
            $record->num_etudiant = referentiel_get_student_number($record->userid);
        }

    	if(!$DB->update_record("referentiel_etudiant", $record)){
	       	// echo "<br /> ERREUR UPDATE ETUDIANT\n";
		  $ok=false;
        }
        else {
		  // echo "<br /> UPDATE ETUDIANT $record->id\n";
		  $ok=true;
        }
	    return $ok;
    }
}

function referentiel_etudiant_set_etablissement($userid, $etablissement_id){
// mise a jour de l'etablisssement
global $DB;
	if ($userid && $etablissement_id){
		$record=referentiel_get_etudiant_user($userid);
		$record->lieu_naissance = ($record->lieu_naissance);
		$record->departement_naissance = ($record->departement_naissance);
		$record->adresse_etudiant = ($record->adresse_etudiant);
		$record->ref_etablissement = $etablissement_id;
		if ($DB->update_record("referentiel_etudiant", $record)){
			return true;
		} 
	}
	return false;	
}


/**
 * Given an etudiant id,
 * this function will permanently delete the etudiant
 *
 * @param object $id
 * @return boolean Success/Failure
 **/

function referentiel_delete_etudiant($id) {
// suppression certificat
global $DB;
$ok_delete=false;
	if (!empty($id)){
		if ($etudiant = $DB->get_record("referentiel_etudiant", array("id" => "$id"))) {
			// suppression 
			$ok_delete = $DB->delete_records("referentiel_etudiant", array("id" => "$id"));
		}
	}
    return $ok_delete;
}

function referentiel_delete_etudiant_user($userid) {
// suppression etudiant
global $DB;
$ok_delete=false;	
	if (!empty($userid)){
		if ($etudiant = $DB->get_record("referentiel_etudiant", array("userid" => "$userid"))) {
			// suppression 
			$ok_delete = $DB->delete_records("referentiel_etudiant", array("id" => "$etudiant->id"));
		}
	}
    return $ok_delete;
}

// ///////////// ETABLISSEMENT ///////////////////
/**
 * This function returns records from table referentiel_etablissement
 *
 * @param ref
 * @return record
 * @todo Finish documenting this function
 **/
function referentiel_get_etablissements(){
global $DB;
	return $DB->get_records("referentiel_etablissement", NULL);
}


function referentiel_add_etablissement($form){
global $DB;
// creer un etablissement
$id=0;
	if (isset($form->action) && ($form->action=="creer_etablissement")){
		$record=new object();
		$record->num_etablissement = ($form->num_etablissement);
		$record->nom_etablissement = ($form->nom_etablissement);
		$record->adresse_etablissement = ($form->adresse_etablissement);
	    $record->logo_etablissement = ' ';
		$id=$DB->insert_record("referentiel_etablissement", $record);
	}
	return $id;
}

function referentiel_update_etablissement($form){
global $DB;

	$ok=false;	
	// DEBUG
	//print_object($form);
	//echo "<br /> 344 ";

// MAJ etablissement
$ok=true;
	if (isset($form->action) && ($form->action=="modifier_etablissement")){
		$record=new object();
		$record->id = $form->etablissement_id;
		$record->num_etablissement = ($form->num_etablissement);
		$record->nom_etablissement = ($form->nom_etablissement);
		$record->adresse_etablissement = ($form->adresse_etablissement);
		if (isset($form->logo_etablissement)){
            $record->logo_etablissement = ($form->logo_etablissement);
        }
        else{

        }
        
		if(!$DB->update_record("referentiel_etablissement", $record)){
			// echo "<br /> ERREUR UPDATE etablissement\n";
			$ok=false;
		}
		else {
			$ok=true;
		}
		return $ok; 
	}
}

function referentiel_select_etablissement($userid, $etablissement_id, $appli){
$s='';
	$records=referentiel_get_etablissements();
	if ($records){
		$s.="\n".'<form action="'.$appli.'" method="get" id="selectetab'.$userid.'" class="popupform">'."\n";
		$s.='<div><select id="selectetab'.$userid.'_jump" name="jump" size="1" 
onchange="self.location=document.getElementById(\'selectetab'.$userid.'\').jump.options[document.getElementById(\'selectetab'.$userid.'\').jump.selectedIndex].value;">'."\n";
		foreach ($records as $record){
            $str_nom_etablissement=referentiel_nom_connu($record->nom_etablissement);
            if ($etablissement_id==$record->id){
				$s.='	<option value="'.$appli.'&amp;userid='.$userid.'&amp;etablissement_id='.$record->id.'&amp;sesskey='.sesskey().'" selected="selected" >'.$str_nom_etablissement.'</option>'."\n";
			}
			else{
				$s.='	<option value="'.$appli.'&amp;userid='.$userid.'&amp;etablissement_id='.$record->id.'&amp;sesskey='.sesskey().'">'.$str_nom_etablissement.'</option>'."\n";
			}
		}
		$s.='</select></div>'."\n";
		$s.='</form>'."\n";
	}
	return $s;
}

//----------------------------------------------
function referentiel_get_nom_etablissement($id){
global $DB;
	if (!empty($id)){
		$record = $DB->get_record("referentiel_etablissement",  array("id" => "$id"));
		if ($record ){
			return referentiel_nom_connu($record->nom_etablissement);
		}
	}
	return "";
}

//----------------------------------------------
function referentiel_delete_etablissement($id){
global $DB;
// suppression etablissement
	if (!empty($id)){
		// supprimer les enregistrements dependants
		$etudiants=referentiel_get_etudiants(array("ref_etablissement" => "$id"),"ref_etablissement=:ref_etablissement ");
		if ($etudiants) {
			foreach ($etudiants as $etudiant){
				referentiel_etudiant_set_etablissement($etudiant->userid, 0);
			}
		}
		return $DB->delete_records("referentiel_etablissement", array("id" => "$id"));
	}
    return false;
}

function referentiel_get_etablissement($id){
global $DB;
	if (!empty($id)){
		return $DB->get_record("referentiel_etablissement", array("id" => "$id"));
	}
	else 
		return 0; 
}


/**
 * This function returns an referentiel_etablissement id
 *
 * @param NULL
 * @return record
 * @todo Finish documenting this function
 **/
function referentiel_genere_etablissement(){
global $DB;
	$record=new object();
	$record->num_etablissement = 'l_inconnu';
	$record->nom_etablissement = 'l_inconnu';
	$record->adresse_etablissement = 'l_inconnu';
	$record->logo_etablissement = '';
	return $DB->insert_record("referentiel_etablissement", $record);
}

/**
 * This function returns an referentiel_etablissement id
 *
 * @param NULL
 * @return id
 * @todo Finish documenting this function
 **/
function referentiel_get_min_etablissement(){
global $DB;
    $sql="SELECT MIN(id) as minid FROM {referentiel_etablissement}";
    $r=$DB->get_record_sql($sql, NULL);
    // DEBUG
    // echo "<br />DEBUG :: lib_etab.php :: 460\n";
    // print_object($r);
    // exit;
	if (empty($r) || empty($r->minid)){
        $id_etab=referentiel_genere_etablissement();
	}
	else{
		$id_etab=$r->minid;
	}

	return $id_etab;
}

function referentiel_get_id_etablissement($num_etablissement){
global $DB;
	if ($num_etablissement){
		$record = $DB->get_record("referentiel_etablissement", array("num_etablissement" => $num_etablissement));
		if ($record ){
			return $record->id;
		}
	}
	return 0;
}

?>
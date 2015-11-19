<?php
 // $Id:  locallib.php,v 2.0 2011/04/20 00:00:00 jfruitet Exp $
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
 * @version $Id: lib.php,v 2.0 2011/04/20 00:00:00 jfruitet Exp $
 * @package referentiel v 6.0.00 2011/20/01 00:00:00
 **/

// Version Moodle 2

require_once($CFG->libdir . '/portfolio/caller.php');
require_once($CFG->libdir . '/filelib.php');
require_once('lib.php');

require_once ("lib_config.php");
require_once ("lib_referentiel.php");     // MODIF JF 2012/03/08
require_once ("lib_users.php");

require_once ("lib_accompagnement.php");

require_once ("lib_protocole.php"); // protocole de certification // A partir de la version 2.1.05
require_once ("lib_backup.php");   // nouveauté Moodle 2.0


// Artefact MAHARA_REFERENTIEL installé sur Mahara ?
// Au 1/12/2011 cet artefact n'est pas disponible et donc inutile d'essayer de l'utiliser !
define ('MAHARA_ARTEFACT_REFERENTIEL', 0);   // placer à 1 pour activer le traitement
// define ('MAHARA_ARTEFACT_REFERENTIEL', 1);   // placer à 0 pour désactiver le traitement



/// FONCTIONS UTILITAIRES /////////////////////////////////////////////////////////////////////////
// -------------------------------------
function referentiel_shortstring($s, $lmax=512){
    if ($s){
        $l=mb_strlen($s, 'utf-8');
        if ($l>$lmax){
            return mb_strimwidth($s, 0, $lmax, ' (...) ', 'utf-8');
        }
    }
    return $s;
}

/**
 * This function return an short string
 *
 * @param string
 * @return string 10 first + ten last caracters
 * @todo Finish documenting this function
 **/
function referentiel_shortname($s){
    if ($s){
        $l=mb_strlen($s, 'utf-8');
        if ($l>30){
            $deb=mb_strimwidth($s, 0, 12, '...', 'utf-8');
            $fin=mb_strimwidth($s, $l-11, $l-1, '', 'utf-8');
            return $deb.$fin;
        }
    }
    return $s;
}

// -------------------------------------------------------------
function referentiel_nom_court($nom, $len=13){
    if (!empty($nom) && (mb_strlen($nom, 'latin1')>$len)){
        return mb_substr($nom,0,$len).'. ';
    }
    else{
        return $nom;
    }
}

// ------------------------------------------------------------------
function referentiel_nom_prenom($nom, $prenom, $lenmax=24){
    $len_nom=mb_strlen($nom, 'latin1');
    $len_prenom=mb_strlen($prenom, 'latin1');
    if ($len_nom+$len_prenom>$lenmax){
        if ($len_nom>(2*$lenmax/3)){
            $nom=mb_substr($nom,0,2*$lenmax/3).'.';
            $len_nom=mb_strlen($nom, 'latin1');
        }
        $prenom=mb_substr($prenom,0,$lenmax-$len_nom).'. ';
    }
    return $nom.' '.$prenom;
}


/**
* @param string Y:2008m:09d:26
* @return timestamp
*/
function referentiel_date_special_date($date_special){
	// Y:2008m:09d:26 -> 2008/09/26
	$ladate="";
	$matches=array();
	preg_match("/Y:(\d+)m:(\d+)d:(\d+)/",$date_special,$matches);
	// print_r($matches);
	if (isset($matches[1]) && $matches[1]){
		$ladate=$matches[1];
	    if (isset($matches[2]) && $matches[2]){
			$ladate.='/'.$matches[2];
		    if (isset($matches[3]) && $matches[3]){
				$ladate.='/'.$matches[3];
			}
		}
	}
	return $ladate;
}

/**
* @param int timestamp
* @return string Y:2008m:09d:26
*/
function referentiel_timestamp_date_special($timestamp){
	// 1222380000 -> Y:2008m:09d:26
	$ladate="Y:".date("Y",$timestamp)."m:".date("m",$timestamp)."d:".date("d",$timestamp);
	return $ladate;
}

/**
* @param string Y:2008m:09d:26
* @return string Y/m/d
*/
function referentiel_date_special_timestamp($date_special){
	// Y:2008m:09d:26 -> 1222380000
	$ladate="";
	$matches=array();
	preg_match("/Y:(\d+)m:(\d+)d:(\d+)/",$date_special,$matches);
	// print_r($matches);
	if (isset($matches[1]) && $matches[1]){
		$ladate=$matches[1];
	    if (isset($matches[2]) && $matches[2]){
			$ladate.='/'.$matches[2];
		    if (isset($matches[3]) && $matches[3]){
				$ladate.='/'.$matches[3];
			}
		}
	}
	return strtotime($ladate);
}


/***
TABLES referentiel_referentiel
*/


/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in pass_inc.php) this function
 * checks the md5 pass
 *
 * @return int The boolean
 **/
function referentiel_check_pass($referentiel_referentiel, $pass){
//
	if (!empty($pass)){
		$pass=md5($pass);
		if (isset($referentiel_referentiel->pass_referentiel) && ($referentiel_referentiel->pass_referentiel!='')){
			return ($referentiel_referentiel->pass_referentiel==$pass);
		}
		else{
			return 1;
		}
	}
    return 0;
}

/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in pass_inc.php) this function
 * set the md5 pass
 *
 * @return int The boolean
 **/
 function referentiel_set_pass($referentiel_referentiel_id, $pass){
 global $DB;
// met à jour le mot de passe
	if (!empty($pass)){
		// MD5
		$pass_referentiel=md5($pass);
        //  sauvegarde
    	$ok=false;
        if (!empty($referentiel_referentiel_id) && !empty($pass_referentiel)){
            $ok=$DB->set_field('referentiel_referentiel','pass_referentiel',"$pass_referentiel", array("id" => "$referentiel_referentiel_id"));
	    }
        if ($ok) return 1;
	}
	return 0;
}



/**
 * Given referentiel_referentiel id
 * this function
 * will return a list of referentiel instance.
 *
 * @param  referentiel_referentiel id
 * @return a array of instance id
 **/
function referentiel_referentiel_list_of_instance($id){
// liste ds instances associees a ce referentiel
global $DB;
	if (!empty($id)){
		// id referentiel doit Ãªtre numerique
		$id = intval(trim($id));
		$params= array("id" => "$id");
		$sql="SELECT id FROM {referentiel} WHERE ref_referentiel=:id";
		$records_instance=$DB->get_records_sql($sql, $params);
		if ($records_instance){
			// DEBUG
			// echo "<br />DEBUG :: lib.php :: 1309 <br />";
			// print_object($records_instance);
			// echo "<br />";
			// exit;
			return ($records_instance);
		}
	}
	return NULL;
}

/**
 * Given referentiel_referentiel id
 * this function
 * will return a list of referentiel instance records.
 *
 * @param  referentiel_referentiel id
 * @return a array of instance id
 **/
function referentiel_referentiel_get_instances($id){
// liste des instances associees a ce referentiel
global $DB;
	if (!empty($id)){
		// id referentiel doit etre numerique
		$id = intval(trim($id));
		$records_instance=$DB->get_records("referentiel", array("ref_referentiel" => "$id"));
		if ($records_instance){
			// DEBUG
			// echo "<br />DEBUG :: lib.php :: 4162 <br />";
			// print_object($records_instance);
			// echo "<br />";
			// exit;
			return ($records_instance);
		}
	}
	return NULL;
}

/**
 * Given an id of  referentiel_referentiel,
 * this function
 * will delete all object associated to this referentiel_referentiel.
 *
 * @param id
 * @return boolean Success/Fail
 **/
function referentiel_delete_referentiel_domaines($id) {
global $DB;
$ok_domaine=true;
$ok_competence=true;
$ok_item=true;

$ok=true;
	// verifier existence
    if (!$id) return false;
	if (!$referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$id"))) {
        return false;
    }

    # Delete any dependent records here #
    if ($domaines = $DB->get_records("referentiel_domaine", array("ref_referentiel" => "$id"))) {
		// DEBUG
		// print_object($domaines);
		// echo "<br />";
		foreach ($domaines as $domaine){
			// Competences
			if ($competences = $DB->get_records("referentiel_competence", array("ref_domaine" => "$domaine->id"))) {
				// DEBUG
				// print_object($competences);
				// echo "<br />";
				// Item
				foreach ($competences as $competence){
					if ($items = $DB->get_records("referentiel_item_competence", array("ref_competence" => "$competence->id"))) {
						// DEBUG
						// print_object($items);
						// echo "<br />";
						foreach ($items as $item){
							// suppression
							$ok_item=$ok_item && $DB->delete_records("referentiel_item_competence", array("id" => "$item->id"));
						}
					}
					$ok_competence=$ok_competence && $DB->delete_records("referentiel_competence", array("id" => "$competence->id"));
				}
			}
			// suppression
			$ok_domaine=$ok_domaine && $DB->delete_records("referentiel_domaine", array("id" =>"$domaine->id"));
		}
    }
	// supprimer le protocole
	$DB->delete_records("referentiel_protocol", array("ref_occurrence" =>"$id"));
	// supprimer le bareme
	$DB->delete_records("referentiel_a_scale_ref", array("refrefid" =>"$id"));

    // supprimer l'occurrence
    if (! $DB->delete_records("referentiel_referentiel", array("id" =>"$id"))) {
        $ok = false;
    }

    return ($ok && $ok_domaine && $ok_competence && $ok_item);
}






/**
 * Given a form,
 * this function will permanently delete the activite instance
 * and any document that depends on it.
 *
 * @param object $form
 * @return boolean Success/Failure
 **/

function referentiel_delete_activity($form) {
// suppression activite + document
$ok_activite_detruite=false;
$ok_document=false;
    // DEBUG
	// echo "<br />";
	// print_object($form);
    // echo "<br />";
	if (isset($form->action) && ($form->action=="modifier_activite")){
		// suppression d'une activite et des documents associes
		if (isset($form->activite_id) && ($form->activite_id>0)){
			$activite=referentiel_get_activite($form->activite_id);
			$ok_activite_detruite=referentiel_delete_activity_record($form->activite_id);
			if 	($ok_activite_detruite
				&& $activite->userid>0
				&& ($activite->competences_activite!='')){
				// mise a jour du certificat
				referentiel_mise_a_jour_competences_certificat_user($activite->competences_activite, '', $activite->userid, $activite->ref_referentiel, $activite->approved, true, true);
			}
		}
	}
	else if (isset($form->action) && ($form->action=="modifier_document")){
		// suppression d'un document
		if (isset($form->document_id) && ($form->document_id>0)){
			$ok_document=referentiel_delete_document_record($form->document_id);
		}
	}

    return $ok_activite_detruite or $ok_document;
}


/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $instance An object from the form in activite.html
 * @return int The id of the newly inserted referentiel record
 **/
function referentiel_add_activity($form) {
// creation activite + document
global $USER;
global $DB;
    // DEBUG
    // echo "DEBUG : ADD ACTIVITY CALLED : lib.php : ligne 1033";
	// print_object($form);
    // echo "<br />";
	// referentiel
	$activite = new object();
	$activite->type_activite=($form->type_activite);
	if (!empty($form->code_item)){
		$activite->competences_activite=reference_conversion_code_2_liste_competence('/', $form->code_item);
	}
	else{
		$activite->competences_activite='';
	}
	$activite->description_activite=($form->description_activite);
	$activite->commentaire_activite=($form->commentaire_activite);
	$activite->ref_instance=$form->instance;
	$activite->ref_referentiel=$form->ref_referentiel;
	$activite->ref_course=$form->courseid;
	$ladate=time();
	$activite->date_creation=$ladate;
	$activite->date_modif_student=$ladate;
	$activite->date_modif=0;
	$activite->approved=0;
	$activite->userid=$USER->id;
	$activite->teacherid=0;
	$activite->ref_task=0;

	$activite->mailed=1;  // MODIF JF 2010/10/05  pour empêcher une notification intempesttive
    if (isset($form->mailnow)){
        $activite->mailnow=$form->mailnow;
        if ($form->mailnow=='1'){ // renvoyer
            $activite->mailed=0;   // forcer l'envoi
        }
    }
    else{
      $activite->mailnow=0;
    }

    // DEBUG
    // echo "<br />DEBUG :: lib.php : 1163 : APRES CREATION\n";
	// print_object($activite);
    // echo "<br /> EXIT lib.php Ligne 1734 <br />";
	// exit;
	$activite_id= $DB->insert_record("referentiel_activite", $activite);
	// DEBUG
	// echo "ACTIVITE ID / $activite_id<br />";
	if 	(($activite_id>0) && ($activite->competences_activite!='')){
		// mise a jour du certificat
		referentiel_mise_a_jour_competences_certificat_user('', $activite->competences_activite, $activite->userid, $activite->ref_referentiel, $activite->approved, true, false);
	}


	if 	(isset($activite_id) && ($activite_id>0)
			&&
			(	(isset($form->url_document) && !empty($form->url_document))
				||
				(isset($form->description_document) && !empty($form->description_document))
			)
	){
		$document = new object();
		$document->url_document=($form->url_document);
		$document->type_document=substr($form->type_document,0,20);
		$document->description_document=($form->description_document);
		$document->ref_activite=$activite_id;
		if (isset($form->cible_document)){
			$document->cible_document=$form->cible_document;
   		}
		else{
			$document->cible_document=1;
		}
		if (isset($form->etiquette_document)){
			$document->etiquette_document=$form->etiquette_document;
   		}
		else{
			$document->etiquette_document='';
		}
        // Modif JF 2013/02/02
		$document->timestamp=time();

	   	// DEBUG
		// print_object($document);
    	// echo "<br />";

		$document_id = $DB->insert_record("referentiel_document", $document);
    	// echo "DOCUMENT ID / $document_id<br />";
	}
    return $activite_id;
}

function referentiel_update_activity($form) {
// MAJ activite + document;
global $USER;
global $DB;
$ok=true;
    // DEBUG
	//echo "<br />locallib 492 :: UPDATE ACTIVITY<br />\n";
	//print_object($form);
    //echo "<br />";

	if (isset($form->action) && ($form->action=="modifier_activite")){
        //echo "<br />OK1";
		// recuperer l'ancien enregistrement pour les mises Ã  jour du certificat
		$old_liste_competences='';
		if ($form->activite_id){
			$record_activite=referentiel_get_activite($form->activite_id);
			if ($record_activite){
				$old_liste_competences=$record_activite->competences_activite;
			}
		}
		if (($old_liste_competences=='') && isset($form->old_liste_competences)){
			$old_liste_competences=$form->old_liste_competences;
		}

		// activite
		$activite = new object();
		$activite->id=$form->activite_id;
		$activite->type_activite=($form->type_activite);
		// Modif jf 2013/12/11
		$cf='code_item_'.$form->activite_id;
		//echo '<br/>'.$cf;

		if (isset($form->$cf) && is_array($form->$cf) ){
        	//print_r ($form->$cf);
			//exit;
			$activite->competences_activite=reference_conversion_code_2_liste_competence('/', $form->$cf);
		}
		else if (isset($form->code_item) && is_array($form->code_item)){
			$activite->competences_activite=reference_conversion_code_2_liste_competence('/', $form->code_item);
		}
		else if (isset($form->competences_activite)){
			$activite->competences_activite=$form->competences_activite;
		}
		else{
			$activite->competences_activite='';
		}
		$activite->description_activite=($form->description_activite);
		$activite->commentaire_activite=($form->commentaire_activite);
		$activite->ref_instance=$form->instance;
		$activite->ref_referentiel=$form->ref_referentiel;
		$activite->ref_course=$form->courseid;
		$activite->date_creation=$form->date_creation;
		$activite->approved=$form->approved;
		$activite->userid=$form->userid;
		$activite->teacherid=$form->teacherid;

        $ladate=time();
		// MODIF JF 2009/10/27
		if ($USER->id==$activite->userid){
			$activite->date_modif_student=$ladate;
			$activite->date_modif=$form->date_modif;
			$activite->teacherid=$form->teacherid;
		}
		else{
			$activite->date_modif=$ladate;
			$activite->date_modif_student=$form->date_modif_student;
            $activite->teacherid=$USER->id;
		}

		// MODIF JF 2010/02/11
        if (isset($form->mailnow)){
            $activite->mailnow=$form->mailnow;
            if ($form->mailnow=='1'){ // renvoyer
                $activite->mailed=0;   // annuler envoi precedent
            }
        }
        else{
            $activite->mailnow=0;
        }

		// DEBUG
		//print_object($activite);
	    //echo "<br />";
	    //exit;
		$ok = $ok && $DB->update_record("referentiel_activite", $activite);

	    //echo "DEBUG :: locallib.php :: 572 :: ACTIVITE ID / $activite->id<br />";


		// MODIF JF 2009/09/21
		// mise a zero du certificat associe a cette personne pour ce referentiel
		// referentiel_certificat_user_invalider($activite->userid, $activite->ref_referentiel);
		// referentiel_regenere_certificat_user($activite->userid, $activite->ref_referentiel);
		if 	($ok && ($activite->userid>0)){
			// mise a jour du certificat
			referentiel_mise_a_jour_competences_certificat_user($old_liste_competences, $activite->competences_activite, $activite->userid, $activite->ref_referentiel, $activite->approved, true, $activite->approved);
		}
	}
	else if (isset($form->action) && ($form->action=="modifier_document")){
		$document = new object();
		$document->id=$form->document_id;
		$document->url_document=($form->url_document);
		$document->type_document=substr($form->type_document,0,20);
		$document->description_document=($form->description_document);
		$document->ref_activite=$form->ref_activite;
		if (isset($form->cible_document)){
			$document->cible_document=$form->cible_document;
   		}
		else{
			$document->cible_document=1;
		}
		if (isset($form->etiquette_document)){
			$document->etiquette_document=$form->etiquette_document;
   		}
		else{
			$document->etiquette_document='';
		}
		// Modif JF 2013/02/02
		$document->timestamp=time();
   		// DEBUG
		// print_object($document);
    	// echo "<br />";
		$ok= $ok && $DB->update_record("referentiel_document", $document);
		if ($ok){
            $activite = $DB->get_record('referentiel_activite', array('id' => $document->ref_activite));
            if ($activite){
                if ($USER->id==$activite->userid){
                    $ok=$DB->set_field('referentiel_activite','date_modif_student',time(),array('id'=>$activite->id));
		        }
		        else{
                   	$ok=$DB->set_field('referentiel_activite','date_modif',time(), array('id'=>$activite->id));
		        }
            }
        }

		// exit;
	}
	else if (isset($form->action) && ($form->action=="creer_document")){
		$document = new object();
		$document->url_document=($form->url_document);
		$document->type_document=substr($form->type_document,0,20);
		$document->description_document=($form->description_document);
		$document->ref_activite=$form->ref_activite;
		if (isset($form->cible_document)){
			$document->cible_document=$form->cible_document;
   		}
		else{
			$document->cible_document=1;
		}
		if (isset($form->etiquette_document)){
			$document->etiquette_document=$form->etiquette_document;
   		}
		else{
			$document->etiquette_document='';
		}
		// Modif JF 2013/02/02
		$document->timestamp=time();
   		// DEBUG
		// print_object($document);
    	// echo "<br />";
		$ok = $DB->insert_record("referentiel_document", $document);
    	// echo "DOCUMENT ID / $ok<br />";
        if ($ok){
            $activite = $DB->get_record('referentiel_activite', array('id' => $document->ref_activite));
            if ($activite){
                if ($USER->id==$activite->userid){
                    $ok=$DB->set_field('referentiel_activite','date_modif_student',time(),array('id'=>$activite->id));
		        }
		        else{
                   	$ok=$DB->set_field('referentiel_activite','date_modif',time(), array('id'=>$activite->id));
		        }
            }
        }
	}
    return $ok;
}

// ------------------------------------------
function referentiel_update_document($form) {
global $DB;
global $USER;
// MAJ document;
    // DEBUG
	// echo "<br />UPDATE ACTIVITY<br />\n";
	// print_object($form);
    // echo "<br />";
	if (isset($form->document_id) && $form->document_id
		&&
		isset($form->ref_activite) && $form->ref_activite){
		$document = new object();
		$document->id=$form->document_id;
		$document->url_document=($form->url_document);
		$document->type_document=substr($form->type_document,0,20);
		$document->description_document=($form->description_document);
		$document->ref_activite=$form->ref_activite;
		if (isset($form->cible_document)){
			$document->cible_document=($form->cible_document);
   		}
		else{
			$document->cible_document=1;
		}
		if (isset($form->etiquette_document)){
			$document->etiquette_document=$form->etiquette_document;
   		}
		else{
			$document->etiquette_document='';
		}
        // Modif JF 2013/02/02
		$document->timestamp=time();

   		// DEBUG
		// print_object($document);
    	// echo "<br />";
		if ($DB->update_record("referentiel_document", $document)){
            // marquer l'activité comme modifiée
            $activite = $DB->get_record('referentiel_activite', array('id' => $document->ref_activite));
            if ($activite){
                if ($USER->id==$activite->userid){
                    $ok=$DB->set_field('referentiel_activite','date_modif_student',time(),array('id'=>$activite->id));
		        }
		        else{
                   	$ok=$DB->set_field('referentiel_activite','date_modif',time(), array('id'=>$activite->id));
		        }
            }
            return true;
		}
	}
	return false;
}

function referentiel_add_document($form) {
// MAJ document;
global $DB;
global $USER;
	$id_document=0;
	if (isset($form->ref_activite) && $form->ref_activite){
		$document = new object();
		$document->url_document=($form->url_document);
		$document->type_document=substr($form->type_document,0,20);
		$document->description_document=($form->description_document);
		$document->ref_activite=$form->ref_activite;
		if (isset($form->cible_document)){
			$document->cible_document=$form->cible_document;
   		}
		else{
			$document->cible_document=1;
		}
		if (isset($form->etiquette_document)){
			$document->etiquette_document=$form->etiquette_document;
   		}
		else{
			$document->etiquette_document='';
		}
		// Modif JF 2013/02/02
		$document->timestamp=time();

		// DEBUG
		// print_object($document);
    	// echo "<br />";
		$id_document = $DB->insert_record("referentiel_document", $document);
    	// echo "DOCUMENT ID / $ok<br />";
		// exit;
        // marquer l'activité comme modifiée
        if ($id_document){
            $activite = $DB->get_record('referentiel_activite', array('id' => $document->ref_activite));
            if ($activite){
                if ($USER->id==$activite->userid){
                    $ok=$DB->set_field('referentiel_activite','date_modif_student',time(),array('id'=>$activite->id));
		        }
		        else{
                   	$ok=$DB->set_field('referentiel_activite','date_modif',time(), array('id'=>$activite->id));
		        }
            }
        }

	}
    return $id_document;
}



/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 **/
function referentiel_user_outline($course, $user, $mod, $referentiel) {
    $return= new Object;

	$return->time = $referentiel->date_instance;
    $return->instance = $referentiel->id;
	$return->info = get_string('name_instance','referentiel').' : <i>'.$referentiel->name.'</i>';
	$return->info .= ", ".get_string('description_instance','referentiel').' : <i>'.$referentiel->description_instance.'</i>';

	if (isset($referentiel->ref_referentiel) && ($referentiel->ref_referentiel>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel->ref_referentiel);
		if ($referentiel_referentiel){
			$return->info .= ", ".get_string('name','referentiel').' : <i>'.$referentiel_referentiel->name.'</i>';
			$return->info .= ", ".get_string('code_referentiel','referentiel').' : <i>'.$referentiel_referentiel->code_referentiel.'</i>';
			if (isset($referentiel_referentiel->local) && ($referentiel_referentiel->local!=0)){
				$return->info .= ", ".get_string('referentiel_global','referentiel').' : <i>' . get_string('no').'</i>';
			}
			else{
				$return->info .= ", ".get_string('referentiel_global','referentiel').' : <i>' . get_string('yes').'</i>';
			}
		}
	}
    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 *
 * @todo Finish documenting this function
 **/
function referentiel_user_complete($course, $user, $mod, $referentiel) {
    $return= new Object;
	$return->time = $referentiel->date_instance;
    $return->instance = $referentiel->id;
	$return->info = "<li>".get_string('name_instance','referentiel').' : <i>'.$referentiel->name.'</i>';
	$return->info .="</li><li>".get_string('description_instance','referentiel').' : <i>'.$referentiel->description_instance.'</i>';
	$return->info .="</li><li>".get_string('label_domaine','referentiel').' : <i>'.$referentiel->label_domaine.'</i>';
	$return->info .="</li><li>".get_string('label_competence','referentiel').' : <i>'.$referentiel->label_competence.'</i>';
	$return->info .="</li><li>".get_string('label_item','referentiel').' : <i>'.$referentiel->label_item.'</i>';

	if (isset($referentiel->ref_referentiel) && ($referentiel->ref_referentiel>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel->ref_referentiel);
		if ($referentiel_referentiel){
			$return->info .="</li><li>".get_string('name','referentiel').' : <i>'.$referentiel_referentiel->name.'</i>';
			$return->info .="</li><li>".get_string('code_referentiel','referentiel').' : <i>'.$referentiel_referentiel->code_referentiel.'</i>';
			$return->info .="</li><li>".get_string('description_referentiel','referentiel').' : <i>'.$referentiel_referentiel->description_referentiel.'</i>';
			$return->info .="</li><li>".get_string('url_referentiel','referentiel').' : <i>'.$referentiel_referentiel->url_referentiel.'</i>';
			$return->info .="</li><li>".get_string('seuil_certificat','referentiel').' : <i>'.$referentiel_referentiel->seuil_certificat.'</i>';
			$return->info .="</li><li>".get_string('modification','referentiel').' : <i>'.date("Y/m/d",$referentiel_referentiel->timemodified).'</i>';

			if (isset($referentiel_referentiel->local) && ($referentiel_referentiel->local!=0)){
				$return->info .="</li><li>".get_string('referentiel_global','referentiel').' : <i>' . get_string('no')."</i></li>";
			}
			else{
				$return->info .="</li><li>".get_string('referentiel_global','referentiel').' : <i>' . get_string('yes')."</i></li>";
			}
		}
		$referentiel_certificat=referentiel_get_certificat_user($user->id, $referentiel->ref_referentiel);
		if ($referentiel_certificat){
		/*
 id bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  commentaire_certificat text NOT NULL,
  synthese_certificat text
  competences_certificat text NOT NULL,
  decision_jury varchar(80) NOT NULL DEFAULT '',
  date_decision bigint(10) unsigned NOT NULL DEFAULT '0',
  ref_referentiel bigint(10) unsigned NOT NULL DEFAULT '0',
  userid bigint(10) unsigned NOT NULL,
  teacherid bigint(10) unsigned NOT NULL,
  verrou tinyint(1) unsigned NOT NULL,
  valide tinyint(1) unsigned NOT NULL,
  evalua*/
			$return->info .="</li>\n<li><b>".get_string('certification','referentiel')."</b><ul>\n";
			if ($referentiel_certificat->decision_jury){
				$return->info .="<li>".get_string('certificat_etat','referentiel').' : <i>'.$referentiel_certificat->decision_jury.' ('.date("Y/m/d",$referentiel_certificat->date_decision).")</i></li>";
			}

			if ($referentiel_certificat->verrou!=0){
				$bgcolor=' color="#ffaaaa"';
			}
			else{
				$bgcolor=' color="#aaffaa"';
			}

			// Pas possible car la fonction ne retourne plus rien
			// $return->info .="<li>".get_string('competences_certificat','referentiel').' :<br />'.referentiel_affiche_certificat_consolide('/',':',$referentiel_certificat->competences_certificat, $referentiel->ref_referentiel, $bgcolor)."</li>";
			// ca c'est ok
            $return->info .="<li>".get_string('competences_certificat','referentiel').' :<br />'.$referentiel_certificat->competences_certificat."</li>";
            $return->info .="<li>".get_string('evaluation','referentiel').' : <i>'.$referentiel_certificat->evaluation."</i></li>";
			$return->info .="</ul></li>";
		}
	}

    echo $return->info;
}


// ACTIVITES

/**
 * This function returns record from table referentiel_activite
 *
 * @param id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_activite($id){
global $DB;
	if (isset($id) && ($id>0)){
	    $params = array("id" => "$id");
        $sql="SELECT * FROM {referentiel_activite} WHERE id=:id ";
		return $DB->get_record_sql($sql, $params);
	}
	else
		return 0;
}

/**
 * This function returns records from table referentiel_activite
 *
 * @param referentiel_id referentiel_activite->ref_referentiel : referentiel_referentiel id
 * @param user_id  referentiel_activite->userid : user id
 * @return array of objects
 * @todo Finish documenting this function
 **/
function referentiel_user_activites($referentiel_id, $user_id){
global $DB;
	if (($user_id>0) && ($referentiel_id>0)){
        $params = array("refid" => "$referentiel_id", "userid" => "$user_id");
        $sql="SELECT id FROM {referentiel_activite} WHERE ref_referentiel=:refid AND userid=:userid";
		$records=$DB->get_records_sql($sql, $params);
		return $records;
	}
	else
		return 0;
}

/**
 * This function returns records owned by user_id from table referentiel_activite
 *
 * @param id reference id , user id
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_all_activites_user_course($referentiel_id, $user_id, $course_id, $sql_filtre_where='', $sql_filtre_order=''){
global $DB;
	if (isset($referentiel_id) && ($referentiel_id>0) && isset($course_id) && ($course_id>0)){
        $params = array("refid" => "$referentiel_id", "courseid" => "$course_id", "userid" => "$user_id");
        $sql="SELECT * FROM {referentiel_activite} WHERE ref_referentiel=:refid AND ref_course=:courseid AND userid=:userid  $sql_filtre_where ORDER BY date_creation DESC $sql_filtre_order";
		return $DB->get_records_sql($sql, $params);
    }
	else
		return 0;
}



/**
 * This function returns records owned by user_id from table referentiel_activite for $referentiel_id
 *
 * @param id reference id , user id
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_all_activites_user($referentiel_id, $user_id, $sql_filtre_where='', $sql_filtre_order=''){
global $DB;
// DEBUG
	if (!empty($referentiel_id)){
		if ($sql_filtre_order==''){
			$sql_filtre_order='  date_creation DESC ';
		}
        $params = array("refid" => "$referentiel_id", "userid" => "$user_id");
		$sql = "SELECT * FROM {referentiel_activite} WHERE ref_referentiel=:refid AND userid=:userid  $sql_filtre_where ORDER BY $sql_filtre_order";
		// DEBUG
		// echo "<br />DEBUG :: lib.sql :: Ligne 2459 :: SQL&gt; $sql\n";
		return $DB->get_records_sql($sql, $params);
	}
	else
		return 0;
}

/**
 * This function returns records from table referentiel_activite for referentiel_instance_id and user_id
 *
 * @param id reference activite
 * @param select clause : ' AND champ=valeur,  ... '
 * @param order clause : ' champ ASC|DESC, ... '
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_instance_get_activites_user($referentiel_instance_id, $user_id, $sql_filtre_where='', $sql_filtre_order=''){
global $DB;
	if (!empty($referentiel_instance_id)){
		if ($sql_filtre_order==''){
			$sql_filtre_order='  date_creation DESC ';
		}
        $params = array("refid" => "$referentiel_instance_id", "userid" => "$user_id");
        $sql="SELECT * FROM {referentiel_activite} WHERE ref_instance=:refid AND userid=:userid  $sql_filtre_where ORDER BY $sql_filtre_order";
		return $DB->get_records_sql($sql, $params);
	}
	else
		return NULL;
}


/**
 * This function returns records from table referentiel_activite
 *
 * @param id reference activite
 * @param select clause : ' AND champ=valeur,  ... '
 * @param order clause : ' champ ASC|DESC, ... '
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_users_activites($referentiel_id, $sql_filtre_where='', $sql_filtre_order=''){
global $DB;
    if (!empty($referentiel_id)){
		if ($sql_filtre_order==''){
			$sql_filtre_order=' userid ASC ';
		}
		else{
			$sql_filtre_order=' userid ASC, '.$sql_filtre_order;
		}
		$params = array("refid" => "$referentiel_id");
        $sql="SELECT DISTINCT userid FROM {referentiel_activite} WHERE ref_referentiel=:refid  $sql_filtre_where ORDER BY $sql_filtre_order ";
		return $DB->get_records_sql($sql, $params);
	}
	else
		return 0;
}



/**
 * This function returns records from table referentiel_activite
 *
 * @param id reference activite
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_teachers_activites($referentiel_id){
global $DB;
	if (!empty($referentiel_id)){
	    $params = array("refid" => "$referentiel_id");
        $sql="SELECT DISTINCT teacherid FROM {referentiel_activite} WHERE ref_referentiel=:refid ORDER BY teacherid ASC ";
		return $DB->get_records_sql($sql, $params);
	}
	else
		return 0;
}

/**
 * This function returns records from table referentiel_activite
 *
 * @param id reference activite
 * @param select clause : ' AND champ=valeur,  ... '
 * @param order clause : ' champ ASC|DESC, ... '
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_activites_users_from_instance($referentiel_instance_id, $userid=0, $select="", $order=""){
global $DB;
	if (!empty($referentiel_instance_id)){
		if (empty($order)){
			$order= 'userid ASC, date_creation DESC ';
		}

		if (!empty($userid)) {
    		$params = array("refid" => "$referentiel_instance_id", "userid" => "$userid");
            $sql="SELECT * FROM {referentiel_activite} WHERE ref_instance=:refid AND userid=:userid " . $select. " ORDER BY $order ";
		}
		else {
    		$params = array("refid" => "$referentiel_instance_id");
            $sql="SELECT * FROM {referentiel_activite} WHERE ref_instance=:refid " . $select. " ORDER BY $order ";
        }
		return $DB->get_records_sql($sql, $params);
	}
	else
		return NULL;
}





/**
 * This function returns records from table referentiel_activite
 *
 * @param id reference activite
 * @param select clause : ' AND champ=valeur,  ... '
 * @param order clause : ' champ ASC|DESC, ... '
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_users_activites_instance($referentiel_instance_id, $user_id=0, $select='', $order=''){
global $DB;
	$where='';
	if (!empty($referentiel_instance_id)){
        $params = array("refid" => "$referentiel_instance_id", "userid" => "$user_id");
		if ($user_id!=0){
			$where= ' AND userid=:userid';
		}
		if (empty($order)){
			$order= 'userid ASC, date_creation DESC ';
		}
        $sql="SELECT DISTINCT userid FROM {referentiel_activite} WHERE ref_instance=:refid  $where  $select ORDER BY $order ";
		return $DB->get_records_sql($sql, $params);
	}
	else
		return NULL;
}



/**
 * This function returns record document from table referentiel_document
 *
 * @param id ref_activite
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_documents($activite_id){
global $DB;
	if (!empty($activite_id)){
	    $params = array("id" => "$activite_id");
        $sql="SELECT * FROM {referentiel_document} WHERE ref_activite=:id ORDER BY id ASC ";
    	return $DB->get_records_sql($sql, $params);
    }
	else
		return NULL;
}

/**
 * This function returns number of document from table referentiel_document
 *
 * @param id ref_activite
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_nombre_documents($activite_id){
global $DB;
	if (!empty($activite_id)){
	    $params = array("id" => "$activite_id");
        $sql="SELECT * FROM {referentiel_document} WHERE ref_activite=:id ";
		$r=$DB->get_records_sql($sql, $params);
        // print_r($r) ;
        return (count($r));
	}
	else
		return 0;
}

function referentiel_user_can_addactivity($referentiel) {
    global $USER;
    global $CFG;
    if (!$cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $referentiel->course)) {
        print_error('Course Module ID was incorrect');
    }
    $context = context_module::instance($cm->id);
    if (!has_capability('mod/referentiel:write', $context)) {
        return false;
    }
	return true;
}


function referentiel_activite_isowner($id){
global $USER;
global $DB;
	if (isset($id) && ($id>0)){
		$record=$DB->get_record("referentiel_activite", array("id" => "$id"));
		// DEBUG
		// echo "<br >USERID : $USER->id ; OWNER : $record->userid\n";
		return ($USER->id == $record->userid);
	}
	else
		return false;
}



/**
 * This function return course link
 *
 * @param courseid reference course id
 * @return string
 * @todo Finish documenting this function
 **/
function referentiel_get_course_link($courseid, $complet=false){
global $CFG;
global $DB;
	if ($courseid){
		$that_course=$DB->get_record("course", array("id" => "$courseid"));
		if ($that_course){
            if ($complet){
    			return '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$that_course->id.'" target="_blank">'.$that_course->shortname.'</a> ';
            }
			else{
                return '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$that_course->id.'">'.$that_course->shortname.'</a> ';
            }
		}
	}
	return '';
}


/**
 * This function return an instance link
 *
 * @param courseid reference course id
 * @return string
 * @todo Finish documenting this function
 **/
function referentiel_get_instance_link($instanceid, $complet=false){
global $CFG;
global $DB;
	if ($instanceid){
		$that_instance=$DB->get_record("referentiel", array("id" => $instanceid));
		if ($that_instance){
            if ($complet){
    		    return '<a href="'.$CFG->wwwroot.'/mod/referentiel/view.php?d='.$that_instance->id.'" target="_blank">'.$that_instance->name.'</a> ';
            }
            else{
                return '<a href="'.$CFG->wwwroot.'/mod/referentiel/view.php?d='.$that_instance->id.'" target="_blank">'.referentiel_shortname($that_instance->name).'</a> ';
            }
        }
	}
	return '';
}



// TACHES
// -----------------------
function referentiel_user_can_use_task($referentiel) {
    global $USER;
    global $CFG;
    if (!$cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $referentiel->course)) {
        print_error('Course Module ID was incorrect');
    }
    //$context = context_module::instance($cm->id);
     // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

    if (has_capability('mod/referentiel:addtask', $context) || has_capability('mod/referentiel:viewtask', $context)) {
        return true;
    }
	else{
		return false;
	}
}


// TRAITEMENT DES LISTES code, poids, empreintes

// ----------------
function referentiel_purge_caracteres_indesirables($texte){
	$cherche = array(",", "\"", "'","\r\n", "\n", "\r");
	$remplace= array(" ", " ", " " , " ", " ", " ");
	return str_replace($cherche, $remplace, $texte);
}


function referentiel_initialise_descriptions_items_referentiel($referentiel_referentiel_id){
// calcule la table des descriptions des items de competences
// necessaire Ã  l'affichage des overlib
global $t_item_code; // codes
global $t_item_description_competence; // descriptifs
	$t_item_code=array();
	$t_item_description_competence=array(); // table des descriptions d'item
	$compteur_domaine=0;
	$compteur_competence=0;
	$compteur_item=0;

	// ITEMS
	if (isset($referentiel_referentiel_id) && ($referentiel_referentiel_id>0)){
		$record_a = referentiel_get_referentiel_referentiel($referentiel_referentiel_id);
		$code_referentiel=$record_a->code_referentiel;
		$nb_domaines = $record_a->nb_domaines;
		$liste_codes_competence=$record_a->liste_codes_competence;
		// charger les domaines associes au referentiel courant
		// DOMAINES
		$records_domaine = referentiel_get_domaines($referentiel_referentiel_id);
	   	if ($records_domaine){
			foreach ($records_domaine as $record){
				$domaine_id=$record->id;
				$nb_competences = $record->nb_competences;
				// LISTE DES COMPETENCES DE CE DOMAINE
				$records_competences = referentiel_get_competences($domaine_id);
		    	if ($records_competences){
					foreach ($records_competences as $record_c){
						$competence_id=$record_c->id;
						$nb_item_competences=$record_c->nb_item_competences;
						// ITEM
						$records_items = referentiel_get_item_competences($competence_id);
					    if ($records_items){
							foreach ($records_items as $record_i){
								$t_item_code[$compteur_item]=stripslashes($record_i->code_item);
								$t_item_description_competence[$t_item_code[$compteur_item]]=referentiel_purge_caracteres_indesirables(stripslashes($record_i->description_item));
								$compteur_item++;
							}
						}
						$compteur_competence++;
					}
				}
				$compteur_domaine++;
			}
		}
	}
	return ($compteur_item>0);
}




/**
 * This function sets referentiel_referentiel contents in arrays
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/
function referentiel_initialise_data_referentiel($referentiel_referentiel_id, $mode_calcul=0){
	if ($mode_calcul==0){
		return (referentiel_initialise_data_referentiel_new($referentiel_referentiel_id));
	}
	else{
		return (referentiel_initialise_data_referentiel_old($referentiel_referentiel_id));
	}
}
/**
 * This function sets referentiel_referentiel contents in arrays
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/


/**
 * This function sets referentiel_referentiel contents in arrays
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/

function referentiel_initialise_data_referentiel_new($referentiel_referentiel_id){
/*
Je comprends mieux maintenant ton approche.
Finalement ce que j'appelais POIDS est pour toi quelque chose  comme
EMPREINT * POIDS
et cela donne pour ta formule
SOMME(V / E * P * E) / SOMME(P * E)  =  SOMME(V *  P) / SOMME(P * E)
qui comme tu le disais est equivalent a
SOMME(V / E * P ) / SOMME(P ) si E identique partout...
*/
// calcule la table des coefficients poids/empreintes pour les item, competences, domaines
// necessaire a l'affichage de la liste des 'notes' dans un certificat (pourcentages de competences validees)
// return true or false
global $t_domaine;
global $t_domaine_coeff;
global $t_competence;
global $t_competence_description;
global $t_competence_coeff;
global $t_item_code;
global $t_item_description_competence; // descriptifs
global $t_item_coeff; // coefficients
global $t_item_domaine; // index du domaine associe a un item
global $t_item_competence; // index de la competence associee a un item
global $t_item_poids;
global $t_item_empreinte;
global $t_nb_item_domaine;

// MODIF JF 2012/03/26
// REFERENTIEL
global $max_minimum_referentiel;  // = nb items
global $max_seuil_referentiel;    // = somme sur items (poids*empreinte

// MODIF JF 2012/02/24
global $t_domaine_description;
global $t_competence_description;

// MODIF JF 2012/03/26
// Ajout pour le protocole des minimas
global $t_competence_domaine; // index du domaine associe a une competence
global $t_nb_competence_domaine; // nombre de competences par domaine
global $t_nb_item_competence; // nombre d'item par competences

// MODIF JF 2012/02/24
    // REFERENTIEL
    $max_minimum_referentiel=0;
    $max_seuil_referentiel=0.0;

	$cherche=array();
	$remplace=array();
	$compteur_domaine=0;
	$compteur_competence=0;
	$compteur_item=0;

	// DOMAINES
	$t_domaine=array();
	$t_domaine_coeff=array();
	$t_domaine_description=array(); // table des descriptions d'item

	// COMPETENCES
	$t_competence=array();
	$t_competence_coeff=array();
	$t_competence_description=array(); // table des descriptions d'item


	// ITEMS
	$t_item_domaine=array(); // table des domaines d' item
	$t_item_competence=array(); // table des competences d' item
	$t_item_description_competence=array(); // table des descriptions d'item
	$t_item_code=array();
	$t_item_poids=array();
	$t_item_empreinte=array();
	$t_item_coeff=array(); // poids / empreinte
	$t_nb_item_domaine=array(); // nb item dans le domaine

// MODIF JF 2012/03/26
// Ajout pour le protocole des minimas
	$t_nb_item_competence=array(); // nb items dans la competence
    $t_competence_domaine=array(); // index du domaine associe a une competence
    $t_nb_competence_domaine=array(); // nombre de competences par domaine

	if (isset($referentiel_referentiel_id) && ($referentiel_referentiel_id>0)){
		$record_a = referentiel_get_referentiel_referentiel($referentiel_referentiel_id);
		$code_referentiel=$record_a->code_referentiel;
		$seuil_certificat = $record_a->seuil_certificat;
		$nb_domaines = $record_a->nb_domaines;
		$liste_codes_competence=$record_a->liste_codes_competence;
		$liste_empreintes_competence=$record_a->liste_empreintes_competence;
		/*
		echo "<br />DEBUG :: lib.php :: Ligne 1959 :: ".$code_referentiel." ".$seuil_certificat."\n";
		echo "<br />CODES : ".$liste_codes_competence." EMPREINTES : ".$liste_empreintes_competence."\n";
		echo "<br /><br />".referentiel_affiche_liste_codes_empreintes_competence('/', $liste_codes_competence, $liste_empreintes_competence);
		*/
		// charger les domaines associes au referentiel courant
		// DOMAINES

		$records_domaine = referentiel_get_domaines($referentiel_referentiel_id);
	   	if ($records_domaine){
    		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($records_domaine);

			foreach ($records_domaine as $record){
				$domaine_id=$record->id;
				$nb_competences = $record->nb_competences;
				$t_domaine[$compteur_domaine]=stripslashes($record->code_domaine);
				$t_nb_item_domaine[$compteur_domaine]=0;
				$t_domaine_description[$compteur_domaine]=stripslashes($record->description_domaine);
                // MODIF JF 2012/03/26
                // Ajout pour le protocole des minimas
                $t_nb_competence_domaine[$compteur_domaine]=0;    // Nombre de competences pour ce domaine


				// LISTE DES COMPETENCES DE CE DOMAINE
				$records_competences = referentiel_get_competences($domaine_id);
		    	if ($records_competences){
					// DEBUG
					// echo "<br/>DEBUG :: COMPETENCES <br />\n";
					// print_r($records_competences);
					foreach ($records_competences as $record_c){
       					$competence_id=$record_c->id;
						$nb_item_competences=$record_c->nb_item_competences;
						$t_competence[$compteur_competence]=stripslashes($record_c->code_competence);
						$t_nb_item_competence[$compteur_competence]=0;
						$t_competence_description[$compteur_competence]=stripslashes($record_c->description_competence);

						// ITEM
						$records_items = referentiel_get_item_competences($competence_id);
					    if ($records_items){
							foreach ($records_items as $record_i){
								$t_item_code[$compteur_item]=stripslashes($record_i->code_item);
								$t_item_description_competence[$t_item_code[$compteur_item]]=referentiel_purge_caracteres_indesirables(stripslashes($record_i->description_item));
								$t_item_poids[$compteur_item]=$record_i->poids_item;
								$t_item_empreinte[$compteur_item]=$record_i->empreinte_item;
								$t_item_domaine[$compteur_item]=$compteur_domaine;
								$t_item_competence[$compteur_item]=$compteur_competence;
								$t_nb_item_domaine[$compteur_domaine]++;
								$t_nb_item_competence[$compteur_competence]++;
								$compteur_item++;
							}
						}

						// MODIF JF 2012/03/26
                        $t_competence_domaine[$compteur_competence]=$compteur_domaine; // index du domaine associe a une competence
                        $t_nb_competence_domaine[$compteur_domaine]++; // nombre de competence par domaine//

						$compteur_competence++;
					}
				}
				$compteur_domaine++;
			}
		}

		// consolidation
		// somme des poids pour les domaines
		for ($i=0; $i<count($t_domaine); $i++){
			$t_domaine_coeff[$i]=0.0;
		}
		for ($i=0; $i<count($t_item_poids); $i++){
			if (($t_item_poids[$i]) && ($t_item_empreinte[$i])){
				// $t_domaine_coeff[$t_item_domaine[$i]]+= ((float)$t_item_poids[$i] / (float)$t_item_empreinte[$i]);
				$t_domaine_coeff[$t_item_domaine[$i]]+= (float)$t_item_poids[$i] * (float)$t_item_empreinte[$i];
			}
		}

		// somme des poids pour les competences
		for ($i=0; $i<count($t_competence); $i++){
			$t_competence_coeff[$i]=0.0;
		}
		for ($i=0; $i<count($t_item_poids); $i++){
			if (($t_item_poids[$i]>0.0) && ($t_item_empreinte[$i]>0)){
				// $t_competence_coeff[$t_item_competence[$i]]+= ((float)$t_item_poids[$i] / (float)$t_item_empreinte[$i]);
				$t_competence_coeff[$t_item_competence[$i]]+= (float)$t_item_poids[$i] * (float)$t_item_empreinte[$i];
// MODIF JF 2012/03/26
				$max_seuil_referentiel+=(float)$t_item_poids[$i] * (float)$t_item_empreinte[$i];
			}
		}

		// coefficient poids / empreinte pour les items
		for ($i=0; $i<count($t_competence); $i++){
			$t_item_coeff[$i]=0.0;
		}
		for ($i=0; $i<count($t_item_poids); $i++){
			if (($t_item_poids[$i]) && ($t_item_empreinte[$i])){
				$t_item_coeff[$i] = (float)$t_item_poids[$i];
			}
		}
	}
// MODIF JF 2012/03/26
	// veleur max = nb items pour ce refrentiel
	$max_minimum_referentiel=$compteur_item;


	return ($compteur_item>0);
}




/**
 * This function sets referentiel_referentiel contents in arrays
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/

function referentiel_initialise_data_referentiel_old($referentiel_referentiel_id){
// calcule la table des coefficients poids/empreintes pour les item, competences, domaines
// necessaire Ã  l'affichage de la liste des 'notes' dans un certificat (pourcentages de competences validees)
// return true or false
/*
ALGO

SOMME(V / E * P ) / SOMME(P)

*/
global $t_domaine;
global $t_domaine_coeff;
global $t_competence;
global $t_competence_coeff;
global $t_item_code;
global $t_item_description_competence; // descriptifs
global $t_item_coeff; // coefficients
global $t_item_domaine; // index du domaine associÃ© Ã  un item
global $t_item_competence; // index de la competence associÃ©e Ã  un item
global $t_item_poids;
global $t_item_empreinte;
global $t_nb_item_domaine;
global $t_nb_item_competence;

	$cherche=array();
	$remplace=array();
	$compteur_domaine=0;
	$compteur_competence=0;
	$compteur_item=0;

	// DOMAINES
	$t_domaine=array();
	$t_domaine_coeff=array();

	// COMPETENCES
	$t_competence=array();
	$t_competence_coeff=array();

	// ITEMS
	$t_item_domaine=array(); // table des domaines d' item
	$t_item_competence=array(); // table des competences d' item
	$t_item_description_competence=array(); // table des descriptions d'item
	$t_item_code=array();
	$t_item_poids=array();
	$t_item_empreinte=array();
	$t_item_coeff=array(); // poids / empreinte
	$t_nb_item_domaine=array(); // nb item dans le domaine
	$t_nb_item_competence=array(); // nb items dans la competence

	if (isset($referentiel_referentiel_id) && ($referentiel_referentiel_id>0)){
		$record_a = referentiel_get_referentiel_referentiel($referentiel_referentiel_id);
		$code_referentiel=$record_a->code_referentiel;
		$seuil_certificat = $record_a->seuil_certificat;
		$nb_domaines = $record_a->nb_domaines;
		$liste_codes_competence=$record_a->liste_codes_competence;
		$liste_empreintes_competence=$record_a->liste_empreintes_competence;
		/*
		echo "<br />DEBUG :: lib.php :: Ligne 1959 :: ".$code_referentiel." ".$seuil_certificat."\n";
		echo "<br />CODES : ".$liste_codes_competence." EMPREINTES : ".$liste_empreintes_competence."\n";
		echo "<br /><br />".referentiel_affiche_liste_codes_empreintes_competence('/', $liste_codes_competence, $liste_empreintes_competence);
		*/
		// charger les domaines associes au referentiel courant
		// DOMAINES

		$records_domaine = referentiel_get_domaines($referentiel_referentiel_id);
	   	if ($records_domaine){
    		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($records_domaine);

			foreach ($records_domaine as $record){
				$domaine_id=$record->id;
				$nb_competences = $record->nb_competences;
				$t_domaine[$compteur_domaine]=stripslashes($record->code_domaine);
				$t_nb_item_domaine[$compteur_domaine]=0;

				// LISTE DES COMPETENCES DE CE DOMAINE
				$records_competences = referentiel_get_competences($domaine_id);
		    	if ($records_competences){
					// DEBUG
					// echo "<br/>DEBUG :: COMPETENCES <br />\n";
					// print_r($records_competences);
					foreach ($records_competences as $record_c){
       					$competence_id=$record_c->id;
						$nb_item_competences=$record_c->nb_item_competences;
						$t_competence[$compteur_competence]=stripslashes($record_c->code_competence);
						$t_nb_item_competence[$compteur_competence]=0;

						// ITEM
						$records_items = referentiel_get_item_competences($competence_id);
					    if ($records_items){
							foreach ($records_items as $record_i){
								$t_item_code[$compteur_item]=stripslashes($record_i->code_item);
								$t_item_description_competence[$t_item_code[$compteur_item]]=referentiel_purge_caracteres_indesirables(stripslashes($record_i->description_item));
								$t_item_poids[$compteur_item]=$record_i->poids_item;
								$t_item_empreinte[$compteur_item]=$record_i->empreinte_item;
								$t_item_domaine[$compteur_item]=$compteur_domaine;
								$t_item_competence[$compteur_item]=$compteur_competence;
								$t_nb_item_domaine[$compteur_domaine]++;
								$t_nb_item_competence[$compteur_competence]++;
								$compteur_item++;
							}
						}
						$compteur_competence++;
					}
				}
				$compteur_domaine++;
			}
		}

		// consolidation
		// somme des poids pour les domaines
		for ($i=0; $i<count($t_domaine); $i++){
			$t_domaine_coeff[$i]=0.0;
		}
		for ($i=0; $i<count($t_item_poids); $i++){
			if (($t_item_poids[$i]) && ($t_item_empreinte[$i])){
				// $t_domaine_coeff[$t_item_domaine[$i]]+= ((float)$t_item_poids[$i] / (float)$t_item_empreinte[$i]);
				$t_domaine_coeff[$t_item_domaine[$i]]+= (float)$t_item_poids[$i];
			}
		}

		// somme des poids pour les competences
		for ($i=0; $i<count($t_competence); $i++){
			$t_competence_coeff[$i]=0.0;
		}
		for ($i=0; $i<count($t_item_poids); $i++){
			if (($t_item_poids[$i]>0.0) && ($t_item_empreinte[$i]>0)){
				// $t_competence_coeff[$t_item_competence[$i]]+= ((float)$t_item_poids[$i] / (float)$t_item_empreinte[$i]);
				$t_competence_coeff[$t_item_competence[$i]]+= (float)$t_item_poids[$i];
			}
		}

		// coefficient poids / empreinte pour les items
		for ($i=0; $i<count($t_competence); $i++){
			$t_item_coeff[$i]=0.0;
		}
		for ($i=0; $i<count($t_item_poids); $i++){
			if (($t_item_poids[$i]) && ($t_item_empreinte[$i])){
				$t_item_coeff[$i] = ((float)$t_item_poids[$i] / (float)$t_item_empreinte[$i]);
			}
		}
	}
	return ($compteur_item>0);
}


// ----------------
function referentiel_affiche_tableau_1d_old($tab_1d){
// DEBUG
	if ($tab_1d){
		echo '<table border="1"><tr>'."\n";
		for ($i=0;$i<count($tab_1d); $i++){
			echo '<td>'.$tab_1d[$i].'</td>'."\n";
		}
		echo '</tr></table>'."\n";
	}
}

// ----------------
function referentiel_affiche_tableau_1d($tab_1d){
// DEBUG
	if ($tab_1d){
		echo '<table border="1"><tr>'."\n";
		foreach ($tab_1d as $val){
			echo '<td>'.$val.'</td>'."\n";
		}
		echo '</tr></table>'."\n";
	}
}

// ----------------
function referentiel_affiche_tableau($tab_1d){
// DEBUG
	if ($tab_1d){
		echo '<table border="1"><tr>'."\n";
		foreach ($tab_1d as $val){
			echo '<td>'.$val.'</td>'."\n";
		}
		echo '</tr></table>'."\n";
	}
}

// ------------------------------
function referentiel_affiche_data_referentiel($referentiel_referentiel_id, $params=NULL){
//
global $OK_REFERENTIEL_DATA;
global $t_domaine;
global $t_domaine_coeff;

// COMPETENCES
global $t_competence;
global $t_competence_coeff;

// ITEMS
global $t_item_code;
global $t_item_description_competence;
global $t_item_coeff; // poids / empreinte
global $t_item_domaine; // index du domaine associÃ© Ã  un item
global $t_item_competence; // index de la competence associÃ©e Ã  un item
	if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false)){
		$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($referentiel_referentiel_id);
	}
	if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){
		$label_d="";
		$label_c="";
		$label_i="";
		if (isset($params) && !empty($params)){
			if (isset($params->label_domaine)){
				$label_d=$params->label_domaine;
			}
			if (isset($params->label_competence)){
				$label_c=$params->label_competence;
			}
			if (isset($params->label_item)){
				$label_i=$params->label_item;
			}
		}

		// affichage
		// DOMAINES
		echo "<br />DOMAINES<br />\n";
		if (!empty($label_d)){
			p($label_d);
		}
		else {
			p(get_string('domaine','referentiel'));
		}

		echo '<br />'."\n";
		referentiel_affiche_tableau_1d($t_domaine);
		echo "<br />DOMAINES COEFF\n";
		referentiel_affiche_tableau_1d($t_domaine_coeff);

		echo "<br />COMPETENCES\n";
		if (!empty($label_c)){
			p($label_c);
		}
		else {
			p(get_string('competence','referentiel')) ;
		}
		echo '<br />'."\n";
		referentiel_affiche_tableau_1d($t_competence);
		echo "<br />COMPETENCES COEFF\n";
		referentiel_affiche_tableau_1d($t_competence_coeff);

		// ITEMS
		echo "<br />ITEMS\n";
		if (!empty($label_i)){
			p($label_i);
		}
		else {
			p(get_string('item_competence','referentiel')) ;
		}

		echo '<br />'."\n";
		echo "<br />CODES ITEM\n";
		referentiel_affiche_tableau_1d($t_item_code);
		echo "<br />DESCRIPTION ITEM\n";
		referentiel_affiche_tableau($t_item_description_competence);
		echo "<br />COMPETENCES COEFF\n";
		referentiel_affiche_tableau_1d($t_item_coeff);

		echo "<br />POIDS ITEM\n";
		referentiel_affiche_tableau_1d($t_item_poids);
		echo "<br />EMPREINTES ITEM\n";
		referentiel_affiche_tableau_1d($t_item_empreinte);
	}
}





// TACHES


// CERTIFICATS
/**
 * This function returns record of certificat from table referentiel_certificat
 *
 * @param id reference certificat id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_certificat($id){
global $DB;
	if (!empty($id)){
    	$params= array("id" => "$id");
        $sql="SELECT * FROM {referentiel_certificat} WHERE id=:id ";
		return $DB->get_record_sql($sql, $params);
	}
	else
		return 0;
}


/**
 * This function returns records of certificat from table referentiel_certificat
 *  order by lastname, firstname of userid
 * @param id reference referentiel (no instance)
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_certificats_users($refrefid, $userid=0){
global $DB;
	if (!empty($referentiel_referentiel_id)){
        if (empty($userid)){
            $params=array("refrefid" => "$refrefid");
            $sql = "SELECT c.*, u.firstname, u.lastname
    FROM {referentiel_certificat} as c, user as u
    WHERE c.ref_referentiel=:refrefid
    AND c.userid = u.id ORDER BY u.lastname, u.firstname ";
        }
        else{
            $params=array("refrefid" => "$refrefid", "userid" => "$userid");
            $sql = "SELECT c.*, u.firstname, u.lastname
    FROM {referentiel_certificat} as c, user as u
    WHERE c.ref_referentiel=:refrefid
    AND c.userid =:userid
    AND c.userid = u.id ORDER BY u.lastname, u.firstname ";
        }
    	return $DB->get_records_sql($sql, $params);
	}
	else{
		return NULL;
    }
}


/**
 * This function returns records of certificat from table referentiel_certificat
 *
 * @param id reference referentiel (no instance)
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_all_users_with_certificate($referentiel_referentiel_id, $select="", $order=""){
global $DB;
    if (!empty($referentiel_referentiel_id)){
		if (empty($order)){
			$order= 'userid ASC ';
		}
		else{
			if (!preg_match("/userid/",$order)){
                $order= ' userid ASC, '.$order;
			}
		}
        $params= array("refid" => "$referentiel_referentiel_id");
		$sql="SELECT userid FROM {referentiel_certificat} WHERE ref_referentiel=:refid $select ORDER BY $order ";
		return $DB->get_records_sql($sql, $params);
	}
	else
		return null;
}





// ACCOMPAGNEMENT
// voir lib_accompagnement.php

// REFERENTIEL_REFERENTIEL IMPORT XML

// -----------------------
function referentiel_set_competence_nb_item($competence_id, $nbitems){
global $DB;
    if ($competence_id && ($nbitems>0)){
        $DB->set_field ('referentiel_competence','nb_item_competences',"$nbitems", array("id" => "$competence_id"));
    }
}

// -----------------------
function referentiel_set_domaine_nb_competence($domaine_id, $nbcompetences){
global $DB;
    if ($domaine_id && ($nbcompetences>0)){
        $DB->set_field ('referentiel_domaine','nb_competences',$nbcompetences, array("id" => "$domaine_id"));
    }
}

// -----------------------
function referentiel_set_referentiel_nb_domaine($referentiel_referentiel_id, $nbdomaines){
global $DB;
    if ($referentiel_referentiel_id && ($nbdomaines>0)){
        $DB->set_field ('referentiel_referentiel','nb_domaines',"$nbdomaines", array("id" => "$referentiel_referentiel_id"));
    }
}



// ACTIVITE - MISE  A JOUR EN CAS DE MODIFICATION DE REFERENTIEL

// --------------------
function referentiel_sup_activites_codes_competence($referentiel_referentiel_id, $liste_codes_competence){
// En cas de modification des codes d'item il faut purger les compétences modifiées
global $DB;
// echo "<br />DEBUG :: lib.php :: 5480 :: referentiel_maj_activites_codes_competence()\n";

// A.1.1/A.1.2/A.1.3/A.1.4/A.1.5/A.2.1/A.2.2/A.2.3/A.3.1/A.3.2/A.3.3/A.3.4/B.1.1/B.1.2/B.1.3/B.2.1/B.2.2/B.2.3/B.2.4/B.3.1/B.3.2/B.3.3/B.3.4/B.3.5/B.4.1/B.4.2/B.4.3/\n";
// echo "<br />liste competences referentiel : $liste_codes_competence\n";
// competence activite :: A.2.2/A.2.3/A.3.1/A.3.2/A.3.3/
	if (!empty($referentiel_referentiel_id) && !empty($liste_codes_competence)){
        $params = array("refid" => "$referentiel_referentiel_id");
		$sql="SELECT * FROM {referentiel_activite} WHERE ref_referentiel=:refid ";
		$records_a=$DB->get_records_sql($sql, $params);

		if ($records_a){
            foreach($records_a as $record_a){
                // verifier la présence des codes corrects
                $tab_comp_activites=explode('/',$record_a->competences_activite);
                // echo "<br />Liste\n";
                // print_r($tab_comp_activites);
                $new_liste_comp_activite='';
                $modif=false;
                foreach($tab_comp_activites as $comp){
                    if (!empty($comp)){
                        $pos = strpos($liste_codes_competence, $comp);
                        if ($pos === false) {
                            $modif=true;
                            // DEBUG
                            // echo "<br />DEBUG :: lib.php :: 5494:: referentiel_maj_activites_codes_competence()\n";
                            // echo "<br />La chaîne '$comp' ne se trouve pas dans la chaîne '$liste_codes_competence'";
                        }
                        else{
                            $new_liste_comp_activite.= $comp.'/';
                        }
                    }
                }
                if ($modif){
                    // DEBUG
                    // echo "<br />DEBUG :: lib.php :: 5507:: referentiel_maj_activites_codes_competence()\n";
                    // echo "<br />La chaîne modifiee '$new_liste_comp_activite'";
                    // mettre à jour
                    $ok=$DB->set_field("referentiel_activite", "competences_activite", "$new_liste_comp_activite", array("id" => "$record_a->id"));
                }
            }
        }
    }

}


// --------------------
function referentiel_maj_activites_codes_competence($referentiel_referentiel_id, $old_code, $new_code){
// En cas de modification des codes d'item il faut remplacer les compétences modifiées
global $DB;
// echo "<br />DEBUG :: lib.php :: 8590 :: referentiel_maj_activites_codes_competence()\n";
// echo "<br />OLD CODE : $old_code NEW_CODE : $new_code\n";
// competence activite :: A.2.2/A.2.3/A.3.1/A.3.2/A.3.3/
	if (!empty($referentiel_referentiel_id) && !empty($new_code) && !empty($old_code)){
        $params = array("refid" => "$referentiel_referentiel_id");
		$sql="SELECT * FROM {referentiel_activite} WHERE ref_referentiel=:refid ";
		$records_a=$DB->get_records_sql($sql, $params);

		if ($records_a){
            foreach($records_a as $record_a){
                // verifier la présence des codes corrects
                $liste_comp_activites=$record_a->competences_activite;
                // echo "<br />Liste : $liste_comp_activites\n";
                $new_liste_comp_activite='';
                $modif=false;
                if (!empty($liste_comp_activites)){
                    $pos = strpos($liste_comp_activites, $old_code);
                    if ($pos !== false) {
                        $new_liste_comp_activite=str_replace($old_code, $new_code, $liste_comp_activites);
                        // DEBUG
                        // echo "<br />DEBUG :: lib.php :: 8608 :: referentiel_maj_activites_codes_competence()\n";
                        // echo "<br />La chaîne '$liste_comp_activites' doit être remplcée par '$new_liste_comp_activite'";
                        // $new_liste_comp_activite.= $comp.'/';
                        $ok=$DB->set_field("referentiel_activite", "competences_activite", "$new_liste_comp_activite", array("id", $record_a->id));
                    }
                }
            }
        }
    }

}


// --------------------
function referentiel_liste_groupes_user($courseid, $userid){
global $CFG;
    if ($usergroups = groups_get_all_groups($courseid, $userid)){
        $groupstr = '';
        foreach ($usergroups as $group){
            $groupstr .= ' <a href="'.$CFG->wwwroot.'/user/index.php?id='.$courseid.'&amp;group='.$group->id.'">'.format_string($group->name).'</a>,';
        }
        return get_string("group").':' . rtrim($groupstr, ', ');
    }
    return '';
}

// ##################### VaLIDITE DES CERTIFICAT ################
// MODIF JF 2012/10/07
// Ces fontions ont completement changé d'usage
// Elle servent désormais à valider définitivement un certificat
// a savoir indiquer une decision du jury
// Un certificat validé n'est plus modifiable (il est définitivement verrouillé)

/**
 * Given a userid  and refrefid
 * this function will return true of false
 *
 * @param $userid user id
 * @param $refrefid referentiel_referentiel id
 * @return
 **/


function referentiel_certificat_user_is_closed($userid, $refrefid){
global $DB;
	if ($userid && $refrefid){
		if ($certificat = $DB->get_record("referentiel_certificat", array("userid" => $userid, "ref_referentiel" => $refrefid)))
		{
			return ($certificat->valide==1);
        }
	}
	return false;
}


/**
 * Given a userid  and refrefid
 * this function will set certificat valide and verrou to 1
 *
 * @param $userid user id
 * @param $refrefid referentiel_referentiel id
 * @return
 **/
function referentiel_certificat_user_close($userid, $refrefid){
global $DB;
    $ok=false;
	if ($userid && $refrefid){
		if ($certificat = $DB->get_record("referentiel_certificat", array("userid" => $userid, "ref_referentiel" => $refrefid)))
		{
            $ok=$DB->set_field('referentiel_certificat','valide', 1, array("id" => $certificat->id));
            $ok=$ok && $DB->set_field('referentiel_certificat','verrou', 1, array("id" => $certificat->id));
        }
	}
	return $ok;
}


/**
 * Given a userid  and refrefid
 * this function will set certificat valide to 0
 *
 * @param $userid user id
 * @param $refrefid referentiel_referentiel id
 * @return
 **/
function referentiel_certificat_user_open($userid, $refrefid){
global $DB;
    $ok=false;
	if ($userid && $refrefidl){
		if ($certificat = $DB->get_record("referentiel_certificat", array("userid" => $userid, "ref_referentiel" => $refrefid)))
		{
            $ok=$DB->set_field('referentiel_certificat','valide', 0, array("id" => $certificat->id));
        }
	}
	return $ok;
}



/**
 * Library of functions for outside of the core api
 */


/**
 * @package   mod-referentiel
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @copyright 2011 Jean Fruitet  {@link http://univ-nantes.fr}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class referentiel_portfolio_caller extends portfolio_module_caller_base {

    protected $instanceid;
    protected $attachment;
    protected $certificatid;
    protected $report;
    protected $export_format;

    protected $cm;
    protected $course;
    protected $referentiel;
    protected $occurrence;
    protected $certificat;

    private $adresse_retour;

    /**
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'instanceid' => false,
            'attachment'   => false,
            'certificatid'   => false,
            'report'  => false,
            'export_format'  => false,
        );
    }
    /**
     * @param array $callbackargs
     */
    function __construct($callbackargs) {
        parent::__construct($callbackargs);
        if (!$this->instanceid) {
            throw new portfolio_caller_exception('mustprovideinstance', 'referentiel');
        }
        if (!$this->attachment && !$this->certificatid) {
            throw new portfolio_caller_exception('mustprovideattachmentorcertificat', 'referentiel');
        }

        if (!isset($this->report)) {
            throw new portfolio_caller_exception('mustprovidereporttype', 'referentiel');
        }

        if (!isset($this->export_format)) {
            throw new portfolio_caller_exception('mustprovideexportformat', 'referentiel');
        }
        else{
            // echo "<br />:: 86 ::$this->export_format\n";
            if ($this->export_format!=PORTFOLIO_FORMAT_FILE) {
                // depending on whether there are files or not, we might have to change richhtml/plainhtml
                $this->supportedformats = array_merge(array($this->supportedformats), array(PORTFOLIO_FORMAT_RICH, PORTFOLIO_FORMAT_LEAP2A));
            }
        }
    }
    /**
     * @global object
     */
    public function load_data() {
        global $DB;
        global $CFG;
        if ($this->instanceid) {
            if (!$this->referentiel = $DB->get_record('referentiel', array('id' => $this->instanceid))) {
                throw new portfolio_caller_exception('invalidinstanceid', 'referentiel');
            }
            if (!$this->occurrence = $DB->get_record('referentiel_referentiel', array('id' => $this->referentiel->ref_referentiel))) {
                throw new portfolio_caller_exception('invalidoccurrence', 'referentiel');
            }
        }

        if (!$this->cm = get_coursemodule_from_instance('referentiel', $this->referentiel->id)) {
            throw new portfolio_caller_exception('invalidcoursemodule');
        }

        if ($this->certificatid) {
            if (!$this->certificat = $DB->get_record('referentiel_certificat', array('id' => $this->certificatid))) {
                throw new portfolio_caller_exception('invalidcertificat', 'referentiel');
            }
        }

        if (!$this->report){
            $this->adresse_retour= '/mod/referentiel/certificat.php?d='.$this->referentiel->id.'&amp;select_acc=0&amp;mode=listcertif';
        }
        else{
            $this->adresse_retour= '/report/referentiel/archive.php?i='.$this->referentiel->id;
        }

        if ($this->attachment){
            $this->set_file_and_format_data($this->attachment); // sets $this->singlefile for us
            // depending on whether there are files or not, we might have to change richhtml/plainhtml
            if ($this->export_format==PORTFOLIO_FORMAT_FILE){
                $this->add_format(PORTFOLIO_FORMAT_FILE);
            }
        }

    }

    /**
     * @global object
     * @return string
     */
    function get_return_url() {
        global $CFG;
        return $CFG->wwwroot . $this->adresse_retour;
    }
    /**
     * @global object
     * @return array
     */
    function get_navigation() {
        global $CFG;

        $navlinks = array();
        $navlinks[] = array(
            'name' => format_string($this->referentiel->name),
            // 'link' => $CFG->wwwroot . '/mod/referentiel/certificat.php?d='.$this->referentiel->id.'&amp;select_acc=0&amp;mode=listcertif',
            'link' => $CFG->wwwroot . $this->adresse_retour,
            'type' => 'title'
        );
        return array($navlinks, $this->cm);
    }
    /**
     * either a whole discussion
     * a single post, with or without attachment
     * or just an attachment with no post
     *
     * @global object
     * @global object
     * @uses PORTFOLIO_FORMAT_HTML
     * @return mixed
     */
    function prepare_package() {
        global $CFG;
        global $OUTPUT;

        if ($this->attachment){
            // DEBUG
            /*
            echo "<br />".$this->exporter->get('formatclass')." == ".PORTFOLIO_FORMAT_FILE;
            echo "<br />\n";
            print_object($this->exporter) ;
            */
            if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_FILE){
                return $this->get('exporter')->copy_existing_file($this->singlefile);
            }
        }
        else if ($this->certificat && $this->occurrence) {
            if (MAHARA_ARTEFACT_REFERENTIEL){
                redirect(new moodle_url('/mod/referentiel/portfolio/set_mahara_referentiel.php', array('id'=>$this->cm->id, 'certificatid' => $this->certificat->id )));
                die();
            }
            else {
                // exporting a single HTML certificat
                $content_to_export = $this->prepare_certificat($this->certificat);
                $name = 'certificat'.'_'.$this->occurrence->code_referentiel.'_'.$this->certificat->userid.'.html';
                // $manifest = ($this->exporter->get('format') instanceof PORTFOLIO_FORMAT_PLAINHTML);
                $manifest = ($this->exporter->get('format') instanceof PORTFOLIO_FORMAT_RICH);

                // DEBUG
                /*
                echo "<br />DEBUG :: 179 :: CONTENT<br />\n";
                echo($content_to_export);
                echo "<br />MANIFEST : $manifest<br />\n";
                echo "<br />FORMAT ".$this->exporter->get('formatclass')."\n";
                */

                $content=$content_to_export;

                if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
                    $leapwriter = $this->exporter->get('format')->leap2a_writer();
                    // DEBUG
                    //echo "<br />DEBUG :: 169 :: LEAPWRITER<br />\n";
                    //print_object($leapwriter);
                    // exit;
                    if ($leapwriter){
                        // echo "<br />DEBUG :: 190\n";
                        if ($this->prepare_certificat_leap2a($leapwriter, $content_to_export)){
                            // echo "<br />DEBUG :: 175\n";
                            $content = $leapwriter->to_xml();
                            // DEBUG
                            /*
                            echo "<br />DEBUG :: mod/referentiel/locallib_portfolio.php :: 195<br />\n";
                            print_object($content);
                            */
                            $name = $this->exporter->get('format')->manifest_name();
                            //exit;
                        }
                    }
                }
                /*
                // DEBUG
                echo "<br />DEBUG :: 176<br />\n";
                print_object($content);
                */
                $this->get('exporter')->write_new_file($content, $name, $manifest);
            }
        }
    }


    /**
     * @return string
     */
    function get_sha1() {
        $filesha = '';
        try {
            $filesha = $this->get_sha1_file();
        } catch (portfolio_caller_exception $e) { } // no files

        if ($this->referentiel){
            if ($this->attachment) {
                return sha1($filesha . ',' . $this->occurrence->code_referentiel. ',' . $this->referentiel->ref_referentiel);
            }
            else if ($this->certificat) {
                return sha1($filesha . ',' . $this->occurrence->code_referentiel. ',' . $this->referentiel->ref_referentiel. ',' . $this->certificat->userid);
            }
        }
    }

    function expected_time() {
        // a file based export
        if ($this->singlefile) {
            return portfolio_expected_time_file($this->singlefile);
        }
        else{
            //return portfolio_expected_time_db(count($this->certificat));
            return PORTFOLIO_TIME_LOW;
        }
    }

    /**
     * @uses CONTEXT_MODULE
     * @return bool
     */
    function check_permissions() {
    global $CFG;
        // $context = context_module::instance($this->cm->id);
        // Valable pour Moodle 2.1 et Moodle 2.2
        //if ($CFG->version < 2011120100) {
            $context = context_module::instance($this->cm->id);
        //} else {
        //    $context = context_module::instance($this->cm);
        //}

        if ($this->attachment){
            return (has_capability('mod/referentiel:archive', $context));
        }
        else{
            return true;
        }
    }

    /**
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'referentiel');
    }

    public static function base_supported_formats() {
        //return array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_PLAINHTML, PORTFOLIO_FORMAT_LEAP2A);
        return array(PORTFOLIO_FORMAT_FILE);
    }

    /**
     * helper function to add a leap2a entry element
     * that corresponds to a single certificate,
     *
     * the entry/ies are added directly to the leapwriter, which is passed by ref
     *
     * @global object $certificate the stdclass object representing the database record
     * @param portfolio_format_leap2a_writer $leapwriter writer object to add entries to
     * @param string $content  the content of the certificate (prepared by {@link prepare_certificate}
     *
     * @return int id of new entry
     */
    private function prepare_certificat_leap2a(portfolio_format_leap2a_writer $leapwriter, $content) {
    global $USER;
        $order   = array( "&nbsp;",  "\r\n", "\n", "\r");
        $replace = ' ';
        $content=str_replace($order, $replace, $content);
        $title=get_string('certificat', 'referentiel').' '.$this->occurrence->code_referentiel;
        $entry = new portfolio_format_leap2a_entry('certificat' . $this->certificat->id, $title, 'leap2', $content); // proposer ability ?
        $entry->published = time();
        $entry->updated = time();
        $entry->author->id = $this->certificat->userid;
        $entry->summary = '<p><h3>'.get_string('certificat', 'referentiel').' '.get_string('referentiel', 'referentiel').$this->occurrence->code_referentiel.'</h3>'."\n".'<p>'.$this->occurrence->description_referentiel.'</p>';
        $entry->add_category('web', 'any_type', 'Referentiel');
        // DEBUG
        // echo "<br />266 :: ENTRY<br />\n";
        // print_object($entry);
        $leapwriter->add_entry($entry);
        // echo "<br />272 :: LEAPWRITER<br />\n";
        // print_object($leapwriter);

        return $entry->id;
    }

    /**
     * this is a very cut down version of what is in referentiel_certificat print_lib
     *
     * @global object
     * @return string
     */
    private function prepare_certificat() {
        global $DB;
        $output='';
        if (!empty($this->certificat)) {
            $fullname = '';
            $fullnameteacher = get_string('l_inconnu', 'referentiel');

            if(!empty($this->certificat->userid)){
                $user= $DB->get_record('user', array('id' => $this->certificat->userid));
                if ($user){
                    $fullname = fullname($user, true);
                    $login=$user->username;
                }
            }

            if (!empty($this->certificat->teacherid)){
                $teacher= $DB->get_record('user', array('id' => $this->certificat->teacherid));
                if ($teacher){
                    $fullnameteacher =fullname($teacher, true);
                }
            }

            $by = new stdClass();
            $by->name = $fullnameteacher;
            $by->date = date("Y-m-d H:i:s");

            $liste_empreintes=referentiel_purge_dernier_separateur(referentiel_get_liste_empreintes_competence($this->certificat->ref_referentiel), '/');
		    $liste_description_competences_poids=referentiel_purge_dernier_separateur(referentiel_get_liste_poids($this->certificat->ref_referentiel), '|');
            // $liste_competences=referentiel_affiche_competences_certificat('/',':',$this->certificat->competences_certificat, $liste_empreintes);
            $liste_competences=$this->affiche_competences_validees('/',':',$this->certificat->competences_certificat, $liste_empreintes, $liste_description_competences_poids);

            // format the body
            $s='<h3>'.get_string('certification','referentiel').' ';
            if (!empty( $this->occurrence->url_referentiel)){
                $s.=get_string('referentiel', 'referentiel').' <a href="'.$this->occurrence->url_referentiel.'" target="_blank">'.$this->occurrence->code_referentiel.'</a></h3>'."\n";
            }
            else{
                $s.=get_string('referentiel', 'referentiel').' '. $this->occurrence->code_referentiel.'</h3>'."\n";
            }

            $s.='<p><b>'.get_string('name','referentiel').':</b> '.$fullname.' (<i>'.$login.'</i>)<br />'
                    .'<b>'.get_string('userid', 'referentiel').'</b>: #'.$this->certificat->userid.'<br />'
                    .'<b>'.get_string('id', 'referentiel').get_string('certificat', 'referentiel').'</b>: #'.$this->certificat->id.'<br />'
                    .'<b>'.get_string('competences_certificat', 'referentiel').':</b><br />'.$liste_competences.'<br />'."\n";
//                    .'<b>'.get_string('competences_declarees', 'referentiel', $fullname).':</b><br />'.$this->certificat->competences_activite.'<br />'
            if (!empty($this->certificat->verrou)){
                $s.='<i>'.get_string('certificat', 'referentiel').' '.get_string('verrou', 'referentiel').'</i><br />'."\n";
            }

            if (!empty($this->certificat->synthese_certificat)){
                $s.= '<b>'.get_string('synthese_certificat', 'referentiel').':</b> '.$this->certificat->synthese_certificat.'<br />'."\n";
            }
            if (empty($this->certificat->decision_jury)){
                $s.= '<b>'.get_string('decisionnotfound', 'referentiel', date("Y-m-d")).'</b><br />';
            }
            else{
                $s.= '<b>'.get_string('decision_jury', 'referentiel').':</b> '.$this->certificat->decision_jury.'<br />';
            }
            if (!empty($this->certificat->teacherid)){
                $s.= '<b>'.get_string('referent', 'referentiel').':</b> '.referentiel_get_user_info($this->certificat->teacherid).'<br />';
            }
            if (!empty($this->certificat->date_decision)){
                $s.= '<b>'.get_string('date_decision', 'referentiel').':</b> '.userdate($this->certificat->date_decision).'<br />';
            }
            if (!empty($this->certificat->commentaire_certificat)){
                $s.='<b>'.get_string('commentaire_certificat', 'referentiel').': </b>'.$this->certificat->commentaire_certificat.'</p>'."\n";
            }
            $s.='</p>'."\n";

            // echo $s;
            // exit;

            $options = portfolio_format_text_options();
            $format = $this->get('exporter')->get('format');
            $formattedtext = format_text($s, FORMAT_HTML, $options);

            // $formattedtext = portfolio_rewrite_pluginfile_urls($formattedtext, $this->context->id, 'mod_referentiel', 'certificat', $certificat->id, $format);

            $output = '<table border="0" cellpadding="3" cellspacing="1" bgcolor="#333300">';
            $output .= '<tr valign="top" bgcolor="#ffffff"><td>';
            $output .= '<div><b>'.get_string('certificat', 'referentiel').' '. format_string($this->occurrence->code_referentiel).'</b></div>';
            if (!empty($this->certificat->decision_jury)){
                $output .= '<div>'.get_string('proposedbynameondate', 'referentiel', $by).'</div>';
            }
            else{
                $output .= '<div>'.get_string('evaluatedbynameondate', 'referentiel', $by).'</div>';
            }
            $output .= '</td></tr>';
            $output .= '<tr valign="top" bgcolor="#ffffff"><td align="left">';
            $output .= $formattedtext;
            $output .= '</td></tr></table>'."\n\n";

        }
        return $output;
    }

    // ----------------------------------------------------
    function affiche_competences_validees($separateur1, $separateur2, $liste, $liste_empreintes, $liste_poids){

    $t_empreinte=explode($separateur1, $liste_empreintes);
	$t_poids=explode('|', $liste_poids);

    // DEBUG
    /*
    print_r($t_empreinte);
    echo "<br />\n";
    print_r($t_poids);
    echo "<br />\n";
    //exit;
    */
    $s='';
	$tc=array();
	$liste=referentiel_purge_dernier_separateur($liste, $separateur1);
		if (!empty($liste) && ($separateur1!="") && ($separateur2!="")){
			$tc = explode ($separateur1, $liste);
			$i=0;
			while ($i<count($tc)){
				if ($tc[$i]!=''){
					$tcc=explode($separateur2, $tc[$i]);

					if (isset($tcc[1]) && ($tcc[1]>=$t_empreinte[$i])){
						$s.='<b>'.$tcc[0].'</b> '."\n";
						// $s.='<td colspan="3">'.str_replace('#','</td><td><i>',$t_poids[$i]).'</i></td>'."\n";
         				$s.=' '.str_replace('#','<!--',$t_poids[$i]).'-->'."\n";
					    // $s.='<td>'.$t_empreinte[$i].'</td>'."\n";
					    $s.='<br />'."\n";
					}
					/*
                    else{
						$s.='<td> <span class="invalide"><i>'.$tcc[0].'</i></span></td>'."\n";
						//$s.='<td>'.referentiel_jauge_activite($tcc[1], $t_empreinte[$i]).'</td>'."\n";
						$s.='<td colspan="3">'.str_replace('#','</td><td><i>',$t_poids[$i]).'</i></td>'."\n";
					}
					*/
				}
				$i++;
			}
		}
	return $s;
    }



}


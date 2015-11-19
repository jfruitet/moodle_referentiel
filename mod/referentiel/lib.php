<?php  // $Id:  lib.php,v 2.0 2011/04/20 00:00:00 jfruitet Exp $
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
// passage en modele objet

/** Include eventslib.php */
require_once($CFG->libdir.'/eventslib.php');
/** Include formslib.php */
require_once($CFG->libdir.'/formslib.php');
/** Include calendar/lib.php */
require_once($CFG->dirroot.'/calendar/lib.php');

// 2010/10/18 : configuration
require_once ("class/referentiel.class.php");

// les constantes suivantes permettent de tuner le fonctionnement du module
// a ne modifier qu'avec précaution
define('MAXPAGE', 15);// Nombre maximal de pages pour la pagination des affichages
define('MAXPARPAGE', 20);// Nombre d'items par page pour la pagination des affichages

// La bibliothèque Overlib n'est plus intégrée par défaut depuis Moodle 2.3
// or je l'utilise pour afficher les compétences en survol...
$OverlibJs='/mod/referentiel/overlib/overlib.js';

// Blocks/referentiel exists ?
if (is_file($CFG->dirroot.'/blocks/referentiel/lib.php')) {
	define('REFERENTIEL_BLOCK_REF', 1);
}
else{
	define('REFERENTIEL_BLOCK_REF', 0);
}
// Utilise dans le module report/referentiel
define ("JOURS_DESHERENCE", 28);  // delai au dela duquel les activités non évaluées sont signalées au gestionnaire / administrateur

// Mahara atranscript est un artefact Mahara en cours de développement
// Il n'est pas disponible opour le moment
// define ('MAHARA_ARTEFACT_ATRANSCRIPT', 1);  // placer à 0 pour desactiver le traitement
define ('MAHARA_ARTEFACT_ATRANSCRIPT', 0);  // placer à 1 pour activer le traitement

// Suppression des fichiers d'ARCHIVES
define('REFERENTIEL_ARCHIVE_OBSOLETE', 7); // Après 7 jours le cron supprime les fichiers d'Archive

// traitement des activites evaluées par objectifs
define ('REFERENTIEL_OUTCOMES', 1);   // placer à 0 pour désactiver le traitement

// DEBUG ?
// si à 1 le cron devient très bavard :))
// et les messages en attente portent sur une semaine au lieu de deux jours.
define ('REFERENTIEL_DEBUG', 0);    // DEBUG INACTIF
//define ('REFERENTIEL_DEBUG', 1);       // DEBUG ACTIF  : le cron devient très bavard :))
define ('OUTCOMES_SUPER_DEBUG', 0);       // SUPER DEBUG OUTCOMES INACTIF
//define ('OUTCOMES_SUPER_DEBUG', 1);       // SUPER DEBUG OUTCOMES ACTIF : affichage tres detaille

define('MAXLENCODE', 220);// Longueur maximale de la liste des codes d'item
// au delà de laquelle les certificats ne sont plus affichés dans un tableau par la fonction locallib.php::referentiel_affiche_certificat_consolide()

define('TIME_LIMIT', 360);// temps  maximal d'exécution d'un script si PHP ne fonctionne pas en safe_mode

define('EDITOR_ON', 1);// editeur de referentiels simplifié wysiwyg actif (necessite le dossier mod/referentiel/editor sur le serveur)
// define('EDITOR_ON', 0);   // editeur inactif

define('MAXBOITESSELECTION', 4);  // à réduire si le nombre de boites de selection des etudiants
// ne tient pas dans la page sans ascenseur horizontal

define('NOTIFICATION_TACHES', 1); // placer à 1 pour activer la notification
define('NOTIFICATION_ACTIVITES', 1); // placer à 0 pour désactiver la notification
define('NOTIFICATION_CERTIFICATS', 1); // placer à 0 pour désactiver la notification

define('NOTIFICATION_TACHES_AUX_REFERENTS', 0); // placer à 1 pour activer la notification de la tâches aux enseignants du cours ; ctte notification est en general inutile
//define('NOTIFICATION_TACHES_AUX_REFERENTS', 1); // placer à 0 pour desactiver la notification de la tâches aux enseignants du cours ;
define('NOTIFICATION_JURY', 0); // placer à 1 pour activer la notification la certification ; notification en général inutile
define('NOTIFICATION_AUTEUR_ACTIVITE', 0); // placer à 0 pour désactiver la notification de l'auteur de la declaration d'activite ; notification en général inutile

define('NOTIFICATION_DELAI', 0); // placer à la valeur à 1 pour activer la temporisation de la notification par mail entre le moment ou l'activité
// est validée et celui où elle est notifiée
// cela est moins utile que pour les forums ou on prend 30 minutes avant d'envoyer la notification par mail
// car a priori les enseignants ne reviennent pas sur leur validation.

define('NOTIFICATION_INTERVALLE_JOUR', 2); // 2 jours d'intervalle  (ou 2 semaines si REFERENTIEL_DEBUG est à 1 ) de durée d'investigation du cron à la recehrches d'Objectifs à remonter
// augmenter la valeur pour prendre en compte des évaluations anciennes
// cela aura pour effet de réactiver des prise en compte d'évaluation par objectifs
// et de relancer des notifications
// surtout utile pour deboguer si le cron ne s'est pas exercé depuis un temps certain


// CONSTANTES  NE PAS MODIFIER
define('TYPE_ACTIVITE', 0);    // Ne pas modifier
define('TYPE_TACHE', 1);       // Ne pas modifier
define('TYPE_CERTIFICAT', 2);  // Ne pas modifier

define('MAXLIGNEGRAPH', 15);  // Nombre de lignes par graphique





/// Liste des rubriques (non exhaustive)

/// CRON
require_once ("lib_users.php");
require_once ("lib_repartition.php"); // version 1.2 decembre 2011
require_once ("lib_accompagnement.php"); // version 1.2 decembre 2011
require_once ("lib_cron.php");
/// CONFIGURATION

/// USERS
/**
 * This function get all user role in current course
 *
 * @param courseid reference course id
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_course_users($referentiel_instance){
global $DB;
    if ($cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id, $referentiel_instance->course)) {
		// SQL
		$params = array("$referentiel_instance->course", "$referentiel_instance->course", "guest");
		
	    $rq = "SELECT DISTINCT u.id FROM {user} u
LEFT OUTER JOIN
    {user_lastaccess} ul on (ul.courseid = ?)
	WHERE u.deleted = 0
        AND (ul.courseid = ? OR ul.courseid IS NULL)
        AND u.username != ?";
		// DEBUG
		// echo "<br /> DEBUG <br />\n";
		// echo "<br /> lib_users.php :: referentiel_get_course_users() :: 171<br />SQL&gt;$rq\n";

        $ru=$DB->get_records_sql($rq, $params);
		// print_r($ru);
		// exit;
		return $ru;
	}
	return NULL;
}


/// ACCOMPAGNEMENT
/**
 * This function returns records of accompagnement from table referentiel_accompagnement
 *
 * @param id reference instance
 * @return objects
 * @todo Finish documenting this function
 **/
// -----------------------
function referentiel_get_accompagnements($referentiel_id){
global $DB;
	if (isset($referentiel_id) && ($referentiel_id>0)){
        $params=array("refid" => "$referentiel_id");
        $sql="SELECT * FROM {referentiel_accompagnement} WHERE ref_instance=:refid";
		return $DB->get_records_sql($sql, $params);
	}
	else
		return NULL;
}

// -----------------------
function referentiel_delete_accompagnement_record($id) {
// suppression de l'accompagnement associe
global $DB;
    $ok=false;
    if (!empty($id)){
		$ok = $DB->delete_records("referentiel_accompagnement", array("id" => "$id"));
	}
    return $ok;
}

/// ACTIVITES
/**
 * This function returns records from table referentiel_activite
 *
 * @param id reference activite
 * @param select clause : ' AND champ=valeur,  ... '
 * @param order clause : ' champ ASC|DESC, ... '
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_activites($referentiel_id, $select="", $order=""){
global $DB;
	if (!empty($referentiel_id)){
	    $params = array("refid" => "$referentiel_id");

		if (empty($order)){
			$order= 'userid ASC, date_creation DESC ';
		}
		$sql="SELECT * FROM {referentiel_activite} WHERE ref_referentiel=:refid  $select ORDER BY $order ";
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
function referentiel_get_activites_instance($referentiel_instance_id, $select="", $order=""){
global $DB;
	if (isset($referentiel_instance_id) && ($referentiel_instance_id>0)){
		if (empty($order)){
			$order= 'userid ASC, date_creation DESC ';
		}
		$params = array("refid" => "$referentiel_instance_id");
        $sql="SELECT * FROM {referentiel_activite} WHERE ref_instance=:refid  $select ORDER BY $order ";
        return $DB->get_records_sql($sql, $params);
    }

	else
		return NULL;
}

/**
 * Given a document id,
 * this function will permanently delete the document instance
 *
 * @param object $id
 * @return boolean Success/Failure
 **/

function referentiel_delete_document_record($id) {
// suppression document
global $DB;
global $USER;
$ok_document=false;
	if (isset($id) && ($id>0)){
		if ($document = $DB->get_record("referentiel_document", array("id" => "$id"))) {
			//  CODE A AJOUTER SI GESTION DE FICHIERS DEPOSES SUR LE SERVEUR
			// Moodle 2.0
			// A TERMINER
			// DEBUG
            // echo "<br />lib.php :: 1701 :: GESTION DES FICHIERS A TERMINER ICI\n";
            // exit;
            if (!preg_match('/http/', $document->url_document)){
                // Fichier à supprimer
                referentiel_delete_a_file( $document->url_document);
            }
			// mettre a joure la date de l'activite
            $activite = $DB->get_record('referentiel_activite', array('id' => $document->ref_activite));
            if ($activite){
                if ($USER->id==$activite->userid){
                    $ok=$DB->set_field('referentiel_activite','date_modif_student',time(),array('id'=>$activite->id));
		        }
		        else{
                   	$ok=$DB->set_field('referentiel_activite','date_modif',time(), array('id'=>$activite->id));
		        }
            }

            $ok_document= $DB->delete_records("referentiel_document", array("id" => "$id"));
		}
	}
	return $ok_document;
}


/**
 * Given an activity id,
 * this function will permanently delete the activite instance
 * and any document that depends on it.
 *
 * @param object $id
 * @return boolean Success/Failure
 **/

function referentiel_delete_activity_record($id) {
// suppression activite + documents associes
global $DB;
$ok_activite=false;
	if (isset($id) && ($id>0)){
		if ($activite = $DB->get_record("referentiel_activite", array("id" => "$id"))) {
	   		// Delete any dependent records here

			// Si c'est une activite - tache il faut aussi supprimer les liens vers cette tache
			if (isset($activite->ref_task) && ($activite->ref_task>0) && isset($activite->userid) && ($activite->userid>0)){
                $params=array("taskid" => "$activite->ref_task" , "userid" => "$activite->userid");
                $sql="SELECT * FROM {referentiel_a_user_task} WHERE ref_task=:taskid AND ref_user=:userid";
                $a_t_records = $DB->get_records_sql($sql, $params);
				if ($a_t_records){
					foreach ($a_t_records as $a_t_record){
						// suppression
						referentiel_delete_a_user_task_record($a_t_record->id);
					}
				}
			}

			// Si c'est une activite de type devoir il faut aussi supprimer les liens vers cette activite
			if (!empty($activite->id) ){
                $params=array("activiteid" => $activite->id);
                $sql="SELECT * FROM {referentiel_activite_modules} WHERE activiteid=:activiteid";
                $a_m_records = $DB->get_records_sql($sql, $params);
				if ($a_m_records){
					foreach ($a_m_records as $a_m_record){
						// suppression
						$DB->delete_records("referentiel_activite_modules", array("id" => $a_m_record->id));
					}
				}
			}

			// Si il ya un bareme il faut aussi supprimer les liens vers bareme
			if (!empty($activite->id) ){
                $params=array("activiteid" => $activite->id);
                $sql="SELECT * FROM {referentiel_activite_scale} WHERE activiteid=:activiteid";
                $a_b_records = $DB->get_records_sql($sql, $params);
				if ($a_b_records){
					foreach ($a_b_records as $a_b_record){
						// suppression
						$DB->delete_records("referentiel_activite_scale", array("id" => $a_b_record->id));
					}
				}
			}

			$ok_document=true;
			if ($documents = $DB->get_records("referentiel_document", array("ref_activite" => "$id"))) {
				// DEBUG
				// print_object($documents);
				// echo "<br />";
				// suppression des documents associes dans la table referentiel_document
				foreach ($documents as $document){
					// suppression
					$ok_document=$ok_document && referentiel_delete_document_record($document->id);
				}
			}

			// suppression activite
			if ($ok_document){
                $ok_activite = $DB->delete_records("referentiel_activite", array("id" => "$id"));
				if 	($ok_activite
					&& isset($activite->userid) && ($activite->userid>0)
					&& isset($activite->competences_activite) && ($activite->competences_activite!='')){
					// mise a jour du certificat
					referentiel_mise_a_jour_competences_certificat_user($activite->competences_activite, '', $activite->userid, $activite->ref_referentiel, $activite->approved, true, true);
				}
			}
		}
	}
    return $ok_activite;
}



/// TACHES
/**
 * This function returns records from table referentiel_task
 *
 * @param id reference activite
 * @param select clause : ' AND champ=valeur,  ... '
 * @param order clause : ' champ ASC|DESC, ... '
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_tasks_instance($referentiel_instance_id){
global $DB;
	if (isset($referentiel_instance_id) && ($referentiel_instance_id>0)){
    	$params= array("refid" => "$referentiel_instance_id");
        $sql="SELECT * FROM {referentiel_task} WHERE ref_instance=:refid ";
		return $DB->get_records_sql($sql, $params);
	}
	else
		return NULL;
}

/**
 * Given an task id,
 * this function will permanently delete the task instance
 * and any consigne that depends on it.
 *
 * @param object $id
 * @return boolean Success/Failure
 **/

 // -----------------------
function referentiel_delete_task_record($id) {
// suppression task + consignes associes
global $DB;
$ok_task=false;
	if (isset($id) && ($id>0)){
		if ($task = $DB->get_record("referentiel_task", array("id" => "$id"))) {
	   		// Delete any dependent records here
			$ok_association=true;
			if ($r_a_users_tasks = $DB->get_records("referentiel_a_user_task", array("ref_task"=>$id))) {
				// DEBUG
				// print_object($r_a_users_tasks);
				// echo "<br />";
				// suppression des associations
				foreach ($r_a_users_tasks as $r_a_user_task){
					// suppression
					$ok_association=$ok_association && referentiel_delete_a_user_task_record($r_a_user_task->id);
				}
			}

			$ok_consigne=true;
			if ($consignes = $DB->get_records("referentiel_consigne", array("ref_task" => "$id"))) {
				// DEBUG
				// print_object($consignes);
				// echo "<br />";
				// suppression des consignes associes dans la table referentiel_consigne
				foreach ($consignes as $consigne){
					// suppression
					$ok_consigne=$ok_consigne && referentiel_delete_consigne_record($consigne->id);
				}
			}

			// suppression task
			if ($ok_consigne && $ok_association){
                $ok_task = $DB->delete_records("referentiel_task", array("id" => "$id"));
			}
		}
	}
    return $ok_task;
}


/**
 * Given a a_user_task id,
 * this function will permanently delete the instance
 *
 * @param object $id
 * @return boolean Success/Failure

 **/

// -----------------------
function referentiel_delete_a_user_task_record($id){
// suppression association user task
global $DB;
$ok_association=false;
	if (isset($id) && ($id>0)){
		if ($association = $DB->get_record("referentiel_a_user_task", array("id" => "$id"))) {
            $ok_association= $DB->delete_records("referentiel_a_user_task", array("id" => "$id"));
		}
	}
	return $ok_association;
}


/**
 * Given a consigne id,
 * this function will permanently delete the consigne instance
 *
 * @param object $id
 * @return boolean Success/Failure
 **/
// -----------------------
function referentiel_delete_consigne_record($id) {
// suppression consigne
global $DB;
$ok_consigne=false;
	if (!empty($id)){
		if ($consigne = $DB->get_record("referentiel_consigne", array("id" => "$id"))) {
            if (!preg_match('/http/', $consigne->url_consigne)){
                // Fichier à supprimer
                referentiel_delete_a_file($consigne->url_consigne);
            }

			$ok_consigne= $DB->delete_records("referentiel_consigne", array("id" => "$id"));
			if ($ok_consigne){
                $task = $DB->get_record('referentiel_task', array('id' => $consigne->ref_task));
                if ($task){
                    $ok=$DB->set_field('referentiel_task','date_modif',time(), array('id'=>$task->id));
                }
            }
		}
	}
	return $ok_consigne;
}

// COMPETENCES
// TRAITEMENT DES LISTES code, poids, empreintes

/**
 purge
*/
function referentiel_purge_dernier_separateur($s, $sep){
	if ($s){
		$s=trim($s);
		if ($sep){
			$pos = strrpos($s, $sep);
			if ($pos === false) { // note : trois signes Ã©gal
				// pas trouvÃ©
			}
			else{
				// supprimer le dernier "/"
				if ($pos==strlen($s)-1){
					return substr($s,0, $pos);
				}
			}
		}
	}
	return $s;
}


// Liste des codes de competences du referentiel
function referentiel_get_liste_codes_competence($id){
// retourne la liste des codes de competences pour la table referentiel
global $DB;
	if (!empty($id)){
        $params = array("id" => "$id");
        $sql="SELECT * FROM {referentiel_referentiel} WHERE id=:id";
		$record_r=$DB->get_record_sql($sql, $params);
		if ($record_r){
    		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($record_r);
			return ($record_r->liste_codes_competence);
		}
	}
	return 0;
}


// Liste des codes de competences du referentiel
function referentiel_new_liste_codes_competence($id){
// regenere la liste des codes de competences pour la table referentiel
global $DB;
$liste_codes_competence="";
	if (!empty($id)){
        $params = array("id" => "$id");
        $sql="SELECT * FROM {referentiel_referentiel} WHERE id=:id";
		$record_r=$DB->get_record_sql($sql, $params);
		if ($record_r){
    		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($record_r);
			$old_liste_codes_competence=$record_r->liste_codes_competence;
			$liste_codes_competence="";
			// charger les domaines associes au referentiel courant
			$referentiel_id=$id; // plus pratique
			// LISTE DES DOMAINES
            $sql2="SELECT * FROM {referentiel_domaine} WHERE ref_referentiel=:id  ORDER BY num_domaine ASC ";
			$records_domaine = $DB->get_records_sql($sql2, $params);
			if ($records_domaine){
    			// afficher
				// DEBUG
				// echo "<br/>DEBUG ::<br />\n";
				// print_r($records_domaine);
				foreach ($records_domaine as $record_d){
                    $params3 = array("id" => "$record_d->id");
                    $sql3="SELECT * FROM {referentiel_competence} WHERE ref_domaine=:id ORDER BY num_competence ASC ";
					$records_competences = $DB->get_records_sql($sql3, $params3);
			   		if ($records_competences){
						// DEBUG
						// echo "<br/>DEBUG :: COMPETENCES <br />\n";
						// print_r($records_competences);
						foreach ($records_competences as $record_c){
							// ITEM
							$compteur_item=0;
                            $params4 = array("id" => "$record_c->id");
                            $sql4="SELECT * FROM {referentiel_item_competence} WHERE ref_competence=:id ORDER BY num_item ASC ";
							$records_items = $DB->get_records_sql($sql4, $params4);
					    	if ($records_items){
								// DEBUG
								// echo "<br/>DEBUG :: ITEMS <br />\n";
								// print_r($records_items);
								foreach ($records_items as $record_i){
									$liste_codes_competence.=$record_i->code_item."/";
								}
							}
						}
					}
				}
			}
		}
	}
	return $liste_codes_competence;
}

// --------------------
function referentiel_set_liste_codes_competence($id, $liste_codes_competence){
global $DB;
	if (isset($id) && ($id>0)){
        $liste_codes_competence=($liste_codes_competence);
        $ok=$DB->set_field("referentiel_referentiel", "liste_codes_competence", "$liste_codes_competence", array("id" => "$id"));
        if ($ok) return 1;
	}
	return 0;
}


// Liste des poids de competences du referentiel
function referentiel_get_liste_poids_competence($id){
// retourne la liste des poids de competences pour la table referentiel
// MODIF JF 2009/10/16
global $DB;
	if (!empty($id)){
	    $params = array("id" => "$id");
        $sql= "SELECT * FROM {referentiel_referentiel} WHERE id=:id";
		$record_r=$DB->get_record_sql($sql, $params);
		if ($record_r){
    		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($record_r);
			if ($record_r->liste_poids_competence==''){
				$record_r->liste_poids_competence=referentiel_new_liste_poids_competence($id);
				referentiel_set_liste_poids_competence($id, $record_r->liste_poids_competence);
			}
			return ($record_r->liste_poids_competence);
		}
	}
	return 0;
}


// Liste des poids de competences du referentiel
function referentiel_new_liste_poids_competence($id){
// regenere la liste des poids de competences pour la table referentiel
global $DB;
$liste_poids_competence="";
	if (!empty($id)){
	    $params = array("id" => "$id");
        $sql="SELECT * FROM {referentiel_referentiel} WHERE id=:id";
		$record_r=$DB->get_record_sql($sql, $params);
		if ($record_r){
    		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($record_r);
			$old_liste_poids_competence=$record_r->liste_poids_competence;
			$liste_poids_competence="";
			// charger les domaines associes au referentiel courant
			// LISTE DES DOMAINES
			$sql2="SELECT * FROM {referentiel_domaine} WHERE ref_referentiel=:id ORDER BY num_domaine ASC";
			$records_domaine = $DB->get_records_sql($sql2, $params);
			if ($records_domaine){
    			// afficher
				// DEBUG
				// echo "<br/>DEBUG ::<br />\n";
				// print_r($records_domaine);
				foreach ($records_domaine as $record_d){
        			$params3 = array("id" => "$record_d->id");
                    $sql3="SELECT * FROM {referentiel_competence} WHERE ref_domaine=:id ORDER BY num_competence ASC";
					$records_competences = $DB->get_records_sql($sql3, $params3);
			   		if ($records_competences){
						// DEBUG
						// echo "<br/>DEBUG :: COMPETENCES <br />\n";
						// print_r($records_competences);
						foreach ($records_competences as $record_c){
							$params4 = array("id" => "$record_c->id");
                            $sql4="SELECT * FROM {referentiel_item_competence} WHERE ref_competence=:id ORDER BY num_item ASC ";
							// ITEM
							$compteur_item=0;
							$records_items = $DB->get_records_sql($sql4, $params4);
					    	if ($records_items){
								// DEBUG
								// echo "<br/>DEBUG :: ITEMS <br />\n";
								// print_r($records_items);
								foreach ($records_items as $record_i){
									$liste_poids_competence.=$record_i->poids_item."/";
								}
							}
						}
					}
				}
			}
		}
	}
	return $liste_poids_competence;
}

/**
 * Given an id referentiel_referentiel and a list of poids
 * this function will update liste_poids_competence.
 *
 * @param id, list
 * @return boolean Success/Fail
 **/

function referentiel_set_liste_poids_competence($id, $liste_poids_competence){
global $DB;
	if (isset($id) && ($id>0)){
        $ok=$DB->set_field("referentiel_referentiel", "liste_poids_competence", "$liste_poids_competence", array("id" => "$id"));
        if ($ok) return 1;
	}
	return 0;
}

/**
 * Given an array ,
 * return a new liste_codes_competence.
 *
 * @param array $instance An object from the form in mod_activite.html
 * @return string
 **/
function reference_conversion_code_2_liste_competence($separateur, $tab_code_item){
$lc="";
// print_r($tab_code_item);
// echo "<br />DEBUG\n";

	if (count($tab_code_item)>0){
		for ($i=0; $i<count($tab_code_item); $i++){
			$lc.=$tab_code_item[$i].$separateur;
		}
	}
	return $lc;
}


// Liste des empreintes de competences du rÃ©fÃ©rentiel
function referentiel_get_liste_empreintes_competence($id){
// retourne la liste des empreintes de competences pour la table referentiel
global $DB;
	if (!empty($id)){
	    $params = array("id" => "$id");
	    $sql="SELECT * FROM {referentiel_referentiel} WHERE id=:id";
		$record_r=$DB->get_record_sql($sql, $params);
		if ($record_r){
    		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($record_r);
			return ($record_r->liste_empreintes_competence);
		}
	}
	return 0;
}


// Liste des empreintes de competences du referentiel
function referentiel_new_liste_empreintes_competence($id){
// regenere la liste des empreintes de competences pour la table referentiel
global $DB;
$liste_empreintes_competence="";
	if (!empty($id)){
	    $params = array("id" => "$id");
	    $sql="SELECT * FROM {referentiel_referentiel} WHERE id=:id";
		$record_r=$DB->get_record_sql($sql, $params);
		if ($record_r){
    		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($record_r);
			$old_liste_empreintes_competence=$record_r->liste_empreintes_competence;
			$liste_empreintes_competence="";
			// charger les domaines associes au referentiel courant

			// LISTE DES DOMAINES
			$params2= array("refid" => "$id");
            $sql2="SELECT * FROM {referentiel_domaine} WHERE ref_referentiel=:refid ORDER BY num_domaine ASC";
			$records_domaine = $DB->get_records_sql($sql2, $params2);
			if ($records_domaine){
    			// afficher
				// DEBUG
				// echo "<br/>DEBUG ::<br />\n";
				// print_r($records_domaine);
				foreach ($records_domaine as $record_d){
        			$params3 = array("id" => "$record_d->id");
                    $sql3="SELECT * FROM {referentiel_competence} WHERE ref_domaine=:id ORDER BY num_competence ASC";
					$records_competences = $DB->get_records_sql($sql3, $params3);
			   		if ($records_competences){
						// DEBUG
						// echo "<br/>DEBUG :: COMPETENCES <br />\n";
						// print_r($records_competences);
						foreach ($records_competences as $record_c){
                  			$params4 = array("id" => "$record_c->id");
                            $sql4="SELECT * FROM {referentiel_item_competence} WHERE ref_competence=:id ORDER BY num_item ASC";
							// ITEM
							$compteur_item=0;
							$records_items = $DB->get_records_sql($sql4, $params4);
					    	if ($records_items){
								// DEBUG
								// echo "<br/>DEBUG :: ITEMS <br />\n";
								// print_r($records_items);
								foreach ($records_items as $record_i){
									$liste_empreintes_competence.=$record_i->empreinte_item."/";
								}
							}
						}
					}
				}
			}
		}
	}
	return $liste_empreintes_competence;
}

/**
 * Given an id referentiel_referentiel and a list of empreintes
 * this function will update liste_empreintes_competence.
 *
 * @param id, list
 * @return boolean Success/Fail
 **/
function referentiel_set_liste_empreintes_competence($id, $liste_empreintes_competence){
global $DB;
	if (isset($id) && ($id>0)){
        $ok=$DB->set_field('referentiel_referentiel','liste_empreintes_competence', "$liste_empreintes_competence", array("id" => "$id"));
        if ($ok) return 1;
	}
	return 0;
}


// CERTIFICATS
/**
 * Given an certificat id,
 * this function will permanently delete the certificat instance
 *
 * @param object $id
 * @return boolean Success/Failure
 **/

function referentiel_delete_certificat_record($id) {
// suppression certificat
global $DB;
$ok_certificat=false;
	if (!empty($id)){
		if ($certificat = $DB->get_record("referentiel_certificat", array("id" => "$id"))) {
			// suppression
			$ok_certificat = $DB->delete_records("referentiel_certificat", array("id" => "$id"));
		}
	}
    return $ok_certificat;
}


/**
 * Given a referentiel_referentiel id,
 * this function will permanently delete the certificates associated to  it
 *
 * @param object $id
 * @return boolean Success/Failure
 **/

function referentiel_delete_referentiel_certificats($refrefid){
global $DB;
    if ($refrefid){
        return $DB->delete_records("referentiel_certificat", array("ref_referentiel" => $refrefid));
    }
    return false;
}


/**
 * This function returns records of certificat from table referentiel_certificat
 *
 * @param id reference referentiel (no instance)
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_certificats($referentiel_referentiel_id, $select="", $order=""){
global $DB;
	if (!empty($referentiel_referentiel_id)){
		if (empty($order)){
			$order= 'userid ASC ';
		}
		$params= array("refid" => "$referentiel_referentiel_id");
        $sql="SELECT * FROM {referentiel_certificat} WHERE ref_referentiel=:refid  $select ORDER BY $order ";
        return $DB->get_records_sql($sql, $params);
	}
	else
		return 0;
}

function referentiel_genere_competences_declarees_vide($liste_competences){
//
// retourne une liste de la forme
// input :: A.1.1:0/A.1.2:0/A.1.3:0/A.1.4:0/A.1.5:0/A.2.1:0 ...
// output A.1.1:0/A.1.2:0/A.1.3:0/A.1.4:0/A.1.5:0/A.2.1:0 ...
	// collecter les competences
	$jauge_competences_declarees='';
	$tcomp=explode("/", $liste_competences);
	while (list($key, $val) = each($tcomp)) {
		// echo "$key => $val\n";
		if ($val!=""){
			$jauge_competences_declarees.=$val.":0/";
		}
	}
	return $jauge_competences_declarees;
}

/**
 * This function get all competencies declared in activities and return a competencies list
 *
 * @param userid reference user id
 * @param $ref_referentiel reference a referentiel id (not an instance of it !)
 * @return bolean
 * @todo Finish documenting this function
 * algorithme : cumule pour chaque competences le nombre d'activitÃ©s oÃ¹ celle ci est validee
 **/
function referentiel_genere_certificat_liste_competences_declarees($userid, $ref_referentiel){
	$t_liste_competences_declarees=array();
	$t_competences_declarees=array();
	$t_competences_referentiel=array(); // les competences du referentiel

	$liste_competences_declarees=""; // la liste sous forme de string
	$jauge_competences_declarees=""; // la juge sous forme CODE_COMP_0:n0/CODE_COMP_1:n1/...
	// avec 0 si competence declaree 0 fois, n>0 sinon

	if (isset($userid) && ($userid>0) && isset($ref_referentiel) && ($ref_referentiel>0)){
		// liste des competences definies dans le referentiel
		$liste_competences_referentiel=referentiel_purge_dernier_separateur(referentiel_get_liste_codes_competence($ref_referentiel), "/");
		// DEBUG
		// echo "<br />DEBUG :: Ligne 2706 :: USERID : $userid :: REFERENTIEL : $ref_referentiel\n";

		$t_competences_referentiel=explode("/", $liste_competences_referentiel);
		// creer un tableau dont les indices sont les codes de competence
		while (list($key, $val) = each($t_competences_referentiel)) {
			$t_competences_declarees[$val]=0;
		}
		// collecter les activites validees
		$select=" AND userid=".$userid." ";
		$order= ' id ASC ';
		$records_activite = referentiel_get_activites($ref_referentiel, $select, $order);
		if (!$records_activite){
			return referentiel_genere_competences_declarees_vide($liste_competences_referentiel);
		}
		// DEBUG
		// echo "<br />Debug :: lib.php :: Ligne 2721 \n";
		// print_r($records_activite);

		// collecter les competences
		foreach ($records_activite  as $activite){
			$t_liste_competences_declarees[]=referentiel_purge_dernier_separateur($activite->competences_activite, "/");
		}
 		for ($i=0; $i<count($t_liste_competences_declarees); $i++){
			$tcomp=explode("/", $t_liste_competences_declarees[$i]);
			while (list($key, $val) = each($tcomp)) {
				// echo "$key => $val\n";
				if (isset($t_competences_declarees[$val])) $t_competences_declarees[$val]++;
			}
		}
		$i=0;
		while (list($key, $val) = each($t_competences_declarees)) {
			// echo "$key => $val\n";
			if ((!is_numeric($key) && ($key!=""))  && ($val!="") && ($val>0)){
				$liste_competences_declarees.=$key."/";
			}
			$jauge_competences_declarees.=$key.":".trim($val)."/";
		}
	}
	// DEBUG
	// echo "<br />DEBUG :: Ligne lib.php :: 4055 :: $jauge_competences_declarees\n";

	return $jauge_competences_declarees;
}



/**
 * This function get all valid competencies in activite and return a competencies list
 *
 * @param userid reference user id
 * @param $ref_referentiel reference a referentiel id (not an instance of it !)
 * @return bolean
 * @todo Finish documenting this function
 * algorithme : cumule pour chaque competences le nombre d'activitÃ©s oÃ¹ celle ci est validee
 **/
function referentiel_genere_certificat_liste_competences($userid, $ref_referentiel){
	$t_liste_competences_valides=array();
	$t_competences_valides=array();
	$t_competences_referentiel=array(); // les competences du referentiel

	$liste_competences_valides=""; // la liste sous forme de string
	$jauge_competences=""; // la juge sous forme CODE_COMP_0:n0/CODE_COMP_1:n1/...
	// avec 0 si competence valide 0 fois, n>0 sinon

	if (isset($userid) && ($userid>0) && isset($ref_referentiel) && ($ref_referentiel>0)){
		// liste des competences definies dans le referentiel
		$liste_competences_referentiel=referentiel_purge_dernier_separateur(referentiel_get_liste_codes_competence($ref_referentiel), "/");
		// DEBUG
		// echo "<br />DEBUG :: lib.php :: Ligne 7275 ::<br />USERID : $userid :: REFERENTIEL : $ref_referentiel<br />$liste_competences_referentiel\n";

		$t_competences_referentiel=explode("/", $liste_competences_referentiel);
		// creer un tableau dont les indices sont les codes de competence
		while (list($key, $val) = each($t_competences_referentiel)) {
			$t_competences_valides[$val]=0;
		}
		// collecter les activites validees
		$select=" AND approved!=0 AND userid=".$userid." ";
		$order= ' id ASC ';
		$records_activite = referentiel_get_activites($ref_referentiel, $select, $order);
		if ($records_activite){
			// DEBUG
			// echo "<br />Debug :: lib.php :: Ligne 7288<br />COMPETENCES REFERENTIEL VALIDES AVANT :<br />\n";
			// print_r($t_competences_valides);

      // echo "<br />Debug :: lib.php :: Ligne 7291 :<br />ACTIVIE<br />\n";
			// print_r($records_activite);

			// collecter les competences
			foreach ($records_activite  as $activite){
				$t_liste_competences_valides[]=referentiel_purge_dernier_separateur($activite->competences_activite, "/");
  			// DEBUG
	   		// echo "<br />Debug :: lib.php :: Ligne 7298<br />COMPETENCES ACTIVITE :<br />".$activite->competences_activite."\n";
			}

			// print_r($t_liste_competences_valides);
			// exit;

      for ($i=0; $i<count($t_liste_competences_valides); $i++){
				if ($t_liste_competences_valides[$i]){
          $tcomp=explode("/", $t_liste_competences_valides[$i]);
				  while (list($key, $val) = each($tcomp)) {
					 // echo "$key => $val\n";
					 // if (isset($t_competences_valides[$val]))
            $t_competences_valides[$val]++;
				  }
				}
			}
		}

		$i=0;
		while (list($key, $val) = each($t_competences_valides)) {
			// echo "$key => $val\n";
			if ((!is_numeric($key) && ($key!=""))  && ($val!="") && ($val>0)){
				$liste_competences_valides.=$key."/";
			}
			$jauge_competences.=$key.":".trim($val)."/";
		}
	}

	// DEBUG
	// echo "<br />DEBUG :: Ligne 4123 :: $jauge_competences\n";

	return $jauge_competences;
}


/**
 * This function returns record certificat from table referentiel_certificat
 *
 * @param userid reference user id of certificat
 * @param ref_referentiel reference referentiel id of certificat
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_certificat_user($userid, $ref_referentiel){
global $DB;
	if (isset($userid) && ($userid>0) && isset($ref_referentiel) && ($ref_referentiel>0)){
		return $DB->get_record_sql("SELECT * FROM {referentiel_certificat} WHERE ref_referentiel=$ref_referentiel AND userid=$userid ");
	}
	else {
		return false;
	}
}


/**
 * This function  create / update with valid competencies a certificat for the userid
 *
 * @param userid reference user id
 * @param $ref_referentiel reference a referentiel id (not an instance of it !)
 * @return bolean
 * @todo Finish documenting this function
 **/
function referentiel_genere_certificat($userid, $ref_referentiel){
global $DB;
	$certificat_id=0; // id du certificat cree / modifie
	if (isset($userid) && ($userid>0) && isset($ref_referentiel) && ($ref_referentiel>0)){
		// MODIF JF 28/11/2009
		$competences_activite=referentiel_genere_certificat_liste_competences_declarees($userid, $ref_referentiel);
		$competences_certificat=referentiel_genere_certificat_liste_competences($userid, $ref_referentiel);
		// DEBUG
		// echo "<br />DEBUG :: lib.php :: LIGNE 4194 :: $competences_activite\n";
		// echo "<br />DEBUG :: lib.php :: LIGNE 4195 :: $competences_certificat\n";

		if (
			($competences_certificat!="")
			||
			($competences_activite!="")
			){
			// si existe update
			if ($certificat=referentiel_get_certificat_user($userid, $ref_referentiel)){
                $certificat_id=$certificat->id;

				// update ?
				if ($certificat->verrou==0
                    && $certificat->valide==0){        // MODIF JF 2012/10/07
					$certificat->commentaire_certificat=($certificat->commentaire_certificat);
	                $certificat->synthese_certificat=($certificat->synthese_certificat);
					$certificat->decision_jury=($certificat->decision_jury);
					$certificat->evaluation=($certificat->evaluation);
					$certificat->competences_certificat=$competences_certificat;
					$certificat->competences_activite=$competences_activite;
					$certificat->evaluation=referentiel_evaluation($competences_certificat, $ref_referentiel);
                    $noerror=$DB->update_record("referentiel_certificat", $certificat);
					if(!$noerror){
						// DEBUG
						// echo "<br /> ERREUR UPDATE CERTIFICAT\n";
					}
				}
			}
			else {
				// sinon creer
				$certificat = new object();
				$certificat->competences_certificat=$competences_certificat;
				$certificat->competences_activite=$competences_activite;
				$certificat->commentaire_certificat="";
                $certificat->synthese_certificat="";
				$certificat->decision_jury="";
				$certificat->date_decision=0;
				$certificat->ref_referentiel=$ref_referentiel;
				$certificat->userid=$userid;
				$certificat->teacherid=0;
				$certificat->verrou=0;
// Modif JF 2012/10/07
				$certificat->valide=0;
				$certificat->evaluation=referentiel_evaluation($competences_certificat, $ref_referentiel);
    			// DEBUG
    			//echo "<br />DEBUG :: lib.php :: 4116<br />\n";
				// print_object($certificat);
    			//echo "<br />";
    			//exit;
				$certificat_id= $DB->insert_record("referentiel_certificat", $certificat);
			}
		}
	}
	return $certificat_id;
}

/**
 * This function modify referentiel_certificat list of competencies
 *
 * @param liste_competences 'A.1.1/A.1.3/A.2.3/'
 * @param userid reference user id
 * @param referentiel_id reference referentiel
 * @return string certificat_jauge
 * A.1.1:0/A.1.2:1/A.1.3:2/A.1.4:0/A.1.5:0/A.2.1:1/A.2.2:1/A.2.3:1/A.3.1:0/A.3.2:0/A.3.3:0/A.3.4:0/B.1.1:0/B.1.2:0/B.1.3:0/B.2.1:0/B.2.2:0/B.2.3:1/B.2.4:0/B.3.1:0/B.3.2:0/B.3.3:0/B.3.4:0/B.3.5:0/B.4.1:1/B.4.2:1/B.4.3:0/
 * @todo Finish documenting this function
 **/
function referentiel_mise_a_jour_competences_certificat_user($liste_competences_moins, $liste_competences_plus, $userid, $referentiel_id, $approved, $modif_declaration=true, $modif_validation=false ){
global $DB;
// 	la liste sous forme de string
//  IN#1  : 'A.1.1/A.1.3/A.2.3/'
//  IN#2  : '      A.1.3/A.2.3/A.3.1'

// 	la jauge sous forme CODE_COMP_0:n0/CODE_COMP_1:n1/...
//  avec 0 si competence valide 0 fois, n>0 sinon
//  GET  : 'A.1.1:1/A.1.2:1/A.1.3:2/A.1.4:0/A.1.5:0/A.2.1:1/A.2.2:1/A.2.3:1/A.3.1:0/A.3.2:0/A.3.3:0/A.3.4:0/B.1.1:0/B.1.2:0/B.1.3:0/B.2.1:0/B.2.2:0/B.2.3:1/B.2.4:0/B.3.1:0/B.3.2:0/B.3.3:0/B.3.4:0/B.3.5:0/B.4.1:1/B.4.2:1/B.4.3:0/'
//  la jauge sous forme CODE_COMP_0:n0/CODE_COMP_1:n1/...
//  PUT  : 'A.1.1:0/A.1.2:1/A.1.3:2/A.1.4:0/A.1.5:0/A.2.1:1/A.2.2:1/A.2.3:1/A.3.1:1/A.3.2:0/A.3.3:0/A.3.4:0/B.1.1:0/B.1.2:0/B.1.3:0/B.2.1:0/B.2.2:0/B.2.3:1/B.2.4:0/B.3.1:0/B.3.2:0/B.3.3:0/B.3.4:0/B.3.5:0/B.4.1:1/B.4.2:1/B.4.3:0/'
//                -               =                                       =       +
	$debug=false;
	$certificat_id=0;

	// Competences validees
	$liste_competences_valides='';
	$jauge_competences='';

	$t_competences_jauge=array();
	$t_competences_supprimees=array(); // les competences Ã  supprimer de la liste
	$t_competences_valides=array(); // les competences du certificat validees

	// Competences declarees
	$liste_jauge_declarees=''; // competences declarees dans les activites
	$t_competences_jauge_declarees=array();
	$t_competences_declarees=array(); // les competences du certificat declarees
	$jauge_competences_declarees='';

	// outils
	$t_jauge= array();
	$tcomp= array();

	// preparation
	// competences a supprimer
	if ($liste_competences_moins!=''){
		// DEBUG
		if ($debug) echo "<br />COMPETENCES MOINS<br />\n";
		$liste_competences_moins=referentiel_purge_dernier_separateur($liste_competences_moins, "/");
	}
	// competences a ajouter
	if ($liste_competences_plus!=''){
		if ($debug) echo "<br />COMPETENCES PLUS<br />\n";
		$liste_competences_plus=referentiel_purge_dernier_separateur($liste_competences_plus, "/");
	}

	// DEBUG
	if ($debug) echo "<br />DEBUG :: lib.php :: Ligne 4346 :: USERID : $userid :: REFERENTIEL : $referentiel_id<br />LISTE MOINS : $liste_competences_moins <br />LISTE PLUS : $liste_competences_plus<br />\n";

	if (!referentiel_certificat_user_exists($userid, $referentiel_id)){
		// CREER ce certificat
		referentiel_genere_certificat($userid, $referentiel_id);
	}

	$certificat=referentiel_get_certificat_user($userid, $referentiel_id);

	if ($certificat){
		$certificat_id=$certificat->id;
		// DEBUG
		if ($debug) {
			echo "<br />DEBUG : lib.php :: Ligne 4315 :: CERTIFICAT<br /> ";
			print_object($certificat);
    		echo "<br />";
		}

		// Competences declarees
		if (!$modif_declaration){ // une validation ou une devalidation d'activite sans ajout ni suppression des competences
			$jauge_competences_declarees=$certificat->competences_activite; // Pas de changement
		}
		else{
			// mise a jour des competences declarees
			$liste_competences_declarees=$certificat->competences_activite;
			if ($liste_competences_declarees!=''){
				$liste_competences_declarees=referentiel_purge_dernier_separateur($liste_competences_declarees, "/");
				// DEBUG
				//echo "<br />DEBUG :: lib.pho :: 4326 :: JAUGE GET : $liste_competences_declarees<br />\n";
				$t_competences_jauge_declarees=explode("/", $liste_competences_declarees); // [A.1.1:0]  [A.1.2:1] [A.1.3:2] [A.1.4:0] ...
				//echo "<br />TABLEAU JAUGE GET :<br />\n";
				//print_r($t_competences_jauge_activite);
				//echo "<br />\n";

				// creer et initialise un tableau dont les indices sont les codes de competence
				// echo "<br />JAUGE GET<br />\n";
				while (list($key, $val) = each($t_competences_jauge_declarees)) {
					//echo "$key => $val\n";
					$t_jauge=explode(':',$val);
					$t_competences_declarees[$t_jauge[0]]=$t_jauge[1]; // // [A.1.1]=0  [A.1.2]=1 [A.1.3]=2 [A.1.4]=0
				}
				if ($debug) {
					echo "<br />TABLEAU COMPETENCES DECLAREEES AVANT SUPPRESSION :<br />\n";
					print_r($t_competences_declarees);
					echo "<br />\n";
				}

				// supprimer des competences
				if ($liste_competences_moins!=''){
					$tcomp0=explode("/", $liste_competences_moins);
					while (list($key0, $val0) = each($tcomp0)) {
						// echo "<br />$key0 => $val0\n";
						if ($t_competences_declarees[$val0]>0){
							$t_competences_declarees[$val0]--;
						}
					}
				}
				if ($debug) {
					echo "<br />TABLEAU COMPETENCES DECLAREEES APRES SUPPRESSION :<br />\n";
					print_r($t_competences_declarees);
				}
				// Ajouter des competences
				if ($liste_competences_plus!=''){
					$tcomp1=explode("/", $liste_competences_plus);
					while (list($key1, $val1) = each($tcomp1)) {
						//echo "<br />$key1 => $val1\n";
						$t_competences_declarees[$val1]++;
					}
				}
				if ($debug) {
					echo "<br />TABLEAU COMPETENCES DECLAREEES APRES AJOUT :<br />\n";
					print_r($t_competences_declarees);
				}
				// reconstitution de la jauge des competences declarees

				while (list($key2, $val2) = each($t_competences_declarees)) {
					// echo "<br />$key2 => $val2\n";
					if ((!is_numeric($key2) && ($key2!=""))  && ($val2!="") && ($val2>0)){
						$liste_jauge_declarees.=$key2."/";
					}
					$jauge_competences_declarees.=$key2.":".trim($val2)."/";
				}
			}
		}

		if ($debug) {
			echo "<br /><br />COMPETENCES DECLAREEES :<br />$jauge_competences_declarees<br />\n";
		}

		// Competences validees
		if (($certificat->verrou!=0) || (!$modif_validation)) { // une mise a jour des competences sans validation ou devalidation
			$jauge_competences=$certificat->competences_certificat; // Pas de changement
		}
		else{
			// sinon modification de la liste des competences validees
			$liste_jauge_competences=$certificat->competences_certificat;
			$liste_jauge_competences=referentiel_purge_dernier_separateur($liste_jauge_competences, "/");
			//
			$t_competences_jauge=explode("/", $liste_jauge_competences); // [A.1.1:0]  [A.1.2:1] [A.1.3:2] [A.1.4:0] ...
			if ($debug) {
				echo "<br />JAUGE CERTIFICAT : $liste_jauge_competences<br />\n";
				echo "<br />TABLEAU COMPETENCES CERTIFICAT :<br />\n";
				print_r($t_competences_jauge);
				echo "<br />\n";
			}
			// creer et initialise un tableau dont les indices sont les codes de competence
			// echo "<br />JAUGE GET<br />\n";
			while (list($key, $val) = each($t_competences_jauge)) {
				// echo "$key => $val\n";
				$t_jauge=explode(':',$val);
				$t_competences_valides[$t_jauge[0]]=$t_jauge[1]; // // [A.1.1]=0  [A.1.2]=1 [A.1.3]=2 [A.1.4]=0
			}
			if ($debug) {
				echo "<br />TABLEAU COMPETENCES VALIDES AVANT SUPPRESSION :<br />\n";
				print_r($t_competences_valides);
				// echo "<br />lib.php :: EXIT ligne 4457\n";
				// exit;
			}

			// competences a supprimer
			if ($liste_competences_moins!=''){
				$tcomp=explode("/", $liste_competences_moins);
				while (list($key1, $val1) = each($tcomp)) {
					// echo "<br />$key1 => $val1\n";
					if ($t_competences_valides[$val1]>0){
						$t_competences_valides[$val1]--;
					}
				}
			}

			if ($debug) {
				echo "<br />TABLEAU COMPETENCES VALIDES APRES SUPPRESSION :<br />\n";
				print_r($t_competences_valides);
			}

			// competences a ajouter
			if ($approved){ // on ajoute si l'activite est validee
				if ($liste_competences_plus!=''){
					$tcomp=explode("/", $liste_competences_plus);
					while (list($key1, $val1) = each($tcomp)) {
						//echo "<br />$key1 => $val1\n";
						$t_competences_valides[$val1]++;
					}
				}

				if ($debug) {
					echo "<br />TABLEAU COMPETENCES VALIDES APRES AJOUT :<br />\n";
					print_r($t_competences_valides);
				}
			}

			// reconstitution de la jauge
			while (list($key2, $val2) = each($t_competences_valides)) {
				// echo "<br />$key2 => $val2\n";
				if ((!is_numeric($key2) && ($key2!=""))  && ($val2!="") && ($val2>0)){
					$liste_competences_valides.=$key2."/";
				}
				$jauge_competences.=$key2.":".trim($val2)."/";
			}
		}

		// DEBUG
		if ($debug) {
			echo "<br />DEBUG :: lib.php :: Ligne 4499 :: USERID : $userid :: REFERENTIEL : $referentiel_id<br />LISTE COMPETENCES : $liste_competences_valides<br />JAUGE : $jauge_competences\n";
		}

		// mise a jour
		$certificat->commentaire_certificat=($certificat->commentaire_certificat);
        $certificat->synthese_certificat=($certificat->synthese_certificat);
		$certificat->decision_jury=($certificat->decision_jury);
		$certificat->evaluation=($certificat->evaluation);
		$certificat->competences_certificat=$jauge_competences;
		$certificat->competences_activite=$jauge_competences_declarees;
		$certificat->evaluation=referentiel_evaluation($certificat->competences_certificat, $referentiel_id);
		// DEBUG
		if ($debug) {
			echo "<br />DEBUG : lib.php :: Ligne 4519 <br /> ";
			print_object($certificat);
    		// echo "<br />lib.php :: EXIT LIGNE 4524";
			// exit;
		}

		if (!$DB->update_record("referentiel_certificat", $certificat)){
			// DEBUG
			// echo "<br />DEBUG : lib_certificat :: Ligne 162  :: ERREUR UPDATE CERTIFICAT\n";
		}
	}
	return $certificat_id;
}


/**
 * This function returns records list of users from table referentiel_certificat
 *
 * @param userid reference user id
 * @param referentiel_id reference referentiel
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_certificat_user_exists($userid, $referentiel_id){
global $DB;
	if (isset($userid) && ($userid>0) && isset($referentiel_id) && ($referentiel_id>0)){
		$r=$DB->get_record_sql("SELECT * FROM {referentiel_certificat} WHERE ref_referentiel=$referentiel_id AND userid=$userid ");
		if ($r){
			// echo "<br />\n";
			// print_r($r);
			// MODIF JF 2009/11/28
			// controler la completude du certificat post version 4.1.1
			if (($r->competences_certificat!='') || ($r->competences_activite=='')){
				return 0;
			}
			else{
				return ($r->id);
			}
		}
	}
	return 0;
}

/**
 * This function returns record certificate from table referentiel_certificat
 *
 * @param userid reference user id
 * @param referentiel_id reference referentiel
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_certificat_user($userid, $referentiel_id){
// Si certificat n'existe pas, cree le certificat et le retourne

	if (isset($userid) && ($userid>0) && isset($referentiel_id) && ($referentiel_id>0)){
		if (!referentiel_certificat_user_exists($userid, $referentiel_id)){
			if (referentiel_genere_certificat($userid, $referentiel_id)){
				return referentiel_get_certificat_user($userid, $referentiel_id);
			}
			else{
				return false;
			}
		}
		else{
			return referentiel_get_certificat_user($userid, $referentiel_id);
		}
	}
	else {
		return false;
	}
}




/**
 * This function returns records list of users from table referentiel_certificat
 *
 * @param id reference certificat
 * @param select clause : ' AND champ=valeur,  ... '
 * @param order clause : ' champ ASC|DESC, ... '
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_users_certificats($referentiel_id, $select="", $order=""){
global $DB;
	if (isset($referentiel_id) && ($referentiel_id>0)){
		if (empty($order)){
			$order= 'userid ASC ';
		}
		return $DB->get_records_sql("SELECT DISTINCT userid FROM {referentiel_certificat} WHERE ref_referentiel=$referentiel_id  $select ORDER BY $order ");
	}
	else
		return 0;
}


/**
 * This function returns records list of users from table referentiel_activite
 *
 * @param id reference certificat
 * @param select clause : ' AND champ=valeur,  ... '
 * @param order clause : ' champ ASC|DESC, ... '
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_users_referentiel_cours($referentiel_id, $course_id, $select="", $order=""){
global $DB;
	if (isset($referentiel_id) && ($referentiel_id>0)){
		if (empty($order)){
			$order= 'userid ASC ';
		}
		return $DB->get_records_sql("SELECT DISTINCT userid FROM {referentiel_activite} WHERE ref_referentiel=$referentiel_id AND ref_course=$course_id $select ORDER BY $order ");
	}
	else
		return 0;
}

/**
 * This function returns records list of teachers from table referentiel_certificat
 *
 * @param id reference certificat
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_teachers_certificats($referentiel_id){
global $DB;
	if (isset($referentiel_id) && ($referentiel_id>0)){
		return $DB->get_records_sql("SELECT DISTINCT teacherid FROM {referentiel_certificat} WHERE ref_referentiel=$referentiel_id ORDER BY teacherid ASC ");
	}
	else
		return 0;
}


/**
 * This function get a competencies list and return a float
 *
 * @param userid reference user id
 * @param $ref_referentiel reference a referentiel id (not an instance of it !)
 * @return bolean
 * @todo Finish documenting this function
 **/
function referentiel_evaluation($listecompetences, $referentiel_id){
//A.1.1:4/A.1.2:1/A.1.3:0/A.1.4:3/A.1.5:0/A.2.1:0/A.2.2:0/A.2.3:0/A.3.1:0/A.3.2:0/A.3.3:0/A.3.4:0/B.1.1:0/B.1.2:0/B.1.3:0/B.2.1:0/B.2.2:0/B.2.3:0/B.2.4:0/B.3.1:0/B.3.2:0/B.3.3:0/B.3.4:0/B.3.5:0/B.4.1:1/B.4.2:1/B.4.3:0/
	// DEBUG
	// echo "<br />LISTE ".$listecompetences."\n";
	$evaluation=0.0;
	$tcode=array();
	$tcode=explode("/",$listecompetences);
	for ($i=0; $i<count($tcode); $i++){
		/*
        $tvaleur=explode(":",$tcode[$i]);
		$code="";
		$svaleur="";
		if (isset($tvaleur[0])){ // le code
			$code=trim($tvaleur[0]);
		}
		if (isset($tvaleur[1])){ // la valeur
			$svaleur=trim($tvaleur[1]);
		}
        */
        if ($tcode[$i]){
            list($code, $svaleur)=explode(":",$tcode[$i]);

            // DEBUG
		    // echo "<br />DEBUG :: locallib.php : 3138 :: CODE : ".$code." VALEUR : ".$svaleur."\n";
		    if (!empty($code) && !empty($svaleur)){
                $poids=referentiel_get_poids_item($code, $referentiel_id);
                $empreinte=referentiel_get_empreinte_item($code, $referentiel_id);
                // echo "<br />POIDS : $poids ; EMPREINTE : $empreinte\n";
                if ($empreinte) {
                    if ($svaleur >= $empreinte){
    				    $evaluation+=$poids;
                    }
                }
            }
	    }
    }
    // DEBUG
    // echo "<br />DEBUG : lib.php : 7716 :: EVALUATION : ".$evaluation."\n";
	return $evaluation;
}



/**
 * This function set all certificates
 *
 * @param $referentiel_instance reference an instance of referentiel !)
 * @return bolean
 * @todo Finish documenting this function
 **/
function referentiel_regenere_certificats($referentiel_instance){
	if ($referentiel_instance){
		$records_users=referentiel_get_course_users($referentiel_instance);
		// echo "<br /> lib.php :: referentiel_get_course_users() :: 2018<br />\n";
		if ($records_users){
			foreach ($records_users as $record_u){
				// echo "<br />DEBUG :: lib.php :: LIGNE 2948 \n";
				// print_r($record_u);
				referentiel_regenere_certificat_user($record_u->id, $referentiel_instance->ref_referentiel);
			}
		}
	}
}

/**
 * This function set all certificates
 *
 * @param $referentiel_instance reference an instance of referentiel !)
 * @return bolean
 * @todo Finish documenting this function
 **/
function referentiel_regenere_certificat_user($userid, $ref_referentiel){
	if ($ref_referentiel && $userid){
		if (!referentiel_certificat_user_exists($userid, $ref_referentiel)){
			// CREER ce certificat
			referentiel_genere_certificat($userid, $ref_referentiel);
		}

// Modif JF 2012/10/07
/*
		if (!referentiel_certificat_user_valide($userid, $ref_referentiel)){
		// drapeau positionne par l'ancienne version <= 3 quand une activite est validee ou devalidee
		// n'est plus utilise car desormais on modifie directement la jauge du certificat dans la partie activite
			// METTRE A JOUR ce certificat
			referentiel_genere_certificat($userid, $ref_referentiel);
		}
*/
	}
}

/**
 * This function reset all certificates
 *
 * @param $certificat record !)
 * @return nothing
 * @todo Finish documenting this function
 **/
function referentiel_recalcule_certificat($certificat){
	if (!empty($certificat->userid) && !empty($certificat->ref_referentiel)){
		referentiel_regenere_certificat_user($certificat->userid, $certificat->ref_referentiel);
	}
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
function referentiel_add_certificat($form) {
// creation certificat
global $USER;
global $DB;
    // DEBUG
    //echo "DEBUG : ADD CERTIFICAT CALLED";
	//print_object($form);
    //echo "<br />";
	// referentiel
	$certificat = new object();
	$certificat->competences_certificat=$form->competences_certificat;
	$certificat->commentaire_certificat=$form->commentaire_certificat;
	$certificat->synthese_certificat=$form->synthese_certificat;
	if (!empty($form->decision_jury)){
        $certificat->date_decision=time();
    }
    else{
        $certificat->date_decision='';
    }
    $certificat->decision_jury=($form->decision_jury);
	$certificat->date_decision='';
	$certificat->ref_referentiel=$form->ref_referentiel;
	$certificat->userid=$USER->id;
	$certificat->teacherid=$USER->id;
	$certificat->verrou=0;
	$certificat->valide=$form->valide;
	$certificat->evaluation=referentiel_evaluation($form->competences_certificat, $form->ref_referentiel);


	$certificat->mailed=1; // MODIF JF 2010/10/05
	if (isset($form->mailnow)){
        $certificat->mailnow=$form->mailnow;
        if ($form->mailnow=='1'){ // renvoyer
            $certificat->mailed=0;   // annuler envoi precedent
        }
    }
    else{
        $certificat->mailnow=0;
    }


    // DEBUG
	//print_object($certificat);
    //echo "<br />";

	$certificat_id= $DB->insert_record("referentiel_certificat", $certificat);
    // echo "certificat ID / $certificat_id<br />";
    // DEBUG
    return $certificat_id;
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
function referentiel_update_certificat($form) {
global $DB;
global $DB;
// MAJ certificat
$ok=true;
    // DEBUG
    /*
    echo "\nDEBUG : localib.php :: UPDATE CERTIFICAT CALLED:: 2998\n";
	print_object($form);
    echo "<br />\n";
    exit;
    */
    // certificat
	if (isset($form->action) && ($form->action=="modifier_certificat")){
		$certificat = new object();
		$certificat->id=$form->certificat_id;
		$certificat->commentaire_certificat=$form->commentaire_certificat;
        $certificat->synthese_certificat=$form->synthese_certificat;
		$certificat->competences_certificat=$form->competences_certificat;

        if (!empty($form->decision_jury_sel) && empty($form->decision_jury)){
            $form->decision_jury=$form->decision_jury_sel;
        }
		if (isset($form->decision_jury_old) && ($form->decision_jury_old!=$form->decision_jury)){
	       	$certificat->date_decision=time();
        }
        else{
            $certificat->date_decision=$form->date_decision;
        }
        $certificat->decision_jury=$form->decision_jury;

		$certificat->ref_referentiel=$form->ref_referentiel;
		$certificat->userid=$form->userid;
		$certificat->teacherid=$form->teacherid;

		$certificat->valide=$form->valide;
        // MODIF JF 2012/10/07
	    if ($certificat->valide){
            $certificat->verrou=1;
        }
        else{
            $certificat->verrou=$form->verrou;
        }

		$certificat->evaluation=referentiel_evaluation($form->competences_certificat, $form->ref_referentiel);

		// MODIF JF 2010/02/11
		if (isset($form->mailnow)){
            $certificat->mailnow=$form->mailnow;
            if ($form->mailnow=='1'){ // renvoyer
                $certificat->mailed=0;   // annuler envoi precedent
            }
        }
        else{
            $certificat->mailnow=0;
        }

	    // DEBUG
	    // echo "<br />DEBUG :: LOCALLIB.PHP :: 3046\n";
		// print_object($certificat);
	    // echo "<br />EXIT\n";
	    // exit;

		$noerror=$DB->update_record("referentiel_certificat", $certificat);
		if (!$noerror){	//echo "<br /> ERREUR UPDATE CERTIFICAT\n";
			$ok=false;
		}
		else {
			// echo "<br /> UPDATE CERTIFICAT $certificat->id\n";
			$ok=true;
		}
		// exit;
		return $ok;
	}
}

function referentiel_user_can_add_certificat($referentiel, $currentgroup, $groupmode) {
    global $USER;
    global $CFG;
    if (!$cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $referentiel->course)) {
        print_error('Course Module ID was incorrect');
    }
	$context = context_module::instance($cm->id);

    if (!has_capability('mod/referentiel:writecertificat', $context)) {
        return false;
    }

    if (!$groupmode or has_capability('moodle/site:accessallgroups', $context)) {
        return true;
    }

    if ($currentgroup) {
        return ismember($currentgroup);
    }
    else {
        //else it might be group 0 in visible mode
        if ($groupmode == VISIBLEGROUPS){
            return true;
        }
        else {
            return false;
        }
    }
}


function referentiel_certificat_isowner($id){
global $USER;
global $DB;
	if (isset($id) && ($id>0)){
		$record=$DB->get_record("referentiel_certificat", array("id" => "$id"));
		// DEBUG
		// echo "<br >USERID : $USER->id ; OWNER : $record->userid\n";
		if ($record){
            return ($USER->id == $record->userid);
        }
	}
	return false;
}



// -------------------------------
function referentiel_pourcentage($a, $b){
	if ($b!=0) return round(($a * 100.0) / (float)$b,1);
	else return NULL;
}



// -------------------
function referentiel_affiche_certificat_consolide($separateur1, $separateur2, $liste_code, $ref_referentiel, $bgcolor, $params=NULL){
// cet affichage du certificat fournit des pourcentages par domaine et competence
    echo referentiel_retourne_certificat_consolide($separateur1, $separateur2, $liste_code, $ref_referentiel, $bgcolor, $params);
}



// -------------------
function referentiel_retourne_certificat_consolide($separateur1, $separateur2, $liste_code, $ref_referentiel, $bgcolor, $params=NULL){
// ce certificat comporte des pourcentages par domaine et competence
// affichage sous forme de tableau et span pour les items
// input liste_code
// A.1-1:0/A.1-2:0/A.1-3:1/A.1-4:0/A.1-5:0/A.2-1:0/A.2-2:0/A.2-3:0/A.3-1:0/A.3-2:1/A.3-3:1/A.3-4:1/B.1-1:0/B.1-2:0/B.1-3:0/B.2-1:0/B.2-2:0/B.2-3:0/B.2-4:0/B.2-5:0/B.3-1:0/B.3-2:0/B.3-3:0/B.3-4:0/B.3-5:0/B.4-1:0/B.4-2:0/B.4-3:0/
global $OK_REFERENTIEL_DATA;
global $t_domaine;
global $t_domaine_coeff;

// COMPETENCES
global $t_competence;
global $t_competence_coeff;

// ITEMS
global $t_item_code;
global $t_item_coeff; // coefficient poids determine par le modele de calcul (soit poids soit poids / empreinte)
global $t_item_domaine; // index du domaine associe a un item
global $t_item_competence; // index de la competence associee a un item
global $t_item_poids; // poids
global $t_item_empreinte;
global $t_nb_item_domaine;
global $t_nb_item_competence;


	$s='';

	// nom des domaines, competences, items
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
	$t_certif_item_valeur=array();	// table des nombres d'items valides
	$t_certif_item_coeff=array(); // somme des poids du domaine
	$t_certif_competence_poids=array(); // somme des poids de la competence
	$t_certif_domaine_poids=array(); // poids certifies
	// affichage


	// donnees globales du referentiel
	if ($ref_referentiel){

		if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
			$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($ref_referentiel);
		}

		if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){
            for ($i=0; $i<count($t_item_code); $i++){
                $t_certif_item_valeur[$i]=0.0;
                $t_certif_item_coeff[$i]=0.0;
            }
            for ($i=0; $i<count($t_competence); $i++){
                $t_certif_competence_poids[$i]=0.0;
            }
            for ($i=0; $i<count($t_domaine); $i++){
                $t_certif_domaine_poids[$i]=0.0;
            }

		    // DEBUG
		    // echo "<br />CODE <br />\n";
		    // referentiel_affiche_data_referentiel($ref_referentiel, $params);

		    // recuperer les items valides
            $tc=array();
            $liste_code=referentiel_purge_dernier_separateur($liste_code, $separateur1);

		    // DEBUG
		    // echo "<br />DEBUG :: print_lib_certificat.php :: 917 :: LISTE : $liste_code<br />\n";

            if (!empty($liste_code) && ($separateur1!="") && ($separateur2!="")){
                $tc = explode ($separateur1, $liste_code);

			    // DEBUG


    			for ($i=0; $i<count($t_item_domaine); $i++){
	       			$t_certif_domaine_poids[$i]=0.0;
		      	}
    			for ($i=0; $i<count($t_item_competence); $i++){
	       			$t_certif_competence_poids[$i]=0.0;
		      	}

    			$i=0;
	       		while ($i<count($tc)){
		      		// CODE1:N1
			     	// DEBUG
    				// echo "<br />".$tc[$i]." <br />\n";
	       			// exit;
		      		$t_cc=explode($separateur2, $tc[$i]); // tableau des items valides

			     	// print_r($t_cc);
				    // echo "<br />\n";
    				// exit;
	       			if (isset($t_cc[1])){
		      			if (isset($t_item_poids[$i]) && isset($t_item_empreinte[$i])){
			     			if (($t_item_poids[$i]>0) && ($t_item_empreinte[$i]>0)){
				        			// echo "<br />".min($t_cc[1],$t_item_empreinte[$i]);
						      	$t_certif_item_valeur[$i]=min($t_cc[1],$t_item_empreinte[$i]);
    							// calculer le taux
	       						$coeff=(float)$t_certif_item_valeur[$i] * (float)$t_item_coeff[$i];
		      					// stocker la valeur pour l'item
			     				$t_certif_item_coeff[$i]=$coeff;
				       			// stocker le taux pour la competence
					       		$t_certif_domaine_poids[$t_item_domaine[$i]]+=$coeff;
    							// stocker le taux pour le domaine
	       						$t_certif_competence_poids[$t_item_competence[$i]]+=$coeff;
		      				}
			     			else{
				        			// echo "<br />".min($t_cc[1],$t_item_empreinte[$i]);
						      	$t_certif_item_valeur[$i]=0.0;
    							$t_certif_item_coeff[$i]=0.0;
	       						// $t_certif_domaine_poids[$t_item_domaine[$i]]+=0.0;
		      					// $t_certif_competence_poids[$t_item_competence[$i]]+=0.0;
			     			}
				       	}
    				}

			     	$i++;
			     }

    			// DEBUG
                $nlen=strlen($liste_code);
                if ($nlen<=MAXLENCODE){  // sous forme de tableau
	       		// DOMAINES
		      	$s.= '<table width="100%" cellspacing="0" cellpadding="2"><tr valign="top" >'."\n";
    			// if (!empty($label_d)){
	       		//	$s.='<td  width="5%">'.$label_d.'</td>';
		      	//}
    			//  else {
	       		//	$s.='<td $t_certif_item_coeff width="5%">'.get_string('domaine','referentiel').'</td>';
		      	//}
    			for ($i=0; $i<count($t_domaine_coeff); $i++){
	       			if ($t_domaine_coeff[$i]){
		      			$s.='<td  align="center" colspan="'.$t_nb_item_domaine[$i].'"><b>'.$t_domaine[$i].'</b> ('.referentiel_pourcentage($t_certif_domaine_poids[$i], $t_domaine_coeff[$i]).'%)</td>';
			     	}
				    else{
    					$s.='<td  align="center" colspan="'.$t_nb_item_domaine[$i].'"><b>'.$t_domaine[$i].'</b> (0%)</td>';
	       			}
		      	}
    			$s.='</tr>'."\n";

	       		$s.=  '<tr valign="top"  >'."\n";
		      	for ($i=0; $i<count($t_competence); $i++){
			     	if ($t_competence_coeff[$i]){
				    	$s.='<td align="center" colspan="'.$t_nb_item_competence[$i].'"><b>'.$t_competence[$i].'</b> ('.referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i]).'%)</td>'."\n";
    				}
	       			else{
		      			$s.='<td align="center" colspan="'.$t_nb_item_competence[$i].'"><b>'.$t_competence[$i].'</b> (0%)</td>'."\n";
			     	}
    			}
	       		$s.='</tr>'."\n";

                // ITEMS

                // DEBUG
                // echo "<br />$nlen\n";

                $s.= '<tr valign="top" >'."\n";
    			for ($i=0; $i<count($t_item_code); $i++){
	       			if ($t_item_empreinte[$i]){
		      			   if (isset($t_certif_item_valeur[$i])){
                                if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
    			     			   // $s.='<td'.$bgcolor.'><span  class="valide">'.$t_item_code[$i].'</span></td>'."\n";
    			     			   $s.='<td class="valide"><span  class="valide">'.$t_item_code[$i].'</span></td>'."\n";

                                }
        				        else{
                                    //$s.='<td'.$bgcolor.'><span class="invalide">'.$t_item_code[$i].'</span></td>'."\n";
                                    $s.='<td class="invalide"><span class="invalide">'.$t_item_code[$i].'</span></td>'."\n";
                                }
                            }
			     	}
				    else{
                        $s.='<td class="nondefini"><span class="nondefini"><i>'.$t_item_code[$i].'</i></span></td>'."\n";
    				}
                }
		      	$s.='</tr>'."\n";
                $s.='<tr valign="top" >'."\n";
    			// <td  width="5%">'.get_string('coeff','referentiel').'</td>'."\n";
	       		// for ($i=0; $i<count($t_item_coeff); $i++){
		      	for ($i=0; $i<count($t_item_code); $i++){
			     	    if ($t_item_empreinte[$i]){
                           if (isset($t_certif_item_valeur[$i])){
					           if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
					       	      // $s.='<td'.$bgcolor.'><span class="valide">100%</span></td>'."\n";
					       	      $s.='<td class="valide"><span class="valide">100%</span></td>'."\n";
					           }
    					       else{
	       					      $s.='<td class="invalide"><span class="invalide">'.referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i]).'%</span></td>'."\n";
		      			       }
                            }
    				    }
	       			    else {
		      			   $s.='<td class="nondefini"><span class="nondefini">&nbsp;</span></td>'."\n";
			     	    }
			    }
			    $s.='</tr></table>'."\n";
                }
                else{
	       		// DOMAINES
		      	$s.= '<table width="100%" cellspacing="0" cellpadding="2">
    <tr valign="top"><td>'."\n";
    			// if (!empty($label_d)){
	       		//	$s.='<td  width="5%">'.$label_d.'</td>';
		      	//}
    			//  else {
	       		//	$s.='<td $t_certif_item_coeff width="5%">'.get_string('domaine','referentiel').'</td>';
		      	//}
    			for ($i=0; $i<count($t_domaine_coeff); $i++){
	       			if ($t_domaine_coeff[$i]){
		      			$s.=' <b>'.$t_domaine[$i].'</b> ('.referentiel_pourcentage($t_certif_domaine_poids[$i], $t_domaine_coeff[$i]).'%)';
			     	}
				    else{
    					$s.=' <b>'.$t_domaine[$i].'</b> (0%)</span>';
	       			}
		      	}
    			$s.='</td></tr>'."\n";

	       		$s.=  '<tr valign="top"><td>'."\n";
		      	for ($i=0; $i<count($t_competence); $i++){
			     	if ($t_competence_coeff[$i]){
				    	$s.=' <b>'.$t_competence[$i].'</b> ('.referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i]).'%)'."\n";
    				}
	       			else{
		      			$s.=' <b>'.$t_competence[$i].'</b> (0%)'."\n";
			     	}
    			}
	       		$s.='</td></tr>'."\n";

                $s.= '<tr valign="top" ><td>'."\n";
                for ($i=0; $i<count($t_item_code); $i++){
                        if ($t_item_empreinte[$i]){
                            if (isset($t_certif_item_valeur[$i])){
                                if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
                                    $s.='<span class="deverrouille"><span  class="valide">'.$t_item_code[$i].' (100%)</span></span>'."\n";
                                }
                                else {
                                    $s.='<span class="verrouille"><span class="invalide">'.$t_item_code[$i].' ('.referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i]).'%)</span></span>'."\n";
                                }
                            }
                        }
    				    else{
                            $s.='<span class="nondefini"><i>'.$t_item_code[$i].'</i></span>'."\n";
                        }
                }
                $s.='</td></tr></table>'."\n";
                }
                }
            }

	}
	return $s;
}



/// URL


/// FONCTIONS A ECRIRE /////////////////////////////////////////////////////////////////////////

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove clean up any related data.
 *
 * @global object
 * @global object
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function referentiel_reset_userdata($data) {
    global $CFG, $DB;
	// DEBUG
	// echo "<br />DEBUG :: lib.php :: 2076<br />\n";
	// print_object($data);
	// exit;
    $componentstr = get_string('modulenameplural', 'referentiel');
    $status = array();

    $strstatus='';
	if ($data->reset_referentiel_declaration){
    	if ($instances=$DB->get_records('referentiel', array('course' => $data->courseid ))){
            foreach ($instances as $instance){
            	referentiel_delete_instance($instance->id, false);
            	$strstatus.=$instance->name.", ";
        	}
        	$status[] = array('component'=>$componentstr, 'item'=>$strstatus, 'error'=>false);
		}
    }

    return $status;
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the assignment.
 * @param $mform form passed by reference
 */
function referentiel_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'referentielheader', get_string('modulenameplural', 'referentiel'));
    $mform->addElement('advcheckbox', 'reset_referentiel_declaration', get_string('deletealldeclarations','referentiel'));
}

/**
 * Course reset form defaults.
 * @param  object $course
 * @return array
 */
function referentiel_reset_course_form_defaults($course) {
    return array('reset_referentiel_declaration'=>1);
}

/**
 * Must return an array of users who are participants for a given instance
 * of referentiel. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $referentielid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */

function referentiel_get_participants($referentielid) {
    global $CFG, $DB;
    return false;
}


/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 * DEPRECATED with Moodle 2
 * @return boolean true if success, false on error
 */
// function referentiel_uninstall() {
//     return true;
// }

/**
 * Given a course and a time, this module should find recent activity 
 * that has occurred in referentiel activities and print it out. 
 * Return true if there was output, or false is there was none. 
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function referentiel_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    return false;  //  True if anything was printed, otherwise false 
}
// ################################### EDITOR

function  referentiel_editor_is_ok(){
// editeur wisiwyg  appele depuis mod.html
// non implanté car en cours de developpement :))
    return EDITOR_ON;
}

// pour suppprimer le probleme des noms de fichiers incorrects
function referentiel_purge_caracteres_incorrects($str){
    $replace_pairs = array("\\" => "","'" => "_", " " => "_");
    return strtr ( $str , $replace_pairs );
}


/**
 * Must return an array of grades for a given instance of this module, 
 * indexed by user.  It also returns a maximum allowed grade.
 * 
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $referentielid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function referentiel_grades($referentielid) {
// A FAIRE
// renvoie le carnet de notes de l'instance Ã  Moodle, afin que la plate-forme l'intÃ¨gre dans son carnet de notes global 
// pour l'Ã©tudiant. Cette fonction DOIT retourner un combinÃ© de deux tableau associatif : 
// { 'grades' => { userId => array of double }, 'maxgrades' => { userId = > array of double }}.
// Le premier tableau renvoie les notes obtenues, le deuxiÃ¨me renvoie les notes maximales 
// (ex : je renvoie pour l'utilisateur 23 les notes 7/20 et 13/15 :
// { 'grades' => { 23 => (7, 13)}, 'maxgrades' => { 23 => (20, 15)}}
// Un module peut donc renvoyer une sÃ©rie de notes pour chaque Ã©tudiant. 

   return false;
}


/**
 * This function returns if a scale is being used by one referentiel
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $referentielid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 **/
function referentiel_scale_used ($referentielid, $scaleid) {
    $return = false;
    // thows dml exception
    //$rec = $DB->get_record("referentiel", array("id"=>"$referentielid","scale"=>"-$scaleid");
    //
    //if (!empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}


/**
 * Checks if scale is being used by any instance of refrentiel
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any referentiel
 */
function referentiel_scale_used_anywhere($scaleid) {
global $DB;
/*
    $params = array('grade' => -$scaleid);
    if ($scaleid and $DB->record_exists('referentiel', $params)) {
        return true;
    }
    else {
        return false;
    }
*/
    return false;
}





/// OTHER STANDARD FUNCTIONS ////////////////////////////////////////////////////////
require_once ("lib_backup.php");

/**
 * Deletes an referentiel instance
 *
 * This is done by calling the delete_instance() method of the referentiel type class
 * Given an ID of an instance of this module,
 * this function will permanently delete any activity and certificate and task ank accompagnement
 * that depends on it.
 *
 * @param int $id Id of the module instance
 * @param boolean $purge : if true module instance is deleted too
 * @return boolean Success/Failure
 */


function referentiel_delete_instance($id, $purge=true){
    global $CFG, $DB;

    if (! $referentiel = $DB->get_record('referentiel', array('id'=>$id))) {
        return false;
    }

    $ref = new referentiel();
    return $ref->delete_instance($referentiel, $purge);
}


/**
 * Updates an referentiel instance
 *
 * This is done by calling the update_instance() method of the referentiel type class
 */
function referentiel_update_instance($referentiel){
    global $CFG;
    $ref = new referentiel();
    return $ref->update_instance($referentiel);
}

/**
 * Adds an referentiel instance
 *
 * This is done by calling the add_instance() method of the referentiel type class
 */
function referentiel_add_instance($referentiel) {
    $ref = new referentiel();
    return $ref->add_instance($referentiel);
}

/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in mod.html) this function
 * will update instance and return true or false
 *
 * @param object $form An object from the form in edit.html
 * @return int The id of the newly inserted referentiel record
 **/
/*

function referentiel_associe_referentiel_instance($form){
// importation ou selection ou creation
global $DB;
	if (!empty($form->instance)
		&& !empty($form->new_referentiel_id)){
		// id referentiel doit etre numerique
		$referentiel_id = intval(trim($form->instance));
		$referentiel_referentiel_id = intval(trim($form->new_referentiel_id));
		$referentiel = referentiel_get_referentiel($referentiel_id);
		$referentiel->ref_referentiel=$referentiel_referentiel_id;
		// DEBUG
		// echo "<br />DEBUG :: lib.php :: 152 :: referentiel_associe_referentiel_instance()<br />\n";
		// print_object($referentiel);
		// echo "<br />";
		return ($DB->update_record("referentiel", $referentiel));
	}
	return 0;
}
*/

// MODIF JF 2012/03/05

function referentiel_associe_referentiel_instance($instanceid, $refrefid){
// importation ou selection ou creation
global $DB;
	if (!empty($instanceid) && !empty($refrefid) ){
        return $DB->set_field("referentiel", "ref_referentiel", $refrefid, array("id"=>$instanceid));
	}
	return 0;
}



/**
 * Given an object containing referentiel id, 
 * will set referentiel_id to 0
 *
 * @param id 
 * @return 0
 **/
function referentiel_de_associe_referentiel_instance($id){
global $DB;
// suppression de la reference vers un referentiel_referentiel
	if (!empty($id)){
		// id referentiel doit Ãªtre numerique
		$id = intval(trim($id));
		$referentiel = referentiel_get_referentiel($id);
		$referentiel->ref_referentiel=0;

		// DEBUG
		// print_object($referentiel);
		// echo "<br />";
		return ($DB->update_record("referentiel", $referentiel));
	}
	return 0;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other referentiel functions go here.  Each of them must have a name that 
/// starts with referentiel_

/**
 * This function returns max id from table passed
 *
 * @param table name
 * @return id
 * @todo Finish documenting this function
 **/
function referentiel_get_max_id($table){
global $DB;
	if (isset($table) && ($table!="")){
        $sql="SELECT MAX(id) as m FROM {$table}";
        $r=$DB->get_record_sql($sql, NULL);
		if (!empty($r) && !empty($r->m)){
			return $r->m;
		}
	}
	return 0;
}

/**
 * This function returns min id from table passed
 *
 * @param table name
 * @return id
 * @todo Finish documenting this function
 **/
function referentiel_get_min_id($table){
global $DB;
	if (isset($table) && ($table!="")){
		$r=$DB->get_record_sql("SELECT MIN(id) as m FROM {$table}", NULL);
		if (!empty($r) && !empty($r->m)){
			return $r->m;
		}
	}
	return 0;
}


function referentiel_get_table($id, $table) {
global $DB;
// retourn un objet  
    // DEBUG
    // temp added for debugging
    // echo "DEBUG : GET INSTANCE CALLED";
    // echo "<br />";
	
	// referentiel
	$objet = $DB->get_record($table, array("id" => "$id"));
    // DEBUG
	// print_object($objet);
    // echo "<br />";
	return $objet;
}

/**
 * This function returns nomber of domains from table referentiel
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/
function referentiel_get_nb_domaines($id){
global $DB;
	if (isset($id) && ($id>0)){
        $params = array("id" => "$id");
        $sql="SELECT nb_domaines FROM {referentiel_referentiel} WHERE id=:id";
		return $DB->get_record_sql($sql, $params);
	}
	else 
		return 0; 
}

/**
 * This function returns records from table referentiel_domaine
 *
 * @param ref
 * @return record
 * @todo Finish documenting this function
 **/
function referentiel_get_domaines($ref_referentiel){
global $DB;
	if (!empty($ref_referentiel)){
        $params = array("refid" => "$ref_referentiel");
        $sql="SELECT * FROM {referentiel_domaine} WHERE ref_referentiel=:refid ORDER BY num_domaine ASC";
		return $DB->get_records_sql($sql, $params);
	}
	else 
		return 0; 
}


/**
 * This function returns nomber of competences from table referentiel_domaine
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/
function referentiel_get_nb_competences($id){
global $DB;
	if (isset($id) && ($id>0)){
        $params = array("id" => "$id");
        $sql="SELECT nb_competences FROM {referentiel_domaine} WHERE id=:id";
        return $DB->get_record_sql($sql, $params);
	}
	else 
		return 0; 
}

/**
 * This function returns records from table referentiel_item_competence
 *
 * @param ref
 * @return id
 * @todo Finish documenting this function
 **/
function referentiel_get_competences($ref_domaine){
global $DB;
	if (!empty($ref_domaine)){
        $params = array("refdomaine" => "$ref_domaine");
        $sql="SELECT * FROM {referentiel_competence} WHERE ref_domaine=:refdomaine ORDER BY num_competence ASC";
        return $DB->get_records_sql($sql, $params);
	}
	else 
		return 0; 
}

/**
 * This function returns nomber of items from table referentiel_competence
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/
function referentiel_get_nb_item_competences($id){
global $DB;
	if (!empty($id)){
        $params = array("id" => "$id");
        $sql="SELECT nb_item_competences FROM {referentiel_competence} WHERE id=:id";
		return $DB->get_record_sql($sql, $params);
	}
	else 
		return 0; 
}

/**
 * This function returns records from table referentiel_item_competence
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/
function referentiel_get_item_competences($ref_competence){
global $DB;
	if (!empty($ref_competence)){
	    $params = array("refcompetence" => "$ref_competence");
        $sql="SELECT * FROM {referentiel_item_competence} WHERE ref_competence=:refcompetence ORDER BY num_item ASC ";
        return $DB->get_records_sql($sql, $params);
	}
	else 
		return 0; 
}

/**
 * This function returns an int from table referentiel_item_competence
 *
 * @param id
 * @return int of poids
 * @todo Finish documenting this function
 **/
function referentiel_get_poids_item($code, $referentiel_id){
global $DB;
	if (!empty($code)){
	    $params=array("code" => "$code", "refid" => "$referentiel_id");
        $sql="SELECT poids_item FROM {referentiel_item_competence} WHERE code_item=:code AND ref_referentiel=:refid ";
		$record=$DB->get_record_sql($sql, $params);
		if ($record){
			return $record->poids_item;
		}
	}
	return 0;
}


/**
 * This function returns an int from table referentiel_item_competence
 *
 * @param referentiel id
 * @return int of empreinte
 * @todo Finish documenting this function
 **/
function referentiel_get_empreinte_item($code, $referentiel_id){
global $DB;
	if (!empty($code)){
	    $params=array("code" => "$code", "refid" => "$referentiel_id");
        $sql="SELECT empreinte_item FROM {referentiel_item_competence} WHERE code_item=:code AND ref_referentiel=:refid";
		$record=$DB->get_record_sql($sql, $params);
		if ($record){
			return $record->empreinte_item;
		}
	}
	return 0;
}


/**
 * This function returns a string from table referentiel_item_competence
 *
 * @param referentiel id
 * @return string of description#poids|
 * @todo Finish documenting this function
 **/
function referentiel_get_liste_poids($referentiel_id){
global $DB;
$liste="";
    $params=array("refid" => "$referentiel_id");
    $sql="SELECT id, description_item, poids_item FROM {referentiel_item_competence} WHERE ref_referentiel=:refid ORDER BY id ";
	$records=$DB->get_records_sql($sql, $params);
	if ($records){
		 foreach ($records as $record) {
		 	$liste.= strtr(stripslashes($record->description_item),'#|/:','    ').'#'.$record->poids_item.'|';
		 }
	}
	return $liste;
}



/**
 * This function returns a string from table referentiel_item_competence
 *
 * @param code, referentiel id
 * @return string
 * @todo Finish documenting this function
 **/

function referentiel_get_description_item($code, $referentiel_id=0){
global $DB;
	if (!empty($code)){
		if ($referentiel_id){
            $params=array("code" => "$code", "refid" => "$referentiel_id");
            $sql="SELECT description_item FROM {referentiel_item_competence} WHERE code_item=:code AND ref_referentiel=:refid";
            $record=$DB->get_record_sql($sql, $params);
			if ($record){
				return $record->description_item;
			}
		}
		else{
            $params=array("code" => "$code");
            $sql="SELECT description_item FROM {referentiel_item_competence} WHERE code_item=:code";
			$records=$DB->get_records_sql($sql, $params);
			if ($records){
				$s="";
				foreach ($records as $record){
					$s.=$record->description_item." ";
				}
				return $s;
			}
		}
	}
	return "";
}

/**
 * This function returns records from table referentiel
 *
 * @param $id : int id refrentiel to filter
 * $params filtering clause
 * @return int
 * @todo Finish documenting this function
 **/
function referentiel_filtrer($id, $data){
global $DB;
	if (!empty($id)){
        $params=array("id" => "$id", "zero" => "0");
		$where = "WHERE id=:id ";
        if (isset($data)){
			if (isset($data->filtrerinstance) && ($data->filtrerinstance!=0)){
				if (isset($data->localinstance) && ($data->localinstance==0)){
					$where .= " AND local=:zero ";
				}
				else {
					$where .= " AND local!=:zero ";
				}
			}
			// $data->referentiel_pass
			if (isset($data->referentiel_pass) && ($data->referentiel_pass!='')){
                $params=array("id" => "$id", "zero" => "0", "refpass" => "$data->referentiel_pass");
				$where .= " AND pass_referentiel=:refpass ";
			}
		}
		$sql="SELECT * FROM {referentiel_referentiel} $where ";
		$record = $DB->get_record_sql($sql, $params);
		if ($record){
			return $record->id;
		}
		else {
			return 0;
		}
	}
	else{
		return 0;
	}
}

/**
 * This function returns records from table referentiel_referentiel
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/
function referentiel_get_referentiel_referentiel($id){
global $DB;
 if (!empty($id)){
        $params = array("id" => "$id");
        $sql="SELECT * FROM {referentiel_referentiel} WHERE id=:id ";
		return $DB->get_record_sql($sql, $params);
	}
	else 
		return 0; 
}

/**
 * This function returns string from table referentiel_referentiel
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/
function referentiel_get_nom_referentiel($id){
global $DB;
$s="";
 if (!empty($id)){
        $params = array("id" => "$id");
        $sql="SELECT * FROM {referentiel_referentiel} WHERE id=:id ";
        $record=$DB->get_record_sql($sql, $params);
		if ($record){
			$s=$record->name;
		}
	}
	return $s; 
}


/**
 * This function returns records from table referentiel
 *
 * @param $params filtering clause
 * @return records
 * @todo Finish documenting this function
 **/
function referentiel_get_referentiel_referentiels($data){
global $DB;
	$where = "";
	if (isset($data)){
		if (isset($data->filtrerinstance) && ($data->filtrerinstance!=0)){
			if (isset($data->localinstance) && ($data->localinstance==0)){
				$where = " WHERE local=0 ";
			}
			else {
				$where = " WHERE local!=0 ";
			}
		}
	}
	$sql="SELECT * FROM {referentiel_referentiel} $where ORDER BY id ASC ";
	return $DB->get_records_sql($sql, NULL);
}

/**
 * This function returns records from table referentiel
 *
 * @param $params filtering clause
 * @return records
 * @todo Finish documenting this function
 **/
function referentiel_get_infos_from_code_ref($code){
global $DB;
	$trefref = array();
	if (!empty($code)){
        $params=array("code" => "$code");
        $sql="SELECT * FROM {referentiel_referentiel} WHERE code_referentiel=:code ORDER BY id ASC ";
        $recs=$DB->get_records_sql($sql, $params);
        if ($recs){
            foreach($recs as $rec){
                $trefref[]=$rec->id.':'.stripslashes($rec->code_referentiel).':'.stripslashes($rec->name);
            }
       }
    }
    return $trefref;
}

/**
 * This function returns records from table referentiel
 *
 * @param id
 * @return int
 * @todo Finish documenting this function
 **/
function referentiel_get_referentiel($id){
global $DB;
	if (!empty($id)){
	    $params = array("id" => "$id");
        $sql="SELECT * FROM {referentiel} WHERE id=:id ";
		return $DB->get_record_sql($sql, $params);
	}
	else 
		return 0; 
}


// ------------------
function referentiel_get_logo($occurrence){
        if (!empty($occurrence)){
            return $occurrence->logo_referentiel;
        }
        else{
            return '';
        }
}


    // ################################ URL  ###############################

    /**
     * display an url accorging to moodle file mangement
     * @return string active link
	 * @ input $url : an uri
	 * @ input $etiquette : a label
     */

    function referentiel_affiche_url($url, $etiquette="", $cible="") {
    // ADAPTE MOODLE2
	global $CFG;
	// Moodle 1.9
		/*
		$importfile = "{$CFG->dataroot}/{$url}";
		if (file_exists($importfile)) {
	        if ($CFG->slasharguments) {
    	    	$efile = "{$CFG->wwwroot}/file.php/$url";
        	}
		    else {
				$efile = "{$CFG->wwwroot}/file.php?file=/$url";
        	}
		}
		else{
			$efile = "$url";
		}
		*/
		// Moodle 2.0
		if (!preg_match("/http/",$url)){ // fichier telecharge
            // l'URL a été correctement formée lors de la création du fichier
            $efile =  $CFG->wwwroot.'/pluginfile.php'.$url;
        }
        else{
            $efile = $url;
        }

		if ($etiquette==""){
			$l=strlen($url);
			$posr=strrpos($url,'/');
			if ($posr===false){ // pas de separateur
				$etiquette=$url;
			}
			else if ($posr==$l-1){ // separateur en fin
				$etiquette=get_string("etiquette_inconnue", "referentiel");
			}
			else if ($posr==0){ // separateur en tete et en fin !
				$etiquette=get_string("etiquette_inconnue", "referentiel");
			}
			else {
				$etiquette=substr($url,$posr+1);
			}
		}
		
        if ($cible){
            return "<a href=\"$efile\" target=\"".$cible."\">$etiquette</a>";
        }
        else{
            return "<a href=\"$efile\">$etiquette</a>";
        }

    }


// ############################ MOODLE 1.9 FILE API #########################
    /**
     * get directory into which export is going
     * @return string file path
	 * @ input $course_id : id of current course
	 * @ input $sous_repertoire : a relative path
     */
    function referentiel_get_export_dir($course_id, $sous_repertoire="") {
	global $CFG;
	/*
    // ensure the files area exists for this course
	// $path_to_data=referentiel_get_export_dir($course->id,"$referentiel->id/$USER->id");
	$path_to_data=referentiel_get_export_dir($course->id);
    make_upload_directory($path_to_data);
	*/
        $dirname = get_string('exportfilename', 'referentiel');
        $path = $course_id.'/'.$CFG->moddata.'/'.$dirname;
		if ($sous_repertoire!=""){
			$pos=strpos($sous_repertoire,'/');
			if (($pos===false) || ($pos!=0)){ // separateur pas en tete
				// RAS
			}
			else {
				$sous_repertoire = substr($sous_repertoire,$pos+1);
			}
			$path .= '/'.$sous_repertoire;
		}
        return $path;
    }


    /**
     * write a file
     * @return boolean
	 * @ input $path_to_data : a data path
	 * @ input $filename : a filename
     */
    function referentiel_enregistre_fichier($path_to_data, $filename, $expout) {
        global $CFG;
        // create a directory for the exports (if not already existing)
        if (! $export_dir = make_upload_directory($path_to_data)) {
              print_error( get_string('cannotcreatepath', 'referentiel', $export_dir) );
			  return "";
        }
        $path = $CFG->dataroot.'/'.$path_to_data;

        // write file
        $filepath = $path."/".$filename;

        if (!$fh=fopen($filepath,"w")) {
            return "";
        }
        if (!fwrite($fh, $expout, strlen($expout) )) {
            return "";
        }
        fclose($fh);
        return $path_to_data.'/'.$filename;
    }

    /**
     * write a file
     * @return boolean
	 * @ input $path_to_data : a data path
	 * @ input $filename : a filename
     */
    function referentiel_upload_fichier($path_to_data, $filename_source, $filename_dest) {
        global $CFG;
        // create a directory for the exports (if not already existing)
        if (! $export_dir = make_upload_directory($path_to_data)) {
              print_error( get_string('cannotcreatepath', 'referentiel', $export_dir) );
			  return "";
        }
        $path = $CFG->dataroot.'/'.$path_to_data;

		if (referentiel_deplace_fichier($path, $filename_source, $filename_dest, '/', true)){
			return $path_to_data.'/'.$filename_dest;
		}
		else {
			return "";
		}
    }

// ------------------
function referentiel_deplace_fichier($dest_path, $source, $dest, $sep, $deplace) {
// recopie un fichier sur le serveur
// pour effectuer un deplacement $deplace=true
// @ devant une fonction signifie qu'aucun message d'erreur n'est affiche
// $dest_path est le dossier de destination du fichier
// source est le nom du fichier source (sans chemin)
// dest est le nom du fichier destination (sans chemin)
// $sep est le separateur de chemin
// retourne true si tout s'est bien deroule

	// Securite
	if (strstr($dest, "..") || strstr($dest, $sep)) {
		// interdire de remonter dans l'arborescence
		// la source est detruite
		if ($deplace) @unlink($source);
		return false;
	}

	// repertoire de stockage des fichiers
	$loc = $dest_path.$sep.$dest;
// 	$ok = @copy($source, $loc);
	$ok =  @copy($source, $loc);
	if ($ok){
		// le fichier temporaire est supprime
		if ($deplace)  @unlink($source);
	}
	else{
		// $ok = @move_uploaded_file($source, $loc);
		$ok =  @move_uploaded_file($source, $loc);
	}
	return $ok;
}


	// ------------------
	function referentiel_get_file_m19($filename, $course_id, $path="" ) {
	// retourne un path/nom_de_fichier dans le dossier moodledata
 		global $CFG; global $DB;
 		if ($path==""){
			$currdir = $CFG->dataroot."/$course_id/$CFG->moddata/referentiel/";
  		}
		else {
			$currdir = $CFG->dataroot."/$course_id/$CFG->moddata/referentiel/".$path;
		}

	    if (!file_exists($currdir.'/'.$filename)) {
      		return "";
      	}
		else{
			return $currdir.'/'.$filename;
		}
 	}


// ############################ CONVERSION DES URL MOODLE 1.9 en MOODLE 2.0 #########################
// Il faut que le dossier moodledata/ de la version moodle 1.9 n'ait pas été supprimé

	// ------------------
	function referentiel_m19_to_m2_file($data, $context, $verbose=false, $delete=false) {
	// convertit une url M1.9 en url M2.x
	// effectue la sauvegarde du fichier cible dans l'espace de fichier de l'utilisateur
	// Input $data->url
	// 2/moddata/referentiel/1/7/Module_Referentiel_de_Moodle_Nouveautes_2013.pdf
	// data : an object
	// $context : context
	// data->filearea = 'document' | 'consigne'
	// Output
	// /28/mod_referentiel/document/1/Module_Referentiel_de_Moodle_Nouveautes_2013.pdf
	global $CFG;
	global $DB;
		$ok=false;
		$msg='';
	    $retour_url=$data->url;
		if (!empty($data->id) && !empty($data->url) && !empty($data->filearea)){
			$oldpath = $CFG->dataroot.'/'.$data->url;
			if (file_exists($oldpath)){
				$content=file_get_contents($oldpath);
				if (!empty($content)) {
					if (preg_match('/moddata\/referentiel/',$data->url)){
						$parts=explode('/',$data->url);
		    			$old_course_id=$parts[0]; // not used here
		    			$old_ref_id=$parts[3];   // not used here
			    		$old_user_id=$parts[4];  // not used here
			    		$filename=$parts[5];
						//echo "<br />FILENAME : $filename<br />\n";
						if (!empty($filename)){
				    		if ($context->contextlevel == CONTEXT_MODULE) {
								// verifier les fileareas acceptables
								$fileareas = array('document', 'consigne');
                                // $fileareas = array('referentiel', 'document', 'consigne', 'activite', 'task', 'certificat', 'scolarite', 'pedagogie', 'outcomes', 'archive');

    	    					if (in_array($data->filearea, $fileareas)) {
        							$fullnewpath = '/'.$context->id.'/mod_referentiel/'.$data->filearea.'/'.$data->id.'/'.$filename;

									$file_record=new object();
	                				$file_record->contextid = $context->id;
    	            				$file_record->component='mod_referentiel';
        	        				$file_record->filearea=$data->filearea;
	        	        			$file_record->itemid=$data->id;
	        	        			$file_record->userid=$data->userid;
                                    if (isset($data->author)){
										$file_record->author=$data->author;
									}
									$file_record->filepath='/';
									$file_record->filename=$filename;

        							$fs = get_file_storage();
	        						if ($fs->create_file_from_string($file_record, $content)) {

										if ($data->filearea=='document'){
				             				$ok=$DB->set_field('referentiel_document','url_document',$fullnewpath, array('id'=>$data->id));
	        							}
        								else if ($data->filearea=='consigne'){
            								$ok=$DB->set_field('referentiel_consigne','url_consigne',$fullnewpath, array('id'=>$data->id));
										}
                   						$retour_url=$fullnewpath;

										$newlink = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullnewpath);
                                        $oldlink = new moodle_url($oldpath);
                						if ($verbose){
											$msg.="<br />New link : <a href=\"$newlink\" target=\"blank\">$newlink</a>\n";
										}
										if ($delete){
											// le fichier original est supprime
											if (unlink($oldpath)){
												if ($verbose){
													$msg.=" :: Old link $oldlink deleted\n";
												}
											}
											else{
            									if ($verbose){
                                                	$msg.=" :: Old link $oldlink <i>NOT</i> deleted\n";
												}
											}
										}
	       							}
									else{
            							if ($verbose){
                                    		$msg.="<br />New file creation error \n";
										}
									}
								}
								else{
									// que doit-on faire ?
            						if ($verbose){
                                    	$msg.="<br />The filearea $data->filearea <i>does't match</i> a link to referentiel_document nor referentiel_consigne\n";
									}
								}
							}
						}
					}
				}
			}
			else{
				if ($verbose){
					$msg .= "<br />File $oldpath not found\n";
				}

			 	if ($delete){
        			if ($data->filearea=='document'){
						if ($DB->delete_records('referentiel_document', array('id'=>$data->id))){
                    		if ($verbose){
								$msg .= " :: Failed link deleted from table 'referentiel_document'\n";
							}
						}
						else{
                    		if ($verbose){
								$msg .= " :: Failed link <i>NOT</i> deleted from table 'referentiel_document'\n";
							}
						}
	        		}
        			else if ($data->filearea=='consigne'){
						if ($DB->delete_records('referentiel_consigne', array('id'=>$data->id))){
                    		if ($verbose){
								$msg .= " :: Failed link deleted from table 'referentiel_consigne'\n";
							}
						}
						else{
                    		if ($verbose){
								$msg .= " :: Failed link <i>NOT</i> deleted from table 'referentiel_consigne'\n";
							}
						}
					}
				}
			}
		}
		if ($verbose && !empty($msg)){
			echo $msg;
		}

		return $retour_url;
	}



// ----------------------
function referentiel_recherche_url_m19(){
// return number of m19 links
	global $DB;
	$n=0;
	$params=array('urlm19'=>'%/moddata/referentiel/%');
	$sql="SELECT id, ref_activite, url_document FROM {referentiel_document} WHERE url_document LIKE :urlm19 ORDER BY ref_activite ";
	$recs_documents=$DB->get_records_sql($sql, $params);
	if (!empty($recs_documents)){
		$n=count($recs_documents);
	} 
	$params=array('urlm19'=>'%/moddata/referentiel/%');
	$sql="SELECT id, ref_task, url_consigne FROM {referentiel_consigne} WHERE url_consigne LIKE :urlm19 ORDER BY ref_task ";
	$recs_consignes=$DB->get_records_sql($sql, $params);
	if (!empty($recs_consignes)){
		$n+=count($recs_consignes);
	}
	return $n; 
}

// ---------------------
function referentiel_conversion_url_m19($delete=false, $verbose=false){
// move all m19 links and data to m2.x links and files
global $CFG;
global $DB;
		// documents
		$params=array('urlm19'=>'%/moddata/referentiel/%');
		$sql="SELECT id, ref_activite, url_document FROM {referentiel_document} WHERE url_document LIKE :urlm19 ORDER BY ref_activite ";
		$recs_documents=$DB->get_records_sql($sql, $params);
		if (!empty($recs_documents)){
			$activiteid=0;
			foreach($recs_documents as $doc){
				if ($doc->ref_activite!=$activiteid){
					$activiteid=$doc->ref_activite;
					$activite=$DB->get_record('referentiel_activite', array('id'=>$doc->ref_activite));
					$cm = get_coursemodule_from_instance('referentiel', $activite->ref_instance, $activite->ref_course);
					$context = context_module::instance($cm->id);
				}
	            $data_r=new Object();
				$data_r->id = $doc->id;
				$data_r->userid = $activite->userid;
				$data_r->author = referentiel_get_user_info($activite->userid);
				$data_r->url = $doc->url_document;
				$data_r->filearea = 'document';
        		$url_document = referentiel_m19_to_m2_file($data_r, $context, $verbose, $delete);
			}
		}

		// consignes
		$params=array('urlm19'=>'%/moddata/referentiel/%');
		$sql="SELECT id, ref_task, url_consigne FROM {referentiel_consigne} WHERE url_consigne LIKE :urlm19 ORDER BY ref_task ";
		$recs_consignes=$DB->get_records_sql($sql, $params);
		if (!empty($recs_consignes)){
			$taskid=0;
			foreach($recs_consignes as $doc){
				if ($doc->ref_task!=$taskid){
					$taskid=$doc->ref_task;
					$task=$DB->get_record('referentiel_task', array('id'=>$doc->ref_task));
					$cm = get_coursemodule_from_instance('referentiel', $task->ref_instance, $task->ref_course);
					$context = context_module::instance($cm->id);
				}
	            $data_r=new Object();
				$data_r->id = $doc->id;
				$data_r->userid = $task->auteurid;
				$data_r->author = referentiel_get_user_info($task->auteurid);
				$data_r->url = $doc->url_consigne;
				$data_r->filearea = 'consigne';
        		$url_consigne = referentiel_m19_to_m2_file($data_r, $context, $verbose, $delete);
			}
		}
		//$CFG->referentiel_migration_19_2x=0;
		// pas de nouvelle conversion en principe...
}


// ############################ MOODLE 2.0 FILE API #########################


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
function referentiel_get_file_areas($course, $cm, $context) {
    return array(
        'referentiel' => get_string('areareferentiel', 'referentiel'),
        'document' => get_string('areadocument', 'referentiel'),
        'consigne' => get_string('areaconsigne', 'referentiel'),
        'activite' => get_string('areaactivite', 'referentiel'),
        'task' => get_string('areatask', 'referentiel'),
        'certificat' => get_string('areacertificat', 'referentiel'),
        'scolarite' => get_string('areascolarite', 'referentiel'),
        'pedagogie' => get_string('areapedagogie', 'referentiel'),
        'outcomes' => get_string('areaoutcomes', 'referentiel'),
        'archive' => get_string('areaarchive', 'referentiel'),
    );
}


/**
 * Serves documents and other files.
 * @package  mod_referentiel
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
function referentiel_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    $areas = referentiel_get_file_areas($course, $cm, $context);

    // filearea must contain a real area
    if (!isset($areas[$filearea])) {
        return false;
    }


    $docid = (int)array_shift($args);

    if ($filearea=='referentiel'){
        // verifier qu'un referentiel est installé dans ce cours
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$cm->instance"))) {
            return false;
        }
    }
    if ($filearea=='document'){
            if (!$document = $DB->get_record('referentiel_document', array('id'=>$docid))) {
                return false;
            }
    }
    if ($filearea=='consigne'){
            if (!$document = $DB->get_record('referentiel_consigne', array('id'=>$docid))) {
                return false;
            }
    }

    //return referentiel_send_file($course, $cm, $context, $filearea, $args);
    $relativepath = implode('/', $args);
    $fullpath = '/'.$context->id.'/mod_referentiel/'.$filearea.'/'.$docid.'/'.$relativepath;

    $fs = get_file_storage();

    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, true); // download MUST be forced - security!

}


//------------------
function referentiel_upload_document($mform, $referentiel_id){
// Traite le formulaire de saisie d'un fichier
// mise à jour des tables document ou consigne
global $CFG, $USER, $DB, $OUTPUT;

    // DEBUG
    // echo "<br />DEBUG :: ./mod/referentiel/lib.php :: upload_moodle2.php :: 1815 :: referentiel_upload_document\n";
    //print_object($mform);

    $viewurl=new moodle_url('/mod/referentiel/view.php', array('d'=>$referentiel_id));

    $retour_url='';

    if ($formdata = $mform->get_data()) {
        // DEBUG
        // echo "<br />DEBUG :: lib.php :: 5682 :: referentiel_upload_document\n";
        // print_object($formdata);

        // documents activites et consignes des tâches
        $fileareas = array('referentiel', 'document', 'consigne', 'activite', 'task', 'certificat', 'scolarite', 'pedagogie', 'archive');

        if (empty($formdata->filearea) || !in_array($formdata->filearea, $fileareas)) {
            return false;
        }


        // DEBUG
        // print_object($document);
        // echo "<br />";
        if ($formdata->filearea=='document'){
            // gestion d'un fichier à la fois
            $document = new object();
            $document->url_document='';
            $document->type_document='';
            $document->description_document='';
            $document->ref_activite='';
            $document->cible_document=1;
            $document->etiquette_document='';
            $document->timestamp=time();

            $docid = $DB->insert_record("referentiel_document", $document);
            $retour_url=new moodle_url('/mod/referentiel/activite.php', array('d'=>$referentiel_id, 'userid'=>$formdata->userid, 'activite_id'=>$formdata->activiteid, 'mailnow' => $formdata->mailnow, 'mode' => 'listactivityall', 'select_acc' => 0));
        }
        else if ($formdata->filearea=='consigne'){
            // gestion d'un fichier à la fois
            $document = new object();
            $document->url_consigne='';
            $document->type_consigne='';
            $document->description_consigne='';
            $document->ref_task='';
            $document->cible_consigne=1;
            $document->etiquette_consigne='';
            $document->timestamp=time();
            
            $docid = $DB->insert_record("referentiel_consigne", $document);
            $retour_url= new moodle_url('/mod/referentiel/task.php', array('d'=>$referentiel_id, 'mode' => 'listtasksingle', 'select_acc' => 0));
        }


        $newfilename = '';
        $fullpath = '';

        $fs = get_file_storage();
        // suppression du fichier existant ?   NON
        // $fs->delete_area_files($formdata->contextid, 'mod_referentiel', $formdata->filearea, $docid);

        if ($newfilename = $mform->get_new_filename('referentiel_file')) {
            $file = $mform->save_stored_file('referentiel_file', $formdata->contextid,
                'mod_referentiel',$formdata->filearea,$docid,'/', $newfilename);
            // DEBUG
            // echo "<br />DEBUG :: lib.php 5730 :: $newfilename\n";
            // print_object($file);

            $fullpath = "/$formdata->contextid/mod_referentiel/$formdata->filearea/$docid/$newfilename";
            $link = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);
            /*
            // DEBUG
            $messagetext = file_rewrite_pluginfile_urls($newfilename, 'pluginfile.php', $formdata->contextid, 'mod_referentiel', '$formdata->filearea', $docid);

            echo "<br />DEBUG :: 1900 :: $link<br />\n";
            echo "<br />Message:: $messagetext<br />\n";
            */

        }
        else if (!empty($formdata->url)){
            $fullpath = $formdata->url;
        }

        if (!empty($fullpath)){

            if (!empty($docid) && ($formdata->filearea=='document') && !empty($formdata->activiteid)){
                $document = new object();
                $document->id=$docid;
                $document->url_document=$fullpath;
                $document->type_document=substr($formdata->type,0, 20);
                if (empty($formdata->description)){
                    $document->description_document=get_string('url', 'referentiel');
                }
                else{
                    $document->description_document=$formdata->description;
                }
                $document->ref_activite=$formdata->activiteid;
                $document->cible_document=$formdata->cible;
                $document->timestamp=time();
            /*
            echo "<br />DOCID : $docid\n";
            echo "<br />URL : $formdata->url\n";
            echo "<br />FILEAREA : $formdata->filearea\n";
            echo "<br />FULLPATH : $fullpath\n";
            echo "<br />DESCRIPTION : $formdata->description\n";
            echo "<br />ACTIVITE ID  : $formdata->activiteid\n";
            echo "<br />Etiquette  : $formdata->etiquette\n";
            */

                if (!empty($formdata->etiquette)){
                    $document->etiquette_document=$formdata->etiquette;
                }
                else{
                    if (!empty($newfilename)){
                        $document->etiquette_document=$newfilename;
                    }
                    else{
                        $document->etiquette_document=get_string('url', 'referentiel');
                    }
                }

                // print_object($document);
                // exit;
                // Modif JF 2013/02/02
                if ($DB->update_record("referentiel_document", $document)){
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
            else if (!empty($docid) && ($formdata->filearea=='consigne')  && !empty($formdata->activiteid)){
                $document = new object();
                $document->id=$docid;
                $document->url_consigne=$fullpath;
                $document->type_consigne=substr($formdata->type,0, 20);
                if (empty($formdata->description)){
                    $document->description_consigne=get_string('url', 'referentiel');
                }
                else{
                    $document->description_consigne=$formdata->description;
                }
                $document->ref_task=$formdata->activiteid;
                $document->cible_consigne=$formdata->cible;
                $document->timestamp=time();
                if (!empty($formdata->etiquette)){
                    $document->etiquette_consigne=$formdata->etiquette;
                }
                else{
                    if (!empty($newfilename)){
                        $document->etiquette_consigne=$newfilename;
                    }
                    else {
                        $document->etiquette_consigne=get_string('url', 'referentiel');
                    }
                }
                
                if ($DB->update_record("referentiel_consigne", $document)){
                    $task = $DB->get_record('referentiel_task', array('id' => $document->ref_task));
                    if ($task){
                        $ok=$DB->set_field('referentiel_task','date_modif',time(), array('id'=>$task->id));
                    }
                }
            }
            else{
                //
                echo  '<div align="center"><a href="'.$link.'" target="_blank">'.$link.'</a>'."</div>\n";
            }
        }
        if (!empty($retour_url)){
            //redirect($retour_url, get_string('uploadedfile'));
            redirect($retour_url);  // inutile d'afficher un message disant que le telechargement est OK !
        }
    }
    redirect($viewurl);
}

// ------------------
function referentiel_get_area_files($contextid, $filearea, $docid){
// retourne la liste des liens vers des fichiers stockes dans le filearea
global $CFG;
    // fileareas autorisees
    $ok=false;
    $fileareas = array('referentiel', 'document', 'consigne', 'activite', 'task', 'certificat', 'scolarite', 'pedagogie', 'archive');
    if (!in_array($filearea, $fileareas)) {
        return false;
    }

    $strfilename=get_string('filename', 'referentiel');
    $strfilesize=get_string('filesize', 'referentiel');
    $strtimecreated=get_string('timecreated', 'referentiel');
    $strtimemodified=get_string('timemodified', 'referentiel');
    $strmimetype=get_string('mimetype', 'referentiel');
    $strurl=get_string('url');

    $table = new html_table();

	$table->head  = array ($strfilename, $strfilesize, $strtimecreated, $strtimemodified, $strmimetype);
    $table->align = array ("center", "left", "left", "left");

    $fs = get_file_storage();
    if ($files = $fs->get_area_files($contextid, 'mod_referentiel', $filearea, $docid, "timemodified", false)) {
         foreach ($files as $file) {
            // print_object($file);
            $filesize = $file->get_filesize();
            $filename = $file->get_filename();
            $mimetype = $file->get_mimetype();
            $filepath = $file->get_filepath();
            $fullpath ='/'.$contextid.'/mod_referentiel/'.$filearea.'/'.$docid.$filepath.$filename;

            $timecreated =  userdate($file->get_timecreated(),"%Y/%m/%d-%H:%M",99,false);
            $timemodified = userdate($file->get_timemodified(),"%Y/%m/%d-%H:%M",99,false);
            $link= new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);
            $url='<a href="'.$link.'" target="_blank">'.$filename.'</a><br />'."\n";
            $table->data[] = array ($url, $filesize, $timecreated, $timemodified, $mimetype);
            $ok=true;
        }
    }
    if ($ok){
        return html_writer::table($table);
    }
    else{
        return '';
    }
}

// ------------------
function referentiel_get_manage_files($contextid, $filearea, $docid, $titre, $appli){
// retourne la liste des liens vers des fichiers stockes dans le filearea
// propose la suppression
global $CFG;
global $OUTPUT;
    $total_size=0;
    $nfile=0;
    // fileareas autorisees
    $fileareas = array('referentiel', 'document', 'consigne', 'activite', 'task', 'certificat', 'scolarite', 'pedagogie', 'archive');
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
    if ($files = $fs->get_area_files($contextid, 'mod_referentiel', $filearea, $docid, "timemodified", false)) {
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
            $fullpath = '/'.$contextid.'/mod_referentiel/'.$filearea.'/'.$docid.$filepath.$filename;
            $timecreated =  userdate($file->get_timecreated(),"%Y/%m/%d-%H:%M",99,false);
            $timemodified = userdate($file->get_timemodified(),"%Y/%m/%d-%H:%M",99,false);

            $link= new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);
            $url='<a href="'.$link.'" target="_blank">'.$filename.'</a><br />'."\n";
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

// ------------------
function referentiel_get_a_file($filename, $contextid, $filearea, $itemid=0){
// retourne un fichier
// NON TESTE
global $CFG;
    // fileareas autorisees
    $fileareas = array('referentiel', 'document', 'consigne', 'activite', 'task', 'certificat', 'scolarite', 'pedagogie', 'archive');
    if (!in_array($filearea, $fileareas)) {
        return false;
    }

    $strfilename=get_string('filename', 'referentiel');
    $strfilesize=get_string('filesize', 'referentiel');
    $strtimecreated=get_string('timecreated', 'referentiel');
    $strtimemodified=get_string('timemodified', 'referentiel');
    $strmimetype=get_string('mimetype', 'referentiel');
    $strurl=get_string('url');

    $table = new html_table();

	$table->head  = array ($strfilename, $strfilesize, $strtimecreated, $strtimemodified, $strmimetype);
    $table->align = array ("center", "left", "left", "left");
    $fs = get_file_storage();
    $file = $fs->get_file($contextid, 'mod_referentiel', $filearea, $itemid,'/', $filename);
    if ($file) {
        // DEBUG
        // echo "<br />DEBUG :: 220 :: $filename\n";
        // print_object($file);
        // echo "<br />CONTENU\n";
        // $contents = $file->get_content();
        // echo htmlspecialchars($contents);
        $filesize = $file->get_filesize();
        $filename = $file->get_filename();
        $mimetype = $file->get_mimetype();
        $filepath = $file->get_filepath();
        $fullpath ='/'.$contextid.'/mod_referentiel/'.$filearea.'/'.$docid.$filepath.$filename;

        $timecreated =  userdate($file->get_timecreated(),"%Y/%m/%d-%H:%M",99,false);
        $timemodified = userdate($file->get_timemodified(),"%Y/%m/%d-%H:%M",99,false);
        $link= new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);
        $url='<a href="'.$link.'" target="_blank">'.$filename.'</a><br />'."\n";
        $table->data[] = array ($url, $filesize, $timecreated, $timemodified, $mimetype);
    }

    echo html_writer::table($table);
}

// ------------------
function referentiel_get_file($moodlefullpath) {
	// retourne le chemin absolu du fichier pour traitement par impression PDF et autres

 	global $CFG;
    $filedir = $CFG->dataroot;
    $filedir = str_replace('\\','/',$filedir);
    $filedir.='/filedir'; // propre à Moodle

    // initialisation par defaut
    $contextid=0;
    $component='mod_referentiel';
    $filearea='referentiel';
    $itemid=0;
    $path='/';
    $filename=$moodlefullpath;

    // Traitement de $fullpath
    if ($moodlefullpath && preg_match('/\//', $moodlefullpath)){
        $t_fullpath=explode('/', $moodlefullpath, 6);
        if (!empty($t_fullpath) && empty($t_fullpath[0])){
            $garbage=array_shift($t_fullpath);
        }
        if (!empty($t_fullpath)){
            list($contextid, $component, $filearea, $itemid, $path )  = $t_fullpath;
            if ($path){
                if (preg_match('/\//', $path)){
                    $filename=substr($path, strrpos($path, '/')+1);
                    $path='/'.substr($path, 0, strrpos($path, '/')+1);
                }
                else{
                    $filename=$path;
                    $path='/';
                }
            }
        }
    }

    require_once($CFG->libdir.'/filelib.php');
    $fs = get_file_storage();

    // Get file
    // echo "<br />($contextid, $component, $filearea, $itemid, $path, $filename)\n";
    $file = $fs->get_file($contextid, $component, $filearea, $itemid, $path, $filename);
    if ($file) {
        $contenthash = $file->get_contenthash(); // nom du fichier tel qu'il est stocké
        $thefilepath=$filedir.'/'.substr($contenthash,0,2).'/'.substr($contenthash,2,2).'/'.$contenthash;
        //echo  "<br />FILE PATH : $thefilepath\n";
        return $thefilepath;
    }
    return '';
}


/**
 * This function wil delete a file
 * fullpath of the form /contextid/mod_referentiel/filearea/itemid.path.filename
 * path  : any path beginning and ending in / like '/' or '/rep1/rep2/'
 * @fullpath string
 * @return nothing
 */
 
// ---------------------------------
function referentiel_delete_a_file($fullpath){
// supprime un fichier
// NON TESTE
// cas 0 : $fullpath de la forme "jf44.png";
// cas 1 : $fullpath de la forme "/30/mod_referentiel/referentiel/0/rep1/rep2/jf44.png"
// cas 2 : $fullpath de la forme "/51/mod_referentiel/referentiel/12/jf44.png"
global $CFG;

    // initialisation par defaut
    $contextid=0;
    $component='mod_referentiel';
    $filearea='referentiel';
    $itemid=0;
    $path='/';
    $filename=$fullpath;

    // Traitement de $fullpath
    if ($fullpath && preg_match('/\//', $fullpath)){
        $t_fullpath=explode('/',$fullpath,6);
        if (!empty($t_fullpath) && empty($t_fullpath[0])){
            $garbage=array_shift($t_fullpath);
        }
        if (!empty($t_fullpath)){
            list($contextid, $component, $filearea, $itemid, $path )  = $t_fullpath;
            if ($path){
                if (preg_match('/\//', $path)){
                    $filename=substr($path, strrpos($path, '/')+1);
                    $path='/'.substr($path, 0, strrpos($path, '/')+1);
                }
                else{
                    $filename=$path;
                    $path='/';
                }
            }
        }
    }
    
    // echo "<br />DEBUG :: lib.php :: Ligne 5918 ::<br /> $contextid, $component, $filearea, $itemid, $path, $filename\n";
    // devrait afficher cas 0  :: 0, mod_referentiel, referentiel, 0, /, jf44.png
    // devrait afficher cas 1  :: 30, mod_referentiel, referentiel, 0, /rep1/rep2/, jf44.png
    // devrait afficher cas 2  :: 51, mod_referentiel, referentiel, 12, /, jf44.png

    require_once($CFG->libdir.'/filelib.php');
    $fs = get_file_storage();

    // Get file
    $file = $fs->get_file($contextid, $component, $filearea, $itemid, $path, $filename);

    // Delete it if it exists
    if ($file) {
        $file->delete();
    }

}

// ----------------------------------------------------
function referentiel_activite_a_suivre($activite, $delai){
global $USER;

    if (empty($activite->approved)){
            // echo "<br>Test: DELAI: $delai secondes\n";
            // print_object($activite);
            // retourne une valeur de couleur si
            if (!empty($activite->date_modif_student)
                    &&
                    ($activite->date_modif < $activite->date_modif_student)
                    &&
                    ($activite->date_modif_student + $delai < time())
                ){

                return 1;
            }
    }
    return 0;
}

/**
 * This function returns records from table referentiel_activite
 *
 * @param id reference instance
 * @param delai : time to backtrack
 * @return objects
 * @todo Finish documenting this function
 **/
function referentiel_get_activites_instance_a_suivre($instance_id, $delai){
global $DB;
	if (!empty($instance_id)){
        $date= time() - $delai;
		$params = array("refid" => $instance_id, 'date' => $date);
        $sql="SELECT * FROM {referentiel_activite} WHERE ref_instance=:refid
 AND (approved=0)
 AND (date_modif_student<>0)
 AND (date_modif_student<:date)
 AND (date_modif<date_modif_student)
 ORDER BY userid ASC, date_creation DESC ";
        return $DB->get_records_sql($sql, $params);
    }
	return NULL;
}



?>

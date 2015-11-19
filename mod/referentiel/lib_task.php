<?php

// $Id:  lib_task.php,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
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
 * @version $Id: lib_task.php,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
 * @package referentiel
 **/
/*
DROP TABLE IF EXISTS prefix_referentiel_task`;
CREATE TABLE IF NOT EXISTS `mdl_referentiel_task` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_task` varchar(80) NOT NULL DEFAULT '',
  `description_task` text NOT NULL,
  `competences_task` text NOT NULL,
  `criteres_evaluation` text NOT NULL,
  `ref_instance` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ref_referentiel` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ref_course` bigint(10) unsigned NOT NULL DEFAULT '0',
  `auteurid` bigint(10) unsigned NOT NULL,
  `date_creation` bigint(10) unsigned NOT NULL DEFAULT '0',
  `date_modif` bigint(10) unsigned NOT NULL DEFAULT '0',
  `date_debut` bigint(10) unsigned NOT NULL DEFAULT '0',
  `date_fin` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='task' AUTO_INCREMENT=1 ;

--
-- Structure de la table `mdl_referentiel_consigne`
--

DROP TABLE IF EXISTS `mdl_referentiel_consigne`;
CREATE TABLE IF NOT EXISTS `mdl_referentiel_consigne` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_consigne` varchar(20) NOT NULL DEFAULT '',
  `description_consigne` text NOT NULL,
  `url_consigne` varchar(255) NOT NULL DEFAULT '',
  `ref_task` bigint(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='consigne' AUTO_INCREMENT=1 ;

--
-- Structure de la table `mdl_referentiel_a_user_task`
--

DROP TABLE IF EXISTS `mdl_referentiel_a_user_task`;
CREATE TABLE IF NOT EXISTS `mdl_referentiel_a_user_task` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref_user` bigint(10) unsigned NOT NULL,
  `ref_task` bigint(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='user_select_task' AUTO_INCREMENT=1 ;

*/

// Quelques fonctions regroupées dans lib.php



/**
 * Given an task id,
 * this function will permanently delete the task instance
 * and any consigne that depends on it.
 *
 * @param object $id
 * @return boolean Success/Failure
 **/

 // -----------------------
function referentiel_delete_task_and_activities($id) {
// suppression task + consignes associes + liens vers activite et user + activites dues a la tache
global $DB;
$ok_task=false;
	if (!empty($id)){
		if ($task = $DB->get_record("referentiel_task", array("id" => "$id"))) {
	   		// Delete any dependent records here
			if ($r_a_users_tasks = $DB->get_records("referentiel_a_user_task", array("ref_task" => "$id"))) {
				// DEBUG
				// echo "<br />            \n";
				// print_object($r_a_users_tasks);
				// echo "<br />suppression des activites\n";
				foreach ($r_a_users_tasks as $r_a_user_task){
					// suppression de l'activite
					referentiel_delete_activity_record($r_a_user_task->ref_activite);
				}
			}
            $ok_task=referentiel_delete_task_record($id);
		}
	}
    return $ok_task;
}

// -----------------------
function referentiel_task_isowner($id){
global $DB;
global $USER;
	if (!empty($id)){
		$record=$DB->get_record("referentiel_task", array("id" => "$id"));
		// DEBUG
		// echo "<br >USERID : $USER->id ; OWNER : $record->userid\n";
		return ($USER->id == $record->auteurid);
	}
	else {
		return false;
	} 
} 



/**
 * Given a form, 
 * this function will permanently delete the task instance 
 * and any consigne that depends on it. 
 *
 * @param object $form
 * @return boolean Success/Failure
 **/

 // -----------------------
function referentiel_delete_task($form) {
// suppression task + consigne
$ok_task=false;
$ok_consigne=false;
    // DEBUG
	// echo "<br />";
	// print_object($form);
    // echo "<br />";
	if (isset($form->action) && ($form->action=="modifier_task")){
		// suppression d'une task et des consignes associes
		if (isset($form->taskid) && ($form->taskid>0)){
			$ok_task=referentiel_delete_task_record($form->taskid);
		}
	}
	else if (isset($form->action) && ($form->action=="modifier_consigne")){
		// suppression d'un consigne
		if (isset($form->consigne_id) && ($form->consigne_id>0)){
			$ok_consigne=referentiel_delete_consigne_record($form->consigne_id);
		}
	}
	
    return $ok_task or $ok_consigne;
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
 /*
CREATE TABLE IF NOT EXISTS `mdl_referentiel_task` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_task` varchar(80) NOT NULL DEFAULT '',
  `description_task` text NOT NULL,
  `competences_task` text NOT NULL,
  `criteres_evaluation` text NOT NULL,
  `ref_instance` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ref_referentiel` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ref_course` bigint(10) unsigned NOT NULL DEFAULT '0',
  `auteurid` bigint(10) unsigned NOT NULL,
  `date_creation` bigint(10) unsigned NOT NULL DEFAULT '0',
  `date_modif` bigint(10) unsigned NOT NULL DEFAULT '0',
  `date_debut` bigint(10) unsigned NOT NULL DEFAULT '0',
  `date_fin` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='task' AUTO_INCREMENT=1 ;
 */

 // -----------------------
function referentiel_add_task($form) {
// creation task + consigne
global $DB;
global $USER;
    // DEBUG
    // echo "DEBUG : ADD task CALLED : lib.php : ligne 1033";
	// print_object($form);
    // echo "<br />";
	// referentiel
	$task = new object();
	$task->type_task=($form->type_task);
	$task->description_task=($form->description_task);
	if (isset($form->code_item)){
  	$task->competences_task=reference_conversion_code_2_liste_competence('/', $form->code_item);
	}
	else{
    $task->competences_task='';
  }
  $task->criteres_evaluation=($form->criteres_evaluation);
	$task->ref_instance=$form->instance;
	$task->ref_referentiel=$form->ref_referentiel;
	$task->ref_course=$form->courseid;
	$task->auteurid=$USER->id;		
	$task->date_creation=time();
	$task->date_modif=time();
  $task->cle_souscription=($form->cle_souscription);
  $task->souscription_libre=($form->souscription_libre);
  if (isset($form->tache_masquee)) 
    $task->tache_masquee=$form->tache_masquee;
  else 
    $task->tache_masquee=0; 

    $task->mailed=1;  // MODIF JF 2010/10/05
    if (isset($form->mailnow)){
        $task->mailnow=$form->mailnow;
        if ($form->mailnow=='1'){ // renvoyer
            $task->mailed=0;   // annuler envoi precedent
        }
    }
    else{
      $task->mailnow=0;
    }

	// DEBUG
	// echo "<br />DEBUG :: lib_task.php :: 252 :: DATE DEBUT: ".$form->date_debut."\n";
	// echo "<br />DEBUG :: lib_task.php :: 252 :: DATE FIN: ".$form->date_fin."\n";
	list($date,$heure)=explode(' ',$form->date_debut);
	list($h,$i)=explode(':',$heure);
	if (!$h) $h=0;
	if (!$i) $i=0;
	list($d,$m,$y)=explode('/',$date);
	$task->date_debut=mktime($h,$i,0,$m,$d,$y);
	// echo "<br />DEBUG :: lib_task.php :: 27 :: $d,$m,$y $h,$i--- ".$task->date_debut."\n";

	list($date,$heure)=explode(' ',$form->date_fin);
	list($h,$i)=explode(':',$heure);
	if (!$h) $h=0;
	if (!$i) $i=0;
	list($d,$m,$y)=explode('/',$date);
	$task->date_fin=mktime($h,$i,0,$m,$d,$y);
	//echo "<br />DEBUG :: lib_task.php :: 342 :: $d,$m,$y $h,$i--- ".$task->date_fin."\n";	
		
	    // DEBUG
		// print_object($task);
	    // echo "<br />";
	
	$taskid= $DB->insert_record("referentiel_task", $task);
		
    // echo "task ID / $taskid<br />";
	if 	(isset($taskid) && ($taskid>0)
			&& 
			(	(isset($form->url_consigne) && !empty($form->url_consigne))
				|| 
				(isset($form->description_consigne) && !empty($form->description_consigne))
			)
	){
	/*
	DROP TABLE IF EXISTS `mdl_referentiel_consigne`;
CREATE TABLE IF NOT EXISTS `mdl_referentiel_consigne` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_consigne` varchar(20) NOT NULL DEFAULT '',
  `description_consigne` text NOT NULL,
  `url_consigne` varchar(255) NOT NULL DEFAULT '',
  `ref_task` bigint(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='consigne' AUTO_INCREMENT=1 ;
	*/
		$consigne = new object();
		$consigne->url_consigne=$form->url_consigne;
		$consigne->type_consigne=substr($form->type_consigne,0,20);
		$consigne->description_consigne=$form->description_consigne;
		$consigne->ref_task=$taskid;
		if (isset($form->cible_consigne)){
			$consigne->cible_consigne=$form->cible_consigne;
   		}
		else{
			$consigne->cible_consigne=1;
		}
		if (isset($form->etiquette_consigne)){
			$consigne->etiquette_consigne=$form->etiquette_consigne;
   		}
		else{
			$consigne->etiquette_consigne='';
		}
        // Modif JF 2013/02/02
		$consigne->timestamp=time();
	   	// DEBUG
		// print_object($consigne);
    	// echo "<br />";
		
		$consigne_id = $DB->insert_record("referentiel_consigne", $consigne);
    	// echo "consigne ID / $consigne_id<br />";
	}
    return $taskid;
}


// -----------------------
function referentiel_mask_task($id, $masque){
global $DB;
  if ($id){
    $record=$DB->get_record("referentiel_task", array("id" => "$id"));
    if ($record){
      if ($masque){
        $record->tache_masquee=1;    
      }
      else{
        $record->tache_masquee=0;
      }
      $record->type_task=($record->type_task);
      $record->description_task=($record->description_task);
      $record->criteres_evaluation=($record->criteres_evaluation);
      return $DB->update_record("referentiel_task", $record);
    }
  }
  return false; 
}

// -----------------------
function referentiel_update_task($form) {
// MAJ task + consigne;
// 19/01/2010 : la reference de l'auteur n'est pas actualisée.
global $DB;
global $USER;
$ok=true;
    // DEBUG
	// echo "<br />UPDATE task<br />\n";
	// print_object($form);
    // echo "<br />";
	
	if (isset($form->action) && ($form->action=="modifier_task")){
		// task
		$task = new object();
		$task->id=($form->taskid);
		$task->type_task=($form->type_task);
		$task->description_task=($form->description_task);
		$task->competences_task=reference_conversion_code_2_liste_competence('/', $form->code_item);
		$task->criteres_evaluation=($form->criteres_evaluation);
		$task->ref_instance=$form->instance;
		$task->ref_referentiel=$form->ref_referentiel;
		$task->ref_course=$form->courseid;
		if (empty($form->auteurid)){
            $task->auteurid=$USER->id;
        }
        else{  // MODIF JF 2012/10/26
            $task->auteurid=$form->auteurid;
        }
		$task->date_creation=$form->date_creation;
		$task->date_modif=time();
        $task->cle_souscription=($form->cle_souscription);
        $task->souscription_libre=$form->souscription_libre;
		if (isset($form->tache_masquee))
            $task->tache_masquee=$form->tache_masquee;
        else
            $task->tache_masquee=0;

        // MODIF JF 2010/02/11
        if (isset($form->mailnow)){
            $task->mailnow=$form->mailnow;
            if ($form->mailnow=='1'){ // renvoyer
                $task->mailed=0;   // annuler envoi precedent
            }
        }
        else{
            $task->mailnow=0;
        }

        /*
        $task->date_debut=mktime($form->debut_heure, $form->debut_mois, $form->debut_jour, $form->debut_annee);
        $task->date_fin=mktime($form->fin_heure, $form->fin_mois, $form->fin_jour, $form->fin_annee);
        */
        // DEBUG
        // echo "<br />DEBUG :: lib_task.php :: 252 :: DATE DEBUT: ".$form->date_debut."\n";
        // echo "<br />DEBUG :: lib_task.php :: 252 :: DATE FIN: ".$form->date_fin."\n";
        list($date,$heure)=explode(' ',$form->date_debut);
        list($h,$i)=explode(':',$heure);
        if (!$h) $h=0;
        if (!$i) $i=0;
        list($d,$m,$y)=explode('/',$date);
        $task->date_debut=mktime($h,$i,0,$m,$d,$y);
        // echo "<br />DEBUG :: lib_task.php :: 27 :: $d,$m,$y $h,$i--- ".$task->date_debut."\n";

        list($date,$heure)=explode(' ',$form->date_fin);
        list($h,$i)=explode(':',$heure);
        if (!$h) $h=0;
        if (!$i) $i=0;
        list($d,$m,$y)=explode('/',$date);
        $task->date_fin=mktime($h,$i,0,$m,$d,$y);
        //echo "<br />DEBUG :: lib_task.php :: 342 :: $d,$m,$y $h,$i--- ".$task->date_fin."\n";
		
	    // DEBUG
		// print_object($task);
	    // echo "<br />";
		$ok = $ok && $DB->update_record("referentiel_task", $task);
		// exit;
	    // echo "DEBUG :: lib_task.php :: 350 :: task ID : $task->id<br />";
	}
	else if (isset($form->action) && ($form->action=="modifier_consigne")){
		$consigne = new object();
		$consigne->id=$form->consigne_id;
		$consigne->url_consigne=($form->url_consigne);
		$consigne->type_consigne=substr($form->type_consigne,0,20);
		$consigne->description_consigne=($form->description_consigne);
		$consigne->ref_task=$form->ref_task;
		if (isset($form->cible_consigne)){
			$consigne->cible_consigne=$form->cible_consigne;
   		}
		else{
			$consigne->cible_consigne=1;
		}
		if (isset($form->etiquette_consigne)){
			$consigne->etiquette_consigne=$form->etiquette_consigne;
   		}
		else{
			$consigne->etiquette_consigne='';
		}
		// Modif JF 2013/02/02
		$consigne->timestamp=time();
   		// DEBUG
		// print_object($consigne);
    	// echo "<br />";
		$ok= $ok && $DB->update_record("referentiel_consigne", $consigne);
		// exit;
	}
	else if (isset($form->action) && ($form->action=="creer_consigne")){
		$consigne = new object();
		$consigne->url_consigne=($form->url_consigne);
		$consigne->type_consigne=substr($form->type_consigne,0,20);
		$consigne->description_consigne=($form->description_consigne);
		$consigne->ref_task=$form->ref_task;
		if (isset($form->cible_consigne)){
			$consigne->cible_consigne=$form->cible_consigne;
   		}
		else{
			$consigne->cible_consigne=1;
		}
		if (isset($form->etiquette_consigne)){
			$consigne->etiquette_consigne=$form->etiquette_consigne;
   		}
		else{
			$consigne->etiquette_consigne='';
		}
		// Modif JF 2013/02/02
		$consigne->timestamp=time();
   		// DEBUG
		// print_object($consigne);
    	// echo "<br />";
		$ok = $DB->insert_record("referentiel_consigne", $consigne);
        if ($ok){
            $task = $DB->get_record('referentiel_task', array('id' => $consigne->ref_task));
            if ($task){
                $ok=$DB->set_field('referentiel_task','date_modif',time(), array('id'=>$task->id));
            }
        }
    	// echo "consigne ID / $ok<br />";
		// exit;
	}
    return $ok;
}


// -----------------------
function referentiel_update_consigne($form) {
global $DB;
// MAJ consigne;
    // DEBUG
	// echo "<br />UPDATE ACTIVITY<br />\n";
	// print_object($form);
    // echo "<br />";
	if (!empty($form->consigne_id) && !empty($form->ref_task)){
		$consigne = new object();
		$consigne->id=$form->consigne_id;
		$consigne->url_consigne=($form->url_consigne);
		$consigne->type_consigne=substr($form->type_consigne,0,20);
		$consigne->description_consigne=($form->description_consigne);
		$consigne->ref_task=$form->ref_task;
		if (isset($form->cible_consigne)){
			$consigne->cible_consigne=$form->cible_consigne;
   		}
		else{
			$consigne->cible_consigne=1;
		}
		if (isset($form->etiquette_consigne)){
			$consigne->etiquette_consigne=$form->etiquette_consigne;
   		}
		else{
			$consigne->etiquette_consigne='';
		}
		// Modif JF 2013/02/02
		$consigne->timestamp=time();
   		// DEBUG
		// print_object($consigne);
    	// echo "<br />";
		if ($DB->update_record("referentiel_consigne", $consigne)){
            $task = $DB->get_record('referentiel_task', array('id' => $consigne->ref_task));
            if ($task){
                $ok=$DB->set_field('referentiel_task','date_modif',time(), array('id'=>$task->id));
            }
            return true;
        }
	}
	return false;
}

// -----------------------
function referentiel_add_consigne($form) {
// MAJ consigne;
global $DB;
	$id_consigne=0;
	if (!empty($form->ref_task)){
		$consigne = new object();
		$consigne->url_consigne=$form->url_consigne;
		$consigne->type_consigne=substr($form->type_consigne,0,20);
		$consigne->description_consigne=$form->description_consigne;
		$consigne->ref_task=$form->ref_task;
		if (isset($form->cible_consigne)){
			$consigne->cible_consigne=$form->cible_consigne;
   		}
		else{
			$consigne->cible_consigne=1;
		}
		if (isset($form->etiquette_consigne)){
			$consigne->etiquette_consigne=$form->etiquette_consigne;
   		}
		else{
			$consigne->etiquette_consigne='';
		}
		// Modif JF 2013/02/02
		$consigne->timestamp=time();
   		// DEBUG
		// print_object($consigne);
    	// echo "<br />";
		$id_consigne = $DB->insert_record("referentiel_consigne", $consigne);
    	// echo "consigne ID / $ok<br />";
		// exit;
        if ($id_consigne){
            $task = $DB->get_record('referentiel_task', array('id' => $consigne->ref_task));
            if ($task){
                $ok=$DB->set_field('referentiel_task','date_modif',time(), array('id'=>$task->id));
            }
        }
	}
    return $id_consigne;
}

// -----------------------
function referentiel_delete_all_associations_users_to_one_task($ref_task) {
// supprime toutes les associations pour une tache donnee
global $DB;
	$ok_association=true;
	if (!empty($ref_task)){
		$a_records = $DB->get_records("referentiel_a_user_task", array("ref_task" => "$ref_task"));
		if ($a_records){
			foreach ($a_records as $a_record){
				// suppression
				$ok_association =$ok_association && referentiel_delete_a_user_task_record($a_record->id);
			}
			$a_records->close();
		}
	}
    return $ok_association;
}

// -----------------------
function referentiel_delete_all_associations_tasks_to_one_user($ref_user) {
// supprime toutes les associations pour un user donne
/*
			DROP TABLE IF EXISTS `mdl_referentiel_a_user_task`;
CREATE TABLE IF NOT EXISTS `mdl_referentiel_a_user_task` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref_user` bigint(10) unsigned NOT NULL,
  `ref_task` bigint(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='user_select_task' AUTO_INCREMENT=1 ;
*/
global $DB;
	$ok_association=true;
	if (!empty($ref_user)){
		$a_records = $DB->get_records("referentiel_a_user_task", "ref_user", $ref_user);
		if ($a_records){
			foreach ($a_records as $a_record){
				// suppression
				$ok_association =$ok_association && referentiel_delete_a_user_task_record($a_record->id);
			}
			$a_records->close();
		}
	}
    return $ok_association;
}

// -----------------------
function referentiel_get_all_tasks_user($ref_user, $course_id, $referentiel_instance_id) {
// retourne un tableau d'objets taches pour un user donne
/*
			DROP TABLE IF EXISTS `mdl_referentiel_a_user_task`;
CREATE TABLE IF NOT EXISTS `mdl_referentiel_a_user_task` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref_user` bigint(10) unsigned NOT NULL,
  `ref_task` bigint(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='user_select_task' AUTO_INCREMENT=1 ;
*/
global $DB;
	$t_records=array();
	if (!empty($referentiel_instance_id) && !empty($course_id)){
		if (!empty($ref_user)){
			$a_records = $DB->get_records("referentiel_a_user_task", array("ref_user" => "$ref_user"));
			if ($a_records){
				foreach ($a_records as $a_record){
					$t_records[] = $DB->get_record("referentiel_task", array("id" => "$a_record->ref_task") );
 				}
 				$a_records->close();
			}
		}
	}
    return $t_records;
}

// -----------------------
function referentiel_user_tache_souscrite($ref_user, $ref_task) {
// retourne vrai si cet utilisateur a souscrit à cette tache
global $DB;
		if (!empty($ref_user) && !empty($ref_task)){
			$a_record = $DB->get_record("referentiel_a_user_task", array("ref_user" => "$ref_user", "ref_task" => "$ref_task"));
			if ($a_record){
				return true;
			}
		}
    return false;
}

// -----------------------
function referentiel_get_all_tasks($course_id, $referentiel_instance_id){
global $DB;
	$t_records=array();
	if (!empty($referentiel_instance_id) && !empty($course_id)){
		$t_records = $DB->get_records_sql("SELECT * FROM {referentiel_task}
 WHERE ref_instance=:refid  AND ref_course=:courseid ORDER BY date_debut DESC, date_fin DESC, auteurid ASC ", array("courseid" => "$course_id", "refid" => "$referentiel_instance_id"));
	}
    return $t_records;
}

// -----------------------
function referentiel_get_task($taskid, $auteur_id=0){
global $DB;
    if (!empty($taskid)){
		if ($auteur_id>0){
			$t_record = $DB->get_record("referentiel_task", array("id" => "$taskid", "auteurid" => "$auteur_id"));
		}
		else {
			$t_record = $DB->get_record("referentiel_task" , array("id" => "$taskid"));
		}
	}
    return $t_record;
}

// -----------------------
function referentiel_closed_task($taskid){
global $DB;
    if (!empty($taskid)){
		if ($t_record = $DB->get_record_sql("SELECT date_fin FROM {referentiel_task} WHERE id = :id ", array("id" => "$taskid"))){
			return ($t_record->date_fin<time());
		}
	}
    return false;
}



/**
 * This function returns record document from table referentiel_document
 *
 * @param id ref_task
 * @return objects
 * @todo Finish documenting this function
 **/
 // -----------------------
function referentiel_get_consignes($taskid){
global $DB;
	if (!empty($taskid)){
		return $DB->get_records_sql("SELECT * FROM {referentiel_consigne} WHERE ref_task = :reftask ORDER BY id ASC ", array("reftask" => "$taskid"));
	}
	else 
		return 0; 
}

// -----------------------
function referentiel_association_user_task($ref_user, $ref_task, $referent_id=0, $mailnow=0, $force=false) {
// associe une tache à un utilisateur
//  cree l'activite a partir de l'association
global $DB;
global $CFG;
global $USER;

	$activite_id=0;
	if ($ref_task && $ref_user){
		// verifier si association existe
		$params1=array("ref_task" => "$ref_task", "ref_user" => "$ref_user");
		$sql1="SELECT * FROM {referentiel_a_user_task} WHERE ref_user=:ref_user AND ref_task=:ref_task ";
		$record_association= $DB->get_record_sql($sql1, $params1);
 		
		if (!$record_association){
			// Recuperer les info de la tache

			$t_record = $DB->get_record("referentiel_task", array("id" => "$ref_task"));
			if ($t_record){
				// Creer l'activite
				$activite = new object();
				$activite->type_activite='['.get_string('task','referentiel').' '.$ref_task.'] '.($t_record->type_task);
				$activite->competences_activite=($t_record->competences_task);
				$activite->description_activite='['.get_string('consigne_task','referentiel').' (<i>'.referentiel_get_user_info($t_record->auteurid).'</i>) : '.($t_record->description_task).']';
				$activite->commentaire_activite=($t_record->criteres_evaluation);
				$activite->ref_instance=$t_record->ref_instance;
				$activite->ref_referentiel=$t_record->ref_referentiel;
				$activite->ref_course=$t_record->ref_course;
                $ladate=time();
                $activite->date_creation=$ladate;
                if ($force){    // souscription realisee par le referent
                    $activite->date_creation=$ladate;
                    $activite->date_modif_student=0;     // notification envoyee à l'etudiant
				    $activite->date_modif=$ladate;      // prioritaire pour l'etudiant
                }
                else{
                    $activite->date_creation=$ladate;
                    $activite->date_modif_student=$ladate;
				    $activite->date_modif=$ladate+100;  // notification envoyee à l'enseignant mais pas d'affichage prioritaire
                }

				$activite->approved=0;
				$activite->userid=$ref_user;
				if (empty($referent_id)){
                    $activite->teacherid=$t_record->auteurid;
				}
                else{
                    $activite->teacherid=$referent_id;
                }
                $activite->ref_task=$ref_task;
				
                $activite->mailed=1;  //pour empêcher une notification intempestive
                if (isset($mailnow)){
                    $activite->mailnow=$mailnow;
                    if ($mailnow==1){
                        $activite->mailed=0;   // forcer l'envoi
                    }
                }
                else{
                    $activite->mailnow=0;
                }

				$activite_id= $DB->insert_record("referentiel_activite", $activite);
                //echo "<br />DEBUG :: lib_atsk.php :: 769 :: ACTIVITE $activite_id\n";

				if ($activite_id){
					referentiel_regenere_certificat_user($activite->userid, $activite->ref_referentiel);
					$record_association = new object();
					$record_association->ref_user=$ref_user;
					$record_association->ref_task=$ref_task;
					$record_association->ref_activite=$activite_id;
					$record_association->date_selection=time();
					$id_a = $DB->insert_record("referentiel_a_user_task", $record_association);
                    //echo "<br />DEBUG :: lib_task.php :: 777 :: AVANT de BAREME\n";

					// Traiter le bareme
					if ($CFG->referentiel_use_scale){
						require_once('lib_bareme.php');

						if ($rec_assoc=referentiel_get_assoc_bareme_occurrence($activite->ref_referentiel)){
							// DEBUG
							//echo "<br />ASSOC BAREME<br />\n";
							//print_object($rec_assoc);
							//echo "<br />\n";
                           							// DEBUG

							if ($bareme=referentiel_get_bareme($rec_assoc->refscaleid)){
                                $liste_evaluations='';
								//echo "<br />BAREME<br />\n";
								//print_object($bareme);
								//echo "<br />\n";
                                $tcompetences=explode("/",$activite->competences_activite);
                                if ($bareme->threshold>0) $value=$bareme->threshold-1; else $value=0;
								foreach ($tcompetences as $comp){
                                    if ($comp){
										$liste_evaluations.=$comp.':'.  $value.'/';
									}
								}
								//echo "<br />DEBUG :: lib_task.php :: 804 :: LISTE $liste_evaluations<br />\n";

                                $id_eval=referentiel_enregistrer_evaluation_activite($liste_evaluations, $activite_id, $bareme->id);
                                //echo "<br />INSERTION $id_eval<br />\n";
							}
						}
					}
				}
			}
		}
	}

    return $activite_id;
}

// -----------------------
function referentiel_validation_activite_task($ref_task, $select='') {
// Effectue la validation des activités souscrites a la tache
global $DB;
global $USER;
	// DEBUG
	// echo '<br />DEBUG :: lib_task.php :: 669 :: Tache : '.$ref_task."<br />Selection :".$select."\n";
    if ($ref_task>0){
        $info_valideur=referentiel_get_user_info($USER->id);
		// verifier si association existe
		$params1=array("ref_task" => "$ref_task");
		$sql1="SELECT * FROM {referentiel_a_user_task}  WHERE ref_task=:ref_task ";
		if (!empty($select)){
          $sql1.=' '.$select.' ';
        }
		// echo '<br />DEBUG :: lib_task.php :: 677 :: SQL: '.$sql1."\n";
		$records_association= $DB->get_records_sql($sql1, $params1);
 		
		if ($records_association){
            foreach ($records_association as $record_association){
 				if ($record_association){
					$ref_user=$record_association->ref_user;
					$ref_activite=$record_association->ref_activite;
   					// DEBUG
					//print_object($record_association);
    				//echo "<br />";
					// Approuver l'activite
					// recuperer l'info sur l'activite
    			    if ($approverecord = $DB->get_record('referentiel_activite', array("id" => "$ref_activite"))) {
						$approverecord->approved = 1;
						$approverecord->teacherid=$USER->id;
						$approverecord->date_modif=time();
						$approverecord->type_activite=($approverecord->type_activite);
						$approverecord->description_activite=($approverecord->description_activite);
						$approverecord->commentaire_activite=($approverecord->commentaire_activite."\n".get_string('approved_task_by','referentiel')." ".$info_valideur." (".date("d/m/Y H:i").")\n");
						// DEBUG
						// print_r($approverecord);
						// echo "<br />\n";
						
                        if ($DB->update_record('referentiel_activite', $approverecord)) {
							// regeneration du certificat associe a cette personne pour ce referentiel 
// Modif JF 2012/10/07
// referentiel_certificat_user_invalider($approverecord->userid, $approverecord->ref_referentiel);
							referentiel_regenere_certificat_user($approverecord->userid, $approverecord->ref_referentiel);
                        }
                    }
                }
            }
        }
    }
}

// -----------------------
function referentiel_get_activites_task($ref_task) {
// Retourne la liste des activités liées à une tache
global $DB;
	// DEBUG
	// echo '<br />DEBUG :: lib_task.php :: 685 :: Tache : '.$ref_task."\n";
	if (!empty($ref_task)){
		// verifier si association existe
		return $DB->get_records("referentiel_activite", array("ref_task"=>"$ref_task"));
 	}
	return false;
}


// -----------------------
function referentiel_get_liste_codes_competence_tache($ref_task) {
global $DB;
	// DEBUG
	// echo '<br />DEBUG :: lib_task.php :: 652 :: Tache : '.$ref_task."\n";
	if ($ref_task>0){
		// verifier si association existe
		$params=array("ref_task"=>"$ref_task");
		$sql="SELECT competences_task FROM {referentiel_task}  WHERE id=:ref_task ";
		// echo '<br />DEBUG :: lib_task.php :: 656 :: SQL: '.$sql."\n";
		$rtask=$DB->get_record_sql($sql, $params);
		if ($rtask){
			return $rtask->competences_task;
		}
 	}
	return '';
}

?>
<?php

// $Id:  lib.php,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
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
 * repartition de l'accomagnement par item d'activite
 * @author jfruitet
 * @version $Id: lib.php,v 1.0 2011/11/26 00:00:00 jfruitet Exp $
 * @package referentiel
 **/

// inclus dans lib.php


/**
 * This function returns records of repartition from table referentiel_repartition
 *
 * @param id reference instance
 * @return objects
 * @todo Finish documenting this function
 **/

// -----------------------
/**
 * This function returns records of teachers from table referentiel_repartition
 *
 * @param id reference instance
 * @param id course
 * @param activite list of code item of competences
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_repartition_competences($instanceid, $courseid, $liste_competences, $teachersids){
global $CFG;
    if (!empty($instanceid) && !empty($courseid) && !empty($liste_competences)){
        $t_codes_competence=explode('/',referentiel_purge_dernier_separateur($liste_competences, '/'));
        foreach($t_codes_competence as $code_item){
            // rechercher les referents associes a cette competence
            // DEBUG
            // mtrace("lib_repartitions.php :: referentiel_get_repartition_competences() :: 83\nCODE :$code_item\n");
            $t_userids=referentiel_get_repartitions_teacher_by_competence($instanceid, $courseid, $code_item);


            if ($t_userids){
                // DEBUG
                // mtrace("88 :: COMPARER TEACHERSIDS \n");
                // print_r($teachersids);
                // mtrace("\nAVEC T_USERIDS\n");
                // print_r($t_userids);
                foreach($t_userids as $teacher){
                    if (in_array($teacher, $teachersids)==false) {
                        // mtrace("\nAJOUTER $teacher->userid");
						$a = new Object();
						$a->userid=$teacher->userid;
                        $teachersids[]=$a;
                    }
                }
            }
        }
    }
    // mtrace("\n101 :: RENVOYER \n");
    // print_r($teachersids);
    return $teachersids;
}

// -----------------------
/**
 * This function returns records of teachers which are in the intersection
 * of referentiel_repartition and referentiel_accompagnement
 * If one array is empty return other one
 *
 * @param object teachers_repartition
 * @param object teachers_accompagnement
 * @return objects
 * @todo Finish documenting this function
 **/

function referentiel_intersection_teachers($teachers_repartition, $teachers_accompagnement){
    //
    $teachersids=array();
    //mtrace("DEBUG :: lib_repartitions.php :: referentiel_intersection_teachers :: 118\n");
    //mtrace("118 :: TEACHERS REPARTITION\n");
    //print_r($teachers_repartition);
    //mtrace("120 :: TEACHERS ACCOMPAGNEMENT\n");
    //print_r($teachers_accompagnement);
    /*
    echo("DEBUG :: lib_repartitions.php :: referentiel_intersection_teachers :: 107\n");
    echo("118 :: TEACHERS REPARTITION<br />\n");
    print_r($teachers_repartition);
    echo("<br />120 :: TEACHERS ACCOMPAGNEMENT<br />\n");
    print_r($teachers_accompagnement);
    echo "<br />\n";
    */
    if (empty($teachers_accompagnement)){
        $teachersids=$teachers_repartition;
    }
    else if (empty($teachers_repartition)){
        $teachersids=$teachers_accompagnement;
    }
    else{
        foreach($teachers_accompagnement as $teacher){
            if (in_array($teacher, $teachers_repartition)==true) {
                // mtrace("\nRETOURNER $teacher->userid");
				$a = new Object();
				$a->userid=$teacher->userid;
                $teachersids[$teacher->userid]->userid=$a;
            }
        }
        if (empty($teachersids)){
            // si aucune intersection retourner les accompagnateurs
            $teachersids=$teachers_accompagnement;
        }
    }
    //mtrace("137 :: TEACHERS SELECTIONNES\n");
    //print_r($teachersids);
    //mtrace("EXIT\n");
    //exit;
    /*
    echo ("lib_repartition :: 129  :: TEACHERS SELECTIONNES<br />\n");
    print_object($teachersids);
    echo "<br />\n";
    // exit;
    */
    return $teachersids;
}




/**
 * This function returns records of repartition from table referentiel_repartition
 *
 * @param id reference instance
 * @return objects
 * @todo Finish documenting this function
 **/
// -----------------------
function referentiel_get_repartitions($instance_id){
global $DB;
	if (isset($instance_id) && ($instance_id>0)){
		return $DB->get_records_sql("SELECT * FROM {referentiel_repartition}
WHERE ref_instance=:instance", array("instance" => $referentiel_id));
	}
	else
		return NULL;
}

/**
 * Given an repartition id,
 * this function will permanently delete the repartition instance
 *
 * @param object $id
 * @return boolean Success/Failure
 **/

// -----------------------
function referentiel_delete_repartition_record($id) {
// suppression de l'repartition associe
global $DB;
    $ok=false;
    if (isset($id) && ($id>0)){
		$ok = $DB->delete_records("referentiel_repartition", array("id" => $id));
	}
    return $ok;
}

// -----------------------
function referentiel_get_repartitions_competence_by_teacher($instanceid, $courseid, $teacherid) {
// retourne la liste des id des accompagnateurs
//
global $DB;
	if (!empty($instanceid) && !empty($courseid) && !empty($teacherid)){
        return ($DB->get_records_sql("SELECT code_item FROM {referentiel_repartition}
 WHERE ref_instance=:instanceid AND courseid=:courseid AND teacherid=:teacherid
  ORDER BY code_item ASC ", array("instanceid" => $instanceid, "courseid" => $courseid, "teacherid" => $teacherid)));
  }
  return false;
}

// -----------------------
function referentiel_get_repartitions_teacher_by_competence($instanceid, $courseid, $code_item) {
// retourne la liste des id des accompagnateurs
//
global $DB;
	if (!empty($instanceid) && !empty($courseid) && !empty($code_item)){
        $sql = "SELECT teacherid as userid FROM {referentiel_repartition}
 WHERE ref_instance=:instanceid
 AND courseid=:courseid AND code_item=:code_item
 ORDER BY teacherid ASC ";
        // mtrace("SQL > $sql\n");
        $records_teachers=$DB->get_records_sql($sql, array("instanceid" => $instanceid, "courseid" => $courseid, "code_item" => $code_item));
        return $records_teachers;
        
  }
  return false;
}

// -----------------------
function referentiel_reset_repartition($instanceid){
// supprime les lignes de la table pour cette instance
global $DB;
    $DB->delete_records("referentiel_repartition", array("ref_instance" => $instanceid));
}

// -----------------------
function referentiel_delete_association_competence_teacher($referentiel_instance_id, $referentiel_occurrence_id, $course_id, $teacherid, $code_item){
global $DB;
    $record_r=$DB->get_record_sql("SELECT id FROM {referentiel_repartition}
 WHERE ref_instance=:instanceid
 AND courseid=:courseid
 AND code_item=:code_item
 AND teacherid=:teacherid",
 array("instanceid" => $referentiel_instance_id, "courseid" => $course_id, "code_item" => $code_item, "teacherid" => $teacherid));
    
    if ($record_r){
      $ok=true;
      foreach ($record_r as $id){
        if ($id){         
          $ok= $ok && $DB->delete_records("referentiel_repartition", array("id" => $id));
        }
      }
      return $ok;
    }
    return false;
}


// -----------------------
function referentiel_set_association_competence_teacher($referentiel_instance_id, $referentiel_occurrence_id, $course_id, $teacherid, $code_item){
global $DB;
    $repartition_id=0;
    $record_r=$DB->get_record_sql("SELECT * FROM {referentiel_repartition}
 WHERE ref_instance=:instanceid
 AND courseid=:courseid
 AND code_item=:code_item
 AND teacherid=:teacherid",
 array("instanceid" => $referentiel_instance_id, "courseid" => $course_id, "code_item" => $code_item, "teacherid" => $teacherid));

    if ($record_r){
        $repartition_id=$record_r->id;
		$DB->update_record("referentiel_repartition", $record_r);
    }
	else{
  	 $repartition = new object();
	 $repartition->ref_instance=$referentiel_instance_id;
     $repartition->ref_occurrence=$referentiel_occurrence_id;
	 $repartition->courseid=$course_id;
     $repartition->code_item=$code_item;
     $repartition->teacherid=$teacherid;		
	 $repartition_id= $DB->insert_record("referentiel_repartition", $repartition);
    }
    return $repartition_id;
}
    


// -----------------------
function referentiel_get_all_repartitions($course_id, $referentiel_instance_id){
global $DB;
	$t_records=array();
	if (!empty($referentiel_instance_id) && !empty($course_id)){
		$t_records = $DB->get_records_sql("SELECT * FROM {referentiel_repartition}
 WHERE ref_instance=:instanceid AND courseid=:course_id
 ORDER BY code_item ASC, teacherid ASC ",
        array("instanceid" => $referentiel_instance_id, "courseid" => $course_id));
  }
  return $t_records;
}

// -----------------------
function referentiel_get_repartitions_teacher($referentiel_instance_id, $course_id, $teacherid) {
// retourne la liste des competences reparties par utilisateur ordonnee selon le nom de l'enseignant
// 
global $DB;
	if (!empty($referentiel_instance_id) && !empty($course_id) && !empty($ref_teacher)){
    return ($DB->get_records_sql("SELECT r.code_item, t.lastname, t.firstname  FROM {referentiel_repartition} as r, user as t
 WHERE r.teacherid=t.id
 AND r.ref_instance=:instanceid AND r.courseid=:courseid  AND r.teacherid=:teacherid
 ORDER BY t.lastname ASC, t.firstname ASC ",
        array("instanceid" => $referentiel_instance_id, "courseid" => $course_id, "teacherid" => $teacherid) ));
  }
  return false;
}

// -----------------------
function referentiel_has_competences($referentiel_instance_id, $course_id, $ref_teacher) {
// retourne le nombre d'etudiants accompagnés par $ref_techer
// 
global $DB;
	if (!empty($referentiel_instance_id) && !empty($course_id) && !empty($ref_teacher)){
    return $DB->count_records( 'referentiel_repartition', array('ref_instance' => $referentiel_instance_id, 'courseid' => $course_id, 'teacherid' => $ref_teacher));
  }
  return false;
}

/**
 * Given an activite record this function returns a list of teachers
 * that may be sent a notification.
 *
 * @param activite object
 * @return string
 * @todo Finish documenting this function
 **/
function referentiel_get_referents_notification($activite){
// retourne la liste des enseignants et tuteurs qui seront notifies
// Créé par JF pour la Version 6.xx
// Decalque du traitement des referents destinataire dans referentiel_cron_activite()

    $destinataires = new stdClass();
    $destinataires->liste_destinataires='';
    $destinataires->nbdestinataires=0;

    $teachers = array();     // liste des enseignants associes à cette activite
    if (!empty($activite->ref_instance) && !empty($activite->ref_course)){
        if (!empty($activite->teacherid)){    // referent de l'activite
            $teachers[]=referentiel_get_user($activite->teacherid);
        }
        else{  // on recherche les accompagnateurs
            $teachers_repartition=array();
            $teachers_repartition=referentiel_get_repartition_competences($activite->ref_instance, $activite->ref_course, $activite->competences_activite, $teachers_repartition);
            $teachers=referentiel_get_accompagnements_user($activite->ref_instance, $activite->ref_course, $activite->userid);
            // verifier si intersection
            $teachers=referentiel_intersection_teachers($teachers_repartition, $teachers);
        }
        if (empty($teachers)){
            // notifier tous les enseignants sauf les administrateurs et createurs de cours
            $teachers=referentiel_get_teachers_course($activite->ref_course);
        }
        if ($teachers){
            // DEBUG
            //mtrace("\nlib.php :: referentiel_get_destinataires_notification :: 147 :: TEACHERS\n");
            //print_r($teachers);
            //mtrace("\n");

            foreach ($teachers as $teacher) {
                // DEBUG
                //mtrace("\nlib.php :: 154 :: TEACHER\n");
                //print_object($teacher);
                //mtrace("\n");
                if (!empty($teacher->userid)){
                    if ($user=referentiel_get_user($teacher->userid)){
/*
u.id, u.username, u.firstname, u.lastname, u.maildisplay, u.mailformat, u.maildigest, u.emailstop, u.imagealt,
u.email, u.city, u.country, u.lastaccess, u.lastlogin, u.picture, u.timezone, u.theme, u.lang, u.trackforums, u.mnethostid
*/                      if (!$user->emailstop){
                            $email='';
                            if ($user->maildisplay){
                                $email=' '.$user->email;
                            }
                            $destinataires->nbdestinataires++;
                            $destinataires->liste_destinataires.=' '.mb_strtoupper($user->lastname,'UTF-8').' '.mb_convert_case($user->firstname, MB_CASE_TITLE, 'UTF-8').$email.' ::';
                        }
                    }
                }
            }
        }
    }
    if ($destinataires->nbdestinataires){
        $destinataires->liste_destinataires='<b>'.$activite->type_activite.'</b><br /> '.$destinataires->liste_destinataires;
    }
    return $destinataires;
}


?>
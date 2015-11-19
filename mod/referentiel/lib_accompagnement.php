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
 * @author jfruitet
 * @version $Id: lib.php,v 1.4 2006/08/28 16:41:20 mark-nielsen Exp $
 * @version $Id: lib.php,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
 * @package referentiel
 **/

// inclus dans lib.php



 $REFERENTIEL_ACCOMPAGEMENT="REF";


/**
 * Given an task id,
 * this function will permanently delete the task instance
 * and any consigne that depends on it.
 *
 * @param object $id
 * @return boolean Success/Failure
 **/
// -----------------------
function referentiel_reset_accompagnement($instanceid){
// reset de la table accompagnement
global $DB;
    $DB->delete_records("referentiel_accompagnement", array("ref_instance" => $instanceid));
}


// -----------------------
function referentiel_get_accompagnements_user($referentiel_instance_id, $course_id, $ref_user) {
// retourne la liste des id des accompagnateurs
//
global $DB;
	if (!empty($referentiel_instance_id) && !empty($course_id) && !empty($ref_user)){
        $params= array("refid" => "$referentiel_instance_id", "courseid" => "$course_id" , "userid" => "$ref_user");
        $sql="SELECT teacherid as userid FROM {referentiel_accompagnement}
 WHERE ref_instance=:refid AND courseid=:courseid AND userid=:userid ORDER BY teacherid ASC ";
        return $DB->get_records_sql($sql, $params);
    }
    return NULL;
}

// -----------------------
function referentiel_delete_association_user_teacher($referentiel_instance_id, $course_id, $userid, $teacherid){  
global $DB;
    $params= array("refid" => "$referentiel_instance_id", "courseid" => "$course_id", "userid" => "$userid" , "teacherid" => "$teacherid");
    $sql="SELECT id FROM {referentiel_accompagnement}
 WHERE ref_instance=:refid AND courseid=:courseid AND userid=:userid AND teacherid=:teacherid ";
    $records_a= $DB->get_record_sql($sql, $params);
    if ($records_a){
        $ok=true;
        foreach ($records_a as $id){
            if ($id){
                $ok= $ok && $DB->delete_records("referentiel_accompagnement", array("id" => $id));
            }
        }
        return $ok;
    }
    return false;
}


// -----------------------
function referentiel_set_association_user_teacher($referentiel_instance_id, $course_id, $userid, $teacherid, $type='REF'){
global $DB;
    $accompagnement_id=0;
    $params= array("refid" => "$referentiel_instance_id", "courseid" => "$course_id", "userid" => "$userid" , "teacherid" => "$teacherid");
    $sql="SELECT * FROM {referentiel_accompagnement}
 WHERE ref_instance=:refid AND courseid=:courseid AND userid=:userid AND teacherid=:teacherid ";
    $records_a= $DB->get_record_sql($sql, $params);
    if ($records_a){
        $accompagnement_id=$records_a->id;
        $records_a->accompagnement=$type;
		$DB->update_record("referentiel_accompagnement", $records_a);
    }
	else{
        $accompagnement = new object();
        $accompagnement->accompagnement=$type;
        $accompagnement->ref_instance=$referentiel_instance_id;
        $accompagnement->courseid=$course_id;
        $accompagnement->userid=$userid;
        $accompagnement->teacherid=$teacherid;
        $accompagnement_id= $DB->insert_record("referentiel_accompagnement", $accompagnement);
    }
    return $accompagnement_id;
}
    
// -----------------------
function referentiel_get_all_accompagnements($course_id, $referentiel_instance_id){
global $DB;
	if (!empty($referentiel_instance_id) && !empty($course_id)){
        $params= array("refid" => "$referentiel_instance_id", "courseid" => "$course_id");
        list($sort, $sortparams) = users_order_by_sql('u');
        $params += $sortparams;
        $sql="SELECT ra.* FROM {referentiel_accompagnement} AS ra, {user} AS u
 WHERE ra.ref_instance=:refid
 	AND ra.courseid=:courseid AND u.id=ra.userid
 ORDER BY $sort, ra.teacherid ASC";
		return $DB->get_records_sql($sql, $params);
    }
    return NULL;
}

// -----------------------
function referentiel_get_accompagnements_teacher($referentiel_instance_id, $course_id, $ref_teacher) {
// retourne la liste des id des accompagnes
// 
global $DB;
	if (!empty($referentiel_instance_id) && !empty($course_id) && !empty($ref_teacher)){
        $params= array("refid" => "$referentiel_instance_id", "courseid" => "$course_id", "teacherid" => "$ref_teacher");
        list($sort, $sortparams) = users_order_by_sql('u');
        $params += $sortparams;
        $sql="SELECT ra.userid FROM {referentiel_accompagnement} AS ra, {user} AS u
 WHERE ra.ref_instance=:refid
 	AND ra.courseid=:courseid AND u.id=ra.userid AND ra.teacherid=:teacherid
 ORDER BY $sort ";
        return $DB->get_records_sql($sql, $params);
  }
  return NULL;
}

// -----------------------
function referentiel_has_pupils($referentiel_instance_id, $course_id, $ref_teacher) {
// retourne le nombre d'etudiants accompagnés par $ref_techer
global $DB;
	if (!empty($referentiel_instance_id) && !empty($course_id) && !empty($ref_teacher)){
        $params= array('ref_instance'=>$referentiel_instance_id,'courseid' => $course_id,'teacherid' => $ref_teacher);
        return $DB->count_records( 'referentiel_accompagnement', $params);
  }
  return 0;
}


?>
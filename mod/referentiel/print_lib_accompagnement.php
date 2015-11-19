<?php  // $Id:  print_lib_task.php,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
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


require_once('locallib.php');


// -----------------------
function referentiel_print_accompagnements_user_by_teachers($referentiel_instance_id, $course_id, $ref_user, $t_teacherids, $indexdeb, $indexfin, $colwidth) {
// affiche une liste de cases cochées 
// 
global $DB;
$s='';
	if (!empty($referentiel_instance_id) && !empty($course_id) && !empty($ref_user)){
        if ($t_teacherids){
            for ($i=$indexdeb; $i<$indexfin; $i++){
                if (!empty($t_teacherids[$i])){
                    $params=array("refid" => "$referentiel_instance_id", "courseid" => "$course_id", "userid" => "$ref_user", "teacherid" =>"$t_teacherids[$i]");
                    $sql="SELECT accompagnement, userid, teacherid FROM {referentiel_accompagnement}
 WHERE ref_instance=:refid  AND courseid=:courseid AND userid=:userid AND teacherid=:teacherid
 ORDER BY userid ASC, teacherid ASC ";
                    $records_acc = $DB->get_records_sql($sql, $params);
                    $s.='<td width="'.$colwidth.'">';
                    if ($records_acc){
                        foreach($records_acc as $record){
                            $s.=get_string($record->accompagnement,'referentiel');
                        }
                    }
                    else{
                        $s.='&nbsp;';
                    }
                    $s.='</td>'."\n";
                }
            }
        }
    }
    return $s;
}


// -----------------------
function referentiel_select_accompagnements_user_by_teachers($referentiel_instance_id, $course_id, $ref_user, $t_teacherids, $indexdeb, $indexfin, $colwidth) {
// affiche une liste de cases à cocher
// 
global $DB;
$s='';
// DEBUG
// echo "LIGNE 65 DEB:$indexdeb, FIN:$indexfin <br />\n";
//
// exit;
	if (!empty($referentiel_instance_id) && !empty($course_id) && !empty($ref_user)){
        if ($t_teacherids){
            //foreach ($t_teacherids as $tid){
            for ($i=$indexdeb; $i<$indexfin; $i++){
                if (!empty($t_teacherids[$i])){
                    $params=array("refid" => "$referentiel_instance_id", "courseid" => "$course_id", "userid" => "$ref_user", "teacherid" =>"$t_teacherids[$i]");
                    $sql="SELECT accompagnement, teacherid, userid FROM {referentiel_accompagnement}
 WHERE ref_instance=:refid  AND courseid=:courseid AND userid=:userid AND teacherid=:teacherid
 ORDER BY userid ASC, teacherid ASC ";
                    $records_acc = $DB->get_records_sql($sql, $params);
                    $s.="<td width='".$colwidth."'>";
                    if ($records_acc){
                        foreach($records_acc as $record){
                            if ($record->accompagnement=='REF'){
                                $s.='<input type="checkbox" name="t_teachers['.$t_teacherids[$i].'][]" id="t_teachers_'.$t_teacherids[$i].'" value="'.$ref_user.'" checked="checked" /> '."\n";
                            }
                            else{
                                $s.=get_string('ACC','referentiel')."\n";
                            }
                        }
                    }
                    else{
                        $s.='<input type="checkbox" name="t_teachers['.$t_teacherids[$i].'][]" id="t_teachers_'.$t_teacherids[$i].'" value="'.$ref_user.'" /> '."\n";
                    }
                    $s.='</td>'."\n";
                }
            }
        }
    }
    return $s;
}



// Affiche une entete accompagnement
// *****************************************************************
// *
// output string                                                     *
// *****************************************************************

function referentiel_print_entete_accompagnement(){
// Affiche une entete accompagnement
$s="";
  $s.='<div align="center">';
	$s.='<table class="activite">'."\n";
	$s.='<tr>';
	$s.='<th width="10%"><b>'.get_string('id','referentiel').'</b></th>';
	$s.='<th width="20%"><b>'.get_string('student').'</b></th>';
	$s.='<th width="20%"><b>'.get_string('teacher').'</b></th>';
  $s.='<th width="20%"><b>'.get_string('type','referentiel').'</b></th>';
  $s.='<th width="30%">&nbsp;</th>';
	$s.='</tr>'."\n";
	return $s;
}

function referentiel_print_enqueue_accompagnement(){
// Affiche une entete accompagnement
	$s='</table>'."\n";
	$s.='</div><br />';
	return $s;
}


// Affiche une ligne de la table quand il n'y a pas d'accompagnement pour userid 
// *****************************************************************
// input @param a user id                                          *
// output string                                                     *
// *****************************************************************

function referentiel_print_aucun_accompagnement_user($userid){
	$s="";
	if ($userid){
		$user_info=referentiel_get_user_info($userid);
	}
	else{
		$user_info="&nbsp;";
	}
	
	$s.='<tr><td class="zero">&nbsp;</td><td class="zero">';
	$s.=$user_info;
	$s.='</td><td class="invalide" colspan="2">';
  $s.='<span class="small">'.get_string('notmatched','referentiel').'</span>';
	$s.='</td><td class="zero">&nbsp;</td></tr>'."\n";
	
	return $s;
}



//------------------------------
function referentiel_print_liste_accompagnements($referentiel_instance, $userid_filtre=0, $gusers=NULL, $select_acc=0){
// Affiche les accompagnements de ce referentiel
global $DB;
global $CFG;
  if (!empty($referentiel_instance)){
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record("course", array("id" => "$cm->course"));
    if (empty($cm) or empty($course)){
        	print_error('REFERENTIEL_ERROR :: print_lib_accompagnement.php :: You cannot call this script in that way');
    }
    // echo "<br />DEBUG :: 220 ::<br />REFERENTIEL_Instance : $referentiel_instance->id <br /> Course_id : $referentiel_instance->course\n"; 
    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

  			 
    $record_id_users  = referentiel_get_students_course($course->id,0,0);  //seulement les stagiaires
    if ($gusers && $record_id_users){ // liste des utilisateurs du groupe courant
		  $record_users  = array_intersect($gusers, array_keys($record_id_users));
			$record_id_users=array(); // remettre à zero
			foreach ($record_users  as $record_id){
                        $a_obj=new stdClass();
                        $a_obj->userid=$record_id;
                        $record_id_users[]=$a_obj;
			}
    }

    $record_teachers  = referentiel_get_teachers_course($course->id);
/*
		// $roles_exclus=array(1,2);
		// $record_teachers  = referentiel_get_teachers_course($course->id,0,0,$roles_exclus);  //seulement les enseignants sans les administrateurs
    $record_teachers  = referentiel_get_teachers_course_old($course->id,0,0,'1,2');  //seulement les enseignants sans les administrateurs
*/
    echo referentiel_print_accompagnement($referentiel_instance->id, $course->id,  $context, $record_id_users, $record_teachers, $userid_filtre);
  }
}


// ----------------------------------------------------
function referentiel_print_accompagnement($referentiel_instance_id, $course_id, $context, $record_users, $record_teachers, $userid){
	$s="";
  $t_users=array();
  $t_teachers=array();
  $nb_teachers=0;
  $nb_users=0;
  $nb_col=0;
  $nb_lig=0;
  $maxcol=8;
  $colwidth=(int)(100 / ($maxcol+1)).'%';

    if ($record_users){
        foreach ($record_users as $record_u) {   // liste d'id users
        // DEBUG
		//echo "<br />Debug :: print_lib_accompagnement.php :: 63 ::<br />\n";
		//print_object($record_u);

            $t_users[]= array('id' => $record_u->userid, 'lastname' => referentiel_get_user_nom($record_u->userid), 'firstname' => referentiel_get_user_prenom($record_u->userid));
            $t_users_id[]= $record_u->userid;
            $t_users_lastname[] = referentiel_get_user_nom($record_u->userid);
            $t_users_firstname[] = referentiel_get_user_prenom($record_u->userid);
        }
        array_multisort($t_users_lastname, SORT_ASC, $t_users_firstname, SORT_ASC, $t_users);

        $users_list=implode(',',$t_users_id);
        $nb_users=count($t_users);
        // echo "<br />Debug :: print_lib_accompagnement.php :: 79 ::<br />\n";
		// print_r($t_users);
    }

    if ($record_teachers){
  		foreach ($record_teachers as $record_t) {   // liste d'id teachers
            if ($record_t){
                $t_teachers[]=$record_t->userid;
		    }
        }
        $teachers_list=implode(',',$t_teachers);
        $nb_teachers=count($t_teachers);
        $nb_lig=$nb_teachers % $maxcol;

        $col=0;
        $lig=0;

        $s.='<div align="center">'."\n";
        $s.='<h3>'.get_string('liste_accompagnement','referentiel').'</h3>'."\n";
		$s.='<table class="activite">'."\n";


        $j=0;
        $index_teacher_deb=0;
        $index_teacher_fin=0;

        while ($j<$nb_teachers) {
            $index_teacher_fin++;
            if ($col==0){
           		$s.="<tr valign='top'><th align='left' width='10%'>".get_string('eleves','referentiel').' \\ '.get_string('referent','referentiel')."</th>\n";
            }
            $s.="<th width='".$colwidth."'>\n";
            $s.="<b>".referentiel_get_user_nom($t_teachers[$j]).' '.referentiel_get_user_prenom($t_teachers[$j])."</b>\n";
            $s.="</th>\n";
            // saut de ligne ?
            $col++;
            if (($col==$maxcol) || ($j==$nb_teachers-1)){
                $lig++;
                $col=0;
                $s.="</tr>\n";
                // eleves
                for ($i=0; $i<$nb_users; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
                    if ($userid==$t_users[$i]['id']){
  			            $s.="<b>".$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."</b>\n";
				    }
				    else{
                        $s.=$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
				    }
                    $s.="</td>";
//$s.=referentiel_select_accompagnements_user_by_teachers($referentiel_instance_id, $course_id, $t_users[$i]['id'], $t_teachers, $index_teacher_deb, $index_teacher_fin);
$s.=referentiel_print_accompagnements_user_by_teachers($referentiel_instance_id, $course_id, $t_users[$i]['id'], $t_teachers, $index_teacher_deb, $index_teacher_fin, $colwidth);
                    $s.="</tr>\n";

                }
                $index_teacher_deb=$index_teacher_fin;
            }

            $j++;
        }

        // completer affichage
        if ($index_teacher_deb<$nb_teachers){
            $s.="</tr>";
            for ($i=0; $i<$nb_users; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
                    if ($userid==$t_users[$i]['id']){
  			            $s.="<b>".$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."</b>\n";
				    }
				    else{
                        $s.=$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
				    }
                    $s.="</td>";
// $s.=referentiel_select_accompagnements_user_by_teachers($referentiel_instance_id, $course_id, $t_users[$i]['id'], $t_teachers, $index_teacher_deb, $index_teacher_fin);
$s.=referentiel_print_accompagnements_user_by_teachers($referentiel_instance_id, $course_id, $t_users[$i]['id'], $t_teachers, $index_teacher_deb, $index_teacher_fin, $colwidth);
                    $s.="</tr>\n";
            }
        }
        $s.='</table>'."\n";
        $s.='</div>'."\n";

	}
	return $s;
}




// ----------------------------------
function referentiel_select_accompagnement_users_teachers($referentiel_instance_id, $course_id, $mode, $record_users, $record_teachers, $userid, $select_acc=0){
	$s="";
  $t_users=array();
  $t_teachers=array();
  $nb_teachers=0;
  $nb_users=0;
  $nb_col=0;
  $nb_lig=0;
  $maxcol=8;
  $colwidth=(int)(100 / ($maxcol+1)).'%';
  

    if ($record_users){
        foreach ($record_users as $record_u) {   // liste d'id users
        // DEBUG
		//echo "<br />Debug :: print_lib_accompagnement.php :: 63 ::<br />\n";
		//print_object($record_u);

            $t_users[]= array('id' => $record_u->userid, 'lastname' => referentiel_get_user_nom($record_u->userid), 'firstname' => referentiel_get_user_prenom($record_u->userid));
            $t_users_id[]= $record_u->userid;
            $t_users_lastname[] = referentiel_get_user_nom($record_u->userid);
            $t_users_firstname[] = referentiel_get_user_prenom($record_u->userid);
        }
        array_multisort($t_users_lastname, SORT_ASC, $t_users_firstname, SORT_ASC, $t_users);

        $users_list=implode(',',$t_users_id);
        $nb_users=count($t_users);
        // echo "<br />Debug :: print_lib_accompagnement.php :: 79 ::<br />\n";
		// print_r($t_users);
    }
    
    if ($record_teachers){
        $s.='<div align="center">'."\n";
        $s.='<h3>'.get_string('aide_accompagnement','referentiel').'</h3>'."\n";

        $s.="\n".'<form name="form" method="post" action="accompagnement.php?d='.$referentiel_instance_id.'&amp;action=selectaccompagnement&amp;mode='.$mode.'">'."\n";
	   

        // DEBUG
		  //echo "<br />Debug :: print_lib_accompagnement.php :: 63 ::<br />\n";
		  //print_r($record_users);      	
        $s.='<div align="center">'."\n";
        $s.='<input type="button" name="select_tous_enseignants" id="select_tous_enseignants" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkall()" />'."\n";
        $s.='&nbsp; &nbsp; &nbsp; <input type="button" name="select_aucun_enseignant" id="select_aucun_enseignant" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckall()" />'."\n";

        $s.='&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type="submit" value="'.get_string('savechanges').'" />'."\n";
        $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
        $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
        $s.='</div>'."\n";


		// Enseignants
        // DEBUG
		    //echo "<br />Debug :: print_lib_accompagnement.php :: 39 ::<br />\n";
		    //print_r($record_teachers);    


  		foreach ($record_teachers as $record_t) {   // liste d'id teachers
            if ($record_t){
                $t_teachers[]=$record_t->userid;
		    }
        }	
        $teachers_list=implode(',',$t_teachers);
        $nb_teachers=count($t_teachers);
        $nb_lig=$nb_teachers % $maxcol;
        
        $col=0;
        $lig=0;
        $s.='<table class="activite">'."\n";
        // foreach ($t_teachers as $tid) {
        $j=0;
        $index_teacher_deb=0;
        $index_teacher_fin=0;

        while ($j<$nb_teachers) {
            $index_teacher_fin++;
            if ($col==0){
           		$s.="<tr valign='top'><th align='left' width='".$colwidth."'>".get_string('eleves','referentiel').' \\ '.get_string('referent','referentiel')."</th>\n";
            }
            $s.="<th width='".$colwidth."'>\n";
            $s.="<b>".referentiel_get_user_nom($t_teachers[$j]).' '.referentiel_get_user_prenom($t_teachers[$j])."</b><br />\n";
            $s.='<input type="button" name="select_enseignant" id="select_enseignant_'.$t_teachers[$j].'" value="v"  onClick="return validerAllCheckBox(\'t_teachers['.$t_teachers[$j].'][]\')" />'."\n";
            $s.='&nbsp; &nbsp; <input type="button" name="select_enseignant" id="select_enseignant_'.$t_teachers[$j].'" value="x"  onClick="return invaliderAllCheckBox(\'t_teachers['.$t_teachers[$j].'][]\')" />'."\n";
            $s.="</th>\n";
            // saut de ligne ?
            $col++;
            if (($col==$maxcol) || ($j==$nb_teachers-1)){
                $lig++;
                $col=0;
                $s.="</tr>\n";
                // eleves
                for ($i=0; $i<$nb_users; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
                    if ($userid==$t_users[$i]['id']){
  			            $s.="<b>".$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."</b>\n";
				    }
				    else{
                        $s.=$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
				    }
                    $s.="</td>";
                    $s.=referentiel_select_accompagnements_user_by_teachers($referentiel_instance_id, $course_id, $t_users[$i]['id'], $t_teachers, $index_teacher_deb, $index_teacher_fin, $colwidth);
                    $s.="</tr>\n";

                }
                $index_teacher_deb=$index_teacher_fin;
            }

            $j++;
        }
        if ($index_teacher_deb<$nb_teachers){
            for ($i=0; $i<$nb_users; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
                    if ($userid==$t_users[$i]['id']){
  			            $s.="<b>".$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."</b>\n";
				    }
				    else{
                        $s.=$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
				    }
                    $s.="</td>";
                    $s.=referentiel_select_accompagnements_user_by_teachers($referentiel_instance_id, $course_id, $t_users[$i]['id'], $t_teachers, $index_teacher_deb, $index_teacher_fin, $colwidth);
                    $s.="</tr>\n";
            }
        }
        $nbcol=$nb_teachers>$maxcol?$maxcol:$nb_teachers;
        $nbcol++;
        $s.="<tr valign='top'><td align='center' colspan='".$nbcol."'>\n";
        $s.='<input type="submit" value="'.get_string('savechanges').'" />'."\n";
        $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
        $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
        $s.='
<input type="hidden" name="select_acc" value="'.$select_acc.'" />
<input type="hidden" name="teachers_list"  value="'.$teachers_list.'" />
<input type="hidden" name="users_list"  value="'.$users_list.'" />
<input type="hidden" name="type"  value="REF" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="'.$course_id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />'."\n";
        $s.='</td></tr>';
	  
        $s.='</table>'."\n";
        $s.='</form>'."\n";
        $s.='</div>'."\n";
	
	}
	return $s;
}



/**************************************************************************
 * takes the current referentiel, an optionnal user id *
 * and mode to display                                                    *
 * input @param string  $mode                                             *
 *       @param object $referentiel_instance                              *
 *       @param int $userid_filtre                                        *
 *       @param array of objects $gusers of users get from current group  *
 *       @param string $sql_filtre_where, $sql_filtre_order               *
 * output null                                                            *
 **************************************************************************/
function  referentiel_select_accompagnement($mode, $referentiel_instance, $teacherid=0, $userid_filtre=0, $gusers=NULL, $select_acc=0){
global $CFG;
global $DB;
global $USER;
static $istutor=false;
static $isteacher=false;
static $isadmin=false;
static $isstudent=false;
static $referentiel_id = NULL;

    // A COMPLETER
    $data=NULL;
	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record("course", array("id" => "$cm->course"));
	if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR 5 :: print_lib_accompagnement.php :: You cannot call this script in that way');
	}
	
    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

	
	$records = array();
	$referentiel_id = $referentiel_instance->ref_referentiel;

    $roles=referentiel_roles_in_instance($referentiel_instance->id);
    $iseditor=$roles->is_editor;
    $isadmin=$roles->is_admin;
    $isteacher=$roles->is_teacher;
    $istutor=$roles->is_tutor;
    $isstudent=$roles->is_student;

	/*
	// DEBUG
    if ($iseditor) echo "Editor ";
    if ($isadmin) echo "Admin ";
	if ($isteacher) echo "Teacher ";
	if ($istutor) echo "Tutor ";
	if ($isstudent) echo "Student ";
	*/

	
	if (isset($referentiel_id) && ($referentiel_id>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_id);
		if (!$referentiel_referentiel){
			if ($iseditor){
				print_error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/mod/referentiel/edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
				print_error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}

		// boite pour selectionner les utilisateurs ?
		if ($isadmin || $isteacher || $istutor){
			// tous les users possibles (pour la boite de selection)
			// Get your userids the normal way
			// ICI on affiche tous les utilisateurs
			$record_id_users  = referentiel_get_students_course($course->id,0,0);  //seulement les stagiaires
			if ($gusers && $record_id_users){ // liste des utilisateurs du groupe courant
				// echo "<br />DEBUG :: print_lib_accompagnement.php :: 740 :: GUSERS<br />\n";
				// print_object($gusers);
				// echo "<br />\n";
				// exit;
				$record_users  = array_intersect($gusers, array_keys($record_id_users));
				// echo "<br />DEBUG :: print_lib_accompagnement.php :: 745 :: RECORD_USERS<br />\n";
				// print_r($record_users  );
				// echo "<br />\n";
				// recopier 
				$record_id_users=array();
				foreach ($record_users  as $record_id){
                        $a_obj=new stdClass();
                        $a_obj->userid=$record_id;
                        $record_id_users[]=$a_obj;
				}
			}
      		$record_teachers  = referentiel_get_teachers_course($course->id);
            echo referentiel_select_accompagnement_users_teachers($referentiel_instance->id, $course->id, $mode, $record_id_users, $record_teachers, $userid_filtre, $select_acc);
		}
	}
	echo '<br /><br />'."\n";
	return true;
}



?>
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


require_once("lib.php");

// -----------------------
function referentiel_get_repartition_notification($instanceid, $courseid, $t_codes_competence, $t_teacherids_accompagnement, $t_teacherids){
// affiche une liste de cases cochées
//
global $CFG;
                // DEBUG
                /*
                echo("print_lib_repartition.php :: 34 ::<br />TEACHERS ACCOMPAGNANTS<br />\n");
                print_object($t_teacherids_accompagnement);
                echo("<br /><br />TEACHERS du COURS : TEACHERIDS <br />\n");
                print_object($t_teacherids);
                echo("<br />\n");
                */

    $nb_teachers=0;
    $t_repartition_teachers = array(array());
    foreach ($t_codes_competence as $code_item){
        if (!empty($instanceid) && !empty($courseid) && !empty($code_item)){
            $teachers_repartition=referentiel_get_repartitions_teacher_by_competence($instanceid, $courseid, $code_item);
            // DEBUG
            /*
            echo("print_lib_repartition.php :: 49 ::<br />TEACHERS REPARTITION pour la COMPETENCE $code_item<br />\n");
            print_object($teachers_repartition);
            echo("<br />\n");
            */
            // verifier si intersection au sens large
            // a savoir si l'un des deux est vide l'autre est retourne
            $t_teacherids_intersect=referentiel_intersection_teachers($teachers_repartition, $t_teacherids_accompagnement);

            // DEBUG
            /*
            echo("print_lib_repartition.php :: 57 ::<br />INTERSECTION<br />\n");
            print_object($t_teacherids_intersect);
            echo("<br />\n");
            */
            foreach ($t_teacherids as $a_teacher){
                if (!empty($t_teacherids_intersect)){
                    // COLLECTER
                    if (array_key_exists($a_teacher->userid, $t_teacherids_intersect)==true) {
                        $t_repartition_teachers[$code_item][]='X';
                    }
                    else{
                        $t_repartition_teachers[$code_item][]='&nbsp;';
                    }
                }
                else{
                    $t_repartition_teachers[$code_item][]='+';
                }
            }

        }
    }
    /*
    echo("<br />\n");
    print_object($t_repartition_teachers);
    echo("<br />EXIT\n");
    exit;
    */
    return $t_repartition_teachers;
}

// -----------------------
function referentiel_print_notification_user($instanceid, $courseid, $context, $t_codes_competence, $userid, $records_teacher) {
    $s="";
    $s_entete="";
    $s_debut="";
    $s_competences="";
    if (!empty($records_teacher) && !empty($instanceid) && !empty($courseid) && !empty($userid) && !empty($t_codes_competence)){
        $nb_lig=0;
        $nb_col=0;
        $maxcol=8; // nombre max de colonnes par page : une colonne = un teacher
        $colwidth=(int)(100 / ($maxcol+1)).'%';

        // DEBUG
        //echo("TEACHERS<br />\n");
        //print_object($records_teacher);
        //echo("<br />\n");

      	foreach ($records_teacher as $record_t) {   // liste d'id teachers
            if ($record_t){
                $t_teachers[]=$record_t->userid;
		    }
        }
        $teachers_list=implode(',',$t_teachers);
        $nb_teachers=count($t_teachers);
        $nb_lig=$nb_teachers % $maxcol;    // nombre de pages

        $identite_user=referentiel_get_user_info($userid);

        if ($identite_user){
            $message='<h3>'.get_string('liste_repartition','referentiel').get_string('identite_utilisateur','referentiel',$identite_user).'</h3>';
        }
        else{
            $message='<h3>'.get_string('liste_repartition','referentiel').'</h3>';
        }

        $t_teacherids_accompagnement=referentiel_get_accompagnements_user($instanceid, $courseid, $userid);

                // DEBUG
/*
                echo("print_lib_repartition.php :: 134 :: TEACHERS ACCOMPAGNATEURS pour $userid<br />\n");
                print_object($t_teacherids_accompagnement);
                echo("<br />\n");
                // DEBUG
                echo("codes <br />\n");
                print_object($t_codes_competence);
                echo("<br />\n");
*/

        $t_repartition_competences=referentiel_get_repartition_notification($instanceid, $courseid, $t_codes_competence, $t_teacherids_accompagnement, $records_teacher);
                /*
                // DEBUG
                echo("print_lib_repartition.php :: 122:: COMPETENCES<br />\n");
                print_object($t_repartition_competences);
                echo("<br />\n");
                */

        if ($t_repartition_competences)
        {
            // DEBUG
/*
            echo("print_lib_repartition.php :: 130 :: COMPETENCES<br />\n");
            print_object($t_repartition_competences);
            echo("<br />\n");
            //exit;
*/
            $col=0;
            $lig=0;

            $s_debut.='<div align="center">'."\n";
            $s_debut.=$message."\n";
            $s_debut.='<table class="activite">'."\n";

            // teachers
            $max_index=min($nb_teachers, $maxcol);
            $index_teacher_deb=0;
            $index_teacher_fin=$max_index;
            $k=0;
            $j=0;
            $col=0;
            while ($k<$nb_teachers) {
                if ($col==0){
                    $s_entete="<tr valign='top'><th align='left' width='10%'>".get_string('item','referentiel').' \\ '.get_string('referent','referentiel')."</th>\n";
                    for ($j=$index_teacher_deb; $j<$index_teacher_fin; $j++) {
                        $s_entete.="<th width='".$colwidth."'>\n";
                        $s_entete.="<b>".referentiel_get_user_nom($t_teachers[$j]).' '.referentiel_get_user_prenom($t_teachers[$j])."</b>\n";
                        $s_entete.="</th>\n";
                    }
                    $s_entete.="</tr>\n";
                    $s.=$s_entete;
                    $k+=$max_index;
                }

                // competences
                $s_competences="";
                foreach($t_repartition_competences as $cle => $une_repartition){
                    if ($une_repartition){
                        // DEBUG
                        // echo("print_lib_repartition.php :: 173 :: CLE $cle <br />REPARTITION<br />\n");
                        // print_object($une_repartition);
                        // echo("<br />EXIT :: print_lib_repartition.php :: 188 \n");
                        // exit;
                        $s_competences.="<tr valign='top'><td>\n";
                        $s_competences.="<b>".$cle."</b>\n";
                        $s_competences.="</td>";
                        while (list($key, $val) = each($une_repartition)) {
                            if (($index_teacher_deb<=$key) && ($key<$index_teacher_fin)){
                                $s_competences.="<td>$val</td>";
                            }
                        }
                        $s_competences.="</tr>\n";
                    }
                }
                $s.=$s_competences;

                // saut de table ?
                $col+=$maxcol;
                if ($col==$max_index){
                    $col=0;
                    $index_teacher_deb+=$max_index;
                    $max_index=min($nb_teachers-$index_teacher_fin, $maxcol);
                    $index_teacher_fin+=$max_index;
                }
            }
        }
        $s=$s_debut."\n".$s.'</table>'."\n";
        $s.='</div>'."\n";

	}

    return $s;
}

// -----------------------
function referentiel_print_repartition_une_competence_by_teachers($referentiel_instance_id, $course_id, $code_item, $t_teacherids, $indexdeb, $indexfin, $colwidth) {
// affiche une liste de cases cochées 
// 
global $DB;
$s='';
	if (!empty($referentiel_instance_id) && !empty($course_id) && !empty($code_item)){
        if ($t_teacherids){
            for ($i=$indexdeb; $i<$indexfin; $i++){
                $sql = "SELECT teacherid, code_item FROM {referentiel_repartition}
 WHERE ref_instance=:instanceid
 AND courseid=:courseid AND code_item=:code_item AND teacherid=:teacherid
 ORDER BY code_item ASC, teacherid ASC ";
                $params=array("instanceid" => $referentiel_instance_id, "courseid" => $course_id, "code_item" => $code_item, "teacherid" => $t_teacherids[$i]);
                
                if (!empty($t_teacherids[$i])){
                    $records_r = $DB->get_records_sql($sql, $params);
                    $s.='<td width="'.$colwidth.'">';
                    if ($records_r){
                        foreach($records_r as $record){
                            if (true){
                                $s.='X';
                            }
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
function referentiel_select_repartition_une_competence_by_teachers($referentiel_instance_id, $course_id, $code_item, $t_teacherids, $indexdeb, $indexfin, $colwidth) {
// affiche une liste de cases à cocher
// 
global $DB;
$s='';
// DEBUG
// echo "LIGNE 65 DEB:$indexdeb, FIN:$indexfin <br />\n";
//
// exit;
	if (!empty($referentiel_instance_id) && !empty($course_id) && !empty($code_item)){
        if ($t_teacherids){
            //foreach ($t_teacherids as $tid){
            for ($i=$indexdeb; $i<$indexfin; $i++){
                $sql = "SELECT teacherid, code_item FROM {referentiel_repartition}
 WHERE ref_instance=:instanceid
 AND courseid=:courseid AND code_item=:code_item AND teacherid=:teacherid
 ORDER BY code_item ASC, teacherid ASC ";
                $params=array("instanceid" => $referentiel_instance_id, "courseid" => $course_id, "code_item" => $code_item, "teacherid" => $t_teacherids[$i]);

                if (!empty($t_teacherids[$i])){
                    $records_r = $DB->get_records_sql($sql, $params);
                    $s.="<td width='".$colwidth."'>";
                    if ($records_r){
                        foreach($records_r as $record){
                            if (true){
                                $s.='<input type="checkbox" name="t_teachers['.$t_teacherids[$i].'][]" id="t_teachers_'.$t_teacherids[$i].'" value="'.$code_item.'" checked="checked" /> '."\n";
                            }
                        }
                    }
                    else{
                        $s.='<input type="checkbox" name="t_teachers['.$t_teacherids[$i].'][]" id="t_teachers_'.$t_teacherids[$i].'" value="'.$code_item.'" /> '."\n";
                    }
                    $s.='</td>'."\n";
                }
            }
        }
    }
    return $s;
}



// Affiche une entete repartition
// *****************************************************************
// *
// output string                                                     *
// *****************************************************************

function referentiel_print_entete_repartition(){
// Affiche une entete repartition
$s="";
  $s.='<div align="center">';
	$s.='<table class="activite">'."\n";
	$s.='<tr>';
	$s.='<th width="10%"><b>'.get_string('id','referentiel').'</b></th>';
	$s.='<th width="20%"><b>'.get_string('code_item').'</b></th>';
	$s.='<th width="20%"><b>'.get_string('teacher').'</b></th>';
  $s.='<th width="20%"><b>'.get_string('affectation','referentiel').'</b></th>';
  $s.='<th width="30%">&nbsp;</th>';
	$s.='</tr>'."\n";
	return $s;
}

function referentiel_print_enqueue_repartition(){
// Affiche une entete repartition
	$s='</table>'."\n";
	$s.='</div><br />';
	return $s;
}


// Affiche une ligne de la table quand il n'y a pas d'repartition pour userid 
// *****************************************************************
// input @param a user id                                          *
// output string                                                     *
// *****************************************************************

function referentiel_print_aucun_repartition_competence($code_item){
	$s="";
	if ($code_item){
		$info=$code_item;
	}
	else{
		$info="&nbsp;";
	}
	
	$s.='<tr><td class="zero">&nbsp;</td><td class="zero">';
	$s.=$info;
	$s.='</td><td class="invalide" colspan="2">';
  $s.='<span class="small">'.get_string('notmatched','referentiel').'</span>';
	$s.='</td><td class="zero">&nbsp;</td></tr>'."\n";
	
	return $s;
}



// ----------------------------------------------------
function referentiel_print_repartition($referentiel_instance_id, $course_id, $context, $t_competences, $records_teacher, $identite_utilisateur=''){
        // DEBUG
/*
		echo "<br />Debug :: print_lib_repartition.php :: 221 :: referenteil_print_repartition<br />\n";
		print_object($t_competences);
		echo "<br />\n";
		print_object($records_teacher);
		echo "<br />\n";
*/


    $s="";
    $t_teachers=array();
    $nb_teachers=0;
    $nb_competences=0;
    $nb_col=0;
    $nb_lig=0;
    $maxcol=8;
    $colwidth=(int)(100 / ($maxcol+1)).'%';

    if ($identite_utilisateur){
        $message='<h3>'.get_string('liste_repartition','referentiel').get_string('identite_utilisateur','referentiel',$identite_utilisateur).'</h3>';
    }
    else{
        $message='<h3>'.get_string('liste_repartition','referentiel').'</h3>';
    }
    
    if ($t_competences){
        $competences_list=implode(',',$t_competences);
        $nb_competences=count($t_competences);
    }

    if ($records_teacher){
  		foreach ($records_teacher as $record_t) {   // liste d'id teachers
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
        $s.=$message."\n";
		$s.='<table class="activite">'."\n";


        $j=0;
        $index_teacher_deb=0;
        $index_teacher_fin=0;
        while ($j<$nb_teachers) {
            $index_teacher_fin++;
            if ($col==0){
           		$s.="<tr valign='top'><th align='left' width='10%'>".get_string('item','referentiel').' \\ '.get_string('referent','referentiel')."</th>\n";
            }
            $s.="<th width='".$colwidth."'>\n";
            $s.="<b>".referentiel_get_user_nom($t_teachers[$j]).' '.referentiel_get_user_prenom($t_teachers[$j])."</b>\n";
            $s.="</th>\n";
            // saut de ligne ?
            $col++;
            if ($col==$maxcol){
                $lig++;
                $col=0;
                $s.="</tr>\n";
                // competences

                for ($i=0; $i<$nb_competences; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
/*
                    if ($code_competence==$t_competences[$i]){
  			            $s.="<b>".$t_competences[$i]."</b>\n";
				    }
				    else{
                        $s.=$t_competences[$i]."\n";
				    }
*/
                    $s.="<b>".$t_competences[$i]."</b>\n";
                    $s.="</td>";
$s.=referentiel_print_repartition_une_competence_by_teachers($referentiel_instance_id, $course_id, $t_competences[$i], $t_teachers, $index_teacher_deb, $index_teacher_fin, $colwidth);
                    $s.="</td></tr>\n";

                }
                $index_teacher_deb=$index_teacher_fin;
            }

            $j++;
        }

        // completer affichage
        if ($index_teacher_deb<$nb_teachers){
            for ($i=0; $i<$nb_competences; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
/*
                    if ($code_competence==$t_competences[$i]){
  			            $s.="<b>".$code_competence."</b>\n";
				    }
				    else{
                        $s.=$code_competence."\n";
				    }
*/
                    $s.="<b>".$t_competences[$i]."</b>\n";
                    $s.="</td>";
                    $s.=referentiel_print_repartition_une_competence_by_teachers($referentiel_instance_id, $course_id, $t_competences[$i], $t_teachers, $index_teacher_deb, $index_teacher_fin, $colwidth);
                    $s.="</td></tr>\n";
            }
        }
        $s.='</table>'."\n";
        $s.='</div>'."\n";

	}
	return $s;
}




// ----------------------------------
function referentiel_select_repartition_competences_by_teachers($referentiel_instance_id, $course_id, $mode, $t_competences, $records_teacher, $select_acc=0){

        // DEBUG
/*
		echo "<br />Debug :: print_lib_repartition.php :: 336 :: referentiel_select_repartition_competence_teachers<br />\n";
		print_object($t_competences);
		echo "<br />\n";
		print_object($records_teacher);
		echo "<br />\n";
*/

  $s="";
  $t_teachers=array();
  $nb_teachers=0;
  $nb_competences=0;
  $nb_col=0;
  $nb_lig=0;
  $maxcol=8;
  $colwidth=(int)(100 / ($maxcol+1)).'%';
  

    if ($t_competences){
        $competences_list=implode(',',$t_competences);
        $nb_competences=count($t_competences);
    }

    if ($records_teacher){
        $s.='<div align="center">'."\n";
        $s.='<h3>'.get_string('aide_repartition','referentiel').'</h3>'."\n";

        $s.="\n".'<form name="form" method="post" action="accompagnement.php?d='.$referentiel_instance_id.'&amp;action=selectrepartition&amp;mode='.$mode.'">'."\n";
        $s.='<div align="center">'."\n";
        $s.='<input type="button" name="select_tous_enseignants" id="select_tous_enseignants" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkall()" />'."\n";
        $s.='&nbsp; &nbsp; &nbsp; <input type="button" name="select_aucun_enseignant" id="select_aucun_enseignant" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckall()" />'."\n";

        $s.='&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type="submit" value="'.get_string('savechanges').'" />'."\n";
        $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
        $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";

        $s.='</div>'."\n";

		// Enseignants
  		foreach ($records_teacher as $record_t) {   // liste d'id teachers
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
           		$s.="<tr valign='top'><th align='left' width='".$colwidth."'>".get_string('item','referentiel').' \\ '.get_string('referent','referentiel')."</th>\n";
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
                // competences
                for ($i=0; $i<$nb_competences; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
/*
                    if ($code_competence==$t_competences[$i]){
  			            $s.="<b>".$t_competences[$i]."</b>\n";
				    }
				    else{
                        $s.=$t_competences[$i]."\n";
				    }
*/
                    $s.="<b>".$t_competences[$i]."</b>\n";
                    $s.="</td>";
                    $s.=referentiel_select_repartition_une_competence_by_teachers($referentiel_instance_id, $course_id, $t_competences[$i], $t_teachers, $index_teacher_deb, $index_teacher_fin, $colwidth);
                    $s.="</tr>\n";

                }
                $index_teacher_deb=$index_teacher_fin;
            }

            $j++;
        }
        if ($index_teacher_deb<$nb_teachers){
            for ($i=0; $i<$nb_competences; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
/*
                    if ($code_competence==$t_competences[$i]){
  			            $s.="<b>".$t_competences[$i]."</b>\n";
				    }
				    else{
                        $s.=$t_competences[$i]."\n";
				    }
*/
                    $s.="<b>".$t_competences[$i]."</b>"."\n";
                    $s.="</td>";
                    $s.=referentiel_select_repartition_une_competence_by_teachers($referentiel_instance_id, $course_id, $t_competences[$i], $t_teachers, $index_teacher_deb, $index_teacher_fin, $colwidth);
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
<input type="hidden" name="competences_list"  value="'.$competences_list.'" />
<input type="hidden" name="type"  value="REF" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="course"        value="'.$course_id.'" />
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
function  referentiel_select_repartition($mode, $referentiel_instance, $teacherid=0, $select_acc=0){
global $DB;
global $CFG;
global $USER;
static $istutor=false;
static $isteacher=false;
static $isadmin=false;
static $iseditor=false;
static $referentiel_id = NULL;

    // A COMPLETER
    $data=NULL;
	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', array('id' => $cm->course));
	if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR 5 :: print_lib_reaprtition.php :: 494 :: You cannot call this script in that way');
	}
	
    $context = context_module::instance($cm->id);
	
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
	if ($isadmin) echo "Admin ";
	if ($isteacher) echo "Teacher ";
	if ($istutor) echo "Tutor ";
	if ($isstudent) echo "Student ";
	*/

	
	if (isset($referentiel_id) && ($referentiel_id>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_id);
		if (!$referentiel_referentiel){
			if ($iseditor){
				print_error(get_string('creer_referentiel','referentiel'), "edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
				print_error(get_string('creer_referentiel','referentiel'), "../../course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}

		// boite pour selectionner les utilisateurs ?
		if ($isteacher || $iseditor || $istutor){
      		$records_teacher  = referentiel_get_teachers_course($course->id);
            // codes item
            $t_codes_competence=explode('/',referentiel_purge_dernier_separateur($referentiel_referentiel->liste_codes_competence, '/'));
            echo referentiel_select_repartition_competences_by_teachers($referentiel_instance->id, $course->id, $mode, $t_codes_competence, $records_teacher, $select_acc);
		}
	}
	echo '<br /><br />'."\n";
	return true;
}

//------------------------------
function referentiel_print_suivi_user($mode, $referentiel_instance, $userid_filtre=0, $gusers=NULL, $sql_filtre_where='', $sql_filtre_order='', $data_filtre, $select_acc=0) {
// Affiche les repartitions d'affectations
// pour toutes les competences cette instance de referentiel
    global $DB;
    global $CFG;
    global $USER;
    static $istutor=false;
    static $isteacher=false;
    static $isadmin=false;
    static $iseditor=false;
    static $referentiel_id = NULL;

    // A COMPLETER
    $data=NULL;


    if (!empty($referentiel_instance)){
        $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
        $course = $DB->get_record('course', array('id' => $cm->course));
        if (empty($cm) or empty($course)){
        	print_error('REFERENTIEL_ERROR :: print_lib_repartition.php :: 166 :: You cannot call this script in that way');
        }
        // echo "<br />DEBUG :: 220 ::<br />REFERENTIEL_Instance : $referentiel_instance->id <br /> Course_id : $referentiel_instance->course\n";
        $context = context_module::instance($cm->id);

        $refrefid = $referentiel_instance->ref_referentiel;

        $roles=referentiel_roles_in_instance($referentiel_instance->id);
        $iseditor=$roles->is_editor;
        $isadmin=$roles->is_admin;
        $isteacher=$roles->is_teacher;
        $istutor=$roles->is_tutor;
        $isstudent=$roles->is_student;

	    /*
        // DEBUG
	    if ($isadmin) echo "Admin ";
        if ($isteacher) echo "Teacher ";
	    if ($istutor) echo "Tutor ";
	    if ($isstudent) echo "Student ";
	    */

        if (!empty($refrefid)){
            $referentiel_referentiel=referentiel_get_referentiel_referentiel($refrefid);
            if (!$referentiel_referentiel){
                if ($iseditor){
                    print_error(get_string('creer_referentiel','referentiel'), "edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
                }
                else {
				    print_error(get_string('creer_referentiel','referentiel'), "../../course/view.php?id=$course->id&amp;sesskey=".sesskey());
    			}
	        }

            // boite pour selectionner les utilisateurs ?
    		if ($isteacher || $iseditor || $istutor){
	       		if (!empty($select_acc)){
		      	    // eleves accompagnes
                    $record_id_users  = referentiel_get_accompagnements_teacher($referentiel_instance->id, $course->id, $USER->id);
                }
                else{
                    // tous les users possibles (pour la boite de selection)
				    // Get your userids the normal way
                    $record_id_users  = referentiel_get_students_course($course->id,0,0);  //seulement les stagiaires
                }
                if ($gusers && $record_id_users){ // liste des utilisateurs du groupe courant
				    // echo "<br />DEBUG :: print_lib_activite.php :: 740 :: GUSERS<br />\n";
				    // print_object($gusers);
				    // echo "<br />\n";
				    // exit;
				    $record_users  = array_intersect($gusers, array_keys($record_id_users));
				    // echo "<br />DEBUG :: print_lib_repartition.php :: 745 :: RECORD_USERS<br />\n";
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

			    // echo referentiel_select_users_activite($record_id_users, $userid_filtre, $mode, $select_acc);
			    echo referentiel_select_users_accompagnes($userid_filtre, $select_acc, $mode);
                echo referentiel_select_users_2($record_id_users, $userid_filtre, $select_acc, $mode);
            }
            else{
                $userid_filtre=$USER->id; // les étudiants ne peuvent voir que leur fiche
            }
            // recuperer les utilisateurs filtres
			// $userid_filtre est l'id de l'utilisateur dont on affiche les activites
			// si $userid_filtre ==0 on retourne tous les utilisateurs du cours et du groupe
            if (!empty($userid_filtre)){
                $record_id_users = referentiel_get_students_course($course->id, $userid_filtre, 0);
            }
            else{
                if (!empty($select_acc)){
                    // eleves accompagnes
                    $record_id_users  = referentiel_get_accompagnements_teacher($referentiel_instance->id, $course->id, $USER->id);
                }
                else{
                    $record_id_users = referentiel_get_students_course($course->id, $userid_filtre, 0);
                    // echo "<br />DEBUG :: print_lib_repartition.php :: 1850 :: RECORD_USERS<br />\n";
                    // print_r($record_users  );
                    // echo "<br />\n";
                }
            }

            if ($record_id_users && $gusers){ // liste des utilisateurs du groupe courant
                $record_users  = array_intersect($gusers, array_keys($record_id_users));
                // recopier
                $record_id_users=array();
                foreach ($record_users  as $record_id){
                    $a_obj=new stdClass();
                    $a_obj->userid=$record_id;
                    $record_id_users[]=$a_obj;
                }
            }

            if ((($userid_filtre==$USER->id) || ($userid_filtre==0)) && ($isteacher || $iseditor|| $istutor)){
                // Ajouter l'utilisateur courant pour qu'il puisse voir ses activites
                $a_obj=new stdClass();
                $a_obj->userid=$USER->id;
                $record_id_users[]=$a_obj;
            }

            // echo "<br />DEBUG :: print_lib_repartition.php :: 1870 :: RECORD_USERS<br />\n";
            // print_r($record_users  );
            // echo "<br />\n";

            if ($userid_filtre){
                // Afficher
                // codes item
                $records_teacher  = referentiel_get_teachers_course($course->id);
                $t_codes_competence=explode('/',referentiel_purge_dernier_separateur($referentiel_referentiel->liste_codes_competence, '/'));
                echo referentiel_print_notification_user($referentiel_instance->id, $course->id, $context, $t_codes_competence, $userid_filtre, $records_teacher);
            }
        }
    }

}

//------------------------------
function referentiel_print_liste_repartitions($referentiel_instance){
// Affiche les repartitions d'affectations de competences de cette instance de referentiel
global $DB;
static $istutor=false;
static $isteacher=false;
static $isadmin=false;
static $iseditor=false;
static $referentiel_id = NULL;

  if (!empty($referentiel_instance)){
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', array('id' => $cm->course));
    if (empty($cm) or empty($course)){
        	print_error('REFERENTIEL_ERROR :: print_lib_repartition.php :: 166 :: You cannot call this script in that way');
    }
    // echo "<br />DEBUG :: 220 ::<br />REFERENTIEL_Instance : $referentiel_instance->id <br /> Course_id : $referentiel_instance->course\n";
    $context = context_module::instance($cm->id);

    $referentiel_id = $referentiel_instance->ref_referentiel;

    $roles=referentiel_roles_in_instance($referentiel_instance->id);
    $iseditor=$roles->is_editor;
    $isadmin=$roles->is_admin;
    $isteacher=$roles->is_teacher;
    $istutor=$roles->is_tutor;
    $isstudent=$roles->is_student;

	/*
	// DEBUG
	if ($isadmin) echo "Admin ";
	if ($isteacher) echo "Teacher ";
	if ($istutor) echo "Tutor ";
	if ($isstudent) echo "Student ";
	*/


	if (!empty($referentiel_id)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_id);
		if (!$referentiel_referentiel){
			if ($iseditor){
				print_error(get_string('creer_referentiel','referentiel'), "edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
				print_error(get_string('creer_referentiel','referentiel'), "../../course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}

        $records_teacher  = referentiel_get_teachers_course($course->id);
        // codes item
        $t_codes_competence=explode('/',referentiel_purge_dernier_separateur($referentiel_referentiel->liste_codes_competence, '/'));
        echo referentiel_print_repartition($referentiel_instance->id, $course->id,  $context, $t_codes_competence, $records_teacher);
    }
  }
}


// ----------------------
function referentiel_select_users_accompagnes($userid=0, $select_acc=0, $mode='notification'){
global $cm;
global $course;

$s="";
  $s.='<div align="center">'."\n";

	// accompagnement
	$s.="\n".'<form name="form" method="post" action="accompagnement.php?id='.$cm->id.'&amp;action=select_acc">'."\n";
	$s.='<table class="selection">'."\n";
	$s.='<tr><td>';
	$s.=get_string('select_acc', 'referentiel');
  if (empty($select_acc)){
      $s.=' <input type="radio" name="select_acc" value="1" />'.get_string('yes')."\n";
		  $s.='<input type="radio" name="select_acc" value="0" checked="checked" />'.get_string('no')."\n";
	}
	else{
      $s.=' <input type="radio" name="select_acc" value="1" checked="checked" />'.get_string('yes')."\n";
		  $s.='<input type="radio" name="select_acc" value="0" />'.get_string('no')."\n";
  }
  $s.='</td><td><input type="submit" value="'.get_string('go').'" />'."\n";;
	$s.='
<!-- These hidden variables are always the same -->
<input type="hidden" name="course"        value="'.$course->id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />'."\n";
	$s.='</td>';
	$s.='</tr></table>'."\n";
	$s.='</form>'."\n";
  $s.='</div>'."\n";

	return $s;
}


// ----------------------
function referentiel_select_users_2($record_users, $userid=0, $select_acc=0, $mode='notification'){
global $cm;
global $course;
$maxcol=MAXBOITESSELECTION;
$s="";
$t_users=array();


    if ($record_users){
        $s.='<div align="center">'."\n";

		// $s.='<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";
	    foreach ($record_users as $record_u) {   // liste d'id users
			//
			$t_users[]= array('id' => $record_u->userid, 'lastname' => referentiel_get_user_nom($record_u->userid), 'firstname' => referentiel_get_user_prenom($record_u->userid));
			$t_users_id[]= $record_u->userid;
			$t_users_lastname[] = referentiel_get_user_nom($record_u->userid);
			$t_users_firstname[] = referentiel_get_user_prenom($record_u->userid);
		}
		array_multisort($t_users_lastname, SORT_ASC, $t_users_firstname, SORT_ASC, $t_users);
		//
		// echo "<br />Debug :: print_lib_repartition.php :: 1419 ::<br />\n";
		// print_r($t_users);

		// exit;
		$n=count($t_users);

		// How many boxes ?
		$size=4;

		$n=count($t_users);
        if ($n>=48){
			$l=$maxcol;
			$c=(int) ($n / $l);
			$size = 8;
		}
        elseif ($n>=18){
			$l=$maxcol;
			$c=(int) ($n / $l);
			$size = 6;
		}
        elseif ($n>=6){
			$l=$maxcol-2;
			$c=(int) ($n / $l);
        }
		else{
			$l=1;
			$c=(int) ($n);
		}

		if ($c*$l==$n){
            $reste=false;
        }
        else{
            $reste=true;
        }
		$i=0;

		$s.='<table class="selection"><tr valign="top"><td>'."\n";
		for ($j=0; $j<$l; $j++){
            $s.='<div style="max-width:250px;padding: 2px 2px 2px 2px;float:left;">'."\n";
			$s.="\n".'<form name="form" method="post" action="accompagnement.php?id='.$cm->id.'&amp;action=selectuser">'."\n";

			$s.='<select name="userid" id="userid" size="'.$size.'">'."\n";

            if ($j<$l-1){
                if (($userid=='') || ($userid==0)){
                    $s.='<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";
                }
                else{
                    $s.='<option value="0">'.get_string('choisir', 'referentiel').'</option>'."\n";
                }
			}
			else{
			   if ($reste){
                    if (($userid=='') || ($userid==0)){
                        $s.='<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";
                    }
                    else{
				      $s.='<option value="0">'.get_string('choisir', 'referentiel').'</option>'."\n";
                    }
                }
                else{
                    if (($userid=='') || ($userid==0)){
                        $s.='<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";
//                        $s.='<option value="0" selected="selected">'.get_string('tous', 'referentiel').'</option>'."\n";
                    }
                    else{
				        // $s.='<option value="0">'.get_string('tous', 'referentiel').'</option>'."\n";
				        $s.='<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";
                    }
                }
			}

			for ($k=0; $k<$c; $k++){
				if ($userid==$t_users[$i]['id']){
					$s.='<option value="'.$t_users[$i]['id'].'" selected="selected">'.referentiel_nom_prenom($t_users[$i]['lastname'], $t_users[$i]['firstname']).'</option>'."\n";
				}
				else{
					$s.='<option value="'.$t_users[$i]['id'].'">'.referentiel_nom_prenom($t_users[$i]['lastname'], $t_users[$i]['firstname']).'</option>'."\n";
				}
				$i++;
			}
			$s.='</select>'."\n";
			$s.='<br /><input type="submit" value="'.get_string('select', 'referentiel').'" />'."\n";;
			$s.='
<!-- accompagnement -->
<input type="hidden" name="select_acc"        value="'.$select_acc.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="course"        value="'.$course->id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />'."\n";
			$s.='</form>'."\n";
			$s.='</div>'."\n";
        }

        if ($i<$n){
            $s.='<div style="max-width:250px;padding: 2px 2px 2px 2px;float:left;">'."\n";
            $s.='<form name="form" method="post" action="accompagnement.php?id='.$cm->id.'&amp;action=selectuser">'."\n";
            $s.='<select name="userid" id="userid" size="'.$size.'">'."\n";
    		if (($userid=='') || ($userid==0)){
    		        $s.='<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";
	       			// $s.='<option value="0" selected="selected">'.get_string('tous', 'referentiel').'</option>'."\n";
		    }
            else{
                $s.='<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";
                // $s.='<option value="0">'.get_string('tous', 'referentiel').'</option>'."\n";
            }

            while ($i <$n){
                if ($userid==$t_users[$i]['id']){
                    $s.='<option value="'.$t_users[$i]['id'].'" selected="selected">'.referentiel_nom_prenom($t_users[$i]['lastname'], $t_users[$i]['firstname']).'</option>'."\n";
                }
				else{
					$s.='<option value="'.$t_users[$i]['id'].'">'.referentiel_nom_prenom($t_users[$i]['lastname'], $t_users[$i]['firstname']).'</option>'."\n";
				}
				$i++;
			}
			$s.='</select>'."\n";
			$s.='<br /><input type="submit" value="'.get_string('select', 'referentiel').'" />'."\n";;
			$s.='
<!-- accompagnement -->
<input type="hidden" name="select_acc" value="'.$select_acc.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="select_acc" value="'.$select_acc.'" />
<input type="hidden" name="course"        value="'.$course->id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />'."\n";
            $s.='</form>'."\n";
			$s.='</div>';
		}
        $s.='</td></tr></table>'."\n";
	}
	return $s;
}


?>
<?php // $Id: lib_pedagogie.php,v 0.1 2011/03/07 12:22:00 jf Exp $
/**
 * This page defines the class Pedagogie
 *
 * @author jf <jean.fruitet@univ-nantes.fr>
 * @version $Id: lib_pedagogie.php, v 0.1 2011/03/07 12:22:00 jf Exp $
 * @package referentiel
 **/
/*
CREATE TABLE referentiel_pedagogie (
    id BIGINT(10) unsigned NOT NULL auto_increment,
    promotion VARCHAR(4) DEFAULT NULL,
    num_groupe VARCHAR(20) DEFAULT NULL,
    date_cloture VARCHAR(20) DEFAULT NULL,
    timemodified BIGINT(10) unsigned NOT NULL,
    formation VARCHAR(40) DEFAULT NULL,
    pedagogie VARCHAR(40) DEFAULT NULL,
    composante VARCHAR(40) DEFAULT NULL,
CONSTRAINT  PRIMARY KEY (id)

CREATE TABLE referentiel_a_user_pedagogie (
    id BIGINT(10) unsigned NOT NULL auto_increment,
    userid BIGINT(10) unsigned NOT NULL DEFAULT 0,
    refrefid BIGINT(10) unsigned DEFAULT NULL,
    refpedago BIGINT(10) unsigned DEFAULT NULL,
CONSTRAINT  PRIMARY KEY (id)

);

*/

/**
 * Pegagogie
 *
 * @author jf <jean.fruitet@univ-nantes.fr>
 * @version $Id: pedagogie.html,v 0.1 2011/02/25 15:12:00 jf Exp $
 * @package referentiel
 */
 
 // ---------------------------------------------
function referentiel_vider_pedagos_assos(){
// vidage des tables de pedagogies
global $DB;
    $DB->delete_records("referentiel_course_users");
    $DB->delete_records("referentiel_a_user_pedagogie");
    $DB->delete_records("referentiel_pedagogie");
}

 
/**
 * referentiel_ajoute_date
 *
 * Usage
 *  $ladate= date("Y-m-d H:i:s",local_to_gmt(time()));
 *  $ladate_10=referentiel_ajoute_date($ladate,0,10,0,0,0,0);
 *  echo(" Date: $ladate_10, $ladate\n");
 */
function referentiel_ajoute_date($givendate, $sec=0, $min=0, $heu=0, $day=0, $mth=0, $yr=0) {
        $cd = strtotime($givendate);
        $newdate = date('Y-m-d H:i:s', mktime(date('H',$cd)+$heu,
            date('i',$cd)+$min, date('s',$cd)+$sec, date('m',$cd)+$mth,
            date('d',$cd)+$day, date('Y',$cd)+$yr));
        return $newdate;
    }

/************************************************************************
 * takes the current referentiel instance,  and mode to display         *
 * print pedagogies records                                             *
 *                                                                      *
 * input @param string  $mode                                           *
 *       @param object $referentiel_instance                            *
 *       @param int $userid_filtre                                      *
 *       @param array $gusers                                           *
 * output null                                                          *
 ************************************************************************/
function referentiel_print_liste_pedagogies($mode, $referentiel_instance) {
global $CFG;
global $DB;
global $USER;
static $referentiel_id = NULL;
	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record("course", array("id" => "$cm->course"));
    if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR :: lib_pedagogie.php :: 78 :: You cannot call this script in that way');
	}	

    $context = context_module::instance($cm->id);


    $roles=referentiel_roles_in_instance($referentiel_instance->id);
    $iseditor=$roles->is_editor;
    $isadmin=$roles->is_admin;
    $isteacher=$roles->is_teacher;
    $istutor=$roles->is_tutor;
    $isstudent=$roles->is_student;

	if (isset($referentiel_instance->ref_referentiel) && ($referentiel_instance->ref_referentiel>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_instance->ref_referentiel);
		if (!$referentiel_referentiel){
			if ($iseditor){
				error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/mod/referentiel/edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
				error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}
		
		// Afficher
		echo referentiel_entete_pedagogie();
		// recupere les enregistrements de pedagogies
		$records=referentiel_get_pedagogies();
		if ($records){
            foreach ($records as $record) {
                referentiel_print_pedagogie($record, $context, $referentiel_instance->id);
            }
        }
        echo referentiel_enqueue_pedagogie();
	}
    echo '<br /><br />'."\n";
}


// Affiche une association entre une pedagogie et un etudiant
// pour le referentiel courantcertificat
// *****************************************************************
// input @param a $record_a   of certificat                        *
// output null                                                     *
// *****************************************************************

function referentiel_print_pedagogie($record, $context, $referentiel_id){

	if ($record){
        // dates
		$date_cloture=$record->date_cloture;
?>

<a name="<?php  echo "pedago_$record->id"; ?>"></a>

<tr valign="top">
    <td align="left">
		<?php  echo $date_cloture; ?>
    </td>
    <td align="left">
		<?php  echo $record->promotion; ?>
    </td>
    <td align="left">
		<?php  echo stripslashes($record->formation); ?>
    </td>
    <td align="left">
		<?php  echo stripslashes($record->pedagogie); ?>
    </td>
    <td align="left">
		<?php  echo stripslashes($record->composante); ?>
    </td>
    <td align="left">
		<?php  echo stripslashes($record->num_groupe); ?>
    </td>
    <td align="left">
		<?php  echo stripslashes($record->commentaire); ?>
    </td>
    <td align="left">
    <?php echo referentiel_menu_pedagogie($context, $record->id, $referentiel_id); ?>
    </tr>
<?php
	}
}



// ----------------------------------------------------
function referentiel_entete_pedagogie(){
$s="";
	$s.='<table class="certificat">'."\n";
	$s.='<tr valign="top">';
	$s.='<th>'.get_string('date_cloture','referentiel').'</th>';
	$s.='<th>'.get_string('promotion','referentiel').'</th>';
    $s.='<th>'.get_string('formation','referentiel').'</th>';
  	$s.='<th>'.get_string('pedagogie','referentiel').'</th>';
	$s.='<th>'.get_string('composante','referentiel').'</th>';
	$s.='<th>'.get_string('num_groupe','referentiel').'</th>';
	$s.='<th>'.get_string('commentaire','referentiel').'</th>';
    $s.='<th>'.get_string('menu','referentiel').'</th>';
   	$s.='</tr>'."\n";
	return $s;
}

// ----------------------------------------------------
function referentiel_enqueue_pedagogie(){
// Affiche une enqueue activite
	$s='';
	$s.='</table>'."\n";
	return $s;
}


// *****************************************************************
// input @param context                                            *
// input @param id_referentiel                                     *
// input @param id_user                                            *
// output string                                                   *
// *****************************************************************
// Affiche le menu EDITER et SUPPRIMER des pedagogies
function referentiel_menu_pedagogie($context, $pedago_id, $referentiel_id){
	global $CFG;
	global $USER;
    global $OUTPUT;
	$s="";

	if (has_capability('mod/referentiel:managecertif', $context)
		or ($USER->id==$userid)) {
        $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/pedagogie.php?d='.$referentiel_id.'&pedago_id='.$pedago_id.'&amp;mode=updatepedago&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('t/edit').'" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>'."\n";
	}
	if (has_capability('mod/referentiel:managecertif', $context)){
	    $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/pedagogie.php?d='.$referentiel_id.'&pedago_id='.$pedago_id.'&amp;mode=deletepedago&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('t/delete').'" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>'."\n";
	}
	return $s;
}



/************************************************************************
 * takes the current referentiel instance,  and mode to display         *
 * print associations records                                             *
 *                                                                      *
 * input @param string  $mode                                           *
 *       @param object $referentiel_instance                            *
 *       @param int $userid_filtre                                      *
 *       @param array $gusers                                           *
 * output null                                                          *
 ************************************************************************/
function referentiel_print_liste_associations($mode, $referentiel_instance, $userid_filtre=0, $gusers) {
global $CFG;
global $DB;
global $USER;
static $referentiel_id = NULL;
	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record("course", array("id" => "$cm->course"));
    if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR :: lib_pedagogie.php :: 242 :: You cannot call this script in that way');
	}

    $context = context_module::instance($cm->id);

	$referentiel_id = $referentiel_instance->ref_referentiel;

    $roles=referentiel_roles_in_instance($referentiel_instance->id);
    $iseditor=$roles->is_editor;
    $isadmin=$roles->is_admin;
    $isteacher=$roles->is_teacher;
    $istutor=$roles->is_tutor;
    $isstudent=$roles->is_student;

	if (isset($referentiel_id) && ($referentiel_id>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_id);
		if (!$referentiel_referentiel){
			if ($iseditor){
				error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/mod/referentiel/edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
				error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}

		// boite pour selectionner les utilisateurs ?
		if ($isteacher || $iseditor || $istutor){
            // tous les users possibles (pour la boite de selection)
			// Get your userids the normal way
            $record_id_users  = referentiel_get_students_course($course->id,0,0);  //seulement les stagiaires

			if ($gusers && $record_id_users){ // liste des utilisateurs du groupe courant
				// echo "<br />DEBUG :: lib_pedagogie.php :: 130 :: GUSERS<br />\n";
				// print_object($gusers);
				// echo "<br />\n";
				$record_users  = array_intersect($gusers, array_keys($record_id_users));
				// $record_users  = array_intersect_assoc($record_id_users, array_keys($gusers));
				// echo "<br />DEBUG :: lib_pedagogie.php :: 135 :: RECORD_USERS<br />\n";
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
			echo referentiel_select_users_pedagogie($record_id_users, "pedagogie.php", $mode,  $userid_filtre);
		}
		else{
			$userid_filtre=$USER->id; // les étudiants ne peuvent voir que leur fiche
		}

		// recuperer les utilisateurs filtres
		// si $userid_filtre ==0 on retourne tous les utilisateurs du cours et du groupe
        if (!empty($userid_filtre)){
            $record_id_users = referentiel_get_students_course($course->id, $userid_filtre, 0);
        }
		else{
            $record_id_users = referentiel_get_students_course($course->id, $userid_filtre, 0);
        }

        // groupes ?
		if ($gusers && $record_id_users){
            $record_users  = array_intersect($gusers, array_keys($record_id_users));
			// recopier
			$record_id_users=array();
			foreach ($record_users  as $record_id){
                        $a_obj=new stdClass();
                        $a_obj->userid=$record_id;
                        $record_id_users[]=$a_obj;
			}
		}

		if ($record_id_users){
			// Afficher
			echo referentiel_entete_asso_pedagogie($context);
            foreach ($record_id_users  as $record_id) {   // afficher la liste d'users
				// recupere les enregistrements de pedagogies
				$record_pedago=referentiel_get_pedagogie_user($record_id->userid, $referentiel_id);
                // DEBUG
                // echo "<br />DEBUG :: lib_pedagogie :: 326 ::<br />\n";
                // print_object($record_pedago);
                if ($record_pedago){
					$isauthor = referentiel_pedagogie_isowner($record_pedago->id, $USER->id);
					if ($isauthor  || $istutor || $isteacher || $iseditor) {
                        referentiel_print_association($record_pedago, $context, $record_id->userid, $referentiel_instance->id, $referentiel_instance->ref_referentiel);
					}
				}

            }
            echo referentiel_enqueue_asso_pedagogie();
		}
    	echo '<br /><br />'."\n";
	}
}


// Affiche une association entre une pedagogie et un etudiant
// pour le referentiel courantcertificat
// *****************************************************************
// input @param a $record_a   of certificat                        *
// output null                                                     *
// *****************************************************************

function referentiel_print_association($record_pedago, $context, $userid, $refid, $refrefid){

	if ($record_pedago){
	    $username='';
	    $user_info='';
        if ($userid){
    	    $username=referentiel_get_user_login($userid);
            $user_info=referentiel_get_user_info($userid);
        }
        // dates
		$date_cloture=$record_pedago->date_cloture;
?>

<a name="<?php  echo "pedago_$record_pedago->id"; ?>"></a>

<tr valign="top">
    <td align="left">
		<?php p($username) ?>
    </td>
    <td align="left">
		<?php p($user_info) ?>
    </td>
    <td align="left">
		<?php  echo $date_cloture; ?>
    </td>		
    <td align="left">
		<?php  echo $record_pedago->promotion; ?>
    </td>		
    <td align="left">
		<?php  echo stripslashes($record_pedago->formation); ?>
    </td>		
    <td align="left">
		<?php  echo stripslashes($record_pedago->pedagogie); ?>
    </td>		
    <td align="left">
		<?php  echo stripslashes($record_pedago->composante); ?>
    </td>
    <td align="left">
		<?php  echo stripslashes($record_pedago->num_groupe); ?>
    </td>

<?php
	if (has_capability('mod/referentiel:managecertif', $context)){
        echo '<td align="left">';
        echo referentiel_menu_association($context, $refid, $userid);
        echo '</td>'."\n";
    }
?>
    </tr>
<?php
	}
}


// ----------------------------------------------------
function referentiel_entete_asso_pedagogie($context){
$s="";
	$s.='<table class="certificat">'."\n";
	$s.='<tr valign="top">';
	$s.='<th>'.get_string('login','referentiel').'</th>';
	$s.='<th>'.get_string('etudiant','referentiel').'</th>';
	$s.='<th>'.get_string('date_cloture','referentiel').'</th>';
	$s.='<th>'.get_string('promotion','referentiel').'</th>';
    $s.='<th>'.get_string('formation','referentiel').'</th>';
  	$s.='<th>'.get_string('pedagogie','referentiel').'</th>';
	$s.='<th>'.get_string('composante','referentiel').'</th>';
	$s.='<th>'.get_string('num_groupe','referentiel').'</th>';
	if (has_capability('mod/referentiel:managecertif', $context)){
        $s.='<th>'.get_string('menu','referentiel').'</th>';
    }
   	$s.='</tr>'."\n";
	return $s;
}

// ----------------------------------------------------
function referentiel_enqueue_asso_pedagogie(){
// Affiche une enqueue activite
	$s='';
	$s.='</table>'."\n";
	return $s;
}

// *****************************************************************
// input @param context                                            *
// input @param id_referentiel                                     *
// input @param id_user                                            *
// output string                                                   *
// *****************************************************************
// Affiche le menu EDITER et SUPPRIMER des pedagogies 
function referentiel_menu_association($context, $referentiel_id, $userid){
	global $CFG;
	global $USER;
	global $OUTPUT;
	$s="";
	if (has_capability('mod/referentiel:managecertif', $context)){
	    $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/pedagogie.php?d='.$referentiel_id.'&amp;userid='.$userid.'&amp;mode=editasso&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('/t/edit').'" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>'."\n";

        $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/pedagogie.php?d='.$referentiel_id.'&amp;userid='.$userid.'&amp;mode=deleteasso&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('t/delete').'" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>'."\n";
	}
	return $s;
}

/**
 * This function returns record pedagogie from table referentiel_pedagogie
 *
 * @param userid references user id
 * @param ref_referentiel references referentiel id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_pedagogies() {
global $DB;
    return $DB->get_records('referentiel_pedagogie', NULL);
}

/**
 * This function returns record pedagogie from table referentiel_pedagogie
 *
 * @param userid references user id
 * @param ref_referentiel references referentiel id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_pedagogie($pedagoid, $sql_filtre_where='') {
global $DB;
	if (!empty($pedagoid)){
        $params = array("id" => "$pedagoid");
	    $where=" WHERE id=:id ";
        if (!empty($sql_filtre_where)){
            $where .= $sql_filtre_where;
        }
        $sql="SELECT * FROM {referentiel_pedagogie} $where ";
        // DEBUG
        // echo "<br />DEBUG :: lib_pedagogie.php :: 476<br />SQL&gt;$sql\n";
        return $DB->get_record_sql($sql, $params);
    }
	return NULL;
}


/**
 * This function returns record pedagogie from table referentiel_pedagogie
 *
 * @param userid references user id
 * @param ref_referentiel references referentiel id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_formation_pedagogie($pedagoid) {
global $DB;
	if (!empty($pedagoid)){
        $rec=$DB->get_record_sql("SELECT id, formation, pedagogie, composante, num_groupe FROM {referentiel_pedagogie} WHERE id=:id" , array("id" => "$pedagoid"));
        return '['.$rec->id.'] '.$rec->composante.' :: '.$rec->formation.' :: '.$rec->pedagogie.' ('.$rec->num_groupe.')';
    }
	return '';
}


/**
 * This function returns record pedagogie from table referentiel_pedagogie
 *
 * @param data
 * @param ref_referentiel references referentiel id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_id_pedago_from_data($num_groupe, $date_cloture, $promotion, $formation, $pedagogie, $composante){
global $DB;
	if (!empty($promotion) && !empty($formation) && !empty($pedagogie)){
        return $DB->get_record_sql("SELECT * FROM {referentiel_pedagogie}
        WHERE  num_groupe=:numgroupe AND date_cloture=:datecloture
        AND promotion=:promotion AND formation=:formation
        AND pedagogie=:pedagogie AND composante=:composante ",
        array("numgroupe" => "$num_groupe", "datecloture" => "$date_cloture", "promotion" => "$promotion", "formation" => "$formation", "pedagogie" => "$pedagogie", "composante" => "$composante") );
    }
	return NULL;
}


/**
 * This function returns record pedagogie from table referentiel_pedagogie
 *
 * @param userid references user id
 * @param ref_referentiel references referentiel id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_pedagogie_user($userid, $refrefid){
// ne renvoie qu'un seul enregistrement
global $DB;
	if (!empty($userid) && !empty($refrefid)){
        $recs=referentiel_get_a_user_pedago($userid, $refrefid);
        if ($recs){
            foreach($recs as $rec){
                if ($rec && !empty($rec->refpedago)){
                    return $DB->get_record("referentiel_pedagogie", array("id" => "$rec->refpedago"));
                }
            }
        }
    }
	return NULL;
}


/**
 * This function returns record pedagogie from table referentiel_pedagogie
 *
 * @param userid references user id
 * @param ref_referentiel references referentiel id
 * @return object
 * @todo Finish documenting this function
 **/
function referentiel_get_pedagogies_referentiel($refrefid){
global $DB;
	if (!empty($refrefid)){
        $params= array("refid" => "$refrefid");
        $sql="SELECT DISTINCT refpedago, count(refpedago) as howmany FROM {referentiel_a_user_pedagogie}  WHERE refrefid=:refid GROUP BY refpedago ORDER BY refpedago ";
        // DEBUG
        // echo "<br />DEBUG :: lib_pedagogie.php :: 563 :: <br />SQL &gt;$sql\n";
        return $DB->get_records_sql($sql, $params);
    }
	return NULL;
}


/**
 * This function returns record pedagogie from table referentiel_pedagogie
 *
 * @param userid references user id 
 * @param pedagoid references a pedogogie id
 * @return true, false
 * @todo Finish documenting this function
 **/
function referentiel_pedagogie_isowner($pedagoid, $userid){
global $DB;
	if (!empty($userid) && !empty($pedagoid)){
        $params= array("refpedago" => "$pedagoid", "userid" => "$userid");
        if ($DB->get_record_sql("SELECT * FROM {referentiel_a_user_pedagogie} WHERE refpedago=:refpedago AND userid=:userid ", $params)){
            return true;
        }
	}
    return false;
}



// ----------------------
function referentiel_select_users_pedagogie($record_users, $appli='pedagogie.php', $mode='listpedago', $userid=0, $select_all=0){

global $cm;
global $course;
$maxcol=MAXBOITESSELECTION;
$s="";
$t_users=array();

	if ($record_users){
        foreach ($record_users as $record_u) {   // liste d'id users
			// 
			$t_users[]= array('id' => $record_u->userid, 'lastname' => referentiel_get_user_nom($record_u->userid), 'firstname' => referentiel_get_user_prenom($record_u->userid));
			$t_users_id[]= $record_u->userid;
			$t_users_lastname[] = referentiel_get_user_nom($record_u->userid);
			$t_users_firstname[] = referentiel_get_user_prenom($record_u->userid);
		}
		array_multisort($t_users_lastname, SORT_ASC, $t_users_firstname, SORT_ASC, $t_users);
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
		$s.='<table class="selection"><tr valign="top"><td>';
		for ($j=0; $j<$l; $j++){
   			$s.='<div style="max-width:250px;padding: 2px 2px 2px 2px;float:left;">'."\n";
			$s.="\n".'<form name="form" method="post" action="'.$appli.'?id='.$cm->id.'&amp;action=selectuser">'."\n";
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
                        $s.='<option value="0" selected="selected">'.get_string('tous', 'referentiel').'</option>'."\n";
                    }
                    else{
				      $s.='<option value="0">'.get_string('tous', 'referentiel').'</option>'."\n";
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
<!-- tous les certificats -->
<input type="hidden" name="select_all" value="'.$select_all.'" />				
<!-- accompagnement -->

<!-- These hidden variables are always the same -->
<input type="hidden" name="course"        value="'.$course->id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />
</form>'."\n";
			$s.='</div>';
		}
		
		if ($i<$n){
   			$s.='<div style="max-width:250px;padding: 2px 2px 2px 2px;float:left;">'."\n";
			$s.='<form name="form" method="post" action="'.$appli.'?id='.$cm->id.'&amp;action=selectuser">'."\n";	
			$s.='<select name="userid" id="userid" size="'.$size.'">'."\n";
			if (($userid=='') || ($userid==0)){
				$s.='<option value="0" selected="selected">'.get_string('tous', 'referentiel').'</option>'."\n";
			}
			else{
				$s.='<option value="0">'.get_string('tous', 'referentiel').'</option>'."\n";
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
<!-- tous les certificats -->
<input type="hidden" name="select_all" value="'.$select_all.'" />			
<!-- These hidden variables are always the same -->
<input type="hidden" name="course"        value="'.$course->id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />
</form>'."\n";
			$s.='</div>';
		}
		$s.='</td></tr></table>'."\n";

	}
	
	return $s;
}


// ---------------------------------------------
function referentiel_add_pedago($form){
// creation d'une entree  dans la table referentiel_pedagogie
// puis dans refrentiel_a_user_pedago
// retourne l'id de l'objet cree
global $DB;
    $record=new object();
    $record->date_cloture = $form->date_cloture;
    $record->promotion = $form->promotion;
    $record->formation= ($form->formation);
	$record->pedagogie= ($form->pedagogie);
    $record->composante= ($form->composante);
	$record->num_groupe = ($form->num_groupe);
    $record->commentaire= ($form->commentaire);

    // DEBUG
    // echo "<br />DEBUG :: lib_pedagogie.php :: 515\n";
    // print_r($record);
    $pedagoid=$DB->insert_record("referentiel_pedagogie", $record);

    if ($pedagoid){
        return $pedagoid;
    }
    return 0;
}


// ---------------------------------------------
function referentiel_update_pedago($form){
// mise a jour d'une entree  dans la table referentiel_pedagogie
// retourne true or false
global $DB;
    if (!empty($form->pedago_id)){
        $record=new object();
        $record->id = $form->pedago_id;
        $record->date_cloture = $form->date_cloture;
        $record->promotion = $form->promotion;
        $record->formation= ($form->formation);
        $record->pedagogie= ($form->pedagogie);
        $record->composante= ($form->composante);
        $record->num_groupe = ($form->num_groupe);
        $record->commentaire= ($form->commentaire);

        return ($DB->update_record("referentiel_pedagogie", $record));
    }
    return 0;
}



// ---------------------------------------------
function referentiel_update_pedagogie_record($rec){
// mise a jour d'une entree  dans la table referentiel_pedagogie
// retourne true or false
global $DB;
    if (!empty($rec->id)){
        $rec->promotion = $rec->promotion;
        $rec->formation= ($rec->formation);
        $rec->pedagogie= ($rec->pedagogie);
        $rec->composante= ($rec->composante);
        $rec->num_groupe = ($rec->num_groupe);
        $rec->commentaire= ($rec->commentaire);

        return ($DB->update_record("referentiel_pedagogie", $rec));
    }
    return 0;
}

// ---------------------------------------------
function referentiel_delete_pedago($pedagoid){
// suppression d'une entree  dans la table referentiel_pedagogie
// suppression de toutes les associations à cette pedagogie
// retourn true or false
global $DB;
    if (!empty($pedagoid)){
		$recs=$DB->get_records("referentiel_a_user_pedagogie", array("refpedago" => "$pedagoid"));
        if ($recs){
            foreach ($recs as $rec){
                if (!empty($rec->userid) && !empty($rec->refrefid)){
                    // supprimer l'association
                    referentiel_delete_asso_user($rec->refpedago, $rec->userid, $rec->refrefid);
                }
            }
        }
        // supprimer l'enregistrement
        return $DB->delete_records("referentiel_pedagogie", array("id" => "$pedagoid"));
    }
    return 0;
}


// ---------------------------------------------
function referentiel_get_a_user_pedago($userid, $refrefid){
// renvoyer les associations
global $DB;
	if (!empty($userid) && !empty($refrefid)){
        $params=array("refrefid" => "$refrefid", "userid" => "$userid");
        $sql="SELECT * FROM {referentiel_a_user_pedagogie} WHERE refrefid=:refrefid AND userid=:userid ORDER BY refpedago ASC ";
		return $DB->get_records_sql($sql, $params);
	}
	else {
		return NULL;
	}
}

// ---------------------------------------------
function referentiel_add_a_user_pedago($userid, $refrefid, $refpedago){
// creation d'une entree  dans la table referentiel_a_user_pedagogie
// retourne l'id de l'objet cree
global $DB;
    if (!empty($userid) && !empty($refrefid) && !empty($refpedago)){
        $recs=referentiel_get_a_user_pedago($userid, $refrefid);
        if ($recs){
            foreach($recs as $rec){
                if (!$rec){
                    $record=new object();
                    $record->userid=$userid;
                    $record->refrefid=$refrefid;
                    $record->refpedago=$refpedago;
                    return $DB->insert_record("referentiel_a_user_pedagogie", $record);
                }
            }
        }
        else{
            return $rec->id;
        }
    }
    return 0;
}

// ---------------------------------------------
function referentiel_get_userids_pedagogie($pedagoid, $refrefid){
// retourne records
global $DB;
    if (!empty($pedagoid) && !empty($refrefid)){
        $params=array("pedagoid" => $pedagoid, "refrefid" => "$refrefid");
        $sql="SELECT userid FROM {referentiel_a_user_pedagogie} WHERE refpedago=:pedagoid AND refrefid=:refrefid  ORDER BY userid ASC ";
		return $DB->get_records_sql($sql, $params);
    }
    return NULL;
}


// ---------------------------------------------
function referentiel_delete_asso($form){
// suppression d'une entree  dans la table referentiel_a_user_pedagogie
// retourn true or false
global $DB;
    if (!empty($form->userid) && !empty($form->refrefid) && !empty($form->pedago_id)){
        $recs=referentiel_get_a_user_pedago($form->userid, $form->refrefid);
        if ($recs){
            foreach($recs as $rec){
                if ($rec && ($rec->refpedago==$form->pedago_id)){
                    return $DB->delete_records("referentiel_a_user_pedagogie", array("id" => "$rec->id"));
                }
            }
        }
    }
    return 0;
}

// ---------------------------------------------
function referentiel_delete_asso_user($pedago_id, $userid, $refrefid){
// suppression d'une entree  dans la table referentiel_a_user_pedagogie
global $DB;
    if (!empty($userid) && !empty($refrefid) && !empty($pedago_id)){
        $recs=referentiel_get_a_user_pedago($userid, $refrefid);
        if ($recs){
            foreach ($recs as $rec){
                if ($rec && ($rec->refpedago==$pedago_id)){
                    $DB->delete_records("referentiel_a_user_pedagogie", array("id" => "$rec->id"));
                }
            }
        }
    }
}




// ---------------------------------------------
function referentiel_select_associations($mode, $referentiel_instance, $userid_filtre, $gusers) {
global $DB;
global $CFG;
global $USER;
static $referentiel_id = NULL;

    // A COMPLETER
    $data=NULL;
	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', array('id' => $cm->course));
    if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR 5 :: lib_pedagogie.php :: 926 :: You cannot call this script in that way');
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
            if (!empty($userid_filtre)){
                $record_id_users = referentiel_get_students_course($course->id,$userid_filtre,0);
            }
            else{
                $record_id_users  = referentiel_get_students_course($course->id,0,0);  //seulement les stagiaires
                if ($gusers && $record_id_users){ // liste des utilisateurs du groupe courant
				    $record_users  = array_intersect($gusers, array_keys($record_id_users));
				    // recopier
				    $record_id_users=array();
				    foreach ($record_users  as $record_id){
                        $a_obj=new stdClass();
                        $a_obj->userid=$record_id;
                        $record_id_users[]=$a_obj;
				    }
                }
            }
			// boites de selection
			// echo referentiel_select_users_accompagnes("pedagogie.php", $mode, $userid_filtre);
			// echo referentiel_select_users_pedagogie($record_id_users, "pedagogie.php", $mode,  $userid_filtre);

            // Afficher les pedagogies

            // recupere les enregistrements de pedagogies
		    $records_pedago=referentiel_get_pedagogies();
            if ($records_pedago){
                /*
                echo referentiel_entete_pedagogie();
                foreach ($records_pedago as $record_pedago) {
                    referentiel_print_pedagogie($record_pedago, $context, $referentiel_instance->id);
                }
                echo referentiel_enqueue_pedagogie();
                */
                // Afficher
                if ($record_id_users){
                    if ($userid_filtre){
                        echo referentiel_select_asso_user_pedago($course->id, $referentiel_instance->id, $referentiel_referentiel->id, $mode, $record_id_users, $records_pedago);
                    }
                    else{
                        echo referentiel_pagination_users_pedago($course->id, $referentiel_instance->id, $referentiel_referentiel->id, $mode, $record_id_users, $records_pedago);
                    }
                }
            }
	   }
	   echo '<br /><br />'."\n";
    }
}


// ----------------------------------
function referentiel_select_asso_user_pedago($course_id, $referentiel_instance_id, $referentiel_referentiel_id, $mode, $record_users, $record_pedagos){
    $s="";
    $t_users=array();
    $t_pedagos=array();
    $nb_pedagos=0;
    $nb_users=0;
    $nb_col=0;
    $nb_lig=0;
    $maxcol=8;
    $colwidth=(int)(100 / ($maxcol+1)).'%';


    if ($record_users){
        // DEBUG
		//echo "<br />Debug :: lib_pedagogie.php :: 1015 ::<br />\n";
		//print_object($record_users);

        foreach ($record_users as $record_u) {   // liste d'id users
            // DEBUG
            //echo "<br />Debug :: lib_pedagogie.php :: 1015 ::<br />\n";
            //print_object($record_u);

            $t_users[]= array('id' => $record_u->userid, 'lastname' => referentiel_get_user_nom($record_u->userid), 'firstname' => referentiel_get_user_prenom($record_u->userid));
            $t_users_id[]= $record_u->userid;
            $t_users_lastname[] = referentiel_get_user_nom($record_u->userid);
            $t_users_firstname[] = referentiel_get_user_prenom($record_u->userid);
        }
        array_multisort($t_users_lastname, SORT_ASC, $t_users_firstname, SORT_ASC, $t_users);

        $users_list=implode(',',$t_users_id);
        $nb_users=count($t_users);
        //echo "<br />Debug :: lib_pedagogie.php :: 1027 ::<br />\n";
		//print_r($t_users);
		//exit;
    }

    if ($record_pedagos){
        $s.='<div align="center">'."\n";
        $s.='<h3>'.get_string('aide_association','referentiel').'</h3>'."\n";

        $s.="\n".'<form name="form" method="post" action="pedagogie.php?d='.$referentiel_instance_id.'&amp;action=selectassociation&amp;mode='.$mode.'">'."\n";

        $s.='<div align="center">'."\n";
        // $s.='<input type="button" name="select_tous_pedagogies" id="select_tous_associations" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkall()" />'."\n";
        $s.='<input type="button" name="select_aucun_enseignant" id="select_aucun_association" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckall()" />'."\n";

        $s.="<br />\n";

        $s.='<input type="submit" value="'.get_string('savechanges').'" />'."\n";
        $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
        $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
        $s.='</div>'."\n";


  		foreach ($record_pedagos as $record_t) {   // liste d'id pedagos
            if ($record_t){
                $t_pedagos[]=$record_t->id;
		    }
        }
        $pedagos_list=implode(',',$t_pedagos);
        $nb_pedagos=count($t_pedagos);
        $nb_lig=$nb_pedagos % $maxcol;

        $col=0;
        $lig=0;
        $s.='<table class="activite">'."\n";
        // foreach ($t_pedagos as $tid) {
        $j=0;
        $index_pedago_deb=0;
        $index_pedago_fin=0;
        while ($j<$nb_pedagos) {
            $index_pedago_fin++;
            if ($col==0){
           		$s.="<tr valign='top'><th align='left' width='".$colwidth."'>".get_string('eleves','referentiel').' \\ '.get_string('referent','referentiel')."</th>\n";
            }
            $s.="<th width='".$colwidth."'>\n";
            $s.="<b>".referentiel_get_formation_pedagogie($t_pedagos[$j])."</b>\n";
            $s.="</th>\n";
            // saut de ligne ?
            $col++;
            if ($col==$maxcol){
                $lig++;
                $col=0;
                $s.="</tr>\n";
                // eleves
                for ($i=0; $i<$nb_users; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
                    $s.=$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
                    $s.="</td>";
$s.=referentiel_select_associations_user_pedagos($referentiel_referentiel_id, $t_users[$i]['id'], $t_pedagos, $index_pedago_deb, $index_pedago_fin, $colwidth);
                    $s.="</td></tr>\n";

                }
                $index_pedago_deb=$index_pedago_fin;
            }

            $j++;
        }
        if ($index_pedago_deb<$nb_pedagos){
            for ($i=0; $i<$nb_users; $i++){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
                    $s.=$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
                    $s.="</td>";
$s.=referentiel_select_associations_user_pedagos($referentiel_referentiel_id, $t_users[$i]['id'], $t_pedagos, $index_pedago_deb, $index_pedago_fin, $colwidth);
                    $s.="</td></tr>\n";
            }
        }
        $nbcol=$nb_pedagos>$maxcol?$maxcol:$nb_pedagos;
        $nbcol++;
        $s.="<tr valign='top'><td align='center' colspan='".$nbcol."'>\n";
        $s.='<input type="submit" value="'.get_string('savechanges').'" />'."\n";
        $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
        $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
        $s.='
<input type="hidden" name="pedagos_list"  value="'.$pedagos_list.'" />
<input type="hidden" name="users_list"  value="'.$users_list.'" />
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



// -----------------------
function referentiel_select_associations_user_pedagos($refrefid, $userid, $t_pedagoids, $indexdeb, $indexfin, $colwidth) {
// affiche une liste de radio boutons
//
global $DB;
$s='';
// DEBUG
// echo "LIGNE 65 DEB:$indexdeb, FIN:$indexfin <br />\n";
//
// exit;
	if (!empty($refrefid) && !empty($userid)){
        if ($t_pedagoids){
            //foreach ($t_pedagoids as $tid){
            for ($i=$indexdeb; $i<$indexfin; $i++){
                if (!empty($t_pedagoids[$i])){
                    $params = array("refrefid" => "$refrefid", "userid" => "$userid", "refpedago" => $t_pedagoids[$i]);
                    $sql="SELECT * FROM {referentiel_a_user_pedagogie}
 WHERE refrefid=:refrefid AND userid=:userid AND refpedago=:refpedago
 ORDER BY userid ASC, refpedago ASC ";
                    $records_acc = $DB->get_records_sql($sql, $params);
                    $s.="<td width='".$colwidth."'>";
                    if ($records_acc){
                        foreach($records_acc as $record){
                            $s.='<input type="radio" name="t_user['.$userid.']" id="t_user['.$userid.']" value="'.$t_pedagoids[$i].'" checked="checked" /> '."\n";
                        }
                    }
                    else{
                        $s.='<input type="radio" name="t_user['.$userid.']" id="t_user['.$userid.']" value="'.$t_pedagoids[$i].'" /> '."\n";
                    }
                    $s.='</td>'."\n";
                }
            }
        }
    }
    return $s;
}


// -----------------------
function referentiel_set_association_user_pedago($userid, $refrefid, $pedago_id){
global $DB;
$asso_id=0;
    if ( $userid && $refrefid && $pedago_id){
        $asso = new object();
        $asso->refrefid=$refrefid;
        $asso->userid=$userid;
        $asso->refpedago=$pedago_id;
        $asso_id= $DB->insert_record("referentiel_a_user_pedagogie", $asso);
    }
    return $asso_id;
}


// ----------------------
function referentiel_select_pedagogies($referentiel_instance, $appli='certificat.php', $mode='listcertif', $list_pedagoids='', $select_acc, $select_all, $sql_filtre_where='', $data_param=NULL){
// selection des certificats en fonction des pedagogies
	// contexte
global $DB;
global $CFG;
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', 'id', $cm->course);
    if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR 5 :: lib_pedagogie.php :: 1190 :: You cannot call this script in that way');
	}

    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}


	// recupere les enregistrements de pedagogies
	$records=referentiel_get_pedagogies_referentiel($referentiel_instance->ref_referentiel);
    if ($records){
        // DEBUG
        // echo "<br />DEBUG :: lib_pedagogie :: 1169 appelée depuis lib_certificat.php :: 165<br />\n";
        // print_r($records);
        echo referentiel_pedagogie_selection($course->id, $referentiel_instance->id, $referentiel_instance->ref_referentiel, $records, $context, $appli, $mode, $list_pedagoids, $select_acc, $select_all, $sql_filtre_where, $data_param);
    }
}


// ---------
function referentiel_trouve_dans_tableau($cherche, $tableau){
    if (is_array($tableau) && (count($tableau)>0)){
        $i=0;
        while ($i<count($tableau)){
            if ($cherche==$tableau[$i]){
                return true;
            }
            $i++;
        }
    }
    return false;
}


// ----------------------
function referentiel_pedagogie_selection($course_id, $referentiel_instance_id, $referentiel_referentiel_id,$records, $context, $appli, $mode, $list_pedagoids,$select_acc, $select_all, $sql_filtre_where='', $data=NULL){//

    $s="";
    $pedagos_list='';
    $l_pedagos=array();
    $s_pedagos=array();
    $t_pedagos=array();
    $r_howmany=array();
    $r_pedagos=array();
    $nb_pedagos=0;
    $nb_certificats=0;

    if (!empty($list_pedagoids)){
        $l_pedagos=explode(',',$list_pedagoids);
    }

    if ($records){
        foreach ($records as $record) {   // liste d'id users
            // DEBUG
            //echo "<br />Debug :: lib_pedagogie.php :: 1015 ::<br />\n";
            //print_object($record);
            $r_howmany[]=$record->howmany;
            $nb_certificats+=$record->howmany;
            $arec=referentiel_get_pedagogie($record->refpedago, $sql_filtre_where );
            if ($arec){
                $r_pedagos[]=$arec;
                // marquer
                if ($l_pedagos && referentiel_trouve_dans_tableau($record->refpedago, $l_pedagos)){
                    $s_pedagos[]=1;
                }
                else{
                    $s_pedagos[]=0;
                }
            }
        }
    }

    $s.='<div align="center">'."\n";
    $s.='<h4>'.get_string('aide_selection_pedago','referentiel').'</h4>'."\n";

    if ($r_pedagos){
  		for($i=0; $i<count($r_pedagos); $i++) {  // liste d'id pedagos
            if ($r_pedagos[$i]->id) {
                $t_pedagos[]=$r_pedagos[$i]->id;
            }
        }

        $pedagos_list=implode(',',$t_pedagos);
        $nb_pedagos=count($t_pedagos);
    }
    // entete avec filtrage des criteres de selection
    $s.=referentiel_entete_pedagogie_filtre($referentiel_referentiel_id, $appli, $data, $mode);

    // formulaire de selection des pedagogies
    $s.='<form name="form" method="post" action="'.$appli.'?d='.$referentiel_instance_id.'&amp;action=selectpedagogie&amp;mode='.$mode.'">'."\n";
    $s.='<div align="center">'."\n";
    $s.='<input type="button" name="select_tous_pedagogies" id="select_tous_pedagogies" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkall()" />'."\n";
    $s.='<input type="button" name="select_aucun_pedagogie" id="select_aucun_pedagogie" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckall()" />'."\n";

    $s.='</div>'."\n";
    if ($r_pedagos){

        for($i=0; $i<count($r_pedagos); $i++) {
            $s.='<tr valign="top">';
            if ($s_pedagos[$i]==true){
                $s.='<th with="5%"><input type="checkbox" name="t_pedago[]" id="t_pedago[]" value="'.$r_pedagos[$i]->id.'" checked="checked" /></th>'."\n";
            }
            else{
                $s.='<th with="5%"><input type="checkbox" name="t_pedago[]" id="t_pedago[]" value="'.$r_pedagos[$i]->id.'" /></th>'."\n";
            }
            $s.='<td with="10%">'.$r_pedagos[$i]->promotion.'</td>';
            $s.='<td with="10%">'.$r_pedagos[$i]->formation.'</td>';
            $s.='<td with="10%">'.$r_pedagos[$i]->pedagogie.'</td>';
    	    $s.='<td with="10%">'.$r_pedagos[$i]->composante.'</td>';
            $s.='<td with="10%">'.$r_pedagos[$i]->num_groupe.'</td>';
            $s.='<td with="10%">'.$r_pedagos[$i]->date_cloture.'</td>';
            $s.='<td with="30%">'.$r_pedagos[$i]->commentaire.'</td>';
            $s.='<td with="5%">'.$r_howmany[$i].'</td>';
            $s.="</tr>\n";
        }
     }
    $s.="<tr valign='top'><th colspan='10'>\n";
    $s.='<input type="submit" value="'.get_string('savechanges').'" />'."\n";
    $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
    $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
    $s.='
<input type="hidden" name="pedagos_list"  value="'.$pedagos_list.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="course"        value="'.$course_id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />
<input type="hidden" name="select_acc" value="'.$select_acc.'" />
<input type="hidden" name="select_all" value="2" />'."\n";
    if ($data){
        $s.='<input type="hidden" name="f_promotion" value="'.$data->f_promotion.'" />
<input type="hidden" name="f_formation" value="'.$data->f_formation.'" />
<input type="hidden" name="f_pedagogie" value="'.$data->f_pedagogie.'" />
<input type="hidden" name="f_composante" value="'.$data->f_composante.'" />
<input type="hidden" name="f_num_groupe" value="'.$data->f_num_groupe.'" />
'."\n";
    }
    $s.='</th></tr>';
    $s.='</table>'."\n";
    $s.='</form>'."\n";
    $s.='</div>'."\n";


	return $s;
}

// ---------------------
function referentiel_pedagogies_exists($refrefid){
//
global $DB;
	if (!empty($refrefid)){
        $sql="SELECT count(id) as c FROM {referentiel_a_user_pedagogie} WHERE refrefid=:refrefid ";
	    // DEBUG
	    // echo "<br />DEBUG :: lib_pedagogie.php :: 1339 :: <br />SQL&gt; $sql\n";
	    // exit;
		$rec_a_pedago=$DB->get_record_sql($sql,  array("refrefid" => "$refrefid"));
		if ($rec_a_pedago){
            if ($rec_a_pedago->c>0){
                return true;
            }
        }
	}
	return false;
}


// Affiche une entete pedagogie
// *****************************************************************
// *
// output string                                                    *
// *****************************************************************


// ---------------------
function referentiel_get_datas_pedago($refrefid){
// retourne un objet
global $DB;
    $o_pedagos= new stdClass();
    $o_pedagos->promotions='';
    $o_pedagos->formations='';
    $o_pedagos->pedagogies='';
    $o_pedagos->composantes='';
    $o_pedagos->num_groupes='';
    
    $tpromo[]=array();
    $tforma[]=array();
    $tpedag[]=array();
    $tcompo[]=array();
    $tgroup[]=array();

   	if (!empty($refrefid)){

		$recs=$DB->get_records_sql("SELECT DISTINCT refpedago FROM {referentiel_a_user_pedagogie} WHERE refrefid=:refrefid ORDER BY refpedago ", array("refrefid" => "$refrefid"));
        if ($recs){
            foreach ($recs as $rec){
                $tpromo[]= $DB->get_record_sql("SELECT promotion FROM {referentiel_pedagogie} WHERE id=:id ", array("id" => "$rec->refpedago"));
                $tforma[]= $DB->get_record_sql("SELECT formation FROM {referentiel_pedagogie} WHERE id=:id ", array("id" => "$rec->refpedago"));
                $tpedag[]= $DB->get_record_sql("SELECT pedagogie FROM {referentiel_pedagogie} WHERE id=:id ", array("id" => "$rec->refpedago"));
                $tcompo[]= $DB->get_record_sql("SELECT composante FROM {referentiel_pedagogie} WHERE id=:id ", array("id" => "$rec->refpedago"));
                $tgroup[]= $DB->get_record_sql("SELECT num_groupe FROM {referentiel_pedagogie} WHERE id=:id ", array("id" => "$rec->refpedago"));
            }
        }
        if ($tpromo){
            for ($i=0; $i<count($tpromo); $i++){
                if ($tpromo[$i] && !preg_match("/".$tpromo[$i]->promotion."/", $o_pedagos->promotions)) $o_pedagos->promotions.=$tpromo[$i]->promotion.',';
            }
            $o_pedagos->promotions=substr($o_pedagos->promotions,0, strlen($o_pedagos->promotions)-1);
        }
        if ($tforma){
            for ($i=0; $i<count($tforma); $i++){
                if ($tforma[$i] && !preg_match("/".$tforma[$i]->formation."/", $o_pedagos->formations)) $o_pedagos->formations.=$tforma[$i]->formation.',';
            }
            $o_pedagos->formations=substr($o_pedagos->formations,0, strlen($o_pedagos->formations)-1);
        }
        if ($tpedag){
            for ($i=0; $i<count($tpedag); $i++){
                if ($tpedag[$i] && !preg_match("/".$tpedag[$i]->pedagogie."/", $o_pedagos->pedagogies)) $o_pedagos->pedagogies.=$tpedag[$i]->pedagogie.',';
            }
            $o_pedagos->pedagogies=substr($o_pedagos->pedagogies,0, strlen($o_pedagos->pedagogies)-1);
        }
        if ($tcompo){
            for ($i=0; $i<count($tcompo); $i++){
                if ($tcompo[$i] && !preg_match("/".$tcompo[$i]->composante."/", $o_pedagos->composantes)) $o_pedagos->composantes.=$tcompo[$i]->composante.',';
            }
            $o_pedagos->composantes=substr($o_pedagos->composantes,0, strlen($o_pedagos->composantes)-1);
        }
        if ($tgroup){
            for ($i=0; $i<count($tgroup); $i++){
                if ($tgroup[$i] && !preg_match("/".$tgroup[$i]->num_groupe."/", $o_pedagos->num_groupes)) $o_pedagos->num_groupes.=$tgroup[$i]->num_groupe.',';
            }
            $o_pedagos->num_groupes=substr($o_pedagos->num_groupes,0, strlen($o_pedagos->num_groupes)-1);
        }
    }

    return $o_pedagos;
}


// --------------------
function referentiel_entete_pedagogie_filtre($referentiel_referentiel_id, $appli, $data, $mode){
// Affiche une entete avec boites de selection
$s="";
$appli=$appli.'&amp;mode_select=selectetab&amp;select_acc=0;&amp;select_all=2&amp;sesskey='.sesskey();
    $tpromos[]=array();
    $tformations[]=array();
    $tpedagogies[]=array();
    $tcomposantes[]=array();
    $tnum_groupes[]=array();
    
$pedago_listes=referentiel_get_datas_pedago($referentiel_referentiel_id);
/*
    $o_pedagos= new stdClass();
    $o_pedagos->promotions='';
    $o_pedagos->formations='';
    $o_pedagos->pedagogies='';
    $o_pedagos->composantes='';
    $o_pedagos->num_groupes='';
*/

if ($pedago_listes){
    // DEBUG
    //echo "<br />DEBUG :: lib_pedagogie.php :: 1394<br />\n";
    //print_r($pedago_listes);

    if ($pedago_listes->promotions){
        $tpromos=explode(',',$pedago_listes->promotions);
    }
    if ($pedago_listes->formations){
        $tformations=explode(',',$pedago_listes->formations);
    }
    if ($pedago_listes->pedagogies){
        $tpedagogies=explode(',',$pedago_listes->pedagogies);
    }
    if ($pedago_listes->composantes){
        $tcomposantes=explode(',',$pedago_listes->composantes);
    }
    if ($pedago_listes->num_groupes){
        $tnum_groupes=explode(',',$pedago_listes->num_groupes);
    }

    // DEBUG
 /*
    echo "<br />PROMOTION:\n";
    print_r($tpromos);
    echo "<br />FORMATIONS:\n";
    print_r($tformations);
    echo "<br />PADAGOGIES:\n";
    print_r($tpedagogies);
    echo "<br />COMPOSANTES:\n";
    print_r($tcomposantes);
    echo "<br />GROUPES:\n";
    print_r($tnum_groupes);
    echo "<br />\n";
 */
	$width="10%";
	$s.='<table class="activite" width="100%">'."\n";
    $s.='<th width="5%">'.get_string('cocher','referentiel').'</th>'."\n";
	$s.='<th width="'.$width.'">'.get_string('promotion','referentiel').'<br />';
	$s.='<form action="'.$appli.'" method="get" id="selectetab_f_promotion" class="popupform">'."\n";
	$s.=' <select id="selectetab_f_promotion" name="f_promotion" size="1"
onchange="self.location=document.getElementById(\'selectetab_f_promotion\').f_promotion.options[document.getElementById(\'selectetab_f_promotion\').f_promotion.selectedIndex].value;">'."\n";
	if (isset($data) && !empty($data)){
         $s.='	<option value="'.$appli.'&f_promotion=&f_formation='.$data->f_formation.'&f_pedagogie='.$data->f_pedagogie.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
         $s.='	<option value="'.$appli.'&f_promotion=&f_formation='.$data->f_formation.'&f_pedagogie='.$data->f_pedagogie.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'">'.get_string('tous','referentiel').'</option>'."\n";
        $i=0;
        while ($i<count($tpromos)){
            if ($tpromos[$i]){
                if ($data->f_promotion==$tpromos[$i]){
                    $s.='	<option value="'.$appli.'&f_promotion='.$tpromos[$i].'&f_formation='.$data->f_formation.'&f_pedagogie='.$data->f_pedagogie.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'" selected="selected">'.$tpromos[$i].'</option>'."\n";
                }
                else{
                    $s.='	<option value="'.$appli.'&f_promotion='.$tpromos[$i].'&f_formation='.$data->f_formation.'&f_pedagogie='.$data->f_pedagogie.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'">'.$tpromos[$i].'</option>'."\n";
                }
            }
            $i++;
	   }
    }
    else{
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=">'.get_string('tous','referentiel').'</option>'."\n";

        $i=0;
        while ($i<count($tpromos)){
            if ($tpromos[$i]){
                $s.='	<option value="'.$appli.'&f_promotion='.$tpromos[$i].'&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=">'.$tpromos[$i].'</option>'."\n";
            }
            $i++;
	    }
    }
	$s.='</select>'."\n";
    $s.='</form>'."\n";
	$s.='</th>';

	$s.='<th width="'.$width.'">'.get_string('formation','referentiel').'<br />';
	$s.="\n".'<form action="'.$appli.'" method="get" id="selectetab_f_formation" class="popupform">'."\n";
	$s.=' <select id="selectetab_f_formation" name="f_formation" size="1"
onchange="self.location=document.getElementById(\'selectetab_f_formation\').f_formation.options[document.getElementById(\'selectetab_f_formation\').f_formation.selectedIndex].value;">'."\n";
	if (isset($data) && !empty($data)){
        $s.='	<option value="'.$appli.'&f_formation=&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
        $s.='	<option value="'.$appli.'&f_formation=&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'">'.get_string('tous','referentiel').'</option>'."\n";

        $i=0;
        while ($i<count($tformations)){
            if ($tformations[$i]){
                if ($data->f_formation==$tformations[$i]){
                    $s.='	<option value="'.$appli.'&f_formation='.$tformations[$i].'&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'" selected="selected">'.$tformations[$i].'</option>'."\n";
                }
                else{
                    $s.='	<option value="'.$appli.'&f_formation='.$tformations[$i].'&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'">'.$tformations[$i].'</option>'."\n";
                }
            }
            $i++;
	   }
    }
    else{
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=">'.get_string('tous','referentiel').'</option>'."\n";

        $i=0;
        while ($i<count($tformations)){
            if ($tformations[$i]){
                $s.='	<option value="'.$appli.'&f_formation='.$tformations[$i].'&f_promotion=&f_pedagogie=&f_composante=&f_num_groupe=">'.$tformations[$i].'</option>'."\n";
            }
            $i++;
	   }
    }
	$s.='</select>'."\n";
    $s.='</form>'."\n";
	$s.='</th>';
	$s.='<th width="'.$width.'">'.get_string('pedagogie','referentiel').'<br />';
	$s.="\n".'<form action="'.$appli.'" method="get" id="selectetab_f_pedagogie" class="popupform">'."\n";
	$s.=' <select id="selectetab_f_pedagogie" name="f_pedagogie" size="1"
onchange="self.location=document.getElementById(\'selectetab_f_pedagogie\').f_pedagogie.options[document.getElementById(\'selectetab_f_pedagogie\').f_pedagogie.selectedIndex].value;">'."\n";
	if (isset($data) && !empty($data)){
        $s.='	<option value="'.$appli.'&f_pedagogie=&f_promotion='.$data->f_promotion.'&f_formation='.$data->f_formation.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
        $s.='	<option value="'.$appli.'&f_pedagogie=&f_promotion='.$data->f_promotion.'&f_formation='.$data->f_formation.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'">'.get_string('tous','referentiel').'</option>'."\n";
        $i=0;
        while ($i<count($tpedagogies)){
            if ($tpedagogies[$i]){
                if ($data->f_pedagogie==$tpedagogies[$i]){
                    $s.='	<option value="'.$appli.'&f_pedagogie='.$tpedagogies[$i].'&f_promotion='.$data->f_promotion.'&f_formation='.$data->f_formation.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'" selected="selected">'.$tpedagogies[$i].'</option>'."\n";
                }
                else{
                    $s.='	<option value="'.$appli.'&f_pedagogie='.$tpedagogies[$i].'&f_promotion='.$data->f_promotion.'&f_formation='.$data->f_formation.'&f_composante='.$data->f_composante.'&f_num_groupe='.$data->f_num_groupe.'">'.$tpedagogies[$i].'</option>'."\n";
                }
            }
            $i++;
       }
    }
    else{
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=">'.get_string('tous','referentiel').'</option>'."\n";

        $i=0;
        while ($i<count($tpedagogies)){
            if ($tpedagogies[$i]){
                $s.='	<option value="'.$appli.'&f_pedagogie='.$tpedagogies[$i].'&f_promotion=&f_composante=&f_formation=&f_num_groupe=">'.$tpedagogies[$i].'</option>'."\n";
            }
            $i++;
	   }
    }
	$s.='</select>'."\n";
    $s.='</form>'."\n";
	$s.='</th>';
	$s.='<th width="'.$width.'">'.get_string('composante','referentiel').'<br />';
	$s.="\n".'<form action="'.$appli.'" method="get" id="selectetab_f_composante" class="popupform">'."\n";
	$s.=' <select id="selectetab_f_composante" name="f_composante" size="1"
onchange="self.location=document.getElementById(\'selectetab_f_composante\').f_composante.options[document.getElementById(\'selectetab_f_composante\').f_composante.selectedIndex].value;">'."\n";
	if (isset($data) && !empty($data)){
        $s.='	<option value="'.$appli.'&f_composante=&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_formation='.$data->f_formation.'&f_num_groupe='.$data->f_num_groupe.'" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
        $s.='	<option value="'.$appli.'&f_composante=&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_formation='.$data->f_formation.'&f_num_groupe='.$data->f_num_groupe.'">'.get_string('tous','referentiel').'</option>'."\n";
        $i=0;
        while ($i<count($tcomposantes)){
            if ($data->f_composante==$tcomposantes[$i]){
                $s.='	<option value="'.$appli.'&f_composante='.$tcomposantes[$i].'&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_formation='.$data->f_formation.'&f_num_groupe='.$data->f_num_groupe.'" selected="selected">'.$tcomposantes[$i].'</option>'."\n";
            }
            else{
                $s.='	<option value="'.$appli.'&f_composante='.$tcomposantes[$i].'&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_formation='.$data->f_formation.'&f_num_groupe='.$data->f_num_groupe.'">'.$tcomposantes[$i].'</option>'."\n";
            }
            $i++;
	   }
    }
    else{
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=">'.get_string('tous','referentiel').'</option>'."\n";
        $i=0;
        while ($i<count($tcomposantes)){
            if ($tcomposantes[$i]){
                $s.='	<option value="'.$appli.'&f_composante='.$tcomposantes[$i].'&f_promotion=&f_pedagogie=&f_formation=&f_num_groupe=">'.$tcomposantes[$i].'</option>'."\n";
            }
            $i++;
	   }
    }
	$s.='</select>'."\n";
    $s.='</form>'."\n";
	$s.='</th>';

	$s.='<th width="'.$width.'">'.get_string('num_groupe','referentiel').'<br />';
	$s.="\n".'<form action="'.$appli.'" method="get" id="selectetab_f_num_groupe" class="popupform">'."\n";
	$s.=' <select id="selectetab_f_num_groupe" name="f_num_groupe" size="1"
onchange="self.location=document.getElementById(\'selectetab_f_num_groupe\').f_num_groupe.options[document.getElementById(\'selectetab_f_num_groupe\').f_num_groupe.selectedIndex].value;">'."\n";
	if (isset($data) && !empty($data)){
        $s.='	<option value="'.$appli.'&f_num_groupe=&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_formation='.$data->f_formation.'&f_composante='.$data->f_composante.'" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
        $s.='	<option value="'.$appli.'&f_num_groupe=&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_formation='.$data->f_formation.'&f_composante='.$data->f_composante.'">'.get_string('tous','referentiel').'</option>'."\n";
        $i=0;
        while ($i<count($tnum_groupes)){
            if ($data->f_num_groupe==$tnum_groupes[$i]){
                $s.='	<option value="'.$appli.'&f_num_groupe='.$tnum_groupes[$i].'&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_formation='.$data->f_formation.'&f_composante='.$data->f_composante.'" selected="selected">'.$tnum_groupes[$i].'</option>'."\n";
            }
            else{
                $s.='	<option value="'.$appli.'&f_num_groupe='.$tnum_groupes[$i].'&f_promotion='.$data->f_promotion.'&f_pedagogie='.$data->f_pedagogie.'&f_formation='.$data->f_formation.'&f_composante='.$data->f_composante.'">'.$tnum_groupes[$i].'</option>'."\n";
            }
            $i++;
	   }
    }
    else{
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=" selected="selected">'.get_string('choisir','referentiel').'</option>'."\n";
        $s.='	<option value="'.$appli.'&f_promotion=&f_formation=&f_pedagogie=&f_composante=&f_num_groupe=">'.get_string('tous','referentiel').'</option>'."\n";

        $i=0;
        while ($i<count($tnum_groupes)){
            if ($tnum_groupes[$i]){
                $s.='	<option value="'.$appli.'&f_num_groupe='.$tnum_groupes[$i].'&f_promotion=&f_pedagogie=&f_formation=&f_composante=">'.$tnum_groupes[$i].'</option>'."\n";
            }
            $i++;
	   }
    }
	$s.='</select>'."\n";
    $s.='</form>'."\n";
	$s.='</th>';
	$s.='<th width="'.$width.'">'.get_string('date_cloture','referentiel').'<br /></th>'."\n";
    $s.='<th width="30%">'.get_string('commentaire','referentiel').'</th>'."\n";
    $s.='<th width="5%">'.get_string('nbcertifs','referentiel').'</th>'."\n";
    $s.='</tr>'."\n";

	return $s;
}
}

/*
// ----------------------------------
function referentiel_pagination_users_pedago($course_id, $referentiel_instance_id, $referentiel_referentiel_id, $mode, $record_users, $record_pedagos){
// Selection des association user / pedagogie
// Les donnees sont enregistrees dans un cache qui utilise la table referentiel_course_users
$s='';
$MAXUSERPARPAGE=20;
// tableau N x M
    $s="";
    $t_users=array();
    $t_pedagos=array();
    $t_initiales=array();
    $nb_pedagos=0;
    $nb_users=0;

    if ($record_users){
        // DEBUG
		//echo "<br />Debug :: lib_pedagogie.php :: 1015 ::<br />\n";
		//print_object($record_users);

        foreach ($record_users as $record_u) {   // liste d'id users
            // DEBUG
            //echo "<br />Debug :: lib_pedagogie.php :: 1015 ::<br />\n";
            //print_object($record_u);

            $t_users[]= array('id' => $record_u->userid, 'lastname' => referentiel_get_user_nom($record_u->userid), 'firstname' => referentiel_get_user_prenom($record_u->userid));
            $t_users_id[]= $record_u->userid;
            $t_users_lastname[] = referentiel_get_user_nom($record_u->userid);
            $t_users_firstname[] = referentiel_get_user_prenom($record_u->userid);
        }
        array_multisort($t_users_lastname, SORT_ASC, $t_users_firstname, SORT_ASC, $t_users);

        $users_list=implode(',',$t_users_id);
        $nb_users=count($t_users);
    }

    if ($record_pedagos){
            foreach ($record_pedagos as $record_t) {   // liste d'id pedagos
                if ($record_t){
                    $t_pedagos[]=$record_t->id;
                }
            }
    }

    if ($t_users && $t_pedagos){
        // calculer l'indes par initiales
        for($i=0; $i<count($t_users); $i++){
            $lastname=$t_users[$i]['lastname'];
            $initiale=mb_strtoupper(substr($lastname,0,1),'UTF-8');
            // echo "<br />$lastname : $initiale\n";
            if ($t_initiales[$initiale]==-1){
                $t_initiales[$initiale]=$i;
            }
        }

        // DEBUG
// print_r($t_initiales);


        // sauvegarder les resultats dans le cache
        referentiel_enregistrer_cache($referentiel_referentiel_id, $course_id, serialize($t_users), serialize($t_initiales), serialize($t_pedagos));

        // affichage de 20 utilisateurs par page
        $ligmin=0;
        $ligmax=min($nb_users, $MAXUSERPARPAGE);
        return referentiel_select_users_pedago_cache($course_id, $referentiel_instance_id, $referentiel_referentiel_id, $mode, $ligmin, $ligmax);
    }
    return '';
}
*/

// ----------------------------------
function referentiel_pagination_users_pedago($course_id, $referentiel_instance_id, $referentiel_referentiel_id, $mode, $record_users, $record_pedagos){
// tableau U x 1 ou 1 x P
$s='';
$MAXUSERPARPAGE=20;
// tableau N x M
    $s="";
    $t_users=array();
    $t_pedagos=array();
    $nb_pedagos=0;
    $nb_users=0;
    $t_initiales=array('A' => -1, 'B' => -1, 'C' => -1, 'D' => -1, 'E' => -1, 'F' => -1, 'G' => -1, 'H' => -1, 'I' => -1,
        'J' => -1, 'K' => -1, 'L' => -1, 'M' => -1, 'N' => -1, 'O' => -1, 'P' => -1, 'Q' => -1, 'R' => -1, 'S' => -1,
        'T' => -1, 'U' => -1, 'V' => -1, 'W' => -1, 'X' => -1, 'Y' => -1, 'Z' => -1);

    if ($record_users){
        // DEBUG
		//echo "<br />Debug :: lib_pedagogie.php :: 1015 ::<br />\n";
		//print_object($record_users);

        foreach ($record_users as $record_u) {   // liste d'id users
            // DEBUG
            //echo "<br />Debug :: lib_pedagogie.php :: 1015 ::<br />\n";
            //print_object($record_u);

            $t_users[]= array('id' => $record_u->userid, 'lastname' => referentiel_get_user_nom($record_u->userid), 'firstname' => referentiel_get_user_prenom($record_u->userid));
            $t_users_id[]= $record_u->userid;
            $t_users_lastname[] = referentiel_get_user_nom($record_u->userid);
            $t_users_firstname[] = referentiel_get_user_prenom($record_u->userid);
        }
        array_multisort($t_users_lastname, SORT_ASC, $t_users_firstname, SORT_ASC, $t_users);

        $users_list=implode(',',$t_users_id);
        $nb_users=count($t_users);
    }

    if ($record_pedagos){
            foreach ($record_pedagos as $record_t) {   // liste d'id pedagos
                if ($record_t){
                    $t_pedagos[]=$record_t->id;
                }
            }
    }

    if ($t_users && $t_pedagos){
        // calculer l'indes par initiales
        for($i=0; $i<count($t_users); $i++){
            $lastname=$t_users[$i]['lastname'];
            $initiale=mb_strtoupper(substr($lastname,0,1),'UTF-8');
            // echo "<br />$lastname : $initiale\n";
            if ($t_initiales[$initiale]==-1){
                $t_initiales[$initiale]=$i;
            }
        }

        // DEBUG
// print_r($t_initiales);
/*
foreach ($t_initiales as $key => $value){
    if ($value!= -1) echo " $key: $value \n";
}
*/

        // sauvegarder les resultats dans le cache
        referentiel_enregistrer_cache($referentiel_referentiel_id, $course_id, serialize($t_users), serialize($t_initiales), serialize($t_pedagos));

        $ligmin=0;
        $ligmax=min($nb_users, $MAXUSERPARPAGE);
        return referentiel_select_users_pedago_cache($course_id, $referentiel_instance_id, $referentiel_referentiel_id, $mode, $ligmin, $ligmax);
    }
    return '';
}

// ----------------------------------
function referentiel_select_users_pedago_cache($course_id, $referentiel_instance_id, $referentiel_referentiel_id, $mode, $ligmin, $ligmax){
// tableau N x M
global $CFG;
$MAXUSERPARPAGE=20;
    $s="";
    $users_list='';
    $nb_pedagos=0;
    $nb_users=0;
    $nb_col=0;
    $nb_lig=0;
    $maxcol=8;
    $colwidth=(int)(100 / ($maxcol+1)).'%';

    $t_users=array();
    $t_initiales=array();
    $t_pedagos=array();

    // charger le cache
	$rec_cache=referentiel_lire_cache($referentiel_referentiel_id, $course_id);

    // DEBUG
    // print_object($rec_cache);

    if ($rec_cache){
        if (!empty($rec_cache->tab_users)){
            $t_users=unserialize($rec_cache->tab_users);
        }
        if (!empty($rec_cache->tab_initiales)){
            $t_initiales=unserialize($rec_cache->tab_initiales);
        }
        if (!empty($rec_cache->tab_pedagos)){
            $t_pedagos=unserialize($rec_cache->tab_pedagos);
        }
	}
    // DEBUG
    // print_r($t_users);
    // print_r($t_pedagos);


    if ($t_users){
        $nb_users=count($t_users);
        for ($i=$ligmin; $i<$ligmax; $i++){
            if (isset($t_users[$i])){
                $users_list.=$t_users[$i]['id'].',';
            }
        }
        // echo "<br />Debug :: lib_pedagogie.php :: 1097 ::<br />USERS_LIST : $users_list<br />\n";
		// print_r($t_users);


        $s.='<div align="center">'."\n";
        $s.='<h3>'.get_string('aide_association','referentiel').'</h3>'."\n";

        // calcul des deltas
        $delta=$ligmax-$ligmin;
        // initiales
        if ($t_initiales && $nb_users >= $MAXUSERPARPAGE){
            $s.='<div align="center">';
            foreach ($t_initiales as $key => $value){
                if ($value!= -1){
                    // echo " $key: $value \n";
                    $suivant= $value+ min($delta, $nb_users);
                    $s.=' <a href="'.$CFG->wwwroot.'/mod/referentiel/pedagogie.php?d='.$referentiel_instance_id.'&amp;mode=selectasso&ligmin='.$value.'&ligmax='.$suivant.'&amp;sesskey='.sesskey().'">'.$key.'</a> &nbsp;'."\n";
                }
            }
            $s.='</div>'."\n";
        }

        $lig_precedent=max(0,$ligmin-$delta);
        $lig_suivant=min($nb_users,$ligmax+$delta);

        if (($lig_precedent<$ligmin) || ($ligmax<$lig_suivant)){
            $s.='<div align="center">';
            if ($lig_precedent<$ligmin) {
                $s.=' <a href="'.$CFG->wwwroot.'/mod/referentiel/pedagogie.php?d='.$referentiel_instance_id.'&amp;mode=selectasso&ligmin='.$lig_precedent.'&ligmax='.$ligmin.'&amp;sesskey='.sesskey().'">'.get_string('precedent','referentiel').'</a> &nbsp;'."\n";
            }
            if ($ligmax<$lig_suivant) {
                $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/pedagogie.php?d='.$referentiel_instance_id.'&amp;mode=selectasso&ligmin='.$ligmax.'&ligmax='.$lig_suivant.'&amp;sesskey='.sesskey().'">'.get_string('suivant','referentiel').'</a>'."\n";
            }
            $s.='</div>'."\n";
        }

        $s.="\n".'<form name="form" method="post" action="pedagogie.php?d='.$referentiel_instance_id.'&amp;action=selectassociation&amp;mode='.$mode.'">'."\n";

        $s.='<div align="center">'."\n";
        // $s.='<input type="button" name="select_tous_pedagogies" id="select_tous_associations" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkall()" />'."\n";
        $s.='<input type="button" name="select_aucun_enseignant" id="select_aucun_association" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckall()" />'."\n";

        $s.="<br />\n";

        $s.='<input type="submit" value="'.get_string('savechanges').'" />'."\n";
        $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
        $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
        $s.='</div>'."\n";

        $pedagos_list=implode(',',$t_pedagos);
        $nb_pedagos=count($t_pedagos);
        $nb_lig=$nb_pedagos % $maxcol;

        $col=0;
        $lig=0;
        $s.='<table class="activite">'."\n";

        $j=0;
        $index_pedago_deb=0;
        $index_pedago_fin=0;
        while ($j<$nb_pedagos) {
            $index_pedago_fin++;
            if ($col==0){
           		$s.="<tr valign='top'><th align='left' width='".$colwidth."'>".get_string('eleves','referentiel').' \\ '.get_string('referent','referentiel')."</th>\n";
            }
            $s.="<th width='".$colwidth."'>\n";
            $s.="<b>".referentiel_get_formation_pedagogie($t_pedagos[$j])."</b>\n";
            $s.="</th>\n";
            // saut de ligne ?
            $col++;
            if ($col==$maxcol){
                $lig++;
                $col=0;
                $s.="</tr>\n";
                // eleves
                //for ($i=0; $i<$nb_users; $i++){
                for ($i=$ligmin; $i<$ligmax; $i++){
                    if (isset($t_users[$i])){
                        $s.="<tr valign='top'><td width='".$colwidth."'>\n";
                        $s.=$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
                        $s.="</td>";
                        $s.=referentiel_select_associations_user_pedagos($referentiel_referentiel_id, $t_users[$i]['id'], $t_pedagos, $index_pedago_deb, $index_pedago_fin, $colwidth);
                        $s.="</td></tr>\n";
                    }
                }
                $index_pedago_deb=$index_pedago_fin;
            }

            $j++;
        }
        if ($index_pedago_deb<$nb_pedagos){
            //for ($i=0; $i<$nb_users; $i++){
            for ($i=$ligmin; $i<$ligmax; $i++){
                if (isset($t_users[$i])){
                    $s.="<tr valign='top'><td width='".$colwidth."'>\n";
                    $s.=$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
                    $s.="</td>";
                    $s.=referentiel_select_associations_user_pedagos($referentiel_referentiel_id, $t_users[$i]['id'], $t_pedagos, $index_pedago_deb, $index_pedago_fin, $colwidth);
                    $s.="</td></tr>\n";
                }
            }
        }
        $nbcol=$nb_pedagos>$maxcol?$maxcol:$nb_pedagos;
        $nbcol++;
        $s.="<tr valign='top'><td align='center' colspan='".$nbcol."'>\n";
        $s.='<input type="submit" value="'.get_string('savechanges').'" />'."\n";
        $s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
        $s.='<input type="submit" name="cancel" value="'.get_string('quit', 'referentiel').'" />'."\n";
        $s.='
<input type="hidden" name="pedagos_list"  value="'.$pedagos_list.'" />
<input type="hidden" name="users_list"  value="'.$users_list.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="course"        value="'.$course_id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="editasso" />'."\n";

        $s.='</td></tr>';

        $s.='</table>'."\n";
        $s.='</form>'."\n";
        $s.='</div>'."\n";

	}
	return $s;
}


// gestion du cache des liste d'utilisateurs et de pedagogies
// facilite la selection



// ------------------
function referentiel_enregistrer_cache($refrefid, $courseid, $ser_t_users, $ser_t_initiales, $ser_t_pedagos){
// Enregistrer le contenu serialise du tableau des users et des pedagogies
// pour la selection des pedagogies pour le $referentiel_referentiel_id dans un fichier de cache
global $CFG;
global $DB;
    if ($refrefid && $courseid){
        // objet
        $rec_o = new object();
        $rec_o->courseid= $courseid;
        $rec_o->refrefid= $refrefid;
        $rec_o->tab_users= $ser_t_users;
        $rec_o->tab_initiales= $ser_t_initiales;
        $rec_o->tab_pedagos= $ser_t_pedagos;

        $rec=$DB->get_record('referentiel_course_users', array('courseid' => $courseid, 'refrefid' => $refrefid));

        if (!$rec){
            $rec_o->timestamp= time();
            return $DB->insert_record("referentiel_course_users", $rec_o);
        }
        else{
            // tester delai de sauvegarde
            if ($rec->timestamp < (time() - $CFG->maxeditingtime)){
                $rec_o->id= $rec->id;
                $rec_o->timestamp= time();
                return $DB->update_record("referentiel_course_users", $rec_o);
            }
        }
    }
    return false;
}

// ------------------
function referentiel_lire_cache($refrefid, $courseid){
global $DB;
    if ($refrefid && $courseid){
        return $DB->get_record('referentiel_course_users', array('courseid' => $courseid, 'refrefid' => $refrefid));
    }
    return NULL;
}


?>

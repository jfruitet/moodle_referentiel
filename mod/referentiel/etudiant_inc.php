<?php
/**
 * This page defines the form to create or edit an instance of this module
 * It is used from /mod/referentiel/etudiant.php.  The whole instance is available as $form.
 *
 * @author jf
 * @version $Id: etudiant_inc.php,v 1. 2013/05/04 09:012:00 jf Exp $
 * @package referentiel
 **/

if (isset($mode) && ($mode=="addetudiant")){
	// ajouter un etudiant
	if (!isset($form->userid)) {
    	$form->userid=$USER->id;
	}
	if (!isset($form->num_etudiant)){
		$form->num_etudiant = referentiel_get_student_number($form->userid);
	}
	if (!isset($form->ddn_etudiant)){
		$form->ddn_etudiant = "";
	}
	if (!isset($form->lieu_naissance)){
		$form->lieu_naissance = "";
	}
	if (!isset($form->departement_naissance)){
		$form->departement_naissance = "";
	}
	if (!isset($form->adresse_etudiant)){
		$form->adresse_etudiant = "";
	}
	if (!isset($form->ref_etablissement)){
		$form->ref_etablissement = 0;
	} 

?>
<h3><?php  print_string('creer_etudiant','referentiel') ?></h3>
<form name="form" method="post" action="<?php p("etudiant.php?d=$referentiel->id") ?>">

<table cellpadding="5">
<tr valign="top">
    <td align="right">
	<b><?php  print_string('num_etudiant','referentiel') ?>:</b>
	</td>
    <td align="left">
<?php
    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_profilefield)){  // Profil
        // profil non modifiable ici
        echo s($form->num_etudiant).' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="num_etudiant" value="'.s($form->num_etudiant).'" />'."\n";
    }
    else {
        echo '<input type="text" name="num_etudiant" size="20" maxlength="20" value="'.s($form->num_etudiant).'" />'."\n";
    }
?>
    </td>
</tr>
<tr valign="top">
    <td align="right">
	<b><?php  print_string('ddn_etudiant','referentiel') ?>:</b>
	</td>
    <td align="left">
<?php
    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_ddnfield)){  // Profil
        // profil non modifiable ici
        echo s($form->ddn_etudiant).' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="ddn_etudiant" value="'.s($form->ddn_etudiant).'" />'."\n";
    }
    else {
		echo '<input type="text" name="ddn_etudiant" size="14" maxlength="14" value="'.s($form->ddn_etudiant).'" />'."\n";
    }
?>
    </td>
</tr>
<tr valign="top">
    <td align="right">
	<b><?php  print_string('lieu_naissance','referentiel') ?>:</b>
	</td>
    <td align="left">
<?php
    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_ldnfield)){  // Profil
        // profil non modifiable ici
        echo s($form->lieu_naissance).' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="lieu_naissance" value="'.s($form->lieu_naissance).'" />'."\n";
    }
    else {
		echo '<input type="text" name="lieu_naissance" size="40" maxlength="255" value="'.s($form->lieu_naissance).'" />'."\n";
    }
?>
    </td>
</tr>
<tr valign="top">
    <td align="right">
	<b><?php  print_string('departement_naissance','referentiel') ?>:</b>
	</td>
    <td align="left">
<?php
    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_dptfield)){  // Profil
        // profil non modifiable ici
        echo s($form->departement_naissance).' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="departement_naissance" value="'.s($form->departement_naissance).'" />'."\n";
    }
    else {
		echo '<input type="text" name="departement_naissance" size="40" maxlength="255" value="'.s($form->departement_naissance).'" />'."\n";
    }
?>
    </td>
</tr>
<tr valign="top">
    <td align="right">
	<b><?php  print_string('adresse_etudiant','referentiel') ?>:</b>
	</td>
    <td align="left">
<?php
    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_adrfield)){  // Profil
        // profil non modifiable ici
        echo s($form->adresse_etudiant).' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="adresse_etudiant" value="'.s($form->adresse_etudiant).'" />'."\n";
    }
    else {
        echo '<input type="text" name="adresse_etudiant" size="60" maxlength="255" value="'.s($form->adress_etudiant).'" />'."\n";
    }
?>
    </td>
</tr>

</table>

<input type="hidden" name="ref_etablissement" value="<?php  p($form->ref_etablissement) ?>" />
<input type="hidden" name="userid" value="<?php  p($form->userid) ?>" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="etudiant_id"        value="<?php  p($form->etudiant_id) ?>" />
<input type="hidden" name="course"        value="<?php  p($form->course) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />



</form>


<?php

}
else if (isset($mode) && ($mode=="updateetudiant")){

	if (!isset($form->userid)) {
    	$form->userid=$USER->id;
	}

	if (!isset($form->sesskey)) {
    	$form->sesskey=sesskey();
	}
	

	// Charger les etudiants
    $context = context_module::instance($cm->id);

	$isteacher = has_capability('mod/referentiel:approve', $context);
	$isauthor = has_capability('mod/referentiel:write', $context) && !$isteacher;
	$iseditor = has_capability('mod/referentiel:writereferentiel', $context);	
	
	if ((!$isteacher) && (!$iseditor)){
			$userid=$USER->id; 
			$records_etudiant=referentiel_get_etudiant($userid);
	}
	else{
		$record_id_users=referentiel_select_liste_etudiants($initiale, $userids, $mode, $referentiel, $userid_filtre, $gusers, $select_acc);
		if ($record_id_users){
			foreach ($record_id_users as $un_user_id){
				// l'enregistrement existe-t-il ?
				// echo "<br />".$un_user_id->userid."\n";
				if ($updateprofile){
                    $re = referentiel_update_profile_student($un_user_id->userid);
                }
                else{
    				$re = $DB->get_record("referentiel_etudiant", array("userid" => "$un_user_id->userid"));
        		}
        		
        		if (!$re) {
                    if (referentiel_add_etudiant_user($un_user_id->userid)){
			     	   $re = $DB->get_record("referentiel_etudiant", array("userid" => $un_user_id->userid));
				    }
                }
				if ($re){
					$records_etudiant[]=$re;
				}
			}
		}
	}

	if (!$records_etudiant){
		print_error(get_string('noetudiant','referentiel'), "etudiant.php?d=$referentiel->id&amp;mode=add");
	}
	else if ($records_etudiant){
		// DEBUG
		// echo "<br/>DEBUG ::<br />\n";
		// print_object($records_etudiant);
        if ($updateprofile){
            referentiel_update_students_numbers($records_etudiant);
        }

        echo '<h3>'.get_string('modifier_etudiant','referentiel').'</h3>'."\n";

        // mise à jour des numeros d'etudiant
        if (!$updateprofile && !empty($CFG->ref_profilecategory) && !empty($CFG->ref_profilefield)){  // Profil
            echo '<div align="right"><a class="select" href="'.$CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id.'&amp;mode='.$mode.'&amp;updateprofile=1&amp;sesskey='.sesskey().'">'.get_string('regenere_profil','referentiel').'</a></div>'."\n";
        }

        echo '<form name="form" method="post" action="etudiant.php?id='.$cm->id.'">'."\n";
	    echo '<br />
 <img class="selectallarrow" src="'.$OUTPUT->pix_url('arrow_ltr_bas','referentiel').'" width="38" height="22" alt="Pour la sélection :" />
 <i>'.get_string('cocher_enregistrer_students', 'referentiel').'</i>
<input type="submit" value="'.get_string("savechanges").'" />
<input type="reset" value="'.get_string("corriger", "referentiel").'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel").'" />
<br />'."\n";

		foreach ($records_etudiant as $record){
			$etudiant_id=$record->id;
			$num_etudiant=$record->num_etudiant;
			$ddn_etudiant=$record->ddn_etudiant;
			$lieu_naissance=$record->lieu_naissance;
			$departement_naissance=$record->departement_naissance;
			$adresse_etudiant=$record->adresse_etudiant;
			$ref_etablissement=$record->ref_etablissement;
			$userid=$record->userid;
		
            $user_info=referentiel_get_user_info($record->userid);
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_object($record);
			$user_info=referentiel_get_user_info($userid);

			// AFFICHER etudiant
            echo '<hr><h3 align="center">'.get_string('student','referentiel').'</h3>
<input type="checkbox" name="tetudiant_id[]" id="tetudiant_id_'.$etudiant_id.'" value="'.$etudiant_id.'" />
<b>'.get_string('select_student','referentiel').'</b>
<br />'."\n";

            echo '
<table cellpadding="5" width="80%">
<tr valign="top">
    <td align="right">
	<b>'.get_string('nom_prenom','referentiel').':</b>
	</td>
    <td align="left">'.$user_info.'</td>
</tr>

<tr valign="top">
    <td align="right">
	<b>'.get_string('num_etudiant','referentiel').':</b>
	</td>
    <td align="left">
';
            if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_profilefield)){  // Profil
                // profil non modifiable ici
                echo $num_etudiant.' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
                echo '<input type="hidden" name="num_etudiant_'.$etudiant_id.'" value="'.$num_etudiant.'" />'."\n";
            }
            else {
                echo '<input type="text" name="num_etudiant_'.$etudiant_id.'" size="20" maxlength="20" value="'.$num_etudiant.'" onchange="return validerCheckBox(\'tetudiant_id_'.$etudiant_id.'\')" />'."\n";
            }
            echo '</td>
</tr>
<tr valign="top">
    <td align="right">
	<b>'.get_string('ddn_etudiant','referentiel').':</b>
	</td>
    <td align="left">
';

    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_ddnfield)){  // Profil
        // profil non modifiable ici
        echo $ddn_etudiant.' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="ddn_etudiant_'.$etudiant_id.'" value="'.$ddn_etudiant.'" />'."\n";
    }
    else {
		echo '<input type="text" name="ddn_etudiant_'.$etudiant_id.'" size="14" maxlength="14" value="'.$ddn_etudiant.'" onchange="return validerCheckBox(\'tetudiant_id_'.$etudiant_id.'\')" />'."\n";
    }
	echo '</td>
</tr>
<tr valign="top">
    <td align="right">
	<b>'.get_string('lieu_naissance','referentiel').':</b>
	</td>
    <td align="left">
';
    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_ldnfield)){  // Profil
        // profil non modifiable ici
        echo $lieu_naissance.' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="lieu_naissance_'.$etudiant_id.'" value="'.$lieu_naissance.'" />'."\n";
    }
    else {
		echo '<input type="text" name="lieu_naissance_'.$etudiant_id.'" size="40" maxlength="255" value="'.$lieu_naissance.'" onchange="return validerCheckBox(\'tetudiant_id_'.$etudiant_id.'\')" />'."\n";
    }
	echo '</td>
</tr>
<tr valign="top">
    <td align="right">
	<b>'.get_string('departement_naissance','referentiel').':</b>
	</td>
    <td align="left">
';
    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_dptfield)){  // Profil
        // profil non modifiable ici
        echo $departement_naissance.' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="departement_naissance_'.$etudiant_id.'" value="'.$departement_naissance.'" />'."\n";
    }
    else {
		echo '<input type="text" name="departement_naissance_'.$etudiant_id.'" size="40" maxlength="255" value="'.$departement_naissance.'" onchange="return validerCheckBox(\'tetudiant_id_'.$etudiant_id.'\')" />'."\n";
    }
	echo '</td>
</tr>
<tr valign="top">
    <td align="right">
	<b>'.get_string('adresse_etudiant','referentiel').':</b>
	</td>
    <td align="left">
';
    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_adrfield)){  // Profil
        // profil non modifiable ici
        echo $adresse_etudiant.' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="adresse_etudiant_'.$etudiant_id.'" value="'.$adresse_etudiant.'" />'."\n";
    }
    else {
        echo '<input type="text" name="adresse_etudiant_'.$etudiant_id.'" size="60" maxlength="255" value="'.$adresse_etudiant.'" onchange="return validerCheckBox(\'tetudiant_id_'.$etudiant_id.'\')" />'."\n";
    }
	echo '</td>
</tr>
</table>
';
            echo '<input type="hidden" name="userid_'.$etudiant_id.'" value="'.$userid.'" />
<input type="hidden" name="etudiant_id_'.$etudiant_id.'" value="'.$etudiant_id.'" />
<input type="hidden" name="ref_etablissement_'.$etudiant_id.'" value="'.$ref_etablissement.'" />'."\n";

            echo '
<!-- SUPPRESSION -->
<div align="right">
<span class="surligne"><a href="'.$CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id.'&amp;deleteid='.$userid.'&amp;confirm=1&amp;sesskey='.sesskey().'">'.get_string('delete_student','referentiel').'</a></span>
<span class="small"><i>'.get_string('deleteitemhelp','referentiel').'</i></span>
</div>'."\n";

		}
        echo '<br /><img class="selectallarrow" src="'.$OUTPUT->pix_url('arrow_ltr','referentiel').'" width="38" height="22" alt="Pour la sélection :" />
<i>'.get_string('cocher_enregistrer_students', 'referentiel').'</i>'."\n";

        echo  '<input type="hidden" name="action" value="modifglobaletudiant" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="course"        value="'.$course->id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />
';
        echo '
<input type="submit" value="'.get_string("savechanges").'" />
<input type="reset" value="'.get_string("corriger", "referentiel").'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel").'" />
</form>'."\n";
	}
}

?>
<?php
/**
 * This page defines the form to create or edit an instance of this module
 * It is used from /mod/referentiel/etudiant.php.  The whole instance is available as $form.
 *
 * @author jf
 * @version $Id: etudiant_edit_inc.php,v 1. 2013/05/04 09:012:00 jf Exp $
 * @package referentiel
 **/

 if (isset($mode) && ($mode=="updateetudiant")){

	// Charger les etudiants
    $context = context_module::instance($cm->id);

	$isteacher = has_capability('mod/referentiel:approve', $context);
	$isauthor = has_capability('mod/referentiel:write', $context) && !$isteacher;
	$iseditor = has_capability('mod/referentiel:writereferentiel', $context);	
	if ((!$isteacher) && (!$iseditor)){
		$userid=$USER->id; 
	}

	if ($record){ // initialise dans etudiant.php
		// DEBUG
		// echo "<br/>DEBUG :: etudiant_edit.html :: 24<br />\n";
		// print_object($record);

			$etudiant_id=$record->id;
			$num_etudiant=$record->num_etudiant;
			$ddn_etudiant=$record->ddn_etudiant;
			$lieu_naissance=$record->lieu_naissance;
			$departement_naissance=$record->departement_naissance;
			$adresse_etudiant=$record->adresse_etudiant;
			$ref_etablissement=$record->ref_etablissement;
			$userid=$record->userid;
		
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_object($record);
			$user_info=referentiel_get_user_info($userid);

			// AFFICHER etudiant
?>
<center>
<h3><?php  print_string('modifier_etudiant','referentiel') ?></h3>
<form name="form" method="post" action="<?php p("etudiant.php?d=$referentiel->id") ?>">
<table cellpadding="5" width="80%">
<tr valign="top">
    <td align="right">
	<b><?php  print_string('nom_prenom','referentiel') ?>:</b>
	</td>
    <td align="left">
<?php  p($user_info); ?>
    </td>
</tr>

<tr valign="top">
    <td align="right">
	<b><?php  print_string('num_etudiant','referentiel') ?>:</b>
	</td>
    <td align="left">
<?php
    if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_profilefield)){  // Profil
        // profil non modifiable ici
        echo $num_etudiant.' <i>'.get_string('profil_non_modifiable','referentiel').'</i>'."\n";
        echo '<input type="hidden" name="num_etudiant" value="'.$num_etudiant.'" />'."\n";
    }
    else {
        echo '<input type="text" name="num_etudiant" size="20" maxlength="20" value="'.$num_etudiant.'" />'."\n";
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

<input type="hidden" name="userid" value="<?php  p($userid) ?>" />
<input type="hidden" name="etudiant_id" value="<?php  p($etudiant_id) ?>" />
<input type="hidden" name="ref_etablissement" value="<?php  p($ref_etablissement) ?>" />

<input type="hidden" name="action" value="modifier_etudiant" />
<!-- These hidden variables are always the same -->

<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />

<input type="submit" name="delete" value="<?php  print_string("delete") ?>" />

</center>

</form>	
<?php
		
	}
}
else if (isset($mode) && ($mode=="deleteetudiant")){
	// DEBUG
	// echo "<br/>DEBUG :: etudiant_edit.html :: 129<br />\n";
	/// Confirmer la suppression d'un enregistrement
    if (!empty($userid)){
        echo $OUTPUT->confirm(get_string('confirmdeleterecord','referentiel'),
        $CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id.'&deleteid='.$userid.'&confirm=1&amp;sesskey='.sesskey(),
        $CFG->wwwroot.'/mod/referentiel/etudiant.php?id='.$cm->id);
	}
	else{
		print_error(get_string('noetudiant','referentiel'), "etudiant.php?id=$cm->id&amp;mode=listetudiant");
	}
}

?>
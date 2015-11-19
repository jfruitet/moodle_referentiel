<?php // $Id: edit.html,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
/**
 * This page defines the form to create or edit an instance of this module
 * It is used from /course/mod.php.  The whole instance is available as $form.
 *
 * @author jfruitet
 * @version $Id: mod.html,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
 * @package referentiel
 **/
 

	// demande de mot de passe
	if (!isset($form->referentiel_id)) {
    	$form->referentiel_id = $referentiel_referentiel->id;
	}
	if (!isset($form->instance) || ($form->instance=="")) {
    	$form->instance = $referentiel_referentiel->id;
	}
	
	if (!isset($form->referentiel_id)) {
		$form->instance = $referentiel_referentiel->id;		
	}
	if (!isset($form->name)) {
    	$form->name = $referentiel_referentiel->name;
	}
	if (!isset($form->code_referentiel)) {
    	$form->code_referentiel = $referentiel_referentiel->code_referentiel;
	}
	if (!isset($form->pass_referentiel)) {
    	$form->pass_referentiel = $referentiel_referentiel->pass_referentiel;
	}
	if (!isset($form->old_pass_referentiel)) {
    	$form->old_pass_referentiel = $referentiel_referentiel->pass_referentiel;
	}
	if (!isset($form->cle_referentiel)) {
    	$form->cle_referentiel = $referentiel_referentiel->cle_referentiel;
	}
	if (!isset($form->mail_auteur_referentiel)) {
    	$form->mail_auteur_referentiel = $referentiel_referentiel->mail_auteur_referentiel;
	}
	
	if (!isset($form->description_referentiel)) {
    	$form->description_referentiel = $referentiel_referentiel->description_referentiel;
	}
	if (!isset($form->url_referentiel)) {
    	$form->url_referentiel = $referentiel_referentiel->url_referentiel;
	}
	if (!isset($form->seuil_certificat)) {
    	$form->seuil_certificat = $referentiel_referentiel->seuil_certificat;
	}
	if (!isset($form->nb_domaines)) {
    	$form->nb_domaines = $referentiel_referentiel->nb_domaines;
	}
	if (!isset($form->liste_codes_competence)) {
    	$form->liste_codes_competence = $referentiel_referentiel->liste_codes_competence;
	}

	if (!isset($form->defaultsort)) {
    	$form->defaultsort = '';
	}
	if (!isset($form->defaultsortdir)) {
    	$form->defaultsortdir = '';
	}
	if (!isset($form->courseid)) {
    	$form->courseid = $course->id;
	}
	
	if (!isset($form->local)) {
    	$form->local = $referentiel_referentiel->local;
	}
	
	if (!isset($form->liste_empreintes_competence)) {
    	$form->liste_empreintes_competence = $referentiel_referentiel->liste_empreintes_competence;
	}

	if (!isset($form->logo_referentiel)) {
    	$form->logo_referentiel = $referentiel_referentiel->logo_referentiel;
	}

	if (!isset($form->sesskey)) {
    	$form->sesskey = sesskey();
	}
	if (!isset($form->mode)) {
    	$form->mode = "update";
	}
	
$str_enregistrer=get_string('savechanges');

?>

<form name="form" method="post" action="<?php p($appli_appelante."?d=".$referentiel->id."&checkpass=checkpass"); ?>">

<table cellpadding="5" align="center">
<tr valign="top">
    <td align="right"><b><?php  print_string('name','referentiel') ?>:</b></td>
    <td align="left">
        <?php  echo stripslashes($form->name) ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('code','referentiel') ?>:</b></td>
    <td align="left">
        <?php  echo stripslashes($form->code_referentiel) ?>
    </td>
</tr>
<?php
    // Administrateur ou auteur ?
    if (!empty($isadmin) || !empty($isreferentielauteur)) {
        echo '<tr valign="top">
    <td align="right"><b>'.get_string('pass_referentiel_admin','referentiel').' :</b></td>
    <td align="left">
        <input type="password" name="pass_referentiel" size="20" maxlength="20" value="" />
     <br /><i>'.get_string('existe_pass_referentiel','referentiel').'</i>
    </td>
</tr>
';
        echo '<input type="hidden" name="force_pass" value="'.$USER->id.'" />'."\n";
        $str_enregistrer=get_string('continue');
    }
    else{
echo '<tr valign="top">
    <td align="right"><b>'.get_string('pass_referentiel','referentiel').' :</b></td>
    <td align="left">
        <input type="password" name="pass_referentiel" size="20" maxlength="20" value="" />
'.get_string('check_pass_referentiel','referentiel').'
    </td>
</tr>
';
    }
?>
<tr valign="top">
<td colspan="2" align="center">
<input type="hidden" name="referentiel_id"      value="<?php  p($form->referentiel_id) ?>" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="mail_auteur_referentiel" value="<?php  p($form->mail_auteur_referentiel); ?>" />
<input type="hidden" name="old_pass_referentiel" value="<?php  p($form->old_pass_referentiel); ?>" />
<input type="hidden" name="cle_referentiel" value="<?php  p($form->cle_referentiel); ?>" />
<input type="hidden" name="liste_codes_competence" value="<?php  p($form->liste_codes_competence); ?>" />
<input type="hidden" name="liste_empreintes_competence" value="<?php  p($form->liste_empreintes_competence); ?>" />
<input type="hidden" name="sesskey"     value="<?php  p($form->sesskey) ?>" />
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"          value="<?php  p($form->mode) ?>" />
<input type="submit" value="<?php echo $str_enregistrer ?>" />
<!-- input type="reset" value="<?php  print_string("corriger", "referentiel") ?>" / -->
<input type="submit" name="cancel" value="<?php  print_string("quit", "referentiel") ?>" />
</td>
</tr>
</table>

</form>




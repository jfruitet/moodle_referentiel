<?php // $Id: edit.html,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
/**
 * This page defines the form to create or edit an occurrence of this module
 * It is used from /course/mod.php.  The whole instance is available as $form.
 *
 * @author jfruitet
 * @version $Id: mod.html,v 1.0 2013/04/29 00:00:00 jfruitet Exp $
 * @package referentiel
 **/

$email_user=referentiel_get_user_mail($USER->id);

// DEBUG
// echo "<br />EMAIL_USER : $email_user<br />\n";

/// First we check that form variables have been initialised
// instance
if (isset($referentiel) &&  ($referentiel) ){
	// referentiel referentiel
	if (isset($referentiel->ref_referentiel) && ($referentiel->ref_referentiel>0)){
		$referentiel_referentiel = $DB->get_record('referentiel_referentiel', array('id' => $referentiel->ref_referentiel));
	}
	if (!isset($form->occurrence)) {
    	$form->occurrence = $referentiel->ref_referentiel;
	}
	else {
    	$form->occurrence = '';
	}
}
else if (!isset($form->occurrence)) {
    	$form->occurrence = '';
}


if (isset($referentiel_referentiel) &&  ($referentiel_referentiel) ){
	// MISE A JOUR
	if (!isset($form->referentiel_id)) {
    	$form->referentiel_id = $referentiel_referentiel->id;
	}
	if (!isset($form->occurrence) || ($form->occurrence=="")) {
    	$form->occurrence = $referentiel_referentiel->id;
	}

	if (!isset($form->referentiel_id)) {
		$form->occurrence = $referentiel_referentiel->id;
	}
	if (!isset($form->name)) {
    	$form->name = $referentiel_referentiel->name;
	}
	if (!isset($form->code_referentiel)) {
    	$form->code_referentiel = $referentiel_referentiel->code_referentiel;
	}
	if (isset($referentiel_referentiel->pass_referentiel)) {
    	$form->pass_referentiel = $referentiel_referentiel->pass_referentiel;
		$form->old_pass_referentiel = $referentiel_referentiel->pass_referentiel;
		$form->suppression_pass_referentiel = 0;
	}
	else{
		$form->pass_referentiel = '';
		$form->old_pass_referentiel = '';
		$form->suppression_pass_referentiel = 0;
	}

	if (isset($referentiel_referentiel->cle_referentiel)) {
    	$form->cle_referentiel = $referentiel_referentiel->cle_referentiel;
	}
	else{
		$form->cle_referentiel='';
	}

	if (isset($referentiel_referentiel->mail_auteur_referentiel)
		 && ($referentiel_referentiel->mail_auteur_referentiel!='')) {
    	$form->mail_auteur_referentiel = $referentiel_referentiel->mail_auteur_referentiel;
	}
	else{
		$form->mail_auteur_referentiel ='';
	}

	if (!isset($form->description_referentiel)) {
    	$form->description_referentiel = $referentiel_referentiel->description_referentiel;
	}
	if (!isset($form->url_referentiel)) {
    	$form->url_referentiel = $referentiel_referentiel->url_referentiel;
	}
	if (!isset($form->seuil_certificat)) {
		// MODIF JF 2012/02/13
		// $form->seuil_certificat = $referentiel_referentiel->seuil_certificat;
		$form->seuil_certificat = referentiel_get_seuil_certification($referentiel_referentiel->id);
	}
// MODIF JF 2012/03/26
	if (!isset($form->minima_certificat)){
		$form->minima_certificat=referentiel_get_minima_certification($referentiel_referentiel->id);
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
	if (!isset($form->course)) {
	$form->course = $course->id;
	}

	if (!isset($form->local)) {
	$form->local = $referentiel_referentiel->local;
	}

	if (!isset($form->liste_empreintes_competence)) {
	$form->liste_empreintes_competence = $referentiel_referentiel->liste_empreintes_competence;
	}

	if (!isset($form->liste_poids_competence)) {
	$form->liste_poids_competence = $referentiel_referentiel->liste_poids_competence;
	}

	if (!isset($form->logo_referentiel)) {
	$form->logo_referentiel = $referentiel_referentiel->logo_referentiel;
	}

	if (!isset($form->sesskey)) {
	$form->sesskey = sesskey();
	}

// MODIF JF 2012/06/02
	if (!isset($form->label_domaine)) {
	$form->label_domaine = $referentiel_referentiel->label_domaine;
	}
	if (!isset($form->label_competence)) {
	$form->label_competence = $referentiel_referentiel->label_competence;
	}
	if (!isset($form->label_item)) {
	$form->label_item = $referentiel_referentiel->label_item;
	}

	if (!isset($form->mode)) {
	$form->mode = "update";
	}

}
else {
	// CREATION DE REFERENTIEL
	if (!isset($form->referentiel_id)) {
	$form->referentiel_id = '';
	}
	if (!isset($form->occurrence)) {
	$form->occurrence = '';
	}
	if (!isset($form->name)) {
	$form->name = "";
	}
	if (!isset($form->code_referentiel)) {
	$form->code_referentiel = "";
	}
	if (!isset($form->pass_referentiel)) {
	$form->pass_referentiel = "";
	}
	if (!isset($form->old_pass_referentiel)){
		$form->old_pass_referentiel='';
	}
	if (!isset($form->suppression_pass_referentiel)){
		$form->suppression_pass_referentiel = 0;
	}

	if (!isset($form->cle_referentiel)) {
	$form->cle_referentiel = "";
	}
	if (!isset($form->mail_auteur_referentiel)) {
	$form->mail_auteur_referentiel = "";
	}

	if (!isset($form->description_referentiel)) {
	$form->description_referentiel = "";
	}
	if (!isset($form->url_referentiel)) {
	$form->url_referentiel = "";
	}
	if (!isset($form->seuil_certificat)) {
	$form->seuil_certificat = 0.0;
	}
// MODIF JF 2012/03/26
	if (!isset($form->minima_certificat)){
		$form->minima_certificat=0;
	}
	if (!isset($form->nb_domaines)) {
	$form->nb_domaines = 1;
	}
	if (!isset($form->liste_codes_competence)) {
	$form->liste_codes_competence = "";
	}
	if (!isset($form->local)) {
	$form->local = 0; // referentiel global
	}
	if (!isset($form->liste_empreintes_competence)) {
	$form->liste_empreintes_competence = "";
	}

	if (!isset($form->liste_poids_competence)) {
	$form->liste_poids_competence = "";
	}

	if (!isset($form->logo_referentiel)) {
	$form->logo_referentiel = "";
	}

	if (!isset($form->sesskey)) {
	$form->sesskey = sesskey();
	}

// Modif JF 2012/06/02
if (!isset($form->label_domaine)){
		$form->label_domaine='';
	}
	if (!isset($form->label_competence)){
$form->label_competence='';
	}
	if (!isset($form->label_item)){
$form->label_item='';
	}

	if (!isset($form->mode)) {
	$form->mode = "add";
	}
}


// DEBUG
/*
echo "<br />DEBUG :: edit.html :: 199 :: REFERENTIEL <br />\n";
print_r($referentiel);
echo "<br />DEBUG :: edit.html :: 201 :: REFERENTIEL REFERENTIEL<br />\n";
print_r($referentiel_referentiel);

echo "<br />DEBUG :: edit.html :: 204 :: FORM <br />\n";
print_r($form);

echo "<br />DEBUG :: edit.html :: 207 :: PASS : $pass <br />\n";
*/

echo "\n".'<form name="form" method="post" action="edit.php?d='.$referentiel->id.'&amp;mode='.$form->mode.'&pass='.$pass.'&amp;sesskey='.sesskey().'"> '."\n";

echo '
<table cellpadding="5" align="center">
<tr valign="top">
<td align="right"><b>'.get_string('name','referentiel').':</b></td>
<td align="left">
<input type="text" name="name" size="60" maxlength="80" value="'.stripslashes(str_replace('"',"''", $form->name)).'" />
</td>
</tr>
<tr valign="top">
<td align="right"><b>'.get_string('code','referentiel').':</b></td>
<td align="left">
<input type="text" name="code_referentiel" size="20" maxlength="20" value="'.stripslashes($form->code_referentiel).'" />
</td>
</tr>
';
	if (isset($form->mail_auteur_referentiel) && ($form->mail_auteur_referentiel!='')){
		echo '
<tr valign="top">
<td align="right"><b>'.get_string('auteur','referentiel').' : </b></td>
<td align="left">
'.$form->mail_auteur_referentiel.'
</td>
</tr>
';
	}

	if (($form->pass_referentiel=='') && ($form->mail_auteur_referentiel=='')){ // nouveau referentiel
		echo '<tr valign="top">
<td align="right"><b>'.get_string('pass_referentiel','referentiel').' :</b></td>
<td align="left">
<input type="password" name="pass_referentiel" size="20" maxlength="20" value="" />
'.get_string('aide_pass_referentiel','referentiel').'
</td>
</tr>
';
	}
	else if (($form->mail_auteur_referentiel!='')
		&& (trim($email_user)==trim($form->mail_auteur_referentiel))) { // mise a jour
		echo '
<tr valign="top">
<td align="right"><b>';
		if ($form->pass_referentiel!=''){
			echo get_string('ressaisir_pass_referentiel','referentiel')."\n";
		}
		else {
			echo get_string('pass_referentiel','referentiel')."\n";
		}
		echo ' :</b></td>
<td align="left">
<input type="password" name="pass_referentiel" size="20" maxlength="20" value="" />  ';
		if (($form->pass_referentiel!='')){
			echo '<i>'.get_string('existe_pass_referentiel','referentiel').'</i>'."\n";
		}
		else{
			echo '<i>'.get_string('aide_pass_referentiel','referentiel').'</i>'."\n";
		}
		echo '</td>
</tr>
';

		if ($form->pass_referentiel!=''){
			echo '
<tr valign="top">
<td align="right"><b>';
			echo get_string('suppression_pass_referentiel','referentiel');
			echo ' :</b></td>
<td align="left">
';
			echo '<input type="radio" name="suppression_pass_referentiel" value="0" checked="checked" /> '.get_string('no').'<input type="radio" name="suppression_pass_referentiel" value="1" /> '.get_string('yes');
			echo '
</td>
</tr>
';
		}
	}
	else{
		echo '<input type="hidden" name="pass_referentiel" value="'.$form->pass_referentiel.'" />'."\n";
	}
	
echo '<tr valign="top">
<td align="right"><b>'.get_string('description','referentiel').':</b></td>
<td align="left">
		<textarea cols="60" rows="5" name="description_referentiel">'.stripslashes($form->description_referentiel).'</textarea>
</td>
</tr>
<tr valign="top">
<td align="right"><b>'.get_string('url','referentiel').':</b></td>
<td align="left">
<input type="text" name="url_referentiel" size="60" maxlength="255" value="'.$form->url_referentiel.'" />
</td>
</tr>
<tr valign="top">
<td align="right"><b>'.get_string('logo','referentiel').':</b></td>
<td align="left">
<input type="text" name="logo_referentiel" size="60" maxlength="255" value="'.$form->logo_referentiel.'" />
</td>
</tr>
';

// MODIF JF 2012/02/26
// gestion du protocole

echo '<input type="hidden" name="minima_certificat" value="'.s($form->minima_certificat).'" /> '."\n";

echo '<tr valign="top">
<td align="right"><b>'.get_string('minima_certificat','referentiel').':</b></td>
<td align="left">
';
echo '<i>'.s($form->minima_certificat).'</i>'."\n";
echo ' &nbsp; &nbsp; <a href="edit_protocole.php?d='.$referentiel->id.'&amp;mode='.$form->mode.'&pass='.$pass.'&amp;sesskey='.sesskey().'" target="_blank">'.get_string('gestion_protocole','referentiel').'</a>'."\n";
// helpbutton('protocole', 'referentiel', 'referentiel');
echo $OUTPUT->help_icon('protocolereferentielh','referentiel')."\n";


echo '</td>
</tr>
';

// MODIF JF 2012/02/17
// gestion du protocole
echo '<input type="hidden" name="seuil_certificat" value="'.s($form->seuil_certificat).'" /> '."\n";

echo '<tr valign="top">
<td align="right"><b>'.get_string('seuil_certificat','referentiel').':</b></td>
<td align="left">
';
//<input type="text" name="seuil_certificat" size="5" maxlength="10" value="'.s($form->seuil_certificat).'" />
echo '<i>'.s($form->seuil_certificat).'</i>'."\n";
echo ' &nbsp; &nbsp; <a href="edit_protocole.php?d='.$referentiel->id.'&amp;mode='.$form->mode.'&pass='.$pass.'&amp;sesskey='.sesskey().'" target="_blank">'.get_string('gestion_protocole','referentiel').'</a>'."\n";
// helpbutton('protocole', 'referentiel', 'referentiel');
echo $OUTPUT->help_icon('protocolereferentielh','referentiel')."\n";

echo '
</td>
</tr>
';

echo'<tr valign="top">
<td align="right"><b>'.get_string('referentiel_global','referentiel').':</b></td>
<td align="left">
';

if (isset($form->local)){
	if ($form->local!=0){ // si local == course_id le referentiel est local
		echo '<input type="radio" name="local" value="0" />'. get_string("yes").'
<input type="radio" name="local" value="'.$form->local.'" checked="checked" />'. get_string("no")."\n";
	}
	else{
		echo '<input type="radio" name="local" value="0" checked="checked" />'. get_string("yes").'
<input type="radio" name="local" value="'.$form->course.'" />'. get_string("no")."\n";
	}
}
else {
	echo '<input type="radio" name="local" value="0" checked="checked" />'. get_string("yes").'
<input type="radio" name="local" value="'.$form->course.'" />'. get_string("no")."\n";
}

echo '
</td>
</tr>
<tr valign="top">
<td align="right"><b>'.get_string('labels','referentiel').': </b></td>
<td align="left">'.get_string('labels_help','referentiel').'<br /><span class="small">'.get_string('labels_help2','referentiel').'</span></td>
</tr>

<tr valign="top">
<td align="right"><b>'.get_string('label_domaine_question','referentiel').' :</b></td>
<td align="left">
<input type="text" name="label_domaine" size="60" maxlength="80" value="'.stripslashes($form->label_domaine).'" />
</td>
</tr>
<tr valign="top">
<td align="right"><b>'.get_string('label_competence_question','referentiel').' :</b></td>
<td align="left">
<input type="text" name="label_competence" size="60" maxlength="80" value="'.stripslashes($form->label_competence).'" />
</td>
</tr>
<tr valign="top">
<td align="right"><b>'.get_string('label_item_question','referentiel').' :</b></td>
<td align="left">
<input type="text" name="label_item" size="60" maxlength="80" value="'.stripslashes($form->label_item).'" />
</td>
</tr> '."\n";
echo '
<tr valign="top">
<td align="right"><b>'.get_string('nombre_domaines_supplementaires','referentiel').':</b></td>
<td align="left">
<input type="text" name="nb_domaines" size="2" maxlength="2" value="'.$form->nb_domaines.'" />
'.get_string('ajouter_domaines','referentiel').'
</td>
</tr>
<tr valign="top">
<td colspan="2" align="center">
<input type="hidden" name="action" value="modifierreferentiel" />
<input type="hidden" name="referentiel_id"  value="'.$form->referentiel_id.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="mail_auteur_referentiel" value="'.$form->mail_auteur_referentiel.'" />
<input type="hidden" name="old_pass_referentiel" value="'.$form->old_pass_referentiel.'" />
<input type="hidden" name="cle_referentiel" value="'.$form->cle_referentiel.'" />
<input type="hidden" name="liste_codes_competence" value="'.stripslashes($form->liste_codes_competence).'" />
<input type="hidden" name="liste_empreintes_competence" value="'.$form->liste_empreintes_competence.'" />

<input type="hidden" name="liste_poids_competence" value="'.$form->liste_poids_competence.'" />
<input type="hidden" name="seuil_certificat" value="'.$form->seuil_certificat.'" />

<input type="hidden" name="sesskey" value="'.$form->sesskey.'" />
<input type="hidden" name="course"value="'.$form->course.'" />
<input type="hidden" name="occurrence"  value="'.$form->occurrence.'" />
<input type="hidden" name="mode"  value="'.$form->mode.'" />
<input type="submit" value="'.get_string("savechanges").'" />
<input type="reset" value="'.get_string("corriger", "referentiel").'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel").'" />
</td>
</tr>
</table>

</form>

<!-- DOMAINES -->
';

    echo '<div align="center"><h3><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('modifdomskillitemh','referentiel').' '.$OUTPUT->help_icon('modifdomskillitemh','referentiel').'</h3></div>'."\n";

	// charger les domaines associes au referentiel courant
	if (isset($form->referentiel_id) && ($form->referentiel_id>0)){
		$ref_referentiel=$form->referentiel_id; // plus pratique
		// Nombre de domaines � creer
		$objet_nb_domaines=referentiel_get_nb_domaines($ref_referentiel);
		$nb_domaines=$objet_nb_domaines->nb_domaines;
		// DEBUG
		// echo "<br/>DEBUG :: NOMBRE DE DOMAINES A AJOUTER : $nb_domaines <br />\n";


		// AFFICHER LA LISTE DES DOMAINES
		$compteur_nouveau_domaine=0;
		$compteur_nouveau_competence=0;
		$compteur_nouveau_item=0;

		$compteur_domaine=0;
		$records_domaine = referentiel_get_domaines($ref_referentiel);

	  if ($records_domaine){
		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($records_domaine);
			// echo "<br/>DEBUG ::<br />\n";

	// Modif jf 2013/04/26
	
	
	echo '<form name="form" method="post" action="edit.php?d='.$referentiel->id.'&pass='.$pass.'">'."\n";
	echo	'<br />
 <img class="selectallarrow" src="'.$OUTPUT->pix_url('arrow_ltr_bas','referentiel').'" width="38" height="22" alt="Pour la sélection :" />
 <i>'.get_string('cocher_enregistrer_domain', 'referentiel').'</i>
<input type="submit" value="'.get_string("savechanges").'" />
<input type="reset" value="'.get_string("corriger", "referentiel").'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel").'" />
<br />'."\n";


			foreach ($records_domaine as $record){
				$compteur_domaine++;
				$domaine_id=$record->id;
				$nb_competences = $record->nb_competences;
				$old_code_domaine = $record->code_domaine;
				$code_domaine = $record->code_domaine;
				$description_domaine = stripslashes($record->description_domaine);
				$num_domaine = $record->num_domaine;
				// MODIF JF 2012/02/20
				if (isset($record->type_domaine)){
					$type_domaine=$record->type_domaine;
				}
				else{
					$type_domaine=0;
				}
				if (isset($record->seuil_domaine)){
					$seuil_domaine=$record->seuil_domaine;
				}
				else{
					$seuil_domaine=0.0;
				}
				// MODIF JF 2012/03/26
				if (isset($record->minima_domaine)){
					$minima_domaine=$record->minima_domaine;
				}
				else{
					$minima_domaine=0;
				}
				// DEBUG
				// echo "<br/>DEBUG ::DOMAINE :: COMPTEUR : $compteur_domaine, DOMAINE_ID : $domaine_id, CODE : $code_domaine, DESCRIPTION : $description_domaine, NUM : $num_domaine, COMPETENCES : $nb_competences<br />\n";


echo '
<!-- DOMAINE -->
';

echo '<hr><h3 align="center">'.get_string('domaine','referentiel').'</h3>
<input type="checkbox" name="tdomaine_id[]" id="tdomaine_id_'.$domaine_id.'" value="'.$domaine_id.'" />
<b>'.get_string('select_domain','referentiel').'</b>
<br />'."\n";


echo '<table cellpadding="5" align="center">
<tr valign="top"	bgcolor="#ffffcc">
<td align="right"><b>'.get_string('id','referentiel').':</b></td>
<td align="left" > '.$domaine_id.'
</td>
</tr>
<tr valign="top"	bgcolor="#ffffcc">
<td align="right"><b>'.get_string('code','referentiel').':</b></td>
<td align="left" >
<input type="text" name="code_domaine_'.$domaine_id.'" size="20" maxlength="20" value="'.stripslashes($code_domaine).'" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />
<span class="small">'.get_string('code_unique','referentiel').'</span>'."\n"; 

echo '
</td>
</tr>
<tr valign="top"	bgcolor="#ffffcc">
<td align="right"><b>'.get_string('description','referentiel').':</b></td>
<td align="left" >
		<textarea cols="60" rows="5" name="description_domaine_'.$domaine_id.'" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')">'.stripslashes($record->description_domaine).'</textarea>
</td>
</tr>
<tr valign="top" bgcolor="#ffffcc">
<td align="right">
<b>'.get_string('type_domaine','referentiel').' : </b>
</td>
<td align="left" >
';
// MODIF JF 2012/02/20
if (!empty($type_domaine)){
echo get_string('yes'). '<input type="radio" name="type_domaine_'.$domaine_id.'" id="type_domaine_'.$domaine_id.'" value="1" checked="checked" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />'."\n";
echo get_string('no'). ' <input type="radio" name="type_domaine_'.$domaine_id.'" id="type_domaine_'.$domaine_id.'" value="0"  onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />'."\n";
}
else{
echo get_string('yes'). '<input type="radio" name="type_domaine_'.$domaine_id.'" id="type_domaine_'.$domaine_id.'" value="1" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />'."\n";
echo get_string('no'). '<input type="radio" name="type_domaine_'.$domaine_id.'" id="type_domaine_'.$domaine_id.'" value="0" checked="checked" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />'."\n";
}
echo '
</td>
</tr>
<tr valign="top"	bgcolor="#ffffcc">
<td align="right">
<b>'.get_string('minima_domaine','referentiel').' : </b>
</td>
<td align="left" >
';
// MODIF JF 2012/03/29
echo '<input type="text" name="minima_domaine_'.$domaine_id.'" size="5" maxlength="10" value="'.$minima_domaine.'" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />
</td>
</tr>
<tr valign="top"	bgcolor="#ffffcc">
<td align="right">
<b> '.get_string('seuil_domaine','referentiel').' : </b>
</td>
<td align="left" >
';

// MODIF JF 2012/02/20
echo '<input type="text" name="seuil_domaine_'.$domaine_id.'" size="5" maxlength="10" value="'.$seuil_domaine.'" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />'."\n";
echo '
</td>
</tr>
<tr valign="top" bgcolor="#ffffcc">
<td align="right">
<b>'.get_string('numero','referentiel').':</b>
</td>
<td align="left" >
<input type="text" name="num_domaine_'.$domaine_id.'" size="2" maxlength="2" value="'.$num_domaine.'" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />
</td>
</tr>
';

echo '<tr valign="top" bgcolor="#ffffcc">
<th align="right">
<i>'.get_string('nombre_competences_supplementaires','referentiel').'</i>:
</th>
<td align="left" >
<input type="text" name="nb_competences_'.$domaine_id.'" size="2" maxlength="2" value="'.$nb_competences.'" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" /> ';
echo '<span class="small">'.get_string('ajouter_competences','referentiel').'</span>'."\n";
echo '</td>';
echo '</tr>';

/*
echo '<input type="hidden" name="nb_competences_'.$domaine_id.'" value="'.$nb_competences.'" /> '."\n";
*/
echo '</table>
';
echo '
<!-- SUPPRESSION DOMAINE -->
<div align="right">
<span class="surligne"><a href="'.$CFG->wwwroot.'/mod/referentiel/edit.php?id='.$cm->id.'&amp;deleteid='.$domaine_id.'&amp;action=modifierdomaine&amp;delete='.get_string('delete').'&amp;pass='.$pass.'&amp;sesskey='.sesskey().'">'.get_string('delete_domain','referentiel').'</a></span>
<span class="small"><i>'.get_string('deletedomainhelp','referentiel').'</i></span>
</div>'."\n";


				// LISTE DES COMPETENCES DE CE DOMAINE
				$compteur_competence=0;
				$records_competences = referentiel_get_competences($domaine_id);

			if ($records_competences){
				// afficher
					// DEBUG
					// echo "<br/>DEBUG :: COMPETENCES <br />\n";
					// print_r($records_competences);

					foreach ($records_competences as $record_c){
						$compteur_competence++;
						$competence_id=$record_c->id;
						$nb_item_competences = $record_c->nb_item_competences;
						$old_code_competence= $record_c->code_competence;
						$code_competence = $record_c->code_competence;
						$description_competence = stripslashes($record_c->description_competence);
// MODIF JF 2012/02/20
						if (isset($record_c->type_competence)){
                            $type_competence=$record_c->type_competence;
						}
						else{
							$type_competence=0;
						}
						if (isset($record_c->seuil_competence)){
							$seuil_competence=$record_c->seuil_competence;
						}
						else{
							$seuil_competence=0.0;
						}
// MODIF JF 2012/03/29
						if (isset($record_c->minima_competence)){
							$minima_competence=$record_c->minima_competence;
						}
						else{
							$minima_competence=0.0;
						}

						$num_competence = $record_c->num_competence;
						$ref_domaine = $record_c->ref_domaine;


echo '<hr><h3 align="center">'.get_string('competence','referentiel').'</h3>
<input type="checkbox" name="tcompetence_id[]" id="tcompetence_id_'.$competence_id.'" value="'.$competence_id.'" />
<b>'.get_string('select_skill','referentiel').'</b>
</br />'."\n";

echo '<input type="hidden" name="ref_domaine_'.$competence_id.'" value="'.$ref_domaine.'" />'."\n";

?>
<!-- COMPETENCE -->
<table cellpadding="6" align="center">
<tr valign="top" bgcolor="#ccffff">
<td align="right">
<b><?php	print_string('id','referentiel') ?>:</b>
</td>
<td align="left">
<?php	p($competence_id) ?>
<input type="hidden" name="ref_domaine_'.$competence_id.'" value="<?php	p($ref_domaine) ?>" />
</td>
</tr>
<tr valign="top" bgcolor="#ccffff">
<td align="right">
<b><?php	print_string('code','referentiel') ?>:</b>
</td>
<td align="left">
<?php
echo '<input type="text" name="code_competence_'.$competence_id.'" size="20" maxlength="20" value="'.stripslashes($code_competence).'" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
echo '<span class="small">'.get_string('code_unique','referentiel').'</span>'."\n";
?>
</td>
</tr>

<tr valign="top" bgcolor="#ccffff">
<td align="right">
<b><?php	print_string('description','referentiel') ?>:</b>
</td>
<td align="left">
<?php
		echo '<textarea cols="60" rows="5" name="description_competence_'.$competence_id.'" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')">'.stripslashes($description_competence).'</textarea>'."\n";
?>
</td>
</tr>

<tr valign="top" bgcolor="#ccffff">
<td align="right">
<b> <?php print_string('type_competence','referentiel'); ?> : </b>
</td>
<td align="left">
<?php
// MODIF JF 2012/02/20
if (!empty($type_competence)){
echo get_string('yes'). '<input type="radio" name="type_competence_'.$competence_id.'" id="type_competence_'.$competence_id.'" value="1" checked="checked" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
echo get_string('no'). ' <input type="radio" name="type_competence_'.$competence_id.'" id="type_competence_'.$competence_id.'" value="0" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
}
else{
echo get_string('yes'). '<input type="radio" name="type_competence_'.$competence_id.'" id="type_competence_'.$competence_id.'" value="1" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
echo get_string('no'). '<input type="radio" name="type_competence_'.$competence_id.'" id="type_competence_'.$competence_id.'" value="0" checked="checked" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
}
?>
</td>
</tr>

<tr valign="top" bgcolor="#ccffff">
<td align="right">
<b> <?php print_string('minima_competence','referentiel'); ?> : </b>
</td>
<td align="left">
<?php
// MODIF JF 2012/03/29
echo '<input type="text" name="minima_competence_'.$competence_id.'" size="5" maxlength="10" value="'.$minima_competence.'" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
?>
</td>
</tr>

<tr valign="top" bgcolor="#ccffff">
<td align="right">
<b> <?php print_string('seuil_competence','referentiel'); ?> : </b>
</td>
<td align="left">
<?php
// MODIF JF 2012/02/20
echo '<input type="text" name="seuil_competence_'.$competence_id.'" size="5" maxlength="10" value="'.$seuil_competence.'" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
?>
</td>
</tr>


<tr valign="top" bgcolor="#ccffff">
<td align="right"><b><?php	print_string('numero','referentiel') ?>:</b></td>
<td align="left">
<?php
	echo '<input type="text" name="num_competence_'.$competence_id.'" size="2" maxlength="2" value="'.$num_competence.'" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
?>
</td>
</tr>

<?php
echo '<tr valign="top" bgcolor="#ccffff">
<td align="right"><i>'.get_string('nombre_item_competences_supplementaires','referentiel').'</i>:</td>
<td align="left">
<input type="text" name="nb_item_competences_'.$competence_id.'" size="2" maxlength="2" value="'.$nb_item_competences.'" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />
</td></tr>'."\n";

// echo '<input type="hidden" name="nb_item_competences_'.$competence_id.'" value="'.$nb_item_competences.'" />'."\n";
?>

</table>

<br />
<?php

echo '
<!-- SUPPRESSION COMPETENCE -->
<div align="right">
<span class="surligne"><a href="'.$CFG->wwwroot.'/mod/referentiel/edit.php?id='.$cm->id.'&amp;deleteid='.$competence_id.'&amp;action=modifiercompetence&amp;delete='.get_string('delete').'&amp;pass='.$pass.'&amp;sesskey='.sesskey().'">'.get_string('delete_skill','referentiel').'</a></span>
<span class="small"><i>'.get_string('deleteskillhelp','referentiel').'</span></i>
</div>'."\n";

						// ITEM
						// LISTE DES ITEMS DE CETTE COMPETENCES
					$compteur_item=0;
					$records_items = referentiel_get_item_competences($competence_id);

					if ($records_items){
						// afficher
							// DEBUG
							// echo "<br/>DEBUG :: ITEMS <br />\n";
							// print_r($records_items);
							foreach ($records_items as $record_i){
								if ($record_i){
									$compteur_item++;
									$item_id=$record_i->id;
									$oldcode=$record_i->code_item;
									$code_item = $record_i->code_item;
									$description_item = stripslashes($record_i->description_item);
									$num_item = $record_i->num_item;
									$type_item = $record_i->type_item;
									$poids_item = $record_i->poids_item;
									$empreinte_item = $record_i->empreinte_item;
									$ref_competence=$record_i->ref_competence;
									// DEBUG
									// echo "<br/>DEBUG 428 ::ITEM :: COMPTEUR : $compteur_item, ID : $item_id, CODE : $code_competence, DESCRIPTION : $description_competence, NUM : $num_item;<br />\n";
									// afficher le formulaire
echo '<hr><h3 align="center">'.get_string('item','referentiel').'</h3>
<input type="checkbox" name="titem_id[]" id="titem_id_'.$item_id.'" value="'.$item_id.'" />
<b>'.get_string('select_item','referentiel').'</b>
<br />'."\n";
echo '
<table cellpadding="5" align="center" bgcolor="#f0ffe0">
<tr valign="top">
<td class="item" align="right">
<b>'.get_string('id','referentiel').' :</b> 
</td>
<td class="item" align="left">
'.$item_id.'
<input type="hidden" name="ref_competence_'.$item_id.'" value="'.$ref_competence.'" />
</td>
</tr>
<tr valign="top">
<td class="item" align="right">
<b>'.get_string('code','referentiel').' :</b>
</td>
<td class="item" align="left">
<input type="text" name="code_item_'.$item_id.'" size="20" maxlength="20" value="'.$code_item.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')" />
<span class="small">'.get_string('code_unique','referentiel').'</span>'."\n";
echo '</td>
</tr>
<tr valign="top">
<td class="item" align="right">
<b>'.get_string('description','referentiel').' :</b>
</td>
<td class="item" align="left">
		<textarea cols="60" rows="5" name="description_item_'.$item_id.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')" >'.$description_item.'</textarea>
</td>
</tr>
<tr valign="top">
<td class="item" align="right">
<b>'.get_string('type_item','referentiel').' :</b>
</td>
<td class="item" align="left">
';
// MODIF JF 2012/02/20
if (!empty($type_item)){
echo get_string('yes').' <input type="radio" name="type_item_'.$item_id.'" id="type_item_'.$item_id.'" value="1" checked="checked" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />'."\n";
echo get_string('no').' <input type="radio" name="type_item_'.$item_id.'" id="type_item_'.$item_id.'" value="0" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />'."\n";
}
else{
echo get_string('yes').' <input type="radio" name="type_item_'.$item_id.'" id="type_item_'.$item_id.'" value="1" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />'."\n";
echo get_string('no').' <input type="radio" name="type_item_'.$item_id.'" id="type_item_'.$item_id.'" value="0" checked="checked" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />'."\n";
}

echo '</td>
</tr>
<tr valign="top">
<td class="item" align="right">
<b>'.get_string('poids_item','referentiel').' :</b>
</td>
<td class="item" align="left">
<input type="text" name="poids_item_'.$item_id.'" size="5" maxlength="10" value="'.$poids_item.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />
</td>
</tr>
<tr valign="top">
<td class="item" align="right">
<b>'.get_string('empreinte_item','referentiel').' :</b>
</td>
<td class="item" align="left">
<input type="text" name="empreinte_item_'.$item_id.'" size="3" maxlength="3" value="'.$empreinte_item.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />
</td>
</tr>
<tr valign="top">
<td class="item" align="right">
<b>'.get_string('numero','referentiel').' :</b>
</td>
<td class="item" align="left">
<input type="text" name="num_item_'.$item_id.'" size="2" maxlength="2" value="'.$num_item.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />
</td>
</tr>
</table>

<br />
';
echo '
<!-- SUPPRESSION ITEM -->
<div align="right">
<span class="surligne"><a href="'.$CFG->wwwroot.'/mod/referentiel/edit.php?id='.$cm->id.'&amp;deleteid='.$item_id.'&amp;action=modifieritem&amp;delete='.get_string('delete').'&amp;pass='.$pass.'&amp;sesskey='.sesskey().'">'.get_string('delete_item','referentiel').'</a></span>
<span class="small"><i>'.get_string('deleteitemhelp','referentiel').'</i></span>
</div>'."\n";

								}
							}
						}
						// NOMBRE DE NOUVEAUX ITEMS DE COMPETENCE DEMANDES
						$nb_items_a_afficher=$nb_item_competences-$compteur_item; // Tenir compte des items enregistres

						if (isset($nb_items_a_afficher) &&  ($nb_items_a_afficher>0)){
							// DEBUG
							// echo "<br/>DEBUG :: NOMBRE DE itemS A AJOUTER : $nb_items_a_afficher <br />\n";
							for ($k=0; $k<$nb_items_a_afficher; $k++){
							  $compteur_nouveau_item++;
								$code_item = '';
								$description_item = '';
								$compteur_item++;
								$num_item = $compteur_item;
								$type_item = 0;
								$poids_item = '1.0';
								$empreinte_item = '1';

echo '<hr><h3 align="center">'.get_string('saisie_item_supplementaire','referentiel').'</h3>
<b>'.get_string('new_item','referentiel').'</b> <input type="checkbox" name="tnewitem_id[]" id="tnewitem_id_'.$compteur_nouveau_item.'" value="'.$compteur_nouveau_item.'" />'."\n";

echo '
<table cellpadding="5" align="center" bgcolor="#ffaa99">
<tr valign="top" bgcolor="#ffaa99">
    <td  align="right">
        <b>'.get_string('code','referentiel').':</b>
    </td>
    <td  align="left">
        <input type="text" name="new_code_item_'.$compteur_nouveau_item.'" size="20" maxlength="20" value="'.$code_item.'" onchange="return validerCheckBox(\'tnewitem_id_'.$compteur_nouveau_item.'\')" />
<span class="small">'.get_string('code_unique','referentiel').'</span>
    </td>
</tr>
';
echo '
<tr valign="top" bgcolor="#ffaa99">
    <td  align="right">
        <b>'.get_string('description','referentiel').':</b>
    </td>
    <td  align="left">
		<textarea cols="60" rows="5" name="new_description_item_'.$compteur_nouveau_item.'" onchange="return validerCheckBox(\'tnewitem_id_'.$compteur_nouveau_item.'\')">'.$description_item.'</textarea>
    </td>
</tr>
<tr valign="top" bgcolor="#ffaa99">
    <td  align="right"><b>'.get_string('type_item','referentiel').':</b></td>
    <td  align="left">
';
    if (!empty($type_item)){
        echo get_string('yes').' <input type="radio" name="new_type_item_'.$compteur_nouveau_item.'" id="new_type_item" value="1" checked="checked" onchange="return validerCheckBox(\'tnewitem_id_'.$compteur_nouveau_item.'\')" />'."\n";
        echo get_string('no').'<input type="radio" name="new_type_item_'.$compteur_nouveau_item.'" id="new_type_item" value="0" onchange="return validerCheckBox(\'tnewitem_id_'.$compteur_nouveau_item.'\')" />'."\n";
    }
    else{
        echo get_string('yes').' <input type="radio" name="new_type_item_'.$compteur_nouveau_item.'" id="new_type_item" value="1" onchange="return validerCheckBox(\'tnewitem_id_'.$compteur_nouveau_item.'\')" />'."\n";
        echo get_string('no').'<input type="radio" name="new_type_item_'.$compteur_nouveau_item.'" id="new_type_item" value="0" checked="checked" onchange="return validerCheckBox(\'tnewitem_id_'.$compteur_nouveau_item.'\')" />'."\n";
    }
echo'
    </td>
</tr>

<tr valign="top" bgcolor="#ffaa99">
    <td  align="right"><b>'.get_string('poids_item','referentiel').':</b></td>
    <td  align="left">
        <input type="text" name="new_poids_item_'.$compteur_nouveau_item.'" size="5" maxlength="10" value="'.$poids_item.'" onchange="return validerCheckBox(\'tnewitem_id_'.$compteur_nouveau_item.'\')" />
    </td>
</tr>
<tr valign="top" bgcolor="#ffaa99">
    <td  align="right"><b>'.get_string('empreinte_item','referentiel').':</b></td>
    <td  align="left">
        <input type="text" name="new_empreinte_item_'.$compteur_nouveau_item.'" size="3" maxlength="3" value="'.$empreinte_item.'"  onchange="return validerCheckBox(\'tnewitem_id_'.$compteur_nouveau_item.'\')"/>
    </td>
</tr>
<tr valign="top" bgcolor="#ffaa99">
    <td  align="right">
        <b>'.get_string('numero','referentiel').':</b>
    </td>
    <td  align="left">
        <input type="text" name="new_num_item_'.$compteur_nouveau_item.'" size="2" maxlength="2" value="'.$num_item.'" onchange="return validerCheckBox(\'tnewitem_id_'.$compteur_nouveau_item.'\')" />
    </td>
</tr>
';
echo '<input type="hidden" name="new_ref_competence_'.$compteur_nouveau_item.'" value="'.$competence_id.'" />'."\n";

// MODIF JF 2012/03/08
echo '<input type="hidden" name="new_num_domaine_'.$compteur_nouveau_item.'" value="'.$num_domaine.'" />'."\n";
echo '<input type="hidden" name="new_num_competence_'.$compteur_nouveau_item.'" value="'.$num_competence.'" />
</table>
';

		echo'<div align="center">
<input type="submit" value="'.get_string("savechanges").'" />
<input type="reset" value="'.get_string("corriger", "referentiel").'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel").'" />
</div>
';

								}
							}
					}
				}

				// NOUVELLE COMPETENCE
				// NOMBRE DE NOUVELLES COMPETENCES DEMANDEES
				$nb_competences_a_afficher=$nb_competences-$compteur_competence; // Tenir compte des competences enregistres

				if (isset($nb_competences_a_afficher) &&  ($nb_competences_a_afficher>0)){
					for ($i=0; $i<$nb_competences_a_afficher; $i++){
						// DEBUG
						// echo "<br/>DEBUG :: NOMBRE DE COMPETENCES A AJOUTER : $nb_competences <br />\n";
						$compteur_nouveau_competence++;
						$nb_item_competences = '0';
						$code_competence = '';
						$description_competence = '';
						$type_competence=0;
            			$seuil_competence=0.0;
            			$minima_competence=0;

						$compteur_competence++;
						$num_competence = $compteur_competence;
						$ref_domaine=$domaine_id;
						

						echo '<hr><h3 align="center">'.get_string('saisie_competence_supplementaire','referentiel').'</h3>
<b>'.get_string('new_competence','referentiel').'</b> <input type="checkbox" name="tnewcompetence_id[]" id="tnewcompetence_id_'.$compteur_nouveau_competence.'" value="'.$compteur_nouveau_competence.'" />'."\n";

						echo '
<!-- COMPETENCE -->
<table cellpadding="5" align="center"  bgcolor="#ccffcc">

<tr valign="top" bgcolor="#ccffcc">
    <td align="right"><b>'.get_string('code','referentiel').':</b></td>
    <td align="left">
        <input type="text" name="new_code_competence_'.$compteur_nouveau_competence.'" size="20" maxlength="20" value="'.stripslashes($code_competence).'" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')" />
        <span class="small">'.get_string('code_unique','referentiel').'</span>'."\n";
						echo '
    </td>
</tr>
<tr valign="top" bgcolor="#ccffcc">
    <td align="right"><b>'.get_string('description','referentiel').':</b></td>
    <td align="left">
		<textarea cols="60" rows="5" name="new_description_competence_'.$compteur_nouveau_competence.'" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')">'.stripslashes($description_competence).'</textarea>
    </td>
</tr>
<tr valign="top"  bgcolor="#ccffcc">
    <td align="right"><b>'.get_string('type_competence','referentiel').':</b></td>
    <td align="left">
';
    				if (!empty($type_competence)){
        			 echo get_string('yes'). '<input type="radio" name="new_type_competence_'.$compteur_nouveau_competence.'" id="new_type_competence" value="1" checked="checked" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')" />'."\n";
        			 echo get_string('no'). '<input type="radio" name="new_type_competence_'.$compteur_nouveau_competence.'" id="new_type_competence" value="0" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')" />'."\n";
    				}
    				else{
        			 echo get_string('yes'). '<input type="radio" name="new_type_competence_'.$compteur_nouveau_competence.'" id="new_type_competence" value="1" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')" />'."\n";
        			 echo get_string('no'). '<input type="radio" name="new_type_competence_'.$compteur_nouveau_competence.'" id="new_type_competence" value="0" checked="checked" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')" />'."\n";
    				}
					echo '
    </td>
</tr>
<tr valign="top" bgcolor="#ccffcc">
    <td align="right"><b>'.get_string('minima_competence','referentiel').':</b> </td>
    <td align="left">';
    				echo '<input type="text" name="new_minima_competence_'.$compteur_nouveau_competence.'" size="5" maxlength="10" value="'.$minima_competence.'" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')" />'."\n";
					echo '</td>
</tr>
<tr valign="top" bgcolor="#ccffcc">
    <td align="right"><b>'.get_string('seuil_competence','referentiel').':</b> </td>
    <td align="left">
';
    				echo '<input type="text" name="new_seuil_competence_'.$compteur_nouveau_competence.'" size="5" maxlength="10" value="'.$seuil_competence.'" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')" />'."\n";
					echo '
    </td>
</tr>
<tr valign="top" bgcolor="#ccffcc">
    <td align="right"><b>'.get_string('numero','referentiel').':</b></td>
    <td align="left">
        <input type="text" name="new_num_competence_'.$compteur_nouveau_competence.'" size="2" maxlength="2" value="'.$num_competence.'" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')" />
    </td>
</tr>
<tr valign="top" bgcolor="#ccffcc">
    <td align="right"><i>'.get_string('nombre_item_competences_supplementaires','referentiel').'</i>:</td>
    <td align="left">
        <input type="text" name="new_nb_item_competences_'.$compteur_nouveau_competence.'" size="2" maxlength="2" value="'.$nb_item_competences.'" onchange="return validerCheckBox(\'tnewcompetence_id_'.$compteur_nouveau_competence.'\')" />
    </td>
</tr>
</table>
';
					echo '<input type="hidden" name="new_num_domaine_'.$compteur_nouveau_competence.'" value="'.$num_domaine.'" />
<input type="hidden" name="new_ref_domaine_'.$compteur_nouveau_competence.'" value="'.$ref_domaine.'" />'."\n";
					echo'<div align="center">
<input type="submit" value="'.get_string("savechanges").'" />
<input type="reset" value="'.get_string("corriger", "referentiel").'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel").'" />
</div>
';

      }
		}
	}
		
}
  		// NOMBRE DE NOUVEAUX DOMAINES DEMANDES
		$nb_domaines_a_afficher=$nb_domaines-$compteur_domaine; // Tenir compte des domaines enregistres
		if (isset($nb_domaines_a_afficher) &&  ($nb_domaines_a_afficher>0)){
			for ($j=0; $j<$nb_domaines_a_afficher; $j++){
				$compteur_nouveau_domaine++;
				$code_domaine = '';
				$description_domaine = '';
                $type_domaine=0;
                $seuil_domaine=0.0;
                $minima_domaine=0;
				$compteur_domaine++;
				$num_domaine = $compteur_domaine;
				// DEBUG
				// echo "<br/>DEBUG ::COMPTEUR : $compteur_domaine<br />\n";

				echo '<hr><h3 align="center">'.get_string('saisie_domaine_supplementaire','referentiel').'</h3>
<b>'.get_string('new_domaine','referentiel').'</b> <input type="checkbox" name="tnewdomaine_id[]" id="tnewdomaine_id_'.$compteur_nouveau_domaine.'" value="'.$compteur_nouveau_domaine.'" />'."\n";


echo '
<!-- DOMAINE -->

<table cellpadding="5" align="center" bgcolor="#ffdddd">
<tr valign="top">
    <td align="right"><b>'.get_string('code','referentiel').':</b></td>
    <td align="left">
        <input type="text" name="new_code_domaine_'.$compteur_nouveau_domaine.'" size="20" maxlength="20" value="'.stripslashes($code_domaine).'" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')" />
				<span class="small">'.get_string('code_unique','referentiel').'</span>'."\n";

echo '
    </td>
</tr>
<tr valign="top">
    <td align="right"><b>'.get_string('description','referentiel').':</b></td>
    <td align="left">
		<textarea cols="60" rows="5" name="new_description_domaine_'.$compteur_nouveau_domaine.'" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')" >'.stripslashes($description_domaine).'</textarea>
    </td>
</tr>
<tr valign="top">
    <td align="right">
        <b>'.get_string('type_domaine','referentiel').':</b>
    </td>
    <td align="left">
';
    if (!empty($type_domaine)){
        echo get_string('yes'). '<input type="radio" name="new_type_domaine_'.$compteur_nouveau_domaine.'" id="new_type_domaine" value="1" checked="checked" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')"  />'."\n";
        echo get_string('no'). '<input type="radio" name="new_type_domaine_'.$compteur_nouveau_domaine.'" id="new_type_domaine" value="0" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')"  />'."\n";
    }
    else{
        echo get_string('yes'). '<input type="radio" name="new_type_domaine_'.$compteur_nouveau_domaine.'" id="new_type_domaine" value="1" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')"  />'."\n";
        echo get_string('no'). '<input type="radio" name="new_type_domaine_'.$compteur_nouveau_domaine.'" id="new_type_domaine" value="0" checked="checked" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')"  />'."\n";
    }
echo '
    </td>
</tr>
<tr valign="top">
    <td align="right">
        <b>'.get_string('minima_domaine','referentiel').':</b>
    </td>
    <td align="left">
';
    echo '<input type="text" name="new_minima_domaine_'.$compteur_nouveau_domaine.'" size="5" maxlength="10" value="'.$minima_domaine.'" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')"  />'."\n";

echo'
    </td>
</tr>
<tr valign="top">
    <td align="right"><b>'.get_string('seuil_domaine','referentiel').':</b> </td>
    <td align="left">
';
    echo '<input type="text" name="new_seuil_domaine_'.$compteur_nouveau_domaine.'" size="5" maxlength="10" value="'.$seuil_domaine.'" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')"  />'."\n";
	echo '
    </td>
</tr>

<tr valign="top">
    <td align="right"><b>'.get_string('numero','referentiel').':</b></td>
    <td align="left">
        <input type="text" name="new_num_domaine_'.$compteur_nouveau_domaine.'" size="2" maxlength="2" value="'.$num_domaine.'" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')"  />
    </td>
</tr>
<tr valign="top">
    <td align="right"><i>'.get_string('nombre_competences_supplementaires','referentiel').'</i>:</td>
    <td align="left">
        <input type="text" name="new_nb_competence_domaine_'.$compteur_nouveau_domaine.'" size="2" maxlength="2" value="0" onchange="return validerCheckBox(\'tnewdomaine_id_'.$compteur_nouveau_domaine.'\')"  />
    </td>
</tr>
</table>
';

}
}
		echo '<br /><img class="selectallarrow" src="'.$OUTPUT->pix_url('arrow_ltr','referentiel').'" width="38" height="22" alt="Pour la sélection :" />
<i>'.get_string('cocher_enregistrer_domain', 'referentiel').'</i>'."\n";

		echo'
<input type="hidden" name="action" value="modifierdomcompitems" />
<input type="hidden" name="referentiel_id"	value='.$form->referentiel_id.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="mail_auteur_referentiel" value="'.$form->mail_auteur_referentiel.'" />
<input type="hidden" name="liste_codes_competence" value="'.stripslashes($form->liste_codes_competence).'" />
<input type="hidden" name="liste_empreintes_competence" value="'.$form->liste_empreintes_competence.'" />
<input type="hidden" name="liste_poids_competence" value="'.$form->liste_poids_competence.'" />
<input type="hidden" name="referentiel_id"	value="'.$form->referentiel_id.'" />
<!-- These hidden variables are always the same -->

<input type="hidden" name="sesskey" value="'.$form->sesskey.'" />
<input type="hidden" name="course"value="'.$form->course.'" />
<input type="hidden" name="occurrence"	value="'.$form->occurrence.'" />
<input type="hidden" name="mode"value="'.$form->mode.'" />
<input type="submit" value="'.get_string("savechanges").'" />
<input type="reset" value="'.get_string("corriger", "referentiel").'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel").'" />
</form>
';
}
?>

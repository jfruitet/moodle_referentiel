<?php // $Id activite.html,v 1 2008-2011 JF Exp $
/**
 * This page defines the form to create or edit an instance of this module
 * It is used from /activite.php.
 *
 * @author
 * @version $Id activite.html,v1 2008-2011 JF Exp $
 * @package referentiel
 **/
 
// DEBUG
// echo "<br />MODE : $mode\n";

if (isset($mode) && ($mode=="addactivity")){
	// ajouter une activite
	if (!isset($form->instance)) {
    	$form->instance = $referentiel->id;
	}
	if (!isset($form->ref_referentiel)) {
    	$form->ref_referentiel = $referentiel_referentiel->id;
	}
	if (!isset($form->courseid)) {
    	$form->courseid = $course->id;
	}	
	if (!isset($form->type_activite)) {
    	$form->type_activite = '';
	}
	if (!isset($form->description_activite)) {
    	$form->description_activite = '';
	}
	if (!isset($form->competences_activite)) {
    	$form->competences_activite = referentiel_get_liste_codes_competence($referentiel_referentiel->id);
	}
	if (!isset($form->commentaire_activite)) {
    	$form->commentaire_activite = '';
	}
	if (!isset($form->approved)) {
    	$form->approved=0;
	}	
	if (!isset($form->userid)) {
    	$form->userid=$USER->id;
	}
	if (!isset($form->teacherid)) {
    	$form->teacherid='';
	}
	
	if (!isset($form->activite_id)) {
		if (isset($activite_id))
			$form->activite_id=$activite_id;
		else
			$form->activite_id='';
	}
	if (!isset($form->description_document)) {
    	$form->description_document = '';
	}
	if (!isset($form->type_document)) {
    	$form->type_document = '';
	}
	if (!isset($form->url_document)) {
    	$form->url_document = '';
	}
	if (!isset($form->courseid_id)) {
    	$form->courseid = $course->id;
	}		
	if (!isset($form->sesskey)) {
    	$form->sesskey=sesskey();
	}
	if (!isset($form->modulename)) {
    	$form->modulename='referentiel';
	}
	if (!isset($form->instance)) {
    	$form->instance=$referentiel->id;
	}
 	// preparer les variables globales pour Overlib
	referentiel_initialise_data_referentiel($referentiel_referentiel->id);
?>


<?php  

if (has_capability('mod/referentiel:managecertif', $context)) { // enseignant
	echo '<p><i>'.get_string('creer_activite_teacher','referentiel').'</i></p>'."\n";
}

$jauge_activite_declarees=referentiel_print_jauge_activite($USER->id, $referentiel_referentiel->id);
if ($jauge_activite_declarees){
	print_string('competences_declarees','referentiel', referentiel_get_user_info($USER->id));
	//echo '<br />DEBUT DEBUG'."\n";
	echo $jauge_activite_declarees."\n";
	//echo '<br />FIN DEBUG'."\n";
}

echo '<form name="form" method="post" action="activite.php?d='.$referentiel->id.'"> '."\n";
echo '<h3 align="center"><br />'.get_string('declarer_activite','referentiel').'</h3>'."\n";
echo '<div class="ref_saisie1">'."\n";
echo '<span class="bold">'.get_string('type_activite','referentiel').'</span>';
echo '<br /><input type="text" name="type_activite" size="80" maxlength="80" value="'.$form->type_activite.'" /> ';
echo '<br />'."\n";
echo '<span class="bold">'.get_string('description','referentiel').'</span>';
echo '<br /> ';
echo '<textarea cols="100" rows="10" name="description_activite">'.s($form->description_activite).'</textarea>';
echo '<br /><br />'."\n";
echo '<span class="bold">'.get_string('aide_saisie_competences','referentiel').'</span>'."\n";
echo '<br />'."\n";
// MODIF JF 2013/10/03
/*
if (!referentiel_hierarchical_display($referentiel->id)){
	echo referentiel_modifier_selection_liste_codes_item_competence('/', $form->competences_activite, $form->competences_activite);
}
else{
	echo referentiel_modifier_selection_codes_item_hierarchique($referentiel_referentiel->id, $form->competences_activite, true);
}
*/
if (!referentiel_hierarchical_display($referentiel->id)){
    echo referentiel_selection_liste_codes_item_competence('/',$form->competences_activite);
}
else{
    echo '<br />'.referentiel_selection_liste_codes_item_hierarchique($referentiel_referentiel->id, true)."\n";
}

echo '<br />'."\n";
echo '<br />'."\n";
echo '	<span class="bold">'.get_string('depot_document','referentiel').'</span>'."\n";
echo '&nbsp; &nbsp; <input type="radio" name="depot_document" value="'.get_string('yes').'"/>'.get_string('yes')."\n";
echo '&nbsp; &nbsp; <input type="radio" name="depot_document" value="'.get_string('no').'" checked="checked" />'.get_string('no');
echo '<br />  <span class="bold">'.get_string('notification_activite','referentiel').'</span>';
echo '&nbsp; &nbsp; <input type="radio" name="mailnow" value="1" />'.get_string('yes').' &nbsp; &nbsp; <input type="radio" name="mailnow" value="0" checked="checked" />'.get_string('no').' &nbsp; &nbsp; '."\n";
echo '<br />'."\n";
?>

<input type="hidden" name="action"  value="ajouter_activite" />
<input type="hidden" name="select_acc" value="<?php echo $select_acc; ?>" />
<input type="hidden" name="commentaire_activite" value="<?php  p($form->commentaire_activite) ?>" />
<input type="hidden" name="approved" value="<?php  p($form->approved) ?>" />
<input type="hidden" name="userid" value="<?php  p($form->userid) ?>" />
<input type="hidden" name="teacherid" value="<?php  p($form->teacherid) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($form->ref_referentiel) ?>" />

<!-- Ajout pour les filtres -->
<input type="hidden" name="f_auteur" value="<?php  p($data_f->f_auteur) ?>" />
<input type="hidden" name="f_validation" value="<?php  p($data_f->f_validation) ?>" />
<input type="hidden" name="f_referent" value="<?php  p($data_f->f_referent) ?>" />
<input type="hidden" name="f_date_modif" value="<?php  p($data_f->f_date_modif) ?>" />
<input type="hidden" name="f_date_modif_student" value="<?php  p($data_f->f_date_modif_student) ?>" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="activite_id"        value="<?php  p($form->activite_id) ?>" />
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="modulename"    value="<?php  p($form->modulename) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"      value="<?php p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
<input type="submit" name="cancel" value="<?php  print_string("quit","referentiel") ?>" />

</form>

<?php
    echo '</div>'."\n";
}
?>
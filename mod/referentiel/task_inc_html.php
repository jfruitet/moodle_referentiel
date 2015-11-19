<?php // $Id: mod.html,v 1.5 2006/10/07 12:28:57 gustav_delius Exp $
/**
 * This page defines the form to create or edit an instance of this module
 * It is used from /course/mod.php.  The whole instance is available as $form.
 *
 * @author jfruitet
 * @version $Id: mod.html,v 1.5 2006/10/07 12:28:57 gustav_delius Exp $
 * @package referentiel
 **/
 /*
 CREATE TABLE IF NOT EXISTS mdl_referentiel_task (
  id bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  type_task varchar(80) NOT NULL DEFAULT '',
  description_task text NOT NULL,
  competences_task text NOT NULL,
  criteres_evaluation text NOT NULL,
  ref_instance bigint(10) unsigned NOT NULL DEFAULT '0',
  ref_referentiel bigint(10) unsigned NOT NULL DEFAULT '0',
  ref_course bigint(10) unsigned NOT NULL DEFAULT '0',
  auteurid bigint(10) unsigned NOT NULL,
  date_creation bigint(10) unsigned NOT NULL DEFAULT '0',
  date_modif bigint(10) unsigned NOT NULL DEFAULT '0',
  date_debut bigint(10) unsigned NOT NULL DEFAULT '0',
  date_fin bigint(10) unsigned NOT NULL DEFAULT '0',

	$task->type_task=($form->type_task);
	$task->description_task=($form->description_task);
	$task->competences_task=reference_conversion_code_2_liste_competence('/', $form->code_item);
	$task->criteres_evaluation=($form->criteres_evaluation);
	$task->ref_instance=$form->instance;
	$task->ref_referentiel=$form->ref_referentiel;
	$task->ref_course=$form->courseid;
	$task->auteurid=$USER->id;		
	$task->date_creation=time();
	$task->date_modif=time();
  	$task->date_debut=mktime($form->date_debut->heure, $form->date_debut->mois, $form->date_debut->jour,$form->date_debut->annee);
	$task->date_fin=mktime($form->date_fin->heure, $form->date_fin->mois, $form->date_fin->jour,$form->date_fin->annee);

 */
 
// DEBUG
// echo "<br />MODE : $mode\n";
if (isset($mode) && ($mode=="addtask")){
	// ajouter une task
	if (!isset($form->instance)) {
    	$form->instance = $referentiel->id;
	}
	if (!isset($form->ref_referentiel)) {
    	$form->ref_referentiel = $referentiel_referentiel->id;
	}
	if (!isset($form->courseid)) {
    	$form->courseid = $course->id;
	}	
	if (!isset($form->type_task)) {
    	$form->type_task = '';
	}
	if (!isset($form->description_task)) {
    	$form->description_task = '';
	}
	if (!isset($form->competences_task)) {
    	$form->competences_task = referentiel_get_liste_codes_competence($referentiel_referentiel->id);
	}
	if (!isset($form->criteres_evaluation)) {
    	$form->criteres_evaluation = '';
	}

    if (!isset($form->auteurid)) {
    	$form->auteurid=$USER->id;
	}

	if (!isset($form->date_debut)){
		$date_debut=date("d/m/Y H:i");
	}
	else{
		$date_debut=date("d/m/Y H:i", $form->date_debut);	
	} 
	if (!isset($form->date_fin)){
		$date=strtotime('+4 weeks');
		$date_fin = date("d/m/Y H:i", $date);
		// echo "<br />DEBUG :: task.html :: 79 :: $date :: $date_fin\n";
		// exit; 
	}
	else{
		$date_fin=date("d/m/Y H:i", $form->date_fin);
	}
	
	
	if (!isset($form->taskid)) {
		if (isset($taskid))
			$form->taskid=$taskid;
		else
			$form->taskid='';
	}

	// consignes
	if (!isset($form->description_consigne)) {
    	$form->description_consigne = '';
	}
	if (!isset($form->type_consigne)) {
    	$form->type_consigne = '';
	}
	if (!isset($form->url_consigne)) {
    	$form->url_consigne = '';
	}
	if (!isset($form->souscription_libre)) {
        $souscription_libre = '1';
	}		
    if (!isset($form->cle_souscription)){
        $cle_souscription = '';
    }
    if (!isset($form->tache_masquee)){
        $tache_masquee = 0;
    }


	if (!isset($form->sesskey)) {
    	$form->sesskey=sesskey();
	}
	if (!isset($form->modulename)) {
    	$form->modulename='referentiel';
	}
	// preparer les variables globales pour Overlib
	referentiel_initialise_data_referentiel($referentiel_referentiel->id);

	// saisie date
	echo "\n".'<script type="text/javascript" src="dhtmlgoodies_calendar.js"></script>'."\n";
?>
<h3 align="center"><?php  print_string('creer_task','referentiel'); ?></h3>
<div align="center">
<form name="form" method="post" action="<?php p("task.php?d=$referentiel->id") ?>">
<table cellpadding="5" align="center">
<tr valign="top">
	<td align="left" colspan="2" rowspan="3" width="30%">
	<b><?php  print_string('date_debut','referentiel') ?>:</b>
	<br />
	<input type="text" value="<?php echo $date_debut; ?>" name="date_debut" />
	<input type="button" value="Cal" onclick="displayCalendar(this.form.date_debut,'dd/mm/yyyy hh:ii',this, true,'','fr')" />
<!-- 
	<input type="button" value="Show 'date_debut' hidden field value" onClick="alert(this.form.date_debut.value)" />
-->

	<br /><br /><br /><br /><br /><br /><br />
	<b><?php  print_string('date_fin','referentiel') ?>:</b>
	<br />
	<input type="text" value="<?php echo $date_fin; ?>" name="date_fin" />
	<input type="button" value="Cal" onclick="displayCalendar(this.form.date_fin,'dd/mm/yyyy hh:ii',this, true,'','fr')" />
<!-- 
	<input type="button" value="Show 'date_fin' hidden field value" onClick="alert(this.form.date_fin.value)" />
-->
	</td>
    <td align="left" colspan="2" >
	<b><?php  print_string('type_task','referentiel') ?>:</b>
	<br />
<input type="text" name="type_task" size="80" maxlength="80" value="<?php  p($form->type_task) ?>" />
    </td>
</tr>
<tr valign="top">
    <td  align="left" colspan="2">
	<b><?php  print_string('description','referentiel') ?>:</b>
	<br />
<textarea cols="80" rows="8" name="description_task"><?php  p($form->description_task) ?></textarea>
    </td>
</tr>
<tr valign="top">
    <td  align="left" colspan="2">
	<b><?php  print_string('aide_saisie_competences','referentiel') ?>:</b>
	<br />
<?php
if (!referentiel_hierarchical_display($referentiel->id)){
    echo referentiel_selection_liste_codes_item_competence('/',$form->competences_task);
}
else{
    echo '<br />'.referentiel_selection_liste_codes_item_hierarchique($referentiel_referentiel->id, true)."\n";
}
?>
    </td>
</tr>
<tr valign="top">
    <td align="right">
	<b><?php  print_string('criteres_evaluation','referentiel') ?>:</b>
	</td>
    <td align="left" colspan="3">
<textarea cols="80" rows="8" name="criteres_evaluation"><?php  p($form->criteres_evaluation) ?></textarea>
    </td>
</tr>

<tr valign="top">
    <td align="center" colspan="4"><b><?php  print_string('consigne_associee','referentiel') ?></b></td>
</tr>
<tr valign="top">
    <td align="right">
	<b><?php  print_string('depot_consigne','referentiel') ?>:</b>
	</td>
    <td align="left" colspan="3">
<input type="radio" name="depot_consigne" value="<?php  p(get_string('yes')); ?>" /> <?php  p(get_string('yes')); ?> &nbsp; &nbsp; <input type="radio" name="depot_consigne" value="<?php  p(get_string('no')); ?>" checked="checked" /> <?php  p(get_string('no')); ?>
    </td>
</tr>
<tr valign="top">
    <th align="center" colspan="4"><?php  print_string('mode_souscription','referentiel')?></th>
</tr>

<tr valign="top">
    <td align="center" colspan="4">
<?php
if ($souscription_libre==1){
  echo '<input type="radio" name="souscription_libre" value="1" checked="checked" /> '.get_string('souscription_libre', 'referentiel').' &nbsp; &nbsp; <input type="radio" name="souscription_libre" value="0" /> '.get_string('souscription_restreinte', 'referentiel').'<br />'."\n";
}
else{
  echo '<input type="radio" name="souscription_libre" value="1" /> '.get_string('souscription_libre', 'referentiel').' &nbsp; &nbsp; <input type="radio" name="souscription_libre" value="0" checked="checked" /> '.get_string('souscription_restreinte', 'referentiel').'<br />'."\n";
}
echo get_string('cle_souscription', 'referentiel').' : <input type="text" name="cle_souscription" value="'.$cle_souscription.'" /> (<span class="small">'.get_string('aide_souscription_cle', 'referentiel').'</span>) 
<br /><br /><input type="checkbox" name="souscription_forcee" value="1" /> '.get_string('souscription_forcee', 'referentiel').' (<span class="small">'.get_string('aide_souscription_forcee', 'referentiel').'</span>)'."\n";
?>

    </td>
</tr>
<tr valign="top">
    <td align="right">
    <b><?php  print_string('tache_masquee','referentiel')?> : </b>
    </td>
	<td align="left" colspan="3">
<?php
if ($tache_masquee==1){
  echo '<input type="radio" name="tache_masquee" value="1" checked="checked" /> '.get_string('yes').' &nbsp; &nbsp; <input type="radio" name="tache_masquee" value="0" /> '.get_string('no').'<br />'."\n";
}
else{
  echo '<input type="radio" name="tache_masquee" value="1" /> '.get_string('yes').' &nbsp; &nbsp; <input type="radio" name="tache_masquee" value="0" checked="checked" /> '.get_string('no').'<br />'."\n";
}
?>
    </td>
</tr>

<?php
if (NOTIFICATION_TACHES){
    echo '<tr valign="top">
    <td align="right">
     <b>'.get_string('notification_tache','referentiel').'</b>';
    if (NOTIFICATION_TACHES_AUX_REFERENTS){
        echo '<br /><i>'.get_string('not_tache_3','referentiel').'</i>';
    }
    else{
        echo '<br /><i>'.get_string('not_tache_1','referentiel').'</i>';
    }
    echo '
    </td>
	<td align="left" colspan="3"><input type="radio" name="mailnow" value="1" /> '.get_string('yes').' &nbsp; &nbsp; <input type="radio" name="mailnow" value="0" checked="checked" /> '.get_string('no').'</td></tr>
';
}
?>
</table>
<br />
<input type="hidden" name="description_consigne" value="<?php  p($form->description_consigne) ?>" />
<input type="hidden" name="type_consigne" value="<?php  p($form->type_consigne) ?>" />
<input type="hidden" name="url_consigne" value="<?php  p($form->url_consigne) ?>" />
<input type="hidden" name="auteurid" value="<?php  p($form->auteurid) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($form->ref_referentiel) ?>" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="taskid"        value="<?php  p($form->taskid) ?>" />
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="modulename"    value="<?php  p($form->modulename) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
</form>
</div>

<?php

    }
    else if (isset($mode) && ($mode=="updatetask")){


        if (isset($taskid) && ($taskid>0)){ // mise a jour d'une tache
	
            if (!isset($form->instance)) {
                $form->instance = $referentiel->id;
            }
            if (!isset($form->ref_referentiel)) {
                $form->ref_referentiel = $referentiel_referentiel->id;
            }
            if (!isset($form->courseid)) {
                $form->courseid = $course->id;
            }
            if (!isset($form->type_task)) {
                $form->type_task = '';
            }
            if (!isset($form->description_task)) {
                $form->description_task = '';
            }
            if (!isset($form->competences_task)) {
                $form->competences_task = referentiel_get_liste_codes_competence($referentiel_referentiel->id);
            }
            if (!isset($form->criteres_evaluation)) {
                $form->criteres_evaluation = '';
            }

            // Modif JF 2012/10/26
            $new_auteurid=$USER->id;
            if (!isset($form->auteurid)) {
                $form->auteurid=$USER->id;
            }

            if (!isset($form->date_debut)){
                $date_debut=date("d/m/Y H:i");
            }
            else{
                $date_debut=date("d/m/Y H:i", $form->date_debut);
            }
            if (!isset($form->date_fin)){
                $date_fin=date("d/m/Y H:i");
            }
            else{
                $date_fin=date("d/m/Y H:i", $form->date_fin);
            }
		
            if (!isset($form->taskid)) {
                if (isset($taskid))
                    $form->taskid=$taskid;
                else
                    $form->taskid='';
            }

            // consignes
            if (!isset($form->description_consigne)) {
                $form->description_consigne = '';
            }
            if (!isset($form->type_consigne)) {
                $form->type_consigne = '';
            }
            if (!isset($form->url_consigne)) {
                $form->url_consigne = '';
            }
	   
            if (!isset($form->souscription_libre)) {
                $souscription_libre = '1';
            }
            if (!isset($form->cle_souscription)){
                $cle_souscription = '';
            }
            if (!isset($form->tache_masquee)){
                $tache_masquee = 0;
            }

            if (!isset($form->sesskey)) {
                $form->sesskey=sesskey();
            }
            if (!isset($form->modulename)) {
                $form->modulename='referentiel';
            }
		
            // preparer les variables globales pour Overlib
            referentiel_initialise_data_referentiel($referentiel_referentiel->id);

            // Charger la tache
            // filtres

            $isauthor = has_capability('mod/referentiel:addtask', $context);
            $iseditor = has_capability('mod/referentiel:writereferentiel', $context);
            // DEBUG
            // echo "<br />DEBUG :: task.html :: 299 :: TACHE : $taskid :: REFERENTIEL : $referentiel->id<br\n";
            $liste_codes_competence=referentiel_get_liste_codes_competence($referentiel_referentiel->id);
            $record_t=referentiel_get_task($taskid,0);
            // print_r($record_t);
            if (!$record_t){
			     print_error(get_string('notask','referentiel'), "task.php?d=$referentiel->idid&amp;select_acc=$select_acc&amp;mode=update");
            }
            else if ($record_t){
                // DEBUG
                // echo "<br/>DEBUG ::<br />\n";
                // print_object($record_task);
		
                echo "\n".'<script type="text/javascript" src="dhtmlgoodies_calendar.js"></script>'."\n";

                $taskid=$record_t->id;
                $type_task = stripslashes($record_t->type_task);
                $description_task = stripslashes($record_t->description_task);
                $competences_task = stripslashes($record_t->competences_task);
                $criteres_evaluation = stripslashes($record_t->criteres_evaluation);
                $ref_instance = $record_t->ref_instance;
                $ref_referentiel = $record_t->ref_referentiel;
                $ref_course = $record_t->ref_course;
                $auteurid = $record_t->auteurid;
                $date_creation = $record_t->date_creation;
                $date_modif = $record_t->date_modif;
                if ($record_t->date_debut==0){
				    $date_debut = date("d/m/Y H:i");
				    // echo "<br />DEBUG :: task.html :: 313 :: $date_debut\n";
				    // exit;
                }
                else{
                    $date_debut=date("d/m/Y H:i", $record_t->date_debut);
                }
                if ($record_t->date_fin==0){
                    $date=strtotime('+4 weeks');
                    $date_fin = date("d/m/Y H:i", $date);
                    // echo "<br />DEBUG :: task.html :: 322 :: $date :: $date_fin\n";
                    // exit;
                }
                else{
                    $date_fin=date("d/m/Y H:i", $record_t->date_fin);
                }
				
                // Modalite souscription
                if (isset($record_t->souscription_libre)) {
                    $souscription_libre = $record_t->souscription_libre;
                }
                else{
                    $souscription_libre = 1;
                }
                if (isset($record_t->cle_souscription)){
                    $cle_souscription = $record_t->cle_souscription;
                }
                else{
                    $cle_souscription='';
                }
                if (isset($record_t->tache_masquee)){
                    $tache_masquee = $record_t->tache_masquee;
                }
                else{
                    $tache_masquee=0;
                }


			    // DEBUG
			    // echo "<br/>DEBUG ::<br />\n";
			    // print_object($record_t);
			
			    $auteur_info=referentiel_get_user_info($auteurid);
                // Modif JF 2012/10/26
                $new_auteur_info=referentiel_get_user_info($new_auteurid);


			    // dates
			    $date_creation_info=userdate($date_creation);
			    $date_modif_info=userdate($date_modif);
			    // $date_debut_info=userdate($date_debut);
			    // $date_fin_info=userdate($date_fin);
			    $date_debut_info=$date_debut;
			    $date_fin_info=$date_fin;
				
                // AFFICHER tache
?>
<div align="center">
<h3><?php  print_string('modifier_task','referentiel') ?></h3>
<form name="form" method="post" action="<?php p("task.php?d=$referentiel->id") ?>">
<table cellpadding="5" width="80%" align="center">
<tr valign="top">
    <td align="center">
<b><?php  print_string('id','referentiel') ?></b> : <?php p($taskid) ?>
    </td>
    <td align="center">
<b><?php print_string('auteur','referentiel') ?></b> :
<?php
p($auteur_info);
if ($auteur_info!=$new_auteur_info) {
    echo '<br /> --&gt; <i>'.$new_auteur_info.'</i>'."\n";
}
?>
    </td>
    <td align="center">
<b><?php  print_string('date_creation','referentiel') ?></b> : <?php p($date_creation_info) ?>
<input type="hidden" name="date_creation" value="<?php  p($date_creation) ?>" />
    </td>		
    <td align="center">
<b><?php  print_string('date_modif','referentiel') ?></b> : <?php p($date_modif_info) ?>	
<input type="hidden" name="date_modif" value="<?php  p($date_modif) ?>" />
    </td>		
</tr>
</table>
<table cellpadding="5" width="80%" align="center">
<tr valign="top">
    <td align="right">
	<b><?php  print_string('date_debut','referentiel') ?>:</b>
	</td>
    <td align="left">
	<input type="text" value="<?php echo $date_debut_info ?>" name="date_debut" />
	<input type="button" value="Cal" onclick="displayCalendar(this.form.date_debut,'dd/mm/yyyy hh:ii',this, true,'','fr')" />
<!-- 
	<input type="button" value="Show 'date_debut' hidden field value" onClick="alert(this.form.date_debut.value)" />
-->
	</td>
    <td align="right">
	<b><?php  print_string('date_fin','referentiel') ?>:</b>
	</td>
    <td align="left">
	<input type="text" value="<?php echo $date_fin_info ?>" name="date_fin" />
	<input type="button" value="Cal" onclick="displayCalendar(this.form.date_fin, 'dd/mm/yyyy hh:ii', this, true,'','fr')" />
<!--
	<input type="button" value="Show 'date_fin' hidden field value" onClick="alert(this.form.date_fin.value)" />
-->
 	</td>
</tr>

<tr valign="top">
    <td align="right">
	<b><?php  print_string('type_task','referentiel') ?>:</b>
	</td>
    <td align="left" colspan="3">
<input type="text" name="type_task" size="80" maxlength="80" value="<?php  p($type_task) ?>" />
    </td>
</tr>
<tr valign="top">
    <td align="right">
	<b><?php  print_string('description','referentiel') ?>:</b>
	</td>
    <td align="left" colspan="3">
<textarea cols="80" rows="8" name="description_task"><?php  p($description_task) ?></textarea>
    </td>
</tr>
<tr valign="top">
    <td align="right">
	<b><?php  print_string('aide_saisie_competences','referentiel') ?>:</b></td>
    <td align="left" colspan="3">	
<?php
    if (!referentiel_hierarchical_display($referentiel->id)){
		echo referentiel_modifier_selection_liste_codes_item_competence('/', $liste_codes_competence, $competences_task);
	}
	else{
    	referentiel_modifier_selection_codes_item_hierarchique($referentiel_referentiel->id, $competences_task, false);
	}
?>
    </td>
</tr>
<tr valign="top">
    <td align="right">
	<b><?php  print_string('criteres_evaluation','referentiel') ?>:</b>
	</td>
    <td align="left" colspan="3">
<textarea cols="80" rows="8" name="criteres_evaluation"><?php  p($criteres_evaluation) ?></textarea>
    </td>
</tr>


<tr valign="top">
    <th align="center" colspan="4"><?php  print_string('mode_souscription','referentiel')?></th>
</tr>

<tr valign="top">
    <td align="center" colspan="4">
<?php
                if ($souscription_libre==1){
                    echo '<input type="radio" name="souscription_libre" value="1" checked="checked" /> '.get_string('souscription_libre', 'referentiel').'
&nbsp; &nbsp;
<input type="radio" name="souscription_libre" value="0" /> '.get_string('souscription_restreinte', 'referentiel').'<br />'."\n";
                }
                else{
                    echo '<input type="radio" name="souscription_libre" value="1" /> '.get_string('souscription_libre', 'referentiel').'
&nbsp; &nbsp;
<input type="radio" name="souscription_libre" value="0" checked="checked" /> '.get_string('souscription_restreinte', 'referentiel').'<br />'."\n";
                }
echo get_string('cle_souscription', 'referentiel').' : <input type="text" name="cle_souscription" value="'.$cle_souscription.'" /> (<span class="small">'.get_string('aide_souscription_cle', 'referentiel').'</span>) 
<br />
<input type="checkbox" name="souscription_forcee" value="1" /> '.get_string('souscription_forcee', 'referentiel').' <span class="small">('.get_string('aide_souscription_forcee', 'referentiel').'</span>)'."\n"; 
?>
    </td>
</tr>


<tr valign="top">
    <th align="center" colspan="4"><?php  print_string('tache_masquee','referentiel')?></th>
</tr>
<tr valign="top">
    <td align="center" colspan="4">
<?php
                if ($tache_masquee==1){
  echo '<input type="radio" name="tache_masquee" value="1" checked="checked" /> '.get_string('yes').'
&nbsp; &nbsp;
<input type="radio" name="tache_masquee" value="0" /> '.get_string('no').'<br />'."\n";
                }
                else{
  echo '<input type="radio" name="tache_masquee" value="1" /> '.get_string('yes').'
&nbsp; &nbsp;
<input type="radio" name="tache_masquee" value="0" checked="checked" /> '.get_string('no').'<br />'."\n";
                }
?>
    </td>
</tr>

<?php
                if (NOTIFICATION_TACHES){
                    echo '<tr valign="top">';
                    echo '  <td align="center" colspan="4">
<b>'.get_string('notification_tache','referentiel').'</b>';
                    if (NOTIFICATION_TACHES_AUX_REFERENTS){
                        echo '<br /><i>'.get_string('not_tache_3','referentiel').'</i>';
                    }
                    else{
                        echo '<br /><i>'.get_string('not_tache_1','referentiel').'</i>';
                    }
                    echo '    </td>
</tr>
<tr valign="top">
    <td align="center" colspan="4">';
                    echo '<input type="radio" name="mailnow" value="1" />'.get_string('yes').' &nbsp; <input type="radio" name="mailnow" value="0" checked="checked" />'.get_string('no').' &nbsp; &nbsp;
    </td>
</tr>
';
                    }
?>


</table>

<input type="hidden" name="auteurid" value="<?php  p($new_auteurid) ?>" />
<input type="hidden" name="taskid" value="<?php  p($taskid) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($ref_referentiel) ?>" />
<input type="hidden" name="ref_course" value="<?php  p($ref_course) ?>" />
<input type="hidden" name="ref_instance" value="<?php  p($ref_instance) ?>" />

<input type="hidden" name="action" value="modifier_task" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="modulename"    value="<?php  p($form->modulename) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
<input type="submit" name="delete" value="<?php  print_string("delete") ?>" />
<input type="submit" name="delete_all_task_associations" value="<?php  print_string('delete_all_task_associations', 'referentiel') ?>" />
</form>

<br />
<?php			
			
			        // Recuperer les consignes associes à la tache
			        $records_consigne = referentiel_get_consignes($taskid);
                    if ($records_consigne){
                        // afficher
				        // DEBUG
				        // echo "<br/>DEBUG ::<br />\n";
				        // print_r($records_consigne);
				        $compteur_consigne=0;
				        foreach ($records_consigne as $record_d){
                            $compteur_consigne++;
                            $consigne_id=$record_d->id;
                            $type_consigne = stripslashes($record_d->type_consigne);
                            $description_consigne = stripslashes($record_d->description_consigne);
                            $url_consigne = stripslashes($record_d->url_consigne);
                            $ref_task = $record_d->ref_task;
                            $cible_consigne = $record_d->cible_consigne; // fenêtre cible
                            $etiquette_consigne = $record_d->etiquette_consigne; // etiquette

?>
<!-- consigne -->
<div align="center">
<form name="form" method="post" action="<?php p("task.php?d=$referentiel->id") ?>">
<table cellpadding="5" bgcolor="#ffffdd">
<tr valign="top">
    <td align="center" colspan="2"><b><?php  print_string('consigne_associe','referentiel') ?></b></td>
</tr>
<tr valign="top">
    <td align="right">
<b><?php  print_string('consigne','referentiel') ?></b>
    </td>
    <td align="left">
<i>
<?php  p($consigne_id) ?>
</i>
<input type="hidden" name="ref_task" value="<?php p($ref_task) ?>" />
<input type="hidden" name="consigne_id" value="<?php p($consigne_id) ?>" />
    </td>
</tr>	
<tr valign="top">		
    <td align="right">
<b><?php  print_string('type_consigne','referentiel') ?></b><br /><?php  print_string('type_aide','referentiel') ?> :
	</td>
    <td align="left">
<input type="text" name="type_consigne" size="20" maxlength="20" value="<?php  p($type_consigne) ?>" />
    </td>
</tr>	
<?php	
	echo '<tr valign="top"><td align="right"><b>'.get_string('url','referentiel').'</b></td><td align="left">
<input type="text" name="url_consigne" size="70" maxlength="255" value="'.$url_consigne.'" /></td></tr>'."\n";
	echo '<tr valign="top"><td align="right">'. get_string('etiquette_consigne','referentiel').'</td><td align="left">
<input type="text" name="etiquette_consigne" size="55" maxlength="255" value="'.$etiquette_consigne.'" /></td></tr>'."\n";
	echo '<tr valign="top"><td align="right">'. get_string('cible_link','referentiel').'</td><td align="left">'."\n";
	if ($cible_consigne){
		echo ' <input type="radio" name="cible_consigne" value="1" checked="checked" />'.get_string('yes').'
<input type="radio" name="cible_consigne" value="0" />'.get_string('no')."\n";
	}
	else{
		echo ' <input type="radio" name="cible_consigne" value="1" />'.get_string('yes').'
<input type="radio" name="cible_consigne" value="0" checked="checked" />'.get_string('no')."\n";
	}
	echo '</td></tr>'."\n";
?>

<tr valign="top">
    <td align="right">
	<b><?php  print_string('description','referentiel') ?></b>
	</td>
    <td align="left">
<textarea cols="80" rows="2" name="description_consigne"><?php  p($description_consigne) ?></textarea>
    </td>
</tr>
<?php
/*
	echo '<tr valign="top">
    <td align="right">
	<b>.'get_string('modifier_depot_consigne','referentiel').'</b>
	</td>
    <td align="left">
<input type="radio" name="depot_consigne" value="'.get_string('yes').'" />'.get_string('yes').'
<input type="radio" name="depot_consigne" value="'.get_string('no').'" checked="checked" />'.get_string('no').'
    </td>
</tr>
';
*/
?>
</table>
<input type="hidden" name="taskid" value="<?php  p($taskid) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($ref_referentiel) ?>" />
<input type="hidden" name="ref_course" value="<?php  p($ref_course) ?>" />
<input type="hidden" name="ref_instance" value="<?php  p($ref_instance) ?>" />

<input type="hidden" name="action" value="modifier_consigne" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="modulename"    value="<?php  p($form->modulename) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
<input type="submit" name="delete" value="<?php  print_string("delete") ?>" />
</form>
</div>

<?php
                        }
                    }
			
			        // AJOUTER un consigne

                    if (!isset($form->description_consigne)) {
                        $form->description_consigne = '';
                    }
                    if (!isset($form->type_consigne)) {
                        $form->type_consigne = '';
                    }
                    if (!isset($form->url_consigne)) {
                        $form->url_consigne = '';
                    }
                    if (!isset($form->cible_consigne)) {
                        $form->cible_consigne = 1;
                    }
                    if (!isset($form->etiquette_consigne)) {
			            $form->etiquette_consigne = '';
			        }

?>				
<!-- NOUVEAU consigne -->
<br />
<div align="center">
<form name="form" method="post" action="<?php p("upload_consigne.php?d=$referentiel->id") ?>">
<table cellpadding="5" bgcolor="#ffeeee">
<tr valign="top">
    <td align="center" colspan="2"><b><?php  print_string('consigne_ajout','referentiel') ?></b></td>
</tr>
<tr valign="top">
    <td align="center" colspan="2">
<input type="radio" name="depot_consigne" value="<?php  p(get_string('yes')); ?>" /> <?php  p(get_string('yes')); ?>
&nbsp; <input type="radio" name="depot_consigne" value="<?php  p(get_string('no')); ?>" checked="checked" /> <?php  p(get_string('no')); ?>
    </td>
</tr>
	
</table>
<input type="hidden" name="type_consigne"  value="<?php  p($form->type_consigne) ?>" />
<input type="hidden" name="url_consigne" value="<?php  p($form->url_consigne) ?>" />
<input type="hidden" name="description_consigne" value="<?php  p($form->description_consigne)?>" />

<input type="hidden" name="ref_task" value="<?php  p($taskid) ?>" />
<input type="hidden" name="auteurid" value="<?php  p($new_auteurid) ?>" />
<input type="hidden" name="taskid" value="<?php  p($taskid) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($ref_referentiel) ?>" />
<input type="hidden" name="ref_course" value="<?php  p($ref_course) ?>" />
<input type="hidden" name="ref_instance" value="<?php  p($ref_instance) ?>" />
<input type="hidden" name="action" value="creer_consigne" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="modulename"    value="<?php  p($form->modulename) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
</form>
</div>
<?php
			
                }
            }
            else { // plusieurs taches affichees
// #########################################################################
// VERIFIER si des tâches existes
// A FAIRE
    	if (!isset($form->instance)) {
        	$form->instance = $referentiel->id;
    	}
    	if (!isset($form->ref_referentiel)) {
        	$form->ref_referentiel = $referentiel_referentiel->id;
    	}
    	if (!isset($form->courseid)) {
        	$form->courseid = $course->id;
    	}    	
    	if (!isset($form->type_task)) {
        	$form->type_task = '';
    	}
    	if (!isset($form->description_task)) {
        	$form->description_task = '';
    	}
    	if (!isset($form->competences_task)) {
        	$form->competences_task = referentiel_get_liste_codes_competence($referentiel_referentiel->id);
    	}
    	if (!isset($form->criteres_evaluation)) {
        	$form->criteres_evaluation = '';
    	}
        // Modif JF 2012/10/26
        $new_auteurid=$USER->id;
        $new_auteur_info=referentiel_get_user_info($new_auteurid);

    	if (!isset($form->auteurid)) {
        	$form->auteurid=$USER->id;
    	}
    	if (!isset($form->date_debut)){
    	    	$date_debut=date("d/m/Y H:i");
    	}
    	else{
    	    	$date_debut=date("d/m/Y H:i", $form->date_debut);    	
    	}
    	if (!isset($form->date_fin)){
    	    	$date_fin=date("d/m/Y H:i");
    	}
    	else{
    	    	$date_fin=date("d/m/Y H:i", $form->date_fin);
    	}
    	    	
    	if (!isset($form->taskid)) {
    	    	if (isset($taskid))
    	    	    	$form->taskid=$taskid;
    	    	else
    	    	    	$form->taskid='';
    	}

        // Modalite souscription
        if (isset($record_t->souscription_libre)) {
            $souscription_libre = $record_t->souscription_libre;
    	}
        else{
            $souscription_libre = 1;
        }
        if (isset($record_t->cle_souscription)){
            $cle_souscription = $record_t->cle_souscription;
        }
        else{
            $cle_souscription='';
        }
        if (isset($record_t->tache_masquee)){
            $tache_masquee = $record_t->tache_masquee;
        }
        else{
            $tache_masquee=0;
        }


    	// consignes
    	if (!isset($form->description_consigne)) {
        	$form->description_consigne = '';
    	}
    	if (!isset($form->type_consigne)) {
        	$form->type_consigne = '';
    	}
    	if (!isset($form->url_consigne)) {
        	$form->url_consigne = '';
    	}


    	if (!isset($form->sesskey)) {
        	$form->sesskey=sesskey();
    	}
    	if (!isset($form->modulename)) {
        	$form->modulename='referentiel';
    	}

    	
    	// Charger les taches
    	// filtres

    	$isauthor = has_capability('mod/referentiel:addtask', $context);
    	$iseditor = has_capability('mod/referentiel:writereferentiel', $context);
        $liste_codes_competence=referentiel_get_liste_codes_competence($referentiel_referentiel->id);
    	$records_task=referentiel_get_all_tasks($course->id, $referentiel->id);
    	if ($records_task){
    	    // DEBUG
    	    // echo "<br/>DEBUG ::<br />\n";
    	    // print_object($records_task);

    	    // preparer les variables globales pour Overlib
    	    referentiel_initialise_data_referentiel($referentiel_referentiel->id);

    	    echo "\n".'<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar.css" media="screen"></link>
<script type="text/javascript" src="dhtmlgoodies_calendar.js"></script>'."\n";

    	    foreach ($records_task as $record_t){
                    $taskid=$record_t->id;
    	    	    $type_task = stripslashes($record_t->type_task);
    	    	    $description_task = stripslashes($record_t->description_task);
    	    	    $competences_task = stripslashes($record_t->competences_task);
    	    	    $criteres_evaluation = stripslashes($record_t->criteres_evaluation);
    	    	    $ref_instance = $record_t->ref_instance;
    	    	    $ref_referentiel = $record_t->ref_referentiel;
    	    	    $ref_course = $record_t->ref_course;
    	    	    $auteurid = $record_t->auteurid;
    	    	    $date_creation = $record_t->date_creation;
    	    	    $date_modif = $record_t->date_modif;
    	    	    $souscription_libre = $record_t->souscription_libre;
    	    	    $cle_souscription = stripslashes($record_t->cle_souscription);
                    if (isset($record_t->tache_masquee)){
                        $tache_masquee = $record_t->tache_masquee;
                    }
                    else{
                        $tache_masquee=0;
                    }
    	    	    	
    	    	    if ($record_t->date_debut==0){
    	    	    	    	$date_debut = date("d/m/Y H:i");
    	    	    	    	//echo "<br />DEBUG :: task.html :: 313 :: $date_debut\n";
    	    	    	    	// exit; 
    	    	    }
    	    	    else{
    	    	    	    	$date_debut=date("d/m/Y H:i", $record_t->date_debut);
    	    	    }
    	    	    if ($record_t->date_fin==0){
    	    	    	    	$date=strtotime('+4 weeks');
    	    	    	    	$date_fin = date("d/m/Y H:i", $date);
    	    	    	    	// echo "<br />DEBUG :: task.html :: 322 :: $date :: $date_fin\n";
    	    	    	    	// exit; 
    	    	    }
    	    	    else{
    	    	    	    	$date_fin=date("d/m/Y H:i", $record_t->date_fin);
    	    	    }

                    // Modalite souscription
                    if (isset($record_t->souscription_libre)) {
                        $souscription_libre = $record_t->souscription_libre;
            	    }
                    else{
                        $souscription_libre = 1;
                    }
                    if (isset($record_t->cle_souscription)){
                        $cle_souscription = $record_t->cle_souscription;
                    }
                    else{
                        $cle_souscription='';
                    }
                    if (isset($record_t->tache_masquee)){
                        $tache_masquee = $record_t->tache_masquee;
                    }
                    else{
                        $tache_masquee=0;
                    }

    	    	    // DEBUG
    	    	    	// echo "<br/>DEBUG ::<br />\n";
    	    	    	// print_object($record_t);
    	    	    	
    	    	    $auteur_info=referentiel_get_user_info($auteurid);

                    // dates
    	    	    $date_creation_info=userdate($date_creation);
    	    	    $date_modif_info=userdate($date_modif);
    	    	    // $date_debut_info=userdate($date_debut);
    	    	    // $date_fin_info=userdate($date_fin);
    	    	    $date_debut_info=$date_debut;
    	    	    $date_fin_info=$date_fin;
    	    	    	    	
    	    	    	// AFFICHER tache
?>
<div align="center">
<h3><?php  print_string('modifier_task','referentiel') ?></h3>
<form name="form" method="post" action="<?php p("task.php?d=$referentiel->id") ?>">
<table cellpadding="5" width="80%" align="center">
<tr valign="top">
    <td align="center">
<b><?php  print_string('id','referentiel') ?></b> : <?php p($taskid) ?>
    </td>
    <td align="center"><b><?php print_string('auteur','referentiel') ?></b> :
<?php
p($auteur_info);
if ($auteur_info!=$new_auteur_info){
	echo ' <br />--&gt;<i>'.$new_auteur_info."</i>\n";
}
?>
    </td>
    <td align="center">
<b><?php  print_string('date_creation','referentiel') ?></b> : <?php p($date_creation_info) ?>
<input type="hidden" name="date_creation" value="<?php  p($date_creation) ?>" />
    </td>    	    	
    <td align="center">
<b><?php  print_string('date_modif','referentiel') ?></b> : <?php p($date_modif_info) ?>    	
<input type="hidden" name="date_modif" value="<?php  p($date_modif) ?>" />
    </td>    	    	
</tr>
</table>
<table cellpadding="5" width="80%" align="center">
<tr valign="top">
    <td align="right">
    	<b><?php  print_string('date_debut','referentiel') ?>:</b>
    	</td>
    <td align="left">
    	<input type="text" value="<?php echo $date_debut_info ?>" name="date_debut" />
    	<input type="button" value="Cal" onclick="displayCalendar(this.form.date_debut,'dd/mm/yyyy hh:ii',this, true,'','fr')" />
<!-- 
    	<input type="button" value="Show 'date_debut' hidden field value" onClick="alert(this.form.date_debut.value)" />
-->
    	</td>
    <td align="right">
    	<b><?php  print_string('date_fin','referentiel') ?>:</b> 
    	</td>
    <td align="left">
    	<input type="text" value="<?php echo $date_fin_info ?>" name="date_fin" />
    	<input type="button" value="Cal" onclick="displayCalendar(this.form.date_fin, 'dd/mm/yyyy hh:ii', this, true,'','fr')" />
<!--
    	<input type="button" value="Show 'date_fin' hidden field value" onClick="alert(this.form.date_fin.value)" />
-->
     	</td>
</tr>

<tr valign="top">
    <td align="right">
    	<b><?php  print_string('type_task','referentiel') ?>:</b>
    	</td>
    <td align="left" colspan="3">
<input type="text" name="type_task" size="80" maxlength="80" value="<?php  p($type_task) ?>" />
    </td>
</tr>
<tr valign="top">
    <td align="right">
    	<b><?php  print_string('description','referentiel') ?>:</b>
    	</td>
    <td align="left" colspan="3">
<textarea cols="80" rows="8" name="description_task"><?php  p($description_task) ?></textarea>
    </td>
</tr>
<tr valign="top">
    <td align="right">
    	<b><?php  print_string('aide_saisie_competences','referentiel') ?>:</b></td>
    <td align="left" colspan="3">
<?php
    if (!referentiel_hierarchical_display($referentiel->id)){
		echo referentiel_modifier_selection_liste_codes_item_competence('/', $liste_codes_competence, $competences_task);
	}
	else{
    	referentiel_modifier_selection_codes_item_hierarchique($referentiel_referentiel->id, $competences_task, false);
	}
?>
    </td>
</tr>
<tr valign="top">
    <td align="right">
    	<b><?php  print_string('criteres_evaluation','referentiel') ?>:</b>
    	</td>
    <td align="left" colspan="3">
<textarea cols="80" rows="8" name="criteres_evaluation"><?php  p($criteres_evaluation) ?></textarea>
    </td>
</tr>

<tr valign="top">
    <th align="center" colspan="4"><?php  print_string('mode_souscription','referentiel')?></th>
</tr>
<tr valign="top">
    <td align="center" colspan="4">
<?php
if ($souscription_libre==1){
  echo '<input type="radio" name="souscription_libre" value="1" checked="checked" /> '.get_string('souscription_libre', 'referentiel').' &nbsp; &nbsp; <input type="radio" name="souscription_libre" value="0" /> '.get_string('souscription_restreinte', 'referentiel').'<br />'."\n";
}
else{
  echo '<input type="radio" name="souscription_libre" value="1" /> '.get_string('souscription_libre', 'referentiel').' &nbsp; &nbsp; <input type="radio" name="souscription_libre" value="0" checked="checked" /> '.get_string('souscription_restreinte', 'referentiel').'<br />'."\n";
}
echo get_string('cle_souscription', 'referentiel').' : <input type="text" name="cle_souscription" value="'.$cle_souscription.'" /> (<span class="small">'.get_string('aide_souscription_cle', 'referentiel').'</span>) 
<br /><br />
<input type="checkbox" name="souscription_forcee" value="1" /> '.get_string('souscription_forcee', 'referentiel').' (<span class="small">'.get_string('aide_souscription_forcee', 'referentiel').'</span>)'."\n"; 
?>
    </td>
</tr>

<tr valign="top">
    <th align="center" colspan="4"><?php  print_string('tache_masquee','referentiel')?></th>
</tr>
<tr valign="top">
    <td align="center" colspan="4">
<?php
if ($tache_masquee==1){
  echo '<input type="radio" name="tache_masquee" value="1" checked="checked" /> '.get_string('yes').' &nbsp; &nbsp; <input type="radio" name="tache_masquee" value="0" /> '.get_string('no').'<br />'."\n";
}
else{
  echo '<input type="radio" name="tache_masquee" value="1" /> '.get_string('yes').' &nbsp; &nbsp; <input type="radio" name="tache_masquee" value="0" checked="checked" /> '.get_string('no').'<br />'."\n";
}
?>
    </td>
</tr>

<?php
if (NOTIFICATION_TACHES){
    echo '<tr valign="top">';
    echo '  <td align="center" colspan="4">
     <b>'.get_string('notification_tache','referentiel').'</b>';
    if (NOTIFICATION_TACHES_AUX_REFERENTS){
        echo '<br /><i>'.get_string('not_tache_3','referentiel').'</i>';
    }
    else{
        echo '<br /><i>'.get_string('not_tache_1','referentiel').'</i>';
    }
    echo '    </td>
</tr>
<tr valign="top">
    <td align="center" colspan="4">';
    	echo '<input type="radio" name="mailnow" value="1" />'.get_string('yes').' &nbsp; &nbsp; <input type="radio" name="mailnow" value="0" checked="checked" />'.get_string('no').' &nbsp; &nbsp;
    </td>
</tr>
';
}
?>

</table>

<input type="hidden" name="auteurid" value="<?php  p($new_auteurid) ?>" />
<input type="hidden" name="taskid" value="<?php  p($taskid) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($ref_referentiel) ?>" />
<input type="hidden" name="ref_course" value="<?php  p($ref_course) ?>" />
<input type="hidden" name="ref_instance" value="<?php  p($ref_instance) ?>" />

<input type="hidden" name="action" value="modifier_task" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="modulename"    value="<?php  p($form->modulename) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
<input type="submit" name="delete" value="<?php  print_string("delete") ?>" />
<input type="submit" name="delete_all_task_associations" value="<?php  print_string('delete_all_task_associations', 'referentiel') ?>" />
</form>
</div>
<br />
<?php    	    	    	
    	    	    	
    	    	    	// Recuperer les consignes associes a la tache
    	    	    $records_consigne = referentiel_get_consignes($taskid);
    	        	if ($records_consigne){
        	    	    // afficher
    	    	    	// DEBUG
    	    	    	// echo "<br/>DEBUG ::<br />\n";
    	    	    	// print_r($records_consigne);
    	    	    	$compteur_consigne=0;
    	    	    	foreach ($records_consigne as $record_d){
                            $compteur_consigne++;
                            $consigne_id=$record_d->id;
    	    	    	    $type_consigne = stripslashes($record_d->type_consigne);
    	    	    	    $description_consigne = stripslashes($record_d->description_consigne);
    	    	    	    $url_consigne = stripslashes($record_d->url_consigne);
    	    	    	    $ref_task = $record_d->ref_task;
    	    	    	    $cible_consigne = $record_d->cible_consigne; // fenêtre cible
    	    	    	    $etiquette_consigne = $record_d->etiquette_consigne; // etiquette
    	    	    	    	    	    	    	
?>
<!-- consigne -->
<div align="center">
<form name="form" method="post" action="<?php p("task.php?d=$referentiel->id") ?>">
<table cellpadding="5" bgcolor="#ffffdd">
<tr valign="top">
    <td align="center" colspan="2"><b><?php  print_string('consigne_associe','referentiel') ?></b></td>
</tr>
<tr valign="top">
    <td align="right">
<b><?php  print_string('consigne','referentiel') ?></b>
    </td>
    <td align="left">
<i>
<?php  p($consigne_id) ?>
</i>
<input type="hidden" name="ref_task" value="<?php p($ref_task) ?>" />
<input type="hidden" name="consigne_id" value="<?php p($consigne_id) ?>" />
    </td>
</tr>    	
<tr valign="top">    	    	
    <td align="right">
<b><?php  print_string('type_consigne','referentiel') ?>:</b>
    	</td>
    <td align="left">
<input type="text" name="type_consigne" size="20" maxlength="20" value="<?php  p($type_consigne) ?>" />
    </td>
</tr>    	
<tr valign="top">
    <td align="right">
    	<b><?php  print_string('description','referentiel') ?>:</b>
    	</td>
    <td align="left">
<textarea cols="80" rows="2" name="description_consigne"><?php  p($description_consigne) ?></textarea>
    </td>
</tr>
<?php    	
    	echo '<tr valign="top"><td align="right"><b>'.get_string('url','referentiel').'</b></td><td align="left">
<input type="text" name="url_consigne" size="70" maxlength="255" value="'.$url_consigne.'" /></td></tr>'."\n";
    	echo '<tr valign="top"><td align="right">'. get_string('etiquette_consigne','referentiel').'</td><td align="left">
<input type="text" name="etiquette_consigne" size="55" maxlength="255" value="'.$etiquette_consigne.'" /></td></tr>'."\n";
    	echo '<tr valign="top"><td align="right">'. get_string('cible_link','referentiel').'</td><td align="left">'."\n";
    	if ($cible_consigne){
    	    	echo ' <input type="radio" name="cible_consigne" value="1" checked="checked" />'.get_string('yes').'
<input type="radio" name="cible_consigne" value="0" />'.get_string('no')."\n";
    	}
    	else{
    	    	echo ' <input type="radio" name="cible_consigne" value="1" />'.get_string('yes').'
<input type="radio" name="cible_consigne" value="0" checked="checked" />'.get_string('no')."\n";
    	}
    	echo '</td></tr>'."\n";
?>

<tr valign="top">
    <td align="right">
    	<b><?php  print_string('description','referentiel') ?></b>
    	</td>
    <td align="left">
<textarea cols="80" rows="2" name="description_consigne"><?php  p($description_consigne) ?></textarea>
    </td>
</tr>
<?php
/*
    	echo '<tr valign="top">
    <td align="right">
    	<b>.'get_string('modifier_depot_consigne','referentiel').'</b>
    	</td>
    <td align="left">
<input type="radio" name="depot_consigne" value="'.get_string('yes').'" />'.get_string('yes').'
<input type="radio" name="depot_consigne" value="'.get_string('no').'" checked="checked" />'.get_string('no').'
    </td>
</tr>
';
*/
?>
</table>

<input type="hidden" name="auteurid" value="<?php  p($new_auteurid) ?>" />
<input type="hidden" name="taskid" value="<?php  p($taskid) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($ref_referentiel) ?>" />
<input type="hidden" name="ref_course" value="<?php  p($ref_course) ?>" />
<input type="hidden" name="ref_instance" value="<?php  p($ref_instance) ?>" />

<input type="hidden" name="action" value="modifier_consigne" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="modulename"    value="<?php  p($form->modulename) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
<input type="submit" name="delete" value="<?php  print_string("delete") ?>" />
</form>
</div>
<br />
<?php
    	    	    	    	}
    	    	    	}
    	    	    	
    	    	    	// AJOUTER un consigne
    	    	    	if (!isset($form->description_consigne)) {
    	    	    	       	$form->description_consigne = '';
    	    	    	}
    	    	    	if (!isset($form->type_consigne)) {
    	    	    	       	$form->type_consigne = '';
    	    	    	}
    	    	    	if (!isset($form->url_consigne)) {
    	    	    	       	$form->url_consigne = '';
    	    	    	}
?>    	    	    	    	
<!-- NOUVEAU consigne -->
<br />
<div align="center">
<form name="form" method="post" action="<?php p("upload_consigne.php?d=$referentiel->id") ?>">
<table cellpadding="5" bgcolor="#ffeeee">
<tr valign="top">
    <td align="center" colspan="2"><b><?php  print_string('consigne_ajout','referentiel') ?></b></td>
</tr>

<tr valign="top">
    <td align="right">
    	<?php  print_string('depot_consigne','referentiel') ?> :
    	</td>
    <td align="left">
<input type="radio" name="depot_consigne" value="<?php  p(get_string('yes')); ?>" /> <?php  p(get_string('yes')); ?>
&nbsp; &nbsp; <input type="radio" name="depot_consigne" value="<?php  p(get_string('no')); ?>" checked="checked" /> <?php  p(get_string('no')); ?>
    </td>
</tr>
    	
</table>
<input type="hidden" name="type_consigne"  value="<?php  p($form->type_consigne) ?>" />
<input type="hidden" name="url_consigne" value="<?php  p($form->url_consigne) ?>" />
<input type="hidden" name="description_consigne" value="<?php  p($form->description_consigne)?>" />

<input type="hidden" name="ref_task" value="<?php  p($taskid) ?>" />
<input type="hidden" name="auteurid" value="<?php  p($new_auteurid) ?>" />
<input type="hidden" name="taskid" value="<?php  p($taskid) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($ref_referentiel) ?>" />
<input type="hidden" name="ref_course" value="<?php  p($ref_course) ?>" />
<input type="hidden" name="ref_instance" value="<?php  p($ref_instance) ?>" />
<input type="hidden" name="action" value="creer_consigne" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="modulename"    value="<?php  p($form->modulename) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
</form>
</div>
<?php
    	    	    	
    	    	}
            }
        }
    }
    else if (isset($mode) && ($mode=="deletetaskall")){
    	/// Confirmer la suppression d'un enregistrement

    	if (isset($taskid) && ($taskid>0)){
            echo $OUTPUT->confirm(get_string('confirmdeleterecord_all_activities','referentiel',$taskid),
    	    	$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&deleteall='.$taskid.'&confirm=1&amp;sesskey='.sesskey(),
                $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id);
        }
    	else{
    	    	print_error(get_string('notask','referentiel'), "task.php?d=$referentiel->id&amp;select_acc=$select_acc&amp;mode=listtask");
    	}
    }
    else if (isset($mode) && ($mode=="deletetaskactivites")){
    	/// Confirmer la suppression d'un enregistrement

    	if (isset($taskid) && ($taskid>0)){
    	    // traité en amont
        }
    	else{
    	    print_error(get_string('notask','referentiel'), "task.php?d=$referentiel->id&amp;select_acc=$select_acc&amp;mode=listtask");
    	}
    }

    else if (isset($mode) && ($mode=="deletetask")){
    	/// Confirmer la suppression d'un enregistrement
    	if (isset($taskid) && ($taskid>0)){
        echo $OUTPUT->confirm(get_string('confirmdeleterecord','referentiel'),
    	    $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&delete='.$taskid.'&confirm=1&amp;sesskey='.sesskey(),
            $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id);
    	}
    	else{
    	    print_error(get_string('notask','referentiel'), "task.php?d=$referentiel->id&amp;select_acc=$select_acc&amp;mode=listtask");
    	}
    }
    else if (isset($mode) && ($mode=="selecttask")){
    	if (isset($taskid) && ($taskid>0)){
    	    // DEBUG
    	    // echo '<br />DEBUG :: task.html :: 688 :: Tache : '.$taskid."\n";
            echo $OUTPUT->confirm(get_string('confirm_association_task','referentiel'),
    	    	$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&select='.$taskid.'&confirm=1&amp;sesskey='.sesskey(),
                $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id);
    	}
    	else{
    	    print_error(get_string('notask','referentiel'), "task.php?d=$referentiel->id&amp;select_acc=$select_acc&amp;mode=listtask");
    	}
    }
    else if (isset($mode) && ($mode=="approvetask")){
    	if (isset($taskid) && ($taskid>0)){
            // traité en amont
            // DEBUG
    	    // echo '<br />DEBUG :: task.html :: 1327 :: Tache : '.$taskid."\n";
    	    	/*
    	    	echo $OUTPUT->confirm(get_string('confirm_validation_task','referentiel'),
    	    	$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&approvetask='.$taskid.'&confirm=1&amp;sesskey='.sesskey(),
                $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id);
    	    	*/
    	    	
    	}
    	else{
    	    	print_error(get_string('notask','referentiel'), "task.php?d=$referentiel->id&amp;select_acc=$select_acc&amp;mode=listtask");
    	}
    }
    else if (isset($mode) && ($mode=="approve")){
    	if (isset($taskid) && ($taskid>0)){
            // DEBUG
            // echo '<br />DEBUG :: task.html :: 1070 :: Tache : '.$taskid."\n";
            echo $OUTPUT->confirm(get_string('confirm_validation_task','referentiel'),
                $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&approve='.$taskid.'&confirm=1&amp;sesskey='.sesskey(),
                $CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel->id);
    	}
    	else{
            print_error(get_string('notask','referentiel'), "task.php?d=$referentiel->id&amp;select_acc=$select_acc&amp;mode=listtask");
    	}
    }
?>
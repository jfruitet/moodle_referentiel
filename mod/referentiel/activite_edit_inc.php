<?php // $Id activite_edit.html,v 1 2008-2011 JF Exp $
/**
 * This page defines the form to create or edit an instance of this module
 * It is used from /activite.php.
 *
 * @author
 * @version $Id activite_edit.html,v1 2008-2011 JF Exp $
 * @package referentiel
 **/

if (!empty($record) && !empty($course)){ 
	// une enregistrement activite est charge

	/////////////////// MODIFIER ////////////////////////////////////////////
	if (isset($mode) && ($mode=="modifactivity")){

		if (!isset($form->approved)) {
    		$form->approved=0;
		}	
		if (!isset($form->userid)) {
    		$form->userid=$USER->id;
		}
		if (!isset($form->teacherid)) {
    		$form->teacherid=0;
		}
		
		if (!isset($form->courseid)) {
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
		
		if (!isset($form->activite_id)) {
			if (isset($activite_id))
				$form->activite_id=$activite_id;
			else
				$form->activite_id='';
		}
        if (!isset($form->mailnow)) {
				$form->mailnow='';
		}

		// AJOUTER un document
		if (!isset($form->description_document)) {
		   	$form->description_document = '';
		}
		if (!isset($form->type_document)) {
		   	$form->type_document = '';
		}
		if (!isset($form->url_document)) {
		   	$form->url_document = '';
		}

		// preparer les variables globales pour Overlib
		if (isset($referentiel_referentiel->id) && ($referentiel_referentiel->id>0)){
			referentiel_initialise_data_referentiel($referentiel_referentiel->id);
		}

		// Charger les activites
		// filtres
        $context = context_module::instance($cm->id);

		$isteacher = has_capability('mod/referentiel:approve', $context);
		$isauthor = has_capability('mod/referentiel:write', $context) && !$isteacher;
		$iseditor = has_capability('mod/referentiel:writereferentiel', $context);	

		$liste_codes_competence=referentiel_get_liste_codes_competence($referentiel_referentiel->id);	
    	$activite_id=$record->id;
		$type_activite = stripslashes($record->type_activite);
		$description_activite = stripslashes(strip_tags($record->description_activite));
		$competences_activite = stripslashes(strip_tags($record->competences_activite));
		$commentaire_activite = stripslashes(strip_tags($record->commentaire_activite));
		$ref_instance = $record->ref_instance;
		$ref_referentiel = $record->ref_referentiel;
		$ref_course = $record->ref_course;
		$userid = $record->userid;
		$teacherid = $record->teacherid;
		if ($teacherid==0){
			if ($isteacher || $iseditor){ 
				$teacherid=$USER->id;
			}
		} 

		$date_creation = $record->date_creation;
		$date_modif = $record->date_modif;
		$approved = $record->approved;
		$ref_task = $record->ref_task;
		if ($ref_task>0){ // remplacer par la liste definie dans la tache
			// Fusionner la liste des codes attachees a la tache et celles de l'activite
            $liste_codes_tache=referentiel_get_liste_codes_competence_tache($ref_task);
			$liste_codes='';
            $tcomps=explode("/",$liste_codes_competence);
            // pour toutes les competences du referentiel
            foreach ($tcomps as $comp){
                if (!empty($comp) && (preg_match('/'.$comp.'/', $liste_codes_tache) || preg_match('/'.$comp.'/', $competences_activite))){
                    $liste_codes.= $comp.'/';
                }
            }
            $liste_codes_competences_tache=$liste_codes;          
		}
		else{
			$liste_codes_competences_tache=$liste_codes_competence;
		}
		$user_info=referentiel_get_user_info($userid);
		$teacher_info=referentiel_get_user_info($teacherid);
		// dates
		$date_creation_info=userdate($date_creation);		
		if ($date_modif){
            $date_modif_info=userdate($date_modif);
		}
		else {
            $date_modif_info='';
        }
		$date_modif_student = $record->date_modif_student;
		if ($date_modif_student!=0){
			$date_modif_student_info=userdate($date_modif_student);
		}
		else{
			$date_modif_student_info='';
		}
		$form->old_liste_competences=stripslashes($record->competences_activite);
		
		// LISTE DES COMPETENCES DECLAREES
        $jauge_activite_declarees=referentiel_print_jauge_activite($userid, $ref_referentiel);
        if ($jauge_activite_declarees){
	        print_string('competences_declarees','referentiel', referentiel_get_user_info($userid));
            echo '<br />';
            echo $jauge_activite_declarees."\n";
        }

        //Bareme
		$okbareme=false;
        $str_evaluation='';
		if ($CFG->referentiel_use_scale){
			require_once('lib_bareme.php');
			//echo "<br />OCCURRENCE<br />\n";
			//print_object($referentiel_referentiel);
			//echo "<br />\n";

			if ($rec_assoc=referentiel_get_assoc_bareme_occurrence($referentiel_referentiel->id)){
				// DEBUG
				//echo "<br />A BAREME OCCURRENCE<br />\n";
				//print_object($rec_assoc);
				//echo "<br />\n";

				if ($bareme=referentiel_get_bareme($rec_assoc->refscaleid)){
					$competences_bareme=referentiel_get_competences_activite($activite_id, $bareme->id);
					if ($competences_bareme){
						$str_evaluation.='<br /><span class="bold">'.get_string('evaluation','referentiel').'</span><br /><span class="white">'.referentiel_affiche_bareme_activite($competences_bareme, $bareme, true).'</span>'."\n";
					}
					$okbareme=true;
				}
			}
		}

		// AFFICHER ACTIVITE
		$link_documents=referentiel_get_liens_documents($activite_id, $userid, $context);
        if ($link_documents){
            echo '<div>'."\n".$link_documents."</div>\n";
        }


?>

<h3><?php  print_string('modifier_activite','referentiel') ?></h3>
<div class="ref_saisie1">
<form name="form" method="post" action="<?php p("activite.php?d=$referentiel->id") ?>">
<span class="bold"><?php print_string('id','referentiel') ?></span> <?php p($activite_id) ?>
<span class="bold"><?php print_string('auteur','referentiel') ?></span> <?php p($user_info) ?>
<span class="bold"><?php print_string('evaluation_par','referentiel') ?></span> <?php p($teacher_info) ?>
<br />
<span class="bold"><?php print_string('date_creation','referentiel') ?></span> <?php p($date_creation_info) ?>
<span class="bold"><?php print_string('date_modif','referentiel') ?></span> <?php p($date_modif_info) ?>
<span class="bold"><?php print_string('date_modif_student','referentiel') ?></span> <?php p($date_modif_student_info) ?>
<br />
<span class="bold"><?php  print_string('titre','referentiel') ?></span>
<input type="text" name="type_activite" size="80" maxlength="80" value="<?php  p($type_activite) ?>" />
<br />
<span class="bold"><?php  print_string('description','referentiel') ?></span>
<?php
    echo '<br /><textarea cols="100" rows="10" name="description_activite">'.s($description_activite).'</textarea>'."\n";
	// MODIF JF 2013/10/07
	echo $str_evaluation; // liste des evaluations avec bareme
	if (($ref_task!=0) && ($USER->id==$userid)) { // activite issue d'une tache et affichee par son auteur
    		echo '<br />
<span class="bold">'.get_string('competences_bloquees','referentiel').'</span>'."\n";
        	if (isset($approved) && ($approved)){
            	echo '<div class="valide">'."\n";
        	}
        	else{
            	echo '<div class="invalide">'."\n";
        	}
    		if (!referentiel_hierarchical_display($referentiel->id)){
				echo referentiel_modifier_selection_liste_codes_item_competence('/', $liste_codes_competences_tache, $competences_activite, $activite_id);
			}
			else{
        		echo referentiel_modifier_selection_codes_item_hierarchique($referentiel_referentiel->id, $competences_activite, true);
			}
			// MODIF JF 2014/06/04
			// permettre la propagation de l'evaluation
			if ($okbareme){
               echo "\n".'<input type="hidden" name="userbareme" value="1" />'."\n";
               echo '<input type="hidden" name="baremeid" value="'.$bareme->id.'" />'."\n";
               echo '<input type="hidden" name="threshold" value="'.$bareme->threshold.'" />'."\n";
               echo '<input type="hidden" name="competencesbareme" value="'.$competences_bareme.'" />'."\n";
            }
			echo '</div>'."\n";
    }
    else{ // activite normale
        echo '<br /><br /><span class="bold">'.get_string('aide_saisie_competences','referentiel').'</span>'."\n";
        if (isset($approved) && ($approved)){
            echo '<div class="valide">'."\n";
        }
        else{
            echo '<div class="invalide">'."\n";
        }
    	$roles=referentiel_roles_in_instance($referentiel->id);
      
        if (($USER->id==$userid) && ($roles->is_student || $roles->is_guest)){ // c'est l'auteur qui affiche 
			if (!referentiel_hierarchical_display($referentiel->id)){
				echo referentiel_modifier_selection_liste_codes_item_competence('/', $liste_codes_competences_tache, $competences_activite, $activite_id);
			}
			else{
        		echo referentiel_modifier_selection_codes_item_hierarchique($referentiel_referentiel->id, $competences_activite, true);
			}

	    }
	    else{ // c'est un referent qui affiche
			//Modif bareme	
			if ($okbareme){
				$competences_bareme=referentiel_get_competences_activite($activite_id, $bareme->id);
				$str_a_evaluer=referentiel_affiche_liste_codes_competence('/',$competences_activite, $referentiel_referentiel->id)."\n";
				if (!empty($str_a_evaluer)){
                	echo '<br /><span class="bold">'.get_string('liste_competence_cochees','referentiel').'</span> '."\n"." ".$str_a_evaluer;
				}
				// echo '<br />'."\n";
                //echo '<div class="error">'."\n";

				// modification
				referentiel_modifier_evaluation_codes_item($bareme, $referentiel_referentiel->id, $competences_activite, $competences_bareme, false, $activite_id);
                //echo '</div>'."\n";
    		}
			else{
	    		if (empty($CFG->referentiel_hierarchy)){
					echo referentiel_modifier_selection_liste_codes_item_competence('/', $liste_codes_competence, $competences_activite, $activite_id);
				}
				else{
		    		echo referentiel_modifier_selection_codes_item_hierarchique($referentiel_referentiel->id, $competences_activite, true);
				}
			}
		
		}
        echo '</div>'."\n";
    }

    if (has_capability('mod/referentiel:comment', $context)){
?>
<span class="bold"><?php  print_string('commentaire','referentiel') ?></span>
<br />
<textarea cols="100" rows="10" name="commentaire_activite"><?php  p($commentaire_activite) ?></textarea>
<?php
	}
	else {
?>
	<span class="bold"><?php  print_string('commentaire','referentiel') ?></span>
<br />
<?php  p($commentaire_activite) ?>
<br />
<input type="hidden" name="commentaire_activite" value="<?php  p($commentaire_activite) ?>" />
<?php
	}
		
    echo '<br /> <span class="bold">'.get_string('validation','referentiel').'</span> '."\n";
	if (has_capability('mod/referentiel:approve', $context)){
	   if (isset($approved) && ($approved)){
				echo '<input type="radio" name="approved" value="1" checked="checked" />'.get_string('yes').' &nbsp; <input type="radio" name="approved" value="0" />'.get_string('no').' &nbsp; &nbsp; '."\n";
	   }
	   else{
				echo '<input type="radio" name="approved" value="1" />'.get_string('yes').' &nbsp; <input type="radio" name="approved" value="0" checked="checked" />'.get_string('no').' &nbsp; &nbsp; '."\n";
	   }
	}
	else{
			if (isset($approved) && ($approved)){
				print_string('approved','referentiel');
			}
			else{
				print_string('not_approved','referentiel');
			}
			echo '<input type="hidden" name="approved" value="'.$approved.'" />'."\n";
	}

// DEPOT DE RESSOURCES
echo '<br />
<span class="bold">'.get_string('depot_document','referentiel').'</span>
<input type="radio" name="depot_document" value="'.get_string('yes').'" />'.get_string('yes').' &nbsp; <input type="radio" name="depot_document" value="'.get_string('no').'" checked="checked" />'.get_string('no').'
';

echo '<br />
<span class="bold">'.get_string('notification_activite','referentiel').'</span>';
echo '<input type="radio" name="mailnow" value="1" />'.get_string('yes').' &nbsp; <input type="radio" name="mailnow" value="0" checked="checked" />'.get_string('no')
."<br /><br />\n";

?>
<input type="hidden" name="date_creation" value="<?php  p($date_creation) ?>" />
<input type="hidden" name="date_modif" value="<?php  p($date_modif) ?>" />
<input type="hidden" name="date_modif_student" value="<?php  p($date_modif_student) ?>" />

<input type="hidden" name="select_acc" value="<?php echo $select_acc; ?>" />
<input type="hidden" name="old_liste_competences" value="<?php p($form->old_liste_competences); ?>" />
<input type="hidden" name="userid" value="<?php  p($userid) ?>" />
<input type="hidden" name="teacherid" value="<?php  p($teacherid) ?>" />
<input type="hidden" name="activite_id" value="<?php  p($activite_id) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($ref_referentiel) ?>" />
<input type="hidden" name="ref_course" value="<?php  p($ref_course) ?>" />
<input type="hidden" name="ref_instance" value="<?php  p($ref_instance) ?>" />

<input type="hidden" name="action" value="modifier_activite" />

<!-- Ajout pour les filtres -->
<input type="hidden" name="f_auteur" value="<?php  p($data_f->f_auteur) ?>" />
<input type="hidden" name="f_validation" value="<?php  p($data_f->f_validation) ?>" />
<input type="hidden" name="f_referent" value="<?php  p($data_f->f_referent) ?>" />
<input type="hidden" name="f_date_modif" value="<?php  p($data_f->f_date_modif) ?>" />
<input type="hidden" name="f_date_modif_student" value="<?php  p($data_f->f_date_modif_student) ?>" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="<?php  p($form->courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="modulename"    value="<?php  p($form->modulename) ?>" />
<input type="hidden" name="instance"      value="<?php  p($form->instance) ?>" />
<input type="hidden" name="mode"      value="<?php p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
<input type="submit" name="delete" value="<?php  print_string("delete") ?>" />
<input type="submit" name="cancel" value="<?php  print_string("quit","referentiel") ?>" />

</form>	
</div>

<!-- DOCUMENTS -->
<?php
        $s='';
		// Recuperer les documents associes a l'activite
		$records_document = referentiel_get_documents($activite_id);
	    if ($records_document){
 // ###################### AFFICHER LA LISTE DES DOCUMENTS  ####################
            $s.= '<span class="bold">'.get_string('document_associe','referentiel').'</span>'."\n";
			$compteur_document=0;

			foreach ($records_document as $record_d){
                if ($compteur_document%2==0)
                    $bgcolor="#afefee";
                else
                    $bgcolor="#faefee";
				$compteur_document++;
        		$document_id=$record_d->id;
				$type_document = stripslashes($record_d->type_document);
				$description_document = stripslashes($record_d->description_document);
				$url_document = stripslashes($record_d->url_document);
				$ref_activite = $record_d->ref_activite;
				$cible_document = $record_d->cible_document; // fen�tre cible
				$etiquette_document = $record_d->etiquette_document; // etiquette
				if (preg_match('/moddata\/referentiel/',$url_document)){
			    	// l'URL doit être transformée
                    $data_r=new Object();
					$data_r->id = $document_id;
					$data_r->userid = $userid;
					$data_r->author = $user_info;
					$data_r->url = $url_document;
					$data_r->filearea = 'document';
        			$url_document = referentiel_m19_to_m2_file($data_r, $context, false, true);
				}				
                $link=referentiel_affiche_url($url_document, $etiquette_document, $cible_document);
                $s.='<!-- DOCUMENT -->
';
                $s.='
<div class="ref_saisie2">
<form name="form" method="post" action="activite.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'">
<input type="hidden" name="ref_activite" value="'.$ref_activite.'" />
<input type="hidden" name="document_id" value="'.$document_id.'" />
<span class="bold">'.get_string('num','referentiel').'</span>
<i>'.$document_id.'</i>
<span class="bold">'.get_string('description','referentiel').'</span>
<br />
<textarea cols="70" rows="2" name="description_document">'.$description_document.'</textarea>
<br />
<span class="bold">'.get_string('type_document','referentiel').'</span><br />'.get_string('type_aide','referentiel').'  :
<input type="text" name="type_document" size="10" maxlength="20" value="'.$type_document.'" />
<i><span class="small">'.get_string('extensions_document','referentiel').'</span></i>
<br />
';
// TRAITEMENT DIFFERENCIE SELON LE TYPE D'URL
                if (preg_match('/http/',$url_document)){     // Url ordinaire
                    $s.='<span class="bold">'.get_string('url','referentiel').'</span>    :
<input type="text" name="url_document" size="50" maxlength="255" value="'.$url_document.'" />
';
                }
                else{
                    $s.='<span class="bold">'.get_string('file').'</span> : <i>'.$link.'</i>
<input type="hidden" name="url_document" value="'.$url_document.'" />
';
                }
                $s.='<br /><span class="bold">'.get_string('etiquette_document','referentiel').'</span>
<input type="text" name="etiquette_document" size="40" maxlength="255" value="'.$etiquette_document.'" />
<br /><span class="bold">'. get_string('cible_link','referentiel').'</span>'."\n";
	if ($cible_document){
		$s.=' <input type="radio" name="cible_document" value="1" checked="checked" />'.get_string('yes').' &nbsp; <input type="radio" name="cible_document" value="0" />'.get_string('no')."\n";
	}
	else{
		$s.=' <input type="radio" name="cible_document" value="1" />'.get_string('yes').'
<input type="radio" name="cible_document" value="0" checked="checked" />'.get_string('no')."\n";
	}
    $s.='
<br />

<input type="hidden" name="select_acc" value="'.$select_acc.'" />
<input type="hidden" name="old_liste_competences" value="'.$form->old_liste_competences.'" />
<input type="hidden" name="approved" value="'.$approved.'" />
<input type="hidden" name="userid" value="'.$userid.'" />
<input type="hidden" name="teacherid" value="'.$teacherid.'" />
<input type="hidden" name="activite_id" value="'.$activite_id.'" />
<input type="hidden" name="ref_referentiel" value="'.$ref_referentiel.'" />
<input type="hidden" name="ref_course" value="'.$ref_course.'" />
<input type="hidden" name="ref_instance" value="'.$ref_instance.'" />

<input type="hidden" name="action" value="modifier_document" />

<!-- Ajout pour les filtres -->
<input type="hidden" name="f_auteur" value="'.$data_f->f_auteur.'" />
<input type="hidden" name="f_validation" value="'.$data_f->f_validation.'" />
<input type="hidden" name="f_referent" value="'.$data_f->f_referent.'" />
<input type="hidden" name="f_date_modif" value="'.$data_f->f_date_modif.'" />
<input type="hidden" name="f_date_modif_student" value="'.$data_f->f_date_modif_student.'" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="'.$form->courseid.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="modulename"    value="'.$form->modulename.'" />
<input type="hidden" name="instance"      value="'.$form->instance.'" />
<input type="hidden" name="mode"          value="'.$mode.'" />
<input type="submit" value="'.get_string("savedoc", "referentiel").'" />
<input type="submit" name="delete" value="'.get_string("delete").'" />
<!-- input type="submit" name="cancel" value='.get_string("quit","referentiel").' / -->

</form>	
</div>
';
			}
		}
        // affichage
        echo $s;
	}
	/////////////////// FIN MODIFIER ////////////////////////////////////////////

	/////////////////// SUPPRIMER ////////////////////////////////////////////
	else if (isset($mode) && ($mode=="deleteactivity")){
		/// Confirmer la suppression d'un enregistrement
		if (isset($activite_id) && ($activite_id>0)){
			// notice_yesno(get_string('confirmdeleterecord','referentiel'),
            echo $OUTPUT->confirm(get_string('confirmdeleterecord','referentiel'),
            'activite.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&amp;delete='.$activite_id.'&amp;userid='.$userid.'&amp;mode='.$old_mode.'&amp;confirm=1&amp;sesskey='.sesskey(),
	        'activite.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&amp;userid='.$userid.'&amp;mode='.$old_mode.'&amp;sesskey='.sesskey());
		}
		else{
			print_print_error(get_string('noactivite','referentiel'), "activite.php?d=$referentiel->id&amp;mode='.$old_mode.'");
		}
	}
	/////////////////// VALIDER ////////////////////////////////////////////
	// http://localhost/moodle253/mod/referentiel/activite.php?id=3&select_acc=1&activite_id=2&mode=desapproveactivity&old_mode=listactivity&sesskey=DMAXwzwehY
	else if (isset($mode) && ($mode=="approveactivity")){
		if (isset($activite_id) && ($activite_id>0)){
			//notice_yesno
	        if (empty($old_mode)){
    	        $old_mode="listactivityall";
	        }

            echo $OUTPUT->confirm(get_string('confirmvalidateactivity','referentiel'),
			'activite.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&amp;approved='.$activite_id.'&amp;userid='.$userid.'&amp;confirm=1&amp;mode='.$old_mode.'&amp;sesskey='.sesskey(),
			'activite.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&amp;approved='.$activite_id.'&amp;userid='.$userid.'&amp;confirm=0&amp;mode='.$old_mode.'&amp;sesskey='.sesskey());
		}
		else{
			print_print_error(get_string('noactivite','referentiel'), "activite.php?d=$referentiel->id.'&amp;userid='.$userid.'&amp;mode='.$old_mode.'");
		}
	}
	/////////////////// DE-VALIDER ////////////////////////////////////////////
	else if (isset($mode) && ($mode=="desapproveactivity")){
		if (isset($activite_id) && ($activite_id>0)){
			//notice_yesno
            echo $OUTPUT->confirm(get_string('confirmdevalidateactivity','referentiel'),
			'activite.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&amp;approved='.$activite_id.'&amp;userid='.$userid.'&amp;confirm=0&amp;mode='.$old_mode.'&amp;sesskey='.sesskey(),
			'activite.php?d='.$referentiel->id.'&amp;select_acc='.$select_acc.'&amp;approved='.$activite_id.'&amp;userid='.$userid.'&amp;confirm=1&amp;mode='.$old_mode.'&amp;sesskey='.sesskey());
		}
		else{
			print_print_error(get_string('noactivite','referentiel'), "activite.php?d=$referentiel->id.'&amp;userid='.$userid.'&amp;mode='.$old_mode.'");
		}
	}

    	/////////////////// COMMENTER ////////////////////////////////////////////
	else if (isset($mode) && ($mode=="commentactivity")){

		$activite_id=$record->id;
		$type_activite = stripslashes($record->type_activite);
		$description_activite = stripslashes($record->description_activite);
		$competences_activite = $record->competences_activite;
		$commentaire_activite = stripslashes($record->commentaire_activite);
		$ref_instance = $record->ref_instance;
		$ref_referentiel = $record->ref_referentiel;
		$ref_course = $record->ref_course;
		$userid = $record->userid;
		$teacherid = $record->teacherid;
		$date_creation = $record->date_creation;
		$date_modif = $record->date_modif;
		$approved = $record->approved;

		$user_info=referentiel_get_user_info($userid);
		$teacher_info=referentiel_get_user_info($teacherid);
		// dates
		$date_creation_info=userdate($date_creation);
		if ($date_modif!=0){
            $date_modif_info=userdate($date_modif);
		}
		else{
            $date_modif_info='';
        }
		// MODIF JF 27/10/2009
		$date_modif_student = $record->date_modif_student;
		if ($date_modif_student!=0){
			$date_modif_student_info=userdate($date_modif_student);
		}
		else{
			$date_modif_student_info='';
		}

		$link_documents=referentiel_get_liens_documents($activite_id, $userid, $context);
        if ($link_documents){
            echo '<br />'."\n";
        }

 		// preparer les variables globales pour Overlib
		referentiel_initialise_data_referentiel($ref_referentiel);
		$jauge_activite_declarees=referentiel_print_jauge_activite($userid, $ref_referentiel);
		if ($jauge_activite_declarees){
			print_string('competences_declarees','referentiel', referentiel_get_user_info($userid));
			//echo '<br />DEBUT DEBUG'."\n";
			echo $jauge_activite_declarees."\n";
			//echo '<br />FIN DEBUG'."\n";
		}

		echo '<div class="ref_saisie1">
<form name="form" method="post" action="'.s("activite.php?id=$cm->id").'">
<span class="bold">'.get_string('id','referentiel').'</span>';
		echo $activite_id;
		echo '<span class="bold">'.get_string('type_activite','referentiel').'</span>'.s($type_activite).'
<br /><span class="bold">'.get_string('auteur','referentiel').'</span>'.s($user_info);
		echo ' <span class="bold">'.get_string('date_creation','referentiel').'</span>'.s($date_creation_info);
		echo '<br />'."\n";
		if (isset($approved) && ($approved)){
			echo ' <span class="valide">'."\n";
		}
		else{
			echo ' <span class="invalide">'."\n";
		}
		echo '<span class="bold">'.get_string('liste_codes_competence','referentiel').'</span>'."\n";
		echo referentiel_affiche_liste_codes_competence('/',$competences_activite, $ref_referentiel)."\n";
        echo '</span>'."\n";
		if ($CFG->referentiel_use_scale){
			require_once('lib_bareme.php');
			if ($rec_assoc=referentiel_get_assoc_bareme_occurrence($ref_referentiel)){
				if ($bareme=referentiel_get_bareme($rec_assoc->refscaleid)){
					$competences_bareme=referentiel_get_competences_activite($activite_id, $bareme->id);
					if (empty($competences_bareme)){ // creer le bareme
						$competences_bareme=referentiel_creer_competences_activite($record_a, $bareme);
					}
					if ($competences_bareme){
                        echo '</span><br /><span class="bold">'.get_string('evaluation','referentiel').'</span><br />'.referentiel_affiche_bareme_activite($competences_bareme, $bareme, true);
					}
				}
			}
		}

		echo '<br /><span class="bold">'.get_string('description','referentiel').'</span><br /><span class="white">'.nl2br($description_activite).'</span>
<br />
';

		echo '<br /><span class="bold">'.get_string('commentaire','referentiel').'</span>';
		echo '<br /><textarea cols="80" rows="10" name="commentaire_activite">'.s($commentaire_activite).'</textarea>
<br /><span class="bold">'.get_string('referent','referentiel').'</span>'.s($teacher_info).'
<br />'."\n";
		echo '<span class="bold">'.get_string('modification','referentiel').'</span>'."\n";
		if (!empty($date_modif_info)){
	 		echo get_string('date_modif_by','referentiel').'<i>'.$date_modif_info.'</i>';
		}
		echo ' '.get_string('date_modif_student_by','referentiel').'<i>'.$date_modif_student_info.'</i>'."\n";
		echo '<br /><span class="bold">'.get_string('validation','referentiel').'</span>'."\n";
		if (!empty($approved)){
			// print_string('approved','referentiel');
			echo '<input type="radio" name="approved" value="1" checked="checked" />'.get_string('yes').' &nbsp; <input type="radio" name="approved" value="0" />'.get_string('no').' &nbsp; &nbsp; '."\n";
		}
		else{
			// print_string('not_approved','referentiel');
			echo '<input type="radio" name="approved" value="1" />'.get_string('yes').' &nbsp; <input type="radio" name="approved" value="0" checked="checked" />'.get_string('no').' &nbsp; &nbsp; '."\n";
		}
		echo '<br /><span class="bold">'.get_string('notification_commentaire','referentiel').'</span>'."\n";
		echo '<input type="radio" name="mailnow" value="1" />'.get_string('yes').' &nbsp; <input type="radio" name="mailnow" value="0" checked="checked" />'.get_string('no').' <br /><br />'."\n";
?>
<input type="hidden" name="select_acc" value="<?php echo $select_acc; ?>" />
<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
<input type="hidden" name="comment" value="<?php echo $activite_id; ?>" />
<input type="hidden" name="activite_id" value="<?php  echo $activite_id; ?>" />

<!-- Ajout pour les filtres -->
<input type="hidden" name="f_auteur" value="<?php echo $data_f->f_auteur; ?>" />
<input type="hidden" name="f_validation" value="<?php echo $data_f->f_validation; ?>" />
<input type="hidden" name="f_referent" value="<?php echo $data_f->f_referent; ?>" />
<input type="hidden" name="f_date_modif" value="<?php echo $data_f->f_date_modif; ?>" />
<input type="hidden" name="f_date_modif_student" value="<?php echo $data_f->f_date_modif_student; ?>" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="<?php  p($ref_course) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<?php
        if (!empty($old_mode)){
            echo '<input type="hidden" name="mode" value="'.$old_mode.'" />'."\n";
        }
        else{
            echo '<input type="hidden" name="mode" value="listactivityall" />'."\n";
        }
?>

<input type="submit" value="<?php  print_string("savechanges") ?>" />
<input type="submit" name="cancel" value="<?php  print_string("quit","referentiel") ?>" />
</form>
</div>
<?php
        if ($link_documents){
            echo '<span class="vert">'."\n".$link_documents."\n".'</span><br />'."\n";
        }
	}
}

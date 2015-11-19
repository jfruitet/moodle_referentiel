<?php
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
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set


/**
 * Standard base class for all referentiel occurrence table.
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitete {@link http://www.univ-nantes.fr/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot.'/mod/referentiel/lib.php');
require_once($CFG->dirroot.'/mod/referentiel/locallib.php');

class occurrence {


    var $referentiel; // L'occurrence
    var $context;
	var $courseid;
	var $blockid;

    /*
	// referentiel of table referentiel_referentiel
    var $id;
	var $name;
    var $code_referentiel;
	var $description_referentiel;
	var $url_referentiel;
	var $logo_referentiel;
    var $local;
	var $liste_codes_competence;
	var $liste_empreintes_competence;
	var $liste_poids_competence;
    var $label_domaine;
    var $label_competence;
    var $label_item;
    var $config;
    var $config_impression;

	var cle_referentiel;
	var mail_auteur_referentiel;
	var pass_referentiel;
    */

    /**
     * Constructor for the base occurrence class
     *
     *
     * @global object
     * @param int $referentiel_referentiel id
     */
    function occurrence($params) {
        global $COURSE, $DB;
        global $CFG;
        if (!empty($params)){
			if (!empty($params['blockid'])) {
            	$this->blockid=$params['blockid'];
       		}
			if (!empty($params['courseid'])) {
            	$this->courseid=$params['courseid'];
       		}

			if (!empty($params['occurrenceid'])) {
            	$this->referentiel = $DB->get_record('referentiel_referentiel', array('id' => $params['occurrenceid']));
        	} else {
				// valeurs par defaut
        		$this->referentiel=$this->new_occurrence();
         	}
		}
    }

	// -----------------------
	function creer_configuration($type='config'){
	// initialise le vecteur de configuration
	global $CFG;
	$s='';
	if ($type=='config'){
		// configuration
        // affichage hierarchique saisie des competence
		$s.='hierarchy:0;';

        // affichage reduit du referentiel sans les poids et les empreintes
		if (isset($CFG->referentiel_light_display)){
			$s.='light:'.$CFG->referentiel_light_display.';';
		}
		else{
			$s.='light:0;';
		}

		if (isset($CFG->referentiel_scolarite_masquee)){
			$s.='scol:'.$CFG->referentiel_scolarite_masquee.';';
		}
		else{
			$s.='scol:0;';
		}
		if (isset($CFG->referentiel_creation_limitee)){
			$s.='creref:'.$CFG->referentiel_creation_limitee.';';
		}
		else{
			$s.='creref:0;';
		}
		if (isset($CFG->referentiel_selection_autorisee)){
			$s.='selref:'.$CFG->referentiel_selection_autorisee.';';
		}
		else{
			$s.='selref:0;';
		}
		if (isset($CFG->referentiel_impression_autorisee)){
			$s.='impcert:'.$CFG->referentiel_impression_autorisee.';';
		}
		else{
			$s.='impcert:0;';
		}
		if (isset($CFG->referentiel_affichage_graphique)){
			$s.='graph:'.$CFG->referentiel_affichage_graphique.';';
		}
		else{
			$s.='graph:0;';
		}
		if (isset($CFG->referentiel_certif_config)){
			$s.='cfcertif:'.$CFG->referentiel_certif_config.';';
		}
		else{
			$s.='cfcertif:0;';
		}
		if (isset($CFG->referentiel_certif_state)){
			$s.='certif:'.$CFG->referentiel_certif_state.';';
		}
		else{
			$s.='certif:1;';
		}
		}
		else{
		// impression certificat
		// instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;

		// impression certificat
		if (isset($CFG->certificat_sel_referentiel)){
			$s.='refcert:'.$CFG->certificat_sel_referentiel.';';
		}
		else{
			$s.='refcert:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_referentiel_instance)){
			$s.='instcert:'.$CFG->certificat_sel_referentiel_instance.';';
		}
		else{
			$s.='instcert:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_numero)){
			$s.='numetu:'.$CFG->certificat_sel_etudiant_numero.';';
		}
		else{
			$s.='numetu:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_nom_prenom)){
			$s.='nometu:'.$CFG->certificat_sel_etudiant_nom_prenom.';';
		}
		else{
			$s.='nometu:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_etablissement)){
			$s.='etabetu:'.$CFG->certificat_sel_etudiant_etablissement.';';
		}
		else{
			$s.='etabetu:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_ddn)){
			$s.='ddnetu:'.$CFG->certificat_sel_etudiant_ddn.';';
		}
		else{
			$s.='ddnetu:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_lieu_naissance)){
			$s.='lieuetu:'.$CFG->certificat_sel_etudiant_lieu_naissance.';';
		}
		else{
			$s.='lieuetu:0;';
		}

				// impression certificat
		if (isset($CFG->certificat_sel_etudiant_adresse)){
			$s.='adretu:'.$CFG->certificat_sel_etudiant_adresse.';';
		}
		else{
			$s.='adretu:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_detail)){
			$s.='detail:'.$CFG->certificat_sel_certificat_detail.';';
		}
		else{
			$s.='detail:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_pourcent)){
			$s.='pourcent:'.$CFG->certificat_sel_certificat_pourcent.';';
		}
		else{
			$s.='pourcent:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_activite_competences)){
			$s.='compdec:'.$CFG->certificat_sel_activite_competences.';';
		}
		else{
			$s.='compdec:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_competences)){
			$s.='compval:'.$CFG->certificat_sel_certificat_competences.';';
		}
		else{
			$s.='compval:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_referents)){
			$s.='nomreferent:'.$CFG->certificat_sel_certificat_referents.';';
		}
		else{
			$s.='nomreferent:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_decision_jury)){
			$s.='jurycert:'.$CFG->certificat_sel_decision_jury.';';
		}
		else{
			$s.='jurycert:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_commentaire)){
			$s.='comcert:'.$CFG->certificat_sel_commentaire.';';
		}
		else{
			$s.='comcert:0;';
		}
		}
		return $s;
	}


	// -----------------------
	function new_occurrence(){
        global $USER;
        $an_occurrence= new Object();
        $an_occurrence->id=0;
		$an_occurrence->name="";
    	$an_occurrence->code_referentiel="";
        $an_occurrence->cle_referentiel="";
    	$an_occurrence->mail_auteur_referentiel=$USER->email;
    	$an_occurrence->pass_referentiel="";
    	$an_occurrence->seuil_certificat=0;
        $an_occurrence->minima_certificat=0;
        $an_occurrence->timemodified=time();
    	$an_occurrence->description_referentiel="";
    	$an_occurrence->url_referentiel="";
        $an_occurrence->nb_domaines=1;
    	$an_occurrence->liste_codes_competence="";
    	$an_occurrence->liste_empreintes_competence="";
    	$an_occurrence->liste_poids_competence="";
        $an_occurrence->local=-1; // cours id non exists :: a course id or a 0 value wil be set later during creation
    	$an_occurrence->logo_referentiel="";
    	$an_occurrence->config=$this->creer_configuration('config');
    	$an_occurrence->config_impression=$this->creer_configuration('config_impression');
        $an_occurrence->label_domaine=trim(get_string('domaine','referentiel'));
        $an_occurrence->label_competence=trim(get_string('competence','referentiel'));
        $an_occurrence->label_item=trim(get_string('item','referentiel'));
		return $an_occurrence;
	}

	// ---------------------------------
	function get_item_config($item, $type='config') {
	// retourne la valeur de configuration globale (au niveau du referentiel) pour l'item considere
	// 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
	// type : config ou config_impression
	global $CFG;
		if ($type=='config'){
			$str_config = $this->referentiel->config;
		}
		else{
			$str_config = $this->referentiel->config_impression;
		}
		if ($str_config!=''){
			$tconfig=explode(';',$str_config);
			$n=count($tconfig);
			if ($n>0){
				$i=0;
				while ($i<$n){
					$tconfig[$i]=trim($tconfig[$i]);
					if ($tconfig[$i]!=''){
						list($cle, $val)=explode(':',$tconfig[$i]);
						$cle=trim($cle);
						$val=trim($val);

						if ($cle==$item){
							return ($val);
						}
					}
					$i++;
				}
			}
		}

		return 0;
	}


	// -----------------------
	function get_display() {
	// checks configuration at site and occurrence level
	// checks if light display is allowed
		global $CFG;
		// configuration
    	if (!isset($CFG->referentiel_light_display)){
			$CFG->referentiel_light_display=0;
		}
		if ($CFG->referentiel_light_display!=2) {
         	if ($this->referentiel->id){
            	return($this->get_item_config('light', 'config')==0);
            }
        }
    	return false;
	}

	// -----------------------
	function can_edit() {
	// checks configuration at site and occurrence level
	// checks if occurrence edition is allowed
    	global $CFG;
		// configuration
    	if (!isset($CFG->referentiel_creation_limitee)){
			$CFG->referentiel_creation_limitee=0;
		}
		if ($CFG->referentiel_creation_limitee!=2) {
         	if ($this->referentiel->id){
            	return($this->get_item_config('creref', 'config')==0);
            }
        }
    	return false;
	}

    // -------------------------
	function roles(){
		global $COURSE;
    	$role= new stdClass();
        $role->can_edit = false;
        $role->can_list = false;
        if ($context = context_course::instance($COURSE->id)){
        	$role->is_admin = has_capability('mod/referentiel:managescolarite', $context);
        	$role->can_edit = has_capability('mod/referentiel:writereferentiel', $context);
        	$role->can_list = has_capability('mod/referentiel:write', $context);
    	}
    	return $role;
	}

	// -------------------------
	function is_author(){
		global $USER;
		if (!empty($this->referentiel->mail_auteur_referentiel)
			&&
			($this->referentiel->mail_auteur_referentiel==$USER->email)){
				return 1;
		}
  		else {
            return 0;
		}
	}

	// -------------------------
	function can_edit_or_import(){
	// rechercher la configuration pour ce referentiel
        // Rôles dans le cours
        $roles=$this->roles();
		if ($roles->can_edit){
			if ($roles->is_admin || ($this->is_author() && $this->can_edit())){
				return true;
			}
		}
        return false;
	}

	// -----------------------
	function display_liste_codes_empreintes_competence(){
	// affiche des codes, poids et empreintes dans un tableau
		$s="";
		$c="";
		$e="";
		$p="";
		$tcode=explode('/',$this->referentiel->liste_codes_competence);
		$tpoids=explode('/',$this->referentiel->liste_poids_competence);
		$tempreinte=explode('/',$this->referentiel->liste_empreintes_competence);
		if (($tcode) && (count($tcode)>0)){
			$s.="<div class='aff1'>";
			$i=0;
			while ($i<count($tcode)){
				if (!empty($tcode[$i])){
					$c="<span class='code'>".$tcode[$i]."</span>";
			 		$p="<span class='poids'>".$tpoids[$i]."</span>";
   					$e="<span class='empreinte'><i>".$tempreinte[$i]."</i></span>";
                	$s.=$c.$p.$e;
				}
				$i++;
        	}
			$s.="</div>\n";
		}
		return $s;
	}


    // -----------------------
	function tabs($mode, $currenttab) {
        require_once('thumbs.php');
        // DEBUG
		//echo "<br />occurrence_class.php :: 422<br />".$this->referentiel->id.", ".$this->blockid.", ".$this->courseid.", ".$currenttab.", ".$mode.", ".$this->can_edit_or_import()."\n";
        $tab_thumbs = new Thumbs($this->referentiel->id, $this->blockid, $this->courseid, $currenttab, $mode, $this->can_edit_or_import());
        $tab_thumbs->display();
    }

    // -----------------------
	function view($params=NULL){
	// Affiche referential occurrence
		global $DB;
    	$labels=NULL;
		$label_d='';
		$label_c='';
		$label_i='';

		if (!empty($params)){
			if (isset($params->label_domaine)){
				$label_d=$params->label_domaine;
			}
			if (isset($params->label_competence)){
				$label_c=$params->label_competence;
			}
			if (isset($params->label_item)){
				$label_i=$params->label_item;
			}
		}
		else{
        	$label_d=$this->referentiel->label_domaine;
        	$label_c=$this->referentiel->label_competence;
        	$label_i=$this->referentiel->label_item;
		}
		// affichage leger du referentiel
		$not_light_display=$this->get_display('ligth')>0;

		if (!empty($this->referentiel)){

			echo '<h3>'.get_string('occurrencereferentiel','referentiel').'</h3>'."\n";
			echo '<div class="ref_aff0">'."\n";
			echo '<span class="bold">'.get_string('name','referentiel').'</span> &nbsp; '.$this->referentiel->name.' &nbsp; &nbsp; '."\n";
			echo '<span class="bold">'.get_string('code','referentiel').'</span> &nbsp; '.$this->referentiel->code_referentiel.' &nbsp; &nbsp; '."\n";
			echo '<br />'.'<span class="bold">'.get_string('description','referentiel').'</span><div class="ref_aff1">'.$this->referentiel->description_referentiel.'</div>'."\n";
			echo '<span class="bold">'.get_string('url','referentiel').'</span> &nbsp; <a target="_blank" href="'.$this->referentiel->url_referentiel.'">'.$this->referentiel->url_referentiel.'</a> &nbsp; &nbsp; '."\n";
			if ($not_light_display){
				echo '<br />'.'<span class="bold">'.get_string('seuil_certificat','referentiel').'</span> &nbsp; '.$this->referentiel->seuil_certificat."\n";
				echo ' &nbsp; '.'<span class="bold">'.get_string('referentiel_global','referentiel').'</span> ';
				if (!empty($this->referentiel->local)){
					echo '&nbsp;'.get_string("no").' &nbsp; &nbsp; '."\n";
				}
				else{
					echo '&nbsp; '.get_string("yes").' &nbsp; &nbsp; '."\n";
				}
				echo '<br />'.'<span class="bold">'.get_string('logo','referentiel').'</span>  &nbsp; '."\n";
				if (!empty($this->referentiel->logo)){
					echo referentiel_affiche_image($this->referentiel->logo).' &nbsp; &nbsp; '."\n";
				}
    			// echo referentiel_menu_logo($cm, !empty($logo))."\n";
    			echo '<br />'.'<span class="bold">'.get_string('liste_codes_empreintes_competence','referentiel').'</span>';
    			echo '<br />'.$this->display_liste_codes_empreintes_competence()."\n";
			}
			echo '</div>'."\n";
			echo '<br />'."\n";
?>
<table class="referentiel" cellpadding="5">
<?php

			// charger les domaines associes au referentiel courant
			if (!empty($this->referentiel->id)){
				// AFFICHER LA LISTE DES DOMAINES
				$compteur_domaine=0;
				$records_domaine = referentiel_get_domaines($this->referentiel->id);
	    		if ($records_domaine){
    				// afficher
					foreach ($records_domaine as $record){
						$compteur_domaine++;
    	    			$domaine_id=$record->id;
						$nb_competences = $record->nb_competences;
						$code_domaine = stripslashes($record->code_domaine);
						$description_domaine = stripslashes($record->description_domaine);
						$num_domaine = $record->num_domaine;
?>
<!-- DOMAINE -->
<tr valign="top" bgcolor="#ffffcc">
    <td class="domaine" align="left"><b>
<?php
						if (!empty($label_d)){
							p($label_d);
						}
						else {
							print_string('domaine','referentiel') ;
						}
						echo ' <i>'.s($num_domaine).'</i>';
?>
</b>
    </td>
    <td class="domaine" align="left">
        <?php  p($code_domaine) ?>
    </td>
    <td class="domaine" align="left" colspan="4">
		<?php  echo (stripslashes($record->description_domaine)); ?>
    </td>
</tr>

<?php
						// LISTE DES COMPETENCES DE CE DOMAINE
						$compteur_competence=0;
						$records_competences = referentiel_get_competences($domaine_id);
			    		if ($records_competences){
							foreach ($records_competences as $record_c){
								$compteur_competence++;
    	    					$competence_id=$record_c->id;
								$nb_item_competences = $record_c->nb_item_competences;
								$code_competence = stripslashes($record_c->code_competence);
								$description_competence = stripslashes($record_c->description_competence);
								$num_competence = $record_c->num_competence;
								$ref_domaine = $record_c->ref_domaine;
?>
<!-- COMPETENCE -->
<tr valign="top">
    <td class="competence" align="left">
<b>
<?php
								if (!empty($label_c)){
									p($label_c);
								}
								else {
									print_string('competence','referentiel') ;
								}
?>

<i>
<?php
								p(' '.$num_competence)
?>
</i>
</b>
    </td>
    <td class="competence" align="left">
<?php
								p($code_competence)
?>
    </td>
    <td class="competence" align="left" colspan="4">
<?php
								echo (stripslashes($description_competence)); ?>
    </td>
</tr>
<?php
							// ITEM
							$compteur_item=0;
							$records_items = referentiel_get_item_competences($competence_id);
						    if ($records_items){

?>
<tr valign="top" bgcolor="#5555000">
    <th class="item" align="right">

<?php
								if (!empty($label_i)){
									p($label_i);
								}
								else {
									print_string('item','referentiel') ;
								}
    							echo ' :: <i>';
    							print_string('numero', 'referentiel');
    							echo '</i>';
?>
    </th>
    <th class="item" align="left">
		<?php  print_string('code', 'referentiel');?>
    </th>
    <th class="item" align="left">
<?php
								print_string('description', 'referentiel');
?>
    </th>

    <?php
    							if ($not_light_display){
    ?>
    <th class="item" align="left">
<?php
									print_string('t_item', 'referentiel');
?>
    </th>
    <th class="item" align="left">
<?php   print_string('p_item', 'referentiel'); ?>
    </th>
    <th class="item" align="left">
<?php   print_string('e_item', 'referentiel'); ?>
    </th>
    <?php
    }
    else{
        // echo '<th class="item" colspan="3">&nbsp;</th>'."\n";
    }
    ?>

</tr>
<?php

								foreach ($records_items as $record_i){
									$compteur_item++;
	    		    				$item_id=$record_i->id;
									$code_item = stripslashes($record_i->code_item);
									$description_item = stripslashes($record_i->description_item);
									$num_item = $record_i->num_item;
									$type_item = stripslashes($record_i->type_item);
									$poids_item = $record_i->poids_item;
									$empreinte_item = $record_i->empreinte_item;
									$ref_competence=$record_i->ref_competence;
?>
<tr valign="top" bgcolor="#ffeefe">
    <td class="item" align="right" bgcolor="#ffffff">
<i>
<?php  p($num_item) ?>
</i>
    </td>
    <td class="item" align="left">
		<?php  p($code_item) ?>
    </td>
    <td class="item" align="left">
<?php  echo (nl2br(stripslashes($description_item))); ?>
    </td>
    <?php
    if ($not_light_display){
    ?>
    <td class="item" align="left">
<?php  p($type_item) ?>
    </td>
    <td class="poids" align="left">
<?php  p($poids_item) ?>
    </td>
    <td class="empreinte" align="left">
<?php  p($empreinte_item) ?>
    </td>
    <?php
    }
    else{
       // echo '<td colspan="3">&nbsp;</td>'."\n";
    }
    ?>

</tr>
<?php
								}
							}
						}
					}
				}
			}
		}
?>
</table>
<?php
	}

	}

}

?>
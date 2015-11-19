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

/**
 * Forms.
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->dirroot.'/blocks/referentiel/lib.php');
require_once($CFG->dirroot.'/mod/referentiel/lib.php');
require_once($CFG->dirroot.'/mod/referentiel/locallib.php');

class occurrence_form extends moodleform {
	var $context;
	var $referentiel;
	var $blockid;
	var $courseid;
	var $pass;
	var $compteur_domain;
    var $compteur_competency;
    var $compteur_item;
	var $list_domains;
	var $list_competencies;
	var $list_items;
    var $compteur_newdomain;
	var $compteur_newcompetency;
	var $compteur_newitem;
	var $list_newdomains;
	var $list_newcompetencies;
	var $list_newitems;


    function definition() {
        $mform = & $this->_form;
        $arguments = $this->_customdata;  // call new form

        // arguments
        if (isset($arguments['options'])){
            $options=$arguments['options'];
        }
        else{
            $option=NULL;
        }

		if (isset($options['pass'])){
			$this->pass=$options['pass'];
		}
		else{
            $this->pass=0;
		}

        if (isset($arguments['context'])){
            $this->context=$arguments['context'];
        }
        else{
            $this->context=NULL;
        }

        if (isset($arguments['blockid'])){
            $this->blockid=$arguments['blockid'];
        }
        else{
            $this->blockid=0;
        }

        if (isset($arguments['courseid'])){
            $this->courseid=$arguments['courseid'];
        }
        else{
            $this->courseid=0;
        }

        if (isset($arguments['userid'])){
            $userid=$arguments['userid'];
        }
        else{
            $userid=0;
        }

        if (isset($arguments['occurrence'])){
            $this->referentiel=$arguments['occurrence'];
        }
        else{
            $this->referentiel=NULL;
        }

        if (isset($arguments['mode'])){
            $this->mode=$arguments['mode'];
        }
        else{
            $this->mode='edit';
        }

        if (!empty($this->referentiel)){

			$mform->addElement('hidden', 'blockid', $this->blockid);
            $mform->setType('blockid', PARAM_INT);
			$mform->addElement('hidden', 'courseid', $this->courseid);
            $mform->setType('courseid', PARAM_INT);
			$mform->addElement('hidden', 'occurrenceid', $this->referentiel->id);
            $mform->setType('occurrenceid', PARAM_INT);
            $mform->addElement('hidden', 'mode', $this->mode);
            $mform->addElement('hidden', 'sesskey', sesskey());
        	$mform->setType('sesskey', PARAM_TEXT);
            $mform->addElement('hidden', 'mode',  $this->mode);
			$mform->setType('mode', PARAM_ALPHA);
            $mform->addElement('hidden', 'pass',  $this->pass);
			$mform->setType('pass', PARAM_INT);

        	$this->edit_occurrence();

			$this->edit_domains_competencies_items();

		}
    }

	// -----------------
	function edit_occurrence() {
    	$mform = & $this->_form;

        	// add group for text areas
	        $mform->addElement('header','displayinfo', get_string('editoccurrence', 'block_referentiel'));
   			$mform->addHelpButton('displayinfo', 'modifreferentielh','referentiel');

			// add page title element.
			$mform->addElement('text', 'name', get_string('name', 'block_referentiel'), array('size'=>'80'));
    	    $mform->setType('name', PARAM_TEXT);
			$mform->addRule('name', null, 'required', null, 'client');
			$mform->setDefault('name', $this->referentiel->name);

			// code
			$mform->addElement('text', 'code_referentiel', get_string('code', 'block_referentiel'));
    	    $mform->setType('code_referentiel', PARAM_TEXT);
			$mform->addRule('code_referentiel', null, 'required', null, 'client');
            $mform->setDefault('code_referentiel', $this->referentiel->code_referentiel);

			// add display text field
			$mform->addElement('htmleditor', 'description_referentiel', get_string('description', 'block_referentiel'));
			$mform->setType('description_referentiel', PARAM_RAW);
			$mform->addRule('description_referentiel', null, 'required', null, 'client');
            $mform->setDefault('description_referentiel', $this->referentiel->description_referentiel);

   			// hidden params
        	$mform->addElement('hidden', 'url_referentiel', $this->referentiel->url_referentiel);
        	$mform->setType('url_referentiel', PARAM_TEXT);
        	$mform->addElement('hidden', 'logo_referentiel', $this->referentiel->logo_referentiel);
        	$mform->setType('logo_referentiel', PARAM_TEXT);

            // Local or global
			if (!empty($this->referentiel->local) && ($this->referentiel->local==-1)){ // creation en cours
                $radioarray=array();
				$radioarray[] =& $mform->createElement('radio', 'local', '', get_string('yes').' ', $this->courseid, null);
				$radioarray[] =& $mform->createElement('radio', 'local', '', get_string('no').' ', 0, null);
				$mform->addGroup($radioarray, 'local', get_string('local_course', 'block_referentiel'), array(' '), false);
       		    $mform->setDefault('yesno', 0);
			}
			else{
	       		$mform->addElement('hidden', 'local', $this->referentiel->local);
    	    	$mform->setType('local', PARAM_INT);
			}

			$mform->addElement('text', 'nb_domaines', get_string('nb_domaines', 'block_referentiel'));
    	    $mform->setType('nb_domaines', PARAM_INT);
			$mform->addRule('nb_domaines', null, 'required', null, 'client');
            $mform->setDefault('nb_domaines', $this->referentiel->nb_domaines);

        	$mform->addElement('hidden', 'liste_codes_competence', $this->referentiel->liste_codes_competence);
        	$mform->setType('liste_codes_competence', PARAM_TEXT);
        	$mform->addElement('hidden', 'liste_empreintes_competence', $this->referentiel->liste_empreintes_competence);
        	$mform->setType('liste_empreintes_competence', PARAM_TEXT);
        	$mform->addElement('hidden', 'liste_poids_competence', $this->referentiel->liste_poids_competence);
        	$mform->setType('liste_poids_competence', PARAM_TEXT);
        	/*
        	$mform->addElement('hidden', 'label_domaine', $this->referentiel->label_domaine);
			$mform->setType('label_domaine', PARAM_TEXT);
        	$mform->addElement('hidden', 'label_competence', $this->referentiel->label_competence);
        	$mform->setType('label_competence', PARAM_TEXT);
        	$mform->addElement('hidden', 'label_item', $this->referentiel->label_item);
        	$mform->setType('label_item', PARAM_TEXT);
 			*/
			$mform->addElement('text', 'label_domaine', get_string('label_domaine', 'referentiel'), 'size="30", maxlength="80"');
    	    $mform->setType('label_domaine', PARAM_TEXT);
            $mform->setDefault('label_domaine', $this->referentiel->label_domaine);
			$mform->addElement('text', 'label_competence', get_string('label_competence', 'referentiel'), 'size="30", maxlength="80"');
    	    $mform->setType('label_competence', PARAM_TEXT);
            $mform->setDefault('label_competence', $this->referentiel->label_competence);
			$mform->addElement('text', 'label_item', get_string('label_item', 'referentiel'), 'size="30", maxlength="80"');
    	    $mform->setType('label_item', PARAM_TEXT);
            $mform->setDefault('label_item', $this->referentiel->label_item);

            $mform->addElement('hidden', 'config', $this->referentiel->config);
			$mform->setType('config', PARAM_TEXT);
        	$mform->addElement('hidden', 'config_impression', $this->referentiel->config_impression);
        	$mform->setType('config_impression', PARAM_TEXT);

       		$mform->addElement('hidden', 'cle_referentiel', $this->referentiel->cle_referentiel);
        	$mform->setType('cle_referentiel', PARAM_TEXT);
       		$mform->addElement('hidden', 'mail_auteur_referentiel', $this->referentiel->mail_auteur_referentiel);
        	$mform->setType('mail_auteur_referentiel', PARAM_TEXT);
       		$mform->addElement('hidden', 'pass_referentiel', $this->referentiel->pass_referentiel);
        	$mform->setType('pass_referentiel', PARAM_TEXT);

            // buttons
        	// $this->add_action_buttons(true);
			//normally you use add_action_buttons instead of this code
			$buttonarray=array();
			$buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
			$buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('revert'));
			$buttonarray[] = &$mform->createElement('cancel');
			$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
			$mform->closeHeaderBefore('buttonar');

	}

    // --------------
	function add_item($competence_id){
		$sform='';
		$mform = & $this->_form;
		$code_item = '';
		$description_item = '';

		$type_item = 0;
		$poids_item = '1.0';
		$empreinte_item = '1';
        $num_item = $this->compteur_item;

		$ref_competence=$competence_id;
        $ref_referentiel=$this->referentiel->id;

        $sform= '<hr><h5>'.get_string('saisie_item_supplementaire','referentiel').'</h5>
<div class="newitem">';
        $mform->addElement('html', $sform);
       	if (!empty($this->list_newitems)) {
			$this->list_newitems.= ','.$this->compteur_newitem;
		}
		else{
            $this->list_newitems.= $this->compteur_newitem;
		}
       	$mform->addElement('hidden', 'new_ref_competence_'.$this->compteur_newitem, $ref_competence);
        $mform->setType('new_ref_competence_'.$this->compteur_newitem, PARAM_INT);

		// code
		$mform->addElement('text', 'new_code_item_'.$this->compteur_newitem, get_string('code_unique','referentiel'), 'size="20"  maxlength="20"');
   	    $mform->setType('new_code_item_'.$this->compteur_newitem, PARAM_TEXT);
		$mform->addRule('new_code_item_'.$this->compteur_newitem, null, 'required', null, 'client');
        $mform->setDefault('new_code_item_'.$this->compteur_newitem, $code_item);

		// add display textarea field
        $mform->addElement('textarea', 'new_description_item_'.$this->compteur_newitem, get_string('description', 'referentiel'), 'wrap="virtual" rows="3" cols="60"');
		$mform->setType('new_description_item_'.$this->compteur_newitem, PARAM_TEXT);
		$mform->addRule('new_description_item_'.$this->compteur_newitem, null, 'required', null, 'client');
        $mform->setDefault('new_description_item_'.$this->compteur_newitem, $description_item);

		$radioarray=array();
		$radioarray[] =& $mform->createElement('radio', 'new_type_item_'.$this->compteur_newitem, '', get_string('yes').' ', 1, null);
		$radioarray[] =& $mform->createElement('radio', 'new_type_item_'.$this->compteur_newitem, '', get_string('no').' ', 0, null);
		$mform->addGroup($radioarray, 'new_type_item_'.$this->compteur_newitem, get_string('type_item','referentiel'), array(' '), false);
		if (!empty($type_item)){
            $mform->setDefault('yesno', 0);
		}
		else{
            $mform->setDefault('yesno', 1);
		}

 		$mform->addElement('text', 'new_poids_item_'.$this->compteur_newitem,get_string('poids_item','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('new_poids_item_'.$this->compteur_newitem, PARAM_TEXT);
		$mform->addRule('new_poids_item_'.$this->compteur_newitem, get_string('decimal','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_poids_item_'.$this->compteur_newitem, $poids_item);

 		$mform->addElement('text', 'new_empreinte_item_'.$this->compteur_newitem,get_string('empreinte_item','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('new_empreinte_item_'.$this->compteur_newitem, PARAM_INT);
		$mform->addRule('new_empreinte_item_'.$this->compteur_newitem, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_empreinte_item_'.$this->compteur_newitem, $empreinte_item);

 		$mform->addElement('text', 'new_num_item_'.$this->compteur_newitem,get_string('num_item','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('new_num_item_'.$this->compteur_newitem, PARAM_INT);
		$mform->addRule('new_num_item_'.$this->compteur_newitem, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_num_item_'.$this->compteur_newitem, $num_item);

		$mform->addElement('html',  '</div>');
	}


	// --------------
	function edit_item($rec){
    	global $CFG;
		$sform='';
		$mform = & $this->_form;

		$item_id=$rec->id;
		$oldcode=$rec->code_item;
		$code_item = $rec->code_item;
		$description_item = stripslashes($rec->description_item);
		$num_item = $rec->num_item;
		$type_item = $rec->type_item;
		$poids_item = $rec->poids_item;
		$empreinte_item = $rec->empreinte_item;
		$ref_competence=$rec->ref_competence;
        $ref_referentiel=$rec->ref_referentiel;


		// DEBUG
		// $sform.= '"<br/>DEBUG 190 ::ITEM :: COMPTEUR : $compteur_item, ID : $item_id, CODE : $code_competence, DESCRIPTION : $description_competence, NUM : $num_item;<br />\n";
        // $mform->addElement('html', $sform);
		// afficher le formulaire

		$sform= '<hr><h5>'.get_string('item','referentiel').'</h5>
<div class="item">
<b>'.get_string('id','referentiel').' : </b>'.$item_id;
        $mform->addElement('html', $sform);

/*
		$mform->addElement('advcheckbox','titem_id[]', get_string('select_item', 'referentiel'),get_string('cocher_enregistrer_domain', 'referentiel'), array('group' => 1), array(0, $item_id));
*/
        $mform->addElement('checkbox', 'titem_id_'.$item_id, get_string('modified_item', 'block_referentiel'), '<i>&lt;---- '.get_string('check_item', 'block_referentiel').'</i>');
   	    $mform->setType('titem_id_'.$item_id, PARAM_INT);
		$mform->addRule('titem_id_'.$item_id, null, 'numeric', null, 'client');
        $mform->setDefault('titem_id_'.$item_id, 0);

		if (!empty($this->list_items)) {
			$this->list_items.= ','.$item_id;
		}
		else{
            $this->list_items.= $item_id;
		}

       	$mform->addElement('hidden', 'ref_competence_'.$item_id, $ref_competence);
        $mform->setType('ref_competence_'.$item_id, PARAM_INT);

		// code
		$mform->addElement('text', 'code_item_'.$item_id, get_string('code_unique','referentiel'), 'size="20"  maxlength="20"');
   	    $mform->setType('code_item_'.$item_id, PARAM_TEXT);
		$mform->addRule('code_item_'.$item_id, null, 'required', null, 'client');
        $mform->setDefault('code_item_'.$item_id, $code_item);

		//<input type="text" name="code_item_'.$item_id.'" size="20" maxlength="20" value="'.$code_item.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')" />

		// add display textarea field
        $mform->addElement('textarea', 'description_item_'.$item_id, get_string('description', 'referentiel'), 'wrap="virtual" rows="3" cols="60"');
		$mform->setType('description_item_'.$item_id, PARAM_TEXT);
		$mform->addRule('description_item_'.$item_id, null, 'required', null, 'client');
        $mform->setDefault('description_item_'.$item_id, $description_item);

		// <textarea cols="60" rows="5" name="description_item_'.$item_id.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')" >'.$description_item.'</textarea>

		$radioarray=array();
		$radioarray[] =& $mform->createElement('radio', 'type_item_'.$item_id, '', get_string('yes').' ', 1, null);
		$radioarray[] =& $mform->createElement('radio', 'type_item_'.$item_id, '', get_string('no').' ', 0, null);
		$mform->addGroup($radioarray, 'type_item_'.$item_id, get_string('type_item','referentiel'), array(' '), false);
		if (!empty($type_item)){
            $mform->setDefault('yesno', 0);
			// <input type="radio" name="type_item_'.$item_id.'" id="type_item_'.$item_id.'" value="1" checked="checked" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />'."\n";
			// <input type="radio" name="type_item_'.$item_id.'" id="type_item_'.$item_id.'" value="0" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />'."\n";
		}
		else{
            $mform->setDefault('yesno', 1);
			// <input type="radio" name="type_item_'.$item_id.'" id="type_item_'.$item_id.'" value="1" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />'."\n";
			// <input type="radio" name="type_item_'.$item_id.'" id="type_item_'.$item_id.'" value="0" checked="checked" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />'."\n";
		}

 		$mform->addElement('text', 'poids_item_'.$item_id,get_string('poids_item','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('poids_item_'.$item_id, PARAM_TEXT);
		$mform->addRule('poids_item_'.$item_id, get_string('decimal','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('poids_item_'.$item_id, $poids_item);

// <input type="text" name="poids_item_'.$item_id.'" size="5" maxlength="10" value="'.$poids_item.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />
 		$mform->addElement('text', 'empreinte_item_'.$item_id,get_string('empreinte_item','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('empreinte_item_'.$item_id, PARAM_INT);
		$mform->addRule('empreinte_item_'.$item_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('empreinte_item_'.$item_id, $empreinte_item);

// <input type="text" name="empreinte_item_'.$item_id.'" size="3" maxlength="3" value="'.$empreinte_item.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />
 		$mform->addElement('text', 'num_item_'.$item_id,get_string('num_item','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('num_item_'.$item_id, PARAM_INT);
		$mform->addRule('num_item_'.$item_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('num_item_'.$item_id, $num_item);
// <input type="text" name="num_item_'.$item_id.'" size="2" maxlength="2" value="'.$num_item.'" onchange="return validerCheckBox(\'titem_id_'.$item_id.'\')"  />

		$sform= '<br />
<!-- SUPPRESSION ITEM -->
<div align="right">
<span class="surligne"><a href="'.$CFG->wwwroot.'/blocks/referentiel/edit.php?blockid='.$this->blockid.'&amp;courseid='.$this->courseid.'&amp;occurrenceid='.$this->referentiel->id.'&amp;deleteid='.$item_id.'&amp;action=modifieritem&amp;delete='.get_string('delete').'&amp;pass='.$this->pass.'&amp;sesskey='.sesskey().'">'.get_string('delete_item','referentiel').'</a></span>
<span class="small"><i>'.get_string('deleteitemhelp','referentiel').'</i></span>
</div>
</div>'."\n";
        $mform->addElement('html', $sform);
	}

	// --------------
	function add_competency($domain_id){
		$sform='';
		$mform = & $this->_form;

		$nb_item_competences = '0';
		$code_competence = '';
		$description_competence = '';
		$type_competence=0;
		$seuil_competence=0.0;
		$minima_competence=0;
		$num_competence = $this->compteur_competency;
		$ref_domaine=$domain_id;

        $sform= '<hr><h4>'.get_string('saisie_competence_supplementaire','referentiel').'</h4>
<div class="newcompetence">';
        $mform->addElement('html', $sform);
       	if (!empty($this->list_newcompetencies)) {
			$this->list_newcompetencies.= ','.$this->compteur_newcompetency;
		}
		else{
            $this->list_newcompetencies.= $this->compteur_newcompetency;
		}

		// Domaine
       	$mform->addElement('hidden', 'new_ref_domaine_'.$this->compteur_newcompetency, $ref_domaine);
        $mform->setType('new_ref_domaine_'.$this->compteur_newcompetency, PARAM_INT);

		// code
		$mform->addElement('text', 'new_code_competence_'.$this->compteur_newcompetency, get_string('code_unique','referentiel'), 'size="20"  maxlength="20"');
   	    $mform->setType('new_code_competence_'.$this->compteur_newcompetency, PARAM_TEXT);
		$mform->addRule('new_code_competence_'.$this->compteur_newcompetency, null, 'required', null, 'client');
        $mform->setDefault('new_code_competence_'.$this->compteur_newcompetency, $code_competence);

		// add display textarea field
        $mform->addElement('textarea', 'new_description_competence_'.$this->compteur_newcompetency, get_string('description', 'referentiel'), 'wrap="virtual" rows="3" cols="60"');
		$mform->setType('new_description_competence_'.$this->compteur_newcompetency, PARAM_TEXT);
		$mform->addRule('new_description_competence_'.$this->compteur_newcompetency, null, 'required', null, 'client');
        $mform->setDefault('new_description_competence_'.$this->compteur_newcompetency, $description_competence);

		$radioarray=array();
		$radioarray[] =& $mform->createElement('radio', 'new_type_competence_'.$this->compteur_newcompetency, '', get_string('yes').' ', 1, null);
		$radioarray[] =& $mform->createElement('radio', 'new_type_competence_'.$this->compteur_newcompetency, '', get_string('no').' ', 0, null);
		$mform->addGroup($radioarray, 'new_type_competence_'.$this->compteur_newcompetency, get_string('type_competence','referentiel'), array(' '), false);
		if (!empty($type_competence)){
            $mform->setDefault('yesno', 0);
		}
		else{
            $mform->setDefault('yesno', 1);
		}

        // <b>'.get_string('minima_competence','referentiel').'
 		$mform->addElement('text', 'new_minima_competence_'.$this->compteur_newcompetency, get_string('minima_competence','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('new_minima_competence_'.$this->compteur_newcompetency, PARAM_INT);
		$mform->addRule('new_minima_competence_'.$this->compteur_newcompetency, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_minima_competence_'.$this->compteur_newcompetency, $minima_competence);

		// <b>'.get_string('seuil_competence','referentiel')
 		$mform->addElement('text', 'new_seuil_competence_'.$this->compteur_newcompetency, get_string('seuil_competence','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('new_seuil_competence_'.$this->compteur_newcompetency, PARAM_TEXT);
		$mform->addRule('new_seuil_competence_'.$this->compteur_newcompetency, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_seuil_competence_'.$this->compteur_newcompetency, $seuil_competence);

 		$mform->addElement('text', 'new_num_competence_'.$this->compteur_newcompetency, get_string('numero','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('new_num_competence_'.$this->compteur_newcompetency, PARAM_INT);
		$mform->addRule('new_num_competence_'.$this->compteur_newcompetency, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_num_competence_'.$this->compteur_newcompetency, $num_competence);

        $mform->addElement('text', 'new_nb_item_competences_'.$this->compteur_newcompetency, get_string('nombre_item_competences_supplementaires','referentiel'), 'size="2" maxlength="2"');
   	    $mform->setType('new_nb_item_competences_'.$this->compteur_newcompetency, PARAM_INT);
		$mform->addRule('new_nb_item_competences_'.$this->compteur_newcompetency, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_nb_item_competences_'.$this->compteur_newcompetency, $nb_item_competences);

        $mform->addElement('html',  '</div>');
	}

	// --------------
	function edit_competency($rec){
    	global $CFG;
		$sform='';
		$mform = & $this->_form;

		$competence_id=$rec->id;
		$nb_item_competences = $rec->nb_item_competences;
		$old_code_competence= $rec->code_competence;
		$code_competence = $rec->code_competence;
		$description_competence = stripslashes($rec->description_competence);
		if (isset($rec->type_competence)){
            $type_competence=$rec->type_competence;
		}
		else{
			$type_competence=0;
		}
		if (isset($rec->seuil_competence)){
			$seuil_competence=$rec->seuil_competence;
        }
		else{
			$seuil_competence=0.0;
		}
		if (isset($rec->minima_competence)){
			$minima_competence=$rec->minima_competence;
		}
		else{
			$minima_competence=0.0;
		}

		$num_competence = $rec->num_competence;
		$ref_domaine = $rec->ref_domaine;

        $sform= '<hr><h4>'.get_string('competence','referentiel').'</h4>
<div class="competence">
<b>'.get_string('id','referentiel').' : </b>'.$competence_id;
        $mform->addElement('html', $sform);
/*
		$mform->addElement('advcheckbox','titem_id[]', get_string('select_item', 'referentiel'),get_string('cocher_enregistrer_domain', 'referentiel'), array('group' => 1), array(0, $item_id));
*/
        $mform->addElement('checkbox', 'tcomp_id_'.$competence_id, get_string('modified_skill', 'block_referentiel'), '<i>&lt;---- '.get_string('check_competency', 'block_referentiel').'</i>');
   	    $mform->setType('tcomp_id_'.$competence_id, PARAM_INT);
		$mform->addRule('tcomp_id_'.$competence_id, null, 'numeric', null, 'client');
        $mform->setDefault('tcomp_id_'.$competence_id, 0);

       	if (!empty($this->list_competencies)) {
			$this->list_competencies.= ','.$competence_id;
		}
		else{
            $this->list_competencies.= $competence_id;
		}

		// Domaine
       	$mform->addElement('hidden', 'ref_domaine_'.$competence_id, $ref_domaine);
        $mform->setType('ref_domaine_'.$competence_id, PARAM_INT);

		// code
		$mform->addElement('text', 'code_competence_'.$competence_id, get_string('code_unique','referentiel'), 'size="20"  maxlength="20"');
   	    $mform->setType('code_competence_'.$competence_id, PARAM_TEXT);
		$mform->addRule('code_competence_'.$competence_id, null, 'required', null, 'client');
        $mform->setDefault('code_competence_'.$competence_id, $code_competence);

		// add display textarea field
        $mform->addElement('textarea', 'description_competence_'.$competence_id, get_string('description', 'referentiel'), 'wrap="virtual" rows="3" cols="60"');
		$mform->setType('description_competence_'.$competence_id, PARAM_TEXT);
		$mform->addRule('description_competence_'.$competence_id, null, 'required', null, 'client');
        $mform->setDefault('description_competence_'.$competence_id, $description_competence);


/*
<input type="checkbox" name="tcompetence_id[]" id="tcompetence_id_'.$competence_id.'" value="'.$competence_id.'" />
<b>'.get_string('select_skill','referentiel').'</b>
</br />'."\n";
*/

		$radioarray=array();
		$radioarray[] =& $mform->createElement('radio', 'type_competence_'.$competence_id, '', get_string('yes').' ', 1, null);
		$radioarray[] =& $mform->createElement('radio', 'type_competence_'.$competence_id, '', get_string('no').' ', 0, null);
		$mform->addGroup($radioarray, 'type_competence_'.$competence_id, get_string('type_competence','referentiel'), array(' '), false);
		if (!empty($type_competence)){
            $mform->setDefault('yesno', 0);
			// <input type="radio" name="type_competence_'.$competence_id.'" id="type_competence_'.$competence_id.'" value="1" checked="checked" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
			// <input type="radio" name="type_competence_'.$competence_id.'" id="type_competence_'.$competence_id.'" value="0" onchange="return validerCheckBox(\'tcompetence_id_'.$competence_id.'\')" />'."\n";
		}
		else{
            $mform->setDefault('yesno', 1);
		}

        // <b>'.get_string('minima_competence','referentiel').'
 		$mform->addElement('text', 'minima_competence_'.$competence_id, get_string('minima_competence','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('minima_competence_'.$competence_id, PARAM_INT);
		$mform->addRule('minima_competence_'.$competence_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('minima_competence_'.$competence_id, $minima_competence);

		// <b>'.get_string('seuil_competence','referentiel')
 		$mform->addElement('text', 'seuil_competence_'.$competence_id, get_string('seuil_competence','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('seuil_competence_'.$competence_id, PARAM_TEXT);
		$mform->addRule('seuil_competence_'.$competence_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('seuil_competence_'.$competence_id, $seuil_competence);

 		$mform->addElement('text', 'num_competence_'.$competence_id, get_string('numero','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('num_competence_'.$competence_id, PARAM_INT);
		$mform->addRule('num_competence_'.$competence_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('num_competence_'.$competence_id, $num_competence);

        $mform->addElement('text', 'nb_item_competences_'.$competence_id, get_string('nombre_item_competences_supplementaires','referentiel'), 'size="2" maxlength="2"');
   	    $mform->setType('nb_item_competences_'.$competence_id, PARAM_INT);
		$mform->addRule('nb_item_competences_'.$competence_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('nb_item_competences_'.$competence_id, $nb_item_competences);

		$sform= '<br />
<!-- SUPPRESSION competence -->
<div align="right">
<span class="surligne"><a href="'.$CFG->wwwroot.'/blocks/referentiel/edit.php?blockid='.$this->blockid.'&amp;courseid='.$this->courseid.'&amp;occurrenceid='.$this->referentiel->id.'&amp;deleteid='.$competence_id.'&amp;action=modifiercompetence&amp;delete='.get_string('delete').'&amp;pass='.$this->pass.'&amp;sesskey='.sesskey().'">'.get_string('delete_skill','referentiel').'</a></span>
<span class="small"><i>'.get_string('deleteskillhelp','referentiel').'</i></span>
</div></div>'."\n";
        $mform->addElement('html', $sform);
	}


	// --------------
	function add_domain(){
		$sform='';
		$mform = & $this->_form;
		$code_domaine = '';
		$description_domaine = '';
        $type_domaine=0;
        $seuil_domaine=0.0;
        $minima_domaine=0;
		$num_domaine = $this->compteur_domain;
        $nb_competences=1;

    	$sform.= '
<!-- DOMAIN -->
<hr><h3>'.get_string('saisie_domaine_supplementaire','referentiel').'</h3>
<div class="newdomaine">';
        $mform->addElement('html', $sform);
/*
		$mform->addElement('advcheckbox','titem_id[]', get_string('select_item', 'referentiel'),get_string('cocher_enregistrer_domain', 'referentiel'), array('group' => 1), array(0, $item_id));
*/
       	if (!empty($this->list_newdomains)){
		   $this->list_newdomains.= ','.$this->compteur_newdomain;
		}
		else{
			$this->list_newdomains.=$this->compteur_newdomain;
		}

       	$mform->addElement('hidden', 'new_ref_referentiel_'.$this->compteur_newdomain, $this->referentiel->id);
        $mform->setType('new_ref_referentiel_'.$this->compteur_newdomain, PARAM_INT);

		// code
		$mform->addElement('text', 'new_code_domaine_'.$this->compteur_newdomain, get_string('code_unique','referentiel'), 'size="20"  maxlength="20"');
   	    $mform->setType('new_code_domaine_'.$this->compteur_newdomain, PARAM_TEXT);
		$mform->addRule('new_code_domaine_'.$this->compteur_newdomain, null, 'required', null, 'client');
        $mform->setDefault('new_code_domaine_'.$this->compteur_newdomain, $code_domaine);

		// add display textarea field
        $mform->addElement('textarea', 'new_description_domaine_'.$this->compteur_newdomain, get_string('description', 'referentiel'), 'wrap="virtual" rows="3" cols="60"');
		$mform->setType('new_description_domaine_'.$this->compteur_newdomain, PARAM_TEXT);
		$mform->addRule('new_description_domaine_'.$this->compteur_newdomain, null, 'required', null, 'client');
        $mform->setDefault('new_description_domaine_'.$this->compteur_newdomain, $description_domaine);

		$radioarray=array();
		$radioarray[] =& $mform->createElement('radio', 'new_type_domaine_'.$this->compteur_newdomain, '', get_string('yes').' ', 1, null);
		$radioarray[] =& $mform->createElement('radio', 'new_type_domaine_'.$this->compteur_newdomain, '', get_string('no').' ', 0, null);
		$mform->addGroup($radioarray, 'new_type_domaine_'.$this->compteur_newdomain, get_string('type_domaine','referentiel'), array(' '), false);
		if (!empty($type_domaine)){
            $mform->setDefault('yesno', 0);
		}
		else{
            $mform->setDefault('yesno', 1);
		}

 		$mform->addElement('text', 'new_minima_domaine_'.$this->compteur_newdomain, get_string('minima_domaine','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('new_minima_domaine_'.$this->compteur_newdomain, PARAM_INT);
		$mform->addRule('new_minima_domaine_'.$this->compteur_newdomain, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_minima_domaine_'.$this->compteur_newdomain, $minima_domaine);

		// <b>'.get_string('seuil_domaine','referentiel')
 		$mform->addElement('text', 'new_seuil_domaine_'.$this->compteur_newdomain, get_string('seuil_domaine','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('new_seuil_domaine_'.$this->compteur_newdomain, PARAM_TEXT);
		$mform->addRule('new_seuil_domaine_'.$this->compteur_newdomain, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_seuil_domaine_'.$this->compteur_newdomain, $seuil_domaine);

 		$mform->addElement('text', 'new_num_domaine_'.$this->compteur_newdomain, get_string('numero','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('new_num_domaine_'.$this->compteur_newdomain, PARAM_INT);
		$mform->addRule('new_num_domaine_'.$this->compteur_newdomain, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_num_domaine_'.$this->compteur_newdomain, $num_domaine);

        $mform->addElement('text', 'new_nb_competences_'.$this->compteur_newdomain, get_string('nombre_competences_supplementaires','referentiel'), 'size="2" maxlength="2"');
   	    $mform->setType('new_nb_competences_'.$this->compteur_newdomain, PARAM_INT);
		$mform->addRule('new_nb_competences_'.$this->compteur_newdomain, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('new_nb_competences_'.$this->compteur_newdomain, $nb_competences);
    	$sform= '<span class="small">'.get_string('ajouter_competences','referentiel').'</span>';
        $mform->addElement('html', $sform);

		$sform= '</div>'."\n";
        $mform->addElement('html', $sform);
	}


	// --------------
	function edit_domain($rec){
    	global $CFG;
		$sform='';
		$mform = & $this->_form;

		$domaine_id=$rec->id;
		$nb_competences = $rec->nb_competences;
		$old_code_domaine = $rec->code_domaine;
		$code_domaine = $rec->code_domaine;
		$description_domaine = stripslashes($rec->description_domaine);
		$num_domaine = $rec->num_domaine;
        $ref_referentiel = $rec->ref_referentiel;

		if (isset($rec->type_domaine)){
			$type_domaine=$rec->type_domaine;
		}
		else{
			$type_domaine=0;
		}
		if (isset($rec->seuil_domaine)){
			$seuil_domaine=$rec->seuil_domaine;
		}
		else{
			$seuil_domaine=0.0;
		}
		if (isset($rec->minima_domaine)){
			$minima_domaine=$rec->minima_domaine;
		}
		else{
			$minima_domaine=0;
		}

    	$sform.= '
<!-- DOMAIN -->
<hr><h3>'.get_string('domaine','referentiel').'</h3>
<div class="domaine">
<b>'.get_string('id','referentiel').' : </b>'.$domaine_id;
        $mform->addElement('html', $sform);

/*
		$mform->addElement('advcheckbox','titem_id[]', get_string('select_item', 'referentiel'),get_string('cocher_enregistrer_domain', 'referentiel'), array('group' => 1), array(0, $item_id));
*/
        $mform->addElement('checkbox', 'tdomain_id_'.$domaine_id, get_string('modified_domain', 'block_referentiel'), '<i>&lt;---- '.get_string('check_domain', 'block_referentiel').'</i>');
   	    $mform->setType('tdomain_id_'.$domaine_id, PARAM_INT);
		$mform->addRule('tdomain_id_'.$domaine_id, null, 'numeric', null, 'client');
        $mform->setDefault('tdomain_id_'.$domaine_id, 0);

       	if (!empty($this->list_domains)){
		   $this->list_domains.= ','.$domaine_id;
		}
		else{
			$this->list_domains.=$domaine_id;
		}


       	$mform->addElement('hidden', 'ref_referentiel_'.$domaine_id, $ref_referentiel);
        $mform->setType('ref_referentiel_'.$domaine_id, PARAM_INT);

		// code
		$mform->addElement('text', 'code_domaine_'.$domaine_id, get_string('code_unique','referentiel'), 'size="20"  maxlength="20"');
   	    $mform->setType('code_domaine_'.$domaine_id, PARAM_TEXT);
		$mform->addRule('code_domaine_'.$domaine_id, null, 'required', null, 'client');
        $mform->setDefault('code_domaine_'.$domaine_id, $code_domaine);

		// add display textarea field
        $mform->addElement('textarea', 'description_domaine_'.$domaine_id, get_string('description', 'referentiel'), 'wrap="virtual" rows="3" cols="60"');
		$mform->setType('description_domaine_'.$domaine_id, PARAM_TEXT);
		$mform->addRule('description_domaine_'.$domaine_id, null, 'required', null, 'client');
        $mform->setDefault('description_domaine_'.$domaine_id, $description_domaine);


/*
<input type="checkbox" name="tdomaine_id[]" id="tdomaine_id_'.$domaine_id.'" value="'.$domaine_id.'" />
<b>'.get_string('select_skill','referentiel').'</b>
</br />'."\n";
*/

		$radioarray=array();
		$radioarray[] =& $mform->createElement('radio', 'type_domaine_'.$domaine_id, '', get_string('yes').' ', 1, null);
		$radioarray[] =& $mform->createElement('radio', 'type_domaine_'.$domaine_id, '', get_string('no').' ', 0, null);
		$mform->addGroup($radioarray, 'type_domaine_'.$domaine_id, get_string('type_domaine','referentiel'), array(' '), false);
		if (!empty($type_domaine)){
            $mform->setDefault('yesno', 0);
			// <input type="radio" name="type_domaine_'.$domaine_id.'" id="type_domaine_'.$domaine_id.'" value="1" checked="checked" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />'."\n";
			// <input type="radio" name="type_domaine_'.$domaine_id.'" id="type_domaine_'.$domaine_id.'" value="0" onchange="return validerCheckBox(\'tdomaine_id_'.$domaine_id.'\')" />'."\n";
		}
		else{
            $mform->setDefault('yesno', 1);
		}

        // <b>'.get_string('minima_domaine','referentiel').'
 		$mform->addElement('text', 'minima_domaine_'.$domaine_id, get_string('minima_domaine','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('minima_domaine_'.$domaine_id, PARAM_INT);
		$mform->addRule('minima_domaine_'.$domaine_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('minima_domaine_'.$domaine_id, $minima_domaine);

		// <b>'.get_string('seuil_domaine','referentiel')
 		$mform->addElement('text', 'seuil_domaine_'.$domaine_id, get_string('seuil_domaine','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('seuil_domaine_'.$domaine_id, PARAM_TEXT);
		$mform->addRule('seuil_domaine_'.$domaine_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('seuil_domaine_'.$domaine_id, $seuil_domaine);

 		$mform->addElement('text', 'num_domaine_'.$domaine_id, get_string('numero','referentiel'), 'size="5"  maxlength="20"');
   	    $mform->setType('num_domaine_'.$domaine_id, PARAM_INT);
		$mform->addRule('num_domaine_'.$domaine_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('num_domaine_'.$domaine_id, $num_domaine);

        $mform->addElement('text', 'nb_competences_'.$domaine_id, get_string('nombre_competences_supplementaires','referentiel'), 'size="2" maxlength="2"');
   	    $mform->setType('nb_competences_'.$domaine_id, PARAM_INT);
		$mform->addRule('nb_competences_'.$domaine_id, get_string('integer','block_referentiel'), 'numeric', null, 'client');
        $mform->setDefault('nb_competences_'.$domaine_id, $nb_competences);
    	//$sform= '<span class="small">'.get_string('ajouter_competences','referentiel').'</span>';
        //$mform->addElement('html', $sform);

		$sform= '<br />
<!-- SUPPRESSION domaine -->
<div align="right">
<span class="surligne"><a href="'.$CFG->wwwroot.'/blocks/referentiel/edit.php?blockid='.$this->blockid.'&amp;courseid='.$this->courseid.'&amp;occurrenceid='.$this->referentiel->id.'&amp;deleteid='.$domaine_id.'&amp;action=modifierdomaine&amp;delete='.get_string('delete').'&amp;pass='.$this->pass.'&amp;sesskey='.sesskey().'">'.get_string('delete_domain','referentiel').'</a></span>
<span class="small"><i>'.get_string('deletedomainhelp','referentiel').'</i></span>
</div></div>'."\n";
        $mform->addElement('html', $sform);
	}


	// -----------------
	function edit_domains_competencies_items() {
		global $OUTPUT, $CFG;
    	$sform='';

		$mform = & $this->_form;

    	// COMPTEURS
    	$this->compteur_domain=0;
	    $this->compteur_competency=0;
    	$this->compteur_item=0;
		$this->compteur_newdomain=0;
		$this->compteur_newcompetency=0;
		$this->compteur_newitem=0;

		// List of Ids
    	$this->list_domains='';
	    $this->list_competencies='';
    	$this->list_items='';
        $this->list_newdomains='';
        $this->list_newcompetencies='';
        $this->list_newitems='';

		// charger les domaines associes au referentiel courant
		if (!empty($this->referentiel->id)){
			// Numbre of domains to create
			$objet_nb_domaines=referentiel_get_nb_domaines($this->referentiel->id);
			$nb_domaines=$objet_nb_domaines->nb_domaines;
			// DEBUG
			// $sform.= '"<br/>DEBUG :: NOMBRE DE DOMAINES A AJOUTER : $nb_domaines <br />\n";

			$rec_domains = referentiel_get_domaines($this->referentiel->id);

			if ($rec_domains){
		    	// add group for text areas
				$mform->addElement('header','domaininfo', get_string('editdomains', 'block_referentiel'));
                //$mform->setAdvanced('domaininfo');

   				$mform->addHelpButton('domaininfo', 'modifdomskillitemh','referentiel');
				$sform= '<img class="selectallarrow" src="'.$OUTPUT->pix_url('arrow_ltr_bas','referentiel').'" width="38" height="22" alt="Pour la sélection :" />
<i>'.get_string('cocher_enregistrer_domain', 'referentiel').'</i>
<br />'."\n";
		        $mform->addElement('html', $sform);

				foreach ($rec_domains as $record){
                    if ($record){
                        $this->compteur_domain++;
						$this->edit_domain($record);

						// LISTE DES COMPETENCES DE CE DOMAINE
                        $this->compteur_competency=0;
						$records_competences = referentiel_get_competences($record->id);
 						if ($records_competences){

							foreach ($records_competences as $record_c){

								if ($record_c){
                                    $this->compteur_competency++;
									$this->edit_competency($record_c);
								}
								// LISTE DES ITEMS DE CETTE COMPETENCES
                                $this->compteur_item=0;
								$records_items = referentiel_get_item_competences($record_c->id);
								if ($records_items){
									foreach ($records_items as $record_i){
										if ($record_i){
                                        	$this->compteur_item++;
											$this->edit_item($record_i);
										}
									}
								}

								// NOMBRE DE NOUVEAUX ITEMS DE COMPETENCE DEMANDES
								$nb_items_a_afficher=$record_c->nb_item_competences-$this->compteur_item; // Tenir compte des items enregistres
								if (isset($nb_items_a_afficher) &&  ($nb_items_a_afficher>0)){
									for ($k=0; $k<$nb_items_a_afficher; $k++){
                                        $this->compteur_item++;
                                        $this->compteur_newitem++;
										$this->add_item($record_c->id);
									}
								}
							}
						}

						// NOUVELLE COMPETENCE
						// NOMBRE DE NOUVELLES COMPETENCES DEMANDEES
						$nb_competences_a_afficher=$record->nb_competences-$this->compteur_competency;
						// Tenir compte des competences enregistres
						if (isset($nb_competences_a_afficher) &&  ($nb_competences_a_afficher>0)){
							for ($i=0; $i<$nb_competences_a_afficher; $i++){
                                $this->compteur_competency++;
                                $this->compteur_newcompetency++;
								$this->add_competency($record->id, $this->compteur_competency);
          					}
						}
					}
				}

				$sform= '<img class="selectallarrow" src="'.$OUTPUT->pix_url('arrow_ltr','referentiel').'" width="38" height="22" alt="Pour la sélection :" />
<i>'.get_string('cocher_enregistrer_domain', 'referentiel').'</i>
<br />'."\n";
			    $mform->addElement('html', $sform);
			}

			// NOMBRE DE NOUVEAUX DOMAINES DEMANDES
			$nb_domaines_a_afficher=$nb_domaines-$this->compteur_domain; // Tenir compte des domaines enregistres
			if (isset($nb_domaines_a_afficher) &&  ($nb_domaines_a_afficher>0)){
		    	// add group for text areas
				$mform->addElement('header','newdomaininfo', get_string('newdomains', 'block_referentiel'));
   				$mform->addHelpButton('displayinfo', 'modifdomskillitemh','referentiel');

				for ($j=0; $j<$nb_domaines_a_afficher; $j++){
                    $this->compteur_domain++;
                    $this->compteur_newdomain++;
					$this->add_domain();
				}
			}
            $mform->addElement('hidden', 'list_domains', $this->list_domains);
            $mform->setType('list_domains', PARAM_TEXT);
            $mform->addElement('hidden', 'list_competencies', $this->list_competencies);
            $mform->setType('list_competencies', PARAM_TEXT);
            $mform->addElement('hidden', 'list_items', $this->list_items);
            $mform->setType('list_items', PARAM_TEXT);
            $mform->addElement('hidden', 'list_newdomains', $this->list_newdomains);
            $mform->setType('list_newdomains', PARAM_TEXT);
            $mform->addElement('hidden', 'list_newcompetencies', $this->list_newcompetencies);
            $mform->setType('list_newcompetencies', PARAM_TEXT);
            $mform->addElement('hidden', 'list_newitems', $this->list_newitems);
            $mform->setType('list_newitems', PARAM_TEXT);
            // buttons
        	//$this->add_action_buttons(true);
			$buttonarray=array();
			$buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
			$buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('revert'));
			$buttonarray[] = &$mform->createElement('cancel');
			$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
			$mform->closeHeaderBefore('buttonar');

		}
	}

}   // end class
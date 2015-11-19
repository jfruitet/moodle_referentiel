<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once($CFG->libdir.'/formslib.php');//putting this is as a safety as i got a class not found error.
/**
 * @package   mod-referentiel
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_referentiel_input_activity_form extends moodleform {

    var $userid;
    var $context;
    var $courseid;
    var $instanceid;
    var $occurrenceid;
    var $activite;

    function definition() {
        $mform = & $this->_form;
        $arguments = $this->_customdata;

        // arguments
        if (isset($arguments['options'])){
            $options=$arguments['options'];
        }
        else{
            $option=NULL;
        }
        
        if (isset($arguments['context'])){
            $this->context=$arguments['context'];
        }
        else{
            $this->context=NULL;
        }
        if (isset($arguments['userid'])){
            $this->userid=$arguments['userid'];
        }
        else{
            $this->userid=0;
        }

        if (isset($arguments['instanceid'])){
            $this->instanceid=$arguments['instanceid'];
        }
        else{
            $this->instanceid=0;
        }
        if (isset($arguments['occurrenceid'])){
            $this->occurrenceid=$arguments['occurrenceid'];
        }
        else{
            $this->occurrenceid=0;
        }
        
        if (isset($arguments['activite'])){
            $this->activiteid=$arguments['activite'];
        }
        else{
            $this->activite=NULL;
        }

        if (!empty($this->activite)){
            if (isset($this->activite->id)){
                $activite_id=$this->activite->id;
            }
            else{
                $activite_id=0;
            }
            if (isset($this->activite->type_activite)){
                $type_activite=$this->activite->type_activite;
            }
            else{
                $type_activite='';
            }
            if (isset($this->activite->description_activite)){
                $description_activite=$this->activite->description_activite;
            }
            else{
                $description_activite='';
            }
            if (isset($this->activite->competences_activite)){
                $competences_activite=$this->activite->competences_activite;
            }
            else{
                $competences_activite=''; // referentiel_get_liste_codes_competence($this->occurrenceid);
            }
            $old_liste_competences=$competences_activite;
            
            if (isset($this->activite->commentaire_activite)){
                $commentaire_activite=$this->activite->commentaire_activite;
            }
            else{
                $commentaire_activite='';
            }
            if (isset($this->activite->userid)){
                $userid=$this->activite->userid;
            }
            else{
                $userid=$this->userid;
            }
            if (isset($this->activite->teacherid)){
                $teacherid=$this->activite->teacherid;
            }
            else{
                $teacherid=0;
            }
            if (isset($this->activite->approved)){
                $approved=$this->activite->approved;
            }
            else{
                $approved=0;
            }
            if (isset($this->activite->ref_task)){
                $ref_task=$this->activite->ref_task;
            }
            else{
                $ref_task=0;
            }

        }
        else{
            $activite_id=0;
            $type_activite='';
            $description_activite='';
            $competences_activite=''; // referentiel_get_liste_codes_competence($this->occurrenceid);
            $old_liste_competences='';
            $commentaire_activite='';
            $userid=$this->userid;
            $teacherid=0;
            $approved=0;
            $ref_task=0;
        }

        if (isset($option->mode)){
            $mode=$option->mode;
        }
        else{
            $mode='';
        }
        if (isset($option->modulename)){
            $modulename=$option->modulename;
        }
        else{
            $modulename='';
        }

        if (isset($option->select_acc)){
            $select_acc=$option->select_acc;
        }
        else{
            $select_acc=0;
        }

        if (isset($option->data_filtre)){
            $data_filtre=$option->data_filtre;
        }
        else{
            $data_filtre=NULL;
        }

        if (isset($arguments['action'])){
            $action=$arguments['action'];
        }
        else{
            $action='addactivity';
        }
        
        // visible elements
        $mform->addElement('header', 'general', $arguments['msg']);
        $mform->addHelpButton('general', 'creer_activiteh','referentiel');


        // preparer les variables globales pour Overlib
        referentiel_initialise_data_referentiel($this->occurrenceid);

        if (has_capability('mod/referentiel:managecertif', $this->context)) { // enseignant
            $mform->addElement('html', '<p><i>'.get_string('creer_activite_teacher','referentiel').'</i></p>'."\n");
        }

        $jauge_activite_declarees=referentiel_print_jauge_activite($userid, $this->occurrenceid);
        if ($jauge_activite_declarees){
            $jauge_activite_declarees=get_string('competences_declarees','referentiel', referentiel_get_user_info($userid)). ' ' .$jauge_activite_declarees."\n";
            $mform->addElement('html', '<div class="qheader">'.$jauge_activite_declarees.'</div>');
        }


        $mform->addElement('text','type_activite',get_string('type_activite','referentiel'), array('size'=>'40'));
        $mform->setType('type_activite', PARAM_ALPHA);
        $mform->addRule('type_activite', get_string('missingtype', 'referentiel'), 'required', null, 'server');
        $mform->setDefault('type_activite', $type_activite);

        $mform->addElement('editor','description_activite',get_string('description','referentiel'));
        $mform->setType('description_activite', PARAM_TEXT);
        $mform->addRule('description_activite', get_string('missingdescription', 'referentiel'), 'required', null, 'server');
        $mform->setDefault('description_activite', $description_activite);

        //if (isteacher($this->userid)){
        if (has_capability('mod/referentiel:approve', $this->context)){
            $mform->addElement('textarea','commentaire_activite', get_string('commentaire','referentiel'), 'wrap="virtual" cols="80" rows="5"');
            $mform->setType('commentaire_activite', PARAM_TEXT);
            $mform->setDefault('commentaire_activite', $commentaire_activite);
        }
        else{
            //echo get_string('commentaire','referentiel').': '.$commentaire_activite."\n";
            $mform->addElement('hidden', 'commentaire_activite', $commentaire_activite);
            $mform->setType('commentaire_activite', PARAM_TEXT);
        }
        
        // $saisie_competences=referentiel_modifier_selection_codes_item_hierarchique($this->occurrenceid, $competences_activite, $ref_task, $activite_id, '', 1);
        // $saisie_competences=referentiel_modifier_selection_codes_item_hierarchique($this->occurrenceid, '', $ref_task, $activite_id, '', 1);
        // Cette approche ne fonctionne pas
        // $mform->addElement('html', "\n".'<br /><div class="qheader">'.$saisie_competences.'</div><br />'."\n");
        $this->modifier_selection_codes_item_hierarchique($mform, $this->occurrenceid, '', $ref_task, $activite_id, '', 1);


/*
echo '<br />'."\n";
referentiel_selection_liste_codes_item_hierarchique($this->occurrenceid);
echo '<br />'."\n";
*/
$radioarray=array();
$radioarray[] = $mform->createElement('radio', 'depot_document', '', get_string('yes'), 1, NULL);
$radioarray[] = $mform->createElement('radio', 'depot_document', '', get_string('no'), 0, NULL);
$mform->addGroup($radioarray, 'depot_document', get_string('depot_document','referentiel'), array(' '), false);
$mform->setDefault('depot_document', 0);

        //echo get_string('notification_activite','referentiel');
$radioarray=array();
$radioarray[] = $mform->createElement('radio', 'mailnow', '', get_string('yes'), 1, NULL);
$radioarray[] = $mform->createElement('radio', 'mailnow', '', get_string('no'), 0, NULL);
$mform->addGroup($radioarray, 'mailnow', get_string('notification_activite','referentiel'), array(' '), false);
$mform->setDefault('mailnow', 0);

        // echo get_string('validation','referentiel').': '."\n";
        if (has_capability('mod/referentiel:approve', $this->context)){
            $radioarray=array();
            $radioarray[] = $mform->createElement('radio', 'approved', '', get_string('yes'), 1, NULL);
            $radioarray[] = $mform->createElement('radio', 'approved', '', get_string('no'), 0, NULL);
            $mform->addGroup($radioarray, 'approved', get_string('approved','referentiel'), array(' '), false);
            $mform->setDefault('approved', $approved);
        }
        else{
            /*
            if ($approved){
                echo get_string('approved','referentiel');
            }
            else{
                echo get_string('not_approved','referentiel');
            }
            */
            $mform->addElement('hidden', 'approved', $approved);
            $mform->setType('approved', PARAM_INT);
        }
/*
        //$mform->addElement('filemanager', 'newfile', get_string('uploadafile'));
        //$mform->addElement('filemanager', 'referentiel_file', get_string('uploadafile'), null, $arguments['options']);

        // pour une importation puis suppression
        $mform->addElement('filepicker', 'referentiel_file', get_string('uploadafile'), null, $arguments['options']);
*/



        // hidden params
        $mform->addElement('hidden', 'activite_id', $activite_id);
        $mform->setType('activite_id', PARAM_INT);
        $mform->addElement('hidden', 'ref_task', $ref_task);
        $mform->setType('ref_task', PARAM_INT);
        $mform->addElement('hidden', 'old_liste_competences', $old_liste_competences);
        $mform->setType('old_liste_competences', PARAM_TEXT);
        $mform->addElement('hidden', 'userid', $userid);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'teacherid', $teacherid);
        $mform->setType('teacherid', PARAM_INT);

        $mform->addElement('hidden', 'd', $this->instanceid);
        $mform->setType('d', PARAM_INT);

        $mform->addElement('hidden', 'contextid', $this->context->id);
        $mform->setType('contextid', PARAM_INT);

        $mform->addElement('hidden', 'ref_referentiel', $this->occurrenceid);
        $mform->setType('ref_referentiel', PARAM_INT);

        $mform->addElement('hidden', 'course', $this->courseid);
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'ref_instance', $this->instanceid);
        $mform->setType('ref_instance', PARAM_INT);
        $mform->addElement('hidden', 'instance', $this->instanceid);    // ??? résidu d'ancienne version ??
        $mform->setType('instance', PARAM_INT);


        $mform->addElement('hidden', 'modulename', $modulename);
        $mform->setType('modulename', PARAM_TEXT);

        $mform->addElement('hidden', 'mode', $mode);
        $mform->setType('mode', PARAM_TEXT);

        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->setType('sesskey', PARAM_TEXT);

        // Ajout pour les filtres
        $mform->addElement('hidden', 'select_acc', $select_acc);
        $mform->setType('select_acc', PARAM_INT);

        if (!empty($data_filtre)){
            $mform->addElement('hidden', 'filtre_auteur', $data_filtre->filtre_auteur);
            $mform->setType('filtre_auteur', PARAM_INT);
            $mform->addElement('hidden', 'filtre_validation', $data_filtre->filtre_validation);
            $mform->setType('filtre_validation', PARAM_INT);
            $mform->addElement('hidden', 'filtre_referent', $data_filtre->filtre_referent);
            $mform->setType('filtre_referent', PARAM_INT);
            $mform->addElement('hidden', 'filtre_date_modif', $data_filtre->filtre_date_modif);
            $mform->setType('filtre_date_modif', PARAM_INT);
            $mform->addElement('hidden', 'filtre_date_modif_student', $data_filtre->filtre_date_modif_student);
            $mform->setType('filtre_date_modif_student', PARAM_INT);
        }

        $mform->addElement('hidden', 'action',  $action);
        $mform->setType('action', PARAM_ALPHA);

        // buttons
        $this->add_action_buttons(true);
    }
    
    
    // ----------------------------------------------------
    function modifier_selection_codes_item_hierarchique($mform, $refrefid, $liste_saisie, $is_task=false, $id_activite=0, $comportement=''){
    // version locale

    // input : liste de code de la forme 'CODE''SEPARATEUR'
    // input : liste2 de code de la forme 'CODE''SEPARATEUR' codes declares
    // retourne le selecteur
	// DEBUG
	// echo "$liste_saisie<br />\n";
    global $OK_REFERENTIEL_DATA;
    global $t_domaine;
    global $t_domaine_coeff;
    global $t_domaine_description;

    // COMPETENCES
    global $t_competence;
    global $t_competence_coeff;
    global $t_competence_description;

    // ITEMS
    global $t_item_code;
    global $t_item_coeff; // coefficient poids determine par le modele de calcul (soit poids soit poids / empreinte)
    global $t_item_domaine; // index du domaine associe a un item
    global $t_item_competence; // index de la competence associee a un item
    global $t_item_poids; // poids
    global $t_item_empreinte;
    global $t_nb_item_domaine;
    global $t_nb_item_competence;


    global $t_item_description_competence;

        $separateur='/';
        $nl='';

        if ($id_activite==0){
/*
            $s1='<input type="checkbox" id="code_item_';
            $s2='" name="code_item[]" value="';
            $s3='"';
            $s4=' />';
            $s5='<label for="code_item_';
            $s6='">';
            $s7='</label> '."\n";
*/
            $id='code_item_';
            $name='code_item[]';
            $s5=' <label for="code_item_';
            $s6='">';
            $s7='</label> '."\n";
        }
        else{
/*
            $s1='<input type="checkbox" id="code_item_'.$id_activite.'_';
            $s2='" name="code_item_'.$id_activite.'[]" value="';
            $s3='"';
            if (!empty($comportement)){
                $s4=' '.$comportement.' />';
            }
            else{
                $s4=' />';
            }
            $s5='<label for="code_item_'.$id_activite.'_';
	   	    $s6='">';
		    $s7='</label> '."\n";
*/
            $id='code_item_'.$id_activite.'_';
            $name='code_item_'.$id_activite.'[]';
            if (!empty($comportement)){
                $s4=' '.$comportement.' />';
            }
            else{
                $s4=' />';
            }
            $s5=' <label for="code_item_'.$id_activite.'_';
	   	    $s6='">';
		    $s7='</label> '."\n";
        }


        $checked=' checked="checked"';
	/*
    $tl=explode($separateur, $liste_complete);
    */

        if ($refrefid){

            if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
                $OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($refrefid);
            }

            if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){

        // DEBUG
/*
echo "<br />DEBUG :: input_activity_form.php :: 227\n";
echo "<br /> T_ITEM_CODE<br />\n";
print_object($t_item_code);
*/
                $tl=$t_item_code;

                $liste_saisie=strtr($liste_saisie, $separateur, ' ');
                $liste_saisie=trim(strtr($liste_saisie, '.', '_'));
            	// echo "<br />DEBUG :: 201 :: $liste_saisie<br />\n";
            	$ne=count($tl);
            	$select='';

                $index_code_domaine=$t_item_domaine[0];
                $code_domaine=$t_domaine[$index_code_domaine];

                $index_code_competence=$t_item_competence[0];
                $code_competence=$t_competence[$index_code_competence];

                $s= '&nbsp; &nbsp; &nbsp; <b>'.$code_domaine.'</b> : '.$t_domaine_description[$index_code_domaine]."\n";      // ouvrir domaine
                $s.= '<br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i>'.$code_competence.'</i> : <span class="small">'.$t_competence_description[$index_code_competence].'</span><br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'."\n";     // ouvrir competence
                $mform->addElement('html', $s);

                $i=0;
                while ($i<$ne){
                    //echo $code_domaine.' '.$code_competence;
                    //echo $t_item_domaine[$i].' '.$t_item_competence[$i];

                    // domaine
                    if ($t_item_domaine[$i] != $index_code_domaine){
                        $index_code_domaine=$t_item_domaine[$i];
                        $code_domaine=$t_domaine[$index_code_domaine];
                        // competence
                        $mform->addElement('html', '<br /> &nbsp; &nbsp; &nbsp; <b>'.$code_domaine.'</b> : '.$t_domaine_description[$index_code_domaine]."\n");  // nouveau domaine
                        // nouvelle competence
                        $index_code_competence=$t_item_competence[$i];
                        $code_competence=$t_competence[$index_code_competence];
                        $mform->addElement('html', '<br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i>'.$code_competence.'</i> : <span class="small">'.$t_competence_description[$index_code_competence].'</span><br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'."\n");
                    }

                    // competence
                    if ($t_item_competence[$i] != $index_code_competence){
                        $index_code_competence=$t_item_competence[$i];
                        $code_competence=$t_competence[$index_code_competence];
                        $mform->addElement('html', '<br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i>'.$code_competence.'</i> : <span class="small">'.$t_competence_description[$index_code_competence].'</span><br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'."\n");
                    }
                    // item

            		$code=trim($tl[$i]);

		            $le_code=referentiel_affiche_overlib_un_item($separateur, $code);

            		if ($code!=""){
                        // $code_search='/'.strtr($code, '.', '_').'/';
			            // echo "RECHERCHE '$code_search' dans '$liste_saisie'<br />\n";
            			// echo "<br />DEBUG :: print_lib_activite :: 213 :: $code_search<br />\n";
			            // if (preg_match($code_search, $liste_saisie)){

                        $code_search=strtr($code, '.', '_');
			// if (eregi($code_search, $liste_saisie)){
			            if (stristr($liste_saisie, $code_search)){
/*

				$s.= $s1.$i.$s2.$code.$s3.$checked.$s4.$s5.$i.$s6.$le_code.$s7;
*/
                            $mform->addElement('checkbox', "code_item[]", $i, $s5.$i.$s6.$le_code.$s7 );
                            $mform->setDefault("code_item[]", 1);  // checked
            			}
			            else {
/*
            $id='code_item_'.$id_activite.'_';
            $name='code_item_'.$id_activite.'[]';
            if (!empty($comportement)){
                $s4=' '.$comportement.' />';
            }
            else{
                $s4=' />';
            }
            $s5='<label for="code_item_'.$id_activite.'_';
	   	    $s6='">';
		    $s7='</label> '."\n";

    */
	               			if (!$is_task){
				            	//$s.=$s1.$i.$s2.$code.$s3.$s4.$s5.$i.$s6.$le_code.$s7;
                                $mform->addElement('checkbox', "code_item[]", $i, $s5.$i.$s6.$le_code.$s7);
                                $mform->setDefault("code_item[]", 0);  // unchecked
			             	}
            				else{
			             		//$s.=' &nbsp; '. $s5.$i.$s6.$le_code.$s7;
                                $mform->addElement('checkbox', "code_item[]", $i, $s5.$i.$s6.$le_code.$s7);
                                $mform->setDefault("code_item[]", 0);  // unchecked
			             	}
            			}
            		}
		            $i++;
	           }

            }
        }
    }   // end of function
    
}   // end of class

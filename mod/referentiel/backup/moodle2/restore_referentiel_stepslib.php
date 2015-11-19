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

/**
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_referentiel_activity_task
 */


define('REF_REF_TEMPID', 9999999); // id temporaire pour sauvegarder des certificats lors de backup / restore


require_once($CFG->dirroot . '/mod/referentiel/locallib.php'); // for certificates management

/**
 * Structure step to restore one choice activity
 */
class restore_referentiel_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('referentiel', '/activity/referentiel');

        $paths[] = new restore_path_element('occurrence', '/activity/referentiel/occurrence');
// MODIF JF 2012/03/12
        $paths[] = new restore_path_element('protocole', '/activity/referentiel/occurrence/protocole');

        $paths[] = new restore_path_element('domaine', '/activity/referentiel/occurrence/domaines/domaine');
        $paths[] = new restore_path_element('competence', '/activity/referentiel/occurrence/domaines/domaine/competences/competence');
        $paths[] = new restore_path_element('item', '/activity/referentiel/occurrence/domaines/domaine/competences/competence/items/item');

        $paths[] = new restore_path_element('scale', '/activity/referentiel/scales/scale');
        $paths[] = new restore_path_element('scale_ref', '/activity/referentiel/scales/scale/scale_refs/scale_ref');

        if ($userinfo) {
            $paths[] = new restore_path_element('activite_scale', '/activity/referentiel/scales/scale/scale_activites/scale_activite');
        }

        if ($userinfo) {
            $paths[] = new restore_path_element('certificat', '/activity/referentiel/certificats/certificat');
        }


        if ($userinfo) {
            $paths[] = new restore_path_element('accompagnement', '/activity/referentiel/accompagnements/accompagnement');
            $paths[] = new restore_path_element('task', '/activity/referentiel/tasks/task');
            $paths[] = new restore_path_element('consigne', '/activity/referentiel/tasks/task/consignes/consigne');
            $paths[] = new restore_path_element('activite', '/activity/referentiel/activites/activite');
            $paths[] = new restore_path_element('document', '/activity/referentiel/activites/activite/documents/document');
            $paths[] = new restore_path_element('a_user_task', '/activity/referentiel/activites/activite/a_user_tasks/a_user_task');
        }


        if ($userinfo){
            $paths[] = new restore_path_element('etablissement', '/activity/referentiel/etablissements/etablissement');
            $paths[] = new restore_path_element('etudiant', '/activity/referentiel/etablissements/etablissement/etudiants/etudiant');
        }

        if ($userinfo){
            $paths[] = new restore_path_element('a_user_pedago', '/activity/referentiel/pedagogies/a_user_pedago');
            $paths[] = new restore_path_element('pedagogie_record', '/activity/referentiel/pedagogies/a_user_pedago/pedagogie_record');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_referentiel($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->date_instance= $this->apply_date_offset($data->date_instance);
        if (empty($data->intro)){
            $data->intro=$data->description_instance;
        }
        // insert the referentiel record
        $newitemid = $DB->insert_record('referentiel', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_accompagnement($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->courseid = $this->get_courseid();
        $data->ref_instance = $this->get_new_parentid('referentiel');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->teacherid = $this->get_mappingid('user', $data->teacherid);

        $newitemid = $DB->insert_record('referentiel_accompagnement', $data);
    }

    protected function process_task($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_course = $this->get_courseid();
        $data->ref_instance = $this->get_new_parentid('referentiel');
        $data->ref_referentiel = $this->get_mappingid('occurrence', $data->ref_referentiel);
        $data->auteurid = $this->get_mappingid('user', $data->auteurid);
        $data->date_creation= $this->apply_date_offset($data->date_creation);
        $data->date_modif= $this->apply_date_offset($data->date_modif);
        $data->date_debut= $this->apply_date_offset($data->date_debut);
        $data->date_fin= $this->apply_date_offset($data->date_fin);

        $newitemid = $DB->insert_record('referentiel_task', $data);
        // need to save this mapping as far as somthing depend on it
        // (child paths, file areas nor links decoder)
        $this->set_mapping('task', $oldid, $newitemid);
    }

    protected function process_consigne($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_task = $this->get_new_parentid('task');
        $newitemid = $DB->insert_record('referentiel_consigne', $data);
        // need to save this mapping as far as file areas depend on it
        $this->set_mapping('consigne', $oldid, $newitemid);
    }


    protected function process_activite($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_course = $this->get_courseid();
        $data->ref_instance = $this->get_new_parentid('referentiel');
        $data->ref_referentiel = $this->get_mappingid('occurrence', $data->ref_referentiel);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->teacherid = $this->get_mappingid('user', $data->teacherid);
        $data->date_creation= $this->apply_date_offset($data->date_creation);
        $data->date_modif_student= $this->apply_date_offset($data->date_modif_student);
        $data->date_modif= $this->apply_date_offset($data->date_modif);
        if ($data->ref_task) { // recuperer le nouvel id de la tache qui doit donc etre charge avant l'activite !
            $this->get_mappingid('task', $data->ref_task);
        }
        $newitemid = $DB->insert_record('referentiel_activite', $data);
        // need to save this mapping as far as somthing depend on it
        // (child paths, file areas nor links decoder)
        $this->set_mapping('activite', $oldid, $newitemid);
    }

    protected function process_document($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_activite = $this->get_new_parentid('activite');
        $newitemid = $DB->insert_record('referentiel_document', $data);
        // need to save this mapping as far as file areas depend on it
        $this->set_mapping('document', $oldid, $newitemid);
    }

    protected function process_a_user_task($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_activite = $this->get_new_parentid('activite');
        $data->ref_user = $this->get_mappingid('user', $data->ref_user);
        if (!empty($data->ref_task)){
            $data->ref_task = $this->get_mappingid('task', $data->ref_task);
        }
        else{
            $data->ref_task = 0;
        }
        $data->date_selection= $this->apply_date_offset($data->date_selection);

        $newitemid = $DB->insert_record('referentiel_a_user_task', $data);
    }


    protected function process_occurrence($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        //print_object($data);
        //exit;
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Eviter d'inserer si l'occurrence existe
        $recs_refref = NULL;
        if (!empty($data->cle_referentiel)){
            $params=array("cle_referentiel" => $data->cle_referentiel);
            $sql = "SELECT * FROM {referentiel_referentiel}
                WHERE cle_referentiel=:cle_referentiel";
            $recs_refref = $DB->get_records_sql($sql, $params);
        }
        if (empty($recs_refref) || empty($data->cle_referentiel)){
            $params=array("code_referentiel" => $data->code_referentiel,
                "liste_codes_competence" => $data->liste_codes_competence);
            $sql = "SELECT * FROM {referentiel_referentiel}
                WHERE code_referentiel=:code_referentiel
                AND liste_codes_competence=:liste_codes_competence";
            $recs_refref = $DB->get_records_sql($sql, $params);
        }

        if (empty($recs_refref)){
            $newitemid = $DB->insert_record('referentiel_referentiel', $data);
            $this->set_mapping('occurrence', $oldid, $newitemid);

            // mettre à jour l'instance
            $rec_ref = $DB->get_record('referentiel', array("id" => $this->get_new_parentid('referentiel')));
            if ($rec_ref){
                $ok=$DB->set_field('referentiel','ref_referentiel',$newitemid, array("id" => $rec_ref->id));
            }
        }
        else{
            // tester le nombre d'occurrences candidates
            if (count($recs_refref)==1){  // une seule  occurrence candidate
                foreach( $recs_refref as $rec_reref){
                    $newitemid=$rec_reref->id;
                }
            }
            else{
                // utiliser la première disponible
                foreach( $recs_refref as $rec_reref){
                    $newitemid=$rec_reref->id;
                    if ($rec_reref->local==0){
                        break;
                    }
                }
            }
            // mettre à jour l'instance
            $rec_ref = $DB->get_record('referentiel', array("id" => $this->get_new_parentid('referentiel')));
            if ($rec_ref){
                $ok=$DB->set_field('referentiel','ref_referentiel',$newitemid, array("id" => $rec_ref->id));
            }
            $this->set_mapping('occurrence', $oldid, 0); // marquer pour eviter la reutilisation

        }

    }

    protected function process_domaine($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_referentiel = $this->get_new_parentid('occurrence');
        if (!empty($data->ref_referentiel)){
            $newitemid = $DB->insert_record('referentiel_domaine', $data);
            $this->set_mapping('domaine', $oldid, $newitemid);
        }
        else{
            $this->set_mapping('domaine', $oldid, 0);
        }
    }

    protected function process_competence($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_domaine = $this->get_new_parentid('domaine');
        if (!empty($data->ref_domaine)){
            $newitemid = $DB->insert_record('referentiel_competence', $data);
            $this->set_mapping('competence', $oldid, $newitemid);
        }
    }

    protected function process_item($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_competence = $this->get_new_parentid('competence');
        $data->ref_referentiel = $this->get_new_parentid('occurrence');
        if (!empty($data->ref_competence) && !empty($data->ref_referentiel)){
            $newitemid = $DB->insert_record('referentiel_item_competence', $data);
        }
    }

// MODIF JF 2012/03/12
    protected function process_protocole($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_occurrence = $this->get_new_parentid('occurrence');
        if (!empty($data->ref_occurrence)){
            $newitemid = $DB->insert_record('referentiel_protocol', $data);
            $this->set_mapping('protocole', $oldid, $newitemid);
        }
        else{
            $this->set_mapping('protocole', $oldid, 0);
        }
    }

    protected function process_certificat($data) {
        // PAS DE SAUVEGARDE REELLE ICI, JUSTE un stockage temporaire
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->ref_referentiel = REF_REF_TEMPID; // 'occurrence' n'est plus parent de certificat donc id temporaire
        if (!empty($data->ref_referentiel)){
            $newitemid = $DB->insert_record('referentiel_certificat', $data);
        }
    }

    protected function process_etablissement($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        // Eviter d'inserer si l'occurrence existe
        $params=array("num_etablissement" => $data->num_etablissement, "nom_etablissement" => $data->nom_etablissement);
        $sql = "SELECT * FROM {referentiel_etablissement}
            WHERE num_etablissement=:num_etablissement
            AND nom_etablissement=:nom_etablissement";
        $recs_etab = $DB->get_records_sql($sql, $params);
        if (empty($recs_etab)){
            $newitemid = $DB->insert_record('referentiel_etablissement', $data);
            // need to save this mapping as far as childs depend on it
            $this->set_mapping('etablissement', $oldid, $newitemid);
        }
        else{
            $this->set_mapping('etablissement', $oldid, 0);
        }
    }

    protected function process_etudiant($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ref_etablissement = $this->get_new_parentid('etablissement');
        $data->userid = $this->get_mappingid('user', $data->userid);
        if (!empty($data->ref_etablissement) && !empty($data->userid)){
            $newitemid = $DB->insert_record('referentiel_etudiant', $data);
            // No need to save this mapping as far as nothing depend on it
            // (child paths, file areas nor links decoder
        }
    }

    protected function process_a_user_pedago($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->refpedago = $this->get_new_parentid('pedagogie');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->refrefid= $this->get_mappingid('occurrence', $data->refrefid);
        if (!empty($data->refpedago) && !empty($data->userid) && !empty($data->refrefid)){
            $newitemid = $DB->insert_record('referentiel_a_user_pedagogie', $data);
        }
        $this->set_mapping('a_user_pedago', $oldid, $newitemid);
    }

     protected function process_pedagogie_record($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $newitemid = $DB->insert_record('referentiel_pedagogie', $data);
        $this->set_mapping('pedagogie', $oldid, $newitemid);
    }

    protected function process_scale($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->scaleid = $this->get_mappingid('scale', $data->scaleid);
        $data->timemodified= $this->apply_date_offset($data->timemodified);
        $newitemid = $DB->insert_record('referentiel_scale', $data);
        // need to save this mapping as far as somthing depend on it
        // (child paths, file areas nor links decoder)
        $this->set_mapping('scale', $oldid, $newitemid);
    }

    protected function process_scale_ref($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->refscaleid = $this->get_new_parentid('scale');
        $data->refrefid= $this->get_mappingid('occurrence', $data->refrefid);
        $data->timemodified= $this->apply_date_offset($data->timemodified);
        $newitemid = $DB->insert_record('referentiel_a_scale_ref', $data);
            // No need to save this mapping as far as nothing depend on it
            // (child paths, file areas nor links decoder
    }

    protected function process_activite_scale($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->refscaleid = $this->get_new_parentid('scale');
        $data->activiteid= $this->get_mappingid('activite', $data->activiteid);
        $newitemid = $DB->insert_record('referentiel_activite_scale', $data);
            // No need to save this mapping as far as nothing depend on it
            // (child paths, file areas nor links decoder
    }

    protected function after_execute() {
        global $DB;
        $userinfo = $this->get_setting_value('userinfo');
        // imposer les references vers la bonne version de l'occurrence
// MODIF JF
        
        if ($userinfo) {
            // recuperer la bonne valeur pour la reference de l'occurrence
            $rec_ref = $DB->get_record('referentiel', array("id" => $this->get_new_parentid('referentiel')));
            if ($rec_ref){
                $ref_instance=$rec_ref->id;
                $ref_referentiel=$rec_ref->ref_referentiel;
                if ($ref_instance && $ref_referentiel){
                    // activite
                    $recs_act = $DB->get_records('referentiel_activite', array("ref_instance" => $ref_instance));

                    if ($recs_act ){
                        foreach($recs_act as $rec_act){
                            $ok=$DB->set_field('referentiel_activite','ref_referentiel',$ref_referentiel, array("id" => $rec_act->id));
                        }
                    }
                    // taches
                    $recs_task = $DB->get_records('referentiel_task', array("ref_instance" => $ref_instance));
                    if ($recs_task){
                        foreach($recs_task as $rec_task){
                            $ok=$DB->set_field('referentiel_task','ref_referentiel',$ref_referentiel, array("id" => $rec_task->id));
                        }
                    }
                    // certificats
                    $recs_certif = $DB->get_records('referentiel_certificat', array("ref_referentiel" => REF_REF_TEMPID));
                    if ($recs_certif){
                        foreach($recs_certif as $rec_certif){
                            // Verifier si le certificat existe pour cet utilisateur dans la base de donnees
                            $params = array("refrefid" => $ref_referentiel, "userid" => $rec_certif->userid );
                            $sql="SELECT * FROM {referentiel_certificat} WHERE ref_referentiel=:refrefid AND userid=:userid";
                            $certificat_exists = $DB->get_record_sql($sql, $params);
                            if (!$certificat_exists){
            	               //The structure is equal to the db, so insert
            	               $rec_certif->ref_referentiel=$ref_referentiel;
                               $new_certificat_id = $DB->insert_record ("referentiel_certificat", $rec_certif);
                            }
                            else{
                                // Doit on ecraser le certificat existant ?
                                // Verifier les champs sensibles
                                if (!empty($certificat_exists->commentaire_certificat)){
                                    $commentaire=$certificat_exists->commentaire_certificat;
                                }
                                else{
                                    $commentaire=$rec_certif->commentaire_certificat;
                                }
                                if (!empty($certificat_exists->synthese_certificat)){
                                    $synthese=$certificat_exists->synthese_certificat;
                                }
                                else{
                                    $synthese=$rec_certif->synthese_certificat;
                                }
                                if (!empty($certificat_exists->decision_jury)){
                                    $decision_jury=$certificat_exists->decision_jury;
                                }
                                else{
                                    $decision_jury=$rec_certif->decision_jury;
                                }
                                if (!empty($certificat_exists->date_decision)){
                                    $date_decision=$certificat_exists->date_decision;
                                }
                                else{
                                    $date_decision=$rec_certif->date_decision;
                                }

                                // modifier le certificat à enregistrer
                                $rec_certif->id=$certificat_exists->id;
                                $rec_certif->ref_referentiel = $certificat_exists->ref_referentiel;
			                    $rec_certif->commentaire_certificat = $commentaire;
			                    $rec_certif->decision_jury = $decision_jury;
                                $rec_certif->date_decision = $date_decision;
				                $rec_certif->synthese_certificat = $synthese;
                                // tester le verrou
                                if ($certificat_exists->verrou){
                                    // verrou : on conserve la liste de compétences du serveur
                                    $rec_certif->competences_certificat=$certificat_exists->competences_certificat;
                                    $rec_certif->competences_activite=$certificat_exists->competences_activite;
                                    $rec_certif->evaluation = $certificat_exists->evaluation;
                                }
                                $rec_certif->verrou=$certificat_exists->verrou; // privilegier le serveur et pas la sauvegarde
                                // mise à jour
                				if ($DB->update_record("referentiel_certificat", $rec_certif)){
                                    // recalculer les listes (cela tient compte du verrou)
                                    referentiel_genere_certificat($rec_certif->userid, $rec_certif->ref_referentiel);
				                }
                            }
                        }
                    }
                    // supprimer le certificats temporaires
                    $DB->delete_records('referentiel_certificat', array("ref_referentiel" => REF_REF_TEMPID));
                }
            }
        }

        // Add refrentiel related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_referentiel', 'document', null);
        $this->add_related_files('mod_referentiel', 'consigne', null);
    }
}

?>
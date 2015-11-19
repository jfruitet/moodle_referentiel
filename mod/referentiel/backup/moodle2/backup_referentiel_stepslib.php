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
 * Define all the backup steps that will be used by the backup_referentiel_activity_task
 */

/*
     //This php script contains all the stuff to backup/restore
    //referentiel mods

    // This is the "graphical" structure of the referentiel module:
    //
    //                 (refrentiel INSTANCE)                                          (referentiel OCCURRENCE)
    //                     Table referentiel                                         Table referentiel_referentiel         Table referentiel_etablissement     Table referentiel_pedagogie
    //                    (CL,pk->id, fk->id_referentiel_referentiel)                         (pk->id)                         (pk->id, files)                      (pk->id)
    //                        |                        |                                        #                                   |                                 |
    //      |-----------------|                        |                                        |                      Table referentiel_etudiant         Table referentiel_a_user_pedagogie
    //      |                 |                        |-----------------n : 1 -----------------|                      (UL, pk->id, fk->id_etablissement,       ( pk->id, fk->id_pedagogie,
    //      |               1 : n                                                               |                       fk->id_user) --------------------------- fk->id_user, fk->id_referentiel_referentiel)
    //      |                 |                                                                 |                                                                                       |
    //      |                 |---------------------------------------|                         |-----|------------|--------------------------------------------------------------------|
    //      |                 |                                       |                               |            |
    //      |    Table referentiel_activites            Table referentiel_task                        |          Table referentiel_certificat
    //      |    (UL,pk->id, fk->referentiel,files,	  (UL,pk->id, fk->referentiel,files)              |          (pk->id, fk->id_referentiel_referentiel
    //      |              fk->id_user)          |      |             |                               |          fk->id_user)
    //      |                 |                  |      |             |                               |
    //      |               1 : n                |      |           1 : n                             |
    //      |                 |                  |      |             |                               |
    //      |          referentiel_document      |      |      referentiel_consigne          referentiel_domaine
    //      |    (pk->id, fk->id_activite, files)|      |     (pk->id, fk->id_task, files)    (pk->id, fk->id_referentiel_referentiel)
	//      |                                    |      |                                             |
	//      |                                    |      |                                    referentiel_competence
	//      |                                    |------|                                     (pk->id, fk->id_domaine)
	//		|   			                         |                                    |
	//    n : m                                      |                                       referentiel_item_competence
	//      |                             referentiel_a_user_task                            (pk->id, fk->id_competence, fk->id_referentiel_referentiel)
	//      |                    (UL, pk->id, fk->id_activite, fk->id_task)
	//      |-----------------|
	//                        |
	//             Table referentiel_accompagnement
    //             (UL, pk->id, fk->referentiel (ref_instance)
    //             fk->course (courseid), fk->id_user (userid), fk->id_user (teacherid)
    //
    //
    //
    //
	//
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------
*/

/**
 * Define the complete referentiel structure for backup, with file and id annotations
 */
class backup_referentiel_activity_structure_step extends backup_activity_structure_step {

    
    protected function define_structure() {
        global $DB;
        
        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $referentiel = new backup_nested_element('referentiel', array('id'), array(
            'name', 'description_instance', 'label_domaine', 'label_competence', 'label_item',
            'date_instance', 'ref_referentiel', 'config', 'config_impression',
            'intro', 'introformat'));

        // $referentiel_referentiels = new backup_nested_element('referentiel_referentiels');
// MODIF JF 2012/06/02 : labels added
        $occurrence = new backup_nested_element('occurrence', array('id'), array(
            'name', 'code_referentiel', 'mail_auteur_referentiel', 'cle_referentiel',
            'pass_referentiel', 'description_referentiel', 'url_referentiel',
            'seuil_certificat', 'minima_certificat', 'timemodified', 'nb_domaines', 'liste_codes_competence',
            'liste_empreintes_competence', 'liste_poids_competence', 'local',
            'logo_referentiel', 'config', 'config_impression', 'label_domaine_occurrence', 'label_competence_occurrence', 'label_item_occurrence'));

// MODIF JF 2012/03/12
        $protocole = new backup_nested_element('protocole', array('id'), array(
            'ref_occurrence', 'seuil_referentiel', 'minima_referentiel',
            'l_domaines_oblig', 'l_seuils_domaines', 'l_minimas_domaines',
            'l_competences_oblig', 'l_seuils_competences', 'l_minimas_competences',
            'l_items_oblig', 'timemodified', 'commentaire','actif'));


        $domaines = new backup_nested_element('domaines');
        $domaine = new backup_nested_element('domaine', array('id'), array(
            'code_domaine', 'description_domaine', 'ref_referentiel',
            'num_domaine', 'nb_competences', 'type_domaine', 'seuil_domaine', 'minima_domaine' ));

        $competences = new backup_nested_element('competences');
        $competence = new backup_nested_element('competence', array('id'), array(
            'code_competence', 'description_competence', 'ref_domaine',
            'num_competence', 'nb_item_competences', 'type_competence', 'seuil_competence', 'minima_competence'));

        $items = new backup_nested_element('items');
        $item = new backup_nested_element('item', array('id'), array(
            'code_item', 'description_item', 'ref_referentiel', 'ref_competence',
            'type_item', 'poids_item', 'empreinte_item', 'num_item'));

        $certificats = new backup_nested_element('certificats');
        $certificat = new backup_nested_element('certificat', array('id'), array(
            'commentaire_certificat', 'competences_certificat',
            'competences_activite', 'decision_jury', 'date_decision',
            'userid', 'teacherid', 'verrou',
            'valide', 'evaluation', 'mailed', 'mailnow', 'synthese_certificat'));

        $accompagnements = new backup_nested_element('accompagnements');
        $accompagnement = new backup_nested_element('accompagnement', array('id'), array(
            'accompagnement', 'userid', 'teacherid'));

        $activites = new backup_nested_element('activites');
        $activite = new backup_nested_element('activite', array('id'), array(
            'type_activite', 'description_activite', 'competences_activite',
            'commentaire_activite', 'ref_instance', 'ref_referentiel',
            'ref_course', 'userid', 'teacherid', 'date_creation',
            'date_modif_student', 'date_modif', 'approved',
            'ref_task', 'mailed', 'mailnow'));

        $documents = new backup_nested_element('documents');
        $document = new backup_nested_element('document', array('id'), array(
            'type_document', 'description_document',
            'url_document', 'ref_activite', 'cible_document',
            'etiquette_document', 'timestamp'));

        $activites_modules = new backup_nested_element('activites_modules');
        $activite_module = new backup_nested_element('activite_module', array('id'), array(
            'type', 'moduleid', 'ref_instance', 'ref_referentiel',
            'ref_course', 'userid', 'activiteid', 'ref_activite'));

        $tasks = new backup_nested_element('tasks');
        $task = new backup_nested_element('task', array('id'), array(
            'type_task', 'description_task', 'competences_task',
            'criteres_evaluation', 'ref_instance', 'ref_referentiel',
            'ref_course', 'auteurid', 'date_creation',
            'date_modif', 'date_debut', 'date_fin',
            'cle_souscription', 'souscription_libre',
            'mailed', 'mailnow', 'tache_masquee'));

        $consignes = new backup_nested_element('consignes');
        $consigne = new backup_nested_element('consigne', array('id'), array(
            'type_consigne', 'description_consigne',
            'url_consigne', 'ref_task', 'cible_consigne', 'etiquette_consigne', 'timestamp'));

        $a_user_tasks = new backup_nested_element('a_user_tasks');
        $a_user_task = new backup_nested_element('a_user_task', array('id'), array(
            'ref_user', 'date_selection', 'ref_activite'));


        $etablissements = new backup_nested_element('etablissements');
        $etablissement = new backup_nested_element('etablissement', array('id'), array(
            'num_etablissement', 'nom_etablissement',
            'adresse_etablissement', 'logo_etablissement'));

        $etudiants = new backup_nested_element('etudiants');
        $etudiant = new backup_nested_element('etudiant', array('id'), array(
            'num_etudiant', 'ddn_etudiant', 'lieu_naissance', 'departement_naissance',
            'adresse_etudiant', 'userid'));

        $pedagogies = new backup_nested_element('pedagogies');

        $a_user_pedago = new backup_nested_element('a_user_pedago', array('id'), array(
            'userid', 'refrefid'));
            
        $pedagogie_record = new backup_nested_element('pedagogie_record', array('id'), array(
            'promotion', 'num_groupe', 'date_cloture', 'formation',
            'pedagogie', 'composante', 'commentaire'));

        $scales = new backup_nested_element('scales');
        $scale = new backup_nested_element('scale', array('id'), array(
            'scaleid', 'name', 'scale',
            'maxscale', 'threshold', 'description',
            'descriptionformat', 'icons', 'labels', 'timemodified'));

        $scale_refs = new backup_nested_element('scale_refs');
        $scale_ref = new backup_nested_element('scale_ref', array('id'), array(
            'refscaleid', 'refrefid', 'timemodified'));

        $scale_activites = new backup_nested_element('activite_scales');
        $scale_activite = new backup_nested_element('activite_scale', array('id'), array(
            'refscaleid', 'activiteid', 'competences_bareme'));

            
        // Build the tree

        $referentiel->add_child($occurrence);

        $occurrence->add_child($protocole);
        
        $occurrence->add_child($domaines);
        $domaines->add_child($domaine);
        $domaine->add_child($competences);
        $competences->add_child($competence);
        $competence->add_child($items);
        $items->add_child($item);

        $referentiel->add_child($certificats);
        $certificats->add_child($certificat);


        $referentiel->add_child($accompagnements);
        $accompagnements->add_child($accompagnement);

        $referentiel->add_child($tasks);
        $tasks->add_child($task);
        $task->add_child($consignes);
        $consignes->add_child($consigne);


        $referentiel->add_child($activites);
        $activites->add_child($activite);
        $activite->add_child($documents);
        $documents->add_child($document);
        $activite->add_child($a_user_tasks);
        $a_user_tasks->add_child($a_user_task);

        $referentiel->add_child($etablissements);
        $etablissements->add_child($etablissement);
        $etablissement->add_child($etudiants);
        $etudiants->add_child($etudiant);

        $referentiel->add_child($pedagogies);
        $pedagogies->add_child($a_user_pedago);
        $a_user_pedago->add_child($pedagogie_record);
        
        $referentiel->add_child($scales);
        $scales->add_child($scale);
        
		$scale->add_child($scale_refs);
        $scale_refs->add_child($scale_ref);
        
		$scale->add_child($scale_activites);
        $scale_activites->add_child($scale_activite);
        
        // SOURCES
        $referentiel->set_source_table('referentiel', array('id' => backup::VAR_ACTIVITYID));

        $occurrence->set_source_sql('
                SELECT t_referentiel.*
                 FROM {referentiel_referentiel} as t_referentiel, {referentiel} as t_instance
                  WHERE t_instance.ref_referentiel=t_referentiel.id
                  AND t_instance.id = ? ',
                 array(backup::VAR_PARENTID));

        $protocole->set_source_sql('
                SELECT *
                FROM {referentiel_protocol}
                WHERE ref_occurrence = ?',
                array(backup::VAR_PARENTID));

        $domaine->set_source_sql('
            SELECT *
              FROM {referentiel_domaine}
             WHERE ref_referentiel = ?',
            array(backup::VAR_PARENTID));

        $competence->set_source_sql('
            SELECT *
              FROM {referentiel_competence}
             WHERE ref_domaine = ?',
            array(backup::VAR_PARENTID));

        $item->set_source_sql('
            SELECT *
              FROM {referentiel_item_competence}
             WHERE ref_competence = ?',
            array(backup::VAR_PARENTID));

        $scale->set_source_sql('
            SELECT *
              FROM {referentiel_scale}',
            array());

        $scale_ref->set_source_sql('
            SELECT *
              FROM {referentiel_a_scale_ref}
             WHERE refscaleid = ?',
            array(backup::VAR_PARENTID));

        $scale_activite->set_source_sql('
            SELECT *
              FROM {referentiel_activite_scale}
             WHERE refscaleid = ?',
            array(backup::VAR_PARENTID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {

            // seulement les certificats des utilisateurs ayant declare une activite pour cette instance
            $certificat->set_source_sql('
                SELECT DISTINCT c.*
                FROM {referentiel_certificat} c, {referentiel_activite} a
                WHERE c.ref_referentiel = a.ref_referentiel
                AND a.ref_instance = ?',
                array(backup::VAR_PARENTID));


            // ETABLISSEMENT
            // Define sources
            $etablissement->set_source_table('referentiel_etablissement', array());

            $accompagnement->set_source_sql('
                    SELECT *
                  FROM {referentiel_accompagnement}
                 WHERE ref_instance = ?',
                array(backup::VAR_PARENTID));

            $task->set_source_sql('
                    SELECT *
                    FROM {referentiel_task}
                 WHERE ref_instance = ?',
                array(backup::VAR_PARENTID));

            $consigne->set_source_sql('
                    SELECT *
                  FROM {referentiel_consigne}
                 WHERE ref_task = ?',
                array(backup::VAR_PARENTID));
/*
// Version a partir des tâches
            $a_user_task->set_source_sql('
                SELECT a_user.*
                  FROM {referentiel_a_user_task} as a_user, {referentiel_task} as task
                 WHERE a_user.ref_task = ? AND a_user.ref_task = task.id AND task.ref_instance= ?',
                array(backup::VAR_PARENTID, backup::VAR_ACTIVITYID));
*/
// Version a partir des activites
            $a_user_task->set_source_sql('
                SELECT a_user.*
                  FROM {referentiel_a_user_task} as a_user, {referentiel_activite} as activite
                 WHERE a_user.ref_activite = activite.id
                 AND activite.ref_instance= ? ',
                array(backup::VAR_ACTIVITYID));

            $activite->set_source_sql('
                SELECT *
                FROM {referentiel_activite}
                 WHERE ref_instance = ?',
                array(backup::VAR_PARENTID));

            $document->set_source_sql('
                SELECT *
                  FROM {referentiel_document}
                 WHERE ref_activite = ?',
                array(backup::VAR_PARENTID));


/*
            $etudiant->set_source_sql('
                    SELECT *
                    FROM {referentiel_etudiant}
                    WHERE ref_etablissement = ?',
                    array(backup::VAR_PARENTID));
*/
            // seulement les etudiants parmi les utilisateurs du cours ayant declare une activite
            $etudiant->set_source_sql('
                SELECT DISTINCT *
                    FROM {referentiel_etudiant}
                    WHERE ref_etablissement = ?
                    AND userid IN (SELECT DISTINCT a.userid FROM {referentiel_activite} a
                        WHERE a.ref_instance = ? )',
                array(backup::VAR_PARENTID , backup::VAR_ACTIVITYID));


            // Define sources


            // seulement les pedagogies ou sont inscrits des utilisateurs du cours
            // ayant declare une activite pour cette instance de cette occurrence de referentiel

            $a_user_pedago->set_source_sql('
                SELECT DISTINCT t_user.*
                FROM {referentiel_a_user_pedagogie} as t_user, {referentiel_activite} as a
                WHERE a.ref_instance = ?
                AND t_user.refrefid = a.ref_referentiel
                AND t_user.userid = a.userid',
                 array(backup::VAR_ACTIVITYID) );

            $pedagogie_record->set_source_sql('
                SELECT *
                FROM {referentiel_pedagogie}
                WHERE id IN (SELECT DISTINCT t_user.refpedago
                    FROM {referentiel_a_user_pedagogie} as t_user, {referentiel_activite} as a
                    WHERE a.ref_instance = ?
                    AND t_user.refrefid=a.ref_referentiel
                    AND t_user.userid=a.userid)',
                 array(backup::VAR_ACTIVITYID) );
                 
	        $scale_activite->set_source_sql('
    	        SELECT *
        	    FROM {referentiel_activite_scale}
             	WHERE refscaleid = ?',
            	array(backup::VAR_PARENTID));                             
        }

        // Define id annotations

        $certificat->annotate_ids('user', 'userid');
        $certificat->annotate_ids('user', 'teacherid');

        $accompagnement->annotate_ids('user', 'userid');
        $accompagnement->annotate_ids('user', 'teacherid');

        $activite->annotate_ids('user', 'userid');
        $activite->annotate_ids('user', 'teacherid');

        $task->annotate_ids('user', 'auteurid');
        $a_user_task->annotate_ids('user', 'ref_user');

        $etudiant->annotate_ids('user', 'userid');

        $a_user_pedago->annotate_ids('user', 'userid');

        $scale->annotate_ids('scale', 'scaleid');
        
        // Define file annotations
        $document->annotate_files('mod_referentiel', 'document', null); // This file area hasn't itemid
        $consigne->annotate_files('mod_referentiel', 'consigne', null); // This file area hasn't itemid

        // Return the root element (referentiel), wrapped into standard activity structure
        return $this->prepare_activity_structure($referentiel);
    }
}


?>

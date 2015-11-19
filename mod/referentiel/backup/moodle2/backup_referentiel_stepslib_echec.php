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

        $referentiel_referentiels = new backup_nested_element('referentiel_referentiels');
        $referentiel_referentiel = new backup_nested_element('referentiel_referentiel', array('id'), array(
            'name', 'code_referentiel', 'mail_auteur_referentiel', 'cle_referentiel',
            'pass_referentiel', 'description_referentiel', 'url_referentiel',
            'seuil_certificat', 'timemodified', 'nb_domaines', 'liste_codes_competence',
            'liste_empreintes_competence', 'liste_poids_competence', 'local',
            'logo_referentiel', 'config', 'config_impression'));

        $domaines = new backup_nested_element('domaines');
        $domaine = new backup_nested_element('domaine', array('id'), array(
            'code_domaine', 'description_domaine', 'ref_referentiel',
            'num_domaine', 'nb_competences'));

        $competences = new backup_nested_element('competences');
        $competence = new backup_nested_element('competence', array('id'), array(
            'code_competence', 'description_competence', 'ref_domaine',
            'num_competence', 'nb_item_competences'));

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
            'etiquette_document'));

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
            'url_consigne', 'ref_task', 'cible_consigne', 'etiquette_consigne'));

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
        $pedagogie = new backup_nested_element('pedagogie', array('id'), array(
            'promotion', 'num_groupe', 'date_cloture', 'formation',
            'pedagogie', 'composante', 'commentaire'));

        $a_user_pedagogies = new backup_nested_element('a_user_pedagogies');
        $a_user_pedagogie = new backup_nested_element('a_user_pedagogie', array('id'), array(
            'userid', 'refrefid'));


        // Build the tree

        $referentiel->add_child($referentiel_referentiels);
        $referentiel_referentiels->add_child($referentiel_referentiel);
        $referentiel_referentiel->add_child($domaines);
        $domaines->add_child($domaine);
        $domaine->add_child($competences);
        $competences->add_child($competence);
        $competence->add_child($items);
        $items->add_child($item);

        $referentiel_referentiel->add_child($certificats);
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

        $task->add_child($a_user_task);

        $referentiel->add_child($etablissements);
        $etablissements->add_child($etablissement);
        $etablissement->add_child($etudiants);
        $etudiants->add_child($etudiant);

        $referentiel->add_child($pedagogies);
        $pedagogies->add_child($pedagogie);
        $pedagogie->add_child($a_user_pedagogies);
        $a_user_pedagogies->add_child($a_user_pedagogie);


        // SOURCES
        $referentiel->set_source_table('referentiel', array('id' => backup::VAR_ACTIVITYID));

        // Define sources referentiel_referentiel

        $rec_referentiel_referentiel = $DB->get_record_sql("SELECT ref_referentiel as refrefid FROM {referentiel} WHERE id = ?",
                array(backup::VAR_ACTIVITYID));
        // DEBUG
                echo "<br />DEBUG :: backup_referentiel_stepslib.php :: 240 \n";
                print_object($rec_referentiel_referentiel);
                echo "<br />EXIT\n";
        exit;
        if ($rec_referentiel_referentiel ) {
            // DEBUG
            echo "<br />DEBUG :: backup_referentiel_stepslib.php :: 242\n";
            print_object($rec_referentiel_referentiel);
            
            $referentiel_referentiel->set_source_sql('
                SELECT *
                 FROM {referentiel_referentiel}
                 WHERE id = ?',
                 array($rec_referentiel_referentiel->refrefid));

            $domaine->set_source_sql('
            SELECT *
              FROM {referentiel_domaine}
             WHERE ref_referentiel = ?',
            array($rec_referentiel_referentiel->refrefid));

            $competence->set_source_sql('
            SELECT *
              FROM {referentiel_competence}
             WHERE ref_domaine = ?',
            array($rec_referentiel_referentiel->refrefid));

            $item->set_source_sql('
            SELECT *
              FROM {referentiel_item_competence}
             WHERE ref_competence = ?',
            array($rec_referentiel_referentiel->refrefid));

            if ($userinfo) {
                $certificat->set_source_sql('
                SELECT *
                FROM {referentiel_certificat}
                WHERE ref_referentiel = ?',
                array($rec_referentiel_referentiel->refrefid));
            }
        }
        else{
            $referentiel_referentiel->set_source_sql('
                SELECT *
                 FROM {referentiel_referentiel}
                 WHERE id IN (SELECT DISTINCT ref_referentiel
                  FROM {referentiel}
                  WHERE id = ?)',
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

            if ($userinfo) {
                $certificat->set_source_sql('
                SELECT *
                FROM {referentiel_certificat}
                WHERE ref_referentiel = ?',
                array(backup::VAR_PARENTID));
            }
        }

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {

            // ETABLISSEMENT
            // Define sources
            $etablissement->set_source_table('referentiel_etablissement', array());

            // Autres données dépendant du cours
            $course_rec=$DB->get_record_sql("SELECT course as courseid FROM {referentiel} WHERE id = ?",
                array(backup::VAR_ACTIVITYID));

            if ($course_rec){
                // DEBUG
                echo "<br />DEBUG :: backup_referentiel_stepslib.php :: 325\n";
                print_object($course_rec);
                echo "<br />\n";
                
                $accompagnement->set_source_sql('
                    SELECT *
                  FROM {referentiel_accompagnement}
                 WHERE ref_instance = ? AND courseid = ? ',
                array(backup::VAR_PARENTID, $course_rec->courseid));

                $task->set_source_sql('
                    SELECT *
                    FROM {referentiel_task}
                 WHERE ref_instance = ? AND ref_course = ? ',
                array(backup::VAR_PARENTID, $course_rec->courseid));

                $consigne->set_source_sql('
                    SELECT *
                    FROM {referentiel_consigne}
                    WHERE ref_task = ? ',
                    array(backup::VAR_PARENTID));

                $activite->set_source_sql('
                    SELECT *
                    FROM {referentiel_activite}
                    WHERE ref_instance = ? AND ref_course = ? ',
                    array(backup::VAR_PARENTID, $course_rec->courseid));

                $document->set_source_sql('
                    SELECT *
                    FROM {referentiel_document}
                    WHERE ref_activite = ?',
                    array(backup::VAR_PARENTID));

                // utilisateurs ayant depose une activite
                $user_recs=$DB->get_records_sql("SELECT DISTINCT userid FROM {referentiel_activite} WHERE ref_instance = ? ORDER BY userid ", array(backup::VAR_ACTIVITYID));

                if ($user_recs){
                    // DEBUG
                    echo "<br />DEBUG :: backup_referentiel_stepslib.php :: 364\n";
                    print_object($user_recs);
                    echo "<br />\n";

                    $params=array(backup::VAR_PARENTID);
                    $sql='SELECT * FROM {referentiel_a_user_task} WHERE ref_task = ? AND ( ref_user = ';
                    $sql_or='';
                    foreach($user_recs as $user_rec){
                        $params[]=$user_rec->userid;
                        if (empty($sql_or)){
                            $sql_or.= ' ? ';
                        }
                        else{
                            $sql_or.= ' OR ref_user = ? ';
                        }
                    }
                    $sql_or.= ' ) ';
                    // DEBUG
                    echo "<br />DEBUG :: backup_referentiel_stepslib.php :: 382\n";
                    echo "<br />$sql<br />\n";
                    print_r($params);
                    echo "<br />\n";
                    
                    $a_user_task->set_source_sql($sql, $params);
                }
                else{
                    $a_user_task->set_source_sql('
                    SELECT *
                    FROM {referentiel_a_user_task}
                    WHERE ref_task = ?',
                    array(backup::VAR_PARENTID));
                }
                
                // ETUDIANTS AYANT DEPOSE UNE DECLARATION D'ACTIVITE
                if ($user_recs){
                    $params=array(backup::VAR_PARENTID);
                    $sql='SELECT * FROM {referentiel_etudiant} WHERE ref_etablissement = ? AND ( userid = ';
                    $sql_or='';
                    foreach($user_recs as $user_rec){
                        $params[]=$user_rec->userid;
                        if (empty($sql_or)){
                            $sql_or.= ' ? ';
                        }
                        else{
                            $sql_or.= ' OR userid = ? ';
                        }
                    }
                    $sql_or.= ' ) ';
                    // DEBUG
                    echo "<br />DEBUG :: backup_referentiel_stepslib.php :: 413\n";
                    echo "<br />$sql<br />\n";
                    print_r($params);
                    echo "<br />\n";

                    $etudiant->set_source_sql($sql, $params);
                }
                else{
                    $etudiant->set_source_sql('
                    SELECT *
                    FROM {referentiel_etudiant}
                    WHERE ref_etablissement = ?',
                    array(backup::VAR_PARENTID));
                }
                
                // Define sources
                $pedagogie->set_source_table('referentiel_pedagogie', array());

                // UTILISATEURS AYANT DEPOSE UNE DECLARATION D'ACTIVITE
                if ($user_recs){
                    $params=array(backup::VAR_PARENTID);
                    $sql='SELECT * FROM {referentiel_a_user_pedagogie} WHERE refpedago = ? AND ( userid = ';
                    $sql_or='';
                    foreach($user_recs as $user_rec){
                        $params[]=$user_rec->userid;
                        if (empty($sql_or)){
                            $sql_or.= ' ? ';
                        }
                        else{
                            $sql_or.= ' OR userid = ? ';
                        }
                    }
                    $sql_or.= ' ) ';
                    if ($rec_referentiel_referentiel ) {
                        $params[]=$rec_referentiel_referentiel->refrefid;
                    }
                    // DEBUG
                    echo "<br />DEBUG :: backup_referentiel_stepslib.php :: 382\n";
                    echo "<br />$sql<br />\n";
                    print_r($params);
                    echo "<br />\n";

                    $a_user_pedagogie->set_source_sql($sql, $params);
                }
                else{
                    $a_user_pedagogie->set_source_sql('
                    SELECT *
                    FROM {referentiel_a_user_pedagogie}
                    WHERE refpedago = ?',
                    array(backup::VAR_PARENTID));
                }
            }
            else{
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

                $a_user_task->set_source_sql('
                SELECT *
                  FROM {referentiel_a_user_task}
                 WHERE ref_task = ?',
                array(backup::VAR_PARENTID));

                $etudiant->set_source_sql('
                    SELECT *
                    FROM {referentiel_etudiant}
                    WHERE ref_etablissement = ?',
                    array(backup::VAR_PARENTID));

                // Define sources
                $pedagogie->set_source_table('referentiel_pedagogie', array());

                $a_user_pedagogie->set_source_sql('
                SELECT *
                FROM {referentiel_a_user_pedagogie}
                WHERE refpedago = ?',
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
        
            $a_user_pedagogie->annotate_ids('user', 'userid');
        }
        
        // Define file annotations
        $document->annotate_files('mod_referentiel', 'document', null); // This file area hasn't itemid
        $consigne->annotate_files('mod_referentiel', 'consigne', null); // This file area hasn't itemid


        // Return the root element (referentiel), wrapped into standard activity structure
        return $this->prepare_activity_structure($referentiel);
    }
}


?>

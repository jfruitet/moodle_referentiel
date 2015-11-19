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
 * This file keeps track of upgrades to
 * the referentiel module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package mod-forum
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @copyright 2011 onwards Jean Fruitet(jfruitet) {@link http://www.univ-nantes.fr}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



function xmldb_referentiel_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

        // VERSION Moodle 2.x
        /// Add intro to table referentiel description

        $table = new xmldb_table('referentiel');
        /// Adding fields
        $field = new xmldb_field('intro');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'config_impression');
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);

            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel}", null);
            foreach ($rs as $res){
                if (!empty($res->description_instance)) {
                    $res->intro=$res->description_instance;
                }
                $DB->update_record('referentiel', $res);
            }
            $rs->close();
        }
        /// Adding fields
        $field = new xmldb_field('introformat');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'intro');
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }


/// And upgrade begins here. 

//===== 1.9.0 upgrade line ======//
    if ($oldversion < 2008052700) {
    /// Define field evaluation to be added to referentiel_certificat
        $table = new xmldb_table('referentiel_certificat');
        $field = new xmldb_field('evaluation');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'valide');
    /// Launch add field evaluation
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2008052700, 'referentiel');
    }		
	
    if ($oldversion < 2008052800) {	
    /// Define field logo_etablissement to be added to referentiel_etablissement
        $table = new xmldb_table('referentiel_etablissement');
        $field = new xmldb_field('logo_etablissement');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'adresse_etablissement');
    /// Launch add field referentiel_etablissement
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

	    /// Add some values to referentiel_etablissement
    	    $rec = new stdClass;
			$rec->num_etablissement = 'INCONNU';
			$rec->nom_etablissement = 'A COMPLETER';
			$rec->adresse_etablissement = 'A COMPLETER';
			$rec->logo_etablissement = ' ';
    	/// Insert the add action in log_display
        	$DB->insert_record('referentiel_etablissement', $rec, false, false);

        upgrade_mod_savepoint(true, 2008052800, 'referentiel');
	}
	
	if ($oldversion < 2008062300) {	// VERSION 1.2
   /// Define new  field liste_codes_competence to be added to referentiel_referentiel
        $table1 = new xmldb_table('referentiel_referentiel');
        $field1 = new xmldb_field('liste_codes_competence');
        $field1->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'nb_domaines');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table1,$field1)) {
            $dbman->add_field($table1, $field1);
        }

   /// Define new  field liste_empreintes_competence to be added to referentiel_referentiel
        $field2 = new xmldb_field('liste_empreintes_competence');
        $field2->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'liste_codes_competence');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table1,$field2)) {
            $dbman->add_field($table1, $field2);
        }

   /// Define new  field logo_referentiel to be added to referentiel_referentiel
        $field3 = new xmldb_field('logo_referentiel');
        $field3->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'local');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table1,$field3)) {
            $dbman->add_field($table1, $field3);
        }

   /// Define new  field empreinte_item to be added to referentiel_item_competence
        $table2 = new xmldb_table('referentiel_item_competence');
        $field4 = new xmldb_field('empreinte_item');
        $field4->set_attributes(XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0', 'poids_item');
    /// change fiel type field  etiquette_url in referentiel_document
        if(!$dbman->field_exists($table2,$field4)) {
            $dbman->add_field($table2, $field4);
        }
        upgrade_mod_savepoint(true, 2008062300, 'referentiel');
	}
	
	if ($oldversion < 2009042900) { // VERSION 3.0
	   /// Define new  field config  to be added to referentiel
        $table = new xmldb_table('referentiel');
        $field = new xmldb_field('config');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'scol:0;creref:0;selref:0;impcert:0;graph:0;', 'visible');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }


	   /// Redefinir type field liste_codes_competence de referentiel_referentiel
        $table = new xmldb_table('referentiel_referentiel');
        $field = new xmldb_field('liste_codes_competence');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'nb_domaines');
    /// Change field type
        if ($dbman->field_exists($table,$field)) {
            $dbman->change_field_type($table, $field);
		}
		
		/// Refinir type field liste_empreintes_competence referentiel_referentiel
        $field = new xmldb_field('liste_empreintes_competence');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'liste_codes_competence');
    /// Change field type
        if ($dbman->field_exists($table,$field)) {
            $dbman->change_field_type($table, $field);
		}

		/// Refinir type field competences_activite de referentiel_activite
        $table = new xmldb_table('referentiel_activite');
        $field = new xmldb_field('competences_activite');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'description_activite');
    /// Change field type
        if ($dbman->field_exists($table,$field)) {
            $dbman->change_field_type($table, $field);
		}

		/// Refinir type field competences_certificat de referentiel_certificat
        $table = new xmldb_table('referentiel_certificat');
        $field = new xmldb_field('competences_certificat');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'commentaire_certificat');
    /// Change field type
        if ($dbman->field_exists($table,$field)) {
            $dbman->change_field_type($table, $field);
		}

		// NOUVEAUX CHAMPS
		/// Define new  fields to be added to referentiel_referentiel
        $table = new xmldb_table('referentiel_referentiel');
        $field = new xmldb_field('mail_auteur_referentiel');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'code_referentiel');
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('cle_referentiel');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'mail_auteur_referentiel');
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('pass_referentiel');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'cle_referentiel');
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }


	/// Nouvelles tables 
    /// Define table referentiel_task to be created
        $table = new xmldb_table('referentiel_task');

    /// Adding fields to table referentiel_task
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('type_task', XMLDB_TYPE_CHAR, '80', null, XMLDB_NOTNULL, null, null,  'id');
        $table->add_field('description_task', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'type_task');
        $table->add_field('competences_task', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'description_task');
        $table->add_field('criteres_evaluation', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'competences_task');
        $table->add_field('ref_instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'criteres_evaluation');
        $table->add_field('ref_referentiel', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_instance');
        $table->add_field('ref_course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_referentiel');
        $table->add_field('auteurid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_course');
        $table->add_field('date_creation', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'auteurid');
        $table->add_field('date_modif', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'date_creation');
        $table->add_field('date_debut', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'date_modif');
        $table->add_field('date_fin', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'date_debut');

    /// Adding keys to table referentiel_task
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding index to table referentiel_notification_queue
        $table->add_index('user',XMLDB_INDEX_NOTUNIQUE, array('userid'));

    /// Launch create table for referentiel_task
        if (!$dbman->table_exists($table)){
            $dbman->create_table($table, true, true);
        }
		
    /// Define table referentiel_consigne to be created
        $table = new xmldb_table('referentiel_consigne');

    /// Adding fields to table referentiel_consigne
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('type_consigne', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('description_consigne', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'type_consigne');
        $table->add_field('url_consigne', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'description_consigne');
        $table->add_field('ref_task', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'url_consigne');
    /// Adding keys to table referentiel_consigne
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    /// Launch create table for referentiel_consigne
        if (!$dbman->table_exists($table)){
            $dbman->create_table($table, true, true);
        }

		
    /// Define table referentiel_a_user_task to be created
        $table = new xmldb_table('referentiel_a_user_task');

    /// Adding fields to table referentiel_a_user_task
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('ref_user', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('ref_task', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_user');
    /// Adding keys to table referentiel_a_user_task
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    /// Launch create table for referentiel_a_user_task
        if (!$dbman->table_exists($table)){
            $dbman->create_table($table, true, true);
        }

		
		// AJOUT CHAMP a table referentiel_activite
        $table = new xmldb_table('referentiel_activite');
        $field = new xmldb_field('ref_task');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'approved');
    /// Launch add field ref_task
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

		
        $table = new xmldb_table('referentiel_a_user_task');
        $field = new xmldb_field('date_selection');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_task');
    /// Launch add field ref_task
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

		
        $table = new xmldb_table('referentiel_a_user_task');
        $field = new xmldb_field('ref_activite');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'date_selection');
    /// Launch add field ref_task
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009042900, 'referentiel');

	}
	
	
	   if ($oldversion < 2009083100) { // VERSION 3.2.5
    /// Changing type of field seuil_certificat on table referentiel_referentiel to float
        $table = new xmldb_table('referentiel_referentiel');
        $field = new xmldb_field('seuil_certificat');
        $field->set_attributes(XMLDB_TYPE_FLOAT, null, null, XMLDB_NOTNULL, null, '0', 'url_referentiel');
    /// Launch change of type for field seuil_certificat
        if ($dbman->field_exists($table,$field)) {
            $dbman->change_field_type($table, $field);
		}

    /// Changing type of field poids_item on table referentiel_item_competence to float
        $table = new xmldb_table('referentiel_item_competence');
        $field = new xmldb_field('poids_item');
        $field->set_attributes(XMLDB_TYPE_FLOAT, null, null, XMLDB_NOTNULL, null, '0', 'type_item');
    /// Launch
        if ($dbman->field_exists($table,$field)) {
            $dbman->change_field_type($table, $field);
		}

        upgrade_mod_savepoint(true, 2009083100, 'referentiel');
    }
	
	if ($oldversion < 2009110100) { // VERSION 4.0.1
    	// Nouveau champ liste_poids_competence dans referentiel_referentiel
   		/// Define new  field liste_poids_competence to be added to referentiel_referentiel
        $table = new xmldb_table('referentiel_referentiel');
        $field = new xmldb_field('liste_poids_competence');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'liste_empreintes_competence');
    	/// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

		// Nouveau champ date_modif_student dans referentiel_activite
		/// Define new  field liste_poids_competence to be added to referentiel_referentiel
        $table = new xmldb_table('referentiel_activite');
        $field = new xmldb_field('date_modif_student');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'date_creation');
    	/// Launch add field 
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009110100, 'referentiel');

	}
	if ($oldversion < 2009112800) { // VERSION 4.1.2
    	// Nouveau champ competences_activite dans referentiel_certificat
   		/// Define new  field competences_activite to be added to referentiel_certificat
        $table = new xmldb_table('referentiel_certificat');
        $field = new xmldb_field('competences_activite');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'competences_certificat');
    	/// Launch add field 
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009112800, 'referentiel');
	}
	if ($oldversion < 2009122009) { // VERSION 4.2.0
    	// Nouveau champ cible_document dans referentiel_document
   		/// Define new  field cible_document to be added to referentiel_document
        $table1 = new xmldb_table('referentiel_document');
        $field1 = new xmldb_field('cible_document');
        $field1->set_attributes(XMLDB_TYPE_INTEGER, 4, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'ref_activite');
    	/// Launch add field 
        if(!$dbman->field_exists($table1,$field1)) {
            $dbman->add_field($table1, $field1);
        }

    	// Nouveau champ etiquette_document dans referentiel_document
   		/// Define new  field etiquette to be added to referentiel_document
        $field2 = new xmldb_field('etiquette_document');
        $field2->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'cible_document');
    	/// Launch add field 
        if(!$dbman->field_exists($table1,$field2)) {
            $dbman->add_field($table1, $field2);
        }

        $table2 = new xmldb_table('referentiel_consigne');
        $field3 = new xmldb_field('cible_consigne');
        $field3->set_attributes(XMLDB_TYPE_INTEGER, 4, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'ref_task');
        if(!$dbman->field_exists($table2,$field3)) {
            $dbman->add_field($table2, $field3);
        }

		$field4 = new xmldb_field('etiquette_consigne');
        $field4->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'cible_consigne');
    	/// Launch add field 
        if(!$dbman->field_exists($table2,$field4)) {
            $dbman->add_field($table2, $field4);
        }

        upgrade_mod_savepoint(true, 2009122009, 'referentiel');
	}

	if ($oldversion < 2010010500) { // VERSION 4.2.1
	   	/// Define new  default for field config for referentiel table
        $table = new xmldb_table('referentiel');
		$field2 = new xmldb_field('config_impression');
        $field2->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;', 'config');
	    /// Launch add field referentiel
        if(!$dbman->field_exists($table,$field2)) {
            $dbman->add_field($table, $field2);
        }

        upgrade_mod_savepoint(true, 2010010500, 'referentiel');
	}
	if ($oldversion < 2010011000) { // VERSION 4.2.3	
	   	/// Define new  fields for referentiel_task table
        $table = new xmldb_table('referentiel_task');       
		$field = new xmldb_field('cle_souscription');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'date_fin');

        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

		$field2 = new xmldb_field('souscription_libre');
        $field2->set_attributes(XMLDB_TYPE_INTEGER, 4, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'cle_souscription');
    	    /// Launch add field referentiel
        if(!$dbman->field_exists($table,$field2)) {
            $dbman->add_field($table, $field2);
        }

        upgrade_mod_savepoint(true, 2010010500, 'referentiel');
	}

  	if ($oldversion < 2010021200) { // VERSION 4.4.4	      
    /// Define table referentiel_notification_queue to be created
        $table = new xmldb_table('referentiel_notification_queue');

    /// Adding fields to table referentiel_notification_queue
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('activiteid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'userid');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'activiteid');
        $table->add_field('activiteid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'userid');
        $table->add_field('type', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'timemodified');
    /// Adding keys to table referentiel_notification_queue
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('activiteid', XMLDB_KEY_FOREIGN, array('activiteid'), 'referentiel_activite', array('id') );
    /// Adding index to table referentiel_notification_queue
        $table->add_index('user', XMLDB_INDEX_NOTUNIQUE, array('userid'));
     /// Create table for referentiel_notification_queue
        if (!$dbman->table_exists($table)){
            $dbman->create_table($table, true, true);
        }

	   	/// Define new  fields for referentiel_activite table
        $table = new xmldb_table('referentiel_activite');       
		$field = new xmldb_field('mailed');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_task');
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

		$field = new xmldb_field('mailnow');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'mailed');
    	/// Launch add field referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

	   	/// Define new  fields for referentiel_task table
        $table = new xmldb_table('referentiel_task');       
		$field = new xmldb_field('mailed');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'souscription_libre');
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

		$field = new xmldb_field('mailnow');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'mailed');
    	    /// Launch add field referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

	   	/// Define new  fields for referentiel_certificat table
        $table = new xmldb_table('referentiel_certificat'); 
		
        $field = new xmldb_field('mailed');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'evaluation');
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

		$field = new xmldb_field('mailnow');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'mailed');
    	/// Launch add field referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2010021200, 'referentiel');
  	}
  

  	if ($oldversion < 2010022800) { // VERSION 5.1.0	      
    /// Define table referentiel_activite_modules to be created
        $table = new xmldb_table('referentiel_activite_modules');

    /// Adding fields to table referentiel_activite_modules
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '80', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('moduleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'type');
        $table->add_field('ref_instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'moduleid');
        $table->add_field('ref_referentiel', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_instance');
        $table->add_field('ref_course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_referentiel');
    	/// Launch add field referentiel
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_course');
        $table->add_field('activiteid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'userid');
    /// Adding keys to table referentiel_activite_modules
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    /// Launch create table for referentiel_activite_modules
        if (!$dbman->table_exists($table)){
            $dbman->create_table($table, true, true);
        }

        upgrade_mod_savepoint(true, 2010022800, 'referentiel');
    }

    if ($oldversion < 2010031600) { // VERSION 5.2.0
	   	/// Define new  fields for referentiel_task table
        $table = new xmldb_table('referentiel_task');       
		$field = new xmldb_field('tache_masquee');
        $field->set_attributes(XMLDB_TYPE_INTEGER, 4, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'souscription_libre');
    	/// Launch add field referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2010031600, 'referentiel');
	 }


   	if ($oldversion < 2010032500) { // VERSION 5.2.1	      
    /// Define table referentiel_accompagnement to be created
        $table = new xmldb_table('referentiel_accompagnement');

    /// Adding fields to table referentiel_accompagnement
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('accompagnement', XMLDB_TYPE_CHAR, '3', null, XMLDB_NOTNULL, null, 'REF', 'id');
        $table->add_field('ref_instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'accompagnement');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'ref_instance');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'courseid');
        $table->add_field('teacherid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'userid');
    /// Adding keys to table referentiel_accompagnement
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Launch create table for referentiel_accompagnement
        if (!$dbman->table_exists($table)){
            $dbman->create_table($table, true, true);
        }

        upgrade_mod_savepoint(true, 2010032500, 'referentiel');
    }
 
    if ($oldversion < 2010033110) { // VERSION 5.2.3

    /// Drop table referentiel_notification_queue
    /// because table name don't respect XML facet specification

    /// Define table referentiel_notification to be created
        $table = new xmldb_table('referentiel_notification');

    /// Adding fields to table referentiel_notification
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('activiteid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'userid');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'activiteid');
        $table->add_field('type', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'timemodified');
    /// Adding keys to table referentiel_notification
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('activiteid', XMLDB_KEY_FOREIGN, array('activiteid'), 'referentiel_activite', array('id') );

    /// Adding index to table referentiel_notification
        $table->add_index('user', XMLDB_INDEX_NOTUNIQUE, array ('userid') );

    /// Launch create table for referentiel_notification
        if (!$dbman->table_exists($table)){
            $dbman->create_table($table, true, true);
        }

        $tableold = new xmldb_table('referentiel_notification_queue');
        /// Silenty drop any previous referentiel_notification_queue table
        if (table_exists($tableold)) {
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel_notification_queue}", null);
            foreach ($rs as $res){
                $DB->insert_record('referentiel_notification', $res, false, false);
            }
            $rs->close();
            $status = $dbman->drop_table($tableold, true, true);
        }

        upgrade_mod_savepoint(true, 2010033110, 'referentiel');
    }

    if ($oldversion < 2010060900) { // VERSION 5.3.3
    /// Define table referentiel_certificat to be updated
        $table = new xmldb_table('referentiel_certificat');
    /// Adding fields to table referentiel_certificat
        $field = new xmldb_field('synthese_certificat');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'mailnow');
    /// Launch add field
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2010060900, 'referentiel');
    }
    if ($oldversion < 2010101800) { // VERSION 5.4.3
    /// Define table referentiel_certificat to be updated
        $table = new xmldb_table('referentiel_referentiel');
    /// Adding fields to table referentiel_referentiel
        $field = new xmldb_field('config');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'scol:0;creref:0;selref:0;impcert:0;graph:0;', 'logo_referentiel');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Adding fields to table referentiel_referentiel
		$field2 = new xmldb_field('config_impression');
        $field2->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;', 'config');
	    /// Launch add field referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field2);
        }

        upgrade_mod_savepoint(true, 2010101800, 'referentiel');
    }

  	if ($oldversion < 2010111100) { // VERSION 5.4.4
    /// Add new field to table referentiel_activite_modules
        $table = new xmldb_table('referentiel_activite_modules');
    /// Adding fields
        $field = new xmldb_field('ref_activite');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'activiteid');
    /// Launch add field
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2010111100, 'referentiel');
    }

    if ($oldversion < 2011022012) { // VERSION 5.5.1
        /// mise à jour
        $table = new xmldb_table('referentiel');
          /// Silenty update
        if ($dbman->table_exists($table)){
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel}", null);
            foreach ($rs as $res){
                $res->config=$res->config."graph:0;";
                $DB->update_record('referentiel', $res);
            }
            $rs->close();
        }

        $table2 = new xmldb_table('referentiel_referentiel');
        /// Silently update
        if ($dbman->table_exists($table2)){
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel_referentiel}", null);
            foreach ($rs as $res){
                $res->config=$res->config."graph:0;";
                $DB->update_record('referentiel_referentiel', $res);
            }
            $rs->close();
        }
        
        upgrade_mod_savepoint(true, 2011022012, 'referentiel');
    }

    if ($oldversion < 2011031100) {  // VERSION 5.5.5

    /// Define table referentiel_pedagogie to be created
        $table = new xmldb_table('referentiel_pedagogie');

    /// Adding fields to table chat_messages_current
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('promotion', XMLDB_TYPE_CHAR, '4', null, null, null, null);
        $table->add_field('num_groupe', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('date_cloture',XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('formation',XMLDB_TYPE_CHAR, '40', null, null, null, null);
        $table->add_field('pedagogie',XMLDB_TYPE_CHAR, '40', null, null, null, null);
        $table->add_field('composante',XMLDB_TYPE_CHAR, '40', null, null, null, null);
        $table->add_field('commentaire',XMLDB_TYPE_CHAR, '40', null, null, null, null);
        
    /// Adding keys to table
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// create table
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


    /// Define table referentiel_a_user_pedagogie to be created

        $table = new xmldb_table('referentiel_a_user_pedagogie');
    /// Adding fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('refrefid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('refpedago', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        /// create table
        if (!$dbman->table_exists($table)){
            $dbman->create_table($table, true, true);
        }

        upgrade_mod_savepoint(true, 2011031100, 'referentiel');
    }


    if ($oldversion < 2011042204) {
    /*
        /// Adding index to table referentiel_notification_queue
        $index = new xmldb_index('user');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('userid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    */
        // Supprimer une source d'erreur lors de la creation d'un certificat
    /// Define index uniqueuser (unique) to be dropped from referentiel_certificat
        $table = new xmldb_table('referentiel_certificat');
        $index = new xmldb_index('uniqueuser');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('userid'));

    /// Launch drop index uniqueuser
        if ($dbman->index_exists($table,$index)) {
            $dbman->drop_index($table, $index);
        }
        // recreer index non unique
    /// Define index indexuser (not unique) to be added to referentiel_certificat
        $table = new xmldb_table('referentiel_certificat');
        $index = new xmldb_index('indexuser');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('userid'));

    /// Launch drop index uniqueuser
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2011042204, 'referentiel');
    }


    if ($oldversion < 2011042205) {  // VERSION 5.6.01
    /// Define index userpedagoref (unique) to be added to referentiel_a_user_pedagogie
        $table = new xmldb_table('referentiel_a_user_pedagogie');
        $index = new xmldb_index('userpedagoref');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('userid', 'refrefid', 'refpedago'));
   /// Launch drop index uniqueuser
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        upgrade_mod_savepoint(true, 2011042205, 'referentiel');

    }

    if ($oldversion < 2011042213) {  // VERSION 5.6.02

        // Define table referentiel_course_users
        $table = new xmldb_table('referentiel_course_users');
        
    /// Adding fields to table
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('tab_users', XMLDB_TYPE_BINARY, 'big', null, null, null, null);
        $table->add_field('tab_initiales', XMLDB_TYPE_BINARY, 'big', null, null, null, null);
        $table->add_field('tab_pedagos', XMLDB_TYPE_BINARY, 'big', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('refrefid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timestamp', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table
        $table->add_index('refcourse', XMLDB_INDEX_UNIQUE, array('courseid', 'refrefid'));

    /// create table for
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2011042213, 'referentiel');
    }

    if ($oldversion < 2011082500) {  // VERSION Moodle 1.9 - 5.6.03 et Moodle 2 - 1.0.6

        // Update type_activite field
        $table = new xmldb_table('referentiel_activite');
        $field = new xmldb_field('type_activite');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        /// Launch change of type for field type_activite
        //if ($dbman->field_exists($table,$field)) {
            $dbman->change_field_type($table, $field);
		//}
        // Update type_task field
        $table1 = new xmldb_table('referentiel_task');
        $field1 = new xmldb_field('type_task');
        $field1->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        /// Launch change of type for field type_task
        if ($dbman->field_exists($table1,$field1)) {
            $dbman->change_field_type($table1, $field1);
		}

        upgrade_mod_savepoint(true, 2011082500, 'referentiel');
    }
    

    if ($oldversion < 2011082610) { // VERSION Moodle 1.9 - 5.6.04 et Moodle 2 - 1.0.8
	   /// Modify field default config table referentiel
        $table = new xmldb_table('referentiel');
        $field = new xmldb_field('config');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;', 'visible');
        /// Launch change of type for field type_activite
        $dbman->change_field_default($table, $field);

	   /// Modify field default config table referentiel_referentiel
        $table1 = new xmldb_table('referentiel_referentiel');
        $field1 = new xmldb_field('config');
        $field1->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;', 'logo_referentiel');
        /// Launch change of type for field type_activite
        $dbman->change_field_default($table1, $field1);

        /// mise à jour des champs existants
        $table2 = new xmldb_table('referentiel');
          /// Silenty update
        if ($dbman->table_exists($table2)){
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel}", null);
            foreach ($rs as $res){
                $res->config=$res->config."light:0;";
                $DB->update_record('referentiel', $res);
            }
            $rs->close();
        }

        $table3 = new xmldb_table('referentiel_referentiel');
        /// Silently update
        if ($dbman->table_exists($table3)){
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel_referentiel}", null);
            foreach ($rs as $res){
                $res->config=$res->config."light:0;";
                $DB->update_record('referentiel_referentiel', $res);
            }
            $rs->close();
        }

        upgrade_mod_savepoint(true, 2011082610, 'referentiel');
    }

    if ($oldversion < 2011090710) { // VERSION Moodle 1.9 - 5.6.08 et Moodle 2 - 1.0.9
       // Define table referentiel_activite
        $table = new xmldb_table('referentiel_activite');
    /// Adding index to table referentiel_activite
        $index1 = new xmldb_index('indexuser');
        $index1->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('userid'));
        if (!$dbman->index_exists($table, $index1)) {
            $dbman->add_index($table, $index1);
        }
    /// Adding index to table referentiel_activite
        $index2 = new xmldb_index('indexinstance');
        $index2->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('ref_instance'));
        if (!$dbman->index_exists($table, $index2)) {
            $dbman->add_index($table, $index2);
        }

        upgrade_mod_savepoint(true, 2011090710, 'referentiel');

    }


	if ($oldversion < 2011090712)  {  // VERSION Moodle 1.9 - 5.6.09 et Moodle 2 - 1.0.7
    /// Define index userid (not unique) to be added to referentiel_task
        $table = new xmldb_table('referentiel_task');
        $index = new xmldb_index('indexinstance');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('ref_instance'));
    /// Launch add index
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    /// Define index activite (not unique) to be added to referentiel_document
        $table1 = new xmldb_table('referentiel_document');
        $index1 = new xmldb_index('indexactivite');
        $index1->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('ref_activite'));
    /// Launch add index
        if (!$dbman->index_exists($table1, $index1)) {
            $dbman->add_index($table1, $index1);
        }

    /// Define index instanceid (not unique) to be added to referentiel_consigne
        $table2 = new xmldb_table('referentiel_consigne');
        $index2 = new xmldb_index('indextask');
        $index2->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('ref_task'));
    /// Launch add index
        if (!$dbman->index_exists($table2, $index2)) {
            $dbman->add_index($table2, $index2);
        }
        
        upgrade_mod_savepoint(true, 2011090712, 'referentiel');
        
    }

	if ($oldversion < 2011092710)  {  // VERSION Moodle 1.9 - 5.6.09 et Moodle 2 - 1.1.4
    /// Define new field maxbytes
        $table = new xmldb_table('referentiel');
        $field = new xmldb_field('maxbytes');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1073741824', 'config_impression');
    /// Launch add field evaluation
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        /// Silently update
        if ($dbman->table_exists($table)) {
            $maxbytes=1073741824;
            $sql="SELECT id, course FROM {referentiel}";
            if ($rs= $DB->get_records_sql($sql, NULL)){
                foreach ($rs as $res){
                    $course= $DB->get_record('course', array("id"=>"$res->course"));
                    $maxbytes=get_max_upload_file_size($CFG->maxbytes, $course->maxbytes);
                    $DB->set_field("referentiel","maxbytes","$maxbytes", array("id" => "$res->id"));
                }
            }
        }

    /// Define index referenteil (not unique) to be added to referentiel_activite
        $table1 = new xmldb_table('referentiel_activite');
        $index1 = new xmldb_index('indexreferentiel');
        $index1->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('ref_referentiel'));
    /// Launch add index
        if (!$dbman->index_exists($table1, $index1)) {
            $dbman->add_index($table1, $index1);
        }

    /// Define index referentiel (not unique) to be added to referentiel_certificat
        $table2 = new xmldb_table('referentiel_certificat');
        $index2 = new xmldb_index('indexreferentiel');
        $index2->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('ref_referentiel'));
    /// Launch add index
        if (!$dbman->index_exists($table2, $index2)) {
            $dbman->add_index($table2, $index2);
        }

        upgrade_mod_savepoint(true, 2011092710, 'referentiel');
   }

    if ($oldversion < 2011101611) {  // VERSION Moodle 1.9 -  '5.7.04 - 2011/10/16' Moodle 2 - '1.1.8 - 2011/10/16';
    /// Define index userpedagoref (unique) to be added to referentiel_a_user_pedagogie
        $table = new xmldb_table('referentiel_etudiant');
        $index = new xmldb_index('usernum');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('userid'));
   /// Launch drop index uniqueuser
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
    /// Change type field logo_etablissement
        $table = new xmldb_table('referentiel_etablissement');
        $field = new xmldb_field('logo_etablissement');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NULL, null, null, 'adresse_etablissement');
        /// Launch change of type for field type_task
        if ($dbman->field_exists($table,$field)) {
            $dbman->change_field_type($table, $field);
		}

        upgrade_mod_savepoint(true, 2011101611, 'referentiel');
    }

    if ($oldversion < 2011112615) {  // VERSION Moodle 1.9 N°6.1.01 VERSION Moodle 2 - '1.2.01 - 2011/12/01';
    /// Define table referentiel_repartition
        $table = new xmldb_table('referentiel_repartition');

    /// Adding fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ref_instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('ref_occurrence', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('teacherid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('code_item', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// create table
        if (!$dbman->table_exists($table)){
            $dbman->create_table($table, true, true);
        }
    /// Indexes
        $index = new xmldb_index('indexinstance');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('ref_instance'));
    /// Add index
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    /// Indexes
        $index = new xmldb_index('indexoccurrence');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('ref_occurrence'));
    /// Add index
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2011112615, 'referentiel');
    }


    if ($oldversion < 2012032813) {  // VERSION Moodle 2.1   2.1.06 - 2012/03/28;

    	/// Add field table referentiel_domaine
        $table = new xmldb_table('referentiel_domaine');
        $field = new xmldb_field('type_domaine');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '0', 'nb_competences');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('seuil_domaine');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '0.0', 'type_domaine');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    	/// Add field table referentiel_competence
        $table = new xmldb_table('referentiel_competence');
        $field = new xmldb_field('type_competence');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '0', 'nb_item_competences');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('seuil_competence');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '0.0', 'type_competence');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


 	   /// Modify field default config table referentiel
        $table = new xmldb_table('referentiel_item_competence');
    	/// Redefinir type field
        $field = new xmldb_field('type_item');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '0', 'ref_competence');
        /// Launch change of default for field
        if ($dbman->field_exists($table, $field)) {
            if ($items = $DB->get_recordset_sql("SELECT id, type_item FROM {referentiel_item_competence} ORDER BY id, type_item ", null)){
                foreach ($items as $item){
                    if ($item->type_item!=''){
                        $DB->set_field('referentiel_item_competence', 'type_item', '1', array('id' => $item->id));
                    }
                    else{
                        $DB->set_field('referentiel_item_competence', 'type_item', '0', array('id' => $item->id));
                    }
                }
            }
            // $dbman->change_field_default($table, $field, true, true);
            $dbman->change_field_type($table, $field);
        }



    /// Define table referentiel_protocol
        $table = new xmldb_table('referentiel_protocol');
    /// Adding fields to table referentiel_protocol
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ref_occurrence', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');
        $table->add_field('seuil_referentiel', XMLDB_TYPE_FLOAT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('l_domaines_oblig', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('l_seuils_domaines', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('l_competences_oblig', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('l_seuils_competences', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('l_items_oblig', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('commentaire', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('actif', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table
        $table->add_index('indexoccurrence', XMLDB_INDEX_UNIQUE, array('ref_occurrence'));

    /// create table for
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        /// initialiser les tables
        $table2 = new xmldb_table('referentiel_referentiel');

        /// Silently update
        if ($dbman->table_exists($table) && $dbman->table_exists($table2)) {
            if ($occurrences = $DB->get_recordset_sql("SELECT id, seuil_certificat, liste_codes_competence FROM {referentiel_referentiel} ORDER BY id", null)){
                foreach ($occurrences as $occurrence){
                        if (!empty($occurrence) && !empty($occurrence->id)){
                            // mettre a jour le protocole
                            $protocole = new object();
                            $protocole->ref_occurrence=$occurrence->id;
                            $protocole->seuil_referentiel=$occurrence->seuil_certificat;
                            // initialiser les items obligatoires à 0
                            $liste_items_obligatoires='';
                            if (!empty($occurrence->liste_codes_competence)){
                                $t_codes_items=explode("/", $occurrence->liste_codes_competence);
			                    while (list($key, $code) = each($t_codes_items)) {
				                    //echo "$key => code\n";
				                    if (!empty($code)){
                                        $liste_items_obligatoires.= $code.':0/';
                                    }
                                }
                            }
                            $protocole->l_items_oblig=$liste_items_obligatoires;

                            // competences
                            $liste_competences_obligatoires='';
                            $liste_competences_seuils='';

                            // domaines
                            $liste_domaines_obligatoires='';
                            $liste_domaines_seuils='';

                            if ($domaines=$DB->get_recordset_sql("SELECT id, num_domaine, code_domaine FROM {referentiel_domaine} WHERE ref_referentiel=:id ORDER BY num_domaine ", array("id"=>$occurrence->id))){
                                foreach($domaines as $domaine){
                                    $liste_comp_oblig='';
                                    $liste_comp_seuil='';
                                    if ($codes_competence=$DB->get_recordset_sql("SELECT code_competence, num_competence FROM {referentiel_competence} WHERE ref_domaine=:id ORDER BY num_competence ", array("id"=>$domaine->id))){
                                        foreach($codes_competence as $codec){
                                            $liste_comp_oblig.=$codec->code_competence.':0/';
                                            $liste_comp_seuil.=$codec->code_competence.':0.0/';
                                        }
                                        $codes_competence->close();
                                    }
                                    $liste_competences_obligatoires.=$liste_comp_oblig;
                                    $liste_competences_seuils.=$liste_comp_seuil;
                                    if (!empty($domaine->code_domaine)){
                                        $liste_domaines_obligatoires.=$domaine->code_domaine.':0/';
                                        $liste_domaines_seuils.=$domaine->code_domaine.':0.0/';
                                    }
                                }
                                $domaines->close();
                            }
                            // initialiser les competences
                            $protocole->l_competences_oblig=$liste_competences_obligatoires;
                            $protocole->l_seuils_competences=$liste_competences_seuils;

                            // initialiser les domaines
                            $protocole->l_domaines_oblig=$liste_domaines_obligatoires;
                            $protocole->l_seuils_domaines=$liste_domaines_seuils;

                            $protocole->commentaire='';
                            $protocole->timemodified=time();
                            $protocole->actif=0;   // par defaut protocole inactive
                            // creer la ligne
                            if (!$DB->get_record('referentiel_protocol', array('ref_occurrence'=> $protocole->ref_occurrence))){
                                $protocole->id=$DB->insert_record('referentiel_protocol', $protocole);
                            }
                        }
                }
                $occurrences->close();
            }
        }

        
        upgrade_mod_savepoint(true, 2012032813, 'referentiel');
    }


    /* ############################################################## */


    if ($oldversion < 2012051700) {  //  moodle 2 version 07 du 02/04/2012

    /// Verifier si un index peut être ajoute aux tables domaine, competence, referentiel

    // ADD fields minimas
        $table = new xmldb_table('referentiel_domaine');
        $field = new xmldb_field('seuil_domaine');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '0.0', 'type_domaine');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('minima_domaine');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '0', 'seuil_domaine');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
        }

        // renommer les lignes qui posent problème
        if ($dbman->table_exists($table)) {
            $sql="SELECT DISTINCT t_domaine2.id as id, t_domaine2.code_domaine as code_domaine, {referentiel_referentiel}.id as refrefid
 FROM {referentiel_referentiel},
 {referentiel_domaine} AS t_domaine1, {referentiel_domaine} AS t_domaine2
 WHERE {referentiel_referentiel}.id=t_domaine1.ref_referentiel
 AND {referentiel_referentiel}.id=t_domaine2.ref_referentiel
 AND t_domaine1.id<t_domaine2.id
 AND t_domaine1.code_domaine=t_domaine2.code_domaine
 ORDER BY t_domaine2.id ";
            if ($rs= $DB->get_records_sql($sql, NULL)){
                foreach ($rs as $res){
                    $DB->set_field("referentiel_domaine","code_domaine", $res->code_domaine."-".$res->id, array("id" => "$res->id"));
                    // Mettre a jour la table referentiel_protocol
                    $sql="SELECT id, code_domaine, type_domaine, num_domaine FROM {referentiel_domaine} WHERE ref_referentiel=:refrefid ORDER BY num_domaine ";
                    $domaines= $DB->get_records_sql($sql, array("refrefid" => $res->refrefid));
                    if ($domaines){
                        $l_domaines_oblig='';
                        $l_seuils_domaines='';
                        foreach ($domaines as $domaine){
                            if ($domaine){
                                $l_domaines_oblig.=$domaine->code_domaine.":".$domaine->type_domaine."/";
                                $l_seuils_domaines.=$domaine->code_domaine.":".$domaine->seuil_domaine."/";
                            }
                        }
                        $DB->set_field("referentiel_protocol", "l_domaines_oblig", $l_domaines_oblig, array("ref_occurrence" => $res->refrefid));
                        $DB->set_field("referentiel_protocol", "l_seuils_domaines", $l_seuils_domaines, array("ref_occurrence" => $res->refrefid));
                    }
                }
            }
        }

        // maintenant creer les indexes
    /// Indexes
        $index = new xmldb_index('codedomaineunique');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('code_domaine', 'ref_referentiel'));
    /// Launch add index
        if (!$dbman->index_exists($table, $index)){
            $dbman->add_index($table, $index);
        }

    	/// Add field table referentiel_competence
        $table = new xmldb_table('referentiel_competence');
        $field = new xmldb_field('seuil_competence');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '0.0', 'type_competence');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('minima_competence');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '0', 'seuil_competence');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // renommer les lignes qui posent problème
        if ($dbman->table_exists($table)) {
            $sql="SELECT DISTINCT t_competence2.id as id, t_competence2.code_competence as code_competence, t_competence2.ref_domaine as ref_domaine, t_domaine.ref_referentiel as refrefid
 FROM {referentiel_referentiel},
 {referentiel_domaine} AS t_domaine,
 {referentiel_competence} AS t_competence1,
 {referentiel_competence} AS t_competence2
 WHERE {referentiel_referentiel}.id=t_domaine.ref_referentiel
 AND t_domaine.id=t_competence1.ref_domaine
 AND t_domaine.id=t_competence2.ref_domaine
 AND t_competence1.id<t_competence2.id
 AND t_competence1.code_competence=t_competence2.code_competence
 ORDER BY t_competence2.id ";
            if ($rs= $DB->get_records_sql($sql, NULL)){
                foreach ($rs as $res){
                    $DB->set_field("referentiel_competence","code_competence", $res->code_competence."-".$res->id, array("id" => "$res->id"));

                    // Mettre a jour la table referentiel_protocol
                    $sql="SELECT id, code_competence, type_competence, num_competence FROM {referentiel_competence} WHERE ref_domaine=:ref_domaine ORDER BY ref_domaine, num_competence ";
                    $competences= $DB->get_records_sql($sql, array("ref_domaine" => $res->ref_domaine));
                    if ($competences){
                        $l_competences_oblig='';
                        $l_seuils_competences='';
                        foreach ($competences as $competence){
                            if ($competence){
                                $l_competences_oblig.=$competence->code_competence.":".$competence>type_competence."/";
                                $l_seuils_competences.=$competence->code_competence.":".$competence->seuil_competence."/";
                            }
                        }
                        $DB->set_field("referentiel_protocol", "l_competences_oblig", $l_competences_oblig, array("ref_occurrence" => $res->refrefid));
                        $DB->set_field("referentiel_protocol", "l_seuils_competences", $l_seuils_competences, array("ref_occurrence" => $res->refrefid));
                    }
                }
            }
        }
        // maintenant creer les indexes
    /// Indexes
        $index = new xmldb_index('codecompetenceunique');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('code_competence', 'ref_domaine'));
    /// Launch add index
        if (!$dbman->index_exists($table, $index)){
            $dbman->add_index($table, $index);
        }

        // Table Item
        $table = new xmldb_table('referentiel_item_competence');
        // renommer les lignes qui posent problème
        if ($dbman->table_exists($table)) {
            $sql="SELECT DISTINCT t_item2.id as id, t_item2.code_item as code_item, {$CFG->prefix}referentiel_referentiel.id as refrefid
 FROM {referentiel_referentiel},
 {referentiel_item_competence} AS t_item1,
 {referentiel_item_competence} AS t_item2
 WHERE {referentiel_referentiel}.id=t_item1.ref_referentiel
 AND {referentiel_referentiel}.id=t_item2.ref_referentiel
 AND t_item1.id<t_item2.id
 AND t_item1.code_item=t_item2.code_item
 ORDER BY t_item2.id ";
            if ($rs= $DB->get_records_sql($sql, NULL)){
                foreach ($rs as $res){
                    $DB->set_field("referentiel_item_competence","code_item", $res->code_item."-".$res->id, array("id" => "$res->id"));

                    // Mettre a jour la table referentiel_referentiel  et refrentiel_protocol
                    $sql="SELECT id, code_item, type_item, ref_competence, num_item FROM {referentiel_item_competence} WHERE ref_referentiel=:refrefid ORDER BY ref_competence, num_item ";
                    $items= $DB->get_records_sql($sql, array("refrefid" => $res->refrefid));
                    if ($items){
                        $l_codes='';
                        $l_items_oblig='';
                        foreach ($items as $item){
                            if ($item){
                                $l_codes.=$item->code_item."/";
                                $l_items_oblig.=$item->code_item.":".$item->type_item."/";
                            }
                        }
                        
                        $DB->set_field("referentiel_referentiel", "liste_codes_competence", $l_codes, array("id" => $res->refrefid));
                        $DB->set_field("referentiel_protocol", "l_items_oblig", $l_items_oblig, array("ref_occurrence" => $res->refrefid));
                    }
                }
            }
        }
        // maintenant creer les indexes
        /// Indexes
        $index = new xmldb_index('codeitemunique');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('code_item', 'ref_competence'));
    /// Launch add index
        if (!$dbman->index_exists($table, $index)){
            $dbman->add_index($table, $index);
        }

        ///// NOUVEAUX CHAMPS minima
        /// Add field table referentiel_referentiel
        $table = new xmldb_table('referentiel_referentiel');
        $field = new xmldb_field('minima_certificat');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1', 'seuil_certificat');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    	/// Add field table referentiel_protocol
        $table = new xmldb_table('referentiel_protocol');
        $field = new xmldb_field('minima_referentiel');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1', 'seuil_referentiel');
    /// Launch add field
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        /// Add field
        $maj1=false;
        $field1 = new xmldb_field('l_minimas_domaines');
        $field1->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'l_seuils_domaines');
    /// Launch add field
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
            $maj1=true;
        }

        $maj2=false;
        $field2 = new xmldb_field('l_minimas_competences');
        $field2->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'l_seuils_competences');
    /// Launch add field
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
            $maj2=true;
        }
        if ($maj1 && $maj2){
            /// initialiser les tables
            $table2 = new xmldb_table('referentiel_referentiel');

            /// Silently update
            if ($dbman->table_exists($table) && $dbman->table_exists($table2)) {
                $sql="SELECT id FROM {referentiel_referentiel} ORDER BY id";
                if ($occurrences = $DB->get_records_sql($sql, NULL)){
                    foreach ($occurrences as $occurrence){
                        if (!empty($occurrence) && !empty($occurrence->id)){
                            // initialiser les minimas
                            $liste_minimas_domaines='';
                            $liste_minimas_competences='';
                            $domaines=$DB->get_records_sql("SELECT id, code_domaine, num_domaine FROM {referentiel_domaine} WHERE ref_referentiel=:refrefid ORDER BY num_domaine ", array("refrefid" => $occurrence->id));
                            if (!empty($domaines)){
                                foreach($domaines as $domaine){
                                    $liste_comp_minimas='';
                                    $codes_competence=$DB->get_records_sql("SELECT id, code_competence, num_competence  FROM {referentiel_competence} WHERE ref_domaine=:ref_domaine ORDER BY num_competence ", array("ref_domaine" => $domaine->id));
                                    if (!empty($codes_competence)){
                                        foreach($codes_competence as $codec){
                                            $liste_comp_minimas.=$codec->code_competence.':0/';
                                        }
                                    }
                                    $liste_minimas_competences.=$liste_comp_minimas;
                                    if (!empty($domaine->code_domaine)){
                                        $liste_minimas_domaines.=$domaine->code_domaine.':0/';
                                    }
                                }
                            }
                            // creer la ligne
                            $DB->set_field('referentiel_protocol', 'l_minimas_domaines', $liste_minimas_domaines, array("ref_occurrence" => $occurrence->id));
                            $DB->set_field('referentiel_protocol', 'l_minimas_competences', $liste_minimas_competences, array("ref_occurrence" => $occurrence->id));
                        }
                    }
                }
            }
        }
        
        upgrade_mod_savepoint(true, 2012051700, 'referentiel');
    }

    if ($oldversion < 2012060200) {
   /// Define new fields label_xxx to be added to referentiel_referentiel
        $table = new xmldb_table('referentiel_referentiel');
        $field = new xmldb_field('label_domaine');
        $field->set_attributes(XMLDB_TYPE_CHAR, '80', null, null, null, null, 'config_impression');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('label_competence');
        $field->set_attributes(XMLDB_TYPE_CHAR, '80', null, null, null, null, 'label_domaine');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('label_item');
        $field->set_attributes(XMLDB_TYPE_CHAR, '80', null, null, null, null, 'label_competence');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2012060200, 'referentiel');
    }

    if ($oldversion < 2012071300) {
        /// Modify referentiel_etudiant
        $table = new xmldb_table('referentiel_etudiant');
        if ($dbman->table_exists($table)) {
            //
            $sql="UPDATE {referentiel_etudiant}  SET `num_etudiant`='l_inconnu'
 WHERE (`num_etudiant`=? OR `num_etudiant`=?  OR `num_etudiant`=? OR `num_etudiant`=?) ";
            $DB->execute($sql, array('INCONNU', 'UNKNOWN', 'НЕВІДОМЕ', 'DESCONOCIDO'));
            $sql="UPDATE {referentiel_etudiant}  SET `ddn_etudiant`='l_inconnu'
 WHERE (`ddn_etudiant`=? OR `ddn_etudiant`=?  OR `ddn_etudiant`=?  OR `ddn_etudiant`=?) ";
            $DB->execute($sql, array('INCONNU', 'UNKNOWN', 'НЕВІДОМЕ', 'DESCONOCIDO'));
            $sql="UPDATE {referentiel_etudiant}  SET `lieu_naissance`='l_inconnu'
 WHERE (`lieu_naissance`=? OR `lieu_naissance`=?  OR `lieu_naissance`=? OR `lieu_naissance`=?) ";
            $DB->execute($sql, array('INCONNU', 'UNKNOWN', 'НЕВІДОМЕ', 'DESCONOCIDO'));
            $sql="UPDATE {referentiel_etudiant}  SET `departement_naissance`='l_inconnu'
 WHERE (`departement_naissance`=? OR `departement_naissance`=?   OR `departement_naissance`=? OR `departement_naissance`=?) ";
            $DB->execute($sql, array('INCONNU', 'UNKNOWN', 'НЕВІДОМЕ', 'DESCONOCIDO'));
            $sql="UPDATE {referentiel_etudiant}  SET `adresse_etudiant`='l_inconnu'
 WHERE (`adresse_etudiant`=? OR `adresse_etudiant`=?  OR `adresse_etudiant`=? OR `adresse_etudiant`=?) ";
            $DB->execute($sql, array('INCONNU', 'UNKNOWN', 'НЕВІДОМЕ', 'DESCONOCIDO'));
        }
        /// Modify referentiel_etablissement
        $table = new xmldb_table('referentiel_etablissement');
        if ($dbman->table_exists($table)) {
            //
            $sql="UPDATE {referentiel_etablissement}  SET `num_etablissement`='l_inconnu'
 WHERE (`num_etablissement`=? OR `num_etablissement`=?  OR `num_etablissement`=? OR `num_etablissement`=?) ";
            $DB->execute($sql, array('INCONNU', 'UNKNOWN', 'НЕВІДОМЕ', 'DESCONOCIDO'));
            $sql="UPDATE {referentiel_etablissement}  SET `nom_etablissement`='l_inconnu'
 WHERE (`nom_etablissement`=? OR `nom_etablissement`=?  OR `nom_etablissement`=? OR `nom_etablissement`=?) ";
            $DB->execute($sql, array('INCONNU', 'UNKNOWN', 'НЕВІДОМЕ', 'DESCONOCIDO'));
            $sql="UPDATE {referentiel_etablissement}  SET `adresse_etablissement`='l_inconnu'
 WHERE (`adresse_etablissement`=? OR `adresse_etablissement`=?  OR `adresse_etablissement`=? OR `adresse_etablissement`=?) ";
            $DB->execute($sql, array('INCONNU', 'UNKNOWN', 'НЕВІДОМЕ', 'DESCONOCIDO'));
        }

        upgrade_mod_savepoint(true, 2012071300, 'referentiel');
    }
    
    if ($oldversion < 2012060200) {
   /// Define new fields label_xxx to be added to referentiel_referentiel
        $table = new xmldb_table('referentiel_referentiel');
        $field = new xmldb_field('label_domaine');
        $field->set_attributes(XMLDB_TYPE_CHAR, '80', null, null, null, null, 'config_impression');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('label_competence');
        $field->set_attributes(XMLDB_TYPE_CHAR, '80', null, null, null, null, 'label_domaine');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('label_item');
        $field->set_attributes(XMLDB_TYPE_CHAR, '80', null, null, null, null, 'label_competence');
    /// Launch add field referentiel_referentiel
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2012060200, 'referentiel');
    }

    if ($oldversion < 2012101000) {
        /// Modify referentiel_referentiel
        $table = new xmldb_table('referentiel_certificat');
        if ($dbman->table_exists($table)) {
            // set each valide field to 0
            $sql="UPDATE {referentiel_certificat}  SET `valide`=0 ";
            $DB->execute($sql, array());
        }

        upgrade_mod_savepoint(true, 2012101000, 'referentiel');
    }
    if ($oldversion < 2013020200){
        /// Modify referentiel_document
        /// <FIELD NAME="timestamp" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" DEFAULT="0" SEQUENCE="false" PREVIOUS="etiquette_consigne" />
        $table = new xmldb_table('referentiel_document');
        $field = new xmldb_field('timestamp');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'etiquette_document');
        /// Launch add field referentiel_document
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        $table = new xmldb_table('referentiel_consigne');
        $field = new xmldb_field('timestamp');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'etiquette_consigne');
        /// Launch add field referentiel_document
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2013020200, 'referentiel');
    }
    
    if ($oldversion < 2013021700){
        /// Modify referentiel_etudiant
        ///<FIELD NAME="num_etudiant" TYPE="char" LENGTH="255" NOTNULL="false" SEQUENCE="false" PREVIOUS="id" NEXT="ddn_etudiant"/>
        $table = new xmldb_table('referentiel_etudiant');
        $field = new xmldb_field('num_etudiant');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        /// Launch change of default for field
        // $dbman->change_field_default($table, $field, true, true);
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint(true, 2013021700, 'referentiel');
    }


    if ($oldversion < 2013062700){
        /// Create new table  referentiel_scale
        $table = new xmldb_table('referentiel_scale');
        if (!$dbman->table_exists($table)) {
        /// Adding fields to table referentiel_task
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
            $table->add_field('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $table->add_field('scale', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
            $table->add_field('threshold', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('maxscale', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('description', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
            $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('icons', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
            $table->add_field('labels', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

            /// Launch create table
            $dbman->create_table($table, true, true);
        }
        
        /// Create new table  referentiel_a_scale_occurrence
        $table = new xmldb_table('referentiel_a_scale_ref');
        if (!$dbman->table_exists($table)) {
        /// Adding fields 
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
            $table->add_field('refscaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('refrefid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

            /// Launch create table
            $dbman->create_table($table, true, true);
        }
        
        /// Create new table referentiel_activite_scale
        $table = new xmldb_table('referentiel_activite_scale');
        if (!$dbman->table_exists($table)) {
            /// Adding fields 
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
            $table->add_field('refscaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('activiteid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('competences_bareme', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);

            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            /// Adding index to table referentiel_notification_queue
            $table->add_index('indscale',XMLDB_INDEX_NOTUNIQUE, array('refscaleid'));
            $table->add_index('indactivite',XMLDB_INDEX_NOTUNIQUE, array('activiteid'));
            /// Launch create table
            $dbman->create_table($table, true, true);
        }

        upgrade_mod_savepoint(true, 2013062700, 'referentiel');
    }

        if ($oldversion < 2013100400) { // VERSION Moodle 2.4
	   /// Modify field default config table referentiel
        $table = new xmldb_table('referentiel');
        $field = new xmldb_field('config');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;', 'visible');
        /// Launch change of type for field type_activite
        $dbman->change_field_default($table, $field);

	   /// Modify field default config table referentiel_referentiel
        $table1 = new xmldb_table('referentiel_referentiel');
        $field1 = new xmldb_field('config');
        $field1->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;', 'logo_referentiel');
        /// Launch change of type for field type_activite
        $dbman->change_field_default($table1, $field1);

        /// mise a jour des champs existants
        $table2 = new xmldb_table('referentiel');
          /// Silenty update
        if ($dbman->table_exists($table2)){
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel}", null);
            foreach ($rs as $res){
                $res->config=$res->config."hierarchy:0;";
                $DB->update_record('referentiel', $res);
            }
            $rs->close();
        }

        $table3 = new xmldb_table('referentiel_referentiel');
        /// Silently update
        if ($dbman->table_exists($table3)){
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel_referentiel}", null);
            foreach ($rs as $res){
                $res->config=$res->config."hierarchy:0;";
                $DB->update_record('referentiel_referentiel', $res);
            }
            $rs->close();
        }

        upgrade_mod_savepoint(true, 2013100400, 'referentiel');
    }

    if ($oldversion < 2013112400) { // VERSION Moodle 2.5
	   /// Modify field default config table referentiel
        $table = new xmldb_table('referentiel');
        $field = new xmldb_field('config');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;cfcertif:0;certif:1;', 'visible');
        /// Launch change of type for field type_activite
        $dbman->change_field_default($table, $field);

	   /// Modify field default config table referentiel_referentiel
        $table1 = new xmldb_table('referentiel_referentiel');
        $field1 = new xmldb_field('config');
        $field1->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;cfcertif:0;certif:1;', 'logo_referentiel');
        /// Launch change of type for field type_activite
        $dbman->change_field_default($table1, $field1);

        /// mise a jour des champs existants
        $table2 = new xmldb_table('referentiel');
          /// Silenty update
        if ($dbman->table_exists($table2)){
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel}", null);
            foreach ($rs as $res){
                $res->config=$res->config."cfcertif:0;certif:1;";
                $DB->update_record('referentiel', $res);
            }
            $rs->close();
        }

        $table3 = new xmldb_table('referentiel_referentiel');
        /// Silently update
        if ($dbman->table_exists($table3)){
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel_referentiel}", null);
            foreach ($rs as $res){
                $res->config=$res->config."cfcertif:0;certif:1;";
                $DB->update_record('referentiel_referentiel', $res);
            }
            $rs->close();
        }

        upgrade_mod_savepoint(true, 2013112400, 'referentiel');
    }

    if ($oldversion < 2014022802) { // VERSION Moodle 2.5 that uses block referentiel
	   /// Modify errorneous field config table referentiel
        $table = new xmldb_table('referentiel');
        if ($dbman->table_exists($table)) {
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel}", null);
            foreach ($rs as $res){
                if (strpos($res->config, 'hierachy')!==false){
					$res->config=str_replace('hierachy','hierarchy',$res->config);
                    $DB->update_record('referentiel', $res);
				}
            }
            $rs->close();
        }
	   /// Modify errorneous field config table referentiel_referentiel
        $table = new xmldb_table('referentiel_referentiel');
        if ($dbman->table_exists($table)) {
            $rs = $DB->get_recordset_sql("SELECT * FROM {referentiel_referentiel}", null);
            foreach ($rs as $res){
                if (strpos($res->config, 'hierachy')!==false){
                    $res->config=str_replace('hierachy','hierarchy',$res->config);
                    $DB->update_record('referentiel_referentiel', $res);
				}
            }
            $rs->close();
        }
        upgrade_mod_savepoint(true, 2014022802, 'referentiel');
    }

	return true;
}



?>

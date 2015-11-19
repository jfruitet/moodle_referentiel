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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage forum
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Forum conversion handler
 */
class moodle1_mod_referentiel_handler extends moodle1_mod_handler {

    /** @var array in-memory cache for the course module information for the current referentiel  */
    protected $cminfo = null;
   
    /** @var int cmid */
    protected $moduleid = null;
	
	/** @var moodle1_file_manager */
    protected $fileman = null;

    /** @var moodle1_inforef_manager */
    protected $inforefman = null;



    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/ASSIGNMENT does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path('referentiel', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL',
                array(
 /*
 // rien à renommer
                    'renamefields' => array(
                        'text' => 'intro',
                        'format' => 'introformat',
                    ),
 */
                    'newfields' => array(
                        'intro' => 0,
                        'introformat' => 0,
                    ),
 /*
 // rien à jeter
                    'dropfields' => array(
                        'modtype'
                    ),
 */
                )
            ),

            //@todo process user data
            new convert_path('occurrence', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE'),

            new convert_path('protocole', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/PROTOCOLE'),

            new convert_path('domaines', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/DOMAINES'),
            new convert_path('domaine', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/DOMAINES/DOMAINE'),
            new convert_path('competences', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/DOMAINES/DOMAINE/COMPETENCES'),
            new convert_path('competence', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/DOMAINES/DOMAINE/COMPETENCES/COMPETENCE'),
            new convert_path('items', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/DOMAINES/DOMAINE/COMPETENCES/COMPETENCE/ITEMS'),
            new convert_path('item', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/DOMAINES/DOMAINE/COMPETENCES/COMPETENCE/ITEMS/ITEM'),

            new convert_path('certificats', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/CERTIFICATS'),
            new convert_path('certificat', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/CERTIFICATS/CERTIFICAT'),

            new convert_path('tasks', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/TASKS'),
            new convert_path('task', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/TASKS/TASK'),
            new convert_path('consignes', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/TASKS/TASK/CONSIGNES'),
            new convert_path('consigne', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/TASKS/TASK/CONSIGNES/CONSIGNE'),

            new convert_path('activites', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACTIVITES'),
            new convert_path('activite', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACTIVITES/ACTIVITE'),
            new convert_path('documents', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACTIVITES/ACTIVITE/DOCUMENTS'),
            new convert_path('document', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACTIVITES/ACTIVITE/DOCUMENTS/DOCUMENT'),
            new convert_path('users_tasks', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACTIVITES/ACTIVITE/USERS_TASKS'),
            new convert_path('user_task', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACTIVITES/ACTIVITE/USERS_TASKS/USER_TASK'),

            new convert_path('accompagnements', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACCOMPAGNEMENTS'),
            new convert_path('accompagnement', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACCOMPAGNEMENTS/ACCOMPAGNEMENT'),

            new convert_path('etablissements', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ETABLISSEMENTS'),
            new convert_path('etablissement', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ETABLISSEMENTS/ETABLISSEMENT'),

            new convert_path('etudiants', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ETUDIANTS'),
            new convert_path('etudiant', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ETUDIANTS/ETUDIANT'),

            new convert_path('pedagogies', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/PEDAGOGIES'),
            new convert_path('a_user_pedago', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/PEDAGOGIES/A_USER_PEDAGO'),
            new convert_path('pedagogie_record', '/MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/PEDAGOGIES/A_USER_PEDAGO/PEDAGOGIE_RECORD'),

        );

    }

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL data
     */
    public function process_referentiel($data, $raw) {
        // get the course module id and context id
        $instanceid     = $data['id'];
        $this->cminfo = $this->get_cminfo($instanceid);
        $this->moduleid = $this->cminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // get a fresh new inforef manager for this instance
        $this->inforefman = $this->converter->get_inforef_manager('activity', $moduleid);
 		        
        // get a fresh new file manager for this instance
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_referentiel');

        // convert course files embedded into the intro
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['intro'], $this->fileman);

        // start writing referentiel.xml
        $this->open_xml_writer("activities/referentiel_{$this->moduleid}/referentiel.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $this->moduleid,
            'modulename' => 'referentiel', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('referentiel', array('id' => $instanceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        /*
        echo "<br />activities/referentiel_{$this->moduleid}/referentiel.xml<br />\n";
        echo "<br />DATA -------------------------------------------------<br /><br />\n";
        print_object($data);
        echo "<br />RAW -------------------------------------------------<br /><br />\n";
        print_object($raw);

        echo "<br />XML -------------------------------------------------<br /><br />\n";
        print_object($this);
        echo "<br />\n";
        */
        return $data;
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'referentiel' path
     */
    public function on_referentiel_end() {
        // finish writing referentiel.xml

        $this->xmlwriter->end_tag('referentiel');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();
/*
        // write inforef.xml
        $this->open_xml_writer("activities/referentiel_{$this->moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
*/

        // write inforef.xml
        $this->inforefman->add_refs('file', $this->fileman->get_fileids());
        $this->open_xml_writer("activities/referentiel_{$this->moduleid}/inforef.xml");
        $this->inforefman->write_refs($this->xmlwriter);
        $this->close_xml_writer();

        // get ready for the next instance
        $this->cminfo   = null;
		$this->moduleid   = null;     
    }
    

    public function on_occurrence_start() {
        // Nothing to do
        // $this->xmlwriter->end_tag('occurence');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE
     * data available
     */

    public function process_occurrence($data) {
        // $this->write_xml('occurrence', $data, array('/referentiel/occurrence/id'));
        //print_r($data);
        //echo "<br />\n";
        $occurrenceid     = $data['id'];
        $this->xmlwriter->begin_tag('occurrence', array('id' => $occurrenceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        // do not close tag yet
        // $this->xmlwriter->end_tag('occurrence');
   }

    /**
     * This is executed when the parser reaches the closing </OCCURRENCE> element
     */
    public function on_occurrence_end() {
        $this->xmlwriter->end_tag('occurrence');
    }

// MODIF JF 2012/03/12
    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/PROTOCOLE
     * data available
     */
    public function process_protocole($data) {
        // $this->write_xml('domaine', $data, array('/protocole/id'));
        $protocoleid     = $data['id'];
        $this->xmlwriter->begin_tag('protocole', array('id' => $protocoleid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }
        // do not close tag yet
    }
    
    /**
     * This is executed when the parser reaches the closing </PROTOCOLE> element
     */
    public function on_protocole_end() {
        $this->xmlwriter->end_tag('protocole');
    }

    /**
     * This is executed when the parser reaches the <DOMAINES> opening element
     */
    public function on_domaines_start() {
        $this->xmlwriter->begin_tag('domaines');
    }

    /**
     * This is executed when the parser reaches the closing </DOMAINES> element
     */
    public function on_domaines_end() {
        $this->xmlwriter->end_tag('domaines');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/DOMAINES/DOMAINE
     * data available
     */
    public function process_domaine($data) {
        // $this->write_xml('domaine', $data, array('/domaine/id'));
        $domaineid     = $data['id'];
        $this->xmlwriter->begin_tag('domaine', array('id' => $domaineid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }
        // do not close tag yet
    }

    /**
     * This is executed when the parser reaches the closing </DOMAINE> element
     */
    public function on_domaine_end() {
        $this->xmlwriter->end_tag('domaine');
    }


    /**
     * This is executed when the parser reaches the <COMPETENCES> opening element
     */
    public function on_competences_start() {
        $this->xmlwriter->begin_tag('competences');
    }

    /**
     * This is executed when the parser reaches the closing </COMPETENCES> element
     */
    public function on_competences_end() {
        $this->xmlwriter->end_tag('competences');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/DOMAINES/DOMAINE/COMPETENCES/COMPETENCE
     * data available
     */
    public function process_competence($data) {
        //$this->write_xml('competence', $data, array('/competence/id'));
        $competenceid     = $data['id'];
        $this->xmlwriter->begin_tag('competence', array('id' => $competenceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }
        // do not close tag yet
    }

    /**
     * This is executed when the parser reaches the closing </COMPETENCE> element
     */
    public function on_competence_end() {
        $this->xmlwriter->end_tag('competence');
    }

    /**
     * This is executed when the parser reaches the <ITEM> opening element
     */
    public function on_items_start() {
        $this->xmlwriter->begin_tag('items');
    }

    /**
     * This is executed when the parser reaches the closing </ITEMS> element
     */
    public function on_items_end() {
        $this->xmlwriter->end_tag('items');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/OCCURRENCE/DOMAINES/DOMAINE/COMPETENCES/COMPETENCE/ITEMS/ITEM
     * data available
     */
    public function process_item($data) {
        $this->write_xml('item', $data, array('/item/id'));
    }

    /**
     * This is executed when the parser reaches the <CERTIFICATS> opening element
     */
    public function on_certificats_start() {
        $this->xmlwriter->begin_tag('certificats');
    }

        /**
     * This is executed when the parser reaches the closing </CERTIFICATS> element
     */
    public function on_certificats_end() {
        $this->xmlwriter->end_tag('certificats');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/CERTIFICATS/CERTIFICAT
     * data available
     */
    public function process_certificat($data) {
        $this->write_xml('certificat', $data, array('/certificat/id'));
    }

    /**
     * This is executed when the parser reaches the <taskS> opening element
     */
    public function on_tasks_start() {
        $this->xmlwriter->begin_tag('tasks');
    }

    /**
     * This is executed when the parser reaches the closing </taskS> element
     */
    public function on_tasks_end() {
        $this->xmlwriter->end_tag('tasks');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/TASKS/TASK/
     * data available
     */
    public function process_task($data) {
        // $this->write_xml('task', $data, array('/task/id'));
        $taskid     = $data['id'];
        $this->xmlwriter->begin_tag('task', array('id' => $taskid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }
        // do not close tag yet
    }

    /**
     * This is executed when the parser reaches the closing </task> element
     */
    public function on_task_end() {
        $this->xmlwriter->end_tag('task');
    }



    /**
     * This is executed when the parser reaches the <consigneS> opening element
     */
    public function on_consignes_start() {
        $this->xmlwriter->begin_tag('consignes');
    }

        /**
     * This is executed when the parser reaches the closing </consigneS> element
     */
    public function on_consignes_end() {
        $this->xmlwriter->end_tag('consignes');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/TASKS/TASK/CONSIGNES/CONSIGNE
     * data available
     */
    public function process_consigne($data) {
        $this->write_xml('consigne', $data, array('/consigne/id'));

        $this->fileman->filearea = 'consigne';
        $this->fileman->itemid   = $data['id'];
        $this->fileman->userid   = $data['userid'];
        $data['consigne'] = moodle1_converter::migrate_referenced_files($data['consigne'], $this->fileman);        		             
        
    }

    /**
     * This is executed when the parser reaches the <accompagnementS> opening element
     */
    public function on_accompagnements_start() {
        $this->xmlwriter->begin_tag('accompagnements');
    }

        /**
     * This is executed when the parser reaches the closing </accompagnementS> element
     */
    public function on_accompagnements_end() {
        $this->xmlwriter->end_tag('accompagnements');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACCOMPAGNEMENT/ACCOMPAGNEMENT
     * data available
     */
    public function process_accompagnement($data) {
        $this->write_xml('accompagnement', $data, array('/accompagnement/id'));
    }

    /**
     * This is executed when the parser reaches the <activiteS> opening element
     */
    public function on_activites_start() {
        $this->xmlwriter->begin_tag('activites');
    }

    /**
     * This is executed when the parser reaches the closing </activiteS> element
     */
    public function on_activites_end() {
        $this->xmlwriter->end_tag('activites');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACTIVITES/ACTIVITE
     * data available
     */
    public function process_activite($data) {
        // $this->write_xml('activite', $data, array('/activite/id'));
        $activiteid     = $data['id'];
        $this->xmlwriter->begin_tag('activite', array('id' => $activiteid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }
        // do not close tag yet
    }

    /**
     * This is executed when the parser reaches the closing </activite> element
     */
    public function on_activite_end() {
        $this->xmlwriter->end_tag('activite');
    }



    /**
     * This is executed when the parser reaches the <documentS> opening element
     */
    public function on_documents_start() {
        $this->xmlwriter->begin_tag('documents');
    }

    /**
     * This is executed when the parser reaches the closing </documentS> element
     */
    public function on_documents_end() {
        $this->xmlwriter->end_tag('documents');
        
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACTIVITES/ACTIVITE/DOCUMENTS/DOCUMENT
     * data available
     */
    public function process_document($data) {
        $this->write_xml('document', $data, array('/document/id'));
        
        $this->fileman->filearea = 'document';
        $this->fileman->itemid   = $data['id'];
        $this->fileman->userid   = $data['userid'];
        $data['document'] = moodle1_converter::migrate_referenced_files($data['document'], $this->fileman);        
        
    }

    /**
     * This is executed when the parser reaches the <USERS_TASKS> opening element
     */
    public function on_users_tasks_start() {
        $this->xmlwriter->begin_tag('users_tasks');
    }

    /**
     * This is executed when the parser reaches the closing </USERS_TASKS> element
     */
    public function on_users_tasks_end() {
        $this->xmlwriter->end_tag('users_tasks');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ACTIVITES/ACTIVITE/USERS_TASKS/USER_TASK
     * data available
     */
    public function process_user_task($data) {
        $this->write_xml('user_task', $data, array('/user_task/id'));
    }




    /**
     * This is executed when the parser reaches the <etablissementS> opening element
     */
    public function on_etablissements_start() {
        $this->xmlwriter->begin_tag('etablissements');
    }

    /**
     * This is executed when the parser reaches the closing </etablissementS> element
     */
    public function on_etablissements_end() {
        $this->xmlwriter->end_tag('etablissements');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ETABLISSEMENTS/ETABLISSEMENT
     * data available
     */
    public function process_etablissement($data) {
        // $this->write_xml('etablissement', $data, array('/etablissement/id'));
        $etablissementid     = $data['id'];
        $this->xmlwriter->begin_tag('etablissement', array('id' => $etablissementid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }
        // do not close tag yet
    }

    /**
     * This is executed when the parser reaches the closing </etablissement> element
     */
    public function on_etablissement_end() {
        $this->xmlwriter->end_tag('etablissement');
    }


    /**
     * This is executed when the parser reaches the <etudiantS> opening element
     */
    public function on_etudiants_start() {
        $this->xmlwriter->begin_tag('etudiants');
    }

    /**
     * This is executed when the parser reaches the closing </etudiantS> element
     */
    public function on_etudiants_end() {
        $this->xmlwriter->end_tag('etudiants');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/ETABLISSEMENTS/ETABLISSEMENT/etudiantS/etudiant
     * data available
     */
    public function process_etudiant($data) {
        $this->write_xml('etudiant', $data, array('/etudiant/id'));
    }

    /**
     * This is executed when the parser reaches the <pedagogieS> opening element
     */
    public function on_pedagogies_start() {
        $this->xmlwriter->begin_tag('pedagogies');
    }

    /**
     * This is executed when the parser reaches the closing </PEDAGOGIES> element
     */
    public function on_pedagogies_end() {
        $this->xmlwriter->end_tag('pedagogies');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/PEDAGOGIES/A_USER_PEDAGO
     * data available
     */
    public function process_a_user_pedago($data) {
        $a_pedagogieid     = $data['id'];
        $this->xmlwriter->begin_tag('a_user_pedago', array('id' => $a_pedagogieid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }
        // do not close tag yet
    }

    /**
     * This is executed when the parser reaches the closing </A_USER_PEDAGO> element
     */
    public function on_a_user_pedago_end() {
        $this->xmlwriter->end_tag('a_user_pedago');
    }


    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/REFERENTIEL/PEDAGOGIES/A_USER_PEDAGO/PEDAGOGIE_RECORD
     * data available
     */
    public function process_pedagogie_record($data) {
        $this->write_xml('pedagogie_record', $data, array('/pedagogie_record/id'));
    }

    /**
     * Provides access to the instance's inforef manager
     *
     * @return moodle1_inforef_manager
     */
    public function get_inforef_manager() {
        return $this->inforefman;
    }


}

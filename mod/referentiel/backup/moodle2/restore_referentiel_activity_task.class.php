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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/referentiel/backup/moodle2/restore_referentiel_stepslib.php'); // Because it exists (must)

/**
 * referentiel restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_referentiel_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Referentiel has one structure step
        $this->add_step(new restore_referentiel_activity_structure_step('referentiel_structure', 'referentiel.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('referentiel', array('name', 'description_instance', 'intro'), 'referentiel');
        $contents[] = new restore_decode_content('referentiel_document', array('url_document'), 'referentiel_document');
        $contents[] = new restore_decode_content('referentiel_consigne', array('url_consigne'), 'referentiel_consigne');
        $contents[] = new restore_decode_content('referentiel_referentiel', array('url_referentiel'), 'referentiel_referentiel');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('REFERENTIELVIEWBYD', '/mod/referentiel/view.php?d=$1', 'referentiel');
        $rules[] = new restore_decode_rule('REFERENTIELVIEWBYID', '/mod/referentiel/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('REFERENTIELINDEX', '/mod/referentiel/index.php?id=$1', 'course');

        $rules[] = new restore_decode_rule('REFERENTIELACTIVITED', '/mod/referentiel/activite.php?d=$1', 'referentiel');
        $rules[] = new restore_decode_rule('REFERENTIELACTIVITEID', '/mod/referentiel/activite.php?id=$1', 'course_module');

        $rules[] = new restore_decode_rule('REFERENTIELACCOMPD', '/mod/referentiel/accompagnement.php?d=$1', 'referentiel');
        $rules[] = new restore_decode_rule('REFERENTIELACCOMPID', '/mod/referentiel/accompagnement.php?id=$1', 'course_module');

        $rules[] = new restore_decode_rule('REFERENTIELTASKD', '/mod/referentiel/task.php?d=$1', 'referentiel');
        $rules[] = new restore_decode_rule('REFERENTIELTASKID', '/mod/referentiel/task.php?id=$1', 'course_module');

        $rules[] = new restore_decode_rule('REFERENTIELCERTIFICATD', '/mod/referentiel/certificat.php?d=$1', 'referentiel');
        $rules[] = new restore_decode_rule('REFERENTIELCERTIFICATID', '/mod/referentiel/certificat.php?id=$1', 'course_module');

        $rules[] = new restore_decode_rule('REFERENTIELETUDIANTD', '/mod/referentiel/etudiant.php?d=$1', 'referentiel');
        $rules[] = new restore_decode_rule('REFERENTIELETUDIANTID', '/mod/referentiel/etudiant.php?id=$1', 'course_module');

        $rules[] = new restore_decode_rule('REFERENTIELETABD', '/mod/referentiel/etablissement.php?d=$1', 'referentiel');
        $rules[] = new restore_decode_rule('REFERENTIELETABID', '/mod/referentiel/etablissement.php?id=$1', 'course_module');

        $rules[] = new restore_decode_rule('REFERENTIELPEDAGOD', '/mod/referentiel/pedagogie.php?d=$1', 'referentiel');
        $rules[] = new restore_decode_rule('REFERENTIELPEDAGOID', '/mod/referentiel/pedagogie.php?id=$1', 'course_module');

        $rules[] = new restore_decode_rule('REFERENTIELSCALED', '/mod/referentiel/bareme.php?d=$1', 'referentiel');
        $rules[] = new restore_decode_rule('REFERENTIELSCALEID', '/mod/referentiel/bareme.php?id=$1', 'course_module');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * referentiel logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('referentiel', 'add', 'add.php?id={course_module}', '{referentiel}');
        $rules[] = new restore_log_rule('referentiel', 'update', 'edit.php?id={course_module}', '{referentiel}');
        $rules[] = new restore_log_rule('referentiel', 'view', 'view.php?id={course_module}', '{referentiel}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        // Fix old wrong uses (missing extension)
        $rules[] = new restore_log_rule('referentiel', 'view all', 'index?id={course}', null,
                                        null, null, 'index.php?id={course}');
        $rules[] = new restore_log_rule('referentiel', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}

?>
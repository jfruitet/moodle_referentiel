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

require_once($CFG->dirroot . '/mod/referentiel/backup/moodle2/backup_referentiel_stepslib.php'); // Because it exists (must)
require_once($CFG->dirroot . '/mod/referentiel/backup/moodle2/backup_referentiel_settingslib.php'); // Because it exists (optional)

/**
 * choice backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_referentiel_activity_task extends backup_activity_task {

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
        // Choice only has one structure step
        $this->add_step(new backup_referentiel_activity_structure_step('referentiel structure', 'referentiel.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of referentiels
        $search="/(".$base."\/mod\/referentiel\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@REFERENTIELINDEX*$2@$', $content);

        // Link to referentiels view by moduleid
        $search="/(".$base."\/mod\/referentiel\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@REFERENTIELVIEWBYID*$2@$', $content);

        /// Link to database view by referentielid
        $search="/(".$base."\/mod\/referentiel\/view.php\?d\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELVIEWBYD*$2@$', $content);

        /// Link to activite of the referentiel
        $search="/(".$base."\/mod\/referentiel\/activite.php\?id\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELACTIVITEID*$2@$', $content);

        $search="/(".$base."\/mod\/referentiel\/activite.php\?d\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELACTIVITED*$2@$', $content);

        /// Link to activite of the referentiel
        $search="/(".$base."\/mod\/referentiel\/accompagnement.php\?id\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELACCOMPID*$2@$', $content);

        $search="/(".$base."\/mod\/referentiel\/accompagnement.php\?d\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELACCOMPD*$2@$', $content);

        /// Link to task of the referentiel
        $search="/(".$base."\/mod\/referentiel\/task.php\?id\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELTASKID*$2@$', $content);

        $search="/(".$base."\/mod\/referentiel\/task.php\?d\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELTASKD*$2@$', $content);

        /// Link to certificate of the referentiel
        $search="/(".$base."\/mod\/referentiel\/certificat.php\?id\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELCERTIFICATID*$2@$', $content);

        $search="/(".$base."\/mod\/referentiel\/certificat.php\?d\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELCERTIFICATD*$2@$', $content);

        /// Link to etudiant of the referentiel
        $search="/(".$base."\/mod\/referentiel\/etudiant.php\?id\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELETUDIANTID*$2@$', $content);

        $search="/(".$base."\/mod\/referentiel\/etudiant.php\?d\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELETUDIANTD*$2@$', $content);

        /// Link to etudiant of the referentiel
        $search="/(".$base."\/mod\/referentiel\/etablissement.php\?id\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELETABID*$2@$', $content);

        $search="/(".$base."\/mod\/referentiel\/etablissement.php\?d\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELETABD*$2@$', $content);

        /// Link to etudiant of the referentiel
        $search="/(".$base."\/mod\/referentiel\/pedagogie.php\?id\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELPEDAGOID*$2@$', $content);

        $search="/(".$base."\/mod\/referentiel\/pedagogie.php\?d\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELPEDAGOD*$2@$', $content);

		/// Link to bareme of the referentiel
        $search="/(".$base."\/mod\/referentiel\/bareme.php\?id\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELSCALEID*$2@$', $content);
        
        $search="/(".$base."\/mod\/referentiel\/bareme.php\?d\=)([0-9]+)/";
        $content= preg_replace($search,'$@REFERENTIELSCALED*$2@$', $content);

        return $content;
    }
}

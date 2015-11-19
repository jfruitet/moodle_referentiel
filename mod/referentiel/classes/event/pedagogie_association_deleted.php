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
 * The mod_referentiel pedagogie deleted event.
 *
 * @package    mod_referentiel
 * @copyright  2014 Jean FRUITET <jean.fruitet@univ-nantes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_referentiel\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_referentiel pedagogie event class.
 *
 * @package    mod_referentiel
 * @since      Moodle 2.7
 * @copyright  2014 Jean FRUITET <jean.fruitet@univ-nantes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pedagogie_association_deleted extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'referentiel_a_user_pedagogie';
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventpedagogyauserdeleted', 'mod_referentiel');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $userdesc = '';
        if ($this->object['userid']) {
            $userdesc = " with the user '{$this->object['userid']}' in ";
        }
        $occurencedesc = '';
        if ($this->object['occurrenceid']) {
            $occurencedesc = " for the occurrence id {$this->object['occurrenceid']} ";
        }

        return "The user with id '$this->userid' deleted the association of the pedagogy of id {$this->objectid} {$userdesc} {$occurrencedesc} of instance referentiel with " .
            "the course module id '$this->contextinstanceid'.";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/referentiel/pedagogie.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Return the legacy event log data.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $url = 'pedagogie.php?id='.$this->contextinstanceid;
        return array($this->courseid, 'referentiel', 'association user pedagogy deleted', $url, $this->objectid, $this->contextinstanceid);
    }
}

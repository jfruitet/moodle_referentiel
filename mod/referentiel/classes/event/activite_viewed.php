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
 * The mod_referentiel activite viewed event.
 *
 * @package    mod_referentiel
 * @copyright  2014 Jean FRUITET <jean.fruitet@univ-nantes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_referentiel\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_referentiel activite viewed event class.
 *
 * @package    mod_referentiel
 * @since      Moodle 2.7
 * @copyright  2014 Jean FRUITET <jean.fruitet@univ-nantes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activite_viewed extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'referentiel_activite';
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventactivityviewed', 'mod_referentiel');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $userdesc = '';
        if ($this->relateduserid) {
            $userdesc = "user '$this->relateduserid' in ";
        }
        return "The user with id '$this->userid' viewed the activity for {$userdesc} of the referentiel with " .
            "the course module id '$this->contextinstanceid'.";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/referentiel/activite.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Return the legacy event log data.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $url = 'activite.php?id='.$this->contextinstanceid;
        if ($this->relateduserid) {
            $url .= '&userid='.$this->relateduserid;
        }
        return array($this->courseid, 'referentiel', 'activity viewed', $url, $this->objectid, $this->contextinstanceid);
    }
}

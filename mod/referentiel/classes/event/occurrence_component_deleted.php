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
 * The mod_referentiel course module deleted event.
 *
 * @package    mod_referentiel
 * @copyright  2014 Jean FRUITET <jean.fruitet@univ-nantes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_referentiel\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_referentiel course module deleted event class.
 *
 * @package    mod_referentiel
 * @since      Moodle 2.7
 * @copyright  2014 Jean FRUITET <jean.fruitet@univ-nantes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class occurrence_component_deleted extends \core\event\base  {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        if (!empty($this->other['component'])){
			$this->data['objecttable'] = "referentiel_{$this->other['component']}";
		}
		else{
            $this->data['objecttable'] = "referentiel_referentiel";
		}
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $msg=0;
		if (!empty($this->other['occurrenceid'])){
  			if (!empty($this->other['component']) && !empty($this->other['componentid']) && ($this->other['component']=='domaine')){
            	$msg="The user with id '$this->userid' deleted the domain {$this->other['componentid']} for the occurrence '{$this->other['occurrenceid']}'. The course module id is '$this->contextinstanceid'";
			}
  			elseif (!empty($this->other['component']) && !empty($this->other['componentid']) && ($this->other['component']=='competence')){
            	$msg="The user with id '$this->userid' deleted the competency {$this->other['componentid']} for the occurrence '{$this->other['occurrenceid']}'. The course module id is '$this->contextinstanceid'";
			}
  			elseif (!empty($this->other['component']) && !empty($this->other['componentid']) && ($this->other['component']=='item')){
            	$msg="The user with id '$this->userid' deleted the item {$this->other['componentid']} for the occurrence '{$this->other['occurrenceid']}'. The course module id is '$this->contextinstanceid'";
			}

			else{
				$msg="The user with id '$this->userid' updated the domain {$this->objectid}. The course module id '$this->contextinstanceid'";
			}
		}
        if (!empty($this->other['msg'])){
            $msg.= ' '.$this->other['msg'];
		}
        return $msg;
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/referentiel/edit.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'referentiel', 'edit', 'edit.php?id='.$this->contextinstanceid, $this->objectid, $this->contextinstanceid);
    }

}


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

require_once($CFG->libdir.'/formslib.php');//putting this is as a safety as i got a class not found error.
/**
 * @package   mod-referentiel
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class referentiel_import_form extends moodleform {
    function definition() {
        $mform = & $this->_form;
        $instance = $this->_customdata;

        // visible elements
        if (isset($instance['msg'])){
            $mform->addElement('header', 'general', $instance['msg']);
        }
        else{
            $mform->addElement('header', 'general', get_string('import', 'referentiel'));
        }
        /*
        $mform->addElement('checkbox', 'stoponerror', 1);
        $mform->setType('stoponerror', PARAM_INT);
        $mform->setDefault('stoponerror', 1);
        */

        // format du fichier
        if (isset($instance['fileformats'])){
            $mform->addElement('select', 'format', get_string('fileformat', 'referentiel'), $instance['fileformats'], NULL);
            // $mform->setDefault('XML', 1);
        }
        else{
            $mform->addElement('hidden','format','XML');
            $mform->setType('format', PARAM_FILE);
        }
        $mform->addHelpButton('format', 'formath','referentiel');

        // override
        if (isset($instance['override'])){
            $radioarray=array();
            $radioarray[] = $mform->createElement('radio', 'override', '', get_string('choix_override', 'referentiel'), 1, NULL);
            $radioarray[] = $mform->createElement('radio', 'override', '', get_string('choix_notoverride', 'referentiel'), 0, NULL);
            $mform->addGroup($radioarray, 'radioar', get_string('override', 'referentiel'), array(' '), false);
            $mform->setDefault('override',  $instance['override']);
            $mform->addHelpButton('radioar', 'overrider','referentiel');
        }
        
        // newinstance  :: non, on conserve l'instance courante
        if (isset($instance['newinstance'])){
            $mform->addElement('hidden', 'newinstance', $instance['newinstance']);
            $mform->setType('newinstance', PARAM_INT);
        }
        if (isset($instance['stoponerror'])){
            // stoponerror :: oui
            $mform->addElement('hidden', 'stoponerror', $instance['stoponerror']);
            $mform->setType('stoponerror', PARAM_INT);
        }

        // override
        if (isset($instance['deleteall'])){
            $radioarray=array();
            $radioarray[] = $mform->createElement('radio', 'deleteall', '', get_string('no'), 0, NULL);
            $radioarray[] = $mform->createElement('radio', 'deleteall', '', get_string('yes'), 1, NULL);
            $mform->addGroup($radioarray, 'radioar', get_string('deleteall', 'referentiel'), array(' '), false);
            $mform->setDefault('deleteall',  $instance['deleteall']);
            $mform->addHelpButton('radioar', 'deleteallh','referentiel');
        }

        //$mform->addElement('filemanager', 'newfile', get_string('uploadafile'));
        //$mform->addElement('filemanager', 'referentiel_file', get_string('uploadafile'), null, $instance['options']);

        // pour une importation puis suppression
        $mform->addElement('filepicker', 'referentiel_file', get_string('uploadafile'), null, $instance['options']);

                if (isset($instance['import_activity'])){
            // stoponerror :: oui
            $mform->addElement('advcheckbox', 'import_activity', get_string('import_activity', 'referentiel'),' '.get_string('yes'), array('group' => 1), array(0, 1));
            $mform->setType('import_activity', PARAM_INT);
        }


        // hidden params
        $mform->addElement('hidden', 'd', $instance['d']);
        $mform->setType('d', PARAM_INT);
        
        $mform->addElement('hidden', 'contextid', $instance['contextid']);
        $mform->setType('contextid', PARAM_INT);
        
        $mform->addElement('hidden', 'filearea', $instance['filearea']);
        $mform->setType('filearea', PARAM_ALPHA);
        
        if (isset($instance['action'])){
            $mform->addElement('hidden', 'action',  $instance['action']);
            $mform->setType('action', PARAM_ALPHA);
        }
        
        // buttons
        $this->add_action_buttons(true, get_string('import', 'referentiel'));
    }
}

<?php

// JF
// modification d'une occurrence de referentiel
// A TERMINER
/*
Appel depuis edit.php
// create form and set initial data
$mform = new mod_referentiel_referentiel_form(null, array('current'=>$entry, 'cm'=>$cm,
    'instance'=>$instanceid,
    'emailuser'=>$email_user,
    'definitionoptions'=>$definitionoptions, 'attachmentoptions'=>$attachmentoptions));

*/


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/lib/formslib.php');

class mod_referentiel_referentiel_form extends moodleform {

    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $referentiel_referentiel     = $this->_customdata['referentiel_referentiel'];
        $referentiel_instance       = $this->_customdata['referentiel'];
        $cm                = $this->_customdata['cm'];
        $definitionoptions = $this->_customdata['definitionoptions'];
        $attachmentoptions = $this->_customdata['attachmentoptions'];

        $email_user = $this->_customdata['email_user'];
        
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name', 'referentiel'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addElement('text', 'code_referentiel', get_string('code', 'referentiel'));
        $mform->setType('code_referentiel', PARAM_TEXT);
        $mform->addRule('code_referentiel', null, 'required', null, 'client');
        $mform->addHelpButton('code_referentiel', 'code_referentiel', 'referentiel');

        $mform->addElement('editor', 'description_referentiel', get_string('definition', 'referentiel'), null, $definitionoptions);
        $mform->setType('description_referentiel', PARAM_RAW);
        $mform->addRule('description_referentiel', get_string('required'), 'required', null, 'client');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'd');
        $mform->setType('d', PARAM_INT);

//-------------------------------------------------------------------------------
        $this->add_action_buttons();

//-------------------------------------------------------------------------------
        $this->set_data($referentiel_referentiel);
    }

    function validation($data, $files) {
        global $CFG, $USER, $DB;
        $errors = parent::validation($data, $files);

        $referentiel = $this->_customdata['referentiel'];
        $cm       = $this->_customdata['cm'];
        
        // Valable pour Moodle 2.1 et Moodle 2.2
        //if ($CFG->version < 2011120100) {
            $context = context_module::instance($cm->id);
        //} else {
            // $context = context_module::instance($cm);
        //}

        $id = (int)$data['id'];
        $data['concept'] = trim($data['concept']);

        if ($id) {
            //We are updating an entry, so we compare current session user with
            //existing entry user to avoid some potential problems if secureforms=off
            //Perhaps too much security? Anyway thanks to skodak (Bug 1823)
            $old = $DB->get_record('referentiel_entries', array('id'=>$id));
            $ineditperiod = ((time() - $old->timecreated <  $CFG->maxeditingtime) || $referentiel->editalways);
            if ((!$ineditperiod || $USER->id != $old->userid) and !has_capability('mod/referentiel:manageentries', $context)) {
                if ($USER->id != $old->userid) {
                    $errors['concept'] = get_string('errcannoteditothers', 'referentiel');
                } elseif (!$ineditperiod) {
                    $errors['concept'] = get_string('erredittimeexpired', 'referentiel');
                }
            }
            if (!$referentiel->allowduplicatedentries) {
                if ($DB->record_exists_select('referentiel_entries',
                        'referentielid = :referentielid AND LOWER(concept) = :concept AND id != :id', array(
                            'referentielid' => $referentiel->id,
                            'concept'    => moodle_strtolower($data['concept']),
                            'id'         => $id))) {
                    $errors['concept'] = get_string('errconceptalreadyexists', 'referentiel');
                }
            }

        }
        else {
            if (!$referentiel->allowduplicatedentries) {
                if ($DB->record_exists_select('referentiel_entries',
                        'referentielid = :referentielid AND LOWER(concept) = :concept', array(
                            'referentielid' => $referentiel->id,
                            'concept'    => moodle_strtolower($data['concept'])))) {
                    $errors['concept'] = get_string('errconceptalreadyexists', 'referentiel');
                }
            }
        }

        return $errors;
    }
}


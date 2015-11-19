<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');


class mod_referentiel_mod_form extends moodleform_mod {
    protected $_referentielinstance = null;

    function definition() {
        global $CFG, $DB;

        $mform =& $this->_form;
        $maxbytes=1073741824; // default value 1073741824
        // this hack is needed for settings of ref_referentiel
        if (!empty($this->_instance)) {
            if ($ref = $DB->get_record("referentiel", array("id"=>"$this->_instance"))) {
                $ref_referentiel = $ref->ref_referentiel;
                if ($ref->course){
                    $course= $DB->get_record("course", array("id" => "$ref->course"));
                    if ($course){
                        $maxbytes=get_max_upload_file_size($CFG->maxbytes, $course->maxbytes);
                    }
                }
            }
            else {
                print_error('invalidreferentiel', 'referentiel');
            }
        }
        else {
            $ref_referentiel = 0;
        }

        if (empty($ref)){
			$referentielinstance = new referentiel(); // objet referentiel
		}
		else{
            $referentielinstance = new referentiel($this->_cm->id, $ref, $this->_cm, $course);
		}

//-------------------------------------------------------------------------------

        $mform->addElement('hidden', 'ref_referentiel', $ref_referentiel);
        $mform->setType('ref_referentiel', PARAM_INT);
        $mform->setDefault('ref_referentiel', $ref_referentiel);
        $mform->addElement('header', 'general', get_string('creer_instance_referentiel', 'referentiel'));
        
        // name
        $mform->addElement('text', 'name', get_string('name_instance', 'referentiel'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('name_instance_obligatoire','referentiel'), 'required', null, 'client');
        $mform->addHelpButton('name', 'name_instanceh', 'referentiel');
        // remplace la description
        // OBSOLETE : $this->add_intro_editor(true, get_string('description', 'referentiel'));
        $this->standard_intro_elements(get_string('description_instance', 'referentiel'));

        $mform->addElement('text', 'label_domaine', trim(get_string('label_domaine','referentiel')), array('size'=>'60'));
        $mform->setType('label_domaine', PARAM_TEXT);
        $mform->setDefault('label_domaine', trim(get_string('domaine','referentiel')));
        //$mform->addRule('label_domaine', null, 'required', null, 'client');

        $mform->addElement('text', 'label_competence', trim(get_string('label_competence','referentiel')), array('size'=>'60'));
        $mform->setType('label_competence', PARAM_TEXT);
        $mform->setDefault('label_competence', trim(get_string('competence','referentiel')));
        //$mform->addRule('label_competence', null, 'required', null, 'client');

        //$this->add_intro_editor(true, get_string('label_item_question', 'referentiel'));
        $mform->addElement('text', 'label_item', trim(get_string('label_item','referentiel')), array('size'=>'60'));
        $mform->setType('label_item', PARAM_TEXT);
        $mform->setDefault('label_item', trim(get_string('item','referentiel')));
        //$mform->addRule('label_item', null, 'required', null, 'client');
/*
        $mform->addElement('text', 'maxbytes', trim(get_string('maxsize','referentiel',display_size($maxbytes))), NULL);
        $mform->setType('maxbytes', PARAM_INT);
        $mform->setDefault('maxbytes', $maxbytes);
        $mform->addRule('maxbytes', null, 'required', null, 'client');
*/

        $choices=get_max_upload_sizes($maxbytes);
        //print_object($choices);
        //exit;
        $mform->addElement('select', 'maxbytes', get_string('maxsize','referentiel',display_size($maxbytes)), $choices);
        $mform->setType('maxbytes', PARAM_INT);
        $mform->setDefault('maxbytes', $maxbytes);
        $mform->addRule('maxbytes', null, 'required', null, 'client');

        $referentielinstance->setup_elements($mform, $referentielinstance);

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();

    }

    // Needed by plugin referentiel types if they include a filemanager element in the settings form
    function has_instance() {
        return ($this->_instance != NULL);
    }

    // Needed by plugin referentiel types if they include a filemanager element in the settings form
    function get_context() {
        return $this->context;
    }

    protected function get_referentiel_instance() {
        global $CFG, $DB;

        if ($this->_referentielinstance) {
            return $this->_referentielinstance;
        }
        if (!empty($this->_instance)) {
            if ($ref = $DB->get_record('referentiel', array('id'=>$this->_instance))) {
                $ref_referentiel = $ref->ref_referentiel;
            }
            else {
                print_error('invalidreferentiel', 'referentiel');
            }
        }
        else {
            $ref_referentiel = required_param('ref_referentiel', PARAM_INT);
        }

        $this->referentielinstance = new referentiel();
        $this->referentielinstance->ref_referentiel=$ref_referentiel;
        return $this->referentielinstance;
    }

}


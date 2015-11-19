<?php  // $Id:  class/referentiel.class.php,v 1.0 2011/04/21 00:00:00 jfruitet Exp $
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Library of functions and constants for module referentiel
 *
 * @author jfruitet
 * @version $Id: class/referentiel.class.php,v 1.0 2011/04/20 00:00:00 jfruitet Exp $
 * @package referentiel v 6.0.00 2011/04/21 00:00:00
 **/

// Version Moodle 2
// passage en modele objet



// ======================================================================

/**
 * Standard base class for all referentiel table.
 *
 * @package   mod-referentiel
 * @copyright 2011 onwards Jean Fruitete {@link http://www.univ-nantes.fr/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class referentiel {

    /** @var object */
    var $cm;
    /** @var object */
    var $course;
    /** @var object */
    var $referentiel; // L'instance

    /** @var object */
    var $referentiel_referentiel; // Le réferentiel associe à l'instance
    
    /** @var string */
    var $strreferentiel;
    /** @var string */
    var $strreferentiels;
    /** @var string */
    var $strsubmissions;
    /** @var string */
    var $strlastmodified;
    /** @var string */
    var $pagetitle;
    /** @var bool */
    var $usehtmleditor;
    /**
     * @todo document this var
     */
    var $defaultformat;
    /**
     * @todo document this var
     */
    var $context;
    /*
    var $name;
    var $description_instance;
    var $label_domaine;
    var $label_competence;
    var $label_item;
    var $config;
    var $config_impression;
    var $config_globale;
    var $config_impression_globale;
    var $ref_referentiel;
    var $visible;
    var $intro;
    var $introformat;
    */
    
    /**
     * Constructor for the base referentiel class
     *
     * Constructor for the base referentiel class.
     * If cmid is set create the cm, course, referentiel objects.
     * If the referentiel is hidden and the user is not a teacher then
     * this prints a page header and notice.
     *
     * @global object
     * @global object
     * @param int $cmid the current course module id - not set for new assignments
     * @param object $referentiel usually null, but if we have it we pass it to save db access
     * @param object $cm usually null, but if we have it we pass it to save db access
     * @param object $course usually null, but if we have it we pass it to save db access
     */
    function referentiel($cmid='staticonly', $referentiel=NULL, $cm=NULL, $course=NULL) {
        global $COURSE, $DB;

        if ($cmid == 'staticonly') {
            //use static functions only!
            return;
        }

        global $CFG;

        if ($cm) {
            $this->cm = $cm;
        } else if (! $this->cm = get_coursemodule_from_id('referentiel', $cmid)) {
            print_error('invalidcoursemodule');
        }


        // Valable pour Moodle 2.1 et Moodle 2.2
        ////if ($CFG->version < 2011120100) {
            $this->context = context_module::instance($this->cm->id);
        //} else {
        //    //  $this->context = context_module::instance($this->cm); 
        //}

        if ($course) {
            $this->course = $course;
        } else if ($this->cm->course == $COURSE->id) {
            $this->course = $COURSE;
        } else if (! $this->course = $DB->get_record('course', array('id'=>$this->cm->course))) {
            print_error('invalidid', 'referentiel');
        }

        // valeurs par defaut
        $this->referentiel= new Object();
        
        $this->referentiel->name="";
        $this->referentiel->description_instance="";
        $this->referentiel->label_domaine=trim(get_string('domaine','referentiel'));
        $this->referentiel->label_competence=trim(get_string('competence','referentiel'));
        $this->referentiel->label_item=trim(get_string('item','referentiel'));
        $this->referentiel->label_item=trim(get_string('item','referentiel'));
        $this->referentiel->config=$this->creer_configuration('config');
        $this->referentiel->config_impression=$this->creer_configuration('configuration_impression');
        $this->referentiel->config_globale=$this->referentiel->config;
        $this->referentiel->config_impression_globale=$this->referentiel->config_impression;
        $this->referentiel->ref_referentiel=0;
        $this->referentiel->visible=1;
        $this->referentiel->intro="";
        $this->referentiel->introformat=1;
        $this->referentiel->maxbytes=1048576;

        if ($referentiel) {
            $this->referentiel = $referentiel;
        } else if (! $this->referentiel = $DB->get_record('referentiel', array('id'=>$this->cm->instance))) {
            print_error('invalidid', 'referentiel');
        }

        $this->referentiel->cmidnumber = $this->cm->idnumber; // compatibility with modedit referentiel obj
        $this->referentiel->course   = $this->course->id; // compatibility with modedit referentiel obj

        if (!empty($this->referentiel->ref_referentiel)){ // occurrence associe
            $this->referentiel_referentiel =  $DB->get_record('referentiel_referentiel', array('id'=>$this->referentiel->ref_referentiel));
        }

        $this->strreferentiel = get_string('modulename', 'referentiel');
        $this->strreferentiels = get_string('modulenameplural', 'referentiel');
        $this->strsubmissions = get_string('submissions', 'referentiel');
        $this->strlastmodified = get_string('lastmodified');
        $this->pagetitle = strip_tags($this->course->shortname.': '.$this->strreferentiel.': '.format_string($this->referentiel->name,true));

        // visibility handled by require_login() with $cm parameter
        // get current group only when really needed

        /// Set up things for a HTML editor if it's needed
        $this->defaultformat = editors_get_preferred_format();


    }

    /**
     * Display the referentiel, used by view.php
     *
     * This in turn calls the methods producing individual parts of the page
     */
    function view($mode, $currenttab, $select_acc, $data_filtre) {

        global $CFG, $USER;
        $this->context = context_module::instance($this->cm->id);
        require_capability('mod/referentiel:view', $this->context);

        if ($CFG->version > 2014051200) { // Moodle 2.7+
            $params = array(
                'contextid' => $this->context->id,
                'objectid' => $this->referentiel->id,
            );
            $event = \mod_referentiel\event\course_module_viewed::create($params);
            $event->trigger();
        } else { // Before Moodle 2.7
	        add_to_log($this->course->id, 'referentiel', 'view', "view.php?id={$this->cm->id}",$this->referentiel->id, $this->cm->id);
        }

        $this->view_header();

        //
        // lien vers le referentiel lui-meme
        if (!$this->get_referentiel_referentiel()){  // not any occurrence
            $this->view_intro();
            $this->view_instance_referentiel();
            $this->add_referentiel_referentiel();
        }
        else{
                $this->view_intro();
                $this->onglets($mode, $currenttab, $select_acc, $data_filtre);
                $this->view_title();
                $this->view_referentiel_referentiel();
        }
        $this->view_footer();
        die();
    }

    /**
     * Import the referentiel, used by import_instance.php
     *
     * This in turn calls the methods producing individual parts of the page
     */
    function load_referentiel($mode, $format='', $action='') {

        global $CFG, $USER;
        global $PAGE, $OUTPUT;
        
        // check role capability
        ////if ($CFG->version < 2011120100) {
            $this->context = context_module::instance($this->cm->id);
        //} else {
        //    //  $this->context = context_module::instance($this->cm); 
        //}

        require_capability('mod/referentiel:import', $this->context);

        if ($CFG->version > 2014051200) { // Moodle 2.7+
            $params = array(
                'contextid' => $this->context->id,
                'objectid' => $this->referentiel->id,
            );
            $event = \mod_referentiel\event\import_intance::create($params);
            $event->trigger();
        } else { // Before Moodle 2.7
	        add_to_log($this->course->id, 'referentiel', 'view', "import_instance.php?id={$this->cm->id}", $this->referentiel->id, $this->cm->id);
		}


        $this->view_header();

	   // get parameters

        $parametres = new stdClass;
        $parametres->choosefile = optional_param('choosefile','',PARAM_PATH);
        $parametres->stoponerror = optional_param('stoponerror', 0, PARAM_BOOL);
        $parametres->override = optional_param('override', 0, PARAM_BOOL);
        $parametres->newinstance = optional_param('newinstance', 0, PARAM_BOOL);

        // get display strings
        $txt = new stdClass();
        $txt->referentiel = get_string('referentiel','referentiel');
        $txt->fileformat = get_string('fileformat','referentiel');
	    $txt->choosefile = get_string('choosefile','referentiel');
    	$txt->formatincompatible= get_string('formatincompatible','referentiel');
        $txt->file = get_string('file');
        $txt->fileformat = get_string('fileformat','referentiel');
        $txt->fromfile = get_string('fromfile','referentiel');
    	$txt->importerror_referentiel_id = get_string('importerror_referentiel_id','referentiel');
        $txt->importerror = get_string('importerror','referentiel');
        $txt->importfilearea = get_string('importfilearea','referentiel');
        $txt->importfileupload = get_string('importfileupload','referentiel');
        $txt->importfromthisfile = get_string('importfromthisfile','referentiel');
        $txt->modulename = get_string('modulename','referentiel');
        $txt->modulenameplural = get_string('modulenameplural','referentiel');
        $txt->onlyteachersimport = get_string('onlyteachersimport','referentiel');
        $txt->stoponerror = get_string('stoponerror', 'referentiel');
	    $txt->upload = get_string('upload');
        $txt->uploadproblem = get_string('uploadproblem');
        $txt->uploadthisfile = get_string('uploadthisfile');
	    $txt->importreferentiel	= get_string('importreferentiel','referentiel');
	    $txt->newinstance	= get_string('newinstance','referentiel');
    	$txt->choix_newinstance	= get_string('choix_newinstance','referentiel');
	    $txt->choix_notnewinstance	= get_string('choix_notnewinstance','referentiel');
	    $txt->override = get_string('override', 'referentiel');
	    $txt->choix_override	= get_string('choix_override','referentiel');
	    $txt->choix_notoverride	= get_string('choix_notoverride','referentiel');
 /*
    	/// Print the page header
	    $strreferentiels = get_string('modulenameplural','referentiel');
    	$strreferentiel = get_string('referentiel','referentiel');
	    $strmessage =
	    $icon = '<img class="icon" src="'.$CFG->wwwroot.'/mod/referentiel/icon.gif" alt="'.get_string('modulename','referentiel').'"/>';

	    $strpagename=get_string('modifier_referentiel','referentiel');
*/

        // echo $OUTPUT->heading($strmessage, 'importreferentiel', 'referentiel', $icon);
        echo $OUTPUT->heading(get_string('importreferentiel','referentiel'), 2);
        
        // file upload form submitted
        if (!empty($format)) {
            if (!confirm_sesskey()) {
        	   print_error( 'sesskey' );
            }
            // file checks out ok
            $fileisgood = false;
            // work out if this is an uploaded file
            // or one from the filesarea.

            if (!empty($parametres->choosefile)) {
                $importfile = "{$CFG->dataroot}/{$this->course->id}/{$parametres->choosefile}";
                if (file_exists($importfile)) {
                    $fileisgood = true;
                }
                else {
                    notify($txt->uploadproblem);
                }
            }
            else {
                // must be upload file
                if (empty($_FILES['newfile'])) {
                    notify( $txt->uploadproblem );
                }
                else if ((!is_uploaded_file($_FILES['newfile']['tmp_name']) or $_FILES['newfile']['size'] == 0)) {
                    notify( $txt->uploadproblem );
                }
                else {
                    $importfile = $_FILES['newfile']['tmp_name'];
                // tester l'extention du fichier
                // DEBUG
                // echo "<br />DEBUG : 214 import_instance.php<br />FORMAT : $format<br />IMPORT_FILE $importfile\n";
       			// Les données suivantes sont disponibles après chargement
			    // echo "<br />DEBUG :: Fichier téléchargé : '". $_FILES['newfile']['tmp_name'] ."'\n";
                // echo "<br />DEBUG :: Nom : '". $_FILES['newfile']['name'] ."'\n";
			    // echo "<br />DEBUG :: Erreur : '". $_FILES['newfile']['error'] ."'\n";
			    // echo "<br />DEBUG :: Taille : '". $_FILES['newfile']['size'] ."'\n";

                // echo "<br />DEBUG :: Type : '". $_FILES['newfile']['type'] ."'\n";
                    $nom_fichier_charge_extension = substr( strrchr($_FILES['newfile']['name'], "." ), 1);
			    // echo "<br />DEBUG :: LIGNE 223 :: Extension : '". $nom_fichier_charge_extension ."'\n";
			    // echo "<br />DEBUG :: LE FICHIER EST CHARGE\n";
                    if ($nom_fichier_charge_extension!=$format){
                        notify( $txt->formatincompatible);
                    }
                    else{
                        $fileisgood = true;
                    }
                }
            }

            // process if we are happy, file is ok
            if ($fileisgood) {
                $returnlink=$CFG->wwwroot.'/mod/referentiel/import_instance.php?courseid='.$this->course->id.'&amp;sesskey='.sesskey().'&amp;instance='.$instance.'&amp;mode='.$mode.'&amp;action='.$action;
			    // DEBUG
			    // echo "<br/>RETURNLINK : $returnlink\n";

                if (! is_readable("format/$format/format.php")) {
                    print_error( get_string('formatnotfound','referentiel', $format) );
                }
                require("format.php");  // Parent class
                require("format/$format/format.php");
                $classname = "rformat_$format";
                $rformat = new $classname();
                // load data into class
                $rformat->setIReferentiel( $this->referentiel ); // instance
                // $rformat->setRReferentiel( $this->referentiel_referentiel ); // not yet
                $rformat->setCourse( $this->course );
                $rformat->setContext( $this->context);
                $rformat->setCoursemodule( $cm);
                $rformat->setFilename( $importfile );
                $rformat->setStoponerror( $parametres->stoponerror );
			    $rformat->setOverride( $parametres->override );
			    $rformat->setNewinstance( $parametres->newinstance );
			    $rformat->setAction( $action );

			    // $rformat->setReturnpage("");

                // Do anything before that we need to
                if (! $rformat->importpreprocess()) {
                    print_error( $txt->importerror , $returnlink);
                }

                // Process the uploaded file

                if (! $rformat->importprocess() ) {
                    print_error( $txt->importerror , $returnlink);
                }

                // In case anything needs to be done after
                if (! $rformat->importpostprocess()) {
                    print_error( $txt->importerror , $returnlink);
                }

			    // Verifier si  referentiel charge
                if (! $rformat->new_referentiel_id) {
                    print_error( $txt->importerror_referentiel_id , $returnlink);
                }
                echo '<hr />
<form name="form" method="post" action="add.php?id='.$this->cm->id.'">

<input type="hidden" name="name_instance" value="'.$this->referentiel->name_instance  .'" />
<input type="hidden" name="description_instance" value="'.htmlentities($this->referentiel->description_instance, ENT_QUOTES, 'UTF-8')  .'" />
<input type="hidden" name="label_domaine" value="'.$this->referentiel->label_domaine  .'" />
<input type="hidden" name="label_competence" value="'.$this->referentiel->label_competence  .'" />
<input type="hidden" name="label_item" value="'.$this->referentiel->label_item  .'" />

<input type="hidden" name="action" value="importreferentiel" />

<input type="hidden" name="new_referentiel_id" value="'.$rformat->new_referentiel_id.'" />
<input type="hidden" name="action" value="'.$rformat->action.'" />

<input type="hidden" name="sesskey" value="'.  sesskey().'" />
<input type="hidden" name="courseid" value="'. $this->course->id.'" />
<input type="hidden" name="instance" value="'.  $this->referentiel->id.'" />
<input type="hidden" name="mode" value="'.$mode.'" />
<input type="submit" value="'. get_string("continue").'" />
</form>
<div>
'. "\n";
                $this->view_footer();
                die();
            }
        }

        /// Print upload form

        // get list of available import formats
        $fileformatnames = referentiel_get_import_export_formats( 'import', 'rformat' );

	   //==========
        // DISPLAY
        //==========

        echo '<form id="form" enctype="multipart/form-data" method="post" action="import_instance.php?id='.  $this->cm->id .'">
        <fieldset class="invisiblefieldset" style="display: block;">'."\n";
        //echo $OUTPUT->box_start('generalbox boxwidthnormal boxaligncenter');
        echo $OUTPUT->box_start('generalbox  boxaligncenter');
        echo '
<table cellpadding="5">
<tr>
<td align="right">'.$txt->fileformat.'</td>
<td>'. html_writer::select($fileformatnames, 'format', 'xml', false).'</td>
<td>'. $OUTPUT->help_icon('formath', 'referentiel'); //, "referentiel", $txt->importreferentiel);
        echo '</td>
</tr>
<tr>
<td align="right">'.  $txt->stoponerror.'
</td>
<td>
<input name="stoponerror" type="checkbox" checked="checked" />
</td>
<td>
&nbsp;
</td>
</tr>
<tr>
<td align="right">'.  $txt->override.'
</td>
<td>
<input name="override" type="radio" value="1" /> '.  $txt->choix_override.'
<br />
<input name="override" type="radio"  value="0"  checked="checked" /> '. $txt->choix_notoverride.'
</td>
<td>'. $OUTPUT->help_icon('overrider', 'referentiel').'</td>
</tr>
';
        if (!empty($this->referentiel->ref_referentiel)){
            echo '
<tr>
<td align="right">'.$txt->newinstance.'</td>
<td>
<input name="newinstance" type="radio"  value="1"  checked="checked"/> '.  $txt->choix_newinstance.'
<br />
<input name="newinstance" type="radio"   value="0" /> '.  $txt->choix_notnewinstance.'
</td>
<td>
'. $OUTPUT->help_icon('overrideo', 'referentiel').'
</td>
</tr>
';
        }
        else{
            echo '<input name="newinstance" type="hidden"  value="1" />'."\n";
        }
        echo '
</table>
';
        echo $OUTPUT->box_end();

        echo $OUTPUT->box_start('generalbox  boxaligncenter');

        echo $txt->importfileupload.'
            <table cellpadding="5">
                <tr>
                    <!-- td align="right">'.  $txt->upload.':</td -->
                    <td colspan="2">
';

        // upload_print_form_fragment(1,array('newfile'),null,false,null,$this->course->maxbytes,0,false);
        echo 'upload_print_form_fragment deprecated';
        echo '</td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="save" value="'.  $txt->uploadthisfile.'" /></td>
                </tr>
            </table>
';
        echo $OUTPUT->box_end();

        echo $OUTPUT->box_start('generalbox boxaligncenter');
        echo $txt->importfilearea.'
            <table cellpadding="5">
                <tr>
                    <td align="right">'.  $txt->file.':</td>
                    <td><input type="text" name="choosefile" size="60" /></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td>
';

        echo $OUTPUT->single_button("/files/index.php?id={$this->course->id}&choose=form.choosefile", $txt->choosefile);
        echo '<br />
<input type="submit" name="save" value="'.  $txt->importfromthisfile.'" /></td>
                </tr>
            </table>
';
        echo $OUTPUT->box_end();
        echo '
<input type="hidden" name="action" value="'.$action.'" />

<input type="hidden" name="name_instance" value="'.$this->referentiel->name  .'" />
<input type="hidden" name="description_instance" value="'.htmlentities($this->referentiel->description_instance, ENT_QUOTES, 'UTF-8') .'" />
<input type="hidden" name="label_domaine" value="'.$this->referentiel->label_domaine  .'" />
<input type="hidden" name="label_competence" value="'.$this->referentiel->label_competence  .'" />
<input type="hidden" name="label_item" value="'.$this->referentiel->label_item  .'" />

<!-- These hidden variables are always the same -->

<input type="hidden" name="sesskey" value="'. sesskey().'" />
<input type="hidden" name="courseid" value="'.  $this->course->id.'" />
<input type="hidden" name="instance" value="'. $this->referentiel->id.'" />
<input type="hidden" name="mode" value="'.$mode.'" />

        </fieldset>
    </form>'."\n";
        $this->view_footer();
        die();
    }
    
    
    /**
     * return Object referentiel_referentiel
     *
     **/
    function get_referentiel_referentiel(){
        // DEBUG
        global $DB;

        if (!empty($this->referentiel->ref_referentiel)){
            $referentiel_referentiel=$DB->get_record('referentiel_referentiel', array("id" => $this->referentiel->ref_referentiel));
            if (!empty($referentiel_referentiel)){
                return $referentiel_referentiel;
            }
        }
        return NULL;
    }

    /**
     * Display the header and top of a page
     *
     * This is used by the view() method to print the header of view.php but
     * it can be used on other pages in which case the string to denote the
     * page in the navigation trail should be passed as an argument
     *
     * @global object
     * @param string $subpage Description of subpage to be used in navigation trail
     */
    function view_header($subpage='') {
        global $CFG, $PAGE, $OUTPUT;

        if ($subpage) {
            $PAGE->navbar->add($subpage);
        }

        $PAGE->set_title($this->pagetitle);
        $PAGE->set_heading($this->course->fullname);

        echo $OUTPUT->header();

        //groups_print_activity_menu($this->cm, $CFG->wwwroot . '/mod/referentiel/view.php?id=' . $this->cm->id.'&non_redirection=1');

        echo '<div class="reportlink">'.$this->submittedlink().'</div>';
        echo '<div class="clearer"></div>';
    }

    /**
     * Display the referentiel intro
     *
     * The default implementation prints the referentiel description in a box
     */
    function view_intro() {

        if (!empty($this->referentiel->name)){
            echo '<div align="center"><h1>'.$this->referentiel->name.'</h1></div>'."\n";
        }
           // plagiarism_print_disclosure($this->cm->id);
    }
    
    /**
     * Display the referentiel intro
     *
     * The default implementation prints the referentiel description in a box
     */
    function view_title() {
        global $OUTPUT;

        $icon = $OUTPUT->pix_url('icon','referentiel');

        $s='<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('listreferentiel', 'referentiel').' '.get_string('referentiel', 'referentiel').' ';
        if (!empty($this->referentiel_referentiel->code_referentiel)){
            $s.=' '.$this->referentiel_referentiel->code_referentiel;
        }
        $s.=$OUTPUT->help_icon('referentielh','referentiel').'</h2></div>'."\n";
        echo $s;
    }

    /**
     * Display the referentiel instance datas
     *
     */
    /*
    function view_instance_referentiel() {
        global $OUTPUT;
        $icon = $OUTPUT->pix_url('icon','referentiel');
        $s='<h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('referentiel_instance', 'referentiel').' ';
        if (!empty($this->referentiel->name)){
            $s.=' '.$this->referentiel->name;
        }
        $s.=$OUTPUT->help_icon('referentielinstanceh','referentiel').'</h2>'."\n";
        echo $s;
        echo '<table>'."\n";
        if ($this->referentiel->date_instance) {
            echo '<tr><th>'.get_string('availabledate','referentiel').':</th>';
            echo '    <td>'.userdate($this->referentiel->date_instance).'</td></tr>';
        }
        echo '<tr><th>'.get_string('name_instance','referentiel').':</th>';
        echo '    <td>'.htmlentities($this->referentiel->name, ENT_QUOTES, 'UTF-8').'</td></tr>';
        echo '<tr><th>'.get_string('description_instance','referentiel').':</th>';
        echo '    <td>'.htmlentities(this->referentiel->description_instance, ENT_QUOTES, 'UTF-8').'</td></tr>';
        echo '<tr><th>'.get_string('label_domaine','referentiel').':</th>';
        echo '    <td>'.$this->referentiel->label_domaine.'</td></tr>';
        echo '<tr><th>'.get_string('label_competence','referentiel').':</th>';
        echo '    <td>'.$this->referentiel->label_competence.'</td></tr>';
        echo '<tr><th>'.get_string('label_item','referentiel').':</th>';
        echo '    <td>'.$this->referentiel->label_item.'</td></tr>';
        echo '<tr><th colspan="2">'.get_string('maxsize','referentiel', display_size($this->referentiel->maxbytes)).'</th>'."\n";

        echo '</table>'."\n";

    }
    */
    function view_instance_referentiel() {
        global $OUTPUT;
        $icon = $OUTPUT->pix_url('icon','referentiel');
        $s='<h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('referentiel_instance', 'referentiel').' ';
        if (!empty($this->referentiel->name)){
            $s.=' '.$this->referentiel->name;
        }
        $s.=$OUTPUT->help_icon('referentielinstanceh','referentiel').'</h2>'."\n";
        echo $s;
/*
        echo '<table>'."\n";
        if ($this->referentiel->date_instance) {
            echo '<tr><th>'.get_string('availabledate','referentiel').':</th>';
            echo '    <td>'.userdate($this->referentiel->date_instance).'</td></tr>';
        }
        echo '<tr><th>'.get_string('name_instance','referentiel').':</th>';
        echo '    <td>'.$this->referentiel->name .'</td></tr>';
        echo '<tr><th>'.get_string('description_instance','referentiel').':</th>';
        echo '    <td>'.$this->referentiel->description_instance.'</td></tr>';
        echo '<tr><th>'.get_string('label_domaine','referentiel').':</th>';
        echo '    <td>'.$this->referentiel->label_domaine.'</td></tr>';
        echo '<tr><th>'.get_string('label_competence','referentiel').':</th>';
        echo '    <td>'.$this->referentiel->label_competence.'</td></tr>';
        echo '<tr><th>'.get_string('label_item','referentiel').':</th>';
        echo '    <td>'.$this->referentiel->label_item.'</td></tr>';
        echo '<tr><th colspan="2">'.get_string('maxsize','referentiel', display_size($this->referentiel->maxbytes)).'</th></tr>'."\n";

        echo '</table>'."\n";
*/
        echo '<div class="ref_aff0">';
        if ($this->referentiel->date_instance) {
            echo '<span class="bold">'.get_string('availabledate','referentiel').'</span> &nbsp; &nbsp; '.userdate($this->referentiel->date_instance).' <br />';
        }
        echo '<span class="bold">'.get_string('name_instance','referentiel').'</span> &nbsp; &nbsp; '.$this->referentiel->name .
'<br /><span class="bold">'.get_string('description_instance','referentiel').'</span><div class="ref_aff1">'.nl2br($this->referentiel->description_instance).'</div>'.
'<span class="bold">'.get_string('label_domaine','referentiel').'</span> &nbsp; '.$this->referentiel->label_domaine.' &nbsp; &nbsp; '.
'<span class="bold">'.get_string('label_competence','referentiel').'</span> &nbsp; '.$this->referentiel->label_competence.' &nbsp; &nbsp; '.
'<span class="bold">'.get_string('label_item','referentiel').'</span> &nbsp; '.$this->referentiel->label_item.' &nbsp; &nbsp; '.
'<span class="bold">'.get_string('maxdoc','referentiel').'</span> &nbsp; '.display_size($this->referentiel->maxbytes).
'</div>'."\n";

    }

    /**
     * Display the referentiel
     *
     * The default implementation prints the referentiel description in a table
     */
    function view_referentiel_referentiel() {
        referentiel_affiche_referentiel_instance($this->cm, $this->referentiel->id);
    }

    /**
     * Display the referentiel thumbs
     *
     */

    function onglets($mode, $currenttab, $select_acc, $data_filtre) {
        require_once('onglets.php');
        $tab_onglets = new Onglets($this->context, $this->referentiel, $this->referentiel_referentiel, $this->cm, $this->course, $currenttab, $select_acc, $data_filtre);
        $tab_onglets->display();
    }
    
    /**
     * Display the bottom and footer of a page
     *
     * This default method just prints the footer.
     * This will be suitable for most referentiel types
     */
    function view_footer() {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }

    /**
     * Add / import a referentiel_referentiel
     *
     *
     */
	 /*OLD VERSION
    function add_referentiel_referentiel(){
    // proposer l'import ou la creation d'un référentiel
        global $OUTPUT;
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'associer');
        echo '<h2 align="center">'.get_string('aide_creer_referentiel','referentiel').'</h2>';


        $ok_existe_au_moins_un_referentiel=(referentiel_referentiel_exists()>0);
        $ok_creer_importer_referentiel=referentiel_get_item_configuration('creref', $this->referentiel->id);
        // creation importation possible si $ok_selectionner_referentiel
        $ok_selectionner_referentiel=referentiel_get_item_configuration('selref', $this->referentiel->id);

        // debut
        if (($ok_selectionner_referentiel==0) || ($ok_creer_importer_referentiel==0)){
            echo '<h3 align="center">'.get_string('associer_referentiel','referentiel').'</h3>';
            if (($ok_selectionner_referentiel==0) && $ok_existe_au_moins_un_referentiel) {
                // formulaire de selection dans la liste des referentiels existants
                echo '<div align="center">
<form name="form" method="post" action="selection.php?id='.$this->cm->id.'">
<input type="hidden" name="name_instance" value="'. $this->referentiel->name.'" />
<input type="hidden" name="description_instance" value="'.htmlentities($this->referentiel->description_instance, ENT_QUOTES, 'UTF-8').'" />
<input type="hidden" name="label_domaine" value="'.$this->referentiel->label_domaine.'" />
<input type="hidden" name="label_competence" value="'.$this->referentiel->label_competence.'" />
<input type="hidden" name="label_item" value="'.$this->referentiel->label_item.'" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="'.$this->referentiel->course.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="instance"      value="'.$this->referentiel->id.'" />
<input type="hidden" name="mode"          value="add" />
<input type="submit" value="'.get_string('selectreferentiel','referentiel').'" />
</form>
</div>'."\n";
            }
            if ($ok_creer_importer_referentiel==0) {
                echo '<div align="center">
<form name="form" method="post" action="import_instance.php?id='.$this->cm->id.'">
<input type="hidden" name="name_instance" value="'.$this->referentiel->name .'" />
<input type="hidden" name="description_instance" value="'.htmlentities($this->referentiel->description_instance, ENT_QUOTES, 'UTF-8').'" />
<input type="hidden" name="label_domaine" value="'.$this->referentiel->label_domaine.'" />
<input type="hidden" name="label_competence" value="'.$this->referentiel->label_competence.'" />
<input type="hidden" name="label_item" value="'.$this->referentiel->label_item.'" />
<input type="hidden" name="intro" value="'.htmlentities($this->referentiel->intro, ENT_QUOTES, 'UTF-8').'" />
<input type="hidden" name="config" value="'.$this->referentiel->config.'" />
<input type="hidden" name="config_impression" value="'.$this->referentiel->config_impression.'" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="'.$this->referentiel->course.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="instance"      value="'.$this->referentiel->id.'" />
<input type="hidden" name="mode"          value="add" />
<input type="submit" value="'.get_string('importreferentiel','referentiel').'" />
</form>
</div>'."\n";
            }
            echo $OUTPUT->box_end();
            // creer un referentiel de toutes pieces
            if ($ok_creer_importer_referentiel==0) {
                echo '<div align="center">'."\n";
                // Editeur wysiwyg
                $this->edition_wysiwyg_referentiel();
                echo '</div>'."\n";
                // CREATION avec formualires
                $this->creation_referentiel();
            }
        }

    }
**************************/
    /**
     * Add / import a referentiel_referentiel
     *
     *
     */
    function add_referentiel_referentiel(){
    // proposer l'import ou la creation d'un référentiel
        global $CFG;
		global $OUTPUT;
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'associer');
        echo '<h2 align="center">'.get_string('aide_creer_referentiel','referentiel').'</h2>';


        $ok_existe_au_moins_un_referentiel=(referentiel_referentiel_exists()>0);
        $ok_creer_importer_referentiel=referentiel_get_item_configuration('creref', $this->referentiel->id);
        // creation importation possible si $ok_selectionner_referentiel
        $ok_selectionner_referentiel=referentiel_get_item_configuration('selref', $this->referentiel->id);

        // debut
        if (($ok_selectionner_referentiel==0) || ($ok_creer_importer_referentiel==0)){
            echo '<h3 align="center">'.get_string('associer_referentiel','referentiel').'</h3>';
            if (($ok_selectionner_referentiel==0) && $ok_existe_au_moins_un_referentiel) {
                // formulaire de selection dans la liste des referentiels existants
                echo '<div align="center">
<form name="form" method="post" action="selection.php?id='.$this->cm->id.'">
<input type="hidden" name="name_instance" value="'. $this->referentiel->name.'" />
<input type="hidden" name="description_instance" value="'.htmlentities($this->referentiel->description_instance, ENT_QUOTES, 'UTF-8').'" />
<input type="hidden" name="label_domaine" value="'.$this->referentiel->label_domaine.'" />
<input type="hidden" name="label_competence" value="'.$this->referentiel->label_competence.'" />
<input type="hidden" name="label_item" value="'.$this->referentiel->label_item.'" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="'.$this->referentiel->course.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="instance"      value="'.$this->referentiel->id.'" />
<input type="hidden" name="mode"          value="add" />
<input type="submit" value="'.get_string('selectreferentiel','referentiel').'" />
</form>
</div>'."\n";
            }
            if ($ok_creer_importer_referentiel==0) {
                echo '<br /><div align="center">
<form name="form" method="post" action="import_instance.php?id='.$this->cm->id.'">
<input type="hidden" name="name_instance" value="'.$this->referentiel->name .'" />
<input type="hidden" name="description_instance" value="'.htmlentities($this->referentiel->description_instance, ENT_QUOTES, 'UTF-8').'" />
<input type="hidden" name="label_domaine" value="'.$this->referentiel->label_domaine.'" />
<input type="hidden" name="label_competence" value="'.$this->referentiel->label_competence.'" />
<input type="hidden" name="label_item" value="'.$this->referentiel->label_item.'" />
<input type="hidden" name="intro" value="'.htmlentities($this->referentiel->intro, ENT_QUOTES, 'UTF-8').'" />
<input type="hidden" name="config" value="'.$this->referentiel->config.'" />
<input type="hidden" name="config_impression" value="'.$this->referentiel->config_impression.'" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="'.$this->referentiel->course.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="instance"      value="'.$this->referentiel->id.'" />
<input type="hidden" name="mode"          value="add" />
<input type="submit" value="'.get_string('importreferentiel','referentiel').'" />
</form>
</div>'."\n";
            }
            echo $OUTPUT->box_end();
            // creer un referentiel de toutes pieces
            if ($ok_creer_importer_referentiel==0) {
				// CREATION avec formualires
                if (!REFERENTIEL_BLOCK_REF){
					$this->creation_referentiel();
				}
				else{
					echo $OUTPUT->box_start('generalbox boxaligncenter', 'creer');
        	        echo '<div align="center"><span class="surligne">'.get_string('block_ref','referentiel').'</span></div>'."\n";
			        echo $OUTPUT->box_end();
				}
            }
        }

    }


    /**
     * new referentiel_referentiel
     *
     *
     */
    function creation_referentiel(){
    // boite de saisie de la creation d'un référentiel
        global $OUTPUT;
                $nb_domaines = '1';
                $num_domaine = '1';
	            $nb_competences = '1';
                $num_competence = '1';
                $nb_item_competences = '1';
                $poids_item = '1.0';
                $empreinte_item = '1';
                $type_domaine= '0';
                $seuil_domaine='0.0';
                $type_competence= '0';
                $seuil_competence='0.0';
                $type_item = '0';
                $num_item = '1';

                echo $OUTPUT->box_start('generalbox boxaligncenter', 'creer');
                echo '
<br />
<h3 align="center">'. get_string('creer_nouveau_referentiel','referentiel')  .'</h3>
<form name="form" method="post" action="add.php?id='.$this->cm->id.'&amp;sesskey='.sesskey() .'">
<table class="saisie">
<tr valign="top">
    <td class="saisie" align="right"><b>'.get_string('name','referentiel')   .':</b></td>
    <td class="saisie" align="left">
        <input type="text" name="name" size="60" maxlength="80" value="" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie" align="right"><b>'.get_string('code','referentiel')  .':</b></td>
    <td class="saisie" align="left">
        <input type="text" name="code_referentiel" size="20" maxlength="20" value="" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie" align="right"><b>'.get_string('pass_referentiel','referentiel').'</b> : <br /><span class="small"><i>'.get_string('aide_pass_referentiel','referentiel').'</i></span>' .'</td>
    <td class="saisie" align="left">
        <input type="password" name="pass_referentiel" size="20" maxlength="20" value="" />

    </td>
</tr>
<tr valign="top">
    <td class="saisie" align="right"><b>'.get_string('description','referentiel')  .':</b></td>
    <td class="saisie" align="left">
		<textarea cols="60" rows="5" name="description_referentiel"></textarea>
    </td>
</tr>
<tr valign="top">
    <td class="saisie" align="right"><b>'.get_string('url','referentiel')  .':</b></td>
    <td class="saisie" align="left">
        <input type="text" name="url_referentiel" size="60" maxlength="255" value="" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie" align="right"><b>'.get_string('seuil_certificat','referentiel')  .':</b></td>
    <td class="saisie" align="left">
        <input type="text" name="seuil_certificat" size="5" maxlength="10" value="0" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie" align="right"><b>'.get_string('referentiel_global','referentiel')  .':</b></td>
    <td class="saisie" align="left">
<input type="radio" name="local" value="0" checked="checked" />'. get_string("yes").'
<input type="radio" name="local" value="1" />'. get_string("no").'
</td>
</tr>
<tr valign="top">
    <td class="saisie" align="right"><b>'.get_string('nombre_domaines_supplementaires','referentiel')  .':</b></td>
    <td class="saisie" align="left">
        <input type="text" name="nb_domaines" size="2" maxlength="2" value="'.$nb_domaines.'" />
    </td>
</tr>
</table>



<!-- DOMAINE -->
<h3 align="center">'.get_string('creation_domaine','referentiel')  .'</h3>
<table class="saisie_domaine">
<tr valign="top">
    <td class="saisie_domaine" align="left" colspan="4"><i>'.get_string('domaine','referentiel')  .'</i></td>
</tr>

<tr valign="top">
    <td class="saisie_domaine" align="right"><b>'.get_string('code','referentiel')  .':</b></td>
    <td class="saisie_domaine" align="left" colspan="3">
        <input type="text" name="code_domaine" size="20" maxlength="20" value="" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie_domaine" align="right"><b>'.get_string('description','referentiel')  .':</b></td>
    <td class="saisie_domaine" align="left" colspan="3">
		<textarea cols="60" rows="5" name="description_domaine"></textarea>
    </td>
</tr>
<tr valign="top">
    <td class="saisie_domaine" align="right"><b>'.get_string('type_domaine','referentiel').':</b></td>
    <td class="saisie_domaine" align="left">
';
    // MODIF JF 2012/02/20
    if (!empty($type_domaine)){
        echo get_string('yes'). ' <input type="radio" name="type_domaine" id="type_domaine" value="1" checked="checked" />'."\n";
        echo get_string('no'). ' <input type="radio" name="type_domaine" id="type_domaine" value="0" />'."\n";
    }
    else{
        echo get_string('yes'). ' <input type="radio" name="type_domaine" id="type_domaine" value="1" />'."\n";
        echo get_string('no'). ' <input type="radio" name="type_domaine" id="type_domaine" value="0" checked="checked" />'."\n";
    }
    echo '
    </td>
    <td class="saisie_domaine" align="left"><b>'.get_string('seuil_domaine','referentiel').':</b> </td>
    <td class="saisie_domaine" align="left">
';
    // MODIF JF 2012/02/20
    echo '<input type="text" name="seuil_domaine" size="5" maxlength="10" value="'.s($seuil_domaine).'" />'."\n";
    echo '</td>
</tr>

<tr valign="top">
    <td class="saisie_domaine" align="right"><b>'.get_string('numero','referentiel')  .':</b></td>
    <td class="saisie_domaine" align="left" colspan="3">
        <input type="text" name="num_domaine" size="2" maxlength="2" value="'. $num_domaine  .'" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie_domaine" align="right"><b><i>'.get_string('nombre_competences_supplementaires','referentiel')  .'</i></b>:</td>
    <td class="saisie_domaine" align="left" colspan="3">
        <input type="text" name="nb_competences" size="2" maxlength="2" value="'.   $nb_competences  .'" />
    </td>
</tr>

</table>
';


    echo ' <!-- COMPETENCE -->
<h3 align="center">'.get_string('creation_competence','referentiel')  .'</h3>
<table class="saisie_competence">

<tr valign="top">
    <td class="saisie_competence" align="left" colspan="4"><i>'.get_string('competence','referentiel')  .'</i></td>
</tr>
<tr valign="top">
    <td class="saisie_competence" align="right"><b>'.get_string('code','referentiel')  .':</b></td>
    <td class="saisie_competence" align="left" colspan="3">
        <input type="text" name="code_competence" size="20" maxlength="20" value="" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie_competence" align="right"><b>'.get_string('description','referentiel')  .':</b></td>
    <td class="saisie_competence" align="left" colspan="3">
		<textarea cols="60" rows="5" name="description_competence"></textarea>
    </td>
</tr>
';
    echo '<tr valign="top">
    <td class="saisie_competence" align="right"><b>'.get_string('type_competence','referentiel').':</b></td>
    <td class="saisie_competence" align="left">
';

    // MODIF JF 2012/02/20
    if (!empty($type_competence)){
        echo get_string('yes'). ' <input type="radio" name="type_competence" id="type_competence" value="1" checked="checked" />'."\n";
        echo get_string('no'). ' <input type="radio" name="type_competence" id="type_competence" value="0" />'."\n";
    }
    else {
        echo get_string('yes'). ' <input type="radio" name="type_competence" id="type_competence" value="1" />'."\n";
        echo get_string('no'). ' <input type="radio" name="type_competence" id="type_competence" value="0" checked="checked" />'."\n";
    }
    echo '</td>
    <td class="saisie_competence" align="left"><b>'.get_string('seuil_competence','referentiel').':</b> </td>
    <td class="saisie_competence" align="left">
';
    // MODIF JF 2012/02/20
    echo '<input type="text" name="seuil_competence" size="5" maxlength="10" value="'.s($seuil_competence).'" />'."\n";
    echo '
    </td>
</tr>

<tr valign="top">
    <td class="saisie_competence" align="right"><b>'.get_string('numero','referentiel')  .':</b></td>
    <td class="saisie_competence" align="left" colspan="3">
        <input type="text" name="num_competence" size="2" maxlength="2" value="'.  $num_competence  .'" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie_competence" align="right"><b><i>'.get_string('nombre_item_competences_supplementaires','referentiel')  .'</i></b>:</td>
    <td class="saisie_competence" align="left" colspan="3">
        <input type="text" name="nb_item_competences" size="2" maxlength="2" value="'.  $nb_item_competences  .'" />
    </td>
</tr>

</table>

<!-- ITEM -->
<h3 align="center">'.get_string('creation_item','referentiel')  .'</h3>

<table class="saisie_item">

<tr valign="top">
    <td class="saisie_item" align="left" colspan="2"><i>'.get_string('item','referentiel')  .'</i></td>
</tr>

<tr valign="top">
    <td class="saisie_item" align="right"><b>'.get_string('code','referentiel')  .':</b></td>
    <td class="saisie_item" align="left">
        <input type="text" name="code_item" size="20" maxlength="20" value="" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie_item" align="right"><b>'.get_string('description','referentiel')  .':</b></td>
    <td class="saisie_item" align="left">
		<textarea cols="60" rows="5" name="description_item"></textarea>
    </td>
</tr>
<tr valign="top">
    <td class="saisie_item" align="right"><b>'.get_string('type_item','referentiel')  .':</b></td>
    <td class="saisie_item" align="left">
';
    // MODIF JF 2012/02/20
    if (!empty($type_item)){
        echo get_string('yes'). ' <input type="radio" name="type_item" id="type_item" value="1" checked="checked" />'."\n";
        echo get_string('no'). ' <input type="radio" name="type_item" id="type_item" value="0" />'."\n";
    }
    else{
        echo get_string('yes'). ' <input type="radio" name="type_item" id="type_item" value="1" />'."\n";
        echo get_string('no'). ' <input type="radio" name="type_item" id="type_item" value="0" checked="checked" />'."\n";
    }
    echo '</td>
</tr>
<tr valign="top">
    <td class="saisie_item" align="right"><b>'.get_string('poids_item','referentiel')  .':</b></td>
    <td class="saisie_item" align="left">
        <input type="text" name="poids_item" size="5" maxlength="10" value="'. $poids_item  .'" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie_item" align="right"><b>'.get_string('empreinte_item','referentiel')  .':</b></td>
    <td class="saisie_item" align="left">
        <input type="text" name="empreinte_item" size="3" maxlength="3" value="'.   $empreinte_item  .'" />
    </td>
</tr>
<tr valign="top">
    <td class="saisie_item" align="right"><b>'.get_string('numero','referentiel')  .':</b></td>
    <td class="saisie_item" align="left">
        <input type="text" name="num_item" size="2" maxlength="2" value="'.   $num_item  .'" />
    </td>
</tr>

</table>
<br />

<input type="hidden" name="action" value="modifierreferentiel" />
<input type="hidden" name="mail_auteur_referentiel" value="" />
<input type="hidden" name="old_pass_referentiel" value="" />
<input type="hidden" name="cle_referentiel" value="" />

<input type="hidden" name="liste_codes_competence" value="" />
<input type="hidden" name="liste_empreintes_competence" value="" />
<input type="hidden" name="liste_poids_competence" value="" />
<input type="hidden" name="logo_referentiel" value="" />

<!-- instance -->
<input type="hidden" name="instance" value="'.$this->referentiel->id.'" />
<input type="hidden" name="name_instance" value="'.$this->referentiel->name.'" />
<input type="hidden" name="description_instance" value="'.htmlentities($this->referentiel->description_instance, ENT_QUOTES, 'UTF-8').'" />
<input type="hidden" name="label_domaine" value="'.$this->referentiel->label_domaine.'" />
<input type="hidden" name="label_competence" value="'.$this->referentiel->label_competence.'" />
<input type="hidden" name="label_item" value="'.$this->referentiel->label_item.'" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="'.  $this->course->id  .'" />
<input type="hidden" name="sesskey"     value="'.  sesskey() .'" />
<input type="hidden" name="mode"          value="add" />
<input type="submit" value="'.get_string("savechanges")  .'" />
<input type="submit" name="cancel" value="'.get_string("quit", "referentiel")  .'" />
</form>'."\n";
        echo $OUTPUT->box_end();
    }

     /**
     * Returns a link with info about the state of the referentiel submissions
     *
     * This is used by view_header to put this link at the top right of the page.
     * For teachers it gives the number of submitted activities declaration with a link
     * For students it gives the time of their declarations.
     *
     * @global object
     * @global object
     * @param bool $allgroup print all groups info if user can access all groups, suitable for index.php
     * @return string
     */
    function submittedlink($allgroups=false) {
        global $USER;
        global $CFG;

        $submitted = '';

       /*******************

        // A REPRENDRE
                        $urlbase = "{$CFG->wwwroot}/mod/referentiel/";

        ////if ($CFG->version < 2011120100) {
            $this->context = context_module::instance($this->cm->id);
        //} else {
        //    //  $this->context = context_module::instance($this->cm); 
        //}

        if (has_capability('mod/referentiel:grade', $this->context)) {
            if ($allgroups and has_capability('moodle/site:accessallgroups', $this->context)) {
                $group = 0;
            }
            else {
                $group = groups_get_activity_group($this->cm);
            }
            if ($count = $this->count_real_submissions($group)) {
                $submitted = '<a href="'.$urlbase.'submissions.php?id='.$this->cm->id.'">'.
                             get_string('viewsubmissions', 'referentiel', $count).'</a>';
            }
            else {
                $submitted = '<a href="'.$urlbase.'submissions.php?id='.$this->cm->id.'">'.
                             get_string('noattempts', 'referentiel').'</a>';
            }
        }
        else {
            if (isloggedin()) {
                if ($submission = $this->get_submission($USER->id)) {
                    if ($submission->timemodified) {
                        if ($submission->timemodified <= $this->referentiel->timedue || empty($this->referentiel->timedue)) {
                            $submitted = '<span class="early">'.userdate($submission->timemodified).'</span>';
                        } $
                        else {
                            $submitted = '<span class="late">'.userdate($submission->timemodified).'</span>';
                        }
                    }
                }
            }
        }
        **************/
        return $submitted;
    }



    /**
     * Create a new referentiel activity
     *
     * Given an object containing all the necessary data,
     * (defined by the form in mod_form.php) this function
     * will create a new instance and return the id number
     * of the new instance.
     * The due data is added to the calendar
     *
     * @global object
     * @global object
     * @param object $referentiel The data from the form on mod_form.php
     * @return int The id of the referentiel instance
     */
    function add_instance($referentiel) {
        global $COURSE, $DB;

        $referentiel->date_instance = time();
        $referentiel->id = $referentiel->instance;
        $referentiel->description_instance=$referentiel->intro;
        // $referentiel->introformat = 1;

        $returnid = $DB->insert_record("referentiel", $referentiel);
        $referentiel->id = $returnid;

        if ($referentiel->id) {
            $event = new stdClass();
            $event->name        = $referentiel->name;
            $event->description = $referentiel->description_instance; // format_module_intro('referentiel', $referentiel, $referentiel->coursemodule);
            $event->courseid    = $referentiel->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'referentiel';
            $event->instance    = $returnid;
            $event->eventtype   = 'due';
            $event->timestart   = $referentiel->date_instance;
            $event->timeduration = 0;

            calendar_event::create($event);
        }
        // pas de notation de cette activite
        // referentiel_grade_item_update($referentiel);

        return $returnid;
    }

    /**
     * Deletes a referentiel instance activity
     *
     * Deletes all database records, files and calendar events for this referentiel instance.
     *
     * @global object  $CFG
     * @global object  $DB
     * @param object $referentiel The referentiel to be purged
     * @param boolean purge : if true referentiel instance is deleted too
     * @return boolean False indicates error
     */
    function delete_instance($referentiel, $purge=true) {
		global $CFG, $DB;

        $result = true;

        // now get rid of all files
        $fs = get_file_storage();
        if ($cm = get_coursemodule_from_instance('referentiel', $referentiel->id)) {
            $context = context_module::instance($cm->id);
            $fs->delete_area_files($context->id);
        }

        // suppression des activites associees
        $activites=referentiel_get_activites_instance($referentiel->id);
        if ($activites){
            foreach ($activites as $activite){
                referentiel_delete_activity_record($activite->id);
            }
        }
        // suppression des taches associees
        $taches=referentiel_get_tasks_instance($referentiel->id);
        if ($taches){
            foreach ($taches as $tache){
                referentiel_delete_task_record($tache->id);
            }
        }

        // suppression des accompagnements
        $accompagnements=referentiel_get_accompagnements($referentiel->id);
        if ($accompagnements){
            foreach ($accompagnements as $accompagnement){
                referentiel_delete_accompagnement_record($accompagnement->id);
            }
        }

        // recalcul des certificats associes
        $certificats=referentiel_get_certificats($referentiel->ref_referentiel);
        if ($certificats){
            foreach ($certificats as $certificat){
                referentiel_recalcule_certificat($certificat);
            }
        }

        // suppression des evenements du calendrier
        if (! $DB->delete_records('event', array('modulename'=>'referentiel', 'instance'=>$referentiel->id))) {
            $result = false;
        }

        if ($purge){  // on supprime aussi l'instance
			if (! $DB->delete_records('referentiel', array('id'=>$referentiel->id))) {
            	$result = false;
        	}
		}
		else{    // l'instance est conservee après avoir ete videe ; utile pour la reinitialisation du cours
            $result = true;
		}

        // $mod = $DB->get_field('modules','id',array('name'=>'referentiel'));
        // referentiel_grade_item_delete($referentiel);   // existe pas

        return $result;
    }

    /**
     * Updates a new referentiel activity
     *
     * Given an object containing all the necessary data,
     * (defined by the form in mod_form.php) this function
     * will update the referentiel instance and return the id number
     * The due date is updated in the calendar
     *
     * @global object
     * @global object
     * @param object $referentiel The data from the form on mod_form.php
     * @return bool success
     */
    function update_instance($referentiel) {
        global $COURSE, $DB;

        $referentiel->date_instance = time();
        $referentiel->id = $referentiel->instance;
        // $referentiel->intro = $referentiel->description_instance;
        $referentiel->description_instance=$referentiel->intro;
        // $referentiel->introformat = 1;
        $referentiel->config=$this->initialise_configuration($referentiel,'config');
        $referentiel->config_impression=$this->initialise_configuration($referentiel,'config_impression');

        $DB->update_record('referentiel', $referentiel);

        if ($referentiel->date_instance) {
            $event = new stdClass();

            if ($event->id = $DB->get_field('event', 'id', array('modulename'=>'referentiel', 'instance'=>$referentiel->id))) {

                $event->name        = $referentiel->name;
                $event->description = $referentiel->description_instance; // format_module_intro('referentiel', $referentiel, $referentiel->coursemodule);
                $event->timestart   = $referentiel->date_instance;

                $calendarevent = calendar_event::load($event->id);
                $calendarevent->update($event);
            }
            else {
                $event = new stdClass();
                $event->name        = $referentiel->name;
                $event->description = $referentiel->description_instance; // format_module_intro('referentiel', $referentiel, $referentiel->coursemodule);
                $event->courseid    = $referentiel->course;
                $event->groupid     = 0;
                $event->userid      = 0;
                $event->modulename  = 'referentiel';
                $event->instance    = $referentiel->id;
                $event->eventtype   = 'due';
                $event->timestart   = $referentiel->date_instance;
                $event->timeduration = 0;

                calendar_event::create($event);
            }
        }
        else {
            $DB->delete_records('event', array('modulename'=>'referentiel', 'instance'=>$referentiel->id));
        }

        // get existing grade item
        // referentiel_grade_item_update($referentiel);

        return true;
    }

// CONFIGURATION
// ---------------------------------
function get_vecteur_config_occurrence() {
// retourne la valeur de configuration globale pour ce referentiel
global $DB;
	if (!empty($ref_referentiel_referentiel)){
		$config = new object();
		$params= array("refid" => "$this->referentiel_referentiel");
		$sql="SELECT config FROM {referentiel_referentiel} WHERE id=:refid";
		$config = $DB->get_record_sql($sql, $params);
		if ($config){
			return($config->config);
		}
	}
	return '';
}


// ---------------------------------
function get_vecteur_config_imp_occurrence() {
// retourne la valeur de configuration globale pour ce referentiel
global $DB;
	if (!empty($this->referentiel_referentiel)){
		$config = new object();
		$params= array("refid" => $this->referentiel_referentiel->id);
        $sql="SELECT config_impression FROM {referentiel_referentiel} WHERE id=:refid";
        $config = $DB->get_record_sql($sql, $params);
		if ($config){
			return($config->config_impression);
		}
	}
	return '';
}

// ---------------------------------
function get_vecteur_config_instance() {
// retourne la valeur de configuration locale pour cette instance de referentiel
global $DB;
	if (!empty($this->referentiel)){
		$config = new object();
		$params= array("refid" => $this->referentiel->id);
        $sql="SELECT config FROM {referentiel} WHERE id=:refid";
		$config = $DB->get_record_sql($sql, $params);
		if ($config){
			return($config->config);
		}
	}
	return '';
}

// ---------------------------------
function get_vecteur_config_imp_instance() {
// retourne la valeur de configuration locale pour ce referentiel
global $DB;
	if (!empty($this->referentiel)){
		$config = new object();
		$params= array("refid" => $this->referentiel->id);
        $sql="SELECT config_impression FROM {referentiel} WHERE id=:refid";
        $config = $DB->get_record_sql($sql, $params);
		if ($config){
			return($config->config_impression);
		}
	}
	return '';
}

// ---------------------------------
function get_item_config_occurrence($item, $type='config') {
// retourne la valeur de configuration globale (au niveau du referentiel) pour l'item considere
// 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
// type : config ou config_impression
global $CFG;
	if (!empty($this->referentiel_referentiel)){
		if ($type=='config'){
			$str_config = $this->get_vecteur_config_occurrence();
		}
		else{
			$str_config = $this->get_vecteur_config_imp_occurrence();
		}
		if ($str_config!=''){
			$tconfig=explode(';',$str_config);
			$n=count($tconfig);
			if ($n>0){
				$i=0;
				while ($i<$n){
					$tconfig[$i]=trim($tconfig[$i]);
					if ($tconfig[$i]!=''){
						list($cle, $val)=explode(':',$tconfig[$i]);
						$cle=trim($cle);
						$val=trim($val);

						if ($cle==$item){
							return ($val);
						}
					}
					$i++;
				}
			}
		}
	}
	return 0;
}

// ---------------------------------
function get_item_config_instance($item, $type='config') {
// retourne la valeur de configuration locale (au niveau de l'instance de referentiel) pour l'item considere
// 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
// type : config ou config_impression
global $CFG;
	if (!empty($this->referentiel)){
		if ($type=='config'){
			$str_config = $this->get_vecteur_config_instance();
		}
		else{
			$str_config = $this->get_vecteur_config_imp_instance();
		}
		if ($str_config!=''){
			$tconfig=explode(';',$str_config);
			$n=count($tconfig);
			if ($n>0){
				$i=0;
				while ($i<$n){
					$tconfig[$i]=trim($tconfig[$i]);
					if ($tconfig[$i]!=''){
						list($cle, $val)=explode(':',$tconfig[$i]);
						$cle=trim($cle);
						$val=trim($val);

						if ($cle==$item){
							return ($val);
						}
					}
					$i++;
				}
			}
		}
	}
	return 0;
}


// -----------------------
function associe_item_configuration($item){
// retourne le nom du parametre de configuration
		switch($item){
            case 'cfcertif' :  return 'referentiel_certif_config'; break; // config certification
            case 'certif' :  return 'referentiel_certif_state'; break; // certification active
            case 'hierarchy' :  return 'referentiel_hierarchy'; break; // affichage hierarchique des competences
            case 'light' :  return 'referentiel_light_display'; break; // affichage reduit du referentiel sans les poids et les empreintes
            case 'graph' :  return 'referentiel_affichage_graphique'; break;
			case 'scol' :	return 'referentiel_scolarite_masquee'; break;
			case 'creref' :	return 'referentiel_creation_limitee'; break;
			case 'selref' :	return 'referentiel_selection_autorisee'; break;
			case 'impcert' : return 'referentiel_impression_autorisee'; break;
			case 'refcert' : return 'certificat_sel_referentiel'; break;
			case 'instcert' : return 'certificat_sel_referentiel_instance'; break;
			case 'numetu' : return 'certificat_sel_etudiant_numero'; break;
			case 'nometu' : return 'certificat_sel_etudiant_nom_prenom'; break;
			case 'etabetu' : return 'certificat_sel_etudiant_etablissement'; break;
			case 'ddnetu' : return 'certificat_sel_etudiant_ddn'; break;
			case 'lieuetu' : return 'certificat_sel_etudiant_lieu_naissance'; break;
			case 'adretu' : return 'certificat_sel_etudiant_adresse'; break;
			case 'detail' : return 'certificat_sel_certificat_detail'; break;
			case 'pourcent' : return 'certificat_sel_certificat_pourcent'; break;
			case 'compdec' : return 'certificat_sel_activite_competences'; break;
			case 'compval' : return 'certificat_sel_certificat_competences'; break;
			case 'nomreferent' : return 'certificat_sel_certificat_referents'; break;
			case 'jurycert' : return 'certificat_sel_decision_jury'; break;
			case 'comcert' : return 'certificat_sel_commentaire'; break;
		}
		return '';
}

// -----------------------
function creer_configuration($type='config'){
// initialise le vecteur de configuration
global $CFG;
$s='';
	if ($type=='config'){
		// configuration
        // affichage hierarchique saisie des competence
		$s.='hierarchy:0;';

        // affichage reduit du referentiel sans les poids et les empreintes
		if (isset($CFG->referentiel_light_display)){
			$s.='light:'.$CFG->referentiel_light_display.';';
		}
		else{
			$s.='light:0;';
		}

		if (isset($CFG->referentiel_scolarite_masquee)){
			$s.='scol:'.$CFG->referentiel_scolarite_masquee.';';
		}
		else{
			$s.='scol:0;';
		}
		if (isset($CFG->referentiel_creation_limitee)){
			$s.='creref:'.$CFG->referentiel_creation_limitee.';';
		}
		else{
			$s.='creref:0;';
		}
		if (isset($CFG->referentiel_selection_autorisee)){
			$s.='selref:'.$CFG->referentiel_selection_autorisee.';';
		}
		else{
			$s.='selref:0;';
		}
		if (isset($CFG->referentiel_impression_autorisee)){
			$s.='impcert:'.$CFG->referentiel_impression_autorisee.';';
		}
		else{
			$s.='impcert:0;';
		}
		if (isset($CFG->referentiel_affichage_graphique)){
			$s.='graph:'.$CFG->referentiel_affichage_graphique.';';
		}
		else{
			$s.='graph:0;';
		}
		if (isset($CFG->referentiel_certif_config)){
			$s.='cfcertif:'.$CFG->referentiel_certif_config.';';
		}
		else{
			$s.='cfcertif:0;';
		}
		if (isset($CFG->referentiel_certif_state)){
			$s.='certif:'.$CFG->referentiel_certif_state.';';
		}
		else{
			$s.='certif:1;';
		}

	}
	else{
		// impression certificat
		// instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;

		// impression certificat
		if (isset($CFG->certificat_sel_referentiel)){
			$s.='refcert:'.$CFG->certificat_sel_referentiel.';';
		}
		else{
			$s.='refcert:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_referentiel_instance)){
			$s.='instcert:'.$CFG->certificat_sel_referentiel_instance.';';
		}
		else{
			$s.='instcert:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_numero)){
			$s.='numetu:'.$CFG->certificat_sel_etudiant_numero.';';
		}
		else{
			$s.='numetu:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_nom_prenom)){
			$s.='nometu:'.$CFG->certificat_sel_etudiant_nom_prenom.';';
		}
		else{
			$s.='nometu:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_etablissement)){
			$s.='etabetu:'.$CFG->certificat_sel_etudiant_etablissement.';';
		}
		else{
			$s.='etabetu:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_ddn)){
			$s.='ddnetu:'.$CFG->certificat_sel_etudiant_ddn.';';
		}
		else{
			$s.='ddnetu:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_lieu_naissance)){
			$s.='lieuetu:'.$CFG->certificat_sel_etudiant_lieu_naissance.';';
		}
		else{
			$s.='lieuetu:0;';
		}

				// impression certificat
		if (isset($CFG->certificat_sel_etudiant_adresse)){
			$s.='adretu:'.$CFG->certificat_sel_etudiant_adresse.';';
		}
		else{
			$s.='adretu:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_detail)){
			$s.='detail:'.$CFG->certificat_sel_certificat_detail.';';
		}
		else{
			$s.='detail:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_pourcent)){
			$s.='pourcent:'.$CFG->certificat_sel_certificat_pourcent.';';
		}
		else{
			$s.='pourcent:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_activite_competences)){
			$s.='compdec:'.$CFG->certificat_sel_activite_competences.';';
		}
		else{
			$s.='compdec:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_competences)){
			$s.='compval:'.$CFG->certificat_sel_certificat_competences.';';
		}
		else{
			$s.='compval:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_referents)){
			$s.='nomreferent:'.$CFG->certificat_sel_certificat_referents.';';
		}
		else{
			$s.='nomreferent:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_decision_jury)){
			$s.='jurycert:'.$CFG->certificat_sel_decision_jury.';';
		}
		else{
			$s.='jurycert:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_commentaire)){
			$s.='comcert:'.$CFG->certificat_sel_commentaire.';';
		}
		else{
			$s.='comcert:0;';
		}
	}
	return $s;
}


// ---------------------------------
function initialise_configuration($form, $type='config'){
// initialise le vecteur de configuration en fonction des parametres saisis dans le formulaire
// item type config = 'scol', 'creref', 'selref', 'impcert', 'graph', 'light', 'hierarchy'
// item type config_impression = 'refcert', 'instcert', 'numetu', nometu, etabetu, ddnetu, lieuetu, adretu, pourcent, compdec, compval, nomreferent, jurycert, comcert,
// Valeurs par defaut 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;
// Valeurs par defaut : refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;

$s='';
	if ($type=='config'){
		// affichage scolarite
		if (isset($form->scol)){
			$s.='scol:'.$form->scol.';';
		}
		else {
			$s.='scol:0;';
		}
		// creation referentiel
		if (isset($form->creref)){
			$s.='creref:'.$form->creref.';';
		}
		else{
			$s.='creref:0;';
		}
		// selection referentiel
		if (isset($form->selref)){
			$s.='selref:'.$form->selref.';';
		}
		else{
			$s.='selref:0;';
		}

		// impression certificat
		if (isset($form->impcert)){
			$s.='impcert:'.$form->impcert.';';
		}
		else{
			$s.='impcert:0;';
		}

		// graphique certification
		if (isset($form->graph)){
			$s.='graph:'.$form->graph.';';
		}
		else{
			$s.='graph:0;';
		}

		// affichage light  du referentiel
		if (isset($form->light)){
			$s.='light:'.$form->light.';';
		}
		else {
			$s.='light:0;';
		}
		// affichage hierarchique des competences
		if (isset($form->hierarchy)){
			$s.='hierarchy:'.$form->hierarchy.';';
		}
		else {
			$s.='hierarchy:0;';
		}
		// config certif
		if (isset($form->cfcertif)){
			$s.='cfcertif:'.$form->cfcertif.';';
		}
		else {
			$s.='cfcertif:0;';
		}
		// config certif
		if (isset($form->certif)){
			$s.='certif:'.$form->certif.';';
		}
		else {
			$s.='certif:0;';
		}

	}
	else{

		//Valeurs par defaut : refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;

		// impression certificat
		if (isset($form->refcert)){
			$s.='refcert:'.$form->refcert.';';
		}
		else{
			$s.='refcert:1;';
		}

				// impression certificat
		if (isset($form->instcert)){
			$s.='instcert:'.$form->instcert.';';
		}
		else{
			$s.='instcert:0;';
		}

		// impression certificat
		if (isset($form->numetu)){
			$s.='numetu:'.$form->numetu.';';
		}
		else{
			$s.='numetu:1;';
		}


		// impression certificat
		if (isset($form->nometu)){
			$s.='nometu:'.$form->nometu.';';
		}
		else{
			$s.='nometu:1;';
		}

		// impression certificat
		if (isset($form->etabetu)){
			$s.='etabetu:'.$form->etabetu.';';
		}
		else{
			$s.='etabetu:0;';
		}

		// impression certificat
		if (isset($form->ddnetu)){
			$s.='ddnetu:'.$form->ddnetu.';';
		}
		else{
			$s.='ddnetu:0;';
		}


		// impression certificat
		if (isset($form->lieuetu)){
			$s.='lieuetu:'.$form->lieuetu.';';
		}
		else{
			$s.='lieuetu:0;';
		}

		// impression certificat
		if (isset($form->adretu)){
			$s.='adretu:'.$form->adretu.';';
		}
		else{
			$s.='adretu:0;';
		}

		// impression certificat
		if (isset($form->detail)){
			$s.='detail:'.$form->detail.';';
		}
		else{
			$s.='detail:0;';
		}

		// impression certificat
		if (isset($form->pourcent)){
			$s.='pourcent:'.$form->pourcent.';';
		}
		else{
			$s.='pourcent:0;';
		}

		// impression certificat
		if (isset($form->compdec)){
			$s.='compdec:'.$form->compdec.';';
		}
		else{
			$s.='compdec:0;';
		}

		// impression certificat
		if (isset($form->compval)){
			$s.='compval:'.$form->compval.';';
		}
		else{
			$s.='compval:1;';
		}

		// impression certificat
		if (isset($form->nomreferent)){
			$s.='nomreferent:'.$form->nomreferent.';';
		}
		else{
			$s.='nomreferent:0;';
		}

		// impression certificat
		if (isset($form->jurycert)){
			$s.='jurycert:'.$form->jurycert.';';
		}
		else{
			$s.='jurycert:1;';
		}

		// impression certificat
		if (isset($form->comcer)){
			$s.='comcert:'.$form->comcer.';';
		}
		else{
			$s.='comcert:0;';
		}
	}
	return ($s);
}

 // -----------------------------
function affiche_config_item($cle, $val){
// item = 'scol', 'creref', 'selref', 'impcert', refcert, instcert, numetu, nometu, etabetu, ddnetu, lieuetu, adretu, pourcent, compdec, compval, referent, jurycert, comcert,
// 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
// retourne une liste de selecteurs
// $type : config ou config_impression
global $CFG;
	$s='';
					$cle=trim($cle);
					$val=trim($val);
					if ($cle!=''){
						$s.=''.get_string($cle,'referentiel').' ';
						$str_conf=$this->associe_item_configuration($cle);
						// creer le parametre si necessaire
						if (!isset($CFG->$str_conf)){
							$CFG->$str_conf=0;
						}
						if ($CFG->$str_conf==2){
							$s.= ' (<i>'.$cle.'</i>) <b>'.get_string('config_verrouillee','referentiel').'</b>'."\n";
 						}
						elseif ($val==1){
							$s.=' (<i>'.$cle.'</i>) <b>'.get_string('yes')."</b>\n";
 						}
						else {
							$s.=' (<i>'.$cle.'</i>) <b>'.get_string('no')."</b>\n";
						}
						$s.='<br />'."\n";
					}
	return $s;
}

 // -----------------------------
function affiche_config($str_config, $type='config'){
// item = 'scol', 'creref', 'selref', 'impcert', refcert, instcert, numetu, nometu, etabetu, ddnetu, lieuetu, adretu, pourcent, compdec, compval, referent, jurycert, comcert,
// 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
// retourne une liste de selecteurs
// $type : config ou config_impression
global $CFG;
	$s='';
	if ($str_config==''){
		$str_config=$this->creer_configuration($type);
	}
	// DEBUG
	// echo "<br />DEBUG :: lib.php :: 3675 ::  $str_config\n";
	if ($str_config!=''){
		$tconfig=explode(';',$str_config);
		$n=count($tconfig);
		if ($n>0){
			$i=0;
			while ($i<$n){
				$tconfig[$i]=trim($tconfig[$i]);
				if ($tconfig[$i]!=''){
					list($cle, $val)=explode(':',$tconfig[$i]);
					$cle=trim($cle);
					$val=trim($val);
					if ($cle!=''){

						$s.=''.get_string($cle,'referentiel').' ';
						$str_conf=$this->associe_item_configuration($cle);
						// creer le parametre si necessaire
						if (!isset($CFG->$str_conf)){
							$CFG->$str_conf=0;
						}
						if ($CFG->$str_conf==2){
							$s.= ' (<i>'.$cle.'</i>) <b>'.get_string('config_verrouillee','referentiel').'</b>'."\n";
 						}
						elseif ($val==1){
							$s.=' (<i>'.$cle.'</i>) <b>'.get_string('yes')."</b>\n";
 						}
						else {
							$s.=' (<i>'.$cle.'</i>) <b>'.get_string('no')."</b>\n";
						}
						$s.='<br />'."\n";
					}
				}
				$i++;
			}
		}
	}
	return $s;
}

    /**
     * @todo Document this function
     * a partir d'une chaine d'item de configuration affiche les boites de saisie
     *
     */
     // -----------------------------
    function select_config_item(&$mform, $cle, $val){
    // item = 'scol', 'creref', 'selref', 'impcert', refcert, instcert, numetu, nometu, etabetu, ddnetu, lieuetu, adretu, pourcent, compdec, compval, referent, jurycert, comcert,
    // 'scol:0;creref:0;selref:0;impcert:0;graph:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
    // retourne une liste de selecteurs
    // $type : config ou config_impression
    global $CFG;
		if (!empty($cle) && isset($val)) {

			//echo "<br />CLE: $cle\n";
            //echo "<br />VAL: $val\n";
            $str_conf=$this->associe_item_configuration($cle);
			//echo "<br />STR_CONF:$str_conf\n";
            //echo "<br />CFG-&gt;STR_CONF:".$CFG->$str_conf."\n";
			if (!empty($str_conf)){
				// creer le parametre si necessaire
				if (!isset($CFG->$str_conf)){
					$CFG->$str_conf=0;
				}
				if ($CFG->$str_conf==2){
					// $s.= '<input type="hidden" name="'.$cle.'" value="2" /> <b>'.get_string('config_verrouillee','referentiel').'</b>'."\n";
	                $mform->addElement('hidden', $cle, 2);
    	            $mform->setType($cle, PARAM_INT);
        	        $mform->setDefault($cle, 2);
 				}
				else{
    	        	$radioarray=array();
        	        if ($cle=='cfcertif'){
					}
					else{
    	        		$radioarray[] = & $mform->createElement('radio', $cle, '', get_string('no'), 0, $cle);
            	    	$radioarray[] = & $mform->createElement('radio', $cle, '', get_string('yes'), 1, $cle);
                		if ($val==1){
                			$mform->setDefault($cle, 1);
		     			}
						else {
        		    		$mform->setDefault($cle, 0);
            			}
					}
					$mform->addGroup($radioarray, 'radioar'.$cle, get_string($cle, 'referentiel'), array(' '), false);
				}
			}
    	}
	}
    /**
     * @todo Document this function
     * a partir d'une chaine de configuration affiche les boites de saisie
     *
     */
     // -----------------------------
    function selection_configuration(&$mform, $str_config, $type='config'){
    // item = 'scol', 'creref', 'selref', 'impcert', refcert, instcert, numetu, nometu, etabetu, ddnetu, lieuetu, adretu, pourcent, compdec, compval, referent, jurycert, comcert,
    // 'scol:0;creref:0;selref:0;impcert:0;graph:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
    // retourne une liste de selecteurs
    // $type : config ou config_impression
    global $CFG;

		if ($str_config==''){
            $str_config=$this->creer_configuration($type);
		}
		// DEBUG
		//echo "<br>$str_config\n";

		if ($str_config!=''){
			$tconfig=explode(';',$str_config);
			$n=count($tconfig);
			if ($n>0){
				$i=0;
				while ($i<$n){
					$tconfig[$i]=trim($tconfig[$i]);
					if ($tconfig[$i]!=''){
						list($cle, $val)=explode(':',$tconfig[$i]);
						$cle=trim($cle);
						$val=trim($val);
                        // echo "<br>$cle =&gt; $val\n";
						if (($cle!='') && ($cle!='cfcertif')){
							$str_conf=$this->associe_item_configuration($cle);
                            //echo "<br>$str_conf &gt;> ".$CFG->$str_conf."\n";
							// creer le parametre si necessaire
							if (!isset($CFG->$str_conf)){
								$CFG->$str_conf=0;
							}
							if ($CFG->$str_conf==2){
								// $s.= '<input type="hidden" name="'.$cle.'" value="2" /> <b>'.get_string('config_verrouillee','referentiel').'</b>'."\n";
                            	$mform->addElement('hidden', $cle, 2);
                            	$mform->setType($cle, PARAM_INT);
                            	$mform->setDefault($cle, 2);
 							}
							else{
                            	$radioarray=array();

   	                        	$radioarray[] = &$mform->createElement('radio', $cle, '', get_string('no'), 0, $cle);
       	                    	$radioarray[] = &$mform->createElement('radio', $cle, '', get_string('yes'), 1, $cle);
           	                	if ($val==1){
               	                	$mform->setDefault($cle, 1);
     							}
							    else {
       	                	        $mform->setDefault($cle, 0);
           	                	}
							}
			            	$mform->addGroup($radioarray, 'radioar'.$cle, get_string($cle, 'referentiel'), array(' '), false);
						}
					}
    				$i++;
				}
			}
	    }
    }

// -----------------------
function instance_get_occurrence($instanceid){
// retourne l'id de l'occurrence de referentiel associée à une instance
    global $DB;
    if ($instanceid){
        $params= array("refid" => "$instanceid");
        $sql="SELECT ref_referentiel FROM {referentiel} WHERE id=:refid";
		$instance=$DB->get_record_sql($sql, $params);
		if ($instance){
            return $instance->ref_referentiel;
        }
    }
    return 0;
}


// -----------------------
function can_config() {
// examine en cascade la configuration au niveau du site et de l'occurrence
// verifier si autorisation de modification de la configuration au niveau de l'instance
    global $CFG;

    $vecteur_config= new Object();
	$vecteur_config->config_hierarchy=0;
	$vecteur_config->config_scol=0;
    $vecteur_config->config_light=0;
    $vecteur_config->config_creref=0;
    $vecteur_config->config_selref=0;
    $vecteur_config->config_impcert=0;
    $vecteur_config->config_graph=0;
    $vecteur_config->config_cfcertif=0;


       	// configuration hierarchy
    if (!isset($CFG->referentiel_hierarchy)){
		$CFG->referentiel_hierarchy=0;
   	}
	// configuration creation referentiel
   	if (!isset($CFG->referentiel_scolarite_masquee)){
		$CFG->referentiel_scolarite_masquee=0;
	}
   	// configuration light display
    if (!isset($CFG->referentiel_light_display)){
		$CFG->referentiel_light_display=0;
    }
	// configuration creation referentiel
   	if (!isset($CFG->referentiel_creation_limitee)){
		$CFG->referentiel_creation_limitee=0;
	}
	// configuration selection referentiel
    if (!isset($CFG->referentiel_selection_autorisee)){
		$CFG->referentiel_selection_autorisee=0;
	}
	// configuration affichage graphique
   	if (!isset($CFG->referentiel_affichage_graphique)){
		$CFG->referentiel_affichage_graphique=0;
	}
   	// configuration affichage certif
    if (!isset($CFG->referentiel_certif_config)){
		$CFG->referentiel_certif_config=0;
   	}
   	// configuration impression certif autorise
    if (!isset($CFG->referentiel_impression_autorisee)){
		$CFG->referentiel_impression_autorisee=0;
   	}

	if (!empty($this->referentiel_referentiel->id)){

		if ($CFG->referentiel_hierarchy!=2) {
        // verifier valeur au niveau de l'occurrence
			if ($this->get_item_config_occurrence('hierarchy', 'config')==0){
            	// configuration possible au niveau de l'instance
                $vecteur_config->config_hierarchy=1;
            }
        }

		if ($CFG->referentiel_scolarite_masquee!=2){
        	// verifier valeur au niveau de l'occurrence
            if ($this->get_item_config_occurrence('scol', 'config')==0){
                // configuration possible au niveau de l'instance
                $vecteur_config->config_scol=1;
            }
        }
		if ($CFG->referentiel_creation_limitee!=2){
        	// verifier valeur au niveau de l'occurrence
            if ($this->get_item_config_occurrence('creref', 'config')==0){
                // configuration possible au niveau de l'instance
                $vecteur_config->config_creref=1;
            }
        }
		if ($CFG->referentiel_selection_autorisee!=2) {
        // verifier valeur au niveau de l'occurrence
            if ($this->get_item_config_occurrence('selref', 'config')==0){
                // configuration possible au niveau de l'instance
                $vecteur_config->config_selref=1;
            }
        }
		if ($CFG->referentiel_impression_autorisee!=2) {
        // verifier valeur au niveau de l'occurrence
            if ($this->get_item_config_occurrence('impcert', 'config')==0){
                // configuration possible au niveau de l'instance
                $vecteur_config->config_impcert=1;
            }
        }

		if ($CFG->referentiel_affichage_graphique!=2) {
        // verifier valeur au niveau de l'occurrence
            if ($this->get_item_config_occurrence('graph', 'config')==0){
            	// configuration possible au niveau de l'instance
                $vecteur_config->config_graph=1;
            }
        }

		if ($CFG->referentiel_light_display!=2) {
        // verifier valeur au niveau de l'occurrence
            if ($this->get_item_config_occurrence('light', 'config')==0){
            	// configuration possible au niveau de l'instance
                $vecteur_config->config_light=1;
            }
        }
		if ($CFG->referentiel_certif_config!=2) {
        // verifier valeur au niveau de l'occurrence
			if ($this->get_item_config_occurrence('cfcertif', 'config')==0){
            	// configuration possible au niveau de l'instance
                $vecteur_config->config_cfcertif=1;
            }
        }
	}
    return  $vecteur_config;
}

    /**
     * @todo Document this function
     */
    function config_instance(&$mform) {
    global $CFG;

 		$vecteur_config=$this->can_config();

        if (!empty($vecteur_config)){
				// DEBUG
				//echo "<br />DEBUG :: class/referentiel.class.php :: 2275 :: VECTEUR CONFIG :: <br />\n";
				//print_object($vecteur_config);
                //echo "<br />\n";
				// exit;
                $val=$this->get_item_config_instance('hierarchy', 'config');
				if (!empty($vecteur_config->config_hierarchy)){  // on peut configurer
                    $this->select_config_item($mform, 'hierarchy', $val);
				}
				else{  // on ne peut pas configurer
	          		$mform->addElement('html', get_string('referentiel_no_config_local','referentiel').' "<i>'. get_string('hierarchy', 'referentiel').'</i>"<br />');
                    $mform->addElement('html', $this->affiche_config_item('hierarchy', $val));
	            	$mform->addElement('hidden', 'hierarchy', $val);
    	        	$mform->setType('hierarchy', PARAM_INT);
        	    	$mform->setDefault('hierarchy', $val);
				}

				$val=$this->get_item_config_instance('light', 'config');
				if (!empty($vecteur_config->config_light)){  // on peut configurer
					$this->select_config_item($mform, 'light', $val);
				}
				else{  // on ne peut pas configurer
	          		$mform->addElement('html', get_string('referentiel_no_config_local','referentiel').' "<i>'. get_string('light', 'referentiel').'</i>"<br />');
					$mform->addElement('html', $this->affiche_config_item('light', $val));
	            	$mform->addElement('hidden', 'light', $val);
    	        	$mform->setType('light', PARAM_INT);
        	    	$mform->setDefault('light', $val);
				}

				$val=$this->get_item_config_instance('scol', 'config');
				if (!empty($vecteur_config->config_scol)){  // on peut configurer
					$this->select_config_item($mform, 'scol', $val);
				}
				else{  // on ne peut pas configurer
	          		$mform->addElement('html', get_string('referentiel_no_config_local','referentiel').' "<i>'. get_string('scol', 'referentiel').'</i>"<br />');
					$mform->addElement('html', $this->affiche_config_item('scol', $val));
	            	$mform->addElement('hidden', 'scol', $val);
    	        	$mform->setType('scol', PARAM_INT);
        	    	$mform->setDefault('scol', $val);
				}

                $val=$this->get_item_config_instance('creref', 'config');
				if (!empty($vecteur_config->config_creref)){  // on peut configurer la creation de referentiel au niveau de l'instance
                	$this->select_config_item($mform, 'creref', $val);
				}
				else{  // on ne peut pas configurer la creation de referentiel au niveau de l'instance
	          		$mform->addElement('html', get_string('referentiel_no_config_local','referentiel').' "<i>'. get_string('creref', 'referentiel').'</i>"<br />');
                    $mform->addElement('html', $this->affiche_config_item('creref', $val));
	            	$mform->addElement('hidden', 'creref', $val);
    	        	$mform->setType('creref', PARAM_INT);
        	    	$mform->setDefault('creref', $val);
          		}
				$val=$this->get_item_config_instance('selref', 'config');
				if (!empty($vecteur_config->config_selref)){  // on peut configurer
					$this->select_config_item($mform, 'selref', $val);
				}
				else{  // on ne peut pas configurer
	          		$mform->addElement('html', get_string('referentiel_no_config_local','referentiel').' "<i>'. get_string('selref', 'referentiel').'</i>"<br />');
                    $mform->addElement('html', $this->affiche_config_item('selref', $val));
	            	$mform->addElement('hidden', 'selref', $val);
    	        	$mform->setType('selref', PARAM_INT);
        	    	$mform->setDefault('selref', $val);
				}

				// Impression des certificats
                $val=$this->get_item_config_instance('impcert', 'config');
				if (!empty($vecteur_config->config_impcert)){  // on peut configurer
                    $this->select_config_item($mform, 'impcert', $val);
				}
				else{  // on ne peut pas configurer
	          		$mform->addElement('html', get_string('referentiel_no_config_local','referentiel').' "<i>'. get_string('impcert', 'referentiel').'</i>"<br />');
                    $mform->addElement('html', $this->affiche_config_item('impcert', $val));
	            	$mform->addElement('hidden', 'impcert', $val);
    	        	$mform->setType('impcert', PARAM_INT);
        	    	$mform->setDefault('impcert', $val);
				}

				$val=$this->get_item_config_instance('graph', 'config');
				if (!empty($vecteur_config->config_graph)){  // on peut configurer
					$this->select_config_item($mform, 'graph', $val);
				}
				else{  // on ne peut pas configurer
	          		$mform->addElement('html', get_string('referentiel_no_config_local','referentiel').' "<i>'. get_string('graph', 'referentiel').'</i>"<br />');
					$mform->addElement('html', $this->affiche_config_item('graph', $val));
	            	$mform->addElement('hidden', 'graph', $val);
    	        	$mform->setType('graph', PARAM_INT);
        	    	$mform->setDefault('graph', $val);
				}

				// ATTENTION configuration de l'onglet 'Certificat'
                $val = $this->get_item_config_instance('certif', 'config');
				if (!empty($vecteur_config->config_certif_config)){  // on peut configurer l'affichage de l'onglet certificat
                    $this->select_config_item($mform, 'certif', $val);
				}
				else{  // on ne peut pas configurer  l'affichage de l'onglet certificat
	          		$mform->addElement('html', get_string('referentiel_no_config_local','referentiel').' "<i>'. get_string('cfcertif', 'referentiel').'</i>"<br />');
                    $mform->addElement('html', $this->affiche_config_item('certif', $val));
	            	$mform->addElement('hidden', 'certif', $val);
    	        	$mform->setType('certif', PARAM_INT);
        	    	$mform->setDefault('certif', $val);
				}

			}
	}

    /**
     * @todo Document this function
     */
    function config_impression_instance(&$mform) {
    global $CFG;
        // Impression des certificats
        if ($this->get_item_config_instance('impcert', 'config')==0){
            $this->selection_configuration($mform, $this->referentiel->config_impression, 'config_impression');
		}
		else{  // on ne peut pas configurer
	          		$mform->addElement('html', '<i>'.get_string('referentiel_no_print_certif','referentiel').' '.get_string('instance_level','referentiel').'</i>'."\n");
	          		$mform->addElement('html', '<br /><b>'.get_string('configuration_impression','referentiel')."</b>\n");
		       	    $mform->addElement('html', '<br />'.$this->affiche_config($this->referentiel->config_impression, 'config_impression'));

            		$mform->addElement('hidden', 'config_impression', $this->referentiel->config_impression);
	            	$mform->setType('config_impression', PARAM_TEXT);
    	        	$mform->setDefault('config_impression', $this->referentiel->config_impression);
		}
	}



    /**
     * @todo Document this function
     */
    function setup_elements(&$mform, $referentielinstance) {
        global $CFG, $COURSE;

        if (empty($referentielinstance->referentiel->id)){
			// paramétrage initial

		    $mform->addElement('header', 'configuration', get_string('configuration','referentiel'));
    	    $mform->addElement('html', get_string('aide_referentiel_config_local','referentiel'));
            $this->selection_configuration($mform, '', 'config');

		    $mform->addElement('header', 'configuration_impression', get_string('configuration_impression','referentiel'));
			$this->selection_configuration($mform, '', 'config_impression');
        }
        else{
			// parametrage individuel
		    $mform->addElement('header', 'configuration', get_string('configuration','referentiel'));
    	    $mform->addElement('html', get_string('aide_referentiel_config_local','referentiel'));
            $this->config_instance($mform);
		    $mform->addElement('header', 'configuration_impression', get_string('configuration_impression','referentiel'));
            $this->config_impression_instance($mform);
		}

        $course_context = context_course::instance($COURSE->id);

        // plagiarism_get_form_elements_module($mform, $course_context);
    }


    /**
     * Any preprocessing needed for the settings form for
     * this referentiel type
     *
     * @param array $default_values - array to fill in with the default values
     *      in the form 'formelement' => 'value'
     * @param object $form - the form that is to be displayed
     * @return none
     */
    function form_data_preprocessing(&$default_values, $form) {
    }

    /**
     * Any extra validation checks needed for the settings
     * form for this referentiel type
     *
     * See lib/formslib.php, 'validation' function for details
     */
    function form_validation($data, $files) {
        return array();
    }


}
// Fin de la classe
// ======================================================================



?>

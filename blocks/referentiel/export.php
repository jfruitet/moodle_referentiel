<?php
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
 * Interface.
 *
 * @package   block_referentiel
 * @copyright 2011 onwards Jean Fruitet <jean.fruitet@univ-nantes.fr>
 * @link http://www.univ-nantes.fr
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require(dirname(__FILE__) . '/../../config.php');
require_once('occurrence_class.php');
require_once('lib.php');
// require_once($CFG->dirroot.'/mod/referentiel/locallib.php');
require_once('import_export_lib.php');	// IMPORT / EXPORT


// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

// Next look for optional variables.
$occurrenceid = optional_param('occurrenceid', 0, PARAM_INT);
$mode  = optional_param('mode', 'export', PARAM_ALPHANUMEXT);    // Force the browse mode  ('list')

$exportfilename = optional_param('exportfilename','',PARAM_FILE );
$format = optional_param('format','', PARAM_FILE );

// get display strings
$txt = new object;
$txt->referentiel = get_string('referentiel','referentiel');
$txt->download = get_string('download','referentiel');
$txt->downloadextra = get_string('downloadextra','referentiel');
$txt->exporterror = get_string('exporterror','referentiel');
$txt->exportname = get_string('exportname','referentiel');
$txt->exportreferentiel = get_string('exportreferentiel', 'referentiel');
$txt->fileformat = get_string('fileformat','referentiel');
$txt->modulename = get_string('modulename','referentiel');
$txt->modulenameplural = get_string('modulenameplural','referentiel');
// $txt->tofile = get_string('tofile','referentiel');



if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_referentiel', $courseid);
}

$contextcourse = context_course::instance($course->id);
$context = context_block::instance($blockid);
$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
$viewurl = new moodle_url('/blocks/referentiel/view.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid));

require_login($course);
require_capability('mod/referentiel:export', $context);

$params=array("blockid"=>$blockid, "courseid"=>$courseid, "occurrenceid"=>$occurrenceid);
$occurrence_object = new occurrence($params);

// RECUPERER LES FORMULAIRES
if (isset($SESSION->modform)) {   // Variables are stored in the session
	$form = $SESSION->modform;
    unset($SESSION->modform);
}
else {
	$form = (object)$_POST;
}

// Suppression des fichiers d'export
if (!empty($form->deletefile) && confirm_sesskey()){
	foreach ($form->deletefile as $fullpathfile) {
		if ($fullpathfile){
			// echo "<br />DEBUG :: archive.php :: 252<br />\n";
			// echo "<br />$fullpathfile\n";
			referentiel_delete_a_file($fullpathfile);
		}
	}
	unset($form);
}




$currenttab = 'export';



$pagetitle=get_string('occurrence', 'block_referentiel', $occurrence_object->referentiel->code_referentiel);
$PAGE->set_url('/blocks/referentiel/export.php', array('blockid'=>$blockid, 'courseid' => $courseid, 'occurrenceid' => $occurrenceid, 'mode' => $mode ));
$PAGE->requires->css('/mod/referentiel/referentiel.css');
// $PAGE->requires->js('/mod/referentiel/functions.js');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);
$PAGE->set_title($pagetitle);
$PAGE->navbar->add($occurrence_object->referentiel->code_referentiel);
//$settingsnode = $PAGE->settingsnav->add(get_string('displayoccurrence', 'block_referentiel'));
//$site = get_site();

$settingsnode = $PAGE->settingsnav->add(get_string('export', 'block_referentiel'));
$exporturl = new moodle_url('/blocks/referentiel/export.php', array('blockid'=>$blockid, 'courseid'=>$courseid, 'occurrenceid'=>$occurrenceid));
$exportnode = $settingsnode->add(get_string('exportreferentiel', 'referentiel'), $exporturl);
$exportnode->make_active();

$icon = $OUTPUT->pix_url('icon','referentiel');

echo $OUTPUT->header();
$occurrence_object->tabs($mode, $currenttab);

    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('exportreferentiel','referentiel').' '.$OUTPUT->help_icon('exportreferentielh','referentiel').'</h2></div>'."\n";

    if (!empty($format)) {   /// Filename et format d'exportation

        if (! is_readable($CFG->dirroot.'/blocks/referentiel/format/'.$format.'/format.php')) {
            print_error( "Format not known ($format)" );
		}

        // load parent class for import/export
        require($CFG->dirroot.'/blocks/referentiel/format.php');
        // and then the class for the selected format
        require($CFG->dirroot.'/blocks/referentiel/format/'.$format.'/format.php');

        $classname = "rformat_$format";
        $rformat = new $classname();
        $rformat->setContext( $context);
        $rformat->setCourse( $course );
        $rformat->setBlockId( $blockid );
        $rformat->setFilename( $exportfilename );
        $rformat->setReferentiel( $occurrence_object->referentiel);

        if (! $rformat->exportpreprocess()) {   // Do anything before that we need to
            print_error( $txt->exporterror, $CFG->wwwroot.'/blocks/referentiel/export.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrence_object->referentiel->id);
        }

        if (! $rformat->exportprocess()) {         // Process the export data
            print_error( $txt->exporterror, $CFG->wwwroot.'/blocks/referentiel/export.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrence_object->referentiel->id);
        }

        if (! $rformat->exportpostprocess()) {                    // In case anything needs to be done after
            print_error( $txt->exporterror, $CFG->wwwroot.'/blocks/referentiel/export.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrence_object->referentiel->id);
        }
        echo "<hr />";

        $file_ext = $rformat->export_file_extension();
        $fullpath = '/'.$context->id.'/block_referentiel/referentiel/'.$occurrence_object->referentiel->id.$rformat->get_export_dir().$exportfilename.$file_ext;
        $efile = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);

        echo "<p><div class=\"boxaligncenter\"><a href=\"$efile\">$txt->download</a></div></p>";
        echo "<p><div class=\"boxaligncenter\"><font size=\"-1\">$txt->downloadextra</font></div></p>";

        echo $OUTPUT->continue_button($viewurl);
        echo $OUTPUT->footer();

        die();

    }

    /// Display upload form

    // get valid formats to generate dropdown list
    $fileformatnames = referentiel_get_import_export_formats('export');

    // get filename
    if (empty($exportfilename)) {
        $exportfilename = referentiel_default_export_filename($occurrence_object->referentiel->code_referentiel);
    }

    echo $OUTPUT->box_start('generalbox  boxaligncenter');
    echo "\n<div align=\"center\">\n";
	echo '<form enctype="multipart/form-data" method="post" action="export.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrence_object->referentiel->id.'">
        <fieldset class="invisiblefieldset" style="display: block;">
            <input type="hidden" name="sesskey" value="'.sesskey().'" />
            <table cellpadding="5">
                <tr>
                    <td>'.$txt->fileformat.':</td>
                    <td>
';
                        echo html_writer::select($fileformatnames, 'format', 'xml', false);
                        echo $OUTPUT->help_icon('formath', 'referentiel');
	echo '
                    </td>
                </tr>
                <tr>
                    <td colspan="2">'.$txt->exportname.':</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="text" size="60" name="exportfilename" value="'.$exportfilename.'" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <input type="submit" name="save" value="'.$txt->exportreferentiel.'" />
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
';
    echo "\n</div>\n";
    echo $OUTPUT->box_end();

    echo "\n<br />\n";
    // Liste de sauvegardes déjà enregistrées
    // Gestion des fichiers d'archives
    block_referentiel_get_manage_block_files($context->id, 'referentiel', $occurrenceid, get_string('exportedreferentiel', 'referentiel'), "export.php?blockid=$blockid&amp;courseid=$courseid&amp;occurrenceid=$occurrenceid");

    if (REFERENTIEL_OUTCOMES){
        echo '<br /><div align="center"><h3><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('export_bareme','referentiel').' '.$OUTPUT->help_icon('exportoutcomesh','referentiel').'</h3>'."\n";
        //echo $OUTPUT->box_start('generalbox  boxaligncenter');

        if (!empty($CFG->enableoutcomes)){
            echo '<span class="surligne"><a href="'.$CFG->wwwroot.'/blocks/referentiel/export_outcomes.php?blockid='.$blockid.'&amp;courseid='.$courseid.'&amp;occurrenceid='.$occurrence_object->referentiel->id.'">'.get_string('export_outcomes','referentiel').'</a></span>'."\n";
        }
        else{
            print_string('activer_outcomes','referentiel');
        }
        echo '<br >'.get_string('help_outcomes','referentiel');

        //echo $OUTPUT->box_end();
        echo '</div>'."\n";
    }


echo $OUTPUT->footer();


?>

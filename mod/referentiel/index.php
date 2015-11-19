<?php // $Id: index.php,v 1.5 2006/08/28 16:41:20 mark-nielsen Exp $
/**
 * This page lists all the instances of referentiel in a particular course
 *
 * @author 
 * @version $Id: index.php,v 1.5 2006/08/28 16:41:20 mark-nielsen Exp $
 * @package referentiel
 **/

/// Replace newmodule by with the name of your module referentiel

    require(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');
	require_once("$CFG->dirroot/mod/referentiel/version.php");

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = $DB->get_record("course", array("id" => "$id"))) {
        print_error("Course ID is incorrect");
    }

    require_login($course->id);
    $PAGE->set_pagelayout('incourse');

	if ($CFG->version > 2014051200) { // Moodle 2.7+
    	$params = array(
        	'context' => context_course::instance($course->id)
    	);
    	$event = \mod_referentiel\event\course_module_instance_list_viewed::create($params);
    	$event->add_record_snapshot('course', $course);
    	$event->trigger();
	} else { // Before Moodle 2.7
    	add_to_log($course->id, 'referentiel', 'view all', "index.php?id=$course->id", '');
	}
    // AFFICHAGE DE LA PAGE Moodle 2
	/// Print the page header
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname,true);
    $icon = $OUTPUT->pix_url('icon','referentiel');
    
    $PAGE->set_url('/mod/referentiel/index.php', array('id'=>$course->id));
    $PAGE->navbar->add($strreferentiels);
    $PAGE->set_title($strreferentiels);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

/// Get all the appropriate data
    if (! $referentiels = get_all_instances_in_course("referentiel", $course)) {
        notice(get_string('erreur_referentiel','referentiel'), "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)
	$s_version='';
	if (!empty($module->release)) {
        $s_version.= $module->release;
   	}

	if (!empty($module->version)){
		// The current module version (Date: YYYYMMDDXX)
		$s_version.= ' ('.get_string('release','referentiel').' '.$module->version.')'."\n";
	}

    $timenow = time();
    $strname  = get_string("name").' '.get_string("instance", "referentiel");
	$strdescription  = get_string("description");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");
    $strreferentiel  = get_string("occurrence", "referentiel").' '.get_string("referentiel", "referentiel");

    $table = new html_table();
    if ($course->format == "weeks") {
		$table->head  = array ($strweek, $strname, $strdescription, $strreferentiel);
        $table->align = array ("center", "left", "left", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strdescription, $strreferentiel);
        $table->align = array ("center", "left", "left", "left");
    }
    else {
        $table->head  = array ($strname, $strdescription, $strreferentiel);
        $table->align = array ("left", "left", "left");
    }
	
//
// debug
// echo "<br />";
// print_r($referentiels);



    foreach ($referentiels as $referentiel) {
        if (!$referentiel->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?d=$referentiel->id\">$referentiel->name</a>";
			$description = "$referentiel->description_instance";

        }
        else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?d=$referentiel->id\">$referentiel->name</a>";
			$description = "$referentiel->description_instance";
        }
		$referentiel_referentiel= $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"));
        if ($referentiel_referentiel){
            $code_referentiel=$referentiel_referentiel->code_referentiel;
            $refrefid=$referentiel_referentiel->id;
        }
        else{
            $code_referentiel=get_string('nondefini','referentiel');
            $refrefid=0;
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($referentiel->section, $link, $description, $code_referentiel. '<br />(#'.$refrefid.') ');
        }
        else {
            $table->data[] = array ($link, $description, $code_referentiel. '<br />(#'.$refrefid.') ');
        }
    }
    echo "<br />";
	if ($s_version!=''){
	echo "<p align='right'>".get_string("version", "referentiel").'<a href="./info_module_referentiel.html" target="_blank"><i>'.$s_version."</i></a></p><br />\n";
	}

    echo html_writer::table($table);

/// Finish the page

    echo $OUTPUT->footer();


?>

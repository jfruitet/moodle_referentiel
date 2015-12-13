<?php // $Id$
// d'après ./report/question/
    require_once(dirname(__FILE__).'/../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/mod/referentiel/lib.php');
    require_once($CFG->dirroot.'/mod/referentiel/locallib.php');
	require_once($CFG->dirroot.'/mod/referentiel/version.php');


/// Get all required strings

    $strreferentiels = get_string("modulenameplural", "referentiel");
    $strreferentiel  = get_string("modulename", "referentiel");

    $base_url=$CFG->wwwroot.'/report/referentiel/';
    
    $i     = optional_param('i', 0, PARAM_INT);    // referentiel_referentiel id
    if ($i){
        if (! $referentiel_instance = get_record('referentiel', 'id', $i)) {
            print_error('Referentiel instance is incorrect');
        }
    }

    $r     = optional_param('r', 0, PARAM_INT);    // referentiel_referentiel id
    if ($r){
        if (! $referentiel_referentiel = $DB->get_record('referentiel_referentiel', array('id' => $r))) {
            print_error('Referentiel is incorrect');
        }
    }

    // Initialise the table.
    $table = new html_table();
    $table->head  = array (get_string('occurrences', 'referentiel'), get_string('name', 'referentiel'), get_string('instances', 'referentiel'));
    $table->align = array ("center", "left", "left");

    $instance_head  = '<table cellspacing="1" cellpadding="2" bgcolor="#333300" width="100%">'.
'<tr bgcolor="#cccccc"><th width="30%">'.get_string('instance', 'referentiel').'</th><th width="60%">'.get_string('description', 'referentiel').'</th><th width="10%">'.get_string('course').'</th></tr>'."\n";


// Print the header & check permissions.
    admin_externalpage_setup('reportreferentiel');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('adminreport', 'referentiel'));
    $msg = '';
    $contextversionneeded = 2007101500;  // Moodle 1.9 branch

    if ($CFG->version < $contextversionneeded){
        ///version issus
        $msg .= get_string('majmoodlesvp', 'referentiel', $contextversionneeded);
        $msg .= "<br />".get_string('moodleversion', 'referentiel',$CFG->version)."<br />\n";
    }
    else{
            // suppression d'une instance
            if (!empty($referentiel_instance)){
                if (referentiel_instance_suppression($referentiel_instance->id, $base_url)){
                    $msg .= get_string('instancedeleted', 'referentiel');
                }
                else{
                    $msg .= get_string('suppression_instance_impossible', 'referentiel');
                }
            }

            // Suppression d'une occurrence
            if (!empty($referentiel_referentiel)){
    			$name_referentiel = stripslashes($referentiel_referentiel->name);
                $code_referentiel = stripslashes($referentiel_referentiel->code_referentiel);
                $local = $referentiel_referentiel->local;
                // Liste d'instances de cette occurence
                $referentiel_instances = $DB->get_records("referentiel", array("ref_referentiel" => "$referentiel_referentiel->id"));
                if ($referentiel_instances){
                    $instance_data=$instance_head;
                    foreach ($referentiel_instances as $referentiel_instance) {
                        $course_instance=$DB->get_record('course', array('id' => $referentiel_instance->course));
                        if ($course_instance){
                            if (!$course_instance->visible) {
                                $link_course = "<a class=\"dimmed\" href=\"$CFG->wwwroot/course/view.php?id=$course_instance->id\">$course_instance->shortname</a>";
                            }
                            else{
                                $link_course = "<a href=\"$CFG->wwwroot/course/view.php?id=$course_instance->id\">$course_instance->shortname</a>";
                            }
                        }
                        else{
                            $link_course = get_string('nondefini','referentiel');
                        }

                        if (!$referentiel_instance->visible) {
                            //Show dimmed if the mod is hidden
                            $link_instance = "<a class=\"dimmed\" href=\"$CFG->wwwroot/mod/referentiel/view.php?d=$referentiel_instance->id\">$referentiel_instance->name</a>";
                        }
                        else {
                            //Show normal if the mod is visible
                            $link_instance = "<a href=\"$CFG->wwwroot/mod/referentiel/view.php?d=$referentiel_instance->id\">$referentiel_instance->name</a>";
                        }

                        $instance_data.='<tr  bgcolor="#ffffee"><td>'.$link_instance. '<br />(#'.$referentiel_instance->id.') </td><td>'. stripslashes($referentiel_instance->description_instance).'</td><td>'.$link_course.'</td></tr>'."\n";
                    }
                    $instance_data.='</table>'."\n";
                    if ($local){
                        $table->data[] = array ($code_referentiel. '<br />(#'.$referentiel_referentiel->id.')<br /><i>'.get_string('local','referentiel').'</i>', $name_referentiel, $instance_data);
                    }
                    else{
                        $table->data[] = array ($code_referentiel. '<br />(#'.$referentiel_referentiel->id.') ', $name_referentiel, $instance_data);
                    }
                    // Print it.
                    echo html_writer::table($table);


                    $msg .= get_string('suppression_referentiel_impossible', 'referentiel');
                }
                else{
                    if (referentiel_referentiel_suppression($referentiel_referentiel->id, $base_url)){
                        $msg .= get_string('occurrencedeleted', 'referentiel');
                    }
                    else{
                        $msg .= get_string('suppression_referentiel_impossible', 'referentiel');
                    }
                }
            }

    }
    



    if ($msg) {
        echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');
        echo $msg;
        echo $OUTPUT->box_end();
    }

    // print_continue("$base_url/index.php");
    echo $OUTPUT->continue_button("$base_url/index.php");

    echo $OUTPUT->footer();

/**
 *
 *
 */
 
    function referentiel_referentiel_suppression($refrefid, $base_url){
    // suppression du référentiel_referentiel
    global $SITE;
    global $CFG;

    // print_object($SITE);
    // exit;
    	$ok=referentiel_delete_referentiel_domaines($refrefid);
    	$ok=$ok && referentiel_delete_referentiel_certificats($refrefid);
        if ($ok){
            $msg=get_string('deletereferentiel', 'referentiel').' '.$refrefid;
            add_to_log($SITE->id, "referentiel", "delete", "$base_url/delete.php?r=".$refrefid, $msg);
        }
        return $ok;
    }

/**
 *
 *
 */

    function referentiel_instance_suppression($id, $base_url){
    // suppression du référentiel_referentiel
    global $SITE;
    global $CFG;
    global $base_url;
    // print_object($SITE);
    // exit;
    	$ok=referentiel_delete_instance($id);

        if ($ok){
            $msg=get_string('deleteinstance', 'referentiel').' '.$id;
            add_to_log($SITE->id, "referentiel", "delete", "$base_url/delete.php?i=".$id, $msg);
        }
        return $ok;
    }

?>

<?php // $Id$
// d'après ./report/question/
    require_once(dirname(__FILE__).'/../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/mod/referentiel/lib.php');
    require_once($CFG->dirroot.'/mod/referentiel/locallib.php');
	require_once($CFG->dirroot.'/mod/referentiel/version.php');
    require_once($CFG->dirroot.'/mod/referentiel/lib_archive.php');  // archivage


/// Get all required strings
    $baseUrl='/report/referentiel/';
    $reportCss=$baseUrl.'report_referentiel.css';
    $base_url=$CFG->wwwroot.$baseUrl;
    
    $strreferentiels = get_string("modulenameplural", "referentiel");
    $strreferentiel  = get_string("modulename", "referentiel");

/// Get all the appropriate data
    $referentiel_referentiels = referentiel_get_referentiel_referentiels( NULL);

    $joursdedelai = optional_param('joursdedelai', -1, PARAM_INT);    // desherence
    if ($joursdedelai<0){
        if (isset($CFG->delaidesherence)){
            $joursdedelai = $CFG->delaidesherence;
        }
        else{
            $joursdedelai=JOURS_DESHERENCE;
        }
    }
    if ($joursdedelai<0) $joursdedelai=0;
    $delai= (3600*24*$joursdedelai);
    $strselection='';
	if (!empty($CFG->ref_profilecategory) && !empty($CFG->ref_profilefield)){
        $strselection.='<br /><a href="'.$base_url.'num_etudiants.php"><b>'.get_string('actualisation','report_referentiel').'</b></a>'."\n";
    }
    $strselection.='<br />'.get_string('avertissementjoursdedelai','referentiel').
    '<form name="form" method="post" action="'.$base_url.'index.php"> <input type="text" name="joursdedelai" size="3" value="'.$joursdedelai.'" /><input type="submit" value="'.get_string('savechanges').'" /></form>';

	// Migration des URL du module référentiel de M1.9 vers M2.x
    $migration = optional_param('migration', 0, PARAM_INT);    // pas de conversion d'url M19 en m2.x
    $suppressionurl = optional_param('suppressionurl', 0, PARAM_INT); // pas de suppression des anciennes Url
    $verbose = optional_param('verbose', 0, PARAM_INT); // pas d'affichage
	$stralertemigration='';

    $bgc0="#ffffee";
    $bgc1="#eeeedd";
    // Initialise the table.
    $table = new html_table();
    
    $table->head  = array (get_string('occurrences', 'referentiel'), get_string('instances', 'referentiel'));
    $table->align = array ("center", "left", "center");
    $table->width = "100%";
    $table->size = array('20%', '70%');
    $instance_head  = '<table cellspacing="1" cellpadding="2" bgcolor="#333300" width="100%">'.
'<tr valign="top" bgcolor="#cccccc"><th width="30%">'.get_string('instance', 'referentiel').'</th><th width="40%">'.get_string('description', 'referentiel').'</th><th width="10%">'.get_string('users_actifs','referentiel').'</th><th width="10%">'.get_string('activites_declarees','referentiel').'</th><th width="10%">'.get_string('course').'</th><th width="10%">'.get_string('archives', 'referentiel').'</th></tr>'."\n";

// Print the header & check permissions.
    $url = new moodle_url($base_url.'index.php');
    admin_externalpage_setup('reportreferentiel');
    $PAGE->set_url($url);
    $PAGE->requires->css($reportCss);
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('adminreport', 'referentiel'));

	if ($migration) {
		if ($verbose){
			echo '<p>'.get_string('conversionencours','report_referentiel').'</p>'."\n";
		}
		referentiel_conversion_url_m19($suppressionurl, $verbose);
		if ($verbose){
			echo '<p>'.get_string('conversionachevee','report_referentiel').'</p>'."\n";
		}
	}

    $msg = '';
    $contextversionneeded = 2012120300;  // Moodle 2.4 branch
    // print_object($CFG);

    // exit;
    if ($CFG->version < $contextversionneeded){
        ///version issus
        $msg .= get_string('majmoodlesvp', 'referentiel', $contextversionneeded);
        $msg .= "<br />".get_string('moodleversion', 'referentiel',$CFG->version)."<br />\n";
    }
    elseif (empty($referentiel_referentiels)){
        $msg .= "<br />".get_string('erreur_referentiel', 'referentiel')."<br />\n";
    }
    else{
     	// Migration Moodle 1.9 vers Moodle 2.x ?
		$CFG->referentiel_migration_19_2x=referentiel_recherche_url_m19();

		if (!empty($CFG->referentiel_migration_19_2x)) {
	    	$stralertemigration='<br />'.get_string('migration','report_referentiel', $CFG->referentiel_migration_19_2x)."\n";
    		$stralertemigration.=$OUTPUT->help_icon('migrationh','report_referentiel')."\n";
            $stralertemigration.='<form name="form" method="post" action="'.$base_url.'index.php">
<input type="radio" name="migration" value="1" checked="checked"/> '.get_string('yes').'
<input type="radio" name="migration" value="0" /> '.get_string('no')."\n";
	    	$stralertemigration.='<br />'.get_string('suppression','report_referentiel').'
<br /><input type="radio" name="suppressionurl" value="1" checked="checked"/> '.get_string('yes').'
<input type="radio" name="suppressionurl" value="0" /> '.get_string('no')."\n";
	    	$stralertemigration.='<br />'.get_string('verbose','report_referentiel').'
<br /><input type="radio" name="verbose" value="1" /> '.get_string('yes').'
<input type="radio" name="verbose" value="0" checked="checked" /> '.get_string('no')."\n";

            $stralertemigration.='<br /><input type="submit" value="'.get_string('savechanges').'" /></form>'."\n";
		}

        // Liste des occurrences de referentiels
        foreach ($referentiel_referentiels as $referentiel_referentiel) {
            if ($referentiel_referentiel){
                $nbactivitedesherence=0;
                if ($referentiel_referentiel->name){
        			$name_referentiel = stripslashes($referentiel_referentiel->name);
                }
                else{
                    $name_referentiel = get_string('inconnu','referentiel');
                }
                if ($referentiel_referentiel->code_referentiel){
                    $code_referentiel = stripslashes($referentiel_referentiel->code_referentiel);
                }
                else{
                    $code_referentiel =get_string('inconnu','referentiel');
                }
                $local = $referentiel_referentiel->local;
                // Liste d'instances de cette occurence
                $link_instance='';
				$referentiel_instances = $DB->get_records("referentiel", array("ref_referentiel" => "$referentiel_referentiel->id"));
                if ($referentiel_instances){
                    $instance_data=$instance_head;
                    $ligne=0;
                    foreach ($referentiel_instances as $referentiel_instance) {
                        $users_data = '';
                        $activites_data = '';
                        $archives_data = '';
                        $context_instance=NULL;
                        
                        $course_instance= $DB->get_record('course', array('id' => $referentiel_instance->course));
                        if ($course_instance){
                            if (!$course_instance->visible) {
                                $link_course = "<a class=\"dimmed\" href=\"$CFG->wwwroot/course/view.php?id=$course_instance->id\">$course_instance->shortname</a>";
                            }
                            else{
                                $link_course = "<a href=\"$CFG->wwwroot/course/view.php?id=$course_instance->id\">$course_instance->shortname</a>";
                            }
                    		$course_module = get_coursemodule_from_instance('referentiel', $referentiel_instance->id, $course_instance->id);
                            if ($course_module){
                                if (!$referentiel_instance->visible) {
                                    //Show dimmed if the mod is hidden
                                    $link_instance = "<a class=\"dimmed\" href=\"$CFG->wwwroot/mod/referentiel/view.php?d=$referentiel_instance->id\">$referentiel_instance->name</a>";
                                }
                                else {
                                    //Show normal if the mod is visible
                                    $link_instance = "<a href=\"$CFG->wwwroot/mod/referentiel/view.php?d=$referentiel_instance->id\">$referentiel_instance->name</a>";
                                }
    							$context_instance = context_module::instance($course_module->id);
                            }
                            else{
                                $link_course = get_string('nondefini','referentiel');
                            }
                        }
                        else{
                            $link_instance = $referentiel_instance->name.'<br /><i>'.get_string('nonexist','referentiel').'</i>'."\n";
                           // Proposer suppression
                            $link_instance.="<br /><a href=\"./delete.php?i=$referentiel_instance->id\">".get_string('supprimer_instance', 'referentiel')."</a>";
                        }

                        // Proposer des infos concernant le nombre de déclarations d'activités et le voluem des données
                        $activites_users_instance=  referentiel_get_users_activites_instance($referentiel_instance->id);
                        if ($activites_users_instance){
                            $users_data = count($activites_users_instance);
                        }

                        $activites_instance= referentiel_get_activites_instance($referentiel_instance->id);
                        if ($activites_instance){
                            $activites_data = count($activites_instance);
                            //
                            $activites_instance_a_suivre= referentiel_get_activites_instance_a_suivre($referentiel_instance->id, $delai);
                            if ($activites_instance_a_suivre){
                                $a_suivre=count($activites_instance_a_suivre);
                                $nbactivitedesherence+=$a_suivre;
                                $activites_data .=' (<a href="'.$base_url.'liste_activites.php?o='.$referentiel_referentiel->id.'&joursdedelai='.$joursdedelai.'"><b>&nbsp;'.$a_suivre.'&nbsp;</b></a>)';
                            }
                            // proposer archivage
                            $archives_data .="<a href=\"./archive.php?i=$referentiel_instance->id\">".get_string('gerer_archives', 'referentiel')."</a>";
                            if (!empty($context_instance)){
                                if ($CFG->referentiel_purge_archives){
                                    // Archives older than REFERENTIEL_ARCHIVE_OBSOLETE days will be deleted.
                                    $delai_destruction = REFERENTIEL_ARCHIVE_OBSOLETE * 24 * 3600;
                                    referentiel_purge_archives($context_instance->id, $delai_destruction, false );
                                }
                                $archive_info=referentiel_get_how_many_files($context_instance->id);
                                if (!empty($archive_info->nfile)){
                                    $archives_data .= " &nbsp; ".display_size($archive_info->total_size)."\n";
                                }
                            }
                        }
                        
                        if (($ligne % 2)==0){
                            $bgcolor=$bgc0;
                        }
                        else{
                            $bgcolor=$bgc1;
                        }
                        $instance_data.='<tr valign="top" bgcolor="'.$bgcolor.'"><td>'.$link_instance. '<br />(#'.$referentiel_instance->id.') </td><td>'. stripslashes($referentiel_instance->description_instance).'</td><td>'.$users_data.'</td><td>'.$activites_data.'</td><td>'.$link_course.'</td><td>'.$archives_data.'</td></tr>'."\n";
                        $ligne++;
                    }
                    $instance_data.='</table>'."\n";
                }
                else{
                    $instance_data=get_string('instancenondefinie','referentiel');
                   // Proposer suppression
                    $instance_data.="<br /><a href=\"./delete.php?r=$referentiel_referentiel->id\">".get_string('supprimer_referentiel', 'referentiel')."</a>";
                }
                if ($local){
                    $strlocal='<b>'.$code_referentiel. '<br>(#'.$referentiel_referentiel->id.')<br /><i>'.get_string('local','referentiel').'</i></b>';
                }
                else{
                    $strlocal='<b>'.$code_referentiel. '<br>(#'.$referentiel_referentiel->id.')</b>';
                }

                if ($nbactivitedesherence){
                    $table->data[] = array ($strlocal.'<br /><i>'.$name_referentiel.'</i><br /><a href="'.$base_url.'liste_activites.php?o='.$referentiel_referentiel->id.'&joursdedelai='.$joursdedelai.'">'.get_string('activitesdesheranceh','referentiel').'</a><br /><a href="'.$base_url.'export_certificats_pedagos.php?o='.$referentiel_referentiel->id.'">'.get_string('certificats','referentiel').'</a>', $instance_data);
                }
                else{
                    $table->data[] = array ($strlocal.'<br /><i>'.$name_referentiel.'</i><br /><a href="'.$base_url.'export_certificats_pedagos.php?o='.$referentiel_referentiel->id.'">'.get_string('certificats','referentiel').'</a>', $instance_data);
                }
            }
        }
    }
    
    // Version du module
    $s_version='';
	if (!empty($module->release)) {
        $s_version.= $module->release;
   	}

	if (!empty($module->version)){
		// 2009042600;  // The current module version (Date: YYYYMMDDXX)
		$s_version.= ' ('.get_string('release','referentiel').' '.$module->version.')'."\n";
	}

	if ($s_version!=''){
	   $msg.= get_string("version", "referentiel").'<br /><a href="'.$CFG->wwwroot.'/mod/referentiel/info_module_referentiel.html" target="_blank"><i>'.$s_version.'</i></a>'."\n";
	}

    $msg.=$strselection;
    $msg.=$stralertemigration;    
    if ($msg) {
        echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');
        echo $msg;
        echo $OUTPUT->box_end();
    }
    // Print it.
    echo html_writer::table($table);
    // Footer.
    echo $OUTPUT->footer();

?>

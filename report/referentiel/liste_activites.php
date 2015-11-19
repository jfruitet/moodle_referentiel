<?php // $Id$
// d'après ./admin/report/question/
    require_once(dirname(__FILE__).'/../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/mod/referentiel/lib.php');
	require_once($CFG->dirroot.'/mod/referentiel/version.php');
    require_once($CFG->dirroot.'/mod/referentiel/print_lib_activite.php');
    // require_once($CFG->dirroot.'/mod/referentiel/lib_archive.php');  // archivage

    $o     = optional_param('o', 0, PARAM_INT);    // referentiel instance id
    $joursdedelai = optional_param('joursdedelai', -1, PARAM_INT);    // referentiel instance id
    if ($joursdedelai<0){
        if (isset($CFG->delaidesherence)){
            $joursdedelai = $CFG->delaidesherence;
        }
        else{
            $joursdedelai=JOURS_DESHERENCE;
        }
    }

    $cancel = optional_param('cancel', '', PARAM_ALPHA);    //

    if ($o){
         if (! $occurrence = $DB->get_record('referentiel_referentiel', array('id' => $o))) {
            print_error('Occurrence Referentiel id is incorrect');
        }
    }
    else{
		print_error(get_string('erreurscript','referentiel','Erreur : '.$base_url.'liste_activites.php'));
	}
	
    $baseUrl='/report/referentiel/';
    $reportCss=$baseUrl.'report_referentiel.css';
    $base_url=$CFG->wwwroot.$baseUrl;
    
    if ($cancel) {
        redirect($base_url.'index.php?joursdedelai='.$joursdedelai);
    }

    if ($joursdedelai<0) $joursdedelai=0;
    $delai= (3600*24*$joursdedelai);
    //$strselection='<div style="z-index:1; position:relative; width:100px; height:100px; border:solid 1px black; background-color:pink;">
$strselection='<div class="saisie_div">
<form name="form" method="post" action="'.$base_url.'liste_activites.php?o='.$o.'">
    <b>'.get_string('joursdedelai','referentiel').'</b><br />
<input type="text" name="joursdedelai" size="3" value="'.$joursdedelai.'" /> '.get_string('jours', 'referentiel').'
<br /><input type="submit" value="'.get_string('savechanges').'" />
<br /><input type="submit" name="cancel" value="'.get_string('retour', 'referentiel').'" />
</form></div>'."\n";
    
    $strreferentiels = get_string("modulenameplural", "referentiel");
    $strreferentiel  = get_string("modulename", "referentiel");

    $strmessage = get_string('activitesdesherance','referentiel', $joursdedelai);

    $bgc0="#ffffee";
    $bgc1="#eeeedd";
    // Initialise the table.
    $table = new html_table();
    $table->head  = array (get_string('occurrence', 'referentiel'), get_string('instances', 'referentiel'));
    $table->align = array ("center", "left");
    $table->width = "100%";
    $table->size = array('20%', '70%');
    
    $instance_head  = '<table cellspacing="1" cellpadding="2" bgcolor="#333300" width="100%">'.
'<tr valign="top" bgcolor="#cccccc"><th width="30%">'.get_string('instance', 'referentiel').'</th><th width="40%">'.get_string('description', 'referentiel').'</th><th width="10%">'.get_string('users_actifs','referentiel').'</th><th width="10%">'.get_string('activites_declarees','referentiel').'</th><th width="10%">'.get_string('course').'</th></tr><tr valign="top" bgcolor="#cccccc"><th colspan="5" width="100%">'.get_string('activites', 'referentiel').'</tr>'."\n";

// Print the header & check permissions.
    $url = new moodle_url($base_url.'liste_activites.php');
    admin_externalpage_setup('reportreferentiel');
    $PAGE->set_url($url);
    $PAGE->requires->css($reportCss);
    $PAGE->requires->js($OverlibJs);
    echo $OUTPUT->header();

    echo $OUTPUT->heading(get_string('adminreport', 'referentiel'));
    echo '<div align="center"><h3>'.$strmessage.' '.$OUTPUT->help_icon('activitesdesheranceh','referentiel').'</h3></div>'."\n";


    $msg = '';
    $contextversionneeded = 2007101500;  // Moodle 1.9 branch
    // print_object($CFG);

    // exit;
    if ($CFG->version < $contextversionneeded){
        ///version issus
        $msg .= get_string('majmoodlesvp', 'referentiel', $contextversionneeded);
        $msg .= "<br />".get_string('moodleversion', 'referentiel',$CFG->version)."<br />\n";
    }
    else if (!empty($occurrence)){
        $name_referentiel = stripslashes($occurrence->name);
        $code_referentiel = stripslashes($occurrence->code_referentiel);
        $local = $occurrence->local;

        // Liste d'instances de cette occurence
        $referentiel_instances = $DB->get_records("referentiel", array("ref_referentiel" => $occurrence->id));
        if ($referentiel_instances){
            $instance_data=$instance_head;
            $ligne=0;
            foreach ($referentiel_instances as $referentiel_instance) {
                $users_data = '';
                $activites_data = '';
                $archives_data = '';
                        
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
                }
                else{
                    $link_instance = $referentiel_instance->name.'<br /><i>'.get_string('nonexist','referentiel').'</i>'."\n";
                    // Proposer suppression
                    // $link_instance.="<br /><a href=\"./delete.php?i=$referentiel_instance->id\">".get_string('supprimer_instance', 'referentiel')."</a>";
                }

                // Proposer des infos concernant le nombre de déclarations d'activités et le voluem des données
                $activites_users_instance=  referentiel_get_users_activites_instance($referentiel_instance->id);
                if ($activites_users_instance){
                    $users_data = count($activites_users_instance);
                }

                $activites_instance= referentiel_get_activites_instance($referentiel_instance->id);
                if ($activites_instance){
                    $activites_data = count($activites_instance);

                    // Afficher les activites non évaluées depuis plus de 15 jours
                    $i=0;
                    foreach ($activites_instance as $activite){
                        //print_object($activite);
                        if (referentiel_activite_a_suivre($activite, $delai)){
                            $classbg=($i%2);
                            $archives_data .=referentiel_liste_activite($activite, $classbg);
                            $i++;
                        }
                    }
                }
                        
                if (($ligne % 2)==0){
                    $bgcolor=$bgc0;
                }
                else{
                    $bgcolor=$bgc1;
                }
                $instance_data.='<tr valign="top" bgcolor="'.$bgcolor.'"><td>'.$link_instance. '<br />(#'.$referentiel_instance->id.') </td><td>'. stripslashes($referentiel_instance->description_instance).'</td><td>'.$users_data.'</td><td>'.$activites_data.'</td><td>'.$link_course.'</td></tr>'."\n";
                if (!empty($archives_data)){
                    $instance_data.='<tr valign="top" bgcolor="#ffffff"><td colspan="6">'.$archives_data.'</td></tr>'."\n";
                }
                $ligne++;
            }
            $instance_data.='</table>'."\n";
        }

        if ($local){
            $table->data[] = array ('<b>'.$code_referentiel. '<br>(#'.$occurrence->id.')<br /><i>'.get_string('local','referentiel').'</i></b><br /><i>'.$name_referentiel.'</i><br />'.$strselection, $instance_data);
        }
        else{
            $table->data[] = array ('<b>'.$code_referentiel. '<br>(#'.$occurrence->id.')</b><br /><i>'.$name_referentiel.'</i><br />'.$strselection, $instance_data);
        }
    }
    
    if ($msg) {
        echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');
        echo $msg;
        echo $OUTPUT->box_end();
    }

    // Print it.
    echo html_writer::table($table);
    echo '<div style="z-index:0; width:400px; height:20px; position:relative; background-color:lightgrey;"><p align="center"><a href="'.$base_url.'index.php?joursdedelai='.$joursdedelai.'">'.get_string('retour','referentiel').'</a><p></div>'."\n";

    // Footer.
    echo $OUTPUT->footer();






// Affiche une activite et les documents associés
// *****************************************************************
// input @param a $record_a   of activite                          *
// output null                                                     *
// *****************************************************************
function referentiel_liste_activite($record_a, $classcolor){
global $CFG;
$s="";
	if ($record_a){
		$activite_id=$record_a->id;
		$type_activite = stripslashes($record_a->type_activite);
		$description_activite = stripslashes($record_a->description_activite);
		$competences_activite = $record_a->competences_activite;
		$commentaire_activite = stripslashes($record_a->commentaire_activite);
		$ref_instance = $record_a->ref_instance;
		$ref_referentiel = $record_a->ref_referentiel;
		$ref_course = $record_a->ref_course;
		$userid = $record_a->userid;
		$teacherid = $record_a->teacherid;
		$date_creation = $record_a->date_creation;
		$date_modif_student = $record_a->date_modif_student;
		$date_modif = $record_a->date_modif;
		$approved = $record_a->approved;
		$ref_task = $record_a->ref_task;

        $user_info=referentiel_get_user_info($userid);
		$teacher_info=referentiel_get_user_info($teacherid);

		// dates
		if ($date_creation!=0){
			$date_creation_info=userdate($date_creation);
		}
		else{
			$date_creation_info='';
		}

		if ($date_modif!=0){
			$date_modif_info=userdate($date_modif);
		}
		else{
			$date_modif_info='';
		}

		if ($date_modif_student==0){
			$date_modif_student=$date_creation;
		}
		if ($date_modif_student!=0){
			$date_modif_student_info=userdate($date_modif_student);
		}
		else{
			$date_modif_student_info='';
		}

        $s.="\n".'<div class="activite_'.$classcolor.'">';

        $s.=' <a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$ref_instance.'&activite_id='.$activite_id.'">'.get_string('activite', 'referentiel').' '.$activite_id.'</a>'."\n";
        $s.=' <b>'.$type_activite.'</b> ';
		$s.='<br />'.get_string('auteur', 'referentiel').' <b>'.$user_info.'</b> ';
		// MODIF JF 2012/05/06
		$group_info=referentiel_liste_groupes_user($ref_course, $userid);
		if (!empty($group_info)){
            $s.= ' ('.$group_info.') ';
        }

		$s.=' (<i>'.get_string('date_modif_student', 'referentiel').' '.$date_modif_student_info.'</i>) ';


        if (!empty($competences_activite)){
            $s.='<br />';
            $s.=get_string('competences', 'referentiel').' ';
            $s.=' '.referentiel_affiche_liste_codes_competence('/',$competences_activite, $ref_referentiel);
        }

 		if (!empty($description_activite)){
            $s.='<br />';
            $s.=get_string('description', 'referentiel').'<i><span class="small">';
            if (strlen($description_activite)>1024){
    		  $s.=substr(nl2br($description_activite),0, 1024).'(...)';
            }
            else{
                $s.=nl2br($description_activite);
            }
            $s.='</span></i> ';
        }

		$s.='<br />';
		if ($teacher_info){
            $s.=get_string('referent','referentiel').' <b>'.$teacher_info.'</b>';
        }else{
            $s.='<span class="alerte">'.get_string('pasdereferent','referentiel').'</span>';
        }
        if ($date_modif_info){
    		$s.=' (<i>';
    		$s.=get_string('date_modif', 'referentiel').' '.$date_modif_info;
    		$s.='</i>) ';
        }
    	if ($commentaire_activite){
            $s.='<br />';
            $s.='<i><span class="small">';
            if (strlen($commentaire_activite)>1024){
                $s.=substr(nl2br($commentaire_activite),0, 1024).'(...)';
            }
            else{
                $s.=nl2br($commentaire_activite);
            }
            $s.='</i></span>'."\n";
        }
		$s.='</div>'."\n";
	}
	return $s;
}




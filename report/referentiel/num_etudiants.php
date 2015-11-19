<?php
    require_once(dirname(__FILE__).'/../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/mod/referentiel/lib.php');
    require_once($CFG->dirroot.'/mod/referentiel/lib_etab.php');
	require_once($CFG->dirroot.'/mod/referentiel/version.php');

    $updateprofile = optional_param('updateprofile', 0, PARAM_INT); // forcer la mise à jour des numero d'etudiant
    $cancel = optional_param('cancel', '', PARAM_ALPHA);    //

/// Get all required strings
    $baseUrl='/report/referentiel/';
    $reportCss=$baseUrl.'report_referentiel.css';
    $base_url=$CFG->wwwroot.$baseUrl;
    
    if ($cancel) {
        redirect($base_url.'index.php');
    }


    $strselection='<div class="saisie_div">
<form name="form" method="post" action="'.$base_url.'num_etudiants.php">
    <b>'.get_string('regenere_profil','referentiel').'</b><br /> ';
        if ($updateprofile){
            $strselection.='<input type="radio" name="updateprofile" value="0" /> '.get_string('no').'
<input type="radio" name="updateprofile" value="1" checked="checked" /> '.get_string('yes');
        }
        else{
            $strselection.='<input type="radio" name="updateprofile" value="0" checked="checked" /> '.get_string('no').'
<input type="radio" name="updateprofile" value="1" /> '.get_string('yes');
        }
        $strselection.='<br /><input type="submit" value="'.get_string('savechanges').'" />
<input type="submit" name="cancel" value="'.get_string('retour', 'referentiel').'" />
</form>
<br /><i>'.get_string('profilcheck','report_referentiel').'</i></div>'."\n";
    $strreferentiels = get_string("modulenameplural", "referentiel");
    $strreferentiel  = get_string("modulename", "referentiel");
	$strmessage = get_string('etudiants','referentiel');
    $icon = '<img class="icon" src="'.$CFG->wwwroot.'/mod/referentiel/icon.gif" alt="'.get_string('modulename','referentiel').'"/>';

    $bgc0="#ffffee";
    $bgc1="#eeeedd";
    // Initialise the table.
    $table = new html_table();	
    $table->head  = array ('', get_string('etudiants_inscrits_referentiel', 'report_referentiel'));
    $table->align = array ("center", "left");
    $table->width = "100%";
    $table->size = array('20%', '70%');
    $etudiant_head  = '<table cellspacing="1" cellpadding="2" bgcolor="#333300" width="100%" border="1">
    <tr valign="top" bgcolor="#cccccc">
<th width="5%"><i>'.get_string('userid','referentiel').'</i></th>
<th width="10%">'.get_string('lastname').'</th>
<th width="10%">'.get_string('firstname').'</th>
<th width="20%">'.get_string('email').'</th>
<th width="5%">'.get_string('num_etudiant','referentiel').'</th>
<th width="10%">'.get_string('ddn_etudiant','referentiel').'</th>
<th width="15%">'.get_string('lieu_naissance', 'referentiel').'</th>
<th width="5%">'.get_string('departement_naissance','referentiel').'</th>
<th width="15%">'.get_string('etablissement','referentiel').'</th>
</tr>'."\n";

	
// Print the header & check permissions.
    $url = new moodle_url($base_url.'export_etudiant_pedagos.php');
    admin_externalpage_setup('reportreferentiel');
    $PAGE->set_url($url);
    $PAGE->requires->css($reportCss);
    $PAGE->requires->js($OverlibJs);
    echo $OUTPUT->header();

    echo $OUTPUT->heading(get_string('adminreport', 'referentiel'));

    $msg = '';
    $contextversionneeded = 2007101500;  // Moodle 1.9 branch
    // print_object($CFG);

    // exit;
    if ($CFG->version < $contextversionneeded){
        ///version issus
        $msg .= print_heading(get_string('majmoodlesvp', 'referentiel', $contextversionneeded), '', 3, 'main', true);
        $msg .= get_string('moodleversion', 'referentiel',$CFG->version)."<br />\n";
    }
    else {
        echo "\n".'<style type="text/css" >
.small {
		font-size : 8pt;
}

.alerte {
		color: Red;
}

div.activite_0 {
	position:relative;
	left:10;
	top:8;
	width:1024;
	z-index: 0;
	color: Navy;
    background-color : #cceeff;
	font-family : sans-serif;
	font-size : 9pt;
	font-weight : normal;
	margin : 1pt;
	padding : 4px;
	voice-family : male;
	volume : inherit;
	white-space : normal;
}

div.activite_1 {
	position:relative;
	left:10;
	top:8;
	width:1024;
	z-index: 0;
	color: Navy;
    background-color : #eeddff;
	font-family : sans-serif;
	font-size : 9pt;
	font-weight : normal;
	margin : 1pt;
	padding : 4px;
	voice-family : male;
	volume : inherit;
	white-space : normal;
}

div.saisie_div {
	position:relative;
	left:10;
	top:8;
	width:100;
	height:100;
	z-index: 1;
	color: Black;
    border:solid 1px black;
    background-color:lightgrey;
	font-family : sans-serif;
	font-size : 10pt;
	font-weight : normal;
	margin : 1pt;
	padding : 4px;
	voice-family : male;
	volume : inherit;
	white-space : normal;
}
</style>'."\n";

        if (!empty($updateprofile)){
            referentiel_set_all_students_numbers();
        }

		echo '<div align="center"><h3>'.$strmessage.' '.$OUTPUT->help_icon('etudianth','referentiel').'</h3></div>'."\n";

        $etudiant_data='';
        $records_etudiants=referentiel_get_all_students($updateprofile);
        if ($records_etudiants){
            $etudiant_data=$etudiant_head;
            $ligne=0;
            foreach ($records_etudiants as $etudiant) {
                // print_object($records_etudiants);
                $nom_etablissement=referentiel_get_nom_etablissement($etudiant->ref_etablissement);
                if (($ligne % 2)==0){
                    $bgcolor=$bgc0;
                }
                else{
                    $bgcolor=$bgc1;
                }
                $etudiant_data.='<tr valign="top" bgcolor="'.$bgcolor.'">'."\n";
                $etudiant_data.='<td><i>'.$etudiant->id.'</i></td>';
                $etudiant_data.='<td><i>'.$etudiant->lastname.'</i></td>';
                $etudiant_data.='<td>'.$etudiant->firstname.'</td>';
                $etudiant_data.='<td>'.$etudiant->email.'</td>';
                $etudiant_data.='<td>'.referentiel_nom_connu($etudiant->num_etudiant).'</td>';
                $etudiant_data.='<td>'.referentiel_nom_connu($etudiant->ddn_etudiant).'</td>';
                $etudiant_data.='<td>'.referentiel_nom_connu($etudiant->lieu_naissance).'</td>';
                $etudiant_data.='<td>'.referentiel_nom_connu($etudiant->departement_naissance).'</td>';
                $etudiant_data.='<td>'.$nom_etablissement.'</td>';
                $etudiant_data.='</tr>';
                $ligne++;
            }
            $etudiant_data.='</table>'."\n";
        }
        $table->data[] = array ($strselection, $etudiant_data);
    }

    if ($msg) {
        echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');
        echo $msg;
        echo $OUTPUT->box_end();
    }

    // Print it.
    echo html_writer::table($table);
    echo '<div style="z-index:0; width:400px; height:20px; position:relative; background-color:lightgrey;"><p align="center"><a href="'.$base_url.'index.php">'.get_string('retour','referentiel').'</a><p></div>'."\n";

    // Footer.
    echo $OUTPUT->footer();



//**********************************************************
function referentiel_set_all_students_numbers(){
global $DB;
    $records=$DB->get_records("referentiel_etudiant", NULL);
    foreach($records as $rec){
        referentiel_update_profile_student($rec->userid);
    }
}

//**********************************************************
function referentiel_get_all_students(){
global $DB;
	$params=NULL;
	$sql = 'SELECT u.id, u.firstname, u.lastname, u.email, e.*
    FROM {user} as u, {referentiel_etudiant} as e
    WHERE e.userid=u.id
    ORDER BY u.lastname, u.firstname';
    //echo "<br>DEBUG :: SQL :<br />$sql\n";
	//exit;
    return $DB->get_records_sql($sql, $params);
}


?>

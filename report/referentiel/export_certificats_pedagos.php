<?php
    require_once(dirname(__FILE__).'/../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/mod/referentiel/lib.php');
    // require_once($CFG->dirroot.'/mod/referentiel/locallib.php');
	require_once($CFG->dirroot.'/mod/referentiel/version.php');


    $o              = optional_param('o', 0, PARAM_INT);    // referentiel instance id
    $formation      = optional_param('formation', 0, PARAM_INT);
    $joursdedelai   = optional_param('joursdedelai', -1, PARAM_INT);    // referentiel instance id
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
		print_error(get_string('erreurscript','referentiel','Erreur : admin/report/referentiel/liste_activites.php'));
	}

/// Get all required strings
    $baseUrl='/report/referentiel/';
    $reportCss=$baseUrl.'report_referentiel.css';
    $base_url=$CFG->wwwroot.$baseUrl;
    
    if ($cancel) {
        redirect($base_url.'index.php?joursdedelai='.$joursdedelai);
    }

    $strselection='<div class="saisie_div">
<form name="form" method="post" action="'.$base_url.'export_certificats_pedagos.php?o='.$o.'&joursdedelai='.$joursdedelai.'">
    <b>'.get_string('pedagogie','referentiel').'</b><br /> ';
    if ($formation){
        $strselection.='<input type="radio" name="formation" value="0" /> '.get_string('no').'
<input type="radio" name="formation" value="1" checked="checked" /> '.get_string('yes');
    }
    else{
        $strselection.='<input type="radio" name="formation" value="0" checked="checked" /> '.get_string('no').'
<input type="radio" name="formation" value="1" /> '.get_string('yes');
    }
    $strselection.='<br /><input type="submit" value="'.get_string('savechanges').'" />
<input type="submit" name="cancel" value="'.get_string('retour', 'referentiel').'" />
</form></div>'."\n";

    $strreferentiels = get_string("modulenameplural", "referentiel");
    $strreferentiel  = get_string("modulename", "referentiel");
	$strmessage = get_string('exportcertificat','referentiel');
    $icon = '<img class="icon" src="'.$CFG->wwwroot.'/mod/referentiel/icon.gif" alt="'.get_string('modulename','referentiel').'"/>';

    $bgc0="#ffffee";
    $bgc1="#eeeedd";
    // Initialise the table.
    $table = new html_table();	
    $table->head  = array (get_string('occurrence', 'referentiel'), get_string('certificats', 'referentiel'));
    $table->align = array ("center", "left");
    $table->width = "100%";
    $table->size = array('20%', '70%');
    $certificat_head  = '<table cellspacing="1" cellpadding="2" bgcolor="#333300" width="100%" border="1">
    <tr valign="top" bgcolor="#cccccc">
    <th width="10%"><i>'.get_string('rank','report_referentiel').'</i></th>
<th width="10%"><i>'.get_string('userid','referentiel').'</i></th>
<th width="10%">'.get_string('username').'</th>
<th width="20%">'.get_string('lastname').'</th>
<th width="20%">'.get_string('firstname').'</th>
<th width="50%" colspan="2">'.get_string('email').'</th></tr>
<tr valign="top" bgcolor="#cccccc">
<th colspan="7">'.get_string('competences', 'referentiel').'</th></tr>'."\n";
    if ($formation){
        $certificat_head  .='<tr valign="top" bgcolor="#cccccc"><th>&nbsp;</th><th>'.get_string('promotion','referentiel').'</th>
<th>'.get_string('formation','referentiel').'</th><th>'.get_string('pedagogie','referentiel').'</th>
<th>'.get_string('num_groupe','referentiel').'</th><th>'.get_string('composante','referentiel').'</th>
<th>'.get_string('commentaire','referentiel').'</th></tr>'."\n";
    }
	
// Print the header & check permissions.
    $url = new moodle_url($base_url.'export_certificat_pedagos.php');
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
    else if (!empty($occurrence)){
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

		echo '<div align="center"><h3>'.$strmessage.' '.$OUTPUT->help_icon('exportcertificath','referentiel').'</h3></div>'."\n";

        $name_referentiel = stripslashes($occurrence->name);
        $code_referentiel = stripslashes($occurrence->code_referentiel);
        $local = $occurrence->local;
        $certificat_data='';
        $records_certificats=referentiel_get_all_certificates($o, $formation);
        if ($records_certificats){
            $certificat_data=$certificat_head;
            $ligne=0;
            foreach ($records_certificats as $certificat) {

                if (($ligne % 2)==0){
                    $bgcolor=$bgc0;
                }
                else{
                    $bgcolor=$bgc1;
                }
                $certificat_data.='<tr valign="top" bgcolor="'.$bgcolor.'">'."\n";
                // print_object($records_certificats);
                $certificat_data.='<td><i>'.$certificat->rank.'</i></td>';
                $certificat_data.='<td><i>'.$certificat->userid.'</i></td>';
                $certificat_data.='<td>'.$certificat->username.'</td>';
                $certificat_data.='<td width="20%">'.$certificat->lastname.'</td>';
                $certificat_data.='<td width="20%">'.$certificat->firstname.'</td>';
                $certificat_data.='<td colspan="2" width="50%">'.$certificat->email.'</td>';
                $certificat_data.='</tr><tr valign="top" bgcolor="'.$bgcolor.'">';
                $certificat_data.='<td colspan="7">'.affiche_competences($certificat->competences_certificat).'</td></tr>';
                if ($formation){
                    if (isset($certificat->promotion)){
                    $certificat_data.='<tr valign="top" bgcolor="'.$bgcolor.'">';
                        $certificat_data.='<td>&nbsp;</td>';
                        $certificat_data.='<td>'.$certificat->promotion.'</td>';
                        $certificat_data.='<td>'.$certificat->formation.'</td>';
                        $certificat_data.='<td>'.$certificat->pedagogie.'</td>';
                        $certificat_data.='<td>'.$certificat->num_groupe.'</td>';
                        $certificat_data.='<td>'.$certificat->composante.'</td>';
                        $certificat_data.='<td width="50%"><span class="small">'.$certificat->commentaire.'</span></td>';
                    }
                    else{
                        $certificat_data.='<tr valign="top" bgcolor="#ffaabb">';
                        $certificat_data.='<td colspan="7">'.get_string('no_data','referentiel',$certificat->lastname).'</td>';
                    }
                    $certificat_data.='</tr>';
                }
                $ligne++;
            }
            $certificat_data.='</table>'."\n";
        }

        if ($local){
            $table->data[] = array ('<b>'.$code_referentiel. '<br>(#'.$occurrence->id.')<br /><i>'.get_string('local','referentiel').'</i></b><br /><i>'.$name_referentiel.'</i><br/>'.$strselection, $certificat_data);
        }
        else{
            $table->data[] = array ('<b>'.$code_referentiel. '<br>(#'.$occurrence->id.')</b><br /><i>'.$name_referentiel.'</i><br/>'.$strselection, $certificat_data);
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



    

//**********************************************************
function referentiel_get_all_certificates($o, $formation=0){
global $DB;
//DEBUG
//echo "<br>DEBUG :: $o; $formation\n";
//exit;
	if (!empty($o)){
        if ($formation) {

$params=array('occurrence'=>$o);
$sql = 'SELECT @rownum := @rownum + 1 AS rank, u.username, u.firstname, u.lastname, u.email, c.*, p.*
 FROM (SELECT @rownum := 0) r, {referentiel_certificat} as c,  {user} as u
 LEFT OUTER JOIN {referentiel_a_user_pedagogie} as aup
   ON aup.userid=u.id
   LEFT JOIN {referentiel_pedagogie} as p
     ON aup.refpedago=p.id
   WHERE c.ref_referentiel=:occurrence
   AND (u.id=c.userid)
   AND (u.id=aup.userid OR aup.userid IS NULL)
   AND (aup.refrefid=c.ref_referentiel OR aup.refrefid IS  NULL)
   ORDER BY u.lastname, u.firstname, p.id  ';
        }
        else{
		$params=array('occurrence'=>$o);
		$sql = 'SELECT @rownum := @rownum + 1 AS rank,  u.username, u.firstname, u.lastname, u.email, c.*
    FROM (SELECT @rownum := 0) r, {referentiel_certificat} as c, {user} as u
    WHERE c.ref_referentiel=:occurrence
    AND c.userid = u.id
    ORDER BY u.lastname, u.firstname';
	  }
        //echo "<br>DEBUG :: SQL :<br />$sql\n";
		//exit;
        return $DB->get_records_sql($sql, $params);
    }
    return NULL;
}

function affiche_competences($listecompetences){

global $tcode;
global $tval;
    $tcc=explode("/",$listecompetences);
    if ($tcc){
        //print_r($tcc);
        $i=0;
        for($k=0; $k<count($tcc); $k++){
            if (isset($tcc[$k])){
                //echo "<br>".$tcc[$k];
                $comp=explode(":",$tcc [$k]);
                if (isset($comp) && isset($comp[0]) && isset($comp[1])){
                        $tcode[$i]=$comp[0];
                        $tval[$i]=$comp[1];
                    $i++;
                }
            }
        }
    }
    $s='<table border="1"><tr>';
    $s1='';
    $s2='';
    for ($j=0; $j<$i; $j++){
        if (isset($tcode[$j])){
            $s1.= '<td><span class="small">'.$tcode[$j]. '</span></td>';
        }
        if (isset($tval[$j])){
            $s2.= '<td><span class="small">'.$tval[$j]. '</span></td>';
        }
    }
    $s.=$s1;
    $s.='</tr><tr>';
    $s.=$s2;
    $s.='</tr></table>'."\n";
    return $s;
}
?>

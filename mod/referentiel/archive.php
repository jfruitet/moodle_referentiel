<?php  // $Id: archive.php,v 1.0 2011/09/02/ 00:00:00 jfruitet Exp $
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
* D'apres export_certificat.php
* Archive referentiel
*
* @version $Id: archive.php,v 1.0 2011/09/02/ 00:00:00 jfruitet Exp $
* @author Jean Fruitet, Martin Dougiamas, Howard Miller, and many others.
*         {@link http://moodle.org}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package referentiel
*/
    require(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');
    require_once('../../config.php');
    require_once('locallib.php');
    require_once('lib_etab.php');     // scolarite
    require_once('lib_archive.php');  // archivage
    require_once('lib_certificat.php');
    require_once('lib_pedagogie.php');
//    require_once('print_lib_certificat.php');	// AFFICHAGES
    require_once('import_export_lib.php');	// IMPORT / EXPORT
    // require_once('portfolio/mahara/locallib_portfolio.php');  // export vers portfolio directement ntégré dans ./locallib.php
        
    $debug_special = optional_param('debug_special', 0, PARAM_INT);

    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel base id

    $mode  = optional_param('mode','', PARAM_ALPHANUM);
    $action  	= optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements

    $exportfilename = optional_param('exportfilename','',PARAM_FILE );
    $format = optional_param('format','', PARAM_FILE );

    $groupmode     = optional_param('groupmode', -1, PARAM_INT);
    $cancel        = optional_param('cancel', 0, PARAM_BOOL);

    $userid = optional_param('userid', 0, PARAM_INT);
    $select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
    $select_all = optional_param('select_all', 0, PARAM_INT);      // tous les certificats
    $format_condense = optional_param('format_condense', 0, PARAM_INT);  // format compact

// Formations / Pedagogies
    $mode_select       = optional_param('mode_select','', PARAM_ALPHA);
    $list_pedagoids  = optional_param('list_pedagoids', '',PARAM_TEXT);
    $list_userids  = optional_param('list_userids', '',PARAM_TEXT);
    $export_pedagos = optional_param('export_pedagos', 0, PARAM_INT);  // exporattion des donnees de formation / pedagogie
    $export_documents= optional_param('export_documents', 0, PARAM_INT);  // exportation des documents attachés aux déclarations

    $f_promotion = optional_param('f_promotion', '', PARAM_ALPHANUM);
    $f_formation = optional_param('f_formation', '', PARAM_ALPHANUM);
    $f_pedagogie = optional_param('f_pedagogie', '', PARAM_ALPHANUM);
    $f_composante = optional_param('f_composante', '', PARAM_ALPHANUM);
    $f_num_groupe = optional_param('f_num_groupe', '', PARAM_ALPHANUM);

/*
  $sql_f_where=optional_param('sql_f_where','', PARAM_ALPHANUM);
  $sql_f_order=optional_param('sql_f_order','', PARAM_ALPHANUM);
*/

	$sql_filtre_where='';
    $data_archive= new Object(); // parametres de filtrage

	if (isset($f_promotion)){
		$data_archive->f_promotion=$f_promotion;
	}
	else{
		$data_archive->f_promotion='';
	}
	if (isset($f_formation)){
		$data_archive->f_formation=$f_formation;
	}
	else{
		$data_archive->f_formation='';
	}
	if (isset($f_pedagogie)){
		$data_archive->f_pedagogie=$f_pedagogie;
	}
	else{
		$data_archive->f_pedagogie='';
	}
    if (isset($f_composante)){
		$data_archive->f_composante=$f_composante;
	}
	else{
		$data_archive->f_composante='';
	}
    if (isset($f_num_groupe)){
		$data_archive->f_num_groupe=$f_num_groupe;
	}
	else{
		$data_archive->f_num_groupe='';
	}


    // get display strings
    $txt = new object;

    $txt->choisir = get_string('choisir','referentiel');
    $txt->condense = get_string('format_condense','referentiel');
    $txt->pourcentage = get_string('format_pourcentage','referentiel');
    $txt->reduit1 = get_string('format_reduit1','referentiel');
    $txt->reduit2 = get_string('format_reduit2','referentiel');
    $txt->referentiel = get_string('referentiel','referentiel');
    $txt->download = get_string('download','referentiel');
    $txt->downloadextra = get_string('downloadextra','referentiel');
    $txt->exporterror = get_string('exporterror','referentiel');
    $txt->exportname = get_string('exportname','referentiel');
    $txt->exportreferentiel = get_string('exportreferentiel', 'referentiel');
    $txt->exportcertificat = get_string('exportcertificat', 'referentiel');
    $txt->fileformat = get_string('fileformat','referentiel');
    $txt->modulename = get_string('modulename','referentiel');
    $txt->modulenameplural = get_string('modulenameplural','referentiel');
    // $txt->tofile = get_string('tofile','referentiel');


   // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/archive.php');

	if ($d) {     // referentiel_referentiel_id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Réferentiel id is incorrect');
        }

		if (! $course = $DB->get_record("course", array("id" => "$referentiel->course"))) {
	            print_error('Course is misconfigured');
    	}

		if (! $cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $course->id)) {
    	        print_error('Course Module ID is incorrect');
		}
		$url->param('d', $d);
	}
	elseif ($id) {
        if (! $cm = get_coursemodule_from_id('referentiel', $id)) {
        	print_error('Course Module ID was incorrect');
        }
        if (! $course = $DB->get_record("course", array("id" => "$cm->course"))) {
            print_error('Course is misconfigured');
        }
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$cm->instance"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Referentiel is incorrect');
        }
        $url->param('id', $id);
    }
	else{
		print_error(get_string('erreurscript','referentiel','Erreur01 : archive.php'), 'referentiel');
	}

    require_login($course->id, false, $cm);

    if (!isloggedin() or isguestuser()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
    }


    // MODIF JF mai 2011
    if ($referentiel->ref_referentiel){
        $existe_pedagos=referentiel_pedagogies_exists($referentiel->ref_referentiel);
    }
    else{
        $existe_pedagos=0;
    }

	// selectionner un utilisateur
	$userid_filtre=0;
    if (!empty($userid)) {
        $userid_filtre=$userid;
    }

    // check role capability
    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

    require_capability('mod/referentiel:archive', $context);
    if (!has_capability('mod/referentiel:managecertif', $context)) {
        // etudiant
        $userid_filtre=$USER->id;
    }

	// RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else {
        $form = (object)$_POST;
    }


    if ($cancel) {
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        }
        else {
            redirect('certificat.php?d='.$referentiel->id);
        }
    }

    if (!empty($form->deletefile) && confirm_sesskey()){
        // Suppression des fichiers
        foreach ($form->deletefile as $fullpathfile) {
            if ($fullpathfile){
                // echo "<br />DEBUG :: archive.php :: 252<br />\n";
                // echo "<br />$fullpathfile\n";
                referentiel_delete_a_file($fullpathfile);
            }
        }
        unset($form);
    }


    /// data selection filtre
    if (isset($mode_select) && ($mode_select=='selectetab') && confirm_sesskey() ){
		// gestion des filtres de formation / promotion;
		if (!empty($f_promotion)){
				$sql_filtre_where.=' AND promotion=\''.$f_promotion.'\' ';
		}
		if (!empty($f_formation)){
				$sql_filtre_where.=' AND formation=\''.$f_formation.'\' ';
		}
		if (!empty($f_pedagogie)){
				$sql_filtre_where.=' AND pedagogie=\''.$f_pedagogie.'\' ';
		}
		if (!empty($f_composante)){
				$sql_filtre_where.=' AND composante=\''.$f_composante.'\' ';
		}
		if (!empty($f_num_groupe)){
				$sql_filtre_where.=' AND num_groupe=\''.$f_num_groupe.'\' ';
		}

		// echo "<br />DEBUG :: archive.php :: Ligne 232 :: FILTRES : WHERE : $sql_filtre_where \n";
		// exit;
    }
    
    // tous les certificats ?
    /// selection utilisateurs accompagnés
    if (isset($action) && ($action=='select_acc') && confirm_sesskey()){
        if (isset($form->select_acc)  ){
            $select_acc=$form->select_acc;
        }
        if (isset($form->select_all)){
		  	$select_all=$form->select_all;
        }
        if (isset($form->mode) && ($form->mode!='')){
			 $mode=$form->mode;
        }
        // echo "<br />ACTION : $action  SEARCH : $userid_filtre\n";
        unset($form);
        unset($action);
        // exit;
    }

    // SELECTION PEDAGOGIES
    $affiche=true;    // affichage les boites de selection

    if (isset($action) && ($action=='selectpedagogie') && confirm_sesskey()){
        // charger les pedagogies
        // DEBUG
        // echo "<br />DEBUG :: archive.php :: 201<br />\n";
        // print_r($form);
        if (isset($form->select_acc)){
		  	$select_acc=$form->select_acc;
        }
		if (isset($form->select_all)){
		  	$select_all=$form->select_all;
		}

        if (isset($form->mode) && ($form->mode!='')){
            $mode=$form->mode;
        }


        $r_userids=array();
        $list_pedagoids='';
        $list_userids='';
        if (!empty($form->t_pedago)){
            // echo "<br />DEBUG :: archive.php :: 271<br />\n";
            // print_r($form->t_pedago);
            foreach($form->t_pedago as $pedagoid){
                $list_pedagoids.=$pedagoid.',';
                $r_userids[]=referentiel_get_userids_pedagogie($pedagoid, $referentiel_referentiel->id);
            }
        }
        if (!empty($r_userids)){
            // recuperer la liste des utilisateurs
            foreach ($r_userids  as $record_u) {   // afficher la liste d'users
                // echo "<br />DEBUG :: archive.php :: 281<br />\n";
                // print_r($record_u);
                foreach ($record_u as $ru){
                    // echo "<br />DEBUG :: archive.php :: 284<br />\n";
                    // print_r($ru);
                    // echo "<br />".$ru->userid;
                    $list_userids.=$ru->userid.',';
                }
            }
            $select_all=2;
        }
        //echo "<br />DEBUG :: export_certificat.php :: 212<br />\n";
        //print_r($r_userids);
        //if ($debug_special){
        //    echo "<br />DEBUG :: archive.php :: 295 :: SELECT_ACC : $select_acc :: SELECT_ALL : $select_all\n";
        //    echo "<br />LISTE_PEDAGO :<br />$list_pedagoids <br /> :: LISTE_USERIDS :<br />$list_userids <br />\n";
        //}
		// echo "<br />ACTION : $action \n";
		unset($form);
		unset($action);
		//exit;
    }

    /// selection utilisateurs
    if (isset($action) && ($action=='select_all_certificates')){
		  if (isset($form->select_all) && confirm_sesskey() ){
		  	$select_all=$form->select_all;
		  }
		  if (isset($form->mode) && ($form->mode!='')){
			 $mode=$form->mode;
		  }
		  // echo "<br />ACTION : $action  SEARCH : $userid_filtre\n";
		  unset($form);
		  unset($action);
		  // exit;
    }



    ///For the tabs
	if (!isset($mode)){
		$mode='archive'; // un seul mode possible
	}
	$currenttab = 'archive';
	
	// Moodle 2
    $url->param('mode', $mode);

    

	/// Check to see if groups are being used here
	/// find out current groups mode
	$groupmode = groups_get_activity_groupmode($cm);
    $currentgroup = groups_get_activity_group($cm, true);

   	/// Get all users that are allowed to submit activite
	$gusers=NULL;
    if ($gusers = get_users_by_capability($context, 'mod/referentiel:write', 'u.id', 'u.lastname', '', '', $currentgroup, '', false)) {
    	$gusers = array_keys($gusers);
    }
	// if groupmembersonly used, remove users who are not in any group
    if ($gusers and !empty($CFG->enablegroupings) and $cm->groupmembersonly) {
    	if ($groupingusers = groups_get_grouping_members($cm->groupingid, 'u.id', 'u.id')) {
       		$gusers = array_intersect($gusers, array_keys($groupingusers));
       	}
    }



    /// Mark as viewed  ??????????? A COMMENTER
    $completion=new completion_info($course);
    $completion->set_module_viewed($cm);

// AFFICHAGE DE LA PAGE Moodle 2
    $strreferentiel = get_string('modulename', 'referentiel');
    $strreferentiels = get_string('modulenameplural', 'referentiel');
    $strmessage = get_string('archivereferentiel','referentiel');
    $strpagename=get_string('archivereferentiel','referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->css('/mod/referentiel/jauge.css');
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    //if ($CFG->version < 2011120100) $PAGE->requires->js('/lib/overlib/overlib.js');  else
    $PAGE->requires->js($OverlibJs);
    // $PAGE->requires->js('/mod/referentiel/functions.js');

    $PAGE->set_title($pagetitle);
    $PAGE->navbar->add($strpagename);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

	groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/archive.php?d='.$referentiel->id.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc);

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();
    echo '<br />'."\n";
    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title=""  alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('archiveh','referentiel').'</h2></div>'."\n";

    $records_users=referentiel_get_liste_users($referentiel, $course, $context, $list_userids, $userid_filtre, $gusers, $select_acc, $select_all);

    $size_data=referentiel_get_size_data($records_users, $referentiel_referentiel->id);


    if (!empty($format)) {   /// Filename et format d'exportation

        if (!confirm_sesskey()) {
            print_error( 'sesskey' );
        }

        // load parent class for import/export
        require("format.php");

        // and then the class for the selected format
        require("format/$format/format.php");

        $classname = "zformat_$format";
        $zformat = new $classname();

        // $zformat->setCategory( $category );
        $zformat->setCourse( $course );
        $zformat->setFilename( $exportfilename );
        $zformat->setIReferentiel( $referentiel);
        $zformat->setRReferentiel( $referentiel_referentiel);
        $zformat->setUserCreator( $USER->id);   // qui réalise l'archive
        $zformat->setUserFiltre($userid_filtre);  // celui qu'on archive
        $zformat->setContext($context );
        
        // fournir la liste des certificats à charger
        $zformat->setRUsers($records_users);

        if (isset($format_condense)){
            $zformat->setRCFormat($format_condense);
		}
        if (isset($export_pedagos)){
            // DEBUG
            // echo "<br />DEBUG :: 484 :: EXPORT_PEDAGOS :: $export_pedagos\n";
            // exit;
            $zformat->setExportPedago($export_pedagos);
		}
        if (isset($export_documents)){
            $zformat->setExportDocuments($export_documents);
		}

        // repertoire temporaire pour ziper l'archive
        // Moodle 2.0 : make_upload_directory( $zformat->get_temp_dir());
        // Moodle 2.3 : utilsation de make_temp_directory($path_temp) dans le

        if (! $zformat->exportpreprocess()) {   // Do anything before that we need to
            print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/archive.php?d='.$referentiel->id);
        }

        if (! $zformat->exportprocess()) {         // Process the export data
            print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/archive.php?d='.$referentiel->id);
        }

        if (! $zformat->exportpostprocess($exportfilename)) {                    // In case anything needs to be done after
            print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/archive.php?d='.$referentiel->id);
        }
        echo "<hr />";

        // link to download the finished file
        $file_ext = $zformat->export_zip_extension();
        $fullpath = '/'.$context->id.'/mod_referentiel/archive/0'.$zformat->get_export_dir().$exportfilename.$file_ext;
        $efile = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);


        echo "<p><div class=\"boxaligncenter\"><a href=\"$efile\">$txt->download</a></div></p>";
        echo "<p><div class=\"boxaligncenter\"><font size=\"-1\">$txt->downloadextra</font></div></p>";

        echo $OUTPUT->continue_button($CFG->wwwroot.'/mod/referentiel/certificat.php?id='.$cm->id);
        echo $OUTPUT->footer();
        die();

    }
    else{ // BOITES DE SELECTION

        if (has_capability('mod/referentiel:export', $context)) {
            echo '<div align="center"><h3><img src="'.$icon.'" border="0" title=""  alt="" /> '.get_string('selectcertificat','referentiel').' '.$OUTPUT->help_icon('selectcertificath','referentiel').'</h3></div>'."\n";
            referentiel_select_liste_certificats($referentiel, $list_pedagoids, $userid_filtre, $gusers, $select_acc, $mode, $CFG->wwwroot . '/mod/referentiel/archive.php?d='.$referentiel->id, $select_all, $sql_filtre_where, $data_archive);
        }

        /// Display upload form

        // get valid formats to generate dropdown list
        $fileformatnames = referentiel_get_import_export_formats( 'archive' , 'zformat');
        // get filename
        if (empty($exportfilename)) {
            $exportfilename = referentiel_default_export_filename($course, $referentiel, 'archive');
        }
        echo "\n<br />\n";
        
        echo $OUTPUT->box_start('generalbox  boxaligncenter');
        echo "\n<div align=\"center\">\n";
?>
            <form enctype="multipart/form-data" method="post" action="archive.php?id=<?php echo $cm->id; ?>">
            <fieldset class="invisiblefieldset" style="display: block;">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />

            <table cellpadding="5">
                <tr>
                    <td>
<?php
        echo $txt->fileformat.': ';
        echo html_writer::select($fileformatnames, 'format', 'html', false);
        echo $OUTPUT->help_icon('format_archiveh', 'referentiel');
?>
                    </td>
<?php
        if (!$format_condense){
            echo '<td>'.$txt->pourcentage.' ?<br /><input type="radio" name="format_condense" value="0" checked="checked"/>'.get_string('no').'
<input type="radio" name="format_condense" value="1"/>'.get_string('yes').'
';
        }
        else{
            echo '<td>'.$txt->pourcentage.' ?<br /><input type="radio" name="format_condense" value="0"/>'.get_string('no').'
<input type="radio" name="format_condense" value="1" checked="checked"/>'.get_string('yes').'
';
        }
        echo $OUTPUT->help_icon('format_certificath', 'referentiel');

        echo '</td>
</tr>
';

// fichiers attachés ?
    echo '<tr><td>'.get_string('export_documents','referentiel', $size_data->nfile).' '. get_string('export_file_size','referentiel', display_size($size_data->size)).'</td><td>';
if (!$export_documents){
    echo '<input type="radio" name="export_documents" value="0" checked="checked"/>'.get_string('export_url', 'referentiel').'
<input type="radio" name="export_documents" value="1"/>'.get_string('export_data', 'referentiel')."\n";
}
else{
    echo '<input type="radio" name="export_documents" value="0"/>'.get_string('export_url', 'referentiel').'
<input type="radio" name="export_documents" value="1" checked="checked"/>'.get_string('export_data', 'referentiel')."\n";
}
    echo '</td></tr>'."\n";
        // Pégagogies
        if ($existe_pedagos){
            if ($export_pedagos){
                echo '<tr><td><i>'.get_string('export_pedagos','referentiel').'</i>: </td>
<td>
    <input type="radio" name="export_pedagos" value="0"/>'.get_string('no').'
    <input type="radio" name="export_pedagos" value="1" checked="checked"/>'.get_string('yes').'
</td></tr>'."\n";
            }
            else{
                echo '<tr><td><i>'.get_string('export_pedagos','referentiel').'</i>: </td>
<td>
    <input type="radio" name="export_pedagos" value="0" checked="checked" />'.get_string('no').'
    <input type="radio" name="export_pedagos" value="1" />'.get_string('yes').'
</td></tr>'."\n";
            }
        }
?>

                <tr>
                    <td><?php echo $txt->exportname; ?>:</td>
                    <td align="center">
                        <input type="text" size="60" name="exportfilename" value="<?php echo $exportfilename; ?>" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <input type="submit" name="save" value="<?php echo $txt->exportcertificat; ?>" />
                    </td>
                </tr>
            </table>
            <!-- tous les certificats -->
            <input type="hidden" name="select_all" value="<?php echo $select_all; ?>" />
            <!-- accompagnement -->
            <input type="hidden" name="select_acc" value="<?php echo $select_acc; ?>" />
            <!-- user -->
            <input type="hidden" name="userid" value="<?php echo $userid; ?>" />
            <input type="hidden" name="afficher" value="0" />
            <input type="hidden" name="list_userids" value="<?php echo $list_userids; ?>" />
            </fieldset>
            </form>
            <?php
        echo "\n</div>\n";
        echo $OUTPUT->box_end();
        // Gestion des fichiers d'archives
        $ok_portfolio=(!empty($CFG->enableportfolios) && has_capability('mod/referentiel:archive', $context));
        referentiel_get_manage_archives($context->id, get_string('archives', 'referentiel'), "archive.php?id=$cm->id", $userid_filtre, $referentiel->id, $ok_portfolio, 0, 'file'); // report type = 0

        echo $OUTPUT->footer();
        die();

    }




?>



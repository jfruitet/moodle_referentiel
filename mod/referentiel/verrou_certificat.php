<?php  // $Id: export_certificat.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// GPL
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
* D'apres quiz/export.php 
* Export instance referentiel + certificat
*
* @version $Id: verrou_certificat.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
* @author Martin Dougiamas, Howard Miller, and many others.
*         {@link http://moodle.org}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package referentiel
*/

  require_once('../../config.php');
  require_once('locallib.php');
  require_once('lib_etab.php');
  include('lib_certificat.php');
  include('lib_pedagogie.php');
  include('print_lib_certificat.php');	// AFFICHAGES 

  $exportfilename = optional_param('exportfilename','',PARAM_FILE );
  $format = optional_param('format','', PARAM_FILE );
	
  $id    = optional_param('id', 0, PARAM_INT);    // course module id    
  $d     = optional_param('d', 0, PARAM_INT);    // referentielbase id
	
  $certificat_id   = optional_param('certificat_id', 0, PARAM_INT);    //record certificat id
  $action        = optional_param('action','', PARAM_ALPHA);
  $mode          = optional_param('mode','', PARAM_ALPHANUMEXT);
  $verrou           = optional_param('verrou',0, PARAM_INT);

  // Modif JF 2012/10/07
  $dossieretat           = optional_param('dossieretat',0, PARAM_INT);

  $course        = optional_param('course', 0, PARAM_INT);
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
    $export_filtre= new Object(); // parametres de filtrage

	if (isset($f_promotion)){
		$export_filtre->f_promotion=$f_promotion;
	}
	else{
		$export_filtre->f_promotion='';
	}
	if (isset($f_formation)){
		$export_filtre->f_formation=$f_formation;
	}
	else{
		$export_filtre->f_formation='';
	}
	if (isset($f_pedagogie)){
		$export_filtre->f_pedagogie=$f_pedagogie;
	}
	else{
		$export_filtre->f_pedagogie='';
	}
    if (isset($f_composante)){
		$export_filtre->f_composante=$f_composante;
	}
	else{
		$export_filtre->f_composante='';
	}
    if (isset($f_num_groupe)){
		$export_filtre->f_num_groupe=$f_num_groupe;
	}
	else{
		$export_filtre->f_num_groupe='';
	}

    // INTANCE REFERENTIEL
    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/verrou_certificat.php');

	if ($d) {     // referentiel_referentiel_id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('R�ferentiel id is incorrect');
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
		print_error(get_string('erreurscript','referentiel','Erreur01 : verrou_certificat.php'), 'referentiel');
	}


	if ($certificat_id) { // id certificat
        if (! $record = $DB->get_record('referentiel_certificat', array("id" => "$certificat_id"))) {
            print_error('certificat ID is incorrect');
        }
	}

    // MODIF JF mai 2011
    if ($referentiel->ref_referentiel){
        $existe_pedagos=referentiel_pedagogies_exists($referentiel->ref_referentiel);
    }
    else{
        $existe_pedagos=0;
    }


	// PAS DE RSS
    // require_once("$CFG->libdir/rsslib.php");
    require_login($course->id, false, $cm);

    if (!isloggedin() or isguestuser()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
    }

    // check role capability

    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

    require_capability('mod/referentiel:export', $context);


    if ($certificat_id) {    // So do you have access?
      if (!(has_capability('mod/referentiel:writereferentiel', $context) 
			or referentiel_certificat_isowner($certificat_id)) or !confirm_sesskey() ) {
            print_error(get_string('noaccess','referentiel'));
        }
    }
	
 
	// selecteur
	$userid_filtre=0;
    if (!empty($userid)) {
        $userid_filtre=$userid;
    }
	
	// RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else {
        $form = (object)$_POST;
    }



/// data selection filtre
  if (isset($mode_select) && ($mode_select=='selectetab') && confirm_sesskey() ){
		// gestion des filtres;


// WHERE
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

		// echo "<br />DEBUG :: verrou_certificat.php :: Ligne 265 :: FILTRES : WHERE : $sql_filtre_where \n";
		// exit;

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
 
    // tous les certificats ?
    /// selection utilisateurs accompagn�s
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
        //echo "<br />DEBUG :: verrou_certificat.php :: 201<br />\n";
        //print_r($form);
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
            // echo "<br />DEBUG :: verrou_certificat.php :: 209<br />\n";
            // print_r($form->t_pedago);
            foreach($form->t_pedago as $pedagoid){
                $list_pedagoids.=$pedagoid.',';
                $r_userids[]=referentiel_get_userids_pedagogie($pedagoid, $referentiel_referentiel->id);
            }
        }
        if (!empty($r_userids)){
            // recuperer la liste des utilisateurs
            foreach ($r_userids  as $record_u) {   // afficher la liste d'users
                //echo "<br />DEBUG :: lib_certificat.php :: 193<br />\n";
                //print_r($record_u);
                foreach ($record_u as $ru){
                    //echo "<br />DEBUG :: lib_certificat.php :: 196<br />\n";
                    //print_r($ru);
                    //echo "<br />".$ru->userid;
                    $list_userids.=$ru->userid.',';
                }
            }
            $select_all=2;
        }
        //echo "<br />DEBUG :: verrou_certificat.php :: 212<br />\n";
        //print_r($r_userids);
        //echo "<br />DEBUG :: verrou_certificat.php :: 209 :: LISTE_PEDAGO : $list_pedagoids :: SELECT_ACC : $select_acc :: SELECT_ALL : $select_all\n";

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
    
	if (empty($mode)){
		$mode='verroucertif'; // un seul mode possible
	}
	$currenttab = 'verroucertif';

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
	/// Print the page header
	$strmessage = get_string('gerercertificat','referentiel');
    $strreferentiel = get_string('modulename', 'referentiel');
    $strreferentiels = get_string('modulenameplural', 'referentiel');
    $strpagename=get_string('verroucertificat','referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);
    
	/// RSS and CSS and JS meta
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->requires->css('/mod/referentiel/jauge.css');
    $PAGE->requires->css('/mod/referentiel/referentiel.css');

    $PAGE->set_title($pagetitle);
    $PAGE->navbar->add($strpagename);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

	groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/verrou_certificat.php?d='.$referentiel->id.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc);

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();
    echo '<br />'."\n";
    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('gerercertificath','referentiel').'</h2></div>'."\n";

    if (!empty($action) && (($action=='verrou') || ($action=='dossieretat')) ) {  // La selection est faite
        $records_certificats=referentiel_get_liste_certificats($referentiel, $course, $context, $list_userids, $userid_filtre, $gusers, $select_acc, $select_all);
        if ($records_certificats){
            if ($action=='verrou'){
                echo '<div align="center">'.referentiel_verrouiller_certificats($records_certificats, $verrou).' '.get_string('certiftraite', 'referentiel')."</div>\n";
            }
            elseif ($action=='dossieretat'){
                echo '<div align="center">'.referentiel_fermer_dossier_certificats($records_certificats, $dossieretat).' '.get_string('certiftraite', 'referentiel')."</div>\n";
            }
            echo $OUTPUT->continue_button($CFG->wwwroot.'/mod/referentiel/certificat.php?id='.$cm->id);
            echo $OUTPUT->footer();
            die();
        }
    }
    else{

        // BOITES DE SELECTION
        echo "\n<br />\n";
        echo '<div align="center"><h3><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('selectverroucertificat','referentiel').' '.$OUTPUT->help_icon('selectcertificath','referentiel').'</h3></div>'."\n";
        referentiel_select_liste_certificats($referentiel, $list_pedagoids, $userid_filtre, $gusers, $select_acc, $mode, $CFG->wwwroot . '/mod/referentiel/verrou_certificat.php?d='.$referentiel->id, $select_all, $sql_filtre_where, $export_filtre);


        echo $OUTPUT->box_start('generalbox  boxaligncenter');
        echo "\n".'<div align="center">'."\n";
        echo '<h3>'.get_string('verroucertificat','referentiel').' '.$OUTPUT->help_icon('verroucertificath','referentiel').'</h3>'."\n";

?>

            <form method="post" action="verrou_certificat.php?id=<?php echo $cm->id; ?>">
            <fieldset class="invisiblefieldset" style="display: block;">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />

            <table cellpadding="5">
                <tr>
                    <td>
        <?php
        echo ' <input type="radio" name="verrou" value="1"  checked="checked" /> <b>'.get_string('verrou_all', 'referentiel').'</b> '."\n";
        ?>
        </td><td> &nbsp; &nbsp; &nbsp;</td><td>
        <?php
        echo '<input type="radio" name="verrou" value="0"  /> <b>'.get_string('deverrou_all', 'referentiel').'</b> '."\n";
        ?>
                        </td>
                </tr>
                <tr>
                    <td align="center" colspan="3">
                        <input type="submit" name="save"  />
                        <input type="reset" name="save"  />
             <input type="submit" name="cancel" value="<?php print_string("quit", "referentiel"); ?>" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="action" value="verrou" />

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

    // MODIF JF 2012/10/07
    // Nouvelle fonction de fermeture du dossier numérique
    // Agit sur les certificats.
    // Envisgaer qu'il agisse aussi sur les activités associées...
    // A suivre...
        echo "</div>\n";
        echo $OUTPUT->box_end();
        echo $OUTPUT->box_start('generalbox  boxaligncenter');
        echo "\n".'<div align="center">'."\n";
        echo '<h3>'.get_string('dossier_fermeture','referentiel').' '.$OUTPUT->help_icon('dossier_fermetureh','referentiel').'</h3>'."\n";

?>
            <form method="post" action="verrou_certificat.php?id=<?php echo $cm->id; ?>">
            <fieldset class="invisiblefieldset" style="display: block;">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />

            <table cellpadding="5">
                <tr>
                    <td>
        <?php
        echo '<input type="radio" name="dossieretat" value="1" checked="checked" /> <b>'.get_string('dossier_fermer_all', 'referentiel').'</b> '."\n";
        ?>
        </td><td> &nbsp; &nbsp; &nbsp;</td><td>
        <?php
        echo '<input type="radio" name="dossieretat" value="0" /> <b>'.get_string('dossier_ouvrir_all', 'referentiel').'</b>' ."\n";
        ?>
                        </td>
                </tr>
                <tr>
                    <td align="center" colspan="3">
                        <input type="submit" name="save"  />
                        <input type="reset" name="save"  />
             <input type="submit" name="cancel" value="<?php print_string("quit", "referentiel"); ?>" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="action" value="dossieretat" />

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
        echo "</div>\n";
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->footer();
    die();

?>

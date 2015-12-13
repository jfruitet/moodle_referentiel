<?php  // $Id: print_certificat.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
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
* D'apres quiz/export.php 
* Export instance referentiel + certificat
*
* @version $Id: print_certificat.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
* @author Martin Dougiamas, Howard Miller, and many others.
*         {@link http://moodle.org}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package referentiel
*/
    require(dirname(__FILE__) . '/../../config.php');
  require_once('locallib.php');
  require_once('lib_etab.php');
  include('lib_certificat.php');
  include('lib_pedagogie.php');
  include('print_lib_certificat.php');	// AFFICHAGES 
  include('print_lib.php');	// PRINT	

  $exportfilename = optional_param('exportfilename','',PARAM_FILE );
  $print = optional_param('print','', PARAM_FILE );
	
  $id    = optional_param('id', 0, PARAM_INT);    // course module id    
  $d     = optional_param('d', 0, PARAM_INT);    // referentielbase id
	
  $certificat_id   = optional_param('certificat_id', 0, PARAM_INT);    //record certificat id
  $action        = optional_param('action','', PARAM_ALPHA);
  $mode           = optional_param('mode','', PARAM_ALPHANUMEXT);
  $add           = optional_param('add','', PARAM_ALPHA);
  $update        = optional_param('update', 0, PARAM_INT);
  $delete        = optional_param('delete', 0, PARAM_INT);
  $approve        = optional_param('approve', 0, PARAM_INT);	
  $comment        = optional_param('comment', 0, PARAM_INT);		
  $course        = optional_param('course', 0, PARAM_INT);
  $groupmode     = optional_param('groupmode', -1, PARAM_INT);
  $cancel        = optional_param('cancel', 0, PARAM_BOOL);
  $userid = optional_param('userid', 0, PARAM_INT);

  $list_userids  = optional_param('list_userids', '',PARAM_TEXT);
  $initiale     = optional_param('initiale','', PARAM_ALPHA); // selection par les initiales du nom

  $userids      = optional_param('userids','', PARAM_TEXT); // id user selectionnes par les initiales du nom

  $select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement
  $select_all = optional_param('select_all', 0, PARAM_INT);      // tous les certificats

// formations / pedagogies
  $mode_select     = optional_param('mode_select','', PARAM_ALPHA);
  $list_pedagoids  = optional_param('list_pedagoids', '',PARAM_TEXT);
  $list_userids  = optional_param('list_userids', '',PARAM_TEXT);
  $f_promotion = optional_param('f_promotion', '', PARAM_ALPHANUM);
  $f_formation = optional_param('f_formation', '', PARAM_ALPHANUM);
  $f_pedagogie = optional_param('f_pedagogie', '', PARAM_ALPHANUM);
  $f_composante = optional_param('f_composante', '', PARAM_ALPHANUM);
  $f_num_groupe = optional_param('f_num_groupe', '', PARAM_ALPHANUM);

/*
  $sql_f_where=optional_param('sql_f_where','', PARAM_ALPHANUM);
  $sql_f_order=optional_param('sql_f_order','', PARAM_ALPHANUM);
*/

  $filtre_auteur = optional_param('filtre_auteur', 0, PARAM_INT);
  $filtre_verrou = optional_param('filtre_verrou', 0, PARAM_INT);

  $filtre_validation = optional_param('filtre_validation', 0, PARAM_INT);
  $filtre_referent = optional_param('filtre_referent', 0, PARAM_INT);
  $filtre_date_decision = optional_param('filtre_date_decision', 0, PARAM_INT);
  $filtre_date_modif = optional_param('filtre_date_modif', 0, PARAM_INT);
  $filtre_date_modif_student = optional_param('filtre_date_modif_student', 0, PARAM_INT);

  $sql_filtre_where=optional_param('sql_filtre_where','', PARAM_ALPHA);
  $sql_filtre_order=optional_param('sql_filtre_order','', PARAM_ALPHA);
  $sql_filtre_user=optional_param('sql_filtre_user','', PARAM_ALPHA);

	$data_filtre= new Object(); // parametres de filtrage
	if (isset($filtre_verrou)){
			$data_filtre->filtre_verrou=$filtre_verrou;
	}
	else {
		$data_filtre->filtre_verrou=0;
	}
	if (isset($filtre_date_decision)){
		$data_filtre->filtre_date_decision=$filtre_date_decision;
	}
	else{
		$data_filtre->filtre_date_decision=0;
	}

	if (isset($filtre_validation)){
			$data_filtre->filtre_validation=$filtre_validation;
	}
	else {
		$data_filtre->filtre_validation=0;
	}
	if (isset($filtre_referent)){
		$data_filtre->filtre_referent=$filtre_referent;
	}
	else{
		$data_filtre->filtre_referent=0;
	}
	if (isset($filtre_date_modif_student)){
		$data_filtre->filtre_date_modif_student=$filtre_date_modif_student;
	}
	else{
		$data_filtre->filtre_date_modif_student=0;
	}
	if (isset($filtre_date_modif)){
		$data_filtre->filtre_date_modif=$filtre_date_modif;
	}
	else{
		$data_filtre->filtre_date_modif=0;
	}
	if (isset($filtre_auteur)){
		$data_filtre->filtre_auteur=$filtre_auteur;
	}
	else{
		$data_filtre->filtre_auteur=0;
	}

    // DEBUG
    // print_object($data_filtre);
    // exit;


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


    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/print_certificat.php');

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
    // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : print_certificat.php'), 'referentiel');
	}


	if ($certificat_id) { // id certificat
        if (! $record = $DB->get_record('referentiel_certificat', array("id" => "$certificat_id"))) {
            print_error('certificat ID is incorrect');
        }
	}

    // get display strings
    $txt = new object;
    $txt->referentiel = get_string('referentiel','referentiel');
    $txt->download = get_string('download','referentiel');
    $txt->downloadextra = get_string('downloadextra','referentiel');
    $txt->exporterror = get_string('exporterror','referentiel');
    $txt->exportname = get_string('exportname','referentiel');
    $txt->printcertificat = get_string('printcertificat', 'referentiel');
    $txt->fileprint = get_string('fileprint','referentiel');
    $txt->modulename = get_string('modulename','referentiel');
    $txt->modulenameplural = get_string('modulenameplural','referentiel');
    //$txt->tofile = get_string('tofile','referentiel');
	
	// PAS DE RSS
    // require_once("$CFG->libdir/rsslib.php");

	
    require_login($course->id, false, $cm);   // pas d'autologin guest

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

    /// If it's hidden then it's don't show anything.  :)
	/// Some capability checks.
    if (empty($cm->visible)
    && (
        !has_capability('moodle/course:viewhiddenactivities', $context)
            &&
        !has_capability('mod/referentiel:managecomments', $context)
        )

    ) {
        print_error(get_string("activityiscurrentlyhidden"),'error',"$CFG->wwwroot/course/view.php?id=$course->id");
    }

	// ensure the files area exists for this course
    // Moodle 1.9
    // make_upload_directory( "$course->id/$CFG->moddata/referentiel" );

    if ($certificat_id) {    // So do you have access?
        if (!(has_capability('mod/referentiel:writereferentiel', $context) 
			or referentiel_certificat_isowner($certificat_id)) or !confirm_sesskey() ) {
            print_error(get_string('noaccess','referentiel'));
        }
    }
	
    // MODIF JF mai 2011
    if ($referentiel->ref_referentiel){
        $existe_pedagos=referentiel_pedagogies_exists($referentiel->ref_referentiel);
    }
    else{
        $existe_pedagos=0;
    }

	// parametres d'impression
	$param=referentiel_get_param_configuration($referentiel->id, 'config_impression'); 
	// DEBUG
	// echo "<br />DEBUG :: print_certificat.php :: 168 \n";
	// print_r($param);
	// exit;

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


    /// data selection filtre pedagogie / formation
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

		// echo "<br />DEBUG :: export_certificat.php :: Ligne 265 :: FILTRES : WHERE : $sql_filtre_where \n";
		// exit;

  }

	if ($cancel) {
        if (!empty($SESSION->returnpage)) {
            $return = $SESSION->returnpage;
            unset($SESSION->returnpage);
            redirect($return);
        }
        else {
            redirect('certificat.php?id='.$cm->id);
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
        //echo "<br />DEBUG :: print_certificat.php :: 201<br />\n";
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
            foreach($form->t_pedago as $pedagoid){
                $list_pedagoids.=$pedagoid.',';
                $r_userids[]=referentiel_get_userids_pedagogie($pedagoid, $referentiel_referentiel->id);
            }
        }
        if (!empty($r_userids)){
            // recuperer la liste des utilisateurs
            foreach ($r_userids  as $record_u) {   // afficher la liste d'users
                 foreach ($record_u as $ru){
                    $list_userids.=$ru->userid.',';
                }
            }
            $select_all=2;
        }

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

    if (!empty($print) && !empty($referentiel) && !empty($course)) {
        if (!confirm_sesskey()) {
            print_error( 'sesskey' );
        }

		// selections
		if (isset($form->certificat_sel_referentiel) && ($form->certificat_sel_referentiel=="1")){
			$param->certificat_sel_referentiel=1;
		}
		else{
			$param->certificat_sel_referentiel=0;
		}
		if (isset($form->certificat_sel_referentiel_instance) && ($form->certificat_sel_referentiel_instance=="1")){
			$param->certificat_sel_referentiel_instance=1;
		}
		else{
			$param->certificat_sel_referentiel_instance=0;
		}

		if (isset($form->certificat_sel_etudiant_numero) && ($form->certificat_sel_etudiant_numero=="1")){
			$param->certificat_sel_etudiant_numero=1;
		}
		else{
			$param->certificat_sel_etudiant_numero=0;
		}
		if (isset($form->certificat_sel_etudiant_nom_prenom) && ($form->certificat_sel_etudiant_nom_prenom=="1")){
			$param->certificat_sel_etudiant_nom_prenom=1;
		}
		else{
			$param->certificat_sel_etudiant_nom_prenom=0;
		}

		if (isset($form->certificat_sel_etudiant_etablissement) && ($form->certificat_sel_etudiant_etablissement=="1")){
			$param->certificat_sel_etudiant_etablissement=1;
		}
		else{
			$param->certificat_sel_etudiant_etablissement=0;
		}

		if (isset($form->certificat_sel_etudiant_ddn) && ($form->certificat_sel_etudiant_ddn=="1")){
			$param->certificat_sel_etudiant_ddn=1;
		}
		else{
			$param->certificat_sel_etudiant_ddn=0;
		}

		if (isset($form->certificat_sel_etudiant_lieu_naissance) && ($form->certificat_sel_etudiant_lieu_naissance=="1")){
			$param->certificat_sel_etudiant_lieu_naissance=1;
		}
		else{
			$param->certificat_sel_etudiant_lieu_naissance=0;
		}

		if (isset($form->certificat_sel_etudiant_adresse) && ($form->certificat_sel_etudiant_adresse=="1")){
			$param->certificat_sel_etudiant_adresse=1;
		}
		else{
			$param->certificat_sel_etudiant_adresse=0;
		}

		if (isset($form->certificat_sel_certificat_detail) && ($form->certificat_sel_certificat_detail=="1")){
			$param->certificat_sel_certificat_detail=1;
		}
		else{
			$param->certificat_sel_certificat_detail=0;
		}

		if (isset($form->certificat_sel_certificat_pourcent) && ($form->certificat_sel_certificat_pourcent=="1")){
			$param->certificat_sel_certificat_pourcent=1;
		}
		else{
			$param->certificat_sel_certificat_pourcent=0;
		}
		if (isset($form->certificat_sel_activite_competences) && ($form->certificat_sel_activite_competences=="1")){
			$param->certificat_sel_activite_competences=1;
		}
		else{
			$param->certificat_sel_activite_competences=0;
		}
		if (isset($form->certificat_sel_certificat_competences) && ($form->certificat_sel_certificat_competences=="1")){
			$param->certificat_sel_certificat_competences=1;
		}
		else{
			$param->certificat_sel_certificat_competences=0;
		}
		if (isset($form->certificat_sel_certificat_referents) && ($form->certificat_sel_certificat_referents=="1")){
			$param->certificat_sel_certificat_referents=1;
		}
		else{
			$param->certificat_sel_certificat_referents=0;
		}

		if (isset($form->certificat_sel_decision_jury) && ($form->certificat_sel_decision_jury=="1")){
			$param->certificat_sel_decision_jury=1;
		}
		else{
			$param->certificat_sel_decision_jury=0;
		}
		if (isset($form->certificat_sel_commentaire) && ($form->certificat_sel_commentaire=="1")){
			$param->certificat_sel_commentaire=1;
		}
		else{
			$param->certificat_sel_commentaire=0;
		}
		// enregitrer les param�tres ?
		if (isset($form->sauver_parametre) && ($form->sauver_parametre=="1")){
			referentiel_set_param_configuration($param, $referentiel->id, 'config_impression');
		}

    }

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


     if (!empty($print) && !empty($referentiel) && !empty($course)) {   // format d'impression choisi
        $records_certificats=referentiel_get_liste_certificats($referentiel, $course, $context, $list_userids, $userid_filtre, $gusers, $select_acc, $select_all);
        // TRAITEMENT SPECIAL PDF / RTF / DOC / ODT
        if ($print=="rtf"){
                require_once("print_rtf.php");

			    // ************************** INITIALISATION RTF *********************
                $file_logo=referentiel_get_logo($referentiel_referentiel);
                if ($file_logo!=""){
                    $image_logo=referentiel_get_file($file_logo);
                }
                else{
                    $image_logo="";
                }
                // Instanciation de la classe d�riv�e
                // A4 paysage en mm
                $nom_fichier = 'certification-'.date("Ymshis").'-'.md5(uniqid());
                $rtf=new RTFClass();
                $copyright = chr(169);
                $registered ="�";
                $puce =  chr(149);

                rtf_write_certification($referentiel, $referentiel_referentiel, $userid, $param, $records_certificats);
				// DEBUG
				//echo "<br />DEBUG !! 597 ::  NOM FICHIER :  $nom_fichier\n";
				//exit;
				$rtf->sendRtf($nom_fichier);
                exit;
        }
        elseif ($print=="pdf"){

                    require_once("print_pdf.php");
			
                    // ************************** INITIALISATION PDF *********************
                    $file_logo=referentiel_get_logo($referentiel_referentiel);
                    if ($file_logo!=""){
                        $image_logo=referentiel_get_file($file_logo);
                    }
                    else{
                        $image_logo="";
                    }
                    // Instanciation de la classe derivee
                    // A4 paysage en mm
                    $Refpdf=new Referentiel_PDF('P','mm','A4');
                    $Refpdf->SetDisplayMode('real');
                    $Refpdf->Open();
                    $copyright = chr(169);
                    $registered ="�";
                    $puce =  chr(149);                    
                    $Refpdf->getAliasNbPages();
                     
                    pdf_write_certification($referentiel, $referentiel_referentiel, $userid, $param, $records_certificats);
                    $Refpdf->Output();
                    exit;
        }
        elseif ($print=="msword"){
                    require_once("print_doc_word.php");
                    
                    // ************************** INITIALISATION MSWORD *********************
                    $file_logo=referentiel_get_logo($referentiel_referentiel);
                    if ($file_logo!=""){
                    	$image_logo=referentiel_get_file($file_logo);
                    }
                    else{
                    	$image_logo="";
                    }
                    // Instanciation de la classe d�riv�e
                    // A4 paysage en mm
                    $mswd=new MSWord();        
                    $mswd->SetEntete();

                    $copyright = chr(169);
                    $registered ="�";
                    $puce =  chr(149);
                    $mswd->SetAutoPageBreak(1, 290);     
                    $mswd->SetCol(0);
                    msword_write_certification($referentiel, $referentiel_referentiel, $userid, $param, $records_certificats);
                    $mswd->SetEnqueue();
                    exit;
        }
        elseif ($print=="ooffice"){
                    require_once("print_doc_odt.php");
                    
                    // ************************** INITIALISATION OpenOffice *********************
                    $file_logo=referentiel_get_logo($referentiel_referentiel);
                    if ($file_logo!=""){
                    	$image_logo=referentiel_get_file($file_logo);
                    }
                    else{
                    	$image_logo="";
                    }
                    // Instanciation de la classe d�riv�e
                    // A4 paysage en mm
                    $odt=new OOffice();        
                    $odt->SetEntete();

                    $copyright = chr(169);
                    $registered ="�";
                    $puce =  chr(149);
                    $odt->SetAutoPageBreak(1, 290);     
                    $odt->SetCol(0);
                    ooffice_write_certification($referentiel, $referentiel_referentiel, $userid, $param, $records_certificats);
                    $odt->SetEnqueue();
                    exit;
        }
        else{
        	$defaultprint = FORMAT_MOODLE;

            /// Print the tabs
            if (empty($mode)){
                $mode='printcertif'; // un seul mode possible
        	}
            $currenttab = 'printcertif';

            // Moodle 2
            $url->param('mode', $mode);

            /// Mark as viewed  ??????????? A COMMENTER
            $completion=new completion_info($course);
            $completion->set_module_viewed($cm);

            // AFFICHAGE DE LA PAGE Moodle 2

			/// Print the page header
			$strreferentiels = get_string('modulenameplural','referentiel');
			$strreferentiel = get_string('referentiel','referentiel');
			$strmessage = get_string('printcertificat','referentiel');
			$strpagename=get_string('printcertificat','referentiel');
            $strlastmodified = get_string('lastmodified');
            $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));

            $icon = $OUTPUT->pix_url('icon','referentiel');

            $PAGE->set_url($url);

            /// RSS and CSS and JS meta
            $PAGE->requires->css('/mod/referentiel/jauge.css');
            $PAGE->requires->css('/mod/referentiel/referentiel.css');
            $PAGE->requires->css('/mod/referentiel/referentiel.css');

            $PAGE->set_title($pagetitle);
            $PAGE->navbar->add($strpagename);
            $PAGE->set_heading($course->fullname);

            echo $OUTPUT->header();

            groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/print_certificat.php?d='.$referentiel->id.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc);

            if (!empty($referentiel->name)){
                echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
            }

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();

            echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('printcertificath','referentiel').'</h2></div>'."\n";

            // impression
            if (! is_readable("print/$print/print.php")) {
    	        print_error( "print not known ($print)" );
            }
            // load parent class for import/export
            require("print.php");

            // and then the class for the selected print
            require("print/$print/print.php");
            $classname = "pprint_$print";
            $pprint = new $classname();

            // $pprint->setCategory( $category );
            $pprint->setUserid($userid);
            $pprint->setParam( $param); // paraemtres de selection
            $pprint->setCoursemodule( $cm );
            $pprint->setCourse( $course );
            $pprint->setContext( $context);
            $pprint->setFilename( $exportfilename );
            $pprint->setReferentielInstance($referentiel);
            $pprint->setReferentielReferentiel($referentiel_referentiel);
            $pprint->setEmpreintes($referentiel_referentiel);
            $pprint->setPoids($referentiel_referentiel);
            $pprint->setRCertificats($records_certificats);

            if (! $pprint->exportpreprocess()) {   // Do anything before that we need to
                        print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/print_certificat.php?id='.$cm->id);
            }
            if (! $pprint->exportprocess($userid)) {         // Process the export data
                        print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/print_certificat.php?id='.$cm->id);
            }
            if (! $pprint->exportpostprocess()) {                            // In case anything needs to be done after
                        print_error( $txt->exporterror, $CFG->wwwroot.'/mod/referentiel/print_certificat.php?id='.$cm->id);
            }
            echo "<hr />";
            // link to download the finished file
            $file_ext = $pprint->export_file_extension();
            $fullpath = '/'.$context->id.'/mod_referentiel/certificat/0'.$pprint->get_export_dir().$exportfilename.$file_ext;
            $efile = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath);

            echo "<p><div class=\"boxaligncenter\"><a href=\"$efile\">$txt->download</a></div></p>";
            echo "<p><div class=\"boxaligncenter\"><font size=\"-1\">$txt->downloadextra</font></div></p>";

            echo $OUTPUT->continue_button($CFG->wwwroot.'/mod/referentiel/certificat.php?id='.$cm->id);

            echo $OUTPUT->footer();
            die();
        }
    }
    else{ // BOITES DE SELECTION
        // affichage des boites de selection des utilisateurs
        $defaultprint = FORMAT_MOODLE;

        /// Print the tabs
		if (!isset($mode)){
            $mode='printcertif'; // un seul mode possible
		}
		$currenttab = 'printcertif';

        // Moodle 2
        $url->param('mode', $mode);

        /// Mark as viewed  ??????????? A COMMENTER
        $completion=new completion_info($course);
        $completion->set_module_viewed($cm);

        // AFFICHAGE DE LA PAGE Moodle 2
        /// Print the page header
		$strreferentiels = get_string('modulenameplural','referentiel');
		$strreferentiel = get_string('referentiel','referentiel');
		$strmessage = get_string('printcertificat','referentiel');
		$strpagename=get_string('printcertificat','referentiel');
        $strlastmodified = get_string('lastmodified');
        $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));

        $icon = $OUTPUT->pix_url('icon','referentiel');

        $PAGE->set_url($url);
        $PAGE->requires->css('/mod/referentiel/jauge.css');
        $PAGE->requires->css('/mod/referentiel/referentiel.css');
        $PAGE->requires->css('/mod/referentiel/referentiel.css');
        // $PAGE->requires->js($OverlibJs);
        // $PAGE->requires->js('/mod/referentiel/functions.js');

        $PAGE->set_title($pagetitle);
        $PAGE->navbar->add($strpagename);
        $PAGE->set_heading($course->fullname);

        echo $OUTPUT->header();

        groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/print_certificat.php?d='.$referentiel->id.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc);

        if (!empty($referentiel->name)){
            echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
        }
        
    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();
    echo '<br />'."\n";
        echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('select_print_certificat','referentiel').' '.$OUTPUT->help_icon('selectcertificath','referentiel').'</h2></div>'."\n";
        referentiel_select_liste_certificats($referentiel, $list_pedagoids, $userid_filtre, $gusers, $select_acc, $mode, $CFG->wwwroot . '/mod/referentiel/print_certificat.php?d='.$referentiel->id, $select_all, $sql_filtre_where, $export_filtre);
        // Gestion des fichiers d'archives
        referentiel_get_manage_files($context->id, 'certificat', 0, get_string('printedcertificates', 'referentiel'), "print_certificat.php?id=$cm->id");
        echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('printcertificath','referentiel').'</h2></div>'."\n";
        // liste des certificats selectionnes
        referentiel_resume_liste_certificats($initiale, $userids, $referentiel, $userid_filtre, $gusers, $sql_filtre_where, $sql_filtre_order, $data_filtre, $select_acc);

        /// Display upload form
        // get valid prints to generate dropdown list
        $fileprintnames = referentiel_get_print_formats( 'print', 'pprint' );
        // get filename
        if (empty($exportfilename)) {
            $exportfilename = referentiel_default_print_filename($course, $referentiel, 'certificat');
        }

        $modform='print_certificat.html';

        echo "\n<br />\n";

        // print_box_start('generalbox boxwidthnormal boxaligncenter');
        echo $OUTPUT->box_start('generalbox  boxaligncenter');
            
        if (file_exists($modform)) {
			$defaultformat = FORMAT_PLAIN;
            $editorfields = '';
        }
        else {
                notice("ERREUR : No file found at : $modform)", "print_certificat.php?d=$referentiel->id");
        }

        include_once($modform);
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        die();
    }
?>

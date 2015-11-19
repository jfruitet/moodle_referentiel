<?php  // $Id: accompagnement.php,v 1.0 2008/05/03 00:00:00 jfruitet Exp $
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

    require_once('../../config.php');
    require_once('locallib.php');

    include('print_lib_accompagnement.php');	// ACCOMPAGNEMENT
    require_once('lib_repartition.php');	// REPARTION COMPETENCES
    require_once('print_lib_repartition.php');	// REPARTION COMPETENCES

	// PAS DE RSS
    // require_once("$CFG->libdir/rsslib.php");

    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel instance id
    $group = optional_param('group', -1, PARAM_INT);   // choose the current group

    $action  	= optional_param('action','', PARAM_ALPHANUMEXT); // pour distinguer differentes formes de traitements
    $mode         = optional_param('mode','', PARAM_ALPHA);
    $old_mode     = optional_param('old_mode','', PARAM_ALPHA); // mode anterieur
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $groupmode    = optional_param('groupmode', -1, PARAM_INT);
    $cancel       = optional_param('cancel', 0, PARAM_BOOL);
    $userid       = optional_param('userid', 0, PARAM_INT);

    $mode_select  = optional_param('mode_select','', PARAM_ALPHANUMEXT);
    $select_acc   = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    // Filtres
    require_once('filtres.php'); // Ne pas deplacer

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/accompagnement.php');

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
    // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : accompagnement.php'), 'referentiel');
	}

    $returnlink_ref = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));
    $returnlink_course = new moodle_url('/course/view.php', array('id'=>$course->id));
    $returnlink_add = new moodle_url('/mod/referentiel/add.php', array('d'=>$referentiel->id, 'sesskey'=>sesskey()));
	$returnlink_accompagnement= new moodle_url('/mod/referentiel/accompagnement.php', array('d'=>$referentiel->id));
	$returnlink_activite= new moodle_url('/mod/referentiel/activite.php', array('d'=>$referentiel->id));

    require_login($course->id, false, $cm);
    if (!isloggedin() || isguestuser()) {
        redirect($returnlink_course);
    }

    $context = context_module::instance($cm->id);

	/// If it's hidden then it's don't show anything.  :)
	/// Some capability checks.
    if (empty($cm->visible)
    && (
        !has_capability('moodle/course:viewhiddenactivities', $context)
            &&
        !has_capability('mod/referentiel:managecomments', $context)
        )

    ) {
        print_error(get_string("activityiscurrentlyhidden"),'error',$returnlink_course);
    }


    // RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else{
      $form = (object)$_POST;
    }
    /*
         // DEBUG
        echo "<br />DEBUG :: accompagnement.php :: Ligne 166\n";
        print_r($form);
        exit;
	*/
  // selecteur
	$userid_filtre=0;
	if (isset($userid) && ($userid>0)){
		$userid_filtre=$userid;
	}
	else if (isset($form->userid) && ($form->userid>0)){
		$userid_filtre=$form->userid;
	}
		// DEBUG
		// echo "<br />$userid_filtre\n";
		// exit;

	/// selection filtre
  if (isset($mode_select) && ($mode_select=='selectetab') && confirm_sesskey() ){
		// gestion des filtres;
		$sql_f_where='';
		$sql_f_order='';

		if (isset($f_validation) && ($f_validation=='1')){
			if ($sql_f_where!='')
				$sql_f_where.=' AND approved=\'1\' ';
			else
				$sql_f_where.=' AND approved=\'1\' ';
		}
		else if (isset($f_validation) && ($f_validation=='-1')){
			if ($sql_f_where!='')
				$sql_f_where.=' AND approved=\'0\' ';
			else
				$sql_f_where.=' AND approved=\'0\' ';
		}
		if (isset($f_referent) && ($f_referent=='1')){
			if ($sql_f_where!='')
				$sql_f_where.=' AND teacherid<>0  ';
			else
				$sql_f_where.=' AND teacherid<>0  ';
		}
		else if (isset($f_referent) && ($f_referent=='-1')){
			if ($sql_f_where!='')
				$sql_f_where.=' AND teacherid=0  ';
			else
				$sql_f_where.=' AND teacherid=0  ';
		}

		if (isset($f_date_modif) && ($f_date_modif=='1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_modif ASC ';
			else
				$sql_f_order.=' date_modif ASC ';
		}
		else if (isset($f_date_modif) && ($f_date_modif=='-1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_modif DESC ';
			else
				$sql_f_order.=' date_modif DESC ';
		}

		if (isset($f_date_modif_student) && ($f_date_modif_student=='1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_modif_student ASC ';
			else
				$sql_f_order.=' date_modif_student ASC ';
		}
		else if (isset($f_date_modif_student) && ($f_date_modif_student=='-1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_modif_student DESC ';
			else
				$sql_f_order.=' date_modif_student DESC ';
		}

		if (isset($f_auteur) && ($f_auteur=='1')){
			if ($sql_f_order!='')
				$sql_f_order.=', userid ASC ';
			else
				$sql_f_order.=' userid ASC ';
		}
		else if (isset($f_auteur) && ($f_auteur=='-1')){
			if ($sql_f_order!='')
				$sql_f_order.=', userid DESC ';
			else
				$sql_f_order.=' userid DESC ';
		}


		// echo "<br />DEBUG :: accompagnement.php :: Ligne 162 :: FILTRES : $sql_f_where $sql_f_order\n";

    }

	/// selection d'utilisateurs

    // accompagnement
	if (!isset($select_acc)){
        if (isset($form->select_acc)){
            $select_acc=$form->select_acc;
        }
        else{
            $select_acc=(referentiel_has_pupils($referentiel->id, $course->id, $USER->id)>0);
        }
        // DEBUG
        // echo "<br />DEBUG :: accompagnement.php :: 280 :: ACCOMPAGNEMENT : $select_acc<br />\n";
    }

	if ($cancel) {
	      // DEBUG
        // echo "<br />DEBUG :: accompagnement.php :: Ligne 285 CANCEL : $cancel SELECT_ACC : $select_acc\n";
        // print_r($form);
        // exit;
        if (isset($form->select_acc)){
          $select_acc=$form->select_acc;
        }

		$mode ='listactivityall';
		if (has_capability('mod/referentiel:managecertif', $context)){
	           $SESSION->returnpage = $returnlink_activite;
		}
		else{
	        $SESSION->returnpage = new moodle_url('/mod/referentiel/activite.php', array('d'=>$referentiel->id, 'select_acc'=>$select_acc, 'userid'=>$userid,'mode'=>$mode));
		}
        $return = $SESSION->returnpage;
        unset($SESSION->returnpage);
    	redirect($return);

       exit;
    }

    	// selection utilisateurs accompagnés
    if (isset($action) && ($action=='select_acc')){
		  if (isset($form->select_acc) && confirm_sesskey() ){
		  	$select_acc=$form->select_acc;
		  }
		  if (isset($form->mode) && ($form->mode!='')){
			 $mode=$form->mode;
		  }
		  // echo "<br />ACTION : $action  SEARCH : $userid_filtre\n";
		  unset($form);
		  unset($action);
		  // exit;
    }

    // utilisateur
    if (isset($action) && ($action=='selectuser')){
		  if (isset($form->userid) && ($form->userid>0)
			&& confirm_sesskey() ){
		  	$userid_filtre=$form->userid;
			 // DEBUG
		  }
		  if (isset($form->select_acc)){
		  	$select_acc=$form->select_acc;
		  }

		  if (isset($form->mode) && ($form->mode!='')){
			 $mode=$form->mode;
		  }
		  // echo "<br />ACTION : $action  SEARCH : $userid_filtre\n";
		  unset($form);
		  unset($action);
		  // exit;
    }

    // REPARTITION DES COMPETENCES ENTRE REFERENTS
    if (isset($action) && ($action=='selectrepartition')
        && isset($form->mode) && ($form->mode=='suivi')
        && confirm_sesskey() )
    {

        // accompagnement
        if (isset($form->select_acc)){
		  	$select_acc=$form->select_acc;
        }
        if (!empty($form->competences_list)){
                $codes_competences_reparties=explode(',',$form->competences_list);
        }
        if (isset($form->mode) && ($form->mode!='')){
            $mode=$form->mode;
        }

        if (!empty($form->teachers_list)){
            $teachersids=explode(',',$form->teachers_list);
            if ($teachersids){
                foreach($teachersids as $tid){
                    foreach ($codes_competences_reparties as $code_item){
                        $ok=false;
                        $i=0;
                        if (!empty($form->t_teachers[$tid])){
                            while (!$ok && ($i<count($form->t_teachers[$tid]))){
                                if ($form->t_teachers[$tid][$i]==$code_item){
                                    $ok=true;
                                    referentiel_set_association_competence_teacher($referentiel->id, $referentiel_referentiel->id, $course->id, $tid, $code_item);
                                }
                                $i++;
                            }

                            if (!$ok){
                                referentiel_delete_association_competence_teacher($referentiel->id, $referentiel_referentiel->id, $course->id, $tid, $code_item);
                            }

                        }

                        else{
                            referentiel_delete_association_competence_teacher($referentiel->id, $referentiel_referentiel->id, $course->id, $tid, $code_item);
                        }
                    }
                }
            }
        }
        
        unset($form);
        unset($action);
        // exit;
    }

    // ACCOMPAGNEMENT

    if (isset($action) && ($action=='selectaccompagnement')
        && isset($form->mode) && ($form->mode=='accompagnement')
		&& confirm_sesskey() ){

		  // accompagnement
        if (isset($form->select_acc)){
		  	$select_acc=$form->select_acc;
        }
			
        if (!empty($form->users_list)){
            $usersids=explode(',',$form->users_list);
        }
        if (isset($form->mode) && ($form->mode!='')){
            $mode=$form->mode;
        }

        if (!empty($form->teachers_list)){
            $teachersids=explode(',',$form->teachers_list);
            if ($teachersids){

                foreach($teachersids as $tid){
                    foreach ($usersids as $uid){
                        $ok=false;
                        $i=0;
                        if (!empty($form->t_teachers[$tid])){
                            while (!$ok && ($i<count($form->t_teachers[$tid]))){
                                if ($form->t_teachers[$tid][$i]==$uid){
                                    $ok=true;
                                    referentiel_set_association_user_teacher($referentiel->id, $course->id, $uid, $tid, $form->type);
                                }
                                $i++;
                            }

                            if (!$ok){
                                referentiel_delete_association_user_teacher($referentiel->id, $course->id, $uid, $tid);
                            }
                        }
                        else{
                            referentiel_delete_association_user_teacher($referentiel->id, $course->id, $uid, $tid);
                        }
                    }
                }
            }
        }
          
        unset($form);
        unset($action);
		// exit;
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


	/// tabs
	if (empty($mode) || ($mode=="menuacc")){
        $mode='accompagnement';
	}
    $currenttab = $mode;

    /// Mark as viewed  ??????????? A COMMENTER
    //$completion=new completion_info($course);
    //$completion->set_module_viewed($cm);

// AFFICHAGE DE LA PAGE Moodle 2
    $stractivite = get_string('accompagnement','referentiel');
    $strreferentiel = get_string('modulename', 'referentiel');
    $strreferentiels = get_string('modulenameplural', 'referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

	// Moodle 2
    $url->param('mode', $mode);

    $PAGE->set_url($url);
    $PAGE->set_context($context);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    //if ($CFG->version < 2011120100) $PAGE->requires->js('/lib/overlib/overlib.js');  else
    $PAGE->requires->js($OverlibJs);
    $PAGE->requires->js('/mod/referentiel/functions.js');
    $PAGE->navbar->add($stractivite);
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();
    if ($mode!='suivi'){
    	groups_print_activity_menu($cm,  $CFG->wwwroot . '/mod/referentiel/accompagnement.php?d='.$referentiel->id.'&amp;mode='.$mode.'&amp;select_acc='.$select_acc);
    }
    
    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, $data_f, $mode);
    $tab_onglets->display();
    echo '<br />'."\n";
	if  ($mode=='suivi'){
        echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('repartition','referentiel').' '.$OUTPUT->help_icon('repartitionh','referentiel').'</h2></div>'."\n";

        if (has_capability('mod/referentiel:managecertif', $context)) {
            referentiel_select_repartition($mode, $referentiel, $USER->id, $select_acc);
        }
        else if (has_capability('mod/referentiel:write', $context)){
            referentiel_print_suivi_user($mode, $referentiel, $userid_filtre, $gusers, $sql_f_where, $sql_f_order, $data_f, $select_acc);
        }
        else{
            referentiel_print_liste_repartitions($referentiel);
        }
	}
	elseif  ($mode=='notification'){
        echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('repartition_notification','referentiel').' '.$OUTPUT->help_icon('rnotificationh','referentiel').'</h2></div>'."\n";

        if (has_capability('mod/referentiel:write', $context)){
            referentiel_print_suivi_user($mode, $referentiel, $userid_filtre, $gusers, $sql_f_where, $sql_f_order, $data_f, $select_acc);
        }
        else{
            referentiel_print_liste_repartitions($referentiel);
        }
	}

	else {
        echo '<div align="center"><h2><img src="'.$icon.'" border="0" title="" alt="" /> '.get_string('accompagnement','referentiel').' '.$OUTPUT->help_icon('accompagnementh','referentiel').'</h2></div>'."\n";

        if (has_capability('mod/referentiel:managecertif', $context)) {
            referentiel_select_accompagnement($mode, $referentiel, $USER->id, $userid_filtre, $gusers, $select_acc);
        }
        else if (has_capability('mod/referentiel:write', $context)){
            referentiel_print_liste_accompagnements($referentiel, $userid_filtre, $gusers, $select_acc);
        }
	}
    echo $OUTPUT->footer();
    die();

?>

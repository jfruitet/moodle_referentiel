<?php  // $Id: tabs.php,v 1.24.2.5 2007/09/24 17:15:31 skodak Exp $
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
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set

/**
 * Standard base class .
 *
 * @package   mod-referentiel
 * @copyright 2011 onwards Jean Fruitete {@link http://www.univ-nantes.fr/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Onglets {

var $select_acc; // accompagnement
var $filtres; // selections diverses
var $course; // course object
var $cm; // course_module  object
var $instance; // instance object
var $occurrence; // occurrence object
var $currenttab; // table active dans les onglets
var $mode;
var $tfiltre;

function onglets($context=NULL, $instance=NULL, $occurrence=NULL, $cm=NULL, $course=NULL, $currenttab=0, $select_acc=0, $filtres=NULL, $mode='') {

    if (empty($currenttab)) {
        $this->currenttab = 'referentiel';
    }
    else{
        $this->currenttab = $currenttab;
    }

    // Accompagnement
    if (!isset($select_acc)){
        $this->select_acc=0 ;
    }
    else{
        $this->select_acc=$select_acc;
    }

    // Filtres
    if (!empty($filtres)){
        $this->tfiltre=array(
                'f_auteur'=>$filtres->f_auteur,
                'f_validation'=>$filtres->f_validation,
                'f_referent'=>$filtres->f_referent,
                'f_date_modif'=>$filtres->f_date_modif,
                'f_date_modif_student'=>$filtres->f_date_modif_student);
    }

    $this->tfiltre['select_acc']=$select_acc;

    if (!empty($context)) {$this->context=$context;}
    if (!empty($instance)) {$this->instance=$instance;}
    if (!empty($occurrence)) {$this->occurrence=$occurrence;}
    if (!empty($cm)) {$this->cm=$cm;}
    if (!empty($course)) {$this->course=$course;}
    $this->mode=$mode;

}

// --------------------
function display(){
    global $USER;
    global $CFG;
    
    if (!empty($this->instance) && !empty($this->course) && !empty($this->cm)) {
        // Rôles dans le cours
        $roles=referentiel_roles_in_instance($this->instance->id);
        //print_object($roles);
        //exit;
        $isadmin=$roles->is_admin;
        $isstudent=$roles->is_student;
        $isguest=$roles->is_guest;
        $isreferentielauteur=referentiel_is_author($USER->id, $this->occurrence, (!($isstudent or $isguest) ));
        // verifier si l'utilisateur peut ajouter des activites ou modifier son certificat
        $edition_autorisee=true;
        if ($isstudent){
            $edition_autorisee=!referentiel_certificat_user_is_closed($USER->id, $this->occurrence->id);
        }

        $tabs = array();
        $row  = array();
        $inactive = NULL;
        $activetwo = NULL;

        $url_param=array('id'=>$this->cm->id, 'non_redirection'=>'0', 'sesskey'=>sesskey());
        // echo "<br>DEBUG :: onglets.php :: 105\n";
        // print_object($this->tfiltre);

        foreach ($this->tfiltre as $key=>$value){
            // echo '<br>'.$key.' : '.$value;
            $url_param[$key]=$value;
        }

        //echo "<br>DEBUG :: onglets.php :: 108\n";
        //print_object($url_param);
        //exit;
        // premier onglet
        if (has_capability('mod/referentiel:view', $this->context)) {
           $url_param['non_redirection']=1;
	       $row[] = new tabobject('referentiel',  new moodle_url('/mod/referentiel/view.php', $url_param), get_string('referentiel','referentiel'));
	       $url_param['non_redirection']=0;
        }

        if (isloggedin()) {
            // accompagnement
            if (has_capability('mod/referentiel:write', $this->context)) {
                $url_param['mode']='accompagnement';
                $row[] = new tabobject('menuacc', new moodle_url('/mod/referentiel/accompagnement.php', $url_param), get_string('accompagnement', 'referentiel'));
            }

            // activites
            if (referentiel_user_can_addactivity($this->instance)) {
                $url_param['mode']='list';
                $row[] = new tabobject('list', new moodle_url('/mod/referentiel/activite.php?id', $url_param), get_string('edit_activity', 'referentiel'));
            }

            // taches
            if (has_capability('mod/referentiel:addtask', $this->context) || has_capability('mod/referentiel:viewtask', $this->context)) {
                $url_param['mode']='listtasksingle';
                $row[] = new tabobject('task', new moodle_url('/mod/referentiel/task.php?', $url_param), get_string('tasks', 'referentiel'));
            }

        	// gestion des certificats
			$certification_active=referentiel_get_certification_active($this->instance->id);
			if ($certification_active){
				if (has_capability('mod/referentiel:write', $this->context)) {
	                $url_param['mode']='listcertif';
    	            $row[] = new tabobject('certificat', new moodle_url('/mod/referentiel/certificat.php', $url_param), get_string('certificat','referentiel'));
 				}
			}

			// archivage du dossier numerique
			if (has_capability('mod/referentiel:write', $this->context)) {
                $url_param['mode']='archive';
    			$row[] = new tabobject('archive', new moodle_url('/mod/referentiel/archive.php', $url_param), get_string('archive','referentiel'));
 			}


            // scolarite
            $scolarite_locale_visible=referentiel_get_item_configuration('scol', $this->instance->id)==0;
        	if (($scolarite_locale_visible	&&  has_capability('mod/referentiel:viewscolarite', $this->context))
                || has_capability('mod/referentiel:managescolarite', $this->context)) {
                $url_param['mode']='listetudiant';
                $row[] = new tabobject('scolarite', new moodle_url('/mod/referentiel/etudiant.php', $url_param), get_string('scolarite','referentiel'));
                $url_param['mode']='pedago';
                $row[] = new tabobject('pedago', new moodle_url('/mod/referentiel/pedagogie.php', $url_param), get_string('formation','referentiel'));
            }

            $tabs[] = $row;
	
            // sous-onglets
            // ACCOMPAGNEMENT

            if (isset($this->currenttab) && has_capability('mod/referentiel:write', $this->context)
                && (
                ($this->currenttab == 'menuacc')
                || ($this->currenttab == 'accompagnement')
                || ($this->currenttab == 'suivi')
                || ($this->currenttab == 'notification'))
            )
            {
                $row  = array();
                $inactive[] = 'menuacc';
                $row[] = new tabobject('accompagnement', new moodle_url('/mod/referentiel/accompagnement.php', $url_param), get_string('accompagnement','referentiel'));
                $url_param['mode']='suivi';
                $row[] = new tabobject('suivi', new moodle_url('/mod/referentiel/accompagnement.php', $url_param), get_string('repartition','referentiel'));
                if (has_capability('mod/referentiel:managecertif', $this->context)) {      // r�le enseignant
                    $url_param['mode']='notification';
                    $row[] = new tabobject('notification', new moodle_url('/mod/referentiel/accompagnement.php', $url_param), get_string('notification','referentiel'));
                }
                $tabs[] = $row;
                $activetwo = array('menuacc');
            }
        	// ACTIVITE
            if (isset($this->currenttab) && (($this->currenttab == 'list')
        		|| ($this->currenttab == 'listactivity')
        		|| ($this->currenttab == 'listactivityall')
		        || ($this->currenttab == 'addactivity')
        		|| ($this->currenttab == 'updateactivity')
        		|| ($this->currenttab == 'exportactivity')
            ))
            {
        		$row  = array();
                $inactive[] = 'list';
		        $url_param['mode']='listactivity';
				$row[] = new tabobject('listactivity', new moodle_url('/mod/referentiel/activite.php', $url_param),  get_string('listactivity','referentiel'));

		        $url_param['mode']='listactivityall';
				$row[] = new tabobject('listactivityall', new moodle_url('/mod/referentiel/activite.php', $url_param),  get_string('listactivityall','referentiel'));

                if (has_capability('mod/referentiel:addactivity', $this->context)) {
                    if ($edition_autorisee){
                        $url_param['mode']='addactivity';
	                    $row[] = new tabobject('addactivity', new moodle_url('/mod/referentiel/activite.php', $url_param), get_string('addactivity','referentiel'));
                    }
                }

		        if (!has_capability('mod/referentiel:managecertif', $this->context)) {      // role etudiant : uniquement pour modifier une activite
                    if ($this->mode=='modifactivity'){
                        $url_param['mode']='modifactivity';
                        $row[] = new tabobject('modifactivity', new moodle_url('/mod/referentiel/activite.php', $url_param), get_string('modifactivity','referentiel'));
                    }
                }
                else {
					$url_param['mode']='updateactivity';
					$row[] = new tabobject('updateactivity', new moodle_url('/mod/referentiel/activite.php', $url_param),  get_string('updateactivity','referentiel'));
                }
                if (has_capability('mod/referentiel:export', $this->context)) {
                    $url_param['mode']='exportactivity';
                    $row[] = new tabobject('exportactivity', new moodle_url('/mod/referentiel/export_activite.php', $url_param), get_string('export','referentiel'));
                }
                $tabs[] = $row;
                $activetwo = array('list');
            }

            // TACHES
            if (isset($this->currenttab) && ( ($this->currenttab == 'listtask')
        		|| ($this->currenttab == 'listtasksingle')
		        || ($this->currenttab == 'selecttask')
                || ($this->currenttab == 'imposetask')
                || ($this->currenttab == 'addtask')
                || ($this->currenttab == 'updatetask')
                || ($this->currenttab == 'exporttask')
                || ($this->currenttab == 'importtask')
            ))
            {
                $row  = array();
                $inactive[] = 'task';
                if (has_capability('mod/referentiel:viewtask', $this->context)) {
                    $url_param['mode']='listtask';
                    $row[] = new tabobject('listtask', new moodle_url('/mod/referentiel/task.php', $url_param),  get_string('listtask','referentiel'));
                    $url_param['mode']='listtasksingle';
                    $row[] = new tabobject('listtasksingle', new moodle_url('/mod/referentiel/task.php', $url_param),  get_string('listtasksingle','referentiel'));
                }
                if (has_capability('mod/referentiel:addtask', $this->context)) {
                    $url_param['mode']='addtask';
                    $row[] = new tabobject('addtask', new moodle_url('/mod/referentiel/task.php', $url_param),  get_string('addtask','referentiel'));
                    $url_param['mode']='updatetask';
                    $row[] = new tabobject('updatetask', new moodle_url('/mod/referentiel/task.php', $url_param),  get_string('updatetask','referentiel'));
                }
        		if (has_capability('mod/referentiel:import', $this->context)) {
                    $url_param['mode']='importtask';
                    $row[] = new tabobject('importtask', new moodle_url('/mod/referentiel/import_task.php', $url_param),  get_string('import','referentiel'));
		        }
        		if (has_capability('mod/referentiel:export', $this->context)) {
                    $url_param['mode']='exporttask';
                    $row[] = new tabobject('exporttask', new moodle_url('/mod/referentiel/export_task.php', $url_param),  get_string('export','referentiel'));
		        }
		
		        $tabs[] = $row;
                $activetwo = array('task');
            }
	
            // SCOLARITE
            // http://localhost/moodle25/moodle/mod/referentiel/etudiant.php?id=7&non_redirection=0&sesskey=6AeeQWDljY&select_acc=0&mode=listcertif
            if (isset($this->currenttab)
                &&  (has_capability('mod/referentiel:viewscolarite', $this->context)
                || has_capability('mod/referentiel:managescolarite', $this->context))
                &&
                (   $scolarite_locale_visible
                    &&
                    ($this->currenttab == 'scolarite')
			         || ($this->currenttab == 'listetudiant')
			         || ($this->currenttab == 'manageetab')
			         || ($this->currenttab == 'addetab')
			         || ($this->currenttab == 'listeetab')
			         || ($this->currenttab == 'exportetudiant')
			         || ($this->currenttab == 'importetudiant')
			         || ($this->currenttab == 'editetudiant')
		        ))
            {
                $row  = array();
                $inactive[] = 'scolarite';

                $url_param['mode']='';
                $row[] =new tabobject('listetudiant', new moodle_url('/mod/referentiel/etudiant.php', $url_param), get_string('listetudiant', 'referentiel'));

                if (has_capability('mod/referentiel:managescolarite', $this->context)) { // import export
                    //if ($this->currenttab == 'editetudiant'){
                        $url_param['mode']='updateetudiant';
                        $row[] =new tabobject('editetudiant', new moodle_url('/mod/referentiel/etudiant.php', $url_param), get_string('editetudiant', 'referentiel'));
                    //}
                    $url_param['mode']='exportetudiant';
                    $row[] =new tabobject('exportetudiant', new moodle_url('/mod/referentiel/export_etudiant.php', $url_param), get_string('exportetudiant', 'referentiel'));
                    $url_param['mode']='importetudiant';
                    $row[] =new tabobject('importetudiant', new moodle_url('/mod/referentiel/import_etudiant.php', $url_param), get_string('importetudiant', 'referentiel'));
                }
                if (has_capability('mod/referentiel:viewscolarite', $this->context)) { // etablissement
                    $url_param['mode']='listeetab';
                    $row[] =new tabobject('listeetab', new moodle_url('/mod/referentiel/etablissement.php', $url_param), get_string('etablissements', 'referentiel'));
                }
                if (has_capability('mod/referentiel:managescolarite', $this->context)) { // etablissement
                    $url_param['mode']='addetab';
                    $row[] =new tabobject('manageetab', new moodle_url('/mod/referentiel/etablissement.php', $url_param), get_string('manageetab', 'referentiel'));
                }

                if ($this->currenttab == '') {
                    $this->currenttab = 'listetudiant';
                    $this->mode = 'listetudiant';
                }
                $tabs[] = $row;
                $activetwo = array('scolarite');
            }

            // PEDAGOGIE
            if (isset($this->currenttab)
                &&  (has_capability('mod/referentiel:viewscolarite', $this->context)
                || has_capability('mod/referentiel:managescolarite', $this->context))
                &&
		          (   $scolarite_locale_visible
                  &&
		          ($this->currenttab == 'pedago')
                    || ($this->currenttab == 'addpedago')
			        || ($this->currenttab == 'editpedago')
			        || ($this->currenttab == 'listpedago')
                    || ($this->currenttab == 'listasso')
                    || ($this->currenttab == 'selectasso')
                    || ($this->currenttab == 'editasso')
        			|| ($this->currenttab == 'importpedago')
		          	|| ($this->currenttab == 'exportpedago')
		          )
		      )
            {
                $row  = array();
                $inactive[] = 'pedago';
                $url_param['mode']='listpedago';
                $row[] =new tabobject('listpedago', new moodle_url('/mod/referentiel/pedagogie.php', $url_param), get_string('listpedago', 'referentiel'));
                $url_param['mode']='listasso';
                $row[] =new tabobject('listasso', new moodle_url('/mod/referentiel/pedagogie.php', $url_param), get_string('listasso', 'referentiel'));

                if (has_capability('mod/referentiel:managescolarite', $this->context)) { // import export
                    $url_param['mode']='addpedago';
                    $row[] =new tabobject('addpedago', new moodle_url('/mod/referentiel/pedagogie.php', $url_param), get_string('addpedago', 'referentiel'));
                    if ($this->currenttab == 'editpedago'){
                        $url_param['mode']='updatepedago';
                        $row[] =new tabobject('editpedago', new moodle_url('/mod/referentiel/pedagogie.php', $url_param), get_string('editpedago', 'referentiel'));
                    }

                    $url_param['mode']='editasso';
                    $row[] =new tabobject('editasso', new moodle_url('/mod/referentiel/pedagogie.php', $url_param), get_string('editasso', 'referentiel'));
                    $url_param['mode']='importpedago';
                    $row[] =new tabobject('importpedago', new moodle_url('/mod/referentiel/import_pedagogie.php', $url_param), get_string('importpedago', 'referentiel'));
			        $url_param['mode']='exportpedago';
                    $row[] =new tabobject('exportpedago', new moodle_url('/mod/referentiel/export_pedagogie.php', $url_param), get_string('exportpedago', 'referentiel'));
            	}

                if ($this->currenttab == '') {
                    $this->currenttab = 'listpedago';
                    $this->mode = 'listpedago';
                }
                $tabs[] = $row;
                $activetwo = array('pedago');
            }

            // CERTIFICATS
            if (isset($this->currenttab) && (($this->currenttab == 'certificat')
                || ($this->currenttab == 'verroucertif')
                || ($this->currenttab == 'statcertif')
                || ($this->currenttab == 'listcertif')
                || ($this->currenttab == 'listcertifsingle')
                || ($this->currenttab == 'scolarite')
                || ($this->currenttab == 'addcertif')
                || ($this->currenttab == 'editcertif')
                || ($this->currenttab == 'printcertif')
                || ($this->currenttab == 'managecertif')
                || ($this->currenttab == 'importcertif')
                || ($this->currenttab == 'manageobjectif')
                 ))
            {
                $row  = array();
                $inactive[] = 'certificat';

                if (has_capability('mod/referentiel:view', $this->context)) { // afficher un certificat
                    $url_param['mode']='listcertif';
                    $row[] = new tabobject('listcertif', new moodle_url('/mod/referentiel/certificat.php', $url_param), get_string('listcertif', 'referentiel'));
                    if (has_capability('mod/referentiel:rate', $this->context)) { // rediger un certificat
                        $label_thumb=get_string('editcertif', 'referentiel');
                    }
                    else{
                        $label_thumb=get_string('synthese_certificat', 'referentiel');
                    }
                    $url_param['mode']='editcertif';
                    $row[] = new tabobject('editcertif', new moodle_url('/mod/referentiel/certificat.php', $url_param), $label_thumb);

                    if (referentiel_site_can_print_graph($this->instance->id) ){
                        $url_param['mode']='statcertif';
                        $row[] = new tabobject('statcertif', new moodle_url('/mod/referentiel/certificat.php', $url_param), get_string('statcertif', 'referentiel'));
                    }
                }
                if (has_capability('mod/referentiel:managecertif', $this->context)) {
                    $url_param['mode']='managecertif';
                    $row[] = new tabobject('managecertif', new moodle_url('/mod/referentiel/export_certificat.php', $url_param), get_string('managecertif', 'referentiel'));
                    $url_param['mode']='importcertif';
                    $row[] = new tabobject('importcertif', new moodle_url('/mod/referentiel/import_certificat.php', $url_param), get_string('importcertif', 'referentiel'));

                    if (referentiel_site_can_print_referentiel($this->instance->id)) {
                        $url_param['mode']='printcertif';
                        $row[] = new tabobject('printcertif', new moodle_url('/mod/referentiel/print_certificat.php', $url_param), get_string('printcertif', 'referentiel'));
                    }
                    $url_param['mode']='verroucertif';
                    $row[] = new tabobject('verroucertif', new moodle_url('/mod/referentiel/verrou_certificat.php', $url_param), get_string('verroucertif', 'referentiel'));

                    // OUTCOMES
                    if (!empty($CFG->enableoutcomes) && (REFERENTIEL_OUTCOMES)){
                        $url_param['mode']='manageobjectif';
                        $row[] = new tabobject('manageobjectif', new moodle_url('/mod/referentiel/outcomes.php', $url_param), get_string('outcomes', 'referentiel'));
                    }
                }

                $tabs[] = $row;
                $activetwo = array('certificat');
            }

			// ARCHIVES
			if (isset($this->currenttab) && ($this->currenttab == 'archive') ) {
				$row  = array();
        		$inactive[] = 'archive';
                // archiver les activites et le certificat
                if (has_capability('mod/referentiel:archive', $this->context)) {
                    $url_param['mode']='archive';
                    $row[] = new tabobject('archive', new moodle_url('/mod/referentiel/archive.php', $url_param), get_string('archive','referentiel'));
                }

        		if ($this->currenttab == '') {
            		$this->currenttab = $mode = 'archive';
        		}
        		$tabs[] = $row;
        		$activetwo = array('archive');
    		}


            // REFERENTIELS
            if (isset($this->currenttab) && 
                (
                ($this->currenttab == 'bareme')
                || ($this->currenttab == 'protocole')
                || ($this->currenttab == 'referentiel') || ($this->currenttab == 'listreferentiel')
                || ($this->currenttab == 'configref')
                || ($this->currenttab == 'editreferentiel') || ($this->currenttab == 'deletereferentiel')
                || ($this->currenttab == 'import')  || ($this->currenttab == 'import_simple')
                || ($this->currenttab == 'export')
            ))
            {
		        $row  = array();
                $inactive[] = 'referentiel';
                if (has_capability('mod/referentiel:view', $this->context)) {
        			$url_param['mode']='listreferentiel';
		          	$url_param['non_redirection']='1';
                    $row[] =new tabobject('listreferentiel', new moodle_url('/mod/referentiel/view.php', $url_param),  get_string('listreferentiel','referentiel'));
                    $url_param['non_redirection']=0;
                    $url_param['mode']='protocole';
                    $row[] =new tabobject('protocole', new moodle_url('/mod/referentiel/protocole.php', $url_param), get_string('protocole','referentiel'));
                    if ($CFG->referentiel_use_scale){
						$url_param['mode']='bareme';
                       	$row[] =new tabobject('bareme', new moodle_url('/mod/referentiel/bareme.php', $url_param), get_string('scale'));
                    }                    
        		}

                if (!empty($isadmin) || (isset($isreferentielauteur) && $isreferentielauteur)
                    	||
                    	(referentiel_site_can_write_or_import_referentiel($this->instance->id) && empty($isstudent) && empty($isguest))
                )
                {
               		if (!REFERENTIEL_BLOCK_REF){
				        if (has_capability('mod/referentiel:writereferentiel', $this->context)) {
        	                if ($CFG->referentiel_use_scale){
								$url_param['mode']='bareme';
                	        	$row[] =new tabobject('bareme', new moodle_url('/mod/referentiel/bareme.php', $url_param), get_string('scale'));
                    	    }
							$url_param['mode']='configref';
    	                    $row[] =new tabobject('configref', new moodle_url('/mod/referentiel/config_ref.php', $url_param), get_string('configref','referentiel'));
        	    	    	$url_param['mode']='editreferentiel';
            	            $row[] =new tabobject('editreferentiel', new moodle_url('/mod/referentiel/edit.php', $url_param), get_string('editreferentiel','referentiel'));
    	    		        $url_param['mode']='deleteferentiel';
                    	    $row[] =new tabobject('deletereferentiel', new moodle_url('/mod/referentiel/delete.php', $url_param), get_string('deletereferentiel','referentiel'));
			          	}
    	    			if (has_capability('mod/referentiel:import', $this->context)) {
        	                $url_param['mode']='import';
            	            $row[] =new tabobject('import', new moodle_url('/mod/referentiel/import.php', $url_param), get_string('import','referentiel'));
                	    }
                	}

                	if (has_capability('mod/referentiel:export', $this->context)) {
    	           		$url_param['mode']='export';
                    	$row[] =new tabobject('export', new moodle_url('/mod/referentiel/export.php', $url_param), get_string('export','referentiel'));
                	}
				}
        	    $tabs[] = $row;
		        $activetwo = array('referentiel');
            }
        }
        else{ // pas d'autre possibilite que l'affichage du referentiel
    	   $tabs[] = $row;
	       $this->currenttab='referentiel';
        }
        /// Print out the tabs and continue!
        print_tabs($tabs, $this->currenttab, $inactive, $activetwo);
    }
}
}
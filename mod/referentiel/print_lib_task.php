<?php  // $Id:  print_lib_task.php,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
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

/*
DROP TABLE IF EXISTS prefix_referentiel_task`;
CREATE TABLE IF NOT EXISTS `mdl_referentiel_task` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_task` varchar(80) NOT NULL DEFAULT '',
  `description_task` text NOT NULL,
  `competences_task` text NOT NULL,
  `criteres_evaluation` text NOT NULL,
  `ref_instance` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ref_referentiel` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ref_course` bigint(10) unsigned NOT NULL DEFAULT '0',
  `auteurid` bigint(10) unsigned NOT NULL,
  `date_creation` bigint(10) unsigned NOT NULL DEFAULT '0',
  `date_modif` bigint(10) unsigned NOT NULL DEFAULT '0',
  `date_debut` bigint(10) unsigned NOT NULL DEFAULT '0',
  `date_fin` bigint(10) unsigned NOT NULL DEFAULT '0',
  `cle_souscription` varchar(255) NOT NULL DEFAULT '',
  `souscription_libre` int(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='task' AUTO_INCREMENT=1 ;

--
-- Structure de la table `mdl_referentiel_consigne`
--

DROP TABLE IF EXISTS `mdl_referentiel_consigne`;
CREATE TABLE IF NOT EXISTS `mdl_referentiel_consigne` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_consigne` varchar(20) NOT NULL DEFAULT '',
  `description_consigne` text NOT NULL,
  `url_consigne` varchar(255) NOT NULL DEFAULT '',
  `ref_task` bigint(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='consigne' AUTO_INCREMENT=1 ;

--
-- Structure de la table `mdl_referentiel_a_user_task`
--

DROP TABLE IF EXISTS `mdl_referentiel_a_user_task`;
CREATE TABLE IF NOT EXISTS `mdl_referentiel_a_user_task` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `ref_user` bigint(10) unsigned NOT NULL,
  `ref_task` bigint(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='user_select_task' AUTO_INCREMENT=1 ;

*/

/**
 * Print Library of functions for task of module referentiel
 * 
 * @author jfruitet
 * @version $Id: lib.php,v 1.4 2006/08/28 16:41:20 mark-nielsen Exp $
 * @version $Id: lib.php,v 1.0 2008/04/29 00:00:00 jfruitet Exp $
 * @package referentiel
 **/

 
require_once('locallib.php');
require_once("lib_task.php");


// Affiche une task et les consignes associés
// *****************************************************************
// input @param a $record_t   of task                            *
// output null                                                     *
// *****************************************************************

function referentiel_print_liste_documents_task($taskid, $auteurid, $context){
    $s_consigne='';
    if ($taskid){
            // charger les consignes associees à la tache courante
            $nbconsigne=0;
            // AFFICHER LA LISTE DES consigneS
            $compteur_consigne=0;
            $records_consigne = referentiel_get_consignes($taskid);

            if ($records_consigne){
                    $s_consigne.="\n".'<!-- consigne -->'."\n";
                    foreach ($records_consigne as $record_d){
                        $nbconsigne++;
                        $compteur_consigne++;
                        $consigne_id=$record_d->id;
                        $type_consigne = stripslashes($record_d->type_consigne);
                        $description_consigne = stripslashes($record_d->description_consigne);
                        $url_consigne = $record_d->url_consigne;
                        $ref_task = $record_d->ref_task;
                        $cible_consigne='_blank'; // fenêtre cible
                        if (isset($record_d->etiquette_consigne)){
                        	$etiquette_consigne=$record_d->etiquette_consigne; // fenêtre cible
                        }
                        else{
                        	$etiquette_consigne='';
                        }
						// affichage de l'url
						if (preg_match('/moddata\/referentiel/',$url_consigne)){
			    			// l'URL doit être transformée
                    		$data_r=new Object();
							$data_r->id = $consigne_id;
							$data_r->userid = $auteurid;
							$data_r->author = referentiel_get_user_info($auteurid);
							$data_r->url = $url_consigne;
							$data_r->filearea = 'consigne';			    		
        					$url_consigne = referentiel_m19_to_m2_file($data_r, $context, false, true);
						}                        
                        $s_consigne.='<i>'.referentiel_affiche_url($url_consigne, $etiquette_consigne, $cible_consigne).'</i>'."\n";
                    }
            }
    }
    return $s_consigne;
}

function referentiel_get_theme_task($taskid){
    $s='';
    if ($taskid){
        $record_t=referentiel_get_task($taskid);
        if ($record_t){
            return (stripslashes($record_t->type_task));
        }
    }
    return $s;
}


function referentiel_get_auteur_task($taskid){
    if ($taskid){
        $record_t=referentiel_get_task($taskid);
        if ($record_t){
            return ($record_t->auteurid);
        }
    }
    return 0;
}

function referentiel_get_content_task($taskid, $all=false){
    $s='';
    if ($taskid){
        $record_t=referentiel_get_task($taskid);
        if ($record_t){
            $type_task = stripslashes($record_t->type_task);
            $description_task = stripslashes($record_t->description_task);
            $competences_task = $record_t->competences_task;
            $criteres_evaluation = stripslashes($record_t->criteres_evaluation);
            $ref_instance = $record_t->ref_instance;
            $ref_referentiel = $record_t->ref_referentiel;
            $ref_course = $record_t->ref_course;
            $auteurid = $record_t->auteurid;
            $date_creation = $record_t->date_creation;
            $date_modif = $record_t->date_modif;

            $date_debut = $record_t->date_debut;
            $date_fin = $record_t->date_fin;
            // Modalite souscription
            $souscription_libre = $record_t->souscription_libre;
            $cle_souscription = stripslashes($record_t->cle_souscription);
            $tache_masquee = $record_t->tache_masquee;

            $user_info=referentiel_get_user_info($auteurid);

            // dates
            $date_creation_info=userdate($date_creation);
            $date_modification_info=userdate($date_modif);
            $date_debut_info=userdate($date_debut);
            $date_fin_info=userdate($date_fin);

            // charger les consignes associees à la tache courante
            $s_consigne='';
            $nbconsigne=0;
            $ref_task=$taskid; // plus pratique
            // AFFICHER LA LISTE DES consigneS
            $compteur_consigne=0;
            $records_consigne = referentiel_get_consignes($ref_task);

            if ($records_consigne){
                    // afficher
                    // DEBUG
                    // echo "<br/>DEBUG ::<br />\n";
                    // print_r($records_consigne);
                    if ($all){
                        $s_consigne.='<!-- consigne -->'."\n";
                        $s_consigne.='<ul><span class="bold">'.get_string('consigne','referentiel').'</span>'."\n";
                    }
                    foreach ($records_consigne as $record_d){
                        $nbconsigne++;
                        $compteur_consigne++;
                        $consigne_id=$record_d->id;
                        $type_consigne = stripslashes($record_d->type_consigne);
                        $description_consigne = stripslashes($record_d->description_consigne);
                        $url_consigne = $record_d->url_consigne;
                        $ref_task = $record_d->ref_task;
                        if (isset($record_d->cible_consigne) && ($record_d->cible_consigne==1)){
                        	$cible_consigne='_blank'; // fenêtre cible
                        }
                        else{
                        	$cible_consigne='';
                        }
                        if (isset($record_d->etiquette_consigne)){
                        	$etiquette_consigne=$record_d->etiquette_consigne; // fenêtre cible
                        }
                        else{
                        	$etiquette_consigne='';
                        }
						// affichage de l'url
						if (preg_match('/moddata\/referentiel/',$url_consigne)){
			    			// l'URL doit être transformée
                    		$data_r=new Object();
							$data_r->id = $consigne_id;
							$data_r->userid = $auteurid;
							$data_r->author = referentiel_get_user_info($auteurid);
							$data_r->url = $url_consigne;
							$data_r->filearea = 'consigne';			    		
        					$url_consigne = referentiel_m19_to_m2_file($data_r, $context, false, true);
						}
                        
                        if ($all){
                            $s_consigne.='<li><i>'.$consigne_id.'</i> '.$type_consigne.' '.nl2br($description_consign).' <span class="bold">'.get_string('url','referentiel').'</span> :';
                            $s_consigne.=referentiel_affiche_url($url_consigne, $etiquette_consigne, $cible_consigne);
                            $s_consigne.='</li>'."\n";
                        }
                        else{
                            $s_consigne.='<br />'.nl2br($description_consigne).'<br />'.referentiel_affiche_url($url_consigne, $etiquette_consigne, $cible_consigne);
                        }
                    }
                    if ($all){
                        $s_consigne.='</ul>'."\n";
                    }
            }

            $nblig=empty($s_consigne)?1:$nbconsigne;
            if ($all){
                $s.='<a name="task_'.$taskid.'"</a>'."\n".'<span class="bold">'.get_string('task','referentiel').'</span>'.$taskid.' '.get_string('auteur','referentiel').': ';
	       	    $s.=$user_info.' '.get_string('date_debut','referentiel').': '.$date_debut_info.' '.get_string('date_fin','referentiel').': '.$date_fin_info;

                $s.=get_string('type_task','referentiel').': '.$type_task.' '.get_string('description','referentiel').': '.nl2br($description_task)."\n";
                $s.=get_string('liste_codes_competence','referentiel').': '.referentiel_affiche_liste_codes_competence('/',$competences_task, $ref_referentiel).' '.get_string('criteres_evaluation','referentiel').': '.nl2br($criteres_evaluation)."\n";
                // consignes
                if ($s_consigne!=''){
                    $s.=$s_consigne;
                }
            }
            else{
                if (strlen($description_task)>2048){
                    $description_task=substr($description_task,0,2048).'<br /><i>(...)</i> ';
                }
                if (strlen($criteres_evaluation)>1024){
                    $criteres_evaluation=substr($criteres_evaluation,0,1024).'<br /><i>(...)</i> ';
                }

                $s.='<span class="bold">'.get_string('auteur','referentiel').'</span>: <i>'.$user_info.'</i><br /><span class="bold">'.get_string('date_fin','referentiel').'</span><i>'.$date_fin_info.'</i><br /><span class="bold">'.get_string('description','referentiel').'</span>: '.nl2br($description_task).'<br /><span class="bold">'.get_string('criteres_evaluation','referentiel').':</span><br /> '.nl2br(substr($criteres_evaluation,0,1024));
            }
        }
    }
    return $s;
}


// ----------------------------------------------------
function referentiel_print_task($record_t, $context, $userid=0){
global $CFG;
global $OUTPUT; // Moodle 2 pour les icones
	$s="";
	if ($record_t){
		$taskid=$record_t->id;
		$type_task = stripslashes($record_t->type_task);
		$description_task = stripslashes($record_t->description_task);
		$competences_task = $record_t->competences_task;
		$criteres_evaluation = stripslashes($record_t->criteres_evaluation);
		$ref_instance = $record_t->ref_instance;
		$ref_referentiel = $record_t->ref_referentiel;
		$ref_course = $record_t->ref_course;
		$auteurid = $record_t->auteurid;
		$date_creation = $record_t->date_creation;
		$date_modif = $record_t->date_modif;
		
		$date_debut = $record_t->date_debut;
		$date_fin = $record_t->date_fin;
		$closed= ($date_fin<time());
		// Modalite souscription	
        $souscription_libre = $record_t->souscription_libre;
        $cle_souscription = stripslashes($record_t->cle_souscription);
		$tache_masquee = $record_t->tache_masquee;
		
		$user_info=referentiel_get_user_info($auteurid);

		// dates
		$date_creation_info=userdate($date_creation);		
		$date_modif_info=userdate($date_modif);
		$date_debut_info=userdate($date_debut);
		$date_fin_info=userdate($date_fin);

		$has_capability_add=has_capability('mod/referentiel:addtask', $context);
		$has_capability_select=has_capability('mod/referentiel:selecttask', $context);
		$has_capability_view=has_capability('mod/referentiel:viewtask', $context);
		$is_owner=referentiel_task_isowner($taskid);


		if ((!$tache_masquee) ||  $has_capability_add ){
		  $s.='<tr><td>';
		  $s.= $taskid;
		  $s.='</td><td>';
		  $s.=$user_info;
		  $s.='</td><td>';
		  $s.=$type_task;
		  // Modif JF 06/10/2010
		  if ($taskid){
            // consignes associées à une tâche
            $titre_task=referentiel_get_theme_task($taskid);
            $info_task=referentiel_get_content_task($taskid);
            if ($info_task!=''){
                // lien vers la tâche
                $s.='<br />'.referentiel_affiche_overlib_texte($titre_task, $info_task);
            }
            // documents associés à une tâche
            $s.=referentiel_print_liste_documents_task($taskid, $auteurid, $context);
          }

		  $s.='</td><td>';
		  $s.=referentiel_affiche_liste_codes_competence('/',$competences_task, $ref_referentiel);
		  $s.='</td><td>';
		  $s.='<span class="small">'.$date_debut_info.'</span>';
		  $s.='</td><td>';
		  $s.='<span class="small">'.$date_fin_info.'</span>';
		  $s.='</td><td>';
		  // Modalite souscription	
          if ($souscription_libre==1){
                $s.=get_string('libre','referentiel').' ';
		  }
          else{
                if ($cle_souscription!=''){
                    $s.=get_string('obtenir_cle_souscription', 'referentiel', $user_info).'</span>';
                }
                else{
                    $s.=get_string('avec_cle','referentiel').'</span>';
                }
          }
		  $s.='</td>';
		
		  // menu
		  $s.='<td align="center">'."\n";
		  $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;mode=listtasksingle&amp;sesskey='.sesskey().'#task_'.$taskid.'"><img src="'.$OUTPUT->pix_url('search','referentiel').'" alt="'.get_string('plus', 'referentiel').'" title="'.get_string('plus', 'referentiel').'" /></a>'."\n";
		
		  if ($has_capability_add	or $is_owner){
            $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;taskid='.$taskid.'&amp;mode=imposetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('assigner','referentiel').'" alt="'.get_string('assigner', 'referentiel').'"  title="'.get_string('assigner', 'referentiel').'" /></a>'."\n";
            $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;taskid='.$taskid.'&amp;mode=updatetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('edit','referentiel').'" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>'."\n";
		    $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;taskid='.$taskid.'&amp;mode=deletetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('delete','referentiel').'" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>'."\n";
		    $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;taskid='.$taskid.'&amp;mode=deletetaskactivites&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('deleteall','referentiel').'" alt="'.get_string('delete_all_task_associations','referentiel').'" title="'.get_string('delete_all_task_associations','referentiel').'" /></a>'."\n";
		    if ($tache_masquee){
                // masquee
                // http://localhost/moodle_dev/pix/t/hide.gif
                $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;taskid='.$taskid.'&amp;hide=0&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('show','referentiel').'" alt="'.get_string('show').'" title="'.get_string('show').'" /></a>'."\n";
            }
            else{
                // affichee
                $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;taskid='.$taskid.'&amp;hide=1&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('hide','referentiel').'" alt="'.get_string('hide').'" title="'.get_string('hide').'" /></a>'."\n";
            }
        }
		  // selectionner
        if (has_capability('mod/referentiel:selecttask', $context)){
            if (!$closed){
                if ($userid && referentiel_user_tache_souscrite($userid, $taskid)){
                    $s.='&nbsp; <img src="'.$OUTPUT->pix_url('subscribed','referentiel').'" alt="'.get_string('subscribed_task', 'referentiel').'" title="'.get_string('subscribed_task', 'referentiel').'" />'."\n";
                }
                else{
                     $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;taskid='.$taskid.'&amp;mode=selecttask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('copy','referentiel').'" alt="'.get_string('souscrire', 'referentiel').'"  title="'.get_string('souscrire', 'referentiel').'" /></a>';
                }
            }
            else{
    			$s.='&nbsp; <img src="'.$OUTPUT->pix_url('stop','referentiel').'" alt="'.get_string('closed_task', 'referentiel').'" title="'.get_string('closed_task', 'referentiel').'" />'."\n";
            }
        }
		// valider
        if (has_capability('mod/referentiel:approve', $context)){
			 if (!$closed){
				$s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;taskid='.$taskid.'&amp;mode=approvetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('go','referentiel').'" alt="'.get_string('approve', 'referentiel').'"  title="'.get_string('approve', 'referentiel').'"/></a>'."\n";
			 }
			 else{
    			$s.='&nbsp;  <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$ref_instance.'&amp;taskid='.$taskid.'&amp;mode=approvetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('closed','referentiel').'" alt="'.get_string('approve', 'referentiel').'" title="'.get_string('approve', 'referentiel').'" /></a>'."\n";
			 }
		  }
		
		  $s.='</td></tr>'."\n";
	   }
  }
	return $s;
}



// Affiche une entete task
// *****************************************************************
// *
// output string                                                     *
// *****************************************************************

function referentiel_print_entete_task(){
// Affiche une entete task
$s="";
	$s.='<table class="activite">'."\n";
	$s.='<tr>';
	$s.='<th width="3%"><span class="bold">'.get_string('id','referentiel').'</span></th>';
	$s.='<th width="10%"><span class="bold">'.get_string('auteur','referentiel').'</span></th>';
	$s.='<th width="20%"><span class="bold">'.get_string('type_task','referentiel').'</span></th>';
	$s.='<th width="20%"><span class="bold">'.get_string('liste_codes_competence','referentiel').'</span></th>';
	$s.='<th width="10%"><span class="bold">'.get_string('date_debut','referentiel').'</span></th>';
	$s.='<th width="10%"><span class="bold">'.get_string('date_fin','referentiel').'</span></th>';
	$s.='<th width="10%"><span class="bold">'.get_string('souscription','referentiel').'</span></th>';
  $s.='<th width="20%">&nbsp;</th>';
	$s.='</tr>'."\n";
	return $s;
}

function referentiel_print_enqueue_task(){
// Affiche une entete task
	$s='</table>'."\n";
	return $s;
}


// Affiche une ligne de la table quand il n'y a pas d'task pour userid 
// *****************************************************************
// input @param a user id                                          *
// output string                                                     *
// *****************************************************************

function referentiel_print_aucune_task_user($userid){
	$s="";
	if ($userid){
		$user_info=referentiel_get_user_info($userid);
		$date_modif_info=userdate(time());
	}
	else{
		$user_info="&nbsp;";
		$date_modif_info="&nbsp;";
	}
	
	$s.='<tr><td class="zero">&nbsp;</td><td class="zero">';
	$s.=$user_info;
	$s.='</td><td class="zero">&nbsp;</td><td class="invalide">&nbsp;</td><td class="zero">&nbsp;</td><td class="zero">&nbsp;</td><td class="zero">';
	$s.='<span class="small">'.$date_modif_info.'</span>';
	$s.='</td><td class="zero">&nbsp;</td></tr>'."\n";
	
	return $s;
}



// Affiche une task et les consignes associés
// *****************************************************************
// input @param a $record_t   of task                            *
// output null                                                     *
// *****************************************************************

function referentiel_print_task_detail($record_t, $context, $detail=true, $numero=0){
    $s='';
    $s0='';
    $s1='';
    $s2='';
    $nblignes=4; // hauteur du tableau
	if ($record_t){
		$taskid=$record_t->id;
		$type_task = stripslashes($record_t->type_task);
		$description_task = stripslashes($record_t->description_task);
		$competences_task = $record_t->competences_task;
		$criteres_evaluation = stripslashes($record_t->criteres_evaluation);
		$ref_instance = $record_t->ref_instance;
		$ref_referentiel = $record_t->ref_referentiel;
		$ref_course = $record_t->ref_course;
		$auteurid = $record_t->auteurid;
		$date_creation = $record_t->date_creation;
		$date_modif = $record_t->date_modif;
		
		$date_debut = $record_t->date_debut;
		$date_fin = $record_t->date_fin;
		// Modalite souscription	
        $souscription_libre = $record_t->souscription_libre;
        $cle_souscription = stripslashes($record_t->cle_souscription);
		$tache_masquee = $record_t->tache_masquee;
		
		$user_info=referentiel_get_user_info($auteurid);

		// dates
		$date_creation_info=userdate($date_creation);		
		$date_modification_info=userdate($date_modif);
		$date_debut_info=userdate($date_debut);
		$date_fin_info=userdate($date_fin);
		
		// charger les consignes associees à la tache courante

        if (!empty($taskid)){
            // AFFICHER LA LISTE DES consigneS
            $compteur_consigne=0;
            $records_consigne = referentiel_get_consignes($taskid);
	    
            if ($records_consigne){
    			// afficher
				// DEBUG
				// echo "<br/>DEBUG ::<br />\n";
				// print_r($records_consigne);
				$nbconsigne=count($records_consigne);
                $s1.='<!-- CONSIGNES -->
<div class="ref_affdoc">'."\n";
                if ($nbconsigne>1){
                    $s1.='<span class="bold">'.get_string('consignes_associees','referentiel',$nbconsigne).'</span>'."\n";
                }
                else{
                    $s1.='<span class="bold">'.get_string('consigne_associee','referentiel',$nbconsigne).'</span>'."\n";
                }
                $s1.="\n";

                foreach ($records_consigne as $record_d){
                    $consigne_id=$record_d->id;
                    $type_consigne = stripslashes($record_d->type_consigne);
                    $description_consigne = stripslashes($record_d->description_consigne);
                    $url_consigne = $record_d->url_consigne;
                    $ref_task = $record_d->ref_task;
                    if (isset($record_d->cible_consigne) && ($record_d->cible_consigne==1)){
					   $cible_consigne='_blank'; // fenêtre cible
                    }
                    else{
                        $cible_consigne='';
                    }
                    if (isset($record_d->etiquette_consigne)){
                        $etiquette_consigne=$record_d->etiquette_consigne; // fenêtre cible
                    }
                    else{
					   $etiquette_consigne='';
                    }
					// affichage de l'url
					if (preg_match('/moddata\/referentiel/',$url_consigne)){
			    			// l'URL doit être transformée
                    		$data_r=new Object();
							$data_r->id = $consigne_id;
							$data_r->userid = $auteurid;
							$data_r->author = referentiel_get_user_info($auteurid);							
							$data_r->url = $url_consigne;
							$data_r->filearea = 'consigne';			    		
        					$url_consigne = referentiel_m19_to_m2_file($data_r, $context, false, true);
					}

    				$date_creation=userdate($record_d->timestamp);
                    if ($detail){
                        $s2.='<span class="bold">'.get_string('consigne','referentiel').'</span><i>'.$consigne_id.'</i>
<span class="bold">'.get_string('date_creation','referentiel').'</span>
<i>'.$date_creation.'</i>
<span class="bold">'.get_string('type','referentiel').'</span>
'.$type_consigne.'
<span class="bold">'.get_string('description','referentiel').'</span><span class="white">
'.nl2br($description_consigne).'</span>
<span class="bold">'.get_string('url','referentiel').'</span>'."\n";
                        $s2.=referentiel_affiche_url($url_consigne, $etiquette_consigne, $cible_consigne);
                        $s2.='<br />'."\n";
                    }
                    else{
                        $s2.=' &nbsp; '.referentiel_affiche_url($url_consigne, $etiquette_consigne, $cible_consigne);
                    }
                }
            }
        }
        $s0.='
<a name="task_'.$taskid.'"></a>'."\n";
        // details
        if ($numero%2==0){
            $s0.= '<div class="ref_affact1">';
        }
        else{
            $s0.= '<div class="ref_affact2">';
        }
        $s0.='<span class="titre"><span class="bold">'.get_string('type_task','referentiel').'</span>"'.$type_task.'"</span>'."\n";
        $s0.='<br /><span class="bold">'.get_string('task','referentiel').' '.get_string('numero','referentiel').'</span>'.$taskid.' <span class="bold">'.get_string('auteur','referentiel').'</span>'.
$user_info."\n";
        $s0.='<br /><span class="bold">'.get_string('date_creation','referentiel').'</span>
<span class="small">'.$date_creation_info.'</span>
<span class="bold">'.get_string('date_modification','referentiel').'</span>
<span class="small">'.$date_modification_info.'</span>
<span class="bold">'.get_string('date_debut','referentiel').'</span>
<span class="small">'.$date_debut_info.'</span>
<span class="bold">'.get_string('date_fin','referentiel').'</span>
<span class="small">'.$date_fin_info.'</span>
<br />
<span class="bold">'.get_string('souscription','referentiel').'</span> '."\n";
    	// Modalite souscription
        if ($souscription_libre==1){
            $s0.=get_string('libre','referentiel').' ';
        }
        else{
            if ($cle_souscription!=''){
                $s0.=get_string('obtenir_cle_souscription', 'referentiel', $user_info).' ';
            }
            else{
                $s0.=get_string('avec_cle','referentiel').' ';
            }
        }
        $s0.='<span class="bold">'.get_string('liste_codes_competence','referentiel').'</span><span class="white">'.
referentiel_affiche_liste_codes_competence('/',$competences_task, $ref_referentiel)."\n";
        $s0.='</span><br /><span class="bold">'.get_string('description','referentiel').'</span>'."\n".
'<div class="ref_aff0">'.nl2br($description_task).'</div></span><br /><span class="bold">'.get_string('criteres_evaluation','referentiel').'</span>
<div class="ref_aff0">'.nl2br($criteres_evaluation ).'</div>'."\n";

        // affichage
		if ($s1){
            $s0.=$s1;
    	   	if ($s2){
                $s0.=$s2;
            }
            $s0.='</div>'."\n";
        }
        echo $s0.'</div>'."\n";
	}
}

// Affiche les tasks de ce referentiel
function referentiel_liste_toutes_taches($referentiel_instance_id){
global $DB;
global $CFG;
    if (!empty($referentiel_instance_id)){
        $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
        $course = $DB->get_record("course", array("id" => "$cm->course"));

		if (empty($cm) or empty($course)){
        	print_error('REFERENTIEL_ERROR 5 :: print_lib_task.php :: You cannot call this script in that way');
		}
    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

		
		$records = referentiel_get_all_tasks($course->id, $referentiel_instance_id);
		if (!$records){
            return false;
		}
	    else {
    		// afficher
			// DEBUG
			// echo "<br/>DEBUG ::<br />\n";
			// print_r($records);
			
			echo '<table class="activite">'."\n";
			foreach ($records as $record){
				referentiel_print_task($record, $context);
			}
			echo '</table>'."\n";
		}
	}
	return true;
}

// Affiche les taches de ce referentiel
function referentiel_menu_task_detail($context, $taskid, $referentiel_instance_id, $closed, $masquee){
	global $CFG;
	global $OUTPUT;
	global $USER;
	$isauthor = has_capability('mod/referentiel:addtask', $context);
	$isstudent = has_capability('mod/referentiel:selecttask', $context) && !$isauthor;
	
	echo '<div align="center">';
	echo '&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;mode=listtask&amp;sesskey='.sesskey().'#task_'.$taskid.'"><img src="'.$OUTPUT->pix_url('nosearch','referentiel').'" alt="'.get_string('moins', 'referentiel').'" title="'.get_string('moins', 'referentiel').'" /></a>';
	if (has_capability('mod/referentiel:addtask', $context) 
				or referentiel_task_isowner($taskid)) {
        echo '&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;mode=imposetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('assigner','referentiel').'" alt="'.get_string('assigner', 'referentiel').'"  title="'.get_string('assigner', 'referentiel').'" /></a>';
       	echo '&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;mode=updatetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('edit','referentiel').'" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
        echo '&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;mode=deletetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('delete','referentiel').'" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
        echo '&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;mode=deletetaskactivites&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('deleteall','referentiel').'" alt="'.get_string('delete_all_task_associations','referentiel').'" title="'.get_string('delete_all_task_associations','referentiel').'" /></a>'."\n";
		if ($masquee){
          // masquee
          // http://localhost/moodle_dev/pix/t/hide.gif
  		    echo '&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;hide=0&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('show','referentiel').'" alt="'.get_string('show').'" title="'.get_string('show').'" /></a>'."\n";
        }
        else{
          // affichee
  		    echo '&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;hide=1&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('hide','referentiel').'" alt="'.get_string('hide').'" title="'.get_string('hide').'" /></a>'."\n";
        }
	}
	// selectionner
    if (has_capability('mod/referentiel:selecttask', $context)){
        if (!$closed){
            if ($isstudent && $USER->id && referentiel_user_tache_souscrite($USER->id, $taskid)){
    			echo '&nbsp; <img src="'.$OUTPUT->pix_url('subscribed','referentiel').'" alt="'.get_string('subscribed_task', 'referentiel').'" title="'.get_string('subscribed_task', 'referentiel').'" />'."\n";

			}
            else{
                echo '&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;mode=selecttask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('copy','referentiel').'" alt="'.get_string('souscrire', 'referentiel').'"  title="'.get_string('souscrire', 'referentiel').'" /></a>';
            }
        }
		else{
    		echo '&nbsp; <img src="'.$OUTPUT->pix_url('stop','referentiel').'" alt="'.get_string('closed_task', 'referentiel').'" title="'.get_string('closed_task', 'referentiel').'" />'."\n";
		}
	}
	// valider
    if (has_capability('mod/referentiel:approve', $context)){
		if (!$closed){
			echo '&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;mode=approvetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('go','referentiel').'" alt="'.get_string('approve', 'referentiel').'"  title="'.get_string('approve', 'referentiel').'"/></a>'."\n";
		}
		else{
    		echo '&nbsp;  <a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance_id.'&amp;taskid='.$taskid.'&amp;mode=approvetask&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('closed','referentiel').'" alt="'.get_string('approve', 'referentiel').'" title="'.get_string('approve', 'referentiel').'" /></a>'."\n";
		}
	}
	echo '</div><br />';
}


/***************************************************************************
 * takes the current referentiel instance, a user id,                      *
 * and mode to display                                                     *
 * input @param array $records   of task                           		   *
 *       @param object $referentiel                                        *
 *       @param string $page                                               *
 * output null                                                             *
 ***************************************************************************/
function referentiel_print_liste_tasks($mode, $referentiel_instance, $userid_filtre=0) {
global $CFG;
global $USER;
static $isteacher=false;
static $isauthor=false;
static $iseditor=false;
static $referentiel_id = NULL;

global $DB;

    if (!empty($referentiel_instance)){
        $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
        $course = $DB->get_record("course", array("id" => "$cm->course"));
    	if (empty($cm) or empty($course)){
            print_error('REFERENTIEL_ERROR 5 :: print_lib_task.php :: 568 :: You cannot call this script in that way');
        }

        $context = context_module::instance($cm->id);

        $records = array();
        $referentiel_id = $referentiel_instance->ref_referentiel;
	
        $isauthor = has_capability('mod/referentiel:addtask', $context);
        $isstudent = has_capability('mod/referentiel:selecttask', $context) && !$isauthor;
        $iseditor = has_capability('mod/referentiel:writereferentiel', $context);
	
        if (isset($referentiel_id) && ($referentiel_id>0)){
            $referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_id);
            if (!$referentiel_referentiel){
                if ($iseditor){
                    print_error(get_string('creer_referentiel','referentiel'), "edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
                }
                else {
                    print_error(get_string('creer_referentiel','referentiel'), "../../course/view.php?id=$course->id&amp;sesskey=".sesskey());
                }
            }
            // preparer les variables globales pour Overlib
            referentiel_initialise_data_referentiel($referentiel_referentiel->id);

    		$record_tasks = referentiel_get_all_tasks($course->id, $referentiel_instance->id); // toutes les taches

    		if ($record_tasks){
    			// Afficher
	       		if (isset($mode) && ($mode=='listtasksingle')){
		      		;
    			}
	       		else{
		      		echo referentiel_print_entete_task();
    			}
    			$num=0;
                foreach ($record_tasks as $record) {   // afficher les taches
                    if (!$record->tache_masquee || $isauthor){
                        if (isset($mode) && ($mode=='listtasksingle')){
                            referentiel_print_task_detail($record, $context, 1, $num);
                            referentiel_menu_task_detail($context, $record->id, $referentiel_instance->id, $record->date_fin<time(), $record->tache_masquee);
                        }
                        else{
                            if ($isstudent){
                                echo referentiel_print_task($record, $context, $USER->id);
                            }
                            else{
                                echo referentiel_print_task($record, $context);
                            }
                        }
                        $num++;
                    }
                }
    			// Afficher
                if (isset($mode) && ($mode=='listtasksingle')){
		          ;
                }
                else{
                    echo referentiel_print_enqueue_task();
                }
                echo '<br /><br />'."\n";
            }
        }
	}
}

/***************************************************************************
 * takes the current referentiel instance, a user id,                      *
 * and mode to display                                                     *
 * input @param array $records   of task                           		   *
 *       @param object $referentiel                                        *
 *       @param string $page                                               *
 * output null                                                             *
 ***************************************************************************/
function referentiel_print_taskid($taskid, $referentiel_instance) {
global $CFG;
global $USER;
static $isstudent=false;
static $isauthor=false;
static $iseditor=false;
static $referentiel_id = NULL;

global $DB;
if (!empty($referentiel_instance)){
	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', array("id" => "$cm->course"));
    if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR 5 :: print_lib_task.php :: You cannot call this script in that way');
    }
	
    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

	$referentiel_id = $referentiel_instance->ref_referentiel;
	
	$isauthor = has_capability('mod/referentiel:addtask', $context);
	$isstudent = has_capability('mod/referentiel:selecttask', $context) && !$isauthor;
	$iseditor = has_capability('mod/referentiel:writereferentiel', $context);
	
	if (isset($referentiel_id) && ($referentiel_id>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_id);
		if (!$referentiel_referentiel){
			if ($iseditor){
				error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/mod/referentiel/edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
			                print_error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}

		$record = referentiel_get_task($taskid); // cette tache même si non auteur
		if ($record){
		  if (!$record->tache_masquee || $isauthor){
  			// Afficher 		
	   		referentiel_print_task_detail($record, $context);
		  	referentiel_menu_task_detail($context, $record->id, $referentiel_instance->id, $record->date_fin<time(), $record->tache_masquee);
		  }
    }
	}
	echo '<br /><br />'."\n";
}
}


/***************************************************************************
 * takes the current referentiel instance, a task id,                      *
 * and mode to display                                                     *
 * input @param array $taskid   of task                           		     *
 *       @param object $referentiel_instance                               *                                             *
 * output null                                                             *
 ***************************************************************************/
function referentiel_print_activities_task($bareme, $taskid, $referentiel_instance, $mode, $userid_filtre=0, $gusers=NULL) {
// Propose la validation globale
global $CFG;
global $OUTPUT;
global $USER;
static $istutor=false;
static $isteacher=false;
static $isadmin=false;
static $isstudent=false;
static $iseditor=false;
static $referentiel_id = NULL;
global $DB;

if (!empty($referentiel_instance)){
	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', array("id" => "$cm->course"));

    if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR 5 :: print_lib_task.php :: You cannot call this script in that way');
    }
	

    $context = context_module::instance($cm->id);

    $roles=referentiel_roles_in_instance($referentiel_instance->id);
    $iseditor=$roles->is_editor;
    $isadmin=$roles->is_admin;
    $isteacher=$roles->is_teacher;
    $istutor=$roles->is_tutor;
    $isstudent=$roles->is_student;
    $isguest=$roles->is_guest;
        
	if (isset($referentiel_instance->id) && ($referentiel_instance->id>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_instance->ref_referentiel);
		if (!$referentiel_referentiel){
			if ($iseditor){
			                print_error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/mod/referentiel/edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
			                print_error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}
		
	   // valider les activites
   	    if (has_capability('mod/referentiel:approve', $context)){

            $records_activity = referentiel_get_activites_task($taskid); // liste des activites associes a cette tache
            if ($records_activity){
                // boite pour selectionner les utilisateurs ?
				$record_id_users=array();

                foreach ($records_activity as $record_a) {
                    $record_id_users[$record_a->userid]=new stdClass();
                    $record_id_users[$record_a->userid]->userid=$record_a->userid;
                    $record_id_users[$record_a->userid]->afficher=true;
                }
                //echo "<br />DEBUG :: 956<br />";
                //print_r($record_id_users);
                //exit;
                if ($isteacher || $iseditor || $istutor || $isadmin){
                    // tous les users possibles (pour la boite de selection)
                    // Get your userids the normal way

                    if ($gusers && $record_id_users){ // liste des utilisateurs du groupe courant
				        // echo "<br />DEBUG :: print_lib_activite.php :: 740 :: GUSERS<br />\n";
				        // print_object($gusers);
				        // echo "<br />\n";
				        // exit;
				        $record_users  = array_intersect($gusers, array_keys($record_id_users));
				        // echo "<br />DEBUG :: print_lib_task.php :: 745 :: RECORD_USERS<br />\n";
				        // print_r($record_users  );
				        // echo "<br />\n";

                        // RAZ
                        for ($i=0; $i<count($record_id_users); $i++) {
                            $record_id_users[$i]=new stdClass();
                            $record_id_users[$i]->userid=0;
                            $record_id_users[$i]->afficher=false;
                        }
                        // reinitialiser
				        foreach ($record_users  as $record_id){
                            $record_id_users[$record_id]->userid=$record_id;
                            $record_id_users[$record_id]->afficher=true;
				        }
                    }
                    //echo "<br />DEBUG :: 985<br />";
                    //print_r($record_id_users);
                    //exit;
                }

                echo '<div align="center"><h4 align="center"></h4>';
                if (!referentiel_closed_task($taskid)){
                    if ($mode=='approvetask'){
				        $str_approve=get_string('approve_all_activity_task', 'referentiel',$taskid);
				        echo '<a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance->id.'&amp;taskid='.$taskid.'&amp;mode=approve&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('go','referentiel').'" alt="'.$str_approve.'"  title="'.$str_approve.'"/> '.$str_approve.'</a>'."\n";
                    }
                    else{
                        $str_approve=get_string('delete_all_activities_task', 'referentiel',$taskid);
				        echo '<a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance->id.'&amp;taskid='.$taskid.'&amp;mode=deletetaskall&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('go','referentiel').'" alt="'.$str_approve.'"  title="'.$str_approve.'"/> '.$str_approve.'</a>'."\n";
                    }
                }else{
                    if ($mode=='approvetask'){
                        $str_approve=get_string('approve_all_activity_task_closed', 'referentiel',$taskid);
                        echo '<a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance->id.'&amp;taskid='.$taskid.'&amp;mode=approve&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('closed','referentiel').'" alt="'.$str_approve.'" title="'.$str_approve.'" /> '.$str_approve.'</a>'."\n";
                    }
                    else{
                        $str_approve=get_string('delete_all_activity_task_closed', 'referentiel',$taskid);
                        echo '<a href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$referentiel_instance->id.'&amp;taskid='.$taskid.'&amp;mode=deletetaskall&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('closed','referentiel').'" alt="'.$str_approve.'" title="'.$str_approve.'" /> '.$str_approve.'</a>'."\n";
                    }
                }
                echo '</div><br />'."\n";
		        // Modif JF 20100118
			    echo '<form name="form" method="post" action="task.php?d='.$referentiel_instance->id.'&amp;sesskey='.sesskey().'">
<center>'."\n";
                echo '<table class="activite" width="100%" cellpadding="5" align="center">'."\n";
			    echo '<tr valign="top"><th class="activite" width="5%">&nbsp;</td><th class="activite" width="95%">';
                if ($mode=='approvetask'){
                    print_string('activites_tache','referentiel');
                }
                else{
                    print_string('activites_tache_delete','referentiel');
                }
                echo '</td></tr>'."\n";
                foreach ($records_activity as $record) {
                    if ($record_id_users[$record->userid]->afficher==true){
                        if ($record->approved){
                            echo '<tr valign="top"><td width="5%"><input type="checkbox" name="t_activite[]" value="'.$record->id.'" />'."\n";
                        }
                        else{
                            echo '<tr valign="top"><td width="5%"><input type="checkbox" name="t_activite[]" value="'.$record->id.'" checked="checked" />'."\n";
                        }
                        // Afficher l'activite
                        echo '</td><td width="95%">'."\n";
                        referentiel_print_activite_detail($bareme, $record, $context, true);
                        echo '</td></tr>'."\n";
                    }
                }
		        echo '</table>
<input type="hidden" name="ref_referentiel" value="'.$referentiel_instance->ref_referentiel.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="taskid"        value="'.$taskid.'" />
<input type="hidden" name="courseid"        value="'.$course->id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="modulename"    value="referentiel" />
<input type="hidden" name="instance"      value="'.$referentiel_instance->id.'" />'."\n";

                if ($mode=='approvetask'){
                    echo '<input type="hidden" name="mode" value="approve" />
<input type="submit" value="'.get_string('approve', 'referentiel').'" />'."\n";
                }
                else{
                    echo '<input type="hidden" name="mode" value="deletetaskall" />
<input type="submit" value="'.get_string('delete').'" />'."\n";
                }
                echo '
<input type="reset" value="'.get_string("restore").'" />
<input type="submit" value="'.get_string("cancel").'" />
</center>
</form>

'."\n";
		        echo '<br /><br />'."\n";
		      }
	       }
        }
    }
}



// ----------------------------------
function referentiel_select_users_tache($taskid, $mode, $record_users, $userid=0){
// Selection d'une liste d'utilisateurs a associer a une tache

global $cm;
global $course;
  $maxcol=8;

  $s="";
  $t_users=array();
	if ($record_users){
	  foreach ($record_users as $record_u) {   // liste d'id users
			// 
			$t_users[]= array('id' => $record_u->userid, 'lastname' => referentiel_get_user_nom($record_u->userid), 'firstname' => referentiel_get_user_prenom($record_u->userid));
			$t_users_id[]= $record_u->userid;
			$t_users_lastname[] = referentiel_get_user_nom($record_u->userid);
			$t_users_firstname[] = referentiel_get_user_prenom($record_u->userid);
		}
		array_multisort($t_users_lastname, SORT_ASC, $t_users_firstname, SORT_ASC, $t_users);
		// 
		// echo "<br />Debug :: print_lib_task.php :: 1419 ::<br />\n";
		// print_r($t_users);
		
		// exit;
		$n=count($t_users);
/*
		if ($n>=100){
			$l=$maxcol;
			$c=(int) ($n / $l);
		}
		else if ($n>=50){
			$l=$maxcol-2;
			$c=(int) ($n / $l);
		}
		else if ($n>=10){
			$l=$maxcol-4;
			$c=(int) ($n / $l);
		}
		else{
			$l=1;
			$c=(int) ($n);		
		}
*/
		$i=0;
		
		$s.="\n".'<form name="form" method="post" action="souscription.php?id='.$cm->id.'&amp;action=selectuser">'."\n";
		
		$s.='<div align="center">'."\n"; 
		$s.="\n".'<h3>'.get_string('aide_souscription','referentiel').'</h3>'."\n";
// MODIF JF 2012/10/09

        // DEBUG
		//echo "<br />Debug :: print_lib_task.php :: 1128 ::<br />\n";
		//print_r($record_users);

        $s.='<input type="button" name="select_tous" id="select_tous" value="'.get_string('select_all', 'referentiel').'"  onClick="return checkall()" />'."\n";
        $s.='&nbsp; &nbsp; &nbsp; <input type="button" name="select_aucun" id="select_aucun" value="'.get_string('select_not_any', 'referentiel').'"  onClick="return uncheckall()" />'."\n";

		$s.='<table class="selection">'."\n";
		$s.='<tr valign="top">';
		
		// $s.='<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";		
		
		$s.='<td>';
        // $s.='<input type="checkbox" name="select_all" id="select_all" value="1" /> '.get_string('tous', 'referentiel')."\n";

		$s.="\n<br />\n";	
		for ($j=0; $j<$n; $j++){
				if ($userid==$t_users[$i]['id']){
  			     $s.='<input type="checkbox" name="tuserid[]" id="tuserid_'.$t_users[$i]['id'].'" value="'.$t_users[$i]['id'].'" checked="checked" /> '.$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
				}
				else{
  			     $s.='<input type="checkbox" name="tuserid[]" id="tuserid_'.$t_users[$i]['id'].'" value="'.$t_users[$i]['id'].'" /> '.$t_users[$i]['lastname'].' '.$t_users[$i]['firstname']."\n";
				}
				$i++;
		}
		
        if (NOTIFICATION_ACTIVITES){
            $s.= '<br /><br /><span class="bold">'.get_string('notification_tache','referentiel').'</span>';
            if (NOTIFICATION_AUTEUR_ACTIVITE){
                $s.=' (<i>'.get_string('not_activite_tache_3','referentiel').'</i>)';
            }
            else{
                $s.=' (<i>'.get_string('not_activite_tache_1','referentiel').'</i>)';
            }
            $s.='<br />
    <input type="radio" name="mailnow" value="1" />'.get_string('yes').' &nbsp; <input type="radio" name="mailnow" value="0" checked="checked" />'.get_string('no').' &nbsp; &nbsp;
';
        }

		$s.='
<!-- These hidden variables are always the same -->
<input type="hidden" name="taskid"        value="'.$taskid.'" />
<input type="hidden" name="courseid"        value="'.$course->id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />
</form>'."\n";
		$s.='</td>';
		
  	$s.='</tr></table>'."\n";
        $s.='<input type="submit" value="'.get_string('select', 'referentiel').'" />'."\n";
		$s.='<input type="reset" value="'.get_string('corriger', 'referentiel').'" />'."\n";
		$s.='<input type="submit" value="'.get_string('cancel').'" />'."\n";

  	
		$s.='</div>'."\n";
	}
		
	return $s;
}


/**************************************************************************
 * takes a list of records, the current referentiel, an optionnal user id *
 * and mode to display                                                    *
 * input @param string  $mode                                             *
 *       @param object $referentiel_instance                              *
 *       @param int $userid_filtre                                        *
 *       @param array of objects $gusers of users get from current group  *
 *       @param string $sql_filtre_where, $sql_filtre_order               *
 * output null                                                            *
 **************************************************************************/
function referentiel_print_selection_user_tache($taskid, $mode, $referentiel_instance, $userid_filtre=0, $gusers=NULL, $sql_filtre_where='', $sql_filtre_order='', $data_filtre=NULL) {
global $CFG;
global $USER;
static $istutor=false;
static $isteacher=false;
static $isauthor=false;
static $iseditor=false;
static $referentiel_id = NULL;

    // A COMPLETER
    $data=NULL;
	// contexte
global $DB;

if (!empty($referentiel_instance)){
	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', array("id" => "$cm->course"));
	if (empty($cm) or empty($course)){
        print_error('REFERENTIEL_ERROR 5 :: print_lib_task.php :: You cannot call this script in that way');
	}
	
    $context = context_module::instance($cm->id);
	
    $records = array();
	$referentiel_id = $referentiel_instance->ref_referentiel;

    $roles=referentiel_roles_in_instance($referentiel_instance->id);
    $iseditor=$roles->is_editor;
    $isadmin=$roles->is_admin;
    $isteacher=$roles->is_teacher;
    $istutor=$roles->is_tutor;
    $isstudent=$roles->is_student;
    $isguest=$roles->is_guest;

	if (isset($referentiel_id) && ($referentiel_id>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_id);
		if (!$referentiel_referentiel){
			if ($iseditor){
			    print_error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/mod/referentiel/edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
			    print_error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}

		// boite pour selectionner les utilisateurs ?
		if ($isteacher || $iseditor || $istutor){
			// tous les users possibles (pour la boite de selection)
			// Get your userids the normal way
			$record_id_users  = referentiel_get_students_course($course->id,0,0);  //seulement les stagiaires
			if ($gusers && $record_id_users){ // liste des utilisateurs du groupe courant
				// echo "<br />DEBUG :: print_lib_task.php :: 740 :: GUSERS<br />\n";
				// print_object($gusers);
				// echo "<br />\n";
				// exit;
				$record_users  = array_intersect($gusers, array_keys($record_id_users));
				// echo "<br />DEBUG :: print_lib_task.php :: 745 :: RECORD_USERS<br />\n";
				// print_r($record_users  );
				// echo "<br />\n";
				// recopier 
				$record_id_users=array();
				foreach ($record_users  as $record_id){
                        $a_obj=new stdClass();
                        $a_obj->userid=$record_id;
                        $record_id_users[]=$a_obj;
				}
			}

			echo referentiel_select_users_tache($taskid, $mode, $record_id_users, $userid_filtre);
		}
	}
	echo '<br /><br />'."\n";
	return true;
}
}


?>
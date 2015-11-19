<?php
    // recupere les notes et objectifs en rapport avec les referentiels de comp�tence
    
    
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

      // Exports selected outcomes in CSV format. 

// JF 
// utilise les tables grades_xx et scale_xx

// Selectionner tous les outcomes generes depuis le module referentiel grace a export_grade_outcomes.php
/**
 * return cron timestamp
 *
 */
 // -------------------------------------------------
 function referentiel_get_cron_timestamp(){
	global $DB;
    if ($rec=$DB->get_record('modules', array('name' => 'referentiel'))){
		return $rec->lastcron;
	}
	return 0;
 }

// -------------------------------------------------
function referentiel_traitement_notations(){
// genere des declarations d'activites "fictives" a partir des notation par objectif
// sur les activites du cours
global $CFG;
global $DB;
global $scales;
	// all users that are subscribed to any post that needs sending
	$notations = array();
	$scales = array();
	$n_activites=0;
	// Enregistrements anterieurs � 2 jours non traites.  This is to avoid the problem where
	// cron has not been running for a long time
	$timenow   = time();
	if (NOTIFICATION_DELAI){
        $endtime   = $timenow - $CFG->maxeditingtime;
	}
	else{
        $endtime   = $timenow;
	}

    // Enregistrements anterieurs a NOTIFICATION_INTERVALLE_JOUR jours non traites.
	// This is to avoid the problem where cron has not been running for a long time

	if (REFERENTIEL_DEBUG){
    	$starttime = $endtime - NOTIFICATION_INTERVALLE_JOUR * 7 * 24 * 3600;   // Two weeks earlier
	}
	else{
    	$starttime = $endtime - NOTIFICATION_INTERVALLE_JOUR * 24 *  3600;  // n days earlier
	}


	$cron_timestamp=referentiel_get_cron_timestamp();

	if (!empty($cron_timestamp)){
		if (REFERENTIEL_DEBUG){
        	$starttime = min($starttime, $cron_timestamp-60);
		}
		else{
        	$starttime = max($starttime, $cron_timestamp-60);
		}
	}


  $scales_list = '';     // for items with a scaleid

  // users
  $users = array();
  $users_list = '';

  // JF
  // DEBUG
  mtrace("\nDEBUT CRON OBJECTIFS.");
  mtrace("\nSTART TIME : ".date("Y/m/d H:i:s",$starttime)." END TIME :  ".date("Y/m/d H:i:s",$endtime));


  $notations=referentiel_get_outcomes($starttime, $endtime);
  //$notations=referentiel_get_outcomes_old($endtime);
	if ($notations){
		foreach($notations as $notation){
      		if ($notation){
        
        		if (OUTCOMES_SUPER_DEBUG){
          			mtrace("\nDEBUG :: grade/cron_outcomes.php Line 102 :: USERID ".$notation->userid." ; COURSEID ".$notation->courseid."\nNOTATION :\n");
          			print_r($notation);
        		}
        
        		if (!empty($notation->scaleid) && !preg_match("/ ".$notation->scaleid."\,/", $scales_list)){
          			$scales_list .= " $notation->scaleid,";
        		}

        		if (!empty($notation->userid) && !empty($notation->courseid)){
                    if (!preg_match("/ ".$notation->userid."\,/", $users_list)){
                        $users_list .= " $notation->userid,";
                        $user=new Object();
                        $user->userid = $notation->userid;
                        $user->courses = array();
                        $user->course_list = '';
                        $users[$notation->userid]=$user;
                    }

                    if (!preg_match("/ ".$notation->courseid."\,/", $users[$notation->userid]->course_list)){
                        $users[$notation->userid]->course_list .= " $notation->courseid,";
                        $course=new Object();
                        $course->courseid = $notation->courseid;
                        $course->referentiel_list = '';
                        $course->referentiels = array();
                        $users[$notation->userid]->courses[$notation->courseid] = $course;
                    }

                    if (!preg_match("/ ".$notation->referentiel_instanceid."\,/", $users[$notation->userid]->courses[$notation->courseid]->referentiel_list)){
                            $users[$notation->userid]->courses[$notation->courseid]->referentiel_list = " $notation->referentiel_instanceid,";
                            $referentiel = new object();
                            $referentiel->referentiel_instanceid = $notation->referentiel_instanceid;
                            $referentiel->ref_referentiel = $notation->ref_referentiel;
                            $referentiel->module_list ='';
                            $referentiel->modules = array();
                            // $referentiel->referentiels[$notation->referentiel_instanceid]->outcome_list = '';
                            $users[$notation->userid]->courses[$notation->courseid]->referentiels[$notation->referentiel_instanceid]=$referentiel;
                    }

                    if ((empty($users[$notation->userid]->courses[$notation->courseid]->referentiels[$notation->referentiel_instanceid]->module_list))
                            || (!preg_match("/ ".$notation->module.":".$notation->moduleinstance."\,/", $users[$notation->userid]->courses[$notation->courseid]->referentiels[$notation->referentiel_instanceid]->module_list))){
                            $users[$notation->userid]->courses[$notation->courseid]->referentiels[$notation->referentiel_instanceid]->module_list .= " $notation->module:$notation->moduleinstance,";
                            $module = new object();
                            $module->modulename = $notation->module;
                            $module->moduleinstance = $notation->moduleinstance;
                            $module->teacherid = $notation->teacherid;    // MODIF JF 2012/01/31
                            $module->outcome_list='';
                            $module->scaleid_list='';
                            $module->timemodified_list='';
                            $users[$notation->userid]->courses[$notation->courseid]->referentiels[$notation->referentiel_instanceid]->modules[$notation->moduleinstance]=$module;
                    }

                    if (!empty($users[$notation->userid]->courses[$notation->courseid]->referentiels[$notation->referentiel_instanceid]->modules[$notation->moduleinstance])){
                            // les notes
                            if ($notation->outcomeshortname!=''){
                                $users[$notation->userid]->courses[$notation->courseid]->referentiels[$notation->referentiel_instanceid]->modules[$notation->moduleinstance]->outcome_list.=" $notation->outcomeshortname:$notation->finalgrade,";
                                $users[$notation->userid]->courses[$notation->courseid]->referentiels[$notation->referentiel_instanceid]->modules[$notation->moduleinstance]->scaleid_list.=" $notation->scaleid,";
                                $users[$notation->userid]->courses[$notation->courseid]->referentiels[$notation->referentiel_instanceid]->modules[$notation->moduleinstance]->timemodified_list.=" $notation->timemodified,";
                            }

                    }
        		}
      		}
    	}
  	}
  
  
  	if (!empty($users)){
    	// DEBUG
    	if (OUTCOMES_SUPER_DEBUG){
    		mtrace("\nDEBUG :: grade/cron_outcomes.php Line 171 :: USERS \n");
    		print_r($users);
    	}

  
    	foreach($users as $user) {
        	if (OUTCOMES_SUPER_DEBUG){
            	mtrace("\nDEBUG :: grade/cron_outcomes.php Line 178 :: USER \n");
            	print_r($user);
        	}
        
        	foreach($user->courses as $course){
            	// echo "<br />COURSE_ID $course->courseid; \n";
            	foreach($course->referentiels  as $referentiel){
                	// echo "<br />REFERENTIEL_INSTANCE $referentiel->referentiel_instanceid; REFERENTIEL_ID $referentiel->ref_referentiel\n";

                	// MODIF JF 2013/08/05
					// bareme
                	$threshold=-1;

					if ($CFG->referentiel_use_scale){
						if ($bareme=referentiel_get_bareme_occurrence($referentiel->ref_referentiel)){
                    		$threshold = $bareme->threshold;
						}
					}

                	foreach($referentiel->modules as $module){
                    	// echo "<br />MODULE $module->modulename ; Instance $module->moduleinstance ; \n";
                    	// preparer l'enregistrement
                    	// DEBUG
                    	// echo "<br />DEBUG :: 180 ; MODULE : $module->modulename, INSTANCE : $module->moduleinstance, COURS : $course->courseid\n";
                    	if ($module && !empty($module->modulename) && !empty($module->moduleinstance) && !empty($course->courseid)){
                        	$m = referentiel_get_module_info($module->modulename, $module->moduleinstance, $course->courseid);
/*
              // module
  $m->id;
  $m->type=$modulename;
  $m->instance=$moduleinstance;
  $m->course=$courseid;
  $m->date=$cm->added;
  $m->userdate=userdate($cm->added);
  $m->ref_activite=$mid;
  $m->name=$mname;
  $m->description=$mdescription;
  $m->link=$mlink;
*/
                        	// DEBUG
                         if (OUTCOMES_SUPER_DEBUG){
                            	mtrace("\nDEBUG :: grade/cron_outcomes.php Line 184 :: MODULE \n");
								print_r($m);
                        	}

                        	$activite= new Object();
                        	$activite->type_activite='['.get_string('outcome_type', 'referentiel').' '.get_string('modulename', $m->type).' '.$m->ref_activite.'] '.get_string('outcome_date','referentiel').' '.$m->userdate;
                        	$activite->description_activite=get_string('outcome_description','referentiel', $m);
                        	$activite->competences_activite='';
                        	$activite->commentaire_activite='';
                        	$activite->bareme_activite='';
                        	$activite->ref_instance=$referentiel->referentiel_instanceid;
                        	$activite->ref_referentiel=$referentiel->ref_referentiel;
                        	$activite->ref_course=$course->courseid;
                        	$activite->userid=$user->userid;
                        	$activite->teacherid=$module->teacherid;  // MODIF JF 2013/02/04
                        	$activite->date_creation=$m->date;
                        	$activite->date_modif_student=0;
                        	$activite->date_modif=$m->date;
                        	$activite->approved=1;   // approuve par defaut
                        	$activite->ref_task=0;

                    		// DEBUG
                    		/*
                    		if (OUTCOMES_SUPER_DEBUG){
                        		mtrace("DEBUG :: grade/cron_outcomes.php Line 181 :: TIMEMODIFIED_LIST $module->timemodified_list\n");
                    		}
                    		*/
                        	$t_datemodif=explode(',',$module->timemodified_list);
                        	sort($t_datemodif);
                        	$imax=count($t_datemodif)-1;
                        	$timemodified=$t_datemodif[$imax];
                        	if ($timemodified>$activite->date_creation){
                            	//$activite->date_modif_student=$timemodified;
                            	$activite->date_modif=$timemodified;
                        	}
              
                        	// echo "<br />SCALE_LIST $module->scaleid_list\n";
                        	$t_scales=explode(',',$module->scaleid_list);
              
                        	// echo "<br />OUTCOME_LIST $module->outcome_list\n";
                        	$t_outcomes=explode(',',$module->outcome_list);
                        	$n=count($t_outcomes);
                        	if ($n>0){
                            	$i=0;
                            	if (OUTCOMES_SUPER_DEBUG){
									mtrace ("\nDEBUG :: grade/cron_outcomes.php Line 264");
                            	}

								$liste_bareme_activite='';

								while ($i<$n){
                                	if ($t_outcomes[$i]!=''){
                                    	list($cle, $val)=explode(':',$t_outcomes[$i]);
	                                    $cle=trim($cle);
    	                                $val=trim($val);
        	                            $scaleid=$t_scales[$i];
            	                        if (OUTCOMES_SUPER_DEBUG){
											mtrace ("\nCODE : $cle ; VALEUR : $val ;");
                    	                }
										if ($threshold==-1){
											$scale  = referentiel_get_scale($scaleid);
                                    		if ($val>=$scale->grademax){     // pas de bareme
                                        		$activite->competences_activite.=$cle.'/';
                                        		if (OUTCOMES_SUPER_DEBUG){
													mtrace (" ---> VALIDE \n");
												}
											}
                                    	}
										else{
                                    		if ($val>$threshold){     // baremes
                                        		$activite->competences_activite.=$cle.'/';
                                        		if (OUTCOMES_SUPER_DEBUG){
													mtrace (" ---> VALIDE \n");
												}
											}
                                  		}
                                		$liste_bareme_activite.=$cle.':'.(int)$val.'/';
                                	}
                            		$i++;
								}
                        	}
                    	}

						// MODIF JF 2014/02/05
						// completer avec des informations obtenues dans l'activite
                        $mdata=NULL;
						if ($m && $m->type=='assign'){
							$mdata=referentiel_get_assign($m, $user->userid);
							if (!empty($mdata)){
   		                        // DEBUG
    		                    if (OUTCOMES_SUPER_DEBUG){
        		                    mtrace("\nDEBUG :: grade/cron_outcomes.php Line 226 :: MODULE \n");
            		                print_r($mdata);
                		        }
							}
						}
                    	// enregistrer l'activite
                    	// DEBUG
                    	if (OUTCOMES_SUPER_DEBUG){
                        	mtrace("\nDEBUG :: grade/cron_outcomes.php Line 313 ; ACTIVITE\n");
                        	print_r($activite);
                    	}
                    	if (referentiel_activite_outcomes($activite, $m, $liste_bareme_activite, $mdata)){
                        	if (OUTCOMES_SUPER_DEBUG){
                            	mtrace("\nDEBUG :: grade/cron_outcomes.php Line 265\n-----------------\nACTIVITE ENREGISTREE\n");
                        	}
                        	$n_activites++;
                    	}
                	}
            	}
        	}
    	}
	}

  	// echo "<br />\n";
  	mtrace($n_activites.' ACTIVITES CREES OU MODIFIEES.');
  	mtrace('FIN CRON REFERENTIEL OBJECTIFS.');
}


// -------------------------------------------------
function referentiel_get_scale($scaleid){
  // Preload scale objects for items with a scaleid
  global $scales;
  global $DB;
  if ($scaleid){
    if (!empty($scales[$scaleid])){
      // echo "<br />DEBUG :: 211 SCALE\n";
      return $scales[$scaleid];
    }    
    else {  
      $scale_r = $DB->get_record("scale", array("id" => "$scaleid"));
      if ($scale_r){
        $scale = new Object();
        $scale->scaleid = $scaleid;
        $scale->scale = $scale_r->scale;
        $tscales=explode(',',$scale_r->scale);
        // reindex because scale is off 1
        // MDL-12104 some previous scales might have taken up part of the array
        // so this needs to be reset  
        $scale->scaleopt = array();
        $i = 0;
        foreach ($tscales as $scaleoption) {
          $i++;
          $scale->scaleopt[$i] = trim($scaleoption);
        }
        $scale->grademin=1;
        $scale->grademax=$i;
        $scales[$scaleid]=$scale;
        return $scale;      
      }
    }
  }
  return NULL;
}

// -------------------------------------------------
function referentiel_get_module_info($modulename, $moduleinstance, $courseid){
// retourne les infos concernant ce module
global $CFG;
global $DB;
  if (! $course = $DB->get_record("course", array("id" => "$courseid"))) {;
    // error("DEBUG :: referentiel_get_module_info :: This course doesn't exist");
    return false;
  }
  if (! $module = $DB->get_record("modules", array("name" => "$modulename"))) {
    // error("DEBUG :: referentiel_get_module_info :: This module type doesn't exist");
    return false;
  }
  if (! $cm = $DB->get_record("course_modules", array("course" => "$course->id", "module" => "$module->id", "instance" => "$moduleinstance"))) {
    // error("DEBUG :: referentiel_get_module_info :: This course module doesn't exist");
    return false;
  }

  $mid=0;
  $mname='';
  $mdescription='';
  $mlink='';

  if ($modulename=='forum'){
    if (! $forum = $DB->get_record("forum", array("id" => "$cm->instance"))) {
      // error("DEBUG :: referentiel_get_module_info :: This forum module doesn't exist");
      return false;
    }
    $mid=$forum->id;
    $mname=$forum->name;
    $mdescription=$forum->intro;
    $mlink = $CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id;
  }
  elseif ($modulename=='assign'){
    if (! $assign= $DB->get_record("assign", array("id" => "$cm->instance"))) {
      // error("DEBUG :: referentiel_get_module_info :: This assignment doesn't exist");
      return false;
    }
    $mid=$assign->id;
    $mname=$assign->name;
    $mdescription=$assign->intro;
    $mlink = $CFG->wwwroot.'/mod/assign/view.php?id='.$cm->id;
  }
  elseif ($modulename=='assignment'){
    if (! $assignment = $DB->get_record("assignment", array("id" => "$cm->instance"))) {
      // error("DEBUG :: referentiel_get_module_info :: This assignment doesn't exist");
      return false;
    }
    $mid=$assignment->id;
    $mname=$assignment->name;
    $mdescription=$assignment->intro;
    $mlink = $CFG->wwwroot.'/mod/assignment/view.php?id='.$cm->id;
  }
  elseif ($modulename=='chat'){
    if (! $chat = $DB->get_record("chat", array("id" => "$cm->instance"))) {
      //error("DEBUG :: referentiel_get_module_info :: This chat doesn't exist");
      return false;
    }
    $mid=$chat->id;
    $mname=$chat->name;
    $mdescription=$chat->intro;
    $mlink = $CFG->wwwroot.'/mod/chat/view.php?id='.$cm->id;
  }
  elseif ($modulename=='choice'){
    if (! $choice = $DB->get_record("choice", array("id" => "$cm->instance"))) {
      // error("DEBUG :: referentiel_get_module_info :: This choice module doesn't exist");
      return false;
    }
    $mid=$choice->id;
    $mname=$choice->name;
    $mdescription=$choice->intro;
    $mlink = $CFG->wwwroot.'/mod/choice/view.php?id='.$cm->id;
  }
  elseif ($modulename=='data'){
    if (! $data = $DB->get_record("data", array("id" => "$cm->instance"))) {
      // error("DEBUG :: referentiel_get_module_info :: This data module doesn't exist");
      return false;
    }
    $mid=$data->id;
    $mname=$data->name;
    $mdescription=$data->intro;
    $mlink = $CFG->wwwroot.'/mod/data/view.php?id='.$cm->id;

// http://tracker.moodle.org/browse/MDL-15566
// Notice: Undefined property: stdClass::$cmidnumber in C:\xampp\htdocs\moodle_dev\mod\data\lib.php on line 831
  }
  elseif ($modulename=='glossary'){
    if (! $glossary = $DB->get_record("glossary",array("id" => "$cm->instance"))) {
      print_error("DEBUG :: referentiel_get_module_info :: This glossary module doesn't exist");
    }
    $mid=$glossary->id;
    $mname=$glossary->name;
    $mdescription=$glossary->intro;
    $mlink = $CFG->wwwroot.'/mod/glossary/view.php?id='.$cm->id;
  }
  else{
    // tentative pour un module generique
    if (! $record_module = $DB->get_record($module->name,array("id" => "$cm->instance"))) {
      // error("DEBUG :: referentiel_get_module_info :: This ".$module->name." module doesn't exist");
      return false;
    }
    $mid=$record_module->id;
    $mname=$record_module->name;
    if (isset($record_module->intro)){
      $mdescription=$record_module->intro;
    }
    else if (isset($record_module->info)){
      $mdescription=$record_module->info;
    }
    else if (isset($record_module->description)){
      $mdescription=$record_module->description;
    }
    else if (isset($record_module->text)){
      $mdescription=$record_module->text;
    }
    else{
      $mdescription=get_string('description_inconnue','referentiel');
    }
    $mlink = $CFG->wwwroot.'/mod/'.$modulename.'/view.php?id='.$cm->id;
  }

  $m=new Object();
  $m->id=$mid;
  $m->type=$modulename;
  $m->instance=$moduleinstance;
  $m->course=$courseid;
  $m->date=$cm->added;
  $m->userdate=userdate($cm->added);
  $m->ref_activite=$mid;
  $m->name=$mname;

 $pattern = array("/<br>/i", "/<br\/>/i", "/<br \/>/i", "/<p>/i",
  	"/<a target='.+' href='(.+)'>(.+)<\/a>/i", "/<a target=\".*\" href=\"(.+)\">(.+)<\/a>/i",
   	"/<a href='(.+)' target='.+'>(.+)<\/a>/i", "/<a href=\"(.+)\" target=\".*\">(.+)<\/a>/i",
    "/<a href='(.+)'>(.+)<\/a>/i", "/<a href=\"(.+)\">(.+)<\/a>/i" );
  $replacement = array(' ', ' ', ' ', ' ', ' $2:$1 ', ' $2:$1 ', ' $2:$1 ', ' $2:$1 ', ' $2:$1 ', ' $2:$1 ');
  $m->description=strip_tags(preg_replace($pattern, $replacement,$mdescription));

  $m->link=$mlink;

  return $m;
}

// ------------------------------------------
function referentiel_user($user_id) {
// retourne le NOM prenom à  partir de l'id
global $DB;
	$user_info="";
	if (!empty($user_id)){
        $params=array("userid" => "$user_id");
		$sql = "SELECT firstname, lastname FROM {user}  WHERE id = :userid ";
		$user = $DB->get_record_sql($sql, $params);
		if ($user){
			$user_info=mb_convert_case($user->firstname, MB_CASE_TITLE, 'UTF-8').' '.mb_strtoupper($user->lastname,'UTF-8');
		}
	}
	return $user_info;
}

// ------------------
function referentiel_url_file($afile) {
	global $CFG;
	// retourne le chemin du fichier
    $fullpath = '/'.$afile->contextid.'/'.$afile->component.'/'.$afile->filearea.'/'.$afile->itemid.$afile->filepath.$afile->filename;
    return(new moodle_url($CFG->wwwroot.'/pluginfile.php'.$fullpath));
}

// ------------------
function referentiel_get_mahara_link($maharalink) {     // Portland version assign mahara plugin
	// retourne l'url du lien
	return(new moodle_url('/auth/mnet/jump.php', array('hostid'=>$maharalink->host, 'wantsurl'=>$maharalink->url)));
}


// -------------------------------------------------
function referentiel_get_assign($m, $userid){
/*
  $m->id;
  $m->type=$modulename;
  $m->instance=$moduleinstance;
  $m->course=$courseid;
  $m->date=$cm->added;
  $m->userdate=userdate($cm->added);
  $m->ref_activite=$mid;
  $m->name=$mname;
  $m->description=$mdescription;
  $m->link=$mlink;

mdl_assign_plugin_config

*/
global $DB;

    $mdata=new Object();
    $mdata->submission='';
    $mdata->comment=array();
    $mdata->feedback='';
    $mdata->file=array();
    $mdata->link=array();  // array of assign mahara plugin object

 	$pattern = array("/<br>/i", "/<br\/>/i", "/<br \/>/i", "/<p>/i",
  	"/<a target='.+' href='(.+)'>(.+)<\/a>/i", "/<a target=\".*\" href=\"(.+)\">(.+)<\/a>/i",
   	"/<a href='(.+)' target='.+'>(.+)<\/a>/i", "/<a href=\"(.+)\" target=\".*\">(.+)<\/a>/i",
    "/<a href='(.+)'>(.+)<\/a>/i", "/<a href=\"(.+)\">(.+)<\/a>/i" );
  	$replacement = array(' ', ' ', ' ', ' ', ' $2:$1 ', ' $2:$1 ', ' $2:$1 ', ' $2:$1 ', ' $2:$1 ', ' $2:$1 ');

	if ($m){
        if (OUTCOMES_SUPER_DEBUG){
        	mtrace("\nDEBUG :: grade/cron_outcomes.php Line 371 ; USER : $userid \nASSIGN MODULE\n");
			print_r($m);
		}

		// rechercher le type
		$assign_plugins = $DB->get_records("assign_plugin_config", array("assignment" => $m->id));
		if ($assign_plugins){
			/*
plugin subtype name value
onlinetext assignsubmission enabled 0|1
file assignsubmission enabled 0|1
file assignsubmission maxfilesubmissions 2
file assignsubmission maxsubmissionsizebytes 0
comments assignsubmission enabled 0|1
comments assignfeedback enabled 0|1
offline assignfeedback enabled 0|1
file assignfeedback enabled 0|1
Lancaster version

// Lancaster  version
--
-- Structure de la table `mdl_assignsubmission_mahara`
--

CREATE TABLE IF NOT EXISTS `mdl_assignsubmission_mahara` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `assignment` bigint(10) NOT NULL DEFAULT '0',
  `submission` bigint(10) NOT NULL DEFAULT '0',
  `viewid` bigint(10) NOT NULL,
  `viewurl` longtext COLLATE utf8_unicode_ci,
  `viewtitle` longtext COLLATE utf8_unicode_ci,
  `viewaccesskey` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `mdl_assimaha_ass_ix` (`assignment`),
  KEY `mdl_assimaha_sub_ix` (`submission`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Data for Mahara submission' AUTO_INCREMENT=2 ;

--
-- Contenu de la table `mdl_assignsubmission_mahara`
--

INSERT INTO `mdl_assignsubmission_mahara` (`id`, `assignment`, `submission`, `viewid`, `viewurl`, `viewtitle`, `viewaccesskey`) VALUES
(1, 1, 1, 27, '/view/view.php?id=27&mt=Mr7cuhqLkgFR8jvSJPpO', 'Enfants', 'Mr7cuhqLkgFR8jvSJPpO');

id	assignment	plugin	subtype	name	value
1	1	mahara	assignsubmission	enabled	0|1
2	1	mahara	assignsubmission	mnethostid	3


// Portland version assign mahara plugin
mahara	assignsubmission	enabled	0|1
mahara	assignsubmission	mahara_host	3
mahara assignfeedback enabled 0|1

http://localhost/moodle24/mod/assign/view.php?id=51&rownum=2&action=grade#
--
-- Structure de la table `mdl24_mahara_portfolio`
--

CREATE TABLE IF NOT EXISTS `mdl24_mahara_portfolio` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `page` bigint(10) NOT NULL,
  `host` bigint(10) NOT NULL,
  `userid` bigint(10) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl24_mahaport_paghos_uix` (`page`,`host`),
  KEY `mdl24_mahaport_pag_ix` (`page`),
  KEY `mdl24_mahaport_paguse_ix` (`page`,`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='A table containing Mahara portfolios' AUTO_INCREMENT=3 ;

--
-- Contenu de la table `mdl24_mahara_portfolio`
--

INSERT INTO `mdl24_mahara_portfolio` (`id`, `page`, `host`, `userid`, `title`, `url`) VALUES
(1, 17, 3, 4, 'Hiver arrive', '/view/view.php?id=17&mt=y6gBTQ0rtp9cUfKlaoVE'),
(2, 22, 3, 3, 'Enfants', '/view/view.php?id=22&mt=JG7YCHagL91XfuwP3hQZ');

			*/
			foreach ($assign_plugins as $ap){
				// DEBUG
                if (OUTCOMES_SUPER_DEBUG){
                    mtrace("\nDEBUG :: grade/cron_outcomes.php Line 394 ; ASSIGN PLUGIN\n");
					print_r($ap);
				}
				if (($ap->plugin=='onlinetext') && ($ap->subtype=='assignsubmission') && ($ap->name=='enabled')  && ($ap->value=='1')){
					// recuperer le texte soumis
                    if ($as = $DB->get_record("assign_submission", array("assignment" => $m->id, "userid" => $userid))){
                        if ($onlinetext = $DB->get_record("assignsubmission_onlinetext", array("assignment" => $m->id, "submission" => $as->id))){
			            	if (OUTCOMES_SUPER_DEBUG){
                    			mtrace("\nDEBUG :: grade/cron_outcomes.php Line 402 ; ASSIGN ONLINETEXT\n");
								print_r($as);
							}
							$mdata->submission.=strip_tags(preg_replace($pattern, $replacement, $onlinetext->onlinetext));
						}
					}
				}
				
				// Assign mahara plugin
				if (($ap->plugin=='mahara') && ($ap->subtype=='assignsubmission') && ($ap->name=='enabled')  && ($ap->value=='1')){
					// recuperer le lien soumis
                    if ($as = $DB->get_record("assign_submission", array("assignment" => $m->id, "userid" => $userid))){
                        // Lancaster version  assign mahara plugin
                        if ($dbman = $DB->get_manager()){ // loads ddl manager and xmldb classes
                            $table = new xmldb_table("assignsubmission_mahara");
		                    if ($dbman->table_exists($table)){
                                if ($maharaobject = $DB->get_record("assignsubmission_mahara", array("assignment" => $m->id, "submission" => $as->id))){ 
                                // Look for hostid                                                                        plugin	subtype	name
                                    if ($apmaharahost = $DB->get_record("assign_plugin_config", array("assignment" => $m->id, "plugin" => "mahara", "subtype" => "assignsubmission", "name" => "mnethostid" ))){
                                        $maharalink= new Object();
                                        $maharalink->page=$maharaobject->viewid;
                                        $maharalink->host=$apmaharahost->value;
                                        $maharalink->userid=$userid;
                                        $maharalink->title=$maharaobject->viewtitle;
                                        $maharalink->url=$maharaobject->viewurl;
                                                           
                                        $mdata->link[]=$maharalink;
                                    }                                                                                                               
				        		}
                            }
                        }
                        
                        // Portland version assign mahara plugin
                        if ($dbman = $DB->get_manager()){ // loads ddl manager and xmldb classes
                            $table = new xmldb_table("assign_mahara_submit_views");
		                    if ($dbman->table_exists($table)){
                                if ($maharaview = $DB->get_record("assign_mahara_submit_views", array("assignment" => $m->id, "submission" => $as->id))){
                                    if ($maharalink = $DB->get_record("mahara_portfolio", array("id" => $maharaview->portfolio, "userid" => $userid))){
                                        $mdata->link[]=$maharalink;
                                    }
                                }
                            }
                        }
                    }                        
				}

				if (($ap->plugin=='comments') && ($ap->subtype=='assignsubmission') && ($ap->name=='enabled')  && ($ap->value=='1')){
					// recuperer les commentaires soumis
					if ($as = $DB->get_record("assign_submission", array("assignment" => $m->id, "userid" => $userid))){
                    	if ($comments = $DB->get_records("comments", array("commentarea" => "submission_comments", "itemid" => $as->id))){
			            	if (OUTCOMES_SUPER_DEBUG){
                    			mtrace("\nDEBUG :: grade/cron_outcomes.php Line 413 ; ASSIGN COMMENTS\n");
								print_r($comments);
							}

							foreach ($comments as $comment){
                        		if (!empty($comment)) {
        							$mdata->comment[]=get_string('commentby','referentiel'). referentiel_user($comment->userid). ' ('.userdate($comment->timecreated).') : '.strip_tags(preg_replace($pattern, $replacement, $comment->content));
								}
							}
						}
					}
				}

				if (($ap->plugin=='comments') && ($ap->subtype=='assignfeedback') && ($ap->name=='enabled')  && ($ap->value=='1')){
					// recuperer le feedback soumis par l'enseignant
                    if ($ag = $DB->get_record("assign_grades", array("assignment" => $m->id, "userid" => $userid))){
                        if ($feedback = $DB->get_record("assignfeedback_comments", array("assignment" => $m->id, "grade" => $ag->id))){
			            	if (OUTCOMES_SUPER_DEBUG){
                    			mtrace("\nDEBUG :: grade/cron_outcomes.php Line 430 ; ASSIGN FEEDBACK\n");
								print_r($feedback);
							}
							$mdata->feedback = strip_tags(preg_replace($pattern, $replacement, $feedback->commenttext));
						}
					}
				}

                if (($ap->plugin=='file') && ($ap->subtype=='assignsubmission') && ($ap->name=='enabled')  && ($ap->value=='1')){
					// recuperer le fichier soumis par l'etudiant
                    if ($as = $DB->get_record("assign_submission", array("assignment" => $m->id, "userid" => $userid))){
                        if ($af = $DB->get_record("assignsubmission_file", array("assignment" => $m->id, "submission" => $as->id))){
			                if (OUTCOMES_SUPER_DEBUG){
                    			mtrace("\nDEBUG :: grade/cron_outcomes.php Line 442 ; ASSIGN FILE\n");
								print_r($af);
							}
							if ($af->numfiles>0){
								// recuperer l'url du fichier
                                if ($files = $DB->get_records("files", array("component" => "assignsubmission_file", "filearea" => "submission_files", "itemid" => $as->id, "userid" => $userid))){
					                if (OUTCOMES_SUPER_DEBUG){
        		            			mtrace("\nDEBUG :: grade/cron_outcomes.php Line 449 ; FILES\n");
										print_r($files);
									}
									foreach ($files as $afile){
                                        if (!empty($afile)) {
											$mdata->file[]=$afile;
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	if (OUTCOMES_SUPER_DEBUG){
		mtrace("\nDEBUG :: grade/cron_outcomes.php Line 467 ; MDATA\n");
		print_r($mdata);
	}

    return ($mdata);
}


/**
 * input : starttime, endtime : fenêtre d'exploration
 * output : notation array
 */
// -------------------------------------------------
function referentiel_get_outcomes($starttime, $endtime){
// genere le liste des notations
global $CFG;
global $DB;

	$notations=array();
	// selectionner tous les codes de referentiel
    $params=array();
	$sql = "SELECT {referentiel_referentiel}.id AS ref_referentiel,
        {referentiel_referentiel}.code_referentiel AS code_referentiel
  FROM {referentiel_referentiel} ";

	if (OUTCOMES_SUPER_DEBUG){
  		mtrace("\nDEBUG :: ./mod/referentiel/grade/cron_outcomes.php :: Line 491\n");
  		mtrace("\nFonction referentiel_get_outcomes \n");
		mtrace("\nSQL:$sql\n");
  	}
	$r_occurrences=$DB->get_records_sql($sql, $params);

	if ($r_occurrences){
		foreach($r_occurrences as $r_occurrence){
      		// DEBUG
      		if (OUTCOMES_SUPER_DEBUG){
        		mtrace("DEBUG :: Line 501 :: OCCURRENCES\n");
        		print_r($r_occurrence);
      		}

	    // selectionner les outcomes
	    /*

--
-- Structure de la table 'mdl_grade_outcomes'
--

CREATE TABLE mdl_grade_outcomes (
  id bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  courseid bigint(10) unsigned DEFAULT NULL,
  shortname varchar(255) NOT NULL DEFAULT '',
  fullname text NOT NULL,
  scaleid bigint(10) unsigned DEFAULT NULL,
  description text,
  timecreated bigint(10) unsigned DEFAULT NULL,
  timemodified bigint(10) unsigned DEFAULT NULL,
  usermodified bigint(10) unsigned DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY mdl_gradoutc_cousho_uix (courseid,shortname),
  KEY mdl_gradoutc_cou_ix (courseid),
  KEY mdl_gradoutc_sca_ix (scaleid),
  KEY mdl_gradoutc_use_ix (usermodified)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='This table describes the outcomes used in the system. An out';

      */
      $params=array("fullname" => "$r_occurrence->code_referentiel%") ;
      $sql = "SELECT id, shortname, fullname, scaleid
      FROM {grade_outcomes}
      WHERE fullname LIKE :fullname
      ORDER BY fullname ASC ";
		if (OUTCOMES_SUPER_DEBUG){
  			mtrace("\nDEBUG :: Line 517\n");
            print_r($params);
			mtrace("\nSQL:$sql\n");
  		}

		$r_outcomes=$DB->get_records_sql($sql, $params);
        if (OUTCOMES_SUPER_DEBUG){
  				mtrace("\nDEBUG :: Line 524\n");
            	print_r($r_outcomes);
				mtrace("\n");
        }

		if ($r_outcomes){
			$t_outcomes=array();  // liste des objectifs associés à cette occurrence

        	foreach ($r_outcomes as $r_outcome){
				// selectionner les items (activites utilisant ces outcomes)
				$a = new Object();
                $a->id=$r_outcome->id;
                $a->shortname=$r_outcome->shortname;
                $a->scaleid=$r_outcome->scaleid;
                $t_outcomes[$r_outcome->id]=$a;
			}

			// Trouver les instances associees à cette occurrence
			$params0= array( "occurrenceid" => $r_occurrence->ref_referentiel);
			$sql0 = "SELECT {referentiel}.id AS instanceid, {referentiel}.course AS courseid
 FROM {referentiel}
 WHERE {referentiel}.ref_referentiel=:occurrenceid
 ORDER BY {referentiel}.course ASC, {referentiel}.id ASC ";

            $r_instances=$DB->get_records_sql($sql0, $params0);

			if (!empty($r_instances)){
                foreach ($r_instances as $r_instance){
					$where1='';
					$params1=array();
        		    $params1[]='';  // item module not null
					if (!empty($t_outcomes)){
						foreach ($t_outcomes as $outcome) {
							if (!empty($outcome->id)){
                        		$params1[]=$r_instance->courseid;
                    			$params1[]=$outcome->id;
                        		$params1[]=$outcome->scaleid;      // jointure scaleid identique
								if (!empty($where1)){
									$where1=$where1 . ' OR ((courseid=?) AND (outcomeid=?) AND (scaleid=?)) ';
								}
								else {
        	        				$where1=' ((courseid=?) AND (outcomeid=?) AND (scaleid=?)) ';
								}
							}
						}

    	    			if (!empty($where1)){
							$where1=' (itemmodule != ?) AND ('. $where1 . ')';

							$sql1='SELECT id, courseid, itemmodule, iteminstance, grademin, grademax, scaleid, outcomeid FROM {grade_items} WHERE '.$where1.' ORDER BY iteminstance, outcomeid  ';

							if (OUTCOMES_SUPER_DEBUG){
  								mtrace("\nDEBUG :: Line 605 :: PARAMETRES REQUETE \n");
								print_r($params1);
								mtrace("\nSQL : $sql1\n");
          					}

		            		$r_items_ids=$DB->get_records_sql($sql1, $params1);


        		  			if ($r_items_ids){
								// DEBUG
              					if (OUTCOMES_SUPER_DEBUG){
	                				mtrace("DEBUG :: Line 614 :: GRADE_ITEMS IDS Outcomes <br/>\n");
    	            				print_r($r_items_ids);
        		      			}

								// selectionner les grades_grades correspondants
								$where2='';
								$params2=array();

								if (!empty($r_items_ids)){
									foreach ($r_items_ids as $r_items_id) {
                		    			$params2[]=$r_items_id->id;        // jointure itemid identique
                    					$params2[]=$starttime;
		                    			$params2[]=$endtime;
										if (!empty($where2)){
											$where2=$where2 . ' OR ((itemid=?) AND(timemodified >= ?) AND (timemodified < ?)) ';
										}
										else {
		        	        				$where2=' ((itemid=?) AND (timemodified >= ?) AND (timemodified < ?)) ';
										}
									}
                                	if (!empty($where2)){
										$where2=' ('. $where2 . ')';
										$sql2='SELECT id, itemid, userid, rawgrademax, rawgrademin, rawscaleid, usermodified, finalgrade, timemodified
 FROM {grade_grades}
 WHERE '.$where2.' ORDER BY itemid, timemodified DESC ';

									if (OUTCOMES_SUPER_DEBUG){
  										mtrace("\nDEBUG :: Line 644 :: PARAMETRES REQUETE\n");
										print_r($params2);
										mtrace("\nSQL : $sql2\n");
          							}
            						$r_grades_recents=$DB->get_records_sql($sql2, $params2);

									if ($r_grades_recents){
										// rechercher les activites correspondantes
										// drapeau pour eviter de traiter plusieurs fois la même ligne grade_grades
										$t_grades_traites=array();

										foreach ($r_grades_recents as $r_grades_recent) {
          									$params3=array("itemid" => "$r_grades_recent->itemid");
          									$sql3 = "SELECT id, courseid, categoryid, itemname, itemtype, itemmodule, iteminstance, itemnumber, iteminfo, idnumber, calculation, gradetype, grademax, grademin, scaleid, outcomeid, timemodified
 FROM {grade_items}  WHERE id= :itemid ORDER BY courseid, outcomeid ASC ";
											if (OUTCOMES_SUPER_DEBUG){
  												mtrace("\nDEBUG :: Line 652\n");
												print_r($params3);
												mtrace("\nSQL : $sql3\n");
        	  								}
          									$r_item_isole=$DB->get_record_sql($sql3, $params3);

											if ($r_item_isole){
    	                                        if (OUTCOMES_SUPER_DEBUG){
        	                                            mtrace("\nDEBUG :: Line 660");
														print_r($r_item_isole);
                	    	    						mtrace("\n");
												}

												// rechercher toutes les lignes similaires en dhors de la fenetre temporelle
          										$params4=array("courseid" => "$r_item_isole->courseid", "iteminstance" => "$r_item_isole->iteminstance", "scaleid" => $r_item_isole->scaleid, "outcomenull" => "" );
	          									$sql4 = "SELECT id, courseid, categoryid, itemname, itemtype, itemmodule, iteminstance, itemnumber, iteminfo, idnumber, calculation, gradetype, grademax, grademin, scaleid, outcomeid, timemodified
 FROM {grade_items}  WHERE courseid=:courseid AND iteminstance=:iteminstance AND scaleid=:scaleid AND outcomeid!=:outcomenull ORDER BY courseid, outcomeid ASC ";
												if (OUTCOMES_SUPER_DEBUG){
  													mtrace("\nDEBUG :: Line 670\n");
													print_r($params4);
													mtrace("\nSQL : $sql4\n");
	          									}

    	      									$r_items=$DB->get_records_sql($sql4, $params4);

												if (!empty($r_items)){
    	                                        	if (OUTCOMES_SUPER_DEBUG){
        	                                            mtrace("\nDEBUG :: Line 679");
														print_r($r_items);
                	    	    						mtrace("\n");
													}

													$t_items=array();
												 	$where5='';
													$params5=array();
													foreach ($r_items as $r_item) {
														$t_items[$r_item->id]=$r_item;   // stocker l'objet dans un tableau indexe par l'id (jointure itemid de grade_grades à l'etape suivante)
                    									$params5[]=$r_item->id;
                    									$params5[]=$endtime;
	                                                    $params5[]=$r_grades_recent->userid;
														if (!empty($where5)){
															$where5=$where5 . ' OR ((itemid=?) AND (timemodified < ?) AND (userid=?)) ';
														}
														else {
        	        										$where5=' ((itemid=?) AND (timemodified < ?) AND (userid=?)) ';
														}

													}

    	    										if (!empty($where5)){
														$where5=' ('. $where5 . ')';
														$sql5='SELECT id, itemid, userid, rawgrademax, rawgrademin, rawscaleid, usermodified, finalgrade, timemodified
 FROM {grade_grades}
 WHERE  '.$where5.'  ORDER BY itemid, timemodified ';
														if (OUTCOMES_SUPER_DEBUG){
  															mtrace("\nDEBUG :: \nLine 694\n");
															print_r($params5);
															mtrace("\nSQL : $sql5\n");
          												}

	                                                    $r_grades=$DB->get_records_sql($sql5, $params5);

														if ($r_grades){
 						                					foreach($r_grades as $r_grade){
																if (($r_grade) && (isset($t_outcomes[$t_items[$r_grade->itemid]->outcomeid]))){
                	                                                if (!isset($t_grades_traites[$r_grade->id])){
																		$t_grades_traites[$r_grade->id]=1;
																	}
                													else{
                                	                                    $t_grades_traites[$r_grade->id]=0;
																	}

																	if (!empty($t_grades_traites[$r_grade->id])){
	   	          														if (OUTCOMES_SUPER_DEBUG){
    	    	        													mtrace("DEBUG :: Line 704 :: ITEMS");
	    	        					    								print_r($r_grades);
            	        	    											mtrace("\n");

	            			       											mtrace("REFERENTIEL INSTANCE : ".$r_instance->instanceid.", Course_id: ".$r_instance->courseid);
						            	    	  							mtrace("REFERENTIEL OCCURRENCE : ".$r_occurrence->code_referentiel);
        		    					      								mtrace("OBJECTIF : Id:".$t_items[$r_grade->itemid]->outcomeid." Nom:".$t_outcomes[$t_items[$r_grade->itemid]->outcomeid]->shortname);
						    		              							mtrace("ITEM : Num_Cours:".$t_items[$r_grade->itemid]->courseid.", Nom_Item:".$t_items[$r_grade->itemid]->itemname.", module:".$t_items[$r_grade->itemid]->itemmodule.", instance:".$t_items[$r_grade->itemid]->iteminstance);
																		}

																		// stocker l'activite pour traitement
						                			      				$notation=new Object();
        	            					  							$notation->referentiel_instanceid=$r_instance->instanceid;
	                                                                    $notation->courseid=$t_items[$r_item->id]->courseid;
									                	    			$notation->ref_referentiel=$r_occurrence->ref_referentiel;
        	    								        				$notation->code_referentiel=$r_occurrence->code_referentiel;
																		// outcome
					            	          							$notation->outcomeid= $t_items[$r_grade->itemid]->outcomeid;
		        						              					$notation->outcomeshortname= $t_outcomes[$t_items[$r_grade->itemid]->outcomeid]->shortname;
	                                                                    $notation->scaleid= $t_outcomes[$t_items[$r_grade->itemid]->outcomeid]->scaleid;
																		// activity
    	            								      				$notation->itemname= $t_items[$r_grade->itemid]->itemname;
								                    	  				$notation->module=  $t_items[$r_grade->itemid]->itemmodule;
        			    					          					$notation->moduleinstance= $t_items[$r_grade->itemid]->iteminstance;
																		// grade
																		$notation->userid=$r_grade->userid;
								                      					$notation->teacherid=$r_grade->usermodified;
	                                                                    $notation->scaleid= $t_outcomes[$t_items[$r_grade->itemid]->outcomeid]->scaleid;
		        								              			$notation->finalgrade=$r_grade->finalgrade;
						    		            		      			$notation->timemodified=$r_grade->timemodified;
																		// archiver
																		$notations[]= $notation;
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
  		}
  	}
	}
	}
  	return $notations;
}

/**
 * Given an object containing all the necessary data,
 * this function will create a new activity and return the id number
 *
 * @param object $activite: a special referentiel_activite object
 * @param object $m a secial module object
 * @return int The id of the newly inserted record
 **/
function referentiel_activite_outcomes($activite, $m, $liste_bareme_evaluations='', $mdata=NULL) {
// creation / mise a jour activite par objectif
global $CFG;
global $DB;

	$activite_id=0;
	if (OUTCOMES_SUPER_DEBUG){
        // DEBUG
        mtrace("\nDEBUG :: referentiel_activite_outcomes :: 715\nDEMANDE MISE A JOUR\n");
        print_r($activite);
        mtrace("\n");
        print_r($m);
        mtrace("\n ".$liste_bareme_evaluations);
        mtrace("\n");
    }
    $activite->type_activite=addslashes($activite->type_activite);

    $activite->mailnow=0;
    $activite->mailed=1;         // ne pas notifier.
/*
  $m->id;
  $m->type=$modulename;
  $m->instance=$moduleinstance;
  $m->course=$courseid;
  $m->date=$cm->added;
  $m->userdate=userdate($cm->added);
  $m->ref_activite=$mid;
  $m->name=$mname;
  $m->description=$mdescription;
  $m->link=$mlink;
*/

  // cette activite est-elle enregistree ?
    $params=array("ref_instance"=>"$activite->ref_instance",
        "ref_referentiel"=>"$activite->ref_referentiel",
        "ref_course"=>"$activite->ref_course",
        "userid"=>"$activite->userid",
        "type"=>"$m->type",
        "moduleid"=>"$m->id",
        "ref_activite"=>"$m->ref_activite");

	$sql = "SELECT * FROM {referentiel_activite_modules}
  WHERE ref_instance=:ref_instance
  AND ref_referentiel=:ref_referentiel AND ref_course=:ref_course
  AND userid=:userid
  AND type=:type
  AND moduleid=:moduleid
  AND ref_activite=:ref_activite ";
    if (OUTCOMES_SUPER_DEBUG){
		mtrace("\nVERIF table referentiel_activite_modules\n");
        print_r($params);
        mtrace("\nSQL:\n$sql\n");
	}

	$r_activite_outcomes=$DB->get_record_sql($sql, $params);

    if ($r_activite_outcomes) {
        if (OUTCOMES_SUPER_DEBUG){
		  // DEBUG
		  mtrace("\nDEBUG :: referentiel_activite_outcomes :: 761\n**** Activité déjà enregistrée *** \n");
		  print_r($r_activite_outcomes);
          mtrace("\n");
        }

        // cet utilisateur est deja reference pour cette activite
        $activite_old=$DB->get_record("referentiel_activite", array("id" => "$r_activite_outcomes->activiteid"));
        if ($activite_old){
            if (OUTCOMES_SUPER_DEBUG){
                // DEBUG
                mtrace("\nDEBUG :: grade/cron_outcomes/referentiel_activite_outcomes :: 772\nOLD\n");
                print_r($activite_old);
                mtrace("\n");
            }

			// Verifier
			/*
				$mdata->submission='';
		    	$mdata->comment=array();
    			$mdata->feedback='';
		    	$mdata->file=array();
                $mdata->link=array();
			*/
			if (!empty($mdata)){
        		if (!empty($mdata->submission)){
					if (!strpos($mdata->submission, $activite_old->description_activite)){
						$activite->description_activite.="\n".get_string('submission','referentiel').' '.$mdata->submission;
					}
				}
    			if (!empty($mdata->comment)){
					foreach ($mdata->comment as $acomment){
						if (!strpos($acomment, $activite_old->description_activite)){
							$activite->description_activite.="\n".$acomment;
						}
					}
 				}
        		if (!empty($mdata->feedback)){
					if (!empty($activite_old->commentaire_activite)){
						if (!strpos($mdata->feedback, $activite_old->commentaire_activite)){
							$activite->commentaire_activite.="\n".$mdata->feedback;
						}
					}
					else{
                        $activite->commentaire_activite.=$mdata->feedback;
					}
				}
			}
    		$activite->description_activite=addslashes($activite->description_activite);
    		$activite->commentaire_activite=addslashes($activite->commentaire_activite);

			// fusionner l'activite
            $activite->id=$activite_old->id;
            $activite->date_modif_student=$activite_old->date_modif_student;
            if (!$activite->date_modif){
                $activite->date_modif=time();
            }
            // $activite->approved=$activite_old->approved;
            $activite->approved=1;

            if (empty($activite_old->date_modif) || ($activite_old->date_modif<$activite->date_modif)){
                // DEBUG
                // print_object($activite);
                // echo "<br />";
            	if ($DB->update_record("referentiel_activite", $activite)){
					referentiel_mise_a_jour_competences_certificat_user($activite_old->competences_activite, $activite->competences_activite, $activite->userid, $activite->ref_referentiel, $activite->approved, true, $activite->approved);
                    $activite_id=$activite->id;
					// Bareme
					if ($CFG->referentiel_use_scale && !empty($liste_bareme_evaluations)){
						if (OUTCOMES_SUPER_DEBUG){
                    		// DEBUG
							mtrace("\nLINE 802 :: BAREME :: $liste_bareme_evaluations");
						}
						referentiel_enregistrer_ce_bareme_activite($liste_bareme_evaluations, referentiel_get_liste_codes_competence($activite->ref_referentiel), $activite_id, referentiel_get_bareme_id_occurrence($activite->ref_referentiel));
					}

	                if (!empty($mdata)){
                        if (!empty($mdata->link)){ // Portland version of assign mahara plugin
							foreach ($mdata->link as $alink){
								if (!empty($alink)){
				            		if (OUTCOMES_SUPER_DEBUG){
    	    			        		// DEBUG
        	        					mtrace("\nDEBUG :: grade/cron_outcomes/referentiel_activite_outcomes :: 1129 :: link DOCUMENT \n");
		    	        	    		print_r($alink);
	        			    		}
                                	$url=addslashes(referentiel_get_mahara_link($alink));
	                                if (!$document_old=$DB->get_record("referentiel_document", array("url_document" => $url, "ref_activite" => $activite_id))){
					        	    	$document = new object();
        				    			$document->url_document=$url;
	        	    					$document->type_document=addslashes(substr(get_string('mahara', 'referentiel'),0,20));
		        	    				$document->description_document=get_string('mahara','referentiel');
    		        					$document->ref_activite=$activite_id;
										$document->cible_document=1;
										$document->etiquette_document=addslashes($alink->title);
			    		        		// DEBUG
    	    				    		// print_r($document);
				            			// echo "<br />";
    	        	        			$document_id = $DB->insert_record("referentiel_document", $document);
        	    						// echo "DOCUMENT ID / $document_id<br />";
									}
								}
							}
						}

			    	    if (!empty($mdata->file)){

							foreach ($mdata->file as $afile){
								if (!empty($afile) && ($afile->filename!='.') && ($afile->filesize>0)){
				            		if (OUTCOMES_SUPER_DEBUG){
    	    			        		// DEBUG
        	        					mtrace("\nDEBUG :: grade/cron_outcomes/referentiel_activite_outcomes :: 1104 :: FILE DOCUMENT \n");
		    	        	    		print_r($afile);
	        			    		}
                                	$url=addslashes(referentiel_url_file($afile));
	                                if (!$document_old=$DB->get_record("referentiel_document", array("url_document" => $url, "ref_activite" => $activite_id))){
					        	    	$document = new object();
        				    			$document->url_document=$url;
	        	    					$document->type_document=addslashes(substr(get_string('document', 'referentiel'),0,20));
		        	    				$document->description_document=get_string('assignementdoc','referentiel');
    		        					$document->ref_activite=$activite_id;
										$document->cible_document=1;
										$document->etiquette_document=addslashes($afile->filename);
			    		        		// DEBUG
    	    				    		// print_r($document);
				            			// echo "<br />";
    	        	        			$document_id = $DB->insert_record("referentiel_document", $document);
        	    						// echo "DOCUMENT ID / $document_id<br />";
									}
								}
							}
						}
					}
    				return true;
				}
            }
        }
        else{
            // Cette activite a ete supprimee
            // Supprimer la ligne qui n'a plus de correspondance dans les activites
            $DB->delete_records("referentiel_activite_modules", array("id" => "$r_activite_outcomes->id"));

        }
    }
    else {
        if (OUTCOMES_SUPER_DEBUG){
                // DEBUG
                mtrace("\nDEBUG :: grade/cron_outcomes/referentiel_activite_outcomes :: 786 :: NEW ACTIVITY\n");
                print_r($activite);
                mtrace("\n");
        }
		/*
		$mdata->submission='';
    	$mdata->comment=array();
    	$mdata->feedback='';
    	$mdata->file=array();
		*/
		if (!empty($mdata)){
        	if (!empty($mdata->submission)){
				$activite->description_activite.="\n".get_string('submission','referentiel').' '.$mdata->submission;
			}
    		if (!empty($mdata->comment)){
				foreach ($mdata->comment as $acomment){
					$activite->description_activite.="\n".$acomment;
				}
    		}
        	if (!empty($mdata->feedback)){
				$activite->commentaire_activite.=$mdata->feedback;
			}
		}
    	$activite->description_activite=addslashes($activite->description_activite);
    	$activite->commentaire_activite=addslashes($activite->commentaire_activite);
        // inserer l'activité
        $activite_id = referentiel_insert_activite_controlee($activite);

        //if 	(($activite_id>0) && ($activite->competences_activite!='')){
        if 	($activite_id>0){
            // mise a jour du certificat
            referentiel_mise_a_jour_competences_certificat_user('', $activite->competences_activite, $activite->userid, $activite->ref_referentiel, $activite->approved, true, false);

			// Bareme
            if ($CFG->referentiel_use_scale && !empty($liste_bareme_evaluations)){
				if (OUTCOMES_SUPER_DEBUG){
                    // DEBUG
					mtrace("\nLINE 838 :: BAREME :: $liste_bareme_evaluations");
				}
				referentiel_enregistrer_ce_bareme_activite($liste_bareme_evaluations, referentiel_get_liste_codes_competence($activite->ref_referentiel), $activite_id, referentiel_get_bareme_id_occurrence($activite->ref_referentiel));
			}

        }
        else{
			// inutile de créer cette activité car aucune compétence n'est validée
            if (OUTCOMES_SUPER_DEBUG){
                // DEBUG
                mtrace("\nDEBUG :: grade/cron_outcomes/referentiel_activite_outcomes :: 848 :: \nINSERTION INUTILE CETTE ACTIVITE \n");
                print_r($activite);
                mtrace("\n");
            }

        }

        // Rajouter le lien
        if (isset($activite_id) && ($activite_id>0)
			&&
			(	(isset($m->link) && !empty($m->link))
				||
				(isset($m->name) && !empty($m->description))
			)
	     ){
            $document = new object();
            $document->url_document=$m->link;
            $document->type_document=addslashes(substr(get_string('modulename', $m->type),0,20));
            $document->description_document=get_string('assignementdoc','referentiel');; // addslashes($m->description);
            $document->ref_activite=$activite_id;
			$document->cible_document=1;
			$document->etiquette_document=addslashes($m->name);
            $document_id = $DB->insert_record("referentiel_document", $document);
        }

		if (!empty($mdata)){
    	    if (!empty($mdata->link)){  // portland version of assign mahara plugin
				foreach ($mdata->link as $alink){
					if (!empty($alink)){
		            	if (OUTCOMES_SUPER_DEBUG){
        		        	// DEBUG
                			mtrace("\nDEBUG :: grade/cron_outcomes/referentiel_activite_outcomes :: 1263 :: link DOCUMENT \n");
		                	print_r($alink);
	        		    }
			            $document = new object();
        			    $document->url_document=addslashes(referentiel_get_mahara_link($alink));
            			$document->type_document=addslashes(substr(get_string('mahara', 'referentiel'),0,20));
            			$document->description_document=get_string('mahara','referentiel'); // addslashes($m->description);
            			$document->ref_activite=$activite_id;
						$document->cible_document=1;
						$document->etiquette_document=addslashes($alink->title);
            	        $document_id = $DB->insert_record("referentiel_document", $document);
					}
				}
			}

    	    if (!empty($mdata->file)){
				foreach ($mdata->file as $afile){
					if (!empty($afile) && ($afile->filename!='.') && ($afile->filesize>0)){
		            	if (OUTCOMES_SUPER_DEBUG){
        		        	// DEBUG
                			mtrace("\nDEBUG :: grade/cron_outcomes/referentiel_activite_outcomes :: 1216 :: FILE DOCUMENT \n");
		                	print_r($afile);
	        		    }
			            $document = new object();
        			    $document->url_document=addslashes(referentiel_url_file($afile));
            			$document->type_document=addslashes(substr(get_string('document', 'referentiel'),0,20));
            			$document->description_document=get_string('assignementdoc','referentiel'); // addslashes($m->description);
            			$document->ref_activite=$activite_id;
						$document->cible_document=1;
						$document->etiquette_document=addslashes($afile->filename);
            	        $document_id = $DB->insert_record("referentiel_document", $document);
					}
				}
			}
		}



        //
        if (isset($activite_id) && ($activite_id>0)){
            //
            $r_a_outcomes=new object();
            $r_a_outcomes->activiteid=$activite_id;
            $r_a_outcomes->ref_course=$activite->ref_course;
            $r_a_outcomes->ref_instance=$activite->ref_instance;
            $r_a_outcomes->ref_referentiel=$activite->ref_referentiel;
            $r_a_outcomes->userid=$activite->userid;
            $r_a_outcomes->type=$m->type;
            $r_a_outcomes->moduleid=$m->id;
            $r_a_outcomes->ref_activite=$m->ref_activite;
            $DB->insert_record("referentiel_activite_modules", $r_a_outcomes);
            return true;
        }
    }
    return false;
}

/**
 * Given an object containing activity data,
 * this function will check the completude and insert a new row and return the id number
 *
 * @param object $activite an special referentiel_activite object
 * @return int The id of the newly inserted record
 **/
function referentiel_insert_activite_controlee($activite){
// on verifie si la liste de competences appartient
// bien au referentiel designe
// histoire d'eviter des affectations intempestives pour des referentiel ayant le meme code
/*

object Object
(
    [type_activite] => [Activity Assignment 6]  Date:  Friday,  22 April 2011, 08:03 AM
    [description_activite] => D�pot de fichier avec Objectifs : D�posez un ficheir PDF
    [competences_activite] => B.2.1/B.2.2/
    [commentaire_activite] =>
    [ref_instance] => 1
    [ref_referentiel] => 1
    [ref_course] => 2
    [userid] => 3
    [teacherid] => 2  // MODIF JF 2012/01/31
    [date_creation] => 1303452226
    [date_modif_student] =>  1303453169
    [date_modif] => 1303453170
    [approved] => 1
    [ref_task] => 0
    [mailnow] => 0
    [mailed] => 1
)


*/
global $DB;

    if (!empty($activite) && !empty($activite->competences_activite)) {
        $rref= $DB->get_record("referentiel_referentiel", array("id" => $activite->ref_referentiel));
        if (OUTCOMES_SUPER_DEBUG){
                // DEBUG
                mtrace("\nDEBUG :: grade/cron_outcomes/referentiel_activite_outcomes :: 894 :: CONTROLE INSERTION ACTIVITE POUR UN REFERENTIEL\n");
                mtrace("COMPETENCES : $activite->competences_activite \n est-elle compatible avec \n LISTE : $rref->liste_codes_competence\n");
        }
        if (!empty($rref->liste_codes_competence)){
            $t_codes_item=explode('/', $activite->competences_activite);
            if ($t_codes_item && (count($t_codes_item)>0)){
                foreach($t_codes_item as $code_item){
                    if (!empty($code_item)){
                        $pos=strpos($rref->liste_codes_competence, $code_item);
                        if ($pos===false){
                            return 0;
                        }
                    }
                }

                return $DB->insert_record("referentiel_activite", $activite);
            }
        }
    }
    return 0;
}

// -------------------------------
function referentiel_enregistrer_ce_bareme_activite($liste_bareme_evaluations, $liste_codes_competences, $activite_id, $bareme_id){
	$liste_evaluations='';
    if (OUTCOMES_SUPER_DEBUG){
    	// DEBUG
		mtrace("\nLigne 1055 Bareme '".$liste_bareme_evaluations."'");
        mtrace("\n Codes '".$liste_codes_competences."'");
	}

	if ($liste_bareme_evaluations){
		$liste_a_comparer=trim(strtr($liste_bareme_evaluations, '.', '_'));
		$titem=explode('/',$liste_codes_competences);
        if ($titem){
			foreach ($titem as $item){
                if (!empty($item)){
                    if (OUTCOMES_SUPER_DEBUG){
            			// DEBUG
						mtrace("\n ITEM '".$item."'");
					}
					$code_search=strtr($item, '.', '_');
					if ($sub=mb_stristr($liste_a_comparer, $code_search)){
						// mtrace("\n SUB '".$sub."'");
						$deb=mb_strpos($sub,':');
						$fin=mb_strpos($sub,'/');
						$len=$fin-$deb-1;
                        //mtrace("\n DEB '".$deb."' FIN '".$fin."' LEN '".$len."'");
						$val=mb_substr($sub,$deb+1,$len);
                        $val--;
                        if (OUTCOMES_SUPER_DEBUG){
            				// DEBUG
							mtrace("\n VAL '".$val."'");
						}
						$liste_evaluations.=$item.":".$val."/";
                    }
                    else{
						$liste_evaluations.=$item.":-1/";
					}
                }
            }
        }
        if (OUTCOMES_SUPER_DEBUG){
            // DEBUG
			mtrace("\n Liste Items '".$liste_codes_competences."' ");
            mtrace("\n Liste bareme '".$liste_evaluations."' ");
		}
    	referentiel_enregistrer_evaluation_activite($liste_evaluations, $activite_id, $bareme_id);
	}
}


// -----------------------
function referentiel_get_bareme_occurrence($occurrence_id){
	global $DB;
	if (!empty($occurrence_id)){
		if ($rec=$DB->get_record('referentiel_a_scale_ref', array('refrefid'=>$occurrence_id))){
			if ($bareme=$DB->get_record('referentiel_scale', array('id'=>$rec->refscaleid))){
				return $bareme;
			}
		}
	}
	return NULL;
}



?>

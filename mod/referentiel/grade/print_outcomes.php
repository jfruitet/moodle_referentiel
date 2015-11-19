<?php
    // affiche les notes et objectifs en rapport avec les referentiels de comp�tence
    
    
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

// ################## APPELE PAR OUTCOMES.PHP ############################
// affichage dans un onglet des modules d'activites utilisant les objectifs deu r�f�rentiel

define ('NOTATION_SUPER_DEBUG', 0);       // SUPER DEBUG NOTATION INACTIF
// define ('NOTATION_SUPER_DEBUG', 1);       // SUPER DEBUG NOTATION ACTIF


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
  elseif ($modulename=='assignment'){
    if (! $assignment = $DB->get_record("assignment", array("id" => "$cm->instance"))) {
      // error("DEBUG :: referentiel_get_module_info :: This assignment doesn't exist");
      return false;
    }
    $mid=$assignment->id;
    $mname=$assignment->name;
    $mdescription=$assignment->intro;
    $mlink = $CFG->wwwroot.'/mod/assignment/view.php?a='.$assignment->id;
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
  $m->id=$module->id;
  $m->type=$modulename;
  $m->instance=$moduleinstance;
  $m->course=$courseid;
  $m->date=$cm->added;
  $m->userdate=userdate($cm->added);
  $m->ref_activite=$mid;
  $m->name=$mname;
  $m->description=$mdescription;
  $m->link=$mlink;

  return $m;
}


//------------------------------
function referentiel_print_repartition_objectifs($referentiel_instance){
// Affiche les repartitions d'affectations de competences
// de cette instance de referentiel
global $DB;
static $iseditor=false;
static $referentiel_id = NULL;

  if (!empty($referentiel_instance)){
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record('course', array('id' => $cm->course));
    if (empty($cm) or empty($course)){
        	print_error('REFERENTIEL_ERROR :: grades/print_outcomes.php :: 168 :: You cannot call this script in that way');
    }

    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}


    $referentiel_id = $referentiel_instance->ref_referentiel;
	$iseditor = has_capability('mod/referentiel:writereferentiel', $context);

	if (!empty($referentiel_id)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_id);
		if (!$referentiel_referentiel){
			if ($iseditor){
				print_error(get_string('creer_referentiel','referentiel'), "edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
				print_error(get_string('creer_referentiel','referentiel'), "../../course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}
        // codes item
        $t_codes_competence=explode('/',referentiel_purge_dernier_separateur($referentiel_referentiel->liste_codes_competence, '/'));
        referentiel_affiche_outcomes($referentiel_instance, $referentiel_referentiel, $course,  $context, $t_codes_competence);
    }
  }
}



/**
 * Given an object containing all the necessary data,
 * this function will display these data
 *
 * @param object $objectifs an array of objects
 * @return null
 **/

function referentiel_affiche_outcomes($referentiel_instance, $referentiel_referentiel, $course, $context, $t_competences){
// liste les activites du serveur evaluees par objectifs du r�f�rentiel
global $DB;
global $scales;
  //
    $objectifs = array();
    $notations = array();

    // liste des cours et des instances utilisant l'occurrence du referentiel
    $params=array("refrefid" => "$referentiel_referentiel->id") ;
	$sql = "SELECT {referentiel}.id AS instanceid,
        {referentiel}.course AS courseid,
        {referentiel_referentiel}.id AS ref_referentiel,
        {referentiel_referentiel}.code_referentiel AS code_referentiel
  FROM {referentiel}, {referentiel_referentiel}
  WHERE {referentiel}.ref_referentiel=:refrefid
  AND {referentiel}.ref_referentiel={referentiel_referentiel}.id
  ORDER BY {referentiel}.course ASC, {referentiel}.id ASC,
    {referentiel_referentiel}.code_referentiel ASC ";

  // DEBUG
  /*
  if (REFERENTIEL_DEBUG){
    echo "<br />DEBUG :: ./mod/referentiel/grade/print_outcomes.php<br />Line 97 :: SQL:$sql<br/>\n";
  }
  */

	$r_referentiels=$DB->get_records_sql($sql, $params);

    // DEBUG
    if (OUTCOMES_SUPER_DEBUG){
        echo "DEBUG :: ./mod/referentiel/grade/print_outcomes.php :: Ligne 233<br />SQL : $sql\n";
    }
	$r_referentiels=$DB->get_records_sql($sql, $params);
    if ($r_referentiels){
        foreach($r_referentiels as $r_referentiel){
            // DEBUG
            /*
            echo "DEBUG :: ./mod/referentiel/grade/print_outcomes.php\n";
            echo "<br />Ligne 113<br />\n";
            print_r($r_referentiel);
            echo "<br />\n";
            */
            if (OUTCOMES_SUPER_DEBUG){
                echo("<p>Cours : $r_referentiel->courseid<br />OCCURRENCE : $r_referentiel->code_referentiel<br />INSTANCE : $r_referentiel->instanceid</p>\n");
            }

            // Selectionner les outcomes AYANT LE CODE REFERENTIEL_REFERENTIEL DANS LEUR FULLNAME

            // ATTENTION :: ceci est source d'ambiguite car si deux referentiels
            // differents ont codes identiques  la recherche n'est pas discriminente.
            // Par ailleurs le courseid obtenu ne sert a rien car il peut �tre NULL
            // si les outcomes sont standards (d�finis au niveau du site
            // et non du cours

            $params2=array("fullname" => "$r_referentiel->code_referentiel%") ;
            $sql2 = "SELECT id, courseid, shortname, fullname, scaleid
      FROM {grade_outcomes}
      WHERE fullname LIKE :fullname
      ORDER BY fullname ASC ";
            $r_outcomes=$DB->get_records_sql($sql2, $params2);

            if ($r_outcomes){
                // DEBUG
                /*
                echo("DEBUG :: ./mod/referentiel/grade/print_outcomes.php Line 267 <br />OBJECTIFS<br />\n");
                print_r($r_outcomes);
                echo "<br />\n";
                */
                if (OUTCOMES_SUPER_DEBUG){
                    echo("<ul>OBJECTIFS\n");
                }
                foreach($r_outcomes as $r_outcome){

                    if (!isset($objectifs[$r_outcome->id])){
                        $objectifs[$r_outcome->id]=new Object();
                        $objectifs[$r_outcome->id]->id=$r_outcome->id;
                        $objectifs[$r_outcome->id]->shortname=$r_outcome->shortname;
                        $objectifs[$r_outcome->id]->fullname=$r_outcome->fullname;
                        $objectifs[$r_outcome->id]->occurrence=$r_referentiel->code_referentiel;
                    }

                    if (OUTCOMES_SUPER_DEBUG){
                        echo("<li>Objectif \n");
                        echo("Id:".$r_outcome->id." Nom long:".$r_outcome->fullname." \n");
                        // print_object($r_outcome);
                        // echo "<br />\n";

                        if (!empty($r_outcome->courseid)){
                            echo(" LOCAL \n");
                        }
                        else{
                            echo(" GLOBAL \n");
                        }
                   }
                    // selectionner les items (activites utilisant ces outcomes)

                    $params3=array("outcomeid" => "$r_outcome->id", "courseid" => "$r_referentiel->courseid");
                    $sql3 = "SELECT id, courseid, categoryid, itemname, itemtype, itemmodule, iteminstance, itemnumber, iteminfo, idnumber, calculation, gradetype, grademax, grademin, scaleid, outcomeid, timemodified
 FROM {grade_items}  WHERE outcomeid= :outcomeid  AND courseid=:courseid
 ORDER BY courseid, outcomeid ASC ";
                    $r_items=$DB->get_records_sql($sql3, $params3);


                    if (empty($r_items)){
                        if (OUTCOMES_SUPER_DEBUG){
                            echo(" : NA <br />\n");
                        }
                    }
                    else{
                        foreach($r_items as $r_item){
                            if ($r_item){
                                if (OUTCOMES_SUPER_DEBUG){
                                    echo("<br />DEBUG :: Ligne 315 :: REFERENTIEL INSTANCE : ".$r_referentiel->instanceid.", Course_id: ".$r_referentiel->courseid."<br />\n");
                                    echo("REFERENTIEL CODE : ".$r_referentiel->code_referentiel."<br />\n");
                                    echo("OBJECTIF : Id:".$r_outcome->id." Nom long:".$r_outcome->fullname."<br />\n");

                                    echo "<br /><i>ITEM\n";
                                    print_object($item);
                                    echo "<br />\n";
                                }
                                // stocker l'activite pour traitement
                                $notation=new Object();
                                $notation->referentiel_instanceid=$r_referentiel->instanceid;
                                $notation->courseid=$r_referentiel->courseid;
                                $notation->ref_referentiel=$r_referentiel->ref_referentiel;
                                $notation->code_referentiel=$r_referentiel->code_referentiel;
                                $notation->outcomeid= $r_outcome->id;
                                $notation->outcomeshortname= $r_outcome->shortname;
                                $notation->scaleid= $r_outcome->scaleid;
                                $notation->itemname= $r_item->itemname;
                                $notation->module=  $r_item->itemmodule;
                                $notation->moduleinstance= $r_item->iteminstance;

                                $objectifs[$r_outcome->id]->notations[]=$notation;

                            }
                        }
                    }
                    if (OUTCOMES_SUPER_DEBUG){
                        echo "</li>\n";
                    }
                }
                if (OUTCOMES_SUPER_DEBUG){
                    echo "</ul>\n";
                }
            }
        }
    }

    $nb_competences=0;
    $message='<h3>'.get_string('usedoutcomes','referentiel').'</h3>';

    if ($t_competences){
        $competences_list=implode(',',$t_competences);
        $nb_competences=count($t_competences);
    }

    if ($objectifs){
        /*
        if (OUTCOMES_SUPER_DEBUG){
            echo("<h3>OBJECTIFS DU REFERENTIEL</h3>\n");
            print_object($objectifs);
            echo "<br />\n";
        }
        */
        echo '<div align="center">'."\n";
        // echo $message."\n";
		echo '<table class="activite">'."\n";
        echo "<tr valign='top'><th align='left' width='10%'>".get_string('outcome','referentiel')."</th>\n";
        echo "<th align='center' width='90%'>";
        echo get_string('module_activite','referentiel');
        echo "</th></tr>\n";

  		foreach($objectifs as $objectif){
            if ($objectif){
                $t_fullname[$objectif->shortname]=$objectif->fullname;
                if (!empty($objectif->notations)){
                    $t_outcomes[$objectif->shortname]=$objectif->notations;
                }
            }
        }
        for ($i=0; $i<$nb_competences; $i++){
            echo "<tr valign='top'><td width='10%'>\n";
            echo "<b>".$t_competences[$i]."</b>\n";
            echo "</td><td>";
            if (!empty($t_fullname[$t_competences[$i]])){
                echo $t_fullname[$t_competences[$i]];
            }
            if (!empty($t_outcomes[$t_competences[$i]])){
                echo "<ul>\n";
                foreach($t_outcomes[$t_competences[$i]] as $notation){
                    referentiel_affiche_module_activite($notation);
                }
                echo "</ul>\n";
            }
            else{
            }
            echo "</td></tr>\n";
        }
        echo '</table>'."\n";
        echo '</div>'."\n";
	}
}

/**
 * Given an object containing all the necessary data,
 * this function will display these data
 *
 * @param object $notation
 * @return null
 **/
function referentiel_affiche_module_activite($notation){
    global $DB;
    global $CFG;
        //print_r($notation);
        //echo "<br />\n";
    if (!empty($notation) && !empty($notation->module) && !empty($notation->moduleinstance) && !empty($notation->courseid)){
        $course_instance=$DB->get_record('course', array('id' => $notation->courseid));
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

        $module = referentiel_get_module_info($notation->module, $notation->moduleinstance, $notation->courseid);
        echo '<li><b>'.get_string('occurrence', 'referentiel').'</b> : #'.$notation->ref_referentiel
        .' '.$notation->code_referentiel
        .' <b>'.get_string('course').'</b> : '.$link_course
        .' <b>'.get_string('module', 'referentiel').' '.get_string('modulename', $notation->module);
        echo '  #'.$notation->moduleinstance ;
        echo '</b> <b>'.get_string('date_creation', 'referentiel').'</b> '.$module->userdate ;
        echo ' <br /> <b>'.get_string('titre', 'referentiel').'</b> : <a href="'.$module->link.'">'.$module->name.'</a>'
        .' <b>'.get_string('description', 'referentiel').'</b> :<i>'.$module->description
        .'</i></li> '."\n";
    }
}


?>

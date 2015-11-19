<?php
 // $Id:  lib_cron.php,v 1.0 2011/03/30 00:00:00 jfruitet Exp $
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
 * Library of functions and constants for module referentiel
 *
 * @author jfruitet
 * @version $Id: lib_cron.php,v 1.0 2011/03/30 00:00:00 jfruitet Exp $
 * @package referentiel v 6.0.00 2011/04/01 00:00:00
 **/

// ###################################  DEBUT CRON


/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
**/

function referentiel_cron() {
global $CFG;

    if (NOTIFICATION_ACTIVITES){
      referentiel_cron_activites();
    }
    if (NOTIFICATION_TACHES){
      referentiel_cron_taches();
    }
    if (NOTIFICATION_CERTIFICATS){
      referentiel_cron_certificats();
    }
    referentiel_cron_scolarite();    // a ne pas deplacer
    referentiel_cron_archives();
    
    if (!empty($CFG->enableoutcomes) && (REFERENTIEL_OUTCOMES)){
        require_once('lib_bareme.php');
        require_once('grade/cron_outcomes.php');
        referentiel_traitement_notations(); //
    }
    return true;
}

// -----------------------------------------
function referentiel_cron_archives(){
global $CFG;
global $DB;

    if ($CFG->referentiel_purge_archives){ // purge automatique des archives
        require_once('lib_archive.php');
        mtrace("DEBUT CRON PURGE REFERENTIEL ARCHIVES");
        // Archives older than REFERENTIEL_ARCHIVE_OBSOLETE days will be deleted.
        $delai_destruction = REFERENTIEL_ARCHIVE_OBSOLETE * 24 * 3600;

        /// Get all the appropriate data
        $referentiel_referentiels = referentiel_get_referentiel_referentiels( NULL);
        // Liste des occurrences de referentiels
        foreach ($referentiel_referentiels as $referentiel_referentiel) {
            if ($referentiel_referentiel){
                // Liste d'instances de cette occurence
                $referentiel_instances = $DB->get_records("referentiel", array("ref_referentiel" => "$referentiel_referentiel->id"));
                if ($referentiel_instances){
                    foreach ($referentiel_instances as $referentiel_instance) {
                        $course_instance=$DB->get_record('course', array('id' => $referentiel_instance->course));
                        if ($course_instance){
                    		$course_module = get_coursemodule_from_instance('referentiel', $referentiel_instance->id, $course_instance->id);
                            if ($course_module){
                                $context_instance = context_module::instance($course_module->id);
                                if ($context_instance){
                                    // purger les archives obsoletes
                                    referentiel_purge_archives($context_instance->id, $delai_destruction, true);
                                }
                            }
                        }
                    }
                }
            }
        }
        mtrace("FIN CRON PURGE REFERENTIEL ARCHIVES.\n");
    }
}


// -----------------------------------------
function referentiel_cron_scolarite(){
// mise a jour de la table referentiel_scolarite
global $DB;
/*
CREATE TABLE IF NOT EXISTS `mdl_referentiel_etudiant` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `num_etudiant` varchar(20) NOT NULL DEFAULT '',
  `ddn_etudiant` varchar(14) NOT NULL DEFAULT '',
  `lieu_naissance` varchar(255) NOT NULL DEFAULT '',
  `departement_naissance` varchar(255) NOT NULL DEFAULT '',
  `adresse_etudiant` varchar(255) NOT NULL DEFAULT '',
  `userid` bigint(10) unsigned NOT NULL,
  `ref_etablissement` bigint(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Fiche Etudiant' AUTO_INCREMENT=22 ;*/

    mtrace("DEBUT CRON REFERENTIEL SCOLARITE");
    // $sql="SELECT * FROM {referentiel_etudiant} ";
    $params = array("num_etudiant" => "", "num_etudiant_inconnu" => "inconnu");
    $sql = "SELECT * FROM {referentiel_etudiant}
    WHERE num_etudiant=:num_etudiant
    OR num_etudiant=:num_etudiant_inconnu ";
    $res=$DB->get_records_sql($sql, $params);
    if ($res){
        foreach ($res as $record){
      	// controle
	      if (($record->userid>0)
/*
          &&
          (($record->num_etudiant=='') || ($record->num_etudiant==get_string('l_inconnu', 'referentiel')))
*/
          )
        {
            $user=$DB->get_record("user", array("id" => "$record->userid"));
            if ($user){
                if (!empty($user->idnumber)){
                    $record->num_etudiant = $user->idnumber;
                }
                else{
                    $record->num_etudiant = $user->username;
                }
              }
            }
	       $DB->update_record("referentiel_etudiant", $record);
        }
    }
    mtrace("FIN CRON REFERENTIEL SCOLARITE");
    return true;
}

// -----------------------------------------
function referentiel_get_courses_for_certificate($certificat){
// MODIF JF 2012/02/28
// Renvoyer un cours à partir des infos d'un certificat
// afin de rediger un contenu du message de notification valide concernant le certificat

global $DB;
    $courseids=array(); // liste de id de cours renvoyee
    //Liste des instances
    $instances=$DB->get_records("referentiel", array("ref_referentiel" => $certificat->ref_referentiel));
    if ($instances){
        foreach ($instances as $instance) {
            // verifier si l'étudiant est inscrit
            $students=referentiel_get_students_course($instance->course, $certificat->userid, 0, true);
            if ($students){ // cet etudiant est effectivement inscrit à ce cours
                // verifier que l'enseignant est aussi inscrit au cours
                $teacherids=referentiel_get_teachers_course($instance->course);
                if ($teacherids){
                    foreach ($teacherids as $teacherid){
                        if ($teacherid->userid==$certificat->teacherid){
                            // OK
                            $a_obj=new stdClass();
                            $a_obj->courseid=$instance->course;
                            $a_obj->instanceid=$instance->id;
                            $courseids[$instance->course]=$a_obj;
                        }
                    }
                }
            }
        }
    }
    return $courseids;
}

// -----------------------------------------
function referentiel_cron_certificats(){
// MODIF JF 2012/02/28
// Essayer quand même de récupérer un cours  pour intialiser correctemnt le contenu du message de notification

global $CFG, $USER;
global $DB;
global $COURSE;

    $cronuser = clone($USER);
    $site = get_site();
    $course = get_site();    // LES CERTIFICATS NE SONT PAS LIES A UN COURS
    // all users that are subscribed to any post that needs sending
    $users = array();

    // status arrays
    $mailcount  = array();
    $errorcount = array();

    // caches
    $certificats     = array();
    $subscribedusers = array();

    // Posts older than 2 days will not be mailed.  This is to avoid the problem where
    // cron has not been running for a long time, and then suddenly people are flooded
    // with mail from the past few weeks or months
    $timenow   = time();
    if (NOTIFICATION_DELAI){
        $endtime   = $timenow - $CFG->maxeditingtime;
    }
    else{
        $endtime   = $timenow;
    }
    $starttime = $endtime - NOTIFICATION_INTERVALLE_JOUR * 24 * 3600;   // Two days earlier

// JF
// DEBUG : cron_lib.php :
mtrace("DEBUT CRON REFERENTIEL CERTIFICATS");

    $certificats_r = referentiel_get_unmailed_certificats($starttime, $endtime);

    if ($certificats_r) {
        // Mark them all now as being mailed.  It's unlikely but possible there
        // might be an error later so that a post is NOT actually mailed out,
        // but since mail isn't crucial, we can accept this risk.  Doing it now
        // prevents the risk of duplicated mails, which is a worse problem.

        if (!referentiel_mark_old_certificates_as_mailed($endtime)) {
            mtrace('Errors occurred while trying to mark some referentiel activities as being mailed.');
            return false;  // Don't continue trying to mail them, in case we are in a cron loop
        }

        // checking activity validity, and adding users to loop through later
        foreach ($certificats_r as $cid => $certificat) {
            $certificatid = $certificat->id;

            // DEBUG : cron_lib.php :
            // mtrace('CERTIFICAT '.$certificat->id);

            if (!isset($certificats[$certificatid])) {
                $certificats[$certificatid] = $certificat;
            }


            // caching subscribed teachers of each activity

            $students=array();
// DEBUG : cron_lib.php :
// mtrace('DESTINATAIRES...');
            if (!isset($subscribedusers[$certificatid])) {
                if (NOTIFICATION_JURY){
                    if ($certificats[$certificatid]->userid){      // notifier l'auteur
                        $userid=$certificats[$certificatid]->userid;
                        $user=referentiel_get_user($userid);
                        if (!$user->emailstop) {
                            // this user is subscribed to this notification
                            $subscribedusers[$certificatid][$userid]=$userid;
                            // this user is a user we have to process later
                            $users[$userid] = $user;
// DEBUG : cron_lib.php :
// mtrace('DESTINATAIRE AUTEUR '.$userid);
                        }
                    }
                }
                if ($certificats[$certificatid]->teacherid){          // le certificat est suivie
                  $userid=$certificats[$certificatid]->teacherid;
                  $user=referentiel_get_user($userid);
                  if ($user->emailstop) {
                    if (!empty($CFG->forum_logblocked)) {
                        // add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $user->id);
                    }
                  }
                  else{
                    // this user is subscribed to this notification
                    $subscribedusers[$certificatid][$userid]=$userid;
                    // this user is a user we have to process later
                    $users[$userid] = $user;
// DEBUG : cron_lib.php :
// mtrace('DESTINATAIRE ENSEIGNANT REFERENT '.$userid);
                  }
                }
            }
            $mailcount[$cid] = 0;
            $errorcount[$cid] = 0;
        }
    }


    if ($users && $certificats) {
// DEBUG : cron_lib.php :
// mtrace('TRAITEMENT DES MESSAGES ');
        $urlinfo = parse_url($CFG->wwwroot);
        $hostname = $urlinfo['host'];

        foreach ($users as $userto) {
            @set_time_limit(TIME_LIMIT); // terminate if processing of any account takes longer than 2 minutes

            // set this so that the capabilities are cached, and environment matches receiving user
            $USER = $userto;

            mtrace('Processing user '.$userto->id);

            // init caches
            $userto->viewfullnames = array();
            foreach ($certificats as $cid => $certificat) {
                // Do some checks  to see if we can mail out now
                if (!isset($subscribedusers[$certificat->id][$userto->id])) {
                    continue; // user does not subscribe to this activity
                }
                // Get info about the author user
				// MODIF JF 2015/03/21
				// We need a really complete user object...
                if (array_key_exists($certificat->teacherid, $users)) { // teacher is userfrom
                	$userfrom = $DB->get_record("user", array("id" => "$certificat->teacherid"));
                    $users[$certificat->teacherid] = $userfrom;
                } else if (array_key_exists($certificat->userid, $users)) { // we might know him/her already
                    $userfrom = $DB->get_record("user", array("id" => "$certificat->userid"));
                    $users[$certificat->userid] = $userfrom;
                } else if ($userfrom = $DB->get_record("user", array("id" => "$certificat->userid"))) {
                    $users[$userfrom->id] = $userfrom; // fetch only once, we can add it to user list, it will be skipped anyway
                }
                else {
                    mtrace('Could not find user '.$certificat->userid);
                    continue;
                }


                // MODIF JF 2012/02/28
                // Essayer de déterminer le cours d'où est adressee la notification
                $info_certif = $certificat;
                $info_certif->ref_instance=0;

                if ($courses_instances_ids=referentiel_get_courses_for_certificate($certificat)){
                    foreach ($courses_instances_ids as $course_instance) {
                        // DEBUG
                        // mtrace("lib.php :: 302 :: Certificate course id $course_instance->courseid : instance id : $course_instance->instanceid\n");
                        if ($course_instance){
                            // selectionner le premier disponible
                            $course=$DB->get_record("course", array("id" => $course_instance->courseid));
                            $cm = get_coursemodule_from_instance("referentiel", $course_instance->instanceid);
                            $info_certif->ref_instance=$course_instance->instanceid;
                            break;
                        }
                    }
                }

                // setup global $COURSE properly - needed for roles and languages
                cron_setup_user($userto, $course);
                
                // Fill caches
                if (!isset($userto->viewfullnames[$certificat->id])) {
					$modcontext = context_system::instance();
                    $userto->viewfullnames[$certificat->id] = has_capability('moodle/site:viewfullnames', $modcontext);
                }

                 // Ajout JF  2011/10/25
                $userfrom->viewfullnames = array();

                // Fill caches
                if (!isset($userfrom->viewfullnames[$certificat->id])) {
                    $modcontext = context_module::instance($cm->id);
                    $userfrom->viewfullnames[$certificat->id] = has_capability('moodle/site:viewfullnames', $modcontext);
                }

                if (!isset($userfrom->groups[$certificat->id])) {
                    if (!isset($userfrom->groups)) {
                        $userfrom->groups = array();
                        $users[$userfrom->id]->groups = array();
                    }
                    $userfrom->groups[$certificat->id] = groups_get_all_groups($course->id, $userfrom->id, $cm->groupingid);
                    $users[$userfrom->id]->groups[$certificat->id] = $userfrom->groups[$certificat->id];
                }

                $groupname='';
                if (!empty($userfrom->groups)){
                    // mtrace("\nDEBUG : lib.php :: 285 :: \n");
                    // print_r($userfrom->groups);
                    foreach($userfrom->groups as $un_groupe){
                        // mtrace("\nDEBUG : lib_cron.php :: 288 \n");
                        // print_r($un_groupe);
                        if (!empty($un_groupe)){
                            foreach($un_groupe as $groupe){
                                //mtrace("\nDEBUG : lib.php :: 1015 \n");
                                //print_r($groupe);

                                if (!empty($groupe->name)){
                                    $groupname.= $groupe->name. ' ';
                                }
                            }
                        }
                    }
                    // mtrace("\nDEBUG : lib_cron.php :: 299 :: $groupname \n");
                }

                // OK so we need to send the email.

                // Does the user want this post in a digest?  If so postpone it for now.
                if ($userto->maildigest > 0) {
                    $queue = new object();
                    $queue->userid       = $userto->id;
                    $queue->activiteid   = $certificat->id;
                    $queue->timemodified = $certificat->date_decision;
                    $queue->type = TYPE_CERTIFICAT;
                    
                    if (!$DB->insert_record('referentiel_notification', $queue)) {
                        mtrace("Error: mod/referentiel/lib.php/referentiel_cron_certificats() : Could not queue for digest mail for id $certificat->id to user $userto->id ($userto->email) .. not trying again.");
                    }
                    continue;
                }

                 // Prepare to actually send the post now, and build up the content
                $strcertificatename=get_string('certificat','referentiel').' '.referentiel_get_referentiel_name($certificat->ref_referentiel);
                $cleancertificatename = str_replace('"', "'", strip_tags(format_string($strcertificatename)));

                $userfrom->customheaders = array (  // Headers to make emails easier to track
                           'Precedence: Bulk',
                           'List-Id: "'.$cleancertificatename.'" <moodle_referentiel_certificat_'.$certificat->id.'@'.$hostname.'>',
                           'List-Help: '.$CFG->wwwroot.'index.php',
                           'Message-ID: <moodle_referentiel_certificat_'.$certificat->id.'@'.$hostname.'>',
                           'X-Course-Id: '.$course->id,
                           'X-Course-Name: '.format_string($course->fullname, true),
                           'X-Group-Name: '.$groupname
                );

                if (!empty($groupname)){
                    $postsubject = "$course->shortname: ".format_string($strcertificatename.' - '.get_string('groupe', 'referentiel', $groupname),true);
                }
                else{
                    $postsubject = "$course->shortname: ".format_string($strcertificatename,true);
                }

                $context = context_module::instance($cm->id);
                $posttext = referentiel_make_mail_text(TYPE_CERTIFICAT, $context, $course, $info_certif, $userfrom, $userto);
                $posthtml = referentiel_make_mail_html(TYPE_CERTIFICAT, $context, $course, $info_certif, $userfrom, $userto);

                // Send the post now!
                // mtrace('Sending ', '');
                if (!$mailresult = email_to_user($userto, $userfrom, $postsubject, $posttext,
                                                 $posthtml, '', '', $CFG->forum_replytouser)) {
                    mtrace("Error: certificates : Could not send out mail for id $certificat->id to user $userto->id ($userto->email) .. not trying again.");
                          $errorcount[$cid]++;
                } else if ($mailresult === 'emailstop') {
                    // should not be reached anymore - see check above
                }
                else {
                    $mailcount[$cid]++;
                }
            }
        }
    }

    if ($certificats) {
        foreach ($certificats as $certificat) {
            mtrace($mailcount[$certificat->id]." users were sent certificate $certificat->id");
            if ($errorcount[$certificat->id]) {
                $DB->set_field("referentiel_certificat", "mailed", "2", array("id" => "$certificat->id"));
            }
        }
    }

    // release some memory
    unset($subscribedusers);
    unset($mailcount);
    unset($errorcount);

    $USER = clone($cronuser);
    // course_setup(SITEID);
    cron_setup_user();
    
    $sitetimezone = $CFG->timezone;

    // Now see if there are any digest mails waiting to be sent, and if we should send them
    mtrace('Starting digest processing...');

    @set_time_limit(TIME_LIMIT*2); // terminate if not able to fetch all digests in 5 minutes

    if (!isset($CFG->digestcertificatetimelast)) {    // To catch the first time
        set_config('digestcertificatetimelast', 0);
    }

    $timenow = time();
    $digesttime = usergetmidnight($timenow, $sitetimezone) + ($CFG->digestmailtime * 3600);

    // Delete any really old ones (normally there shouldn't be any)
    $weekago = $timenow - (7 * 24 * 3600);
    $DB->delete_records_select('referentiel_notification', "timemodified < $weekago AND type='".TYPE_CERTIFICAT."'", NULL);
    mtrace('Cleaned old digest records');

    if ($CFG->digestcertificatetimelast < $digesttime and $timenow > $digesttime) {

        mtrace("Sending activity digests: ".userdate($timenow, '', $sitetimezone));

        $params= array("digesttime" => "$digesttime", "type" => TYPE_CERTIFICAT);
        $sql= " (timemodified < :digesttime) AND (type=:type) " ;
        $digestposts_rs = $DB->get_recordset_select('referentiel_notification',$sql, $params);

        // if (!rs_EOF($digestposts_rs)) {     // deprecated
        if ($digestposts_rs->valid()) {

            // We have work to do
            $usermailcount = 0;

            //caches - reuse the those filled before too
            $userposts = array();

            foreach ($digestposts_rs as $digestpost) {
                if (!isset($users[$digestpost->userid])) {
                    if ($user = $DB->get_record("user", array("id" => "$digestpost->userid"))) {
                        $users[$digestpost->userid] = $user;
                    }
                    else {
                        continue;
                    }
                }
                $postuser = $users[$digestpost->userid];
                if ($postuser->emailstop) {
                    if (!empty($CFG->forum_logblocked)) {
                        // add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $postuser->id);
                    }
                    continue;
                }

                // contenu certificat
                // 0 : certificat
                if (!isset($certificats[$digestpost->activiteid])) {
                    if ($certificat = $DB->get_record("referentiel_certificat", array("id" => "$digestpost->activiteid"))) {
                        $certificats[$digestpost->activiteid] = $certificat;
                    }
                    else {
                        continue;
                    }
                }

                $userposts[$digestpost->userid][$digestpost->activiteid] = $digestpost->activiteid;
            }

            $digestposts_rs->close(); /// Finished iteration, let's close the resultset

            // Data collected, start sending out emails to each user
            // print_r($userposts);
            // mtrace("A SUPPRIMER  ligne 453");
            foreach ($userposts as $userid => $theseactivities) {
                @set_time_limit(TIME_LIMIT); // terminate if processing of any account takes longer than 2 minutes

                $USER = $cronuser;
                
                // course_setup(SITEID); // reset cron user language, theme and timezone settings

                cron_setup_user();

                mtrace(get_string('processingdigest', 'referentiel', $userid), '... ');

                // First of all delete all the queue entries for this user
                $DB->delete_records_select('referentiel_notification', "userid = $userid AND (timemodified < $digesttime) AND type='".TYPE_CERTIFICAT."'", NULL);
                $userto = $users[$userid];


                // Override the language and timezone of the "current" user, so that
                // mail is customised for the receiver.
                $USER = $userto;
                // course_setup(SITEID);
                cron_setup_user();
                
                // init caches
                $userto->viewfullnames = array();

                $postsubject = get_string('digestmailsubject', 'referentiel', format_string($site->shortname, true));

                $headerdata = new object();
                $headerdata->sitename = format_string($site->fullname, true);
                $headerdata->userprefs = $CFG->wwwroot.'/user/edit.php?id='.$userid.'&amp;course='.$site->id;

                $posttext = get_string('digestmailheader', 'referentiel', $headerdata)."\n\n";
                $headerdata->userprefs = '<a target="_blank" href="'.$headerdata->userprefs.'">'.get_string('digestmailprefs', 'referentiel').'</a>';

                $posthtml = "<head>";
/*
                foreach ($CFG->stylesheets as $stylesheet) {
                    $posthtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
                }
*/
                $posthtml .= "</head>\n<body id=\"email\">\n";
                $posthtml .= '<p>'.get_string('digestmailheader', 'referentiel', $headerdata).'</p><br /><hr size="1" noshade="noshade" />';


                foreach ($theseactivities as $tid) {
                    mtrace("ligne 495 TID $tid");
                    @set_time_limit(TIME_LIMIT);   // to be reset for each post
                    $type_notification=TYPE_CERTIFICAT;
                    $certificat = $certificats[$tid];

                    //override language
                    // course_setup($course);
                    // setup global $COURSE properly - needed for roles and languages
                    cron_setup_user($userto, $course);


                    // Fill caches
                    if (!isset($userto->viewfullnames[$certificat->id])) {
						$modcontext = context_system::instance();
                        $userto->viewfullnames[$certificat->id] = has_capability('moodle/site:viewfullnames', $modcontext);
                    }

                    $strcertificat   = get_string('certificat', 'referentiel').' '.referentiel_get_referentiel_name($certificat->ref_referentiel);

                    $posttext .= "\n \n";
                    $posttext .= '=====================================================================';
                    $posttext .= "\n \n";
                    $posttext .= "$course->shortname -> ".format_string($strcertificat,true);
                    $posttext .= "\n";

                    $posthtml .= "<p><font face=\"sans-serif\">".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> ";
                    $posthtml .= "</font></p>";
                    $posthtml .= '<p>';

                    // $postsarray = $userposts[$certificat->id];
                    $postsarray = $userposts[$userid];
                    sort($postsarray);
                    // print_r($postsarray);

                    foreach ($postsarray as $activiteid) {
                        $post = $certificats[$activiteid];

                        if (array_key_exists($post->userid, $users)) { // we might know him/her already
                            $userfrom = $users[$post->userid];
                        } else if ($userfrom = $DB->get_record("user", array("id" => "$post->userid"))) {
                            $users[$userfrom->id] = $userfrom; // fetch only once, we can add it to user list, it will be skipped anyway
                        }
                        else {
                            mtrace('Could not find user '.$post->userid);
                            continue;
                        }

                        // JF 2011/10/25
                        if (!isset($userfrom->groups[$post->id])) {
                           	if (!isset($userfrom->groups)) {
                               	$userfrom->groups = array();
                                $users[$userfrom->id]->groups = array();
                            }
                            $userfrom->groups[$post->id] = groups_get_all_groups($course->id, $userfrom->id, $cm->groupingid);
                            $users[$userfrom->id]->groups[$post->id] = $userfrom->groups[$post->id];
                        }
                        $groupname='';
                        if (!empty($userfrom->groups)){
                            // mtrace("\nDEBUG : lib.php :: 285 :: \n");
                            // print_r($userfrom->groups);
                            foreach($userfrom->groups as $un_groupe){
                                // mtrace("\nDEBUG : lib_cron.php :: 288 \n");
                                // print_r($un_groupe);
                                if (!empty($un_groupe)){
                                    foreach($un_groupe as $groupe){
                                        //mtrace("\nDEBUG : lib.php :: 1015 \n");
                                        //print_r($groupe);
                                        if (!empty($groupe->name)){
                                        	$groupname.= $groupe->name. ' ';
                                    	}
                                	}
                            	}
                            }
                        	// mtrace("\nDEBUG : lib_cron.php :: 299 :: $groupname \n");
                        }
                                
                        $userfrom->customheaders = array ("Precedence: Bulk");

                        if ($userto->maildigest == 2) {
                            // Subjects only
                            $by = new object();
                            $by->name = fullname($userfrom);
                            $by->date = userdate($post->date_decision);
                            if (!empty($groupname)){
                                $posttext .= "\n".format_string($post->subject,true).' '.get_string("bynameondate", "referentiel", $by). ' - '.get_string('groupe', 'referentiel', $groupname);
                            }
                            else{
                                $posttext .= "\n".format_string($post->subject,true).' '.get_string("bynameondate", "referentiel", $by);
                            }
                            $posttext .= "\n---------------------------------------------------------------------";

                            $by->name = "<a target=\"_blank\" href=\"$CFG->wwwroot/user/view.php?id=$userfrom->id&amp;course=$course->id\">$by->name</a>";
                            $posthtml .= '<div><a target="_blank" href="'.$CFG->wwwroot.'/index.php">'.format_string($strcertificat,true).'</a> '.get_string("bynameondate", "referentiel", $by).'</div>';

                        }
                        else {
                            // The full treatment
							$context = context_system::instance();
                            $posttext = referentiel_make_mail_text(TYPE_CERTIFICAT, $context, $course, $post, $userfrom, $userto, true);
                            $posthtml = referentiel_make_mail_post(TYPE_CERTIFICAT, $context, $course, $post, $userfrom, $userto, false, true, false);
                        }
                    }
                    $posthtml .= '<hr size="1" noshade="noshade" /></p>';
                }
                $posthtml .= '</body>';

                if ($userto->mailformat != 1) {
                    // This user DOESN'T want to receive HTML
                    $posthtml = '';
                }

                if (!$mailresult =  email_to_user($userto, $site->shortname, $postsubject, $posttext, $posthtml,
                                                  '', '', $CFG->forum_replytouser)) {
                    mtrace("ERROR!");
                    mtrace ("Error:  Could not send out digest mail to user $userto->id ($userto->email)... not trying again.\n");
                    // add_to_log($course->id, 'referentiel', 'mail digest error', '', '', $cm->id, $userto->id);
                } else if ($mailresult === 'emailstop') {
                    // should not happen anymore - see check above
                }
                else {
                    mtrace("success.");
                    $usermailcount++;
                }
            }
        }
		/// We have finishied all digest emails activities, update $CFG->digestcertificatetimelast
        set_config('digestcertificatetimelast', $timenow);
    }

    $USER = $cronuser;
    // course_setup(SITEID); // reset cron user language, theme and timezone settings
    cron_setup_user();
    
    if (!empty($usermailcount)) {
          mtrace(get_string('digestsentusers', 'referentiel', $usermailcount));
    }
    mtrace("FIN CRON REFERENTIEL CERTIFICAT.\n");
    return true;
}


 // -------------------------------------
function referentiel_cron_activites(){
// traite les declarations d'activite
global $CFG, $USER;
global $DB;

    $cronuser = clone($USER);
    $site = get_site();

    // all users that are subscribed to any post that needs sending
    $users = array();

    // status arrays
    $mailcount  = array();
    $errorcount = array();

    // caches
    $activites          = array();     // un tableau des activités à envoyer
    $courses         = array();
    $coursemodules   = array();
    $subscribedusers = array();

    // Posts older than 2 days will not be mailed.  This is to avoid the problem where
    // cron has not been running for a long time, and then suddenly people are flooded
    // with mail from the past few weeks or months
    $timenow   = time();
    if (NOTIFICATION_DELAI){
        $endtime   = $timenow - $CFG->maxeditingtime;
    }
    else{
        $endtime   = $timenow;
    }

    $starttime = $endtime - NOTIFICATION_INTERVALLE_JOUR * 24 * 3600;   // Two days earlier

    // JF
    // DEBUG : cron_lib.php :
    mtrace("\nDEBUT CRON REFERENTIEL ACTIVITES");


    $activities = referentiel_get_unmailed_activities($starttime, $endtime);

    if ($activities) {
        // DEBUG : cron_lib.php :
        // mtrace('ACTIVITES...');

        // Mark them all now as being mailed.  It's unlikely but possible there
        // might be an error later so that a post is NOT actually mailed out,
        // but since mail isn't crucial, we can accept this risk.  Doing it now
        // prevents the risk of duplicated mails, which is a worse problem.

        if (!referentiel_mark_old_activities_as_mailed($endtime)) {
            mtrace('Errors occurred while trying to mark some referentiel activities as being mailed.');
            return false;  // Don't continue trying to mail them, in case we are in a cron loop
        }

        // checking activity validity, and adding users to loop through later
        foreach ($activities as $aid => $activite) {
			if (REFERENTIEL_DEBUG){
            	// DEBUG : cron_lib.php :
            	mtrace("lib_cron.php :: 821 :: ACTIVITE: AID\n");
            	print_r($aid);
				mtrace("\n");
    	        mtrace("ACTIVITE: ACTIVITE\n");
        	    print_r($activite);
				mtrace("\n");
			}
            $activiteid = $activite->id;

            // DEBUG : cron_lib.php :
            // mtrace("ACTIVITE ID: $activite->id\n");

            if (!isset($activites[$activiteid])) {
                $activites[$activiteid] = $activite;
            }
            // cours
            $courseid = $activites[$activiteid]->ref_course;
            if (!isset($courses[$courseid])) {
                if ($course = $DB->get_record("course", array("id" => "$courseid"))) {
                    $courses[$courseid] = $course;
                }
                else {
                    mtrace('Could not find course '.$courseid);
                    unset($activities[$aid]);
                    continue;
                }
            }
            // modules
            $instanceid = $activite->ref_instance;
            if (!isset($coursemodules[$instanceid])) {
                if ($cm = get_coursemodule_from_instance('referentiel', $instanceid, $courseid)) {
                    $coursemodules[$instanceid] = $cm;
                }
                else {
                    mtrace('Could not get course module for referentiel instance '.$instanceid);
                    unset($activities[$aid]);
                    continue;
                }
            }


// DEBUG : cron_lib.php :
// mtrace("DESTINATAIRES...\n");
            if (!isset($subscribedusers[$activiteid])) {
                // MODIF JF 12/12/2010
                // Commencer par déterminer qui a fait quoi
                // Est-ce un etudiant qui a modifié la fiche ou un référent ?
                //
                // Déterminer qui est l'emetteur du message en comparant les dates
                $emetteur=0; // c'est l'etudiant l'emetteur par defaut

                if (!empty($activites[$activiteid]->date_creation)
                    &&
                   (
                            empty($activites[$activiteid]->date_modif)
                            ||
                            (
                                !empty($activites[$activiteid]->date_modif)
                                &&
                                !empty($activites[$activiteid]->date_modif_student)
                                &&
                                ($activites[$activiteid]->date_modif > $activites[$activiteid]->date_modif_student)
                            )
                    )
                )
                {
                        $emetteur=1;     // enseignant est l'emetteur du message
                        // mtrace("\nlib_cron.php :: 697 :: emetteur... TEACHER\n");
                }


                // ENSEIGANTS ASSOCIES A LA DECLARATION

                $teachers = array();     // liste des enseignants associes à cette activite

                if (!empty($activites[$activiteid]->teacherid)){    // referent de l'activite
                    $teachers[]=referentiel_get_user($activites[$activiteid]->teacherid);
                }
                else{  // on recherche les accompagnateurs
                    $teachers_repartition=array();
                    $teachers_repartition=referentiel_get_repartition_competences($instanceid, $courseid, $activite->competences_activite, $teachers_repartition);
                    $teachers=referentiel_get_accompagnements_user($instanceid, $courseid, $activite->userid);
                    // verifier si intersection
                    $teachers=referentiel_intersection_teachers($teachers_repartition, $teachers);
                }
                if (empty($teachers)){
                    // on recherche parmi les enseignants du cours
                    // notifier tous les enseignants sauf les administrateurs et createurs de cours
                    $teachers=referentiel_get_teachers_course($courseid);
                }
if (REFERENTIEL_DEBUG){
	if (!empty($teachers)){
                // DEBUG : cron_lib.php :
                mtrace("\nlib_cron.php :: 913 :: TEACHERS\n");
                print_r($teachers);
                mtrace("\n");
	}
}
                // Liste des destinataires
                // s'il y a un référent et qu'il n'est pas l'emetteur du message

                if ($emetteur==0) { // etudiant
                    if ($teachers)  {
                        foreach ($teachers as $teacher) {
                            // DEBUG
                            if (REFERENTIEL_DEBUG){
                                if (!empty($teacher)){
                                	mtrace("\nlib.php :: 867 :: TEACHER\n");
 	                               	print_r($teacher);
    	                            mtrace("\n");
								}
                            }

                            if (!empty($teacher->id)){
                                $user=referentiel_get_user($teacher->id);
                            }
                            else if (!empty($teacher->userid)){   // car certaines fonctions renvoient userid
                                $user=referentiel_get_user($teacher->userid);
                            }

                            if (!empty($user)){
                                if ($user->emailstop) {
                                    if (!empty($CFG->forum_logblocked)) {
                                        //add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $user->id);
                                    }
                                }
                                else{
                                    // this user is subscribed to this notification
                                    $subscribedusers[$activiteid][$user->id]=$user->id;
                                    // this user is a user we have to process later
                                    $users[$user->id] = $user;
                                    // DEBUG
                                    if (REFERENTIEL_DEBUG){
                                        mtrace('DESTINATAIRE ENSEIGNANT '.$user->id);
                                    }
                                }
                            }
                        }
                    }
                }
                else {
                    // emetteur enseignant
                    // le destinataire est l'étudiant
                    if (!empty($activites[$activiteid]->userid)){      // notifier l'etudiant
                        $userid=$activites[$activiteid]->userid;
                        $user=referentiel_get_user($userid);
                        if ($user){
                            if ($user->emailstop) {
                                if (!empty($CFG->forum_logblocked)) {
                                    //add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $user->id);
                                }
                                mtrace("\nMAIL DESTINATAIRE BLOQUE: $user->id\n");
                            }
                            else{
                                // this user is subscribed to this notification
                                $subscribedusers[$activiteid][$user->id]=$user->id;
                                // this user is a user we have to process later
                                $users[$user->id] = $user;
                                // DEBUG : cron_lib.php :
                                // mtrace('DESTINATAIRE AUTEUR ACTIVITE '.$userid);
                            }
                        }
                    }
                }



                // Faut-il ajouter l'auteur de l'activité à la liste des destinataires ?
                if (NOTIFICATION_AUTEUR_ACTIVITE){
                    if ($emetteur==0){
                        if ($activites[$activiteid]->userid){      // notifier l'etudiant
                            $userid=$activites[$activiteid]->userid;
                            $user=referentiel_get_user($userid);
                            if ($user){
                                if ($user->emailstop) {
                                    if (!empty($CFG->forum_logblocked)) {
                                        //add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $user->id);
                                    }
                                }
                                else{
                                    // this user is subscribed to this notification
                                    $subscribedusers[$activiteid][$user->id]=$user->id;
                                    // this user is a user we have to process later
                                    $users[$user->id] = $user;
                                    // DEBUG : cron_lib.php :
                                    // mtrace('DESTINATAIRE AUTEUR '.$userid);
                                }
                            }
                        }
                    }
                    else{   // le referent est l'emetteur
                        if ($activites[$activiteid]->teacherid){      // notifier le referent

                            $teacherid=$activites[$activiteid]->teacherid;
                            $user=referentiel_get_user($teacherid);
                            if ($user){
                                if ($user->emailstop) {
                                    if (!empty($CFG->forum_logblocked)) {
                                        //add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $user->id);
                                    }
                                }
                                else{
                                    // this user is subscribed to this notification
                                    $subscribedusers[$activiteid][$user->id]=$user->id;
                                    // this user is a user we have to process later
                                    $users[$user->id] = $user;
                                    // DEBUG : cron_lib.php :
                                    // mtrace('DESTINATAIRE AUTEUR '.$userid);
                                }
                            }
                        }
                    }
                }
                unset($teachers); // release memory

            }

            $mailcount[$aid] = 0;
            $errorcount[$aid] = 0;
        }
    }

    // PREPARATION DES MESSAGES
    // La collection des destinataires est maintenant disponible
    // Preparer les messages
    if ($users && $activites) {

if (REFERENTIEL_DEBUG){
	if (!empty($users)){
        // DEBUG : cron_lib.php :
        mtrace("\nlib_cron.php :: 1058 :: USERS DESTINATAIRES\n");
        print_r($users);
        mtrace("\n");
	}
    if (!empty($activites)){
        mtrace("\nlib_cron.php :: 1063 :: ACTIVITES A POSTER\n");
        print_r($activites);
        mtrace("\n");
	}
	if (!empty($subscribedusers)){
        mtrace("\nlib_cron.php :: 1068 :: SUBSCRIBED USERS\n");
        print_r($subscribedusers);
        mtrace("\n");
	}
        // mtrace("\nEXIT : lib_cron.php :: 1072 \n");
        // exit;
}
        $urlinfo = parse_url($CFG->wwwroot);
        $hostname = $urlinfo['host'];

        foreach ($users as $userto) {

if (REFERENTIEL_DEBUG){
        mtrace("\nlib_cron.php :: 1056 :: USER DESTINATAIRE (userto)\n");
        print_r($userto);
        mtrace("\n");
        //mtrace("\nEXIT : lib_cron.php :: 1059 \n");
        //exit;
}
            @set_time_limit(TIME_LIMIT); // terminate if processing of any account takes longer than 3 minutes if PHP not in safe_mode

            // set this so that the capabilities are cached, and environment matches receiving user
            $USER = $userto;

            // mtrace('./mod/referentiel/lib.php :: Line 767 : Processing user '.$userto->id);

            // init caches
            $userto->viewfullnames = array();
            $userto->enrolledin    = array();

            // reset the caches
            foreach ($coursemodules as $coursemoduleid => $unused) {
                $coursemodules[$coursemoduleid]->cache       = new object();
                $coursemodules[$coursemoduleid]->cache->caps = array();
                unset($coursemodules[$coursemoduleid]->uservisible);
            }

            foreach ($activites as $aid => $activite) {
if (REFERENTIEL_DEBUG){
	if (!empty($activite)){
        // DEBUG : cron_lib.php :
        mtrace("\nlib_cron.php :: 1101 :: ACTIVITE POSTEE ($aid)\n");
        print_r($activite);
        mtrace("\n");
	}
    if (!empty($courses)){
		// DEBUG : cron_lib.php :
        mtrace("\nlib_cron.php :: 1106 :: COURS IMPLIQUES \n");
        print_r($courses);
        mtrace("\n");
	}
}
                // Set up the environment for activity, course
                $course     = $courses[$activite->ref_course];
                $cm         =& $coursemodules[$activite->ref_instance];

                // Do some checks  to see if we can mail out now
                if (!isset($subscribedusers[$activite->id][$userto->id])) {
                    continue; // user does not subscribe to this activity
                }

/*

// BIG PROBLEM
                // Verify user is enrolled in course - if not do not send any email
                if (!isset($userto->enrolledin[$course->id])) {
                    // Valable pour Moodle 2.1 et Moodle 2.2
                    //if ($CFG->version < 2011120100) {
                        $context = context_module::instance($cm->id);
                    //} else {
                        // $context = context_module::instance($cm);
                    //}

                    $userto->enrolledin[$course->id] = has_capability('moodle/course:view', $context);
                }
                if (!$userto->enrolledin[$course->id]) {
                    // oops - this user should not receive anything from this course
        mtrace("\nDEBUG : cron_lib.php : : lib_cron.php :: 918\n this user should not receive anything from this course");
        mtrace("\nEXIT : lib_cron.php :: 019 \n");
        exit;

                    continue;
                }
*/

                // Get info about the emettor
                if (!empty($activite->teacherid)
                    && !empty($activite->date_modif)
                    && ($activite->date_modif>$activite->date_modif_student) ){
                    $userfrom = $DB->get_record("user", array("id" => "$activite->teacherid"));
                    if (empty($users[$activite->teacherid])){
                        $users[$activite->teacherid] = $userfrom;
                    }
if (REFERENTIEL_DEBUG){
	mtrace(" lib.php :: 1167 :: ACTIVITE ENVOYEE PAR TEACHER:\n");
}

                }
                else if (array_key_exists($activite->userid, $users)
                    && !empty($users[$activite->userid]) ) { // we might know him/her already
if (REFERENTIEL_DEBUG){
	mtrace(" lib.php :: 1174 :: ACTIVITE ENVOYEE PAR ETUDIANT : \n ");
}                   $userfrom = $DB->get_record("user", array("id" => "$activite->userid"));
                    $users[$activite->userid] = $userfrom;
                }
                else if ($userfrom = $DB->get_record("user", array("id" => "$activite->userid"))) {
                    //mtrace(" lib.php :: 945 :: ACTIVITE ENVOYEE PAR ETUDIANT\n");
                    $users[$userfrom->id] = $userfrom; // fetch only once, we can add it to user list, it will be skipped anyway
if (REFERENTIEL_DEBUG){
	mtrace(" lib.php :: 1182 :: ACTIVITE ENVOYEE PAR ETUDIANT : \n");
}

                }
                else {
                    mtrace('Could not find user '.$activite->userid);
                    continue;
                }
                
                // setup global $COURSE properly - needed for roles and languages
                cron_setup_user($userto, $course);

                // Fill caches
                if (!isset($userto->viewfullnames[$activite->id])) {
                    $modcontext = context_module::instance($cm->id);
                    $userto->viewfullnames[$activite->id] = has_capability('moodle/site:viewfullnames', $modcontext);
                }
                
                // Ajout JF  2011/10/25
                $userfrom->viewfullnames = array();

                // Fill caches
                if (!isset($userfrom->viewfullnames[$activite->id])) {
                    $modcontext = context_module::instance($cm->id);
                    $userfrom->viewfullnames[$activite->id] = has_capability('moodle/site:viewfullnames', $modcontext);
                }

                if (!isset($userfrom->groups[$activite->id])) {
                    if (!isset($userfrom->groups)) {
                        $userfrom->groups = array();
                        $users[$userfrom->id]->groups = array();
                    }
                    $userfrom->groups[$activite->id] = groups_get_all_groups($course->id, $userfrom->id, $cm->groupingid);
                    $users[$userfrom->id]->groups[$activite->id] = $userfrom->groups[$activite->id];
                }



                $groupname='';
                if (!empty($userfrom->groups)){
                    // mtrace("\nDEBUG : lib.php :: 1014 :: \n");
                    // print_r($userfrom->groups);
                                    foreach($userfrom->groups as $un_groupe){
                                        if (!empty($un_groupe)){
                                            // mtrace("\nDEBUG : lib_cron.php :: 288 \n");
                                            // print_r($un_groupe);
                                            if (!empty($un_groupe)){
                                                foreach($un_groupe as $groupe){
                                                  //mtrace("\nDEBUG : lib.php :: 1015 \n");
                                                  //print_r($groupe);

                                                    if (!empty($groupe->name)){
                                                        $groupname.= $groupe->name. ' ';
                                                     }
                                                }
                                            }
                                        }
                                    }
                    // mtrace("\nDEBUG : lib_cron.php :: 1028 :: $groupname \n");
                }


                // OK so we need to send the email.
if (REFERENTIEL_DEBUG){
        mtrace("\nlib_cron.php :: 1216:: EXPEDITEUR : USERFROM ");
        print_r($userfrom);
}


if (REFERENTIEL_DEBUG){
	mtrace(" lib.php :: 1222 :: DESTINATAIRE : USERTO\n");
	print_r($userto);
    mtrace("\n");
}


                // Does the user want this post in a digest?  If so postpone it for now.
                if ($userto->maildigest > 0) {
                    $queue = new object();
                    $queue->userid       = $userto->id;
                    $queue->activiteid   = $activite->id;
                    // A MODIFIER   pour tenir compte de toutes les combinaisons de dates possibles
                    if ($activite->date_modif && $activite->date_modif_student && ($activite->date_modif<$activite->date_modif_student)){
                        $queue->timemodified = $activite->date_modif_student;
                    }
                    else if ( $activite->date_modif && $activite->date_creation && ($activite->date_modif>$activite->date_creation)){
                        $queue->timemodified = $activite->date_modif;
                    }
                    else{
                        $queue->timemodified = $activite->date_creation;
                    }

                    $queue->type = TYPE_ACTIVITE;
                    if (!$DB->insert_record('referentiel_notification', $queue)) {
                        mtrace("Error: mod/referentiel/lib.php : Line 1271 : Could not queue for digest mail for id $activite->id to user $userto->id ($userto->email) .. not trying again.");
                    }
                    continue;
                }


                // Prepare to actually send the post now, and build up the content
                $strreferentielname=get_string('referentiel','referentiel').': '.referentiel_get_instance_name($activite->ref_instance);
                $cleanactivityname = str_replace('"', "'", strip_tags(format_string($strreferentielname.' -> '.$activite->type_activite)));

                $userfrom->customheaders = array (  // Headers to make emails easier to track
                           'Precedence: Bulk',
                           'List-Id: "'.$cleanactivityname.'" <moodle_referentiel_activity_'.$activite->id.'@'.$hostname.'>',
                           'List-Help: '.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$activite->ref_instance.'&activite_id='.$activite->id.'&amp;mode=listactivityall',
                           'Message-ID: <moodle_referentiel_activity_'.$activite->id.'@'.$hostname.'>',
                           'X-Course-Id: '.$course->id,
                           'X-Course-Name: '.format_string($course->fullname, true),
                           'X-Group-Name: '.$groupname
                );


                if (!empty($groupname)){
                    $postsubject = "$course->shortname: ".format_string($strreferentielname.' - '.get_string('groupe', 'referentiel', $groupname).' - '.$activite->type_activite,true);
                }
                else{
                    $postsubject = "$course->shortname: ".format_string($strreferentielname.' '.$activite->type_activite,true);
                }

if (REFERENTIEL_DEBUG){
	mtrace(" lib.php :: 1275 :: ACTIVITE ENVOYEE PAR ETUDIANT : USERFROM\n");
	print_r($userfrom);
    mtrace("\n");
}

		        $context = context_module::instance($cm->id);
                $posttext = referentiel_make_mail_text(TYPE_ACTIVITE, $context, $course, $activite, $userfrom, $userto);
                $posthtml = referentiel_make_mail_html(TYPE_ACTIVITE, $context, $course, $activite, $userfrom, $userto);

if (REFERENTIEL_DEBUG){
	mtrace(" lib.php :: 1285 :: CONTENU  DU MESSAGE a l'UTILISATEUR :\n");
 	mtrace("TEXT\n".$posttext);
    mtrace("\n");
 	mtrace("HTML\n".$posthtml);
    mtrace("\n");
}

                // Send the post now!
                // mtrace('Sending ', '');

                if (!$mailresult = email_to_user($userto, $userfrom, $postsubject, $posttext,
                                                 $posthtml, '', '', $CFG->forum_replytouser)) {
                    mtrace("\nError: mod/referentiel/lib.php:  Could not send out mail for id $activite->id to user $userto->id".
                         " ($userto->email) .. not trying again.");
                    //add_to_log($course->id, 'referentiel', 'mail error', "activite $activite->id to user $userto->id ($userto->email)","", $cm->id, $userto->id);
                    $errorcount[$aid]++;
                }
                else if ($mailresult === 'emailstop') {
                    // should not be reached anymore - see check above
                }
                else {
                    $mailcount[$aid]++;
                }
            }
                // mtrace('./mod/referentiel/lib.php :: Line 371 : post '.$activite->id. ': '.$activite->type_activite);
        }
    }
if (REFERENTIEL_DEBUG){
    if (!empty($mailcount)){
    	mtrace("\nlib_cron.php :: 1314 :: SENT MESSAGES :: MAILCOUNT\n");
        print_r($mailcount);
        mtrace("\n");
    }
    if (!empty($errorcount)){
	    mtrace("\nlib_cron.php :: 1319 :: NOT SENT MESSAGES :: ERRORCOUNT\n");
        print_r($errorcount);
        mtrace("\n");
	}
}
    if ($activites) {
        foreach ($activites as $activite) {
            mtrace($mailcount[$activite->id]." users were sent activity $activite->id, $activite->type_activite");
            if ($errorcount[$activite->id]) {
                $DB->set_field("referentiel_activite", "mailed", "2", array("id" => "$activite->id"));
            }
        }
    }

    // release some memory
    unset($subscribedusers);
    unset($mailcount);
    unset($errorcount);

    $USER = clone($cronuser);
    // course_setup(SITEID);
    cron_setup_user();
    
    $sitetimezone = $CFG->timezone;

    // Now see if there are any digest mails waiting to be sent, and if we should send them

    mtrace("Starting digest processing...\n");

    @set_time_limit(TIME_LIMIT*2); // terminate if not able to fetch all digests in 5 minutes

    if (!isset($CFG->digestactivitytimelast)) {    // To catch the first time
        set_config('digestactivitytimelast', 0);
    }

    $timenow = time();
    $digesttime = usergetmidnight($timenow, $sitetimezone) + ($CFG->digestmailtime * 3600);

    // Delete any really old ones (normally there shouldn't be any)
    $weekago = $timenow - (7 * 24 * 3600);
    $DB->delete_records_select('referentiel_notification', "timemodified < $weekago AND type='".TYPE_ACTIVITE."'", NULL);

    if ($CFG->digestactivitytimelast < $digesttime and $timenow > $digesttime) {

        mtrace("Sending activity digests: ".userdate($timenow, '', $sitetimezone));

        $params= array("digesttime" => "$digesttime", "type" => TYPE_ACTIVITE);
        $sql= " (timemodified < :digesttime) AND (type=:type) ";
        $digestposts_rs = $DB->get_recordset_select('referentiel_notification',$sql, $params);

        // if (!rs_EOF($digestposts_rs)) { // deprecated
        if ($digestposts_rs->valid()) {
            // We have work to do
            $usermailcount = 0;
            $userposts = array();


            foreach ($digestposts_rs as $digestpost) {

                if (!isset($users[$digestpost->userid])) {
                    if ($user = $DB->get_record("user", array("id" => "$digestpost->userid"))) {
                        $users[$digestpost->userid] = $user;
                    }
                    else {
                        continue;
                    }
                }
                $postuser = $users[$digestpost->userid];
                if ($postuser->emailstop) {
                    if (!empty($CFG->forum_logblocked)) {
                        //add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $postuser->id);
                    }
                    continue;
                }

                // contenu activite
                // 0 : ACTIVITE
                if (!isset($activites[$digestpost->activiteid])) {
                    if ($activite = $DB->get_record("referentiel_activite", array("id" => "$digestpost->activiteid"))) {
                        $activites[$digestpost->activiteid] = $activite;
                    }
                    else {
                        continue;
                    }
                }
                $courseid = $activites[$digestpost->activiteid]->ref_course;
                if (!isset($courses[$courseid])) {
                    if ($course = $DB->get_record("course", array("id" => "$courseid"))) {
                        $courses[$courseid] = $course;
                    }
                    else {
                        continue;
                    }
                }

                if (!isset($coursemodules[$activites[$digestpost->activiteid]->ref_instance]) && $activites[$digestpost->activiteid]) {
                    if ($cm = get_coursemodule_from_instance('referentiel', $activites[$digestpost->activiteid]->ref_instance, $courseid)) {
                        $coursemodules[$activites[$digestpost->activiteid]->ref_instance] = $cm;
                    }
                    else {
                        continue;
                    }
                }

                $userposts[$digestpost->userid][$digestpost->activiteid] = $digestpost->activiteid;
            }

            $digestposts_rs->close(); /// Finished iteration, let's close the resultset

            // Data collected, start sending out emails to each user
            foreach ($userposts as $userid => $theseactivities) {
                @set_time_limit(TIME_LIMIT); // terminate if processing of any account takes longer than 2 minutes

                $USER = $cronuser;
                // course_setup(SITEID); // reset cron user language, theme and timezone settings
                cron_setup_user();
                
                mtrace(get_string('processingdigest', 'referentiel', $userid), '... ');

                // First of all delete all the queue entries for this user
                $DB->delete_records_select('referentiel_notification', "userid = $userid AND (timemodified < $digesttime) AND type='".TYPE_ACTIVITE."'", NULL);
                $userto = $users[$userid];


                // Override the language and timezone of the "current" user, so that
                // mail is customised for the receiver.
                $USER = $userto;
                // course_setup(SITEID);
                cron_setup_user();
                
                // init caches
                $userto->viewfullnames = array();

                $postsubject = get_string('digestmailsubject', 'referentiel', format_string($site->shortname, true));

                $headerdata = new object();
                $headerdata->sitename = format_string($site->fullname, true);
                $headerdata->userprefs = $CFG->wwwroot.'/user/edit.php?id='.$userid.'&amp;course='.$site->id;

                $posttext = get_string('digestmailheader', 'referentiel', $headerdata)."\n\n";
                $headerdata->userprefs = '<a target="_blank" href="'.$headerdata->userprefs.'">'.get_string('digestmailprefs', 'referentiel').'</a>';

                $posthtml = "<head>";
/*
                foreach ($CFG->stylesheets as $stylesheet) {
                    $posthtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
                }
*/
                $posthtml .= "</head>\n<body id=\"email\">\n";
                $posthtml .= '<p>'.get_string('digestmailheader', 'referentiel', $headerdata).'</p><br /><hr size="1" noshade="noshade" />';


                foreach ($theseactivities as $tid) {
                    // DEBUG : cron_lib.php :
                    // mtrace("DEBUG : cron_lib.php : :: lib.php 1031 :: TID : $tid");
                    // print_r($theseactivities);

                    @set_time_limit(TIME_LIMIT);   // to be reset for each post
                    $type_notification=TYPE_ACTIVITE;
                    $activite = $activites[$tid];
                    $course     = $courses[$activites[$tid]->ref_course];
                    $cm         = $coursemodules[$activites[$tid]->ref_instance];

                    //override language

                    // course_setup($course);
                    // setup global $COURSE properly - needed for roles and languages
                    cron_setup_user($userto, $course);


                    // Fill caches
                    if (!isset($userto->viewfullnames[$activite->id])) {
				        $modcontext = context_module::instance($cm->id);
                        $userto->viewfullnames[$activite->id] = has_capability('moodle/site:viewfullnames', $modcontext);
                    }

                    $strreferentiels      = get_string('referentiels', 'referentiel');

                    $posttext .= "\n \n";
                    $posttext .= '=====================================================================';
                    $posttext .= "\n \n";
                    $posttext .= "$course->shortname -> $strreferentiels -> ".format_string($activite->type_activite,true);
                    $posttext .= "\n";
                    $posthtml .= "<p><font face=\"sans-serif\">".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> ".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/referentiel/index.php?id=$course->id\">$strreferentiels</a> -> ".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/referentiel/activite.php?d=$activite->ref_instance&activite_id=$activite->id\">".format_string($activite->type_activite,true)."</a>";
                    $posthtml .= "</font></p>";
                    $posthtml .= '<p>';

                    // $postsarray = $discussionposts[$discussionid];
                    // $postsarray = $userposts[$activite->id];
                    // mtrace("DEBUG : cron_lib.php : :: lib.php 1237 :: USERPOST");
                    // print_r($userposts);

                    // mtrace("DEBUG : cron_lib.php : :: lib.php 1240 :: POSTARRAY");
                    $postsarray = $userposts[$userid];
                    // print_r($userposts);

                    if (($postsarray) && is_array($postsarray)){
                        sort($postsarray);

                        foreach ($postsarray as $activiteid) {
                            $post = $activites[$activiteid];
                            if ($post->courseid==$course->id){   // CORRIGE ERREUR affichage activites n'appartenat pas au cours AJOUT JF 07/12/2010
                                if (array_key_exists($post->userid, $users)) { // we might know him/her already
                                    $userfrom = $users[$post->userid];
                                }
                                else if ($userfrom = $DB->get_record("user", array("id" => "$post_userid"))) {
                                    $users[$userfrom->id] = $userfrom; // fetch only once, we can add it to user list, it will be skipped anyway
                                }
                                else{
                                    mtrace('Could not find user '.$post->userid);
                                    continue;
                                }
                                
                                // JF 2011/10/25
                                if (!isset($userfrom->groups[$post->id])) {
                                    if (!isset($userfrom->groups)) {
                                        $userfrom->groups = array();
                                        $users[$userfrom->id]->groups = array();
                                    }
                                    $userfrom->groups[$post->id] = groups_get_all_groups($course->id, $userfrom->id, $cm->groupingid);
                                    $users[$userfrom->id]->groups[$post->id] = $userfrom->groups[$post->id];
                                }
                                $groupname='';
                                if (!empty($userfrom->groups)){
                                    // mtrace("\nDEBUG : lib.php :: 285 :: \n");
                                    // print_r($userfrom->groups);
                                    foreach($userfrom->groups as $un_groupe){
                                        if (!empty($un_groupe)){
                                            // mtrace("\nDEBUG : lib_cron.php :: 288 \n");
                                            // print_r($un_groupe);
                                            foreach($un_groupe as $groupe){
                                                // mtrace("\nDEBUG : lib_cron.php :: 291 \n");
                                                // print_r($groupe);

                                                if (!empty($groupe->name)){
                                                    $groupname.= $groupe->name. ' ';
                                                }
                                            }
                                        }
                                    }
                                    // mtrace("\nDEBUG : lib_cron.php :: 299 :: $groupname \n");
                                }

                                $userfrom->customheaders = array ("Precedence: Bulk");

                                if ($userto->maildigest == 2) {
                                    // Subjects only
                                    $by = new object();
                                    $by->name = fullname($userfrom);
                                    $by->date = userdate($post->date_modif);
                                    if (!empty($groupname)){
                                        $posttext .= "\n".format_string($post->subject,true).' '.get_string("bynameondate", "referentiel", $by). ' - '.get_string('groupe', 'referentiel', $groupname);
                                    }
                                    else{
                                        $posttext .= "\n".format_string($post->subject,true).' '.get_string("bynameondate", "referentiel", $by);
                                    }
                                    $posttext .= "\n---------------------------------------------------------------------";

                                    $by->name = "<a target=\"_blank\" href=\"$CFG->wwwroot/user/view.php?id=$userfrom->id&amp;course=$course->id\">$by->name</a>";
                                    $posthtml .= '<div><a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$post->ref_instance.'&activite_id='.$post->id.'">'.format_string($post->type_activite,true).'</a> '.get_string("bynameondate", "referentiel", $by).'</div>';
                                }
                                else {
                                    // The full treatment
							        $context = context_module::instance($cm->id);
                                    $posttext = referentiel_make_mail_text(TYPE_ACTIVITE, $context, $course, $post, $userfrom, $userto, true);
                                    $posthtml = referentiel_make_mail_post(TYPE_ACTIVITE, $context, $course, $post, $userfrom, $userto, false, true, false);
                                }
                            }
                        }
                    }
                    $posthtml .= '<hr size="1" noshade="noshade" /></p>';
                }
                $posthtml .= '</body>';

                if ($userto->mailformat != 1) {
                        // This user DOESN'T want to receive HTML
                        $posthtml = '';
                }

                if (!$mailresult =  email_to_user($userto, $site->shortname, $postsubject, $posttext, $posthtml,
                                                  '', '', $CFG->forum_replytouser)) {
                        mtrace("ERROR!: Could not send out referentiel activity digest mail to user $userto->id ($userto->email)... not trying again.");
                        // add_to_log($course->id, 'referentiel', 'mail digest error', '', '', $cm->id, $userto->id);
                }
                else if ($mailresult === 'emailstop') {
                        // should not happen anymore - see check above
                }
                else {
                        mtrace("success.");
                        $usermailcount++;
                }
            }
        }
        /// We have finishied all digest emails activities, update $CFG->digestactivitytimelast
        set_config('digestactivitytimelast', $timenow);
    }

    $USER = $cronuser;
    // course_setup(SITEID); // reset cron user language, theme and timezone settings
    cron_setup_user();
    
    if (!empty($usermailcount)) {
            mtrace(get_string('digestsentusers', 'referentiel', $usermailcount));
    }

    mtrace("FIN CRON REFERENTIEL ACTIVITE.\n");
    return true;

}

// -----------------------------------------
function referentiel_cron_taches(){
global $CFG, $USER;
global $DB;
    $cronuser = clone($USER);
    $site = get_site();

    // all users that are subscribed to any post that needs sending
    $users = array();

    // status arrays
    $mailcount  = array();
    $errorcount = array();

    // caches
    $tasks          = array();
    $courses         = array();
    $coursemodules   = array();
    $subscribedusers = array();

    // Posts older than 2 days will not be mailed.  This is to avoid the problem where
    // cron has not been running for a long time, and then suddenly people are flooded
    // with mail from the past few weeks or months
    $timenow   = time();
    if (NOTIFICATION_DELAI){
        $endtime   = $timenow - $CFG->maxeditingtime;
    }
    else{
        $endtime   = $timenow;
    }


    $starttime = $endtime - NOTIFICATION_INTERVALLE_JOUR * 24 * 3600;   // Two days earlier

// JF
// DEBUG : cron_lib.php :
    mtrace("DEBUT CRON REFERENTIEL TACHES");


    $taches = referentiel_get_unmailed_tasks($starttime, $endtime);
    if ($taches) {

		if (REFERENTIEL_DEBUG){
			mtrace("\nlib_cron.php :: 1675 :: TACHES\n");
			print_r($taches);
			mtrace("\n");
		}

        // Mark them all now as being mailed.  It's unlikely but possible there
        // might be an error later so that a post is NOT actually mailed out,
        // but since mail isn't crucial, we can accept this risk.  Doing it now
        // prevents the risk of duplicated mails, which is a worse problem.

        if (!referentiel_mark_old_tasks_as_mailed($endtime)) {
            mtrace('Errors occurred while trying to mark some referentiel tasks as being mailed.');
            return false;  // Don't continue trying to mail them, in case we are in a cron loop
        }
        // checking task validity, and adding users to loop through later
        foreach ($taches as $tid => $task) {
            // DEBUG : cron_lib.php :
            // mtrace('task '.$task->id.' '.$tid);
			if (REFERENTIEL_DEBUG){
				mtrace("\nlib_cron.php :: 1694 :: TASK\n");
				print_r($task);
				mtrace("\n");
			}

            if (!isset($tasks[$tid])) {
                $tasks[$tid] = $task;
            }
            // cours
            $courseid = $tasks[$tid]->ref_course;
            if (!isset($courses[$courseid])) {
                if ($course = $DB->get_record("course", array("id" => "$courseid"))) {
                    $courses[$courseid] = $course;
                }
                else {
                    mtrace('Could not find course '.$courseid);
                    unset($tasks[$tid]);
                    continue;
                }
            }
            // modules
            $instanceid = $task->ref_instance;
            if (!isset($coursemodules[$instanceid])) {
                if ($cm = get_coursemodule_from_instance('referentiel', $instanceid, $courseid)) {
                    $coursemodules[$instanceid] = $cm;
                }
                else {
                    mtrace('./mod/referentiel/lib.php : 1355 :  Could not load course module for referentiel instance '.$instanceid);
                    unset($tasks[$tid]);
                    continue;
                }
            }


            // caching subscribed students of each task

            $students=array();
            $teachers=referentiel_get_teachers_course($courseid);

// DEBUG : cron_lib.php :

            if (!isset($subscribedusers[$tid])) {
                // notifier tous les etudiants
                // ICI MODIFIER LA COLLECTE
                // NE PRENDRE QUE LES ETUDIANTS ACCOMPAGNES
                // $students=referentiel_get_students_accompagne($courseid,$task->auteurid);
                $students= referentiel_get_accompagnements_teacher($task->ref_instance, $task->ref_course, $task->auteurid);
                if (empty($students)){
                    $students=referentiel_get_students_course($courseid,0,0,false);
                }
                if ($students){
                  foreach ($students as $student) {
					$user = referentiel_get_user($student->userid);
                    if ($user->emailstop) {
                      if (!empty($CFG->forum_logblocked)) {
                        // add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $user->id);
                      }
                    }
                    else{
						// this user is subscribed to this notification
						$subscribedusers[$tid][$student->userid]=$student->userid;
						// this user is a user we have to process later
						$users[$student->userid] = $user;
						if (REFERENTIEL_DEBUG){
							mtrace("\nDEBUG :: lib_cron:: 1758 :: \nDESTINATAIRE ETUDIANT ID: ".$user->id."\n");
							//print_r($user);
						}
                    }
                  }
                }
                unset($students); // release memory

                if (NOTIFICATION_TACHES_AUX_REFERENTS){ //
                    if ($teachers) {
                        foreach ($teachers as $teacher) {
                            $user=referentiel_get_user($teacher->userid);
                            if ($user->emailstop) {
                                if (!empty($CFG->forum_logblocked)) {
                                    // add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $teacher->userid);
                                }
                            }
                            else{
                               // this user is subscribed to this notification
                                $subscribedusers[$tid][$user->id]=$user->id;
                                // this user is a user we have to process later
                                $users[$user->id] = $user;
                                // DEBUG : cron_lib.php :
                                if (REFERENTIEL_DEBUG){
									mtrace("\nDEBUG :: lib_cron:: 1782 :: \nDESTINATAIRE TEACHER: ".$user->id." \n");
									//print_r($user);
								}		
                            }
                        }
                    }
                }

                unset($teachers); // release memory
                $mailcount[$tid] = 0;
                $errorcount[$tid] = 0;
            }

        }
    }

    if ($users && $tasks) {
// DEBUG : cron_lib.php :
if (REFERENTIEL_DEBUG){
	mtrace("TRAITEMENT DES MESSAGES TACHES\n");
}	
        $urlinfo = parse_url($CFG->wwwroot);
        $hostname = $urlinfo['host'];

        foreach ($users as $userto) {

            @set_time_limit(TIME_LIMIT); // terminate if processing of any account takes longer than 2 minutes

            // set this so that the capabilities are cached, and environment matches receiving user
            $USER = $userto;

            // mtrace('./mod/referentiel/lib.php :: Line 253 : Processing user '.$userto->id);

            // init caches
            $userto->viewfullnames = array();
			// $userto->canpost       = array();
            // $userto->markposts     = array();
            $userto->enrolledin    = array();

            // reset the caches
            foreach ($coursemodules as $coursemoduleid => $unused) {
                $coursemodules[$coursemoduleid]->cache       = new object();
                $coursemodules[$coursemoduleid]->cache->caps = array();
                unset($coursemodules[$coursemoduleid]->uservisible);
            }

            foreach ($tasks as $tid => $task) {

                // Set up the environment for activity, course
                $course     = $courses[$task->ref_course];
                $cm         =& $coursemodules[$task->ref_instance];

                // Do some checks  to see if we can mail out now
                if (!isset($subscribedusers[$tid][$userto->id])) {
                    continue; // user does not subscribe to this activity
                }

                // Get info about the author user
                if (array_key_exists($task->auteurid, $users)) { // we might know him/her already but we need a really complete user object, so.. get it again!
                	if ($userfrom = $DB->get_record("user", array("id" => "$task->auteurid"))){
                    	$users[$task->auteurid] = $userfrom;
					}
					else{
						// error
                    	mtrace('Could not find user '.$task->auteurid);
                    	continue;
					}
                }
				else if ($userfrom = $DB->get_record("user", array("id" => "$task->auteurid"))) {
                    $users[$userfrom->id] = $userfrom; // fetch only once, we can add it to user list, it will be skipped anyway
                }
                else {
                    mtrace('Could not find user '.$task->auteurid);
                    continue;
                }

                // setup global $COURSE properly - needed for roles and languages
                // course_setup($course);   // More environment
                // setup global $COURSE properly - needed for roles and languages
                cron_setup_user($userto, $course);

                // Fill caches
                if (!isset($userto->viewfullnames[$tid])) {
			        $modcontext = context_module::instance($cm->id);
                    $userto->viewfullnames[$tid] = has_capability('moodle/site:viewfullnames', $modcontext);
                }
                if (!isset($userfrom->groups[$tid])) {
                    if (!isset($userfrom->groups)) {
                        $userfrom->groups = array();
                        $users[$userfrom->id]->groups = array();
                    }
                    $userfrom->groups[$tid] = groups_get_all_groups($course->id, $userfrom->id, $cm->groupingid);
                    $users[$userfrom->id]->groups[$tid] = $userfrom->groups[$tid];
                }

                // OK so we need to send the email.

                // Does the user want this post in a digest?  If so postpone it for now.
                if ($userto->maildigest > 0) {
                    $queue = new object();
                    $queue->userid       = $userto->id;
                    $queue->activiteid   = $task->id;
                    $queue->timemodified = $task->date_modif;
                    $queue->type = TYPE_TACHE; // 1
                    if (!$DB->insert_record('referentiel_notification', $queue)) {
                        mtrace("Error: mod/referentiel/lib.php : Line 1510 : Could not queue for digest mail for id $task->id to user $userto->id ($userto->email) .. not trying again.");
                    }
                    continue;
                }


                // Prepare to actually send the post now, and build up the content
                $strreferentielname=get_string('referentiel','referentiel').': '.referentiel_get_instance_name($task->ref_instance);
                $cleanactivityname = str_replace('"', "'", strip_tags(format_string($strreferentielname.' -> '.$task->type_task)));

                $userfrom->customheaders = array (  // Headers to make emails easier to track
                           'Precedence: Bulk',
                           'List-Id: "'.$cleanactivityname.'" <moodle_referentiel_activity_'.$task->id.'@'.$hostname.'>',
                           'List-Help: '.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$task->ref_instance.'&task_id='.$task->id.'&amp;mode=listtaskall',
                           'Message-ID: <moodle_referentiel_task_'.$task->id.'@'.$hostname.'>',
                           'X-Course-Id: '.$course->id,
                           'X-Course-Name: '.format_string($course->fullname, true)
                );


                if (!$cm = get_coursemodule_from_instance('referentiel', $task->ref_instance, $course->id)) {
                  print_error('Course Module ID was incorrect');
                }

                $postsubject = "$course->shortname: ".format_string($strreferentielname.' '.$task->type_task,true);
		        $context = context_module::instance($cm->id);
                $posttext = referentiel_make_mail_text(TYPE_TACHE, $context, $course, $task, $userfrom, $userto);
                $posthtml = referentiel_make_mail_html(TYPE_TACHE, $context, $course, $task, $userfrom, $userto);

                // Send the post now!
                // mtrace('Sending ', '');

                if (!$mailresult = email_to_user($userto, $userfrom, $postsubject, $posttext,
                                                 $posthtml, '', '', $CFG->forum_replytouser)) {
                    mtrace("Error: Could not send out mail for id $task->id to user $userto->id".
                         " ($userto->email) .. not trying again.");
                    // add_to_log($course->id, 'referentiel', 'mail error', "task $task->id to $userto->id ($userto->email)","", $cm->id, $userto->id);
                    $errorcount[$tid]++;
                } else if ($mailresult === 'emailstop') {
                    // should not be reached anymore - see check above
                }
                else {
                    $mailcount[$tid]++;
                }
            }
        }
    }

    if ($tasks) {
        foreach ($tasks as $task) {
            mtrace($mailcount[$task->id]." users were sent task $task->id, $task->type_task");
            if ($errorcount[$task->id]) {
                $DB->set_field("referentiel_task", "mailed", "2", array("id" => "$task->id"));
            }
        }
    }

    // release some memory
    unset($subscribedusers);
    unset($mailcount);
    unset($errorcount);

    $USER = clone($cronuser);
    // course_setup(SITEID);
    cron_setup_user();
    
    $sitetimezone = $CFG->timezone;

    // Now see if there are any digest mails waiting to be sent, and if we should send them

    mtrace('Starting digest processing...');

    @set_time_limit(TIME_LIMIT*2); // terminate if not able to fetch all digests in 5 minutes

    if (!isset($CFG->digesttasktimelast)) {    // To catch the first time
        set_config('digesttasktimelast', 0);
    }

    $timenow = time();
    $digesttime = usergetmidnight($timenow, $sitetimezone) + ($CFG->digestmailtime * 3600);

    // Delete any really old ones (normally there shouldn't be any)
    $weekago = $timenow - (7 * 24 * 3600);
    $DB->delete_records_select('referentiel_notification', "timemodified < $weekago AND type='".TYPE_TACHE."'", NULL);
    mtrace('Cleaned old digest records');

    if ($CFG->digesttasktimelast < $digesttime and $timenow > $digesttime) {

        mtrace("Sending task digests: ".userdate($timenow, '', $sitetimezone));

        $params= array("digesttime" => "$digesttime", "type" => TYPE_TACHE);
        $sql= " (timemodified < :digesttime) AND (type=:type) ";
        $digestposts_rs = $DB->get_recordset_select('referentiel_notification',$sql, $params);

        // if (!rs_EOF($digestposts_rs)) {     // deprecated
        if ($digestposts_rs->valid()) {
            // We have work to do
            $usermailcount = 0;

            //caches - reuse the those filled before too
            $userposts = array();
//             while ($digestpost = rs_fetch_next_record($digestposts_rs)) {
            foreach ($digestposts_rs as $digestpost) {
                if (!isset($users[$digestpost->userid])) {
                    if ($user = $DB->get_record("user", array("id" => "$digestpost->userid"))) {
                        $users[$digestpost->userid] = $user;
                    }
                    else {
                        continue;
                    }
                }
                $postuser = $users[$digestpost->userid];
                if ($postuser->emailstop) {
                    if (!empty($CFG->forum_logblocked)) {
                        // add_to_log(SITEID, 'referentiel', 'mail blocked', '', '', 0, $postuser->id);
                    }
                    continue;
                }

                // contenu activite
                  if (!isset($taches[$digestpost->activiteid])) {
                    if ($tache = $DB->get_record("referentiel_task", array("id" => "$digestpost->activiteid"))) {
                        $taches[$digestpost->activiteid] = $tache;
                    }
                    else {
                        continue;
                    }
                  }
                  $courseid = $taches[$digestpost->activiteid]->ref_course;
                  if (!isset($courses[$courseid])) {
                    if ($course = $DB->get_record("course", array("id" => "$courseid"))) {
                        $courses[$courseid] = $course;
                    }
                    else {
                        continue;
                    }
                  }
                  if (!isset($coursemodules[$taches[$digestpost->activiteid]->ref_instance]) && $taches[$digestpost->activiteid]) {
                    if ($cm = get_coursemodule_from_instance('referentiel', $taches[$digestpost->activiteid]->ref_instance, $courseid)) {
                        $coursemodules[$taches[$digestpost->activiteid]->ref_instance] = $cm;
                    }
                    else {
                        continue;
                    }
                  }
                $userposts[$digestpost->userid][$digestpost->activiteid] = $digestpost->activiteid;
            }

            $digestposts_rs->close(); /// Finished iteration, let's close the resultset

            // Data collected, start sending out emails to each user
            // foreach ($userdiscussions as $userid => $thesediscussions) {
            foreach ($userposts as $userid => $theseactivities) {
                @set_time_limit(TIME_LIMIT); // terminate if processing of any account takes longer than 2 minutes

                $USER = $cronuser;
                // course_setup(SITEID); // reset cron user language, theme and timezone settings
                cron_setup_user();
                
                mtrace(get_string('processingdigest', 'referentiel', $userid), '... ');

                // First of all delete all the queue entries for this user
                $DB->delete_records_select('referentiel_notification', "userid = $userid AND timemodified < $digesttime AND type='".TYPE_TACHE."'", NULL);
                $userto = $users[$userid];


                // Override the language and timezone of the "current" user, so that
                // mail is customised for the receiver.
                $USER = $userto;
                // course_setup(SITEID);
                cron_setup_user();
                
                // init caches
                $userto->viewfullnames = array();
                // $userto->canpost       = array();
                // $userto->markposts     = array();

                $postsubject = get_string('digestmailsubject', 'referentiel', format_string($site->shortname, true));

                $headerdata = new object();
                $headerdata->sitename = format_string($site->fullname, true);
                $headerdata->userprefs = $CFG->wwwroot.'/user/edit.php?id='.$userid.'&amp;course='.$site->id;

                $posttext = get_string('digestmailheader', 'referentiel', $headerdata)."\n\n";
                $headerdata->userprefs = '<a target="_blank" href="'.$headerdata->userprefs.'">'.get_string('digestmailprefs', 'referentiel').'</a>';

                $posthtml = "<head>";
/*
                foreach ($CFG->stylesheets as $stylesheet) {
                    $posthtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
                }
*/
                $posthtml .= "</head>\n<body id=\"email\">\n";
                $posthtml .= '<p>'.get_string('digestmailheader', 'referentiel', $headerdata).'</p><br /><hr size="1" noshade="noshade" />';


                foreach ($theseactivities as $tid) {

                    @set_time_limit(TIME_LIMIT);   // to be reset for each post
                    $type_notification=TYPE_TACHE;

                    $tache = $taches[$tid];
                    $course     = $courses[$taches[$tid]->ref_course];
                    $cm         = $coursemodules[$taches[$tid]->ref_instance];

                    //override language

                    // course_setup($course);
                    // setup global $COURSE properly - needed for roles and languages
                    cron_setup_user($userto, $course);


                    // Fill caches
                    if (!isset($userto->viewfullnames[$tache->id])) {
	 			        $modcontext = context_module::instance($cm->id);
                        $userto->viewfullnames[$tache->id] = has_capability('moodle/site:viewfullnames', $modcontext);
                    }

                    $strreferentiels      = get_string('referentiels', 'referentiel');

                    $posttext .= "\n \n";
                    $posttext .= '=====================================================================';
                    $posttext .= "\n \n";
                    $posttext .= "$course->shortname -> $strreferentiels -> ".format_string($tache->type_task,true);
                    $posttext .= "\n";

                    $posthtml .= "<p><font face=\"sans-serif\">".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> ".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/referentiel/index.php?id=$course->id\">$strreferentiels</a> -> ".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/referentiel/tache.php?id=$tache->ref_instance&activite_id=$tache->id\">".format_string($tache->type_task,true)."</a>";
                    $posthtml .= "</font></p>";
                    $posthtml .= '<p>';
                    //$postsarray = $userposts[$tache->id];
                    $postsarray = $userposts[$userid];
                    sort($postsarray);

                    foreach ($postsarray as $activiteid) {
                        $post = $taches[$activiteid];

                        if (array_key_exists($post->auteurid, $users)) { // we might know him/her already
                            $userfrom = $users[$post->auteurid];
                        } else if ($userfrom = $DB->get_record("user", array("id" => "$post->auteurid"))) {
                            $users[$userfrom->id] = $userfrom; // fetch only once, we can add it to user list, it will be skipped anyway
                        }
                        else {
                            mtrace('Could not find user '.$post->auteurid);
                            continue;
                        }

                        if (!isset($userfrom->groups[$post->id])) {
                            if (!isset($userfrom->groups)) {
                                $userfrom->groups = array();
                                $users[$userfrom->id]->groups = array();
                            }
                            $userfrom->groups[$post->id] = groups_get_all_groups($course->id, $userfrom->id, $cm->groupingid);
                            $users[$userfrom->id]->groups[$post->id] = $userfrom->groups[$post->id];
                        }

                        $userfrom->customheaders = array ("Precedence: Bulk");

                        if ($userto->maildigest == 2) {
                            // Subjects only
                            $by = new object();
                            $by->name = fullname($userfrom);
                            $by->date = userdate($post->date_modif);
                            $posttext .= "\n".format_string($post->type_task,true).' '.get_string("bynameondate", "referentiel", $by);
                            $posttext .= "\n---------------------------------------------------------------------";

                            $by->name = "<a target=\"_blank\" href=\"$CFG->wwwroot/user/view.php?id=$userfrom->id&amp;course=$course->id\">$by->name</a>";
                            $posthtml .= '<div><a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$post->ref_instance.'&activite_id='.$post->id.'">'.format_string($post->type_activite,true).'</a> '.get_string("bynameondate", "referentiel", $by).'</div>';
                        }
                        else {
					        $context = context_module::instance($cm->id);
                            $posttext = referentiel_make_mail_text(TYPE_TACHE, $context, $course, $post, $userfrom, $userto, true);
                            $posthtml = referentiel_make_mail_post(TYPE_TACHE, $context, $course, $post, $userfrom, $userto, false, true, false);
                        }
                    }
                    $posthtml .= '<hr size="1" noshade="noshade" /></p>';
                }
                $posthtml .= '</body>';

                if ($userto->mailformat != 1) {
                    // This user DOESN'T want to receive HTML
                    $posthtml = '';
                }

                if (!$mailresult =  email_to_user($userto, $site->shortname, $postsubject, $posttext, $posthtml,
                                                  '', '', $CFG->forum_replytouser)) {
                    mtrace("ERROR!");
                    mtrace("Error: Could not send out digest mail to user $userto->id ($userto->email)... not trying again.\n");
                    // add_to_log($course->id, 'referentiel', 'mail digest error', '', '', $cm->id, $userto->id);
                } else if ($mailresult === 'emailstop') {
                    // should not happen anymore - see check above
                }
                else {
                    mtrace("success.");
                    $usermailcount++;
                }
            }
        }
    /// We have finishied all digest emails activities, update $CFG->digestactivitytimelast
        set_config('digesttasktimelast', $timenow);
    }

    $USER = $cronuser;
    // course_setup(SITEID); // reset cron user language, theme and timezone settings
    cron_setup_user();
    
    if (!empty($usermailcount)) {
        mtrace(get_string('digestsentusers', 'referentiel', $usermailcount));
    }
    mtrace("FIN CRON REFERENTIEL TACHE.\n");
    return true;
}




/**
 * Builds and returns the body of the email notification in html format.
 *
 * @param object $course
 * @param object $forum
 * @param object $discussion
 * @param object $post
 * @param object $userfrom
 * @param object $userto
 * @return string The email text in HTML format
 */
function referentiel_make_mail_html($type, $context, $course, $post, $userfrom, $userto) {
  global $CFG;
  $site=get_site();
/*
if (REFERENTIEL_DEBUG){
	mtrace("DEBUG : cron_lib.php : 2267 : referentiel_make_mail_html \n TYPE: $type\n");
    mtrace("CONTEXT\n");
	print_r($context);
mtrace("COUSE\n");
	print_r($course);
mtrace("POST\n");
	print_r($post);
mtrace("USERFROM\n");
	print_r($userfrom);
mtrace("USERTO\n");
	print_r($userto);
}
*/
  if ($userto->mailformat != 1) {  // Needs to be HTML
        return '';
  }

  $posthtml = '<head>';
/*
  foreach ($CFG->stylesheets as $stylesheet) {
    $posthtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
  }
*/
  if ($type==TYPE_CERTIFICAT){
    $strreferentiel = get_string('certificat','referentiel'). ' '. referentiel_get_referentiel_name($post->ref_referentiel);
    $posthtml .= '</head>';
    $posthtml .= "\n<body id=\"email\">\n\n";
    $posthtml .= '<div class="navbar">'.
    '<a target="_blank" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a> &raquo; '.$strreferentiel;
    $posthtml .= '</div>';
    $posthtml .= referentiel_make_mail_post($type, $context, $course, $post, $userfrom, $userto, false, true, false);
  }
  else if ($type==TYPE_TACHE){
    $strreferentiel = referentiel_get_referentiel_name($post->ref_referentiel).' ('.referentiel_get_instance_name($post->ref_instance).') ';
    $posthtml .= '</head>';
    $posthtml .= "\n<body id=\"email\">\n\n";
    $posthtml .= '<div class="navbar">'.
    '<a target="_blank" href="'.$CFG->wwwroot.'/course/view.php?id='.$site->id.'">'.$site->shortname.'</a> &raquo; '.
    '<a target="_blank" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a> &raquo; '.
    '<a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/view.php?d='.$post->ref_instance.'&amp;non_redirection=1">'.$strreferentiel.'</a> &raquo; '.
    '<a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$post->ref_instance.'">'.get_string('tasks','referentiel').'</a>';
    $posthtml .= '</div>';
    $posthtml .= referentiel_make_mail_post($type, $context, $course, $post, $userfrom, $userto, false, true, false);
  }
  else{
    $strreferentiel = referentiel_get_referentiel_name($post->ref_referentiel).' ('.referentiel_get_instance_name($post->ref_instance).') ';
    $posthtml .= '</head>';
    $posthtml .= "\n<body id=\"email\">\n\n";
    $posthtml .= '<div class="navbar">'.
    '<a target="_blank" href="'.$CFG->wwwroot.'/course/view.php?id='.$site->id.'">'.$site->shortname.'</a> &raquo; '.
    '<a target="_blank" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a> &raquo; '.
    '<a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/view.php?d='.$post->ref_instance.'&amp;non_redirection=1">'.$strreferentiel.'</a> &raquo; '.
    '<a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$post->ref_instance.'">'.get_string('activites','referentiel').'</a>';
    $posthtml .= '</div>';
    $posthtml .= referentiel_make_mail_post($type, $context, $course, $post, $userfrom, $userto, false, true, false);
  }
  $posthtml .= '</body>';
  return $posthtml;
}



/**
* Given the data about a posting, builds up the HTML to display it and
* returns the HTML in a string.  This is designed for sending via HTML email.
*/
function referentiel_make_mail_post($type, $context, $course, $post, $userfrom, $userto,
                              $ownpost=false, $link=false, $rate=false, $footer="") {

    global $CFG;
    global $OUTPUT;

    $output='';

if (REFERENTIEL_DEBUG){
	mtrace("DEBUG : cron_lib.php : 2342 : referentiel_make_mail_post \n TYPE: ".$type."\n");
    mtrace("CONTEXT\n");
	print_r($context);
	mtrace("COURSE ID ".$course->id."\n");
	//print_r($course);
	mtrace("POST\n");
	print_r($post);
	mtrace("USERFROM ID : ".$userfrom->id."\n");
	//print_r($userfrom);
	mtrace("USERTO ID : ".$userto->id."\n");
	//print_r($userto);
}
if ($userfrom && $course){
    // JF 2011/10/25
    if (!isset($userfrom->viewfullnames[$post->id])) {
        $viewfullnamesfrom = has_capability('moodle/site:viewfullnames', $context, $userfrom->id);
    }
    else {
        $viewfullnamesfrom = $userfrom->viewfullnames[$post->id];
    }

    $fullnamefrom = fullname($userfrom, $viewfullnamesfrom);

    $by = new object();
    $by->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$userfrom->id.'&amp;course='.$course->id.'">'.$fullnamefrom.'</a>';

    if ($type==TYPE_CERTIFICAT){
        if ($post->date_decision){
            $by->date = userdate($post->date_decision, "", $userfrom->timezone);
        }
        else {
            $by->date = userdate(time(), "", $userfrom->timezone);
        }
    }
    elseif ($type==TYPE_TACHE){
        if ($post->date_modif){
            $by->date = userdate($post->date_modif, "", $userto->timezone);
        }
        else {
            $by->date = userdate($post->date_creation, "", $userto->timezone);
        }
    }
    else{
        if ($post->date_modif){
            $by->date = userdate($post->date_modif, "", $userto->timezone);
        }
        elseif ($post->date_modif_student){
            $by->date = userdate($post->date_modif_student, "", $userto->timezone);
        }
        else {
            $by->date = userdate($post->date_creation, "", $userto->timezone);
        }
    }

    // JF 2011/10/25
    // groupes
    if (isset($userfrom->groups)) {
        $groups = $userfrom->groups[$post->id];
    }
    else {
        $groups = groups_get_all_groups($course->id, $userfrom->id);
    }

    $groupname='';
    if ($groups) {
        // mtrace("\n lib.php :: 2038\n");
        // print_r($groups);
        foreach ($groups as $groupe){
            // mtrace("\n lib.php :: 2041\n");
            // print_r($groupe);
            if (!empty($groupe->name)){
                $groupname.=$groupe->name.' ';
            }
        }
    }

    $strbynameondate = get_string('bynameondate', 'referentiel', $by);
    if (!empty($groupname)){
        $strbynameondate .= ' - '.get_string('groupe', 'referentiel',$groupname);
    }

  if (!isset($userto->viewfullnames[$post->id])) {
        $viewfullnames = has_capability('moodle/site:viewfullnames', $context, $userto->id);
  }
  else {
        $viewfullnames = $userto->viewfullnames[$post->id];
  }

  // format the post body

  $strreferentiel = get_string('referentiel', 'referentiel').' '.referentiel_get_referentiel_name($post->ref_referentiel);

  $output = '<table border="0" cellpadding="3" cellspacing="0" class="forumpost">';

  $output .= '<tr class="header"><td width="35" valign="top" class="picture left">';

  if (!empty($userfrom->id)){
  	$output .=  $OUTPUT->user_picture($userfrom, array('courseid'=>$course->id, 'link' => true));
  }

  $output .= '</td>';
  $output .= '<td class="topic starter">';

  if ($type==TYPE_CERTIFICAT){

    $output .= '<div class="subject">'.format_string(get_string('certificat', 'referentiel')).'</div>';
    $output .= '<div class="author">'.$strbynameondate.'</div>';
    $output .= '</td></tr>';
    $output .= '<tr><td class="left side" valign="top">';

    if (isset($userfrom->groups)) {
        $groups = $userfrom->groups[$post->id];
    }
    else {
        $group = groups_get_all_groups($course->id, $userfrom->id);
    }

    if ($groups) {
        $output .= print_group_picture($groups, $course->id, false, true, true);
    }
    else {
        $output .= '&nbsp;';
    }

    $output .= '</td><td class="content">';

    $output .= "<hr>\n";
    $output .= trusttext_strip("<b>".get_string('certificat','referentiel')."</b> </i>".$post->id."</i>");

    $output .= "<br />";

    $la_liste_competences=referentiel_digest_competences_certificat($post->competences_certificat, $post->ref_referentiel, true);
    $output .= trusttext_strip($la_liste_competences);
    $output .= "<br />\n";

    if ($post->decision_jury){
        $output.= trusttext_strip(get_string('decision_jury', 'referentiel').': '.$post->decision_jury);
        if ($post->date_decision){
          $output.= ' '.trusttext_strip(get_string('date_decision', 'referentiel').': '.userdate($post->date_decision));
        }
        $output .= "<br />\n";
    }

    if ($post->teacherid){
        $output.= trusttext_strip(get_string('referent', 'referentiel').': '.referentiel_get_user_info($post->teacherid));
        if ($post->verrou){
          $output.= ' '.trusttext_strip(get_string('verrou', 'referentiel'));
        }
        $output .= "<br />\n";
    }

    if ($post->commentaire_certificat) {
        $output.= trusttext_strip(get_string('commentaire', 'referentiel').': '.$post->commentaire_certificat);
        $output .= "<br />\n";
    }
    if ($post->synthese_certificat) {
        $output.= trusttext_strip(get_string('synthese_certificat', 'referentiel').': '.$post->synthese_certificat);
        $output .= "<br />\n";
    }

    $output .= "<hr>\n";

    // Context link to post if required
    if ($link) {
            $output .= '<div class="link">';
            $output .= '<a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/certificat.php?d='.$post->ref_instance.'&amp;certificat_id='.$post->id.'">'.
                     get_string('postincontext', 'referentiel').'</a>';
            $output .= '</div>';
    }

  }
  elseif ($type==TYPE_TACHE){
    if (isset($post->auteurid) && ($post->auteurid)){
      $auteur_info=referentiel_get_user_info($post->auteurid);
    }
    else {
      $auteur_info=get_string('un_enseignant','referentiel');
    }

    $output .= '<div class="subject">'.format_string($post->type_task).'</div>';

    $output .= '<div class="author">'.$strbynameondate.'</div>';
    $output .= '</td></tr>';
    $output .= '<tr><td class="left side" valign="top">';

    if (isset($userfrom->groups)) {
        $groups = $userfrom->groups[$post->id];
    }
    else {
        if (!$cm = get_coursemodule_from_instance('referentiel', $post->ref_instance, $course->id)) {
            print_error('Course Module ID was incorrect');
        }
        $group = groups_get_all_groups($course->id, $userfrom->id);
    }

    if ($groups) {
        $output .= print_group_picture($groups, $course->id, false, true, true);
    }
    else {
        $output .= '&nbsp;';
    }

    $output .= '</td><td class="content">';
    /*
    // Plus tard proposer les documents attachÃ©s ?
    if ($post->attachment) {
        $post->course = $course->id;
        $output .= '<div class="attachments">';
        $output .= forum_print_attachments($post, 'html');
        $output .= "</div>";
    }
    */
    // $output .= $formattedtext;



    $output .= "<hr>\n";
    $output .= trusttext_strip("<b>".get_string('task','referentiel')."</b> </i>".$post->id."</i>");

    $output .= "<br />";
    $output .= trusttext_strip(get_string('description','referentiel').' : '.$post->description_task);
    $output .= "<br />";
    $output .= trusttext_strip(get_string('certificat_sel_activite_competences', 'referentiel').': '.$post->competences_task);
    $output .= "<br />\n";

    $output.= trusttext_strip(get_string('auteur', 'referentiel').': '.$auteur_info);
    $output .= "<br />\n";
    $output.= trusttext_strip(get_string('date_fin', 'referentiel').': '.userdate($post->date_fin));
    $output .= "<br />\n";


    if ($post->criteres_evaluation) {
        $output.= trusttext_strip(get_string('criteres_evaluation', 'referentiel').': '.$post->criteres_evaluation);
        $output .= "<br />\n";
    }

    if ($post->souscription_libre) {
        $output.= trusttext_strip(get_string('souscription_libre', 'referentiel'));
    }

    if (isset($post->cle_souscription) && ($post->cle_souscription!='')){
        $output.= ' '.trusttext_strip(get_string('obtenir_cle_souscription', 'referentiel', $auteur_info));
    }

    if (isset($post->tache_masquee) && ($post->tache_masquee!=0)){
        $output.= ' '.trusttext_strip(get_string('tache_masquee_num', 'referentiel', $post->id));
        $output .= "\n";
    }

    $output .= "<br />\n";
    $output .= "<hr>\n";


// Context link to post if required
    if ($link) {
        $output .= '<div class="link">';
//        $output .= '<a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$post->ref_instance.'&task_id='.$post->id.'&amp;userid='.$post->auteurid.'&amp;mode=listtasksingle">'.
          $output .= '<a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/task.php?d='.$post->ref_instance.'&task_id='.$post->id.'">'.
                     get_string('postincontext', 'referentiel').'</a>';
        $output .= '</div>';
    }

  }
  else {           // ACTIVITE
    $output .= '<div class="subject">'.format_string($post->type_activite).'</div>';

    $output .= '<div class="author">'.$strbynameondate.'</div>';
    $output .= '</td></tr>';
    $output .= '<tr><td class="left side" valign="top">';

    if (isset($userfrom->groups)) {
        $groups = $userfrom->groups[$post->id];
    }
    else {
        if (!$cm = get_coursemodule_from_instance('referentiel', $post->ref_instance, $course->id)) {
            print_error('Course Module ID was incorrect');
        }
        $group = groups_get_all_groups($course->id, $userfrom->id, $cm->groupingid);
    }

    if ($groups) {
        $output .= print_group_picture($groups, $course->id, false, true, true);
    }
    else {
        $output .= '&nbsp;';
    }

    $output .= '</td><td class="content">';
    /*
    // Plus tard proposer les documents attachÃ©s ?
    if ($post->attachment) {
        $post->course = $course->id;
        $output .= '<div class="attachments">';
        $output .= forum_print_attachments($post, 'html');
        $output .= "</div>";
    }
    */

    // $output .= $formattedtext;

    $output .= "<hr>\n";
    $output .= trusttext_strip('<b>'.get_string('activite','referentiel').'</b> </i>'.$post->id.'</i>');
    $output .= "<br />";
    $output .= trusttext_strip('<b>'.get_string('auteur', 'referentiel').'</b> ');
    $output .= trusttext_strip(referentiel_get_user_info($post->userid));
    $output .= "<br />";

    $output .= trusttext_strip('<b>'.get_string('type_activite','referentiel').'</b> : '.$post->type_activite);
    $output .= "<br />";
    $output .= trusttext_strip('<b>'.get_string('description','referentiel').'</b> : '.$post->description_activite);
    $output .= "<br />";
    $output .= trusttext_strip('<b>'.get_string('certificat_sel_activite_competences', 'referentiel').'</b> : '.$post->competences_activite);
    $output .= "<br />\n";

    if ($post->teacherid){
        $output.= trusttext_strip('<b>'.get_string('referent', 'referentiel').'</b> : '.referentiel_get_user_info($post->teacherid));
        if ($post->approved){
          $output .= ' '.trusttext_strip(get_string('approved', 'referentiel'));
        }
        $output .= "<br />\n";
    }

    if ($post->commentaire_activite) {
        $output.= trusttext_strip('<b>'.get_string('commentaire', 'referentiel').'</b> : '.$post->commentaire_activite);
        $output .= "<br />\n";
    }
    $output .= "<hr>\n";


// Context link to post if required
    if ($link) {
        $output .= '<div class="link">';
        $output .= '<a target="_blank" href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$post->ref_instance.'&activite_id='.$post->id.'">'.
                     get_string('postincontext', 'referentiel').'</a>';
        $output .= '</div>';
    }
  }


  if ($footer) {
        $output .= '<div class="footer">'.$footer.'</div>';
  }
  $output .= '</td></tr></table>'."\n\n";

  }
  if (REFERENTIEL_DEBUG){
	mtrace("\nOUTPUT: \n".$output."\n");
  }
  return $output;
}


/**
 * Builds and returns the body of the email notification in plain text.
 *
 * @param object $course
 * @param object $forum
 * @param object $discussion
 * @param object $post
 * @param object $userfrom
 * @param object $userto
 * @param boolean $bare
 * @return string The email body in plain text format.
 */

function referentiel_make_mail_text($type, $context, $course, $post, $userfrom, $userto, $bare = false) {
    global $CFG, $USER;
// DEBUG : cron_lib.php :
// mtrace("referentiel_make_mail_text()");

  if (!isset($userto->viewfullnames[$post->id])) {
        $viewfullnames = has_capability('moodle/site:viewfullnames', $context, $userto->id);
  }
  else {
        $viewfullnames = $userto->viewfullnames[$post->id];
  }

      // groups
    if (isset($userfrom->groups)) {
        $groups = $userfrom->groups[$post->id];
    }
    else {
        $groups = groups_get_all_groups($course->id, $userfrom->id, $cm->groupingid);
    }

    $groupname='';
    if ($groups) {
        // mtrace("\n lib.php :: 2038\n");
        // print_r($groups);
        foreach ($groups as $groupe){
            // mtrace("\n lib.php :: 2041\n");
            // print_r($groupe);
            if (!empty($groupe->name)){
                $groupname.=$groupe->name.' ';
            }
        }
    }

  $by = New stdClass;
  $by->name = fullname($userfrom, $viewfullnames);

  $a = New stdClass;
  $a->site=$course->shortname;

  $posttext = '';

  if ($type==TYPE_CERTIFICAT){
    if ($post->date_decision){
      $by->date = userdate($post->date_decision, "", $userto->timezone);
    }
    else {
      $by->date = userdate(time(), "", $userto->timezone);
    }

    $strbynameondate = get_string('bynameondate', 'referentiel', $by);
    if(!empty($groupname)){
        $strbynameondate .= ' - '.get_string('groupe', 'referentiel', $groupname);
    }

    $strreferentiel = get_string('certificat', 'referentiel').' '.referentiel_get_referentiel_name($post->ref_referentiel);

    if (!$bare) {
        $posttext  = "$course->shortname -> ";
        $posttext .= format_string($strreferentiel,true);
        $posttext .= " ($CFG->wwwroot/)";
    }
    $posttext .= "\n".$strbynameondate."\n";

    $posttext .= format_text_email(trusttext_strip(get_string('certificat', 'referentiel').' '.$post->id), FORMAT_PLAIN);
    $posttext .= "\n\n";
    $posttext .= format_text_email(trusttext_strip(get_string('certificat_sel_certificat_competences', 'referentiel')), FORMAT_PLAIN);
    $posttext .= "\n";
    $posttext .= format_text_email(trusttext_strip($post->competences_certificat), FORMAT_PLAIN);
    $posttext .= "\n\n";


    if ($post->commentaire_certificat) {
        $posttext .=format_text_email(trusttext_strip($post->commentaire_certificat), FORMAT_PLAIN);
        $posttext .= "\n\n";
    }
    if ($post->synthese_certificat) {
        $posttext .=format_text_email(trusttext_strip($post->synthese_certificat), FORMAT_PLAIN);
        $posttext .= "\n\n";
    }

    if (!$bare) {
        $posttext .= "---------------------------------------------------------------------\n";
        $a->type=get_string('certificat', 'referentiel');
        $posttext .= get_string("postmailinfo", "referentiel", $a)."\n";
        $posttext .= "$CFG->wwwroot/\n";
    }

  }
  else  if ($type==TYPE_TACHE){
    if (isset($post->auteurid) && ($post->auteurid)){
      $auteur_info=referentiel_get_user_info($post->auteurid);
    }
    else {
      $auteur_info=get_string('un_enseignant','referentiel');
    }
    if ($post->date_modif){
      $by->date = userdate($post->date_modif, "", $userto->timezone);
    }
    else {
      $by->date = userdate($post->date_creation, "", $userto->timezone);
    }

    $strbynameondate = get_string('bynameondate', 'referentiel', $by);
    if(!empty($groupname)){
            $strbynameondate .= ' - '.get_string('groupe', 'referentiel', $groupname);
    }

    $strreferentiel = get_string('referentiel', 'referentiel').': '.referentiel_get_instance_name($post->ref_referentiel);

    if (!$bare) {
        $posttext  = "$course->shortname -> $strreferentiel -> ".format_string($post->type_task,true);
    }
    $posttext .= "\n".$strbynameondate."\n";

    $posttext .= "\n---------------------------------------------------------------------\n";
    $posttext .= format_text_email(trusttext_strip(get_string('task', 'referentiel').' '.$post->id), FORMAT_PLAIN);
    $posttext .= "\n---------------------------------------------------------------------\n\n";
    $posttext .= format_string($post->type_task,true);
    if ($bare) {
        $posttext .= "(".get_string('postincontext', 'referentiel')." $CFG->wwwroot/mod/referentiel/task.php?d=$post->ref_instance&task_id=$post->id)";
    }
    $posttext .= "\n\n";
    $posttext .= format_text_email(trusttext_strip($post->description_task), FORMAT_PLAIN);
    $posttext .= "\n\n";

    $posttext .= format_text_email(trusttext_strip(get_string('certificat_sel_activite_competences', 'referentiel')), FORMAT_PLAIN);
    $posttext .= "\n";
    $posttext .= format_text_email(trusttext_strip($post->competences_task), FORMAT_PLAIN);
    $posttext .= "\n\n";

    if ($post->criteres_evaluation) {
        $posttext.= format_text_email(trusttext_strip(get_string('criteres_evaluation', 'referentiel').': '.$post->criteres_evaluation), FORMAT_PLAIN);
        $posttext .= "\n\n";
    }

    if ($post->souscription_libre) {
        $posttext .= format_text_email(trusttext_strip(get_string('souscription_libre', 'referentiel')), FORMAT_PLAIN);
        $posttext .= "\n";
    }

    if (isset($post->cle_souscription) && ($post->cle_souscription!='')){
          $posttext.= format_text_email(trusttext_strip(get_string('obtenir_cle_souscription', 'referentiel', $auteur_info)), FORMAT_PLAIN);
        $posttext .= "\n";
    }
    if (isset($post->tache_masquee) && ($post->tache_masquee!=0)){
        $posttext.= format_text_email(trusttext_strip(get_string('tache_masquee_num', 'referentiel', $post->id)), FORMAT_PLAIN);
        $posttext .= "\n";
    }

    $posttext .= "\n";
    if (!$bare) {
        $posttext .= "---------------------------------------------------------------------\n";
        $a->type=get_string('task', 'referentiel');
        $posttext .= get_string("postmailinfo", "referentiel",  $a)."\n";
        $posttext .= "$CFG->wwwroot/mod/referentiel/task.php?d=$post->ref_instance&task_id=$post->id\n";
    }
  }
  else
  {     // ACTIVITE
    if ($post->date_modif){
      $by->date = userdate($post->date_modif, "", $userto->timezone);
    }
    else if ($post->date_modif_student){
      $by->date = userdate($post->date_modif_student, "", $userto->timezone);
    }
    else {
      $by->date = userdate($post->date_creation, "", $userto->timezone);
    }
    $strbynameondate = get_string('bynameondate', 'referentiel', $by);
        if (!empty($groupname)){
            $strbynameondate .= ' - '.get_string('groupe', 'referentiel', $groupname);
        }

    $strreferentiel = get_string('referentiel', 'referentiel').': '.referentiel_get_instance_name($post->ref_referentiel);

    if (!$bare) {
        $posttext  = "$course->shortname -> $strreferentiel -> ".format_string($post->type_activite,true);
    }
    $posttext .= "\n".$strbynameondate."\n";
    $posttext .= "\n---------------------------------------------------------------------\n";
    $posttext .= format_text_email(trusttext_strip(get_string('activite', 'referentiel').' '.$post->id), FORMAT_PLAIN);
    $posttext .= "\n---------------------------------------------------------------------\n";
    $posttext .= format_string($post->type_activite,true);
    if ($bare) {
        $posttext .= "(".get_string('postincontext', 'referentiel')." $CFG->wwwroot/mod/referentiel/activite.php?d=$post->ref_instance&activite_id=$post->id)";
    }

    $posttext .= "\n";
    $posttext .= format_text_email(trusttext_strip(get_string('auteur', 'referentiel').' '), FORMAT_PLAIN);
    $posttext .= format_text_email(trusttext_strip(referentiel_get_user_info($post->userid)), FORMAT_PLAIN);
    $posttext .= "\n";

    $posttext .= format_text_email(trusttext_strip($post->type_activite), FORMAT_PLAIN);
    $posttext .= "\n";
    $posttext .= format_text_email(trusttext_strip($post->description_activite), FORMAT_PLAIN);
    $posttext .= "\n\n";

    $posttext .= format_text_email(trusttext_strip(get_string('certificat_sel_activite_competences', 'referentiel')), FORMAT_PLAIN);
    $posttext .= "\n";
    $posttext .= format_text_email(trusttext_strip($post->competences_activite), FORMAT_PLAIN);
    $posttext .= "\n\n";

    if ($post->commentaire_activite) {
        $posttext .=format_text_email(trusttext_strip($post->commentaire_activite), FORMAT_PLAIN);
        $posttext .= "\n\n";
    }
    if (!$bare) {
        $posttext .= "---------------------------------------------------------------------\n";
        $a->type=get_string('activite', 'referentiel');
        $posttext .= get_string("postmailinfo", "referentiel",  $a)."\n";
        $posttext .= "$CFG->wwwroot/mod/referentiel/activite.php?d=$post->ref_instance&activite_id=$post->id\n";
    }
  }

  return $posttext;
}


/**
 * Returns a list of all new activities that have not been mailed yet
 * @param int $starttime - activity created after this time
 * @param int $endtime - activity created before this time
 */
function referentiel_get_unmailed_activities($starttime, $endtime) {
// detournement du module forum
global $DB;
    $params = array("starttime" => "$starttime", "endtime" => "$endtime",
"starttime2" => "$starttime", "endtime2" => "$endtime",
"starttime3" => "$starttime", "endtime3" => "$endtime" );
    $sql="SELECT * FROM {referentiel_activite}
 WHERE ((mailed = '0') AND (mailnow = '1'))
 AND (
 ((date_creation >= :starttime) AND (date_creation < :endtime))
  OR ((date_modif >= :starttime2) AND (date_modif < :endtime2))
  OR ((date_modif_student >= :starttime3) AND (date_modif_student < :endtime3))
 )
 ORDER BY date_creation ASC, date_modif ASC, date_modif_student ASC ";

    // mtrace("DEBUG : cron_lib.php : : lib_cron.php : 2483 : SQL : $sql");
    return $DB->get_records_sql($sql, $params);
}

/**
 * Returns a list of all new tasks that have not been mailed yet
 * @param int $starttime - task created after this time
 * @param int $endtime - task created before this time
 */
function referentiel_get_unmailed_tasks($starttime, $endtime) {
// detournement du module forum
global $DB;
    /*
    // BUGUEE CAR trop de notifications
        $sql="SELECT a.* FROM {referentiel_task} a
 WHERE a.mailed = '0'
 AND a.date_modif >= '".$starttime."'
 AND (a.date_modif < '".$endtime."' OR a.mailnow = '1')
 ORDER BY a.date_modif ASC, a.date_creation ASC ";
    // mtrace("DEBUG : cron_lib.php : : cron_lib.php : 2167 : SQL : $sql");
    return $DB->get_records_sql($sql);
    */
    $params = array("starttime" => "$starttime", "endtime" => "$endtime", "mailed" => "0", "mailnow" => "1");
    $sql="SELECT * FROM {referentiel_task}
 WHERE  (mailed = :mailed) AND  (mailnow = :mailnow)
 AND (date_modif >= :starttime) AND (date_modif < :endtime)
 ORDER BY date_modif ASC, date_creation ASC ";

    // mtrace("DEBUG : cron_lib.php : : cron_lib.php : 2511 : SQL : $sql");
    return $DB->get_records_sql($sql, $params);
}

/**
 * Returns a list of all new activities that have not been mailed yet
 * @param int $starttime - activity created after this time
 * @param int $endtime - activity created before this time
 */
function referentiel_get_unmailed_certificats($starttime, $endtime) {
// detournement du module forum
global $DB;
    $params = array("starttime" => "$starttime", "endtime" => "$endtime", "mailed" => "0", "mailnow" => "1");
    $sql="SELECT * FROM {referentiel_certificat}
 WHERE ((mailed = :mailed) AND (mailnow = :mailnow))
 AND ((date_decision >= :starttime)  AND (date_decision < :endtime))
 ORDER BY date_decision ASC ";
    // mtrace("DEBUG : cron_lib.php : : cron_lib.php : 2528 : SQL : $sql");
    return $DB->get_records_sql($sql, $params);
}


/**
 * Marks posts before a certain time as being mailed already
 */
function referentiel_mark_old_activities_as_mailed($endtime) {
// detournement du module forum
global $DB;
    $params = array("zero" => "0", "endtime" => "$endtime",
    "zero1" => "0", "endtime1" => "$endtime",
    "zero2" => "0", "endtime2" => "$endtime",
    "mailnow" => "1", "mailed" => "0" );

    $sql="SELECT * FROM {referentiel_activite}
 WHERE (((date_modif != :zero) AND (date_modif < :endtime))
 OR ((date_creation != :zero1) AND (date_creation < :endtime1))
 OR ((date_modif_student != :zero2) AND (date_modif_student < :endtime2)))
 OR ((mailnow = :mailnow) AND (mailed = :mailed)) ";
        // mtrace("DEBUG : cron_lib.php : : cron_lib.php : 3549 : SQL : $sql");
    $records=$DB->get_records_sql($sql, $params);
    if ($records){
            foreach ($records as $record){
                $DB->set_field("referentiel_activite", "mailed", 1, array("id" => "$record->id"));
            }
    }
    return true;
}

/**
 * Marks posts before a certain time as being mailed already
 */
function referentiel_mark_old_tasks_as_mailed($endtime) {
// detournement du module forum
global $DB;
    $params = array("endtime" => "$endtime", "mailnow" => "1", "mailed" => "0");
    $sql="SELECT * FROM {referentiel_task}
     WHERE (date_modif < :endtime OR mailnow = :mailnow) AND mailed = :mailed";
    $records=$DB->get_records_sql($sql, $params);
    if ($records){
            foreach ($records as $record){
                $DB->set_field("referentiel_task", "mailed", 1, array("id" => "$record->id"));
            }
    }
    return true;
}

/**
 * Marks posts before a certain time as being mailed already
 */
function referentiel_mark_old_certificates_as_mailed($endtime) {
// detournement du module forum
    global $DB;
    $params = array("endtime" => "$endtime", "mailnow" => "1", "mailed" => "0");
    $sql="SELECT * FROM {referentiel_certificat}
 WHERE (date_decision < :endtime OR mailnow = :mailnow) AND mailed = :mailed";

    $records=$DB->get_records_sql($sql, $params);
    if ($records){
            foreach ($records as $record){
                $DB->set_field("referentiel_certificat", "mailed", "1", array("id" => "$record->id"));
            }
    }
    return true;
}


/**
 * Must return an instance name
 * @param int $referentielid ID of an instance of this module
 * @return string
 **/
function referentiel_get_instance_name($id){
global $DB;
	if (!empty($id)){
        $params = array("id" => "$id");
        $sql="SELECT name FROM {referentiel} WHERE id=:id";
        $un_referentiel=$DB->get_record_sql($sql, $params);
	    if (!empty($un_referentiel->name)){
            return $un_referentiel->name;
        }
    }
	else{
	   return '';
    }
}

/**
 * Must return an id
 * @param none
 * @return boolean
 **/
function referentiel_referentiel_exists(){
global $DB;
    return($DB->count_records('referentiel_referentiel'));
}

/**
 * Must return an instance name
 * @param int $referentielid ID of an instance of this module
 * @return string
 **/
function referentiel_get_referentiel_name($id){
global $DB;
	if (isset($id) && ($id>0)){
        $params = array("id" => "$id");
        $sql="SELECT name FROM {referentiel_referentiel} WHERE id=:id";
        $un_referentiel=$DB->get_record_sql($sql, $params);
	    if (!empty($un_referentiel->name)){
            return $un_referentiel->name;
        }
    }
    else{
		return '';
    }
}

// ----------------------------------------------------
function referentiel_digest_competences_certificat($liste, $referentiel_referentiel_id, $invalide=true){
// affiche les compétences en mode texte

$separateur1='/';
$separateur2=':';
$liste_empreintes= referentiel_get_liste_empreintes_competence($referentiel_referentiel_id);
// Affiche les codes competences en tenant compte de l'empreinte
// si detail = true les compétences non validees sont aussi affichees
    $t_empreinte=explode($separateur1, $liste_empreintes);
    $yes=get_string('yes');
    $no=get_string('no');
	$s=get_string('competences','referentiel')."<br />\n";

	$tc=array();

	$liste=referentiel_purge_dernier_separateur($liste, $separateur1);
	if (!empty($liste) && ($separateur1!="") && ($separateur2!="")){
			$tc = explode ($separateur1, $liste);
			// DEBUG : cron_lib.php :
			// echo "<br />CODE <br />\n";
			// print_r($tc);
			$i=0;
			while ($i<count($tc)){
				// CODE1:N1
				// DEBUG : cron_lib.php :
				// echo "<br />".$tc[$i]." <br />\n";
				// exit;
				if ($tc[$i]!=''){
					$tcc=explode($separateur2, $tc[$i]);
					// echo "<br />".$tc[$i]." <br />\n";
					// print_r($tcc);
					// exit;

					if (isset($tcc[1]) && ($tcc[1]>=$t_empreinte[$i])){
						$s.=$tcc[0].' ';
						if ($invalide==true){
              $s.=' '.$tcc[1].' [/'.$t_empreinte[$i].'] : ';
					    $s.=$yes.' ; ';
            }
          }
					elseif ($invalide==true){
						$s.=$tcc[0].' ';
						$s.=' '.$tcc[1].' [/'.$t_empreinte[$i].'] : ';
						$s.=$no.' ; ';
					}
				  $s.="<br /> \n";
        }
				$i++;
			}
		}
	return $s;
}



// ###################################  FIN CRON


?>

<?php

// Inspire de la gestion des evenements du forum

// A TERMINER


/**
 * This function gets run whenever user is enrolled into course
 *
 * @param object $cp
 * @return void
 */
function referentiel_user_enrolled($cp) {
global $CFG;
    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_course::instance($cp->courseid);
    //} else {
        //$context = context_course::instance($cp->courseid);
    //}

    referentiel_add_user_default_subscriptions($cp->userid, $context);
}


/**
 * This function gets run whenever user is unenrolled from course
 *
 * @param object $cp
 * @return void
 */
function referentiel_user_unenrolled($cp) {
global $CFG;
    if ($cp->lastenrol) {
        $context = context_course::instance($cp->courseid);
        referentiel_remove_user_subscriptions($cp->userid, $context);
        referentiel_remove_user_tracking($cp->userid, $context);
    }
}


/**
 * Add subscriptions for new users
 *
 * @global object
 * @uses CONTEXT_SYSTEM
 * @uses CONTEXT_COURSE
 * @uses CONTEXT_COURSECAT
 * @uses referentiel_INITIALSUBSCRIBE
 * @param int $userid
 * @param object $context
 * @return bool
 */
function referentiel_add_user_default_subscriptions($userid, $context) {
    global $DB;
    global $CFG;
    if (empty($context->contextlevel)) {
        return false;
    }

    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:   // For the whole site
             $rs = $DB->get_recordset('course',null,'','id');
             foreach ($rs as $course) {
     // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $subcontext = context_course::instance($course->id);
    //} else {
    //    $subcontext = context_course::instance($course->id);
    //}

                 referentiel_add_user_default_subscriptions($userid, $subcontext);
             }
             $rs->close();
             break;

        case CONTEXT_COURSECAT:   // For a whole category
             $rs = $DB->get_recordset('course', array('category' => $context->instanceid),'','id');
             foreach ($rs as $course) {
                 //$subcontext = context_course::instance($course->id);
     // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $subcontext = context_course::instance($course->id);
    //} else {
    //    $subcontext = context_course::instance($course->id);
    //}

                 referentiel_add_user_default_subscriptions($userid, $subcontext);
             }
             $rs->close();
             if ($categories = $DB->get_records('course_categories', array('parent' => $context->instanceid))) {
                 foreach ($categories as $category) {
					$subcontext = context_coursecat::instance($category->id);
                    referentiel_add_user_default_subscriptions($userid, $subcontext);
                 }
             }
             break;


        case CONTEXT_COURSE:   // For a whole course
             if (is_enrolled($context, $userid)) {
                if ($course = $DB->get_record('course', array('id' => $context->instanceid))) {
                     if ($referentiels = get_all_instances_in_course('referentiel', $course, $userid, false)) {
                         foreach ($referentiels as $referentiel) {
                            // Valable pour Moodle 2.1 et Moodle 2.2
                            //if ($CFG->version < 2011120100) {
                                $modcontext = context_module::instance($referentiel->coursemodule);
                            //} else {
                            //    $modcontext = context_module::instance($referentiel->coursemodule);
                            //}

                            if ($modcontext) {
                                 if (has_capability('mod/referentiel:write', $modcontext, $userid)) {
                                     referentiel_subscribe($userid, $referentiel->id);
                                 }
                            }
                         }
                     }
                 }
             }
             break;

        case CONTEXT_MODULE:   // Just one referentiel
            if (has_capability('mod/referentiel:write', $context, $userid)) {
                 if ($cm = get_coursemodule_from_id('referentiel', $context->instanceid)) {
                     if ($referentiel = $DB->get_record('referentiel', array('id' => $cm->instance))) {
                         if (has_capability('mod/referentiel:write', $context, $userid)) {
                             referentiel_subscribe($userid, $referentiel->id);
                         }
                     }
                 }
            }
            break;
    }

    return true;
}


/**
 * Remove subscriptions for a user in a context
 *
 * @global object
 * @global object
 * @uses CONTEXT_SYSTEM
 * @uses CONTEXT_COURSECAT
 * @uses CONTEXT_COURSE
 * @uses CONTEXT_MODULE
 * @param int $userid
 * @param object $context
 * @return bool
 */
function referentiel_remove_user_subscriptions($userid, $context) {

    global $CFG, $DB;

    if (empty($context->contextlevel)) {
        return false;
    }

    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:   // For the whole site
            // find all courses in which this user has a referentiel subscription
            if ($courses = $DB->get_records_sql("SELECT c.id
                                                  FROM {course} c,
                                                       {referentiel_subscriptions} fs,
                                                       {referentiel} f
                                                       WHERE c.id = f.course AND f.id = fs.referentiel AND fs.userid = ?
                                                       GROUP BY c.id", array($userid))) {

                foreach ($courses as $course) {
                    //$subcontext = context_course::instance($course->id);
     // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $subcontext = context_course::instance($course->id);
    //} else {
    //    $subcontext = context_course::instance($course->id);
    //}

                    referentiel_remove_user_subscriptions($userid, $subcontext);
                }
            }
            break;

        case CONTEXT_COURSECAT:   // For a whole category
             if ($courses = $DB->get_records('course', array('category' => $context->instanceid), '', 'id')) {
                 foreach ($courses as $course) {
                     //$subcontext = context_course::instance($course->id);
    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $subcontext = context_course::instance($course->id);
    //} else {
    //    $subcontext = context_course::instance($course->id);
    //}

                     referentiel_remove_user_subscriptions($userid, $subcontext);
                 }
             }
             if ($categories = $DB->get_records('course_categories', array('parent' => $context->instanceid), '', 'id')) {
                 foreach ($categories as $category) {
                     $subcontext = context_coursecat::instance($category->id);
                     referentiel_remove_user_subscriptions($userid, $subcontext);
                 }
             }
             break;

        case CONTEXT_COURSE:   // For a whole course
            if (!is_enrolled($context, $userid)) {
                 if ($course = $DB->get_record('course', array('id' => $context->instanceid), 'id')) {
                    // find all referentiels in which this user has a subscription, and its coursemodule id
                    if ($referentiels = $DB->get_records_sql("SELECT f.id, cm.id as coursemodule
                                                         FROM {referentiel} f,
                                                              {modules} m,
                                                              {course_modules} cm,
                                                              {referentiel_subscriptions} fs
                                                        WHERE fs.userid = ? AND f.course = ?
                                                              AND fs.referentiel = f.id AND cm.instance = f.id
                                                              AND cm.module = m.id AND m.name = 'referentiel'", array($userid, $context->instanceid))) {

                         foreach ($referentiels as $referentiel) {
                            // Valable pour Moodle 2.1 et Moodle 2.2
                            //if ($CFG->version < 2011120100) {
                                $modcontext = context_module::instance($referentiel->coursemodule);
                            //} else {
                            //    $modcontext = context_module::instance($referentiel->coursemodule);
                            //}

                             if ($modcontext) {
                                 if (!has_capability('mod/referentiel:viewdiscussion', $modcontext, $userid)) {
                                     referentiel_unsubscribe($userid, $referentiel->id);
                                 }
                             }
                         }
                     }
                 }
            }
            break;

        case CONTEXT_MODULE:   // Just one referentiel
            if (!is_enrolled($context, $userid)) {
                 if ($cm = get_coursemodule_from_id('referentiel', $context->instanceid)) {
                     if ($referentiel = $DB->get_record('referentiel', array('id' => $cm->instance))) {
                         if (!has_capability('mod/referentiel:viewdiscussion', $context, $userid)) {
                             referentiel_unsubscribe($userid, $referentiel->id);
                         }
                     }
                 }
            }
            break;
    }

    return true;
}


/**
 * Remove subscriptions for a user in a context
 *
 * @global object
 * @global object
 * @uses CONTEXT_SYSTEM
 * @uses CONTEXT_COURSECAT
 * @uses CONTEXT_COURSE
 * @uses CONTEXT_MODULE
 * @param int $userid
 * @param object $context
 * @return bool
 */
/*
function referentiel_remove_user_subscriptions($userid, $context) {

    global $CFG, $DB;

    if (empty($context->contextlevel)) {
        return false;
    }

    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:   // For the whole site
            // find all courses in which this user has a referentiel subscription
            if ($courses = $DB->get_records_sql("SELECT c.id
                                                  FROM {course} c,
                                                       {referentiel_subscriptions} fs,
                                                       {referentiel} f
                                                       WHERE c.id = f.course AND f.id = fs.referentiel AND fs.userid = ?
                                                       GROUP BY c.id", array($userid))) {

                foreach ($courses as $course) {
                    //$subcontext = context_course::instance($course->id);
     // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $subcontext = context_course::instance($course->id);
    //} else {
    //    $subcontext = context_course::instance($course->id);
    //}

                    referentiel_remove_user_subscriptions($userid, $subcontext);
                }
            }
            break;

        case CONTEXT_COURSECAT:   // For a whole category
             if ($courses = $DB->get_records('course', array('category' => $context->instanceid), '', 'id')) {
                 foreach ($courses as $course) {
                     //$subcontext = context_course::instance($course->id);
      // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $subcontext = context_course::instance($course->id);
    //} else {
    //    $subcontext = context_course::instance($course->id);
    //}
                    referentiel_remove_user_subscriptions($userid, $subcontext);
                 }
             }
             if ($categories = $DB->get_records('course_categories', array('parent' => $context->instanceid), '', 'id')) {
                 foreach ($categories as $category) {
                     //$subcontext = get_context_instance(CONTEXT_COURSECAT, $category->id);
     // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $subcontext = get_context_instance(CONTEXT_COURSECAT, $category->id);
    //} else {
    //    $subcontext = context_coursecat::instance($category->id);
    //}

                     referentiel_remove_user_subscriptions($userid, $subcontext);
                 }
             }
             break;

        case CONTEXT_COURSE:   // For a whole course
            if (!is_enrolled($context, $userid)) {
                 if ($course = $DB->get_record('course', array('id' => $context->instanceid), 'id')) {
                    // find all referentiels in which this user has a subscription, and its coursemodule id
                    if ($referentiels = $DB->get_records_sql("SELECT f.id, cm.id as coursemodule
                                                         FROM {referentiel} f,
                                                              {modules} m,
                                                              {course_modules} cm,
                                                              {referentiel_subscriptions} fs
                                                        WHERE fs.userid = ? AND f.course = ?
                                                              AND fs.referentiel = f.id AND cm.instance = f.id
                                                              AND cm.module = m.id AND m.name = 'referentiel'", array($userid, $context->instanceid))) {

                         foreach ($referentiels as $referentiel) {
                            // Valable pour Moodle 2.1 et Moodle 2.2
                            //if ($CFG->version < 2011120100) {
                                $modcontext = context_module::instance($referentiel->coursemodule);
                            //} else {
                            //    $modcontext = context_module::instance($referentiel->coursemodule);
                            //}

                             if ($modcontext) {
                                 if (!has_capability('mod/referentiel:viewdiscussion', $modcontext, $userid)) {
                                     referentiel_unsubscribe($userid, $referentiel->id);
                                 }
                             }
                         }
                     }
                 }
            }
            break;

        case CONTEXT_MODULE:   // Just one referentiel
            if (!is_enrolled($context, $userid)) {
                 if ($cm = get_coursemodule_from_id('referentiel', $context->instanceid)) {
                     if ($referentiel = $DB->get_record('referentiel', array('id' => $cm->instance))) {
                         if (!has_capability('mod/referentiel:viewdiscussion', $context, $userid)) {
                             referentiel_unsubscribe($userid, $referentiel->id);
                         }
                     }
                 }
            }
            break;
    }

    return true;
}

*/
?>

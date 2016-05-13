<?php // $Id: version.php,v 1.0 2013/04/05 16:41:20 jf Exp $
/**
 * Code fragment to define the version of referentiel
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author 
 * @version $Id: version.php,v 1.0 2013/04/05 16:41:20 jf Exp $
 * @package referentiel for Moodle 2.5
 **/


defined('MOODLE_INTERNAL') || die();
if (!isset($plugin)) {
    // Avoid warning message in M2.5 and below.
    $plugin = new stdClass();
}
$plugin->requires = 2016051000.00;    // Requires this Moodle version.
$plugin->version  = 2016051300;  // The current module version (Date: YYYYMMDDXX)
$plugin->release  = 'Referentiel v 10.3 for Moodle 3.1 with scale support, block and report - Release 2016-05-13';    // User-friendly date of release
$plugin->cron     = 60; //  Period for cron to check this module (secs)
$plugin->component  = 'mod_referentiel'; // Full name of the plugin (used for diagnostics)
$plugin->maturity  = MATURITY_STABLE;
$plugin->dependencies = NULL;


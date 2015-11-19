<?php
// referentiel module
// Moodle 2
$string['pluginname'] = 'Skills repository';
$string['rank'] = 'Rank';
$string['etudiants_inscrits_referentiel'] = 'Registered Students in any certification process';
$string['actualisation'] = 'Update students\' numbers from User profile';
$string['profilcheck'] = 'The modifications in Students numbers Profile will be set in Skills repository Student table...';
$string['migration'] = 'Skills repository plugin tables contain {$a} Moodle 1.9 links<br />Confirm the conversion to moodle 2.x format';
$string['suppression'] = 'Confirm deletion of old files and failed links ';

$string['verbose'] = 'Verbose Mode (converted links are displayed) ';
$string['conversionencours'] = 'The links migration process can take a very long time...';
$string['conversionachevee'] = 'The links migration process is finished.';

$string['migrationh'] = 'Obsoletes links migration';
$string['migrationh_help'] = 'When you migrate Moodle 1.9 to Moodle 2.x some links in table referentiel_document abd referentiel_consigne
are not converted errorously.

With this plugin you car force conversion and move documents from Moodle 1.9 old file system to the Moodle 2.X new one.

You may choose to keep old files for archive or delete them (a better choice to spare data space on server)

N.B. : Il the number of files is very hight (more than 3000), conversion process takes a lot of time.
You have to adapt php.ini settings

* max_execution_time = 5000

* memory_limit = 1024M

If you get a \'Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 284205254 bytes)\'
try one mor time the conversion process.';


?>

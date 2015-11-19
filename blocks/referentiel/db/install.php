<?php

function xmldb_block_referentiel_install() {
    global $DB;

/// Disable this block by default (because Referentiel is not technically part of 2.0)
$DB->set_field('block', 'visible', 0, array('name'=>'referentiel'));

}


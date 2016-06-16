<?php

function xmldb_block_referentiel_install() {
    global $DB;

/// esable this block by default (Referentiel is not technically part of 3.0 but this is useful)
$DB->set_field('block', 'visible', 1, array('name'=>'referentiel'));

}


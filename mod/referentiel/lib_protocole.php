<?php

// $Id:  lib_protocole.php,v 1.0 2012/02/05 00:00:00 jfruitet Exp $
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
 * calcule le seuil de certification pour une occurrence de referentiel
 * @author jfruitet
 * @version $Id: lib_protocole.php,v 1.0 2012/02/05 00:00:00 jfruitet Exp $
 * @package referentiel
 **/

// inclus dans lib.php

/*
    <TABLE NAME="referentiel_protocol" COMMENT="Protocole de validation du Referentiel de competence" PREVIOUS="referentiel_referentiel" NEXT="referentiel_domaine">
      <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" SEQUENCE="true" COMMENT="" NEXT="ref_occurrence"/>
        <FIELD NAME="ref_occurrence" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" DEFAULT="0" SEQUENCE="false" COMMENT="occurrence id" PREVIOUS="id" NEXT="seuil_referentiel"/>
        <FIELD NAME="seuil_referentiel" TYPE="float" NOTNULL="true" UNSIGNED="false" DEFAULT="0" SEQUENCE="false" COMMENT="certification threshold" PREVIOUS="ref_occurrence" NEXT="minima_referentiel"/>
        <FIELD NAME="minima_referentiel" TYPE="int"  LENGTH="10" NOTNULL="true" UNSIGNED="false" DEFAULT="0" SEQUENCE="false" COMMENT="certification minima threshold" PREVIOUS="seuil_referentiel" NEXT="l_domaines_oblig"/>
        <FIELD NAME="l_domaines_oblig" TYPE="text" LENGTH="small" NOTNULL="true" SEQUENCE="false" COMMENT="mandatory domains list" PREVIOUS="minima_referentiel" NEXT="l_seuils_domaines"/>
        <FIELD NAME="l_seuils_domaines" TYPE="text" LENGTH="small" NOTNULL="true" SEQUENCE="false" COMMENT="domains thresholds" PREVIOUS="l_domaines_oblig" NEXT="l_minimas_domaines"/>
        <FIELD NAME="l_minimas_domaines" TYPE="text" LENGTH="small" NOTNULL="true" SEQUENCE="false" COMMENT="domains thresholds" PREVIOUS="l_seuils_domaines" NEXT="l_competences_oblig"/>
        <FIELD NAME="l_competences_oblig" TYPE="text" LENGTH="small" NOTNULL="true" SEQUENCE="false" COMMENT="mandatory competencies list" PREVIOUS="l_minimas_domaines" NEXT="l_seuils_competences"/>
        <FIELD NAME="l_seuils_competences" TYPE="text" LENGTH="small" NOTNULL="true" SEQUENCE="false" COMMENT="competencies thresholds" PREVIOUS="l_competences_oblig" NEXT="l_minimas_competences"/>
        <FIELD NAME="l_minimas_competences" TYPE="text" LENGTH="small" NOTNULL="true" SEQUENCE="false" COMMENT="competencies thresholds" PREVIOUS="l_seuils_competences" NEXT="l_items_oblig"/>
        <FIELD NAME="l_items_oblig" TYPE="text" LENGTH="small" NOTNULL="true" SEQUENCE="false" COMMENT="mandatory items list" PREVIOUS="l_minimas_competences" NEXT="timemodified"/>
        <FIELD NAME="timemodified" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" DEFAULT="0" SEQUENCE="false" PREVIOUS="l_items_oblig" NEXT="commentaire"/>
        <FIELD NAME="commentaire" TYPE="text" LENGTH="small" NOTNULL="true" SEQUENCE="false" PREVIOUS="timemodified" NEXT="actif"/>
        <FIELD NAME="actif" TYPE="int" LENGTH="1" NOTNULL="true" UNSIGNED="true" DEFAULT="0" SEQUENCE="false" PREVIOUS="commentaire"/>
      </FIELDS>
      <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id" COMMENT="Primary key for referentiel"/>
      </KEYS>
    </TABLE>
*/


/**
 * Function add a protocol to table protocol
 *
 * @input object
 * @return true
 * @todo Finish documenting this function
**/
function referentiel_add_protocol($protocole){
global $DB;
    if (!empty($protocole->ref_occurrence)){
        // verifier si existe deja
        if ($record=$DB->get_record("referentiel_protocol", array("ref_occurrence" => $protocole->ref_occurrence))){
            $protocole->id=$record->id;
            return $DB->update_record("referentiel_protocol", $protocole);
        }
        else{
            return $DB->insert_record("referentiel_protocol", $protocole);
        }
    }
    return 0;
}


/**
 * Function set protocol to tables dommaine, competence, item
 *
 * @uses $CFG
 * @input object
 * @return true
 * @todo Finish documenting this function
**/
function referentiel_set_domaine_competence_item($protocole){
global $DB;

/*
                $protocole = new object();
                $protocole->ref_occurrence=$occurrence->id;
                $protocole->seuil_referentiel=$occurrence->seuil_certificat;
                $protocole->minima_referentiel=$occurrence->minima_certificat;

                $protocole->l_domaines_oblig=referentiel_initialise_domaines($occurrence->id, '0');
                $protocole->l_seuils_domaines=referentiel_initialise_domaines($occurrence->id, '0.0');
                $protocole->l_minimas_domaines=referentiel_initialise_domaines($occurrence->id, '0');

                $protocole->l_competences_oblig=referentiel_initialise_competences($occurrence->id, '0');
                $protocole->l_seuils_competences=referentiel_initialise_competences($occurrence->id, '0.0');
                $protocole->l_minimas_competences=referentiel_initialise_competences($occurrence->id, '0');

                $protocole->l_items_oblig=referentiel_initialise_items($occurrence->id, '0');
                $protocole->timemodified=0;
                $protocole->actif=0;
                $protocole->commentaire=addslashes(get_string('aide_protocole_completer','referentiel'));
*/
    if ($protocole){
        // DEBUG
        // echo "<br />DEBUG :: lib_protocole.php :: 90<br />\n";
        // print_object($protocole);
        // echo "<br />\n";

        $params=array("id" => $protocole->ref_occurrence );
        $sql="SELECT * FROM {referentiel_referentiel} WHERE id=:id";
        
        $occurrence=$DB->get_record_sql($sql, $params);
        if (!empty($occurrence)){
            // DEBUG
            //echo "<br />OCCURRENCE<br />\n";
            //print_object($occurrence);
            referentiel_set_seuil_certification($protocole->ref_occurrence, $protocole->seuil_referentiel);
			referentiel_set_minima_certification($protocole->ref_occurrence, $protocole->minima_referentiel);

            // rechercher dans les tables domaine, competence, item


            // ITEM
            if (!empty($protocole->l_items_oblig)){
                $t_codes_items=explode("/", $protocole->l_items_oblig);
                //echo "<br />DEBUG LISTE\n";
                //print_r($t_codes_items);
                //echo "<br />\n";
                if ($t_codes_items){
                    while (list($key, $code) = each($t_codes_items)) {
                        // DEBUG
                        //echo "<br />$key => $code\n";
				        if (!empty($code)){
                            $t_oblig= explode(':', $code);
                            // DEBUG
                            //echo "<br />\n";
                            //print_r($t_oblig);
                            
                            if (!empty($t_oblig[0])){
                                referentiel_set_type_item($protocole->ref_occurrence, $t_oblig[0], $t_oblig[1]);
                            }
                        }
                    }
                }
            }
            //echo "<br />EXIT :: 112\n";
            //exit;

            // DOMAINE ET COMPETENCES
            
            $domaines=$DB->get_records_sql("SELECT id, code_domaine, num_domaine FROM {referentiel_domaine} WHERE ref_referentiel=:id ORDER BY num_domaine ", array("id" => $occurrence->id));
            if (!empty($domaines)){
                foreach($domaines as $domaine){
                    $competence=$DB->get_records_sql("SELECT id, code_competence, num_competence FROM {referentiel_competence} WHERE ref_domaine=:id ORDER BY num_competence ", array("id"=>$domaine->id));
                    if (!empty($competence)){
                        foreach($competence as $competence){
                            if (!empty($competence->code_competence)){
                                if (preg_match('/'.preg_quote($competence->code_competence).':\d/', $protocole->l_competences_oblig, $matches)){
                                    // DEBUG
                                    // print_object($matches);
                                    // echo "<br />\n";
                                    if (isset($matches[0])){
                                        $t_oblig= explode(':', $matches[0]);
                                        // DEBUG
                                        // echo "<br />\n";
                                        // print_r($t_oblig);

                                        if (!empty($t_oblig[0])){
                                            referentiel_set_type_competence($competence->id, $t_oblig[1]);
                                        }
                                    }
                                }
								
                                if (preg_match('/'.preg_quote($competence->code_competence).':\d/', $protocole->l_minimas_competences, $matches)){
                                    // DEBUG
                                    // print_object($matches);
                                    // echo "<br />\n";
                                    if (isset($matches[0])){
                                        $t_oblig= explode(':', $matches[0]);
                                        // DEBUG
                                        // echo "<br />\n";
                                        // print_r($t_oblig);

                                        if (!empty($t_oblig[0])){
                                            referentiel_set_minima_competence($competence->id, $t_oblig[1]);
                                        }
                                    }
                                }
								
                                if (preg_match('/'.preg_quote($competence->code_competence).':\d+\.?\d*/', $protocole->l_seuils_competences, $matches)){
                                    // DEBUG
                                    // print_object($matches);
                                    // echo "<br />\n";
                                    if (isset($matches[0])){
                                        $t_oblig= explode(':', $matches[0]);
                                        // DEBUG
                                        // echo "<br />\n";
                                        // print_r($t_oblig);

                                        if (!empty($t_oblig[0])){
                                            referentiel_set_seuil_competence($competence->id, $t_oblig[1]);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    
                    // Mettre a jour le domaine
                    if (!empty($domaine->code_domaine)){
                        if (preg_match('/'.preg_quote($domaine->code_domaine).':\d/', $protocole->l_domaines_oblig, $matches)){
                            // DEBUG
                            // print_object($matches);
                            // echo "<br />\n";
                            if (isset($matches[0])){
                                $t_oblig= explode(':', $matches[0]);
                                if (!empty($t_oblig[0])){
                                    referentiel_set_type_domaine($domaine->id, $t_oblig[1]);
                                }
                            }
                        }
                        if (preg_match('/'.preg_quote($domaine->code_domaine).':\d/', $protocole->l_minimas_domaines, $matches)){
                            // DEBUG
                            // print_object($matches);
                            // echo "<br />\n";
                            if (isset($matches[0])){
                                $t_minima= explode(':', $matches[0]);
                                if (!empty($t_oblig[0])){
                                    referentiel_set_minima_domaine($domaine->id, $t_minima[1]);
                                }
                            }
                        }
						
                        if (preg_match('/'.preg_quote($domaine->code_domaine).':\d+\.?\d*/', $protocole->l_seuils_domaines, $matches)){
                            // DEBUG
                            // print_object($matches);
                            // echo "<br />\n";
                            if (isset($matches[0])){
                                $t_oblig= explode(':', $matches[0]);
                                if (!empty($t_oblig[0])){
                                    referentiel_set_seuil_domaine($domaine->id, $t_oblig[1]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

/**
 * Function get protocol for occurrence
 *
 * @uses $CFG
 * @return object
 * @todo Finish documenting this function
**/
function referentiel_delete_protocole_occurrence($refrefid) {
global $DB;
    if (!empty($refrefid)){
        $DB->delete_records("referentiel_protocol", array("ref_occurrence" => $refrefid));
    }
}

/**
 * Function get protocol for occurrence
 *
 * @uses $CFG
 * @return object
 * @todo Finish documenting this function
**/
function referentiel_get_protocol($refrefid) {
global $DB;
    // rechercher si la ligne existe pour l'occurrence
    // echo "<br />DEBUG :: lib_protocole.php :: 290 :: OCCURRENCE : $refrefid<br />\n";
	if (!empty($refrefid)){
        //
        $protocole=$DB->get_record_sql("SELECT * FROM {referentiel_protocol} WHERE ref_occurrence=:id ", array("id"=>$refrefid));
		if (empty($protocole)){
            // mettre a jour la table referentiel_protocol
            // Rechercher l'occurrence dans la table referentiel_referentiel
            $occurrence=$DB->get_record_sql("SELECT * FROM {referentiel_referentiel} WHERE id=:id ", array("id"=>$refrefid));
            if (!empty($occurrence)){
                // rechercher dans les tables domaine, competence, item
                // mettre a jour le protocole
                $protocole = new object();
                $protocole->ref_occurrence=$occurrence->id;
                $protocole->seuil_referentiel=$occurrence->seuil_certificat;
                $protocole->minima_referentiel=$occurrence->minima_certificat;
				
                // initialiser les items obligatoires à 0
                $liste_items_obligatoires='';
                if (!empty($occurrence->liste_codes_competence)){
                    $t_codes_items=explode("/", $occurrence->liste_codes_competence);
                    while (list($key, $code) = each($t_codes_items)) {
				        //echo "$key => code\n";
				        if (!empty($code)){
                            $item=$DB->get_record_sql("SELECT id, code_item, type_item FROM {referentiel_item_competence} WHERE ref_referentiel=:id  AND code_item=:code ", array("id"=>$occurrence->id, "code" =>$code));
                            if ($item){
                                $liste_items_obligatoires.= $item->code_item.':'.$item->type_item.'/';
                            }
                        }
                    }
                }
                $protocole->l_items_oblig=$liste_items_obligatoires;

                // competences
                $liste_competences_obligatoires='';
                $liste_competences_seuils='';
                $liste_competences_minimas='';
                // domaines
                $liste_domaines_obligatoires='';
                $liste_domaines_seuils='';
                $liste_domaines_minimas='';
				
                $domaines=$DB->get_records_sql("SELECT id, code_domaine, type_domaine, seuil_domaine, minima_domaine, num_domaine FROM {referentiel_domaine} WHERE ref_referentiel=:id ORDER BY num_domaine ", array("id"=>$occurrence->id));
                if (!empty($domaines)){
                    foreach($domaines as $domaine){
                        $liste_comp_oblig='';
                        $liste_comp_seuil='';
                        $liste_comp_minima='';						
                        $codes_competence=$DB->get_records_sql("SELECT code_competence, type_competence, seuil_competence, minima_competence, num_competence FROM {referentiel_competence} WHERE ref_domaine=:id ORDER BY num_competence ", array("id"=>$domaine->id));
                        if (!empty($codes_competence)){
                            foreach($codes_competence as $codec){
                                $liste_comp_oblig.=$codec->code_competence.':'.$codec->type_competence.'/';
                                $liste_comp_seuil.=$codec->code_competence.':'.$codec->seuil_competence.'/';
                                $liste_comp_minima.=$codec->code_competence.':'.$codec->minima_competence.'/';								
                            }
                        }
                        $liste_competences_obligatoires.=$liste_comp_oblig;
                        $liste_competences_seuils.=$liste_comp_seuil;
						$liste_competences_minimas.=$liste_comp_minima;
						
                        if (!empty($domaine->code_domaine)){
                            $liste_domaines_obligatoires.=$domaine->code_domaine.':'.$domaine->type_domaine.'/';
                            $liste_domaines_seuils.=$domaine->code_domaine.':'.$domaine->seuil_domaine.'/';
							$liste_domaines_minimas.=$domaine->code_domaine.':'.$domaine->minima_domaine.'/';
                        }
                    }
                }

                // initialiser les competences
                $protocole->l_competences_oblig=$liste_competences_obligatoires;
                $protocole->l_seuils_competences=$liste_competences_seuils;
				$protocole->l_minimas_competences=$liste_competences_minimas;
				
                // initialiser les domaines
                $protocole->l_domaines_oblig=$liste_domaines_obligatoires;
                $protocole->l_seuils_domaines=$liste_domaines_seuils;
				$protocole->l_minimas_domaines=$liste_domaines_minimas;
				
                $protocole->commentaire='';
                $protocole->timemodified=time();
                $protocole->actif=0;   // cela permet de reperer les protocoles jamais actives
                // creer la ligne
                $protocole->id=$DB->insert_record('referentiel_protocol', $protocole);
            }
        }
        // DEBUG
        // echo "<br />DEBUG :: lib_protocole.php :: 378 :: PROTOCOLE<br />\n";
        // print_object($protocole);

        return $protocole;
    }
    return NULL;
}



// Retourne le seuil de certification
// *****************************************************************
// * input referentiel_referentiel id
// * output int                                                    *
// *****************************************************************

function referentiel_get_seuil_certification($refrefid) {
global $DB;
    // rechercher si la ligne existe pour l'occurrence avec la table protocole
    if (!empty($refrefid)){
        if ($protocole=$DB->get_record_sql("SELECT seuil_referentiel FROM {referentiel_protocol} WHERE ref_occurrence=:id", array("id"=>$refrefid))){
            return $protocole->seuil_referentiel;
        }
    }
    else{
        if ($occurrence=$DB->get_record_sql("SELECT seuil_certificat FROM {referentiel_referentiel} WHERE id=:id ", array("id"=>$refrefid))){
            return $occurrence->seuil_certificat;
        }
    }
    return 0;
}



// Retourne le minimum de certification
// *****************************************************************
// * input referentiel_referentiel id
// * output int                                                    *
// *****************************************************************

function referentiel_get_minima_certification($refrefid) {
global $DB;
    // rechercher si la ligne existe pour l'occurrence avec la table protocole
    if (!empty($refrefid)){
        if ($protocole=$DB->get_record_sql("SELECT minima_referentiel FROM {referentiel_protocol} WHERE ref_occurrence=:id", array("id"=>$refrefid))){
            return $protocole->minima_referentiel;
        }
    }
    else{
        if ($occurrence=$DB->get_record_sql("SELECT minima_certificat FROM {referentiel_referentiel} WHERE id=:id ", array("id"=>$refrefid))){
            return $occurrence->minima_certificat;
        }
    }
    return 0;
}


// Retourne le seuil de certification
// *****************************************************************
// * input referentiel_referentiel id
// * output int                                                  *
// *****************************************************************

function referentiel_get_nb_items($refrefid) {
global $DB;
    // rechercher si la ligne existe pour l'occurrence avec la table protocole
    if ($occurrence=$DB->get_record_sql("SELECT liste_codes_competence FROM {referentiel_referentiel} WHERE id=:id ", array("id"=>$refrefid))){
        if (!empty($occurrence->liste_codes_competence)){
                $t_codes=explode('/',$occurrence->liste_codes_competence);
                return(count($t_codes)-1);
        }
    }
    return 0;
}

/**
 * Function initialise global table protocol
 *
 * @uses globals
 * @inmut referentiel_referentiel id
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_initialise_protocole_referentiel($refrefid){
    global $protocol_seuil_referentiel;
    global $protocol_minima_referentiel;	
    global $protocol_t_domaines_oblig;
    global $protocol_t_competences_oblig;
    global $protocol_t_domaines_seuil;
    global $protocol_t_domaines_minima;	
    global $protocol_t_competences_seuil;
    global $protocol_t_competences_minima;	
    global $protocol_t_items_oblig;
    global $protocol_commentaire;
    global $protocol_timemodified;
    global $protocol_actif;

    if ($p=referentiel_get_protocol($refrefid)){
            $protocol_seuil_referentiel=$p->seuil_referentiel;
			$protocol_minima_referentiel=$p->minima_referentiel;
            $protocol_t_domaines_oblig=explode('/',referentiel_purge_dernier_separateur($p->l_domaines_oblig,'/'));
            $protocol_t_domaines_seuil=explode('/',referentiel_purge_dernier_separateur($p->l_seuils_domaines,'/'));
			$protocol_t_domaines_minima=explode('/',referentiel_purge_dernier_separateur($p->l_minimas_domaines,'/'));

            $protocol_t_competences_oblig=explode('/',referentiel_purge_dernier_separateur($p->l_competences_oblig,'/'));
            $protocol_t_competences_seuil=explode('/',referentiel_purge_dernier_separateur($p->l_seuils_competences,'/'));
			$protocol_t_competences_minima=explode('/',referentiel_purge_dernier_separateur($p->l_minimas_competences,'/'));
			
            $protocol_t_items_oblig=explode('/',referentiel_purge_dernier_separateur($p->l_items_oblig,'/'));
            $protocol_commentaire=stripslashes($p->commentaire);
            $protocol_timemodified=$p->timemodified;
            $protocol_actif=$p->actif;
        return true;
    }
    return false;
}

/**
 * Function set mandatory items for occurrence
 *
 * @param refrefid references referentiel_referentiel occurrence
 * @return string
 * A.1.1:0/A.1.2:1/A.1.3:2/A.1.4:0/A.1.5:0/A.2.1:1/A.2.2:1/A.2.3:1/A.3.1:0/A.3.2:0/A.3.3:0/A.3.4:0/B.1.1:0/B.1.2:0/B.1.3:0/B.2.1:0/B.2.2:0/B.2.3:1/B.2.4:0/B.3.1:0/B.3.2:0/B.3.3:0/B.3.4:0/B.3.5:0/B.4.1:1/B.4.2:1/B.4.3:0/

 * @todo Finish documenting this function
**/

function referentiel_initialise_items($refrefid, $valeur){
    // rtourne une liste de codes initialisee

    global $DB;
    $liste_items='';
    
    if (!empty($refrefid)){
        if ($ref=$DB->get_record_sql("SELECT liste_codes_competence FROM {referentiel_referentiel} WHERE id=:id", array("id"=>$refrefid))){
            // echo "<br />DEBUG :: 176 :: $ref->liste_codes_competence\n";
            $l_codes=$ref->liste_codes_competence;
            if (!empty($l_codes)){
                $l_codes=referentiel_purge_dernier_separateur($l_codes, "/");
                $t_codes_items=explode("/", $l_codes);
	   		    while (list($key, $code) = each($t_codes_items)) {
				    //echo "$key => code\n";
                    $liste_items.= $code.':'.$valeur.'/';
                }
            }
        }
    }
    // echo "<br />DEBUG :: lib_protocole.php :: 183 :: ITEMS OBLIGATOIRES :<br />\n";
	// echo $liste_items."<br />\n";
    return $liste_items;
}

/**
 * Function set mandatory domaines for an occurrence
 *
 * @param refrefid references referentiel_referentiel id
 * @return string
 * A:0/B:0/
 * @todo Finish documenting this function
**/

function referentiel_initialise_domaines($refrefid, $valeur){

    global $DB;
    $liste_domaines='';

    if (!empty($refrefid)){
        if ($domaines=$DB->get_records_sql("SELECT code_domaine, num_domaine FROM {referentiel_domaine} WHERE ref_referentiel=:id ORDER BY num_domaine ", array("id"=>$refrefid))){
            foreach($domaines as $domaine){
                $liste_domaines.=$domaine->code_domaine.':'.$valeur.'/';
            }
        }
    }
    // echo "<br />DEBUG :: lib_protocole.php :: 156 :: DOMAINES OBLIGATOIRES :<br />\n";
	// echo $liste_domaines."<br />\n";
    return $liste_domaines;
}

/**
 * Function set mandatory competencies for a domain
 *
 * @param domaineid references referentiel_domaine id
 * @return string
 * A.1:0/A.2:1/A.3:0/

 * @todo Finish documenting this function
**/

function referentiel_initialise_competences_domaine($domaineid, $valeur){

    global $DB;
    $liste_competences='';

    if (!empty($domaineid)){
        if ($competences=$DB->get_records_sql("SELECT code_competence, num_competence FROM {referentiel_competence} WHERE ref_domaine=:id ORDER BY num_competence ", array("id"=>$domaineid))){
            foreach($competences as $competence){
                $liste_competences.=$competence->code_competence.':'.$valeur.'/';
            }
        }
    }
    // echo "<br />DEBUG :: lib_protocole.php :: 182 :: COMPETENCES OBLIGATOIRES POUR le domaine $domaineid :<br />\n";
	// echo $liste_competences."<br />\n";
    return $liste_competences;
}

/**
 * Function set mandatory competencies for an occurrence
 *
 * @param refrefid references referentiel_referentiel id
 * @return string
 * A.1:0/A.2:0/A.3:0/B.1:0/B.2:0/
 * @todo Finish documenting this function
**/

function referentiel_initialise_competences($refrefid, $valeur){

    global $DB;
    $liste_competences='';

    if (!empty($refrefid)){
        if ($domaines=$DB->get_records_sql("SELECT id, num_domaine FROM {referentiel_domaine} WHERE ref_referentiel=:id ORDER BY num_domaine ", array("id"=>$refrefid))){
            foreach($domaines as $domaine){
                $liste_competences.=referentiel_initialise_competences_domaine($domaine->id, $valeur);
            }
        }
    }
    // echo "<br />DEBUG :: lib_protocole.php :: 208 :: COMPETENCES OBLIGATOIRES :<br />\n";
	// echo $liste_competences."<br />\n";
    return $liste_competences;
}


/**
 * Function set thresholds for an occurrence
 *
 * @param refrefid reference referentiel_referentiel id
 * @return string
 * A.1:0/A.2:0/A.3:0/B.1:0/B.2:0/
 * @todo Finish documenting this function
**/


function referentiel_certificat_valide($competences_certificat, $refrefid){
// ce certificat comporte des pourcentages par domaine et competence
// affichage sous forme de tableau et span pour les items
// input competences_certificat
// A.1-1:0/A.1-2:0/A.1-3:1/A.1-4:0/A.1-5:0/A.2-1:0/A.2-2:0/A.2-3:0/A.3-1:0/A.3-2:1/A.3-3:1/A.3-4:1/B.1-1:0/B.1-2:0/B.1-3:0/B.2-1:0/B.2-2:0/B.2-3:0/B.2-4:0/B.2-5:0/B.3-1:0/B.3-2:0/B.3-3:0/B.3-4:0/B.3-5:0/B.4-1:0/B.4-2:0/B.4-3:0/

$return_value=-1;
global $OK_REFERENTIEL_DATA;        // les données du certificat sont disponibles
global $OK_REFERENTIEL_PROTOCOLE;  // les données du protocole sont disponibles


// DOMAINES
global $t_domaine;
global $t_domaine_coeff;

// COMPETENCES
global $t_competence;
global $t_competence_coeff;

// ITEMS
global $t_item_code;
global $t_item_coeff; // coefficient poids determine par le modele de calcul (soit poids soit poids / empreinte)
global $t_item_domaine; // index du domaine associe a un item
global $t_item_competence; // index de la competence associee a un item
global $t_item_poids; // poids
global $t_item_empreinte;
global $t_nb_item_domaine;
global $t_nb_item_competence;

// MODIF JF 2012/03/26
global $t_competence_domaine; // index du domaine associe a une competence
global $t_nb_competence_domaine; // nombre de competence par domaine

// tables de protocoles (seuils, item obligatoires, etc.
global $protocol_seuil_referentiel;
global $protocol_minima_referentiel;

global $protocol_t_domaines_oblig;
global $protocol_t_domaines_seuil;
global $protocol_t_domaines_minima;

global $protocol_t_competences_oblig;
global $protocol_t_competences_seuil;
global $protocol_t_competences_minima;

global $protocol_t_items_oblig;
global $protocol_timemodified;
global $protocol_actif;

$separateur1='/';
$separateur2=':';

    $nb_items_valides=0;            // items valides
	$t_certif_item_valeur=array();	// table des nombres d'items valides
	$t_certif_item_coeff=array(); // somme des poids du domaine
	$t_certif_competence_poids=array(); // somme des poids de la competence
	$t_certif_domaine_poids=array(); // poids certifies

    // MODIF JF 2012/03/26
    $t_certif_competences_valides=array(); // nombre de competences valides
    $t_certif_domaines_valides=array(); // nombre de domaines valides

	// donnees globales du referentiel
	if ($refrefid){
		if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
			$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($refrefid);
		}
		if (!isset($OK_REFERENTIEL_PROTOCOLE) || ($OK_REFERENTIEL_PROTOCOLE==false) ){
			$OK_REFERENTIEL_PROTOCOLE=referentiel_initialise_protocole_referentiel($refrefid);
		}

		if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)
            && isset($OK_REFERENTIEL_PROTOCOLE) && ($OK_REFERENTIEL_PROTOCOLE==true)){

            // DEBUG
            /*
            echo "<br />DEBUG:: lib_protocol.php :: 318 :: PROTOCOL VARIABLES<br />\n";
            echo "SEUIL : $protocol_seuil_referentiel<br />\n";
            echo "<br />DOMAINES OBLIG<br />\n";
            print_object($protocol_t_domaines_oblig);
            echo "<br />DOMAINES SEUILS<br />\n";
            print_object($protocol_t_domaines_seuil);
            echo "<br />DOMAINES MINIMAS<br />\n";
            print_object($protocol_t_domaines_minima);
            echo "<br />COMPETENCES OBLIG<br />\n";
            print_object($protocol_t_competences_oblig);
            echo "<br />COMPETENCES SEUILS<br />\n";
            print_object($protocol_t_competences_seuil);
            echo "<br />COMPETENCES MINIMAS<br />\n";
            print_object($protocol_t_competences_minima);
            echo "<br />ITEMS OBLIG<br />\n";
            print_object($protocol_t_items_oblig);
            */
            if ($protocol_actif){
            
                // Initialisation
                for ($i=0; $i<count($t_item_code); $i++){
                    $t_certif_item_valeur[$i]=0.0;
	       	        $t_certif_item_coeff[$i]=0.0;
	            }
                for ($i=0; $i<count($t_competence); $i++){
                    $t_certif_competence_poids[$i]=0.0;
                    $t_certif_competences_valides[$i]=0;
                }
                for ($i=0; $i<count($t_domaine); $i++){
                    $t_certif_domaine_poids[$i]=0.0;
                    $t_certif_domaines_valides[$i]=0;
                }

                // RECUPERER les pourcentages par competence et domaine
                // en fonction des items valides dans le certificat
                // DEBUG
                // echo "<br />DEBUG :: lib_protocole.php :: 329 :: CODE <br />\n";
                // referentiel_affiche_data_referentiel($refrefid, NULL);

                // recuperer les items valides
                $tc=array();
                $liste_code=referentiel_purge_dernier_separateur($competences_certificat, $separateur1);
                // DEBUG
                // echo "<br />DEBUG :: lib_protocole.php :: 383 :: LISTE : $liste_code<br />\n";

                if (!empty($liste_code) && ($separateur1!="") && ($separateur2!="")){
                    $tc = explode ($separateur1, $liste_code);
                    for ($i=0; $i<count($t_item_domaine); $i++){
                        $t_certif_domaine_poids[$i]=0.0;
                    }
                    for ($i=0; $i<count($t_item_competence); $i++){
                        $t_certif_competence_poids[$i]=0.0;
                    }

                    $i=0;
                    while ($i<count($tc)){
		      		    // CODE1:N1
		  	       	    // DEBUG
				        // echo "<br />".$tc[$i]." <br />\n";
    				    // exit;
	       			    $t_cc=explode($separateur2, $tc[$i]); // tableau des items valides

		      		    // print_r($t_cc);
			     	    // echo "<br />\n";
				        // exit;
    				    if (isset($t_cc[1])){
                            if (isset($t_item_poids[$i]) && isset($t_item_empreinte[$i])){
                                if (($t_item_poids[$i]>0) && ($t_item_empreinte[$i]>0)){
                                    // echo "<br />".min($t_cc[1],$t_item_empreinte[$i]);
				    			    $t_certif_item_valeur[$i]=min($t_cc[1],$t_item_empreinte[$i]);
					       		    // calculer le taux
					   	       	    $coeff=(float)$t_certif_item_valeur[$i] * (float)$t_item_coeff[$i];
							        // stocker la valeur pour l'item
    							    $t_certif_item_coeff[$i]=$coeff;
	       						    // stocker le taux pour la competence
				    			    $t_certif_competence_poids[$t_item_competence[$i]]+=$coeff;
			     				    // stocker le taux pour le domaine
		      					    $t_certif_domaine_poids[$t_item_domaine[$i]]+=$coeff;

					       		    if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
                                        $t_certif_competences_valides[$t_item_competence[$i]]++;
    					       		    $nb_items_valides++;
                                    }
    						    }
	       					    else{
		      					    // echo "<br />".min($t_cc[1],$t_item_empreinte[$i]);
			     				    $t_certif_item_valeur[$i]=0.0;
				    			    $t_certif_item_coeff[$i]=0.0;
					       	    }
    					   }
	       			    }

				        $i++;
    			    }

                    /* DOMAINES */
                    // mettre a jour les minimas
                    // compter les competences valides par domaine
                    for ($i=0; $i<count($t_competence); $i++){
                        if (isset($t_certif_competences_valides[$t_competence_domaine[$i]])){
                            $t_certif_domaines_valides[$t_competence_domaine[$i]]++;
                        }
                    }


                    // Verifier si le certificat est valide
                    // Calculer la valeur des items valides
                    $valeur_items_valides=0.0;
                    for ($i=0; $i<count($t_certif_item_coeff); $i++){
                        $valeur_items_valides+=$t_certif_item_coeff[$i];
                    }
                    // Vérifier si les minimas sont depassés
                    $ok_minima_competence=true;
                    $ok_minima_domaine=true;
                    
                    // Verifier si le seuil est depassé
                    $ok_seuil=true;
                    $ok_competences=true;
                    $ok_domaines =true;
                    $ok_items=true;
                
                    // $ok_items=true;   echo "<br />DEBUG:: lib_protocol.php :: 413 :: DEBUG FORCER le test du PROTOCOLE\n";
                    //$ok_items=($protocol_seuil_referentiel>0) && ($nb_items_valides>=$protocol_seuil_referentiel);
                    if ($protocol_seuil_referentiel>0){
                        $ok_seuil=($valeur_items_valides>=$protocol_seuil_referentiel);
                    }
                    if ($ok_seuil){
                        $ok_seuil=($nb_items_valides>=$protocol_minima_referentiel);
                    }

                    // 1) Verifier si tous les items obligatoires sont valides
                    $ok_items=true;
                    if ($ok_items){
                        $i=0;
                        while (($i<count($protocol_t_items_oblig)) && $ok_items){
                            $t_oblig= explode($separateur2,$protocol_t_items_oblig[$i]);
                            // echo "<br />ITEM_OBLIG<br />\n";
                            // print_r($t_oblig);
                            if (isset($t_oblig[1])){
                                if (($t_item_poids[$i]>0) && ($t_item_empreinte[$i]>0) && ($t_oblig[1]>0)){
                                    $ok_items=$ok_items && ($t_certif_item_valeur[$i] >= $t_item_empreinte[$i]);
                                }
                            }
                            $i++;
                        }
                    }

                    // 2) Verifier les competences obligatoires
                    $ok_competences_oblig=true;
                    $i=0;
                    while (($i<count($protocol_t_competences_oblig)) && $ok_competences_oblig){
                        $t_oblig= explode($separateur2, $protocol_t_competences_oblig[$i]);
                        $t_seuil= explode($separateur2, $protocol_t_competences_seuil[$i]);
                        //echo "<br />COMPETENCES_OBLIGS<br />\n";
                        //print_r($t_oblig);
                        //echo "<br />COMPETENCES_SEUILS<br />\n";
                        //print_r($t_seuil);

                        if (isset($t_oblig[1]) && $t_oblig[1]>0 && isset($t_seuil[1])){
                            $ok_competences_oblig=$ok_competences_oblig && ($t_certif_competence_poids[$i] >= $t_seuil[1]);
                        }
                        $i++;
                    }

// MODIF JF 2012/03/26
                    // 3) Puis verifier les competences par rapport aux minimas
                    // cela est utile si aucune compétence n'est obligatoire mais qu'il y a une contrainte du style
                    // pas plus de n items invalides par compétence
                    $ok_competences=$ok_competences_oblig;
                    if ($ok_competences){
                        $i=0;
                        while (($i<count($protocol_t_competences_seuil)) && $ok_competences){
                            $t_minima= explode($separateur2, $protocol_t_competences_minima[$i]);
                            if (isset($t_minima[1])){
                                $ok_competences=$ok_competences && ($t_certif_competences_valides[$i] >= $t_minima[1]);

                            }
                            $i++;
                        }
                    }

                    // 4) Puis verifier les competences par rapport aux seuils
                    // cela est utile si aucune compétence n'est obligatoire mais qu'il y a une contrainte du style
                    // la somme ponderees de items suerieure au seuil de la competence
                    if ($ok_competences){
                        $i=0;
                        while (($i<count($protocol_t_competences_seuil)) && $ok_competences){
                            $t_seuil= explode($separateur2, $protocol_t_competences_seuil[$i]);
                            if (isset($t_seuil[1])){
                                $ok_competences=$ok_competences && ($t_certif_competence_poids[$i] >= $t_seuil[1]);
                            }
                            $i++;
                        }
                    }



                    // 5) verifier les domaines
                    // if ($ok_competences_oblig){
                    // Verifier les domaines obligatoire par rapport aux seuils
                    $ok_domaines_oblig=true;
                    $i=0;
                    while (($i<count($protocol_t_domaines_oblig))  && $ok_domaines_oblig){
                        $t_oblig= explode($separateur2, $protocol_t_domaines_oblig[$i]);
                        $t_seuil= explode($separateur2, $protocol_t_domaines_seuil[$i]);
                            //echo "<br />DOMAINES_OBLIGS<br />\n";
                            //print_r($t_oblig);
                            //echo "<br />DOMAINES_SEUILS<br />\n";
                            //print_r($t_seuil);

                        if (isset($t_oblig[1]) && $t_oblig[1]>0 && isset($t_seuil[1])){
                            $ok_domaines_oblig=$ok_domaines_oblig && ($t_certif_domaine_poids[$i] >= $t_seuil[1]);
                        }
                        $i++;
                    }
                    
                    $ok_domaines=$ok_domaines_oblig;

                    // 6) Verifier les domaines par rapport aux minimas
                    // Cela est utile si aucun domaine n'est obligatoire mais qu'il y a une contrainte du style
                    // pas plus de n competences invalides par domaine

                    $ok_domaines=$ok_domaines_oblig;
                    if ($ok_domaines){
                        $i=0;
                        while (($i<count($protocol_t_domaines_seuil)) && $ok_domaines){
                            $t_minima= explode($separateur2, $protocol_t_domaines_minima[$i]);
                            if (isset($t_minima[1])){
                                $ok_domaines=$ok_domaines && ($t_certif_domaines_valides[$i] >= $t_minima[1]);
                            }
                            $i++;
                        }
                    }

                    // 7) Verifier les domaines par rapport aux les seuils
                    // Cela est utile si aucun domaine n'est obligatoire mais qu'il y a une contrainte du style
                    // pas moins de x pourcentage par rapport au seuil du domaine
                    if ($ok_domaines){
                        $i=0;
                        while (($i<count($protocol_t_domaines_seuil)) && $ok_domaines){
                            $t_seuil= explode($separateur2, $protocol_t_domaines_seuil[$i]);
                            if (isset($t_seuil[1])){
                                $ok_domaines=$ok_domaines && ($t_certif_domaine_poids[$i] >= $t_seuil[1]);
                            }
                            $i++;
                        }
                    }


                    // DEBUG
                    /*
                    echo "<br />DEBUG :: lib_protocole.php :: 531\n";
                    if ($ok_items) {echo "<br />ITEMS : TRUE";} else {echo "<br />ITEMS : FALSE";}
                    if ($ok_competences) {echo "<br />COMPETENCE: TRUE";} else {echo "<br />COMPETENCE: FALSE";}
                    if ($ok_domaines) {echo "<br />DOMAINE: TRUE";} else {echo "<br />DOMAINE: FALSE";}
                    */
                    if ($ok_seuil && $ok_items && $ok_competences && $ok_domaines){
                        $return_value=1;
                    }
                    else{
                        $return_value=0;
                    }
                }
            }
        }
	}
	return $return_value;
}


/**
 * Function set table referentiel_protocol
 *
 * @inmut referentiel_referentiel id
 * @return true or false
 * @todo Finish documenting this function
**/

function referentiel_set_protocole($refrefid, $form){
global $DB;
    // traiter le formulaire
    $lcode_item='';
    $lcode_competence='';
    $lcode_domaine='';
    $lseuil_competence='';
    $lseuil_domaine='';
    $lminima_competence='';
    $lminima_domaine='';
    
    if (!empty($refrefid)){
        $protocole=referentiel_get_protocol($refrefid);


            // DEBUG
            //echo "<br />Debug :: lib_protocole.php :: 568 <br />\n";
            //print_object($form);
            //print_object($protocole);

     }
     
     if (!empty($form)){
        if (isset($form->commentaire_protocole)){
            $protocole->commentaire = addslashes(strip_tags($form->commentaire_protocole));
        }

        if (isset($form->seuil_certif)){
            $protocole->seuil_referentiel = $form->seuil_certif;
            referentiel_set_seuil_certification($refrefid, $form->seuil_certif);
        }

        if (isset($form->minima_certif)){
            $protocole->minima_referentiel = $form->minima_certif;
            referentiel_set_minima_certification($refrefid, $form->minima_certif);
        }

        // raz
        $lcode_item=referentiel_initialise_items($refrefid, '0');
        if (!empty($form->item_oblig)){
                //echo "<br />\n";
                //print_object($form->item_oblig);
                // DEBUG
                // echo "<br />LISTE AVANT MODIF :: $lcode_item\n";
                foreach($form->item_oblig as $code){
                    // DEBUG
                    //echo "<br />".$code;
                    $lcode_item=str_replace($code.':0/',$code.':1/',$lcode_item);
                }
                // DEBUG
                //echo "<br />LISTE MODIFIEE :: $lcode_item\n";
                // referentiel_set_item_oblig($refrefid, $lcode_item);
        }
        $protocole->l_items_oblig=$lcode_item;

        // raz
        $lcode_competence=referentiel_initialise_competences($refrefid, '0');
        if (!empty($form->comp_oblig)){
                //echo "<br />\n";
                //print_object($form->comp_oblig);
                // DEBUG
                //echo "<br />LISTE AVANT MODIF :: $lcode_competence\n";
                foreach($form->comp_oblig as $code){
                    // DEBUG
                    //echo "<br />".$code;
                    $lcode_competence=str_replace($code.':0/',$code.':1/',$lcode_competence);
                }
                // DEBUG
                //echo "<br />LISTE MODIFIEE :: $lcode_competence\n";
        }
        $protocole->l_competences_oblig=$lcode_competence;

        // raz
        $lcode_domaine=referentiel_initialise_domaines($refrefid, '0');
        if (!empty($form->dom_oblig)){
                //echo "<br />\n";
                //print_object($form->dom_oblig);
                // DEBUG
                //echo "<br />LISTE AVANT MODIF :: $lcode_domaine\n";
                foreach($form->dom_oblig as $code){
                    // DEBUG
                    //echo "<br />".$code;
                    $lcode_domaine=str_replace($code.':0/',$code.':1/',$lcode_domaine);
                }
                // DEBUG
                //echo "<br />LISTE MODIFIEE :: $lcode_domaine\n";
        }
        $protocole->l_domaines_oblig=$lcode_domaine;
        
        
        if (!empty($form->comp_seuil)){
                //echo "<br />\n";
                //print_object($form->comp_seuil);
                $lseuil_competence='';
                foreach($form->comp_seuil as $code => $value){
                    // DEBUG
                    //echo "<br />".$code." =&gt; ".$value."\n";
                    $lseuil_competence.=$code.':'.$value.'/';
                }
                $protocole->l_seuils_competences=$lseuil_competence;
        }
        
        if (!empty($form->dom_seuil)){
                //echo "<br />\n";
                //print_object($form->dom_seuil);
                $lseuil_domaine='';
                foreach($form->dom_seuil as $code => $value){
                    // DEBUG
                    //echo "<br />".$code." =&gt; ".$value."\n";
                    $lseuil_domaine.=$code.':'.$value.'/';
                }
                $protocole->l_seuils_domaines=$lseuil_domaine;
        }
        
        if (!empty($form->comp_minima)){
                //echo "<br />\n";
                //print_object($form->comp_minima);
                $lminima_competence='';
                foreach($form->comp_minima as $code => $value){
                    // DEBUG
                    //echo "<br />".$code." =&gt; ".$value."\n";
                    $lminima_competence.=$code.':'.$value.'/';
                }
                $protocole->l_minimas_competences=$lminima_competence;
        }

        if (!empty($form->dom_minima)){
                //echo "<br />\n";
                //print_object($form->dom_minima);
                $lminima_domaine='';
                foreach($form->dom_minima as $code => $value){
                    // DEBUG
                    //echo "<br />".$code." =&gt; ".$value."\n";
                    $lminima_domaine.=$code.':'.$value.'/';
                }
                $protocole->l_minimas_domaines=$lminima_domaine;
        }

        if (isset($form->protocole_actif)){
            $protocole->actif = $form->protocole_actif;
        }
        if ($protocole){
            $protocole->timemodified=time();

            if ($DB->update_record("referentiel_protocol", $protocole)){
                // mettre a jour les tables domaine, competence, item
                referentiel_set_domaine_competence_item($protocole);
            }
        }
    }
}


/**
 * Function initialize field table referentiel_referentiel
 * @input referentiel_referentiel id
 * @input seuil int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_set_seuil_certification($refrefid, $seuil){
global $DB;
    if (!empty($refrefid)){
        $DB->set_field("referentiel_referentiel", "seuil_certificat", $seuil, array("id" => $refrefid));
    }
}

/**
 * Function initialize field table referentiel_referentiel
 * @input referentiel_referentiel id
 * @input seuil int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_set_minima_certification($refrefid, $minima){
global $DB;
    if (!empty($refrefid)){
        $DB->set_field("referentiel_referentiel", "minima_certificat", $minima, array("id" => $refrefid));
    }
}


/**
 * Function initialize field table referentiel_protocol
 * @input referentiel_referentiel id
 * @input old_code, new_code string
 * @input seuil int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_update_domaine_protocole($oldcode, $newcode, $refrefid, $type_domaine, $seuil_domaine, $minima_domaine){
global $DB;
    if (!empty($refrefid)){
        $protocole=referentiel_get_protocol($refrefid);
        if ($protocole){
            if ($protocole->l_domaines_oblig){
                $protocole->l_domaines_oblig=preg_replace('/'.preg_quote($oldcode).':\d+\//', $newcode.':'.$type_domaine.'/', $protocole->l_domaines_oblig);
            }
            if ($protocole->l_seuils_domaines){
                $protocole->l_seuils_domaines=preg_replace('/'.preg_quote($oldcode).':\d+\.?\d*\//', $newcode.':'.$seuil_domaine.'/', $protocole->l_seuils_domaines);
            }
            if ($protocole->l_minimas_domaines){
                //$protocole->l_minimas_domaines=str_replace($oldcode, $newcode, $protocole->l_minimas_competences);
                $protocole->l_minimas_domaines=preg_replace('/'.preg_quote($oldcode).':\d+\//', $newcode.':'.$minima_domaine.'/', $protocole->l_minimas_domaines);
            }
            $protocole->timemodified=time();
            return $DB->update_record("referentiel_protocol", $protocole);
        }
    }
}

/**
 * Function initialize field domaine of table referentiel_protocol
 * @input referentiel_referentiel id
 * @input new_code string
 * @input position int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_add_domaine_protocole($domaine, $refrefid){
global $DB;
    if (!empty($refrefid) && !empty($domaine) ){
        $protocole=referentiel_get_protocol($refrefid);
        // DEBUG
        // echo "<br />DEBUG :: lib_protocole.php :: 1172 :: PROTOCOLE<br />\n";
        // print_object($protocole);
        // echo "<br />\n";

        if ($protocole){
            if ($protocole->l_domaines_oblig){
                $protocole->l_domaines_oblig.=$domaine->code_domaine.':'.$domaine->type_domaine.'/';
            }
            else{
                $protocole->l_domaines_oblig=$domaine->code_domaine.':'.$domaine->type_domaine.'/';
            }
            if ($protocole->l_seuils_domaines){
                $protocole->l_seuils_domaines.=$domaine->code_domaine.':'.$domaine->seuil_domaine.'/';
            }
            else{
                $protocole->l_seuils_domaines=$domaine->code_domaine.':'.$domaine->seuil_domaine.'/';
            }
            if ($protocole->l_minimas_domaines){
                $protocole->l_minimas_domaines.=$domaine->code_domaine.':'.$domaine->minima_domaine.'/';
            }
            else{
                $protocole->l_minimas_domaines=$domaine->code_domaine.':'.$domaine->minima_domaine.'/';
            }

            $protocole->timemodified=time();
            // DEBUG
            // echo "<br />DEBUG :: lib_protocole.php :: 1198 :: PROTOCOLE<br />\n";
            // print_object($protocole);
            // echo "<br />\n";
            return $DB->update_record("referentiel_protocol", $protocole);
        }
    }
}


/**
 * Function initialize field table referentiel_protocol
 * @input referentiel_referentiel id
 * @input old_code, new_code string
 * @input seuil int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_update_competence_protocole($oldcode, $newcode, $refrefid, $type_competence, $seuil_competence, $minima_competence){
global $DB;
    if (!empty($refrefid)){
        $protocole=referentiel_get_protocol($refrefid);
        if ($protocole){
            if ($protocole->l_competences_oblig){
                // $protocole->l_competences_oblig=str_replace($oldcode, $newcode, $protocole->l_competences_oblig);
                $protocole->l_competences_oblig=preg_replace('/'.preg_quote($oldcode).':\d+\//', $newcode.':'.$type_competence.'/',  $protocole->l_competences_oblig);
            }
            if ($protocole->l_seuils_competences){
                //$protocole->l_seuils_competences=str_replace($oldcode, $newcode, $protocole->l_seuils_competences);
                $protocole->l_seuils_competences=preg_replace('/'.preg_quote($oldcode).':\d+\.?\d*\//', $newcode.':'.$seuil_competence.'/', $protocole->l_seuils_competences);
            }
            if ($protocole->l_minimas_competences){
                //$protocole->l_seuils_competences=str_replace($oldcode, $newcode, $protocole->l_seuils_competences);
                $protocole->l_minimas_competences=preg_replace('/'.preg_quote($oldcode).':\d+\//', $newcode.':'.$minima_competence.'/', $protocole->l_minimas_competences);
            }
            $protocole->timemodified=time();
            return $DB->update_record("referentiel_protocol", $protocole);
        }
    }
}


/**
 * Function initialize field table referentiel_protocol
 * @input referentiel_referentiel id
 * @input new_code string
 * @input position int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_add_competence_protocole($competence, $refrefid){
global $DB;
     if (!empty($refrefid) && !empty($competence) ){
        // DEBUG
        // echo "<br />DEBUG :: lib_protocole :: 1236 :: COMPETECE A CREER<br />\n";
        // print_object($competence);
        // echo "<br />\n";
        $protocole=referentiel_get_protocol($refrefid);

        if ($protocole){
            if ($protocole->l_competences_oblig){
                $protocole->l_competences_oblig.=$competence->code_competence.':'.$competence->type_competence.'/';
            }
            else{
                $protocole->l_competences_oblig=$competence->code_competence.':'.$competence->type_competence.'/';
            }
            if ($protocole->l_seuils_competences){
                $protocole->l_seuils_competences.=$competence->code_competence.':'.$competence->seuil_competence.'/';
            }
            else{
                $protocole->l_seuils_competences=$competence->code_competence.':'.$competence->seuil_competence.'/';
            }
            if ($protocole->l_minimas_competences){
                $protocole->l_minimas_competences.=$competence->code_competence.':'.$competence->minima_competence.'/';
            }
            else{
                $protocole->l_minimas_competences=$competence->code_competence.':'.$competence->minima_competence.'/';
            }

            $protocole->timemodified=time();
            return $DB->update_record("referentiel_protocol", $protocole);
        }
    }
}


/**
 * Function initialize field table referentiel_protocol
 * @input referentiel_referentiel id
 * @input old_code, new_code string
 * @input seuil int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_update_item_protocole($oldcode, $newcode, $refrefid, $type_item){
global $DB;
    if (!empty($refrefid)){
        $protocole=referentiel_get_protocol($refrefid);
        if ($protocole){
            if ($protocole->l_items_oblig){
                $protocole->l_items_oblig=preg_replace('/'.preg_quote($oldcode).':\d\//',$newcode.':'.$type_item.'/', $protocole->l_items_oblig);
            }
            $protocole->timemodified=time();
            return $DB->update_record("referentiel_protocol", $protocole);
        }
    }
}

/**
 * Function initialize field table referentiel_protocol
 * @input referentiel_referentiel id
 * @input new_code string
 * @input position int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_add_item_protocole($newcode, $refrefid, $type_item){
global $DB;
    if (!empty($refrefid) && !empty($newcode) ){
        $protocole=referentiel_get_protocol($refrefid);
        if ($protocole){
            if ($protocole->l_items_oblig){
                $protocole->l_items_oblig.=$newcode.':'.$type_item.'/';
            }
            else{
                $protocole->l_items_oblig.=$newcode.':'.$type_item.'/';
            }
            $protocole->timemodified=time();
            return $DB->update_record("referentiel_protocol", $protocole);
            
        }
    }
}



/**
 * Function initialize field table referentiel_protocol
 * @input referentiel_referentiel id
 * @input new_code string
 * @input position int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_delete_item_protocole($code, $refrefid){
global $DB;
    if (!empty($refrefid) && !empty($code) ){
        $protocole=referentiel_get_protocol($refrefid);
        if ($protocole){
            $l_items_oblig='';
            if ($protocole->l_items_oblig){
                $l_items_oblig=$protocole->l_items_oblig;
                $l_items_oblig=preg_replace('/'.preg_quote($code).':\d\//','',$l_items_oblig);
            }

            if ($l_items_oblig){
                $protocole->l_items_oblig=$l_items_oblig;
                $protocole->timemodified=time();

               return $DB->update_record("referentiel_protocol", $protocole);
            }
        }
    }
}


/**
 * Function initialize field table referentiel_protocol
 * @input referentiel_referentiel id
 * @input new_code string
 * @input position int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_delete_competence_protocole($code, $refrefid){
global $DB;
    if (!empty($refrefid) && !empty($code) ){
        $protocole=referentiel_get_protocol($refrefid);
        if ($protocole){
            if ($protocole->l_competences_oblig){
                $protocole->l_competences_oblig=preg_replace('/'.preg_quote($code).':\d\//','',$protocole->l_competences_oblig);
            }

            if ($protocole->l_seuils_competences){
                $protocole->l_seuils_competences=preg_replace('/'.preg_quote($code).':\d+\.?\d*\//','',$protocole->l_seuils_competences);
            }

            if ($protocole->l_minimas_competences){
                $protocole->l_minimas_competences=preg_replace('/'.preg_quote($code).':\d+\//','',$protocole->l_minimas_competences);
            }

            $protocole->timemodified=time();
            return $DB->update_record("referentiel_protocol", $protocole);
        }
    }
}

/**
 * Function initialize field table referentiel_protocol
 * @input referentiel_referentiel id
 * @input new_code string
 * @input position int
 * @return true or false
 * @todo Finish documenting this function
**/
function referentiel_delete_domaine_protocole($code, $refrefid){
global $DB;
    if (!empty($refrefid) && !empty($code) ){
        $protocole=referentiel_get_protocol($refrefid);
        if ($protocole){
            if ($protocole->l_domaines_oblig){
                $protocole->l_domaines_oblig=preg_replace('/'.preg_quote($code).':\d\//','',$protocole->l_domaines_oblig);
            }

            if ($protocole->l_seuils_domaines){
                $protocole->l_seuils_domaines=preg_replace('/'.preg_quote($code).':\d+\.?\d*\//','',$protocole->l_seuils_domaines);
            }

            if ($protocole->l_minimas_domaines){
                $protocole->l_minimas_domaines=preg_replace('/'.preg_quote($code).':\d+\//','',$protocole->l_minimas_domaines);
            }

            $protocole->timemodified=time();
            return $DB->update_record("referentiel_protocol", $protocole);
        }
    }
}


/**
 * Function initialize field table referentiel_domaine
 * @input referentiel_domaine id
 * @input type_competence int
 * @todo Finish documenting this function
**/

function referentiel_set_type_domaine($id, $type){
global $DB;
    $DB->set_field("referentiel_domaine", "type_domaine", $type, array("id" => $id));
}

/**
 * Function initialize field table referentiel_domaine
 * @input referentiel_domaine id
 * @input type_competence int
 * @todo Finish documenting this function
**/

function referentiel_set_seuil_domaine($id, $seuil){
global $DB;
    $DB->set_field("referentiel_domaine", "seuil_domaine", $seuil, array("id" => $id));
}

/**
 * Function initialize field table referentiel_domaine
 * @input referentiel_domaine id
 * @input type_competence int
 * @todo Finish documenting this function
**/

function referentiel_set_minima_domaine($id, $minima){
global $DB;
    $DB->set_field("referentiel_domaine", "minima_domaine", $minima, array("id" => $id));
}

/**
 * Function initialize field table referentiel_competence
 * @input referentiel_domaine id
 * @input type_competence int
 * @todo Finish documenting this function
**/

function referentiel_set_type_competence($id, $type){
global $DB;
    $DB->set_field("referentiel_competence", "type_competence", $type, array("id" => $id));
}

/**
 * Function initialize field table referentiel_competence
 * @input referentiel_domaine id
 * @input type_competence int
 * @todo Finish documenting this function
**/

function referentiel_set_seuil_competence($id, $seuil){
global $DB;
    $DB->set_field("referentiel_competence", "seuil_competence", $seuil, array("id" => $id));
}

function referentiel_set_minima_competence($id, $minima){
global $DB;
    $DB->set_field("referentiel_competence", "minima_competence", $minima, array("id" => $id));
}


/**
 * Function initialize field table referentiel_competence
 * @input referentiel_domaine id
 * @input type_competence int
 * @todo Finish documenting this function
**/

function referentiel_set_type_item($refrefid, $code, $type){
global $DB;
    $DB->set_field("referentiel_item_competence", "type_item", $type, array("ref_referentiel" => $refrefid, "code_item" => $code));
}


?>
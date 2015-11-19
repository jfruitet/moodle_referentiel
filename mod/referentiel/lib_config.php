<?php
/// CONFIGURATION
// inclus dans lib.php
	/**
 * Given an object containing all the necessary configuration data,
 * this function
 * will update an existing record.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail

 CREATE TABLE `mdl_referentiel` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '',
  `description_instance` text NOT NULL,
  `label_domaine` varchar(80) NOT NULL DEFAULT '',
  `label_competence` varchar(80) NOT NULL DEFAULT '',
  `label_item` varchar(80) NOT NULL DEFAULT '',
  `date_instance` bigint(10) unsigned NOT NULL DEFAULT '0',
  `course` bigint(10) unsigned NOT NULL DEFAULT '0',
  `ref_referentiel` bigint(10) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `config` varchar(255) NOT NULL,
  `config_impression` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Instance de referentiel de competence';



CREATE TABLE IF NOT EXISTS `mdl_referentiel_referentiel` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '',
  `code_referentiel` varchar(20) NOT NULL DEFAULT '',
  `mail_auteur_referentiel` varchar(255) NOT NULL DEFAULT '',
  `cle_referentiel` varchar(255) NOT NULL DEFAULT '',
  `pass_referentiel` varchar(255) NOT NULL DEFAULT '',
  `description_referentiel` text NOT NULL,
  `url_referentiel` varchar(255) NOT NULL DEFAULT '',
  `seuil_certificat` double NOT NULL DEFAULT '0',
  `timemodified` bigint(10) unsigned NOT NULL DEFAULT '0',
  `nb_domaines` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `liste_codes_competence` text NOT NULL,
  `liste_empreintes_competence` text NOT NULL,
  `liste_poids_competence` text NOT NULL,
  `local` bigint(10) unsigned NOT NULL DEFAULT '0',
  `logo_referentiel` varchar(255) NOT NULL DEFAULT '',
  `config` varchar(255) NOT NULL DEFAULT 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;',
  `config_impression` varchar(255) NOT NULL DEFAULT 'refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Referentiel de competence' AUTO_INCREMENT=51 ;

 */

 // -----------------------
function referentiel_can_config_certification($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, du referentiel, de l'instance
// verifier si autorisation de modification de la configuration
// au niveau de l'instance
    global $CFG;
    $config_certif=1;

    $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);

	// valeur de la configuration globale au niveau du site
    if (!isset($CFG->referentiel_certif_config)){
		$CFG->referentiel_certif_config=0;
	}

	if ($CFG->referentiel_certif_config!=2){
        /// verifier valeur au niveau de l'occurrence
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('cfcertif', $referentiel_referentiel_id, 'config')==0){
                /// retourner valeur au niveau de l'instance
                $config_certif=(referentiel_get_item_configuration('cfcertif', $referentiel_instance_id, 'config')==0);
            }
        }
    }
    return  ($config_certif);
}


// -----------------------
function referentiel_get_certification_active($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, du referentiel, de l'instance
// initialise et returne la valeur $CFG->referentiel_certif_state
// au niveau de l'instance
    global $CFG;
    $certif=1; // par defaut
    $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);

	// configuration
    if (!isset($CFG->referentiel_certif_config)){
		$CFG->referentiel_certif_config=0;
	}

    if (!isset($CFG->referentiel_certif_state)){
		$CFG->referentiel_certif_state=1;    // certification active par defaut
	}

	if ($CFG->referentiel_certif_config==2){  // c'est le choix de l'administrateur qui s'impose
		$certif=$CFG->referentiel_certif_state;
	}
	else{
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('cfcertif', $referentiel_referentiel_id, 'config')>=1){    // choix au niveau de l'occurrence de referentiel qui s'impose
                $certif=referentiel_ref_get_item_config('certif', $referentiel_referentiel_id, 'config');
			}
			else{   // choix au niveau de l'instance
				$certif=referentiel_get_item_configuration('certif', $referentiel_instance_id, 'config');
            }
        }
    }
	return $certif;
}




// -----------------------
function referentiel_hierarchical_display($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, du referentiel, de l'instance
// return 1 pour affichage hierarchique, 0 sinon
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_hierarchy)){
		$CFG->referentiel_hierarchy=0;  // autoriser la configuration locale
	}
    $hierarchy=0; // affichage non hierarchique par defaut

    $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);
	if ($CFG->referentiel_hierarchy!=2){
		/// verifier valeur globale
		if ($referentiel_referentiel_id){
			$hierarchy=referentiel_ref_get_item_config('hierarchy', $referentiel_referentiel_id, 'config');
			if ($hierarchy==0){
				/// retourner valeur locale
        		$hierarchy=referentiel_get_item_configuration('hierarchy', $referentiel_instance_id, 'config');
			}
		}
    }

	return $hierarchy;
}



// -----------------------
function referentiel_site_can_config_referentiel($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, de l'occurrence
// verifier si autorisation de modification de la configuration
// au niveau de l'instance
    global $CFG;
    $config_creref=0;
    $config_selref=0;
    $config_graph=0;
    $config_light=0;
    $config_hierarchy=0;

    $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);

	// configuration
    if (!isset($CFG->referentiel_creation_limitee)){
		$CFG->referentiel_creation_limitee=0;
	}
	// configuration
    if (!isset($CFG->referentiel_selection_autorisee)){
		$CFG->referentiel_selection_autorisee=0;
	}
	// configuration
    if (!isset($CFG->referentiel_affichage_graphique)){
		$CFG->referentiel_affichage_graphique=0;
	}
    // configuration
    if (!isset($CFG->referentiel_light_display)){
		$CFG->referentiel_light_display=0;
    }

    // configuration
    if (!isset($CFG->referentiel_hierarchy)){
		$CFG->referentiel_hierarchy=0;
    }

	if ($CFG->referentiel_creation_limitee!=2){
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('creref', $referentiel_referentiel_id, 'config')==0){
                /// retourner valeur locale
                // $config_creref=(referentiel_get_item_configuration('creref', $referentiel_instance_id, 'config')==0);
                $config_creref=1;
            }
        }
    }


	if ($CFG->referentiel_selection_autorisee!=2) {
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('selref', $referentiel_referentiel_id, 'config')==0){
            	//$config_selref=(referentiel_get_item_configuration('selref', $referentiel_instance_id, 'config')==0);
                $config_selref=1;
            }
        }
    }
	if ($CFG->referentiel_affichage_graphique!=2) {
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('graph', $referentiel_referentiel_id, 'config')==0){
            	/// renvoyer valeur locale
                //$config_affgraph=(referentiel_get_item_configuration('graph', $referentiel_instance_id, 'config')==0);
                $config_graph=1;
            }
        }
    }
	if ($CFG->referentiel_light_display!=2) {
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('light', $referentiel_referentiel_id, 'config')==0){
            	/// renvoyer valeur locale
                // $config_light=(referentiel_get_item_configuration('light', $referentiel_instance_id, 'config')==0);
                $config_light=1;
            }
        }
    }
	if ($CFG->referentiel_hierarchy!=2) {
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
			// ATTENTION : test inverted
			if (referentiel_ref_get_item_config('hierarchy', $referentiel_referentiel_id, 'config')==0){
            	/// renvoyer valeur locale
                //$config_hierarchy=(referentiel_get_item_configuration('hierarchy', $referentiel_instance_id, 'config')==0);
                $config_hierarchy=1;
            }
        }
    }
	// DEBUG
	//echo "<br />lib_config.php :: 170 :: CREREF:$config_creref SELREF: $config_selref AFFGRAPH: $config_affgraph LIGTH: $config_light HIERRCHY: $hierarchy\n";
    //exit;
	return  ($config_creref && $config_selref && $config_graph && $config_light && $config_hierarchy);
}






 // -----------------------------
function referentiel_affiche_config($str_config, $type='config'){
// item = 'scol', 'creref', 'selref', 'impcert', refcert, instcert, numetu, nometu, etabetu, ddnetu, lieuetu, adretu, pourcent, compdec, compval, referent, jurycert, comcert,
// 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
// retourne une liste de selecteurs
// $type : config ou config_impression
global $CFG;
	$s='';
	if ($str_config==''){
		$str_config=referentiel_creer_configuration($type);
	}
	// DEBUG
	// echo "<br />DEBUG :: lib.php :: 3675 ::  $str_config\n";
	if ($str_config!=''){
		$tconfig=explode(';',$str_config);
		$n=count($tconfig);
		if ($n>0){
			$i=0;
			while ($i<$n){
				$tconfig[$i]=trim($tconfig[$i]);
				if ($tconfig[$i]!=''){
					list($cle, $val)=explode(':',$tconfig[$i]);
					$cle=trim($cle);
					$val=trim($val);
					if ($cle!=''){

						$s.=''.get_string($cle,'referentiel').' ';
						$str_conf=referentiel_associe_item_configuration($cle);
						// creer le parametre si necessaire
						if (!isset($CFG->$str_conf)){
							$CFG->$str_conf=0;
						}
						if ($CFG->$str_conf==2){
							$s.= ' (<i>'.$cle.'</i>) <b>'.get_string('config_verrouillee','referentiel').'</b>'."\n";
 						}
						elseif ($val==1){
							$s.=' (<i>'.$cle.'</i>) <b>'.get_string('yes')."</b>\n";
 						}
						else {
							$s.=' (<i>'.$cle.'</i>) <b>'.get_string('no')."</b>\n";
						}
						$s.='<br />'."\n";
					}
				}
				$i++;
			}
		}
	}
	return $s;
}

// -----------------------
function referentiel_is_author($userid, $referentiel_referentiel, $is_not_student=true){
    // return true if userid is refrentiel_referentiel author
	return ($is_not_student==true && !empty($referentiel_referentiel->mail_auteur_referentiel)
        && ($referentiel_referentiel->mail_auteur_referentiel==referentiel_get_user_mail($userid)));
}


// -----------------------
function referentiel_instance_get_referentiel($instanceid){
// retourne l'id de l'occurrence de referentiel associée à une instance
    global $DB;
    if ($instanceid){
        $params= array("refid" => "$instanceid");
        $sql="SELECT ref_referentiel FROM {referentiel} WHERE id=:refid";
		$instance=$DB->get_record_sql($sql, $params);
		if ($instance){
            return $instance->ref_referentiel;
        }
    }
    return 0;
}

// -----------------------
function referentiel_global_can_write_or_import_ref($referentiel_referentiel_id) {
// examine en cascade la configuration au niveau du site, du referentiel
// verifier si autorisation de creation de referentiel au niveau des cours
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_creation_limitee)){
		$CFG->referentiel_creation_limitee=0;
	}

	if ($CFG->referentiel_creation_limitee!=2){
        if ($referentiel_referentiel_id){
            return (referentiel_ref_get_item_config('creref', $referentiel_referentiel_id, 'config')==0);
        }
    }
    return false;
}

// -----------------------
function referentiel_global_can_select_referentiel($referentiel_referentiel_id) {
// examine en cascade la configuration au niveau du site, du referentiel
// verifier si autorisation de selection d'un referentiel existant au niveau des cours
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_selection_autorisee)){
		$CFG->referentiel_selection_autorisee=0;
	}
	if ($CFG->referentiel_selection_autorisee!=2) {
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            return(referentiel_ref_get_item_config('selref', $referentiel_referentiel_id, 'config')==0);
        }
    }
    return false;
}



// -----------------------
function referentiel_global_can_print_referentiel($referentiel_referentiel_id) {
// examine en cascade la configuration au niveau du site, du referentiel
// verifier si autorisation d'impression d'un certificat au niveau des cours
    global $CFG;
	// configuration
    if (!isset($CFG->impression_referentiel_autorisee)){
		$CFG->impression_referentiel_autorisee=0;
	}
	if ($CFG->impression_referentiel_autorisee!=2) {
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            return(referentiel_ref_get_item_config('impcert', $referentiel_referentiel_id, 'config')==0);
        }
    }
    return false;
}


// -----------------------
function referentiel_site_can_write_or_import_referentiel($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, du referentiel, de l'instance
// verifier si autorisation de creation de referentiel au niveau des cours
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_creation_limitee)){
		$CFG->referentiel_creation_limitee=0;
	}

	if ($CFG->referentiel_creation_limitee!=2){
        /// verifier valeur globale au niveau du referentiel
        $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('creref', $referentiel_referentiel_id, 'config')==0){
                /// retourner valeur locale
                return (referentiel_get_item_configuration('creref', $referentiel_instance_id, 'config')==0);
            }
        }
    }
    return false;
}

// -----------------------
function referentiel_site_can_select_referentiel($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, du referentiel, de l'instance
// verifier si autorisation de selection d'un referentiel existant au niveau des cours
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_selection_autorisee)){
		$CFG->referentiel_selection_autorisee=0;
	}
	if ($CFG->referentiel_selection_autorisee!=2) {
        /// verifier valeur globale
        $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('selref', $referentiel_referentiel_id, 'config')==0){
            	/// renvoyer valeur locale
                return (referentiel_get_item_configuration('selref', $referentiel_instance_id, 'config')==0);
            }
        }
    }
    return false;
}

// -----------------------
function referentiel_global_can_print_graph($referentiel_referentiel_id) {
// examine en cascade la configuration au niveau du site, du referentiel
// verifier si autorisation de selection d'un referentiel existant au niveau des cours
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_affichage_graphique)){
		$CFG->referentiel_affichage_graphique=0;
	}
	if ($CFG->referentiel_affichage_graphique!=2) {
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            return(referentiel_ref_get_item_config('graph', $referentiel_referentiel_id, 'config')==0);
        }
    }
    return false;
}

// -----------------------
function referentiel_site_can_print_graph($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, du referentiel, de l'instance
// verifier si autorisation de selection d'un referentiel existant au niveau des cours
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_affichage_graphique)){
		$CFG->referentiel_affichage_graphique=0;
	}
	if ($CFG->referentiel_affichage_graphique!=2) {
        /// verifier valeur globale
        $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('graph', $referentiel_referentiel_id, 'config')==0){
            	/// renvoyer valeur locale
                return (referentiel_get_item_configuration('graph', $referentiel_instance_id, 'config')==0);
            }
        }
    }
    return false;
}

// -----------------------
function referentiel_global_hierarchy($referentiel_referentiel_id) {
// examine en cascade la configuration au niveau du site, du referentiel
// verifier si affichage hierarchique
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_hierarchy)){
		$CFG->referentiel_hierarchy=0;
	}
	if ($CFG->referentiel_hierarchy!=2) {
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            return(referentiel_ref_get_item_config('hierarchy', $referentiel_referentiel_id, 'config')==0);
        }
    }
    return false;
}

// -----------------------
function referentiel_site_hierarchy($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, du referentiel, de l'instance
// verifier si affichage light du referentiel au niveau du site
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_hierarchy)){
		$CFG->referentiel_hierarchy=0;
	}
	if ($CFG->referentiel_hierarchy!=2) {
        /// verifier valeur globale
        $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('hierarchy', $referentiel_referentiel_id, 'config')==0){
            	/// renvoyer valeur locale
                return (referentiel_get_item_configuration('hierarchy', $referentiel_instance_id, 'config')==0);
            }
        }
    }
    return false;
}

// -----------------------
function referentiel_global_light_display($referentiel_referentiel_id) {
// examine en cascade la configuration au niveau du site, du referentiel
// verifier si affichage reduit du referentiel au niveau des cours
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_light_display)){
		$CFG->referentiel_light_display=0;
	}
	if ($CFG->referentiel_light_display!=2) {
        /// verifier valeur globale
        if ($referentiel_referentiel_id){
            return(referentiel_ref_get_item_config('light', $referentiel_referentiel_id, 'config')==0);
        }
    }
    return false;
}

// -----------------------
function referentiel_site_light_display($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, du referentiel, de l'instance
// verifier si affichage light du referentiel au niveau du site
    global $CFG;
	// configuration
    if (!isset($CFG->referentiel_light_display)){
		$CFG->referentiel_light_display=0;
	}
	if ($CFG->referentiel_light_display!=2) {
        /// verifier valeur globale
        $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('light', $referentiel_referentiel_id, 'config')==0){
            	/// renvoyer valeur locale
                return (referentiel_get_item_configuration('light', $referentiel_instance_id, 'config')==0);
            }
        }
    }
    return false;
}

// -----------------------
function referentiel_site_can_print_referentiel($referentiel_instance_id) {
// examine en cascade la configuration au niveau du site, du referentiel, de l'instance
// verifier si autorisation d'impression d'un certificat au niveau des cours
    global $CFG;
	// configuration
    if (!isset($CFG->impression_referentiel_autorisee)){
		$CFG->impression_referentiel_autorisee=0;
	}
	if ($CFG->impression_referentiel_autorisee!=2) {
        /// verifier valeur globale
        $referentiel_referentiel_id=referentiel_instance_get_referentiel($referentiel_instance_id);
        if ($referentiel_referentiel_id){
            if (referentiel_ref_get_item_config('impcert', $referentiel_referentiel_id, 'config')==0){
            	/// renvoyer valeur locale
                return (referentiel_get_item_configuration('impcert', $referentiel_instance_id, 'config')==0);
            }
        }
    }
    return false;
}

// -----------------------
function referentiel_associe_item_configuration($item){
// retourne le nom du parametre de configuration
		switch($item){
            case 'cfcertif' :  return 'referentiel_certif_config'; break; // config certification
            case 'certif' :  return 'referentiel_certif_state'; break; // certification active
            case 'hierarchy' :  return 'referentiel_hierarchy'; break; // affichage hierarchique des competences
            case 'light' :  return 'referentiel_light_display'; break; // affichage reduit du referentiel sans les poids et les empreintes
            case 'graph' :  return 'referentiel_affichage_graphique'; break;
			case 'scol' :	return 'referentiel_scolarite_masquee'; break;
			case 'creref' :	return 'referentiel_creation_limitee'; break;
			case 'selref' :	return 'referentiel_selection_autorisee'; break;
			case 'impcert' : return 'referentiel_impression_autorisee'; break;
			case 'refcert' : return 'certificat_sel_referentiel'; break;
			case 'instcert' : return 'certificat_sel_referentiel_instance'; break;
			case 'numetu' : return 'certificat_sel_etudiant_numero'; break;
			case 'nometu' : return 'certificat_sel_etudiant_nom_prenom'; break;
			case 'etabetu' : return 'certificat_sel_etudiant_etablissement'; break;
			case 'ddnetu' : return 'certificat_sel_etudiant_ddn'; break;
			case 'lieuetu' : return 'certificat_sel_etudiant_lieu_naissance'; break;
			case 'adretu' : return 'certificat_sel_etudiant_adresse'; break;
			case 'detail' : return 'certificat_sel_certificat_detail'; break;
			case 'pourcent' : return 'certificat_sel_certificat_pourcent'; break;
			case 'compdec' : return 'certificat_sel_activite_competences'; break;
			case 'compval' : return 'certificat_sel_certificat_competences'; break;
			case 'nomreferent' : return 'certificat_sel_certificat_referents'; break;
			case 'jurycert' : return 'certificat_sel_decision_jury'; break;
			case 'comcert' : return 'certificat_sel_commentaire'; break;
		}
		return '';
}

// -----------------------
function referentiel_creer_configuration($type='config'){
// initialise le vecteur de configuration
global $CFG;
$s='';
	if ($type=='config'){
		// configuration
        // affichage hierarchique saisie des competence
		$s.='hierarchy:0;';

        // affichage reduit du referentiel sans les poids et les empreintes
		if (isset($CFG->referentiel_light_display)){
			$s.='light:'.$CFG->referentiel_light_display.';';
		}
		else{
			$s.='light:0;';
		}

		if (isset($CFG->referentiel_scolarite_masquee)){
			$s.='scol:'.$CFG->referentiel_scolarite_masquee.';';
		}
		else{
			$s.='scol:0;';
		}
		if (isset($CFG->referentiel_creation_limitee)){
			$s.='creref:'.$CFG->referentiel_creation_limitee.';';
		}
		else{
			$s.='creref:0;';
		}
		if (isset($CFG->referentiel_selection_autorisee)){
			$s.='selref:'.$CFG->referentiel_selection_autorisee.';';
		}
		else{
			$s.='selref:0;';
		}
		if (isset($CFG->referentiel_impression_autorisee)){
			$s.='impcert:'.$CFG->referentiel_impression_autorisee.';';
		}
		else{
			$s.='impcert:0;';
		}
		if (isset($CFG->referentiel_affichage_graphique)){
			$s.='graph:'.$CFG->referentiel_affichage_graphique.';';
		}
		else{
			$s.='graph:0;';
		}
		if (isset($CFG->referentiel_certif_config)){
			$s.='cfcertif:'.$CFG->referentiel_certif_config.';';
		}
		else{
			$s.='cfcertif:0;';
		}
		if (isset($CFG->referentiel_certif_state)){
			$s.='certif:'.$CFG->referentiel_certif_state.';';
		}
		else{
			$s.='certif:1;';
		}
	}
	else{
		// impression certificat
		// instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;

		// impression certificat
		if (isset($CFG->certificat_sel_referentiel)){
			$s.='refcert:'.$CFG->certificat_sel_referentiel.';';
		}
		else{
			$s.='refcert:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_referentiel_instance)){
			$s.='instcert:'.$CFG->certificat_sel_referentiel_instance.';';
		}
		else{
			$s.='instcert:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_numero)){
			$s.='numetu:'.$CFG->certificat_sel_etudiant_numero.';';
		}
		else{
			$s.='numetu:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_nom_prenom)){
			$s.='nometu:'.$CFG->certificat_sel_etudiant_nom_prenom.';';
		}
		else{
			$s.='nometu:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_etablissement)){
			$s.='etabetu:'.$CFG->certificat_sel_etudiant_etablissement.';';
		}
		else{
			$s.='etabetu:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_ddn)){
			$s.='ddnetu:'.$CFG->certificat_sel_etudiant_ddn.';';
		}
		else{
			$s.='ddnetu:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_etudiant_lieu_naissance)){
			$s.='lieuetu:'.$CFG->certificat_sel_etudiant_lieu_naissance.';';
		}
		else{
			$s.='lieuetu:0;';
		}

				// impression certificat
		if (isset($CFG->certificat_sel_etudiant_adresse)){
			$s.='adretu:'.$CFG->certificat_sel_etudiant_adresse.';';
		}
		else{
			$s.='adretu:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_detail)){
			$s.='detail:'.$CFG->certificat_sel_certificat_detail.';';
		}
		else{
			$s.='detail:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_pourcent)){
			$s.='pourcent:'.$CFG->certificat_sel_certificat_pourcent.';';
		}
		else{
			$s.='pourcent:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_activite_competences)){
			$s.='compdec:'.$CFG->certificat_sel_activite_competences.';';
		}
		else{
			$s.='compdec:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_competences)){
			$s.='compval:'.$CFG->certificat_sel_certificat_competences.';';
		}
		else{
			$s.='compval:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_certificat_referents)){
			$s.='nomreferent:'.$CFG->certificat_sel_certificat_referents.';';
		}
		else{
			$s.='nomreferent:0;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_decision_jury)){
			$s.='jurycert:'.$CFG->certificat_sel_decision_jury.';';
		}
		else{
			$s.='jurycert:1;';
		}

		// impression certificat
		if (isset($CFG->certificat_sel_commentaire)){
			$s.='comcert:'.$CFG->certificat_sel_commentaire.';';
		}
		else{
			$s.='comcert:0;';
		}
	}
	return $s;
}


// ---------------------------------
function referentiel_global_set_vecteur_config($str_config, $referentiel_referentiel_id){
//  sauvegarde de la configuration globale
global $DB;
	$ok=false;
	if (!empty($referentiel_referentiel_id) && !empty($str_config)){
        $ok=$DB->set_field('referentiel_referentiel','config',$str_config, array("id" => "$referentiel_referentiel_id"));
	}
	return $ok;
}


// ---------------------------------
function referentiel_set_vecteur_configuration($str_config, $referentiel_instance_id){
//  sauvegarde de la configuration locale
global $DB;
	$ok=false;
	if (!empty($referentiel_instance_id) && !empty($str_config)){
        $ok=$DB->set_field('referentiel','config',$str_config, array("id" => "$referentiel_instance_id"));
	}
	return $ok;
}

// ---------------------------------
function referentiel_global_set_vecteur_config_imp($str_config, $referentiel_referentiel_id){
//  sauvegarde de la configuration d'impression globale
global $DB;
	$ok=false;
	if (!empty($referentiel_referentiel_id) && !empty($str_config)){
		$ok=$DB->set_field('referentiel_referentiel','config_impression',$str_config,array("id" => "$referentiel_referentiel_id"));
	}
	return $ok;
}

// ---------------------------------
function referentiel_set_vecteur_configuration_impression($str_config, $referentiel_instance_id){
//  sauvegarde de la configuration d'impression locale
global $DB;
	$ok=false;
	if (!empty($referentiel_instance_id) && !empty($str_config)){
		$ok=$DB->set_field('referentiel','config_impression',$str_config,array("id" => "$referentiel_instance_id"));
	}
	return $ok;
}


// ---------------------------------
function referentiel_ref_set_option_imp_certificat($referentiel_referentiel_id, $form){
//  sauvegarde de la configuration d'impression globale
// $form : un formulaire de saisie
	$ok=false;
	if (!empty($referentiel_referentiel_id) && !empty($form)){
		$str_config=referentiel_initialise_configuration($form, 'config_impression');
		return referentiel_global_set_vecteur_config_imp($str_config, $referentiel_referentiel_id);
	}
	return $ok;
}

// ---------------------------------
function referentiel_set_option_impression_certificat($referentiel_instance_id, $form){
//  sauvegarde de la configuration d'impression locale
// $form : un formulaire de saisie
	$ok=false;
	if (!empty($referentiel_instance_id) && !empty($form)){
		$str_config=referentiel_initialise_configuration($form, 'config_impression');
		return referentiel_set_vecteur_configuration_impression($str_config, $referentiel_instance_id);
	}
	return $ok;
}

// ---------------------------------
function referentiel_initialise_configuration($form, $type='config'){
// initialise le vecteur de configuration en fonction des parametres saisis dans le formulaire
// item type config = 'scol', 'creref', 'selref', 'impcert', 'graph', 'light', 'hierarchy'
// item type config_impression = 'refcert', 'instcert', 'numetu', nometu, etabetu, ddnetu, lieuetu, adretu, pourcent, compdec, compval, nomreferent, jurycert, comcert,
// Valeurs par defaut 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;
// Valeurs par defaut : refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;

$s='';
	if ($type=='config'){
		// affichage scolarite
		if (isset($form->scol)){
			$s.='scol:'.$form->scol.';';
		}
		else {
			$s.='scol:0;';
		}
		// creation referentiel
		if (isset($form->creref)){
			$s.='creref:'.$form->creref.';';
		}
		else{
			$s.='creref:0;';
		}
		// selection referentiel
		if (isset($form->selref)){
			$s.='selref:'.$form->selref.';';
		}
		else{
			$s.='selref:0;';
		}

		// impression certificat
		if (isset($form->impcert)){
			$s.='impcert:'.$form->impcert.';';
		}
		else{
			$s.='impcert:0;';
		}

		// graphique certification
		if (isset($form->graph)){
			$s.='graph:'.$form->graph.';';
		}
		else{
			$s.='graph:0;';
		}

		// affichage light  du referentiel
		if (isset($form->light)){
			$s.='light:'.$form->light.';';
		}
		else {
			$s.='light:0;';
		}
		// affichage hierarchique des competences
		if (isset($form->hierarchy)){
			$s.='hierarchy:'.$form->hierarchy.';';
		}
		else {
			$s.='hierarchy:0;';
		}
		// config certification
		if (isset($form->cfcertif)){
			$s.='cfcertif:'.$form->cfcertif.';';
		}
		else {
			$s.='cfcertif:0;';
		}
        // certification active
		if (isset($form->certif)){
			$s.='certif:'.$form->certif.';';
		}
		else {
			$s.='certif:1;';
		}
	}
	else{

		//Valeurs par defaut : refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;

		// impression certificat
		if (isset($form->refcert)){
			$s.='refcert:'.$form->refcert.';';
		}
		else{
			$s.='refcert:1;';
		}

				// impression certificat
		if (isset($form->instcert)){
			$s.='instcert:'.$form->instcert.';';
		}
		else{
			$s.='instcert:0;';
		}

		// impression certificat
		if (isset($form->numetu)){
			$s.='numetu:'.$form->numetu.';';
		}
		else{
			$s.='numetu:1;';
		}


		// impression certificat
		if (isset($form->nometu)){
			$s.='nometu:'.$form->nometu.';';
		}
		else{
			$s.='nometu:1;';
		}

		// impression certificat
		if (isset($form->etabetu)){
			$s.='etabetu:'.$form->etabetu.';';
		}
		else{
			$s.='etabetu:0;';
		}

		// impression certificat
		if (isset($form->ddnetu)){
			$s.='ddnetu:'.$form->ddnetu.';';
		}
		else{
			$s.='ddnetu:0;';
		}


		// impression certificat
		if (isset($form->lieuetu)){
			$s.='lieuetu:'.$form->lieuetu.';';
		}
		else{
			$s.='lieuetu:0;';
		}

		// impression certificat
		if (isset($form->adretu)){
			$s.='adretu:'.$form->adretu.';';
		}
		else{
			$s.='adretu:0;';
		}

		// impression certificat
		if (isset($form->detail)){
			$s.='detail:'.$form->detail.';';
		}
		else{
			$s.='detail:0;';
		}

		// impression certificat
		if (isset($form->pourcent)){
			$s.='pourcent:'.$form->pourcent.';';
		}
		else{
			$s.='pourcent:0;';
		}

		// impression certificat
		if (isset($form->compdec)){
			$s.='compdec:'.$form->compdec.';';
		}
		else{
			$s.='compdec:0;';
		}

		// impression certificat
		if (isset($form->compval)){
			$s.='compval:'.$form->compval.';';
		}
		else{
			$s.='compval:1;';
		}

		// impression certificat
		if (isset($form->nomreferent)){
			$s.='nomreferent:'.$form->nomreferent.';';
		}
		else{
			$s.='nomreferent:0;';
		}

		// impression certificat
		if (isset($form->jurycert)){
			$s.='jurycert:'.$form->jurycert.';';
		}
		else{
			$s.='jurycert:1;';
		}

		// impression certificat
		if (isset($form->comcer)){
			$s.='comcert:'.$form->comcer.';';
		}
		else{
			$s.='comcert:0;';
		}
	}
	return ($s);
}


// -----------------------------
function referentiel_selection_configuration($str_config, $type='config', $instance_id=0){
// item = 'scol', 'creref', 'selref', 'grap', 'light', 'impcert', refcert, instcert, numetu, nometu, etabetu, ddnetu, lieuetu, adretu, pourcent, compdec, compval, referent, jurycert, comcert,
// 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
// retourne une liste de selecteurs
// $type : config ou config_impression
global $CFG;
	$s='';
	if ($str_config==''){
		$str_config=referentiel_creer_configuration($type);
	}
	// DEBUG
	//echo "<br />DEBUG :: lib_config.php :: 1001 ::  $str_config\n";
	if ($str_config!=''){
		$tconfig=explode(';',$str_config);
		$n=count($tconfig);
		if ($n>0){
			$i=0;
			while ($i<$n){
				$tconfig[$i]=trim($tconfig[$i]);
				if ($tconfig[$i]!=''){
					list($cle, $val)=explode(':',$tconfig[$i]);
					$cle=trim($cle);
					$val=trim($val);
					if ($cle!=''){

						$str_conf=referentiel_associe_item_configuration($cle);
	// DEBUG
	//echo "<br />DEBUG :: lib_config.php :: 1018 ::  $str_conf\n";
				// creer le parametre si necessaire
						if (!isset($CFG->$str_conf)){
							$CFG->$str_conf=0;
						}
						// cas particulier pour la certification
						if ($cle=='cfcertif') {
                        	$s.=get_string('certif_config','referentiel').' ';
							if (!empty($instance_id)){
								// rechercher l'autorisation au niveau de l'instance
								if (referentiel_can_config_certification($instance_id)){
                                	$s.=get_string('ok_config_certif','referentiel').' ';
								}
								else{
                                    $s.=get_string('no_config_certif','referentiel').' ';
								}
 								$s.= "\n".'<input type="hidden" name="cfcertif" value="'.$val.'" />' ."\n";
							}
							else {
								if (isset($CFG->referentiel_certif_config) && ($CFG->referentiel_certif_config==2)){
                                    $s.=get_string('no_config_certif_occ','referentiel').' ';
									$s.= '<input type="hidden" name="'.$cle.'" value="2" /> <i>'.get_string('config_verrouillee','referentiel').'</i>'."\n";
								}
								else{
                                    if ($val!=0){
$s.='(<i>'.get_string('no_config_certif','referentiel').'</i>) ';
                                		$s.=get_string('yes_config_certif_locale','referentiel').' ';
										$s.=' <input type="radio" name="'.$cle.'" value="1" checked="checked" />'.get_string('no').'
 <input type="radio" name="'.$cle.'" value="0"  />'.get_string('yes')."\n";
									}
									else{
$s.='(<i>'.get_string('ok_config_certif','referentiel').'</i>) ';
                                		$s.=get_string('no_config_certif_locale','referentiel').' ';
										$s.=' <input type="radio" name="'.$cle.'" value="0"  checked="checked" />'.get_string('no').'
 <input type="radio" name="'.$cle.'" value="1"/>'.get_string('yes')."\n";
									}
								}
							}
						}
						elseif ($cle=='certif'){
                            $s.='&nbsp; &nbsp; &nbsp;';
							if (isset($CFG->referentiel_certif_config) && ($CFG->referentiel_certif_config==2)){
								if (!empty($CFG->referentiel_certif_state)) {
                                	$s.=get_string('certif_activee','referentiel');
								}
								else{
                                	$s.=get_string('certif_desactivee','referentiel');
								}
								// $s.=' <i>'.get_string('config_verrouillee','referentiel').'</i>'."\n";
                            	$s.= ' <input type="hidden" name="certif" value="'.$CFG->referentiel_certif_state.'" /> '."\n";
							}
							else{
								if ($val==0) {
                                	$s.='(<i>'.get_string('certif_desactivee','referentiel').'</i>) ';
                                	$s.=get_string('certif','referentiel').' ';
									$s.=' <input type="radio" name="'.$cle.'" value="0" checked="checked" />'.get_string('no').'
 <input type="radio" name="'.$cle.'" value="1" />'.get_string('yes')."\n";
								}
								else{
                                	$s.='(<i>'.get_string('certif_activee','referentiel').'</i>) ';
                                    $s.=get_string('nocertif','referentiel').' ';
									$s.=' <input type="radio" name="'.$cle.'" value="1"  checked="checked" />'.get_string('no').'
 <input type="radio" name="'.$cle.'" value="0" />'.get_string('yes')."\n";
								}
							}
						}
						else{
                        	$s.=''.get_string($cle,'referentiel').' ';
							if ($CFG->$str_conf==2){
								$s.= '<input type="hidden" name="'.$cle.'" value="2" /> <i>'.get_string('config_verrouillee','referentiel').'</i>'."\n";
 							}
							elseif ($val==1){
								$s.=' <input type="radio" name="'.$cle.'" value="0" />'.get_string('no').'
 <input type="radio" name="'.$cle.'" value="1"  checked="checked" />'.get_string('yes')."\n";
 							}
							else {
								$s.=' <input type="radio" name="'.$cle.'" value="0" checked="checked" />'.get_string('no').'
 <input type="radio" name="'.$cle.'" value="1" />'.get_string('yes')."\n";
							}
						}
						$s.='<br />'."\n";
					}
				}
				$i++;
			}
		}
	}
	$s.=' <input type="hidden" name="config" value="'.$str_config.'" />'."\n";
	return $s;
}


// ---------------------------------
function referentiel_ref_get_vecteur_config($ref_referentiel_referentiel) {
// retourne la valeur de configuration globale pour ce referentiel
global $DB;
	if (!empty($ref_referentiel_referentiel)){
		$config = new object();
		$params= array("refid" => "$ref_referentiel_referentiel");
		$sql="SELECT config FROM {referentiel_referentiel} WHERE id=:refid";
		$config = $DB->get_record_sql($sql, $params);
		if ($config){
			return($config->config);
		}
	}
	return '';
}


// ---------------------------------
function referentiel_get_vecteur_configuration($ref_instance_referentiel) {
// retourne la valeur de configuration locale pour cette instance de referentiel
global $DB;
	if (!empty($ref_instance_referentiel)){
		$config = new object();
		$params= array("refid" => "$ref_instance_referentiel");
        $sql="SELECT config FROM {referentiel} WHERE id=:refid";
		$config = $DB->get_record_sql($sql, $params);
		if ($config){
			return($config->config);
		}
	}
	return '';
}

// ---------------------------------
function referentiel_ref_get_vecteur_config_imp($ref_referentiel_referentiel) {
// retourne la valeur de configuration globale pour ce referentiel
global $DB;
	if (!empty($ref_referentiel_referentiel)){
		$config = new object();
		$params= array("refid" => "$ref_referentiel_referentiel");
        $sql="SELECT config_impression FROM {referentiel_referentiel} WHERE id=:refid";
        $config = $DB->get_record_sql($sql, $params);
		if ($config){
			return($config->config_impression);
		}
	}
	return '';
}


// ---------------------------------
function referentiel_get_vecteur_configuration_impression($ref_instance_referentiel) {
// retourne la valeur de configuration locale pour cette instance de referentiel
global $DB;
	if (!empty($ref_instance_referentiel)){
		$config = new object();
		$params= array("refid" => "$ref_instance_referentiel");
        $sql="SELECT config_impression FROM {referentiel} WHERE id=:refid";
        $config = $DB->get_record_sql($sql, $params);
		if ($config){
			return($config->config_impression);
		}
	}
	return '';
}

// ---------------------------------
function referentiel_ref_get_item_config($item, $ref_referentiel_referentiel, $type='config') {
// retourne la valeur de configuration globale (au niveau du referentiel) pour l'item considere
// 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
// type : config ou config_impression
global $CFG;
	if (isset($ref_referentiel_referentiel) && ($ref_referentiel_referentiel>0)){
		if ($type=='config'){
			$str_config = referentiel_ref_get_vecteur_config($ref_referentiel_referentiel);
		}
		else{
			$str_config = referentiel_ref_get_vecteur_config_imp($ref_referentiel_referentiel);
		}
		if ($str_config!=''){
			$tconfig=explode(';',$str_config);
			$n=count($tconfig);
			if ($n>0){
				$i=0;
				while ($i<$n){
					$tconfig[$i]=trim($tconfig[$i]);
					if ($tconfig[$i]!=''){
						list($cle, $val)=explode(':',$tconfig[$i]);
						$cle=trim($cle);
						$val=trim($val);

						if ($cle==$item){
							return ($val);
						}
					}
					$i++;
				}
			}
		}
	}
	return 0;
}

// ---------------------------------
function referentiel_get_item_configuration($item, $ref_instance_referentiel, $type='config') {
// retourne la valeur de configuration locale pour l'item considere
// 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
// type : config ou config_impression
global $CFG;
	if (isset($ref_instance_referentiel) && ($ref_instance_referentiel>0)){
		if ($type=='config'){
			$str_config = referentiel_get_vecteur_configuration($ref_instance_referentiel);
		}
		else{
			$str_config = referentiel_get_vecteur_configuration_impression($ref_instance_referentiel);
		}
		if ($str_config!=''){
			$tconfig=explode(';',$str_config);
			$n=count($tconfig);
			if ($n>0){
				$i=0;
				while ($i<$n){
					$tconfig[$i]=trim($tconfig[$i]);
					if ($tconfig[$i]!=''){
						list($cle, $val)=explode(':',$tconfig[$i]);
						$cle=trim($cle);
						$val=trim($val);

						if ($cle==$item){
							return ($val);
						}
					}
					$i++;
				}
			}
		}
	}
	return 0;
}


// -----------------------
function referentiel_associe_item_param_configuration($param, $item, $value){
// retourne un objet intitialisé
		switch($item){
		// type config
			case 'scol' :	$param->referentiel_scolarite_masquee=$value; break;
			case 'creref' :	$param->referentiel_creation_limitee=$value; break;
			case 'selref' :	$param->referentiel_selection_autorisee=$value; break;
			case 'impcert' : $param->referentiel_impression_autorisee=$value; break;
            case 'graph' : $param->referentiel_affichage_graphique=$value; break;
            case 'light' : $param->referentiel_light_display=$value; break;
            case 'hierarchy' : $param->referentiel_hierarchy=$value; break;
            case 'cfcertif' : $param->referentiel_certif_config=$value; break;
            case 'certif' : $param->referentiel_certif_state=$value; break;
		// type config_impression
			case 'refcert' : $param->certificat_sel_referentiel=$value; break;
			case 'instcert' : $param->certificat_sel_referentiel_instance=$value; break;
			case 'numetu' : $param->certificat_sel_etudiant_numero=$value; break;
			case 'nometu' : $param->certificat_sel_etudiant_nom_prenom=$value; break;
			case 'etabetu' : $param->certificat_sel_etudiant_etablissement=$value; break;
			case 'ddnetu' : $param->certificat_sel_etudiant_ddn=$value; break;
			case 'lieuetu' : $param->certificat_sel_etudiant_lieu_naissance=$value; break;
			case 'adretu' : $param->certificat_sel_etudiant_adresse=$value; break;
			case 'detail' : $param->certificat_sel_certificat_detail=$value; break;
			case 'pourcent' : $param->certificat_sel_certificat_pourcent=$value; break;
			case 'compdec' : $param->certificat_sel_activite_competences=$value; break;
			case 'compval' : $param->certificat_sel_certificat_competences=$value; break;
			case 'nomreferent' : $param->certificat_sel_certificat_referents=$value; break;
			case 'jurycert' : $param->certificat_sel_decision_jury=$value; break;
			case 'comcert' : $param->certificat_sel_commentaire=$value; break;
		}
		return $param;
}


// ---------------------------------
function referentiel_ref_set_param_config($param, $ref_referentiel_referentiel, $type='config'){
// enregistre la configuration globale
// type config : 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;'
// type config_impression : 'refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
//
global $CFG;
$str_config='';
	if (!empty($param) && isset($ref_referentiel_referentiel) && ($ref_referentiel_referentiel>0)){
		if ($type=='config'){
			if (!empty($param->referentiel_scolarite_masquee) && ($param->referentiel_scolarite_masquee==1)) $str_config.='scol:1;'; else $str_config.='scol:0;';
			if (!empty($param->referentiel_creation_limitee) && ($param->referentiel_creation_limitee==1)) $str_config.='creref:1;'; else $str_config.='creref:0;';
			if (!empty($param->referentiel_selection_autorisee) && ($param->referentiel_selection_autorisee==1)) $str_config.='selref:1;'; else $str_config.='selref:0;';
			if (!empty($param->referentiel_impression_autorisee) && ($param->referentiel_impression_autorisee==1)) $str_config.='impcert:1;'; else $str_config.='impcert:0;';
			if (!empty($param->referentiel_affichage_graphique) && ($param->referentiel_affichage_graphique==1)) $str_config.='graph:1;'; else $str_config.='graph:0;';
			if (!empty($param->referentiel_light_display) && ($param->referentiel_light_display==1)) $str_config.='light:1;'; else $str_config.='light:0;';
			if (!empty($param->referentiel_hierarchy) && ($param->referentiel_hierarchy==1)) $str_config.='hierarchy:1;'; else $str_config.='hierarchy:0;';
			if (!empty($param->referentiel_certif_config) && ($param->referentiel_certif_config==1)) $str_config.='cfcertif:1;'; else $str_config.='cfcertif:0;';
			if (!empty($param->referentiel_certif_state)) $str_config.='certif:1;'; else $str_config.='certif:0;';

			if ($str_config!='') {
				referentiel_global_set_vecteur_config($str_config, $ref_referentiel_referentiel);
			}
		}
		else{
		// type config_impression
			if (!empty($param->certificat_sel_referentiel) && ($param->certificat_sel_referentiel==1)) $str_config.='refcert:1;';  else $str_config.='refcert:0;';
			if (!empty($param->certificat_sel_referentiel_instance) && ($param->certificat_sel_referentiel_instance==1)) $str_config.='instcert:1;'; else $str_config.='instcert:0;';
			if (!empty($param->certificat_sel_etudiant_numero) && ($param->certificat_sel_etudiant_numero==1)) $str_config.='numetu:1;'; else $str_config.='numetu:0;';
			if (!empty($param->certificat_sel_etudiant_nom_prenom) && ($param->certificat_sel_etudiant_nom_prenom==1)) $str_config.='nometu:1;'; else $str_config.='nometu:0;';
			if (!empty($param->certificat_sel_etudiant_etablissement) && ($param->certificat_sel_etudiant_etablissement==1)) $str_config.='etabetu:1;'; else $str_config.='etabetu:0;';
			if (!empty($param->certificat_sel_etudiant_ddn) && ($param->certificat_sel_etudiant_ddn==1)) $str_config.='ddnetu:1;'; else $str_config.='ddnetu:0;';
			if (!empty($param->certificat_sel_etudiant_lieu_naissance) && ($param->certificat_sel_etudiant_lieu_naissance==1)) $str_config.='lieuetu:1;'; else $str_config.='lieuetu:0;';
			if (!empty($param->certificat_sel_etudiant_adresse) && ($param->certificat_sel_etudiant_adresse==1)) $str_config.='adretu:1'; else $str_config.='adretu:0';
			if (!empty($param->certificat_sel_certificat_detail) && ($param->certificat_sel_certificat_detail==1)) $str_config.='detail:1;'; else $str_config.='detail:0;';
			if (!empty($param->certificat_sel_certificat_pourcent) && ($param->certificat_sel_certificat_pourcent==1)) $str_config.='pourcent:1;'; else $str_config.='pourcent:0;';
			if (!empty($param->certificat_sel_activite_competences) && ($param->certificat_sel_activite_competences==1)) $str_config.='compdec:1;'; else $str_config.='compdec:0;';
			if (!empty($param->certificat_sel_certificat_competences) && ($param->certificat_sel_certificat_competences==1)) $str_config.='compval:1;'; else $str_config.='compval:0;';
			if (!empty($param->certificat_sel_certificat_referents) && ($param->certificat_sel_certificat_referents==1)) $str_config.='nomreferent:1;'; else $str_config.='nomreferent:0;';
			if (!empty($param->certificat_sel_decision_jury) && ($param->certificat_sel_decision_jury==1)) $str_config.='jurycert:1;'; else $str_config.='jurycert:0;';
			if (!empty($param->certificat_sel_commentaire) && ($param->certificat_sel_commentaire==1)) $str_config.='comcert:1;'; else $str_config.='comcert:0;';
			if ($str_config!='') {
				referentiel_global_set_vecteur_config_imp($str_config, $ref_referentiel_referentiel);
			}
		}
		// DEBUG
		// echo "<br />DEBUG :: lib_config.php :: 815 :: $str_config\n";
	}
}


// ---------------------------------
function referentiel_set_param_configuration($param, $ref_instance_referentiel, $type='config'){
// enregistre la configuration locale
// type config : 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;'
// type config_impression : 'refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
//
global $CFG;
$str_config='';
	if (!empty($param) && isset($ref_instance_referentiel) && ($ref_instance_referentiel>0)){
		if ($type=='config'){
			if (!empty($param->referentiel_scolarite_masquee) && ($param->referentiel_scolarite_masquee==1)) $str_config.='scol:1;'; else $str_config.='scol:0;';
			if (!empty($param->referentiel_creation_limitee) && ($param->referentiel_creation_limitee==1)) $str_config.='creref:1;'; else $str_config.='creref:0;';
			if (!empty($param->referentiel_selection_autorisee) && ($param->referentiel_selection_autorisee==1)) $str_config.='selref:1;'; else $str_config.='selref:0;';
			if (!empty($param->referentiel_impression_autorisee) && ($param->referentiel_impression_autorisee==1)) $str_config.='impcert:1;'; else $str_config.='impcert:0;';
			if (!empty($param->referentiel_affichage_graphique) && ($param->referentiel_affichage_graphique==1)) $str_config.='graph:1;'; else $str_config.='graph:0;';
			if (!empty($param->referentiel_light_display) && ($param->referentiel_light_displaye==1)) $str_config.='light:1;'; else $str_config.='light:0;';
			if (!empty($param->referentiel_hierarchy) && ($param->referentiel_hierarchy==1)) $str_config.='hierarchy:1;'; else $str_config.='hierarchy:0;';
			if (!empty($param->referentiel_certif_config) && ($param->referentiel_certif_config==1)) $str_config.='cfcertif:1;'; else $str_config.='cfcertif:0;';
			if (!empty($param->referentiel_certif_state)) $str_config.='certif:1;'; else $str_config.='certif:0;';

			if ($str_config!='') {
				referentiel_set_vecteur_configuration($str_config, $ref_instance_referentiel);
			}
		}
		else{
		// type config_impression
			if (!empty($param->certificat_sel_referentiel) && ($param->certificat_sel_referentiel==1)) $str_config.='refcert:1;';  else $str_config.='refcert:0;';
			if (!empty($param->certificat_sel_referentiel_instance) && ($param->certificat_sel_referentiel_instance==1)) $str_config.='instcert:1;'; else $str_config.='instcert:0;';
			if (!empty($param->certificat_sel_etudiant_numero) && ($param->certificat_sel_etudiant_numero==1)) $str_config.='numetu:1;'; else $str_config.='numetu:0;';
			if (!empty($param->certificat_sel_etudiant_nom_prenom) && ($param->certificat_sel_etudiant_nom_prenom==1)) $str_config.='nometu:1;'; else $str_config.='nometu:0;';
			if (!empty($param->certificat_sel_etudiant_etablissement) && ($param->certificat_sel_etudiant_etablissement==1)) $str_config.='etabetu:1;'; else $str_config.='etabetu:0;';
			if (!empty($param->certificat_sel_etudiant_ddn) && ($param->certificat_sel_etudiant_ddn==1)) $str_config.='ddnetu:1;'; else $str_config.='ddnetu:0;';
			if (!empty($param->certificat_sel_etudiant_lieu_naissance) && ($param->certificat_sel_etudiant_lieu_naissance==1)) $str_config.='lieuetu:1;'; else $str_config.='lieuetu:0;';
			if (!empty($param->certificat_sel_etudiant_adresse) && ($param->certificat_sel_etudiant_adresse==1)) $str_config.='adretu:1'; else $str_config.='adretu:0';
			if (!empty($param->certificat_sel_certificat_detail) && ($param->certificat_sel_certificat_detail==1)) $str_config.='detail:1;'; else $str_config.='detail:0;';
			if (!empty($param->certificat_sel_certificat_pourcent) && ($param->certificat_sel_certificat_pourcent==1)) $str_config.='pourcent:1;'; else $str_config.='pourcent:0;';
			if (!empty($param->certificat_sel_activite_competences) && ($param->certificat_sel_activite_competences==1)) $str_config.='compdec:1;'; else $str_config.='compdec:0;';
			if (!empty($param->certificat_sel_certificat_competences) && ($param->certificat_sel_certificat_competences==1)) $str_config.='compval:1;'; else $str_config.='compval:0;';
			if (!empty($param->certificat_sel_certificat_referents) && ($param->certificat_sel_certificat_referents==1)) $str_config.='nomreferent:1;'; else $str_config.='nomreferent:0;';
			if (!empty($param->certificat_sel_decision_jury) && ($param->certificat_sel_decision_jury==1)) $str_config.='jurycert:1;'; else $str_config.='jurycert:0;';
			if (!empty($param->certificat_sel_commentaire) && ($param->certificat_sel_commentaire==1)) $str_config.='comcert:1;'; else $str_config.='comcert:0;';
			if ($str_config!='') {
				referentiel_set_vecteur_configuration_impression($str_config, $ref_instance_referentiel);
			}
		}
		// DEBUG
		// echo "<br />DEBUG :: lib.php :: 3922 :: $str_config\n";
	}
}

// ---------------------------------
function referentiel_ref_get_param_config($ref_referentiel_referentiel, $type='config') {

// retourne la valeur de configuration globale sous forme d'un objet
// type config : 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;'
// type config_impression : 'refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
//
global $CFG;
$parametre = new Object();
	if (isset($ref_referentiel_referentiel) && ($ref_referentiel_referentiel>0)){
		if ($type=='config'){
			$str_config = referentiel_ref_get_vecteur_config($ref_referentiel_referentiel);
		}
		else{
			$str_config = referentiel_ref_get_vecteur_config_imp($ref_referentiel_referentiel);
		}
		if ($str_config!=''){
			$tconfig=explode(';',$str_config);
			$n=count($tconfig);
			if ($n>0){
				$i=0;
				while ($i<$n){
					$tconfig[$i]=trim($tconfig[$i]);
					if ($tconfig[$i]!=''){
						list($cle, $val)=explode(':',$tconfig[$i]);
						$cle=trim($cle);
						$val=trim($val);
						$parametre=referentiel_associe_item_param_configuration($parametre, $cle, $val);
					}
					$i++;
				}
			}
		}
	}

	return $parametre;
}

// ---------------------------------
function referentiel_get_param_configuration($ref_instance_referentiel, $type='config') {
// retourne la valeur de configuration locale sous forme d'un objet
// type config : 'scol:0;creref:0;selref:0;impcert:0;graph:0;light:0;hierarchy:0;'
// type config_impression : 'refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;detail:1;pourcent:0;compdec:0;compval:1;nomreferent:0;jurycert:1;comcert:0;'
//
global $CFG;
$parametre = new Object();
	if (isset($ref_instance_referentiel) && ($ref_instance_referentiel>0)){
		if ($type=='config'){
			$str_config = referentiel_get_vecteur_configuration($ref_instance_referentiel);
		}
		else{
			$str_config = referentiel_get_vecteur_configuration_impression($ref_instance_referentiel);
		}
		if ($str_config!=''){
			$tconfig=explode(';',$str_config);
			$n=count($tconfig);
			if ($n>0){
				$i=0;
				while ($i<$n){
					$tconfig[$i]=trim($tconfig[$i]);
					if ($tconfig[$i]!=''){
						list($cle, $val)=explode(':',$tconfig[$i]);
						$cle=trim($cle);
						$val=trim($val);
						$parametre=referentiel_associe_item_param_configuration($parametre, $cle, $val);
					}
					$i++;
				}
			}
		}
	}

	return $parametre;
}


?>

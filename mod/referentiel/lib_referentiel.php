<?php  // $Id:  lib_referentiel.php,v 1.0 2012/03/08 00:00:00 jfruitet Exp $
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



// MODIF JF 2012/03/04  ///////////////////////////////////////////////////
// Toutes les fonctions sont réécrites pour tenir compte du protocole    //
///////////////////////////////////////////////////////////////////////////

// MODIF JF 2012/06/02

/**
 * Given an object containing an instance, will return label value
 *
 * @param object instance
 * @return string
 **/
function referentiel_get_labels($instance){
global $DB;
    $label=new object();
    $label->domaine=get_string('domaine', 'referentiel');
    $label->competence=get_string('competence', 'referentiel');
    $label->item=get_string('item', 'referentiel');
    if (!empty($instance)){
        if (!empty($instance->ref_referentiel)){
            if ($occurrence= $DB->get_record('referentiel_referentiel', array("id" => $instance->ref_referentiel))){
                if (!empty($instance->label_domaine)){
                    $label->domaine=stripslashes($instance->label_domaine);
                }
                else{
                    if (!empty($occurrence->label_domaine)){
                        $label->domaine=stripslashes($occurrence->label_domaine);
                    }
                }
                if (!empty($instance->label_competence)){
                    $label->competence=stripslashes($instance->label_competence);
                }
                else{
                    if (!empty($occurrence->label_competence)){
                        $label->competence=stripslashes($occurrence->label_competence);
                    }
                }
                if (!empty($instance->label_item)){
                    $label->item=stripslashes($instance->label_item);
                }
                else{
                    if (!empty($occurrence->label_item)){
                        $label->item=stripslashes($occurrence->label_item);
                    }
                }
            }
        }
    }
    return $label;
}

/**
 * Given an object containing an occurrence, will return label value
 *
 * @param object instance
 * @return string
 **/
function referentiel_get_labels_occurrence($refrefid){
global $DB;
    $label=new object();
    $label->domaine=get_string('domaine', 'referentiel');
    $label->competence=get_string('competence', 'referentiel');
    $label->item=get_string('item', 'referentiel');

    if (!empty($refrefid)){
        if ($occurrence= $DB->get_record('referentiel_referentiel', array("id" => $refrefid))){
            if (!empty($occurrence->label_domaine)){
                $label->domaine=stripslashes($occurrence->label_domaine);
            }
            if (!empty($occurrence->label_competence)){
                $label->competence=stripslashes($occurrence->label_competence);
            }
            if (!empty($occurrence->label_item)){
                $label->item=stripslashes($occurrence->label_item);
            }
        }
    }

    return $label;
}

/**
 * Given an object containing an instance, will return label value
 *
 * @param object instance
 * @return string
 **/
function referentiel_get_labels_instance($instanceid){
global $DB;
    $label=new object();
    $label->domaine=get_string('domaine', 'referentiel');
    $label->competence=get_string('competence', 'referentiel');
    $label->item=get_string('item', 'referentiel');
    if (!empty($instanceid)){
        if ($instance= $DB->get_record('referentiel', array("id" => $instanceid))){
            $label=referentiel_get_labels($instance);
        }
    }
    return $label;
}

/**
 * Given an object containing all the necessary referentiel_referentiel,
 * (defined by the form in edit.html) this function
 * will update an existing occurence .
 *
 * @param object $form An object from the form in edit.html
 * @return boolean Success/Fail
 **/
function referentiel_update_referentiel_referentiel($form) {
global $USER;
global $DB;
	$ok=false;
	// DEBUG
	// echo "<br />DEBUG :: lib.php :: 3460 <br />";
	// print_object($form);
	// echo "<br />";
	if (isset($form->referentiel_id) && ($form->referentiel_id>0)){
			// referentiel
			$referentiel_referentiel = new object();
			$referentiel_referentiel->name=($form->name);
			$referentiel_referentiel->code_referentiel=($form->code_referentiel);
			$referentiel_referentiel->description_referentiel=($form->description_referentiel);
			$referentiel_referentiel->url_referentiel=($form->url_referentiel);
			$referentiel_referentiel->seuil_certificat=($form->seuil_certificat);
    		$referentiel_referentiel->timemodified = time();
			$referentiel_referentiel->nb_domaines=$form->nb_domaines;
			$referentiel_referentiel->liste_codes_competence=$form->liste_codes_competence;
			$referentiel_referentiel->liste_empreintes_competence=$form->liste_empreintes_competence;

			$referentiel_referentiel->timemodified = time();
    		$referentiel_referentiel->id = $form->referentiel_id;

			// Modif JF 2009/10/16
			if (isset($form->liste_poids_competence)){
				$referentiel_referentiel->liste_poids_competence=$form->liste_poids_competence;
			}
			else{
				$referentiel_referentiel->liste_poids_competence='';
			}

			if (isset($form->minima_certificat)){
				$referentiel_referentiel->minima_certificat=$form->minima_certificat;
			}
			else{
				$referentiel_referentiel->minima_certificat='';
			}

		    if (isset($form->logo_referentiel)){
			     $referentiel_referentiel->logo_referentiel=$form->logo_referentiel;
		    }
		    else{
			     $referentiel_referentiel->logo_referentiel='';
		    }

			// local ou global
			if (isset($form->local) && ($form->local!=0) && isset($form->course) && ($form->course!=0))
				$referentiel_referentiel->local=$form->course;
			else
				$referentiel_referentiel->local=0;


            // Modif JF 2012/06/02
			if (isset($form->label_domaine)){
				$referentiel_referentiel->label_domaine=$form->label_domaine;
			}
			else{
				$referentiel_referentiel->label_domaine='';
			}
			if (isset($form->label_competence)){
				$referentiel_referentiel->label_competence=$form->label_competence;
			}
			else{
				$referentiel_referentiel->label_competence='';
			}
			if (isset($form->label_item)){
				$referentiel_referentiel->label_item=$form->label_item;
			}
			else{
				$referentiel_referentiel->label_item='';
			}

			// traitements speciaux
			if (isset($form->mail_auteur_referentiel) && ($form->mail_auteur_referentiel!='')){
				$referentiel_referentiel->mail_auteur_referentiel=$form->mail_auteur_referentiel;
			}
			else{
				// Modif JF 2009/10/16
				if (isset($USER->email) && ($USER->email!='')){
					$referentiel_referentiel->mail_auteur_referentiel=$USER->email;
				}
				else{
					$referentiel_referentiel->mail_auteur_referentiel='';
				}
			}

            if (!isset($form->cle_referentiel)){
                $referentiel_referentiel->cle_referentiel='';
		    }
		    else{
			    $referentiel_referentiel->cle_referentiel=$form->cle_referentiel;
            }

			// Modif JF 2009/10/16
    		if (isset($form->pass_referentiel) && ($form->pass_referentiel!='')){
	       		// MD5
		      	$referentiel_referentiel->pass_referentiel=md5($form->pass_referentiel);
    		}
	       	else{
		      	if (isset($form->old_pass_referentiel)){
			     	$referentiel_referentiel->pass_referentiel=$form->old_pass_referentiel; // archive md5()
    			}
	       		else{
		      		$referentiel_referentiel->pass_referentiel='';
    			}
	       	}

	    	// DEBUG
		    // echo "<br />";
			// print_object($referentiel_referentiel);
	    	// echo "<br />";
			// exit;
			if ($ok=$DB->update_record("referentiel_referentiel", $referentiel_referentiel)){
                // MODIF JF 2012/03/04
                referentiel_recalcule_cle_referentiel($form->referentiel_id);
            }
	}
	return $ok;
}


/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in add.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $instance An object from the form in add.html
 * @return int The id of the newly inserted referentiel record
 **/
function referentiel_add_referentiel_domaines($form) {
global $USER;
global $DB;
// La premiere creation permet aussi la saisie d'un domaine, d'une compÃ©tence et d'un item
	$referentiel_referentiel_id=0;
    // temp added for debugging
    // echo "<br />DEBUG :: lib.php :: 3364:: ADD INSTANCE CALLED";
    // DEBUG
	// print_object($form);
    // echo "<br />";

	// saisie d'un referentiel
	if (isset($form->name) && ($form->name!="")
		&& isset($form->code_referentiel) && ($form->code_referentiel!="")){
		// creer
		$referentiel_referentiel = new object();
		$referentiel_referentiel->name=($form->name);
		$referentiel_referentiel->code_referentiel=($form->code_referentiel);
		$referentiel_referentiel->description_referentiel=($form->description_referentiel);
		$referentiel_referentiel->url_referentiel=($form->url_referentiel);
		$referentiel_referentiel->seuil_certificat=$form->seuil_certificat;
		$referentiel_referentiel->nb_domaines=$form->nb_domaines;
		$referentiel_referentiel->liste_codes_competence=($form->liste_codes_competence);
		$referentiel_referentiel->liste_empreintes_competence=$form->liste_empreintes_competence;
		// Modif JF 2009/10/16
		if (isset($form->liste_poids_competence)){
			$referentiel_referentiel->liste_poids_competence=$form->liste_poids_competence;
		}
		else{
			$referentiel_referentiel->liste_poids_competence='';
		}

		$referentiel_referentiel->timemodified = time();
		if (isset($form->local) && ($form->local!=0) && isset($form->course) && ($form->course!=0)){
			$referentiel_referentiel->local=$form->course;
		}
		else{
			$referentiel_referentiel->local=0;
		}
		$referentiel_referentiel->logo_referentiel = $form->logo_referentiel;

        // Modif JF 2012/06/02
		if (isset($form->label_domaine)){
				$referentiel_referentiel->label_domaine=$form->label_domaine;
		}
		else{
				$referentiel_referentiel->label_domaine='';
		}
		if (isset($form->label_competence)){
				$referentiel_referentiel->label_competence=$form->label_competence;
		}
		else{
				$referentiel_referentiel->label_competence='';
		}
		if (isset($form->label_item)){
				$referentiel_referentiel->label_item=$form->label_item;
		}
		else{
				$referentiel_referentiel->label_item='';
		}

		// traitements speciaux
		if (isset($form->mail_auteur_referentiel) && ($form->mail_auteur_referentiel!='')){
			$referentiel_referentiel->mail_auteur_referentiel=$form->mail_auteur_referentiel;
		}
		else{
			// Modif JF 2009/10/16
			if (isset($USER->email) && ($USER->email!='')){
				$referentiel_referentiel->mail_auteur_referentiel=$USER->email;
			}
			else{
				$referentiel_referentiel->mail_auteur_referentiel='';
			}
		}

		// Modif JF 2009/10/16
		if (isset($form->old_pass_referentiel)){ // mot de passe stocke au format Crypte MD5()
			$referentiel_referentiel->old_pass_referentiel=$form->old_pass_referentiel;
		}
		else{
			$referentiel_referentiel->old_pass_referentiel='';
		}
		if (isset($form->pass_referentiel) && (trim($form->pass_referentiel)!='')){ // mot de passe changÃ©
			// MD5
			$referentiel_referentiel->pass_referentiel=md5($form->pass_referentiel);
		}



	    // DEBUG
	    // echo "<br />DEBUG :: lib.php :: 221";
		// print_object($referentiel_referentiel);
	    // echo "<br />";

		if ($referentiel_referentiel_id = $DB->insert_record("referentiel_referentiel", $referentiel_referentiel)){
    	   // echo "REFERENTIEL ID : $referentiel_referentiel_id<br />";
            /* *****************
            // MODIF JF 2012/03/06
			// saisie de l'instance
			// cela n'est pas necessaire à ce niveau
			// on le fait dans les scripts appelants
			if (!empty($form->instance)){
                referentiel_associe_referentiel_instance($form->instance, $referentiel_referentiel_id);
			}
			else{
        		$referentiel = new object();
	       		$referentiel->name=($form->name_instance);
    			$referentiel->description_instance=($form->description_instance);
                $referentiel->label_domaine=($form->label_domaine);
    			$referentiel->label_competence=($form->label_competence);
	       		$referentiel->label_item=($form->label_item);
		        $referentiel->date_instance = time();
			    $referentiel->course=$form->course;
			    $referentiel->ref_referentiel=$referentiel_referentiel_id;
		        // DEBUG
			    // echo "<br />DEBUG :: lib.php :: 240";
			    // print_object($referentiel);
		        // echo "<br />";
			    $referentiel_id = $DB->insert_record("referentiel", $referentiel);
			}
			******************************************* */
			// saisie du domaine
			$domaine = new object();
			$domaine->ref_referentiel=$referentiel_referentiel_id;
			if (empty($form->code_domaine)){
                $form->code_domaine=get_string('c_domaine','referentiel').'.'.$form->num_domaine;
            }
			$domaine->code_domaine=$form->code_domaine;
            if (empty($form->description_domaine)){
                $form->description_domaine=get_string('a_completer', 'referentiel');
            }
			$domaine->description_domaine=$form->description_domaine;
			$domaine->num_domaine=$form->num_domaine;
			$domaine->nb_competences=$form->nb_competences;
    		// MODIF JF 2012/02/20
	       	if (isset($form->type_domaine)){
                $domaine->type_domaine = $form->type_domaine;
            }
            else{
                $domaine->type_domaine = 0;
            }
            if (isset($form->seuil_domaine)){
                $domaine->seuil_domaine = $form->seuil_domaine;
            }
            else{
                $domaine->seuil_domaine = 0.0;
            }
           // MODIF JF 2012/03/26
            if (isset($form->minima_domaine)){
                $domaine->minima_domaine = $form->minima_domaine;
            }
            else{
                $domaine->minima_domaine = 0;
            }


		    // DEBUG
			// echo "<br />DEBUG :: lib.php :: 253";
			// print_object($domaine);
			// echo "<br />";

			$domaine_id = $DB->insert_record("referentiel_domaine", $domaine);
    		// echo "DOMAINE ID / $domaine_id<br />";
			if ($domaine_id>0){
				$competence = new object();
				$competence->ref_domaine=$domaine_id;
                if (empty($form->code_competence)){
                    $form->code_competence=get_string('c_competence','referentiel').'.'.$form->num_domaine.'.'.$form->num_competence;
                }
				$competence->code_competence=$form->code_competence;
                if (empty($form->description_competence)){
                    $form->description_competence=get_string('a_completer', 'referentiel');
                }
				$competence->description_competence=$form->description_competence;
				$competence->num_competence=$form->num_competence;
				$competence->nb_item_competences=$form->nb_item_competences;

    			// MODIF JF 2012/02/20
	       		if (isset($form->type_competence)){
                    $competence->type_competence = $form->type_competence;
                }
                else{
                    $competence->type_competence = 0;
                }
                if (isset($form->seuil_competence)){
                    $competence->seuil_competence = $form->seuil_competence;
                }
                else{
                    $competence->seuil_competence = 0.0;
                }
               // MODIF JF 2012/03/26
                if (isset($form->minima_competence)){
                    $competence->minima_competence = $form->minima_competence;
                }
                else{
                    $competence->minima_competence = 0;
                }

    			// DEBUG
				// echo "<br />DEBUG :: lib.php :: 268";
				// print_object($competence);
    			// echo "<br />";

				$competence_id = $DB->insert_record("referentiel_competence", $competence);
		    	// echo "COMPETENCE ID / $competence_id<br />";
				if ($competence_id>0){
					$item = new object();
					$item->ref_referentiel=$referentiel_referentiel_id;
					$item->ref_competence=$competence_id;
          if (empty($form->code_item)){
          	$form->code_item=get_string('c_item','referentiel').'.'.$form->num_domaine.'.'.$form->num_competence.'.'.$form->num_item;
          }
					$item->code_item=$form->code_item;
          if (empty($form->description_item)){
          	$form->description_item=get_string('a_completer', 'referentiel');
          }
					$item->description_item=$form->description_item;
					$item->type_item=$form->type_item;
					$item->poids_item=$form->poids_item;
					$item->empreinte_item=$form->empreinte_item;
					$item->num_item=$form->num_item;
    				// DEBUG
					// echo "<br />DEBUG :: lib.php :: 283";
					// print_object($item);
    				// echo "<br />";

					$item_id=$DB->insert_record("referentiel_item_competence", $item);
				    // echo "ITEM ID / $item_id<br />";
				}
			}
		}
		if ($referentiel_referentiel_id>0){
			// MODIF JF 2009/10/16
			$liste_codes_competence=referentiel_new_liste_codes_competence($referentiel_referentiel_id);
			referentiel_set_liste_codes_competence($referentiel_referentiel_id, $liste_codes_competence);
			$liste_empreintes_competence=referentiel_new_liste_empreintes_competence($referentiel_referentiel_id);
			referentiel_set_liste_empreintes_competence($referentiel_referentiel_id, $liste_empreintes_competence);
			$liste_poids_competence=referentiel_new_liste_poids_competence($referentiel_referentiel_id);
			referentiel_set_liste_poids_competence($referentiel_referentiel_id, $liste_poids_competence);
            // MODIF JF 2012/03/04
            referentiel_recalcule_cle_referentiel($referentiel_referentiel_id);
            // MODIF JF 2012/02/25
            referentiel_initialise_protocole_referentiel($referentiel_referentiel_id);
        }
    	# May have to add extra stuff in here #
	}
	else{
		return get_string('erreur_creation','referentiel');
		// "Name and code mandatory";
	}
	return $referentiel_referentiel_id;
}


/**
 * Given an object containing all the necessary stuff,
 * (defined by the form in edit.html) this function
 * will update an existing instance .
 *
 * @param object $iform An object from the form in edit.html
 * @return boolean Success/Fail
 **/
function referentiel_update_referentiel_domaines($form) {
global $USER;
global $DB;
	$ok=true;
	// DEBUG
	// echo "<br />DEBUG :: lib.php :: 3460 <br />";
	// print_object($form);
	// echo "<br />";
	if (!empty($form->referentiel_id)){
		if (isset($form->action) && ($form->action=="modifierreferentiel")){
			// referentiel
			$referentiel_referentiel = new object();
			$referentiel_referentiel->name=($form->name);
			$referentiel_referentiel->code_referentiel=($form->code_referentiel);
			$referentiel_referentiel->description_referentiel=($form->description_referentiel);
			$referentiel_referentiel->url_referentiel=($form->url_referentiel);
			$referentiel_referentiel->seuil_certificat=($form->seuil_certificat);
    		$referentiel_referentiel->timemodified = time();
			$referentiel_referentiel->nb_domaines=$form->nb_domaines;
			$referentiel_referentiel->liste_codes_competence=$form->liste_codes_competence;
			$referentiel_referentiel->liste_empreintes_competence=$form->liste_empreintes_competence;
			// Modif JF 2009/10/16
			if (isset($form->liste_poids_competence)){
				$referentiel_referentiel->liste_poids_competence=$form->liste_poids_competence;
			}
			else{
				$referentiel_referentiel->liste_poids_competence='';
			}
			$referentiel_referentiel->mail_auteur_referentiel=$form->mail_auteur_referentiel;
			$referentiel_referentiel->cle_referentiel=$form->cle_referentiel;
			$referentiel_referentiel->pass_referentiel=$form->old_pass_referentiel;	// sera modifie par traitement special

			// traitements speciaux
			if (isset($form->mail_auteur_referentiel) && ($form->mail_auteur_referentiel!='')){
				$referentiel_referentiel->mail_auteur_referentiel=$form->mail_auteur_referentiel;
			}
			else{
				// Modif JF 2009/10/16
				if (isset($USER->email) && ($USER->email!='')){
					$referentiel_referentiel->mail_auteur_referentiel=$USER->email;
				}
				else{
					$referentiel_referentiel->mail_auteur_referentiel='';
				}
			}

			if (isset($form->cle_referentiel) && (trim($form->cle_referentiel)!='')){
				$referentiel_referentiel->cle_referentiel=$form->cle_referentiel;
			}
			else{
				// Modif JF 2009/10/16
				if (isset($USER->email) && ($USER->email!='')){
					// MD5
					$referentiel_referentiel->cle_referentiel=md5($USER->email.$referentiel_referentiel->code_referentiel);
				}
				else{
					$referentiel_referentiel->cle_referentiel='';
				}
			}
			// Modif JF 2009/10/16
			$referentiel_referentiel->pass_referentiel=$form->old_pass_referentiel;	// sera modifie par traitement special
			if ($form->pass_referentiel!=''){ // le pass a ete ressaisi
				// MD5
				$referentiel_referentiel->pass_referentiel=md5($form->pass_referentiel);
			}


			// local ou global
			if (isset($form->local) && ($form->local!=0) && isset($form->course) && ($form->course!=0))
				$referentiel_referentiel->local=$form->course;
			else
				$referentiel_referentiel->local=0;

			$referentiel_referentiel->timemodified = time();
    		$referentiel_referentiel->id = $form->referentiel_id;
			$referentiel_referentiel->logo_referentiel = $form->logo_referentiel;

	    	// DEBUG
		    // echo "<br />";
			// print_object($referentiel_referentiel);
	    	// echo "<br />";
			// exit;
			if ($ok=$DB->update_record("referentiel_referentiel", $referentiel_referentiel)){
                // MODIF JF 2012/03/04
                referentiel_recalcule_cle_referentiel($referentiel_referentiel->id);
            }
		}
		else if (isset($form->action) && ($form->action=="completerreferentiel")){
			if (isset($form->domaine_id) && is_array($form->domaine_id)){
				for ($i=0; $i<count($form->domaine_id); $i++){
					$domaine = new object();
					$domaine->id=$form->domaine_id[$i];
					$domaine->ref_referentiel=$form->referentiel_id;
					$oldcode=$form->oldcode[$i];
					$domaine->code_domaine=($form->code_domaine[$i]);

					$domaine->description_domaine=($form->description_domaine[$i]);
					$domaine->num_domaine=$form->num_domaine[$i];
					$domaine->nb_competences=$form->nb_competences[$i];

                    // MODIF JF 2012/02/20
	              	if (isset($form->type_domaine)){
                        $domaine->type_domaine = $form->type_domaine[$i];
                    }
                    else{
                        $domaine->type_domaine = 0;
                    }
                    if (isset($form->seuil_domaine)){
                        $domaine->seuil_domaine = $form->seuil_domaine[$i];
                    }
                    else{
                        $domaine->seuil_domaine = 0.0;
                    }
                    // MODIF JF 2012/03/26
                    if (isset($form->minima_domaine)){
                        $domaine->minima_domaine = $form->minima_domaine[$i];
                    }
                    else{
                        $domaine->minima_domaine = 0;
                    }

					if (!$DB->update_record("referentiel_domaine", $domaine)){
						// DEBUG
						// print_object($domaine);
						// echo "<br />ERREUR DE MISE A JOUR...";
						$ok=false;
						// exit;
					}
					else{
						// DEBUG
						// print_object($domaine);
						// echo "<br />MISE A JOUR DOMAINE...";
						// MODIF JF 2012/02/17
                        referentiel_update_domaine_protocole($oldcode, $domaine->code_domaine, $domaine->ref_referentiel, $domaine->type_domaine, $domaine->seuil_domaine, $domaine->minima_domaine);
					}
				}
			}
			// NOUVEAU DOMAINE
			if (isset($form->new_code_domaine) && is_array($form->new_code_domaine)){
				for ($i=0; $i<count($form->new_code_domaine); $i++){
					$domaine = new object();
					$domaine->ref_referentiel=$form->referentiel_id;
					$domaine->code_domaine=($form->new_code_domaine[$i]);
					$domaine->description_domaine=($form->new_description_domaine[$i]);
					$domaine->num_domaine=$form->new_num_domaine[$i];
					$domaine->nb_competences=$form->new_nb_competences[$i];
                    // MODIF JF 2012/02/20
                    if (isset($form->new_type_domaine)){
                        $domaine->type_domaine = $form->new_type_domaine[$i];
                    }
                    else{
                        $domaine->type_domaine = 0;
                    }
                    if (isset($form->new_seuil_domaine)){
                        $domaine->seuil_domaine = $form->new_seuil_domaine[$i];
                    }
                    else{
                        $domaine->seuil_domaine = 0.0;
                    }
                    // MODIF JF 2012/03/26
                    if (isset($form->new_minima_domaine)){
                        $domaine->minima_domaine = $form->new_minima_domaine[$i];
                    }
                    else{
                        $domaine->minima_domaine = 0;
                    }

					// DEBUG
					// print_object($domaine);
					// echo "<br />";
					if ($new_domaine_id = $DB->insert_record("referentiel_domaine", $domaine)){
                        $domaine->id=$new_domaine_id;
                        referentiel_add_domaine_protocole($domaine, $domaine->ref_referentiel);
                    }
                    else{
                        $ok=false;
                    }
    				// echo "DOMAINE ID / $new_domaine_id<br />";
				}
			}
			// COMPETENCES
			if (isset($form->competence_id) && is_array($form->competence_id)){
				for ($i=0; $i<count($form->competence_id); $i++){
					$competence = new object();
					$competence->id=$form->competence_id[$i];
                    $oldcode=$form->oldcode[$i];
					$competence->code_competence=($form->code_competence[$i]);
					$competence->description_competence=($form->description_competence[$i]);
					$competence->ref_domaine=$form->ref_domaine[$i];
					$competence->num_competence=$form->num_competence[$i];
					$competence->nb_item_competences=$form->nb_item_competences[$i];

                    // MODIF JF 2012/02/20
                    if (isset($form->type_competence)){
                        $competence->type_competence = $form->type_competence[$i];
                    }
                    else{
                        $competence->type_competence = 0;
                    }
                    if (isset($form->seuil_competence)){
                        $competence->seuil_competence = $form->seuil_competence[$i];
                    }
                    else{
                        $competence->seuil_competence = 0.0;
                    }
                    // MODIF JF 2012/03/26
                    if (isset($form->minima_competence)){
                        $competence->minima_competence = $form->minima_competence[$i];
                    }
                    else{
                        $competence->minima_competence = 0;
                    }

					// DEBUG
					// print_object($competence);
					if (!$DB->update_record("referentiel_competence", $competence)){
						// echo "<br />ERREUR DE MISE A JOUR...";
						$ok=false;
						// exit;
					}
					else{
						// echo "<br />MISE A JOUR COMPETENCES...";
                        referentiel_update_competence_protocole($oldcode, $competence->code_competence, $form->referentiel_id, $competence->type_competence, $competence->seuil_competence, $competence->minima_competence);
					}
				}
			}
			// NOUVElle competence
			if (isset($form->new_code_competence) && is_array($form->new_code_competence)){
				for ($i=0; $i<count($form->new_code_competence); $i++){
					$competence = new object();
					$competence->code_competence=($form->new_code_competence[$i]);
					$competence->description_competence=($form->new_description_competence[$i]);
					$competence->ref_domaine=$form->new_ref_domaine[$i];
					$competence->num_competence=$form->new_num_competence[$i];
					$competence->nb_item_competences=$form->new_nb_item_competences[$i];
                    // MODIF JF 2012/02/20
    	       	   	if (isset($form->new_type_competence)){
                        $competence->type_competence = $form->new_type_competence[$i];
                    }
                    else{
                        $competence->type_competence = 0;
                    }
                    if (isset($form->new_seuil_competence)){
                        $competence->seuil_competence = $form->new_seuil_competence[$i];
                    }
                    else{
                        $competence->seuil_competence = 0.0;
                    }
                    // MODIF JF 2012/03/26
                    if (isset($form->new_minima_competence)){
                        $competence->minima_competence = $form->new_minima_competence[$i];
                    }
                    else{
                        $competence->minima_competence = 0;
                    }

					// DEBUG
					// print_object($competence);
					// echo "<br />";
					if ($new_competence_id = $DB->insert_record("referentiel_competence", $competence)){
                        $competence->id=$new_competence_id;
                        referentiel_add_competence_protocole($competence, $form->referentiel_id);
                    }
                    else{
                        $ok=false;
                    }

   					// echo "competence ID / $new_competence_id<br />";
				}
			}
			// ITEM COMPETENCES
			if (isset($form->item_id) && is_array($form->item_id)){
				for ($i=0; $i<count($form->item_id); $i++){
					$item = new object();
					$item->id=$form->item_id[$i];
					$item->ref_referentiel=$form->referentiel_id;
					$item->ref_competence=$form->ref_competence[$i];
					$oldcode=$form->oldcode[$i];
					$item->code_item=($form->code_item[$i]);
					$item->description_item=($form->description_item[$i]);
					$item->num_item=$form->num_item[$i];
					$item->type_item=$form->type_item[$i];
					$item->poids_item=$form->poids_item[$i];
					$item->empreinte_item=$form->empreinte_item[$i];

					// DEBUG
					// print_object($item);
					// echo "<br />";
					if (!$DB->update_record("referentiel_item_competence", $item)){
						// echo "<br />ERREUR DE MISE A JOUR ITEM COMPETENCE...";
						$ok=false;
						// exit;
					}
					else {
						// echo "<br />MISE A JOUR ITEM COMPETENCES...";
                        referentiel_update_item_protocole($oldcode, $item->code_item, $item->ref_referentiel, $item->type_item);
					}
				}
			}
			// NOUVEL item
			if (isset($form->new_code_item) && is_array($form->new_code_item)){
				for ($i=0; $i<count($form->new_code_item); $i++){
					$item = new object();
					$item->ref_referentiel=$form->referentiel_id;
					$item->ref_competence=$form->new_ref_competence[$i];
					$item->code_item=($form->new_code_item[$i]);
					$item->description_item=($form->new_description_item[$i]);
					$item->num_item=$form->new_num_item[$i];
					$item->type_item=($form->new_type_item[$i]);
					$item->poids_item=$form->new_poids_item[$i];
					$item->empreinte_item=$form->new_empreinte_item[$i];

					// DEBUG
					// print_object($item);
					// echo "<br />";
					if ($new_item_id = $DB->insert_record("referentiel_item_competence", $item)){
                        referentiel_add_item_protocole($item->code_item, $item->ref_referentiel, $item->type_item);
                    }
                    else{
                        $ok=false;
                    }
   					// echo "item ID / $new_item_id<br />";
				}
			}

			// Mise a jour de la liste de competences
			$liste_codes_competence=referentiel_new_liste_codes_competence($form->referentiel_id);
			// echo "<br />LISTE_CODES_COMPETENCE : $liste_codes_competence\n";
			referentiel_set_liste_codes_competence($form->referentiel_id, $liste_codes_competence);
			$liste_empreintes_competence=referentiel_new_liste_empreintes_competence($form->referentiel_id);
			// echo "<br />LISTE_empreintes_COMPETENCE : $liste_empreintes_competence\n";
			referentiel_set_liste_empreintes_competence($form->referentiel_id, $liste_empreintes_competence);
			// Modif JF 2009/10/16
			$liste_poids_competence=referentiel_new_liste_poids_competence($form->referentiel_id);
			referentiel_set_liste_poids_competence($form->referentiel_id, $liste_poids_competence);
            // MODIF JF 2012/03/04
            referentiel_recalcule_cle_referentiel($form->referentiel_id);
		}
	}
	return $ok;
}


/**
 * Given a referentiel_referentiel id,
 * will calculate the referentiel key of new referenteil_referentiel.
 *
 * @param object $form An object
 * @return the referentiel_referentiel key string
 **/
function referentiel_recalcule_cle_referentiel($refrefid){
    global $CFG;
	global $DB;
    $cle_referentiel='';
    // DEBUG
    // echo "<br />lib.php :: 3803 Recalcule clé référenteil $refrefid<br />\n";
    // echo "<br />\n";
    // echo "EXIT\n";
    // exit;
    if (!empty($refrefid)){
        if ($record=$DB->get_record("referentiel_referentiel", array("id" => $refrefid))){
            if (!empty($record->code_referentiel)){
                $cle_referentiel=$record->code_referentiel;
            }
            if (!empty($record->liste_codes_competence)){
                $cle_referentiel.=$record->liste_codes_competence;
            }
            /*
            if (!empty($form->mail_auteur_referentiel)){
                $cle_referentiel.=$record->mail_auteur_referentiel);
            }
            */

            if (!empty($cle_referentiel)){
                $cle_referentiel=md5($cle_referentiel);
            }

            $DB->set_field("referentiel_referentiel", "cle_referentiel", $cle_referentiel, array("id" => $refrefid));
        }
        // DEBUG
        // echo "<br />lib.php :: 3828 :: Clé Référentiel = '$cle_referentiel'<br />\n";
    }
    return $cle_referentiel;
}


/**
 * returns referentiel_referentiel_key
 * @param int refrefid
 * @return strin referentiel_referentiel cle (key))
 *
 **/
function referentiel_referentiel_retourne_cle($refrefid){
    global $DB;
    if (!empty($refrefid)){
        if ($record=$DB->get_record("referentiel_referentiel", array("id" => $refrefid))){
            return $record->cle_referentiel;
        }
	}
    return '';
}

/**
 * returns referentiel_referentiel_key
 * @param int refrefid
 * @return strin referentiel_referentiel cle (key))
 *
 **/
function referentiel_referentiel_positionne_cle($refrefid, $cle){
    global $DB;
    if (!empty($refrefid)){
        if ($record=$DB->get_record("referentiel_referentiel", array("id" => $refrefid))){
            $DB->set_field("referentiel_referentiel", "cle_referentiel", $cle, array("id" => $refrefid));
        }
	}
}

/////////////////////////////////////////////////////////////////

/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $form An object
 * @return int The id of the newly inserted referentiel_referentiel record
 **/
function referentiel_add_referentiel($form) {
global $USER;
    global $CFG;
	global $DB;
// Creer un referentiel_referentiel sans domaine ni competence ni item
    // Added for debugging
    // echo "<br />DEBUG : ADD REFERENTIEL CALLED :: lib.php Ligne 633";
    // DEBUG
	// print_object($form);
    // echo "<br />";

	// referentiel
	$referentiel_referentiel = new object();
	$referentiel_referentiel->name=($form->name);
	$referentiel_referentiel->code_referentiel=($form->code_referentiel);
	$referentiel_referentiel->description_referentiel=($form->description_referentiel);
	$referentiel_referentiel->url_referentiel=($form->url_referentiel);
	$referentiel_referentiel->seuil_certificat=$form->seuil_certificat;
	$referentiel_referentiel->nb_domaines=$form->nb_domaines;
	$referentiel_referentiel->liste_codes_competence=($form->liste_codes_competence);
    $referentiel_referentiel->timemodified = time();
	$referentiel_referentiel->liste_empreintes_competence=$form->liste_empreintes_competence;

        // Modif JF 2012/06/02
	if (isset($form->label_domaine)){
	   $referentiel_referentiel->label_domaine=$form->label_domaine;
	}
	else{
	   $referentiel_referentiel->label_domaine='';
	}
	if (isset($form->label_competence)){
		$referentiel_referentiel->label_competence=$form->label_competence;
	}
	else{
		$referentiel_referentiel->label_competence='';
	}
	if (isset($form->label_item)){
		$referentiel_referentiel->label_item=$form->label_item;
    }
	else{
		$referentiel_referentiel->label_item='';
	}

	// Modif JF 2009/10/16
	if (isset($form->liste_poids_competence)){
		$referentiel_referentiel->liste_poids_competence=$form->liste_poids_competence;
	}
	else{
		$referentiel_referentiel->liste_poids_competence='';
	}
	$referentiel_referentiel->logo_referentiel=$form->logo_referentiel;
	// local ou global
	if (isset($form->local) && ($form->local!=0) && isset($form->course) && ($form->course!=0))
		$referentiel_referentiel->local=$form->course;
	else
		$referentiel_referentiel->local=0;

	// traitements speciaux
	if (!isset($form->mail_auteur_referentiel)){
		$form->mail_auteur_referentiel='';
	}

	if (!isset($form->old_pass_referentiel)){
		$form->old_pass_referentiel='';
	}
	if (!isset($form->pass_referentiel)){
		$form->pass_referentiel='';
	}

	if ($form->mail_auteur_referentiel==''){
		if (isset($USER->id)  && ($USER->id>0)){
			// mail auteur
			$referentiel_referentiel->mail_auteur_referentiel=referentiel_get_user_mail($USER->id);
		}
		else{
			$referentiel_referentiel->mail_auteur_referentiel='';
		}
	}

	if ($form->pass_referentiel!=''){
		// MD5
		$referentiel_referentiel->pass_referentiel=md5($form->pass_referentiel);
	}
	else{
		$referentiel_referentiel->pass_referentiel=$form->old_pass_referentiel; // archive md5()
	}


    // DEBUG
    // echo "<br />DEBUG :: lib.php Ligne 658";
	// print_object($referentiel_referentiel);
    // echo "<br />";

	if ($new_referentiel_id = $DB->insert_record("referentiel_referentiel", $referentiel_referentiel)){
        // echo "REFERENTIEL ID / $referentiel_referentiel_id<br />";
        // MODIF JF 2012/03/04
        referentiel_recalcule_cle_referentiel($new_referentiel_id);
    }


	return $new_referentiel_id;
}

/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in mod.html) this function
 * will update an instance and return true
 *
 * @param object $form An object from the form in mod.html
 * @return boolean
 **/
function referentiel_update_referentiel($form) {
    global $CFG;
	global $DB;
    global $USER;
// $form : formulaire
	// DEBUG
	// echo "<br />DEBUG lib_referentiel.php Ligne 676";
	// print_object($form);
	// echo "<br />";
	$ok=false;
	if (isset($form->referentiel_id) && ($form->referentiel_id>0)){
		// referentiel
		$referentiel = new object();
		$referentiel->name=addslashes($form->name);
		$referentiel->code_referentiel=addslashes($form->code_referentiel);
		$referentiel->description_referentiel=addslashes($form->description_referentiel);
		$referentiel->url_referentiel=addslashes($form->url_referentiel);
		$referentiel->seuil_certificat=$form->seuil_certificat;
    	$referentiel->timemodified = time();
		$referentiel->nb_domaines=$form->nb_domaines;
		$referentiel->liste_codes_competence=$form->liste_codes_competence;
		$referentiel->liste_empreintes_competence=$form->liste_empreintes_competence;

		$referentiel->timemodified = time();
    	$referentiel->id = $form->referentiel_id;

		// Modif JF 2009/10/16
		if (isset($form->liste_poids_competence)){
			$referentiel->liste_poids_competence=$form->liste_poids_competence;
		}
		else{
			$referentiel->liste_poids_competence='';
		}

		if (isset($form->logo_referentiel)){
			$referentiel->logo_referentiel=$form->logo_referentiel;
		}
		else{
			$referentiel->logo_referentiel='';
		}

		// local ou global
		if (isset($form->local) && ($form->local!=0) && isset($form->course) && ($form->course!=0)){
			$referentiel->local=$form->course;
		}
		else {
			$referentiel->local=0;
		}

		// traitements speciaux
		if (isset($form->mail_auteur_referentiel) && ($form->mail_auteur_referentiel!='')){
			$referentiel->mail_auteur_referentiel=$form->mail_auteur_referentiel;
		}
		else{
			// Modif JF 2009/10/16
			if (isset($USER->email) && ($USER->email!='')){
				$referentiel->mail_auteur_referentiel=$USER->email;
			}
			else{
				$referentiel->mail_auteur_referentiel='';
			}
		}

		if (!isset($form->cle_referentiel)){
			$referentiel->cle_referentiel='';
		}
		else{
			$referentiel->cle_referentiel=$form->cle_referentiel;
		}

		if (isset($form->pass_referentiel) && ($form->pass_referentiel!='')){
			// MD5
			$referentiel->pass_referentiel=md5($form->pass_referentiel);
		}
		else{
			if (isset($form->old_pass_referentiel)){
				$referentiel->pass_referentiel=$form->old_pass_referentiel; // archive md5()
			}
			else{
				$referentiel->pass_referentiel='';
			}
		}

		if (isset($form->minima_certificat)){
            $referentiel->minima_certificat=$form->minima_certificat;
		}
		else{
            $referentiel->minima_certificat='';
		}


		// Modif JF 2012/06/02
		if (isset($form->label_domaine)){
			$referentiel->label_domaine=$form->label_domaine;
		}
		else{
			$referentiel->label_domaine='';
		}
		if (isset($form->label_competence)){
			$referentiel->label_competence=$form->label_competence;
		}
		else{
			$referentiel->label_competence='';
		}
		if (isset($form->label_item)){
			$referentiel->label_item=$form->label_item;
		}
		else{
			$referentiel->label_item='';
		}

	    // DEBUG
	    // echo "<br />";
		// print_object($referentiel);
	    // echo "<br />";
		if ($ok=$DB->update_record("referentiel_referentiel", $referentiel)){
            // MODIF JF 2012/03/04
            referentiel_recalcule_cle_referentiel($referentiel->id);
        }
    }
	// DEBUG
	// exit;
    return $ok;
}


/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new referentiel.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function referentiel_update_domaine($form) {
    global $CFG;
	global $DB;
	$ok=false;
	// DEBUG
    // echo "\n<br />DEBUG :: lib.php :: 1207<br />\n";
	// print_object($form);
	// echo "<br />\n";

    if (!empty($form->domaine_id)){
		if ($domaine = $DB->get_record("referentiel_domaine", array("id" => $form->domaine_id))){
			$old_code_domaine=$domaine->code_domaine; // pour le protocle
			$domaine->code_domaine=($form->code_domaine);
			$domaine->description_domaine=($form->description_domaine);
			$domaine->num_domaine=$form->num_domaine;
			$domaine->nb_competences=$form->nb_competences;
			// MODIF JF 2012/02/20
			if (isset($form->type_domaine)){
                $domaine->type_domaine = $form->type_domaine;
            }
        	else{
                $domaine->type_domaine = 0;
            }
			if (isset($form->seuil_domaine)){
                $domaine->seuil_domaine = $form->seuil_domaine;
            }
        	else{
                $domaine->seuil_domaine = 0.0;
            }
        	// MODIF JF 2012/03/26
        	if (isset($form->minima_domaine)){
                $domaine->minima_domaine = $form->minima_domaine;
            }
        	else{
                $domaine->minima_domaine = 0;
            }

			if ($ok=$DB->update_record("referentiel_domaine", $domaine)){
				// DEBUG
				// print_object($domaine);
				// echo "<br />MISE A JOUR DOMAINE...";
				// Mettre a jour le protocole associe
				// MODIF JF 2012/02/17
                referentiel_update_domaine_protocole($old_code_domaine, $domaine->code_domaine, $domaine->ref_referentiel, $domaine->type_domaine, $domaine->seuil_domaine, $domaine->minima_domaine);
        	}
		}
	}
	return $ok;
}

/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in mod.html) this function
 * will add an existing instance with new domaine.
 *
 * @param object $instance An object from the form in mod.html
 * @return new_domaine_id
 **/
function referentiel_add_domaine($form) {
    global $CFG;
	global $DB;
	$new_domaine_id=0;
    // temp added for debugging
    // echo "<br />DEBUG :: lib_referentiel.php :: 1066 ::  ADD DOMAINE CALLED";
    // DEBUG
	// print_object($form);
	// echo "<br />";

	// NOUVEAU DOMAINE
	if (isset($form->new_code_domaine) && ($form->new_code_domaine!="")){
		$domaine = new object();
		if (isset($form->reference_id)){
			$domaine->ref_referentiel=$form->reference_id;
      	}
      	elseif (isset($form->occurrence)){
			$domaine->ref_referentiel=$form->occurrence;
		}
		elseif (isset($form->instance)){
			$domaine->ref_referentiel=$form->instance;
		}

		if (empty($form->new_code_domaine)){
			$form->new_code_domaine=get_string('c_domaine','referentiel').'.'.$form->num_domaine;
		}
		if (empty($form->new_description_domaine)){
			$form->new_description_domaine=get_string('a_completer', 'referentiel');
		}

		$domaine->code_domaine=$form->new_code_domaine;
		$domaine->description_domaine=($form->new_description_domaine);
		$domaine->num_domaine=$form->new_num_domaine;
		$domaine->nb_competences=$form->new_nb_competences;

		if (isset($form->new_type_domaine)){
			$domaine->type_domaine = $form->new_type_domaine;
		}
		else{
			$domaine->type_domaine = 0;
		}
		if (isset($form->new_seuil_domaine)){
			$domaine->seuil_domaine = $form->new_seuil_domaine;
		}
		else{
			$domaine->seuil_domaine = 0.0;
		}

		if (isset($form->new_minima_domaine)){
			$domaine->minima_domaine = $form->new_minima_domaine;
		}
		else{
			$domaine->minima_domaine = 0;
		}

		if ($exist=$DB->get_record("referentiel_domaine", array("ref_referentiel" => $domaine->ref_referentiel, "code_domaine" => $domaine->code_domaine) )){
			$domaine->code_domaine=$domaine->code_domaine."-".$exist->id."-Bis";
		}

		//print_object($domaine);
		//echo "<br />";
		//exit;
		$new_domaine_id = $DB->insert_record("referentiel_domaine", $domaine);
		if ($new_domaine_id){
			$domaine->id=$new_domaine_id;
			referentiel_add_domaine_protocole($domaine, $domaine->ref_referentiel);
		}
		// echo "DOMAINE ID / $new_domaine_id<br />";
		// echo "<br />";
	}
	return $new_domaine_id;
}

/**
 * Given a domain id,
 * this function will delete this domain and any competence and items associated.
 *
 * @param int id
 * @return boolean
 **/
function referentiel_delete_domaine($domaine_id){
// suppression
    global $DB;
		$ok_domaine=true;
		$ok_competence=true;
		$ok_item=true;
    # Delete any dependent records here #
		// Competences
		if ($competences = $DB->get_records("referentiel_competence", array("ref_domaine" => $domaine_id))) {
			// DEBUG
			// print_object($competences);
			// echo "<br />";
			// Item
			foreach ($competences as $competence){
				$ok_competence=$ok_competence && referentiel_supprime_competence($competence->id);
			}
		}
		// suppression
		$ok_domaine=$ok_domaine && $DB->delete_records("referentiel_domaine", array("id" => $domaine_id));
    return ($ok_domaine && $ok_competence);
}


/**
 * Given a domain id,
 * this function will delete this domain and dependent competencies and items .
 *
 * @param int id
 * @return boolean
 **/
function referentiel_supprime_domaine($domaine_id){
// suppression avec mise a jour du nombre de domaines dans le referentiel
    global $DB;

    $ok=false;
	if ($domaine_id){
		// suppression du domaine avec mise a jour dans le referentiel associe
	    $reference_domaine = $DB->get_record_sql("SELECT code_domaine, num_domaine, ref_referentiel FROM {referentiel_domaine} WHERE id=:id", array("id" => $domaine_id) );
        if ($reference_domaine){
            // maj domaines suivantes
            $r_domaines = $DB->get_records_sql("SELECT id, num_domaine FROM {referentiel_domaine} WHERE ref_referentiel=:refrefid AND num_domaine > :num_domaine ORDER BY num_domaine", array("refrefid" => $reference_domaine->ref_referentiel, "num_domaine" => $reference_domaine->num_domaine));

            if ($r_domaines){
                foreach($r_domaines as $domaine){
                    // renommer les numeros
                    $DB->set_field("referentiel_domaine", "num_domaine", $domaine->num_domaine-1, array("id" => $domaine->id));
                }
            }
            // maj referentiel
            $referentiel_referentiel=$DB->get_record("referentiel_referentiel", array("id" => $reference_domaine->ref_referentiel));
            // DEBUG
            // echo "<br />DEBUG : lib.php :: 4036<br />\n";
            // print_r($referentiel_referentiel);
            // exit;
            if ($referentiel_referentiel){
                $DB->set_field("referentiel_referentiel", "nb_domaines", $referentiel_referentiel->nb_domaines-1, array("id" => $referentiel_referentiel->id));
                // MODIF JF 2012/02/17
                // mettre a jour le protocole
                referentiel_delete_domaine_protocole($reference_domaine->code_domaine, $reference_domaine->ref_referentiel);
                // supprimer la compétence
                if ($ok=referentiel_delete_domaine($domaine_id)){
                    // Mise a jour de la liste de competences dans le referentiel_referentiel associe
		            	$liste_codes_competence=referentiel_new_liste_codes_competence($referentiel_referentiel->id);
        		    	// echo "<br />LISTE_CODES_COMPETENCE : $liste_codes_competence\n";
		            	referentiel_set_liste_codes_competence($referentiel_referentiel->id, $liste_codes_competence);
        		    	$liste_empreintes_competence=referentiel_new_liste_empreintes_competence($referentiel_referentiel->id);
			            // echo "<br />LISTE_empreintes_COMPETENCE : $liste_empreintes_competence\n";
	        		    referentiel_set_liste_empreintes_competence($referentiel_referentiel->id, $liste_empreintes_competence);
                    // Modif JF 2009/10/16
        		    	$liste_poids_competence=referentiel_new_liste_poids_competence($referentiel_referentiel->id);
                }
	       }
        }
    }
	return $ok;
}


/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in mod.html) this function
 * will add an existing instance with new domaine.
 *
 * @param object $instance An object from the form in mod.html
 * @return new_competence_id
 **/
function referentiel_add_competence($form) {
    global $DB;
	$new_competence_id=0;
    // temp added for debugging
    // DEBUG
    // echo "DEBUG :: lib_referentiel :: 1225 :: DEBUG : ADD COMPETENCE CALLED<br />";
    // DEBUG
	// print_object($form);
    // echo "<br />";

	// NOUVElle competence
	if (!empty($form->new_code_competence)){
		if (isset($form->reference_id)){
			$ref_referentiel=$form->reference_id;
		}
		elseif(isset($form->occurrence)){
			$ref_referentiel=$form->occurrence;
		}
		elseif (isset($form->instance)){
			$ref_referentiel=$form->instance;
		}

		$competence = new object();
		if (empty($form->new_description_competence)){
        	$form->new_description_competence=get_string('a_completer', 'referentiel');
      	}
		$competence->code_competence=$form->new_code_competence;
		$competence->description_competence=$form->new_description_competence;
		$competence->ref_domaine=$form->new_ref_domaine;
		$competence->num_competence=$form->new_num_competence;
		$competence->nb_item_competences=$form->new_nb_item_competences;

		if (isset($form->new_type_competence)){
        	$competence->type_competence = $form->new_type_competence;
		}
		else{
        	$competence->type_competence = 0;
		}
		if (isset($form->new_seuil_competence)){
        	$competence->seuil_competence = $form->new_seuil_competence;
		}
		else{
        	$competence->seuil_competence = 0.0;
		}
		if (isset($form->new_minima_competence)){
        	$competence->minima_competence = $form->new_minima_competence;
		}
		else{
        	$competence->minima_competence = 0;
		}

		// DEBUG
      	//echo "<br />DEBUG :: lib_referentiel :: 1475 :: COMPETENCE_RECORD<br />";
      	//print_object($competence);
		//echo "<br />\n";
		if ($exist=$DB->get_record("referentiel_competence", array("ref_domaine" => $competence->ref_domaine, "code_competence" => $competence->code_competence))){
        	$competence->code_competence=$competence->code_competence."-".$exist->id."-Bis";
		}
		if ($new_competence_id = $DB->insert_record("referentiel_competence", $competence)){
        	$competence->id=$new_competence_id;
         	referentiel_add_competence_protocole($competence, $ref_referentiel);
		}
		// echo "competence ID / $new_competence_id<br />";
	}

	return $new_competence_id;
}

/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new referentiel.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function referentiel_update_competence($form) {
    global $DB;
	$ok=false;
	// DEBUG
    //echo "\n<br />DEBUG :: lib.php :: 1506<br />\n";
	//print_object($form);
	//echo "<br />\n";

	if (!empty($form->competence_id)){
		if (isset($form->reference_id)){
			$ref_referentiel=$form->reference_id;
		}
		elseif (isset($form->occurrence)){
			$ref_referentiel=$form->occurrence;
		}
		elseif (isset($form->instance)){
			$ref_referentiel=$form->instance;
		}

		$competence=$DB->get_record("referentiel_competence", array("id" => $form->competence_id) );
		if ($competence){
			$oldcode=$competence->code_competence;
    		$competence->code_competence=($form->code_competence);
        	$competence->description_competence=($form->description_competence);
        	$competence->ref_domaine=$form->ref_domaine;
			$competence->num_competence=$form->num_competence;
			$competence->nb_item_competences=$form->nb_item_competences;
        	if (isset($form->type_competence)){
				$competence->type_competence = $form->type_competence;
        	}
        	else{
				$competence->type_competence = 0;
        	}
			if (isset($form->seuil_competence)){
           		$competence->seuil_competence = $form->seuil_competence;
        	}
        	else{
           		$competence->seuil_competence = 0.0;
        	}
        	// MODIF JF 2012/03/26
        	if (isset($form->minima_competence)){
				$competence->minima_competence = $form->minima_competence;
        	}
        	else{
				$competence->minima_competence = 0;
        	}

			// DEBUG
			// print_object($competence);
			if ($ok=$DB->update_record("referentiel_competence", $competence)){
				//echo "<br />DEBUG :: lib_refentiel.php :: 1192 :: MISE A JOUR COMPETENCES...<br />\n";
				//print_object($competence);
				// MODIF JF 2012/02/17
				referentiel_update_competence_protocole($oldcode, $competence->code_competence, $ref_referentiel, $competence->type_competence, $competence->seuil_competence, $competence->minima_competence);
				$ok=true;
			}
		}
	}
	return $ok;
}

/**
 * Given a competence id,
 * this function will delete of this competence.
 *
 * @param int id
 * @return boolean
 **/
function referentiel_delete_competence($competence_id){
// suppression
    global $DB;
$ok_competence=false;
    if ($competence_id){
        # Delete any dependent records here #
	    // items
        if ($items = $DB->get_records("referentiel_item_competence", array("ref_competence" => $competence_id))) {
		      // DEBUG
		      // print_object($items);
		      // echo "<br />";
            foreach ($items as $item){
                // suppression
                referentiel_supprime_item($item->id);
            }
        }
        // suppression
        $ok_competence=$DB->delete_records("referentiel_competence", array("id" => $competence_id));
	}
    return ($ok_competence);
}

/**
 * Given an cometence id,
 * this function will delete this competence and update competence number in domain linked.
 *
 * @param int id
 * @return boolean
 **/
function referentiel_supprime_competence($competence_id){
// suppression avec mise a jour du nombre de competences dans le domaine associe
    global $DB;

    $ok_competence=false;
	if ($competence_id){
        // suppression de la competence avec mise a jour dans le domaine associe
	    $reference_competence = $DB->get_record_sql("SELECT code_competence, num_competence, ref_domaine FROM {referentiel_competence} WHERE id=:id", array("id" => $competence_id));
        if ($reference_competence){
            // maj competences suivantes
            $r_competences = $DB->get_records_sql("SELECT id, num_competence FROM {referentiel_competence} WHERE ref_domaine=:ref_domaine AND num_competence > :num_competence ORDER BY num_competence", array("ref_domaine" => $reference_competence->ref_domaine, "num_competence" => $reference_competence->num_competence));
	        if ($r_competences){
                foreach($r_competences as $competence){
                    // renommer les numeros
                    $DB->set_field("referentiel_competence", "num_competence", $competence->num_competence-1, array("id" => $competence->id));
                }
            }
            // maj domaine
            $domaine=$DB->get_record("referentiel_domaine", array("id" => $reference_competence->ref_domaine));
            if ($domaine){
                $DB->set_field("referentiel_domaine", "nb_competences", $domaine->nb_competences-1, array("id" => $domaine->id));

                // mettre a jour le protocole
                // MODIF JF 2012/02/17
                referentiel_delete_competence_protocole($reference_competence->code_competence, $domaine->ref_referentiel);
                // supprimer la compétence
                if ($ok_competence=referentiel_delete_competence($competence_id)){
                    // Mise a jour de la liste de competences dans le referentiel_referentiel associe
		            $liste_codes_competence=referentiel_new_liste_codes_competence($domaine->ref_referentiel);
        		    // echo "<br />LISTE_CODES_COMPETENCE : $liste_codes_competence\n";
		            referentiel_set_liste_codes_competence($domaine->ref_referentiel, $liste_codes_competence);
        		    $liste_empreintes_competence=referentiel_new_liste_empreintes_competence($domaine->ref_referentiel);
		            // echo "<br />LISTE_empreintes_COMPETENCE : $liste_empreintes_competence\n";
        		    referentiel_set_liste_empreintes_competence($domaine->ref_referentiel, $liste_empreintes_competence);
                    // Modif JF 2009/10/16
        		    $liste_poids_competence=referentiel_new_liste_poids_competence($domaine->ref_referentiel);
                    // echo "<br />LISTE_poids_COMPETENCE : $liste_poids_competence\n";
		            referentiel_set_liste_poids_competence($domaine->ref_referentiel, $liste_poids_competence);
                }
            }
        }
    }
    return ($ok_competence);
}


/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new referentiel.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function referentiel_update_item($form) {
    global $DB;
	$ok=false;
	// DEBUG
    //echo "\n<br />DEBUG :: lib.php :: 1655<br />\n";
	//print_object($form);
	//echo "<br />\n";
	//exit;
	// ITEM COMPETENCES
	if (!empty($form->item_id)){
		$item = $DB->get_record("referentiel_item_competence", array("id" => $form->item_id));
		$oldcode=$item->code_item;
		$item->code_item=($form->code_item);
		$item->description_item=($form->description_item);
		$item->num_item=$form->num_item;
		$item->type_item=($form->type_item);
		$item->poids_item=$form->poids_item;
		$item->empreinte_item=$form->empreinte_item;
		// DEBUG
		//print_object($item);
		//echo "<br />";
		//exit;
		if ($ok=$DB->update_record("referentiel_item_competence", $item)){
  			// echo "<br />MISE A JOUR ITEM COMPETENCES...";
			// Mise Ã  jour de la liste de competences
			$liste_codes_competence=referentiel_new_liste_codes_competence($item->ref_referentiel);
			// echo "<br />LISTE_CODES_COMPETENCE : $liste_codes_competence\n";
			referentiel_set_liste_codes_competence($item->ref_referentiel, $liste_codes_competence);
			$liste_empreintes_competence=referentiel_new_liste_empreintes_competence($item->ref_referentiel);
			// echo "<br />LISTE_empreintes_COMPETENCE : $liste_empreintes_competence\n";
			referentiel_set_liste_empreintes_competence($item->ref_referentiel, $liste_empreintes_competence);
			// Modif JF 2009/10/16
			$liste_poids_competence=referentiel_new_liste_poids_competence($item->ref_referentiel);
			// echo "<br />LISTE_poids_COMPETENCE : $liste_poids_competence\n";
			referentiel_set_liste_poids_competence($item->ref_referentiel, $liste_poids_competence);
			// Modif JF 2011/05/11
			// supprimer si necessaire les items de compétence dans les activites et les certificat
			if ($item->code_item != $oldcode){
				// remplacer le code des items de compétence dans les activites
        		referentiel_maj_activites_codes_competence($item->ref_referentiel, $oldcode, $item->code_item);
			}
			// Modif JF 2012/02/17
			// mise à jour du protocole
			referentiel_update_item_protocole($oldcode, $item->code_item, $item->ref_referentiel, $item->type_item);
		}
	}
	return $ok;
}

/**
 * Given an object containing all the necessary referentiel,
 * (defined by the form in mod.html) this function
 * will add an existing instance with new item.
 *
 * @param object $instance An object from the form
 * @return new_item_id
 **/

function referentiel_add_item($form) {
// NOUVEL item
    global $DB;

	$new_item_id=0;
	if (isset($form->new_code_item) && ($form->new_code_item!="")){
		if (isset($form->occurrence)){ $ref_referentiel=$form->occurrence;}
		else if (isset($form->instance)){ $ref_referentiel=$form->instance;}

		$item = new object();
		$item->ref_referentiel=$ref_referentiel;
		$item->ref_competence=$form->new_ref_competence;

		// Modif JF 2012/03/07
		if (empty($form->new_code_item)){
			$form->new_code_item=get_string('c_item','referentiel').'.'.$form->num_domaine.'.'.$form->num_competence.'.'.$form->new_num_item;
		}
		$item->code_item=$form->new_code_item;
		if (empty($form->new_description_item)){
			$form->new_description_item=get_string('a_completer', 'referentiel');
		}
		$item->description_item=$form->new_description_item;
		$item->num_item=$form->new_num_item;
		$item->type_item=$form->new_type_item;
		$item->poids_item=$form->new_poids_item;
		$item->empreinte_item=$form->new_empreinte_item;

		// DEBUG
		//echo "<br />DEBUG :: lib_referentiel.php :: 1740<br />\n";
		//print_object($item);
		//echo "<br />";
		// Verifier unicite du code item
		if ($exist=$DB->get_record("referentiel_item_competence", array("ref_referentiel" => $ref_referentiel, "code_item" => $item->code_item))){
			$item->code_item=$item->code_item."-".$exist->id."-Bis";
		}

		if ($new_item_id = $DB->insert_record("referentiel_item_competence", $item)){
			// echo "item ID / $new_item_id<br />";
			// Mise a jour de la liste des codes de competences
			$liste_codes_competence=referentiel_new_liste_codes_competence($ref_referentiel);
			// echo "<br />lib_referentiel.php :: 1546 ::LISTE_CODES_COMPETENCE : $liste_codes_competence\n";
			referentiel_set_liste_codes_competence($ref_referentiel, $liste_codes_competence);
			$liste_empreintes_competence=referentiel_new_liste_empreintes_competence($ref_referentiel);
			// echo "<br />LISTE_empreintes_COMPETENCE : $liste_empreintes_competence\n";
			referentiel_set_liste_empreintes_competence($ref_referentiel, $liste_empreintes_competence);
			// Modif JF 2009/10/16
			$liste_poids_competence=referentiel_new_liste_poids_competence($ref_referentiel);
			// echo "<br />LISTE_poids_COMPETENCE : $liste_poids_competence\n";
			referentiel_set_liste_poids_competence($ref_referentiel, $liste_poids_competence);

			// Modif JF 2012/02/17
			// protocole
			referentiel_add_item_protocole($item->code_item, $ref_referentiel, $item->type_item);
		}
	}
	return $new_item_id;
}

/**
 * Given an item id,
 * this function will delete of this item.
 *
 * @param int id
 * @return boolean
 **/
function referentiel_delete_item($item_id){
// suppression
    global $DB;
	if ($item_id){
		return $DB->delete_records("referentiel_item_competence", array("id" => $item_id));
	}
}

/**
 * Given an item id,
 * this function will delete of this item.
 *
 * @param int id
 * @return boolean
 **/
function referentiel_supprime_item($item_id){
// suppression avec mise a jour de la liste des item dans la competence associee
// Modif JF 2012/02/17
    global $DB;

$ok=false;
	if ($item_id){
		$reference_item = $DB->get_record_sql("SELECT code_item, num_item, ref_competence, ref_referentiel FROM {referentiel_item_competence} WHERE id=:id", array("id" => $item_id));

        if ($reference_item){
            // maj items suivants
            $r_items = $DB->get_records_sql("SELECT id, num_item FROM  {referentiel_item_competence} WHERE ref_competence=:ref_competence AND ref_referentiel=:ref_referentiel AND num_item > :num_item ORDER BY num_item",
							array("ref_competence" => $reference_item->ref_competence, "ref_referentiel" =>$reference_item->ref_referentiel, "num_item" => $reference_item->num_item ));
	       	if ($r_items){
                foreach($r_items as $item){
                    // renommer les numeros
                    $DB->set_field("referentiel_item_competence", "num_item", $item->num_item-1, array("id" => $item->id));
                }
            }
            // maj competence
            $competence=$DB->get_record("referentiel_competence", array("id" => $reference_item->ref_competence));
            if ($competence){
                $DB->set_field("referentiel_competence", "nb_item_competences", $competence->nb_item_competences-1, array("id" => $reference_item->ref_competence));
            }

    		// mettre a jour le protocole
    		// Modif JF 2012/02/17
            referentiel_delete_item_protocole($reference_item->code_item, $reference_item->ref_referentiel);

            if ($ok=referentiel_delete_item($item_id)){
                // Mise a jour de la liste des codes de competences
    			$liste_codes_competence=referentiel_new_liste_codes_competence($reference_item->ref_referentiel);
	       		// echo "<br />LISTE_CODES_COMPETENCE : $liste_codes_competence\n";
		      	referentiel_set_liste_codes_competence($reference_item->ref_referentiel, $liste_codes_competence);
       			$liste_empreintes_competence=referentiel_new_liste_empreintes_competence($reference_item->ref_referentiel);
	       		// echo "<br />LISTE_empreintes_COMPETENCE : $liste_empreintes_competence\n";
		      	referentiel_set_liste_empreintes_competence($reference_item->ref_referentiel, $liste_empreintes_competence);
    			// Modif JF 2009/10/16
	       		$liste_poids_competence=referentiel_new_liste_poids_competence($reference_item->ref_referentiel);
		      	// echo "<br />LISTE_poids_COMPETENCE : $liste_poids_competence\n";
    			referentiel_set_liste_poids_competence($reference_item->ref_referentiel, $liste_poids_competence);
	       		// Modif JF 20110511
		      	referentiel_sup_activites_codes_competence($reference_item->ref_referentiel, $liste_codes_competence);
            }
        }
    }
	return $ok;
}



function referentiel_get_maxnbitem($occurrence_id){
// Affiche le référentiel
global $DB;
	$maxnbitem=1;
	if (!empty($occurrence_id)){
		if ($records_domaine = referentiel_get_domaines($occurrence_id)){
			foreach ($records_domaine as $record_d){
				if ($records_competences = referentiel_get_competences($record_d->id)){
					foreach ($records_competences as $record_c){
						if ($record_c->nb_item_competences>$maxnbitem){
                            $maxnbitem=$record_c->nb_item_competences;
						}
					}
				}
			}
		}
    }
	return $maxnbitem;
}

?>

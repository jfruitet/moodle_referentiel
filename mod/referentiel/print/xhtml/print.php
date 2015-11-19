<?php 
// Based on default.php, included by ../import.php


// ACTIVITES</td><td class='referentiel'>export des activites
class pprint_xhtml extends pprint_default {

	function provide_print() {
      return true;
    }

	function repchar( $text ) {
	    // escapes 'reserved' characters # = ~ { ) and removes new lines
    	$reserved = array( '#','=','~','{','}',"\n","\r" );
	    $escaped = array( '\#','\=','\~','\{','\}',' ','' );
		return str_replace( $reserved, $escaped, $text ); 
	}

	function presave_process( $content ) {
	  // override method to allow us to add xhtml headers and footers
  	global $CFG;

  	// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/print/xhtml/xhtml.css" );
		$css = implode( ' ',$css_lines ); 
		$xp =  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$xp .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
        $xp .= "<head>\n";
        $xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
        $xp .= "<title>Moodle Referentiel :: Certificats XHTML Export</title>\n";
        $xp .= $css;
        $xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  	return $xp;
	}

	function export_file_extension() {
  		return ".html";
	}

    /**
     * Include an image encoded in base 64
     * @param string imagepath The location of the image file
     * @return string xml code segment 
     */
    function writeimage( $imagepath ) {
        global $CFG;
   		
        if (empty($imagepath)) {
            return '';
        }

        $courseid = $this->course->id;
        if (!$binary = file_get_contents( "{$CFG->dataroot}/$courseid/$imagepath" )) {
            return '';
        }

        $content = "    <image_base64>\n".(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string printted text
     */

    function write_ligne( $raw, $sep="/", $nmaxcar=80) {
        // insere un saut de ligne apres le 80 caracter 
		$nbcar=strlen($raw);
		if ($nbcar>$nmaxcar){
			$s1=substr( $raw,0,$nmaxcar);
			$pos1=strrpos($s1,$sep);
			if ($pos1>0){
				$s1=substr( $raw,0,$pos1);
				$s2=substr( $raw,$pos1+1);
			}
			else {
				$s1=substr( $raw,0,$nmaxcar);
				$s2=substr( $raw,$nmaxcar);
			}
		    return $s1." ".$s2;
		}
		else{
			return $raw;
		}
    }


		function write_etablissement( $record, $nbchamps_referentiel ) {
        // initial string;
        $expout = "";
        // add comment
        $expout .= "\n\n<!-- etablissement: $record->id  -->\n";
		if ($record){
			$expout .= "<tr class='referentiel'><td class='referentiel' colspan='".$nbchamps_referentiel."'><i>".get_string('etablissement','referentiel')."</i></td></tr>\n";
    		$expout .= "<tr class='referentiel'><td class='referentiel' colspan='".$nbchamps_referentiel."'>\n";

			$id = trim( $record->id );
			$num_etablissement = trim( $record->num_etablissement);
			$nom_etablissement = trim( $record->nom_etablissement);
			$adresse_etablissement = trim( $record->adresse_etablissement);
			$logo = trim( $record->logo_etablissement);
						
    		$expout .= "<table class='referentiel'>\n";
			$expout .= "<tr class='referentiel'>\n";
			$expout .= " <th class='referentiel'>".get_string('num_etablissement','referentiel')."</th>\n";
            $expout .= " <th class='referentiel' colspan='2'>".get_string('nom_etablissement','referentiel')."</th>\n";
            $expout .= " <th class='referentiel'>".get_string('logo','referentiel')."</th>\n";
            $expout .= " <th class='referentiel' colspan='3'>".get_string('adresse_etablissement','referentiel')."</th>\n";
			$expout .= " </tr>\n";
			$expout .= "<tr class='referentiel'>\n";
            $expout .= " <td class='referentiel'>$num_etablissement</td>\n";
            $expout .= " <td class='referentiel' colspan='2'>$nom_etablissement</td>\n";			
			if ($logo){
            	$expout .= " <td class='referentiel'><img src='$logo' border='0' alt='logo'></td>\n";
			}
			else{
            	$expout .= " <td class='referentiel'>&nbsp;</td>\n";
			}
            $expout .= " <td class='referentiel' colspan='3'>$adresse_etablissement</td>\n";
			$expout .= " </tr>\n";
			$expout .= "</table>\n";
			$expout .= "</td></tr>\n";
        }
        return $expout;
    }


	
	function write_etudiant( $record , $nbchamps_referentiel) {
        // initial string;
        $s1='';
		$s2='';
		$nbchamps=0;
		$expout = "";
        // add comment

		if ($record){
			// DEBUG
			// echo "<br />\n";
			// print_r($record);
	    	// add header
			//
			$id = trim( $record->id );
			$userid = trim( $record->userid );
	        $login = trim(referentiel_get_user_login($record->userid));
            $ref_etablissement = trim( $record->ref_etablissement);
			$num_etudiant = trim( $record->num_etudiant);
			$ddn_etudiant = trim( $record->ddn_etudiant);
			$lieu_naissance = trim( $record->lieu_naissance);
			$departement_naissance = trim( $record->departement_naissance);
			$adresse_etudiant = trim( $record->adresse_etudiant);			
            if ($num_etudiant==$login){
                    $texte=$num_etudiant;
            }
            elseif ($num_etudiant==''){
                    $texte=$login;
            }
            else{
                    $texte=$num_etudiant." (".$login.")";
            }

			if ($this->certificat_sel_param->certificat_sel_etudiant_nom_prenom){
				$nbchamps++;
				$s2 .= "<th class='referentiel'>".get_string('lastname')." ".get_string('firstname')."</th>\n";
			}
			if ($this->certificat_sel_param->certificat_sel_etudiant_numero){
				$nbchamps++;
				$s2 .= "<th class='referentiel'>".get_string('num_etudiant','referentiel')."</th>\n";
			}
			if ($this->certificat_sel_param->certificat_sel_etudiant_ddn){
				$nbchamps++;
				$s2 .= "<th class='referentiel'>".get_string('ddn_etudiant','referentiel')."</th>\n";
			}
			if ($this->certificat_sel_param->certificat_sel_etudiant_lieu_naissance){
				$s2.= "<th class='referentiel'>".get_string('lieu_naissance','referentiel')."</th>\n";
				$s2.= "<th class='referentiel'>".get_string('departement_naissance','referentiel')."</th>\n";
				$nbchamps+=2;
			}
			if ($this->certificat_sel_param->certificat_sel_etudiant_adresse){
				$nbchamps++;
				$s2.= "<th class='referentiel' colspan='2'>".get_string('adresse_etudiant','referentiel')."</th>\n";
			}
			$s1 .= "\n\n<!-- etudiant: $record->id  -->\n";			
			$s1 .= "<tr class='referentiel'><td class='referentiel' colspan='".$nbchamps_referentiel."'><b>".get_string('etudiant','referentiel')."</b></td></tr>\n";
    		$s1 .= "<tr class='referentiel'><td class='referentiel' colspan='".$nbchamps_referentiel."'>\n";
			$s1 .= "<table class='referentiel'>\n<tr class='referentiel'>\n";
			$s1.=$s2;
			$s1 .= "</tr>\n<tr class='referentiel'>\n";
			
			$s2='';
			if ($this->certificat_sel_param->certificat_sel_etudiant_nom_prenom){
				$s2 .= " <td class='referentiel'>".referentiel_get_user_info($record->userid)."</td>\n";
			}

			if ($this->certificat_sel_param->certificat_sel_etudiant_numero){
				$s2 .= " <td class='referentiel'>$texte</td>\n";
			}
			if ($this->certificat_sel_param->certificat_sel_etudiant_ddn){
				$s2 .= " <td class='referentiel'>$ddn_etudiant</td>\n";
			}
			if ($this->certificat_sel_param->certificat_sel_etudiant_lieu_naissance){
	            $s2 .= " <td class='referentiel'>$lieu_naissance</td>\n";
    			$s2 .= " <td class='referentiel'>$departement_naissance</td>\n";
            }
			if ($this->certificat_sel_param->certificat_sel_etudiant_adresse){
				$s2 .= " <td class='referentiel' colspan='2'>$adresse_etudiant</td>\n";
			}
			$s1.=$s2;
			$s1 .= " </tr>\n";
			$s1 .= "</table>\n";
			$s1 .= "</td></tr>\n";
			// Etablissement
			$record_etablissement=referentiel_get_etablissement($record->ref_etablissement);
	    	if ($record_etablissement){
				if ($this->certificat_sel_param->certificat_sel_etudiant_etablissement){
					$s1 .= $this->write_etablissement( $record_etablissement, $nbchamps_referentiel);
				}
			}
			$expout.=$s1;
        }
        return $expout;
    }

	
	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_certificat( $record) {
    	global $CFG;
        // initial string;
        $s1='';
		$s2='';
		$nbchamps=0;
		$expout = "";

    	// add comment and div tags
		if ($record){
			// DEBUG
			// echo "<br />DEBUG LIGNE 1021<br />\n";
			// print_r($referentiel_instance);
      $id = trim( $record->id );
      $commentaire_certificat = trim($record->commentaire_certificat);
      $synthese_certificat = trim($record->synthese_certificat);
      $competences_certificat =  trim($record->competences_certificat) ;
      $competences_activite = trim($record->competences_activite);
      $decision_jury = trim($record->decision_jury);
      if ($record->date_decision){
	       $date_decision = userdate(trim($record->date_decision));
      }
			else{
			   $date_decision ="";
			}
      $userid = trim( $record->userid);
      $teacherid = trim( $record->teacherid);
			if ($teacherid!=0){
				$nom_prenom_teacher=referentiel_get_user_info($teacherid);
			}
			else{
				$nom_prenom_teacher="";
			}
      $ref_referentiel = trim( $record->ref_referentiel);
			$verrou = trim( $record->verrou );
			$valide = trim( $record->valide );
			$evaluation = trim( $record->evaluation );
			
			$pourcentages='';
			// calcul des pourcentages
			if ($this->certificat_sel_param->certificat_sel_certificat_pourcent){
  			if (isset($verrou) && ($verrou!="")) {
          if ($verrou!=0){
		  		  $bgcolor='verrouille';
			    }
			    else{
				    $bgcolor='deverrouille';;
			    }
		    }
		    else{
			   $bgcolor='deverrouille';
		    }
		    // Tableau
		    $pourcentages=referentiel_retourne_certificat_consolide('/',':',$competences_certificat, $ref_referentiel, ' class="'.$bgcolor.'"');
		  }
		  
			 // USER
			if (isset($record->userid) && ($record->userid>0)){
				$record_etudiant = referentiel_get_etudiant_user($record->userid);
                if (!$record_etudiant){
                    // creer l'enregistrement car on en a besoin immediatement
                    if (referentiel_add_etudiant_user($record->userid)){
                        $record_etudiant = referentiel_get_etudiant_user($record->userid);
                    }
                }


		    	if ($record_etudiant){
					$s2='';
					if ($this->certificat_sel_param->certificat_sel_decision_jury){
						$s2 .= "<th class='referentiel'>".get_string('decision','referentiel')."</th>\n";
						$s2 .= "<th class='referentiel'>".get_string('datedecision','referentiel')."</th>\n";
						$nbchamps+=2;
					}
					if ($this->certificat_sel_param->certificat_sel_certificat_referents){
						$s2 .= "<th class='referentiel'>".get_string('valide_par','referentiel')."</th>\n";
						$nbchamps++;
					}
					if ($this->certificat_sel_param->certificat_sel_certificat_detail){
						$s2 .= "<th class='referentiel'>".get_string('verrou','referentiel')."</th>\n";
						$s2 .= "<th class='referentiel'>".get_string('evaluation','referentiel')."</th>\n";
						$nbchamps+=2;
					}

					if ($this->certificat_sel_param->certificat_sel_commentaire){
						$s2 .= "<th class='referentiel'>".get_string('commentaire','referentiel')."</th>\n";
						$s2 .= "<th class='referentiel'>".get_string('synthese','referentiel')."</th>\n";
						$nbchamps+=2;
					}
					if ($this->certificat_sel_param->certificat_sel_activite_competences){
						$s2 .= "<th class='referentiel'>".get_string('competences_declare','referentiel')."</th>\n";
						$nbchamps++;
					}
					if ($this->certificat_sel_param->certificat_sel_certificat_competences){
						$s2 .= "<th class='referentiel'>".get_string('competences_certificat','referentiel')."</th>\n";
						$nbchamps++;
					}
					
			    $s1 .= "<!-- certification : $record->id  -->\n";
					$s1 .= "<table class='referentiel'>\n";
					$s1 .= $this->write_etudiant( $record_etudiant, $nbchamps);
    				
					$s1 .= "<tr class='referentiel'><td class='referentiel' colspan='".$nbchamps."'><b>".get_string('certificat','referentiel')."</b></td></tr>\n";
					$s1 .= "<tr class='referentiel'>\n</tr>\n";
					$s1 .= $s2;
					$s1 .="</tr>\n";

					$s2='';
					if ($this->certificat_sel_param->certificat_sel_decision_jury){
	        		    $s2 .= "<td class='referentiel'>$decision_jury</td>\n";
						if ($date_decision!=""){
					        $s2 .= "<td class='referentiel'>$date_decision</td>\n";
    					}
						else {
							$s2 .= "<td class='referentiel'>&nbsp;</td>\n";
						}
					}
					if ($this->certificat_sel_param->certificat_sel_certificat_referents){
						$s2 .= "<td class='referentiel'>".$nom_prenom_teacher."</td>\n";
    				}
					if ($this->certificat_sel_param->certificat_sel_certificat_detail){
		    		    $s2 .= "<td class='referentiel'>$verrou</td>\n";
			            $s2 .= "<td class='referentiel'>$evaluation</td>\n";
					}
					if ($this->certificat_sel_param->certificat_sel_commentaire){
						$s2 .= "<td class='referentiel'>$commentaire_certificat &nbsp;</td>\n";
						$s2 .= "<td class='referentiel'>$synthese_certificat &nbsp;</td>\n";
					}
					if ($this->certificat_sel_param->certificat_sel_activite_competences){
				    	$s2 .= "<td class='referentiel'>".referentiel_affiche_competences_certificat('/',':',$competences_activite, $this->liste_empreintes_competence)."</td>\n";
					}
					if ($this->certificat_sel_param->certificat_sel_certificat_competences){
	    	    		$s2 .= "<td class='referentiel'>".referentiel_affiche_competences_certificat('/',':',$competences_certificat, $this->liste_empreintes_competence, false)."</td>\n";
					}
					if (($this->certificat_sel_param->certificat_sel_certificat_competences) 
                        && ($this->certificat_sel_param->certificat_sel_certificat_detail)){
						$s2 .= "</tr>\n<tr class='referentiel'>\n<th class='referentiel' colspan='".$nbchamps."'>\n".get_string('certificat_sel_certificat_detail','referentiel')."</th></tr>\n";
						$s2 .= "<tr class='referentiel'>\n<td class='referentiel' colspan='".$nbchamps."'>\n<table class='referentiel'>\n";
						$s2 .= '<tr valign="top"><th>'.get_string('code','referentiel').'</th><th>'.get_string('approved','referentiel').'</th><th colspan="3">'.get_string('description_item','referentiel').'</th><th>'.get_string('p_item','referentiel').'</th><th>'.get_string('e_item','referentiel').'</th></tr>'."\n";
						$s2 .= referentiel_affiche_detail_competences('/',':',$competences_certificat, $this->liste_empreintes_competence, $this->liste_poids_competence)."</table>\n</td>\n";
					}
					if ($this->certificat_sel_param->certificat_sel_certificat_pourcent){
						$s2 .= "</tr>\n<tr class='referentiel'>\n<th class='referentiel' colspan='".$nbchamps."'>\n".get_string('pourcentage','referentiel')."</th></tr>\n";
						$s2 .= "<tr class='referentiel'>\n<td class='referentiel' colspan='".$nbchamps."'>".$pourcentages."</td>\n";
					}					
					$s1 .= "<tr class='referentiel'>\n";
					$s1.=$s2;
					$s1 .= "</tr>\n";
					$s1 .= "</table>\n\n";
					$expout.=$s1;
				}
			}
		}
        return $expout;
    }
	
    /**
     * Turns item into an xml segment
     * @param item object
     * @return string xml segment
     */

    function write_item( $item ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment
        $expout .= "\n\n<!-- item: $item->id  -->\n";
		// 
		if ($item){
			// DEBUG
			// echo "<br />\n";
			// print_r($item);
            $code = $item->code_item;
            $description_item = $item->description_item;
            $ref_referentiel = $item->ref_referentiel;
            $ref_competence = $item->ref_competence;
			$type_item = $item->type_item;
			$poids_item = $item->poids_item;
			$empreinte_item = $item->empreinte_item;
			$num_item = $item->num_item;
            $expout .= "<tr class='referentiel'>";
			$expout .= "<td class='referentiel'>".stripslashes($code)."</td>\n";   
            $expout .= "<td class='referentiel'>".stripslashes($description_item)."</td>\n";
            // $expout .= "<td>".$ref_referentiel."</td>\n";
            // $expout .= "<td>".$ref_competence."</td>\n";
            $expout .= "<td class='referentiel'>".stripslashes($type_item)."</td>\n";
            $expout .= "<td class='referentiel'>".$poids_item."</td>\n";
            $expout .= "<td>".$empreinte_item."</td>\n";
            $expout .= "<td class='referentiel'>".$num_item."</td>\n";			
			$expout .= "</tr>\n";   
        }
		$expout .= "\n";
        return $expout;
    }
	 /**
     * Turns competence into an xml segment
     * @param competence object
     * @return string xml segment
     */

    function write_competence( $competence ) {
    global $CFG;
        // initial string;
        $expout = "";
		if ($competence){
            $code = $competence->code_competence;
            $description_competence = $competence->description_competence;
            $ref_domaine = $competence->ref_domaine;
			$num_competence = $competence->num_competence;
			$nb_item_competences = $competence->nb_item_competences;
			$expout .= "<tr class='referentiel'>\n";	
			$expout .= " <td class='referentiel'>".stripslashes($code)."</td>\n";   
            $expout .= " <td class='referentiel'>".stripslashes($description_competence)."</td>\n";
            // $expout .= " class='referentiel'".$ref_domaine."</td>\n";
            $expout .= " <td class='referentiel'>".$num_competence."</td>\n";
            $expout .= " <td class='referentiel'>".$nb_item_competences."</td>\n";
			$expout .= "</tr>\n";
			
			// ITEM
			$compteur_item=0;
			$records_items = referentiel_get_item_competences($competence->id);
			
			if ($records_items){
				// DEBUG
				// echo "<br/>DEBUG :: ITEMS <br />\n";
				// print_r($records_items);
				$expout .= "<tr class='referentiel'><td colspan='4'>\n";	
            	$expout .= "<table class='referentiel'>\n<tr>\n";
				$expout .= "<th class='referentiel'>".get_string('code','referentiel')."</th>\n";   
            	$expout .= "<th class='referentiel'>".get_string('description_item','referentiel')."</th>\n";
            	// $expout .= "<th>".get_string('ref_referentiel','referentiel')."</th>\n";
	            // $expout .= "<th>".get_string('ref_competence','referentiel')."</th>\n";
    	        $expout .= "<th class='referentiel'>".get_string('type_item','referentiel')."</th>\n";
        	    $expout .= "<th class='referentiel'>".get_string('poids_item','referentiel')."</th>\n";
            	$expout .= "<th class='referentiel'>".get_string('empreinte_item','referentiel')."</th>\n";
	            $expout .= "<th class='referentiel'>".get_string('num_item','referentiel')."</th>\n";			
				$expout .= "</tr>\n";   
				
				foreach ($records_items as $record_i){
					$expout .= $this->write_item( $record_i );
				}
				$expout .= "</table></td></tr>\n";   
			}
        }
        return $expout;
    }


	 /**
     * Turns domaine into an xml segment
     * @param domaine object
     * @return string xml segment
     */

    function write_domaine( $domaine ) {
    global $CFG;
        // initial string;
        $expout = "";
		if ($domaine){
            $code = $domaine->code_domaine;
            $description_domaine = $domaine->description_domaine;
            $ref_referentiel = $domaine->ref_referentiel;
			$num_domaine = $domaine->num_domaine;
			$nb_competences = $domaine->nb_competences;
			
			
			// LISTE DES COMPETENCES DE CE DOMAINE
			$compteur_competence=0;
			$records_competences = referentiel_get_competences($domaine->id);
			if ($records_competences){
				$expout .= "<tr class='referentiel'>\n";			
				$expout .= "   <td class='referentiel'>".stripslashes($code)."</td>\n";   
        	    $expout .= "   <td class='referentiel'>".stripslashes($description_domaine)."</td>\n";
            	// $expout .= "   <td class='referentiel'>".$ref_referentiel</td>\n";
	            $expout .= "   <td class='referentiel'>".$num_domaine."</td>\n";
    	        $expout .= "   <td class='referentiel'>".$nb_competences."</td>\n";
				$expout .= "</tr>\n";
				
				foreach ($records_competences as $record_c){
					$expout .= "<tr class='referentiel'>\n";	
					$expout .= "<th class='referentiel'>".get_string('code_competence','referentiel')."</th>\n";   
            		$expout .= "<th class='referentiel'>".get_string('description_competence','referentiel')."</th>\n";
            		// $expout .= "<th class='referentiel'>".get_string('ref_domaine','referentiel')."</th>\n";
            		$expout .= "<th class='referentiel'>".get_string('num_competence','referentiel')."</th>\n";
            		$expout .= "<th class='referentiel'>".get_string('nb_item_competences','referentiel')."</th>\n";
					$expout .= "</tr>\n";
					$expout .= $this->write_competence( $record_c );
				}
			}
        }
        return $expout;
    }



	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_certification() {
    	global $CFG;
		
		$nbchamps=0;
        // initial string;
	    $expout = "";
		

		if (($this->referentiel_referentiel) && ($this->referentiel_instance)){
      $name = trim($this->referentiel_referentiel->name);
      $code = trim($this->referentiel_referentiel->code_referentiel);
			$description = trim($this->referentiel_referentiel->description_referentiel);
			
			$id = $this->referentiel_instance->id;
      $name_instance = trim($this->referentiel_instance->name);
      $description_instance = trim($this->referentiel_instance->description_instance);
      $label_domaine = trim($this->referentiel_instance->label_domaine);
      $label_competence = trim($this->referentiel_instance->label_competence);
      $label_item = trim($this->referentiel_instance->label_item);
      $date_instance = userdate($this->referentiel_instance->date_instance);
      $course = $this->referentiel_instance->course;
      $ref_referentiel = $this->referentiel_instance->ref_referentiel;
			$visible = $this->referentiel_instance->visible;
			
			// add comment and div tags
			$expout .= "<!-- certification-->\n";
	    $expout .= "<h2>$name - ($code)</h2>\n";
			$expout .= "<p>$description</p>\n";

			$s='';
			if ($this->certificat_sel_param->certificat_sel_referentiel_instance){
				//$expout .= "<th class='referentiel'>id</th>\n";
				$s.= "<th class='referentiel'>".get_string('name_instance','referentiel')."</th>\n";
				$s.= "<th class='referentiel'>".get_string('description_instance','referentiel')."</th>\n";
				$s.= "<th class='referentiel'>".get_string('label_domaine','referentiel')."</th>\n";
				$s.= "<th class='referentiel'>".get_string('label_competence','referentiel')."</th>\n";
				$s.= "<th class='referentiel'>".get_string('label_item','referentiel')."</th>\n";
				$s.= "<th class='referentiel'>".get_string('date_instance','referentiel')."</th>\n";
				$s.= "<th class='referentiel'>".get_string('course')."</th>\n";
				$s.= "<th class='referentiel'>".get_string('ref_referentiel','referentiel')."</th>\n";
				$s.= "<th class='referentiel'>".get_string('visible','referentiel')."</th>\n";
				$nbchamps+=9;
			}
			
			if ($nbchamps==0) $nbchamps=1;
			
			if ($this->certificat_sel_param->certificat_sel_referentiel
				&& isset($this->referentiel_referentiel->id) && ($this->referentiel_referentiel->id>0)){
				
				// DOMAINES
				// LISTE DES DOMAINES
				$compteur_domaine=0;
				$records_domaine = referentiel_get_domaines($this->referentiel_referentiel->id);
		    if ($records_domaine){
					$expout .= "<table class='referentiel'>\n";
					$expout .= "<tr class='referentiel'><th class='referentiel' colspan='".$nbchamps."'>".get_string('detail_referentiel','referentiel')."</th></tr>\n";
					$expout .= "<tr class='referentiel'><td class='referentiel' colspan='".$nbchamps."'>\n";
					$expout .= "<table class='referentiel'>\n";
					
					foreach ($records_domaine as $record_d){
						$expout .= "<tr class='referentiel'>\n";
						$expout .= "<th class='referentiel'>".get_string('code_domaine','referentiel')."</th>\n";   
            			$expout .= "<th class='referentiel'>".get_string('description_domaine','referentiel')."</th>\n";
	            		// $expout .= "<th class='referentiel'>".get_string('ref_referentiel','referentiel')."</th>\n";
    	        		$expout .= "<th class='referentiel'>".get_string('num_domaine','referentiel')."</th>\n";
        	    		$expout .= "<th class='referentiel'>".get_string('nb_competences','referentiel')."</th>\n";
						$expout .= "</tr>\n";
						
						$expout .= $this->write_domaine($record_d );
					}
					$expout .= "</table>\n</td></tr>\n";
				}
			} 
			


			if ($s!=''){
				$expout .= "<tr class='referentiel'>\n";
				$expout .= $s;
				$expout .= "</tr>\n";
			}
			
			$s='';
			
			if ($this->certificat_sel_param->certificat_sel_referentiel_instance){
				// $expout .= " <td class='referentiel'>$id</td>\n";
				$s .= " <td class='referentiel'>$name_instance</td>\n";
				$s .= " <td class='referentiel'>$description_instance</td>\n";   
        $s .= " <td class='referentiel'>$label_domaine</td>\n";
        $s .= " <td class='referentiel'>$label_competence</td>\n";
	      $s .= " <td class='referentiel'>$label_item</td>\n";			
    	  $s .= " <td class='referentiel'>$date_instance</td>\n";
        $s .= " <td class='referentiel'>$course</td>\n";
	      $s .= " <td class='referentiel'>$ref_referentiel</td>\n";
    	  $s .= " <td class='referentiel'>$visible</td>\n";
			}
			if ($s!=''){
				$expout .= "<tr class='referentiel'>\n";
				$expout .= $s;
				$expout .= "</tr>\n";
			}
			

			$expout .= "</table>\n";
			
			// CERTIFICATS

		    	if ($this->records_certificats){
					 foreach ($this->records_certificats as $record){
						if ($record){
							$expout .= $this->write_certificat( $record);
						}
					 }
				  }

      }
      return $expout;
    }
}


?>

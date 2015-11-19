<?php 
// Based on default.php, included by ../import.php


// ACTIVITES</td><td class='referentiel'>export des activites
class pprint_csv extends pprint_default {

	var $sep = ";";
	
	var $table_caractere_input='latin1'; // par defaut import latin1
	var $table_caractere_output='latin1'; // par defaut export latin1
	
	// ----------------
	function purge_sep($texte){
		$cherche= array($this->sep, "\r\n", "\n", "\r");
		$remplace= array(",",  " ", " ", " ");
		return str_replace($cherche, $remplace, $texte);
	}

	// ----------------
	function recode_latin1_vers_utf8($string) {
		return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}


	// ----------------
	function recode_utf8_vers_latin1($string) {
		return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}
	

	 /**
     * @param 
     * @return string recode latin1
	 * 
     */
    function input_codage_caractere($s){
		if (!isset($this->table_caractere_input) || ($this->table_caractere_input=="")){
			$this->table_caractere_input='latin1';
		}
		
		if ($this->table_caractere_input=='latin1'){
			$s=$this->recode_latin1_vers_utf8($s);
		}
		return $s;
	}
	
	 /**
     * @param 
     * @return string recode utf8
	 * 
     */
    function output_codage_caractere($s){
		if (!isset($this->table_caractere_output) || ($this->table_caractere_output=="")){
			$this->table_caractere_output='latin1';
		}
		
		if ($this->table_caractere_output=='latin1'){
			$s=$this->recode_utf8_vers_latin1($s);
		}
		return $s;
	}


// -------------------
function affiche_certificat_consolide($separateur1, $separateur2, $liste_code, $ref_referentiel, $bgcolor, $params=NULL){
// ce certificat comporte des pourcentages par domaine et competence

global $OK_REFERENTIEL_DATA;
global $t_domaine;
global $t_domaine_coeff;
		
// COMPETENCES
global $t_competence;
global $t_competence_coeff;
		
// ITEMS
global $t_item_code;
global $t_item_coeff; // coefficient poids determeine par le modele de calcul (soit poids soit poids / empreinte)
global $t_item_domaine; // index du domaine associ� � un item 
global $t_item_competence; // index de la competence associ�e � un item 
global $t_item_poids; // poids
global $t_item_empreinte;
global $t_nb_item_domaine;
global $t_nb_item_competence;

	// nom des domaines, comp�tences, items
	$label_d="";
	$label_c="";
	$label_i="";
	if (isset($params) && !empty($params)){
		if (isset($params->label_domaine)){
					$label_d=$params->label_domaine;
		}
		if (isset($params->label_competence)){
					$label_c=$params->label_competence;
		}
		if (isset($params->label_item)){
					$label_i=$params->label_item;
		}
	}
	$t_certif_item_valeur=array();	// table des nombres d'items valides 
	$t_certif_item_coeff=array(); // somme des poids du domaine
	$t_certif_competence_poids=array(); // somme des poids de la competence
	$t_certif_domaine_poids=array(); // poids certifies
	for ($i=0; $i<count($t_item_code); $i++){
		$t_certif_item_valeur[$i]=0.0;
		$t_certif_item_coeff[$i]=0.0;
	}
	for ($i=0; $i<count($t_competence); $i++){
		$t_certif_competence_poids[$i]=0.0;
	}
	for ($i=0; $i<count($t_domaine); $i++){
		$t_certif_domaine_poids[$i]=0.0;
	}
	// affichage
	$s='';
		
	// donnees globales du referentiel
	if ($ref_referentiel){
		
		if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
			$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($ref_referentiel);
		}

		if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){
		// DEBUG 
		// echo "<br />CODE <br />\n";
		// referentiel_affiche_data_referentiel($ref_referentiel, $params);
		
		// recuperer les items valides
		$tc=array();
		$liste_code=referentiel_purge_dernier_separateur($liste_code, $separateur1);
			
		// DEBUG 
		// echo "<br />DEBUG :: print_lib_certificat.php :: 917 :: LISTE : $liste_code<br />\n";

		if (!empty($liste_code) && ($separateur1!="") && ($separateur2!="")){
			$tc = explode ($separateur1, $liste_code);
			
			// DEBUG 
			/*
			echo "<br />DEBUG print_lib_certificat_.php :: Ligne 1090 <br />LISTE DECOMPOSEE CODE :: $liste_code<br />\n";
			print_r($tc);
			echo "<br />POIDS<br />\n";
			print_r($t_item_poids);
			echo "<br />EMPREINTES<br />\n";
			print_r($t_item_empreinte);
			echo "<br /><br />\n";
			
			echo "<br />INNDEX DOMAINES<br />\n";
			print_r($t_item_domaine);
			echo "<br />INDEX COMPETENCE<br />\n";
			print_r($t_item_competence);
			// exit;
			*/
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
							$t_certif_domaine_poids[$t_item_domaine[$i]]+=$coeff;
							// stocker le taux pour le domaine
							$t_certif_competence_poids[$t_item_competence[$i]]+=$coeff;
						}
						else{
							// echo "<br />".min($t_cc[1],$t_item_empreinte[$i]);
							$t_certif_item_valeur[$i]=0.0;
							$t_certif_item_coeff[$i]=0.0;
							// $t_certif_domaine_poids[$t_item_domaine[$i]]+=0.0;
							// $t_certif_competence_poids[$t_item_competence[$i]]+=0.0;
						}
					}
				}
				
				$i++;
			}
			
			// DEBUG 
			/*
			echo "<br />DEBUG :: Ligne :: 1107<br />\n";
			echo "<br />Liste des items valides <br />\n";
			print_r($t_certif_item_valeur);
			echo "<br />Taux : poids / empreinte<br />\n";
			print_r($t_certif_item_coeff);
			echo "<br /><br />\n";
			print_r($t_certif_domaine_poids);
			echo "<br /><br />\n";
			print_r($t_certif_competence_poids);
			exit;
			*/
			
			// DOMAINES
			// $s.= '<table width="100%" cellspacing="0" cellpadding="2"><tr valign="top" >'."\n";
			// if (!empty($label_d)){
			//	$s.='<td  width="5%">'.$label_d.'</td>';
			//}
			// else {
			//	$s.='<td $t_certif_item_coeff width="5%">'.get_string('domaine','referentiel').'</td>';
			//}
			for ($i=0; $i<count($t_domaine_coeff); $i++){
				if ($t_domaine_coeff[$i]){
					$s.=$t_domaine[$i].';';
				}
				else{
					$s.=$t_domaine[$i].';';
				}
			}
			$s.="\n";
			for ($i=0; $i<count($t_domaine_coeff); $i++){
				if ($t_domaine_coeff[$i]){
					$s.=referentiel_pourcentage($t_certif_domaine_poids[$i], $t_domaine_coeff[$i]).'%;';
				}
				else{
					$s.='0%;';
				}
			}
			$s.="\n";

			/*
			if (!empty($label_c)){
				$s.='<td  width="5%">'.$label_c.'</td>'."\n";
			}
			else {
				$s.='<td  width="5%">'.get_string('competence','referentiel').'</td>'."\n";
			}
			*/
			for ($i=0; $i<count($t_competence); $i++){
				if ($t_competence_coeff[$i]){
					$s.=$t_competence[$i].';';
				}
				else{
					$s.=$t_competence[$i].';';
				}
			}
			$s.="\n";
			for ($i=0; $i<count($t_competence); $i++){
				if ($t_competence_coeff[$i]){
					$s.=referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i]).'%;';
				}
				else{
					$s.='0%;';
				}
			}
			$s.="\n";
			
			// ITEMS
			// $s.= '<tr valign="top" >'."\n";
			/*
			if (!empty($label_i)){
				$s.='<td  width="5%">'.$label_i.'</td>'."\n";
			}
			else {
				$s.='<td  width="5%">'.get_string('item','referentiel').'</td>'."\n";
			}
			*/
			for ($i=0; $i<count($t_item_code); $i++){
				if ($t_item_empreinte[$i]){
					if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i])
						$s.=$t_item_code[$i].';';
					else
						$s.=$t_item_code[$i].';';
				}
				else{
					$s.=';';
				}
			}
			$s.="\n";
			
			// <td  width="5%">'.get_string('coeff','referentiel').'</td>'."\n";
			for ($i=0; $i<count($t_item_coeff); $i++){
				if ($t_item_empreinte[$i]){
					if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
						$s.='100%;';
					}
					else{
						$s.=referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i]).'%;';
					}
				}
				else {
					$s.=';';
				}
			}
			$s.="\n";
			/* 
			$s.='</tr><tr valign="top">'."\n";
			$s.='<td  width="5%">'.get_string('item_poids','referentiel').'</td>';
			for ($i=0; $i<count($t_item_poids); $i++){
				$s.='<td >'.$t_item_poids[$i].'</td>';
			}
			$s.='</tr><tr valign="top">'."\n";
			$s.='<td  width="5%">'.get_string('item_empreinte','referentiel').'</td>';		
			for ($i=0; $i<count($t_item_empreinte); $i++){
				$s.='<td >'.$t_item_empreinte[$i].'</td>';
			}
            */
			$s.="\n";
		}
	}
	}
	
	return $s;
}

// ----------------------------------------------------
function jauge_activite($valide, $empreinte){
// ecrit un tableau dont le nombre de cases est proportionnel � la valeur de l'empreinte
// remplit ce tableau avec des cases colorees en indiquant le nombre de validation obtenues / a obtenir
	$s='';
	if ($valide==0)	{
		$s.='0;'.$empreinte.';';
	}
	else if ($valide<$empreinte){
		$reste=$empreinte-$valide;
		$s.=$valide.';'.$reste.';';
	}
	else if ($valide>=$empreinte){
		$s.=$valide.';0;';
	}		
	return $s;
}


// ----------------------------------------------------
function affiche_competences_certificat($separateur1, $separateur2, $liste, $liste_empreintes, $invalide=true){
// Affiche les codes competences en tenant compte de l'empreinte
// si detail = true les comp�tences non validees sont aussi affichees
	$t_empreinte=explode($separateur1, $liste_empreintes);
  $s1='';
	$s2='';
	$s3='';
	$tc=array();
	
	$liste=referentiel_purge_dernier_separateur($liste, $separateur1);
	if (!empty($liste) && ($separateur1!="") && ($separateur2!="")){
			$tc = explode ($separateur1, $liste);
			// DEBUG 
			// echo "<br />CODE <br />\n";
			// print_r($tc);
			$i=0;
			while ($i<count($tc)){
				// CODE1:N1
				// DEBUG 
				// echo "<br />".$tc[$i]." <br />\n";
				// exit;
				if ($tc[$i]!=''){
					$tcc=explode($separateur2, $tc[$i]);
					// echo "<br />".$tc[$i]." <br />\n";
					// print_r($tcc);
					// exit;
					
					if (isset($tcc[1]) && ($tcc[1]>=$t_empreinte[$i])){
						$s1.=$tcc[0].';';						
						if ($invalide==true){
              $s2.=' '.$tcc[1].' [/'.$t_empreinte[$i].'];';
					    $s3.='1;';
            }
          }
					elseif ($invalide==true){
						$s1.=$tcc[0].';';
						$s2.=' '.$tcc[1].' [/'.$t_empreinte[$i].'];';
						$s3.='0;';
					}
				}
				$i++;
			} 
		}
		$s=get_string('competences','referentiel').';'.$s1."\n";
		if ($s2) $s.=get_string('valide_empreinte','referentiel').';'.$s2."\n";
		if ($s3) $s.=get_string('competence_certifiee','referentiel').';'.$s3."\n";
	return $s;
}

// ----------------------------------------------------
function affiche_detail_competences($separateur1, $separateur2, $liste, $liste_empreintes, $liste_poids){

	$t_empreinte=explode($separateur1, $liste_empreintes);
	$t_poids=explode('|', $liste_poids);	
	// DEBUG
	// echo "<br />DEBUG : print_lib_certificat.php :: 105<br />LISTE EMPREINTES : $liste_empreintes<br />\n";
	// print_r($t_empreinte);
	// DEBUG
	// echo "<br />DEBUG : print_lib_certificat.php :: 108<br />LISTE POIDS : $liste_poids<br />\n";
	// print_r($t_poids);
	// exit;
	$s='';
	$tc=array();
	$liste=referentiel_purge_dernier_separateur($liste, $separateur1);
		if (!empty($liste) && ($separateur1!="") && ($separateur2!="")){
			$tc = explode ($separateur1, $liste);
			// DEBUG 
			// echo "<br />CODE <br />\n";
			// print_r($tc);
			$i=0;
			while ($i<count($tc)){
				// CODE1:N1
				// DEBUG 
				// echo "<br />".$tc[$i]." <br />\n";
				// exit;
				if ($tc[$i]!=''){
					$tcc=explode($separateur2, $tc[$i]);
					// echo "<br />".$tc[$i]." <br />\n";
					// print_r($tcc);
					// exit;
					// $s.='<tr>'."\n";
					
					if (isset($tcc[1]) && ($tcc[1]>=$t_empreinte[$i])){
						$s.=$tcc[0];
						$s.=$this->jauge_activite($tcc[1], $t_empreinte[$i]);
						$s.=str_replace('#',';',$t_poids[$i]).';';
					}
					else{
						$s.=$tcc[0];
						$s.=$this->jauge_activite($tcc[1], $t_empreinte[$i]);
						$s.=str_replace('#',';',$t_poids[$i]).';';
					}
					$s.=$t_empreinte[$i].';';
				}
				$i++;
			} 
			$s.="\n";
		}
	return $s;
}



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
		$xp =  "#Moodle Certification CSV Export;latin1;\n";
		$xp .= $content;
  	return $xp;
	}

	function export_file_extension() {
  		return ".csv";
	}

/*
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
*/
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

	function write_etablissement( $record ) {
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\netablissement: $record->id\n";
		if ($record){
			$expout .= "#id_etablissement";
			$expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('num_etablissement','referentiel'))))).";";
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('nom_etablissement','referentiel'))))).";";
      // $expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('logo','referentiel'))))).";";
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('adresse_etablissement','referentiel'))))).";";
			$expout .= "\n";
            
      $id = trim( $record->id );
			$num_etablissement = trim( $record->num_etablissement);
			$nom_etablissement = trim( $this->purge_sep($record->nom_etablissement));
			$adresse_etablissement = trim( $this->purge_sep($record->adresse_etablissement));
			
			$expout .= "$id;$num_etablissement;".stripslashes($this->output_codage_caractere($nom_etablissement)).";".stripslashes($this->output_codage_caractere($adresse_etablissement))."\n";
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

			$s2='';
			$s2 .= $id.';';
			if ($this->certificat_sel_param->certificat_sel_etudiant_nom_prenom){
				$s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(referentiel_get_user_nom($record->userid))).";".$this->purge_sep(stripslashes(referentiel_get_user_prenom($record->userid))))).";";
			}

			if ($this->certificat_sel_param->certificat_sel_etudiant_numero){
				$s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($texte)))).";";
			}
			if ($this->certificat_sel_param->certificat_sel_etudiant_ddn){
				$s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($ddn_etudiant)))).";";
			}
			if ($this->certificat_sel_param->certificat_sel_etudiant_lieu_naissance){
	            $s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($lieu_naissance)))).";";
    			$s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($departement_naissance)))).";";			
            }
			if ($this->certificat_sel_param->certificat_sel_etudiant_adresse){
				$s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($adresse_etudiant)))).";";
			}
			$s1.=$s2;
			$s1 .= " \n";

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
		$s3='';
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
                // ligne des certificat
                $pourcentages=$this->affiche_certificat_consolide('/',':',$competences_certificat, $ref_referentiel, ' class="'.$bgcolor.'"');
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
                    // ligne de presentation des champs etudiant
                    $s3='';
          
                    $s3 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes('#'.get_string('etudiant','referentiel'))))).";\n";
                    $s3 .= "#id;";
                    if ($this->certificat_sel_param->certificat_sel_etudiant_nom_prenom){
                        $s3 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('lastname'))).";".$this->purge_sep(stripslashes(get_string('firstname'))))).";";
                    }

                    if ($this->certificat_sel_param->certificat_sel_etudiant_numero){
                        $s3 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('num_etudiant','referentiel'))))).";";
                    }
			        if ($this->certificat_sel_param->certificat_sel_etudiant_ddn){
				        $s3 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('ddn_etudiant','referentiel'))))).";";
			        }
			        if ($this->certificat_sel_param->certificat_sel_etudiant_lieu_naissance){
				        $s3.= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('lieu_naissance','referentiel'))))).";";
				        $s3.= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('departement_naissance','referentiel'))))).";";
			        }
			        if ($this->certificat_sel_param->certificat_sel_etudiant_adresse){
				        $s3.= $this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('adresse_etudiant','referentiel'))))).";";
			        }
			        $s3 .= "\n";
			    
			        $s1.=$s3;
          // fin ligne presentation      		
					$s1 .= $this->write_etudiant( $record_etudiant, $nbchamps);	
					$s1 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes('#'.get_string('certificat','referentiel'))))).";\n";

					$s2='';
					
					if ($this->certificat_sel_param->certificat_sel_decision_jury){
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep('#'.get_string('decision','referentiel')))).";";
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep(get_string('datedecision','referentiel')))).";";
						$s2.="\n";

                        $s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($decision_jury)))).";";
						if ($date_decision!=""){
					        $s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($date_decision)))).";";
    					}
						else {
							$s2 .= ";";
						}
						$s2 .= "\n";
					}
					
					if ($this->certificat_sel_param->certificat_sel_certificat_referents){
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep('#'.get_string('valide_par','referentiel')))).";";
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($nom_prenom_teacher)))).";\n";
    			    }
    			
					
					if ($this->certificat_sel_param->certificat_sel_certificat_detail){
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep('#'.get_string('verrou','referentiel')))).";";
						$s2 .= $verrou.";";
                        $s2 .= $this->output_codage_caractere(trim($this->purge_sep(get_string('evaluation','referentiel')))).";";
                        $s2 .= $evaluation.";";
                        $s2 .= "\n";
					}
					if ($this->certificat_sel_param->certificat_sel_commentaire){
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep('#'.get_string('commentaire','referentiel')))).";\n";
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($commentaire_certificat)))).";\n";
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep('#'.get_string('synthese_certificat','referentiel')))).";\n";
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($synthese_certificat)))).";\n";
					}
					if ($this->certificat_sel_param->certificat_sel_activite_competences){
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep('#'.get_string('competences_declare','referentiel')))).";\n";
				        $s2 .= $this->output_codage_caractere($this->affiche_competences_certificat('/',':',$competences_activite, $this->liste_empreintes_competence, true)).";\n";
					}
					if ($this->certificat_sel_param->certificat_sel_certificat_competences){
						$s2 .= $this->output_codage_caractere(trim($this->purge_sep('#'.get_string('competences_certificat','referentiel')))).";\n";
					    $s2 .= $this->output_codage_caractere($this->affiche_competences_certificat('/',':',$competences_certificat, $this->liste_empreintes_competence, true)).";\n";
                    }
					if (($this->certificat_sel_param->certificat_sel_certificat_competences) 
                        && ($this->certificat_sel_param->certificat_sel_certificat_detail)){
						$s2 .= "\n#".$this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('certificat_sel_certificat_detail','referentiel'))))).";\n";
						$s2 .= "#".$this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('code','referentiel'))))).';'.$this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('approved','referentiel'))))).';'.$this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('description_item','referentiel'))))).';'.$this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('p_item','referentiel'))))).';'.$this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('e_item','referentiel'))))).';'."\n";
						$s2 .= $this->output_codage_caractere($this->affiche_detail_competences('/',':',$competences_certificat, $this->liste_empreintes_competence, $this->liste_poids_competence)).";\n";
					}
					if ($this->certificat_sel_param->certificat_sel_certificat_pourcent){
						$s2 .= "\n#".$this->output_codage_caractere(trim($this->purge_sep(stripslashes(get_string('pourcentage','referentiel'))))).";\n";
						$s2 .= $pourcentages.";\n";
					}					
					$s1.=$s2;
					$s1 .= "\n\n";
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
		// 
		if ($item){
			// DEBUG
			// echo "<br />\n";
			// print_r($item);
			$id=$item->id;
      $code = $item->code_item;
      $description_item = $item->description_item;
      $ref_referentiel = $item->ref_referentiel;
      $ref_competence = $item->ref_competence;
			$type_item = $item->type_item;
			$poids_item = $item->poids_item;
			$empreinte_item = $item->empreinte_item;
			$num_item = $item->num_item;
			
      $expout .= "$id;";
			$expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($code)))).";";   
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($description_item)))).";";
      // $expout .= $ref_referentiel.";";
      // $expout .= $ref_competence.";";
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($type_item)))).";";
      $expout .= $poids_item.";";
      $expout .= $empreinte_item.";";
      $expout .= $num_item.";";			
			$expout .= "\n";   
    }
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
		  $id_competence=$competence->id;
      $code = $competence->code_competence;
      $description_competence = $competence->description_competence;
      $ref_domaine = $competence->ref_domaine;
			$num_competence = $competence->num_competence;
			$nb_item_competences = $competence->nb_item_competences;

			$expout .= "#id_competence;";
  		$expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('code_competence','referentiel')))).";";   
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('description_competence','referentiel')))).";";
      // $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('ref_domaine','referentiel')))).";";
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('num_competence','referentiel')))).";";
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('nb_item_competences','referentiel')))).";";
			$expout .= "\n";
	
	    $expout .= $id_competence.";";
			$expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($code)))).";";   
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($description_competence)))).";";
      // $expout .= $ref_domaine.";";
      $expout .= $num_competence.";";
      $expout .= $nb_item_competences.";";
			$expout .= "\n";
			
			// ITEM
			$compteur_item=0;
			$records_items = referentiel_get_item_competences($competence->id);
			
			if ($records_items){
				// DEBUG
				// echo "<br/>DEBUG :: ITEMS <br />\n";
				// print_r($records_items);
        $expout .= "#id_item;";
				$expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('code','referentiel')))).";";   
        $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('description_item','referentiel')))).";";
        // $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('ref_referentiel','referentiel')))).";";
	      // $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('ref_competence','referentiel')))).";";
    	  $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('type_item','referentiel')))).";";
        $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('poids_item','referentiel')))).";";
        $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('empreinte_item','referentiel')))).";";
	      $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('num_item','referentiel')))).";";			
				$expout .= "\n";   
				
				foreach ($records_items as $record_i){
					$expout .= $this->write_item( $record_i );
				}
				$expout .= "\n";   
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
		  $id_domaine = $domaine->id;
      $code = $domaine->code_domaine;
      $description_domaine = $domaine->description_domaine;
      $ref_referentiel = $domaine->ref_referentiel;
			$num_domaine = $domaine->num_domaine;
			$nb_competences = $domaine->nb_competences;
			
	    $expout .= "#id_domaine;";
  		$expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('code_domaine','referentiel')))).";";   
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('description_domaine','referentiel')))).";";
      // $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('ref_referentiel','referentiel')))).";";
 	    $expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('num_domaine','referentiel')))).";";
     	$expout .= $this->output_codage_caractere(trim($this->purge_sep(get_string('nb_competences','referentiel')))).";";
			$expout .= "\n";

     	$expout .= "$id_domaine;";	
			$expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($code)))).";";   
      $expout .= $this->output_codage_caractere(trim($this->purge_sep(stripslashes($description_domaine)))).";";
      // $expout .= "$ref_referentiel;";
      $expout .= $num_domaine.";";
   	  $expout .= $nb_competences.";";
			$expout .= "\n";			
			
			// LISTE DES COMPETENCES DE CE DOMAINE
			$compteur_competence=0;
			$records_competences = referentiel_get_competences($domaine->id);
			if ($records_competences){	        				
				foreach ($records_competences as $record_c){
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
      $id_referentiel = trim($this->referentiel_referentiel->id);
      $name = trim($this->referentiel_referentiel->name);
      $code = trim($this->referentiel_referentiel->code_referentiel);
			$description = trim($this->referentiel_referentiel->description_referentiel);
			
			$id_instance = $this->referentiel_instance->id;
      $name_instance = trim($this->referentiel_instance->name);
      $description_instance = trim($this->referentiel_instance->description_instance);
      $label_domaine = trim($this->referentiel_instance->label_domaine);
      $label_competence = trim($this->referentiel_instance->label_competence);
      $label_item = trim($this->referentiel_instance->label_item);
      $date_instance = userdate($this->referentiel_instance->date_instance);
      $course = $this->referentiel_instance->course;
      $ref_referentiel = $this->referentiel_instance->ref_referentiel;
			$visible = $this->referentiel_instance->visible;
			
			// 
			// add comment and div tags
	    $expout .= $this->output_codage_caractere(trim(get_string('name', 'referentiel'))).";";
			$expout .= $this->output_codage_caractere(trim(get_string('description', 'referentiel'))).";\n";
			
	    $expout .= $this->output_codage_caractere(trim($this->purge_sep($name.' - ('.$code.')'))).";";
			$expout .= $this->output_codage_caractere(trim($this->purge_sep($description))).";\n";

			$s='';
			if ($this->certificat_sel_param->certificat_sel_referentiel_instance){
				$s.= "#id_instance;";
				$s.= $this->output_codage_caractere(trim($this->purge_sep(get_string('name_instance','referentiel')))).";";
				$s.= $this->output_codage_caractere(trim($this->purge_sep(get_string('description_instance','referentiel')))).";";
				$s.= $this->output_codage_caractere(trim($this->purge_sep(get_string('label_domaine','referentiel')))).";";
				$s.= $this->output_codage_caractere(trim($this->purge_sep(get_string('label_competence','referentiel')))).";";
				$s.= $this->output_codage_caractere(trim($this->purge_sep(get_string('label_item','referentiel')))).";";
				$s.= $this->output_codage_caractere(trim($this->purge_sep(get_string('date_instance','referentiel')))).";";
				$s.= get_string('course').";";
				$s.= get_string('ref_referentiel','referentiel').";";
				$s.= $this->output_codage_caractere(trim($this->purge_sep(get_string('visible','referentiel')))).";";
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
					foreach ($records_domaine as $record_d){						
						$expout .= $this->write_domaine($record_d );
					}
					$expout .= "\n";
				}
			} 
			


			if ($s!=''){
				$expout .= $s;
				$expout .= "\n";
			}
			
			$s='';
			
			if ($this->certificat_sel_param->certificat_sel_referentiel_instance){
				$s .= "$id_instance;";
				$s .= $this->output_codage_caractere(trim($this->purge_sep($name_instance))).";";
				$s .= $this->output_codage_caractere(trim($this->purge_sep($description_instance))).";";   
                $s .= $this->output_codage_caractere(trim($this->purge_sep($label_domaine))).";";
                $s .= $this->output_codage_caractere(trim($this->purge_sep($label_competence))).";";
                $s .= $this->output_codage_caractere(trim($this->purge_sep($label_item))).";";
                $s .= $this->output_codage_caractere(trim($this->purge_sep($date_instance))).";";
                $s .= "$course;";
                $s .= "$ref_referentiel;";
                $s .= $this->output_codage_caractere(trim($this->purge_sep($visible))).";";
			}
			if ($s!=''){
				$expout .= $s."\n";
			}
			
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

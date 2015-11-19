<?php 
// Based on default.php, included by ../import.php

class rformat_xhtml extends rformat_default {

    function provide_export() {
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
		global $USER; 
  		// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/xhtml/xhtml.css" );
		$css = implode( ' ',$css_lines ); 
		$xp =  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$xp .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
		$xp .= "<meta author=\"".referentiel_get_user_info($USER->id)."\">\n";
  		$xp .= "<title>Moodle Referentiel XHTML Export</title>\n";
  		$xp .= $css;
  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function export_file_extension() {
  		return "_x.html";
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

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
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
        $expout .= "\n\n<!-- item: $item->id  -->
<div class='item'>\n";
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
            $expout .= "   <ul>\n";
			$expout .= "     <li><b>".get_string('code','referentiel')."</b> : ".stripslashes($code)."</li>\n";
            $expout .= "     <li><b>".get_string('description_item','referentiel')."</b> : ".stripslashes($description_item)."</li>\n";
            // $expout .= "     <li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
            // $expout .= "     <li><b>".get_string('ref_competence','referentiel')."</b> : $ref_competence</li>\n";
            $expout .= "     <li><b>".get_string('type_item','referentiel')."</b> : ".stripslashes($type_item)."</li>\n";
            $expout .= "     <li><b>".get_string('poids_item','referentiel')."</b> : $poids_item</li>\n";
            $expout .= "     <li><b>".get_string('empreinte_item','referentiel')."</b> : $empreinte_item</li>\n";
            $expout .= "     <li><b>".get_string('num_item','referentiel')."</b> : $num_item</li>\n";
			$expout .= "   </ul>\n";
        }
		$expout .= "</div>\n";
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
        // add comment
        $expout .= "\n\n<!-- competence: $competence->id  -->
<div class='competence'>\n";
		//

		if ($competence){
            $code = $competence->code_competence;
            $description_competence = $competence->description_competence;
            $ref_domaine = $competence->ref_domaine;
			$num_competence = $competence->num_competence;
			$nb_item_competences = $competence->nb_item_competences;

// MODIF 2012/03/08
			$type_competence = trim($competence->type_competence);
			$seuil_competence = trim($competence->seuil_competence);

// MODIF 2012/03/08
			$minima_competence = trim($competence->minima_competence);

			$expout .= "  <ul>\n";
			$expout .= "    <li><b>".get_string('code_competence','referentiel')."</b> : ".stripslashes($code)."</li>\n";
            $expout .= "    <li><b>".get_string('description_competence','referentiel')."</b> : ".stripslashes($description_competence)."</li>\n";
            // $expout .= "    <li><b>".get_string('ref_domaine','referentiel')."</b> : $ref_domaine</li>\n";
// MODIF 2012/03/08
            $expout .= "   <li><b>".get_string('type_competence','referentiel')."</b> : $type_competence</li>\n";
            $expout .= "   <li><b>".get_string('seuil_competence','referentiel')."</b> : $seuil_competence</li>\n";
// MODIF 2012/03/08
            $expout .= "   <li><b>".get_string('minima_competence','referentiel')."</b> : $minima_competence</li>\n";

            $expout .= "    <li><b>".get_string('num_competence','referentiel')."</b> : $num_competence</li>\n";
            $expout .= "    <li><b>".get_string('nb_item_competences','referentiel')."</b> : $nb_item_competences</li>\n";

			// ITEM
			$compteur_item=0;
			$records_items = referentiel_get_item_competences($competence->id);

			if ($records_items){
				// DEBUG
				// echo "<br/>DEBUG :: ITEMS <br />\n";
				// print_r($records_items);
				foreach ($records_items as $record_i){
					// DEBUG
					// echo "<br/>DEBUG :: ITEM <br />\n";
					// print_r($record_i);
					$expout .= $this->write_item( $record_i );
				}
			}
			$expout .= "  </ul>\n";
        }
		$expout .= "</div>\n";
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
        // add comment
        $expout .= "\n\n<!-- domaine: $domaine->id  -->
<div class='domaine'>\n";
		//

		if ($domaine){
            $code = $domaine->code_domaine;
            $description_domaine = $domaine->description_domaine;
            $ref_referentiel = $domaine->ref_referentiel;
			$num_domaine = $domaine->num_domaine;
			$nb_competences = $domaine->nb_competences;
// MODIF 2012/03/08
			$type_domaine = trim($domaine->type_domaine);
			$seuil_domaine = trim($domaine->seuil_domaine);
// MODIF 2012/03/26
			$minima_domaine = trim($domaine->minima_domaine);

			$expout .= "<ul>\n";
			$expout .= "   <li><b>".get_string('code_domaine','referentiel')."</b> : ".stripslashes($code)."</li>\n";
            $expout .= "   <li><b>".get_string('description_domaine','referentiel')."</b> : ".stripslashes($description_domaine)."</li>\n";
            // $expout .= "   <li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
// MODIF 2012/03/08
            $expout .= "   <li><b>".get_string('type_domaine','referentiel')."</b> : $type_domaine</li>\n";
            $expout .= "   <li><b>".get_string('seuil_domaine','referentiel')."</b> : $seuil_domaine</li>\n";
// MODIF 2012/03/26
            $expout .= "   <li><b>".get_string('minima_domaine','referentiel')."</b> : $minima_domaine</li>\n";

            $expout .= "   <li><b>".get_string('num_domaine','referentiel')."</b> : $num_domaine</li>\n";
            $expout .= "   <li><b>".get_string('nb_competences','referentiel')."</b> : $nb_competences</li>\n";

			// LISTE DES COMPETENCES DE CE DOMAINE
			$compteur_competence=0;
			$records_competences = referentiel_get_competences($domaine->id);
			if ($records_competences){
				// DEBUG
				// echo "<br/>DEBUG :: COMPETENCES <br />\n";
				// print_r($records_competences);
				foreach ($records_competences as $record_c){
					$expout .= $this->write_competence( $record_c );
				}
			}
			$expout .= "</ul>\n";
        }
		$expout .= "</div>\n";
        return $expout;
    }

	 /**
     * Turns protocol into an xml segment
     * @param protocol object
     * @return string xml segment
     */
    // MODIF JF 2012/03/09
    function write_protocol( $protocole ){
    global $CFG;
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- protocole: $protocole->id  -->\n";
		//
		if ($protocole){
			$id = $protocole->id;
            $ref_occurrence = $protocole->ref_occurrence;
			$seuil_referentiel = $protocole->seuil_referentiel;
			$minima_referentiel = $protocole->minima_referentiel;
            $l_domaines_oblig = trim($protocole->l_domaines_oblig);
            $l_seuils_domaines = trim($protocole->l_seuils_domaines);
            $l_minimas_domaines = trim($protocole->l_minimas_domaines) ;
            $l_domaines_oblig = trim($protocole->l_domaines_oblig);
            $l_competences_oblig = trim($protocole->l_competences_oblig);
            $l_seuils_competences = trim($protocole->l_seuils_competences) ;
            $l_minimas_competences = trim($protocole->l_minimas_competences);
            $l_items_oblig = trim($protocole->l_items_oblig) ;
            $timemodified =$protocole->timemodified ;
			$actif = $protocole->actif;
            $commentaire = trim($protocole->commentaire);

            $expout .= "\n\n<!-- protocole: $protocole->id  -->
<div class=\"referentiel\">\n";
	    	$expout .= "<br /><ul>\n";

			$expout .= "   <li><b>".get_string('seuil_protocole','referentiel')."</b>: ".$seuil_referentiel."</li>\n";
			$expout .= "   <li><b>".get_string('minima_certificat','referentiel')."</b>: ".$minima_referentiel."</li>\n";
		    $expout .= "   <li><b>".get_string('oblig_domaine','referentiel')."</b>: ".$this->write_ligne($l_domaines_oblig,"/",80)."</li>\n";
            $expout .= "   <li><b>".get_string('seuil_domaine','referentiel')."</b>: ".$this->write_ligne($l_seuils_domaines,"/",80)."</li>\n";
            $expout .= "   <li><b>".get_string('minima_domaine','referentiel')."</b>: ".$this->write_ligne($l_minimas_domaines,"/",80)."</li>\n";
		    $expout .= "   <li><b>".get_string('oblig_competence','referentiel')."</b>: ".$this->write_ligne($l_competences_oblig,"/",80)."</li>\n";
            $expout .= "   <li><b>".get_string('seuil_competence','referentiel')."</b>: ".$this->write_ligne($l_seuils_competences,"/",80)."</li>\n";
            $expout .= "   <li><b>".get_string('minima_competence','referentiel')."</b>: ".$this->write_ligne($l_minimas_competences,"/",80)."</li>\n";
            $expout .= "   <li><b>".get_string('oblig_item','referentiel')."</b>: ".$this->write_ligne($l_items_oblig,"/",80)."</li>\n";
            $expout .= "   <li><b>".get_string('date')."</b>: ".userdate($timemodified)."</li>\n";
            $expout .= "   <li><b>".get_string('protocole_active','referentiel')."</b>: ".$actif."</li>\n";
            $expout .= "   <li><b>".get_string('protocole_active','referentiel')."</b></li>\n";
            $expout .= "   <li><b>".get_string('commentaire','referentiel')."</b>: ".$this->write_ligne($commentaire,"/",80)."</li>\n";
			$expout .= "</ul>\n";
    	    $expout .= "</div>\n";
        }
        return $expout;
    }


	 /**
     * Turns referentiel into an xml segment
     * @param competence object
     * @return string xml segment
     */

    function write_referentiel() {
    	global $CFG;
		global $USER;
        // initial string;
        $expout = "";
	    $id = $this->rreferentiel->id;

    	// add comment and div tags
    	$expout .= "<!-- date: ".date("Y/m/d")." referentiel:  ".$this->rreferentiel->id."  name: ".stripslashes($this->rreferentiel->name)." -->\n";
    	$expout .= "<div class=\"referentiel\">\n";

    	// add header
    	$expout .= "<h3>".stripslashes($this->rreferentiel->name)."</h3>\n";

		//
		$expout .= "<ul>\n";
		if ($this->rreferentiel){
            $name = $this->rreferentiel->name;
            $code_referentiel = $this->rreferentiel->code_referentiel;
            $description_referentiel = $this->rreferentiel->description_referentiel;
            $url_referentiel = $this->rreferentiel->url_referentiel;
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
			$minima_certificat = $this->rreferentiel->minima_certificat;
			$timemodified = $this->rreferentiel->timemodified;
			$nb_domaines = $this->rreferentiel->nb_domaines;
			$liste_codes_competence = $this->rreferentiel->liste_codes_competence;
			$liste_empreintes_competence = $this->rreferentiel->liste_empreintes_competence;
			$local = $this->rreferentiel->local;
			$logo_referentiel = $this->rreferentiel->logo_referentiel;

			$expout .= " <li><b>".get_string('name','referentiel')."</b> : ".stripslashes($name)."</li>\n";
			$expout .= " <li><b>".get_string('code_referentiel','referentiel')."</b> : ".stripslashes($code_referentiel)."</li>\n";
            $expout .= " <li><b>".get_string('description_referentiel','referentiel')."</b> : ".stripslashes($description_referentiel)."</li>\n";
            $expout .= " <li><b>".get_string('url_referentiel','referentiel')."</b> : $url_referentiel</li>\n";
            $expout .= " <li><b>".get_string('seuil_certificat','referentiel')."</b> : $seuil_certificat</li>\n";
            $expout .= " <li><b>".get_string('minima_certificat','referentiel')."</b> : $minima_certificat</li>\n";
            $expout .= " <li><b>".get_string('nb_domaines','referentiel')."</b> : $nb_domaines</li>\n";
            $expout .= " <li><b>".get_string('liste_codes_competence','referentiel')."</b> : ".$this->write_ligne($liste_codes_competence,"/",80)."</li>\n";
			$expout .= " <li><b>".get_string('liste_empreintes_competence','referentiel')."</b> : ".$this->write_ligne($liste_empreintes_competence,"/",80)."</li>\n";

            // $expout .= " <li><b>".get_string('local','referentiel')."</b> : $local</li>\n";
            $expout .= " <li><b>".get_string('logo','referentiel')."</b> : $logo_referentiel</li>\n";


			// MODIF JF 2012/03/09
			// PROTOCOLE
            if (!empty($this->rreferentiel->id)){
                if ($record_protocol=referentiel_get_protocol($this->rreferentiel->id)){
                    $expout .= $this->write_protocol( $record_protocol );
                }
            }

			// DOMAINES
			if (isset($this->rreferentiel->id) && ($this->rreferentiel->id>0)){
				// LISTE DES DOMAINES
				$compteur_domaine=0;
				$records_domaine = referentiel_get_domaines($this->rreferentiel->id);
		    	if ($records_domaine){
    				// afficher
					// DEBUG
					// echo "<br/>DEBUG ::<br />\n";
					// print_r($records_domaine);
					foreach ($records_domaine as $record_d){
						// DEBUG
						// echo "<br/>DEBUG ::<br />\n";
						// print_r($records_domaine);
						$expout .= $this->write_domaine( $record_d );
					}
				}
			}
        }
		$expout .= "</ul>\n";
	    // close off div
    	$expout .= "</div>\n\n\n";
        return $expout;
    }
}


/**********************************************************************
***********************************************************************
									ACTIVITES
***********************************************************************
**********************************************************************/


// ACTIVITES : export des activites
class aformat_xhtml extends aformat_default {

    function provide_export() {
      return true;
    }

	function provide_import() {
        return false;
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
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/xhtml/xhtml.css" );
		$css = implode( ' ',$css_lines ); 
		$xp =  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$xp .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
  		$xp .= "<title>Moodle Referentiel :: Activite XHTML Export</title>\n";
  		$xp .= $css;
  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function export_file_extension() {
  		return "_x.html";
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

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
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
        $expout .= "\n\n<!-- item: $item->id  -->
<div class='item'>\n";
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
			$num_item = $item->num_item;
            $expout .= "   <ul>\n";
			$expout .= "     <li><b>".get_string('code','referentiel')."</b> : $code</li>\n";   
            $expout .= "     <li><b>".get_string('description_item','referentiel')."</b> : $description_item</li>\n";
            $expout .= "     <li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
            $expout .= "     <li><b>".get_string('ref_competence','referentiel')."</b> : $ref_competence</li>\n";
            $expout .= "     <li><b>".get_string('type_item','referentiel')."</b> : $type_item</li>\n";
            $expout .= "     <li><b>".get_string('poids_item','referentiel')."</b> : $poids_item</li>\n";
            $expout .= "     <li><b>".get_string('num_item','referentiel')."</b> : $num_item</li>\n";			
			$expout .= "   </ul>\n";   
        }
		$expout .= "</div>\n";
        return $expout;
    }

	 /**
     * Turns document into an xml segment
     * @param document object
     * @return string xml segment
     */

    function write_document( $document ) {
    global $CFG;
       // initial string;
        $expout = "";
        // add comment
        $expout .= "\n\n<!-- document: $document->id  -->
<div class='item'>\n";
		if ($document){
			$id = $document->id ;		
            $type_document = trim($document->type_document);
            $description_document = trim($document->description_document);
			$url_document = $document->url_document;
            $ref_activite = $document->ref_activite;
            $expout .= "   <ul>\n";
            $expout .= "     <li><b>".get_string('type_document','referentiel')."</b> : $type_document</li>\n";   
            $expout .= "     <li><b>".get_string('description','referentiel')."</b> : $description_document</li>\n";
            $expout .= "     <li><b>".get_string('url','referentiel')."</b> : $url_document</li>\n";
            $expout .= "     <li><b>".get_string('ref_activite','referentiel')."</b> : $ref_activite</li>\n";
			$expout .= "   </ul>\n";   
        }
		$expout .= "</div>\n";
        return $expout;
    }

    /**
     * Turns activite into an xml segment
     * @param activite object
     * @return string xml segment
     */

    function write_activite( $activite ) {
    global $CFG;
       // initial string;
        $expout = "";
        // add comment
        $expout .= "\n\n<!-- activite: $activite->id  -->
<div class='competence'>\n";
		// 
		if ($activite){
			// DEBUG
			// echo "<br />\n";
			// print_r($activite);
			$id = $activite->id;
            $type_activite = trim($activite->type_activite);
            $description_activite = trim($activite->description_activite);
            $competences_activite = trim($activite->competences_activite);
            $commentaire_activite = trim($activite->commentaire_activite);
            $ref_instance = $activite->ref_instance;
            $ref_referentiel = $activite->ref_referentiel;
            $ref_course = $activite->ref_course;
			$userid = trim($activite->userid);
			$teacherid = $activite->teacherid;
			$date_creation = $activite->date_creation;
			$date_modif = $activite->date_modif;
			$approved = $activite->approved;
			
            $expout .= "<ul>\n";
            $expout .= "<li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
			$expout .= "<li><b>".get_string('type_activite','referentiel')."</b> : $type_activite</li>\n";
            $expout .= "<li><b>".get_string('description','referentiel')."</b> : $description_activite</li>\n";
            $expout .= "<li><b>".get_string('competences_activite','referentiel')."</b> : $competences_activite</li>\n";
            $expout .= "<li><b>".get_string('commentaire','referentiel')."</b> : $commentaire_activite</li>\n";
            $expout .= "<li><b>".get_string('instance','referentiel')."</b> : $ref_instance</li>\n";
            $expout .= "<li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
            $expout .= "<li><b>".get_string('ref_course','referentiel')."</b> : $ref_course</li>\n";
            $expout .= "<li><b>".get_string('userid','referentiel')."</b> : $userid</li>\n";
            $expout .= "<li><b>".get_string('teacherid','referentiel')."</b> : $teacherid</li>\n";
            $expout .= "<li><b>".get_string('date_creation','referentiel')."</b> : ".date("Y-m-d H:i:s",$date_creation)."</li>\n";
            $expout .= "<li><b>".get_string('date_modif','referentiel')."</b> : ".date("Y-m-d H:i:s",$date_modif)."</li>\n";
            $expout .= "<li><b>".get_string('approved','referentiel')."</b> : $approved</li>\n";
			
			// DOCUMENTS
			$records_documents = referentiel_get_documents($activite->id);
			
			if ($records_documents){
				foreach ($records_documents as $record_d){
					$expout .= $this->write_document( $record_d );
				}
			}
		}	
		$expout .= "</div>\n";
        return $expout;
    }

	
	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_liste_activites() {
    	global $CFG;
        // initial string;
        $expout = "";
	    $id = $this->ireferentiel->id;

    	// add comment and div tags
    	$expout .= "<!-- certification :  ".$this->ireferentiel->id."  name: ".$this->ireferentiel->name." -->\n";
    	$expout .= "<div class=\"referentiel\">\n";

    	// add header
    	$expout .= "<h3>".$this->ireferentiel->name."</h3>\n";
		// 
		$expout .= "<ul>\n";
		// 
		if ($this->ireferentiel){
			$id = $this->ireferentiel->id;
            $name = trim($this->ireferentiel->name);
            $description_instance = trim($this->ireferentiel->description_instance);
            $label_domaine = trim($this->ireferentiel->label_domaine);
            $label_competence = trim($this->ireferentiel->label_competence);
            $label_item = trim($this->ireferentiel->label_item);
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
			$visible = $this->ireferentiel->visible;

			$expout .= " <li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
			$expout .= " <li><b>".get_string('name','referentiel')."</b> : $name</li>\n";
			$expout .= " <li><b>".get_string('description_instance','referentiel')."</b> : description_instance</li>\n";   
            $expout .= " <li><b>".get_string('label_domaine','referentiel')."</b> : $label_domaine</li>\n";
            $expout .= " <li><b>".get_string('label_competence','referentiel')."</b> : $label_competence</li>\n";
            $expout .= " <li><b>".get_string('label_item','referentiel')."</b> : $label_item</li>\n";			
            $expout .= " <li><b>".get_string('date_instance','referentiel')."</b> : ".date("Y-m-d H:i:s",$date_instance)."</li>\n";
            $expout .= " <li><b>".get_string('course')."</b> : $course</li>\n";
            $expout .= " <li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
            $expout .= " <li><b>".get_string('visible','referentiel')."</b> : $visible</li>\n";
			
			// ACTIVITES
			if (isset($this->ireferentiel->id) && ($this->ireferentiel->id>0)){
				$records_activites = referentiel_get_activites_instance($this->ireferentiel->id);
		    	if ($records_activites){
					foreach ($records_activites as $record_a){
						// DEBUG
						// print_r($record_a);
						// echo "<br />\n";
						$expout .= $this->write_activite( $record_a );
					}
				}
			}
        }
        return $expout;
    }
}



/* *****************************************************************************

COMPETENCES

******************************************************************************** */
// ACTIVITES : export des activites
class cformat_xhtml extends cformat_default {

    function provide_export() {
      return true;
    }

	function provide_import() {
        return false;
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
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/xhtml/xhtml.css" );
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
  		return "_x.html";
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

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
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
        $expout .= "\n\n<!-- etablissement: $record->id  -->\n";
		if ($record){
    		$expout .= "<div class=\"referentiel\">\n";
			$expout .= "<h4>".get_string('etablissement','referentiel')."</h4>\n";
			// 
			
			$expout .= "<ul>\n";		// 
			$id = trim( $record->id );
			$num_etablissement = trim( $record->num_etablissement);
			$nom_etablissement = trim( $record->nom_etablissement);
			$adresse_etablissement = trim( $record->adresse_etablissement);
			$logo = trim( $record->logo_etablissement);
						
			$expout .= " <li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
            $expout .= " <li><b>".get_string('num_etablissement','referentiel')."</b> : $num_etablissement</li>\n";
            $expout .= " <li><b>".get_string('nom_etablissement','referentiel')."</b> : $nom_etablissement</li>\n";			
            $expout .= " <li><b>".get_string('adresse_etablissement','referentiel')."</b> : $adresse_etablissement</li>\n";
            $expout .= " <li><b>".get_string('logo','referentiel')."</b> : $logo</li>\n";			
			$expout .= " </ul>\n";
			$expout .= "</div>\n\n";		//			
        }
        return $expout;
    }


	
	function write_etudiant( $record ) {
        // initial string;
        $expout = "";
        // add comment
        $expout .= "\n\n<!-- etudiant: $record->id  -->\n";
		if ($record){
			// DEBUG
			// echo "<br />\n";
			// print_r($record);
	    	// add header
			$expout .= "<div class=\"referentiel\">\n";
			
    		$expout .= "<h4>Etudiant</h4>\n";
			// 
			$expout .= "<ul>\n";		// 
			
			$id = trim( $record->id );
			$userid = trim( $record->userid );
            $ref_etablissement = trim( $record->ref_etablissement);
			$num_etudiant = trim( $record->num_etudiant);
			$ddn_etudiant = trim( $record->ddn_etudiant);
			$lieu_naissance = trim( $record->lieu_naissance);
			$departement_naissance = trim( $record->departement_naissance);
			$adresse_etudiant = trim( $record->adresse_etudiant);			

                $login = trim(referentiel_get_user_login($record->userid));

                if ($num_etudiant==$login){
                    $texte=$num_etudiant;
                }
                elseif ($num_etudiant==''){
                    $texte=$login;
                }
                else{
                    $texte=$num_etudiant." (".$login.")";
                }

            if ($this->format_condense){
			$expout .= " <li><b>".get_string('userid','referentiel')."</b> : $userid</li>\n";
			$expout .= " <li><b>".get_string('nom_prenom','referentiel')."</b> : ".referentiel_get_user_info($record->userid)."</li>\n";
			$expout .= " <li><b>".get_string('num_etudiant','referentiel')."</b> : $texte</li>\n";
            }
            else{
			$expout .= " <li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
			$expout .= " <li><b>".get_string('userid','referentiel')."</b> : $userid</li>\n";	
			$expout .= " <li><b>".get_string('nom_prenom','referentiel')."</b> : ".referentiel_get_user_info($record->userid)."</li>\n";
			$expout .= " <li><b>".get_string('num_etudiant','referentiel')."</b> : $texte</li>\n";
            $expout .= " <li><b>".get_string('ddn_etudiant','referentiel')."</b> : $ddn_etudiant</li>\n";
            $expout .= " <li><b>".get_string('lieu_naissance','referentiel')."</b> : $lieu_naissance</li>\n";
            $expout .= " <li><b>".get_string('departement_naissance','referentiel')."</b> : $departement_naissance</li>\n";			
            $expout .= " <li><b>".get_string('adresse_etudiant','referentiel')."</b> : $adresse_etudiant</li>\n";
			$expout .= " <li><b>".get_string('ref_etablissement','referentiel')."</b> : $ref_etablissement</li>\n";
			// Etablissement
			$record_etablissement=referentiel_get_etablissement($record->ref_etablissement);
	    	if ($record_etablissement){
				$expout .= $this->write_etablissement( $record_etablissement );
			}
			}
		    $expout .= " </ul>\n";
			$expout .= "</div>\n\n";		//			
        }
        return $expout;
    }

	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_certificat( $record ) {
    	global $CFG;
        // initial string;
        $expout = "";
    	// add comment and div tags
    	$expout .= "<!-- certification :  $record->id  -->\n";
    	$expout .= "<div class=\"referentiel\">\n";

    	// add header
		
    	$expout .= "<h3>".get_string('certificat','referentiel')."</h3>\n";
		// 
		$expout .= "<ul>\n";		// 
		if ($record){
			// DEBUG
			// echo "<br />DEBUG LIGNE 1021<br />\n";
			// print_r($this->ireferentiel);
			$id = trim( $record->id );
            $commentaire_certificat = trim($record->commentaire_certificat);
            $synthese_certificat = trim($record->synthese_certificat);
            $competences_certificat =  trim($record->competences_certificat) ;
            $decision_jury = trim($record->decision_jury);
            $date_decision = userdate(trim($record->date_decision));
            $userid = trim( $record->userid);
            $teacherid = trim( $record->teacherid);
            $ref_referentiel = trim( $record->ref_referentiel);
			$verrou = trim( $record->verrou );
			$valide = trim( $record->valide );
			$evaluation = trim( $record->evaluation );
            $synthese_certificat = trim($record->synthese_certificat);
            
            if ($this->format_condense){
                $expout .= "<li>\n";
                // DEBUG
                // echo "<br />DEBUG LIGNE 1314<br />\n";
                // echo htmlentities ($expout, ENT_QUOTES, 'UTF-8')  ;
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
                        $expout .= $this->write_etudiant( $record_etudiant );
                    }
                }
                $expout .= "<ol>\n\n";
                $expout .= $this->certificat_pourcentage($competences_certificat, $ref_referentiel);
                $expout .= "</ol></li>\n\n";
			}
			else{

                $expout .= " <li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
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
                        $expout .= $this->write_etudiant( $record_etudiant );
                    }
                }
			
                $expout .= "<li><b>".get_string('commentaire_certificat','referentiel')."</b> : $commentaire_certificat</li>\n";
                $expout .= "<li><b>".get_string('synthese_certificat','referentiel')."</b> : $synthese_certificat</li>\n";
                $expout .= "<li><b>".get_string('competences_certificat','referentiel')."</b> : $competences_certificat</li>\n";
                $expout .= "<li><b>".get_string('decision_jury','referentiel')."</b> : $decision_jury</li>\n";
                $expout .= "<li><b>".get_string('date_decision','referentiel')."</b> : ".$date_decision."</li>\n";
                $expout .= "<li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
                $expout .= "<li><b>".get_string('verrou','referentiel')."</b> : $verrou</li>\n";
                $expout .= "<li><b>".get_string('valide','referentiel')."</b> : $valide</li>\n";
                $expout .= "<li><b>".get_string('evaluation','referentiel')."</b> : $evaluation</li>\n";
                $expout .= "<li><b>".get_string('synthese_certificat','referentiel')."</b> : $synthese_certificat</li>\n";
            }
	    }
        $expout .= " </ul>\n";
		$expout .= "</div>\n\n";
		
        return $expout;
    }



	
	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_certification() {
    	global $CFG;
      // initial string;
      $expout = "";
	    $id = $this->ireferentiel->id;

    	// add comment and div tags
    	$expout .= "<!-- certification :  ".$this->ireferentiel->id."  name: ".$this->ireferentiel->name." -->\n";
    	$expout .= "<div class=\"referentiel\">\n";

    	// add header
    	$expout .= "<h2>".$this->ireferentiel->name."</h2>\n";
    	// 
		  $expout .= "<ul>\n";
		  // 
        if ($this->ireferentiel){
            $id = $this->ireferentiel->id;
            $name = trim($this->ireferentiel->name);
            $description = trim($this->ireferentiel->description_instance);
            $label_domaine = trim($this->ireferentiel->label_domaine);
            $label_competence = trim($this->ireferentiel->label_competence);
            $label_item = trim($this->ireferentiel->label_item);
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
			$visible = $this->ireferentiel->visible;

            if ($this->format_condense){
                $expout .= " <b>".get_string('instance','referentiel')."</b><li><b>".get_string('name','referentiel')."</b> : $name</li>\n";
                $expout .= " <li><b>".get_string('description','referentiel')."</b> : $description</li>\n";
            }
            else{
                $expout .= " <li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
                $expout .= " <li><b>".get_string('name','referentiel')."</b> : $name</li>\n";
                $expout .= " <li><b>".get_string('description','referentiel')."</b> : $description</li>\n";
                $expout .= " <li><b>".get_string('label_domaine','referentiel')."</b> : $label_domaine</li>\n";
                $expout .= " <li><b>".get_string('label_competence','referentiel')."</b> : $label_competence</li>\n";
                $expout .= " <li><b>".get_string('label_item','referentiel')."</b> : $label_item</li>\n";                    
                $expout .= " <li><b>".get_string('date_instance','referentiel')."</b> : ".date("Y/m/d",$date_instance)."</li>\n";
                $expout .= " <li><b>".get_string('course')."</b> : $course</li>\n";
                $expout .= " <li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
                $expout .= " <li><b>".get_string('visible','referentiel')."</b> : $visible</li>\n";
			}
        }
        
		// CERTIFICATS
		if (empty($this->rreferentiel) && (!empty($this->ireferentiel->ref_referentiel) && ($this->ireferentiel->ref_referentiel>0))){
  				$this->rreferentiel = referentiel_get_referentiel_referentiel($this->ireferentiel->ref_referentiel);          
        }

        if (!empty($this->rreferentiel)){
                $expout .= $this->write_referentiel($this->rreferentiel);

                if (!$this->records_certificats){
                    $this->records_certificats = referentiel_get_certificats($this->rreferentiel->id);
                }
                // print_r($this->records_certificats);

		    	if ($this->records_certificats){
					   foreach ($this->records_certificats as $record){
						    $expout .= $this->write_certificat( $record );
					   }
				  }
        }
        $expout .= " </ul>\n";
        $expout .= "</div>\n";
        return $expout;
    }

    function write_referentiel() {
    	global $CFG;
        // initial string;
		$expout ="";
		if ($this->rreferentiel){
			$id = $this->rreferentiel->id;
            $name = $this->rreferentiel->name;
            $code_referentiel = $this->rreferentiel->code_referentiel;
            $description_referentiel = $this->rreferentiel->description_referentiel;
            $url_referentiel = $this->rreferentiel->url_referentiel;
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
			$timemodified = $this->rreferentiel->timemodified;
			$nb_domaines = $this->rreferentiel->nb_domaines;
			$liste_codes_competence = $this->rreferentiel->liste_codes_competence;
			$liste_empreintes_competence = $this->rreferentiel->liste_empreintes_competence;
			$local = $this->rreferentiel->local;

			// $expout = "#Referentiel : ".$this->rreferentiel->id." : ".stripslashes($this->rreferentiel->name)."\n";
            // add header
            if ($this->format_condense){
                // echo "DEBUG :: ".$this->format_condense."\n";
                // exit;
                $expout .= "<div class=\"referentiel\">\n";
                $expout .= "<ul>\n";
                $expout .= "<b>".get_string('referentiel','referentiel')."</b>\n";
                $expout .= " <li><b>".get_string('name','referentiel')."</b> : $name</li>\n";
                $expout .= " <li><b>".get_string('code','referentiel')."</b> : $code_referentiel</li>\n";
                $expout .= " <li><b>".get_string('description_referentiel','referentiel')."</b> : <li>".stripslashes($description_referentiel)."</li><li>".stripslashes($description_referentiel)."</li>\n</ul>\n";
            }
            /*
            else{     // developper le referentiel ?
                $expout .= "#id_referentiel;name;code_referentiel;description_referentiel;url_referentiel;seuil_certificat;timemodified;nb_domaines;liste_codes_competences;liste_empreintes_competences;local\n";
                $expout .= "$id;".stripslashes($name).";".stripslashes($code_referentiel).";".stripslashes($description_referentiel).";$url_referentiel;$seuil_certificat;".referentiel_timestamp_date_special($timemodified).";$nb_domaines;".stripslashes($liste_codes_competence).";".stripslashes($liste_empreintes_competence).";$local\n";

                // DOMAINES
                if (isset($this->rreferentiel->id) && ($this->rreferentiel->id>0)){
                    // LISTE DES DOMAINES
                    $compteur_domaine=0;
                    $records_domaine = referentiel_get_domaines($this->rreferentiel->id);
                    if ($records_domaine){
                        // afficher
                        // DEBUG
                        // echo "<br/>DEBUG ::<br />\n";
                        // print_r($records_domaine);
                        foreach ($records_domaine as $record_d){
                            // DEBUG
                            // echo "<br/>DEBUG ::<br />\n";
                            // print_r($records_domaine);
                            $expout .= $this->write_domaine( $record_d );
                        }
                    }
				}
			}
			*/
        }
        return $expout;
  }

    // -------------------
    function certificat_pourcentage($liste_competences, $ref_referentiel){
    // retourne les pourcentages par competence

    $separateur1='/';
    $separateur2=':';

    global $OK_REFERENTIEL_DATA;
    global $t_domaine;
    global $t_domaine_coeff;

    // COMPETENCES
    global $t_competence;
    global $t_competence_coeff;

    // ITEMS
    global $t_item_code;
    global $t_item_coeff; // coefficient poids determine par le modele de calcul (soit poids soit poids / empreinte)
    global $t_item_domaine; // index du domaine associé à un item
    global $t_item_competence; // index de la competence associée à un item
    global $t_item_poids; // poids
    global $t_item_empreinte;
    global $t_nb_item_domaine;
    global $t_nb_item_competence;

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
		$liste_code=referentiel_purge_dernier_separateur($liste_competences, $separateur1);

		// DEBUG
		// echo "<br />DEBUG :: print_lib_certificat.php :: 917 :: LISTE : $liste_code<br />\n";

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
				$t_cc=explode($separateur2, $tc[$i]); // tableau des items valides
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
            /*
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
            */

            /*
			for ($i=0; $i<count($t_competence); $i++){
					$s.=$t_competence[$i].';';
			}
			$s.="\n";
			*/
			for ($i=0; $i<count($t_competence); $i++){
                $s.= '<li><b>'.$t_competence[$i].'</b> : '.referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i]).'%</li>'."\n";
			}
			// $s.="\n";

			// ITEMS
            /*
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
            */
		}
	}
	}

	return $s;
    }

} // fin de la classe


/* *****************************************************************************

ETUDIANTS

******************************************************************************** */
// export des etudiants
class eformat_xhtml extends eformat_default {

    function provide_export() {
      return true;
    }

	function provide_import() {
        return false;
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
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/xhtml/xhtml.css" );
		$css = implode( ' ',$css_lines ); 
		$xp =  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$xp .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
  		$xp .= "<title>Moodle Referentiel :: Students XHTML Export</title>\n";
  		$xp .= $css;
  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function export_file_extension() {
  		return "_x.html";
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

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
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
//        $expout .= "\n\n<!-- etablissement: $record->id  -->\n";
		if ($record){
//    		$expout .= "<div class=\"referentiel\">\n";
//			$expout .= "<h4>".get_string('etablissement','referentiel')."</h4>\n";
			
			$expout .= "<ul>\n";		// 
			$id = trim( $record->id );
			$num_etablissement = trim( $record->num_etablissement);
			$nom_etablissement = trim( $record->nom_etablissement);
			$adresse_etablissement = trim( $record->adresse_etablissement);
			$logo = trim( $record->logo_etablissement);
						
			$expout .= " <li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
            $expout .= " <li><b>".get_string('num_etablissement','referentiel')."</b> : $num_etablissement</li>\n";
            $expout .= " <li><b>".get_string('nom_etablissement','referentiel')."</b> : $nom_etablissement</li>\n";			
            $expout .= " <li><b>".get_string('adresse_etablissement','referentiel')."</b> : $adresse_etablissement</li>\n";
//            $expout .= " <li><b>".get_string('logo','referentiel')."</b> : $logo</li>\n";			
			$expout .= " </ul>\n";
//			$expout .= "</div>\n\n";		//			
        }
        return $expout;
    }


	
	function write_etudiant( $record ) {
        // initial string;
        $expout = "";
        // add comment
//        $expout .= "\n\n<!-- etudiant: $record->id  -->\n";
		if ($record){
	    	// add header
//			$expout .= "<div class=\"referentiel\">\n";	
//    		$expout .= "<h4>Etudiant</h4>\n";
			// 
			$expout .= "<ul>\n";		// 
			
			$id = trim( $record->id );
			$userid = trim( $record->userid );
            $ref_etablissement = trim( $record->ref_etablissement);
			$num_etudiant = trim( $record->num_etudiant);
			$ddn_etudiant = trim( $record->ddn_etudiant);
			$lieu_naissance = trim( $record->lieu_naissance);
			$departement_naissance = trim( $record->departement_naissance);
			$adresse_etudiant = trim( $record->adresse_etudiant);			

                $login = trim(referentiel_get_user_login($record->userid));

                if ($num_etudiant==$login){
                    $texte=$num_etudiant;
                }
                elseif ($num_etudiant==''){
                    $texte=$login;
                }
                else{
                    $texte=$num_etudiant." (".$login.")";
                }

			$expout .= " <li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
			$expout .= " <li><b>".get_string('userid','referentiel')."</b> : $userid</li>\n";	
            $expout .= " <li><b>".get_string('nom_prenom','referentiel')."</b> : ".referentiel_get_user_info($record->userid)."</li>\n";
			$expout .= " <li><b>".get_string('num_etudiant','referentiel')."</b> : $texte</li>\n";
            $expout .= " <li><b>".get_string('ddn_etudiant','referentiel')."</b> : $ddn_etudiant</li>\n";
            $expout .= " <li><b>".get_string('lieu_naissance','referentiel')."</b> : $lieu_naissance</li>\n";
            $expout .= " <li><b>".get_string('departement_naissance','referentiel')."</b> : $departement_naissance</li>\n";			
            $expout .= " <li><b>".get_string('adresse_etudiant','referentiel')."</b> : $adresse_etudiant</li>\n";
			$expout .= " <li><b>".get_string('ref_etablissement','referentiel')."</b> : $ref_etablissement</li>\n";
			/*
			// Etablissement
			$record_etablissement=referentiel_get_etablissement($record->ref_etablissement);
	    	if ($record_etablissement){
				$expout .= $this->write_etablissement( $record_etablissement );
			}
			*/
		    $expout .= " </ul>\n";
//			$expout .= "</div>\n";
        }
        return $expout;
    }

	
	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_liste_etudiants() {
    	global $CFG;
        // initial string;
        $expout = "";
	    $id = $this->ireferentiel->id;

    	// add comment and div tags
    	// $expout .= "<!-- etudiants :  $this->ireferentiel->id  name: $this->ireferentiel->name -->\n";
    	// $expout .= "<div class=\"referentiel\">\n";

    	// add header
    	// $expout .= "<h2>$this->ireferentiel->name</h2>\n";
		// 
		// $expout .= "<ul>\n";
		// 
		if ($this->ireferentiel){
			$id = $this->ireferentiel->id;
            $name = trim($this->ireferentiel->name);
            $description = trim($this->ireferentiel->description_instance);
            $label_domaine = trim($this->ireferentiel->label_domaine);
            $label_competence = trim($this->ireferentiel->label_competence);
            $label_item = trim($this->ireferentiel->label_item);
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
			$visible = $this->ireferentiel->visible;
/*
			$expout .= " <li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
			$expout .= " <li><b>".get_string('name','referentiel')."</b> : $name</li>\n";
			$expout .= " <li><b>".get_string('description','referentiel')."</b> : $description</li>\n";   
            $expout .= " <li><b>".get_string('label_domaine','referentiel')."</b> : $label_domaine</li>\n";
            $expout .= " <li><b>".get_string('label_competence','referentiel')."</b> : $label_competence</li>\n";
            $expout .= " <li><b>".get_string('label_item','referentiel')."</b> : $label_item</li>\n";			
            $expout .= " <li><b>".get_string('date_instance','referentiel')."</b> : ".date("Y-m-d H:i:s",$date_instance)."</li>\n";
            $expout .= " <li><b>".get_string('course')."</b> : $course</li>\n";
            $expout .= " <li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
            $expout .= " <li><b>".get_string('visible','referentiel')."</b> : $visible</li>\n";
*/			
			// ETUDIANTS
			if (isset($this->ireferentiel->course) && ($this->ireferentiel->course>0)){
				// ETUDIANTS
				$records_all_students = referentiel_get_students_course($this->ireferentiel->course);
				if ($records_all_students){
					foreach ($records_all_students as $record){
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
								$expout .= $this->write_etudiant( $record_etudiant );
							}
						}
					}
				}
			}
        }
/*
	    $expout .= " </ul>\n";
		$expout .= "</div>\n";
*/
        return $expout;
    }
	
	function write_liste_etablissements() {
    	global $CFG;
        // initial string;
        $expout = ""; 
		// ETABLISSEMENTS
		$records_all_etablissements = referentiel_get_etablissements();
		if ($records_all_etablissements){
			foreach ($records_all_etablissements as $record){
				if ($record){
					$expout.=$this->write_etablissement($record);
				}
			}
        }
        return $expout;
    }

}

/**********************************************************************
***********************************************************************
									TACHES
***********************************************************************
**********************************************************************/


// taches : export des taches
class tformat_xhtml extends tformat_default {

    function provide_export() {
      return true;
    }

	function provide_import() {
        return false;
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
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/xhtml/xhtml.css" );
		$css = implode( ' ',$css_lines ); 
		$xp =  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$xp .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
  		$xp .= "<title>Moodle Referentiel :: TASKS XHTML Export</title>\n";
  		$xp .= $css;
  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function export_file_extension() {
  		return "_x.html";
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

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
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


	 /**
     * Turns consigne into an xml segment
     * @param consigne object
     * @return string xml segment
     */

    function write_consigne( $consigne ) {
    global $CFG;
       // initial string;
        $expout = "";
        // add comment
        $expout .= "\n\n<!-- consigne: $consigne->id  -->
<div class='item'>\n";
		if ($consigne){
			$id = $consigne->id ;		
            $type_consigne = trim($consigne->type_consigne);
            $description_consigne = trim($consigne->description_consigne);
			$url_consigne = $consigne->url_consigne;
            $ref_task = $consigne->ref_task;
            $expout .= "   <ul>\n";
            $expout .= "     <li><b>".get_string('type_consigne','referentiel')."</b> : $type_consigne</li>\n";   
            $expout .= "     <li><b>".get_string('description','referentiel')."</b> : $description_consigne</li>\n";
            $expout .= "     <li><b>".get_string('url','referentiel')."</b> : $url_consigne</li>\n";
            $expout .= "     <li><b>".get_string('task','referentiel')."</b> : $ref_task</li>\n";
			$expout .= "   </ul>\n";   
        }
		$expout .= "</div>\n";
        return $expout;
    }

    /**
     * Turns task into an xml segment
     * @param task object
     * @return string xml segment
     */

    function write_task( $task ) {
    global $CFG;
       // initial string;
        $expout = "";
        // add comment
        $expout .= "\n\n<!-- task: $task->id  -->
<div class='competence'>\n";
		// 
		if ($task){
			// DEBUG
			// echo "<br />\n";
			// print_r($task);
			$id = $task->id;
            $type_task = trim($task->type_task);
            $description_task = trim($task->description_task);
            $competences_task = trim($task->competences_task);
            $criteres_evaluation = trim($task->criteres_evaluation);
            $ref_instance = $task->ref_instance;
            $ref_referentiel = $task->ref_referentiel;
            $ref_course = $task->ref_course;
			$auteurid = trim($task->auteurid);
			$date_creation = $task->date_creation;
			$date_modif = $task->date_modif;
			$date_debut = $task->date_debut;
			$date_fin = $task->date_fin;
			
            $expout .= "<ul>\n";
            $expout .= "<li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
			$expout .= "<li><b>".get_string('type_task','referentiel')."</b> : $type_task</li>\n";
            $expout .= "<li><b>".get_string('description','referentiel')."</b> : $description_task</li>\n";
            $expout .= "<li><b>".get_string('competences','referentiel')."</b> : $competences_task</li>\n";
            $expout .= "<li><b>".get_string('criteres_evaluation','referentiel')."</b> : $criteres_evaluation</li>\n";
            $expout .= "<li><b>".get_string('instance','referentiel')."</b> : $ref_instance</li>\n";
            $expout .= "<li><b>".get_string('referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
            $expout .= "<li><b>".get_string('course')."</b> : $ref_course</li>\n";
            $expout .= "<li><b>".get_string('auteur','referentiel')."</b> : $auteurid</li>\n";
            $expout .= "<li><b>".get_string('date_creation','referentiel')."</b> : ".date("Y-m-d H:i:s",$date_creation)."</li>\n";
            $expout .= "<li><b>".get_string('date_modif','referentiel')."</b> : ".date("Y-m-d H:i:s",$date_modif)."</li>\n";
            $expout .= "<li><b>".get_string('date_debut','referentiel')."</b> : ".date("Y-m-d H:i:s",$date_debut)."</li>\n";
            $expout .= "<li><b>".get_string('date_fin','referentiel')."</b> : ".date("Y-m-d H:i:s",$date_fin)."</li>\n";

			
			// consigneS
			$records_consignes = referentiel_get_consignes($task->id);
			
			if ($records_consignes){
				foreach ($records_consignes as $record_d){
					$expout .= $this->write_consigne( $record_d );
				}
			}
			$expout .= "</ul>\n";
		}	
		$expout .= "</div>\n";
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
        $expout .= "\n\n<!-- item: $item->id  -->
<div class='item'>\n";
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
			$num_item = $item->num_item;
            $expout .= "   <ul>\n";
			$expout .= "     <li><b>".get_string('code','referentiel')."</b> : ".stripslashes($code)."</li>\n";   
            $expout .= "     <li><b>".get_string('description_item','referentiel')."</b> : ".stripslashes($description_item)."</li>\n";
            // $expout .= "     <li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
            // $expout .= "     <li><b>".get_string('ref_competence','referentiel')."</b> : $ref_competence</li>\n";
            $expout .= "     <li><b>".get_string('type_item','referentiel')."</b> : ".stripslashes($type_item)."</li>\n";
            $expout .= "     <li><b>".get_string('poids_item','referentiel')."</b> : $poids_item</li>\n";
            $expout .= "     <li><b>".get_string('num_item','referentiel')."</b> : $num_item</li>\n";			
			$expout .= "   </ul>\n";   
        }
		$expout .= "</div>\n";
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
        // add comment		
        $expout .= "\n\n<!-- competence: $competence->id  -->
<div class='competence'>\n";
		//
		
		if ($competence){
            $code = $competence->code_competence;
            $description_competence = $competence->description_competence;
            $ref_domaine = $competence->ref_domaine;
			$num_competence = $competence->num_competence;
			$nb_item_competences = $competence->nb_item_competences;
			$expout .= "  <ul>\n";
			$expout .= "    <li><b>".get_string('code_competence','referentiel')."</b> : ".stripslashes($code)."</li>\n";   
            $expout .= "    <li><b>".get_string('description_competence','referentiel')."</b> : ".stripslashes($description_competence)."</li>\n";
            // $expout .= "    <li><b>".get_string('ref_domaine','referentiel')."</b> : $ref_domaine</li>\n";
            $expout .= "    <li><b>".get_string('num_competence','referentiel')."</b> : $num_competence</li>\n";
            $expout .= "    <li><b>".get_string('nb_item_competences','referentiel')."</b> : $nb_item_competences</li>\n";
							
			// ITEM
			$compteur_item=0;
			$records_items = referentiel_get_item_competences($competence->id);
			
			if ($records_items){
				// DEBUG
				// echo "<br/>DEBUG :: ITEMS <br />\n";
				// print_r($records_items);
				foreach ($records_items as $record_i){
					// DEBUG
					// echo "<br/>DEBUG :: ITEM <br />\n";
					// print_r($record_i);
					$expout .= $this->write_item( $record_i );
				}
			}
			$expout .= "  </ul>\n";   
        }
		$expout .= "</div>\n";
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
        // add comment		
        $expout .= "\n\n<!-- domaine: $domaine->id  -->
<div class='domaine'>\n";
		// 

		if ($domaine){
            $code = $domaine->code_domaine;
            $description_domaine = $domaine->description_domaine;
            $ref_referentiel = $domaine->ref_referentiel;
			$num_domaine = $domaine->num_domaine;
			$nb_competences = $domaine->nb_competences;
			$expout .= "<ul>\n";			
			$expout .= "   <li><b>".get_string('code_domaine','referentiel')."</b> : ".stripslashes($code)."</li>\n";   
            $expout .= "   <li><b>".get_string('description_domaine','referentiel')."</b> : ".stripslashes($description_domaine)."</li>\n";
            // $expout .= "   <li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
            $expout .= "   <li><b>".get_string('num_domaine','referentiel')."</b> : $num_domaine</li>\n";
            $expout .= "   <li><b>".get_string('nb_competences','referentiel')."</b> : $nb_competences</li>\n";
			
			// LISTE DES COMPETENCES DE CE DOMAINE
			$compteur_competence=0;
			$records_competences = referentiel_get_competences($domaine->id);
			if ($records_competences){
				// DEBUG
				// echo "<br/>DEBUG :: COMPETENCES <br />\n";
				// print_r($records_competences);
				foreach ($records_competences as $record_c){
					$expout .= $this->write_competence( $record_c );
				}
			}
			$expout .= "</ul>\n";
        }
		$expout .= "</div>\n";
        return $expout;
    }



	 /**
     * Turns referentiel into an xml segment
     * @param competence object
     * @return string xml segment
     */

    function write_referentiel( $referentiel ) {
    	global $CFG;
		global $USER;
        // initial string;
        $expout = "";
	    $id = $referentiel->id;

    	// add comment and div tags
    	$expout .= "<!-- date: ".date("Y/m/d")." referentiel:  $referentiel->id  name: ".stripslashes($referentiel->name)." -->\n";
    	$expout .= "<div class=\"referentiel\">\n";

    	// add header
    	$expout .= "<h3>".stripslashes($referentiel->name)."</h3>\n";
		
		// 
		$expout .= "<ul>\n";
		if ($referentiel){
            $name = $referentiel->name;
            $code_referentiel = $referentiel->code_referentiel;
            $description_referentiel = $referentiel->description_referentiel;
            $url_referentiel = $referentiel->url_referentiel;
			$seuil_certificat = $referentiel->seuil_certificat;
			$timemodified = $referentiel->timemodified;			
			$nb_domaines = $referentiel->nb_domaines;
			$liste_codes_competence = $referentiel->liste_codes_competence;
			$liste_empreintes_competence = $referentiel->liste_empreintes_competence;
			$local = $referentiel->local;
			$logo_referentiel = $referentiel->logo_referentiel;

			$expout .= " <li><b>".get_string('name','referentiel')."</b> : ".stripslashes($name)."</li>\n";
			$expout .= " <li><b>".get_string('code_referentiel','referentiel')."</b> : ".stripslashes($code_referentiel)."</li>\n";   
            $expout .= " <li><b>".get_string('description_referentiel','referentiel')."</b> : ".stripslashes($description_referentiel)."</li>\n";
            $expout .= " <li><b>".get_string('url_referentiel','referentiel')."</b> : $url_referentiel</li>\n";
            $expout .= " <li><b>".get_string('seuil_certificat','referentiel')."</b> : $seuil_certificat</li>\n";
            $expout .= " <li><b>".get_string('nb_domaines','referentiel')."</b> : $nb_domaines</li>\n";
            $expout .= " <li><b>".get_string('liste_codes_competence','referentiel')."</b> : ".$this->write_ligne($liste_codes_competence,"/",80)."</li>\n";
			$expout .= " <li><b>".get_string('liste_empreintes_competence','referentiel')."</b> : ".$this->write_ligne($liste_empreintes_competence,"/",80)."</li>\n";
			
            // $expout .= " <li><b>".get_string('local','referentiel')."</b> : $local</li>\n";
            $expout .= " <li><b>".get_string('logo','referentiel')."</b> : $logo_referentiel</li>\n";
						
			// DOMAINES
			if (isset($referentiel->id) && ($referentiel->id>0)){
				// LISTE DES DOMAINES
				$compteur_domaine=0;
				$records_domaine = referentiel_get_domaines($referentiel->id);
		    	if ($records_domaine){
    				// afficher
					// DEBUG
					// echo "<br/>DEBUG ::<br />\n";
					// print_r($records_domaine);
					foreach ($records_domaine as $record_d){
						// DEBUG
						// echo "<br/>DEBUG ::<br />\n";
						// print_r($records_domaine);
						$expout .= $this->write_domaine( $record_d );
					}
				}
			} 
        }
		$expout .= "</ul>\n";
	    // close off div 
    	$expout .= "</div>\n\n\n";
        return $expout;
    }

	
	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_liste_tasks() {
    	global $CFG;
        // initial string;
        $expout = "";
	    $id = $this->ireferentiel->id;

    	// add comment and div tags
		$expout .= "<h1>".get_string('tasks','referentiel')."</h1>\n";
 		// 
		if ($this->rreferentiel){
			$expout .= $this->write_referentiel( $this->rreferentiel );
		}

		if ($this->ireferentiel){
			$id = $this->ireferentiel->id;
            $name = trim($this->ireferentiel->name);
            $description_instance = trim($this->ireferentiel->description_instance);
            $label_domaine = trim($this->ireferentiel->label_domaine);
            $label_competence = trim($this->ireferentiel->label_competence);
            $label_item = trim($this->ireferentiel->label_item);
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
			$visible = $this->ireferentiel->visible;

			// add comment and div tags
    		$expout .= "<!-- instance :  ".$this->ireferentiel->id."  name: ".$this->ireferentiel->name." -->\n";
    		$expout .= "<div class=\"referentiel\">\n";
	    	// add header
    		$expout .= "<h3>".$this->ireferentiel->name."</h3>\n";

			$expout .= "<ul>\n";
			$expout .= " <li><b>".get_string('id','referentiel')."</b> : $id</li>\n";
			$expout .= " <li><b>".get_string('name','referentiel')."</b> : $name</li>\n";
			$expout .= " <li><b>".get_string('description_instance','referentiel')."</b> : $description_instance</li>\n";   
            $expout .= " <li><b>".get_string('label_domaine','referentiel')."</b> : $label_domaine</li>\n";
            $expout .= " <li><b>".get_string('label_competence','referentiel')."</b> : $label_competence</li>\n";
            $expout .= " <li><b>".get_string('label_item','referentiel')."</b> : $label_item</li>\n";			
            $expout .= " <li><b>".get_string('date_instance','referentiel')."</b> : ".date("Y-m-d H:i:s",$date_instance)."</li>\n";
            $expout .= " <li><b>".get_string('course')."</b> : $course</li>\n";
            $expout .= " <li><b>".get_string('ref_referentiel','referentiel')."</b> : $ref_referentiel</li>\n";
            $expout .= " <li><b>".get_string('visible','referentiel')."</b> : $visible</li>\n";
			$expout .= "</ul>\n";
			
			
			// taches
			if (isset($this->ireferentiel->id) && ($this->ireferentiel->id>0)){
				$records_taches = referentiel_get_tasks_instance($this->ireferentiel->id);
		    	if ($records_taches){
					$expout .= "<h4>".get_string('tasks','referentiel')."</h4>\n";
					foreach ($records_taches as $record_a){
						// DEBUG
						// print_r($record_a);
						// echo "<br />\n";
						$expout .= $this->write_task( $record_a );
					}
				}
			}
			$expout .= "</div>\n";
        }
        return $expout;
    }
}


// ################################################################################################################
// pedagoS : export des pedagos
class pformat_xhtml extends pformat_default {

    function provide_export() {
      return false;
    }

	function provide_import() {
        return false;
    }
}
// ################################################################################################################
// archive : export des pedagos
class zformat_xhtml extends zformat_default {

    function provide_export() {
      return false;
    }

	function provide_import() {
        return false;
    }
}
// //////////////////////////////////////////////////////////////////////////////////////////////////////

?>

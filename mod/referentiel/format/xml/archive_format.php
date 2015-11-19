<?php

class zformat_xml extends zformat_default {

    function provide_import() {
        return false;
    }

    function provide_export() {
        return true;
    }


    // EXPORT FUNCTIONS START HERE

    function export_file_extension() {
    // override default type so extension is .xml
        return ".xml";
    }


    /**
     * Convert internal Moodle text format code into
     * human readable form
     * @param int id internal code
     * @return string format text
     */
    function get_format( $id ) {
        switch( $id ) {
        case 0:
            $name = "moodle_auto_format";
            break;
        case 1:
            $name = "html";
            break;
        case 2:
            $name = "plain_text";
            break;
        case 3:
            $name = "wiki_like";
            break;
        case 4:
            $name = "markdown";
            break;
        default:
            $name = "unknown";
        }
        return $name;
    }

    /**
     * Convert internal single question code into
     * human readable form
     * @param int id single question code
     * @return string single question string
     */
    function get_single( $id ) {
        switch( $id ) {
        case 0:
            $name = "false";
            break;
        case 1:
            $name = "true";
            break;
        default:
            $name = "unknown";
        }
        return $name;
    }

    /**
     * generates <text></text> tags, processing raw text therein
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
     */

    function writetext( $raw, $ilev=0, $short=true) {
        $indent = str_repeat( "  ",$ilev );

        // encode the text to 'disguise' HTML content
		$raw=preg_replace("/\r/", "", $raw);
		$raw=preg_replace("/\n/", " ", $raw);

        $raw = htmlspecialchars( $raw );

        if ($short) {
            $xml = "$indent<text>$raw</text>\n";
        }
        else {
            $xml = "$indent<text>\n$raw\n$indent</text>\n";
        }

        return $xml;
    }

    /**
     * generates raw text therein
     * @return string not formatted text
     */

    function writeraw( $raw) {
		$raw=preg_replace("/\r/", "", $raw);
		$raw=preg_replace("/\n/", " ", $raw);
	    return $raw;
    }

    function xmltidy( $content ) {
        // can only do this if tidy is installed
        if (extension_loaded('tidy')) {
            $config = array( 'input-xml'=>true, 'output-xml'=>true, 'indent'=>true, 'wrap'=>0 );
            $tidy = new tidy;
            $tidy->parseString($content, $config, 'utf8');
            $tidy->cleanRepair();
            return $tidy->value;
        }
        else {
            return $content;
        }
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
     * Turns item into an xml segment
     * @param item object
     * @return string xml segment
     */

    function write_item( $item ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- item: $item->id  -->\n";
		//
		if ($item){
			// DEBUG
			// echo "<br />\n";
			// print_r($item);
			$id = $this->writeraw( $item->id );
            $code = $this->writeraw( trim($item->code_item));
            $description_item = $this->writetext(trim($item->description_item));
            $ref_referentiel = $this->writeraw( $item->ref_referentiel);
            $ref_competence = $this->writeraw( $item->ref_competence);
			$type_item = $this->writeraw( trim($item->type_item));
			$poids_item = $this->writeraw( $item->poids_item);
			$empreinte_item = $this->writeraw( $item->empreinte_item);
			$num_item = $this->writeraw( $item->num_item);
            $expout .= "   <item>\n";
			// $expout .= "    <id>$id</id>\n";
			$expout .= "    <code>$code</code>\n";
            $expout .= "    <description_item>\n$description_item</description_item>\n";
            // $expout .= "    <ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            // $expout .= "    <ref_competence>$ref_competence</ref_competence>\n";
            $expout .= "    <type_item>$type_item</type_item>\n";
            $expout .= "    <poids_item>$poids_item</poids_item>\n";
            $expout .= "    <empreinte_item>$empreinte_item</empreinte_item>\n";
            $expout .= "    <num_item>$num_item</num_item>\n";
			$expout .= "   </item>\n\n";
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
        // add comment
        // $expout .= "\n\n<!-- competence: $competence->id  -->\n";
		//
		if ($competence){
			$id = $this->writeraw( $competence->id );
            $code = $this->writeraw( trim($competence->code_competence));
            $description_competence = $this->writetext(trim($competence->description_competence));
            $ref_domaine = $this->writeraw( $competence->ref_domaine);
			$num_competence = $this->writeraw( $competence->num_competence);
			$nb_item_competences = $this->writeraw( $competence->nb_item_competences);

            $expout .= "  <competence>\n";
			// $expout .= "<id>$id</id>\n";
			$expout .= "   <code_competence>$code</code_competence>\n";
            $expout .= "   <description_competence>\n$description_competence</description_competence>\n";
            // $expout .= "   <ref_domaine>$ref_domaine</ref_domaine>\n";
            $expout .= "   <num_competence>$num_competence</num_competence>\n";
            $expout .= "   <nb_item_competences>$nb_item_competences</nb_item_competences>\n\n";

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
			$expout .= "  </competence>\n\n";
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
        // add comment
        // $expout .= "\n\n<!-- domaine: $domaine->id  -->\n";
		//
		if ($domaine){
			$id = $this->writeraw( $domaine->id );
            $code = $this->writeraw( trim($domaine->code_domaine) );
            $description_domaine = $this->writetext(trim($domaine->description_domaine));
            $ref_referentiel = $this->writeraw( $domaine->ref_referentiel );
			$num_domaine = $this->writeraw( $domaine->num_domaine );
			$nb_competences = $this->writeraw( $domaine->nb_competences );

            $expout .= " <domaine>\n";
			// $expout .= "  <id>$id</id>\n";
			$expout .= "  <code_domaine>$code</code_domaine>\n";
            $expout .= "  <description_domaine>\n$description_domaine</description_domaine>\n";
            // $expout .= " <ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            $expout .= "  <num_domaine>$num_domaine</num_domaine>\n";
            $expout .= "  <nb_competences>$nb_competences</nb_competences>\n\n";

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
			$expout .= " </domaine>\n\n";
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
        // initial string;
        $expout = "";
        // add comment
		//         $rreferentiel
		if ($this->rreferentiel){
			$id = $this->writeraw( $this->rreferentiel->id );
            $name = $this->writeraw( trim($this->rreferentiel->name) );
            $code_referentiel = $this->writeraw( trim($this->rreferentiel->code_referentiel));
            $description_referentiel = $this->writetext(trim($this->rreferentiel->description_referentiel));
            $url_referentiel = $this->writeraw( trim($this->rreferentiel->url_referentiel) );
			$timemodified = $this->writeraw( $this->rreferentiel->timemodified );
			$nb_domaines = $this->writeraw( $this->rreferentiel->nb_domaines );
			$liste_codes_competence = $this->writeraw( trim($this->rreferentiel->liste_codes_competence) );
			$liste_empreintes_competence = $this->writeraw( trim($this->rreferentiel->liste_empreintes_competence) );
            $liste_poids_competence = $this->writeraw( trim($this->rreferentiel->liste_poids_competence) );
			$local = $this->writeraw( $this->rreferentiel->local );
			$logo_referentiel = $this->writeraw( $this->rreferentiel->logo_referentiel );
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
            $minima_certificat = $this->rreferentiel->minima_certificat;

			// $expout .= "<id>$id</id>\n";
			$expout .= " <name>$name</name>\n";
			$expout .= " <code_referentiel>$code_referentiel</code_referentiel>\n";
            $expout .= " <description_referentiel>\n$description_referentiel</description_referentiel>\n";
            $expout .= " <url_referentiel>$url_referentiel</url_referentiel>\n";
            $expout .= " <seuil_certificat>$seuil_certificat</seuil_certificat>\n";
            $expout .= " <minima_certificat>$minima_certificat</minima_certificat>\n";
            $expout .= " <timemodified>$timemodified</timemodified>\n";
            $expout .= " <nb_domaines>$nb_domaines</nb_domaines>\n";
            $expout .= " <liste_codes_competence>$liste_codes_competence</liste_codes_competence>\n";
            $expout .= " <liste_empreintes_competence>$liste_empreintes_competence</liste_empreintes_competence>\n";
            $expout .= " <liste_poids_competence>$liste_poids_competence</liste_poids_competence>\n";

			// $expout .= " <local>$local</local>\n";
			// PAS DE LOGO ICI
			// $expout .= " <logo_referentiel>$logo_referentiel</logo_referentiel>\n";

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
        return $expout;
    }

/// ACTIVITES
    function write_document( $userid, $document ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- document: $document->id  -->\n";
		//
		if ($document){
			$id = $this->writeraw( $document->id );
            $type_document = $this->writeraw( trim($document->type_document));
            $description_document = $this->writetext(trim($document->description_document));
			$url_document = $this->writeraw( $document->url_document);
            $ref_activite = $this->writeraw( $document->ref_activite);
            $timestamp = $this->writeraw( $document->timestamp );
            if (!empty($url_document) && !preg_match("/http/",$url_document)){
                if ($this->export_documents){
                // le fichier est copié dans le dossier temporaire moodledata/archive/referentiel_id/document_files
                    $new_url=referentiel_copy_document_file ($this->rreferentiel->id, $this->user_creator, $userid, $url_document);
                    if (!empty($new_url)){
                        $url_document='./'.$new_url;
                    }
                }
                else{   // recopie de l'adresse vers le serveur Moodle
                    $url_document = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$url_document);
                    // DEBUG
                    //echo "<br />DEBUG :: format/xml/archive_format.php :: 380\n";
                    //echo "<br />URL : $url_document\n";
                }
            }

            $expout .= "<document>\n";
			$expout .= "<id>$id</id>\n";
			$expout .= "<type_document>$type_document</type_document>\n";
            $expout .= "<description_document>\n$description_document</description_document>\n";
            $expout .= "<url_document>$url_document</url_document>\n";
            $expout .= "<ref_activite>$ref_activite</ref_activite>\n";
            $expout .= "<timestamp>$timestamp</timestamp>\n";
			$expout .= "</document>\n";
			
        }
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
        // $expout .= "\n\n<!-- activite: $activite->id  -->\n";
		//
		if ($activite){
			// DEBUG
			// echo "<br />DEBUG LIGNE 960<br />\n";
			// print_r($activite);

			$id = $this->writeraw( $activite->id );
            $type_activite = $this->writeraw( trim($activite->type_activite));
            $description_activite = $this->writetext(trim($activite->description_activite));
            $competences_activite = $this->writeraw(trim($activite->competences_activite));
            $commentaire_activite = $this->writetext(trim($activite->commentaire_activite));
            $ref_instance = $this->writeraw( $activite->ref_instance);
            $ref_referentiel = $this->writeraw( $activite->ref_referentiel);
            $ref_course = $this->writeraw( $activite->ref_course);
			$userid = $this->writeraw( trim($activite->userid));
			$teacherid = $this->writeraw( $activite->teacherid);
			$date_creation = $this->writeraw( $activite->date_creation);
			$date_modif_student = $this->writeraw( $activite->date_modif_student);
			$date_modif = $this->writeraw( $activite->date_modif);
			$approved = $this->writeraw( $activite->approved);

            $expout .= "<activite>\n";
			$expout .= "<id>$id</id>\n";
			$expout .= "<type_activite>$type_activite</type_activite>\n";
            $expout .= "<description_activite>\n$description_activite</description_activite>\n";
            $expout .= "<competences_activite>$competences_activite</competences_activite>\n";
            $expout .= "<commentaire_activite>\n$commentaire_activite</commentaire_activite>\n";
            $expout .= "<ref_instance>$ref_instance</ref_instance>\n";
            $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            $expout .= "<ref_course>$ref_course</ref_course>\n";
            $expout .= "<userid>$userid</userid>\n";
    		$expout .= "<lastname>".referentiel_get_user_nom($activite->userid)."</lastname>\n";
            $expout .= "<firstname>".referentiel_get_user_prenom($activite->userid)."</firstname>\n";

            $expout .= "<teacherid>$teacherid</teacherid>\n";
    		$expout .= "<teacher_lastname>".referentiel_get_user_nom($activite->teacherid)."</teacher_lastname>\n";
            $expout .= "<teacher_firstname>".referentiel_get_user_prenom($activite->teacherid)."</teacher_firstname>\n";

            $expout .= "<date_creation>$date_creation</date_creation>\n";
            $expout .= "<date_modif_student>$date_modif_student</date_modif_student>\n";

            $expout .= "<date_modif>$date_modif</date_modif>\n";
            $expout .= "<approved>$approved</approved>\n";

			// DOCUMENTS
			$records_documents = referentiel_get_documents($activite->id);

			if ($records_documents){
				foreach ($records_documents as $record_d){
                    $expout .= $this->write_document( $activite->userid, $record_d );
    			}
			}

			$expout .= "</activite>\n";
        }
        return $expout;
    }

    function write_instance($instance_record) {
    	global $CFG;
        // initial string;
        $expout = "";
        // add comment
        //$expout .= "\n\n<!-- instance : ".$instance_record->id."  -->\n";
		//
		if ($instance_record){
			// DEBUG
			//echo "<br />DEBUG :: ./formta/xml/archive_format.php :: LIGNE 455<br />\n";
			//print_r($instance_record);
            // exit;
			$id = $this->writeraw( $instance_record->id );
            $name = $this->writeraw( trim($instance_record->name) );
            $description_instance = $this->writetext(trim($instance_record->description_instance));
            $label_domaine = $this->writeraw( trim($instance_record->label_domaine) );
            $label_competence = $this->writeraw( trim($instance_record->label_competence) );
            $label_item = $this->writeraw( trim($instance_record->label_item) );
            $date_instance = $this->writeraw( $instance_record->date_instance);
            $course = $this->writeraw( $instance_record->course);
            $ref_referentiel = $this->writeraw( $instance_record->ref_referentiel);
			$visible = $this->writeraw( $instance_record->visible );

// INSTANCE
            $expout .= "<instance>\n";
//			$expout .= "<id>$id</id>\n";
			$expout .= "<name>$name</name>\n";
            $expout .= "<description_instance>\n$description_instance</description_instance>\n";
//            $expout .= "<label_domaine>$label_domaine</label_domaine>\n";
//            $expout .= "<label_competence>$label_competence</label_competence>\n";
//            $expout .= "<label_item>$label_item</label_item>\n";
            $expout .= "<date_instance>$date_instance</date_instance>\n";
            $expout .= "<course>$course</course>\n";
//            $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
//            $expout .= "<visible>$visible</visible>\n";

			$expout .= "</instance>\n";
        }
        return $expout;
    }

    function write_liste_activites($records_activites) {
    	global $CFG;
        // initial string;
        $expout = "";
		if (!empty($records_activites)){
			// ACTIVITES
            foreach ($records_activites as $record_a){
                $expout .= $this->write_activite( $record_a );
			}
        }
        return $expout;
    }


    /// ETABLISSEMENT


    function write_etablissement( $record ) {
        // initial string;
        $expout = "";
        // add comment
        //$expout .= "\n\n<!-- etablissement: $record->id  -->\n";
		if ($record){
			// DEBUG
			// echo "<br />\n";
			// print_r($record);
			$id = $this->writeraw( $record->id );
			$num_etablissement = $this->writeraw( $record->num_etablissement);
			$nom_etablissement = $this->writeraw( $record->nom_etablissement);
			$adresse_etablissement = $this->writetext( $record->adresse_etablissement);
			$logo = $this->writeraw( $record->logo_etablissement);

            $expout .= "<etablissement>\n";
//			$expout .= "<id>$id</id>\n";
            $expout .= "<num_etablissement>$num_etablissement</num_etablissement>\n";
            $expout .= "<nom_etablissement>$nom_etablissement</nom_etablissement>\n";
            $expout .= "<adresse_etablissement>\n$adresse_etablissement</adresse_etablissement>\n";
//            $expout .= "<logo_etablissement>$logo</logo_etablissement>\n";
			$expout .= "</etablissement>\n";
        }
        return $expout;
    }



    /// ETUDIANT

	function write_etudiant( $record ) {
        // initial string;
        $expout = "";
        // add comment
        //$expout .= "\n\n<!-- etudiant: $record->id  -->\n";
		if ($record){
			// DEBUG
			// echo "<br />\n";
			// print_r($record);
			$id = $this->writeraw( $record->id );
			$userid = $this->writeraw( $record->userid );
            $ref_etablissement = $this->writeraw( $record->ref_etablissement);
			$num_etudiant = $this->writeraw( $record->num_etudiant);
			$ddn_etudiant = $this->writeraw( $record->ddn_etudiant);
			$lieu_naissance = $this->writeraw( $record->lieu_naissance);
			$departement_naissance = $this->writeraw( $record->departement_naissance);
			$adresse_etudiant = $this->writeraw( $record->adresse_etudiant);

			$login=referentiel_get_user_login($record->userid );
/*
            if ($num_etudiant==$login){
                    $texte=$num_etudiant;
            }
            elseif ($num_etudiant==''){
                    $texte=$login;
            }
            else{
                    $texte=$num_etudiant." (".$login.")";
            }
*/

                $expout .= "<etudiant>\n";
//	       		$expout .= "<id>$id</id>\n";
		      	$expout .= "<userid>$userid</userid>\n";
		      	$expout .= "<login>$login</login>\n";
    			$expout .= "<num_etudiant>$num_etudiant</num_etudiant>\n";
    		    $expout .= "<lastname>".referentiel_get_user_nom($record->userid)."</lastname>\n";
                $expout .= "<firstname>".referentiel_get_user_prenom($record->userid)."</firstname>\n";
                $expout .= "<ddn_etudiant>$ddn_etudiant</ddn_etudiant>\n";
                $expout .= "<lieu_naissance>$lieu_naissance</lieu_naissance>\n";
                $expout .= "<departement_naissance>$departement_naissance</departement_naissance>\n";
                $expout .= "<adresse_etudiant>$adresse_etudiant</adresse_etudiant>\n";
//		      	$expout .= "<ref_etablissement>$ref_etablissement</ref_etablissement>\n";

                // Etablissement
                $record_etablissement=$this->t_etablissements[$record->ref_etablissement];
	           	if ($record_etablissement){
			     	$expout .= $this->write_etablissement( $record_etablissement );
			    }
			    $expout .= "</etudiant>\n";
        }
        return $expout;
    }

    // PEDAGOGIES
	function write_pedagogie( $userid) {
    // $expout .= "promotion;formation;pedagogie;composante;num_groupe;commentaire;date_cloture";
        $expout="";
        if ($userid){
            $rec_pedago=referentiel_get_pedagogie_user($userid, $this->rreferentiel->id);
            if ($rec_pedago){
                $expout .= "\n<pedagos>\n";

                $expout .= "<promotion>".$rec_pedago->promotion."</promotion>
<formation>".stripslashes($rec_pedago->formation)."</formation>
<pedagogie>".stripslashes($rec_pedago->pedagogie)."</pedagogie>
<composante>".stripslashes($rec_pedago->composante)."</composante>
<num_groupe>".stripslashes($rec_pedago->num_groupe)."</num_groupe>
<commentaire>".stripslashes($rec_pedago->commentaire)."</commentaire>
<date_cloture>".stripslashes($rec_pedago->date_cloture)."</date_cloture>
</pedagos>\n";
            }
        }
        return $expout;
    }


    /// CERTIFICATS

	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_certificat( $record ) {
    	global $CFG;
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- certificat : $record->id  -->\n";
		//
		if ($record){
			// DEBUG
			// echo "<br />DEBUG LIGNE 1298<br />\n";
			// print_r($record);

			$id = $this->writeraw( $record->id );
            $commentaire_certificat = $this->writetext(trim($record->commentaire_certificat));
            $competences_certificat = $this->writeraw( trim($record->competences_certificat) );
            $competences_activite = $this->writeraw( trim($record->competences_activite) );

            $decision_jury = $this->writeraw( trim($record->decision_jury) );
            $date_decision = $this->writeraw( userdate(trim($record->date_decision)) );
            $userid = $this->writeraw( $record->userid);
            $teacherid = $this->writeraw( $record->teacherid);
            $ref_referentiel = $this->writeraw( $record->ref_referentiel);
			$verrou = $this->writeraw( $record->verrou );
			$valide = $this->writeraw( $record->valide );
			$evaluation = $this->writeraw( $record->evaluation );
			$synthese_certificat = $this->writetext(trim($record->synthese_certificat));
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

                    if ($this->export_pedagos){
                        $expout .= $this->write_pedagogie($record->userid);
                    }

                    $expout .= "<certificat>\n";

                        // la totale
                        if ($this->format_condense!=1){
                            $expout .= "<competences_certificat>".$this->certificat_items_binaire($competences_certificat, $this->rreferentiel->id)."</competences_certificat>\n";
                        }
                        else{
                            $expout .= "<competences_certificat>".$this->certificat_pourcentage($competences_certificat, $this->rreferentiel->id)."</competences_certificat>";
                        }
                        $expout .= "<competences_activite>$competences_certificat</competences_activite>\n";
                        $expout .= "<synthese>\n$synthese_certificat</synthese>\n";
                        $expout .= "<commentaire_certificat>\n$commentaire_certificat</commentaire_certificat>\n";
                        $expout .= "<decision_jury>$decision_jury</decision_jury>\n";
                        $expout .= "<date_decision>$date_decision</date_decision>\n";
                        $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
                        $expout .= "<verrou>$verrou</verrou>\n";
                        $expout .= "<valide>$valide</valide>\n";
        			    $expout .= "<evaluation>$evaluation</evaluation>\n";

                    $expout .= "</certificat>\n";
                }
            }
        }
		// DEBUG
		// echo "<br />DEBUG LIGNE 1330<br />\n";
		// echo htmlentities ($expout, ENT_QUOTES, 'UTF-8')  ;

        return $expout;
    }

	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */
    function write_archive_user($userid) {
    	global $CFG;
        // initial string;
        $expout = "";
        // REFERENTIEL
        if (!empty($this->rreferentiel)){

            $expout .= $this->write_referentiel();

            // recuperer les instances associées à ce référentiel  pour l'affichage ultérieur
            $records_instance=referentiel_referentiel_get_instances($this->rreferentiel->id);
            foreach ($records_instance as $record_i){
                $this->t_instances[$record_i->id]=$record_i;
            }
            // recuperer les etablissement associées à ce référentiel  pour l'affichage ultérieur
            $records_etablissement=referentiel_get_etablissements();
            foreach ($records_etablissement as $record_e){
                $this->t_etablissements[$record_e->id]=$record_e;
            }

            if (!empty($userid)){

                        $expout .= "<user>\n";
                        if (is_object($userid)){
                            $userid=$userid->userid;
                        }
                        //echo "<br />\n";
                        //echo($userid);
                        // exit;

                        // certificat
                        $record_certificat = referentiel_get_certificat_user($userid, $this->rreferentiel->id);
						if (!empty($record_certificat)){
                            $expout .= $this->write_certificat($record_certificat);
                        }

                        foreach ($this->t_instances as $record_i){
                            $expout .= "<activites>\n";
                            $recs=referentiel_get_activites_users_from_instance($record_i->id, $userid, '', 'userid ASC, date_creation ASC ');

                            if (!empty($recs)){
                                $expout .= $this->write_instance( $record_i);
                                $expout .= $this->write_liste_activites( $recs );
                            }
                            $expout .= "</activites>\n";
                        }
                        $expout .= "</user>\n";
				}
		}
        return $expout;
    }

	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_archive() {
    	global $CFG;
        // initial string;
        $expout = "";
        // REFERENTIEL
        if (!empty($this->rreferentiel)){

            $expout .= $this->write_referentiel();

            // recuperer les instances associées à ce référentiel  pour l'affichage ultérieur
            $records_instance=referentiel_referentiel_get_instances($this->rreferentiel->id);
            foreach ($records_instance as $record_i){
                $this->t_instances[$record_i->id]=$record_i;
            }
            // recuperer les etablissement associées à ce référentiel  pour l'affichage ultérieur
            $records_etablissement=referentiel_get_etablissements();
            foreach ($records_etablissement as $record_e){
                $this->t_etablissements[$record_e->id]=$record_e;
            }

            if (!empty($this->records_users)){

                    $expout .= "<users>\n";
                    $u=0;
					foreach ($this->records_users as $userid){
                        // $expout .= "<user>\n";
                        if (is_object($userid)){
                            $userid=$userid->userid;
                        }
                        //echo "<br />\n";
                        //echo($userid);
                        // exit;

                        // certificat
                        $record_certificat = referentiel_get_certificat_user($userid, $this->rreferentiel->id);
						if (!empty($record_certificat)){
                            $expout .= $this->write_certificat($record_certificat);
                        }

                        foreach ($this->t_instances as $record_i){
                            $expout .= "<activites>\n";
                            $recs=referentiel_get_activites_users_from_instance($record_i->id, $userid, '', 'userid ASC, date_creation ASC ');

                            if (!empty($recs)){
                                $expout .= $this->write_instance( $record_i);
                                $expout .= $this->write_liste_activites( $recs );
                            }
                            $expout .= "</activites>\n";
                        }
                        $expout .= "</user>\n";
					}
					$expout .= "</users>\n\n";
				}
		}
        return $expout;
    }

        // -------------------
    function certificat_items_binaire($liste_code, $ref_referentiel){
    // retourne les valeur 0/1 pour chaque item de competence

    $separateur1='/';
    $separateur2=':';

    global $OK_REFERENTIEL_DATA;

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

			// ITEMS
/*
			for ($i=0; $i<count($t_item_code); $i++){
                $s.=$t_item_code[$i].';';
			}
			$s.="\n";
*/
            $s.="\n<items>\n";
			for ($i=0; $i<count($t_item_coeff); $i++){
				if ($t_item_empreinte[$i]){
                    $s.='<item code="'.$t_item_code[$i].'" valeur=';
    				if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
						$s.='"1" ';
					}
					else{
                        $s.='"0" ';
					}
                    $s.="/>\n";
				}
    		}
            $s.="</items>\n";
		}
	}
	}

	return $s;
    }

   // -------------------
    function certificat_pourcentage($liste_code, $ref_referentiel){
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
		$liste_code=referentiel_purge_dernier_separateur($liste_code, $separateur1);

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
            */
            $s.="\n<domaines>\n";
            for ($i=0; $i<count($t_domaine_coeff); $i++){
                $s.='<domaine code="'.$t_domaine[$i].'" pourcent=';
				if ($t_domaine_coeff[$i]){
					$s.='"'.referentiel_pourcentage($t_certif_domaine_poids[$i], $t_domaine_coeff[$i]).'%" ';
				}
				else{
					$s.='"0%" ';
				}
                $s.="/>\n";
            }
            $s.="</domaines>\n";


            /*
			for ($i=0; $i<count($t_competence); $i++){
					$s.=$t_competence[$i].';';
			}
			$s.="\n";
			*/
			$s.="<competences>\n";
			for ($i=0; $i<count($t_competence); $i++){
			    $s.='<competence code="'.$t_competence[$i].'" pourcent=';
				if ($t_competence_coeff[$i]){
					$s.='"'.referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i]).'%" ';
				}
				else{
					$s.='"0%" ';
				}
                $s.="/>\n";
            }
            $s.="</competences>\n";

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
            */
            $s.="<items>\n";
			for ($i=0; $i<count($t_item_coeff); $i++){
            	if ($t_item_empreinte[$i]){
                    $s.='<item code="'.$t_item_code[$i].'" pourcent=';

					if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
						$s.='"100%" ';
					}
					else{
						$s.='"'.referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i]).'%" ';
					}
                    $s.="/>\n";
                }
			}
            $s.="</items>\n";

		}
	}
	}

	return $s;
    }


    /**
     * @param string processed output text
     */
    function get_url_users(){
        /*
        	echo "<br />DEBUG format.php :: LIGNE 2977<br />\n";
			print_object($this->records_users);
            echo "<br />EXIT\n";
        //    exit;
        */
        $expout ='';
        foreach ($this->records_users as $userid){
            if ($filename=$this->get_filename_by_userid($userid)){
                $expout .= '<a href="./'. $filename . $this->export_file_extension().'">'.$filename.$this->export_file_extension().'</a><br />'."\n";
            }
        }
        return $expout;
    }

	function write_index() {
	  // add xhtml headers and footers

  		global $CFG;
		global $USER;
  		// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/html/html.css" );
		$css = implode( ' ',$css_lines );
		$xp = "<html>\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
		$xp .= "<meta author=\"".referentiel_get_user_info($USER->id)."\">\n";
	    $xp .= "<title>".get_string('archive_file', 'referentiel')."</title>\n";
  		$xp .= $css;

  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= "<div id='dataDiv'><h1 align='center'>".get_string('generatedby', 'referentiel')."</h1>\n";
        $xp .= "<p align='center'>".date("Y-m-d H:g:s")."</p>\n";
		$xp .= "<div align='center'>\n";
        $xp .= $this->get_url_users()."\n";
        $xp .= "</div>\n";
        $xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function presave_process( $content, $userid=0, $filename='' ) {
    // override method to allow us to add xml headers and footers

        $lastname=referentiel_get_user_nom($userid);
        $firstname=referentiel_get_user_prenom($userid);

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<referentiel>\n" .
                       $content .
                       "</referentiel>\n\n";

        // make the xml look nice
        $content = $this->xmltidy( $content );

        return $content;
    }


}


?>

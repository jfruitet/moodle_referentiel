<?php // $Id: format.php,v 1.21.2.16 2008/01/15 14:58:10 thepurpleblob Exp $
//
///////////////////////////////////////////////////////////////
// XML import/export
//
//////////////////////////////////////////////////////////////////////////
// Based on default.php, included by ../import.php
/**
 * @package referetielbank
 * @subpackage importexport
 */
require_once( "$CFG->libdir/xmlize.php" );


class rformat_xml extends rformat_default {

    function provide_import() {
        return true;
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
		$raw=preg_replace("/\n/", "|||", $raw);

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


    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<referentiel>\n" .
                       $content .
                       "</referentiel>\n\n";

        // make the xml look nice
        $content = $this->xmltidy( $content );

        return $content;
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
// MODIF 2012/03/08
			$type_competence = $this->writeraw( trim($competence->type_competence));
			$seuil_competence = $this->writeraw( trim($competence->seuil_competence));
// MODIF 2012/03/26
			$minima_competence = $this->writeraw( trim($competence->minima_competence));

            $expout .= "  <competence>\n";
			// $expout .= "<id>$id</id>\n";
			$expout .= "   <code_competence>$code</code_competence>\n";
            $expout .= "   <description_competence>\n$description_competence</description_competence>\n";
// MODIF 2012/03/08
            $expout .= "   <type_competence>$type_competence</type_competence>\n";
            $expout .= "   <seuil_competence>$seuil_competence</seuil_competence>\n";
// MODIF 2012/03/26
            $expout .= "   <minima_competence>$minima_competence</minima_competence>\n";

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
// MODIF 2012/03/08
			$type_domaine = $this->writeraw( trim($domaine->type_domaine));
			$seuil_domaine = $this->writeraw( trim($domaine->seuil_domaine));
// MODIF 2012/03/26
			$minima_domaine = $this->writeraw( trim($domaine->minima_domaine));

            $expout .= " <domaine>\n";
			// $expout .= "  <id>$id</id>\n";
			$expout .= "  <code_domaine>$code</code_domaine>\n";
            $expout .= "  <description_domaine>\n$description_domaine</description_domaine>\n";
// MODIF 2012/03/08
            $expout .= "  <type_domaine>$type_domaine</type_domaine>\n";
            $expout .= "  <seuil_domaine>$seuil_domaine</seuil_domaine>\n";
// MODIF 2012/03/26
            $expout .= "  <minima_domaine>$minima_domaine</minima_domaine>\n";

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
			$id = $this->writeraw( $protocole->id );
            $ref_occurrence = $this->writeraw($protocole->ref_occurrence);
			$seuil_referentiel = $this->writeraw( $protocole->seuil_referentiel );
			$minima_referentiel = $this->writeraw( $protocole->minima_referentiel );
            $l_domaines_oblig = $this->writetext(trim($protocole->l_domaines_oblig));
            $l_seuils_domaines = $this->writetext(trim($protocole->l_seuils_domaines));
            $l_minimas_domaines = $this->writetext(trim($protocole->l_minimas_domaines));
            $l_domaines_oblig = $this->writetext(trim($protocole->l_domaines_oblig));
            $l_competences_oblig = $this->writetext(trim($protocole->l_competences_oblig));
            $l_seuils_competences = $this->writetext(trim($protocole->l_seuils_competences));
            $l_minimas_competences = $this->writetext(trim($protocole->l_minimas_competences));
            $l_items_oblig = $this->writetext(trim($protocole->l_items_oblig));
            $timemodified = $this->writeraw($protocole->timemodified);
			$actif = $this->writeraw( $protocole->actif );
            $commentaire = $this->writetext(trim($protocole->commentaire));

            $expout .= " <protocole>\n";
			// $expout .= "  <p_id>$id</p_id>\n";
			// $expout .= "  <p_ref_occurrence>$ref_occurrence</p_ref_occurrence>\n";
			$expout .= "  <p_seuil_referentiel>$seuil_referentiel</p_seuil_referentiel>\n";
			$expout .= "  <p_minima_referentiel>$minima_referentiel</p_minima_referentiel>\n";
			$expout .= "  <p_domaines_oblig>\n$l_domaines_oblig</p_domaines_oblig>\n";
			$expout .= "  <p_seuils_domaines>\n$l_seuils_domaines</p_seuils_domaines>\n";
			$expout .= "  <p_minimas_domaines>\n$l_minimas_domaines</p_minimas_domaines>\n";
			$expout .= "  <p_competences_oblig>\n$l_competences_oblig</p_competences_oblig>\n";
			$expout .= "  <p_seuils_competences>\n$l_seuils_competences</p_seuils_competences>\n";
			$expout .= "  <p_minimas_competences>\n$l_minimas_competences</p_minimas_competences>\n";
			$expout .= "  <p_items_oblig>\n$l_items_oblig</p_items_oblig>\n";
			$expout .= "  <p_timemodified>$timemodified</p_timemodified>\n";
			$expout .= "  <p_actif>$actif</p_actif>\n";
            $expout .= "  <p_commentaire>\n$commentaire</p_commentaire>\n\n";
			$expout .= " </protocole>\n\n";
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
			$seuil_certificat = $this->writeraw( $this->rreferentiel->seuil_certificat );
// MODIF JF 2012/03/26
			$minima_certificat = $this->writeraw( $this->rreferentiel->minima_certificat );

			$timemodified = $this->writeraw( $this->rreferentiel->timemodified );
			$nb_domaines = $this->writeraw( $this->rreferentiel->nb_domaines );
			$liste_codes_competence = $this->writeraw( trim($this->rreferentiel->liste_codes_competence) );
			$liste_empreintes_competence = $this->writeraw( trim($this->rreferentiel->liste_empreintes_competence) );
			$local = $this->writeraw( $this->rreferentiel->local );
			$logo_referentiel = $this->writeraw( $this->rreferentiel->logo_referentiel );

			// $expout .= "<id>$id</id>\n";
			$expout .= " <name>$name</name>\n";
			$expout .= " <code_referentiel>$code_referentiel</code_referentiel>\n";
            $expout .= " <description_referentiel>\n$description_referentiel</description_referentiel>\n";
            $expout .= " <url_referentiel>$url_referentiel</url_referentiel>\n";

            $expout .= " <seuil_certificat>$seuil_certificat</seuil_certificat>\n";
// MODIF JF 2012/03/26
            $expout .= " <minima_certificat>$minima_certificat</minima_certificat>\n";
            $expout .= " <timemodified>$timemodified</timemodified>\n";
            $expout .= " <nb_domaines>$nb_domaines</nb_domaines>\n";
            $expout .= " <liste_codes_competence>$liste_codes_competence</liste_codes_competence>\n";
            $expout .= " <liste_empreintes_competence>$liste_empreintes_competence</liste_empreintes_competence>\n";
			// $expout .= " <local>$local</local>\n";
			// PAS DE LOGO ICI
			// $expout .= " <logo_referentiel>$logo_referentiel</logo_referentiel>\n";

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
        return $expout;
    }



    // IMPORT FUNCTIONS START HERE

    /**
     * Translate human readable format name
     * into internal Moodle code number
     * @param string name format name from xml file
     * @return int Moodle format code
     */
    function trans_format( $name ) {
        $name = trim($name);

        if ($name=='moodle_auto_format') {
            $id = 0;
        }
        elseif ($name=='html') {
            $id = 1;
        }
        elseif ($name=='plain_text') {
            $id = 2;
        }
        elseif ($name=='wiki_like') {
            $id = 3;
        }
        elseif ($name=='markdown') {
            $id = 4;
        }
        else {
            $id = 0; // or maybe warning required
        }
        return $id;
    }

    /**
     * Translate human readable single answer option
     * to internal code number
     * @param string name true/false
     * @return int internal code number
     */
    function trans_single( $name ) {
        $name = trim($name);
        if ($name == "false" || !$name) {
            return 0;
        }
        else {
            return 1;
        }
    }

    /**
     * process text string from xml file
     * @param array $text bit of xml tree after ['text']
     * @return string processed text
     */
    function import_text( $text ) {
        // quick sanity check
        if (empty($text)) {
            return '';
        }
        $data = $text[0]['#'];
        return addslashes(trim( $data ));
    }

    /**
     * return the value of a node, given a path to the node
     * if it doesn't exist return the default value
     * @param array xml data to read
     * @param array path path to node expressed as array
     * @param mixed default
     * @param bool istext process as text
     * @param string error if set value must exist, return false and issue message if not
     * @return mixed value
     */
    function getpath( $xml, $path, $default, $istext=false, $error='' ) {
        foreach ($path as $index) {
			// echo " $index ";
            if (!isset($xml[$index])) {
                if (!empty($error)) {
                    $this->error( $error );
                    return false;
                }
                else {
					// echo " erreur ";
                    return $default;
                }
            }
            else {
				$xml = $xml[$index];
				// echo " $xml ";
			}
        }
        if ($istext) {
            $xml = addslashes( trim( $xml ) );
        }

        return $xml;
    }


    /**
     * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees
     */
    function import_referentiel( $xmlreferentiel ) {
	// recupere le fichier xml
	// selon les parametres soit cree une nouvelle occurence
	// soit modifie une occurrence courante de referentiel
	global $SESSION;
	global $USER;
	global $CFG;
	$nbdomaines=0;        // compteur
	$nbcompetences=0;        // compteur
    $nbitems=0;              // compteur

		// print_r($xmlreferentiel);
		if (!isset($this->action) || (isset($this->action) && ($this->action!="selectreferentiel") && ($this->action!="importreferentiel"))){
			if (!(isset($this->course->id) && ($this->course->id>0))
				||
				!(isset($this->rreferentiel->id) && ($this->rreferentiel->id>0))
				||
				!(isset($this->coursemodule->id) && ($this->coursemodule->id>0))
				){
				$this->error( get_string( 'incompletedata', 'referentiel' ) );
				return false;
			}
		}
		else if (isset($this->action) && ($this->action=="selectreferentiel")){
			if (!(isset($this->course->id) && ($this->course->id>0))){
				$this->error( get_string( 'incompletedata', 'referentiel' ) );
				return false;
			}
		}
		else if (isset($this->action) && ($this->action=="importreferentiel")){
			if (!(isset($this->course->id) && ($this->course->id>0))){
				$this->error( get_string( 'incompletedata', 'referentiel' ) );
				return false;
			}
		}

		$risque_ecrasement=false;

        // get some error strings
        $error_noname = get_string( 'xmlimportnoname', 'referentiel' );
        $error_nocode = get_string( 'xmlimportnocode', 'referentiel' );
		$error_override = get_string( 'overriderisk', 'referentiel' );

        // this routine initialises the import object
        $re = $this->defaultreferentiel();
        //
		// $re->id = $this->getpath( $xmlreferentiel, array('#','id',0,'#'), '', false, '');
        $re->name = $this->getpath( $xmlreferentiel, array('#','name','0','#'), '', true, $error_noname);
        $re->code_referentiel = $this->getpath( $xmlreferentiel, array('#','code_referentiel',0,'#'), '', true, $error_nocode);
        $re->description_referentiel = $this->getpath( $xmlreferentiel, array('#','description_referentiel',0,'#','text',0,'#'), '', true, '');
        $re->url_referentiel = $this->getpath( $xmlreferentiel, array('#','url_referentiel',0,'#'), '', true, '');
		$re->seuil_certificat = $this->getpath( $xmlreferentiel, array('#','seuil_certificat',0,'#'), '', false, '');
		$re->minima_certificat = $this->getpath( $xmlreferentiel, array('#','minima_certificat',0,'#'), '', false, '');
		$re->timemodified = $this->getpath( $xmlreferentiel, array('#','timemodified',0,'#'), '', false, '');
		$re->nb_domaines = $this->getpath( $xmlreferentiel, array('#','nb_domaines',0,'#'), '', false, '');
		$re->liste_codes_competence = $this->getpath( $xmlreferentiel, array('#','liste_codes_competence',0,'#'), '', true, '');
		$re->liste_empreintes_competence = $this->getpath( $xmlreferentiel, array('#','liste_empreintes_competence',0,'#'), '', true, '');
		$re->logo_referentiel = $this->getpath( $xmlreferentiel, array('#','logo_referentiel',0,'#'), '', true, '');
		// $re->local = $this->getpath( $xmlreferentiel, array('#','course',0,'#'), '', false, '');

		/*
		// traitement d'une image associee
		// non implante
        $image = $this->getpath( $xmlreferentiel, array('#','image',0,'#'), $re->image );
        $image_base64 = $this->getpath( $xmlreferentiel, array('#','image_base64','0','#'),'' );
        if (!empty($image_base64)) {
            $re->image = $this->importimagefile( $image, stripslashes($image_base64) );
        }
		*/

		$re->export_process = false;
		$re->import_process = true;

		// le referentiel est toujours place dans le cours local d'appel
		$re->course = $this->course->id;

		$risque_ecrasement=false;
		if (!isset($this->action) || ($this->action!="importreferentiel")){
			// importer dans le cours courant en remplacement du referentiel courant
			// Verifier si ecrasement referentiel local

			if (isset($re->name) && ($re->name!="")
				&&
				isset($re->code_referentiel) && ($re->code_referentiel!="")
				&&
				isset($re->id) && ($re->id>0)
				&&
				isset($re->course) && ($re->course>0)){
				// sauvegarder ?
				if ($this->course->id==$re->course){
					if (
						(isset($this->rreferentiel->id) && ($this->rreferentiel->id==$re->id))
						||
						(
							(isset($this->rreferentiel->name) && ($this->rreferentiel->name==$re->name))
							&&
							(isset($this->rreferentiel->code_referentiel) && ($this->rreferentiel->code_referentiel==$re->code_referentiel))
						)
					)
					{
						$risque_ecrasement=true;
					}
				}
			}
		}

		if (($risque_ecrasement==false) || ($this->newinstance==1)) {
			// Enregistrer dans la base comme un nouveau referentiel_referentiel du cours courant
			$new_referentiel_id=referentiel_add_referentiel($re);
			$this->setReferentielId($new_referentiel_id);
			// DEBUG
			// echo "<br />DEBUG xml/format.php ligne 572<br />NEW REFERENTIEL ID ENREGISTRE : ".$this->new_referentiel_id."\n";
		}
		else if (($risque_ecrasement==true) && ($this->override==1)) {
			// Enregistrer dans la base en remplaçant la version courante (update)
			// NE FAUDRAIT IL PAS SUPPRIMER LE REFERENTIEL AVANT DE LA RECHARGER ?
			$re->instance=$this->rreferentiel->id;    // en realite instance est ici occurrence
			$re->referentiel_id=$this->rreferentiel->id;
			$ok=referentiel_update_referentiel($re);
			$new_referentiel_id=$this->rreferentiel->id;
		}
		else {
			// ni nouvelle instance ni recouvrement
			$this->error( $error_override );
			return false;
		}

    // MODIF JF 2012/03/09
	// importer le protocole
	$pindex=0;
	$nbprotocoles=0;  // compteur
    $re->protocole = array();

	if (!empty($xmlreferentiel['#']['protocole'])) {
        $xmlprotocole = $xmlreferentiel['#']['protocole'];
        foreach ($xmlprotocole as $protocole) {
			// PROTOCOLE
			// print_r($protocole);
			$pindex++;
			$new_protocole = array();
			$new_protocole = $this->defaultprotocole();
			// $new_protocole->id=$this->getpath( $protocole, array('#','p_id',0,'#'), '', false, '');
			// $new_protocole->ref_occurrence=$this->getpath( $protocole, array('#','p_ref_occurrence',0,'#'), '', false, '');
            $new_protocole->seuil_referentiel=$this->getpath( $protocole, array('#','p_seuil_referentiel',0,'#'), '', false, '');
            $new_protocole->minima_referentiel=$this->getpath( $protocole, array('#','p_minima_referentiel',0,'#'), '', false, '');
            // La suite initialise en chargeant les domaines / compétences / items
            // $new_protocole->l_domaines_oblig=$this->getpath( $protocole, array('#','p_domaines_oblig',0,'#','text',0,'#'), '', true, '');
			// $new_protocole->l_seuils_domaines=$this->getpath( $protocole, array('#','p_seuils_domaines',0,'#','text',0,'#'), '', true, '');
            // $new_protocole->l_competences_oblig=$this->getpath( $protocole, array('#','p_competences_oblig',0,'#','text',0,'#'), '', true, '');
            // $new_protocole->l_seuils_competences=$this->getpath( $protocole, array('#','p_seuils_competences',0,'#','text',0,'#'), '', true, '');
            // $new_protocole->l_minimas_competences=$this->getpath( $protocole, array('#','p_minimas_competences',0,'#','text',0,'#'), '', true, '');
            // $new_protocole->l_items_oblig=$this->getpath( $protocole, array('#','p_items_oblig',0,'#','text',0,'#'), '', true, '');
            $new_protocole->timemodified=$this->getpath( $protocole, array('#','p_timemodified',0,'#'), '', false, '');
            $new_protocole->actif=$this->getpath( $protocole, array('#','p_actif',0,'#'), '', false, '');
            $new_protocole->commentaire=$this->getpath( $protocole, array('#','p_commentaire',0,'#','text',0,'#'), '', true, '');
			// enregistrer
			$re->protocoles[$pindex]=$new_protocole;

			// sauvegarder dans la base
			// remplacer l'id du referentiel importe par l'id du referentiel cree
			// trafiquer les donnees pour appeler la fonction ad hoc
			$new_protocole->ref_occurrence=$new_referentiel_id;
			// DEBUG
			// echo "<br />DEBUG ./format/xml/format.php :: 710<br />\n";
			// print_object($new_protocole);

			if (referentiel_add_protocol($new_protocole)){
                $nbprotocoles++;
            }
        }
   }
   else{
        $new_protocole = $this->defaultprotocole();
        $new_protocole->ref_occurrence=$new_referentiel_id;
        $re->protocoles[1]=$new_protocole;
        if (referentiel_add_protocol($new_protocole)){
            $nbprotocoles++;
        }
   }
		// importer les domaines
		$xmldomaines = $xmlreferentiel['#']['domaine'];
		$dindex=0;
        $re->domaines = array();

		$nbdomaines=0;        // compteur
        foreach ($xmldomaines as $domaine) {
			// DOMAINES
			// print_r($domaine);
			$dindex++;
			$new_domaine = $this->defaultdomaine();
			// $new_domaine->id=$this->getpath( $domaine, array('#','id',0,'#'), '', false, '');
			$new_domaine->code_domaine=$this->getpath( $domaine, array('#','code_domaine',0,'#'), '', true, $error_nocode);
			$new_domaine->description_domaine=$this->getpath( $domaine, array('#','description_domaine',0,'#','text',0,'#'), '', true, '');
// MODIF JF 2012/05/22
// retablir des sauts de ligne
            $new_domaine->description_domaine=preg_replace("/\|\|\|/", "\r\n" , $new_domaine->description_domaine);

			$new_domaine->num_domaine=$this->getpath( $domaine, array('#','num_domaine',0,'#'), '', false, '');
			$new_domaine->nb_competences=$this->getpath( $domaine, array('#','nb_competences',0,'#'), '', false, '');
			// $new_domaine->ref_referentiel=$this->getpath( $domaine, array('#','ref_referentiel',0,'#'), '', false, '');
// MODIF 2012/03/08
            $new_domaine->type_domaine=$this->getpath( $domaine, array('#','type_domaine',0,'#'), '', false, '');
            if (empty($new_domaine->type_domaine)){
                $new_domaine->type_domaine=0;
            }
            $new_domaine->seuil_domaine=$this->getpath( $domaine, array('#','seuil_domaine',0,'#'), '', false, '');
            if (empty($new_domaine->seuil_domaine)){
                $new_domaine->seuil_domaine='0.0';
            }
// MODIF 2012/03/26
            $new_domaine->minima_domaine=$this->getpath( $domaine, array('#','minima_domaine',0,'#'), '', false, '');
            if (empty($new_domaine->minima_domaine)){
                $new_domaine->minima_domaine='0';
            }

			// enregistrer
			$re->domaines[$dindex]=$new_domaine;

			// sauvegarder dans la base
			// remplacer l'id du referentiel importe par l'id du referentiel cree
			// trafiquer les donnees pour appeler la fonction ad hoc
			$new_domaine->ref_referentiel=$new_referentiel_id;
			$new_domaine->instance=$new_referentiel_id; // pour que ca marche
			$new_domaine->new_code_domaine=$new_domaine->code_domaine;
			$new_domaine->new_description_domaine=$new_domaine->description_domaine;
			$new_domaine->new_num_domaine=$new_domaine->num_domaine;
			$new_domaine->new_nb_competences=$new_domaine->num_domaine;
// MODIF 2012/03/08
            $new_domaine->new_type_domaine=$new_domaine->type_domaine;
            $new_domaine->new_seuil_domaine=$new_domaine->seuil_domaine;
// MODIF 2012/03/26
            $new_domaine->new_minima_domaine=$new_domaine->minima_domaine;

			$new_domaine_id=referentiel_add_domaine($new_domaine);
			if ($new_domaine_id){
                $nbdomaines++;
            }

			// importer les competences
			$xmlcompetences = $domaine['#']['competence'];

			$cindex=0;
			$re->domaines[$dindex]->competences=array();

			$nbcompetences=0;        // compteur
            foreach ($xmlcompetences as $competence) {
				$cindex++;
				$new_competence = array();
				$new_competence = $this->defaultcompetence();
		    	// $new_competence->id = $this->getpath( $competence, array('#','id',0,'#'), '', false, '');
				$new_competence->code_competence=$this->getpath( $competence, array('#','code_competence',0,'#'), '', true, $error_nocode);
				$new_competence->description_competence=$this->getpath( $competence, array('#','description_competence',0,'#','text',0,'#'), '', true, '');
// MODIF JF 2012/05/22
// retablir des sauts de ligne
                $new_competence->description_competence=preg_replace("/\|\|\|/", "\r\n" , $new_competence->description_competence);

				$new_competence->num_competence=$this->getpath( $competence, array('#','num_competence',0,'#'), '', false, '');
				$new_competence->nb_item_competences=$this->getpath( $competence, array('#','nb_item_competences',0,'#'), '', false, '');
				// $new_competence->ref_domaine=$this->getpath( $competence, array('#','ref_domaine',0,'#'), '', false, '');
// MODIF 2012/03/08
                $new_competence->type_competence=$this->getpath( $competence, array('#','type_competence',0,'#'), '', false, '');
                $new_competence->seuil_competence=$this->getpath( $competence, array('#','seuil_competence',0,'#'), '', false, '');
                if (empty($new_competence->type_competence)){
                    $new_competence->type_competence=0;
                }
                if (empty($new_competence->seuil_competence)){
                    $new_competence->seuil_competence='0.0';
                }
// MODIF 2012/03/26
                $new_competence->minima_competence=$this->getpath( $competence, array('#','minima_competence',0,'#'), '', false, '');
                if (empty($new_competence->minima_competence)){
                    $new_competence->minima_competence=0;
                }

				// enregistrer
				$re->domaines[$dindex]->competences[$cindex]=$new_competence;

				// sauvegarder dans la base
				// remplacer l'id du referentiel importe par l'id du referentiel cree
				$new_competence->ref_domaine=$new_domaine_id;
				// trafiquer les donnees pour appeler la fonction ad hoc
				$new_competence->instance=$new_referentiel_id; // pour que ca marche
				$new_competence->new_code_competence=$new_competence->code_competence;
				$new_competence->new_description_competence=$new_competence->description_competence;
				$new_competence->new_ref_domaine=$new_competence->ref_domaine;
				$new_competence->new_num_competence=$new_competence->num_competence;
				$new_competence->new_nb_item_competences=$new_competence->nb_item_competences;
// MODIF 2012/03/08
                $new_competence->new_type_competence=$new_competence->type_competence;
                $new_competence->new_seuil_competence=$new_competence->seuil_competence;

// MODIF 2012/03/26
                $new_competence->new_minima_competence=$new_competence->minima_competence;

				// creation
				$new_competence_id=referentiel_add_competence($new_competence);
				if ($new_competence_id){
                    $nbcompetences++;        // compteur
                }

				// importer les items
				$xmlitems = $competence['#']['item'];
				$iindex=0;
				$re->domaines[$dindex]->competences[$cindex]->items=array();

                $nbitems=0; // compteur
		        foreach ($xmlitems as $item) {
					$iindex++;
					$new_item = array();
					$new_item = $this->defaultitem();
					// $new_item->id = $this->getpath( $item, array('#','id',0,'#'), '', false, '');
					$new_item->code_item = $this->getpath( $item, array('#','code',0,'#'), '', true, $error_nocode);
					$new_item->description_item=$this->getpath( $item, array('#','description_item',0,'#','text',0,'#'), '', true, '');
// MODIF JF 2012/05/22
// retablir des sauts de ligne
                    $new_item->description_item=preg_replace("/\|\|\|/", "\r\n" , $new_item->description_item);

					$new_item->num_item=$this->getpath( $item, array('#','num_item',0,'#'), '', false, '');
					$new_item->type_item=$this->getpath( $item, array('#','type_item',0,'#'), '', true, '');
					$new_item->poids_item=$this->getpath( $item, array('#','poids_item',0,'#'), '', false, '');
					// $new_item->ref_competence=$this->getpath( $item, array('#','ref_competence',0,'#'), '', false, '');
					// $new_item->ref_referentiel=$this->getpath( $item, array('#','ref_referentiel',0,'#'), '', false, '');
					$new_item->empreinte_item=$this->getpath( $item, array('#','empreinte_item',0,'#'), '', false, '');
					// enregistrer
					$re->domaines[$dindex]->competences[$cindex]->items[$iindex]=$new_item;

					// sauvegarder dans la base

					// remplacer l'id du referentiel importe par l'id du referentiel cree
					$new_item->ref_referentiel=$new_referentiel_id;
					$new_item->ref_competence=$new_competence_id;
					// trafiquer les donnees pour pouvoir appeler la fonction ad hoc
					$new_item->instance=$new_item->ref_referentiel;
					$new_item->new_ref_competence=$new_item->ref_competence;
					$new_item->new_code_item=$new_item->code_item;
					$new_item->new_description_item=$new_item->description_item;
					$new_item->new_num_item=$new_item->num_item;
					$new_item->new_type_item=$new_item->type_item;
					$new_item->new_poids_item=$new_item->poids_item;
					$new_item->new_empreinte_item=$new_item->empreinte_item;
					// creer
					$new_item_id=referentiel_add_item($new_item);
					if ($new_item_id){
                        $nbitems++;
                    }
                    // that's all folks
				} // items
    			if ($nbitems>0){
                    // mettre a jour
                    referentiel_set_competence_nb_item($new_competence_id, $nbitems);
                }
			} // competences
			if ($nbcompetences>0){
                // mettre a jour
                referentiel_set_domaine_nb_competence($new_domaine_id, $nbcompetences);
            }
        }
        // mettre a jour
        if ($nbdomaines>0){
            // mettre a jour
            referentiel_set_referentiel_nb_domaine($new_referentiel_id, $nbdomaines);
        }
        return $re;
    }



    /**
     * parse the array of lines into an array of questions
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array (of objects) question objects
     */
    function read_import_referentiel($lines) {
        // we just need it as one big string
        $text = implode($lines, " ");
        unset( $lines );

        // this converts xml to big nasty data structure
        // the 0 means keep white space as it is (important for markdown format)
        // print_r it if you want to see what it looks like!
        $xml = xmlize( $text, 0 );

		// DEBUG
		// echo "<br />DEBUG xml/format.php :: ligne 580<br />\n";
		// print_r($xml);
		// echo "<br /><br />\n";
		// print_r($xml['referentiel']['domaine']['competence']);
		// print_r($xml['referentiel']['#']['domaine']['#']);
		// echo "<br /><br />\n";
		// exit;
		$re=$this->import_referentiel($xml['referentiel']);
        // stick the result in the $treferentiel array
 		// DEBUG
		// echo "<br />DEBUG xml/format.php :: ligne 632\n";
		// print_r($re);
        return $re;
    }
}


 
/**********************************************************************
***********************************************************************
									ACTIVITES
***********************************************************************
**********************************************************************/

class aformat_xml extends aformat_default {

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
		$raw=preg_replace("/\n/", "||| ", $raw);

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


    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<certification>\n" .
                       $content . "\n" .
                       "</certification>";

        // make the xml look nice
        $content = $this->xmltidy( $content );

        return $content;
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
     * Turns document into an xml segment
     * @param document object
     * @return string xml segment
     */

    function write_document( $document ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment		
        $expout .= "\n\n<!-- document: $document->id  -->\n";
		//
		if ($document){
			$id = $this->writeraw( $document->id );		
            $type_document = $this->writeraw( trim($document->type_document));
            $description_document = $this->writetext(trim($document->description_document));
			$url_document = $this->writeraw( $document->url_document);
            $ref_activite = $this->writeraw( $document->ref_activite);
            $timestamp = $this->writeraw( $document->timestamp );
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
        $expout .= "\n\n<!-- activite: $activite->id  -->\n";
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
					$expout .= $this->write_document( $record_d );
				}
			}
			
			$expout .= "</activite>\n";
        }
        return $expout;
    }


    function write_liste_activites() {
    	global $CFG;
        // initial string;
        $expout = "";
        // add comment		
        $expout .= "\n\n<!-- instance : ".$this->ireferentiel->id."  -->\n";
		// 
		if ($this->ireferentiel){
			// DEBUG
			// echo "<br />DEBUG LIGNE 1021<br />\n";
			// print_r($this->ireferentiel);
			
			$id = $this->writeraw( $this->ireferentiel->id );
            $name = $this->writeraw( trim($this->ireferentiel->name) );
            $description_instance = $this->writetext(trim($this->ireferentiel->description_instance));
            $label_domaine = $this->writeraw( trim($this->ireferentiel->label_domaine) );
            $label_competence = $this->writeraw( trim($this->ireferentiel->label_competence) );
            $label_item = $this->writeraw( trim($this->ireferentiel->label_item) );
            $date_instance = $this->writeraw( $this->ireferentiel->date_instance);
            $course = $this->writeraw( $this->ireferentiel->course);
            $ref_referentiel = $this->writeraw( $this->ireferentiel->ref_referentiel);
			$visible = $this->writeraw( $this->ireferentiel->visible );
			
			$expout .= "<id>$id</id>\n";
			$expout .= "<name>$name</name>\n";   
            $expout .= "<description_instance>\n$description_instance</description_instance>\n";
            $expout .= "<label_domaine>$label_domaine</label_domaine>\n";
            $expout .= "<label_competence>$label_competence</label_competence>\n";
            $expout .= "<label_item>$label_item</label_item>\n";
            $expout .= "<date_instance>$date_instance</date_instance>\n";
            $expout .= "<course>$course</course>\n";
            $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            $expout .= "<visible>$visible</visible>\n";
			
			// ACTIVITES
			if (isset($this->ireferentiel->id) && ($this->ireferentiel->id>0)){
				$records_activites = referentiel_get_activites_instance($this->ireferentiel->id);
				// print_r($records_activites);
				
		    	if ($records_activites){
					foreach ($records_activites as $record_a){
						$expout .= $this->write_activite( $record_a );
					}
				}
			}
        }
        return $expout;
    }
}


class cformat_xml extends cformat_default {

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
		$raw=preg_replace("/\n/", "|||", $raw);

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


    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<certification>\n" .
                       $content . "\n" .
                       "</certification>";

        // make the xml look nice
        $content = $this->xmltidy( $content );

        return $content;
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
			$expout .= "<id>$id</id>\n";
            $expout .= "<num_etablissement>$num_etablissement</$num_etablissement>\n";
            $expout .= "<nom_etablissement>$nom_etablissement</nom_etablissement>\n";			
            $expout .= "<adresse_etablissement>\n$adresse_etablissement</adresse_etablissement>\n";
            $expout .= "<logo_etablissement>$logo</logo_etablissement>\n";			
			$expout .= "</etablissement>\n\n";
        }
        return $expout;
    }


	
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
            if (!$this->format_condense){
                $expout .= "<etudiant>\n";
	       		$expout .= "<id>$id</id>\n";
		      	$expout .= "<userid>$userid</userid>\n";
		      	$expout .= "<login>$login</login>\n";
    			$expout .= "<num_etudiant>$num_etudiant</num_etudiant>\n";
    		    $expout .= "<lastname>".referentiel_get_user_nom($record->userid)."</lastname>\n";
                $expout .= "<firstname>".referentiel_get_user_prenom($record->userid)."</firstname>\n";
                $expout .= "<ddn_etudiant>$ddn_etudiant</ddn_etudiant>\n";
                $expout .= "<lieu_naissance>$lieu_naissance</lieu_naissance>\n";
                $expout .= "<departement_naissance>$departement_naissance</departement_naissance>\n";
                $expout .= "<adresse_etudiant>$adresse_etudiant</adresse_etudiant>\n";
		      	$expout .= "<ref_etablissement>$ref_etablissement</ref_etablissement>\n";
    			// Etablissement
	       		$record_etablissement=referentiel_get_etablissement($record->ref_etablissement);
	           	if ($record_etablissement){
			     	$expout .= $this->write_etablissement( $record_etablissement );
			    }
			    $expout .= "</etudiant>\n\n";
            }
            elseif ($this->format_condense==1){
                $expout .= "<userid>$userid</userid>\n";
		      	$expout .= "<login>$login</login>\n";
    			$expout .= "<num_etudiant>$num_etudiant</num_etudiant>\n";
    		    $expout .= "<lastname>".referentiel_get_user_nom($record->userid)."</lastname>\n";
                $expout .= "<firstname>".referentiel_get_user_prenom($record->userid)."</firstname>\n";
            }
            elseif ($this->format_condense==2){
		      	$expout .= "<login>$login</login>\n";
    			$expout .= "<num_etudiant>$num_etudiant</num_etudiant>\n";
    		    $expout .= "<lastname>".referentiel_get_user_nom($record->userid)."</lastname>\n";
                $expout .= "<firstname>".referentiel_get_user_prenom($record->userid)."</firstname>\n";
            }
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
                    $expout .= "<certificat>\n";
                    $expout .= $this->write_etudiant( $record_etudiant );

                    if (!$this->format_condense){
                        // la totale
                        $expout .= "<commentaire_certificat>\n$commentaire_certificat</commentaire_certificat>\n";
                        $expout .= "<competences_certificat>$competences_certificat</competences_certificat>\n";
                        $expout .= "<decision_jury>$decision_jury</decision_jury>\n";
                        $expout .= "<date_decision>$date_decision</date_decision>\n";
                        $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
                        $expout .= "<verrou>$verrou</verrou>\n";
                        $expout .= "<valide>$valide</valide>\n";
        			    $expout .= "<evaluation>$evaluation</evaluation>\n";
                        $expout .= "<synthese>\n$synthese_certificat</synthese>\n";
                    }
                    else if($this->format_condense==1){
                        // la partielle
                        $expout .= "<decision_jury>$decision_jury</decision_jury>\n";
                        $expout .= $this->certificat_pourcentage($competences_certificat, $this->ref_referentiel);
                    }
                    else if($this->format_condense==2){
                        $expout .= "<decision_jury>$decision_jury</decision_jury>\n";
                        $expout .= $this->certificat_items_binaire($competences_certificat, $this->ref_referentiel);
                    }
                    
                    // PEDAGOGIES
                    if ($this->export_pedagos){
                        // $expout .= ";promotion;formation;pedagogie;composante;num_groupe;commentaire;date_cloture";
                        $rec_pedago=referentiel_get_pedagogie_user($userid, $ref_referentiel);
                        if ($rec_pedago){
                            $expout .="<promotion>".$this->writeraw( trim($rec_pedago->promotion))."</promotion>\n";
                            $expout .="<formation>".$this->writeraw( trim($rec_pedago->formation))."</formation>\n";
                            $expout .="<pedagogie>".$this->writeraw( trim($rec_pedago->pedagogie))."</pedagogie>\n";
                            $expout .="<composante>".$this->writeraw( trim($rec_pedago->composante))."</composante>\n";
                            $expout .="<num_groupe>".$this->writeraw( trim($rec_pedago->num_groupe))."</num_groupe>\n";
                            $expout .="<commentaire>\n".$this->writetext(trim($rec_pedago->commentaire))."</commentaire>\n";
                            $expout .="<date_cloture>".$this->writeraw( trim($rec_pedago->date_cloture))."</date_cloture>\n";
                        }
                    }

                    $expout .= "</certificat>\n\n";
                }
            }
        }
		// DEBUG
		// echo "<br />DEBUG LIGNE 1330<br />\n";
		// echo htmlentities ($expout, ENT_QUOTES, 'UTF-8')  ;
		
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
        //$expout .= "\n\n<!-- item: $item->id  -->\n";
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
			$num_item = $this->writeraw( $item->num_item);
            $expout .= "<item>\n";
			$expout .= "<id>$id</id>\n";
			$expout .= "<code>$code</code>\n";
            $expout .= "<description_item>\n$description_item</description_item>\n";
            $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            $expout .= "<ref_competence>$ref_competence</ref_competence>\n";
            $expout .= "<type_item>$type_item</type_item>\n";
            $expout .= "<poids_item>$poids_item</poids_item>\n";
            $expout .= "<num_item>$num_item</num_item>\n";
			$expout .= "</item>\n";
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
        //$expout .= "\n\n<!-- competence: $competence->id  -->\n";
		//
		if ($competence){
			$id = $this->writeraw( $competence->id );		
            $code = $this->writeraw( trim($competence->code_competence));
            $description_competence = $this->writetext(trim($competence->description_competence));
            $ref_domaine = $this->writeraw( $competence->ref_domaine);
			$num_competence = $this->writeraw( $competence->num_competence);
			$nb_item_competences = $this->writeraw( $competence->nb_item_competences);
            $expout .= "<competence>\n";
			$expout .= "<id>$id</id>\n";
			$expout .= "<code_competence>$code</code_competence>\n";   
            $expout .= "<description_competence>\n$description_competence</description_competence>\n";
            $expout .= "<ref_domaine>$ref_domaine</ref_domaine>\n";
            $expout .= "<num_competence>$num_competence</num_competence>\n";
            $expout .= "<nb_item_competences>$nb_item_competences</nb_item_competences>\n";
							
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
			$expout .= "</competence>\n";   
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
        //$expout .= "\n\n<!-- domaine: $domaine->id  -->\n";
		// 
		if ($domaine){
			$id = $this->writeraw( $domaine->id );
            $code = $this->writeraw( trim($domaine->code_domaine) );
            $description_domaine = $this->writetext(trim($domaine->description_domaine));
            $ref_referentiel = $this->writeraw( $domaine->ref_referentiel );
			$num_domaine = $this->writeraw( $domaine->num_domaine );
			$nb_competences = $this->writeraw( $domaine->nb_competences );
            $expout .= "<domaine>\n";
			$expout .= "<id>$id</id>\n";
			$expout .= "<code_domaine>$code</code_domaine>\n";   
            $expout .= "<description_domaine>\n$description_domaine</description_domaine>\n";
            $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            $expout .= "<num_domaine>$num_domaine</num_domaine>\n";
            $expout .= "<nb_competences>$nb_competences</nb_competences>\n";
			
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
			$expout .= " </domaine>\n";   
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
        // $expout .= "\n\n<!-- referentiel: $referentiel->id  -->\n";
		// 
		if ($this->rreferentiel){
			$id = $this->writeraw( $this->rreferentiel->id );
            $name = $this->writeraw( trim($this->rreferentiel->name) );
            $code_referentiel = $this->writeraw( trim($this->rreferentiel->code_referentiel));
            $description_referentiel = $this->writetext(trim($this->rreferentiel->description_referentiel));
            $url_referentiel = $this->writeraw( trim($this->rreferentiel->url_referentiel) );
			$seuil_certificat = $this->writeraw( $this->rreferentiel->seuil_certificat );
			$timemodified = $this->writeraw( $this->rreferentiel->timemodified );			
			$nb_domaines = $this->writeraw( $this->rreferentiel->nb_domaines );
			$liste_codes_competence = $this->writeraw( trim($this->rreferentiel->liste_codes_competence) );
			$local = $this->writeraw( $this->rreferentiel->local );
			
            if ($this->format_condense==1){
                $expout .= "<name>$name</name>\n";
    			$expout .= "<code_referentiel>$code_referentiel</code_referentiel>\n";
                $expout .= "<description_referentiel>\n$description_referentiel</description_referentiel>\n";
            }
            else if ($this->format_condense==2){
                $expout .= "<name>$name</name>\n";
    			$expout .= "<code_referentiel>$code_referentiel</code_referentiel>\n";
                $expout .= "<description_referentiel>\n$description_referentiel</description_referentiel>\n";
            }
            else{
                $expout .= "<id>$id</id>\n";
                $expout .= "<name>$name</name>\n";
    			$expout .= "<code_referentiel>$code_referentiel</code_referentiel>\n";
                $expout .= "<description_referentiel>\n$description_referentiel</description_referentiel>\n";
                $expout .= "<url_referentiel>$url_referentiel</url_referentiel>\n";
                $expout .= "<seuil_certificat>$seuil_certificat</seuil_certificat>\n";
                $expout .= "<timemodified>$timemodified</timemodified>\n";
                $expout .= "<nb_domaines>$nb_domaines</nb_domaines>\n";
                $expout .= "<liste_codes_competence>$liste_codes_competence</liste_codes_competence>\n";
    			$expout .= "<local>$local</local>\n";
			
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
        // initial string;
        $expout = "";
        // add comment		
        // $expout .= "\n\n<!-- instance : ".$this->ireferentiel->id."  -->\n";
		// 

		if ($this->ireferentiel){  // instance courante
			// DEBUG
			// echo "<br />DEBUG LIGNE 1348<br />\n";
			// print_r($this->ireferentiel);
			
			$id = $this->writeraw( $this->ireferentiel->id );
            $name = $this->writeraw( trim($this->ireferentiel->name) );
            $description_instance = $this->writetext(trim($this->ireferentiel->description_instance));
            $label_domaine = $this->writeraw( trim($this->ireferentiel->label_domaine) );
            $label_competence = $this->writeraw( trim($this->ireferentiel->label_competence) );
            $label_item = $this->writeraw( trim($this->ireferentiel->label_item) );
            $date_instance = $this->writeraw( userdate($this->ireferentiel->date_instance));
            $course = $this->writeraw( $this->ireferentiel->course);
            $ref_referentiel = $this->writeraw( $this->ireferentiel->ref_referentiel);
			$visible = $this->writeraw( $this->ireferentiel->visible );

			if (!$this->format_condense){
                $expout .= "<instance>\n";
                $expout .= "<id>$id</id>\n";
                $expout .= "<name>$name</name>\n";
                $expout .= "<description_instance>\n$description_instance</description_instance>\n";
                $expout .= "<label_domaine>$label_domaine</label_domaine>\n";
                $expout .= "<label_competence>$label_competence</label_competence>\n";
                $expout .= "<label_item>$label_item</label_item>\n";
                $expout .= "<date_instance>$date_instance</date_instance>\n";
                $expout .= "<course>$course</course>\n";
                $expout .= "<visible>$visible</visible>\n";
			    // referentiel
                $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
                $expout .= "</instance>\n";
			}
        }

        // CERTIFICATS
		if (!empty($this->rreferentiel)){    // referentiel_referentiel
				
				//$record_referentiel = referentiel_get_referentiel_referentiel($this->ireferentiel->ref_referentiel);
				$expout .= $this->write_referentiel();

				if (empty($this->records_certificats)){
    				$this->records_certificats = referentiel_get_certificats($this->ireferentiel->ref_referentiel);
                }
    			// echo "<br />DEBUG LIGNE 1377<br />\n";
				// print_r($records_certificats);
				// exit;
		    	if ($this->records_certificats){
                    $expout .= "<certificats>\n";
					foreach ($this->records_certificats as $record){
						$expout .= $this->write_certificat( $record );
					}
					$expout .= "</certificats>\n\n";
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
            $s.="<items>\n";
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
            $s.="<domaines>\n";
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

    
}   // fin classe cformat


// ****************************************************************
// ETUDIANT

class eformat_xml extends eformat_default {

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
		$raw=preg_replace("/\n/", "|||", $raw);

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


    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<etudiants>\n" .
                       $content . "\n" .
                       "</etudiants>";

        // make the xml look nice
        $content = $this->xmltidy( $content );

        return $content;
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

	function write_etablissement( $record ) {
        // initial string;
        $expout = "";
		if ($record){
			$id = $this->writeraw( $record->id );
			$num_etablissement = $this->writeraw( $record->num_etablissement);
			$nom_etablissement = $this->writeraw( $record->nom_etablissement);
			$adresse_etablissement = $this->writetext( $record->adresse_etablissement);
			// $logo = $this->writeraw( $record->logo_etablissement);
            $expout .= "<etablissement>\n";
			$expout .= "<id>$id</id>\n";
            $expout .= "<num_etablissement>$num_etablissement</$num_etablissement>\n";
            $expout .= "<nom_etablissement>$nom_etablissement</nom_etablissement>\n";			
            $expout .= "<adresse_etablissement>\n$adresse_etablissement</adresse_etablissement>\n";
            // $expout .= "<logo_etablissement>$logo</logo_etablissement>\n";			
			$expout .= "</etablissement>\n";
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
			$id = $this->writeraw( $record->id );
			$userid = $this->writeraw( $record->userid );
            $ref_etablissement = $this->writeraw( $record->ref_etablissement);
			$num_etudiant = $this->writeraw( $record->num_etudiant);
			$ddn_etudiant = $this->writeraw( $record->ddn_etudiant);
			$lieu_naissance = $this->writeraw( $record->lieu_naissance);
			$departement_naissance = $this->writeraw( $record->departement_naissance);
			$adresse_etudiant = $this->writeraw( $record->adresse_etudiant);
            $expout .= "<etudiant>\n";
			$expout .= "<id>$id</id>\n";
			$expout .= "<userid>$userid</userid>\n";
			$expout .= "<login>".referentiel_get_user_login($userid)."</login>\n";
			$expout .= "<lastname_firstname>".referentiel_get_user_info($record->userid)."</lastname_firstname>\n";
			$expout .= "<num_etudiant>$num_etudiant</num_etudiant>\n";
            $expout .= "<ddn_etudiant>$ddn_etudiant</ddn_etudiant>\n";
            $expout .= "<lieu_naissance>$lieu_naissance</lieu_naissance>\n";
            $expout .= "<departement_naissance>$departement_naissance</departement_naissance>\n";			
            $expout .= "<adresse_etudiant>$adresse_etudiant</adresse_etudiant>\n";
			$expout .= "<ref_etablissement>$ref_etablissement</ref_etablissement>\n";
			
	/*
			// Etablissement
			$record_etablissement=referentiel_get_etablissement($record->ref_etablissement);
	    	if ($record_etablissement){
				$expout .= $this->write_etablissement( $record_etablissement );
			}
	*/
			$expout .= "</etudiant>\n";
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
		if ($this->ireferentiel){
			// ETUDIANTS
			if (isset($this->ireferentiel->course) && ($this->ireferentiel->course>0)){
				// ETUDIANTS
				$records_all_students = referentiel_get_students_course($this->ireferentiel->course);
				if ($records_all_students){
					foreach ($records_all_students as $record){
						// USER
						if (isset($record->userid) && ($record->userid>0)){
							$record_etudiant = referentiel_get_etudiant_user($record->userid);
		    				if ($record_etudiant){
								$expout .= $this->write_etudiant($record_etudiant);
							}
						}
					}
				}
			}
        }
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

class tformat_xml extends tformat_default {

    function provide_import() {
        return true;
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
		$raw=preg_replace("/\n/", "|||", $raw);

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


    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<referentiel>\n" .
                       $content . "\n" .
                       "</referentiel>";

        // make the xml look nice
        $content = $this->xmltidy( $content );

        return $content;
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
     * Turns consigne into an xml segment
     * @param consigne object
     * @return string xml segment
     */

    function write_consigne( $consigne ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment		
        $expout .= "\n\n<!-- consigne: $consigne->id  -->\n";
		//
		if ($consigne){
			$id = $this->writeraw( $consigne->id );		
            $type_consigne = $this->writeraw( trim($consigne->type_consigne));
            $description_consigne = $this->writetext(trim($consigne->description_consigne));
			$url_consigne = $this->writeraw( $consigne->url_consigne);
            $ref_task = $this->writeraw( $consigne->ref_task);
            $timestamp = $this->writeraw( $consigne->timestamp );
            $expout .= "<timestamp>$timestamp</timestamp>\n";
            $expout .= "<consigne>\n";
			$expout .= "<id>$id</id>\n";
			$expout .= "<type_consigne>$type_consigne</type_consigne>\n";   
            $expout .= "<description_consigne>\n$description_consigne</description_consigne>\n";
            if (!preg_match("/http/", $url_consigne)){  // completer l'adresse relative
                // Moodle 1.9 :: $url_consigne=$CFG->wwwroot.'/file.php/'.$url_consigne;
                // Moodle 2.x
                $url_consigne=$CFG->wwwroot.'/pluginfile.php'.$url_consigne;
            }
            $expout .= "<url_consigne>$url_consigne</url_consigne>\n";
            $expout .= "<ref_task>$ref_task</ref_task>\n";
            $expout .= "<timestamp>$timestamp</timestamp>\n";
			$expout .= "</consigne>\n";   
        }
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
        $expout .= "\n\n<!-- task: $task->id  -->\n";
		// 
		if ($task){
			// DEBUG
			// echo "<br />DEBUG LIGNE 960<br />\n";
			// print_r($task);
			
			$id = $this->writeraw( $task->id );
            $type_task = $this->writeraw( trim($task->type_task));
            $description_task = $this->writetext(trim($task->description_task));
            $competences_task = $this->writeraw(trim($task->competences_task));
            $criteres_evaluation = $this->writetext(trim($task->criteres_evaluation));
            $ref_instance = $this->writeraw( $task->ref_instance);
            $ref_referentiel = $this->writeraw( $task->ref_referentiel);
            $ref_course = $this->writeraw( $task->ref_course);
			$auteurid = $this->writeraw( trim($task->auteurid));
			$date_creation = $this->writeraw( $task->date_creation);
			$date_modif = $this->writeraw( $task->date_modif);
			$date_debut = $this->writeraw( $task->date_debut);
			$date_fin = $this->writeraw( $task->date_fin);
			
            $expout .= "<task>\n";
			$expout .= "<id>$id</id>\n";
			$expout .= "<type_task>$type_task</type_task>\n";
            $expout .= "<description_task>\n$description_task</description_task>\n";
            $expout .= "<competences_task>$competences_task</competences_task>\n";
            $expout .= "<criteres_evaluation>\n$criteres_evaluation</criteres_evaluation>\n";
            $expout .= "<ref_instance>$ref_instance</ref_instance>\n";
            $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            $expout .= "<ref_course>$ref_course</ref_course>\n";
            $expout .= "<auteurid>$auteurid</auteurid>\n";
            $expout .= "<date_creation>$date_creation</date_creation>\n";
            $expout .= "<date_modif>$date_modif</date_modif>\n";
            $expout .= "<date_debut>$date_debut</date_debut>\n";
			$expout .= "<date_fin>$date_fin</date_fin>\n";

			// consigneS
			$records_consignes = referentiel_get_consignes($task->id);
			
			if ($records_consignes){
				foreach ($records_consignes as $record_d){
					$expout .= $this->write_consigne( $record_d );
				}
			}
			
			$expout .= "</task>\n";
        }
        return $expout;
    }

	 /**
     * Turns referentiel into an xml segment
     * @param competence object
     * @return string xml segment
     */

    function write_referentiel_reduit() {
    	global $CFG;
		global $USER;
        // initial string;
        $expout = "";
	    $id = $this->rreferentiel->id;
		
		if ($this->rreferentiel){
		/*
CREATE TABLE mdl_referentiel_referentiel (
  id bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '',
  code_referentiel varchar(20) NOT NULL DEFAULT '',
  mail_auteur_referentiel varchar(255) NOT NULL DEFAULT '',
  cle_referentiel varchar(255) NOT NULL DEFAULT '',
  pass_referentiel varchar(255) NOT NULL DEFAULT '',
  description_referentiel text NOT NULL,
  url_referentiel varchar(255) NOT NULL DEFAULT '',
  seuil_certificat double NOT NULL DEFAULT '0',
  timemodified bigint(10) unsigned NOT NULL DEFAULT '0',
  nb_domaines tinyint(2) unsigned NOT NULL DEFAULT '0',
  liste_codes_competence text NOT NULL,
  liste_empreintes_competence text NOT NULL,
  local bigint(10) unsigned NOT NULL DEFAULT '0',
  logo_referentiel varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Referentiel de competence';
		
		*/
            $name = $this->writeraw( trim($this->rreferentiel->name));
            $code_referentiel = $this->writeraw( trim($this->rreferentiel->code_referentiel));
			$mail_auteur_referentiel = $this->writeraw( trim($this->rreferentiel->mail_auteur_referentiel));
			$cle_referentiel = $this->writeraw( trim($this->rreferentiel->cle_referentiel));
			$pass_referentiel = $this->writeraw( trim($this->rreferentiel->pass_referentiel));
            $description_referentiel = $this->writetext(trim($this->rreferentiel->description_referentiel));
            $url_referentiel = $this->writeraw( trim($this->rreferentiel->url_referentiel));
			$seuil_certificat = $this->writeraw( trim($this->rreferentiel->seuil_certificat));
			$timemodified = $this->writeraw( trim($this->rreferentiel->timemodified));			
			$nb_domaines = $this->writeraw( trim($this->rreferentiel->nb_domaines));
			$liste_codes_competence = $this->writeraw( trim($this->rreferentiel->liste_codes_competence));
			$liste_empreintes_competence = $this->writeraw( trim($this->rreferentiel->liste_empreintes_competence));
			$local = $this->writeraw( trim($this->rreferentiel->local));
			$logo_referentiel = $this->writeraw( trim($this->rreferentiel->logo_referentiel));
			
			// $expout .= "<id>$id</id>\n";
			$expout .= " <name>$name</name>\n";   
			$expout .= " <code_referentiel>$code_referentiel</code_referentiel>\n";   
            $expout .= " <description_referentiel>\n$description_referentiel</description_referentiel>\n";
            $expout .= " <cle_referentiel>$cle_referentiel</cle_referentiel>\n";			
            // $expout .= " <url_referentiel>$url_referentiel</url_referentiel>\n";
            // $expout .= " <seuil_certificat>$seuil_certificat</seuil_certificat>\n";
            // $expout .= " <timemodified>$timemodified</timemodified>\n";			
            // $expout .= " <nb_domaines>$nb_domaines</nb_domaines>\n";
            $expout .= " <liste_codes_competence>$liste_codes_competence</liste_codes_competence>\n";
            // $expout .= " <liste_empreintes_competence>$liste_empreintes_competence</liste_empreintes_competence>\n";
			// $expout .= " <local>$local</local>\n";
			// PAS DE LOGO ICI
			// $expout .= " <logo_referentiel>$logo_referentiel</logo_referentiel>\n";
		}
        return $expout;
    }
	

    function write_liste_tasks() {
    	global $CFG;
        // initial string;
        $expout = "";
		if ($this->rreferentiel){
			$expout .= $this->write_referentiel_reduit();
		}

		if ($this->ireferentiel){
			// DEBUG
			// echo "<br />DEBUG LIGNE 1021<br />\n";
			// print_r($this->ireferentiel);
			
			$id = $this->writeraw( $this->ireferentiel->id );
            $name = $this->writeraw( trim($this->ireferentiel->name) );
            $description_instance = $this->writetext(trim($this->ireferentiel->description_instance));
            $label_domaine = $this->writeraw( trim($this->ireferentiel->label_domaine) );
            $label_competence = $this->writeraw( trim($this->ireferentiel->label_competence) );
            $label_item = $this->writeraw( trim($this->ireferentiel->label_item) );
            $date_instance = $this->writeraw( $this->ireferentiel->date_instance);
            $course = $this->writeraw( $this->ireferentiel->course);
            $ref_referentiel = $this->writeraw( $this->ireferentiel->ref_referentiel);
			$visible = $this->writeraw( $this->ireferentiel->visible );
			
			/*
	        // INUTILE ICI
    	    $expout .= "<instance>\n";
			$expout .= "<id>$id</id>\n";
			$expout .= "<name>$name</name>\n";   
            $expout .= "<description_instance>$description_instance</description_instance>\n";
            $expout .= "<label_domaine>$label_domaine</label_domaine>\n";
            $expout .= "<label_competence>$label_competence</label_competence>\n";
            $expout .= "<label_item>$label_item</label_item>\n";
            $expout .= "<date_instance>$date_instance</date_instance>\n";
            $expout .= "<course>$course</course>\n";
            $expout .= "<ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            $expout .= "<visible>$visible</visible>\n";
			$expout .= "</instance>\n";
			*/
			
			// tasks
			if (isset($this->ireferentiel->id) && ($this->ireferentiel->id>0)){
				$records_tasks = referentiel_get_tasks_instance($this->ireferentiel->id);
				// print_r($records_tasks);
		    	if ($records_tasks){
					foreach ($records_tasks as $record_a){
						$expout .= $this->write_task( $record_a );
					}
				}
			}
        }
        return $expout;
    }
	
	
/***********************
 * IMPORTING FUNCTIONS
 ***********************/



    /** 
     * Translate human readable format name
     * into internal Moodle code number
     * @param string name format name from xml file
     * @return int Moodle format code
     */
    function trans_format( $name ) {
        $name = trim($name); 
 
        if ($name=='moodle_auto_format') {
            $id = 0;
        }
        elseif ($name=='html') {
            $id = 1;
        }
        elseif ($name=='plain_text') {
            $id = 2;
        }
        elseif ($name=='wiki_like') {
            $id = 3;
        }
        elseif ($name=='markdown') {
            $id = 4;
        }
        else {
            $id = 0; // or maybe warning required
        }
        return $id;
    }

    /**
     * Translate human readable single answer option
     * to internal code number
     * @param string name true/false
     * @return int internal code number
     */
    function trans_single( $name ) {
        $name = trim($name);
        if ($name == "false" || !$name) {
            return 0;
        }
        else {
            return 1;
        }
    }

    /**
     * process text string from xml file
     * @param array $text bit of xml tree after ['text']
     * @return string processed text
     */
    function import_text( $text ) {
        // quick sanity check
        if (empty($text)) {
            return '';
        }
        $data = $text[0]['#'];
        return addslashes(trim( $data ));
    }

    /**
     * return the value of a node, given a path to the node
     * if it doesn't exist return the default value
     * @param array xml data to read
     * @param array path path to node expressed as array 
     * @param mixed default 
     * @param bool istext process as text
     * @param string error if set value must exist, return false and issue message if not
     * @return mixed value
     */
    function getpath( $xml, $path, $default, $istext=false, $error='' ) {
        foreach ($path as $index) {
			// echo " $index ";
            if (!isset($xml[$index])) {
                if (!empty($error)) {
                    $this->error( $error );
                    return false;
                }
                else {
					// echo " erreur ";
                    return $default;
                }
            }
            else {
				$xml = $xml[$index];
				// echo " $xml ";
			}
        }
        if ($istext) {
            $xml = addslashes( trim( $xml ) );
        }
		
        return $xml;
    }


    /**
     * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees
     */
    function import_tasks( $xmlreferentiel ) {
	// recupere le fichier xml
	// selon les parametres soit cree une nouvelle instance
	// soit modifie une instance courante de referentiel
	global $SESSION;
	global $USER;
	global $CFG;
	global $DB;
	
		// print_r($xmlreferentiel);
		if (!(isset($this->course->id) && ($this->course->id>0))
			||
			!(isset($this->rreferentiel->id) && ($this->rreferentiel->id>0))
			||
			!(isset($this->coursemodule->id) && ($this->coursemodule->id>0))
			){
			$this->error( get_string( 'incompletedata', 'referentiel' ) );
			return false;
		}


        // get some error strings
        $error_type='';
        $error_noname = get_string( 'xmlimportnoname', 'referentiel' );
        $error_nocode = get_string( 'xmlimportnocode', 'referentiel' );
		$error_override = get_string( 'overriderisk', 'referentiel' );
		$error_incompatible = get_string( 'incompatible_task', 'referentiel' );
		$error_cle_incompatible = get_string( 'incompatible_cle', 'referentiel' );
		$error_code_incompatible = get_string( 'incompatible_code', 'referentiel' );
		$error_competence_incompatible = get_string( 'incompatible_competences', 'referentiel' );

        $aide_type='';
		$aide_cle_incompatible = get_string( 'aide_incompatible_cle', 'referentiel' );
		$aide_code_incompatible = get_string( 'aide_incompatible_code', 'referentiel' );
		$aide_competence_incompatible = get_string( 'aide_incompatible_competences', 'referentiel' );

        // this routine initialises the import object
        $re = $this->defaultreferentiel_reduit();

        // Une partie des données seulement sont utiles
		// $re->id = $this->getpath( $xmlreferentiel, array('#','id',0,'#'), '', false, '');
        $re->name = $this->getpath( $xmlreferentiel, array('#','name','0','#'), '', true, $error_noname);
        $re->code_referentiel = $this->getpath( $xmlreferentiel, array('#','code_referentiel',0,'#'), '', true, $error_nocode);
        $re->description_referentiel = $this->getpath( $xmlreferentiel, array('#','description_referentiel',0,'#','text',0,'#'), '', true, '');
		$re->cle_referentiel = $this->getpath( $xmlreferentiel, array('#','cle_referentiel',0,'#'), '', true, '');
        /*
		$re->url_referentiel = $this->getpath( $xmlreferentiel, array('#','url_referentiel',0,'#'), '', true, '');
		$re->seuil_certificat = $this->getpath( $xmlreferentiel, array('#','seuil_certificat',0,'#'), '', false, '');
		$re->timemodified = $this->getpath( $xmlreferentiel, array('#','timemodified',0,'#'), '', false, '');
		$re->nb_domaines = $this->getpath( $xmlreferentiel, array('#','nb_domaines',0,'#'), '', false, '');
		*/
		$re->liste_codes_competence = $this->getpath( $xmlreferentiel, array('#','liste_codes_competence',0,'#'), '', true, '');
		/*
		$re->liste_empreintes_competence = $this->getpath( $xmlreferentiel, array('#','liste_empreintes_competence',0,'#'), '', true, '');
		/*
		$re->logo_referentiel = $this->getpath( $xmlreferentiel, array('#','logo_referentiel',0,'#'), '', true, '');
		// $re->local = $this->getpath( $xmlreferentiel, array('#','course',0,'#'), '', false, '');

		/*
		// traitement d'une image associee
		// non implante
        $image = $this->getpath( $xmlreferentiel, array('#','image',0,'#'), $re->image );
        $image_base64 = $this->getpath( $xmlreferentiel, array('#','image_base64','0','#'),'' );
        if (!empty($image_base64)) {
            $re->image = $this->importimagefile( $image, stripslashes($image_base64) );
        }
		*/

		$re->export_process = false;
		$re->import_process = true;

		$referentiel_reconnu=false;

        // DEBUG
		//echo "<br />DEBUG :: format/xml/format.php :: LIGNE 2791 :: Referentiel chargé<br />\n";
        //print_r($re);
		//echo "<br />DEBUG :: format/xml/format.php :: LIGNE 2793:: REFERENTIEL EN COURS<br />\n";
        //print_r($this->rreferentiel);

        // exit;
		// importer dans le cours courant et l'instance courante
		// Verifier si referentiel referentiel local identique a referentiel referentiel importe
		if (!empty($re->cle_referentiel)){
			if (!empty($this->rreferentiel->cle_referentiel)
                && ($this->rreferentiel->cle_referentiel==$re->cle_referentiel)){
				$referentiel_reconnu=true;
			}
			else{
                $error_type=$error_cle_incompatible;
                $aide_type=$aide_cle_incompatible;
            }
		}
        // DEBUG
        //if ($referentiel_reconnu){
        //    echo "<br />Referentiel reconnu\n";
        //}
        //else{
        //    echo "<br />Referentiel NON reconnu par la clé...\n";
        //}
        //exit;

        if (($referentiel_reconnu==false) && !empty($re->code_referentiel)
            && (mb_strtoupper($this->rreferentiel->code_referentiel,'UTF-8')==mb_strtoupper($re->code_referentiel,'UTF-8'))){
            // verifier la liste des items
            // DEBUG
            //echo "<br />Referentiel reconnu au niveau des codes\n";

            if ($this->rreferentiel->liste_codes_competence==$re->liste_codes_competence){
    			$referentiel_reconnu=true;
                // DEBUG
                //echo "<br />Referentiel reconnu au niveau des competences\n";
            }
			else{
                $error_type=$error_competence_incompatible;
                $aide_type=$aide_competence_incompatible;
            }

    	}
		else{
            $error_type=$error_code_incompatible;
            $aide_type=$aide_code_incompatible;
        }
        // DEBUG
        //if ($referentiel_reconnu){
        //    echo "<br />Referentiel reconnu\n";
        //}
        //else{
        //    echo "<br />Referentiel NON reconnu par le code...\n";
        //}
        //exit;

        // DEBUG
        // echo "<br />FORMAT XML :: 2461 :: CODE REFERENTIEL: '".$re->code_referentiel."' --- CODE IMPORTE :'".$this->rreferentiel->code_referentiel."'\n";


		if ($referentiel_reconnu==false) {
			// ni nouvelle instance ni recouvrement
			$s=get_string('tasks', 'referentiel'). ' '.$re->code_referentiel;
			$this->error( $aide_type, $error_type, $s ,'import');
			return false;
		}
		else{
			// importer les taches
			$xmltasks = $xmlreferentiel['#']['task'];

        	foreach ($xmltasks as $xmltask) {
				// TACHE
				// print_r($task);
				$new_task = $this->defaulttask();
				$new_task->type_task=$this->getpath( $xmltask, array('#','type_task',0,'#'), '', true, $error_nocode);
				$new_task->description_task=$this->getpath( $xmltask, array('#','description_task',0,'#','text',0,'#'), '', true, '');

// MODIF JF 2012/05/22
// retablir des sauts de ligne
                $new_task->description_task=preg_replace("/\|\|\|/", "\r\n" , $new_task->description_task);

				$new_task->competences_task=$this->getpath( $xmltask, array('#','competences_task',0,'#'), '', true, $error_nocode);

				$new_task->criteres_evaluation=$this->getpath( $xmltask, array('#','criteres_evaluation',0,'#','text',0,'#'), '', true, '');
// MODIF JF 2012/05/22
// retablir des sauts de ligne
                $new_task->criteres_evaluation=preg_replace("/\|\|\|/", "\r\n" , $new_task->criteres_evaluation);

				$new_task->ref_instance=$this->ireferentiel->id;
				$new_task->ref_referentiel=$this->rreferentiel->id;
				$new_task->ref_course=$this->course->id;
				$new_task->auteurid=$USER->id;
				$new_task->date_creation=$this->getpath( $xmltask, array('#','date_creation',0,'#'), '', true, $error_nocode);
				$new_task->date_modif=$this->getpath( $xmltask, array('#','date_modif',0,'#'), '', true, $error_nocode);
				$new_task->date_debut=$this->getpath( $xmltask, array('#','date_debut',0,'#'), '', true, $error_nocode);
				$new_task->date_fin=$this->getpath( $xmltask, array('#','date_fin',0,'#'), '', true, $error_nocode);
				// MODIF JF 2010/10/07
				$new_task->tache_masquee=1;

				// enregistrer
				$new_task_id=$DB->insert_record("referentiel_task", $new_task);
				if ($new_task_id){
					// importer les consignes
					if (!empty($xmltask['#']['consigne'])){
                        $xmlconsignes = $xmltask['#']['consigne'];
                        $cindex=0;
                        foreach ($xmlconsignes as $xmlconsigne) {
                            $new_consigne = $this->defaultconsigne();
		    			    // $new_consigne->id = $this->getpath( $xmlconsigne, array('#','id',0,'#'), '', false, '');
						    $new_consigne->type_consigne=$this->getpath( $xmlconsigne, array('#','type_consigne',0,'#'), '', true, $error_nocode);
						    $new_consigne->description_consigne=$this->getpath( $xmlconsigne, array('#','description_consigne',0,'#','text',0,'#'), '', true, '');
						    $new_consigne->url_consigne=$this->getpath( $xmlconsigne, array('#','url_consigne',0,'#'), '', true, $error_nocode);
                            $new_consigne->timestamp = $this->getpath( $xmlconsigne, array('#','timestamp',0,'#'), '', false, '');
                            // MODIF JF 2012/10/09
                            // necessite de passer à l'API de Moodle 2
                            // A FAIRE
                            if (!preg_match("/http/", $new_consigne->url_consigne)){
                                // $new_consigne->url_consigne=$CFG->wwwroot.'/file.php/'.$new_consigne->url_consigne; // pas de liens relatifs sur les fichiers telecharges
                                $new_consigne->url_consigne='';
						    }

                            $new_consigne->ref_task=$new_task_id;
						    // enregistrer :  creation
						    $new_consigne_id=$DB->insert_record("referentiel_consigne", $new_consigne);
                        }
                    }
				}
			}
        }
        return $re;
    }



    /**
     * parse the array of lines into an array of questions
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array (of objects) question objects
     */
    function read_import_tasks($lines) {
        // we just need it as one big string
        $text = implode($lines, " ");
        unset( $lines );

        // this converts xml to big nasty data structure
        // the 0 means keep white space as it is (important for markdown format)
        // print_r it if you want to see what it looks like!
        $xml = xmlize( $text, 0 ); 
		
		// DEBUG
		// echo "<br />DEBUG xml/format.php :: ligne 580<br />\n";
		// print_r($xml);
		// echo "<br /><br />\n";		
		// exit;
        if (!empty($xml['referentiel'])){
            $re=$this->import_tasks($xml['referentiel']);
            // stick the result in the $treferentiel array
            // DEBUG
            // echo "<br />DEBUG xml/format.php :: ligne 2894<br />EXIT<br />\n";
            // print_r($re);
            // exit;
            return $re;
        }
        return NULL;
    }
}


// ################################################################################################################
// pedagoS : export des pedagos
class pformat_xml extends pformat_default {


    function provide_import() {
        return true;
    }

    function provide_export() {
        return true;
    }


    // EXPORT FUNCTIONS START HERE


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
		$raw=preg_replace("/\n/", "|||", $raw);

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


    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<pedagogies>\n" .
                       $content .
                       "</pedagogies>\n\n";

        // make the xml look nice
        $content = $this->xmltidy( $content );

        return $content;
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


	function write_pedago($record_asso, $record_pedago ) {
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\npedago: $record->id  -->\n";
		if ($record_asso && $record_pedago){
			$id = $this->writeraw(trim($record_pedago->id));
			$userid = $this->writeraw(trim($record_asso->userid));
			$username=$this->writeraw(referentiel_get_user_login($userid));
            $refrefid = $this->writeraw(trim($record_asso->refrefid));
			$date_cloture = $this->writetext(trim($record_pedago->date_cloture));
			$promotion =  $this->writetext(trim($record_pedago->promotion));
			$formation =  $this->writetext(trim($record_pedago->formation));
			$pedagogie =  $this->writetext(trim($record_pedago->pedagogie));
            $composante =  $this->writetext(trim($record_pedago->composante));
			$num_groupe=  $this->writetext(trim($record_pedago->num_groupe));
            $commentaire =  $this->writetext(trim($record_pedago->commentaire));
            $prenom= $this->writetext(referentiel_get_user_prenom($record_asso->userid));
            $patronyme = $this->writetext(referentiel_get_user_nom($record_asso->userid));

            $expout .= "   <pedago>\n";
			// $expout .= "    <id>$id</id>\n";
			// $expout .= "    <userid>$userid</userid>\n";
			// $expout .= "    <refrefid>$refrefid</refrefid>\n";
			$expout .= "    <username>$username</username>\n";
            $expout .= "    <firstname>$prenom</firtsname>\n";
            $expout .= "    <lastname>$patronyme</lastname>\n";
            $expout .= "    <date_cloture>$date_cloture</date_cloture>\n";
            $expout .= "    <promotion>$promotion</promotion>\n";
            $expout .= "    <formation>$formation</formation>\n";
            $expout .= "    <pedagogie>$pedagogie</pedagogie>\n";
            $expout .= "    <composante>$composante</composante>\n";
            $expout .= "    <num_groupe>$num_groupe</num_groupe>\n";
            $expout .= "    <commentaire>$commentaire</commentaire>\n";
            $expout .= "   </pedago>\n";
        }
        return $expout;
    }

	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_liste_pedagos() {
    	global $CFG;
        // initial string;
        $expout = "";

		if ($this->ireferentiel){

			if (isset($this->ireferentiel->course) && ($this->ireferentiel->course>0)){
				// ETUDIANTS
				$records_all_students = referentiel_get_students_course($this->ireferentiel->course);
				if ($records_all_students){
				    foreach ($records_all_students as $record_user){
                        // USER
                        if (isset($record_user->userid) && ($record_user->userid>0)){
                            $record_assos=referentiel_get_a_user_pedago($record_user->userid, $this->ireferentiel->ref_referentiel);
                            if ($record_assos){
                                foreach($record_assos as $record_asso){
                                    $record_pedago = referentiel_get_pedagogie($record_asso->refpedago);
                                    if ($record_pedago){
                                        $expout .= $this->write_pedago($record_asso, $record_pedago);
                                    }
                                }
                            }
                        }
                    }
				}
			}
        }
        return $expout;
    }


	// IMPORTATION
	/***************************************************************************

	// IMPORT FUNCTIONS START HERE

	***************************************************************************/
    // IMPORT FUNCTIONS START HERE

    /**
     * Translate human readable format name
     * into internal Moodle code number
     * @param string name format name from xml file
     * @return int Moodle format code
     */
    function trans_format( $name ) {
        $name = trim($name);

        if ($name=='moodle_auto_format') {
            $id = 0;
        }
        elseif ($name=='html') {
            $id = 1;
        }
        elseif ($name=='plain_text') {
            $id = 2;
        }
        elseif ($name=='wiki_like') {
            $id = 3;
        }
        elseif ($name=='markdown') {
            $id = 4;
        }
        else {
            $id = 0; // or maybe warning required
        }
        return $id;
    }

    /**
     * Translate human readable single answer option
     * to internal code number
     * @param string name true/false
     * @return int internal code number
     */
    function trans_single( $name ) {
        $name = trim($name);
        if ($name == "false" || !$name) {
            return 0;
        }
        else {
            return 1;
        }
    }

    /**
     * process text string from xml file
     * @param array $text bit of xml tree after ['text']
     * @return string processed text
     */
    function import_text( $text ) {
        // quick sanity check
        if (empty($text)) {
            return '';
        }
        $data = $text[0]['#'];
        return addslashes(trim( $data ));
    }

    /**
     * return the value of a node, given a path to the node
     * if it doesn't exist return the default value
     * @param array xml data to read
     * @param array path path to node expressed as array
     * @param mixed default
     * @param bool istext process as text
     * @param string error if set value must exist, return false and issue message if not
     * @return mixed value
     */
    function getpath( $xml, $path, $default, $istext=false, $error='' ) {
        foreach ($path as $index) {
			// echo " $index ";
            if (!isset($xml[$index])) {
                if (!empty($error)) {
                    $this->error( $error );
                    return false;
                }
                else {
					// echo " erreur ";
                    return $default;
                }
            }
            else {
				$xml = $xml[$index];
				// echo " $xml ";
			}
        }
        if ($istext) {
            $xml = addslashes( trim( $xml ) );
        }

        return $xml;
    }

     /**
	 * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees
     */
    function importation_referentiel_possible(){
	// selon les parametres soit cree une nouvelle instance
	// soit modifie une instance courante de referentiel
	global $CFG;

		if (!isset($this->action) || (isset($this->action) && ($this->action!="selectreferentiel") && ($this->action!="importreferentiel"))){
			if (!(isset($this->course->id) && ($this->course->id>0))
				||
				!(isset($this->rreferentiel->id) && ($this->rreferentiel->id>0))
				||
				!(isset($this->coursemodule->id) && ($this->coursemodule->id>0))
				){
				$this->error( get_string( 'incompletedata', 'referentiel' ) );
				return false;
			}
		}
		else if (isset($this->action) && ($this->action=="selectreferentiel")){
			if (!(isset($this->course->id) && ($this->course->id>0))){
				$this->error( get_string( 'incompletedata', 'referentiel' ) );
				return false;
			}
		}
		else if (isset($this->action) && ($this->action=="importreferentiel")){
			if (!(isset($this->course->id) && ($this->course->id>0))){
				$this->error( get_string( 'incompletedata', 'referentiel' ) );
				return false;
			}
		}
		return true;
	}


     /**
	 * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees
     */
    function import_pedagogies( $xmlpedagos ) {
    global $DB;
	// recupere le tableau de lignes
	   // selon les parametres soit cree une nouvelle instance
	    // soit modifie une instance courante de la table referentiel_a_user_scol
        // get some error strings
        $error_message='';
        // importer les pedagos
		$index=0;

		$nbpedagos=0;        // compteur
        foreach ($xmlpedagos as $pedago) {
			// PEDAGOS
            // echo "<br />\n";
            // print_r($pedago);
			$index++;
            // $id=$this->getpath( $pedago, array('#','id',0,'#'), '', false, '');
            $login = $this->getpath( $pedago, array('#','username',0,'#'), '', false, '');
            $firstname= $this->getpath( $pedago, array('#','firstname',0,'#','text',0,'#'), '', true, '');
  			$lastname= $this->getpath( $pedago, array('#','lastname',0,'#','text',0,'#'), '', true, '');
			$date_cloture = $this->getpath( $pedago, array('#','date_cloture',0,'#','text',0,'#'), '', false, '');
            $promotion = $this->getpath( $pedago, array('#','promotion',0,'#','text',0,'#'), '', false, '');
            $formation = $this->getpath( $pedago, array('#','formation',0,'#','text',0,'#'), '', true, '');
            $pedagogie = $this->getpath( $pedago, array('#','pedagogie',0,'#','text',0,'#'), '', true, '');
            $composante = $this->getpath( $pedago, array('#','composante',0,'#','text',0,'#'), '', true, '');
		    $num_groupe = $this->getpath( $pedago, array('#','num_groupe',0,'#','text',0,'#'), '', true, '');
            $commentaire = $this->getpath( $pedago, array('#','commentaire',0,'#','text',0,'#'), '', true, '');
            $code_referentiel = $this->getpath( $pedago, array('#','code_referentiel',0,'#','text',0,'#'), '', true, '');

            // rechercher la formation
			if (!empty($formation) && !empty($pedagogie)  && !empty($composante)){
                        $import_pedago = new stdClass();
						$import_pedago->date_cloture = $date_cloture;
					    $import_pedago->promotion =addslashes($promotion);
						$import_pedago->formation =addslashes($formation);
						$import_pedago->pedagogie =addslashes($pedagogie);
						$import_pedago->composante =addslashes($composante);
                        $import_pedago->num_groupe = addslashes($num_groupe);
                        $import_pedago->commentaire =addslashes($commentaire);

                        $userid=0;
                        if ($login!=''){
                            $userid=referentiel_get_userid_by_login($login);
                        }
                        if ($userid){ // this routine initialises the import object
                            $import_association = new stdClass();
                            $import_association->userid=$userid;
						    // $tref=referentiel_get_infos_from_code_ref($code_referentiel);     // probablement vide
						    $import_association->refrefid = $this->ref_referentiel;
                        }

                        // verification dans la base
                        $trouve_pedago=0;
                        $creerasso=0; // insertion association necessaire

                        if ($userid){
                            $rec_assos=referentiel_get_a_user_pedago($userid, $this->ref_referentiel);
                            if ($rec_assos){
                                // un enregistrement existe
                                $creerasso=1; // un enregistrement existe : update necessaire

                                foreach($rec_assos as $rec_asso){
                                    if ($rec_asso){
                                        $import_association->id=$rec_asso->id;
                                        $rec_pedago=referentiel_get_pedagogie($rec_asso->refpedago);
    						            if ($rec_pedago && !$trouve_pedago){    // sinon pas utile de chercher plus loin
                                            if (($rec_pedago->num_groupe == $num_groupe)
                                                //&&
                                                //($rec_pedago->date_cloture == $date_cloture)
                                                &&
                                                ($rec_pedago->promotion == $promotion)
                                                &&
                                                ($rec_pedago->formation == $formation)
                                                &&
                                                ($rec_pedago->pedagogie == $pedagogie)
                                                &&
                                                ($rec_pedago->composante == $composante)){ // deja connu
                                                $trouve_pedago=$rec_pedago->id;

                                                $creerasso=2;   // rien a faire pour l'association
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        else{
                             // MODIF JF 2013/05/23
                             // il faut probablement ajouter cet utilisateur à Moodle, mais comment faire ?
                             // A tout le moins signaler la difficulté
                             $error_message.='<br/>'.get_string('user_unknown','referentiel',$login.' '.$firstname.' '.$lastname);                      
                        }                        


        				if (!$trouve_pedago){  // verifier si une pedagogie identique existe
                            $rec_pedago=referentiel_get_id_pedago_from_data($num_groupe, $date_cloture, $promotion, $formation, $pedagogie, $composante);
                            if ($rec_pedago){
                                $trouve_pedago=$rec_pedago->id;
                            }
						}
        				if (!$trouve_pedago){   // enregistrer  pedagogie
                            $trouve_pedago=$DB->insert_record("referentiel_pedagogie", $import_pedago);
                        }
                        if ($trouve_pedago){    // mettre a jour association si necessaire
                            // faut-il mettre quelque chose à jour ?
                            $rec_pedago=referentiel_get_pedagogie($trouve_pedago);

                            if (!empty($commentaire) && ($commentaire!=$rec_pedago->commentaire)){
                                    $rec_pedago->commentaire=$commentaire;
                                    referentiel_update_pedagogie_record($rec_pedago);
                            }
                            if (!empty($date_cloture) && ($date_cloture!=$rec_pedago->date_cloture)){
                                    $rec_pedago->date_cloture=$date_cloture;
                                    referentiel_update_pedagogie_record($rec_pedago);
                            }

                            if ($userid){
                                $import_association->refpedago = $trouve_pedago;
                                if ($creerasso==1){  // update
                                    $DB->update_record("referentiel_a_user_pedagogie", $import_association);
                                }
                                elseif ($creerasso==0){ // insertion
                                    $id_asso=$DB->insert_record("referentiel_a_user_pedagogie", $import_association);
                                }
                            }
                        }
			}
		}
        if (!empty($error_message)){
           echo '<div class="error">';
           echo get_string('import_pedagogie_error','referentiel')."\n";
           echo $error_message."\n";
           echo '</div>'."\n";
        }
    
        return true;
    }

    /**
     * parse the array of lines into an array
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array of pedago object
     */
	function read_import_pedagos($lines) {
        // we just need it as one array
        // we just need it as one big string
        $text = implode($lines, " ");
        unset( $lines );

        // this converts xml to big nasty data structure
        // the 0 means keep white space as it is (important for markdown format)
        // print_r it if you want to see what it looks like!
        $xml = xmlize( $text, 0 );
		// DEBUG
		// echo "<br />DEBUG xml/format.php :: ligne 3310<br />\n";
		// print_r($xml);
		// echo "<br /><br />\n";
		// exit;
        if (!empty($xml['pedagogies'])){
            return $this->import_pedagogies($xml['pedagogies']);
        }
        else{
            return NULL;
        }
    }
    
    // ###############"" Fin de la classe pformat
    
    
}

// INCLUSION
require_once "archive_format.php";

?>

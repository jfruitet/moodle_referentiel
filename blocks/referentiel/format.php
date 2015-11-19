<?php  // $Id: format.php,v 1.0 2008/05/01 00:00:00 jfruitet Exp $ 
/**
 * Base class for referentiel import and export formats.
 * recupere de question/format.php
 *
 * @author Martin Dougiamas, Howard Miller, and many others.
 *         {@link http://moodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package referentiel
 */
 

class rformat_default {

	var $blockid=0;
    var $displayerrors = true;
	var $referentiel = NULL; // referentiel_referentiel
    var $course = NULL;
    var $filename = '';
    var $importerrors = 0;
    var $stoponerror = true;
	var $override = false;
	var $returnpage = "";
	var $new_referentiel_id = ""; // id d'un referentiel_referentiel
    var $contents = "";
    var $context = NULL;
    
// functions to indicate import/export functionality
// override to return true if implemented

    function provide_import() {
      return false;
    }

    function provide_export() {
      return false;
    }

// Accessor methods

    function setBlockId( $blockid ) {
        $this->blockid = $blockid;
    }

    /**
     * set the context class variable
     * @param contexte object Moodle context variable
     */
    function setContext( $context ) {
        $this->context = $context;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setContents( $contents ) {
        $this->contents = $contents;
    }

    /**
     * set the referentiel
     * @param object referentiel the referentiel object
     */
	function setReferentiel( $referentiel ) {
        $this->referentiel = $referentiel;
    }


    /**
     * set the referentiel
     * @param id referentiel the referentiel referentiel id
     */
	function setReferentielId( $id ) {
        $this->new_referentiel_id = $id;
    }

    /**
     * set the action 
     * @param string action 
     */
	function setAction( $action ) {
        $this->action = $action;
    }

    /**
     * set the course class variable
     * @param course object Moodle course variable
     */
    function setCourse( $course ) {
        $this->course = $course;
    }

    /**
     * set the filename
     * @param string filename name of file to import/export
     */
    function setFilename( $filename ) {
        $this->filename = $filename;
    }


    /**
     * set returnpage
     * @param bool stoponerror stops database write if any errors reported
     */
    function setReturnpage( $returnpage ) {
        $this->returnpage = $returnpage;
    }

    /**
     * set stoponerror
     * @param bool stoponerror stops database write if any errors reported
     */
    function setStoponerror( $stoponerror ) {
        $this->stoponerror = $stoponerror;
    }
	
    /**
     * set override
     * @param bool override database write 
     */
    function setOverride( $override ) {
        $this->override = $override;
    }
	
    /**
     * set newinstance
     * @param bool newinstance database write 
     */
    function setNewinstance( $newinstance ){
        $this->newinstance = $newinstance;
    }
	

/*******************
 * EXPORT FUNCTIONS
 *******************/

    /** 
     * Provide export functionality for plugin referentiel types
     * Do not override
     * @param name referentiel name
     * @param referentiel object data to export 
     * @param extra mixed any addition format specific data needed
     * @return string the data to append to export or false if error (or unhandled)
     */
    function try_exporting( $name, $referentiel, $extra=null ) {

        // work out the name of format in use
        $formatname = substr( get_class( $this ), strlen( 'rformat_' ));
        $methodname = "export_to_$formatname";

		if (method_exists( $methodname )) {
			if ($data = $methodname( $referentiel, $this, $extra )) {
				return $data;
            }
        }
        return false;
    }

    /**
     * Return the files extension appropriate for this type
     * override if you don't want .txt
     * @return string file extension
     */
    function export_file_extension() {
        return ".txt";
    }

    /**
     * Do any pre-processing that may be required
     * @param boolean success
     */
    function exportpreprocess() {
        return true;
    }

    /**
     * Enable any processing to be done on the content
     * just prior to the file being saved
     * default is to do nothing
     * @param string output text
     * @param string processed output text
     */
    function presave_process( $content ) {
        return $content;
    }

    /**
     * Do the export
     * For most types this should not need to be overrided
     * @return boolean success
     */
    function exportprocess() {
        global $CFG;

        //notify( get_string('exportingreferentiels', 'referentiel') );
        $count = 0;

        // results are first written into string (and then to a file)
        // so create/initialize the string here
        $expout = "";
        
        // export the item displaying message
        $count++;
        echo "<hr /><p><b>$count</b>. ".$this->referentiel->name."</p>";
        $expout .= $this->write_referentiel() . "\n";

        // continue path for following error checks
        $course = $this->course;
        $continuepath = $CFG->wwwroot.'/blocks/referentiel/export.php?blockid='.$this->blockid.'&amp;courseid='.$this->course->id.'&amp;occurrenceid='.$this->referentiel->id;

        // did we actually process anything
        if ($count==0) {
            $this->error( get_string('noreferentiels','referentiel',$continuepath),$filepath,'','export' );
        }

        // final pre-process on exported data
        $expout = $this->presave_process( $expout );

        // Moodle 2.0
        $fs = get_file_storage();
        // Prepare file record object
        $fileinfo = array(
            'contextid' => $this->context->id, // ID of context
            'component' => 'block_referentiel',     // usually = module name
            'filearea' => 'referentiel',     // usually = table name
            'itemid' => $this->referentiel->id,               // usually = ID of row in table
            'filepath' => $this->get_export_dir(),  // any path beginning and ending in /
            'filename' => $this->filename.$this->export_file_extension()); // any filename

		// DEBUG
		// echo "<br>format.php :: 234 :: FILEINFO      \n";
		// print_object($fileinfo);


        // Create file containing text
        $fs->create_file_from_string($fileinfo, $expout);

        		// DEBUG
		// echo "<br>format.php :: 234 :: FILEINFO      \n";
		// print_object($fs);
		// exit;

        return true;
    }

    /**
     * Do an post-processing that may be required
     * @return boolean success
     */
    function exportpostprocess() {
        return true;
    }

    /**
     * convert a single referentiel object into text output in the given
     * format.
     * This must be overriden
     * @param object referentiel referentiel object
     * @return mixed referentiel export text or null if not implemented
     */
    function write_referentiel() {
        // if not overidden, then this is an error.
        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * get directory into which export is going 
     * @return string file path
     */
    function get_export_dir() {
		global $CFG;
        // Moodle 2.0
        return '/';
    }



/***********************
 * IMPORTING FUNCTIONS
 ***********************/

    /**
     * Handle parsing error
     */
    function error( $message, $text='', $name='', $type='' ) {
        if ($type=='import')
            $error = get_string('importerror', 'referentiel');
        else
            $error = get_string('exporterror', 'referentiel');

        echo "<div class=\"importerror\">\n";
        echo "<strong>$error $name</strong>";
        if (!empty($text)) {
            $text = s($text);
            echo "<blockquote>$text</blockquote>\n";
        }
        echo "<i>$message</i>\n";
        echo "</div>";

         $this->importerrors++;
    }



    /** 
     * Import for referentieltype plugins
     * Do not override.
     * @param data mixed The segment of data containing the referentiel
     * @param referentiel object processed (so far) by standard import code if appropriate
     * @param extra mixed any additional format specific data that may be passed by the format
     * @return object referentiel object suitable for save_options() or false if cannot handle
     */
    function try_importing( $data, $referentiel=null, $extra=null ) {

        // work out what format we are using
        $formatname = substr( get_class( $this ), strlen('rformat_'));
        $methodname = "import_from_$formatname";

        // loop through installed referentieltypes checking for
        // function to handle this referentiel
        if (method_exists( $methodname)) {
        	if ($referentiel = $methodname( $data, $referentiel, $this, $extra )) {
            	return $referentiel;
            }
        }
        return false;   
    }

    /**
     * Perform any required pre-processing
     * @return boolean success
     */
    function importpreprocess() {
        return true;
    }

    /**
     * Process the file
     * This method should not normally be overidden
     * @return boolean success
     */
    function importprocess() {

       	// reset the timer in case file upload was slow
       	@set_time_limit();

       	// STAGE 1: Parse the file
       	// notify( get_string('parsing', 'referentiel') );

        // Moodle 2.0
        if (! $lines = $this->readdata()) {
            notify( get_string('cannotread', 'referentiel') );
            return false;
        }
		$newly_imported_referentiel = new stdClass();
		
		// DEBUG
		// echo "<br />DEBUG :: ./mod/referentiel/format.php :: 390<br />EXIT<br />\n";
		// print_r($lines);
		// exit;
        if (! $newly_imported_referentiel = $this->lines_2_referentiel($lines)) {   // Extract the referentiel
            notify( get_string('noinfile', 'referentiel') );
            return false;
        }

        // STAGE 2: Write data to database
		// echo "<br />\n";
		// print_object($newly_imported_referentiel);
		// echo "<br />\n";
        // check for errors before we continue
        if ($this->stoponerror and ($this->importerrors>0)) {
            return false;
        }

		// notify( get_string('importdone', 'referentiel') );
		

		return true;
    }

    /**
     * Return complete file within an array, one item per line
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */

    function readdata() {
    // MOODLE 2.0
        if (!empty($this->contents)) {
            // DEBUG
            // echo "<br />DEBUG :: ./mod/referentiel/format.php :: 439<br />EXIT<br />\n";
            // echo nl2br($this->contents)." NEWLINE\n";


            // $filearray = file($filename);
            /// Check for Macintosh OS line returns (ie file on one line), and fix
            //if (ereg("\r", $this->contents) AND !ereg("\n", $this->contents)) {
            if (preg_match("/\r/", $this->contents) AND !preg_match("/\n/", $this->contents)) {
                $content=explode("\r", $this->contents);
            }
            else if (preg_match("/\r\n/", $this->contents)) {
                $content=explode("\r\n", $this->contents);
            }
            else {
                $content=explode("\n", $this->contents);
            }

            // print_r($content);
            // exit;
            return ($content);
        }
        return false;
    }

    /**
     * Parses an array of lines into a referentiel, 
     * where is a newly_imported_referentiel object as defined by 
     * readimportedreferentiel().
     *
     * @param array lines array of lines from readdata
     * @return array referentiel object
     */
    function lines_2_referentiel($lines) {
	// 
        $tline = array();
		if (is_array($lines)){
            foreach ($lines as $line) {
                $line = trim($line);
			
                if (!empty($line)) {
                    $tline[] = $line;
                }
            }
		    // echo "<br />DEBUG 3 : format.php :: ligne 453 :: fonction lines_2_referentiel()<br />\n";

	       	// echo "<br />\n";
		    // exit;
            if (!empty($tline)) {  // conversion
                $imported_referentiel = $this->read_import_referentiel($tline);
            }
        }
        else{
            $imported_referentiel = $this->read_import_referentiel($lines);
        }
        return $imported_referentiel ;
    }


    /**
     * return an "empty" referentiel
     * Somewhere to specify referentiel parameters that are not handled
     * by import but are required db fields.
     * This should not be overridden.
     * @return object default referentiel
	*/      
    function defaultreferentiel() {
	// retourne un objet import_referentiel qui mime l'objet refrentiel
        $import_referentiel = new stdClass();
		$import_referentiel->name="";
		$import_referentiel->code_referentiel="";
		$import_referentiel->description_referentiel="";
		$import_referentiel->url_referentiel="";
		$import_referentiel->seuil_certificat="";
    	$import_referentiel->timemodified = time();
		$import_referentiel->nb_tasks="";
		$import_referentiel->liste_codes_competence="";
        $import_referentiel->liste_empreintes_competence="";
        $import_referentiel->liste_poids_competence="";
		$import_referentiel->local=0;
    	$import_referentiel->id = 0;
        // this option in case the referentieltypes class wants
        // to know where the data came from
        $import_referentiel->export_process = true;
        $import_referentiel->import_process = true;
        return $import_referentiel;
    }

	function defaultprotocole() {
    // retourne un objet protocol
        $protocole = new stdClass();
    	// $protocole->id = 0;
		$protocole->ref_occurrence=0;
		$protocole->seuil_referentiel=0;
		$protocole->l_domaines_oblig='';
		$protocole->l_seuils_domaines='';
		$protocole->l_minimas_domaines='';
		$protocole->l_competences_oblig='';
        $protocole->l_seuils_competences='';
        $protocole->l_minimas_competences='';
        $protocole->l_items_oblig='';
		$protocole->timemodified=0;
		$protocole->actif=0;
        $protocole->commentaire='';
        return $protocole;
    }

    function defaultdomaine() {
        // retourne un objet domaine
        $domaine = new stdClass();
    	$domaine->id = 0;
		$domaine->code_domaine="";
		$domaine->description_domaine="";
		$domaine->num_domaine=0;
		$domaine->nb_competences=0;
		$domaine->ref_referentiel=0;
        return $domaine;
    }

    function defaultcompetence() {
	// retourne un objet competence	
        $competence = new stdClass();
    	$competence->id = 0;
		$competence->code_competence="";
		$competence->description_competence="";
		$competence->num_competence=0;
		$competence->nb_item_competences=0;
		$competence->ref_domaine=0;
        return $competence;
    }

    function defaultitem() {
	// retourne un objet item de competence
        $item = new stdClass();
    	$item->id = 0;
		$item->code_item="";
		$item->description_item="";
		$item->num_item=0;
		$item->type_item="";
		$item->poids_item=0;
		$item->ref_competence=0;
		$item->ref_referentiel=0;
        return $item;
    }

    /**
     * Given the data known to define a referentiel in 
     * this format, this function converts it into a referentiel 
     * object suitable for processing and insertion into Moodle.
     *
     * If your format does not use blank lines to delimit referentiels
     * (e.g. an XML format) you must override 'readreferentiels' too
     * @param $lines mixed data that represents referentiel
     * @return object referentiel object
     */
	function read_import_referentiel($lines) {

        $formatnotimplemented = get_string( 'formatnotimplemented', 'referentiel' );
        echo "<p>$formatnotimplemented</p>";

        return NULL;
    }

    /**
     * Override if any post-processing is required
     * @return boolean success
     */
    function importpostprocess() {
        return true;
    }

    /**
     * Import an image file encoded in base64 format
     * @param string path path (in course data) to store picture
     * @param string base64 encoded picture
     * @return string filename (nb. collisions are handled)
     */
    function importimagefile( $path, $base64 ) {
        global $CFG;

        // all this to get the destination directory
        // and filename!
        $fullpath = "{$CFG->dataroot}/{$this->course->id}/$path";
        $path_parts = pathinfo( $fullpath );
        $destination = $path_parts['dirname'];
        $file = clean_filename( $path_parts['basename'] );

        // check if path exists
        check_dir_exists($destination, true, true );

        // detect and fix any filename collision - get unique filename
        $newfiles = resolve_filename_collisions( $destination, array($file) );        
        $newfile = $newfiles[0];

        // convert and save file contents
        if (!$content = base64_decode( $base64 )) {
            return '';
        }
        $newfullpath = "$destination/$newfile";
        if (!$fh = fopen( $newfullpath, 'w' )) {
            return '';
        }
        if (!fwrite( $fh, $content )) {
            return '';
        }
        fclose( $fh );

        // return the (possibly) new filename
        //$newfile = ereg_replace("{$CFG->dataroot}/{$this->course->id}/", '',$newfullpath);
        $newfile = preg_replace("/".$CFG->dataroot."\/".$this->course->id."\//", '',$newfullpath);
        return $newfile;
    }
}




?>
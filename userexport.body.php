<?php
/** \file
* \brief Contains code for the UserExport Class (extends SpecialPage).
*/

///Special page class for the User Export extension
/**
 * Special page that allows sysops to export the username and
 * user email to a CSV file
 * 
 * @addtogroup Extensions
 * @author Rodrigo Sampaio Primo <rodrigo@utopia.org.br>
 */
class UserExport extends SpecialPage {
	function __construct() {
		parent::__construct( 'UserExport', 'userexport' );
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser;

		wfLoadExtensionMessages( 'UserExport' );

		$this->setHeaders();

		if ( !$wgUser->isAllowed( 'userexport' ) ) {
			$wgOut->permissionRequired( 'userexport' );
			return;
		}

        if ( $wgRequest->getText( 'exportusers' ) ) {
            if ( !$wgUser->matchEditToken( $wgRequest->getVal( 'token' ) ) ) {
                // bad edit token
                $wgOut->addHtml( "<span style=\"color: red;\">" . wfMsg( 'userexport-badtoken' ) . "</span><br />\n" );
            } else {
                $this->exportUsers();
            }
        }

        $wgOut->addHTML(
            Xml::openElement('p') .
            wfMsg( 'userexport-description' ) .
            Xml::closeElement('p') . 
            Xml::openElement( 'form', array( 'method' => 'post', 'action' => $this->getTitle()->getLocalUrl(), 'id' => 'userexportform' ) ) .
            Xml::submitButton( wfMsg( 'userexport-submit' ) ) .
			Xml::hidden( 'token', $wgUser->editToken() ) .
	    	Xml::hidden( 'exportusers', true ) .
		    Xml::closeElement( 'form' ) . "\n"
		);
	}

	/**
     * Function to query the database and generate the CVS file
     *
	 * @return Always returns true - throws exceptions on failure.
	 */
    private function exportUsers()
    {
        $filePath = tempnam(sys_get_temp_dir(), '');
        $file = fopen($filePath, 'w');

        $db = wfGetDB( DB_MASTER );
        $users = $db->select('user', array('user_name', 'user_email'));
        
        fputcsv($file, array('login', 'email'));

        while ( $user = $db->fetchObject( $users ) ) {
            fputcsv($file, array($user->user_name, $user->user_email));
        }

        fclose($file);

        header("Pragma:  no-cache");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: text/csv");
        header("Content-Transfer-Encoding: binary"); 
        header("Content-Disposition: attachment; filename=\"mediawiki_users.csv\"");
        header("Content-Length: " . filesize($filePath));  
        header("Accept-Ranges: bytes");  

        readfile($filePath);
        unlink($filePath);
        die;
    }
}

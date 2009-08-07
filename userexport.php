<?php
/** \file
* \brief Contains setup code for the User Export Extension.
*/

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
    echo "User Export extension";
    exit(1);
}

$wgExtensionCredits['specialpage'][] = array(
    'path'           => __FILE__,
    'name'           => 'User Export',
    'url'            => 'http://www.mediawiki.org/wiki/Extension:User_Export',
    'author'         => 'Rodrigo Sampaio Primo',
    'description'    => "Export the Mediawiki users (username and email) to a CSV file. Requires userexport permission.",
    'descriptionmsg' => 'userexport-desc',
    'version'        => '1.0'
);

$wgAvailableRights[] = 'userexport';
$wgGroupPermissions['bureaucrat']['userexport'] = true;

$dir = dirname(__FILE__) . '/';
$wgAutoloadClasses['UserExport'] = $dir . 'userexport.body.php';

$wgExtensionMessagesFiles['UserExport'] = $dir . 'userexport.i18n.php';
$wgExtensionAliasesFiles['UserExport'] = $dir . 'userexport.alias.php';
$wgSpecialPages['UserExport'] = 'UserExport';
$wgSpecialPageGroups['UserExport'] = 'users';

$wgUserExportProtectedGroups = array( "sysop" );

# Add a new log type
$wgLogTypes[]                         = 'userexport';
$wgLogNames['userexport']              = 'userexport-logpage';
$wgLogHeaders['userexport']            = 'userexport-logpagetext';
$wgLogActions['userexport/exportuser']  = 'userexport-success-log';

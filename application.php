<?

/*
 * $LastChangedRevision:  $
 * $LastChangedBy:  $
 * $LastChangedDate:  $
 */

/* define a generic object */
class object {};





/* define database error handling behavior, since we are in development stages
* we will turn on all the debugging messages to help us troubleshoot */

$DB_DEBUG = true;
$DB_DIE_ON_FAIL = true;

/*
 * Here is how debug levels work.  
 * 1 = INFO
 * 2 = WARN
 * 3 = ERROR
 */
$APP_DEBUG = 1;
$MAIL_DEBUG = true;



/* start up the sessions, to keep things clean and manageable we will just
 * use one array called SESSION to store our persistent variables.   */

session_start();


/* reset the configuration cache if switched from another instance */

if( isset($_SESSION["CFG"]["wwwroot"]) ){
	
	$scriptName =  $_SERVER['SCRIPT_NAME'];
	$pathArray = explode("/",$scriptName);
	
	if( "$pathArray[1]/" != $_SESSION["CFG"]["wwwroot"]){
		unset($_SESSION["CFG"]);
	}

}

/* initialize the SESSION CFG variable if necessary */
if( !isset($_SESSION["CFG"]) ){
	$_SESSION["CFG"]["wwwroot"] = "/clubpro";
	$_SESSION["CFG"]["imagedir"] =  "/clubpro/images";
	$_SESSION["CFG"]["dirroot"]  = "/Users/Adam/workspace/myClubpro/clubpro";
	$_SESSION["CFG"]["templatedir"] = "/Users/Adam/workspace/myClubpro/clubpro/templates";
	$_SESSION["CFG"]["libdir"] = "/Users/Adam/workspace/myClubpro/clubpro/lib";
	$_SESSION["CFG"]["wordlist"] =  "/Users/Adam/workspace/myClubpro/clubpro/lib/wordlist.txt";

	$_SESSION["CFG"]["includedir"] =  "/Users/Adam/workspace/myClubpro/clubpro/includes";
	$_SESSION["CFG"]["dns"] = "localhost";
	$_SESSION["CFG"]["support"]  = "support@sportsynergy.net";
	$_SESSION["CFG"]["wordlist"] = "localhost";
	$_SESSION["CFG"]["logFile"] = "/Users/Adam/Logs/SystemOut.log";
	
}

/* load up standard libraries */
require($_SESSION["CFG"]["libdir"]."/stdlib.php");
require($_SESSION["CFG"]["libdir"]."/dblib.php");
require($_SESSION["CFG"]["libdir"]."/applicationlib.php");
require($_SESSION["CFG"]["includedir"]."/include_phpAjaxTags.php");

/* setup some global variables */
$ME = qualified_me();
$MEWQ = qualified_mewithq();


$dbhost = "localhost";
$dbname = "sportfx_clubpro03122011";
$dbuser = "sportfxuser";
$dbpass = "sportfxpass";

/* connect to the database */
db_connect($dbhost, $dbname, $dbuser, $dbpass);



?>
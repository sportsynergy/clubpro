<?

/*
This script runs every day to disable player who have an expired membership. The parameter passed into this script is the parameter id associated with the membership expiration.
*/


include ("../application.php");
require '../vendor/autoload.php';

date_default_timezone_set('GMT');

$service = new AccountExpirationService();

$expiration_parameter = $argv[1];

$service->getExpiredUsers($expiration_parameter);


class AccountExpirationService {

	/* 

	Get the users that have an expiration

	*/
	public function getExpiredUsers($parameter){

		// This format is the same format set in the change settings form.
		$today = gmdate("m/d/Y", mktime() );


		if (isDebugEnabled(1)) logMessage("AccountExpirationService:getExpiredUsers $today for $parameter");

		$query = "SELECT userid FROM tblParameterValue WHERE parameterid = $parameter AND parametervalue = '$today'";
	
		print $query;

	  	$result = db_query($query);

	  	while( $res_array = mysqli_fetch_array($result ) ){

	  		print("disabling a iuser");
	  		$this->disableUser($res_array['userid']);
	  	}

	  	
	}


	/* 

	For these users set their account to disabled and log it.

	*/
	private function disableUser($userid){

		if (isDebugEnabled(1)) logMessage("AccountExpirationService:disableUser");

		print "disabling user: $userid";
	}



}





?>

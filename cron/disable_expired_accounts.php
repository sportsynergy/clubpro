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
		$today = gmdate("m/d/Y", time() );


		if (isDebugEnabled(2)) logMessage("AccountExpirationService:getExpiredUsers $today for $parameter");

		$query = "SELECT userid FROM tblParameterValue WHERE parameterid = $parameter AND parametervalue = '$today'";
	
	  	$result = db_query($query);

	  	if (mysqli_num_rows($result) > 0){

	  		while( $res_array = mysqli_fetch_array($result ) ){

	  			$this->disableUser($res_array['userid']);
	  		}
	  	} else {

	  		if (isDebugEnabled(2)) logMessage("AccountExpirationService.getExpiredUsers: No users to disable for $today for parameter $parameter");
	  	}


	  	

	  	
	}


	/* 

	For these users set their account to disabled and log it.

	*/
	private function disableUser($userid){

		if (isDebugEnabled(2)) logMessage("AccountExpirationService.disableUser: Disabling: $userid");

		$query = "UPDATE tblClubUser SET enable = 'n' where userid = $userid";
		$result = db_query($query);
	}



}





?>

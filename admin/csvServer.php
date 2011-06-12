<?

include("../application.php");

$searchname = $_REQUEST['searchname'];

$extraParametersResult = load_site_parameters();
$playerresult = get_admin_player_search($searchname);

header('Content-type: text/csv');
header('Content-disposition: attachment;filename=users.csv');

// get site parameters
$csvHeaderArray = array();


// Start with the headers
array_push($csvHeaderArray, "First Name");
array_push($csvHeaderArray, "Last Name");
array_push($csvHeaderArray, "Year Joined");
array_push($csvHeaderArray, "Home Phone");
array_push($csvHeaderArray, "Work Phone");
array_push($csvHeaderArray, "Cell Phone");
array_push($csvHeaderArray, "Email");

while( $parameterArray = db_fetch_array($extraParametersResult)){ 
	array_push($csvHeaderArray, $parameterArray['parameterlabel']);
}

//Print the Headers
print implode(",",$csvHeaderArray);
print "\n";


// Print the Data
while ($row = mysql_fetch_array($playerresult)) {
	
	$csvDataArray = array();
	
	array_push($csvDataArray, wrapWithDoubleQuotes($row['firstname']) );
	array_push($csvDataArray, wrapWithDoubleQuotes($row['lastname']));
	array_push($csvDataArray, wrapWithDoubleQuotes($row['msince'] ) );
	array_push($csvDataArray, wrapWithDoubleQuotes($row['homephone'] )  );
	array_push($csvDataArray, wrapWithDoubleQuotes($row['workphone'] ) );
	array_push($csvDataArray, wrapWithDoubleQuotes($row['cellphone']) ) ;
	array_push($csvDataArray, wrapWithDoubleQuotes($row['email'] ) );

	if( mysql_num_rows($extraParametersResult) > 0 ){
		mysql_data_seek($extraParametersResult, 0);
	}
	
	
	while( $parameterArray = db_fetch_array($extraParametersResult)){ 
		
		$parameterValue = load_site_parameter($parameterArray['parameterid'], $row['userid']);
		
		if($parameterArray['parametertypename']=="select"){
			$optionName = load_parameter_option_name($parameterArray['parameterid'], $parameterValue);
			array_push($csvDataArray,$optionName );
		}else{
			array_push($csvDataArray,$parameterValue );
		}
		
		
		
	}
	
	// first name, last name , spouse name,  year joined,  home phone, work phone, cell phone, email,  Membership type. 
	// users.firstname, users.lastname, users.email, users.workphone, users.homephone, users.userid
	print implode(",",$csvDataArray);
	print "\n";
	
}

/**
 * 
 * @param unknown_type $string
 */
function wrapWithDoubleQuotes($string){
	return '"'.trim($string,'"').'"';
}

?>
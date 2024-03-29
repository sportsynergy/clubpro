<? 
require '../vendor/autoload.php';

use Aws\Ses\SesClient; 
use Aws\Exception\AwsException;

include ("../application.php");


$SesClient = new Aws\Ses\SesClient([
    'profile' => 'default',
    'version' => 'latest',
    'region' => 'us-east-2'

]);


try {
    $result = $SesClient->listIdentities([
        'IdentityType' => 'Domain',
    ]);
    var_dump($result);
} catch (AwsException $e) {
    // output error message if fails
    echo $e->getMessage();
    echo "\n";
}

?>

<?=$_SESSION["CFG"]["AWS_ACCESS_KEY_ID"] ?>
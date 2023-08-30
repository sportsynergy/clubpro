<? 



require '../vendor/autoload.php';

use Aws\Ses\SesClient; 
use Aws\Exception\AwsException;

include ("../application.php");

$email = $_GET["email"];



//Create a SESClient 
// snippet-start:[ses.php.list_email.main]
$SesClient = new Aws\Ses\SesClient([
    'profile' => 'default',
    'version' => 'latest',
    'region' => 'us-east-1'
]);



$html_body = '<h1>AWS Amazon Simple Email Service Test Email</h1>' .
    '<p>This email was sent with <a href="https://aws.amazon.com/ses/">' .
    'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">' .
    'AWS SDK for PHP</a>.</p>';
$subject = 'Amazon SES test (AWS SDK for PHP)';
$plaintext_body = 'This email was send with Amazon SES using the AWS SDK for PHP.';
$sender_email = 'Player Mailer <player.mailer@sportsynergy.net>';
$recipient_emails = [$email];
$char_set = 'UTF-8';
//$configuration_set = 'ConfigSet';

try {
    $result = $SesClient->sendEmail([
        'Destination' => [
            'ToAddresses' => $recipient_emails,
        ],
        'ReplyToAddresses' => [$sender_email],
        'Source' => $sender_email,
        'Message' => [

            'Body' => [
                'Html' => [
                    'Charset' => $char_set,
                    'Data' => $html_body,
                ],
                'Text' => [
                    'Charset' => $char_set,
                    'Data' => $plaintext_body,
                ],
            ],
            'Subject' => [
                'Charset' => $char_set,
                'Data' => $subject,
            ],
        ],
        // If you aren't using a configuration set, comment or delete the
        // following line
        //'ConfigurationSetName' => $configuration_set,
    ]);
    var_dump($result);
} catch (AwsException $e) {
    // output error message if fails
    echo $e->getMessage();
    echo "\n";
}


?>
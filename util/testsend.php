<?


#include("../application.php");
require '../vendor/autoload.php';


sendHelloEmail();


function helloEmail()
{
    $from = new SendGrid\Email(null, "test@example.com");
    $subject = "Hello World from the SendGrid PHP Library";
    $to = new SendGrid\Email(null, "test@example.com");
    $content = new SendGrid\Content("text/plain", "some text here");
    $mail = new SendGrid\Mail($from, $subject, $to, $content);
    $to = new SendGrid\Email(null, "test2@example.com");
    $mail->personalization[0]->addTo($to);
    //echo json_encode($mail, JSON_PRETTY_PRINT), "\n";
    return $mail;
}

function sendHelloEmail()
{
    $apiKey = getenv('SENDGRID_API_KEY');
    

    $sg = new \SendGrid($apiKey);
    $request_body = helloEmail();
    $response = $sg->client->mail()->send()->post($request_body);
    echo $response->statusCode();
    echo $response->body();
    echo $response->headers();
}



?>

<?


#include("../application.php");
require '../vendor/autoload.php';


sendHelloEmail();


function helloEmail()
{
    $from = new SendGrid\Email(null, "support@sportsynergy.net");
    $subject = "Hello World from the SendGrid PHP Library";
    
    
    $personalization = new SendGrid\Personalization();
    $to = new SendGrid\Email("Bob Jones", "adam704a@hotmail.com");
    $personalization->addTo($to);
    
    $content = new SendGrid\Content("text/plain", "some text here");
    $mail = new SendGrid\Mail();

    
    $mail->setFrom($from);
    $mail->setSubject($subject);
    $mail->addPersonalization($personalization);
    $mail->addContent($content);

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

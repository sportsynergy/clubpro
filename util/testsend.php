<?

include("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/sendgrid-php/SendGrid_loader.php");

date_default_timezone_set('GMT');


$to_emails = array();
$to_emails2 = array();
$to_email = "Adam Preston <adam704a@hotmail.com>";
$to_emails[$to_email] = array('name' => "adam");
$to_emails2[] = $to_email;

$to_email = "Bill Smith <adamatlast@gmail.com>";
$to_emails[$to_email] = array('name' => "Bill");
$to_emails2[] = $to_email;


#$toList = array('adam704a@hotmail.com', 'adamatlast@gmail.com');

var_dump($to_emails);

$toList = array();
$names = array();
foreach ($to_emails as $k=>$v){
	$toList[] = $k;
	$names[] = $v['name'];
}

var_dump($names);

die;

$sendgrid = new SendGrid($_SESSION["CFG"]["sendgriduser"], $_SESSION["CFG"]["sendgridpass"]);

$template = file_get_contents($_SESSION["CFG"]["templatedir"]."/email/standard.email.html");

$bodytag = str_replace("%clubname%", "The Commodore", $template);

$mail = new SendGrid\Mail();
$mail->
  setFrom('adam.m.preston@gmail.com')->
  setFromName('Sportsynergy')->
  setSubject('foobar subject')->
  setText('foobar text')->
  addCategory("Test")->
  setTos($toList)->
setHtml($bodytag)->
#addSubstitution("%name%", array("John"));
addSubstitution("%firstname%", array("Bill", "Tom"))->
addSubstitution("%content%", array("This is a test","Again"));

$sendgrid->smtp->send($mail);



?>

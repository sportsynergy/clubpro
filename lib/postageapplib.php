
<?php

  
  class PostageApp
  {
    // Sends a message to Postage App
    function mail($recipient, $subject, $mail_body, $header, $variables=NULL) {
      
    	if( isDebugEnabled(1) ) logMessage("postageapplib.PostageApp: sending email with template: $mail_body");
	
    	$content = array(
        'recipients'  => $recipient,
        'headers'     => array_merge($header, array('Subject' => $subject)),
        'variables'   => $variables,
        'uid'         => time()
      );
      if (is_string($mail_body)) {
        $content['template'] = $mail_body;
      } else {
        $content['content'] = $mail_body;
      }
      
      return PostageApp::post(
        'send_message', 
        json_encode(
          array(
            'api_key' => $_SESSION["CFG"]["emailkey"], 
            'arguments' => $content
          )
        )
      );
    }
    
    // Makes a call to the Postage App API
    function post($api_method, $content) {
      $ch = curl_init($_SESSION["CFG"]["emailhost"].'/v.1.0/'.$api_method.'.json');
      curl_setopt($ch, CURLOPT_POSTFIELDS,  $content);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'User-Agent: PostageApp PHP ' . phpversion()
      ));   
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $output = curl_exec($ch);
      curl_close($ch);
      return json_decode($output);
    }
  }
?>
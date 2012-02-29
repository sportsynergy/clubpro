<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */
/**
* Class and Function List:
* Function list:
* - mail()
* - post()
* Classes list:
* - PostageApp
*/

class PostageApp {

    // Sends a message to Postage App
    function mail($recipient, $subject, $mail_body, $header, $variables = NULL) {
        
        if (isDebugEnabled(1)) logMessage("postageapplib.PostageApp: sending email with template: $mail_body");
        $content = array(
            'recipients' => $recipient,
            'headers' => array_merge($header, array(
                'Subject' => $subject
            )) ,
            'variables' => $variables,
            'uid' => time()
        );
        
        if (is_string($mail_body)) {
            $content['template'] = $mail_body;
        } else {
            $content['content'] = $mail_body;
        }
        return PostageApp::post('send_message', json_encode(array(
            'api_key' => $_SESSION["CFG"]["emailkey"],
            'arguments' => $content
        )));
    }

    // Makes a call to the Postage App API
    function post($api_method, $content) {
        $ch = curl_init($_SESSION["CFG"]["emailhost"] . '/v.1.0/' . $api_method . '.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
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
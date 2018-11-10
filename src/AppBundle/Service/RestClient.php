<?php

namespace AppBundle\Service;

use \Unirest;

class RestClient
{

    private $domain = "http://raidtracker.ddns.net/raid_tracker_api/web/app.php/";

    public function isValidLogin($username, $password){
        $headers = array('Accept' => 'application/json');
        $data = array(
            'login' => $username,
            'password' => $password);
        $response = Unirest\Request::post($this->domain.'api/auth-tokens', $headers, $data);
        
        if($response->code == 201) {
            return $response->body;
        }
        return false;
    }
}
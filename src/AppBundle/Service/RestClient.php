<?php

namespace AppBundle\Service;

use \Unirest;

class RestClient
{

    private $domain = "http://raidtracker.ddns.net/raid_tracker_api/web/app.php/";

    public function isValidLogin($username, $password){
        $data = array(
            'login' => $username,
            'password' => $password
        );
        $response = $this->post('api/auth-tokens', $data);
        
        if($response->code == 201) {
            return $response->body;
        }
        return false;
    }

    public function post($url, $data){
        $headers = array('Accept' => 'application/json');
        $response = Unirest\Request::post($this->domain.''.$url, $headers, $data);
        return $response;
    }

    public function get($url){
        $headers = array('Accept' => 'application/json');      
        $response = Unirest\Request::get($this->domain.''.$url, $headers);
        return $response;
    }

    public function delete($url){
        $headers = array('Accept' => 'application/json');      
        $response = Unirest\Request::delete($this->domain.''.$url, $headers);
        return $response;
    }
}
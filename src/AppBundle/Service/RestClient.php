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
            'password' => $password
        );

        $response = Unirest\Request::post($this->domain.'api/auth-tokens', $headers, $data);
        
        if($response->code == 201) {
            return $response->body;
        }
        return false;
    }

    public function post($url, $data, $token = null){

        $headers = array(
            'Accept' => 'application/json',
            'X-Auth-Token' => $token
        );

        if($token === null){
            $headers = array('Accept' => 'application/json');  
        }
        
        $response = Unirest\Request::post($this->domain.''.$url, $headers, $data);

        if($response->code == 201){
            return $response;
        }
        return null;
    }

    public function get($url, $token=null){
        $headers = array(
            'Accept' => 'application/json',
            'X-Auth-Token' => $token
        );

        $response = Unirest\Request::get($this->domain.''.$url, $headers);

        if($response->code == 200){
            return $response->body;
        }
        return null;
    }

    public function delete($url, $token){
        $headers = array(
            'Accept' => 'application/json',
            'X-Auth-Token' => $token
        );
        $response = Unirest\Request::delete($this->domain.''.$url, $headers);
        
        if($response->code == 202){
            return $response;
        }
        return null;
    }
}
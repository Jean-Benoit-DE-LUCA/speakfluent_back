<?php

namespace App\JWT;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use \DateTime;
use \DateTimeZone;

require_once '../Config.php';
use Config;

class JwtClass {

    private $key;





    public function __construct()
    {   
        $this->key = Config::getKey();
    }







    public function getKey() {
        
        return $this->key;
    }

    public function setKey($key) {

        $this->key = $key;
        return $this;
    }







    public function encodeJwt() {

        $currentDateTime = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $currentTimeStamp = $currentDateTime->getTimestamp();

        $payload = [
            'iss' => 'speakfluent',
            'aud' => 'speakfluent',
            'iat' => $currentDateTime,
            'nbf' => $currentTimeStamp,
            'exp' => $currentTimeStamp + 18000  // 5 hours // 10800 // 3 hours //
        ];

        $jwt = JWT::encode($payload, $this->key, 'HS256');

        return $jwt;
    }






    public function decodeJwt($jwt) {

        try {
            
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
        }

        catch (\Exception $e) {

            return $e->getMessage();
        }
        

        return $decoded;
    }
}
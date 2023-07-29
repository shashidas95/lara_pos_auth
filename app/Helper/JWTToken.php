<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class JWTToken
{
    public static function CreateToken($userEmail, $userId)
    {
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time() + 60 * 60,
            'userEmail' => $userEmail,
            'userId' => $userId,

        ];
        return $token = JWT::encode($payload, $key, 'HS256');
    }
    public static function DecodeToken($token)
    {
        try {
            $key = env('JWT_KEY');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->userEmail;
        } catch (Exception $e) {
            return 'unauthorised';
        }
    }
    public static function CreateTokenForSetPassword($userEmail)
    {
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time() + 60 * 60,
            'userEmail' => $userEmail,
            'userId' => '0',


        ];
        return $token = JWT::encode($payload, $key, 'HS256');
    }
    public static function VerifyToken($token)
    {
        try {
            if ($token == null) {
                return 'unauthorised';
            } else {
                $key = env('JWT_KEY');
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                return $decoded;
            }
        } catch (Exception $e) {
            return 'unauthorised';
        }
    }
}

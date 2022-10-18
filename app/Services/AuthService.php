<?php namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;

class AuthService
{
    public static function requestToken($clientId, $clientSecret, $username, $password) {

        try {
            $response = Http::asForm()->post('http://127.0.0.1:8000/oauth/token', [
                'grant_type'    => 'password',
                'client_id'     => 13,
                'client_secret' => 'GdWPyaMkL9wik0whd2hn4Vzlncy1Xj2gV45Kre0O',
                'username'      => "edilson18martins@gmail.com",
                'password'      => "63286144",
                'scope' => '*',
            ]);
        }
        catch (\Exception $e) {
            return null;
        } catch (GuzzleException $e) {
        }

        return json_decode((string) $response->getBody(), true);
    }
}

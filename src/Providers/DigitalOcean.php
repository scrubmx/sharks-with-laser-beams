<?php

namespace Sharks\Providers;

use GuzzleHttp\Client;
use Sharks\Storage\Config;
use Sharks\Storage\Droplet;
use Sharks\Support\SSH;

class DigitalOcean
{
    /**
     * @param  $servers
     * @param  $token
     * @param  $key
     * @return array
     */
    public function create($servers, $token, $key)
    {
        $fingerprint = SSH::getFingerPrint($key);

        $droplets = [];

        while($servers--) {
            $droplets[] = $this->createInstance($token, $fingerprint);
        }

        return $droplets;
    }

    /**
     * @return array
     */
    public function down()
    {
        return array_map([$this, 'deleteDroplet'], Droplet::ids());
    }

    /**
     * @param $ip
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteDroplet($ip)
    {
        $http = new Client();

        return $http->delete("https://api.digitalocean.com/v2/droplets/{$ip}", [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . Config::get('api_token'),
            ]
        ]);
    }
    /**
     * @param $token
     * @param $fingerprint
     *
     * @return mixed
     */
    private function createInstance($token, $fingerprint)
    {
        return $this->post('https://api.digitalocean.com/v2/droplets', [
            'name'     => 'shark',
            'region'   => 'nyc3',
            'size'     => '512mb',
            'image'    => 'ubuntu-14-04-x64',
            'ssh_keys' => [$fingerprint]
        ], $token);
    }

    /**
     * @param $url
     * @param $body
     * @param $token
     *
     * @return mixed
     */
    public function post($url, $body, $token)
    {
        $http = new Client();

        return $http->post($url, [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
            'body' => json_encode($body)
        ]);
    }
}
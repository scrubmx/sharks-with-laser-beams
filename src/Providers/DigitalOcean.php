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
     * @return array
     */
    public function allDroplets()
    {
        $http = new Client();

        $response = $http->get("https://api.digitalocean.com/v2/droplets", [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . Config::get('api_token'),
            ]
        ]);

        $response = json_decode($response->getBody()->getContents());

        return is_null($response) ? [] : $response->droplets;
    }

    /**
     * @return array
     */
    public function report(array $ids=[])
    {
        $droplets = $this->allDroplets();

        $ids = empty($ids) ? Droplet::ids() : $ids;

        $filtered = array_filter($droplets, function($instance) use ($ids) {
            return in_array($instance->id, $ids);
        });

        $result = [];

        foreach($filtered as $droplet){
            $temp = new \stdClass();
            $temp->id = $droplet->id;
            $temp->name = $droplet->name;
            $temp->status = $droplet->status;
            $temp->ip_address = isset($droplet->networks->v4[0]) ? $droplet->networks->v4[0]->ip_address : '';
            $temp->region = $droplet->region->name;
            $temp->price_hourly = $droplet->size->price_hourly;
            $result[] = $temp;
        }

        return $result;
//         return array_map(function($droplet) {
//            return [
//                'id'           => $droplet->id,
//                'name'         => $droplet->name,
//                'status'       => $droplet->status,
//                'ip_address'   => isset($droplet->networks->v4[0]) ? $droplet->networks->v4[0]->ip_address : '',
//                'region'       => $droplet->region->name,
//                'price_hourly' => $droplet->size->price_hourly
//            ];
//        }, $filtered);
    }

    /**
     * @param $ip
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteDroplet($ip)
    {
        $http = new Client();

        try {
            return $http->delete("https://api.digitalocean.com/v2/droplets/{$ip}", [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . Config::get('api_token'),
                ]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return;
        }
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
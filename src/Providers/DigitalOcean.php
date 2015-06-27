<?php

namespace Sharks\Providers;

use GuzzleHttp\Client;
use Sharks\Storage\Config;
use Sharks\Storage\Droplet;
use Sharks\Support\SSH;

class DigitalOcean
{
    /**
     * @param  $amount
     * @param  $token
     * @param  $key
     * @return array
     */
    public function create($amount, $token, $key)
    {
        $fingerprint = SSH::getFingerPrint($key);

        $droplets = [];

        while($amount--) {
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
        $instances = $this->getSharks($ids);

        $result = [];

         foreach($instances as $instance){
            $result[] = (object) [
                'id'           => $instance->id,
                'name'         => $instance->name,
                'status'       => $instance->status,
                'ip_address'   => isset($instance->networks->v4[0]) ? $instance->networks->v4[0]->ip_address : '',
                'region'       => $instance->region->name,
                'price_hourly' => $instance->size->price_hourly
            ];
        }

        return $result;
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

    /**
     * @param array $ids
     * @return array
     */
    private function getSharks(array $ids=[])
    {
        $droplets = $this->allDroplets();

        $ids = empty($ids) ? Droplet::ids() : $ids;

        return array_filter($droplets, function ($instance) use ($ids) {
            return in_array($instance->id, $ids);
        });
    }
}

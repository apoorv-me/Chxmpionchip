<?php

namespace App\Library;

use GuzzleHttp;
use Config;
trait GuzzleTrait {

    /**
     * @param $url
     * @return GuzzleHttp\Message\FutureResponse|GuzzleHttp\Message\ResponseInterface|GuzzleHttp\Ring\Future\FutureInterface|null
     * Setup function for the guzzle requests going out to the live subscription site
     */
    private function guzzleRequest($url)
    {
        $client = new GuzzleHttp\Client();

        $subResponse = $client->get(env('API_URL').$url,[
            'headers' => [
                'Content-Type' => 'application/json'
            ]]);

        return $subResponse;
    }

    // private function GuzzlePostRequest($data, $url)
    // {
    //     $client = new GuzzleHttp\Client();
    //     $client->setDefaultOption('headers', [
    //             'Content-Type' => 'application/json',
    //             'customer_name' => 'test',
    //             'customer_key' => 's324ghe7cf77f652ef2f030b9f26'
    //     ]);

    //     $subResponse = $client->post(env('API_URL').$url,['form_params' => $data]);

    //     return $subResponse;
    // }

    // private function GuzzlePutRequest($data, $url)
    // {
    //     $client = new GuzzleHttp\Client();
    //     $client->setDefaultOption('headers', [
    //             'Content-Type' => 'application/json',
    //             'customer_name' => 'test',
    //             'customer_key' => 's324ghe7cf77f652ef2f030b9f26'
    //     ]);

    //     $subResponse = $client->put(env('API_URL').$url,['body' => $data]);

    //     return $subResponse;
    // }
}
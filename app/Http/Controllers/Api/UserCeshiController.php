<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

class UserCeshiController extends Controller
{
    //
    //
    public function ceshi(){
        //curl 请求
        $http = new GuzzleClient();
        $response = $http->get('http://jsonplaceholder.typicode.com/posts/2', [
             /*'name' => 'Taylor',
             'page' => 1,*/
        ]);
        $res = json_decode( $response->getBody(), true);

        $response = $http->post('http://127.0.0.1:8788/api/mobileLogin', [
            'mobile' => '15689092620',
            'code' => '3333',
        ]);

        $response = json_decode( $response->getBody(), true);
        return $response;
        //return $res;
       // return 'guaosi';
    }
}

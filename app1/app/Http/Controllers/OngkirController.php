<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class OngkirController extends Controller
{
    protected $client;

    public function __construct()
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.rajaongkir.com/starter/',
            'timeout'  => 15,
        ]);

        $this->client = $client;
    }

    public function province(Request $request)
    {
        $query['key'] = $this->_getRajaOngkirKey();

        if ($request->get('id')) $query['id'] = $request->get('id');

        try {
            $res = $this->client->get('province', [
                'query' => $query
            ]);

            $body = $res->getBody();
            $response = json_decode((string) $body);
            $rajaongkir = $response->rajaongkir;

            return response()->json([
                'ok' => true,
                'msg' => 'Success',
                'rajaongkir' => [
                    'status' => (array) $rajaongkir->status ?? [],
                    'results' => (array) $rajaongkir->results ?? []
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'An error occurred'
            ]);
        }
    }

    public function city(Request $request)
    {
        $query['key'] = $this->_getRajaOngkirKey();

        if ($request->get('province')) $query['province'] = $request->get('province');
        if ($request->get('id')) $query['id'] = $request->get('id');

        try {
            $res = $this->client->get('city', [
                'query' => $query
            ]);

            $body = $res->getBody();
            $response = json_decode((string) $body);
            $rajaongkir = $response->rajaongkir;

            return response()->json([
                'ok' => true,
                'msg' => 'Success',
                'rajaongkir' => [
                    'status' => (array) $rajaongkir->status ?? [],
                    'results' => (array) $rajaongkir->results ?? []
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'An error occurred'
            ]);
        }
    }

    public function cost(Request $request)
    {
        $query['key'] = $this->_getRajaOngkirKey();

        $query['origin'] = $request->get('origin');
        $query['destination'] = $request->get('destination');
        $query['weight'] = (int) $request->get('weight');
        $query['courier'] = $request->get('courier');

        $res = $this->client->post('cost', [
            'form_params' => $query,
            'http_errors' => false
        ]);

        $body = $res->getBody();
        $response = json_decode((string) $body);
        $rajaongkir['status'] = (array) $response->rajaongkir->status ?? [];

        // if status from server is valid
        if (($rajaongkir['status']['code'] ?? 0) == 200) {
            $rajaongkir['origin_details'] =  (array) $response->rajaongkir->origin_details ?? [];
            $rajaongkir['destination_details'] = (array) $response->rajaongkir->destination_details ?? [];
            $rajaongkir['results'] = (array) $response->rajaongkir->results ?? [];
        }

        return response()->json([
            'ok' => true,
            'msg' => 'Success',
            'rajaongkir' => $rajaongkir
        ]);
        try {
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'An error occurred',
            ]);
        }
    }

    private function _getRajaOngkirKey()
    {
        return env('RAJAONGKIR_KEY', '');
    }
}

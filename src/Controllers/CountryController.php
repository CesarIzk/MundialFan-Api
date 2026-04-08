<?php

namespace App\Controllers;

use App\Models\Country;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CountryController
{
    // ── GET /api/countries ────────────────────────────────────────────────────
    public function index(Request $request, Response $response): Response
    {
        $countries = Country::all();

        $response->getBody()->write(json_encode($countries));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    // ── GET /api/countries/{id} ───────────────────────────────────────────────
    public function show(Request $request, Response $response, array $args): Response
    {
        $country = Country::find($args['id']);

        $response->getBody()->write(json_encode($country));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($country ? 200 : 404);
    }
}
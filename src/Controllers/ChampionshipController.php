<?php

namespace App\Controllers;

use App\Models\Championship;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ChampionshipController
{
    // ── GET /api/championships ────────────────────────────────────────────────
    public function index(Request $request, Response $response): Response
    {
        $championships = Championship::allSorted('desc');

        $response->getBody()->write(json_encode($championships));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
<?php

namespace App\Controllers;

use App\Models\Category;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryController
{
    // ── GET /api/categories ───────────────────────────────────────────────────
    public function index(Request $request, Response $response): Response
    {
        $categories = Category::allSorted();

        $response->getBody()->write(json_encode($categories));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
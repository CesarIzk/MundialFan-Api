<?php

namespace App\Controllers;

use App\Models\Championship;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ChampionshipController
{
    // ════════════════════════════════════════════════════════════════════════
    // RUTAS PÚBLICAS
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/championships ────────────────────────────────────────────────
    public function index(Request $request, Response $response): Response
    {
        $championships = Championship::allSorted('desc');
        
        // Mapear campos para el frontend
        $championships = $championships->map(function($c) {
            $c->host = $c->host_country;
            $c->goals = $c->total_goals;
            $c->teams = $c->participating_teams;
            $c->runner_up = $c->runner_up;
            return $c;
        });

        return $this->json($response, $championships);
    }

    // ── GET /api/championships/{id} ───────────────────────────────────────────
    public function show(Request $request, Response $response, array $args): Response
    {
        $championship = Championship::find($args['id']);
        if (!$championship) {
            return $this->json($response, ['message' => 'Campeonato no encontrado.'], 404);
        }

        // Mapear campos para el frontend
        $championship->host = $championship->host_country;
        $championship->goals = $championship->total_goals;
        $championship->teams = $championship->participating_teams;

        return $this->json($response, $championship);
    }

    // ── GET /api/stats/general ────────────────────────────────────────────────
    public function statsGeneral(Request $request, Response $response): Response
    {
        // ── Resumen de campeonatos ─────────────────────────────────────────
        $championships = DB::table('championships')
            ->orderBy('year', 'desc')
            ->get();

        $totalGoals      = $championships->sum('total_goals');
        $totalEditions   = $championships->count();
        $lastChamp       = $championships->first();

        // ── Ranking de países (por títulos) ───────────────────────────────
        $countries = DB::table('countries')
            ->orderBy('titles', 'desc')
            ->orderBy('participations', 'desc')
            ->get(['name', 'code', 'flag', 'titles', 'participations', 'continent']);

        // ── Equipos exitosos ──────────────────────────────────────────────
        $teams = DB::table('successful_teams')
            ->orderBy('titles', 'desc')
            ->get(['name', 'flag', 'titles']);

        // ── Goles por edición (para gráfica) ─────────────────────────────
        $goalsByYear = $championships
            ->sortBy('year')
            ->map(fn($c) => [
                'year'  => $c->year,
                'goals' => $c->total_goals,
                'host'  => $c->host_country,
            ])->values();

        // ── Campeones históricos (veces que ganó cada país) ───────────────
        $championsCount = $championships
            ->groupBy('champion')
            ->map(fn($group, $name) => [
                'country' => $name,
                'titles'  => $group->count(),
            ])
            ->sortByDesc('titles')
            ->values();

        return $this->json($response, [
            'summary' => [
                'total_editions'  => $totalEditions,
                'total_goals'     => $totalGoals,
                'last_champion'   => $lastChamp?->champion ?? '—',
                'last_year'       => $lastChamp?->year ?? '—',
                'last_host'       => $lastChamp?->host_country ?? '—',
            ],
            'champions_ranking' => $championsCount,
            'countries_ranking' => $countries,
            'successful_teams'  => $teams,
            'goals_by_year'     => $goalsByYear,
        ]);
    }



    // ── GET /api/admin/championships ─────────────────────────────────────────
    public function adminIndex(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $q = trim($request->getQueryParams()['q'] ?? '');

        $query = DB::table('championships')->orderBy('year', 'desc');

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('host_country', 'like', "%{$q}%")
                   ->orWhere('year', 'like', "%{$q}%")
                   ->orWhere('champion', 'like', "%{$q}%");
            });
        }

        $championships = $query->get()->map(function($c) {
            // Mapear para el frontend admin
            $c->host = $c->host_country;
            $c->goals = $c->total_goals;
            $c->teams = $c->participating_teams;
            return $c;
        });

        return $this->json($response, $championships);
    }

    // ── GET /api/admin/championships/{id} ─────────────────────────────────────
    public function adminShow(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $champ = DB::table('championships')->where('id', $args['id'])->first();
        if (!$champ) {
            return $this->json($response, ['message' => 'Campeonato no encontrado.'], 404);
        }

        // Mapear campos para el frontend admin
        $champ->host = $champ->host_country;
        $champ->goals = $champ->total_goals;
        $champ->teams = $champ->participating_teams;
        $champ->runner_up = $champ->runner_up;

        return $this->json($response, $champ);
    }

// ── POST /api/admin/championships ─────────────────────────────────────────
public function adminCreate(Request $request, Response $response): Response
{
    $this->requireAdmin($request);

    $body = (array) $request->getParsedBody();

    // Convertir valores vacíos a null para strings y a 0 para números
    $year = !empty($body['year']) ? $body['year'] : null;
    $hostCountry = !empty($body['host']) ? $body['host'] : null;
    $champion = !empty($body['champion']) ? $body['champion'] : null;
    $runnerUp = !empty($body['runner_up']) ? $body['runner_up'] : null;
    $description = !empty($body['description']) ? $body['description'] : null;
    
    // Para campos numéricos, convertir a entero (0 si está vacío)
    $totalGoals = isset($body['goals']) && $body['goals'] !== '' ? (int)$body['goals'] : 0;
    $participatingTeams = isset($body['teams']) && $body['teams'] !== '' ? (int)$body['teams'] : 0;

    $id = DB::table('championships')->insertGetId([
        'year'                => $year,
        'host_country'        => $hostCountry,
        'champion'            => $champion,
        'runner_up'           => $runnerUp,
        'total_goals'         => $totalGoals,
        'participating_teams' => $participatingTeams,
        'description'         => $description,
        'created_at'          => date('Y-m-d H:i:s'),
        'updated_at'          => date('Y-m-d H:i:s'),
    ]);

    return $this->json($response, ['message' => 'Campeonato creado.', 'id' => $id], 201);
}
  
// ── PUT /api/admin/championships/{id} ─────────────────────────────────────
public function adminUpdate(Request $request, Response $response, array $args): Response
{
    $this->requireAdmin($request);

    $exists = DB::table('championships')->where('id', $args['id'])->exists();
    if (!$exists) {
        return $this->json($response, ['message' => 'Campeonato no encontrado.'], 404);
    }

    $body = (array) $request->getParsedBody();

    $year = !empty($body['year']) ? $body['year'] : null;
    $hostCountry = !empty($body['host']) ? $body['host'] : null;
    $champion = !empty($body['champion']) ? $body['champion'] : null;
    $runnerUp = !empty($body['runner_up']) ? $body['runner_up'] : null;
    $description = !empty($body['description']) ? $body['description'] : null;
    
    $totalGoals = isset($body['goals']) && $body['goals'] !== '' ? (int)$body['goals'] : 0;
    $participatingTeams = isset($body['teams']) && $body['teams'] !== '' ? (int)$body['teams'] : 0;

    DB::table('championships')->where('id', $args['id'])->update([
        'year'                => $year,
        'host_country'        => $hostCountry,
        'champion'            => $champion,
        'runner_up'           => $runnerUp,
        'total_goals'         => $totalGoals,
        'participating_teams' => $participatingTeams,
        'description'         => $description,
        'updated_at'          => date('Y-m-d H:i:s'),
    ]);

    return $this->json($response, ['message' => 'Campeonato actualizado.']);
}

    // ── DELETE /api/admin/championships/{id} ──────────────────────────────────
    public function adminDelete(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $exists = DB::table('championships')->where('id', $args['id'])->exists();
        if (!$exists) {
            return $this->json($response, ['message' => 'Campeonato no encontrado.'], 404);
        }

        DB::table('championships')->where('id', $args['id'])->delete();
        return $this->json($response, ['message' => 'Campeonato eliminado.']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // HELPERS
    // ════════════════════════════════════════════════════════════════════════

    private function requireAdmin(Request $request): void
    {
        $authUser = $request->getAttribute('auth_user');
        if (!$authUser || $authUser['role'] !== 'admin') {
            throw new \Slim\Exception\HttpForbiddenException($request, 'Acceso restringido a administradores.');
        }
    }

    private function json(Response $response, mixed $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
<?php

use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\UserController;
use App\Controllers\AdminController;
use App\Controllers\CategoryController;
use App\Controllers\CountryController;
use App\Controllers\ChampionshipController;
use App\Controllers\NotificationController;
use App\Controllers\NotificationStream;
use App\Middleware\JwtMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // ── Prefijo global /api ───────────────────────────────────────────────────
    $app->group('/api', function (RouteCollectorProxy $api) {

        // ── Auth (públicas) ───────────────────────────────────────────────────
        $api->group('/auth', function (RouteCollectorProxy $auth) {
            $auth->post('/login',    [AuthController::class, 'login']);
            $auth->post('/register', [AuthController::class, 'register']);
        });

        // ── Categorías (públicas) ─────────────────────────────────────────────
        $api->get('/categories', [CategoryController::class, 'index']);

        // ── Países (públicas) ─────────────────────────────────────────────────
        $api->get('/countries',      [CountryController::class, 'index']);
        $api->get('/countries/{id}', [CountryController::class, 'show']);

        // ── Campeonatos (públicas) ────────────────────────────────────────────
        $api->get('/championships',  [ChampionshipController::class, 'index']);

        // ── Posts (GET público, resto protegido) ──────────────────────────────
        $api->get('/posts',     [PostController::class, 'index']);   // público
        $api->get('/posts/{id}', [PostController::class, 'show']);   // público

        // Rutas de posts que requieren JWT
        $api->group('/posts', function (RouteCollectorProxy $posts) {
            $posts->post('',               [PostController::class, 'store']);
            $posts->delete('/{id}',        [PostController::class, 'destroy']);
            $posts->post('/{id}/like',     [PostController::class, 'toggleLike']);
            $posts->get('/{id}/comments',  [PostController::class, 'comments']);
            $posts->post('/{id}/comments', [PostController::class, 'storeComment']);

        })->add(JwtMiddleware::class);

        // ── Comentarios (eliminar) ────────────────────────────────────────────
        $api->delete('/comments/{id}', [PostController::class, 'destroyComment'])
            ->add(JwtMiddleware::class);

        // ── Notificaciones: SSE (fuera del grupo JWT, se autentica via ?token=) ──
        $api->get('/users/me/notifications/stream', [NotificationStream::class, 'stream']);

        // ── Usuarios ──────────────────────────────────────────────────────────
        $api->group('/users', function (RouteCollectorProxy $users) {
            $users->get('',               [UserController::class, 'search']);
            $users->get('/{id}',          [UserController::class, 'show']);
            $users->put('/me',            [UserController::class, 'updateProfile']);
            $users->post('/me/avatar',    [UserController::class, 'updateAvatar']);
            $users->post('/me/cover',     [UserController::class, 'updateCover']);
            $users->put('/me/password',   [UserController::class, 'updatePassword']);
            $users->delete('/me',         [UserController::class, 'deactivate']);
            $users->get('/me/friends',               [UserController::class, 'getFriends']);
            $users->get('/me/requests',              [UserController::class, 'getRequests']);
            $users->post('/me/requests/{id}',        [UserController::class, 'sendRequest']);
            $users->post('/me/requests/{id}/accept', [UserController::class, 'acceptRequest']);
            $users->post('/me/requests/{id}/decline',[UserController::class, 'declineRequest']);
            
            // ── Chats ─────────────────────────────────────────────────────────────
            $users->get('/me/chats',                 [UserController::class, 'getChats']);
            $users->get('/me/chats/{id}',            [UserController::class, 'getMessages']);
            $users->post('/me/chats/{id}',           [UserController::class, 'sendMessage']);

            // ── Notificaciones ────────────────────────────────────────────────────
            $users->get('/me/notifications',              [NotificationController::class, 'index']);
            $users->get('/me/notifications/unread-count', [NotificationController::class, 'unreadCount']);
            $users->post('/me/notifications/read',        [NotificationController::class, 'markAllRead']);
        })->add(JwtMiddleware::class);

        // ── Admin ─────────────────────────────────────────────────────────────
        $api->group('/admin', function (RouteCollectorProxy $admin) {
            // Posts
            $admin->get('/posts',                    [AdminController::class, 'posts']);
            $admin->put('/posts/{id}',               [AdminController::class, 'updatePost']);
            $admin->put('/posts/{id}/status',        [AdminController::class, 'changePostStatus']);
            $admin->delete('/posts/{id}',            [AdminController::class, 'deletePost']);

            // Usuarios
            $admin->get('/users',                    [AdminController::class, 'users']);
            $admin->get('/users/{id}',               [AdminController::class, 'showUser']);
            $admin->post('/users',                   [AdminController::class, 'createUser']);
            $admin->put('/users/{id}',               [AdminController::class, 'updateUser']);
            $admin->put('/users/{id}/status',        [AdminController::class, 'changeUserStatus']);
            $admin->delete('/users/{id}',            [AdminController::class, 'deleteUser']);

            // Comentarios
            $admin->get('/comments',                 [AdminController::class, 'comments']);
            $admin->delete('/comments/{id}',         [AdminController::class, 'deleteComment']);

            // Categorías
            $admin->get('/categories',               [AdminController::class, 'categories']);
            $admin->post('/categories',              [AdminController::class, 'createCategory']);
            $admin->put('/categories/{id}',          [AdminController::class, 'updateCategory']);
            $admin->delete('/categories/{id}',       [AdminController::class, 'deleteCategory']);

            // Campeonatos
            $admin->get('/championships',            [AdminController::class, 'championships']);
            $admin->get('/championships/{id}',       [AdminController::class, 'showChampionship']);
            $admin->post('/championships',           [AdminController::class, 'createChampionship']);
            $admin->put('/championships/{id}',       [AdminController::class, 'updateChampionship']);
            $admin->delete('/championships/{id}',    [AdminController::class, 'deleteChampionship']);

            // Países
            $admin->get('/countries',                [AdminController::class, 'adminCountries']);
            $admin->get('/countries/{id}',           [AdminController::class, 'showCountry']);
            $admin->post('/countries',               [AdminController::class, 'createCountry']);
            $admin->post('/countries/{id}',          [AdminController::class, 'updateCountry']); // frontend usa _method=PUT
            $admin->delete('/countries/{id}',        [AdminController::class, 'deleteCountry']);

            // Jugadores Destacados
            $admin->get('/featured-players',         [AdminController::class, 'featuredPlayers']);
            $admin->get('/featured-players/{id}',    [AdminController::class, 'showFeaturedPlayer']);
            $admin->post('/featured-players',        [AdminController::class, 'createFeaturedPlayer']);
            $admin->put('/featured-players/{id}',    [AdminController::class, 'updateFeaturedPlayer']);
            $admin->delete('/featured-players/{id}', [AdminController::class, 'deleteFeaturedPlayer']);

            // Equipos Exitosos
            $admin->get('/successful-teams',         [AdminController::class, 'successfulTeams']);
            $admin->get('/successful-teams/{id}',    [AdminController::class, 'showSuccessfulTeam']);
            $admin->post('/successful-teams',        [AdminController::class, 'createSuccessfulTeam']);
            $admin->put('/successful-teams/{id}',    [AdminController::class, 'updateSuccessfulTeam']);
            $admin->delete('/successful-teams/{id}', [AdminController::class, 'deleteSuccessfulTeam']);

            // Admin creation & stats
            $admin->post('/create-admin',            [AdminController::class, 'createAdmin']);
            $admin->get('/dashboard/stats',          [AdminController::class, 'dashboardStats']);

            // Reportes
            $admin->get('/reports/metrics',          [AdminController::class, 'reportMetrics']);
            $admin->get('/reports/daily',            [AdminController::class, 'reportDaily']);
            $admin->get('/reports/top-users',        [AdminController::class, 'reportTopUsers']);
            $admin->get('/reports/top-posts',        [AdminController::class, 'reportTopPosts']);
            $admin->get('/reports/users-by-country', [AdminController::class, 'reportUsersByCountry']);
            $admin->get('/reports/content-types',    [AdminController::class, 'reportContentTypes']);

            // Logs
            $admin->get('/logs',                     [AdminController::class, 'getLogs']);
            $admin->delete('/logs',                  [AdminController::class, 'clearLogs']);
        })->add(JwtMiddleware::class);

    });
};
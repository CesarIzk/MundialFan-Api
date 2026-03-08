<?php

use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\UserController;
use App\Controllers\AdminController;
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

        // ── Usuarios ──────────────────────────────────────────────────────────
        $api->group('/users', function (RouteCollectorProxy $users) {
            $users->get('',               [UserController::class, 'search']);
            $users->get('/{id}',          [UserController::class, 'show']);
            $users->put('/me',            [UserController::class, 'updateProfile']);
            $users->post('/me/avatar',    [UserController::class, 'updateAvatar']);
            $users->put('/me/password',   [UserController::class, 'updatePassword']);
            $users->delete('/me',         [UserController::class, 'deactivate']);
        })->add(JwtMiddleware::class);

        // ── Admin ─────────────────────────────────────────────────────────────
        $api->group('/admin', function (RouteCollectorProxy $admin) {
            $admin->get('/posts',                    [AdminController::class, 'posts']);
            $admin->put('/posts/{id}/status',        [AdminController::class, 'changePostStatus']);
            $admin->delete('/posts/{id}',            [AdminController::class, 'deletePost']);
            $admin->get('/users',                    [AdminController::class, 'users']);
            $admin->put('/users/{id}/status',        [AdminController::class, 'changeUserStatus']);
        })->add(JwtMiddleware::class);

    });
};

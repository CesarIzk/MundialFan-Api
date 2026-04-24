<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminController
{
    // ════════════════════════════════════════════════════════════════════════
    // POSTS
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/posts ──────────────────────────────────────────────────
    public function posts(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $params = $request->getQueryParams();
        $q      = trim($params['q']      ?? '');
        $status = trim($params['status'] ?? '');
        $type   = trim($params['type']   ?? '');

        $query = DB::table('posts')
            ->leftJoin('users',      'posts.user_id',     '=', 'users.id')
            ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
            ->select(
                'posts.id',
                'posts.content',
                'posts.content_type',
                'posts.media_path',
                'posts.status',
                'posts.likes',
                'posts.comments_count',
                'posts.created_at',
                DB::raw("JSON_OBJECT('id', users.id, 'name', users.name, 'username', users.username) AS user"),
                DB::raw("JSON_OBJECT('id', categories.id, 'name', categories.name)                   AS category")
            )
            ->orderBy('posts.created_at', 'desc');

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('posts.content', 'like', "%{$q}%")
                   ->orWhere('users.name',  'like', "%{$q}%");
            });
        }

        if ($status && in_array($status, ['public', 'hidden'])) {
            $query->where('posts.status', $status);
        }

        if ($type && in_array($type, ['text', 'image', 'video'])) {
            $query->where('posts.content_type', $type);
        }

        $posts = $query->get()->map(function ($p) {
            $p->user     = json_decode($p->user);
            $p->category = json_decode($p->category);
            $p->likes_count = $p->likes;
            return $p;
        });

        return $this->json($response, $posts);
    }

    // ── PUT /api/admin/posts/{id}/status ─────────────────────────────────────
    public function changePostStatus(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $post = Post::find($args['id']);
        if (!$post) {
            return $this->json($response, ['message' => 'Publicación no encontrada.'], 404);
        }

        $body   = (array) $request->getParsedBody();
        $status = $body['status'] ?? '';

        if (!in_array($status, ['public', 'hidden'])) {
            return $this->json($response, ['message' => 'Estado inválido. Use: public | hidden'], 422);
        }

        $post->status = $status;
        $post->save();

        return $this->json($response, ['message' => "Publicación marcada como '{$status}'."]);
    }

    // ── PUT /api/admin/posts/{id} ────────────────────────────────────────────
    public function updatePost(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $post = Post::find($args['id']);
        if (!$post) {
            return $this->json($response, ['message' => 'Publicación no encontrada.'], 404);
        }

        $body   = (array) $request->getParsedBody();
        $status = $body['status'] ?? null;

        if ($status && in_array($status, ['public', 'hidden'])) {
            $post->status = $status;
        }

        $post->save();
        return $this->json($response, ['message' => 'Publicación actualizada.']);
    }

    // ── DELETE /api/admin/posts/{id} ──────────────────────────────────────────
    public function deletePost(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $post = Post::find($args['id']);
        if (!$post) {
            return $this->json($response, ['message' => 'Publicación no encontrada.'], 404);
        }

        $post->delete();
        return $this->json($response, ['message' => 'Publicación eliminada.']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // USUARIOS
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/users ──────────────────────────────────────────────────
    public function users(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $params = $request->getQueryParams();
        $q      = trim($params['q']      ?? '');
        $role   = trim($params['role']   ?? '');
        $status = trim($params['status'] ?? '');

        $query = DB::table('users')
            ->leftJoin(DB::raw('(SELECT user_id, COUNT(*) AS total_posts FROM posts GROUP BY user_id) AS pc'),
                       'users.id', '=', 'pc.user_id')
            ->select(
                'users.id', 'users.name', 'users.username', 'users.email',
                'users.role', 'users.status', 'users.created_at', 'users.birth_date',
                DB::raw('COALESCE(pc.total_posts, 0) AS total_posts')
            )
            ->orderBy('users.created_at', 'desc');

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('users.name',     'like', "%{$q}%")
                   ->orWhere('users.email',    'like', "%{$q}%")
                   ->orWhere('users.username', 'like', "%{$q}%");
            });
        }

        if ($role && in_array($role, ['admin', 'user'])) {
            $query->where('users.role', $role);
        }

        if ($status && in_array($status, ['active', 'inactive'])) {
            $query->where('users.status', $status);
        }

        return $this->json($response, $query->get());
    }

    // ── GET /api/admin/users/{id} ─────────────────────────────────────────────
    public function showUser(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $user = DB::table('users')
            ->where('id', $args['id'])
            ->select('id', 'name', 'username', 'email', 'role', 'status', 'birth_date', 'created_at')
            ->first();

        if (!$user) {
            return $this->json($response, ['message' => 'Usuario no encontrado.'], 404);
        }

        return $this->json($response, $user);
    }

    // ── POST /api/admin/users ─────────────────────────────────────────────────
    public function createUser(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $body = (array) $request->getParsedBody();

        $name     = trim($body['name']     ?? '');
        $username = trim($body['username'] ?? '');
        $email    = trim($body['email']    ?? '');
        $password = $body['password']      ?? '';
        $role     = $body['role']          ?? 'user';
        $status   = $body['status']        ?? 'active';

        if (!$name || !$username || !$email || !$password) {
            return $this->json($response, ['message' => 'Nombre, usuario, email y contraseña son requeridos.'], 422);
        }

        if (DB::table('users')->where('email', $email)->exists()) {
            return $this->json($response, ['message' => 'El email ya está en uso.'], 409);
        }
        if (DB::table('users')->where('username', $username)->exists()) {
            return $this->json($response, ['message' => 'El nombre de usuario ya existe.'], 409);
        }

        $id = DB::table('users')->insertGetId([
            'name'       => $name,
            'username'   => $username,
            'email'      => $email,
            'password'   => $password,
            'role'       => in_array($role, ['admin', 'user']) ? $role : 'user',
            'status'     => in_array($status, ['active', 'inactive']) ? $status : 'active',
            'birth_date' => $body['birth_date'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->json($response, ['message' => 'Usuario creado.', 'id' => $id], 201);
    }

    // ── PUT /api/admin/users/{id} ─────────────────────────────────────────────
    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $user = User::find($args['id']);
        if (!$user) {
            return $this->json($response, ['message' => 'Usuario no encontrado.'], 404);
        }

        $body = (array) $request->getParsedBody();

        if (isset($body['status'])) {
            if (!in_array($body['status'], ['active', 'inactive'])) {
                return $this->json($response, ['message' => 'Estado inválido.'], 422);
            }
            $user->status = $body['status'];
        }

        foreach (['name', 'username', 'email', 'role', 'birth_date'] as $field) {
            if (isset($body[$field]) && $body[$field] !== '') {
                $user->$field = $body[$field];
            }
        }

        if (!empty($body['password'])) {
            $user->password = $body['password'];
        }

        $user->save();
        return $this->json($response, ['message' => 'Usuario actualizado.']);
    }

    // ── DELETE /api/admin/users/{id} ──────────────────────────────────────────
    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $user = User::find($args['id']);
        if (!$user) {
            return $this->json($response, ['message' => 'Usuario no encontrado.'], 404);
        }

        $user->delete();
        return $this->json($response, ['message' => 'Usuario eliminado.']);
    }

    // ── PUT /api/admin/users/{id}/status ─────────────────────────────────────
    public function changeUserStatus(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $user = User::find($args['id']);
        if (!$user) {
            return $this->json($response, ['message' => 'Usuario no encontrado.'], 404);
        }

        $body   = (array) $request->getParsedBody();
        $status = $body['status'] ?? '';

        if (!in_array($status, ['active', 'inactive'])) {
            return $this->json($response, ['message' => 'Estado inválido. Use: active | inactive'], 422);
        }

        $user->status = $status;
        $user->save();

        return $this->json($response, ['message' => "Usuario marcado como '{$status}'."]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // COMENTARIOS
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/comments ───────────────────────────────────────────────
    public function comments(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $q = trim($request->getQueryParams()['q'] ?? '');

        $query = DB::table('comments')
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->select(
                'comments.id',
                'comments.content',
                'comments.post_id',
                'comments.user_id',
                'comments.created_at',
                DB::raw("JSON_OBJECT('id', users.id, 'name', users.name, 'username', users.username) AS user")
            )
            ->orderBy('comments.created_at', 'desc');

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('comments.content', 'like', "%{$q}%")
                   ->orWhere('users.name',      'like', "%{$q}%");
            });
        }

        $comments = $query->get()->map(function ($c) {
            $c->user = json_decode($c->user);
            return $c;
        });

        return $this->json($response, $comments);
    }

    // ── DELETE /api/admin/comments/{id} ──────────────────────────────────────
    public function deleteComment(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $comment = Comment::find($args['id']);
        if (!$comment) {
            return $this->json($response, ['message' => 'Comentario no encontrado.'], 404);
        }

        DB::table('posts')->where('id', $comment->post_id)->decrement('comments_count');
        $comment->delete();

        return $this->json($response, ['message' => 'Comentario eliminado.']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // CATEGORÍAS
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/categories ─────────────────────────────────────────────
    public function categories(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $cats = DB::table('categories')
            ->leftJoin(DB::raw('(SELECT category_id, COUNT(*) AS posts_count FROM posts GROUP BY category_id) AS pc'),
                       'categories.id', '=', 'pc.category_id')
            ->select('categories.*', DB::raw('COALESCE(pc.posts_count, 0) AS posts_count'))
            ->orderBy('categories.name')
            ->get();

        return $this->json($response, $cats);
    }

    // ── POST /api/admin/categories ────────────────────────────────────────────
    public function createCategory(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $body = (array) $request->getParsedBody();
        $name = trim($body['name'] ?? '');

        if (!$name) {
            return $this->json($response, ['message' => 'El nombre es requerido.'], 422);
        }

        $id = DB::table('categories')->insertGetId([
            'name'        => $name,
            'description' => $body['description'] ?? null,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->json($response, ['message' => 'Categoría creada.', 'id' => $id], 201);
    }

    // ── PUT /api/admin/categories/{id} ────────────────────────────────────────
    public function updateCategory(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $exists = DB::table('categories')->where('id', $args['id'])->exists();
        if (!$exists) {
            return $this->json($response, ['message' => 'Categoría no encontrada.'], 404);
        }

        $body = (array) $request->getParsedBody();
        $name = trim($body['name'] ?? '');

        if (!$name) {
            return $this->json($response, ['message' => 'El nombre es requerido.'], 422);
        }

        DB::table('categories')->where('id', $args['id'])->update([
            'name'        => $name,
            'description' => $body['description'] ?? null,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->json($response, ['message' => 'Categoría actualizada.']);
    }

    // ── DELETE /api/admin/categories/{id} ─────────────────────────────────────
    public function deleteCategory(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $exists = DB::table('categories')->where('id', $args['id'])->exists();
        if (!$exists) {
            return $this->json($response, ['message' => 'Categoría no encontrada.'], 404);
        }

        DB::table('categories')->where('id', $args['id'])->delete();
        return $this->json($response, ['message' => 'Categoría eliminada.']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // PAÍSES
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/countries ──────────────────────────────────────────────
    public function adminCountries(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $q = trim($request->getQueryParams()['q'] ?? '');

        $query = DB::table('countries')->orderBy('name');

        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $countries = $query->get()->map(function ($c) {
            $c->images = DB::table('country_images')->where('country_id', $c->id)->get();
            $c->videos = DB::table('country_videos')->where('country_id', $c->id)->get();
            return $c;
        });

        return $this->json($response, $countries);
    }

    // ── GET /api/admin/countries/{id} ─────────────────────────────────────────
    public function showCountry(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $country = DB::table('countries')->where('id', $args['id'])->first();
        if (!$country) {
            return $this->json($response, ['message' => 'País no encontrado.'], 404);
        }

        $country->images = DB::table('country_images')->where('country_id', $country->id)->get();
        $country->videos = DB::table('country_videos')->where('country_id', $country->id)->get();

        return $this->json($response, $country);
    }

    // ── POST /api/admin/countries ─────────────────────────────────────────────
    public function createCountry(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $body      = (array) $request->getParsedBody();
        $files     = $request->getUploadedFiles();
        $uploadDir = __DIR__ . '/../../public/uploads/countries/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Bandera
        $flagPath = null;
        if (!empty($files['flag']) && $files['flag']->getError() === UPLOAD_ERR_OK) {
            $ext      = strtolower(pathinfo($files['flag']->getClientFilename(), PATHINFO_EXTENSION));
            $filename = 'flag_' . time() . '.' . $ext;
            $files['flag']->moveTo($uploadDir . $filename);
            $flagPath = 'countries/' . $filename;
        }

        $countryId = DB::table('countries')->insertGetId([
            'name'           => trim($body['name']        ?? ''),
            'code'           => strtoupper(trim($body['code'] ?? '')),
            'continent'      => $body['continent']        ?? null,
            'titles'         => $body['titles']           ?? 0,
            'participations' => $body['participations']   ?? 0,
            'coach'          => $body['coach']            ?? null,
            'best_player'    => $body['best_player']      ?? null,
            'flag'           => $flagPath,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        // Imágenes de galería
        if (!empty($files['images'])) {
            foreach ((array) $files['images'] as $img) {
                if ($img->getError() !== UPLOAD_ERR_OK) continue;
                $ext  = strtolower(pathinfo($img->getClientFilename(), PATHINFO_EXTENSION));
                $name = 'img_' . $countryId . '_' . uniqid() . '.' . $ext;
                $img->moveTo($uploadDir . $name);
                DB::table('country_images')->insert([
                    'country_id' => $countryId,
                    'path'       => 'countries/' . $name,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Video URL
        if (!empty($body['video_url'])) {
            DB::table('country_videos')->insert([
                'country_id' => $countryId,
                'url'        => $body['video_url'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->json($response, ['message' => 'País creado.', 'id' => $countryId], 201);
    }

    // ── POST /api/admin/countries/{id} ────────────────────────────────────────
    public function updateCountry(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $country = DB::table('countries')->where('id', $args['id'])->first();
        if (!$country) {
            return $this->json($response, ['message' => 'País no encontrado.'], 404);
        }

        $body      = (array) $request->getParsedBody();
        $files     = $request->getUploadedFiles();
        $uploadDir = __DIR__ . '/../../public/uploads/countries/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $update = [
            'name'           => trim($body['name']        ?? $country->name),
            'code'           => strtoupper(trim($body['code'] ?? $country->code)),
            'continent'      => $body['continent']        ?? $country->continent,
            'titles'         => $body['titles']           ?? $country->titles,
            'participations' => $body['participations']   ?? $country->participations,
            'coach'          => $body['coach']            ?? $country->coach,
            'best_player'    => $body['best_player']      ?? $country->best_player,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        // Nueva bandera
        if (!empty($files['flag']) && $files['flag']->getError() === UPLOAD_ERR_OK) {
            $ext      = strtolower(pathinfo($files['flag']->getClientFilename(), PATHINFO_EXTENSION));
            $filename = 'flag_' . $args['id'] . '_' . time() . '.' . $ext;
            $files['flag']->moveTo($uploadDir . $filename);
            $update['flag'] = 'countries/' . $filename;
        }

        DB::table('countries')->where('id', $args['id'])->update($update);

        // Nuevas imágenes
        if (!empty($files['images'])) {
            foreach ((array) $files['images'] as $img) {
                if ($img->getError() !== UPLOAD_ERR_OK) continue;
                $ext  = strtolower(pathinfo($img->getClientFilename(), PATHINFO_EXTENSION));
                $name = 'img_' . $args['id'] . '_' . uniqid() . '.' . $ext;
                $img->moveTo($uploadDir . $name);
                DB::table('country_images')->insert([
                    'country_id' => $args['id'],
                    'path'       => 'countries/' . $name,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Actualizar video URL
        if (isset($body['video_url'])) {
            DB::table('country_videos')->where('country_id', $args['id'])->delete();
            if ($body['video_url']) {
                DB::table('country_videos')->insert([
                    'country_id' => $args['id'],
                    'url'        => $body['video_url'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return $this->json($response, ['message' => 'País actualizado.']);
    }

    // ── DELETE /api/admin/countries/{id} ──────────────────────────────────────
    public function deleteCountry(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $exists = DB::table('countries')->where('id', $args['id'])->exists();
        if (!$exists) {
            return $this->json($response, ['message' => 'País no encontrado.'], 404);
        }

        DB::table('country_images')->where('country_id', $args['id'])->delete();
        DB::table('country_videos')->where('country_id', $args['id'])->delete();
        DB::table('countries')->where('id', $args['id'])->delete();

        return $this->json($response, ['message' => 'País eliminado.']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // JUGADORES DESTACADOS
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/featured-players ──────────────────────────────────────
    public function featuredPlayers(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $q = trim($request->getQueryParams()['q'] ?? '');

        $query = DB::table('featured_players')
            ->select('featured_players.*')
            ->orderBy('featured_players.name');

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('featured_players.name', 'like', "%{$q}%")
                   ->orWhere('featured_players.country', 'like', "%{$q}%");
            });
        }

        $players = $query->get();

        return $this->json($response, $players);
    }

    // ── GET /api/admin/featured-players/{id} ──────────────────────────────────
    public function showFeaturedPlayer(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $player = DB::table('featured_players')->where('id', $args['id'])->first();
        if (!$player) {
            return $this->json($response, ['message' => 'Jugador no encontrado.'], 404);
        }

        return $this->json($response, $player);
    }

    // ── POST /api/admin/featured-players ──────────────────────────────────────
    public function createFeaturedPlayer(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $body = (array) $request->getParsedBody();

        $id = DB::table('featured_players')->insertGetId([
            'name'         => trim($body['name']        ?? ''),
            'country'      => $body['country']          ?? null,
            'achievements' => $body['achievements']     ?? null,
            'photo'        => $body['photo']            ?? null,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return $this->json($response, ['message' => 'Jugador creado.', 'id' => $id], 201);
    }

    // ── PUT /api/admin/featured-players/{id} ──────────────────────────────────
    public function updateFeaturedPlayer(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $exists = DB::table('featured_players')->where('id', $args['id'])->exists();
        if (!$exists) {
            return $this->json($response, ['message' => 'Jugador no encontrado.'], 404);
        }

        $body = (array) $request->getParsedBody();

        DB::table('featured_players')->where('id', $args['id'])->update([
            'name'         => trim($body['name']     ?? ''),
            'country'      => $body['country']       ?? null,
            'achievements' => $body['achievements']  ?? null,
            'photo'        => $body['photo']         ?? null,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return $this->json($response, ['message' => 'Jugador actualizado.']);
    }

    // ── DELETE /api/admin/featured-players/{id} ───────────────────────────────
    public function deleteFeaturedPlayer(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $exists = DB::table('featured_players')->where('id', $args['id'])->exists();
        if (!$exists) {
            return $this->json($response, ['message' => 'Jugador no encontrado.'], 404);
        }

        DB::table('featured_players')->where('id', $args['id'])->delete();
        return $this->json($response, ['message' => 'Jugador eliminado.']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // EQUIPOS EXITOSOS
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/successful-teams ──────────────────────────────────────
    public function successfulTeams(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $q = trim($request->getQueryParams()['q'] ?? '');

        $query = DB::table('successful_teams')
            ->orderBy('titles', 'desc');

        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $teams = $query->get();

        return $this->json($response, $teams);
    }

    // ── GET /api/admin/successful-teams/{id} ──────────────────────────────────
    public function showSuccessfulTeam(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $team = DB::table('successful_teams')->where('id', $args['id'])->first();
        if (!$team) {
            return $this->json($response, ['message' => 'Equipo no encontrado.'], 404);
        }

        return $this->json($response, $team);
    }

    // ── POST /api/admin/successful-teams ──────────────────────────────────────
    public function createSuccessfulTeam(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $body = (array) $request->getParsedBody();

        $id = DB::table('successful_teams')->insertGetId([
            'name'          => trim($body['name']      ?? ''),
            'flag'          => $body['flag']           ?? null,
            'titles'        => $body['titles']         ?? 0,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return $this->json($response, ['message' => 'Equipo creado.', 'id' => $id], 201);
    }

    // ── PUT /api/admin/successful-teams/{id} ──────────────────────────────────
    public function updateSuccessfulTeam(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $exists = DB::table('successful_teams')->where('id', $args['id'])->exists();
        if (!$exists) {
            return $this->json($response, ['message' => 'Equipo no encontrado.'], 404);
        }

        $body = (array) $request->getParsedBody();

        DB::table('successful_teams')->where('id', $args['id'])->update([
            'name'          => trim($body['name']      ?? ''),
            'flag'          => $body['flag']           ?? null,
            'titles'        => $body['titles']         ?? 0,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return $this->json($response, ['message' => 'Equipo actualizado.']);
    }

    // ── DELETE /api/admin/successful-teams/{id} ───────────────────────────────
    public function deleteSuccessfulTeam(Request $request, Response $response, array $args): Response
    {
        $this->requireAdmin($request);

        $exists = DB::table('successful_teams')->where('id', $args['id'])->exists();
        if (!$exists) {
            return $this->json($response, ['message' => 'Equipo no encontrado.'], 404);
        }

        DB::table('successful_teams')->where('id', $args['id'])->delete();
        return $this->json($response, ['message' => 'Equipo eliminado.']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // CREAR ADMIN
    // ════════════════════════════════════════════════════════════════════════

    // ── POST /api/admin/create-admin ──────────────────────────────────────────
    public function createAdmin(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $authUser    = $request->getAttribute('auth_user');
        $currentUser = User::find($authUser['sub']);

        $body          = (array) $request->getParsedBody();
        $adminPassword = $body['confirm_admin_password'] ?? '';

        if ($currentUser->password !== $adminPassword) {
            return $this->json($response, ['message' => 'Tu contraseña de confirmación es incorrecta.'], 403);
        }

        $name     = trim($body['name']     ?? '');
        $username = trim($body['username'] ?? '');
        $email    = trim($body['email']    ?? '');
        $password = $body['password']      ?? '';

        if (!$name || !$username || !$email || !$password) {
            return $this->json($response, ['message' => 'Todos los campos son requeridos.'], 422);
        }

        if (DB::table('users')->where('email', $email)->exists()) {
            return $this->json($response, ['message' => 'El email ya está registrado.'], 409);
        }

        if (DB::table('users')->where('username', $username)->exists()) {
            return $this->json($response, ['message' => 'El nombre de usuario ya existe.'], 409);
        }

        $id = DB::table('users')->insertGetId([
            'name'       => $name,
            'username'   => $username,
            'email'      => $email,
            'password'   => $password,
            'role'       => 'admin',
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->json($response, ['message' => 'Administrador creado exitosamente.', 'id' => $id], 201);
    }

    // ════════════════════════════════════════════════════════════════════════
    // DASHBOARD
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/dashboard/stats ───────────────────────────────────────
    public function dashboardStats(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $today = date('Y-m-d');

        $stats = [
            'total_users'   => DB::table('users')->count(),
            'total_posts'   => DB::table('posts')->count(),
            'active_users'  => DB::table('users')
                ->where('status', 'active')
                ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->count(),
            'hidden_posts'  => DB::table('posts')->where('status', 'hidden')->count(),
            'total_comments'=> DB::table('comments')->count(),
            'total_likes'   => DB::table('posts')->sum('likes'),
            'users_today'   => DB::table('users')->whereDate('created_at', $today)->count(),
            'posts_today'   => DB::table('posts')->whereDate('created_at', $today)->count(),
        ];

        return $this->json($response, $stats);
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORTES
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/reports/metrics ────────────────────────────────────────
    public function reportMetrics(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $params    = $request->getQueryParams();
        $dateStart = $params['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
        $dateEnd   = $params['fecha_fin']    ?? date('Y-m-d');

        $totalUsers = DB::table('users')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateStart, $dateEnd])
            ->count();

        $totalPosts = DB::table('posts')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateStart, $dateEnd])
            ->count();

        $totalLikes = DB::table('posts')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateStart, $dateEnd])
            ->sum('likes');

        $avgLikes = $totalPosts > 0 ? round($totalLikes / $totalPosts, 1) : 0;

        $days      = max(1, (int)((strtotime($dateEnd) - strtotime($dateStart)) / 86400));
        $prevStart = date('Y-m-d', strtotime($dateStart) - $days * 86400);
        $prevEnd   = date('Y-m-d', strtotime($dateStart) - 86400);

        $prevUsers = DB::table('users')
            ->whereBetween(DB::raw('DATE(created_at)'), [$prevStart, $prevEnd])
            ->count();

        $prevPosts = DB::table('posts')
            ->whereBetween(DB::raw('DATE(created_at)'), [$prevStart, $prevEnd])
            ->count();

        $usersPct = $prevUsers > 0 ? round((($totalUsers - $prevUsers) / $prevUsers) * 100, 1) : 0;
        $postsPct = $prevPosts > 0 ? round((($totalPosts - $prevPosts) / $prevPosts) * 100, 1) : 0;

        return $this->json($response, [
            'total_users'  => $totalUsers,
            'total_posts'  => $totalPosts,
            'total_likes'  => $totalLikes,
            'likes_avg'    => $avgLikes,
            'users_pct'    => $usersPct,
            'posts_pct'    => $postsPct,
        ]);
    }

    // ── GET /api/admin/reports/daily ─────────────────────────────────────────
    public function reportDaily(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $params    = $request->getQueryParams();
        $dateStart = $params['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
        $dateEnd   = $params['fecha_fin']    ?? date('Y-m-d');
        $limit     = (int) ($params['limit'] ?? 30);

        $userRows = DB::table('users')
            ->selectRaw('DATE(created_at) AS date, COUNT(*) AS new_users')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateStart, $dateEnd])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('date');

        $postRows = DB::table('posts')
            ->selectRaw('DATE(created_at) AS date, COUNT(*) AS new_posts, SUM(likes) AS total_likes')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateStart, $dateEnd])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('date');

        $dates = collect();
        $current = strtotime($dateStart);
        $end     = strtotime($dateEnd);

        while ($current <= $end) {
            $d = date('Y-m-d', $current);
            $dates->push([
                'date'         => $d,
                'new_users'    => $userRows->get($d)->new_users    ?? 0,
                'new_posts'    => $postRows->get($d)->new_posts    ?? 0,
                'total_likes'  => $postRows->get($d)->total_likes  ?? 0,
                'active_users' => $userRows->get($d)->new_users    ?? 0,
            ]);
            $current = strtotime('+1 day', $current);
        }

        $result = $dates->sortByDesc('date')->take($limit)->values();

        return $this->json($response, $result);
    }

    // ── GET /api/admin/reports/top-users ─────────────────────────────────────
    public function reportTopUsers(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $params    = $request->getQueryParams();
        $limit     = (int) ($params['limit'] ?? 10);
        $dateStart = $params['fecha_inicio'] ?? null;
        $dateEnd   = $params['fecha_fin']    ?? null;

        $query = DB::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.username',
                DB::raw('COUNT(posts.id) AS posts'),
                DB::raw('COALESCE(AVG(posts.likes), 0) AS avg_interactions')
            )
            ->groupBy('users.id', 'users.name', 'users.username')
            ->orderByDesc('posts')
            ->limit($limit);

        if ($dateStart && $dateEnd) {
            $query->whereBetween(DB::raw('DATE(posts.created_at)'), [$dateStart, $dateEnd]);
        }

        return $this->json($response, $query->get());
    }

    // ── GET /api/admin/reports/top-posts ─────────────────────────────────────
    public function reportTopPosts(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $params    = $request->getQueryParams();
        $limit     = (int) ($params['limit'] ?? 10);
        $dateStart = $params['fecha_inicio'] ?? null;
        $dateEnd   = $params['fecha_fin']    ?? null;

        $query = DB::table('posts')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->select(
                'posts.id',
                'posts.content',
                'posts.likes',
                'posts.comments_count',
                DB::raw('users.username AS author')
            )
            ->orderByDesc('posts.likes')
            ->limit($limit);

        if ($dateStart && $dateEnd) {
            $query->whereBetween(DB::raw('DATE(posts.created_at)'), [$dateStart, $dateEnd]);
        }

        $posts = $query->get()->map(function ($p) {
            $p->likes_count = $p->likes;
            return $p;
        });

        return $this->json($response, $posts);
    }

    // ── GET /api/admin/reports/users-by-country ───────────────────────────────
    public function reportUsersByCountry(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $params    = $request->getQueryParams();
        $dateStart = $params['fecha_inicio'] ?? null;
        $dateEnd   = $params['fecha_fin']    ?? null;

        $query = DB::table('users')
            ->leftJoin('countries', 'users.country', '=', 'countries.name')
            ->selectRaw('COALESCE(users.country, "Sin país") AS country, COUNT(*) AS total')
            ->groupBy('users.country')
            ->orderByDesc('total')
            ->limit(10);

        if ($dateStart && $dateEnd) {
            $query->whereBetween(DB::raw('DATE(users.created_at)'), [$dateStart, $dateEnd]);
        }

        return $this->json($response, $query->get());
    }

    // ── GET /api/admin/reports/content-types ──────────────────────────────────
    public function reportContentTypes(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        $params    = $request->getQueryParams();
        $dateStart = $params['fecha_inicio'] ?? null;
        $dateEnd   = $params['fecha_fin']    ?? null;

        $query = DB::table('posts')
            ->selectRaw('content_type AS type, COUNT(*) AS total')
            ->groupBy('content_type');

        if ($dateStart && $dateEnd) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$dateStart, $dateEnd]);
        }

        return $this->json($response, $query->get());
    }

    // ════════════════════════════════════════════════════════════════════════
    // LOGS
    // ════════════════════════════════════════════════════════════════════════

    // ── GET /api/admin/logs ───────────────────────────────────────────────────
    public function getLogs(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        try {
            $logs = DB::table('admin_logs')
                ->orderBy('created_at', 'desc')
                ->limit(500)
                ->get()
                ->map(function ($l) {
                    return [
                        'level'     => $l->level     ?? 'info',
                        'message'   => $l->message   ?? '',
                        'timestamp' => $l->created_at ?? '',
                    ];
                });
            return $this->json($response, $logs);
        } catch (\Exception $e) {
            $logFile = __DIR__ . '/../../logs/app.log';
            if (!file_exists($logFile)) {
                return $this->json($response, []);
            }

            $lines = array_filter(array_map('trim', file($logFile)));
            $logs  = [];

            foreach (array_reverse(array_values($lines)) as $line) {
                preg_match('/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2})\].*\.(INFO|WARNING|ERROR|DEBUG): (.+)/', $line, $m);
                $logs[] = [
                    'level'     => strtolower($m[2] ?? 'info'),
                    'message'   => $m[3]            ?? $line,
                    'timestamp' => $m[1]            ?? '',
                ];
                if (count($logs) >= 500) break;
            }

            return $this->json($response, $logs);
        }
    }

    // ── DELETE /api/admin/logs ────────────────────────────────────────────────
    public function clearLogs(Request $request, Response $response): Response
    {
        $this->requireAdmin($request);

        try {
            DB::table('admin_logs')->truncate();
        } catch (\Exception $e) {
            $logFile = __DIR__ . '/../../logs/app.log';
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }
        }

        return $this->json($response, ['message' => 'Logs eliminados.']);
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
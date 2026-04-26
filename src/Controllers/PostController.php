<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use Cloudinary\Cloudinary;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostController
{
    private ?Cloudinary $cloudinary = null;

    private function getCloudinary(): Cloudinary
    {
        if ($this->cloudinary !== null) {
            return $this->cloudinary;
        }

        $cloudinaryUrl = getenv('CLOUDINARY_URL') ?: ($_SERVER['CLOUDINARY_URL'] ?? '');

        if (!empty($cloudinaryUrl)) {
            $this->cloudinary = new Cloudinary($cloudinaryUrl);
            return $this->cloudinary;
        }

        $cloudName = getenv('CLOUDINARY_CLOUD_NAME') ?: ($_SERVER['CLOUDINARY_CLOUD_NAME'] ?? '');
        $apiKey    = getenv('CLOUDINARY_API_KEY')    ?: ($_SERVER['CLOUDINARY_API_KEY']    ?? '');
        $apiSecret = getenv('CLOUDINARY_API_SECRET') ?: ($_SERVER['CLOUDINARY_API_SECRET'] ?? '');

        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key'    => $apiKey,
                'api_secret' => $apiSecret,
            ],
        ]);

        return $this->cloudinary;
    }

    // ── GET /api/posts ────────────────────────────────────────────────────────
    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $q      = trim($params['q']     ?? '');
        $order  = trim($params['orden'] ?? 'reciente');

        $posts = $q ? Post::search($q) : Post::feed();

        if ($order === 'populares') {
            $posts = $posts->sortByDesc('likes')->values();
        }

        $authUser = $request->getAttribute('auth_user');
        $userId   = $authUser['sub'] ?? null;

        $result = $posts->map(function ($post) use ($userId) {
            $arr                = $post->toArray();
            $arr['liked']       = $userId ? Like::exists($post->id, $userId) : false;
            $arr['likes_count'] = $post->likes;
            $arr['comments']    = Comment::byPost($post->id)
                ->map(fn($c) => [
                    'id'      => $c->id,
                    'content' => $c->content,
                    'user'    => [
                        'id'              => $c->user?->id,
                        'name'            => $c->user?->name,
                        'profile_picture' => $c->user?->profile_picture,
                    ],
                    'created_at' => $c->created_at,
                ]);
            
            // Asegurar que media_path sea URL completa
            return $this->formatPostResponse($arr);
        });

        return $this->json($response, $result);
    }

    // ── GET /api/posts/{id} ───────────────────────────────────────────────────
    public function show(Request $request, Response $response, array $args): Response
    {
        $post = Post::with(['user:id,name,username,profile_picture', 'category:id,name'])
            ->find($args['id']);

        if (!$post) {
            return $this->json($response, ['message' => 'Publicación no encontrada.'], 404);
        }

        $authUser        = $request->getAttribute('auth_user');
        $arr             = $post->toArray();
        $arr['liked']    = $authUser ? Like::exists($post->id, $authUser['sub']) : false;
        $arr['comments'] = Comment::byPost($post->id);

        return $this->json($response, $this->formatPostResponse($arr));
    }

    // ── POST /api/posts ───────────────────────────────────────────────────────
    public function store(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $body     = (array) $request->getParsedBody();
        $files    = $request->getUploadedFiles();

        $content = trim($body['content'] ?? '');

        if (empty($content)) {
            return $this->json($response, ['message' => 'El contenido no puede estar vacío.'], 422);
        }

        $mediaPath   = null;
        $contentType = 'text';

        if (!empty($files['media'])) {
            $file = $files['media'];

            if ($file->getError() === UPLOAD_ERR_OK) {
                $ext     = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov'];

                if (!in_array($ext, $allowed)) {
                    return $this->json($response, ['message' => 'Tipo de archivo no permitido.'], 422);
                }

                $contentType = in_array($ext, ['mp4', 'mov']) ? 'video' : 'image';
                $folder      = $contentType === 'video' ? 'mundialfan/videos' : 'mundialfan/imagenes';

                // Obtener la ruta temporal que PHP ya creó — igual que $file['tmp_name'] en el proyecto que funciona
                $tmpName = $file->getStream()->getMetadata('uri');

                $uploadOptions = [
                    'folder'        => $folder,
                    'resource_type' => $contentType,
                ];

                if ($contentType === 'video') {
                    $uploadOptions['video_codec'] = 'auto';
                    $uploadOptions['quality']     = 'auto';
                }

                try {
                    $uploadResult = $this->getCloudinary()->uploadApi()->upload($tmpName, $uploadOptions);
                    $mediaPath    = $uploadResult['secure_url'];
                } catch (\Exception $e) {
                    return $this->json($response, ['message' => 'Error al subir el archivo: ' . $e->getMessage()], 500);
                }
            }
        }

        $post = Post::create([
            'user_id'        => $authUser['sub'],
            'category_id'    => $body['category_id'] ?? null,
            'content'        => $content,
            'content_type'   => $contentType,
            'media_path'     => $mediaPath,
            'status'         => 'public',
            'likes'          => 0,
            'comments_count' => 0,
        ]);

        $post->load('user:id,name,profile_picture');
        $arr = $post->toArray();
        
        return $this->json($response, $this->formatPostResponse($arr), 201);
    }

    // ── DELETE /api/posts/{id} ────────────────────────────────────────────────
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $post     = Post::find($args['id']);

        if (!$post) {
            return $this->json($response, ['message' => 'Publicación no encontrada.'], 404);
        }

        if ((int)$post->user_id !== (int)$authUser['sub'] && $authUser['role'] !== 'admin') {
            return $this->json($response, ['message' => 'No autorizado.'], 403);
        }

        if ($post->media_path) {
            $this->deleteFromCloudinary($post->media_path, $post->content_type);
        }

        $post->delete();

        return $this->json($response, ['message' => 'Publicación eliminada.']);
    }

    // ── POST /api/posts/{id}/like ─────────────────────────────────────────────
    public function toggleLike(Request $request, Response $response, array $args): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $post     = Post::find($args['id']);

        if (!$post) {
            return $this->json($response, ['message' => 'Publicación no encontrada.'], 404);
        }

        $liked = Like::toggle($post->id, $authUser['sub']);
        $post->refresh();

        if ($liked) {
            Notification::notify(
                userId:     (int) $post->user_id,
                type:       'post_like',
                actorId:    (int) $authUser['sub'],
                entityId:   $post->id,
                entityType: 'post'
            );
        }

        return $this->json($response, [
            'liked'       => $liked,
            'likes_count' => $post->likes,
        ]);
    }

    // ── GET /api/posts/{id}/comments ─────────────────────────────────────────
    public function comments(Request $request, Response $response, array $args): Response
    {
        $comments = Comment::byPost($args['id']);
        return $this->json($response, $comments);
    }

    // ── POST /api/posts/{id}/comments ────────────────────────────────────────
    public function storeComment(Request $request, Response $response, array $args): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $body     = (array) $request->getParsedBody();
        $content  = trim($body['content'] ?? '');

        if (empty($content)) {
            return $this->json($response, ['message' => 'El comentario no puede estar vacío.'], 422);
        }

        $comment = Comment::createAndCount($args['id'], $authUser['sub'], $content);
        $comment->load('user:id,name,profile_picture');

        $post = Post::find($args['id']);
        if ($post) {
            Notification::notify(
                userId:     (int) $post->user_id,
                type:       'post_comment',
                actorId:    (int) $authUser['sub'],
                entityId:   $post->id,
                entityType: 'post'
            );
        }

        return $this->json($response, $comment, 201);
    }

    // ── DELETE /api/comments/{id} ─────────────────────────────────────────────
    public function destroyComment(Request $request, Response $response, array $args): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $comment  = Comment::find($args['id']);

        if (!$comment) {
            return $this->json($response, ['message' => 'Comentario no encontrado.'], 404);
        }

        if ((int)$comment->user_id !== (int)$authUser['sub'] && $authUser['role'] !== 'admin') {
            return $this->json($response, ['message' => 'No autorizado.'], 403);
        }

        $comment->deleteAndCount();

        return $this->json($response, ['message' => 'Comentario eliminado.']);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Convertir media_path a URL completa de Cloudinary si es necesario
     */
    private function ensureCloudinaryUrl(?string $mediaPath, string $contentType = 'image'): ?string
    {
        if (!$mediaPath) {
            return null;
        }
        
        // Si ya es una URL completa, devolverla
        if (strpos($mediaPath, 'http://') === 0 || strpos($mediaPath, 'https://') === 0) {
            return $mediaPath;
        }
        
        // Si es un public_id de Cloudinary, construir la URL
        if (strpos($mediaPath, 'mundialfan/') === 0) {
            $cloudName     = getenv('CLOUDINARY_CLOUD_NAME') ?: ($_SERVER['CLOUDINARY_CLOUD_NAME'] ?? 'dposwljhe');
            $resourceType  = $contentType === 'video' ? 'video' : 'image';
            return "https://res.cloudinary.com/{$cloudName}/{$resourceType}/upload/{$mediaPath}";
        }
        
        return $mediaPath;
    }

    /**
     * Formatear array de post para incluir URL completa
     */
    private function formatPostResponse(array $post): array
    {
        if (!empty($post['media_path'])) {
            $post['media_path'] = $this->ensureCloudinaryUrl($post['media_path'], $post['content_type'] ?? 'image');
        }
        return $post;
    }

    private function deleteFromCloudinary(string $url, string $contentType): void
    {
        try {
            $pattern = '/\/upload\/(?:v\d+\/)?(.+?)(?:\.[a-z0-9]+)?$/i';

            if (preg_match($pattern, $url, $matches)) {
                $publicId     = $matches[1];
                $resourceType = $contentType === 'video' ? 'video' : 'image';

                $this->getCloudinary()->uploadApi()->destroy($publicId, [
                    'resource_type' => $resourceType,
                ]);
            }
        } catch (\Exception $e) {
            error_log('Cloudinary delete error: ' . $e->getMessage());
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
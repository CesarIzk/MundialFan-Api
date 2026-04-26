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
    private Cloudinary $cloudinary;

    public function __construct()
    {
        // Usar CLOUDINARY_URL si está disponible (forma recomendada)
        $cloudinaryUrl = getenv('CLOUDINARY_URL') ?: ($_ENV['CLOUDINARY_URL'] ?? null);
        
        if ($cloudinaryUrl) {
            $this->cloudinary = new Cloudinary($cloudinaryUrl);
        } else {
            // Fallback a configuración manual si es necesario
            $config = [
                'cloud' => [
                    'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME') ?: ($_ENV['CLOUDINARY_CLOUD_NAME'] ?? ''),
                    'api_key'    => getenv('CLOUDINARY_API_KEY')    ?: ($_ENV['CLOUDINARY_API_KEY']    ?? ''),
                    'api_secret' => getenv('CLOUDINARY_API_SECRET') ?: ($_ENV['CLOUDINARY_API_SECRET'] ?? ''),
                ],
            ];
            
            // Validar que tengamos al menos cloud_name
            if (empty($config['cloud']['cloud_name'])) {
                throw new \Exception('Cloudinary no está configurado. Establece CLOUDINARY_URL o las variables CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY y CLOUDINARY_API_SECRET');
            }
            
            $this->cloudinary = new Cloudinary($config);
        }
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
            return $arr;
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

        return $this->json($response, $arr);
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

                try {
                    $contentType = in_array($ext, ['mp4', 'mov']) ? 'video' : 'image';
                    $folder      = $contentType === 'video' ? 'mundialfan/videos' : 'mundialfan/images';

                    // Obtener la ruta temporal del archivo
                    $tempPath = $file->getStream()->getMetadata('uri');

                    $uploadOptions = [
                        'folder'        => $folder,
                        'resource_type' => $contentType,
                        'public_id'     => 'post_' . uniqid('', true),
                    ];

                    if ($contentType === 'video') {
                        $uploadOptions['video_codec'] = 'auto';
                        $uploadOptions['quality']     = 'auto';
                    }

                    $uploadResult = $this->cloudinary->uploadApi()->upload($tempPath, $uploadOptions);
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

        return $this->json($response, $post->load('user:id,name,profile_picture'), 201);
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
     * Elimina un recurso de Cloudinary a partir de su secure_url.
     * URL ejemplo: https://res.cloudinary.com/CLOUD/image/upload/v123/mundialfan/images/post_abc.jpg
     */
    private function deleteFromCloudinary(string $url, string $contentType): void
    {
        try {
            $pattern = '/\/upload\/(?:v\d+\/)?(.+?)(?:\.[a-z0-9]+)?$/i';

            if (preg_match($pattern, $url, $matches)) {
                $publicId     = $matches[1];
                $resourceType = $contentType === 'video' ? 'video' : 'image';

                $this->cloudinary->uploadApi()->destroy($publicId, [
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
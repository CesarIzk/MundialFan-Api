<?php

namespace App\Controllers;

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\User;
use App\Models\Post;
use App\Models\Notification;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    // ── GET /api/users?q=... ──────────────────────────────────────────────────
    public function search(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $q        = trim($request->getQueryParams()['q'] ?? '');

        $users = User::search($q, $authUser['sub']);

        return $this->json($response, $users);
    }

    // ── GET /api/users/{id} ───────────────────────────────────────────────────
    public function show(Request $request, Response $response, array $args): Response
    {
        $user = User::find($args['id']);

        if (!$user) {
            return $this->json($response, ['message' => 'Usuario no encontrado.'], 404);
        }

        $posts = Post::byUser($user->id);

        return $this->json($response, [
            'user'  => $user->makeHidden(['password']),
            'posts' => $posts,
        ]);
    }

    // ── PUT /api/users/me ─────────────────────────────────────────────────────
    public function updateProfile(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $user     = User::find($authUser['sub']);
        $body     = (array) $request->getParsedBody();

        $allowed = ['name', 'username', 'bio', 'birth_date', 'gender', 'country', 'city'];

        foreach ($allowed as $field) {
            if (isset($body[$field])) {
                $user->$field = trim($body[$field]);
            }
        }

        // Validar que el username no esté tomado por otro usuario
        if (isset($body['username'])) {
            $existing = User::findByUsername($body['username']);
            if ($existing && $existing->id !== $user->id) {
                return $this->json($response, ['message' => 'El nombre de usuario ya está en uso.'], 409);
            }
        }

        $user->save();

        return $this->json($response, $user->makeHidden(['password']));
    }

    // ── POST /api/users/me/avatar ─────────────────────────────────────────────
    public function updateAvatar(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $user     = User::find($authUser['sub']);
        $files    = $request->getUploadedFiles();

        if (empty($files['avatar']) || $files['avatar']->getError() !== UPLOAD_ERR_OK) {
            return $this->json($response, ['message' => 'No se recibió ningún archivo.'], 422);
        }

        $file    = $files['avatar'];
        $ext     = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            return $this->json($response, ['message' => 'Formato de imagen no permitido.'], 422);
        }

        $filename  = 'avatar_' . $user->id . '_' . time() . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/uploads/avatars/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file->moveTo($uploadDir . $filename);
        $user->profile_picture = 'uploads/avatars/' . $filename;
        $user->save();

        return $this->json($response, [
            'message'         => 'Avatar actualizado.',
            'profile_picture' => $user->profile_picture,
        ]);
    }

    // ── POST /api/users/me/cover ──────────────────────────────────────────────
    public function updateCover(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $user     = User::find($authUser['sub']);
        $files    = $request->getUploadedFiles();

        if (empty($files['cover']) || $files['cover']->getError() !== UPLOAD_ERR_OK) {
            return $this->json($response, ['message' => 'No se recibió ningún archivo.'], 422);
        }

        $file    = $files['cover'];
        $ext     = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            return $this->json($response, ['message' => 'Formato de imagen no permitido.'], 422);
        }

        $filename  = 'cover_' . $user->id . '_' . time() . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/uploads/covers/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file->moveTo($uploadDir . $filename);
        $user->cover_picture = 'uploads/covers/' . $filename;
        $user->save();

        return $this->json($response, [
            'message'       => 'Portada actualizada.',
            'cover_picture' => $user->cover_picture,
        ]);
    }

    // ── PUT /api/users/me/password ────────────────────────────────────────────
    public function updatePassword(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $user     = User::find($authUser['sub']);
        $body     = (array) $request->getParsedBody();

        $current = $body['current_password'] ?? '';
        $new     = $body['new_password']     ?? '';

        if ($user->password !== $current) {
            return $this->json($response, ['message' => 'La contraseña actual es incorrecta.'], 422);
        }

        if (strlen($new) < 6) {
            return $this->json($response, ['message' => 'La nueva contraseña debe tener al menos 6 caracteres.'], 422);
        }

        $user->password = $new;
        $user->save();

        return $this->json($response, ['message' => 'Contraseña actualizada.']);
    }

    // ── DELETE /api/users/me ──────────────────────────────────────────────────
    public function deactivate(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $user     = User::find($authUser['sub']);

        $user->status = 'inactive';
        $user->save();

        return $this->json($response, ['message' => 'Cuenta desactivada.']);
    }

    // ── GET /api/users/me/friends ─────────────────────────────────────────────
    public function getFriends(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $myId     = $authUser['sub'];

        $friendsSent = DB::table('friendships')
            ->join('users', 'friendships.friend_id', '=', 'users.id')
            ->where('friendships.user_id', $myId)
            ->where('friendships.status', 'accepted')
            ->select('users.id', 'users.name', 'users.username', 'users.profile_picture', 'users.cover_picture', 'users.country');

        $friendsReceived = DB::table('friendships')
            ->join('users', 'friendships.user_id', '=', 'users.id')
            ->where('friendships.friend_id', $myId)
            ->where('friendships.status', 'accepted')
            ->select('users.id', 'users.name', 'users.username', 'users.profile_picture', 'users.cover_picture', 'users.country');

        $friends = $friendsSent->union($friendsReceived)->get();

        return $this->json($response, $friends);
    }

    // ── GET /api/users/me/requests ────────────────────────────────────────────
    public function getRequests(Request $request, Response $response): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $myId     = $authUser['sub'];

        // Obtener las solicitudes pendientes donde el receptor soy yo
        $requests = DB::table('friendships')
            ->join('users', 'friendships.user_id', '=', 'users.id')
            ->where('friendships.friend_id', $myId)
            ->where('friendships.status', 'pending')
            ->select('users.id', 'users.name', 'users.username', 'users.profile_picture', 'users.cover_picture', 'users.country')
            ->get();

        return $this->json($response, $requests);
    }

    // ── POST /api/users/me/requests/{id} (Enviar Solicitud) ───────────────────
    public function sendRequest(Request $request, Response $response, array $args): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $myId     = $authUser['sub'];
        $friendId = $args['id'];

        $exists = DB::table('friendships')->where('user_id', $myId)->where('friend_id', $friendId)->exists();
        
        if (!$exists) {
            DB::table('friendships')->insert([
                'user_id'   => $myId,
                'friend_id' => $friendId,
                'status'    => 'pending'
            ]);

            Notification::notify(
                userId:     (int) $friendId,
                type:       'friend_request',
                actorId:    (int) $myId,
                entityId:   (int) $myId,
                entityType: 'user'
            );
        }

        return $this->json($response, ['message' => 'Solicitud enviada.']);
    }

    // ── POST /api/users/me/requests/{id}/accept ─────────────────────────────
    public function acceptRequest(Request $request, Response $response, array $args): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $myId     = $authUser['sub'];
        $senderId = $args['id'];

        DB::table('friendships')
            ->where('friend_id', $myId)
            ->where('user_id', $senderId)
            ->update(['status' => 'accepted']);

        Notification::notify(
            userId:     (int) $senderId,
            type:       'friend_accepted',
            actorId:    (int) $myId,
            entityId:   (int) $myId,
            entityType: 'user'
        );

        return $this->json($response, ['message' => 'Solicitud aceptada.']);
    }

    // ── POST /api/users/me/requests/{id}/decline ────────────────────────────
    public function declineRequest(Request $request, Response $response, array $args): Response
    {
        $authUser = $request->getAttribute('auth_user');
        $myId     = $authUser['sub'];
        $senderId = $args['id'];

        DB::table('friendships')
            ->where('friend_id', $myId)
            ->where('user_id', $senderId)
            ->delete();

        return $this->json($response, ['message' => 'Solicitud rechazada.']);
    }

    // ── GET /api/users/me/chats ───────────────────────────────────────────────
    public function getChats(Request $request, Response $response): Response
    {
        $myId  = $request->getAttribute('auth_user')['sub'];
        
        $chats = DB::table('vw_chat_sidebar')
            ->where('owner_id', $myId)
            ->orderBy('last_message_date', 'desc')
            ->get();

        return $this->json($response, $chats);
    }

    // ── GET /api/users/me/chats/{id} ──────────────────────────────────────────
    public function getMessages(Request $request, Response $response, array $args): Response
    {
        $myId     = $request->getAttribute('auth_user')['sub'];
        $friendId = $args['id'];
        
        DB::table('messages')
            ->where('sender_id', $friendId)
            ->where('receiver_id', $myId)
            ->where('status', '!=', 'read')
            ->update(['status' => 'read']);

        $messages = DB::table('messages')
            ->where(function($q) use ($myId, $friendId) {
                $q->where('sender_id', $myId)->where('receiver_id', $friendId);
            })
            ->orWhere(function($q) use ($myId, $friendId) {
                $q->where('sender_id', $friendId)->where('receiver_id', $myId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->json($response, $messages);
    }

    // ── POST /api/users/me/chats/{id} ─────────────────────────────────────────
    public function sendMessage(Request $request, Response $response, array $args): Response
    {
        $myId     = $request->getAttribute('auth_user')['sub'];
        $friendId = $args['id'];
        $body     = (array) $request->getParsedBody();
        $content  = $body['content'] ?? null;
        $files    = $request->getUploadedFiles();

        $mediaUrl  = null;
        $mediaType = null;

        if (!empty($files['media']) && $files['media']->getError() === UPLOAD_ERR_OK) {
            $file = $files['media'];
            $ext  = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $mediaType = 'image';
            } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'webm'])) {
                $mediaType = 'video';
            } else {
                return $this->json($response, ['message' => 'Formato no permitido.'], 422);
            }

            // Lógica para crear la carpeta combinada con los IDs ordenados (Ej: 1-2)
            $minId      = min($myId, $friendId);
            $maxId      = max($myId, $friendId);
            $folderName = $minId . '-' . $maxId;
            
            // Back->public->uploads->Chat Multimedia->[CarpetaCombinada]->[MiID]
            $baseDir   = __DIR__ . '/../../public/uploads/Chat Multimedia';
            $targetDir = $baseDir . '/' . $folderName . '/' . $myId;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $filename = 'msg_' . time() . '_' . uniqid() . '.' . $ext;
            $file->moveTo($targetDir . '/' . $filename);
            
            // Guardamos la ruta relativa en BD
            $mediaUrl = 'Chat Multimedia/' . $folderName . '/' . $myId . '/' . $filename;
        }

        if (!$content && !$mediaUrl) {
            return $this->json($response, ['message' => 'El mensaje está vacío.'], 422);
        }

        $msgId = DB::table('messages')->insertGetId([
            'sender_id'   => $myId,
            'receiver_id' => $friendId,
            'content'     => $content,
            'media_url'   => $mediaUrl,
            'media_type'  => $mediaType,
            'status'      => 'sent',
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        $newMessage = DB::table('messages')->where('id', $msgId)->first();

        Notification::notify(
            userId:     (int) $friendId,
            type:       'message',
            actorId:    (int) $myId,
            entityId:   $msgId,
            entityType: 'message'
        );

        return $this->json($response, (array) $newMessage);
    }

    private function json(Response $response, mixed $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
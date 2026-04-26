# Integración de Cloudinary

## Descripción
Se ha implementado Cloudinary en el `PostController` para gestionar la carga de imágenes y videos en publicaciones de forma segura en la nube.

## Configuración Requerida

### 1. Variables de Entorno
Agrega las siguientes variables de entorno a tu archivo `.env`:

```env
CLOUDINARY_CLOUD_NAME=tu_cloud_name
CLOUDINARY_API_KEY=tu_api_key
CLOUDINARY_API_SECRET=tu_api_secret
```

### 2. Obtener Credenciales
1. Crea una cuenta en [Cloudinary](https://cloudinary.com/)
2. Ve a tu Dashboard
3. Copia:
   - **Cloud Name**: Se encuentra en la sección "Account Details"
   - **API Key**: Se encuentra en la sección "API Keys"
   - **API Secret**: Se encuentra en la sección "API Keys"

## Cambios Realizados

### PostController
- **Método `store()`**: 
  - Sube imágenes y videos a Cloudinary en lugar de guardarlos localmente
  - Almacena la URL segura de Cloudinary en la base de datos
  - Genera un `public_id` único para cada archivo

- **Método `destroy()`**: 
  - Elimina archivos de Cloudinary cuando se borra una publicación
  - Maneja errores sin interrumpir la eliminación del post

- **Método `deleteFromCloudinary()`**: 
  - Método privado que extrae el `public_id` de la URL de Cloudinary
  - Detecta si es imagen o video y lo elimina correctamente

## Tipos de Archivo Soportados
- **Imágenes**: jpg, jpeg, png, gif
- **Videos**: mp4, mov

## Características
- ✅ Subida segura a la nube
- ✅ Optimización automática de medios
- ✅ URLs permanentes y confiables
- ✅ Eliminación automática de archivos al borrar posts
- ✅ Manejo de errores robusto

## Ejemplo de Uso

### Subir una publicación con imagen/video
```
POST /api/posts
Content-Type: multipart/form-data

{
  "content": "Mi publicación",
  "category_id": 1,
  "media": <archivo>
}
```

La respuesta incluirá:
```json
{
  "id": 1,
  "content": "Mi publicación",
  "media_path": "https://res.cloudinary.com/...",
  "content_type": "image",
  ...
}
```

## Notas Importantes
- Los archivos se organizan en carpetas: `mundialfan/images/` e `mundialfan/videos/`
- Las URLs devueltas son seguras (https)
- Se recomienda verificar los límites de almacenamiento de tu cuenta Cloudinary
- Para más información, consulta la [documentación oficial de Cloudinary PHP SDK](https://cloudinary.com/documentation/php_integration)

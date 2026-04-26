# Integración de Cloudinary

## Descripción
Se ha implementado Cloudinary en el `PostController` para gestionar la carga de imágenes y videos en publicaciones de forma segura en la nube.

## Configuración Requerida

### ⚠️ IMPORTANTE: Las credenciales de Cloudinary DEBEN estar configuradas

Si ves el error "Invalid configuration, please set up your environment", significa que las credenciales no están configuradas.

### Opción 1: URL de Cloudinary (RECOMENDADO)
Agrega esta variable de entorno a tu archivo `.env`:

```env
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
```

### Opción 2: Variables Separadas
Agrega estas variables de entorno a tu archivo `.env`:

```env
CLOUDINARY_CLOUD_NAME=tu_cloud_name
CLOUDINARY_API_KEY=tu_api_key
CLOUDINARY_API_SECRET=tu_api_secret
```

## Obtener Credenciales

1. Crea una cuenta en [Cloudinary](https://cloudinary.com/) (gratis)
2. Ve a tu [Dashboard](https://cloudinary.com/console)
3. Encontrarás:
   - **Cloud Name**: En la sección "Account Details"
   - **API Key**: En "API Keys"
   - **API Secret**: En "API Keys"

### Construir CLOUDINARY_URL
La URL tiene este formato:
```
cloudinary://api_key:api_secret@cloud_name
```

Ejemplo:
```
cloudinary://123456789:abcdef1234567890@my_cloud
```

## Cambios Realizados

### PostController
- **Método `store()`**: 
  - Sube imágenes y videos a Cloudinary
  - Almacena la URL segura de Cloudinary en la base de datos
  - Genera un `public_id` único para cada archivo

- **Método `destroy()`**: 
  - Elimina automáticamente el archivo de Cloudinary al borrar un post
  - Manejo seguro de errores sin afectar la eliminación del post

- **Método `deleteFromCloudinary()`**: 
  - Método privado que extrae el `public_id` de la URL de Cloudinary
  - Detecta si es imagen o video y lo elimina correctamente

- **Constructor con Validación**: 
  - Intenta usar `CLOUDINARY_URL` primero (más confiable)
  - Fallback a variables separadas si es necesario
  - Lanza excepción clara si no hay configuración disponible

## Tipos de Archivo Soportados
- **Imágenes**: jpg, jpeg, png, gif
- **Videos**: mp4, mov

## Características
- ✅ Subida segura a la nube
- ✅ Optimización automática de medios
- ✅ URLs permanentes y confiables
- ✅ Eliminación automática de archivos al borrar posts
- ✅ Manejo de errores robusto
- ✅ Validación de configuración

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

## Troubleshooting

### Error: "Invalid configuration, please set up your environment"
**Solución**: Configura `CLOUDINARY_URL` o las variables separadas en tu archivo `.env`

### Error: "Unauthorized" al eliminar archivos
**Solución**: Verifica que tu `API_SECRET` sea correcto en la configuración

### Los archivos no se suben
**Solución**: Revisa que tu cuenta Cloudinary tenga espacio disponible

## Notas Importantes
- Los archivos se organizan automáticamente en carpetas: `mundialfan/images/` e `mundialfan/videos/`
- Las URLs devueltas son seguras (HTTPS)
- Se recomienda verificar los límites de almacenamiento de tu cuenta Cloudinary
- Para más información, consulta la [documentación oficial de Cloudinary PHP SDK](https://cloudinary.com/documentation/php_integration)

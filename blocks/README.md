# Bloques custom del tema zenyx

Esta guía define el estándar para crear bloques nuevos en este tema sin ACF.

## Convenciones de naming

- Namespace de bloque: `zenyx/<slug>`.
- Slug en kebab-case y semántico (`hero`, `hero-media`, `cards-grid`).
- Usa sufijos numéricos solo para variantes reales (`hero-01`, `hero-02`, `hero-03`).
- Clases CSS con prefijo `mwm-` y estilo BEM simple (`mwm-hero`, `mwm-hero__media`).

## Estructura recomendada

Cada bloque vive en `blocks/<slug>/` con esta estructura:

- `block.json`
- `render.php` (SSR)
- `src/index.js`
- `src/edit.js`
- `build/index.js`
- `build/index.asset.php`
- opcional: `view.js` (si necesitas JS en frontend)

## Contrato mínimo de block.json

- `apiVersion: 3`
- `name: zenyx/<slug>`
- `editorScript: file:./build/index.js`
- `render: file:./render.php`
- `attributes` con tipos y defaults explícitos

## Patrón de implementación (SSR)

1. **Editor React (`src/edit.js`)**
   - `InspectorControls` para settings.
   - Controles de `@wordpress/components`.
   - Preview con `ServerSideRender`.
2. **Render PHP (`render.php`)**
   - Leer todo desde `$attributes`.
   - Sanitizar/castear antes de usar.
   - Escapar salida con `esc_html`, `esc_attr`, `esc_url`.
   - Wrapper con `get_block_wrapper_attributes()`.
3. **Save**
   - En `src/index.js`: `save: () => null` para SSR.

## Caso condicional (como hero image/video)

- Atributo selector (`mediaType`: `image|video`).
- En editor:
  - `SelectControl` para elegir tipo.
  - `MediaUpload` de imagen si `image`.
  - `MediaUpload` de video si `video`.
- En `render.php`:
  - Render condicional `<img>` o `<video>` según `mediaType`.

## Registro de bloques

El tema registra automáticamente todo `blocks/*/block.json` desde `functions.php`.
No hace falta añadir cada bloque manualmente si respetas la estructura.

## Build de bloques

- **Todos los bloques:** `npm run build:blocks` (compila cada carpeta en `blocks/` que tenga `block.json` y `src/index.js`). `npm run watch:blocks` para watch en paralelo.
- Bloques disponibles (ejemplos): `blocks/hero-01` (zenyx/hero-01), `blocks/hero-03` (zenyx/hero-03), `blocks/media-text-01` (zenyx/media-text-01), `blocks/text-01` (zenyx/text-01). Build de uno: `npm run build:block:hero-01` o `npm run build:block:hero-03`; watch: `npm run watch:block:hero-01` o `npm run watch:block:hero-03`.

Al añadir un bloque nuevo con la misma estructura, no hace falta tocar `package.json`: `build:blocks` y `watch:blocks` lo incluyen automáticamente. Opcionalmente puedes añadir scripts concretos (`build:block:<slug>`) para desarrollo de un solo bloque.

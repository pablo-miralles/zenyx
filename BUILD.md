# Build del tema zenyx

El tema usa **Tailwind CSS v4**. Todo el CSS (Preflight, fuentes, utilidades) sale de un único build.

## Comandos

```bash
npm install
npm run build
```

- **`npm run build`** — genera `assets/dist/theme.css` (minificado, incluye tipografías del tema). Obligatorio antes de usar el tema.
- **`npm run dev`** — genera y queda en watch (recompila al cambiar `assets/src/tailwind.css` o los archivos de `@source`).

## Carga de estilos

Siempre se encola: **theme.css** → **style.css**.

## Build de bloques Gutenberg (React)

- **`npm run build:blocks`** — compila todos los bloques que tengan `block.json` y `src/index.js` en `blocks/`.
- **`npm run watch:blocks`** — watch de todos los bloques en paralelo (Ctrl+C para detener).
- Por bloque: `npm run build:block:hero-01` y `npm run watch:block:hero-01` (bloque de ejemplo en `blocks/hero-01`).

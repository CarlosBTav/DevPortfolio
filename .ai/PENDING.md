# Tareas pendientes — DevPortfolio

Backlog vivo del proyecto. Ver `.ai/workflows/pending.md` para mantenerlo.

No hay tareas abiertas conocidas (25/09/2026). La suite pasa entera (38 tests).

Al abrir una, anótala aquí con qué falta, por qué y las rutas implicadas; las
tareas transversales de la VPS van a `/var/www/.ai/PENDING.md`.

## Rendimiento (25/09/2026)

Hecho: la portada servía **18,7 MB de imágenes** (capturas PNG de hasta 4,8 MB
a tamaño original, todas a la vez y sin `lazy`). Ahora sirve **0,97 MB** con
copias WebP de 640/1280 px (`App\Support\ImageDerivatives`, comando
`php artisan images:derive`). Las subidas nuevas generan su copia sola.

Queda por mirar:

- [ ] La portada lleva **60 KB de CSS y 47 KB de JS en línea** (~50% del HTML).
      Al estar dentro del HTML se redescargan en cada página y no se pueden
      cachear aparte. Moverlos a un bundle de Vite los haría cacheables, pero
      es un cambio con riesgo visual: hacerlo por bloques y comprobando.
- [ ] El widget de habilidades enlaza **imágenes externas de Pexels**
      (`home.blade.php`, sobre la línea 2030) por URL directa. Dependen de un
      tercero y no se pueden optimizar; conviene descargarlas y servirlas
      locales, ya convertidas.
- [ ] Si algún día se suben imágenes fuera de proyectos (clientes, documentación),
      pasarlas también por `ImageDerivatives`.

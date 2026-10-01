# Tareas pendientes — DevPortfolio

Backlog vivo del proyecto. Ver `.ai/workflows/pending.md` para mantenerlo.

Las tareas abiertas se detallan a continuación.

Al abrir una, anótala aquí con qué falta, por qué y las rutas implicadas; las
tareas transversales de la VPS van a `/var/www/.ai/PENDING.md`.

## Rendimiento (25/09/2026)

Hecho: la portada servía **18,7 MB de imágenes** (capturas PNG de hasta 4,8 MB
a tamaño original, todas a la vez y sin `lazy`). Ahora sirve **0,97 MB** con
copias WebP de 640/1280 px (`App\Support\ImageDerivatives`, comando
`php artisan images:derive`). Las subidas nuevas generan su copia sola.

Queda por mirar:

- [ ] Investigar si la estela del hover y la onda de clic/toque pueden mantenerse
      al hacer scroll en Sobre mí, Stack tecnológico, CV y Portfolio **sin perder
      rendimiento** (01/10/2026). Ahora el scroll cancela los efectos para
      preservar la fluidez; conservar ese comportamiento hasta validar una
      alternativa. Rutas: `resources/js/ai-dots-hover.js`,
      `resources/views/components/ai-dots-background.blade.php`,
      `resources/views/public/{about,stack,cv,projects}.blade.php`.
      Contexto y límites actuales: `.ai/frontend-performance.md`.
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

# Rendimiento del fondo de puntos

Sobre mí, Stack tecnológico, CV y Portfolio usan el modo `hoverOnly` de
`x-ai-dots-background`, con separación de 22 px: patrón CSS estático y un
conjunto local de canvases de 176×176 px. El hover aplica la estela orgánica
y la fuerza de desplazamiento de la home; el clic o toque reutiliza sus
helpers de onda expansiva. La home conserva las ondas autónomas; las páginas
de contenido solo animan durante una interacción y su disipación.

Límites: máximo 48 tiles reutilizables, 24 muestras de estela y dos ondas
simultáneas. Solo se dibuja el área visible; el desplazamiento se limita a
18 px y el retorno se acelera suavemente después del efecto. Cada interacción
duerme como máximo a los 3,4 segundos. Scroll, pestaña oculta y movimiento
reducido cancelan el trabajo. En móviles hay onda al tocar, sin hover.

La home conserva el modo animado original. Las cuatro páginas de contenido
cargan `ai-dots-hover.js` en lugar de `ai-dots-background.js`.

## Errores que no repetir

- **Quitar `wavesOverlay` no detiene la animación de los puntos**
  (01/10/2026): la prueba de copiar el efecto de la home volvió a producir
  lag porque `frameWaves` y el `requestAnimationFrame` continuo seguían
  activos aunque no hubiera cintas SVG. Para estas páginas, conservar
  `hoverOnly`; no volver a usar el motor completo para un hover sutil.
- **No cancelar la onda táctil al recibir `pointerout` del dedo**
  (01/10/2026): levantar el dedo genera ese evento aunque la página siga
  visible. Solo cancelar por salida del ratón; el toque debe disiparse solo.

# `.ai/` — Instrucciones locales de DevPortfolio

Guía humana de la documentación específica de este proyecto. El punto de
entrada de cualquier agente es [`AGENTS.md`](../AGENTS.md): contiene el núcleo
siempre activo y enruta a estos ficheros solo cuando aplican.

Las reglas compartidas de la VPS viven en `/var/www/.ai/` y se leen por ruta;
aquí no se duplican.

## Cómo funciona la carga

- `AGENTS.md` se lee siempre y se mantiene breve.
- Los ficheros de `.ai/**` se referencian por ruta y se leen a demanda.
- No uses `@import`: cargaría el fichero entero en contexto sin ahorrar nada.
- Si una regla local contradice a una compartida, manda la local.

## Dónde guardar la información

| Qué es | Dónde va |
| --- | --- |
| Tarea pendiente específica de DevPortfolio | `.ai/PENDING.md` |
| Proceso para registrar o cerrar pendientes | `.ai/workflows/pending.md` |
| Regla transversal de la VPS | `/var/www/.ai/` según su tabla de enrutado |

Si una nota no encaja en ningún fichero, crea el fichero temático mínimo y
añade su fila aquí **y** en la tabla de enrutado de `AGENTS.md`: sin fila, el
fichero es invisible.

## Estructura

```text
.ai/
├── README.md
├── PENDING.md
└── workflows/
    └── pending.md
```

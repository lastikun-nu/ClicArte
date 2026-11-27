# Pruebas automáticas de ClicArte (hitos 3, 7, 8 y 9)

Pruebas ejecutadas el 27/11/2026 con Chromium 158.0.8127.36 (Playwright, sin interfaz) sobre
las 13 vistas de `frontend/`. `resultados/antes` corresponde al commit 1d7e587 y
`resultados/despues` a la versión corregida. Las capturas `antes_*` y `despues_*` son comparables
(mismo tamaño de ventana y misma página).

| Script | Qué comprueba | Resultado |
|---|---|---|
| `audit.py` | Contraste de cada texto visible (WCAG 1.4.3) y desbordamiento horizontal, 13 vistas × 375/768/1366 px × tema claro/oscuro | `report.json` |
| `keyboard.py` | Tareas solo con teclado: login, formulario vacío, salto al contenido, menú móvil, consulta de tabla; anillo de foco en cada paso | `kbd_375.json`, `kbd_1366.json` |
| `matrix.py` | Las mismas 4 tareas en las 6 combinaciones tamaño × tema y persistencia del tema al recargar | `matriz.json` |
| `check_html.py` | Enlace a `css/main.css`, recursos, `id` duplicados, `label` sin campo, `caption`/`scope`, orden de encabezados, títulos únicos | `html.json` |
| `extra.py` | Reflujo a 320 px, nombres accesibles (árbol de accesibilidad) y teclado en gestión de incidencias | `extra.json` |
| `cascade.py` | Declaraciones aplicadas y anuladas (como el panel Styles) en `.btn-submit`, `a.button:hover`, tema oscuro y experimento CE2B | `cascada.json` |

Uso (desde la raíz del repositorio, con `pip install playwright` y `playwright install chromium`):

```bash
python docs/pruebas/scripts/audit.py ClicArte/frontend salida capturas
python docs/pruebas/scripts/summary.py salida/report.json
python docs/pruebas/scripts/keyboard.py ClicArte/frontend salida/kbd.json 1366
python docs/pruebas/scripts/matrix.py ClicArte/frontend salida/matriz.json
python docs/pruebas/scripts/check_html.py ClicArte/frontend salida/html.json
```

Estas pruebas no sustituyen a las pruebas manuales en un segundo navegador, con NVDA ni con
participantes; esas se documentan en la memoria.

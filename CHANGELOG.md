# Changelog - Warriors Lab's - © 2006-2021

## [2.0.0] - 2021-04-29
### Added
- Nueva interfaz del proyecto.
- Se agrego un Dashboard.
- Se agregaron nuevas gráficas para el dashboard. 

### Removed
- Se elimino la sección de 'software version'. 

---

## [2.1.0] - 2021-05-28
### Added
- Se agrego una acción para ver los detalles del correo desde 
  la tabla de mensajes recientes mediante un modal.
- Se agregaron checkbox a la tabla de mensajes recientes para agregar
  a la tabla whitelist o blacklist

### Changed
- Se modificaron colores en las tablas.
- Se agregaron traducciones en los archivos de lenguajes de palabras faltantes.

---

## [2.2.0] 
### Added
- Se agregaron botones a los lados de los checkbox en la tabla 
  de mensajes recientes para agregar directamente a la whitelist y blacklist

---

## [2.2.1] 
### Added
- Se agrego que se pudieran buscar mensajes por la dirección de correo y su id.
- Se muestra una tabla con los resultados de la búsqueda en un modal.

### Changed
- Se modificaron colores en las tablas.
- Se agregaron traducciones en los archivos de lenguajes de palabras faltantes.

### Fixed
- Se corrigieron las vistas para los usuarios de tipo Domain y User.
- Se corrigieron las traducciones para la página lists.php.

---

## [2.2.2] - 2021-07-22
### Added
#### Services
- Se agrego una etiqueta para saber el status del servicio de mailscanner.
- Se agrego una etiqueta para saber el status de Engine.
- Se agrego un botón para para ver el status desde services.php.

### Changed
#### Services
- Se agregaron traducciones en los archivos de lenguajes de palabras faltantes.
#### Demonio
- Se optimizó el código del demonio de python

### Fixed
#### Demonio
- Se corrigió la forma en que se ejecutaban las acciones, ahora se hacen mediante peticiones js
- Se corrigierón las salidas de los comandos a la hora de realizar las acciones, restart, stop y start no muestran una salida en consola, se agrego la salida del status para mostrar una salida. 
- Se agregaron acciones al demonio para remplazar los headers_checks de postfix ya que desde php no se tenian los permisos necesarios. 
- Para realizar las peticiones se agrego una verificación por token. 

---

## [2.2.3] - 2021-08-16
### Fixed
- Se corrigió error al presionar el botón de realase en viewmail.php

---

## [2.2.4] - 2021-08-19
### Fixed
- Se corrigió error al agregar a whitelist y blacklist desde lists.php
- Se corrigierón los nombres en las card de MTA y Engine para services.php
- Se corrigió el tamaño para la gráfica de trafico. 

---

## [2.2.4] - 2021-09-15
### Changed
- Se quitó el páginado para las tablas de lists.php
- Se modificó la estructura de las carpetas, se creo la carpeta "public" y se movieron las carpetas de images, css, js y librerias de js a esta nueva carpeta. 
- Se cambiaron las consultas para las gráficas del dashboard, se agregó la condición para solo los registros del último mes. 

---

## [2.2.5] - 2021-09-21
### Added
- Se agregó el script para revertir el skin de Warriors Lab's con nombre "remove_skin.sh"

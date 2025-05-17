# Portada
## Trabajo Práctico – PHP

### Año: 7° 1° TECPRO grupo 2
### Materia: Proyecto de App web dinamica
### Nombre del proyecto: CRUD de Tienda
### Estudiantes: Melina Beloqui – Martina Jauregui

# Resumen Ejecutivo
Este trabajo práctico consistió en el desarrollo de un sistema CRUD para una tienda, con el objetivo de afianzar conocimientos sobre manejo de bases de datos, integración de archivos CSV y programación backend con PHP. El sistema permite gestionar usuarios, productos y pedidos a través de una interfaz web sencilla, incorporando operaciones básicas y relaciones entre tablas.

# Introducción
El trabajo práctico se llevó a cabo como parte del proceso de aprendizaje en el desarrollo de un sistema que integre la manipulación de datos en una base relacional, junto con la importación de archivos CSV. Esto permitió poner en práctica conceptos clave como transacciones, validaciones, uso de claves foráneas y diseño de estructuras de datos.

# Objetivos
- Desarrollar un sistema CRUD completo utilizando PHP y MySQL.

- Incorporar la importación de datos desde archivos CSV.

- Gestionar usuarios, productos y pedidos desde un panel web.

- Representar la estructura de la base de datos mediante un DER.

# Alcance: 
## Datos de entrada:
- Archivos CSV con registros de usuarios, productos y pedidos.

## Salidas
- Listado de datos por entidad.

- Formularios para la carga, edición y eliminación de registros.

## Consideraciones
- Validación de datos repetidos (email, combinación usuario-producto).

- Relaciones entre tablas a través de claves foráneas.

- Creación automática de categorías al importar productos si no existen.

# Análisis del problema
Se planteó como ejercicio crear un sistema que permita realizar operaciones básicas (CRUD) sobre una base de datos relacional. La solución debía contemplar no solo la gestión manual desde una interfaz web, sino también la posibilidad de importar datos masivos desde archivos CSV. 

# Diseño de la solución
1) Se utilizó PHP y PDO para la conexión y operaciones con la base de datos tienda2.

2) Se dividieron las funcionalidades por entidad (usuarios, productos, pedidos).

3) Se desarrolló un panel principal con navegación y formularios.

4) Se implementaron controles de duplicados y validaciones básicas.

# Contenidos abordados
- PHP orientado a base de datos.

- PDO y transacciones.

- CRUD de usuarios, productos y pedidos.

- Importación desde archivos CSV.

- Uso de claves foráneas y relaciones entre tablas.
- Generación y análisis de un DER.

# Diagrama Entidad-Relación (DER)

## Validaciones y consideraciones de calidad
- Se realizaron pruebas de carga con archivos válidos y con duplicados.

- Se controlaron errores al cargar archivos con formato incorrecto.

- Se comprobó el funcionamiento de las operaciones de edición y eliminación.

- Se verificaron las relaciones entre tablas mediante claves foráneas.

# Conclusiones
Este trabajo práctico permitió reforzar habilidades clave en programación backend, manejo de bases de datos y validación de datos. La integración de archivos CSV ofreció un desafío adicional que ayudó a comprender el flujo completo de información desde el archivo hasta la base de datos. El desarrollo del CRUD permitió practicar la lógica de programación orientada a la gestión de datos y prepararse para proyectos más complejos.
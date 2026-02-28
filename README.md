# Ejercicio de vulnerabilidad SSRF y SQLInjection con Laravel 8

## Descripción
Este proyecto demuestra una vulnerabilidad SSRF y su mitigación.

## Instalación

1. Clonar repositorio
2. Ejecutar:
   composer install
   php artisan key:generate
   php artisan serve

3. Acceder a:
   http://127.0.0.1:8000/ssrf

## Prueba SSRF

URL vulnerable:
http://127.0.0.1:8000/internal/metadata

## Mitigación

Se bloquean IPs privadas y loopback.

# SQL Injection Demo – Laravel + MySQL
## Descripción
Este proyecto demuestra una vulnerabilidad de tipo **SQL Injection (SQLi)** y su respectiva mitigación utilizando Laravel y MySQL.
El objetivo es evidenciar cómo la concatenación directa de entradas del usuario en una consulta SQL puede permitir el bypass de autenticación.

El sistema incluye:
- Login vulnerable a SQL Injection
- Login seguro con consultas parametrizadas (Query Builder)
- Ejemplo práctico de explotación
- Implementación de mitigación

## ⚙️ Requisitos del Entorno
- PHP 7.4 o superior
- Composer
- MySQL o MariaDB
- XAMPP (recomendado)
- Git

## Instalación y Puesta en Marcha
### Clonar el repositorio
```bash
git clone https://github.com/AlexMolina11/ssrf_Actividad1.git
cd ssrf_Actividad1

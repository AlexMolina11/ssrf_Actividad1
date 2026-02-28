# Ejercicio de vulnerabilidad SSRF con Laravel 8

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

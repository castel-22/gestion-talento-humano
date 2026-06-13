@echo off
title Worker de Colas de Laravel - Procesador de PDFs
color 0A
echo ==================================================================
echo Iniciando el procesador en segundo plano (Queue Worker)
echo Por favor, no cierres esta ventana mientras uses el sistema.
echo ==================================================================
php artisan queue:work
pause

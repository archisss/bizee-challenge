#!/bin/bash

echo "Creando directorios requeridos para Laravel..."

# Crear directorios de almacenamiento
mkdir -p storage/app
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Agregar archivos .gitkeep para mantener estructura en Git
touch storage/app/.gitkeep
touch storage/framework/cache/.gitkeep
touch storage/framework/sessions/.gitkeep
touch storage/framework/views/.gitkeep
touch storage/logs/.gitkeep
touch bootstrap/cache/.gitkeep

# Asignar permisos adecuados
chmod -R 775 storage
chmod -R 775 bootstrap/cache

echo "Directorios y permisos listos."

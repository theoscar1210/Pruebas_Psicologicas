# MenteClara — Sistema de Evaluación Psicológica

> **"Donde el talento encuentra su medida"**  
> Plataforma web para la administración, calificación e interpretación de pruebas psicológicas orientadas a la selección de personal.

---

## Descripción general

MenteClara es una aplicación Laravel diseñada para equipos de psicología y recursos humanos que necesitan gestionar procesos de evaluación de candidatos de forma centralizada, segura y con soporte de inteligencia artificial. Permite aplicar pruebas estandarizadas en línea, calificarlas automáticamente o de forma manual, generar perfiles psicológicos y exportar informes en PDF.

---

## Funcionalidades principales

- **Portal del candidato** — acceso mediante código único, sin necesidad de registro
- **8 tipos de pruebas** psicométricas (personalidad, cognitivas, competencias, proyectivas)
- **Calificación automática** para pruebas de selección múltiple y escala Likert
- **Calificación manual** por el evaluador para entrevista STAR, Wartegg y Assessment Center
- **Dibujo digital Wartegg** sobre lienzo canvas en el navegador
- **Perfil psicológico unificado** que consolida todos los resultados de un candidato
- **Narrativas IA por sección** generadas con Groq (Llama 3.3-70B)
- **Informe psicológico completo con IA** con recomendación automática (Apto / Apto con reservas / No apto)
- **Exportación PDF** de informes individuales y reportes masivos
- **Exportación Excel** para análisis de RRHH
- **Autenticación con 2FA** (TOTP) para todos los usuarios del panel
- **Solicitud de eliminación de datos** conforme a la Ley 1581/2012 (habeas data)
- **Gestión de roles** con tres niveles de acceso diferenciados
- **Backups automáticos** cifrados con AES-256

---

## Tecnologías

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade, Tailwind CSS 3, Alpine.js 3, Chart.js 4 |
| Base de datos | SQLite (desarrollo) / MySQL (producción) |
| Generación PDF | barryvdh/laravel-dompdf |
| Exportación Excel | maatwebsite/excel |
| 2FA | pragmarx/google2fa + bacon/bacon-qr-code |
| IA generativa | Groq API — `llama-3.3-70b-versatile` |
| Backups | spatie/laravel-backup (S3 opcional) |
| Build | Vite 7 |

---

## Roles de usuario

| Rol | Descripción |
|---|---|
| `admin` | Acceso completo. Gestiona usuarios, cargos, pruebas, candidatos y solicitudes de eliminación. |
| `psicologo` | Configura pruebas, evalúa candidatos, genera perfiles y narrativas con IA. |
| `hr` | Recursos Humanos. Consulta candidatos y descarga el Informe Psicológico Completo en PDF. |

---

## Pruebas psicológicas incluidas

| Prueba | Módulo | Calificación |
|---|---|---|
| Big Five IPIP-50 | Personalidad | Automática — 5 dimensiones OCEAN |
| 16PF-SL | Personalidad | Automática — 16 factores |
| Matrices de Raven (MPR-SL) | Cognitivo | Automática — 20 ítems, 3 sets |
| Assessment Center (AC-SL) | Competencias | Evaluador — escala BARS 1-5 |
| Wartegg (Proyectivo) | Proyectivo | Evaluador — 8 campos de dibujo |
| TSC-SL — Servicio al cliente | Competencias | Mixta — 3 módulos |
| TSC-SL-H — Hospitalidad | Competencias | Mixta — 3 módulos |
| TTE-SL — Trabajo en equipo | Competencias | Mixta — 3 módulos |
| Entrevista STAR | Entrevista | Evaluador — 10 competencias conductuales |

---

## Requisitos previos

- PHP 8.2 o superior con extensiones: `pdo`, `mbstring`, `openssl`, `gd`, `fileinfo`, `zip`
- Composer 2
- Node.js 18+ y npm
- MySQL 8+ (producción) o SQLite (desarrollo)
- Cuenta en [console.groq.com](https://console.groq.com) para obtener la API key de IA (gratuita)

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone <url-del-repositorio> menteclara
cd menteclara

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias frontend
npm install

# 4. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar la base de datos en .env y ejecutar migraciones
php artisan migrate --seed

# 6. Compilar assets
npm run build

# 7. Enlace de almacenamiento público
php artisan storage:link
```

---

## Variables de entorno principales

```dotenv
APP_NAME="MenteClara"
APP_URL=https://tu-dominio.com

# Base de datos (producción)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=menteclara
DB_USERNAME=usuario
DB_PASSWORD=contraseña_segura

# IA — Groq (requerida para narrativas e informes automáticos)
# Obtén tu clave gratuita en https://console.groq.com
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxx

# Backups cifrados (obligatorio en producción)
BACKUP_ARCHIVE_PASSWORD=contraseña_larga_y_segura

# Sesiones seguras (producción)
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120
```

---

## Inteligencia artificial

El sistema usa la API de **Groq** con el modelo `llama-3.3-70b-versatile` para dos funcionalidades:

**Narrativas por sección** — genera un párrafo interpretativo para cada módulo del perfil (personalidad, cognitivo, competencias, proyectivo, entrevista STAR). Se accede desde el panel del psicólogo en la vista de perfil del candidato.

**Informe psicológico completo** — analiza la totalidad del perfil y produce un informe estructurado de 7 secciones con una recomendación final: **APTO**, **APTO CON RESERVAS** o **NO APTO**. El informe se guarda en la base de datos y es exportable a PDF.

> **Privacidad:** el servicio nunca envía el nombre real del candidato a la API externa. Se utiliza un seudónimo generado con SHA-256 (`candidateId + APP_KEY`). Las observaciones del evaluador se sanitizan para prevenir inyección de prompts.

---

## Portal del candidato

Los candidatos acceden en `https://tu-dominio.com/candidato` ingresando el código de acceso que les proporciona el equipo de RRHH. El flujo es:

```
Código de acceso → Consentimiento informado → Prueba → Resultado
```

Cada prueba tiene su propio flujo adaptado. El candidato puede solicitar la eliminación de sus datos desde `/candidato/eliminar-mis-datos`.

---

## Comandos útiles

```bash
# Desarrollo local
php artisan serve
npm run dev

# Ejecutar backup manualmente
php artisan backup:run

# Limpiar caché de vistas y configuración
php artisan optimize:clear
```

---

## Cumplimiento legal

- **Ley 1581/2012** — Protección de datos personales (Colombia)
- **Ley 1090/2006** — Código deontológico del psicólogo colombiano
- Consentimiento informado registrado con marca de tiempo e IP por cada prueba
- Flujo de eliminación de datos con aprobación del administrador
- Backups cifrados con contraseña AES-256
- Sesiones cifradas en base de datos
- Auditoría de acceso a informes en logs del sistema

---

## Créditos

Desarrollado para **MenteClara** · Emma Naranjo  
Sistema de evaluación psicológica para procesos de selección de personal.

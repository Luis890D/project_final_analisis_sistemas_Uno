# Sistema de Información Hospitalario (HIS)
## Módulo: Detalle de Paciente — Evaluación Final Análisis de Sistemas I

Proyecto **Laravel 12 + Vue 3 (Vite)** con **JWT Auth**, **Spatie Laravel Permission** y multitenancy por cabecera `X-Tenant-ID`. Implementado en 3 sprints como parte de la evaluación final del curso de Análisis de Sistemas I.

---

## 🏥 Módulo Implementado: Detalle de Paciente

El módulo **Expediente / Detalle de Paciente** es el núcleo de gestión médica del sistema HIS. Permite a los usuarios autenticados acceder a la ficha clínica completa de cualquier paciente del tenant activo.

### ¿Qué ofrece este módulo?

| Funcionalidad | Descripción |
|--------------|-------------|
| **Directorio de Pacientes** | Listado en cuadrícula con búsqueda en tiempo real y estadísticas de soporte |
| **Expediente Clínico** | Ficha completa con DPI, fecha de nacimiento, edad calculada automáticamente, teléfono, dirección, género y tipo sanguíneo |
| **Sistema de Alertas** | Advertencias visuales destacadas para pacientes con alergias críticas (penicilina, sulfas, etc.) |
| **Historial de Accesos** | Cronología de consultas, citas y emergencias con filtros por tipo y búsqueda por texto |
| **Registro de Nuevos Pacientes** | Modal flotante con validaciones completas desde el directorio |
| **Edición Rápida** | Modal de actualización de datos disponible desde el expediente |
| **Impresión Nativa** | Soporte de impresión del historial clínico con diseño optimizado para papel |
| **Multitenancy** | Aislamiento total de datos — cada tenant solo ve sus propios pacientes |

---

## 🏗️ Arquitectura del Sistema

La aplicación sigue un modelo **SPA + API REST**: el navegador carga una única vista Blade que monta Vue 3; el backend expone JSON bajo `/api/v1`.

### Stack Tecnológico

| Capa | Tecnología | Función |
|------|-----------|---------|
| **Backend / API** | Laravel 12 | Controladores REST, persistencia, contratos HTTP JSON |
| **Autenticación** | `tymon/jwt-auth` | Emite y valida tokens JWT en el guard `api` |
| **Autorización (RBAC)** | `spatie/laravel-permission` | Roles y permisos sobre el modelo `User` |
| **Multitenancy** | Cabecera `X-Tenant-ID` + tabla `tenants` | Aislamiento lógico por tenant sin bases de datos separadas |
| **Middleware propio** | `TenantMiddleware`, `JwtAuth` | Resuelve tenant por ID o slug; protege rutas con JWT |
| **Frontend** | Vue 3 + Vue Router + Pinia | SPA con rutas del lado cliente y estado global |
| **Build Frontend** | Vite 7 + `@vitejs/plugin-vue` | Empaqueta JS/CSS |
| **Cliente HTTP** | Axios (`resources/js/plugins/axios.js`) | Inyecta `Authorization: Bearer` y `X-Tenant-ID` automáticamente |
| **Base de Datos** | SQLite (dev) / MySQL 8 (prod) | Persistencia relacional |

### Estructura del Módulo de Pacientes

```
app/
├── Http/Controllers/Api/V1/
│   └── PatientController.php          # index, show, store, update (con filtrado por tenant)
├── Models/
│   ├── Patient.php                    # Modelo Eloquent: tenant(), user(), accesses()
│   └── PatientAccess.php              # Modelo Eloquent: registro de historial médico
│
database/
├── migrations/
│   ├── ..._create_patients_table.php          # Tabla patients con multitenancy
│   └── ..._create_patient_accesses_table.php  # Tabla historial médico
└── seeders/
    └── PatientSeeder.php              # 4 pacientes de prueba con historial clínico
│
resources/js/
├── modules/patients/
│   ├── pages/
│   │   ├── PatientListPage.vue        # Directorio con estadísticas y registro
│   │   └── PatientDetailPage.vue      # Expediente completo con impresión y edición
│   ├── components/
│   │   ├── PatientInfoCard.vue        # Tarjeta de datos generales
│   │   └── PatientAccessTable.vue     # Tabla de historial con filtros
│   └── services/
│       └── patientService.js          # Capa Axios para el API
├── stores/
│   └── patients.js                    # Store Pinia: currentPatient, patients, loading
└── router/
    └── index.js                       # Rutas /patients y /patients/:id protegidas
│
tests/
└── Feature/
    └── PatientModuleTest.php          # Tests de integración del módulo (Sprint 1, 2, 3)
```

### Flujo de Petición — Visualización de Expediente

```
Personal de Salud
    │
    ▼ click "Ver Expediente" (id: 1)
Vue Router (/patients/1)
    │
    ▼ PatientDetailPage.vue → Pinia Store.fetchPatient(1)
Axios Service
    │  GET /api/v1/patients/1
    │  Headers: X-Tenant-ID + Authorization: Bearer JWT
    ▼
Laravel Backend
    │  TenantMiddleware → valida X-Tenant-ID (por id o slug)
    │  jwt.auth         → valida token JWT
    ▼
PatientController.show()
    │  Query: Patient WHERE tenant_id = ? AND id = ?
    │  WITH: accesses (desc), user
    ▼
SQLite/MySQL
    │  Retorna registro + historial
    ▼
JSON Response → Pinia actualiza currentPatient → Vue reactivo
    │
    ▼ Interfaz muestra ficha clínica + historial de accesos
```

---

## 📋 Sprints Implementados

### Sprint 1 — Base de Datos y Modelos

Se definieron las estructuras de almacenamiento relacionales:

| Archivo | Descripción |
|---------|-------------|
| `0001_01_01_000005_create_patients_table.php` | Tabla `patients` con `tenant_id`, DPI único y datos médicos |
| `0001_01_01_000006_create_patient_accesses_table.php` | Tabla `patient_accesses` con FK en cascada a `patients` |
| `app/Models/Patient.php` | Modelo con relaciones `tenant()`, `user()`, `accesses()` |
| `app/Models/PatientAccess.php` | Modelo con relación inversa a `patient()` |
| `app/Models/User.php` | Añadida relación `patient()` (hasOne) |
| `database/seeders/PatientSeeder.php` | 4 pacientes de prueba con historial clínico real |

### Sprint 2 — API REST Backend

Se construyó el controlador con lógica multitenant implícita:

| Endpoint | Método | Auth | Descripción |
|----------|--------|------|-------------|
| `/api/v1/patients` | `GET` | JWT + Tenant | Listado del directorio (ordenado por nombre) |
| `/api/v1/patients` | `POST` | JWT + Tenant | Registro de nuevo paciente |
| `/api/v1/patients/{id}` | `GET` | JWT + Tenant | **Detalle del paciente + historial de accesos** |
| `/api/v1/patients/{id}` | `PUT` | JWT + Tenant | Actualización de datos del paciente |

**Correcciones aplicadas en esta fase:**
- Espacio de nombres corregido en `routes/api.php` → `App\Http\Controllers\Api\V1\`
- `TenantMiddleware.php` actualizado para aceptar tanto UUID como slug del tenant

### Sprint 3 — Frontend Vue 3

Se desarrolló el ecosistema de componentes UI:

| Componente | Descripción |
|-----------|-------------|
| `PatientInfoCard.vue` | Tarjeta con edad calculada al vuelo, grupo sanguíneo y alerta de alergias en rojo |
| `PatientAccessTable.vue` | Tabla interactiva con búsqueda instantánea y filtros por tipo de atención |
| `PatientListPage.vue` | Directorio con cuadrícula, estadísticas y modal de registro |
| `PatientDetailPage.vue` | Expediente completo con impresión nativa y modal de edición rápida |
| `patients.js` (Pinia) | Store con estados: `currentPatient`, `patients`, `loading`, `error` |
| `patientService.js` | Capa Axios con llamadas tipadas al API |

---

## 🚀 Instalación y Ejecución

### Requisitos

| Requisito | Versión mínima |
|-----------|----------------|
| PHP | ≥ 8.2 |
| Composer | ≥ 2.x |
| Node.js | ≥ 20 |
| Extensiones PHP | `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json` |
| Base de datos | SQLite (dev) o MySQL 8 (prod) |

### Pasos de instalación

```bash
# 1. Instalar dependencias PHP
composer install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# 3. Crear base de datos y cargar datos de prueba
php artisan migrate --seed

# 4. Instalar dependencias frontend
npm install
```

### Ejecución en desarrollo

```bash
# Terminal 1 — Backend Laravel
php artisan serve

# Terminal 2 — Frontend Vite
npm run dev
```

Abre `http://localhost:5173` e inicia sesión con:
- **Tenant ID:** `san-marcos-demo`
- **Email:** `luis@his.com`
- **Contraseña:** `password123`

### Variables `.env` relevantes

| Variable | Valor por defecto | Descripción |
|----------|-------------------|-------------|
| `APP_URL` | `http://localhost:8000` | URL del backend Laravel |
| `FRONTEND_URL` | `http://localhost:5173` | URL del frontend Vite |
| `JWT_SECRET` | *(generado)* | Secreto de firma JWT |
| `JWT_TTL` | `60` | Minutos de vida del access token |
| `VITE_API_URL` | `http://localhost:8000/api/v1` | Base URL del API para Axios |
| `DB_CONNECTION` | `sqlite` | Motor de base de datos |

---

## 🧪 Tests del Módulo

El módulo incluye tests de integración completos en `tests/Feature/PatientModuleTest.php`:

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar solo los tests del módulo de pacientes
php artisan test --filter PatientModuleTest

# Ejecutar con cobertura (requiere Xdebug)
php artisan test --coverage
```

### Casos cubiertos

| Test | Sprint | Descripción |
|------|--------|-------------|
| `test_can_list_patients` | Sprint 2 | Listado del directorio con JWT y tenant válidos |
| `test_cannot_list_patients_without_tenant` | Sprint 2 | Rechazo si falta la cabecera `X-Tenant-ID` |
| `test_cannot_list_patients_without_auth` | Sprint 2 | Rechazo si falta el token JWT |
| `test_can_view_patient_detail_with_accesses` | Sprint 1+2 | Expediente completo con historial de accesos |
| `test_patient_detail_not_found_returns_404` | Sprint 2 | 404 para paciente inexistente |
| `test_cannot_access_patient_from_other_tenant` | Sprint 2 | Aislamiento multitenant |
| `test_can_create_patient` | Sprint 2 | Registro de nuevo paciente |
| `test_create_patient_requires_mandatory_fields` | Sprint 2 | Validaciones del formulario |
| `test_can_update_patient` | Sprint 2 | Edición de datos del expediente |
| `test_tenant_resolves_by_slug` | Sprint 2 | Middleware acepta slug además de UUID |

---

## 🔗 API Reference

Todas las rutas requieren la cabecera `X-Tenant-ID` (UUID o slug del tenant).

### Autenticación

| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| `POST` | `/api/v1/auth/register` | No | Registrar usuario |
| `POST` | `/api/v1/auth/login` | No | Iniciar sesión → devuelve JWT |
| `GET` | `/api/v1/auth/me` | Bearer JWT | Perfil del usuario autenticado |
| `POST` | `/api/v1/auth/refresh` | Refresh token | Renovar access token |
| `POST` | `/api/v1/auth/logout` | Bearer JWT | Cerrar sesión |

### Módulo Pacientes *(nuevo)*

| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| `GET` | `/api/v1/patients` | Bearer JWT | Listar pacientes del tenant |
| `POST` | `/api/v1/patients` | Bearer JWT | Crear nuevo paciente |
| `GET` | `/api/v1/patients/{id}` | Bearer JWT | **Detalle + historial de accesos** |
| `PUT` | `/api/v1/patients/{id}` | Bearer JWT | Actualizar datos del paciente |

---

## 📦 Commits principales por Sprint

```bash
# Sprint 1 — Base de Datos
git commit -m "feat(database): crear migraciones de pacientes e historial de accesos"
git commit -m "feat(models): relacionar modelos Patient, PatientAccess y User"
git commit -m "seed: poblar pacientes y log de atenciones de prueba"

# Sprint 2 — API Backend
git commit -m "feat(api): implementar PatientController con filtrado de tenant"
git commit -m "fix(routes): corregir namespace de controladores a Api/V1"
git commit -m "fix(middleware): aceptar slug y UUID en TenantMiddleware"

# Sprint 3 — Frontend Vue 3
git commit -m "feat(store): crear Pinia store y API services para pacientes"
git commit -m "feat(components): construir PatientInfoCard y PatientAccessTable"
git commit -m "feat(pages): desarrollar PatientListPage y PatientDetailPage"
git commit -m "feat(tests): agregar suite completa de tests del módulo de pacientes"
```

---

## 📄 Entrega Canvas

- **Enlace al repositorio forkeado:** `https://github.com/[SuUsuario]/project_final_analisis_sistemas_Uno`
- **Módulo implementado:** Detalle de Paciente — vista de expediente clínico con datos generales e historial de accesos
- **Commits evidenciados:** Ver sección "Commits principales por Sprint" ↑


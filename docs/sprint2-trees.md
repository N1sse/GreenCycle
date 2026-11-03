# Sprint 2 — Módulo de árboles (Tree)
. Descripción general

El módulo Tree permite administrar los árboles virtuales de cada usuario dentro de GreenCycle.

Cada árbol pertenece a un usuario y a un tipo de semilla (SeedType). El sistema permite plantar árboles, consultar sus detalles, listar los árboles propios y realizar cuidados que afectan su salud, progreso, nivel y estado.

La lógica de cuidado se ejecuta en el servidor para evitar que el cliente pueda modificar directamente los valores internos del árbol.

## 2. Modelo Tree

La entidad Tree contiene los siguientes atributos:

| Campo | Descripción |
|---|---|
| id | Identificador único del árbol. |
| user_id | Usuario propietario del árbol. |
| seed_type_id | Tipo de semilla utilizado. |
| level | Nivel actual del árbol. |
| progress | Cantidad de cuidados acumulados para avanzar de nivel. |
| status | Estado actual del árbol. |
| health | Salud actual del árbol. |
| planted_at | Fecha de plantación. |
| last_cared_at | Fecha del último cuidado. |
| next_care_at | Fecha y hora en que puede recibir el siguiente cuidado. |
| last_decay_at | Fecha del último deterioro de salud. |
| harvested_at | Fecha de cosecha, reservada para una funcionalidad futura. |
| created_at | Fecha de creación del registro. |
| updated_at | Fecha de última actualización. |

### Valores iniciales

Al plantar un árbol, el servidor establece:

- `level`: 0.
- `progress`: 0.
- `health`: 100.
- `status`: ACTIVE.
- `planted_at`: fecha y hora del servidor.
- `last_cared_at`: null.
- `next_care_at`: null.
- `last_decay_at`: null.
- `harvested_at`: null.

El primer cuidado puede realizarse inmediatamente.

## 3. Relaciones

El modelo Tree mantiene las siguientes relaciones:

| Relación | Tipo | Descripción |
|---|---|---|
| User | BelongsTo | Cada árbol pertenece a un usuario. |
| SeedType | BelongsTo | Cada árbol utiliza un tipo de semilla. |
| ActiveEffect | HasMany | Un árbol puede tener varios efectos activos asociados. |

Las relaciones permiten consultar el propietario, el tipo de semilla y los efectos asociados al árbol.

## 4. API de árboles

Todas las rutas del módulo requieren autenticación mediante Laravel Sanctum.

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/trees` | Lista los árboles del usuario autenticado. |
| GET | `/api/trees/{tree}` | Consulta los detalles de un árbol propio. |
| POST | `/api/trees` | Planta un árbol nuevo. |
| DELETE | `/api/trees/{tree}` | Elimina un árbol propio. |
| POST | `/api/trees/{tree}/care` | Realiza un cuidado del árbol. |

### Plantar un árbol

**Endpoint:** `POST /api/trees`

El cliente proporciona el identificador del tipo de semilla mediante `seed_type_id`.

El servidor establece automáticamente el propietario y los valores iniciales del árbol.

No se permite que el cliente determine directamente el nivel, progreso, salud, estado o fechas funcionales.

### Listar árboles

**Endpoint:** `GET /api/trees`

Devuelve únicamente los árboles pertenecientes al usuario autenticado.

### Consultar un árbol

**Endpoint:** `GET /api/trees/{tree}`

Permite consultar la información de un árbol propio.

Si el árbol pertenece a otro usuario, la solicitud es rechazada.

## 5. API de cuidado

**Endpoint:** `POST /api/trees/{tree}/care`

Esta operación permite cuidar un árbol y actualizar sus valores de acuerdo con las reglas establecidas para Sprint 2.

### Reglas de negocio

1. El usuario debe estar autenticado.
2. El usuario debe ser propietario del árbol.
3. Los árboles con estado DEAD, MATURE o HARVESTED no pueden recibir cuidados.
4. Si todavía no ha transcurrido el tiempo de espera, el cuidado es rechazado.
5. Un cuidado válido aumenta el progreso en una unidad.
6. La salud aumenta 20 puntos, sin superar el máximo de 100.
7. Al completar cinco cuidados, el árbol sube un nivel y el progreso vuelve a cero.
8. Al alcanzar el nivel 10, el árbol cambia al estado MATURE.
9. Se actualiza `last_cared_at` con la fecha y hora del cuidado.
10. Se establece `next_care_at` 60 minutos después del cuidado.

### Cooldown

El sistema permite un cuidado cada 60 minutos.

La comparación se realiza utilizando la fecha y hora del servidor.

Si el momento actual es anterior a `next_care_at`, el sistema rechaza la solicitud.

En el instante exacto de `next_care_at`, el cuidado vuelve a estar permitido.

### Progreso y niveles

Se necesitan cinco cuidados válidos para avanzar un nivel.

Ejemplo:

| Cuidados | Nivel | Progreso |
|---|---:|---:|
| Inicio | 0 | 0 |
| 1 | 0 | 1 |
| 2 | 0 | 2 |
| 3 | 0 | 3 |
| 4 | 0 | 4 |
| 5 | 1 | 0 |

Al alcanzar el nivel 10, el estado del árbol cambia a MATURE.

### Salud

Cada cuidado recupera 20 puntos de salud.

La salud máxima permitida es 100.

Por ejemplo, si un árbol tiene 90 puntos de salud, después de cuidarlo tendrá 100 y no 110.

## 6. Respuestas JSON

### Cuidado exitoso

**Código HTTP:** 200 OK.

La respuesta contiene un mensaje de confirmación y el árbol actualizado mediante `TreeResource`.

Ejemplo ilustrativo:

```json
{
    "message": "¡Árbol cuidado correctamente!",
    "tree": {
        "id": 1,
        "user_id": 1,
        "seed_type_id": 1,
        "level": 0,
        "progress": 1,
        "status": "ACTIVE",
        "health": 100,
        "planted_at": "2026-11-02T12:00:00.000000Z",
        "last_cared_at": "2026-11-02T12:00:00.000000Z",
        "next_care_at": "2026-11-02T13:00:00.000000Z",
        "last_decay_at": null,
        "harvested_at": null,
        "created_at": "2026-11-02T12:00:00.000000Z",
        "updated_at": "2026-11-02T12:00:00.000000Z"
    }
}
```

Los valores y fechas son ilustrativos. La estructura corresponde a los campos definidos en `TreeResource`.

Los valores del ejemplo son ilustrativos.

### Cooldown activo

**Código HTTP:** 409 Conflict.

```json
{
    "message": "Todavía no puedes cuidar este árbol.",
    "next_care_at": "2026-11-02T15:00:00.000000Z"
}
```

La fecha es ilustrativa. La respuesta real utiliza la fecha almacenada para el árbol.

### Estado no permitido

**Código HTTP:** 409 Conflict.

```json
{
    "message": "Este árbol ya no puede recibir cuidados.",
    "status": "MATURE"
}
```

### Árbol de otro usuario

**Código HTTP:** 403 Forbidden.

La operación se rechaza porque el usuario autenticado no es propietario del árbol.

### Usuario no autenticado

**Código HTTP:** 401 Unauthorized.

Se requiere un token válido de Sanctum.

### Árbol inexistente

**Código HTTP:** 404 Not Found.

El sistema responde cuando no encuentra el árbol solicitado.

## 7. Seguridad y autorización

El módulo utiliza TreePolicy para controlar el acceso a los árboles.

La propiedad se verifica antes de evaluar el estado o el tiempo de espera del árbol.

Esto evita que un usuario pueda consultar o modificar árboles pertenecientes a otras cuentas.

Además, los valores internos se calculan en el servidor y no se aceptan como valores controlados por el cliente.

La operación de cuidado utiliza una transacción y `lockForUpdate()` para volver a consultar el árbol antes de modificarlo, reduciendo el riesgo de actualizaciones simultáneas.

## 8. Pruebas automatizadas

Las pruebas del módulo se encuentran en:

`tests/Feature/TreeCareTest.php`

Se utiliza `RefreshDatabase` para trabajar con una base de datos de pruebas independiente.

Los escenarios implementados incluyen:

1. Cuidado exitoso de un árbol propio.
2. Rechazo de un segundo cuidado inmediato.
3. Aumento de nivel después de cinco cuidados válidos.
4. Cambio de estado a MATURE al alcanzar el nivel 10.
5. Rechazo del cuidado de un árbol perteneciente a otro usuario.
6. Rechazo de solicitudes sin autenticación.
7. Respuesta 404 para árboles inexistentes.
8. Rechazo de árboles en estados terminales.
9. Límite máximo de salud de 100.
10. Aceptación del cuidado en el instante exacto del vencimiento del cooldown.

### Ejecutar las pruebas

Desde la carpeta raíz de la aplicación Laravel:

```powershell
php artisan test --filter=TreeCareTest
```

Para ejecutar todas las pruebas del proyecto:

```powershell
php artisan test
```

La configuración de pruebas utiliza SQLite en memoria, evitando modificar la base de datos de desarrollo.

## 9. Decisiones de implementación

- El servidor controla los valores internos del árbol.
- El primer cuidado está permitido inmediatamente.
- Se requieren cinco cuidados para avanzar de nivel.
- Cada cuidado recupera 20 puntos de salud.
- El cooldown es de 60 minutos.
- El nivel máximo es 10.
- El estado MATURE impide nuevos cuidados.
- Se utiliza autorización por propiedad.
- Se emplean transacciones y bloqueo de registros para proteger las actualizaciones.
- El deterioro automático de salud y la cosecha quedan reservados para futuras etapas.

## 10. Verificación del módulo

Comandos útiles para verificar las rutas y pruebas:

```powershell
php artisan route:list --path=api/trees
php artisan test --filter=TreeCareTest
php artisan test
```

La suite completa del proyecto se ejecutó satisfactoriamente durante la verificación del módulo.

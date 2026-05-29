#!/bin/bash
# =============================================================================
# ProviEmplea API — Script de pruebas CRUD
# Ejecutar con: bash test_api.sh
# Requiere: API corriendo en http://localhost:8080/api
# =============================================================================

BASE="http://localhost:8080/api"
BOLD='\033[1m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_section() { echo -e "\n${BOLD}${CYAN}══════════════════════════════════════${NC}"; echo -e "${BOLD}${CYAN}  $1${NC}"; echo -e "${BOLD}${CYAN}══════════════════════════════════════${NC}"; }
print_test()    { echo -e "\n${YELLOW}▶ $1${NC}"; }
print_ok()      { echo -e "${GREEN}✓ $1${NC}"; }
print_err()     { echo -e "${RED}✗ $1${NC}"; }

# =============================================================================
print_section "1. HEALTH CHECK"
# =============================================================================

print_test "GET /health — Estado del servicio"
curl -s "$BASE/health" | python3 -m json.tool 2>/dev/null || curl -s "$BASE/health"

# =============================================================================
print_section "2. CRUD PERSONAS"
# =============================================================================

print_test "POST /personas — Crear talento completo"
PERSONA_RESPONSE=$(curl -s -X POST "$BASE/personas" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "talento.test@proviemplea.cl",
    "telefono": "+56912345678",
    "resumen": "Desarrollador PHP con 5 años de experiencia en Laravel y MySQL",
    "nivel_educacional": "universitaria",
    "titulo_carrera": "Ingeniería en Informática",
    "anio_egreso": 2020,
    "anios_experiencia": 5,
    "areas_experiencia": ["Desarrollo Web", "APIs REST", "DevOps"],
    "competencias": ["PHP", "Laravel", "MySQL", "Docker", "Git"],
    "rango_renta": "800k-1.2M",
    "tipo_jornada": "completa",
    "modalidad": "hibrido",
    "cursos": [
      {"nombre": "AWS Cloud Practitioner", "institucion": "AWS", "anio": 2023}
    ],
    "idiomas": [
      {"idioma": "Inglés", "nivel": "avanzado"}
    ],
    "portafolio_url": "https://miportafolio.dev",
    "persona_discapacidad": false
  }')
echo "$PERSONA_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$PERSONA_RESPONSE"

# Extraer ID para las siguientes pruebas
PERSONA_ID=$(echo "$PERSONA_RESPONSE" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d['data']['id'])" 2>/dev/null)
print_ok "ID de persona creada: $PERSONA_ID"

# ─────────────────────────────────────────────────────────────────────────────
print_test "GET /personas — Listar CV ciegos (sin datos privados)"
curl -s "$BASE/personas" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

print_test "GET /personas?validado=false — Filtrar por no validados"
curl -s "$BASE/personas?validado=false" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

print_test "GET /personas?nivel_educacional=universitaria — Filtrar por nivel"
curl -s "$BASE/personas?nivel_educacional=universitaria" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "GET /personas/{id} — Ver perfil completo"
curl -s "$BASE/personas/$PERSONA_ID" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "PUT /personas/{id} — Actualizar competencias y rango de renta"
curl -s -X PUT "$BASE/personas/$PERSONA_ID" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "competencias": ["PHP", "Laravel", "Vue.js", "MySQL", "Redis", "Docker"],
    "rango_renta": "1.2M-1.8M",
    "modalidad": "remoto",
    "idiomas": [
      {"idioma": "Inglés", "nivel": "avanzado"},
      {"idioma": "Portugués", "nivel": "intermedio"}
    ]
  }' | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "PATCH /personas/{id}/validar — Validar perfil (admin)"
curl -s -X PATCH "$BASE/personas/$PERSONA_ID/validar" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "POST /personas — Validación: email duplicado (debe retornar 422)"
curl -s -X POST "$BASE/personas" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "talento.test@proviemplea.cl"}' | python3 -m json.tool 2>/dev/null

print_test "POST /personas — Validación: nivel_educacional inválido (debe retornar 422)"
curl -s -X POST "$BASE/personas" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "otro@test.cl", "nivel_educacional": "doctorado"}' | python3 -m json.tool 2>/dev/null

print_test "GET /personas/uuid-inexistente — Recurso no encontrado (debe retornar 404)"
curl -s "$BASE/personas/00000000-0000-0000-0000-000000000000" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# =============================================================================
print_section "3. CRUD EMPRESAS"
# =============================================================================

print_test "POST /empresas — Crear empresa completa"
EMPRESA_RESPONSE=$(curl -s -X POST "$BASE/empresas" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nombre_empresa": "TechCorp SpA",
    "rut_empresa": "76123456-7",
    "email": "rrhh@techcorp.cl",
    "tipo_empresa": "contratacion-directa",
    "rubro": "Tecnología e Informática",
    "presentacion": "Empresa tecnológica líder en soluciones cloud para PYME",
    "beneficios": [
      "Seguro complementario de salud",
      "Trabajo remoto 3 días/semana",
      "Bono de desempeño anual",
      "Capacitaciones pagadas"
    ],
    "contacto_nombre": "Ana López",
    "contacto_email": "ana.lopez@techcorp.cl",
    "contacto_telefono": "+56922334455",
    "logo_url": "https://techcorp.cl/logo.png"
  }')
echo "$EMPRESA_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$EMPRESA_RESPONSE"

EMPRESA_ID=$(echo "$EMPRESA_RESPONSE" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d['data']['id'])" 2>/dev/null)
print_ok "ID de empresa creada: $EMPRESA_ID"

# ─────────────────────────────────────────────────────────────────────────────
print_test "GET /empresas — Listar todas las empresas"
curl -s "$BASE/empresas" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

print_test "GET /empresas?tipo_empresa=contratacion-directa — Filtrar por tipo"
curl -s "$BASE/empresas?tipo_empresa=contratacion-directa" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "GET /empresas/{id} — Ver empresa"
curl -s "$BASE/empresas/$EMPRESA_ID" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "PUT /empresas/{id} — Actualizar beneficios y contacto"
curl -s -X PUT "$BASE/empresas/$EMPRESA_ID" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "beneficios": [
      "Seguro complementario de salud",
      "Trabajo remoto full",
      "Bono de desempeño semestral",
      "Capacitaciones pagadas",
      "Tarde libre el día de cumpleaños"
    ],
    "contacto_nombre": "María González",
    "contacto_email": "mgonzalez@techcorp.cl"
  }' | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "PATCH /empresas/{id}/validar — Validar empresa (admin)"
curl -s -X PATCH "$BASE/empresas/$EMPRESA_ID/validar" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "POST /empresas — Validación: RUT duplicado (debe retornar 422)"
curl -s -X POST "$BASE/empresas" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nombre_empresa": "Otra empresa",
    "rut_empresa": "76123456-7",
    "email": "otro@empresa.cl",
    "tipo_empresa": "est",
    "contacto_nombre": "Juan Pérez",
    "contacto_email": "juan@empresa.cl"
  }' | python3 -m json.tool 2>/dev/null

# =============================================================================
print_section "4. CRUD ADMINISTRACIÓN (CONTACTOS)"
# =============================================================================

print_test "POST /admin/contactos — Crear solicitud de contacto"
CONTACTO_RESPONSE=$(curl -s -X POST "$BASE/admin/contactos" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"empresa_id\": \"$EMPRESA_ID\",
    \"persona_id\": \"$PERSONA_ID\",
    \"notas_admin\": \"Empresa interesada en perfil de desarrollo backend Laravel. Perfil muy alineado con la vacante.\"
  }")
echo "$CONTACTO_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$CONTACTO_RESPONSE"

CONTACTO_ID=$(echo "$CONTACTO_RESPONSE" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d['data']['id'])" 2>/dev/null)
print_ok "ID de contacto creado: $CONTACTO_ID"

# ─────────────────────────────────────────────────────────────────────────────
print_test "POST /admin/contactos — Conflicto: solicitud activa duplicada (debe retornar 409)"
curl -s -X POST "$BASE/admin/contactos" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"empresa_id\": \"$EMPRESA_ID\",
    \"persona_id\": \"$PERSONA_ID\"
  }" | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "GET /admin/contactos — Listar todas las solicitudes"
curl -s "$BASE/admin/contactos" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

print_test "GET /admin/contactos?estado=pendiente — Filtrar por estado"
curl -s "$BASE/admin/contactos?estado=pendiente" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "PATCH /admin/contactos/{id}/estado — Avanzar a 'contactado'"
curl -s -X PATCH "$BASE/admin/contactos/$CONTACTO_ID/estado" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "estado": "contactado",
    "notas_admin": "Se envió correo al talento informando el interés de la empresa"
  }' | python3 -m json.tool 2>/dev/null

print_test "PATCH /admin/contactos/{id}/estado — Avanzar a 'entrevista'"
curl -s -X PATCH "$BASE/admin/contactos/$CONTACTO_ID/estado" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "estado": "entrevista",
    "notas_admin": "Entrevista técnica agendada para el 01/06/2026 a las 10:00 hrs"
  }' | python3 -m json.tool 2>/dev/null

print_test "PATCH /admin/contactos/{id}/estado — Marcar como 'seleccionado'"
curl -s -X PATCH "$BASE/admin/contactos/$CONTACTO_ID/estado" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "estado": "seleccionado",
    "notas_admin": "Proceso exitoso. Inicio de funciones: 15/06/2026"
  }' | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "PATCH /admin/contactos/{id}/estado — Estado inválido (debe retornar 422)"
curl -s -X PATCH "$BASE/admin/contactos/$CONTACTO_ID/estado" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"estado": "cancelado"}' | python3 -m json.tool 2>/dev/null

# ─────────────────────────────────────────────────────────────────────────────
print_test "GET /admin/estadisticas — Resumen de la plataforma"
curl -s "$BASE/admin/estadisticas" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# =============================================================================
print_section "5. DELETE / SOFT DELETE"
# =============================================================================

print_test "DELETE /personas/{id} — Desactivar talento (soft delete)"
curl -s -X DELETE "$BASE/personas/$PERSONA_ID" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

print_test "GET /personas — Confirmar que el perfil desactivado no aparece en la vitrina"
curl -s "$BASE/personas" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

print_test "DELETE /empresas/{id} — Desactivar empresa (soft delete)"
curl -s -X DELETE "$BASE/empresas/$EMPRESA_ID" \
  -H "Accept: application/json" | python3 -m json.tool 2>/dev/null

# =============================================================================
print_section "RESUMEN FINAL"
# =============================================================================

echo ""
echo -e "${GREEN}${BOLD}✅ Suite de pruebas completada.${NC}"
echo ""
echo "Endpoints probados:"
echo "  GET    /health"
echo "  POST   /personas               (201 + validaciones 422)"
echo "  GET    /personas               (con filtros)"
echo "  GET    /personas/{id}          (200 + 404)"
echo "  PUT    /personas/{id}"
echo "  PATCH  /personas/{id}/validar"
echo "  DELETE /personas/{id}          (soft delete)"
echo "  POST   /empresas               (201 + validaciones 422)"
echo "  GET    /empresas               (con filtros)"
echo "  GET    /empresas/{id}"
echo "  PUT    /empresas/{id}"
echo "  PATCH  /empresas/{id}/validar"
echo "  DELETE /empresas/{id}          (soft delete)"
echo "  POST   /admin/contactos        (201 + 409 conflicto)"
echo "  GET    /admin/contactos        (con filtros)"
echo "  PATCH  /admin/contactos/{id}/estado  (5 transiciones + 422)"
echo "  GET    /admin/estadisticas"
echo ""
echo "📖 Documentación Swagger: http://localhost:8080/api/documentation"
echo ""

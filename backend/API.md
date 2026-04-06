# API - Guia Doméstico

Documentação completa dos endpoints da API REST.

## 🔗 Base URL

```
http://localhost:5000/api
```

## 📋 Endpoints

### 🔐 Autenticação

#### Registrar Novo Usuário
```http
POST /auth/register
Content-Type: application/json

{
    "nome_completo": "João Silva",
    "email": "joao@example.com",
    "senha": "Senha123"
}
```

**Resposta (201):**
```json
{
    "mensagem": "Usuário registrado com sucesso",
    "usuario_id": 1,
    "email": "joao@example.com"
}
```

#### Fazer Login
```http
POST /auth/login
Content-Type: application/json

{
    "email": "joao@example.com",
    "senha": "Senha123"
}
```

**Resposta (200):**
```json
{
    "mensagem": "Login realizado com sucesso",
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "usuario": {
        "id": 1,
        "nome_completo": "João Silva",
        "email": "joao@example.com"
    }
}
```

#### Fazer Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "mensagem": "Logout realizado com sucesso"
}
```

#### Verificar Token
```http
GET /auth/verify
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "valido": true,
    "usuario": {
        "id": 1,
        "nome_completo": "João Silva",
        "email": "joao@example.com"
    }
}
```

### 👤 Usuários

#### Obter Perfil
```http
GET /usuarios/perfil
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "usuario": {
        "id": 1,
        "nome_completo": "João Silva",
        "email": "joao@example.com",
        "telefone": "11987654321",
        "endereco": "Rua das Flores, 123",
        "cidade": "São Paulo",
        "estado": "SP",
        "cep": "01234-567",
        "data_cadastro": "2024-01-15T10:30:00",
        "status": "ativo"
    }
}
```

#### Atualizar Perfil
```http
PUT /usuarios/atualizar
Authorization: Bearer {token}
Content-Type: application/json

{
    "nome_completo": "João Silva Santos",
    "telefone": "11987654321",
    "endereco": "Rua das Flores, 456",
    "cidade": "São Paulo",
    "estado": "SP",
    "cep": "01234-567"
}
```

**Resposta (200):**
```json
{
    "mensagem": "Perfil atualizado com sucesso"
}
```

#### Alterar Senha
```http
POST /usuarios/alterar-senha
Authorization: Bearer {token}
Content-Type: application/json

{
    "senha_atual": "Senha123",
    "senha_nova": "NovaSenha456"
}
```

**Resposta (200):**
```json
{
    "mensagem": "Senha alterada com sucesso"
}
```

#### Deletar Conta
```http
DELETE /usuarios/deletar
Authorization: Bearer {token}
Content-Type: application/json

{
    "senha": "Senha123"
}
```

**Resposta (200):**
```json
{
    "mensagem": "Conta deletada com sucesso"
}
```

#### Listar Usuários (Admin)
```http
GET /usuarios/?limite=100&offset=0&status=ativo
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "usuarios": [
        {
            "id": 1,
            "nome_completo": "João Silva",
            "email": "joao@example.com",
            "telefone": "11987654321",
            "cidade": "São Paulo",
            "data_cadastro": "2024-01-15T10:30:00",
            "status": "ativo"
        }
    ],
    "total": 1,
    "limite": 100,
    "offset": 0
}
```

### 💼 Cargos

#### Listar Cargos
```http
GET /cargos/?limite=100&offset=0&ativo=true
```

**Resposta (200):**
```json
{
    "cargos": [
        {
            "id": 1,
            "numero_cargo": 1,
            "nome_cargo": "Faxineira",
            "cbo_codigo": "5121-15",
            "descricao_funcoes": "Realiza limpeza profunda de ambientes",
            "salario_minimo": 1500.00,
            "salario_maximo": 2500.00,
            "salario_medio": 1800.00,
            "ativo": true
        }
    ],
    "total": 45,
    "limite": 100,
    "offset": 0
}
```

#### Obter Cargo
```http
GET /cargos/{cargo_id}
```

**Resposta (200):**
```json
{
    "cargo": {
        "id": 1,
        "numero_cargo": 1,
        "nome_cargo": "Faxineira",
        "cbo_codigo": "5121-15",
        "descricao_funcoes": "Realiza limpeza profunda de ambientes",
        "responsabilidades": [
            "Limpeza de pisos",
            "Limpeza de vidros",
            "Organização de ambientes"
        ],
        "nao_responsabilidades": [
            "Cozinhar",
            "Cuidar de crianças"
        ],
        "obrigacoes": [
            "Usar EPIs",
            "Manter ambiente seguro"
        ],
        "salario_minimo": 1500.00,
        "salario_maximo": 2500.00,
        "salario_medio": 1800.00,
        "data_criacao": "2024-01-01T00:00:00",
        "ativo": true
    }
}
```

#### Buscar Cargos
```http
GET /cargos/buscar?termo=faxineira&limite=50
```

**Resposta (200):**
```json
{
    "cargos": [
        {
            "id": 1,
            "numero_cargo": 1,
            "nome_cargo": "Faxineira",
            "cbo_codigo": "5121-15",
            "salario_minimo": 1500.00,
            "salario_maximo": 2500.00,
            "salario_medio": 1800.00
        }
    ],
    "total": 1
}
```

#### Criar Cargo (Admin)
```http
POST /cargos/
Authorization: Bearer {token}
Content-Type: application/json

{
    "numero_cargo": 1,
    "nome_cargo": "Faxineira",
    "cbo_codigo": "5121-15",
    "descricao_funcoes": "Realiza limpeza profunda de ambientes",
    "responsabilidades": ["Limpeza de pisos", "Limpeza de vidros"],
    "nao_responsabilidades": ["Cozinhar"],
    "obrigacoes": ["Usar EPIs"],
    "salario_minimo": 1500.00,
    "salario_maximo": 2500.00,
    "salario_medio": 1800.00
}
```

**Resposta (201):**
```json
{
    "mensagem": "Cargo criado com sucesso",
    "cargo_id": 1
}
```

#### Atualizar Cargo (Admin)
```http
PUT /cargos/{cargo_id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "salario_medio": 1900.00,
    "descricao_funcoes": "Realiza limpeza profunda e organização de ambientes"
}
```

**Resposta (200):**
```json
{
    "mensagem": "Cargo atualizado com sucesso"
}
```

#### Deletar Cargo (Admin)
```http
DELETE /cargos/{cargo_id}
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "mensagem": "Cargo deletado com sucesso"
}
```

### 📊 Diagnósticos

#### Criar Diagnóstico
```http
POST /diagnosticos/
Authorization: Bearer {token}
Content-Type: application/json

{
    "cargo_id": 1,
    "salario_mensal": 1500.00,
    "data_admissao": "2022-01-15",
    "horas_por_dia": 8,
    "dias_por_semana": 5
}
```

**Resposta (201):**
```json
{
    "mensagem": "Diagnóstico criado com sucesso",
    "diagnostico_id": 1,
    "calculos": {
        "salario_por_hora": 8.65,
        "carga_horaria_mensal": 160,
        "tempo_servico_meses": 24,
        "dias_ferias": 60,
        "valor_ferias": 3000.00,
        "valor_decimo_terceiro": 1500.00,
        "valor_fgts_mensal": 120.00,
        "valor_inss_desconto": 135.00
    }
}
```

#### Listar Diagnósticos
```http
GET /diagnosticos/?limite=100&offset=0
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "diagnosticos": [
        {
            "id": 1,
            "cargo_id": 1,
            "nome_cargo": "Faxineira",
            "salario_mensal": 1500.00,
            "data_admissao": "2022-01-15",
            "tempo_servico_meses": 24,
            "dias_ferias": 60,
            "valor_ferias": 3000.00,
            "valor_decimo_terceiro": 1500.00,
            "data_geracao": "2024-01-15T10:30:00",
            "status": "ativo"
        }
    ],
    "total": 1,
    "limite": 100,
    "offset": 0
}
```

#### Obter Diagnóstico
```http
GET /diagnosticos/{diagnostico_id}
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "diagnostico": {
        "id": 1,
        "usuario_id": 1,
        "nome_completo": "João Silva",
        "email": "joao@example.com",
        "cargo_id": 1,
        "nome_cargo": "Faxineira",
        "cbo_codigo": "5121-15",
        "salario_mensal": 1500.00,
        "data_admissao": "2022-01-15",
        "horas_por_dia": 8,
        "dias_por_semana": 5,
        "salario_por_hora": 8.65,
        "carga_horaria_mensal": 160,
        "tempo_servico_meses": 24,
        "dias_ferias": 60,
        "valor_ferias": 3000.00,
        "valor_decimo_terceiro": 1500.00,
        "valor_fgts_mensal": 120.00,
        "valor_inss_desconto": 135.00,
        "data_geracao": "2024-01-15T10:30:00",
        "status": "ativo"
    },
    "salario_liquido": 1365.00
}
```

#### Atualizar Diagnóstico
```http
PUT /diagnosticos/{diagnostico_id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "salario_mensal": 1600.00,
    "horas_por_dia": 6
}
```

**Resposta (200):**
```json
{
    "mensagem": "Diagnóstico atualizado com sucesso"
}
```

#### Deletar Diagnóstico
```http
DELETE /diagnosticos/{diagnostico_id}
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
    "mensagem": "Diagnóstico deletado com sucesso"
}
```

## 🔒 Autenticação

Todos os endpoints que requerem autenticação devem incluir o header:

```
Authorization: Bearer {access_token}
```

O token é obtido no login e expira em 24 horas (configurável).

## 📊 Códigos de Status HTTP

| Código | Significado |
|--------|-----------|
| 200 | OK - Requisição bem-sucedida |
| 201 | Created - Recurso criado |
| 400 | Bad Request - Dados inválidos |
| 401 | Unauthorized - Não autenticado |
| 403 | Forbidden - Sem permissão |
| 404 | Not Found - Recurso não encontrado |
| 409 | Conflict - Conflito (ex: email duplicado) |
| 500 | Internal Server Error - Erro do servidor |

## 🧪 Exemplos com cURL

### Registrar
```bash
curl -X POST http://localhost:5000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "nome_completo": "João Silva",
    "email": "joao@example.com",
    "senha": "Senha123"
  }'
```

### Login
```bash
curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao@example.com",
    "senha": "Senha123"
  }'
```

### Obter Perfil
```bash
curl -X GET http://localhost:5000/api/usuarios/perfil \
  -H "Authorization: Bearer {token}"
```

### Criar Diagnóstico
```bash
curl -X POST http://localhost:5000/api/diagnosticos/ \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "cargo_id": 1,
    "salario_mensal": 1500.00,
    "data_admissao": "2022-01-15",
    "horas_por_dia": 8,
    "dias_por_semana": 5
  }'
```

## 📝 Notas

- Todas as datas devem estar no formato `YYYY-MM-DD`
- Todos os valores monetários são em BRL
- As senhas devem ter no mínimo 8 caracteres
- Os emails devem ser únicos no sistema
- Soft delete é usado para usuários, cargos e diagnósticos

---

**Desenvolvido com ❤️ para empoderar trabalhadores domésticos**

# Backend - Guia Doméstico

Backend da plataforma Guia Doméstico com CRUD completo para Usuários, Cargos e Diagnósticos.

## 📋 Estrutura

```
backend/
├── crud_usuarios.py      # CRUD de Usuários (Cadastros)
├── crud_cargos.py        # CRUD de Cargos Domésticos
├── crud_diagnosticos.py  # CRUD de Diagnósticos com cálculos
├── requirements.txt      # Dependências Python
└── README.md            # Este arquivo
```

## 🔧 Instalação

### Pré-requisitos

- Python 3.8+
- MySQL 5.7+ ou MariaDB
- pip (gerenciador de pacotes Python)

### Passos

1. **Instalar dependências:**

```bash
pip install -r requirements.txt
```

2. **Configurar banco de dados:**

Execute o script SQL para criar as tabelas:

```bash
mysql -u root -p guia_domestico < ../database/schema.sql
```

3. **Configurar variáveis de ambiente:**

Crie um arquivo `.env` na raiz do backend:

```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=sua_senha
DB_NAME=guia_domestico
```

## 📚 Módulos CRUD

### 1. UsuarioCRUD (`crud_usuarios.py`)

Gerencia operações com usuários (cadastros).

#### Métodos principais:

- `criar_usuario()` - Cria novo usuário
- `obter_usuario()` - Obtém dados de um usuário
- `obter_usuario_por_email()` - Busca usuário por email
- `listar_usuarios()` - Lista usuários com paginação
- `atualizar_usuario()` - Atualiza dados do usuário
- `alterar_senha()` - Altera a senha do usuário
- `deletar_usuario()` - Marca usuário como inativo
- `autenticar_usuario()` - Autentica com email e senha

#### Exemplo de uso:

```python
from crud_usuarios import UsuarioCRUD

# Inicializar
crud = UsuarioCRUD(
    host='localhost',
    user='root',
    password='senha',
    database='guia_domestico'
)

# Conectar
crud.conectar()

# Criar usuário
usuario_id = crud.criar_usuario(
    nome_completo="João Silva",
    email="joao@example.com",
    senha="senha123",
    telefone="11987654321",
    cidade="São Paulo",
    estado="SP"
)

# Autenticar
usuario = crud.autenticar_usuario("joao@example.com", "senha123")

# Desconectar
crud.desconectar()
```

### 2. CargoCRUD (`crud_cargos.py`)

Gerencia operações com cargos domésticos.

#### Métodos principais:

- `criar_cargo()` - Cria novo cargo
- `obter_cargo()` - Obtém dados de um cargo
- `obter_cargo_por_numero()` - Busca cargo por número
- `listar_cargos()` - Lista cargos com paginação
- `buscar_cargos()` - Busca cargos por termo
- `atualizar_cargo()` - Atualiza dados do cargo
- `deletar_cargo()` - Marca cargo como inativo
- `obter_faixa_salarial()` - Obtém faixa salarial

#### Exemplo de uso:

```python
from crud_cargos import CargoCRUD

# Inicializar
crud = CargoCRUD(
    host='localhost',
    user='root',
    password='senha',
    database='guia_domestico'
)

# Conectar
crud.conectar()

# Criar cargo
cargo_id = crud.criar_cargo(
    numero_cargo=1,
    nome_cargo="Faxineira",
    cbo_codigo="5121-15",
    descricao_funcoes="Realiza limpeza profunda de ambientes",
    responsabilidades=["Limpeza de pisos", "Limpeza de vidros"],
    salario_medio=1700.00
)

# Listar cargos
cargos = crud.listar_cargos(limite=10)

# Desconectar
crud.desconectar()
```

### 3. DiagnosticoCRUD (`crud_diagnosticos.py`)

Gerencia diagnósticos com cálculos automáticos de direitos.

#### Métodos principais:

- `criar_diagnostico()` - Cria diagnóstico com cálculos
- `obter_diagnostico()` - Obtém dados do diagnóstico
- `obter_diagnostico_completo()` - Obtém com informações do usuário e cargo
- `listar_diagnosticos_usuario()` - Lista diagnósticos de um usuário
- `atualizar_diagnostico()` - Atualiza diagnóstico
- `arquivar_diagnostico()` - Arquiva diagnóstico
- `deletar_diagnostico()` - Deleta diagnóstico
- `obter_resumo_direitos()` - Obtém resumo dos direitos calculados

#### Cálculos automáticos:

- **Salário por hora** - Baseado em horas/dia e dias/semana
- **Carga horária mensal** - Total de horas trabalhadas por mês
- **Tempo de serviço** - Em meses desde a admissão
- **Férias** - 30 dias a cada 12 meses + 1/3 adicional
- **13º Salário** - Proporcional ao tempo de serviço
- **FGTS** - 8% do salário mensal
- **INSS** - 9% do salário mensal (desconto do empregado)

#### Exemplo de uso:

```python
from crud_diagnosticos import DiagnosticoCRUD
from datetime import date

# Inicializar
crud = DiagnosticoCRUD(
    host='localhost',
    user='root',
    password='senha',
    database='guia_domestico'
)

# Conectar
crud.conectar()

# Criar diagnóstico
diagnostico_id = crud.criar_diagnostico(
    usuario_id=1,
    cargo_id=1,
    salario_mensal=1500.00,
    data_admissao=date(2022, 1, 15),
    horas_por_dia=8,
    dias_por_semana=5
)

# Obter resumo de direitos
resumo = crud.obter_resumo_direitos(diagnostico_id)
print(f"Férias: R$ {resumo['valor_ferias']}")
print(f"13º Salário: R$ {resumo['valor_decimo_terceiro']}")
print(f"FGTS mensal: R$ {resumo['valor_fgts_mensal']}")

# Desconectar
crud.desconectar()
```

## 🔐 Segurança

### Senhas

As senhas são criptografadas usando **bcrypt** com salt aleatório:

```python
# Hash da senha
senha_hash = UsuarioCRUD.hash_senha("senha123")

# Verificar senha
if UsuarioCRUD.verificar_senha("senha123", senha_hash):
    print("Senha correta")
```

### Validações

- Email único no sistema
- Número de cargo único
- Verificação de existência antes de operações
- Soft delete (marcação como inativo) em vez de exclusão física

### Auditoria

Todas as operações são registradas na tabela `auditoria`:

- Tipo de operação (INSERT, UPDATE, DELETE)
- Dados antigos e novos
- Data e hora
- Usuário responsável

## 📊 Banco de Dados

### Tabelas principais

1. **usuarios** - Dados de cadastro dos usuários
2. **cargos** - Informações dos 45+ cargos domésticos
3. **diagnosticos** - Diagnósticos gerados com cálculos
4. **auditoria** - Log de todas as operações

### Views úteis

- `vw_diagnosticos_completo` - Diagnósticos com dados do usuário e cargo
- `vw_usuarios_diagnosticos` - Usuários com contagem de diagnósticos

## 🧪 Testes

Para testar os módulos CRUD:

```bash
python crud_usuarios.py
python crud_cargos.py
python crud_diagnosticos.py
```

Cada módulo possui um exemplo de uso no final do arquivo.

## 📝 Variáveis de Ambiente

Crie um arquivo `.env`:

```env
# Banco de Dados
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=sua_senha
DB_NAME=guia_domestico

# Aplicação
APP_ENV=development
APP_DEBUG=True
SECRET_KEY=sua_chave_secreta_aqui

# JWT
JWT_SECRET_KEY=sua_chave_jwt_aqui
JWT_EXPIRATION_HOURS=24
```

## 🚀 Próximos Passos

1. **API REST** - Criar endpoints Flask para cada CRUD
2. **Autenticação** - Implementar JWT
3. **Validação** - Adicionar validações de entrada
4. **Testes** - Criar testes unitários
5. **Documentação** - Gerar documentação da API (Swagger)
6. **Deploy** - Preparar para produção

## 📞 Suporte

Para dúvidas ou problemas, abra uma issue no repositório GitHub.

---

**Desenvolvido com ❤️ para empoderar trabalhadores domésticos**

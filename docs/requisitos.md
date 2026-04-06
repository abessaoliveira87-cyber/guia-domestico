# 📋 Especificação de Requisitos - Guia Doméstico

## 1. Requisitos Funcionais

### 1.1 Autenticação e Conta

#### RF-001: Cadastro de Usuário
- O sistema deve permitir que novos usuários se cadastrem
- Campos obrigatórios: Nome Completo, Email, Senha, Confirmação de Senha
- Validações:
  - Email único no sistema
  - Senha com mínimo 8 caracteres
  - Aceitar termos de uso

#### RF-002: Login
- Usuários devem fazer login com email e senha
- Manter sessão ativa
- Opção "Lembrar-me"

#### RF-003: Gerenciar Conta
- Alterar dados pessoais (nome, email, telefone, endereço, cidade, estado, CEP)
- Alterar senha com validação de senha atual
- Excluir conta com confirmação de segurança

### 1.2 Diagnóstico

#### RF-004: Gerar Diagnóstico
- Coletar dados do empregado:
  - Cargo (dropdown com 48 opções)
  - Salário atual (R$)
  - Data de admissão
  - Horas por dia
  - Dias por semana
- Calcular automaticamente:
  - Salário por hora
  - Carga horária mensal
  - Tempo de serviço
- Gerar relatório personalizado em PDF

#### RF-005: Armazenar Diagnóstico
- Salvar diagnósticos gerados
- Permitir visualizar histórico
- Permitir baixar relatórios anteriores

### 1.3 Conteúdo Educativo

#### RF-006: Lei 150/2015
- Apresentar direitos e deveres em linguagem simples
- Incluir exemplos práticos
- Avisos e destaques para informações importantes

#### RF-007: Afastamento, Auxílio Doença e Licenças
- Explicar tipos de afastamento
- Informar quem paga
- Detalhar procedimentos

#### RF-008: Cargo e Detalhamento
- Descrever 48 cargos domésticos
- Para cada cargo:
  - Descrição
  - Funções principais
  - O que não é responsabilidade
  - Obrigações
  - Faixa salarial média

#### RF-009: Demissão ou Pedido de Demissão
- Comparar cenários de demissão
- Explicar direitos em cada situação
- Diferenciar período de experiência

#### RF-010: Férias e 13º Salário
- Explicar cálculo de férias
- Explicar cálculo de 13º
- Fornecer exemplos com números reais
- Informar prazos

#### RF-011: INSS e FGTS
- Explicar contribuições
- Listar benefícios
- Informar direitos
- Detalhar situações de saque

#### RF-012: Pagamentos e Descontos
- Explicar folha de pagamento
- Calcular salário por hora
- Detalhar horas extras
- Informar descontos legais e ilegais

### 1.4 Navegação

#### RF-013: Menu Principal
- Acesso fácil a todas as seções
- Breadcrumb de navegação
- Botões "Voltar" e "Próximo"

#### RF-014: Responsividade
- Funcionar em desktop, tablet e mobile
- Menu adaptativo
- Elementos redimensionáveis

## 2. Requisitos Não-Funcionais

### 2.1 Acessibilidade

#### RNF-001: WCAG 2.1 Nível AA
- Contraste mínimo 4.5:1 para texto
- Tamanho mínimo de fonte: 14px
- Elementos interativos: mínimo 44x44px

#### RNF-002: Navegação por Teclado
- Todos os elementos devem ser acessíveis via teclado
- Ordem lógica de tabulação
- Indicador visual de foco

#### RNF-003: Leitores de Tela
- Compatibilidade com NVDA, JAWS, VoiceOver
- Labels apropriadas
- ARIA labels quando necessário

### 2.2 Usabilidade

#### RNF-004: Linguagem Simples
- Sem jargão técnico
- Frases curtas (máximo 15 palavras)
- Explicações contextualizadas

#### RNF-005: Tempo de Carregamento
- Página inicial: máximo 3 segundos
- Outras páginas: máximo 2 segundos
- Otimização de imagens

#### RNF-006: Compatibilidade
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

### 2.3 Segurança

#### RNF-007: Proteção de Dados
- Criptografia de senhas (bcrypt)
- HTTPS obrigatório
- Proteção contra CSRF
- Sanitização de inputs

#### RNF-008: Privacidade
- Conformidade com LGPD
- Política de privacidade clara
- Opção de deletar dados

### 2.4 Performance

#### RNF-009: Otimização
- Compressão de assets
- Cache de recursos estáticos
- Lazy loading de imagens
- Minificação de CSS/JS

#### RNF-010: SEO
- Meta tags apropriadas
- URLs amigáveis
- Sitemap XML
- Robots.txt

## 3. Requisitos de Dados

### 3.1 Usuário

```
- ID (UUID)
- Nome Completo
- Email (único)
- Senha (hash)
- Telefone (opcional)
- Endereço (opcional)
- Cidade (opcional)
- Estado (opcional)
- CEP (opcional)
- Data de Cadastro
- Data de Atualização
- Status (ativo/inativo)
```

### 3.2 Diagnóstico

```
- ID (UUID)
- ID do Usuário (FK)
- Cargo
- Salário Mensal
- Data de Admissão
- Horas por Dia
- Dias por Semana
- Salário por Hora (calculado)
- Carga Horária Mensal (calculada)
- Tempo de Serviço (calculado)
- Data de Geração
- Status (ativo/arquivado)
```

### 3.3 Cargo

```
- ID
- Nome
- Descrição
- Funções Principais (array)
- Responsabilidades (array)
- Não Responsabilidades (array)
- Obrigações (array)
- Salário Mínimo
- Salário Máximo
- Salário Médio
```

## 4. Casos de Uso

### CU-001: Novo Usuário Conhecer Seus Direitos

1. Usuário acessa a plataforma
2. Realiza cadastro
3. Faz login
4. Acessa "Lei 150/2015"
5. Lê direitos e deveres
6. Consulta "Pagamentos e Descontos"
7. Compreende seus direitos

### CU-002: Calcular Direitos Trabalhistas

1. Usuário faz login
2. Clica em "Gerar Diagnóstico"
3. Preenche dados (cargo, salário, data admissão, horas/dia, dias/semana)
4. Sistema calcula automaticamente
5. Usuário visualiza resultados
6. Baixa PDF com relatório personalizado

### CU-003: Entender Demissão

1. Usuário acessa "Demissão ou Pedido de Demissão"
2. Lê comparação entre cenários
3. Consulta seus direitos específicos
4. Entende diferenças entre demissão e pedido

## 5. Critérios de Aceitação

### CA-001: Cadastro Bem-Sucedido
- ✅ Usuário consegue se cadastrar com dados válidos
- ✅ Recebe confirmação de email
- ✅ Pode fazer login imediatamente
- ✅ Dados são salvos corretamente

### CA-002: Diagnóstico Preciso
- ✅ Cálculos estão corretos
- ✅ PDF é gerado sem erros
- ✅ Dados são salvos para consultas futuras
- ✅ Relatório é compreensível

### CA-003: Acessibilidade
- ✅ Passa em teste WAVE
- ✅ Funciona com leitores de tela
- ✅ Navegável apenas com teclado
- ✅ Contraste adequado

### CA-004: Usabilidade
- ✅ Usuários com pouca escolaridade compreendem
- ✅ Tempo de aprendizado < 5 minutos
- ✅ Sem necessidade de manual
- ✅ Linguagem clara e simples

## 6. Priorização

### Alta Prioridade
- RF-001, RF-002, RF-003 (Autenticação)
- RF-004, RF-005 (Diagnóstico)
- RF-006 (Lei 150/2015)
- RNF-001, RNF-004 (Acessibilidade e Usabilidade)

### Média Prioridade
- RF-007 a RF-012 (Conteúdo)
- RF-013, RF-014 (Navegação)
- RNF-007, RNF-008 (Segurança)

### Baixa Prioridade
- RNF-009, RNF-010 (Performance e SEO avançado)
- Integrações externas
- Funcionalidades extras

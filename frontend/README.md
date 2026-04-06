# Frontend - Guia Doméstico

Frontend da plataforma Guia Doméstico com interface responsiva e acessível.

## 📋 Estrutura

```
frontend/
├── index.html          # Página principal com todas as seções
├── css/               # Estilos CSS (a ser separado)
├── js/                # Scripts JavaScript (a ser separado)
├── assets/            # Imagens e ícones
└── README.md          # Este arquivo
```

## 🎨 Características

### Design

- **Responsivo** - Funciona em desktop, tablet e mobile
- **Acessível** - Contraste alto, navegação por teclado
- **Moderno** - Gradientes, animações suaves, cards
- **Intuitivo** - Linguagem simples, sem jargão técnico

### Paleta de Cores

| Cor | Código | Uso |
|-----|--------|-----|
| Azul Principal | #4682B4 | Cabeçalho, botões, destaques |
| Azul Escuro | #2C5282 | Gradiente, sombras |
| Cinza Claro | #f9f9f9 | Fundo de cards |
| Branco | #ffffff | Fundo principal |
| Vermelho | #CC0000 | Avisos, atenção |
| Verde | #27AE60 | Ações positivas |
| Amarelo | #FFF3CD | Avisos importantes |

### Tipografia

- **Fonte Principal**: Segoe UI, Tahoma, Geneva, Verdana, sans-serif
- **Tamanho Base**: 14px
- **Tamanho Mínimo**: 12px
- **Tamanho Máximo**: 32px (títulos)

## 📱 Páginas Implementadas

### 1. Home
Página inicial com:
- Diagnóstico do usuário (cards informativos)
- Seção para baixar diagnóstico em PDF
- Conteúdos disponíveis (grid de cards)

### 2. Cadastro
Formulário de registro com:
- Nome completo
- Email
- Senha
- Confirmação de senha
- Termos de uso

### 3. Login
Autenticação com:
- Email
- Senha
- Lembrar-me
- Link de recuperação de senha

### 4. Gerenciar Conta
Painel de controle com:
- Informações do perfil
- Alterar dados pessoais
- Alterar senha
- Excluir conta

### 5. Gerar Diagnóstico
Formulário com:
- Seleção de cargo (dropdown)
- Salário mensal
- Data de admissão
- Horas por dia
- Dias por semana

### 6. Lei 150/2015
Conteúdo educativo sobre:
- Direitos do empregado doméstico
- Deveres do empregado doméstico
- Informações importantes

### 7. Afastamento, Auxílio Doença e Licenças
Informações sobre:
- Tipos de afastamento
- Quem paga
- Como dar entrada
- Prazos

### 8. Cargo e Detalhamento
Descrição de cargos com:
- Funções principais
- Responsabilidades
- O que não é responsabilidade
- Faixa salarial

### 9. Demissão ou Pedido de Demissão
Comparação entre:
- Demissão pelo empregador
- Pedido de demissão
- Direitos em cada caso
- Período de experiência

### 10. Férias e 13º Salário
Cálculos e informações sobre:
- Como calcular férias
- Adicional de 1/3
- 13º salário proporcional
- Prazos

### 11. INSS e FGTS
Explicação sobre:
- Contribuições INSS
- Benefícios INSS
- FGTS
- Situações de saque

### 12. Pagamentos e Descontos
Detalhes sobre:
- Folha de pagamento
- Horas extras
- Adicional noturno
- Descontos legais

## 🔧 Instalação

### Pré-requisitos

- Navegador moderno (Chrome, Firefox, Safari, Edge)
- Servidor web (opcional para desenvolvimento local)

### Executar Localmente

**Opção 1: Abrir arquivo diretamente**
```bash
# Simplesmente abra o arquivo index.html no navegador
open frontend/index.html
```

**Opção 2: Usar servidor local (recomendado)**
```bash
# Python 3
python -m http.server 8000

# Node.js
npx http-server

# PHP
php -S localhost:8000
```

Acesse `http://localhost:8000/frontend/` no navegador.

## 📁 Estrutura do HTML

```html
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Meta tags, título, estilos -->
</head>
<body>
    <!-- Cabeçalho -->
    <header>
        <!-- Logo, informações do usuário, botões -->
    </header>
    
    <!-- Container principal -->
    <div class="container">
        <!-- Página 1: Home -->
        <div class="page active" id="home">
            <!-- Conteúdo da home -->
        </div>
        
        <!-- Página 2: Cadastro -->
        <div class="page" id="cadastro">
            <!-- Formulário de cadastro -->
        </div>
        
        <!-- ... outras páginas ... -->
    </div>
    
    <!-- Scripts JavaScript -->
    <script>
        // Lógica de navegação
        // Validações de formulário
        // Interações
    </script>
</body>
</html>
```

## 🎯 Funcionalidades JavaScript

### Navegação
- Alternar entre páginas
- Manter histórico de navegação
- Breadcrumb de localização

### Formulários
- Validação de email
- Confirmação de senha
- Campos obrigatórios
- Mensagens de erro/sucesso

### Interatividade
- Mostrar/ocultar senha
- Expandir/colapsar seções
- Modais de confirmação
- Tooltips informativos

### Armazenamento
- LocalStorage para dados temporários
- SessionStorage para sessão
- Cookies para preferências

## 🔐 Segurança

- ✅ Validação de entrada no cliente
- ✅ Sanitização de dados
- ✅ HTTPS recomendado
- ✅ Proteção contra XSS
- ✅ CSRF tokens (quando integrado com backend)

## 📊 Performance

- ✅ CSS inline para carregamento rápido
- ✅ JavaScript otimizado
- ✅ Imagens otimizadas
- ✅ Lazy loading (quando aplicável)
- ✅ Minificação (para produção)

## ♿ Acessibilidade

- ✅ Contraste WCAG AA
- ✅ Navegação por teclado
- ✅ Labels em formulários
- ✅ ARIA labels
- ✅ Texto alternativo em imagens
- ✅ Leitor de tela compatível

## 🚀 Próximos Passos

### Curto Prazo
1. **Separar CSS** - Extrair estilos para arquivo separado
2. **Separar JavaScript** - Extrair scripts para arquivo separado
3. **Adicionar assets** - Ícones, imagens, logos
4. **Integrar API** - Conectar com backend Python

### Médio Prazo
1. **Autenticação** - Login/logout com JWT
2. **Formulários dinâmicos** - Integração com backend
3. **Relatórios PDF** - Gerar diagnósticos em PDF
4. **Responsividade** - Testes em vários dispositivos

### Longo Prazo
1. **Framework** - Migrar para React/Vue/Angular
2. **PWA** - Tornar aplicativo instalável
3. **Offline** - Funcionar sem internet
4. **Testes** - Testes unitários e E2E

## 📝 Convenções de Código

### HTML
- Usar IDs para elementos únicos
- Usar classes para estilos reutilizáveis
- Estrutura semântica
- Comentários para seções principais

### CSS
- Organizar por seções (header, main, footer)
- Usar variáveis CSS para cores
- Mobile-first
- Comentários para regras complexas

### JavaScript
- Usar camelCase para variáveis
- Comentários em funções complexas
- Evitar variáveis globais
- Usar const/let em vez de var

## 🧪 Testes

### Testes Manuais
- [ ] Testar em Chrome
- [ ] Testar em Firefox
- [ ] Testar em Safari
- [ ] Testar em Edge
- [ ] Testar em mobile
- [ ] Testar navegação por teclado
- [ ] Testar com leitor de tela

### Testes de Validação
- [ ] Validar HTML
- [ ] Validar CSS
- [ ] Validar acessibilidade (WAVE)
- [ ] Testar performance (Lighthouse)

## 📞 Suporte

Para dúvidas ou problemas, abra uma issue no repositório GitHub.

## 📄 Licença

Este projeto está licenciado sob a Licença MIT.

---

**Desenvolvido com ❤️ para empoderar trabalhadores domésticos**

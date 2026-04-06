# 🏠 Guia Doméstico

**Plataforma educativa para empregados domésticos conhecerem seus direitos e deveres**

Uma solução digital inclusiva que capacita empregados domésticos a compreender a legislação trabalhista, seus direitos, deveres e benefícios de forma simples e acessível.

## 📋 Sobre o Projeto

O Guia Doméstico é uma plataforma web desenvolvida com foco em **acessibilidade** e **usabilidade** para pessoas com pouca escolaridade. O projeto oferece informações completas sobre a Lei 150/2015 (Lei do Empregado Doméstico) em linguagem clara e simples.

### 🎯 Objetivos

- ✅ Informar empregados domésticos sobre seus direitos e deveres
- ✅ Facilitar o cálculo de direitos trabalhistas (férias, 13º, FGTS, INSS)
- ✅ Gerar diagnósticos personalizados baseados na situação do empregado
- ✅ Disponibilizar informações em formato acessível e compreensível
- ✅ Empoderar trabalhadores domésticos com conhecimento legal

## 🏗️ Arquitetura do Projeto

### Páginas Principais

1. **Cadastro** - Criação de conta com nome, email e senha
2. **Gerenciar Conta** - Alterar dados, reset de senha, excluir conta
3. **Home** - Menu principal com diagnóstico e conteúdos disponíveis
4. **Gerar Diagnóstico** - Cálculo de direitos baseado em dados do empregado
5. **Lei 150/2015** - Direitos e deveres em linguagem simples
6. **Afastamento, Auxílio Doença e Licenças** - Tipos de afastamento e benefícios
7. **Cargo e Detalhamento** - Descrição de 48 cargos domésticos (funções, responsabilidades, salário)
8. **Demissão ou Pedido de Demissão** - Diferenças e direitos em cada cenário
9. **Férias e 13º Salário** - Cálculos, prazos e regras
10. **INSS e FGTS** - Contribuições, benefícios e direitos
11. **Pagamentos e Descontos** - Folha de pagamento, horas extras, descontos legais

## 🎨 Wireframes

Todos os wireframes foram desenvolvidos com foco em:

- **Acessibilidade**: Contraste alto, tipografia clara, elementos grandes
- **Usabilidade**: Navegação simples, linguagem clara, sem jargão técnico
- **Responsividade**: Funciona em desktop, tablet e mobile
- **Inclusão**: Projetado para pessoas com pouca escolaridade

### Visualizar Wireframes

Os wireframes de todas as 11 páginas estão disponíveis na pasta `/wireframes`:

- `wireframe_01_cadastro_correto.png` - Página de Cadastro
- `wireframe_02_gerenciar_correto.png` - Gerenciar Conta
- `wireframe_03_home_correto.png` - Home
- `wireframe_04_diagnostico_correto.png` - Gerar Diagnóstico
- `wireframe_05_lei_correto.png` - Lei 150/2015
- `wireframe_06_afastamento_correto.png` - Afastamento e Licenças
- `wireframe_07_cargo_correto.png` - Cargo e Detalhamento
- `wireframe_08_demissao_correto.png` - Demissão
- `wireframe_09_ferias_correto.png` - Férias e 13º
- `wireframe_10_inss_fgts_correto.png` - INSS e FGTS
- `wireframe_11_pagamentos_correto.png` - Pagamentos e Descontos

## 🎯 Princípios de Design

### Acessibilidade

- **Tipografia Clara**: Fonte Segoe UI, tamanho mínimo 14px
- **Contraste Alto**: Cores bem definidas (azul #4682B4, branco, cinza)
- **Navegação Intuitiva**: Botões grandes (mínimo 44x44px), caminho claro
- **Feedback Visual**: Mudanças de cor, sombras, animações suaves

### Usabilidade para Pouca Escolaridade

- **Linguagem Simples**: Sem jargão técnico, frases curtas
- **Exemplos Práticos**: Números reais, cálculos demonstrados
- **Ícones Visuais**: Facilita identificação rápida de seções
- **Avisos Destacados**: Informações críticas em caixas coloridas
- **Confirmações**: Ações críticas requerem confirmação do usuário

## 🛠️ Tecnologias (Planejadas)

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla ou Framework)
- **Backend**: Node.js/Express ou Python/FastAPI
- **Banco de Dados**: PostgreSQL ou MySQL
- **Autenticação**: JWT ou OAuth
- **Relatórios**: Geração de PDF com dados personalizados

## 📊 Dados do Projeto

### 48 Cargos Domésticos Cobertos

O projeto contempla 48 diferentes cargos domésticos, incluindo:

- Faxineira
- Cozinheira
- Babá
- Jardineiro
- Motorista
- Segurança
- Cuidador de Idosos
- E muitos outros...

Cada cargo possui:
- Descrição detalhada
- Funções principais
- Responsabilidades
- Obrigações legais
- Faixa salarial média

## 📱 Responsividade

- ✅ Desktop (1200px+)
- ✅ Tablet (768px - 1199px)
- ✅ Mobile (até 767px)

## 🚀 Como Começar

### Pré-requisitos

- Node.js 14+ ou Python 3.8+
- Git
- Navegador moderno

### Instalação

```bash
# Clonar o repositório
git clone https://github.com/abessaoliveira87-cyber/guia-domestico.git

# Entrar no diretório
cd guia-domestico

# Instalar dependências (quando aplicável)
npm install
# ou
pip install -r requirements.txt
```

### Executar Localmente

```bash
# Iniciar servidor de desenvolvimento
npm start
# ou
python app.py
```

Acesse `http://localhost:3000` (ou porta configurada) no seu navegador.

## 📖 Documentação

- [Especificação de Requisitos](docs/requisitos.md)
- [Guia de Desenvolvimento](docs/desenvolvimento.md)
- [Guia de Contribuição](CONTRIBUTING.md)

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor:

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 👥 Autores

- **Desenvolvido com foco em inclusão social**
- Baseado na Lei 150/2015 (Lei do Empregado Doméstico)

## 📞 Contato e Suporte

Para dúvidas, sugestões ou relatar problemas:

- Abra uma [Issue](https://github.com/abessaoliveira87-cyber/guia-domestico/issues)
- Envie um email para o mantenedor do projeto

## 🙏 Agradecimentos

- Lei 150/2015 - Legislação do Empregado Doméstico
- Comunidade de desenvolvimento inclusivo
- Todos os que acreditam em igualdade de direitos trabalhistas

---

**Desenvolvido com ❤️ para empoderar trabalhadores domésticos**

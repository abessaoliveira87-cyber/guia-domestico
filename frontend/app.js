/**
 * Aplicação Principal - Guia Doméstico
 * Gerencia navegação, formulários e integração com API
 */

// ============================================
// VARIÁVEIS GLOBAIS
// ============================================

let usuarioLogado = null;
let diagnosticoAtual = null;

// ============================================
// INICIALIZAÇÃO
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    console.log('Aplicação iniciada');
    
    // Verificar se usuário está logado
    verificarAutenticacao();
    
    // Configurar event listeners
    configurarEventListeners();
    
    // Carregar dados do usuário se logado
    if (usuarioLogado) {
        carregarDadosUsuario();
    }
});

// ============================================
// AUTENTICAÇÃO
// ============================================

function verificarAutenticacao() {
    const token = Armazenamento.obter('access_token');
    const usuario = Armazenamento.obter('usuario');
    
    if (token && usuario) {
        usuarioLogado = usuario;
        mostrarPagina('home');
    } else {
        mostrarPagina('landing');
    }
}

// ============================================
// NAVEGAÇÃO
// ============================================

function mostrarPagina(pagina) {
    // Ocultar todas as páginas
    document.querySelectorAll('.page').forEach(p => {
        p.classList.remove('active');
    });
    
    // Mostrar página solicitada
    const elemento = document.getElementById(pagina);
    if (elemento) {
        elemento.classList.add('active');
        console.log(`Página exibida: ${pagina}`);
    }
}

// ============================================
// EVENT LISTENERS
// ============================================

function configurarEventListeners() {
    // Botões de navegação
    document.getElementById('btnComecaAgora')?.addEventListener('click', () => {
        if (usuarioLogado) {
            mostrarPagina('diagnostico');
        } else {
            mostrarPagina('cadastro');
        }
    });
    
    document.getElementById('btnPerfil')?.addEventListener('click', () => {
        mostrarPagina('gerenciar');
    });
    
    document.getElementById('btnSair')?.addEventListener('click', fazerLogout);
    
    // Formulários
    document.getElementById('formCadastro')?.addEventListener('submit', handleCadastro);
    document.getElementById('formLogin')?.addEventListener('submit', handleLogin);
    document.getElementById('formDiagnostico')?.addEventListener('submit', handleDiagnostico);
    document.getElementById('formPerfil')?.addEventListener('submit', handleAtualizarPerfil);
    document.getElementById('formSenha')?.addEventListener('submit', handleAlterarSenha);
    document.getElementById('formDeletar')?.addEventListener('submit', handleDeletarConta);
    
    // Tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const tabName = e.target.dataset.tab;
            mudarTab(tabName);
        });
    });
    
    // Links de conteúdo
    document.querySelectorAll('.content-card .btn-secondary').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const titulo = e.target.closest('.content-card').querySelector('h3').textContent;
            console.log(`Acessando: ${titulo}`);
            // TODO: Implementar navegação para páginas de conteúdo
        });
    });
}

// ============================================
// FORMULÁRIO: CADASTRO
// ============================================

async function handleCadastro(e) {
    e.preventDefault();
    
    const nome = document.getElementById('nome').value;
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const confirmaSenha = document.getElementById('confirmaSenha').value;
    
    // Validações
    if (!Validacao.nome(nome)) {
        Notificacao.erro('Nome deve ter no mínimo 3 caracteres');
        return;
    }
    
    if (!Validacao.email(email)) {
        Notificacao.erro('Email inválido');
        return;
    }
    
    const validacaoSenha = Validacao.senha(senha);
    if (!validacaoSenha.valida) {
        Notificacao.erro(validacaoSenha.mensagem);
        return;
    }
    
    if (senha !== confirmaSenha) {
        Notificacao.erro('Senhas não conferem');
        return;
    }
    
    try {
        const resposta = await HTTP.post(
            ConfigHelper.getApiUrl(API_ENDPOINTS.auth.register),
            {
                nome_completo: nome,
                email: email,
                senha: senha
            }
        );
        
        if (resposta.usuario_id) {
            Notificacao.sucesso('Conta criada com sucesso! Faça login.');
            document.getElementById('formCadastro').reset();
            setTimeout(() => mostrarPagina('login'), 1500);
        } else {
            Notificacao.erro(resposta.erro || 'Erro ao criar conta');
        }
    } catch (erro) {
        Notificacao.erro('Erro ao conectar com servidor');
        console.error(erro);
    }
}

// ============================================
// FORMULÁRIO: LOGIN
// ============================================

async function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('emailLogin').value;
    const senha = document.getElementById('senhaLogin').value;
    
    if (!Validacao.email(email)) {
        Notificacao.erro('Email inválido');
        return;
    }
    
    if (!senha) {
        Notificacao.erro('Senha obrigatória');
        return;
    }
    
    try {
        const resposta = await HTTP.post(
            ConfigHelper.getApiUrl(API_ENDPOINTS.auth.login),
            {
                email: email,
                senha: senha
            }
        );
        
        if (resposta.access_token) {
            // Salvar token e dados do usuário
            Armazenamento.salvar('access_token', resposta.access_token);
            Armazenamento.salvar('usuario', resposta.usuario);
            
            usuarioLogado = resposta.usuario;
            
            Notificacao.sucesso('Login realizado com sucesso!');
            document.getElementById('formLogin').reset();
            
            setTimeout(() => {
                mostrarPagina('home');
                carregarDadosUsuario();
            }, 1000);
        } else {
            Notificacao.erro(resposta.erro || 'Email ou senha incorretos');
        }
    } catch (erro) {
        Notificacao.erro('Erro ao conectar com servidor');
        console.error(erro);
    }
}

// ============================================
// FORMULÁRIO: DIAGNÓSTICO
// ============================================

async function handleDiagnostico(e) {
    e.preventDefault();
    
    const cargoId = document.getElementById('cargo').value;
    const salario = parseFloat(document.getElementById('salario').value);
    const dataAdmissao = document.getElementById('dataAdmissao').value;
    const horasPorDia = parseFloat(document.getElementById('horasPorDia').value);
    const diasPorSemana = parseInt(document.getElementById('diasPorSemana').value);
    
    // Validações
    if (!cargoId) {
        Notificacao.erro('Selecione um cargo');
        return;
    }
    
    if (salario <= 0) {
        Notificacao.erro('Salário deve ser maior que zero');
        return;
    }
    
    if (!Validacao.data(dataAdmissao)) {
        Notificacao.erro('Data de admissão inválida');
        return;
    }
    
    if (horasPorDia <= 0 || diasPorSemana <= 0) {
        Notificacao.erro('Horas e dias devem ser maiores que zero');
        return;
    }
    
    try {
        const token = Armazenamento.obter('access_token');
        const resposta = await HTTP.post(
            ConfigHelper.getApiUrl(API_ENDPOINTS.diagnosticos.criar),
            {
                cargo_id: parseInt(cargoId),
                salario_mensal: salario,
                data_admissao: dataAdmissao,
                horas_por_dia: horasPorDia,
                dias_por_semana: diasPorSemana
            },
            {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        );
        
        if (resposta.diagnostico_id) {
            diagnosticoAtual = resposta;
            Armazenamento.salvar('diagnostico_atual', resposta);
            
            Notificacao.sucesso('Diagnóstico gerado com sucesso!');
            document.getElementById('formDiagnostico').reset();
            
            setTimeout(() => {
                mostrarPagina('home');
                exibirDiagnostico();
            }, 1000);
        } else {
            Notificacao.erro(resposta.erro || 'Erro ao gerar diagnóstico');
        }
    } catch (erro) {
        Notificacao.erro('Erro ao conectar com servidor');
        console.error(erro);
    }
}

// ============================================
// FORMULÁRIO: ATUALIZAR PERFIL
// ============================================

async function handleAtualizarPerfil(e) {
    e.preventDefault();
    
    const nome = document.getElementById('nomePerfil').value;
    const email = document.getElementById('emailPerfil').value;
    const telefone = document.getElementById('telefonePerfil').value;
    const endereco = document.getElementById('enderecoPerfil').value;
    const cidade = document.getElementById('cidadePerfil').value;
    const estado = document.getElementById('estadoPerfil').value;
    const cep = document.getElementById('cepPerfil').value;
    
    // Validações
    if (!Validacao.nome(nome)) {
        Notificacao.erro('Nome inválido');
        return;
    }
    
    if (!Validacao.email(email)) {
        Notificacao.erro('Email inválido');
        return;
    }
    
    try {
        const token = Armazenamento.obter('access_token');
        const resposta = await HTTP.put(
            ConfigHelper.getApiUrl(API_ENDPOINTS.usuarios.atualizar),
            {
                nome_completo: nome,
                telefone: telefone,
                endereco: endereco,
                cidade: cidade,
                estado: estado,
                cep: cep
            },
            {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        );
        
        if (resposta.mensagem) {
            Notificacao.sucesso('Perfil atualizado com sucesso!');
            usuarioLogado.nome_completo = nome;
            Armazenamento.salvar('usuario', usuarioLogado);
        } else {
            Notificacao.erro(resposta.erro || 'Erro ao atualizar perfil');
        }
    } catch (erro) {
        Notificacao.erro('Erro ao conectar com servidor');
        console.error(erro);
    }
}

// ============================================
// FORMULÁRIO: ALTERAR SENHA
// ============================================

async function handleAlterarSenha(e) {
    e.preventDefault();
    
    const senhaAtual = document.getElementById('senhaAtual').value;
    const senhaNova = document.getElementById('senhaNova').value;
    const confirmaSenhaNova = document.getElementById('confirmaSenhaNova').value;
    
    // Validações
    if (!senhaAtual) {
        Notificacao.erro('Senha atual obrigatória');
        return;
    }
    
    const validacaoSenha = Validacao.senha(senhaNova);
    if (!validacaoSenha.valida) {
        Notificacao.erro(validacaoSenha.mensagem);
        return;
    }
    
    if (senhaNova !== confirmaSenhaNova) {
        Notificacao.erro('Senhas não conferem');
        return;
    }
    
    try {
        const token = Armazenamento.obter('access_token');
        const resposta = await HTTP.post(
            ConfigHelper.getApiUrl(API_ENDPOINTS.usuarios.alterarSenha),
            {
                senha_atual: senhaAtual,
                senha_nova: senhaNova
            },
            {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        );
        
        if (resposta.mensagem) {
            Notificacao.sucesso('Senha alterada com sucesso!');
            document.getElementById('formSenha').reset();
        } else {
            Notificacao.erro(resposta.erro || 'Erro ao alterar senha');
        }
    } catch (erro) {
        Notificacao.erro('Erro ao conectar com servidor');
        console.error(erro);
    }
}

// ============================================
// FORMULÁRIO: DELETAR CONTA
// ============================================

async function handleDeletarConta(e) {
    e.preventDefault();
    
    const confirmacao = confirm('Tem certeza que deseja deletar sua conta? Esta ação é irreversível!');
    if (!confirmacao) return;
    
    const senha = document.getElementById('senhaConfirmacao').value;
    
    if (!senha) {
        Notificacao.erro('Senha obrigatória');
        return;
    }
    
    try {
        const token = Armazenamento.obter('access_token');
        const resposta = await HTTP.delete(
            ConfigHelper.getApiUrl(API_ENDPOINTS.usuarios.deletar),
            {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        );
        
        if (resposta.mensagem) {
            Notificacao.sucesso('Conta deletada com sucesso!');
            fazerLogout();
        } else {
            Notificacao.erro(resposta.erro || 'Erro ao deletar conta');
        }
    } catch (erro) {
        Notificacao.erro('Erro ao conectar com servidor');
        console.error(erro);
    }
}

// ============================================
// FUNÇÕES AUXILIARES
// ============================================

function carregarDadosUsuario() {
    if (usuarioLogado) {
        // Atualizar header com nome do usuário
        const headerInfo = document.querySelector('.header-info');
        if (headerInfo) {
            headerInfo.innerHTML = `
                <h2>${usuarioLogado.nome_completo}</h2>
                <p>${usuarioLogado.email}</p>
            `;
        }
        
        // Carregar diagnóstico se existir
        const diagnostico = Armazenamento.obter('diagnostico_atual');
        if (diagnostico) {
            diagnosticoAtual = diagnostico;
            exibirDiagnostico();
        }
    }
}

function exibirDiagnostico() {
    if (diagnosticoAtual && diagnosticoAtual.calculos) {
        const calc = diagnosticoAtual.calculos;
        
        document.getElementById('diagCargo').textContent = 'Cargo';
        document.getElementById('diagSalario').textContent = Formatacao.moeda(diagnosticoAtual.salario_mensal);
        document.getElementById('diagCargaHoraria').textContent = `${calc.carga_horaria_mensal.toFixed(0)}h`;
        document.getElementById('diagTempoServico').textContent = `${calc.tempo_servico_meses}m`;
        document.getElementById('diagSalarioHora').textContent = Formatacao.moeda(calc.salario_por_hora);
        document.getElementById('diagDataAdmissao').textContent = Formatacao.data(diagnosticoAtual.data_admissao);
    }
}

function mudarTab(tabName) {
    // Remover active de todos os tabs e conteúdos
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    // Adicionar active ao tab e conteúdo selecionado
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    document.getElementById(`tab-${tabName}`).classList.add('active');
}

function fazerLogout() {
    const confirmacao = confirm('Tem certeza que deseja fazer logout?');
    if (!confirmacao) return;
    
    Armazenamento.remover('access_token');
    Armazenamento.remover('usuario');
    Armazenamento.remover('diagnostico_atual');
    
    usuarioLogado = null;
    diagnosticoAtual = null;
    
    Notificacao.sucesso('Logout realizado com sucesso!');
    setTimeout(() => mostrarPagina('landing'), 1000);
}

// ============================================
// NOTIFICAÇÕES MELHORADAS
// ============================================

const NotificacaoUI = {
    mostrar: (mensagem, tipo = 'info') => {
        const div = document.createElement('div');
        div.className = `notificacao notificacao-${tipo}`;
        div.textContent = mensagem;
        div.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            background-color: ${tipo === 'sucesso' ? '#4CAF50' : tipo === 'erro' ? '#F44336' : '#2196F3'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(div);
        
        setTimeout(() => {
            div.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => div.remove(), 300);
        }, 3000);
    }
};

// Sobrescrever Notificacao.sucesso e Notificacao.erro
Notificacao.sucesso = (msg) => NotificacaoUI.mostrar(msg, 'sucesso');
Notificacao.erro = (msg) => NotificacaoUI.mostrar(msg, 'erro');
Notificacao.info = (msg) => NotificacaoUI.mostrar(msg, 'info');

// Adicionar animações CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

console.log('App.js carregado com sucesso');

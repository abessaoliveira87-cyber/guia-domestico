/**
 * Configuração Frontend - Guia Doméstico
 * Define URLs da API e constantes da aplicação
 */

// Configuração de Ambiente
const ENV = {
    development: {
        apiUrl: 'http://localhost:5000/api',
        appName: 'Guia Doméstico - Dev',
        debug: true
    },
    production: {
        apiUrl: 'https://api.guia-domestico.com.br/api',
        appName: 'Guia Doméstico',
        debug: false
    }
};

// Detectar ambiente
const currentEnv = window.location.hostname === 'localhost' ? 'development' : 'production';
const config = ENV[currentEnv];

// Endpoints da API
const API_ENDPOINTS = {
    // Autenticação
    auth: {
        login: '/auth/login',
        logout: '/auth/logout',
        register: '/auth/register',
        refresh: '/auth/refresh'
    },
    
    // Usuários
    usuarios: {
        perfil: '/usuarios/perfil',
        atualizar: '/usuarios/atualizar',
        alterarSenha: '/usuarios/alterar-senha',
        deletar: '/usuarios/deletar'
    },
    
    // Cargos
    cargos: {
        listar: '/cargos',
        obter: '/cargos/:id',
        buscar: '/cargos/buscar'
    },
    
    // Diagnósticos
    diagnosticos: {
        criar: '/diagnosticos',
        listar: '/diagnosticos',
        obter: '/diagnosticos/:id',
        atualizar: '/diagnosticos/:id',
        deletar: '/diagnosticos/:id',
        pdf: '/diagnosticos/:id/pdf'
    }
};

// Constantes da Aplicação
const APP_CONSTANTS = {
    // Validações
    validacao: {
        senhaMinima: 8,
        nomeMinimo: 3,
        emailRegex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    },
    
    // Paginação
    paginacao: {
        itensPorPagina: 10,
        maxPaginas: 10
    },
    
    // Cargos
    cargos: {
        total: 45,
        categorias: [
            'Limpeza',
            'Cuidados',
            'Cozinha',
            'Manutenção',
            'Segurança',
            'Transporte'
        ]
    },
    
    // Mensagens
    mensagens: {
        sucesso: 'Operação realizada com sucesso!',
        erro: 'Ocorreu um erro. Tente novamente.',
        carregando: 'Carregando...',
        confirmacao: 'Tem certeza que deseja continuar?'
    },
    
    // Timeouts
    timeouts: {
        requisicao: 30000,  // 30 segundos
        sessao: 3600000,    // 1 hora
        notificacao: 5000   // 5 segundos
    }
};

// Funções Auxiliares
const ConfigHelper = {
    /**
     * Obter URL completa da API
     * @param {string} endpoint - Endpoint relativo
     * @returns {string} URL completa
     */
    getApiUrl: (endpoint) => {
        return config.apiUrl + endpoint;
    },
    
    /**
     * Obter URL completa de um recurso
     * @param {string} endpoint - Endpoint com :id
     * @param {string|number} id - ID do recurso
     * @returns {string} URL completa
     */
    getApiUrlWithId: (endpoint, id) => {
        return config.apiUrl + endpoint.replace(':id', id);
    },
    
    /**
     * Verificar se está em desenvolvimento
     * @returns {boolean}
     */
    isDevelopment: () => {
        return currentEnv === 'development';
    },
    
    /**
     * Log com prefixo
     * @param {string} mensagem
     * @param {*} dados
     */
    log: (mensagem, dados = null) => {
        if (config.debug) {
            console.log(`[${config.appName}] ${mensagem}`, dados || '');
        }
    },
    
    /**
     * Erro com prefixo
     * @param {string} mensagem
     * @param {*} erro
     */
    error: (mensagem, erro = null) => {
        console.error(`[${config.appName}] ERRO: ${mensagem}`, erro || '');
    }
};

// Exportar para uso global
window.config = config;
window.API_ENDPOINTS = API_ENDPOINTS;
window.APP_CONSTANTS = APP_CONSTANTS;
window.ConfigHelper = ConfigHelper;

// Log de inicialização
ConfigHelper.log(`Aplicação iniciada em modo ${currentEnv}`);

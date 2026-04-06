/**
 * Utilitários - Guia Doméstico
 * Funções auxiliares para o frontend
 */

// ============================================
// VALIDAÇÕES
// ============================================

const Validacao = {
    /**
     * Validar email
     * @param {string} email
     * @returns {boolean}
     */
    email: (email) => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    },
    
    /**
     * Validar senha
     * @param {string} senha
     * @returns {object} { valida: boolean, mensagem: string }
     */
    senha: (senha) => {
        if (senha.length < 8) {
            return { valida: false, mensagem: 'Senha deve ter no mínimo 8 caracteres' };
        }
        if (!/[A-Z]/.test(senha)) {
            return { valida: false, mensagem: 'Senha deve conter pelo menos uma letra maiúscula' };
        }
        if (!/[0-9]/.test(senha)) {
            return { valida: false, mensagem: 'Senha deve conter pelo menos um número' };
        }
        return { valida: true, mensagem: 'Senha válida' };
    },
    
    /**
     * Validar nome
     * @param {string} nome
     * @returns {boolean}
     */
    nome: (nome) => {
        return nome.trim().length >= 3;
    },
    
    /**
     * Validar telefone
     * @param {string} telefone
     * @returns {boolean}
     */
    telefone: (telefone) => {
        const regex = /^(\d{10}|\d{11})$/;
        return regex.test(telefone.replace(/\D/g, ''));
    },
    
    /**
     * Validar CEP
     * @param {string} cep
     * @returns {boolean}
     */
    cep: (cep) => {
        const regex = /^\d{5}-?\d{3}$/;
        return regex.test(cep);
    },
    
    /**
     * Validar data
     * @param {string} data - Formato YYYY-MM-DD
     * @returns {boolean}
     */
    data: (data) => {
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if (!regex.test(data)) return false;
        
        const date = new Date(data);
        return date instanceof Date && !isNaN(date);
    }
};

// ============================================
// FORMATAÇÃO
// ============================================

const Formatacao = {
    /**
     * Formatar moeda
     * @param {number} valor
     * @returns {string}
     */
    moeda: (valor) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valor);
    },
    
    /**
     * Formatar data
     * @param {string|Date} data
     * @returns {string}
     */
    data: (data) => {
        const date = new Date(data);
        return new Intl.DateTimeFormat('pt-BR').format(date);
    },
    
    /**
     * Formatar data e hora
     * @param {string|Date} data
     * @returns {string}
     */
    dataHora: (data) => {
        const date = new Date(data);
        return new Intl.DateTimeFormat('pt-BR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    },
    
    /**
     * Formatar telefone
     * @param {string} telefone
     * @returns {string}
     */
    telefone: (telefone) => {
        const cleaned = telefone.replace(/\D/g, '');
        if (cleaned.length === 10) {
            return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 6)}-${cleaned.slice(6)}`;
        }
        if (cleaned.length === 11) {
            return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 7)}-${cleaned.slice(7)}`;
        }
        return telefone;
    },
    
    /**
     * Formatar CEP
     * @param {string} cep
     * @returns {string}
     */
    cep: (cep) => {
        const cleaned = cep.replace(/\D/g, '');
        return `${cleaned.slice(0, 5)}-${cleaned.slice(5)}`;
    },
    
    /**
     * Formatar percentual
     * @param {number} valor
     * @returns {string}
     */
    percentual: (valor) => {
        return `${valor.toFixed(2)}%`;
    },
    
    /**
     * Formatar número com separador de milhar
     * @param {number} numero
     * @returns {string}
     */
    numero: (numero) => {
        return new Intl.NumberFormat('pt-BR').format(numero);
    }
};

// ============================================
// CÁLCULOS
// ============================================

const Calculos = {
    /**
     * Calcular salário por hora
     * @param {number} salarioMensal
     * @param {number} horasPorDia
     * @param {number} diasPorSemana
     * @returns {number}
     */
    salarioPorHora: (salarioMensal, horasPorDia, diasPorSemana) => {
        const horasPorMes = (horasPorDia * diasPorSemana * 52) / 12;
        return salarioMensal / horasPorMes;
    },
    
    /**
     * Calcular carga horária mensal
     * @param {number} horasPorDia
     * @param {number} diasPorSemana
     * @returns {number}
     */
    cargaHorariaMensal: (horasPorDia, diasPorSemana) => {
        return (horasPorDia * diasPorSemana * 52) / 12;
    },
    
    /**
     * Calcular tempo de serviço em meses
     * @param {string|Date} dataAdmissao
     * @returns {number}
     */
    tempoServico: (dataAdmissao) => {
        const admissao = new Date(dataAdmissao);
        const hoje = new Date();
        
        let meses = (hoje.getFullYear() - admissao.getFullYear()) * 12;
        meses += hoje.getMonth() - admissao.getMonth();
        
        return Math.max(0, meses);
    },
    
    /**
     * Calcular férias
     * @param {number} salarioMensal
     * @param {number} tempoServicoMeses
     * @returns {object}
     */
    ferias: (salarioMensal, tempoServicoMeses) => {
        let diasFerias = (Math.floor(tempoServicoMeses / 12)) * 30;
        
        const mesesRestantes = tempoServicoMeses % 12;
        if (mesesRestantes >= 1) {
            diasFerias += Math.floor((mesesRestantes * 30) / 12);
        }
        
        const valorFerias = (salarioMensal / 30) * diasFerias * (4 / 3);
        
        return {
            diasFerias: diasFerias,
            valorFerias: Math.round(valorFerias * 100) / 100
        };
    },
    
    /**
     * Calcular 13º salário proporcional
     * @param {number} salarioMensal
     * @param {number} tempoServicoMeses
     * @returns {number}
     */
    decimoTerceiro: (salarioMensal, tempoServicoMeses) => {
        let mesesTrabalhados = tempoServicoMeses % 12;
        if (mesesTrabalhados === 0 && tempoServicoMeses > 0) {
            mesesTrabalhados = 12;
        }
        
        const valor13 = (salarioMensal / 12) * mesesTrabalhados;
        return Math.round(valor13 * 100) / 100;
    },
    
    /**
     * Calcular FGTS (8% do salário)
     * @param {number} salarioMensal
     * @returns {number}
     */
    fgts: (salarioMensal) => {
        return Math.round(salarioMensal * 0.08 * 100) / 100;
    },
    
    /**
     * Calcular INSS (9% do salário para doméstico)
     * @param {number} salarioMensal
     * @returns {number}
     */
    inss: (salarioMensal) => {
        return Math.round(salarioMensal * 0.09 * 100) / 100;
    }
};

// ============================================
// ARMAZENAMENTO LOCAL
// ============================================

const Armazenamento = {
    /**
     * Salvar dados no localStorage
     * @param {string} chave
     * @param {*} valor
     */
    salvar: (chave, valor) => {
        try {
            localStorage.setItem(chave, JSON.stringify(valor));
        } catch (e) {
            console.error('Erro ao salvar no localStorage:', e);
        }
    },
    
    /**
     * Obter dados do localStorage
     * @param {string} chave
     * @returns {*}
     */
    obter: (chave) => {
        try {
            const item = localStorage.getItem(chave);
            return item ? JSON.parse(item) : null;
        } catch (e) {
            console.error('Erro ao obter do localStorage:', e);
            return null;
        }
    },
    
    /**
     * Remover dados do localStorage
     * @param {string} chave
     */
    remover: (chave) => {
        try {
            localStorage.removeItem(chave);
        } catch (e) {
            console.error('Erro ao remover do localStorage:', e);
        }
    },
    
    /**
     * Limpar todo o localStorage
     */
    limpar: () => {
        try {
            localStorage.clear();
        } catch (e) {
            console.error('Erro ao limpar localStorage:', e);
        }
    }
};

// ============================================
// REQUISIÇÕES HTTP
// ============================================

const HTTP = {
    /**
     * Fazer requisição GET
     * @param {string} url
     * @param {object} opcoes
     * @returns {Promise}
     */
    get: async (url, opcoes = {}) => {
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    ...opcoes.headers
                },
                ...opcoes
            });
            return await response.json();
        } catch (erro) {
            console.error('Erro na requisição GET:', erro);
            throw erro;
        }
    },
    
    /**
     * Fazer requisição POST
     * @param {string} url
     * @param {object} dados
     * @param {object} opcoes
     * @returns {Promise}
     */
    post: async (url, dados, opcoes = {}) => {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...opcoes.headers
                },
                body: JSON.stringify(dados),
                ...opcoes
            });
            return await response.json();
        } catch (erro) {
            console.error('Erro na requisição POST:', erro);
            throw erro;
        }
    },
    
    /**
     * Fazer requisição PUT
     * @param {string} url
     * @param {object} dados
     * @param {object} opcoes
     * @returns {Promise}
     */
    put: async (url, dados, opcoes = {}) => {
        try {
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    ...opcoes.headers
                },
                body: JSON.stringify(dados),
                ...opcoes
            });
            return await response.json();
        } catch (erro) {
            console.error('Erro na requisição PUT:', erro);
            throw erro;
        }
    },
    
    /**
     * Fazer requisição DELETE
     * @param {string} url
     * @param {object} opcoes
     * @returns {Promise}
     */
    delete: async (url, opcoes = {}) => {
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    ...opcoes.headers
                },
                ...opcoes
            });
            return await response.json();
        } catch (erro) {
            console.error('Erro na requisição DELETE:', erro);
            throw erro;
        }
    }
};

// ============================================
// NOTIFICAÇÕES
// ============================================

const Notificacao = {
    /**
     * Mostrar notificação de sucesso
     * @param {string} mensagem
     */
    sucesso: (mensagem) => {
        console.log('✓ Sucesso:', mensagem);
        // Implementar UI de notificação
    },
    
    /**
     * Mostrar notificação de erro
     * @param {string} mensagem
     */
    erro: (mensagem) => {
        console.error('✗ Erro:', mensagem);
        // Implementar UI de notificação
    },
    
    /**
     * Mostrar notificação de aviso
     * @param {string} mensagem
     */
    aviso: (mensagem) => {
        console.warn('⚠ Aviso:', mensagem);
        // Implementar UI de notificação
    },
    
    /**
     * Mostrar notificação de informação
     * @param {string} mensagem
     */
    info: (mensagem) => {
        console.info('ℹ Info:', mensagem);
        // Implementar UI de notificação
    }
};

// Exportar para uso global
window.Validacao = Validacao;
window.Formatacao = Formatacao;
window.Calculos = Calculos;
window.Armazenamento = Armazenamento;
window.HTTP = HTTP;
window.Notificacao = Notificacao;

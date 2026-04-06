-- ============================================
-- GUIA DOMÉSTICO - DATABASE SCHEMA
-- Banco de Dados para Plataforma de Educação
-- de Empregados Domésticos
-- ============================================

-- Tabela de Usuários (Cadastros)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    cidade VARCHAR(100),
    estado CHAR(2),
    cep VARCHAR(10),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('ativo', 'inativo', 'bloqueado') DEFAULT 'ativo',
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_data_cadastro (data_cadastro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Cargos Domésticos
CREATE TABLE IF NOT EXISTS cargos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_cargo INT NOT NULL UNIQUE,
    nome_cargo VARCHAR(255) NOT NULL,
    cbo_codigo VARCHAR(10) NOT NULL,
    descricao_funcoes TEXT NOT NULL,
    responsabilidades JSON,
    nao_responsabilidades JSON,
    obrigacoes JSON,
    salario_minimo DECIMAL(10, 2),
    salario_maximo DECIMAL(10, 2),
    salario_medio DECIMAL(10, 2),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE,
    
    INDEX idx_numero (numero_cargo),
    INDEX idx_nome (nome_cargo),
    INDEX idx_cbo (cbo_codigo),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Diagnósticos
CREATE TABLE IF NOT EXISTS diagnosticos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    cargo_id INT NOT NULL,
    salario_mensal DECIMAL(10, 2) NOT NULL,
    data_admissao DATE NOT NULL,
    horas_por_dia DECIMAL(5, 2) NOT NULL,
    dias_por_semana INT NOT NULL,
    
    -- Campos calculados
    salario_por_hora DECIMAL(10, 2),
    carga_horaria_mensal DECIMAL(10, 2),
    tempo_servico_meses INT,
    
    -- Direitos calculados
    dias_ferias INT,
    valor_ferias DECIMAL(10, 2),
    valor_decimo_terceiro DECIMAL(10, 2),
    valor_fgts_mensal DECIMAL(10, 2),
    valor_inss_desconto DECIMAL(10, 2),
    
    -- Metadados
    data_geracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('ativo', 'arquivado', 'expirado') DEFAULT 'ativo',
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (cargo_id) REFERENCES cargos(id) ON DELETE RESTRICT,
    
    INDEX idx_usuario (usuario_id),
    INDEX idx_cargo (cargo_id),
    INDEX idx_data_geracao (data_geracao),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Auditoria (Log de Ações)
CREATE TABLE IF NOT EXISTS auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    tabela VARCHAR(50) NOT NULL,
    operacao ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    dados_antigos JSON,
    dados_novos JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    data_acao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    
    INDEX idx_usuario (usuario_id),
    INDEX idx_tabela (tabela),
    INDEX idx_operacao (operacao),
    INDEX idx_data_acao (data_acao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- VIEWS ÚTEIS
-- ============================================

-- View: Diagnósticos com informações do usuário e cargo
CREATE OR REPLACE VIEW vw_diagnosticos_completo AS
SELECT 
    d.id,
    d.usuario_id,
    u.nome_completo,
    u.email,
    d.cargo_id,
    c.nome_cargo,
    c.cbo_codigo,
    d.salario_mensal,
    d.data_admissao,
    d.horas_por_dia,
    d.dias_por_semana,
    d.salario_por_hora,
    d.carga_horaria_mensal,
    d.tempo_servico_meses,
    d.dias_ferias,
    d.valor_ferias,
    d.valor_decimo_terceiro,
    d.valor_fgts_mensal,
    d.valor_inss_desconto,
    d.data_geracao,
    d.status
FROM diagnosticos d
JOIN usuarios u ON d.usuario_id = u.id
JOIN cargos c ON d.cargo_id = c.id;

-- View: Usuários ativos com contagem de diagnósticos
CREATE OR REPLACE VIEW vw_usuarios_diagnosticos AS
SELECT 
    u.id,
    u.nome_completo,
    u.email,
    u.data_cadastro,
    COUNT(d.id) as total_diagnosticos,
    MAX(d.data_geracao) as ultimo_diagnostico
FROM usuarios u
LEFT JOIN diagnosticos d ON u.id = d.usuario_id
WHERE u.status = 'ativo'
GROUP BY u.id, u.nome_completo, u.email, u.data_cadastro;

-- ============================================
-- ÍNDICES ADICIONAIS PARA PERFORMANCE
-- ============================================

-- Índice composto para buscas frequentes
ALTER TABLE diagnosticos ADD INDEX idx_usuario_status (usuario_id, status);
ALTER TABLE usuarios ADD INDEX idx_email_status (email, status);
ALTER TABLE cargos ADD INDEX idx_nome_ativo (nome_cargo, ativo);

-- ============================================
-- TRIGGERS PARA AUDITORIA
-- ============================================

-- Trigger: Registrar INSERT em usuários
DELIMITER //
CREATE TRIGGER trg_audit_usuarios_insert
AFTER INSERT ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (tabela, operacao, dados_novos)
    VALUES ('usuarios', 'INSERT', JSON_OBJECT(
        'id', NEW.id,
        'nome_completo', NEW.nome_completo,
        'email', NEW.email,
        'data_cadastro', NEW.data_cadastro
    ));
END//
DELIMITER ;

-- Trigger: Registrar UPDATE em usuários
DELIMITER //
CREATE TRIGGER trg_audit_usuarios_update
AFTER UPDATE ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (usuario_id, tabela, operacao, dados_antigos, dados_novos)
    VALUES (NEW.id, 'usuarios', 'UPDATE', 
        JSON_OBJECT('email', OLD.email, 'status', OLD.status),
        JSON_OBJECT('email', NEW.email, 'status', NEW.status)
    );
END//
DELIMITER ;

-- Trigger: Registrar DELETE em usuários
DELIMITER //
CREATE TRIGGER trg_audit_usuarios_delete
AFTER DELETE ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (tabela, operacao, dados_antigos)
    VALUES ('usuarios', 'DELETE', JSON_OBJECT(
        'id', OLD.id,
        'nome_completo', OLD.nome_completo,
        'email', OLD.email
    ));
END//
DELIMITER ;

-- ============================================
-- DADOS INICIAIS - CARGOS DOMÉSTICOS
-- ============================================

INSERT INTO cargos (numero_cargo, nome_cargo, cbo_codigo, descricao_funcoes, salario_medio) VALUES
(1, 'Empregado doméstico nos serviços gerais', '5121-05', 'Realiza limpeza geral, organização e manutenção de ambientes domésticos, incluindo varredura, limpeza de pisos, móveis e utensílios.', 1500.00),
(2, 'Empregado doméstico arrumador', '5121-10', 'Arruma camas, organiza ambientes, limpa superfícies, organiza móveis e mantém ordem geral da residência.', 1600.00),
(3, 'Empregado doméstico faxineiro', '5121-15', 'Realiza limpeza profunda de ambientes, limpeza de vidros, espelhos, pisos e superfícies com produtos específicos.', 1700.00),
(4, 'Babá', '5162-05', 'Cuida de crianças, supervisiona atividades, auxilia em higiene e alimentação, acompanha em passeios e atividades educativas.', 2000.00),
(5, 'Passador de roupas', '5133-05', 'Lava, seca e passa roupas, realiza pequenos reparos em tecidos, organiza e guarda roupas.', 1500.00),
(6, 'Lavadeiro, em geral', '5133-10', 'Lava roupas, realiza pré-tratamento de manchas, seca e organiza peças, cuida de diferentes tipos de tecidos.', 1550.00),
(7, 'Caseiro', '5121-20', 'Realiza limpeza geral, manutenção da propriedade, cuida de jardins, animais e segurança da residência.', 1800.00),
(8, 'Governanta de residência', '5131-05', 'Supervisiona tarefas domésticas, organiza equipe, controla estoque de produtos, coordena limpeza e manutenção.', 2200.00),
(9, 'Mordomo de residência', '5131-10', 'Coordena serviços domésticos, atende visitantes, organiza eventos, supervisiona funcionários.', 2500.00),
(10, 'Cozinheiro do serviço doméstico', '5132-10', 'Prepara refeições, planeja cardápios, realiza compras de alimentos, mantém cozinha limpa e organizada.', 2300.00),
(11, 'Jardineiro de residência', '6210-10', 'Cuida de plantas, realiza podas, irrigação, limpeza de jardins, manutenção de áreas verdes.', 1900.00),
(12, 'Motorista de residência', '7822-05', 'Dirige veículo particular, realiza manutenção básica, cuida da limpeza e segurança do automóvel.', 2100.00),
(13, 'Segurança de residência', '5169-05', 'Realiza vigilância, controla acesso, monitora propriedade, auxilia em emergências.', 2000.00),
(14, 'Cuidador de idosos', '5162-10', 'Auxilia em higiene pessoal, administra medicamentos, acompanha em atividades, oferece companhia e apoio.', 2200.00),
(15, 'Cuidador de pessoas com deficiência', '5162-15', 'Auxilia em atividades diárias, higiene pessoal, mobilidade, oferece apoio emocional e companhia.', 2300.00);

-- Adicionar mais cargos conforme necessário
-- Total de 45 cargos conforme documento fornecido

-- ============================================
-- PERMISSÕES E SEGURANÇA
-- ============================================

-- Criar usuário para aplicação (exemplo)
-- CREATE USER 'guia_app'@'localhost' IDENTIFIED BY 'senha_segura_aqui';
-- GRANT SELECT, INSERT, UPDATE ON guia_domestico.* TO 'guia_app'@'localhost';
-- FLUSH PRIVILEGES;

"""
Rotas de Diagnósticos
Endpoints para gerenciar diagnósticos com cálculos automáticos
"""

from flask import Blueprint, request, jsonify
from flask_jwt_extended import jwt_required, get_jwt_identity
from datetime import date, datetime
import logging

from database.db import Database

logger = logging.getLogger(__name__)

diagnosticos_bp = Blueprint('diagnosticos', __name__)

# ============================================
# FUNÇÕES AUXILIARES DE CÁLCULO
# ============================================

def calcular_salario_por_hora(salario_mensal, horas_por_dia, dias_por_semana):
    """Calcular salário por hora"""
    horas_por_mes = (horas_por_dia * dias_por_semana * 52) / 12
    return salario_mensal / horas_por_mes if horas_por_mes > 0 else 0

def calcular_carga_horaria_mensal(horas_por_dia, dias_por_semana):
    """Calcular carga horária mensal"""
    return (horas_por_dia * dias_por_semana * 52) / 12

def calcular_tempo_servico(data_admissao):
    """Calcular tempo de serviço em meses"""
    hoje = date.today()
    admissao = datetime.strptime(data_admissao, '%Y-%m-%d').date()
    meses = (hoje.year - admissao.year) * 12 + (hoje.month - admissao.month)
    return max(0, meses)

def calcular_ferias(salario_mensal, tempo_servico_meses):
    """Calcular férias"""
    dias_ferias = (tempo_servico_meses // 12) * 30
    meses_restantes = tempo_servico_meses % 12
    if meses_restantes >= 1:
        dias_ferias += (meses_restantes * 30) // 12
    
    valor_ferias = (salario_mensal / 30) * dias_ferias * (4/3)
    return dias_ferias, round(valor_ferias, 2)

def calcular_decimo_terceiro(salario_mensal, tempo_servico_meses):
    """Calcular 13º salário proporcional"""
    meses_trabalhados = tempo_servico_meses % 12
    if meses_trabalhados == 0 and tempo_servico_meses > 0:
        meses_trabalhados = 12
    
    valor_13 = (salario_mensal / 12) * meses_trabalhados
    return round(valor_13, 2)

def calcular_fgts(salario_mensal):
    """Calcular FGTS (8%)"""
    return round(salario_mensal * 0.08, 2)

def calcular_inss(salario_mensal):
    """Calcular INSS (9% para doméstico)"""
    return round(salario_mensal * 0.09, 2)

# ============================================
# CRIAR DIAGNÓSTICO
# ============================================

@diagnosticos_bp.route('/', methods=['POST'])
@jwt_required()
def criar_diagnostico():
    """Criar novo diagnóstico"""
    try:
        usuario_id = get_jwt_identity()
        data = request.get_json()
        
        # Validar dados obrigatórios
        campos_obrigatorios = ['cargo_id', 'salario_mensal', 'data_admissao', 
                              'horas_por_dia', 'dias_por_semana']
        if not all(campo in data for campo in campos_obrigatorios):
            return jsonify({'erro': 'Dados obrigatórios faltando'}), 400
        
        cargo_id = data['cargo_id']
        salario_mensal = float(data['salario_mensal'])
        data_admissao = data['data_admissao']
        horas_por_dia = float(data['horas_por_dia'])
        dias_por_semana = int(data['dias_por_semana'])
        
        # Validações
        if salario_mensal <= 0:
            return jsonify({'erro': 'Salário deve ser maior que zero'}), 400
        if horas_por_dia <= 0 or dias_por_semana <= 0:
            return jsonify({'erro': 'Horas e dias devem ser maiores que zero'}), 400
        
        # Verificar se cargo existe
        cargo = Database.fetch_one(
            "SELECT id FROM cargos WHERE id = %s",
            (cargo_id,)
        )
        
        if not cargo:
            return jsonify({'erro': 'Cargo não encontrado'}), 404
        
        # Calcular valores
        salario_por_hora = calcular_salario_por_hora(salario_mensal, horas_por_dia, dias_por_semana)
        carga_horaria_mensal = calcular_carga_horaria_mensal(horas_por_dia, dias_por_semana)
        tempo_servico_meses = calcular_tempo_servico(data_admissao)
        
        dias_ferias, valor_ferias = calcular_ferias(salario_mensal, tempo_servico_meses)
        valor_13 = calcular_decimo_terceiro(salario_mensal, tempo_servico_meses)
        valor_fgts = calcular_fgts(salario_mensal)
        valor_inss = calcular_inss(salario_mensal)
        
        # Inserir diagnóstico
        diagnostico_id = Database.insert(
            """
            INSERT INTO diagnosticos 
            (usuario_id, cargo_id, salario_mensal, data_admissao, 
             horas_por_dia, dias_por_semana, salario_por_hora, 
             carga_horaria_mensal, tempo_servico_meses, dias_ferias, 
             valor_ferias, valor_decimo_terceiro, valor_fgts_mensal, 
             valor_inss_desconto, status)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'ativo')
            """,
            (usuario_id, cargo_id, salario_mensal, data_admissao,
             horas_por_dia, dias_por_semana, salario_por_hora,
             carga_horaria_mensal, tempo_servico_meses, dias_ferias,
             valor_ferias, valor_13, valor_fgts, valor_inss)
        )
        
        if not diagnostico_id:
            return jsonify({'erro': 'Erro ao criar diagnóstico'}), 500
        
        logger.info(f"Diagnóstico criado para usuário {usuario_id}")
        
        return jsonify({
            'mensagem': 'Diagnóstico criado com sucesso',
            'diagnostico_id': diagnostico_id,
            'calculos': {
                'salario_por_hora': salario_por_hora,
                'carga_horaria_mensal': carga_horaria_mensal,
                'tempo_servico_meses': tempo_servico_meses,
                'dias_ferias': dias_ferias,
                'valor_ferias': valor_ferias,
                'valor_decimo_terceiro': valor_13,
                'valor_fgts_mensal': valor_fgts,
                'valor_inss_desconto': valor_inss
            }
        }), 201
    
    except Exception as e:
        logger.error(f"Erro ao criar diagnóstico: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# LISTAR DIAGNÓSTICOS DO USUÁRIO
# ============================================

@diagnosticos_bp.route('/', methods=['GET'])
@jwt_required()
def listar_diagnosticos():
    """Listar diagnósticos do usuário autenticado"""
    try:
        usuario_id = get_jwt_identity()
        limite = request.args.get('limite', 100, type=int)
        offset = request.args.get('offset', 0, type=int)
        
        diagnosticos = Database.fetch_all(
            """
            SELECT d.id, d.cargo_id, c.nome_cargo, d.salario_mensal, 
                   d.data_admissao, d.tempo_servico_meses, d.dias_ferias,
                   d.valor_ferias, d.valor_decimo_terceiro, d.data_geracao, d.status
            FROM diagnosticos d
            JOIN cargos c ON d.cargo_id = c.id
            WHERE d.usuario_id = %s AND d.status = 'ativo'
            ORDER BY d.data_geracao DESC
            LIMIT %s OFFSET %s
            """,
            (usuario_id, limite, offset)
        )
        
        total = Database.fetch_one(
            "SELECT COUNT(*) as total FROM diagnosticos WHERE usuario_id = %s AND status = 'ativo'",
            (usuario_id,)
        )
        
        return jsonify({
            'diagnosticos': diagnosticos,
            'total': total['total'] if total else 0,
            'limite': limite,
            'offset': offset
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao listar diagnósticos: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# OBTER DIAGNÓSTICO
# ============================================

@diagnosticos_bp.route('/<int:diagnostico_id>', methods=['GET'])
@jwt_required()
def obter_diagnostico(diagnostico_id):
    """Obter dados de um diagnóstico específico"""
    try:
        usuario_id = get_jwt_identity()
        
        diagnostico = Database.fetch_one(
            """
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
            JOIN cargos c ON d.cargo_id = c.id
            WHERE d.id = %s AND d.usuario_id = %s
            """,
            (diagnostico_id, usuario_id)
        )
        
        if not diagnostico:
            return jsonify({'erro': 'Diagnóstico não encontrado'}), 404
        
        # Calcular salário líquido
        salario_liquido = diagnostico['salario_mensal'] - diagnostico['valor_inss_desconto']
        
        return jsonify({
            'diagnostico': diagnostico,
            'salario_liquido': round(salario_liquido, 2)
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao obter diagnóstico: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# ATUALIZAR DIAGNÓSTICO
# ============================================

@diagnosticos_bp.route('/<int:diagnostico_id>', methods=['PUT'])
@jwt_required()
def atualizar_diagnostico(diagnostico_id):
    """Atualizar diagnóstico"""
    try:
        usuario_id = get_jwt_identity()
        data = request.get_json()
        
        if not data:
            return jsonify({'erro': 'Nenhum dado para atualizar'}), 400
        
        # Verificar se diagnóstico pertence ao usuário
        diagnostico = Database.fetch_one(
            "SELECT usuario_id FROM diagnosticos WHERE id = %s",
            (diagnostico_id,)
        )
        
        if not diagnostico or diagnostico['usuario_id'] != usuario_id:
            return jsonify({'erro': 'Diagnóstico não encontrado'}), 404
        
        campos_permitidos = {
            'salario_mensal', 'data_admissao', 'horas_por_dia', 
            'dias_por_semana', 'status'
        }
        
        campos_atualizacao = {}
        for campo in campos_permitidos:
            if campo in data and data[campo] is not None:
                campos_atualizacao[campo] = data[campo]
        
        if not campos_atualizacao:
            return jsonify({'erro': 'Nenhum campo válido para atualizar'}), 400
        
        set_clause = ", ".join([f"{k} = %s" for k in campos_atualizacao.keys()])
        valores = list(campos_atualizacao.values()) + [diagnostico_id]
        
        linhas_afetadas = Database.update(
            f"UPDATE diagnosticos SET {set_clause} WHERE id = %s",
            valores
        )
        
        if linhas_afetadas == 0:
            return jsonify({'erro': 'Erro ao atualizar diagnóstico'}), 500
        
        logger.info(f"Diagnóstico {diagnostico_id} atualizado")
        
        return jsonify({
            'mensagem': 'Diagnóstico atualizado com sucesso'
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao atualizar diagnóstico: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# DELETAR DIAGNÓSTICO
# ============================================

@diagnosticos_bp.route('/<int:diagnostico_id>', methods=['DELETE'])
@jwt_required()
def deletar_diagnostico(diagnostico_id):
    """Deletar diagnóstico (soft delete)"""
    try:
        usuario_id = get_jwt_identity()
        
        # Verificar se diagnóstico pertence ao usuário
        diagnostico = Database.fetch_one(
            "SELECT usuario_id FROM diagnosticos WHERE id = %s",
            (diagnostico_id,)
        )
        
        if not diagnostico or diagnostico['usuario_id'] != usuario_id:
            return jsonify({'erro': 'Diagnóstico não encontrado'}), 404
        
        linhas_afetadas = Database.update(
            "UPDATE diagnosticos SET status = 'arquivado' WHERE id = %s",
            (diagnostico_id,)
        )
        
        if linhas_afetadas == 0:
            return jsonify({'erro': 'Erro ao deletar diagnóstico'}), 500
        
        logger.info(f"Diagnóstico {diagnostico_id} deletado")
        
        return jsonify({
            'mensagem': 'Diagnóstico deletado com sucesso'
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao deletar diagnóstico: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

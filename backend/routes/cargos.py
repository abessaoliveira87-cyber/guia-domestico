"""
Rotas de Cargos Domésticos
Endpoints para gerenciar cargos
"""

from flask import Blueprint, request, jsonify
from flask_jwt_extended import jwt_required, get_jwt_identity
import json
import logging

from database.db import Database

logger = logging.getLogger(__name__)

cargos_bp = Blueprint('cargos', __name__)

# ============================================
# LISTAR CARGOS
# ============================================

@cargos_bp.route('/', methods=['GET'])
def listar_cargos():
    """Listar todos os cargos"""
    try:
        limite = request.args.get('limite', 100, type=int)
        offset = request.args.get('offset', 0, type=int)
        ativo = request.args.get('ativo', 'true').lower() == 'true'
        
        cargos = Database.fetch_all(
            """
            SELECT id, numero_cargo, nome_cargo, cbo_codigo, descricao_funcoes,
                   salario_minimo, salario_maximo, salario_medio, ativo
            FROM cargos 
            WHERE ativo = %s
            ORDER BY numero_cargo ASC
            LIMIT %s OFFSET %s
            """,
            (ativo, limite, offset)
        )
        
        total = Database.fetch_one(
            "SELECT COUNT(*) as total FROM cargos WHERE ativo = %s",
            (ativo,)
        )
        
        return jsonify({
            'cargos': cargos,
            'total': total['total'] if total else 0,
            'limite': limite,
            'offset': offset
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao listar cargos: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# OBTER CARGO
# ============================================

@cargos_bp.route('/<int:cargo_id>', methods=['GET'])
def obter_cargo(cargo_id):
    """Obter dados de um cargo específico"""
    try:
        cargo = Database.fetch_one(
            """
            SELECT id, numero_cargo, nome_cargo, cbo_codigo, descricao_funcoes,
                   responsabilidades, nao_responsabilidades, obrigacoes,
                   salario_minimo, salario_maximo, salario_medio,
                   data_criacao, ativo
            FROM cargos WHERE id = %s
            """,
            (cargo_id,)
        )
        
        if not cargo:
            return jsonify({'erro': 'Cargo não encontrado'}), 404
        
        # Converter JSON strings para listas
        if cargo['responsabilidades']:
            cargo['responsabilidades'] = json.loads(cargo['responsabilidades'])
        if cargo['nao_responsabilidades']:
            cargo['nao_responsabilidades'] = json.loads(cargo['nao_responsabilidades'])
        if cargo['obrigacoes']:
            cargo['obrigacoes'] = json.loads(cargo['obrigacoes'])
        
        return jsonify({
            'cargo': cargo
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao obter cargo: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# BUSCAR CARGOS
# ============================================

@cargos_bp.route('/buscar', methods=['GET'])
def buscar_cargos():
    """Buscar cargos por termo"""
    try:
        termo = request.args.get('termo', '', type=str)
        limite = request.args.get('limite', 50, type=int)
        
        if not termo or len(termo) < 2:
            return jsonify({'erro': 'Termo de busca deve ter no mínimo 2 caracteres'}), 400
        
        termo_busca = f"%{termo}%"
        
        cargos = Database.fetch_all(
            """
            SELECT id, numero_cargo, nome_cargo, cbo_codigo, 
                   salario_minimo, salario_maximo, salario_medio
            FROM cargos 
            WHERE (nome_cargo LIKE %s OR cbo_codigo LIKE %s) AND ativo = TRUE
            LIMIT %s
            """,
            (termo_busca, termo_busca, limite)
        )
        
        return jsonify({
            'cargos': cargos,
            'total': len(cargos)
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao buscar cargos: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# CRIAR CARGO (ADMIN)
# ============================================

@cargos_bp.route('/', methods=['POST'])
@jwt_required()
def criar_cargo():
    """Criar novo cargo (apenas admin)"""
    try:
        usuario_id = get_jwt_identity()
        data = request.get_json()
        
        # TODO: Verificar se é admin
        
        # Validar dados obrigatórios
        campos_obrigatorios = ['numero_cargo', 'nome_cargo', 'cbo_codigo', 'descricao_funcoes']
        if not all(campo in data for campo in campos_obrigatorios):
            return jsonify({'erro': 'Dados obrigatórios faltando'}), 400
        
        # Verificar se número de cargo já existe
        cargo_existente = Database.fetch_one(
            "SELECT id FROM cargos WHERE numero_cargo = %s",
            (data['numero_cargo'],)
        )
        
        if cargo_existente:
            return jsonify({'erro': 'Cargo com este número já existe'}), 409
        
        # Converter listas para JSON
        responsabilidades = json.dumps(data.get('responsabilidades', []))
        nao_responsabilidades = json.dumps(data.get('nao_responsabilidades', []))
        obrigacoes = json.dumps(data.get('obrigacoes', []))
        
        cargo_id = Database.insert(
            """
            INSERT INTO cargos 
            (numero_cargo, nome_cargo, cbo_codigo, descricao_funcoes,
             responsabilidades, nao_responsabilidades, obrigacoes,
             salario_minimo, salario_maximo, salario_medio, ativo)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, TRUE)
            """,
            (data['numero_cargo'], data['nome_cargo'], data['cbo_codigo'],
             data['descricao_funcoes'], responsabilidades, nao_responsabilidades,
             obrigacoes, data.get('salario_minimo'), data.get('salario_maximo'),
             data.get('salario_medio'))
        )
        
        if not cargo_id:
            return jsonify({'erro': 'Erro ao criar cargo'}), 500
        
        logger.info(f"Novo cargo criado: {data['nome_cargo']}")
        
        return jsonify({
            'mensagem': 'Cargo criado com sucesso',
            'cargo_id': cargo_id
        }), 201
    
    except Exception as e:
        logger.error(f"Erro ao criar cargo: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# ATUALIZAR CARGO (ADMIN)
# ============================================

@cargos_bp.route('/<int:cargo_id>', methods=['PUT'])
@jwt_required()
def atualizar_cargo(cargo_id):
    """Atualizar cargo (apenas admin)"""
    try:
        usuario_id = get_jwt_identity()
        data = request.get_json()
        
        # TODO: Verificar se é admin
        
        if not data:
            return jsonify({'erro': 'Nenhum dado para atualizar'}), 400
        
        # Campos permitidos
        campos_permitidos = {
            'nome_cargo', 'cbo_codigo', 'descricao_funcoes',
            'responsabilidades', 'nao_responsabilidades', 'obrigacoes',
            'salario_minimo', 'salario_maximo', 'salario_medio', 'ativo'
        }
        
        campos_atualizacao = {}
        for campo in campos_permitidos:
            if campo in data and data[campo] is not None:
                if campo in ['responsabilidades', 'nao_responsabilidades', 'obrigacoes']:
                    campos_atualizacao[campo] = json.dumps(data[campo])
                else:
                    campos_atualizacao[campo] = data[campo]
        
        if not campos_atualizacao:
            return jsonify({'erro': 'Nenhum campo válido para atualizar'}), 400
        
        set_clause = ", ".join([f"{k} = %s" for k in campos_atualizacao.keys()])
        valores = list(campos_atualizacao.values()) + [cargo_id]
        
        linhas_afetadas = Database.update(
            f"UPDATE cargos SET {set_clause} WHERE id = %s",
            valores
        )
        
        if linhas_afetadas == 0:
            return jsonify({'erro': 'Cargo não encontrado'}), 404
        
        logger.info(f"Cargo {cargo_id} atualizado")
        
        return jsonify({
            'mensagem': 'Cargo atualizado com sucesso'
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao atualizar cargo: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# DELETAR CARGO (ADMIN)
# ============================================

@cargos_bp.route('/<int:cargo_id>', methods=['DELETE'])
@jwt_required()
def deletar_cargo(cargo_id):
    """Deletar cargo (apenas admin)"""
    try:
        usuario_id = get_jwt_identity()
        
        # TODO: Verificar se é admin
        
        linhas_afetadas = Database.update(
            "UPDATE cargos SET ativo = FALSE WHERE id = %s",
            (cargo_id,)
        )
        
        if linhas_afetadas == 0:
            return jsonify({'erro': 'Cargo não encontrado'}), 404
        
        logger.info(f"Cargo {cargo_id} deletado (soft delete)")
        
        return jsonify({
            'mensagem': 'Cargo deletado com sucesso'
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao deletar cargo: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

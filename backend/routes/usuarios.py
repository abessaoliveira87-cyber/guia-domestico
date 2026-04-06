"""
Rotas de Usuários
Endpoints para gerenciar perfil de usuários
"""

from flask import Blueprint, request, jsonify
from flask_jwt_extended import jwt_required, get_jwt_identity
import bcrypt
import logging

from database.db import Database

logger = logging.getLogger(__name__)

usuarios_bp = Blueprint('usuarios', __name__)

# ============================================
# OBTER PERFIL
# ============================================

@usuarios_bp.route('/perfil', methods=['GET'])
@jwt_required()
def get_perfil():
    """Obter dados do perfil do usuário autenticado"""
    try:
        usuario_id = get_jwt_identity()
        
        usuario = Database.fetch_one(
            """
            SELECT id, nome_completo, email, telefone, endereco, 
                   cidade, estado, cep, data_cadastro, status
            FROM usuarios WHERE id = %s
            """,
            (usuario_id,)
        )
        
        if not usuario:
            return jsonify({'erro': 'Usuário não encontrado'}), 404
        
        return jsonify({
            'usuario': usuario
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao obter perfil: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# ATUALIZAR PERFIL
# ============================================

@usuarios_bp.route('/atualizar', methods=['PUT'])
@jwt_required()
def atualizar_perfil():
    """Atualizar dados do perfil"""
    try:
        usuario_id = get_jwt_identity()
        data = request.get_json()
        
        if not data:
            return jsonify({'erro': 'Nenhum dado para atualizar'}), 400
        
        # Campos permitidos para atualização
        campos_permitidos = {
            'nome_completo', 'telefone', 'endereco', 
            'cidade', 'estado', 'cep'
        }
        
        # Filtrar apenas campos permitidos
        campos_atualizacao = {}
        for campo in campos_permitidos:
            if campo in data and data[campo] is not None:
                campos_atualizacao[campo] = data[campo]
        
        if not campos_atualizacao:
            return jsonify({'erro': 'Nenhum campo válido para atualizar'}), 400
        
        # Construir query dinâmica
        set_clause = ", ".join([f"{k} = %s" for k in campos_atualizacao.keys()])
        valores = list(campos_atualizacao.values()) + [usuario_id]
        
        query = f"UPDATE usuarios SET {set_clause} WHERE id = %s"
        
        linhas_afetadas = Database.update(query, valores)
        
        if linhas_afetadas == 0:
            return jsonify({'erro': 'Usuário não encontrado'}), 404
        
        logger.info(f"Perfil do usuário {usuario_id} atualizado")
        
        return jsonify({
            'mensagem': 'Perfil atualizado com sucesso'
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao atualizar perfil: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# ALTERAR SENHA
# ============================================

@usuarios_bp.route('/alterar-senha', methods=['POST'])
@jwt_required()
def alterar_senha():
    """Alterar senha do usuário"""
    try:
        usuario_id = get_jwt_identity()
        data = request.get_json()
        
        # Validar dados
        if not data or not all(k in data for k in ('senha_atual', 'senha_nova')):
            return jsonify({'erro': 'Dados obrigatórios faltando'}), 400
        
        senha_atual = data.get('senha_atual', '')
        senha_nova = data.get('senha_nova', '')
        
        if len(senha_nova) < 8:
            return jsonify({'erro': 'Nova senha deve ter no mínimo 8 caracteres'}), 400
        
        # Buscar usuário
        usuario = Database.fetch_one(
            "SELECT senha_hash FROM usuarios WHERE id = %s",
            (usuario_id,)
        )
        
        if not usuario:
            return jsonify({'erro': 'Usuário não encontrado'}), 404
        
        # Verificar senha atual
        if not bcrypt.checkpw(senha_atual.encode('utf-8'), usuario['senha_hash'].encode('utf-8')):
            return jsonify({'erro': 'Senha atual incorreta'}), 401
        
        # Hash da nova senha
        nova_hash = bcrypt.hashpw(senha_nova.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
        
        # Atualizar senha
        linhas_afetadas = Database.update(
            "UPDATE usuarios SET senha_hash = %s WHERE id = %s",
            (nova_hash, usuario_id)
        )
        
        if linhas_afetadas == 0:
            return jsonify({'erro': 'Erro ao atualizar senha'}), 500
        
        logger.info(f"Senha do usuário {usuario_id} alterada")
        
        return jsonify({
            'mensagem': 'Senha alterada com sucesso'
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao alterar senha: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# DELETAR CONTA
# ============================================

@usuarios_bp.route('/deletar', methods=['DELETE'])
@jwt_required()
def deletar_conta():
    """Deletar conta do usuário (soft delete)"""
    try:
        usuario_id = get_jwt_identity()
        data = request.get_json()
        
        # Validar senha para confirmação
        if not data or 'senha' not in data:
            return jsonify({'erro': 'Senha necessária para deletar conta'}), 400
        
        senha = data.get('senha', '')
        
        # Buscar usuário
        usuario = Database.fetch_one(
            "SELECT senha_hash FROM usuarios WHERE id = %s",
            (usuario_id,)
        )
        
        if not usuario:
            return jsonify({'erro': 'Usuário não encontrado'}), 404
        
        # Verificar senha
        if not bcrypt.checkpw(senha.encode('utf-8'), usuario['senha_hash'].encode('utf-8')):
            return jsonify({'erro': 'Senha incorreta'}), 401
        
        # Soft delete - marcar como inativo
        linhas_afetadas = Database.update(
            "UPDATE usuarios SET status = 'inativo' WHERE id = %s",
            (usuario_id,)
        )
        
        if linhas_afetadas == 0:
            return jsonify({'erro': 'Erro ao deletar conta'}), 500
        
        logger.info(f"Conta do usuário {usuario_id} deletada (soft delete)")
        
        return jsonify({
            'mensagem': 'Conta deletada com sucesso'
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao deletar conta: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# LISTAR USUÁRIOS (ADMIN)
# ============================================

@usuarios_bp.route('/', methods=['GET'])
@jwt_required()
def listar_usuarios():
    """Listar usuários (apenas admin)"""
    try:
        usuario_id = get_jwt_identity()
        
        # TODO: Verificar se é admin
        
        limite = request.args.get('limite', 100, type=int)
        offset = request.args.get('offset', 0, type=int)
        status = request.args.get('status', 'ativo')
        
        usuarios = Database.fetch_all(
            """
            SELECT id, nome_completo, email, telefone, cidade, 
                   data_cadastro, status
            FROM usuarios 
            WHERE status = %s
            ORDER BY data_cadastro DESC
            LIMIT %s OFFSET %s
            """,
            (status, limite, offset)
        )
        
        total = Database.fetch_one(
            "SELECT COUNT(*) as total FROM usuarios WHERE status = %s",
            (status,)
        )
        
        return jsonify({
            'usuarios': usuarios,
            'total': total['total'] if total else 0,
            'limite': limite,
            'offset': offset
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao listar usuários: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

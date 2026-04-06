"""
Rotas de Autenticação
Endpoints para login, registro e autenticação
"""

from flask import Blueprint, request, jsonify
from flask_jwt_extended import create_access_token
from datetime import datetime
import bcrypt
import logging

from database.db import Database

logger = logging.getLogger(__name__)

auth_bp = Blueprint('auth', __name__)

# ============================================
# REGISTRO
# ============================================

@auth_bp.route('/register', methods=['POST'])
def register():
    """Registrar novo usuário"""
    try:
        data = request.get_json()
        
        # Validar dados obrigatórios
        if not data or not all(k in data for k in ('nome_completo', 'email', 'senha')):
            return jsonify({'erro': 'Dados obrigatórios faltando'}), 400
        
        nome_completo = data.get('nome_completo', '').strip()
        email = data.get('email', '').strip().lower()
        senha = data.get('senha', '')
        
        # Validações
        if len(nome_completo) < 3:
            return jsonify({'erro': 'Nome deve ter no mínimo 3 caracteres'}), 400
        
        if '@' not in email or '.' not in email:
            return jsonify({'erro': 'Email inválido'}), 400
        
        if len(senha) < 8:
            return jsonify({'erro': 'Senha deve ter no mínimo 8 caracteres'}), 400
        
        # Verificar se email já existe
        usuario_existente = Database.fetch_one(
            "SELECT id FROM usuarios WHERE email = %s",
            (email,)
        )
        
        if usuario_existente:
            return jsonify({'erro': 'Email já cadastrado'}), 409
        
        # Hash da senha
        senha_hash = bcrypt.hashpw(senha.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
        
        # Inserir usuário
        usuario_id = Database.insert(
            """
            INSERT INTO usuarios 
            (nome_completo, email, senha_hash, status)
            VALUES (%s, %s, %s, 'ativo')
            """,
            (nome_completo, email, senha_hash)
        )
        
        if not usuario_id:
            return jsonify({'erro': 'Erro ao criar usuário'}), 500
        
        logger.info(f"Novo usuário registrado: {email}")
        
        return jsonify({
            'mensagem': 'Usuário registrado com sucesso',
            'usuario_id': usuario_id,
            'email': email
        }), 201
    
    except Exception as e:
        logger.error(f"Erro ao registrar usuário: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# LOGIN
# ============================================

@auth_bp.route('/login', methods=['POST'])
def login():
    """Fazer login e obter token JWT"""
    try:
        data = request.get_json()
        
        # Validar dados
        if not data or not all(k in data for k in ('email', 'senha')):
            return jsonify({'erro': 'Email e senha são obrigatórios'}), 400
        
        email = data.get('email', '').strip().lower()
        senha = data.get('senha', '')
        
        # Buscar usuário
        usuario = Database.fetch_one(
            """
            SELECT id, nome_completo, email, senha_hash, status 
            FROM usuarios WHERE email = %s
            """,
            (email,)
        )
        
        if not usuario:
            return jsonify({'erro': 'Email ou senha incorretos'}), 401
        
        if usuario['status'] != 'ativo':
            return jsonify({'erro': 'Usuário inativo ou bloqueado'}), 403
        
        # Verificar senha
        if not bcrypt.checkpw(senha.encode('utf-8'), usuario['senha_hash'].encode('utf-8')):
            return jsonify({'erro': 'Email ou senha incorretos'}), 401
        
        # Criar token JWT
        access_token = create_access_token(identity=usuario['id'])
        
        logger.info(f"Usuário autenticado: {email}")
        
        return jsonify({
            'mensagem': 'Login realizado com sucesso',
            'access_token': access_token,
            'usuario': {
                'id': usuario['id'],
                'nome_completo': usuario['nome_completo'],
                'email': usuario['email']
            }
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao fazer login: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# LOGOUT
# ============================================

@auth_bp.route('/logout', methods=['POST'])
def logout():
    """Fazer logout (cliente deve descartar token)"""
    try:
        return jsonify({
            'mensagem': 'Logout realizado com sucesso'
        }), 200
    
    except Exception as e:
        logger.error(f"Erro ao fazer logout: {e}")
        return jsonify({'erro': 'Erro interno do servidor'}), 500

# ============================================
# VERIFICAR TOKEN
# ============================================

@auth_bp.route('/verify', methods=['GET'])
def verify_token():
    """Verificar se token é válido"""
    try:
        from flask_jwt_extended import jwt_required, get_jwt_identity
        
        @jwt_required()
        def verify():
            usuario_id = get_jwt_identity()
            
            usuario = Database.fetch_one(
                "SELECT id, nome_completo, email FROM usuarios WHERE id = %s",
                (usuario_id,)
            )
            
            if not usuario:
                return jsonify({'erro': 'Usuário não encontrado'}), 404
            
            return jsonify({
                'valido': True,
                'usuario': usuario
            }), 200
        
        return verify()
    
    except Exception as e:
        logger.error(f"Erro ao verificar token: {e}")
        return jsonify({'erro': 'Token inválido'}), 401

"""
Guia Doméstico - Backend API
Aplicação Flask com endpoints para Usuários, Cargos e Diagnósticos
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
from flask_jwt_extended import JWTManager, create_access_token, jwt_required, get_jwt_identity
from datetime import datetime, timedelta
import os
from dotenv import load_dotenv
import logging

# Carregar variáveis de ambiente
load_dotenv()

# Configurar logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Criar aplicação Flask
app = Flask(__name__)

# Configuração
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', 'sua-chave-secreta-desenvolvimento')
app.config['JWT_SECRET_KEY'] = os.getenv('JWT_SECRET_KEY', 'sua-chave-jwt-desenvolvimento')
app.config['JWT_ACCESS_TOKEN_EXPIRES'] = timedelta(hours=int(os.getenv('JWT_EXPIRATION_HOURS', 24)))

# Habilitar CORS
CORS(app, resources={
    r"/api/*": {
        "origins": ["http://localhost:8000", "http://localhost:3000", "http://127.0.0.1:8000"],
        "methods": ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
        "allow_headers": ["Content-Type", "Authorization"]
    }
})

# Inicializar JWT
jwt = JWTManager(app)

# Importar blueprints
from routes.auth import auth_bp
from routes.usuarios import usuarios_bp
from routes.cargos import cargos_bp
from routes.diagnosticos import diagnosticos_bp

# Registrar blueprints
app.register_blueprint(auth_bp, url_prefix='/api/auth')
app.register_blueprint(usuarios_bp, url_prefix='/api/usuarios')
app.register_blueprint(cargos_bp, url_prefix='/api/cargos')
app.register_blueprint(diagnosticos_bp, url_prefix='/api/diagnosticos')

# ============================================
# ROTAS GERAIS
# ============================================

@app.route('/api/health', methods=['GET'])
def health_check():
    """Verificar saúde da API"""
    return jsonify({
        'status': 'ok',
        'timestamp': datetime.now().isoformat(),
        'version': '1.0.0'
    }), 200

@app.route('/api/', methods=['GET'])
def api_info():
    """Informações da API"""
    return jsonify({
        'nome': 'Guia Doméstico API',
        'versao': '1.0.0',
        'descricao': 'API para plataforma de educação de empregados domésticos',
        'endpoints': {
            'autenticacao': '/api/auth',
            'usuarios': '/api/usuarios',
            'cargos': '/api/cargos',
            'diagnosticos': '/api/diagnosticos'
        }
    }), 200

# ============================================
# TRATAMENTO DE ERROS
# ============================================

@app.errorhandler(404)
def not_found(error):
    """Erro 404 - Não encontrado"""
    return jsonify({
        'erro': 'Recurso não encontrado',
        'status': 404
    }), 404

@app.errorhandler(500)
def internal_error(error):
    """Erro 500 - Erro interno do servidor"""
    logger.error(f"Erro interno: {error}")
    return jsonify({
        'erro': 'Erro interno do servidor',
        'status': 500
    }), 500

@app.errorhandler(401)
def unauthorized(error):
    """Erro 401 - Não autorizado"""
    return jsonify({
        'erro': 'Não autorizado',
        'status': 401
    }), 401

@app.errorhandler(403)
def forbidden(error):
    """Erro 403 - Proibido"""
    return jsonify({
        'erro': 'Acesso proibido',
        'status': 403
    }), 403

# ============================================
# CALLBACKS JWT
# ============================================

@jwt.user_lookup_loader
def user_lookup_callback(jwt_header, jwt_data):
    """Callback para buscar usuário do JWT"""
    identity = jwt_data["sub"]
    return identity

@jwt.additional_claims_loader
def add_claims_to_jwt(identity):
    """Adicionar claims ao JWT"""
    return {
        'iat': datetime.utcnow(),
        'exp': datetime.utcnow() + app.config['JWT_ACCESS_TOKEN_EXPIRES']
    }

# ============================================
# INICIALIZAÇÃO
# ============================================

if __name__ == '__main__':
    # Criar tabelas se não existirem
    try:
        from database.db import init_db
        init_db()
        logger.info("Banco de dados inicializado")
    except Exception as e:
        logger.warning(f"Aviso ao inicializar banco de dados: {e}")
    
    # Executar aplicação
    debug = os.getenv('APP_ENV', 'development') == 'development'
    app.run(
        host='0.0.0.0',
        port=int(os.getenv('PORT', 5000)),
        debug=debug
    )

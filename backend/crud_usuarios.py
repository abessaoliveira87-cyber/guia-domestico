"""
CRUD - Usuários (Cadastros)
Módulo para gerenciar operações de Criar, Ler, Atualizar e Deletar usuários
"""

import mysql.connector
from mysql.connector import Error
from datetime import datetime
import bcrypt
from typing import Optional, List, Dict, Any
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


class UsuarioCRUD:
    """Classe para gerenciar operações CRUD de usuários"""
    
    def __init__(self, host: str, user: str, password: str, database: str):
        """
        Inicializa a conexão com o banco de dados
        
        Args:
            host: Endereço do servidor MySQL
            user: Usuário do banco de dados
            password: Senha do banco de dados
            database: Nome do banco de dados
        """
        self.host = host
        self.user = user
        self.password = password
        self.database = database
        self.connection = None
    
    def conectar(self) -> bool:
        """Estabelece conexão com o banco de dados"""
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                user=self.user,
                password=self.password,
                database=self.database
            )
            logger.info("Conexão com banco de dados estabelecida")
            return True
        except Error as e:
            logger.error(f"Erro ao conectar ao banco de dados: {e}")
            return False
    
    def desconectar(self):
        """Fecha a conexão com o banco de dados"""
        if self.connection and self.connection.is_connected():
            self.connection.close()
            logger.info("Conexão com banco de dados fechada")
    
    @staticmethod
    def hash_senha(senha: str) -> str:
        """
        Criptografa a senha usando bcrypt
        
        Args:
            senha: Senha em texto plano
            
        Returns:
            Senha criptografada
        """
        salt = bcrypt.gensalt()
        return bcrypt.hashpw(senha.encode('utf-8'), salt).decode('utf-8')
    
    @staticmethod
    def verificar_senha(senha: str, hash_senha: str) -> bool:
        """
        Verifica se a senha corresponde ao hash
        
        Args:
            senha: Senha em texto plano
            hash_senha: Hash da senha armazenada
            
        Returns:
            True se a senha está correta, False caso contrário
        """
        return bcrypt.checkpw(senha.encode('utf-8'), hash_senha.encode('utf-8'))
    
    def criar_usuario(self, nome_completo: str, email: str, senha: str, 
                     telefone: Optional[str] = None, endereco: Optional[str] = None,
                     cidade: Optional[str] = None, estado: Optional[str] = None,
                     cep: Optional[str] = None) -> Optional[int]:
        """
        Cria um novo usuário
        
        Args:
            nome_completo: Nome completo do usuário
            email: Email único do usuário
            senha: Senha em texto plano
            telefone: Telefone (opcional)
            endereco: Endereço (opcional)
            cidade: Cidade (opcional)
            estado: Estado (opcional)
            cep: CEP (opcional)
            
        Returns:
            ID do usuário criado ou None em caso de erro
        """
        try:
            cursor = self.connection.cursor()
            
            # Verificar se email já existe
            cursor.execute("SELECT id FROM usuarios WHERE email = %s", (email,))
            if cursor.fetchone():
                logger.warning(f"Email já cadastrado: {email}")
                return None
            
            # Hash da senha
            senha_hash = self.hash_senha(senha)
            
            # Inserir novo usuário
            sql = """
            INSERT INTO usuarios 
            (nome_completo, email, senha_hash, telefone, endereco, cidade, estado, cep, status)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 'ativo')
            """
            
            cursor.execute(sql, (nome_completo, email, senha_hash, telefone, 
                                endereco, cidade, estado, cep))
            self.connection.commit()
            
            usuario_id = cursor.lastrowid
            logger.info(f"Usuário criado com sucesso: ID {usuario_id}")
            cursor.close()
            
            return usuario_id
            
        except Error as e:
            logger.error(f"Erro ao criar usuário: {e}")
            self.connection.rollback()
            return None
    
    def obter_usuario(self, usuario_id: int) -> Optional[Dict[str, Any]]:
        """
        Obtém informações de um usuário
        
        Args:
            usuario_id: ID do usuário
            
        Returns:
            Dicionário com dados do usuário ou None
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
            SELECT id, nome_completo, email, telefone, endereco, cidade, 
                   estado, cep, data_cadastro, status
            FROM usuarios WHERE id = %s
            """
            
            cursor.execute(sql, (usuario_id,))
            usuario = cursor.fetchone()
            cursor.close()
            
            return usuario
            
        except Error as e:
            logger.error(f"Erro ao obter usuário: {e}")
            return None
    
    def obter_usuario_por_email(self, email: str) -> Optional[Dict[str, Any]]:
        """
        Obtém usuário pelo email
        
        Args:
            email: Email do usuário
            
        Returns:
            Dicionário com dados do usuário ou None
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
            SELECT id, nome_completo, email, telefone, endereco, cidade, 
                   estado, cep, data_cadastro, status, senha_hash
            FROM usuarios WHERE email = %s
            """
            
            cursor.execute(sql, (email,))
            usuario = cursor.fetchone()
            cursor.close()
            
            return usuario
            
        except Error as e:
            logger.error(f"Erro ao obter usuário por email: {e}")
            return None
    
    def listar_usuarios(self, status: str = 'ativo', limite: int = 100, 
                       offset: int = 0) -> List[Dict[str, Any]]:
        """
        Lista usuários com paginação
        
        Args:
            status: Status dos usuários ('ativo', 'inativo', 'bloqueado')
            limite: Número máximo de registros
            offset: Número de registros a pular
            
        Returns:
            Lista de usuários
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
            SELECT id, nome_completo, email, telefone, cidade, 
                   data_cadastro, status
            FROM usuarios 
            WHERE status = %s
            ORDER BY data_cadastro DESC
            LIMIT %s OFFSET %s
            """
            
            cursor.execute(sql, (status, limite, offset))
            usuarios = cursor.fetchall()
            cursor.close()
            
            return usuarios
            
        except Error as e:
            logger.error(f"Erro ao listar usuários: {e}")
            return []
    
    def atualizar_usuario(self, usuario_id: int, **kwargs) -> bool:
        """
        Atualiza dados de um usuário
        
        Args:
            usuario_id: ID do usuário
            **kwargs: Campos a atualizar (nome_completo, email, telefone, etc)
            
        Returns:
            True se atualizado com sucesso, False caso contrário
        """
        try:
            cursor = self.connection.cursor()
            
            # Campos permitidos para atualização
            campos_permitidos = {
                'nome_completo', 'email', 'telefone', 'endereco', 
                'cidade', 'estado', 'cep', 'status'
            }
            
            # Filtrar apenas campos permitidos
            campos_atualizacao = {k: v for k, v in kwargs.items() 
                                 if k in campos_permitidos and v is not None}
            
            if not campos_atualizacao:
                logger.warning("Nenhum campo válido para atualização")
                return False
            
            # Construir query dinâmica
            set_clause = ", ".join([f"{k} = %s" for k in campos_atualizacao.keys()])
            valores = list(campos_atualizacao.values()) + [usuario_id]
            
            sql = f"UPDATE usuarios SET {set_clause} WHERE id = %s"
            
            cursor.execute(sql, valores)
            self.connection.commit()
            
            if cursor.rowcount > 0:
                logger.info(f"Usuário {usuario_id} atualizado com sucesso")
                cursor.close()
                return True
            else:
                logger.warning(f"Usuário {usuario_id} não encontrado")
                cursor.close()
                return False
                
        except Error as e:
            logger.error(f"Erro ao atualizar usuário: {e}")
            self.connection.rollback()
            return False
    
    def alterar_senha(self, usuario_id: int, senha_atual: str, 
                     senha_nova: str) -> bool:
        """
        Altera a senha de um usuário
        
        Args:
            usuario_id: ID do usuário
            senha_atual: Senha atual para verificação
            senha_nova: Nova senha
            
        Returns:
            True se alterado com sucesso, False caso contrário
        """
        try:
            # Obter usuário
            usuario = self.obter_usuario(usuario_id)
            if not usuario:
                logger.warning(f"Usuário {usuario_id} não encontrado")
                return False
            
            # Verificar senha atual (precisamos obter o hash)
            cursor = self.connection.cursor()
            cursor.execute("SELECT senha_hash FROM usuarios WHERE id = %s", (usuario_id,))
            resultado = cursor.fetchone()
            
            if not resultado:
                return False
            
            hash_armazenado = resultado[0]
            
            if not self.verificar_senha(senha_atual, hash_armazenado):
                logger.warning(f"Senha atual incorreta para usuário {usuario_id}")
                return False
            
            # Atualizar com nova senha
            nova_hash = self.hash_senha(senha_nova)
            cursor.execute(
                "UPDATE usuarios SET senha_hash = %s WHERE id = %s",
                (nova_hash, usuario_id)
            )
            self.connection.commit()
            cursor.close()
            
            logger.info(f"Senha do usuário {usuario_id} alterada com sucesso")
            return True
            
        except Error as e:
            logger.error(f"Erro ao alterar senha: {e}")
            self.connection.rollback()
            return False
    
    def deletar_usuario(self, usuario_id: int) -> bool:
        """
        Deleta um usuário (soft delete - marca como inativo)
        
        Args:
            usuario_id: ID do usuário
            
        Returns:
            True se deletado com sucesso, False caso contrário
        """
        try:
            cursor = self.connection.cursor()
            
            # Soft delete - marcar como inativo
            cursor.execute(
                "UPDATE usuarios SET status = 'inativo' WHERE id = %s",
                (usuario_id,)
            )
            self.connection.commit()
            
            if cursor.rowcount > 0:
                logger.info(f"Usuário {usuario_id} marcado como inativo")
                cursor.close()
                return True
            else:
                logger.warning(f"Usuário {usuario_id} não encontrado")
                cursor.close()
                return False
                
        except Error as e:
            logger.error(f"Erro ao deletar usuário: {e}")
            self.connection.rollback()
            return False
    
    def autenticar_usuario(self, email: str, senha: str) -> Optional[Dict[str, Any]]:
        """
        Autentica um usuário com email e senha
        
        Args:
            email: Email do usuário
            senha: Senha em texto plano
            
        Returns:
            Dicionário com dados do usuário se autenticado, None caso contrário
        """
        try:
            usuario = self.obter_usuario_por_email(email)
            
            if not usuario:
                logger.warning(f"Usuário não encontrado: {email}")
                return None
            
            if usuario['status'] != 'ativo':
                logger.warning(f"Usuário inativo ou bloqueado: {email}")
                return None
            
            if self.verificar_senha(senha, usuario['senha_hash']):
                # Remover hash da senha da resposta
                del usuario['senha_hash']
                logger.info(f"Usuário autenticado: {email}")
                return usuario
            else:
                logger.warning(f"Senha incorreta para usuário: {email}")
                return None
                
        except Error as e:
            logger.error(f"Erro ao autenticar usuário: {e}")
            return None
    
    def contar_usuarios(self, status: str = 'ativo') -> int:
        """
        Conta o número de usuários
        
        Args:
            status: Status dos usuários
            
        Returns:
            Número de usuários
        """
        try:
            cursor = self.connection.cursor()
            cursor.execute("SELECT COUNT(*) FROM usuarios WHERE status = %s", (status,))
            resultado = cursor.fetchone()
            cursor.close()
            
            return resultado[0] if resultado else 0
            
        except Error as e:
            logger.error(f"Erro ao contar usuários: {e}")
            return 0


# Exemplo de uso
if __name__ == "__main__":
    # Configurar conexão
    crud = UsuarioCRUD(
        host='localhost',
        user='root',
        password='senha',
        database='guia_domestico'
    )
    
    # Conectar
    if crud.conectar():
        # Criar usuário
        usuario_id = crud.criar_usuario(
            nome_completo="João Silva",
            email="joao@example.com",
            senha="senha123",
            telefone="11987654321",
            cidade="São Paulo",
            estado="SP"
        )
        
        if usuario_id:
            print(f"Usuário criado com ID: {usuario_id}")
            
            # Obter usuário
            usuario = crud.obter_usuario(usuario_id)
            print(f"Dados do usuário: {usuario}")
            
            # Atualizar usuário
            crud.atualizar_usuario(usuario_id, telefone="11999999999")
            
            # Autenticar
            autenticado = crud.autenticar_usuario("joao@example.com", "senha123")
            print(f"Autenticação: {autenticado is not None}")
        
        # Desconectar
        crud.desconectar()

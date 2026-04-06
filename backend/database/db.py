"""
Módulo de Banco de Dados
Gerencia conexão e inicialização do banco de dados
"""

import mysql.connector
from mysql.connector import Error
import os
from dotenv import load_dotenv
import logging

load_dotenv()
logger = logging.getLogger(__name__)

# Configuração do banco de dados
DB_CONFIG = {
    'host': os.getenv('DB_HOST', 'localhost'),
    'user': os.getenv('DB_USER', 'root'),
    'password': os.getenv('DB_PASSWORD', ''),
    'database': os.getenv('DB_NAME', 'guia_domestico')
}

class Database:
    """Classe para gerenciar conexão com banco de dados"""
    
    _connection = None
    
    @classmethod
    def get_connection(cls):
        """Obter conexão com banco de dados"""
        try:
            if cls._connection is None or not cls._connection.is_connected():
                cls._connection = mysql.connector.connect(**DB_CONFIG)
                logger.info("Conexão com banco de dados estabelecida")
            return cls._connection
        except Error as e:
            logger.error(f"Erro ao conectar ao banco de dados: {e}")
            raise
    
    @classmethod
    def close_connection(cls):
        """Fechar conexão com banco de dados"""
        if cls._connection and cls._connection.is_connected():
            cls._connection.close()
            logger.info("Conexão com banco de dados fechada")
    
    @classmethod
    def execute_query(cls, query, params=None):
        """Executar query e retornar resultado"""
        try:
            connection = cls.get_connection()
            cursor = connection.cursor(dictionary=True)
            
            if params:
                cursor.execute(query, params)
            else:
                cursor.execute(query)
            
            connection.commit()
            cursor.close()
            
            return True
        except Error as e:
            logger.error(f"Erro ao executar query: {e}")
            return False
    
    @classmethod
    def fetch_all(cls, query, params=None):
        """Buscar todos os resultados"""
        try:
            connection = cls.get_connection()
            cursor = connection.cursor(dictionary=True)
            
            if params:
                cursor.execute(query, params)
            else:
                cursor.execute(query)
            
            results = cursor.fetchall()
            cursor.close()
            
            return results
        except Error as e:
            logger.error(f"Erro ao buscar resultados: {e}")
            return []
    
    @classmethod
    def fetch_one(cls, query, params=None):
        """Buscar um resultado"""
        try:
            connection = cls.get_connection()
            cursor = connection.cursor(dictionary=True)
            
            if params:
                cursor.execute(query, params)
            else:
                cursor.execute(query)
            
            result = cursor.fetchone()
            cursor.close()
            
            return result
        except Error as e:
            logger.error(f"Erro ao buscar resultado: {e}")
            return None
    
    @classmethod
    def insert(cls, query, params):
        """Inserir e retornar ID"""
        try:
            connection = cls.get_connection()
            cursor = connection.cursor()
            
            cursor.execute(query, params)
            connection.commit()
            
            insert_id = cursor.lastrowid
            cursor.close()
            
            return insert_id
        except Error as e:
            logger.error(f"Erro ao inserir: {e}")
            connection.rollback()
            return None
    
    @classmethod
    def update(cls, query, params):
        """Atualizar e retornar número de linhas afetadas"""
        try:
            connection = cls.get_connection()
            cursor = connection.cursor()
            
            cursor.execute(query, params)
            connection.commit()
            
            rows_affected = cursor.rowcount
            cursor.close()
            
            return rows_affected
        except Error as e:
            logger.error(f"Erro ao atualizar: {e}")
            connection.rollback()
            return 0
    
    @classmethod
    def delete(cls, query, params):
        """Deletar e retornar número de linhas afetadas"""
        try:
            connection = cls.get_connection()
            cursor = connection.cursor()
            
            cursor.execute(query, params)
            connection.commit()
            
            rows_affected = cursor.rowcount
            cursor.close()
            
            return rows_affected
        except Error as e:
            logger.error(f"Erro ao deletar: {e}")
            connection.rollback()
            return 0


def init_db():
    """Inicializar banco de dados com schema"""
    try:
        connection = mysql.connector.connect(
            host=DB_CONFIG['host'],
            user=DB_CONFIG['user'],
            password=DB_CONFIG['password']
        )
        cursor = connection.cursor()
        
        # Criar banco de dados se não existir
        cursor.execute(f"CREATE DATABASE IF NOT EXISTS {DB_CONFIG['database']}")
        logger.info(f"Banco de dados {DB_CONFIG['database']} criado/verificado")
        
        cursor.close()
        connection.close()
        
        # Executar schema
        with open('database/schema.sql', 'r', encoding='utf-8') as f:
            schema = f.read()
            
            connection = mysql.connector.connect(**DB_CONFIG)
            cursor = connection.cursor()
            
            # Executar cada comando do schema
            for statement in schema.split(';'):
                if statement.strip():
                    try:
                        cursor.execute(statement)
                    except Error as e:
                        # Ignorar erros de tabelas já existentes
                        if 'already exists' not in str(e):
                            logger.warning(f"Aviso ao executar schema: {e}")
            
            connection.commit()
            cursor.close()
            connection.close()
            
            logger.info("Schema do banco de dados aplicado com sucesso")
    
    except Error as e:
        logger.error(f"Erro ao inicializar banco de dados: {e}")
        raise

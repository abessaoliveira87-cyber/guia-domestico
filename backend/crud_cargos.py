"""
CRUD - Cargos Domésticos
Módulo para gerenciar operações de Criar, Ler, Atualizar e Deletar cargos
"""

import mysql.connector
from mysql.connector import Error
import json
from typing import Optional, List, Dict, Any
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


class CargoCRUD:
    """Classe para gerenciar operações CRUD de cargos"""
    
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
    
    def criar_cargo(self, numero_cargo: int, nome_cargo: str, cbo_codigo: str,
                   descricao_funcoes: str, responsabilidades: List[str] = None,
                   nao_responsabilidades: List[str] = None, obrigacoes: List[str] = None,
                   salario_minimo: float = None, salario_maximo: float = None,
                   salario_medio: float = None) -> Optional[int]:
        """
        Cria um novo cargo
        
        Args:
            numero_cargo: Número único do cargo
            nome_cargo: Nome do cargo
            cbo_codigo: Código CBO (Classificação Brasileira de Ocupações)
            descricao_funcoes: Descrição das funções
            responsabilidades: Lista de responsabilidades (JSON)
            nao_responsabilidades: Lista do que não é responsabilidade (JSON)
            obrigacoes: Lista de obrigações (JSON)
            salario_minimo: Salário mínimo
            salario_maximo: Salário máximo
            salario_medio: Salário médio
            
        Returns:
            ID do cargo criado ou None em caso de erro
        """
        try:
            cursor = self.connection.cursor()
            
            # Verificar se número de cargo já existe
            cursor.execute("SELECT id FROM cargos WHERE numero_cargo = %s", (numero_cargo,))
            if cursor.fetchone():
                logger.warning(f"Cargo com número {numero_cargo} já existe")
                return None
            
            # Converter listas para JSON
            resp_json = json.dumps(responsabilidades) if responsabilidades else None
            nao_resp_json = json.dumps(nao_responsabilidades) if nao_responsabilidades else None
            obrig_json = json.dumps(obrigacoes) if obrigacoes else None
            
            sql = """
            INSERT INTO cargos 
            (numero_cargo, nome_cargo, cbo_codigo, descricao_funcoes, 
             responsabilidades, nao_responsabilidades, obrigacoes,
             salario_minimo, salario_maximo, salario_medio, ativo)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, TRUE)
            """
            
            cursor.execute(sql, (numero_cargo, nome_cargo, cbo_codigo, descricao_funcoes,
                                resp_json, nao_resp_json, obrig_json,
                                salario_minimo, salario_maximo, salario_medio))
            self.connection.commit()
            
            cargo_id = cursor.lastrowid
            logger.info(f"Cargo criado com sucesso: ID {cargo_id}")
            cursor.close()
            
            return cargo_id
            
        except Error as e:
            logger.error(f"Erro ao criar cargo: {e}")
            self.connection.rollback()
            return None
    
    def obter_cargo(self, cargo_id: int) -> Optional[Dict[str, Any]]:
        """
        Obtém informações de um cargo
        
        Args:
            cargo_id: ID do cargo
            
        Returns:
            Dicionário com dados do cargo ou None
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
            SELECT id, numero_cargo, nome_cargo, cbo_codigo, descricao_funcoes,
                   responsabilidades, nao_responsabilidades, obrigacoes,
                   salario_minimo, salario_maximo, salario_medio,
                   data_criacao, ativo
            FROM cargos WHERE id = %s
            """
            
            cursor.execute(sql, (cargo_id,))
            cargo = cursor.fetchone()
            
            if cargo:
                # Converter JSON strings para listas
                if cargo['responsabilidades']:
                    cargo['responsabilidades'] = json.loads(cargo['responsabilidades'])
                if cargo['nao_responsabilidades']:
                    cargo['nao_responsabilidades'] = json.loads(cargo['nao_responsabilidades'])
                if cargo['obrigacoes']:
                    cargo['obrigacoes'] = json.loads(cargo['obrigacoes'])
            
            cursor.close()
            return cargo
            
        except Error as e:
            logger.error(f"Erro ao obter cargo: {e}")
            return None
    
    def obter_cargo_por_numero(self, numero_cargo: int) -> Optional[Dict[str, Any]]:
        """
        Obtém cargo pelo número
        
        Args:
            numero_cargo: Número do cargo
            
        Returns:
            Dicionário com dados do cargo ou None
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
            SELECT id, numero_cargo, nome_cargo, cbo_codigo, descricao_funcoes,
                   responsabilidades, nao_responsabilidades, obrigacoes,
                   salario_minimo, salario_maximo, salario_medio,
                   data_criacao, ativo
            FROM cargos WHERE numero_cargo = %s
            """
            
            cursor.execute(sql, (numero_cargo,))
            cargo = cursor.fetchone()
            
            if cargo:
                if cargo['responsabilidades']:
                    cargo['responsabilidades'] = json.loads(cargo['responsabilidades'])
                if cargo['nao_responsabilidades']:
                    cargo['nao_responsabilidades'] = json.loads(cargo['nao_responsabilidades'])
                if cargo['obrigacoes']:
                    cargo['obrigacoes'] = json.loads(cargo['obrigacoes'])
            
            cursor.close()
            return cargo
            
        except Error as e:
            logger.error(f"Erro ao obter cargo por número: {e}")
            return None
    
    def listar_cargos(self, ativo: bool = True, limite: int = 100, 
                     offset: int = 0) -> List[Dict[str, Any]]:
        """
        Lista cargos com paginação
        
        Args:
            ativo: Se True, lista apenas cargos ativos
            limite: Número máximo de registros
            offset: Número de registros a pular
            
        Returns:
            Lista de cargos
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
            SELECT id, numero_cargo, nome_cargo, cbo_codigo, descricao_funcoes,
                   salario_minimo, salario_maximo, salario_medio, ativo
            FROM cargos 
            WHERE ativo = %s
            ORDER BY numero_cargo ASC
            LIMIT %s OFFSET %s
            """
            
            cursor.execute(sql, (ativo, limite, offset))
            cargos = cursor.fetchall()
            cursor.close()
            
            return cargos
            
        except Error as e:
            logger.error(f"Erro ao listar cargos: {e}")
            return []
    
    def buscar_cargos(self, termo_busca: str, limite: int = 50) -> List[Dict[str, Any]]:
        """
        Busca cargos por nome ou CBO
        
        Args:
            termo_busca: Termo para buscar
            limite: Número máximo de resultados
            
        Returns:
            Lista de cargos encontrados
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            termo = f"%{termo_busca}%"
            
            sql = """
            SELECT id, numero_cargo, nome_cargo, cbo_codigo, 
                   salario_minimo, salario_maximo, salario_medio
            FROM cargos 
            WHERE (nome_cargo LIKE %s OR cbo_codigo LIKE %s) AND ativo = TRUE
            LIMIT %s
            """
            
            cursor.execute(sql, (termo, termo, limite))
            cargos = cursor.fetchall()
            cursor.close()
            
            return cargos
            
        except Error as e:
            logger.error(f"Erro ao buscar cargos: {e}")
            return []
    
    def atualizar_cargo(self, cargo_id: int, **kwargs) -> bool:
        """
        Atualiza dados de um cargo
        
        Args:
            cargo_id: ID do cargo
            **kwargs: Campos a atualizar
            
        Returns:
            True se atualizado com sucesso, False caso contrário
        """
        try:
            cursor = self.connection.cursor()
            
            # Campos permitidos
            campos_permitidos = {
                'nome_cargo', 'cbo_codigo', 'descricao_funcoes',
                'responsabilidades', 'nao_responsabilidades', 'obrigacoes',
                'salario_minimo', 'salario_maximo', 'salario_medio', 'ativo'
            }
            
            campos_atualizacao = {}
            for k, v in kwargs.items():
                if k in campos_permitidos and v is not None:
                    # Converter listas para JSON
                    if k in ['responsabilidades', 'nao_responsabilidades', 'obrigacoes']:
                        campos_atualizacao[k] = json.dumps(v) if isinstance(v, list) else v
                    else:
                        campos_atualizacao[k] = v
            
            if not campos_atualizacao:
                logger.warning("Nenhum campo válido para atualização")
                return False
            
            set_clause = ", ".join([f"{k} = %s" for k in campos_atualizacao.keys()])
            valores = list(campos_atualizacao.values()) + [cargo_id]
            
            sql = f"UPDATE cargos SET {set_clause} WHERE id = %s"
            
            cursor.execute(sql, valores)
            self.connection.commit()
            
            if cursor.rowcount > 0:
                logger.info(f"Cargo {cargo_id} atualizado com sucesso")
                cursor.close()
                return True
            else:
                logger.warning(f"Cargo {cargo_id} não encontrado")
                cursor.close()
                return False
                
        except Error as e:
            logger.error(f"Erro ao atualizar cargo: {e}")
            self.connection.rollback()
            return False
    
    def deletar_cargo(self, cargo_id: int) -> bool:
        """
        Deleta um cargo (soft delete - marca como inativo)
        
        Args:
            cargo_id: ID do cargo
            
        Returns:
            True se deletado com sucesso, False caso contrário
        """
        try:
            cursor = self.connection.cursor()
            
            # Soft delete
            cursor.execute(
                "UPDATE cargos SET ativo = FALSE WHERE id = %s",
                (cargo_id,)
            )
            self.connection.commit()
            
            if cursor.rowcount > 0:
                logger.info(f"Cargo {cargo_id} marcado como inativo")
                cursor.close()
                return True
            else:
                logger.warning(f"Cargo {cargo_id} não encontrado")
                cursor.close()
                return False
                
        except Error as e:
            logger.error(f"Erro ao deletar cargo: {e}")
            self.connection.rollback()
            return False
    
    def contar_cargos(self, ativo: bool = True) -> int:
        """
        Conta o número de cargos
        
        Args:
            ativo: Se True, conta apenas cargos ativos
            
        Returns:
            Número de cargos
        """
        try:
            cursor = self.connection.cursor()
            cursor.execute("SELECT COUNT(*) FROM cargos WHERE ativo = %s", (ativo,))
            resultado = cursor.fetchone()
            cursor.close()
            
            return resultado[0] if resultado else 0
            
        except Error as e:
            logger.error(f"Erro ao contar cargos: {e}")
            return 0
    
    def obter_faixa_salarial(self, cargo_id: int) -> Optional[Dict[str, float]]:
        """
        Obtém a faixa salarial de um cargo
        
        Args:
            cargo_id: ID do cargo
            
        Returns:
            Dicionário com salário mínimo, máximo e médio
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
            SELECT salario_minimo, salario_maximo, salario_medio
            FROM cargos WHERE id = %s
            """
            
            cursor.execute(sql, (cargo_id,))
            resultado = cursor.fetchone()
            cursor.close()
            
            return resultado
            
        except Error as e:
            logger.error(f"Erro ao obter faixa salarial: {e}")
            return None


# Exemplo de uso
if __name__ == "__main__":
    crud = CargoCRUD(
        host='localhost',
        user='root',
        password='senha',
        database='guia_domestico'
    )
    
    if crud.conectar():
        # Criar cargo
        cargo_id = crud.criar_cargo(
            numero_cargo=1,
            nome_cargo="Faxineira",
            cbo_codigo="5121-15",
            descricao_funcoes="Realiza limpeza profunda de ambientes",
            responsabilidades=["Limpeza de pisos", "Limpeza de vidros"],
            salario_medio=1700.00
        )
        
        if cargo_id:
            print(f"Cargo criado com ID: {cargo_id}")
            
            # Obter cargo
            cargo = crud.obter_cargo(cargo_id)
            print(f"Dados do cargo: {cargo}")
            
            # Listar cargos
            cargos = crud.listar_cargos(limite=5)
            print(f"Total de cargos: {len(cargos)}")
        
        crud.desconectar()

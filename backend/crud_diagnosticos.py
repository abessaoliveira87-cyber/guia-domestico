"""
CRUD - Diagnósticos
Módulo para gerenciar operações de Criar, Ler, Atualizar e Deletar diagnósticos
Inclui cálculos de direitos trabalhistas
"""

import mysql.connector
from mysql.connector import Error
from datetime import datetime, date
from typing import Optional, List, Dict, Any
import logging
from decimal import Decimal

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


class DiagnosticoCRUD:
    """Classe para gerenciar operações CRUD de diagnósticos"""
    
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
    def calcular_salario_por_hora(salario_mensal: float, horas_por_dia: float, 
                                  dias_por_semana: int) -> float:
        """
        Calcula o salário por hora
        
        Args:
            salario_mensal: Salário mensal
            horas_por_dia: Horas trabalhadas por dia
            dias_por_semana: Dias trabalhados por semana
            
        Returns:
            Salário por hora
        """
        horas_por_mes = (horas_por_dia * dias_por_semana * 52) / 12
        return salario_mensal / horas_por_mes if horas_por_mes > 0 else 0
    
    @staticmethod
    def calcular_carga_horaria_mensal(horas_por_dia: float, dias_por_semana: int) -> float:
        """
        Calcula a carga horária mensal
        
        Args:
            horas_por_dia: Horas por dia
            dias_por_semana: Dias por semana
            
        Returns:
            Carga horária mensal
        """
        return (horas_por_dia * dias_por_semana * 52) / 12
    
    @staticmethod
    def calcular_tempo_servico(data_admissao: date) -> int:
        """
        Calcula o tempo de serviço em meses
        
        Args:
            data_admissao: Data de admissão
            
        Returns:
            Tempo de serviço em meses
        """
        hoje = date.today()
        meses = (hoje.year - data_admissao.year) * 12 + (hoje.month - data_admissao.month)
        return max(0, meses)
    
    @staticmethod
    def calcular_ferias(salario_mensal: float, tempo_servico_meses: int) -> Dict[str, float]:
        """
        Calcula direito a férias
        
        Args:
            salario_mensal: Salário mensal
            tempo_servico_meses: Tempo de serviço em meses
            
        Returns:
            Dicionário com dias de férias e valor
        """
        # 30 dias de férias a cada 12 meses
        dias_ferias = (tempo_servico_meses // 12) * 30
        
        # Adicionar proporção se houver meses restantes
        meses_restantes = tempo_servico_meses % 12
        if meses_restantes >= 1:
            dias_ferias += (meses_restantes * 30) // 12
        
        # Valor das férias com 1/3 adicional
        valor_ferias = (salario_mensal / 30) * dias_ferias * (4/3)
        
        return {
            'dias_ferias': dias_ferias,
            'valor_ferias': round(valor_ferias, 2)
        }
    
    @staticmethod
    def calcular_decimo_terceiro(salario_mensal: float, tempo_servico_meses: int) -> float:
        """
        Calcula o 13º salário proporcional
        
        Args:
            salario_mensal: Salário mensal
            tempo_servico_meses: Tempo de serviço em meses
            
        Returns:
            Valor do 13º salário proporcional
        """
        meses_trabalhados = tempo_servico_meses % 12
        if meses_trabalhados == 0 and tempo_servico_meses > 0:
            meses_trabalhados = 12
        
        valor_13 = (salario_mensal / 12) * meses_trabalhados
        return round(valor_13, 2)
    
    @staticmethod
    def calcular_fgts(salario_mensal: float, tempo_servico_meses: int) -> float:
        """
        Calcula o FGTS mensal (8% do salário)
        
        Args:
            salario_mensal: Salário mensal
            tempo_servico_meses: Tempo de serviço em meses
            
        Returns:
            Valor mensal do FGTS
        """
        return round(salario_mensal * 0.08, 2)
    
    @staticmethod
    def calcular_inss(salario_mensal: float) -> float:
        """
        Calcula o desconto do INSS (9% para doméstico)
        
        Args:
            salario_mensal: Salário mensal
            
        Returns:
            Valor do desconto INSS
        """
        # Alíquota para empregado doméstico é 9%
        return round(salario_mensal * 0.09, 2)
    
    def criar_diagnostico(self, usuario_id: int, cargo_id: int, 
                         salario_mensal: float, data_admissao: date,
                         horas_por_dia: float, dias_por_semana: int) -> Optional[int]:
        """
        Cria um novo diagnóstico com cálculos automáticos
        
        Args:
            usuario_id: ID do usuário
            cargo_id: ID do cargo
            salario_mensal: Salário mensal
            data_admissao: Data de admissão
            horas_por_dia: Horas por dia
            dias_por_semana: Dias por semana
            
        Returns:
            ID do diagnóstico criado ou None em caso de erro
        """
        try:
            cursor = self.connection.cursor()
            
            # Calcular valores
            salario_por_hora = self.calcular_salario_por_hora(
                salario_mensal, horas_por_dia, dias_por_semana
            )
            
            carga_horaria_mensal = self.calcular_carga_horaria_mensal(
                horas_por_dia, dias_por_semana
            )
            
            tempo_servico_meses = self.calcular_tempo_servico(data_admissao)
            
            ferias = self.calcular_ferias(salario_mensal, tempo_servico_meses)
            
            decimo_terceiro = self.calcular_decimo_terceiro(
                salario_mensal, tempo_servico_meses
            )
            
            fgts = self.calcular_fgts(salario_mensal, tempo_servico_meses)
            
            inss = self.calcular_inss(salario_mensal)
            
            # Inserir diagnóstico
            sql = """
            INSERT INTO diagnosticos 
            (usuario_id, cargo_id, salario_mensal, data_admissao, 
             horas_por_dia, dias_por_semana, salario_por_hora, 
             carga_horaria_mensal, tempo_servico_meses, dias_ferias, 
             valor_ferias, valor_decimo_terceiro, valor_fgts_mensal, 
             valor_inss_desconto, status)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'ativo')
            """
            
            cursor.execute(sql, (
                usuario_id, cargo_id, salario_mensal, data_admissao,
                horas_por_dia, dias_por_semana, salario_por_hora,
                carga_horaria_mensal, tempo_servico_meses, ferias['dias_ferias'],
                ferias['valor_ferias'], decimo_terceiro, fgts, inss
            ))
            
            self.connection.commit()
            
            diagnostico_id = cursor.lastrowid
            logger.info(f"Diagnóstico criado com sucesso: ID {diagnostico_id}")
            cursor.close()
            
            return diagnostico_id
            
        except Error as e:
            logger.error(f"Erro ao criar diagnóstico: {e}")
            self.connection.rollback()
            return None
    
    def obter_diagnostico(self, diagnostico_id: int) -> Optional[Dict[str, Any]]:
        """
        Obtém informações de um diagnóstico
        
        Args:
            diagnostico_id: ID do diagnóstico
            
        Returns:
            Dicionário com dados do diagnóstico ou None
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
            SELECT * FROM diagnosticos WHERE id = %s
            """
            
            cursor.execute(sql, (diagnostico_id,))
            diagnostico = cursor.fetchone()
            cursor.close()
            
            return diagnostico
            
        except Error as e:
            logger.error(f"Erro ao obter diagnóstico: {e}")
            return None
    
    def listar_diagnosticos_usuario(self, usuario_id: int, 
                                   limite: int = 100, offset: int = 0) -> List[Dict[str, Any]]:
        """
        Lista diagnósticos de um usuário
        
        Args:
            usuario_id: ID do usuário
            limite: Número máximo de registros
            offset: Número de registros a pular
            
        Returns:
            Lista de diagnósticos
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
            SELECT d.id, d.cargo_id, c.nome_cargo, d.salario_mensal, 
                   d.data_admissao, d.tempo_servico_meses, d.dias_ferias,
                   d.valor_ferias, d.valor_decimo_terceiro, d.data_geracao, d.status
            FROM diagnosticos d
            JOIN cargos c ON d.cargo_id = c.id
            WHERE d.usuario_id = %s AND d.status = 'ativo'
            ORDER BY d.data_geracao DESC
            LIMIT %s OFFSET %s
            """
            
            cursor.execute(sql, (usuario_id, limite, offset))
            diagnosticos = cursor.fetchall()
            cursor.close()
            
            return diagnosticos
            
        except Error as e:
            logger.error(f"Erro ao listar diagnósticos: {e}")
            return []
    
    def obter_diagnostico_completo(self, diagnostico_id: int) -> Optional[Dict[str, Any]]:
        """
        Obtém diagnóstico com informações completas do usuário e cargo
        
        Args:
            diagnostico_id: ID do diagnóstico
            
        Returns:
            Dicionário com dados completos ou None
        """
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            sql = """
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
            WHERE d.id = %s
            """
            
            cursor.execute(sql, (diagnostico_id,))
            diagnostico = cursor.fetchone()
            cursor.close()
            
            return diagnostico
            
        except Error as e:
            logger.error(f"Erro ao obter diagnóstico completo: {e}")
            return None
    
    def atualizar_diagnostico(self, diagnostico_id: int, **kwargs) -> bool:
        """
        Atualiza um diagnóstico
        
        Args:
            diagnostico_id: ID do diagnóstico
            **kwargs: Campos a atualizar
            
        Returns:
            True se atualizado com sucesso, False caso contrário
        """
        try:
            cursor = self.connection.cursor()
            
            campos_permitidos = {
                'salario_mensal', 'data_admissao', 'horas_por_dia', 
                'dias_por_semana', 'status'
            }
            
            campos_atualizacao = {k: v for k, v in kwargs.items() 
                                 if k in campos_permitidos and v is not None}
            
            if not campos_atualizacao:
                return False
            
            set_clause = ", ".join([f"{k} = %s" for k in campos_atualizacao.keys()])
            valores = list(campos_atualizacao.values()) + [diagnostico_id]
            
            sql = f"UPDATE diagnosticos SET {set_clause} WHERE id = %s"
            
            cursor.execute(sql, valores)
            self.connection.commit()
            
            if cursor.rowcount > 0:
                logger.info(f"Diagnóstico {diagnostico_id} atualizado com sucesso")
                cursor.close()
                return True
            else:
                logger.warning(f"Diagnóstico {diagnostico_id} não encontrado")
                cursor.close()
                return False
                
        except Error as e:
            logger.error(f"Erro ao atualizar diagnóstico: {e}")
            self.connection.rollback()
            return False
    
    def arquivar_diagnostico(self, diagnostico_id: int) -> bool:
        """
        Arquiva um diagnóstico
        
        Args:
            diagnostico_id: ID do diagnóstico
            
        Returns:
            True se arquivado com sucesso, False caso contrário
        """
        try:
            cursor = self.connection.cursor()
            
            cursor.execute(
                "UPDATE diagnosticos SET status = 'arquivado' WHERE id = %s",
                (diagnostico_id,)
            )
            self.connection.commit()
            
            if cursor.rowcount > 0:
                logger.info(f"Diagnóstico {diagnostico_id} arquivado com sucesso")
                cursor.close()
                return True
            else:
                logger.warning(f"Diagnóstico {diagnostico_id} não encontrado")
                cursor.close()
                return False
                
        except Error as e:
            logger.error(f"Erro ao arquivar diagnóstico: {e}")
            self.connection.rollback()
            return False
    
    def deletar_diagnostico(self, diagnostico_id: int) -> bool:
        """
        Deleta um diagnóstico (soft delete)
        
        Args:
            diagnostico_id: ID do diagnóstico
            
        Returns:
            True se deletado com sucesso, False caso contrário
        """
        return self.arquivar_diagnostico(diagnostico_id)
    
    def contar_diagnosticos_usuario(self, usuario_id: int) -> int:
        """
        Conta diagnósticos de um usuário
        
        Args:
            usuario_id: ID do usuário
            
        Returns:
            Número de diagnósticos
        """
        try:
            cursor = self.connection.cursor()
            cursor.execute(
                "SELECT COUNT(*) FROM diagnosticos WHERE usuario_id = %s AND status = 'ativo'",
                (usuario_id,)
            )
            resultado = cursor.fetchone()
            cursor.close()
            
            return resultado[0] if resultado else 0
            
        except Error as e:
            logger.error(f"Erro ao contar diagnósticos: {e}")
            return 0
    
    def obter_resumo_direitos(self, diagnostico_id: int) -> Optional[Dict[str, Any]]:
        """
        Obtém resumo dos direitos calculados
        
        Args:
            diagnostico_id: ID do diagnóstico
            
        Returns:
            Dicionário com resumo dos direitos
        """
        diagnostico = self.obter_diagnostico_completo(diagnostico_id)
        
        if not diagnostico:
            return None
        
        return {
            'cargo': diagnostico['nome_cargo'],
            'salario_mensal': float(diagnostico['salario_mensal']),
            'salario_por_hora': float(diagnostico['salario_por_hora']),
            'tempo_servico_meses': diagnostico['tempo_servico_meses'],
            'dias_ferias': diagnostico['dias_ferias'],
            'valor_ferias': float(diagnostico['valor_ferias']),
            'valor_decimo_terceiro': float(diagnostico['valor_decimo_terceiro']),
            'valor_fgts_mensal': float(diagnostico['valor_fgts_mensal']),
            'valor_inss_desconto': float(diagnostico['valor_inss_desconto']),
            'salario_liquido': float(diagnostico['salario_mensal']) - float(diagnostico['valor_inss_desconto'])
        }


# Exemplo de uso
if __name__ == "__main__":
    crud = DiagnosticoCRUD(
        host='localhost',
        user='root',
        password='senha',
        database='guia_domestico'
    )
    
    if crud.conectar():
        # Criar diagnóstico
        diagnostico_id = crud.criar_diagnostico(
            usuario_id=1,
            cargo_id=1,
            salario_mensal=1500.00,
            data_admissao=date(2022, 1, 15),
            horas_por_dia=8,
            dias_por_semana=5
        )
        
        if diagnostico_id:
            print(f"Diagnóstico criado com ID: {diagnostico_id}")
            
            # Obter diagnóstico completo
            diag = crud.obter_diagnostico_completo(diagnostico_id)
            print(f"Dados do diagnóstico: {diag}")
            
            # Obter resumo de direitos
            resumo = crud.obter_resumo_direitos(diagnostico_id)
            print(f"Resumo de direitos: {resumo}")
        
        crud.desconectar()

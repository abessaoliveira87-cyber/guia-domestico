#!/usr/bin/env python3
"""
Script para executar a aplicação Flask
"""

import os
import sys
from app import app

if __name__ == '__main__':
    # Adicionar diretório atual ao path
    sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
    
    # Executar aplicação
    app.run()

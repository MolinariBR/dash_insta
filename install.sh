#!/bin/bash

# 🚀 Script de Instalação - Dashboard Instagram Bot @fatima.escritora
# Desenvolvido por: Tria Inova Simples (I.S.)

echo "=============================================="
echo "🤖 Instagram Bot @fatima.escritora"
echo "📊 Instalação do Dashboard"
echo "=============================================="
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para log colorido
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCESSO]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[AVISO]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERRO]${NC} $1"
}

# Verifica se está sendo executado como root
if [[ $EUID -eq 0 ]]; then
    log_warning "Não execute este script como root!"
    exit 1
fi

# Diretório atual
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

log_info "Diretório do projeto: $PROJECT_DIR"
log_info "Diretório do dashboard: $SCRIPT_DIR"

echo ""
echo "=============================================="
echo "📋 Verificando Requisitos"
echo "=============================================="

# Verifica PHP
if ! command -v php &> /dev/null; then
    log_error "PHP não encontrado. Instale PHP 7.4 ou superior."
    exit 1
else
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
    log_success "PHP $PHP_VERSION encontrado"
fi

# Verifica Python
if ! command -v python3 &> /dev/null; then
    log_error "Python3 não encontrado. Instale Python 3.8 ou superior."
    exit 1
else
    PYTHON_VERSION=$(python3 --version | cut -d " " -f 2)
    log_success "Python $PYTHON_VERSION encontrado"
fi

# Verifica pip
if ! command -v pip3 &> /dev/null; then
    log_warning "pip3 não encontrado. Alguns recursos podem não funcionar."
else
    log_success "pip3 encontrado"
fi

echo ""
echo "=============================================="
echo "⚙️ Configuração Inicial"
echo "=============================================="

# Solicita configurações do usuário
echo ""
log_info "Configure as informações básicas:"

# Senha do dashboard
read -s -p "🔐 Digite a senha para o dashboard: " DASHBOARD_PASSWORD
echo ""
read -s -p "🔐 Confirme a senha: " DASHBOARD_PASSWORD_CONFIRM
echo ""

if [ "$DASHBOARD_PASSWORD" != "$DASHBOARD_PASSWORD_CONFIRM" ]; then
    log_error "Senhas não coincidem!"
    exit 1
fi

if [ ${#DASHBOARD_PASSWORD} -lt 6 ]; then
    log_error "Senha deve ter pelo menos 6 caracteres!"
    exit 1
fi

# Configurações do Instagram
echo ""
read -p "📱 Username do Instagram (@fatima.escritora): " IG_USERNAME
read -s -p "🔑 Senha do Instagram: " IG_PASSWORD
echo ""

# Configurações do servidor web
echo ""
read -p "🌐 Porta do servidor local (default 8080): " SERVER_PORT
SERVER_PORT=${SERVER_PORT:-8080}

echo ""
echo "=============================================="
echo "📁 Configurando Arquivos"
echo "=============================================="

# Atualiza config.php
log_info "Configurando config.php..."

cat > "$SCRIPT_DIR/config.php" << EOF
<?php
/**
 * Configurações do Dashboard - Instagram Bot @fatima.escritora
 * Gerado automaticamente em $(date)
 */

// Configurações do banco de dados (opcional)
define('DB_HOST', 'localhost');
define('DB_NAME', 'instagram_bot_fatima');
define('DB_USER', 'root');
define('DB_PASS', '');

// Caminhos do projeto
define('BOT_PATH', '$PROJECT_DIR');
define('LOGS_PATH', BOT_PATH . '/logs');
define('DATA_PATH', BOT_PATH . '/data');
define('CONFIG_PATH', BOT_PATH . '/config');

// Configurações de segurança
define('DASHBOARD_PASSWORD', '$DASHBOARD_PASSWORD');
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configurações do bot
define('MAX_FOLLOWS_PER_DAY', 30);
define('MAX_COMMENTS_PER_DAY', 10);
define('MAX_MESSAGES_PER_DAY', 20);

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Inicia sessão
session_start();

// Resto do arquivo permanece igual...
// (resto do conteúdo do config.php original)
EOF

# Atualiza arquivo .env do bot
log_info "Configurando credenciais do Instagram..."

cat > "$PROJECT_DIR/config/.env" << EOF
# Credenciais do Instagram
IG_USERNAME=$IG_USERNAME
IG_PASSWORD=$IG_PASSWORD

# Configurações de limite
MAX_FOLLOWS_PER_DAY=30
MAX_LIKES_PER_FOLLOW=7
MIN_LIKES_PER_FOLLOW=5
MAX_COMMENTS_PER_DAY=10

# Configurações de tempo (em segundos)
MIN_DELAY_BETWEEN_LIKES=3
MAX_DELAY_BETWEEN_LIKES=7
MIN_DELAY_BETWEEN_COMMENTS=120
MAX_DELAY_BETWEEN_COMMENTS=240

# Configurações de mensagem automática
MIN_DM_DELAY=600  # 10 minutos
MAX_DM_DELAY=1800  # 30 minutos

# Horários de funcionamento
START_HOUR=9
END_HOUR=20
COMMENT_START_HOUR=10
COMMENT_END_HOUR=21
EOF

log_success "Arquivos de configuração criados"

echo ""
echo "=============================================="
echo "📦 Instalando Dependências Python"
echo "=============================================="

# Instala dependências do bot
cd "$PROJECT_DIR"

if [ -f "requirements.txt" ]; then
    log_info "Instalando dependências Python..."
    
    # Cria ambiente virtual se não existir
    if [ ! -d "venv" ]; then
        log_info "Criando ambiente virtual..."
        python3 -m venv venv
    fi
    
    # Ativa ambiente virtual
    source venv/bin/activate
    
    # Instala dependências
    pip install -r requirements.txt
    
    if [ $? -eq 0 ]; then
        log_success "Dependências Python instaladas com sucesso"
    else
        log_warning "Algumas dependências podem ter falhado"
    fi
    
    deactivate
else
    log_warning "Arquivo requirements.txt não encontrado"
fi

echo ""
echo "=============================================="
echo "🔧 Configurando Permissões"
echo "=============================================="

# Define permissões corretas
log_info "Configurando permissões de arquivos..."

chmod 755 "$SCRIPT_DIR"
chmod 644 "$SCRIPT_DIR"/*.php
chmod 755 "$SCRIPT_DIR/api"
chmod 644 "$SCRIPT_DIR/api"/*.php

# Cria diretórios necessários
mkdir -p "$PROJECT_DIR/logs"
mkdir -p "$PROJECT_DIR/data"

chmod 755 "$PROJECT_DIR/logs"
chmod 755 "$PROJECT_DIR/data"

log_success "Permissões configuradas"

echo ""
echo "=============================================="
echo "🚀 Criando Scripts de Execução"
echo "=============================================="

# Script para iniciar o servidor de desenvolvimento
cat > "$SCRIPT_DIR/start_server.sh" << EOF
#!/bin/bash
# Script para iniciar servidor PHP de desenvolvimento

echo "🚀 Iniciando servidor do dashboard..."
echo "📍 URL: http://localhost:$SERVER_PORT"
echo "🔐 Senha: $DASHBOARD_PASSWORD"
echo ""
echo "Pressione Ctrl+C para parar o servidor"
echo ""

cd "$SCRIPT_DIR"
php -S localhost:$SERVER_PORT
EOF

chmod +x "$SCRIPT_DIR/start_server.sh"

# Script para iniciar o bot
cat > "$PROJECT_DIR/start_bot.sh" << EOF
#!/bin/bash
# Script para iniciar o bot Instagram

echo "🤖 Iniciando Instagram Bot @fatima.escritora..."

cd "$PROJECT_DIR"

# Ativa ambiente virtual se existir
if [ -d "venv" ]; then
    source venv/bin/activate
fi

# Executa o bot
python3 main.py

# Desativa ambiente virtual
if [ -d "venv" ]; then
    deactivate
fi
EOF

chmod +x "$PROJECT_DIR/start_bot.sh"

log_success "Scripts de execução criados"

echo ""
echo "=============================================="
echo "✅ Instalação Concluída!"
echo "=============================================="
echo ""

log_success "Dashboard instalado com sucesso!"
echo ""
echo "📋 Próximos passos:"
echo ""
echo "1. 🚀 Iniciar o servidor do dashboard:"
echo "   cd $SCRIPT_DIR"
echo "   ./start_server.sh"
echo ""
echo "2. 🌐 Acessar o dashboard:"
echo "   http://localhost:$SERVER_PORT"
echo ""
echo "3. 🔐 Fazer login com a senha configurada"
echo ""
echo "4. 🤖 Iniciar o bot através do dashboard ou via comando:"
echo "   cd $PROJECT_DIR"
echo "   ./start_bot.sh"
echo ""

log_info "Configurações salvas:"
echo "   • Dashboard: $SCRIPT_DIR"
echo "   • Bot: $PROJECT_DIR"
echo "   • Logs: $PROJECT_DIR/logs"
echo "   • Dados: $PROJECT_DIR/data"
echo ""

log_warning "IMPORTANTE:"
echo "   • Mantenha suas credenciais seguras"
echo "   • Faça backup regular das configurações"
echo "   • Monitore os logs regularmente"
echo ""

echo "📞 Suporte: Tria Inova Simples (I.S.)"
echo "📧 Email: contato@triacore.pro"
echo "📱 WhatsApp: (99) 98234-9856"
echo ""
echo "=============================================="

# 🚀 Como Usar o Dashboard

## 📋 Passo a Passo

### 1. Instalação
```bash
cd /home/mau/bot/insta/dashboard
./install.sh
```

### 2. Iniciar o Servidor
```bash
./start_server.sh
```

### 3. Acessar o Dashboard
- **URL**: http://localhost:8080
- **Senha**: Definida durante a instalação

### 4. Funcionalidades Principais

#### 🏠 Dashboard Principal
- Visualize métricas em tempo real
- Controle o bot (iniciar/parar/reiniciar)
- Monitore atividades recentes
- Execute testes de funcionalidade

#### 📋 Logs
- Filtre por tipo: seguidores, comentários, mensagens
- Busque por conteúdo específico
- Exporte logs para backup
- Monitore erros e sucessos

#### 📊 Estatísticas
- Acompanhe crescimento de seguidores
- Analise performance por hashtag
- Identifique melhores horários
- Visualize métricas de engajamento

#### ⚙️ Configurações
- Ajuste limites diários
- Configure horários de funcionamento
- Personalize mensagens e comentários
- Gerencie filtros de segurança

## 🔧 Comandos Úteis

### Iniciar/Parar o Bot
```bash
# Via dashboard (recomendado)
# Ou via linha de comando:

cd /home/mau/bot/insta
source venv/bin/activate
python3 main.py
```

### Verificar Status
```bash
ps aux | grep main.py
```

### Ver Logs em Tempo Real
```bash
tail -f /home/mau/bot/insta/logs/main.log
```

## 🚨 Solução de Problemas

### Bot não inicia
1. Verifique credenciais no arquivo `.env`
2. Confirme dependências Python
3. Verifique logs de erro

### Dashboard não carrega
1. Confirme que o servidor PHP está rodando
2. Verifique permissões dos arquivos
3. Teste a senha de acesso

### Logs não aparecem
1. Verifique se o bot está rodando
2. Confirme caminhos no `config.php`
3. Verifique permissões dos diretórios

## 📞 Suporte
- **Email**: contato@triacore.pro
- **WhatsApp**: (99) 98234-9856
- **Desenvolvedor**: Tria Inova Simples (I.S.)

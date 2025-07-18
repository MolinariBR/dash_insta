# DEPLOY.md — Guia de Deploy Produção/Staging dash_insta

## 1. Pré-requisitos
- PHP 8.0+ (com SQLite3 habilitado)
- Python 3.8+ (com pip)
- Composer
- Git
- (Opcional) Docker
- Servidor Linux (Ubuntu recomendado)

---

## 2. Clonando o Projeto
```bash
git clone https://github.com/MolinariBR/dash_insta.git
cd dash_insta
git clone https://github.com/MolinariBR/bot_instagram.git ../insta
```

---

## 3. Instalando Dependências
### PHP
```bash
composer install
```
### Python
```bash
cd ../insta
pip install -r requirements.txt
```

---

## 4. Configurando o Banco de Dados
```bash
cd ../dash_insta
mkdir -p data logs
sqlite3 data/database.db < data/schema.sql
```

---

## 5. Variáveis e Configurações
- Edite `config.php`, `dashboard_config.php` e `insta/config/settings.json` conforme necessário.
- (Opcional) Use `.env` para variáveis sensíveis.

---

## 6. Permissões
```bash
chmod 750 logs data vendor
chown -R www-data:www-data logs data vendor
```

---

## 7. Iniciando o Backend
### Desenvolvimento
```bash
php -S 0.0.0.0:8080
```
### Produção (Apache/Nginx)
- Configure o VirtualHost para apontar para a pasta do projeto.
- Certifique-se de que o usuário do servidor web tem permissão nas pastas `logs/`, `data/` e `vendor/`.

---

## 8. Iniciando o Bot Python
```bash
cd ../insta
nohup python3 main.py &
```
- Para múltiplas contas:
```bash
python3 main.py --conta <username>
```

---

## 9. Monitoramento e Healthcheck
- Use `/api/status` e `/api/logs` para monitorar o sistema.
- (Opcional) Configure UptimeRobot, Healthchecks.io ou scripts customizados para checar endpoints.
- Monitore logs em `logs/` e `insta/logs/`.

---

## 10. Backup e Restore
- Faça backup regular das pastas `data/` e `logs/`.
- Para restaurar, basta copiar os arquivos de volta e garantir permissões corretas.

---

## 11. Atualização
```bash
git pull
composer install
pip install -r ../insta/requirements.txt
```

---

## 12. Suporte
- Dúvidas: contato@triacore.pro
- WhatsApp: (99) 98234-9856

---

**Última atualização:** 2025-07-18 
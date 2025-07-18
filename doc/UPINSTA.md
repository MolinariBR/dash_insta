# UPINSTA.md — Passo a Passo de Atualização e Desenvolvimento do dash_insta

---

## Objetivo
Este documento reúne, de forma prática e sequencial, as etapas recomendadas para atualizar, evoluir e manter o projeto **dash_insta**. Agora, o sistema suporta múltiplos clientes, cada um com suas próprias contas do Instagram, garantindo controle individualizado e profissional.

---

## 1. Planejamento Inicial
- Leia atentamente o [README.md](./README.md) para entender o propósito, tecnologias e requisitos do projeto.
- Consulte o [DOC.md](./DOC.md) para detalhes técnicos, estrutura e fluxos internos.
- Revise o [UPDATE.md](./UPDATE.md) para histórico, mudanças críticas e guias de migração.

---

## 2. Preparação do Ambiente
1. **Clone o repositório:**
   ```bash
   git clone https://github.com/MolinariBR/dash_insta.git
   cd dash_insta
   ```
2. **Instale dependências PHP:**
   ```bash
   composer install
   ```
3. **Instale dependências Python:**
   ```bash
   cd ../insta
   pip install -r requirements.txt
   ```
4. **Configure o banco de dados:**
   - O banco SQLite será criado automaticamente em `data/database.db`.
   - Crie as tabelas principais executando:
   ```bash
   sqlite3 data/database.db "CREATE TABLE IF NOT EXISTS clientes (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT NOT NULL, email TEXT NOT NULL UNIQUE, empresa TEXT, cpf TEXT, cnpj TEXT, nome_projeto TEXT, contato TEXT, data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP);"

   sqlite3 data/database.db "CREATE TABLE IF NOT EXISTS contas_instagram (id INTEGER PRIMARY KEY AUTOINCREMENT, cliente_id INTEGER NOT NULL, username TEXT NOT NULL, senha TEXT NOT NULL, status TEXT DEFAULT 'ativa', data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP, ultima_atividade DATETIME, FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE);"
   ```
5. **Ajuste arquivos de configuração:**
   - Edite `config.php`, `dashboard_config.php` e `insta/config/settings.json`.
   - (Opcional) Configure variáveis sensíveis em `.env`.

---

## 3. Cadastro e Gerenciamento de Clientes e Contas
- Cadastre clientes com nome, email, empresa, cpf, cnpj, nome do projeto e contato.
- Associe uma ou mais contas do Instagram a cada cliente.
- Todas as ações, métricas e logs são controlados individualmente por conta e por cliente.

---

## 4. Desenvolvimento de Funcionalidades
- Consulte o [DOC.md](./DOC.md) para:
  - Estrutura de pastas e arquivos
  - Fluxo de funcionamento
  - Integração entre PHP e Python
  - Boas práticas de segurança e testes
- Implemente novas features sempre em branches separadas.
- Documente cada alteração relevante.

---

## 5. Testes e Validação
- Crie e mantenha testes automatizados em `tests/` (PHPUnit).
- Execute:
  ```bash
  ./vendor/bin/phpunit
  ```
- Teste fluxos críticos: cadastro de cliente, associação de contas, login, troca de conta, logs, métricas, comandos do bot.
- Valide isolamento entre clientes, contas e segurança dos dados.

---

## 6. Deploy e Monitoramento
- Para desenvolvimento, use:
  ```bash
  php -S localhost:8080
  ```
- Para produção, configure Apache/Nginx corretamente.
- Inicie o bot Python para cada conta ativa:
  ```bash
  cd ../insta
  python3 main.py --conta <username>
  ```
- Monitore logs, métricas e performance do sistema.

---

## 7. Manutenção e Evolução
- Mantenha a documentação (README.md, DOC.md, UPDATE.md, UPINSTA.md) sempre atualizada.
- Revise periodicamente a segurança (hash de senhas, validação de inputs, logs).
- Implemente melhorias sugeridas no UPDATE.md e registre novas demandas.
- Faça code review e testes antes de cada deploy.

---

## 8. Suporte e Contato
- Para dúvidas técnicas, consulte o [DOC.md](./DOC.md) e [UPDATE.md](./UPDATE.md).
- Para suporte, utilize o e-mail: [contato@suaempresa.com.br](mailto:contato@suaempresa.com.br)
- Para questões comerciais, acesse: [https://www.suaempresa.com.br](https://www.suaempresa.com.br)

---

> Siga este roteiro para garantir um ciclo de desenvolvimento seguro, organizado e eficiente no dash_insta. 
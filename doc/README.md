# dash_insta

Dashboard Profissional para Gerenciamento de Contas do Instagram

---

## Sobre o Projeto
O **dash_insta** é um painel web completo para automação, monitoramento e controle de contas do Instagram. Suporta múltiplos clientes, cada um podendo ter uma ou mais contas do Instagram, com controle individualizado, métricas detalhadas, logs, controle de ações e gerenciamento seguro. Para detalhes técnicos, consulte o [DOC.md](./DOC.md).

---

## Modelagem de Dados
O sistema utiliza duas tabelas principais no SQLite:
- **clientes**: armazena dados do cliente (nome, email, empresa, cpf, cnpj, nome do projeto, contato, data de cadastro).
- **contas_instagram**: armazena as contas do Instagram, associando cada uma a um cliente via `cliente_id`.

Cada cliente pode ter várias contas do Instagram, e todas as ações, métricas e logs são controlados individualmente por conta e por cliente.

---

## Tecnologias Utilizadas
- **PHP 8+** — Backend do dashboard, APIs e integração com banco de dados SQLite
- **Python 3.8+** — Bot de automação do Instagram (instagrapi)
- **Composer** — Gerenciamento de dependências PHP
- **SQLite** — Banco de dados relacional local
- **HTML5, CSS3, TailwindCSS** — Interface web responsiva
- **JavaScript** — Interatividade no frontend
- **PHPUnit** — Testes automatizados
- **Docker (opcional)** — Para facilitar deploy e ambiente isolado

---

## Guia de Instalação

### 1. Pré-requisitos
- PHP 8.0 ou superior
- Composer
- Python 3.8 ou superior
- SQLite3
- Git
- (Opcional) Docker

### 2. Clone o repositório
```bash
git clone https://github.com/MolinariBR/dash_insta.git
cd dash_insta
```

### 3. Instale as dependências PHP
```bash
composer install
```

### 4. Instale as dependências Python
```bash
cd ../insta
pip install -r requirements.txt
```

### 5. Configure o banco de dados
- O banco SQLite será criado automaticamente em `data/database.db`.
- Crie as tabelas principais executando:
```bash
sqlite3 data/database.db "CREATE TABLE IF NOT EXISTS clientes (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT NOT NULL, email TEXT NOT NULL UNIQUE, empresa TEXT, cpf TEXT, cnpj TEXT, nome_projeto TEXT, contato TEXT, data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP);"

sqlite3 data/database.db "CREATE TABLE IF NOT EXISTS contas_instagram (id INTEGER PRIMARY KEY AUTOINCREMENT, cliente_id INTEGER NOT NULL, username TEXT NOT NULL, senha TEXT NOT NULL, status TEXT DEFAULT 'ativa', data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP, ultima_atividade DATETIME, FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE);"
```

### 6. Configure variáveis e arquivos de ambiente
- Edite `config.php`, `dashboard_config.php` e `insta/config/settings.json` conforme sua necessidade.
- (Opcional) Use `.env` para variáveis sensíveis.

### 7. Inicie o servidor
- Para desenvolvimento:
```bash
php -S localhost:8080
```
- Para produção, configure Apache/Nginx apontando para a pasta do projeto.

### 8. Inicie o bot Python
```bash
cd ../insta
python3 main.py
```

---

## Como Usar
- Acesse `http://localhost:8080` no navegador.
- Faça login com a senha definida em `config.php`.
- Cadastre clientes e associe contas do Instagram a cada cliente.
- Gerencie contas, visualize métricas, logs e controle o bot pelo painel.
- Para multi-conta, utilize a interface de gerenciamento de contas (ver [UPDATE.md](./UPDATE.md)).

---

## Testes Automatizados
- Os testes PHP estão na pasta `tests/`.
- Execute:
```bash
./vendor/bin/phpunit
```

---

## Contribuição
1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b minha-feature`)
3. Commit suas alterações (`git commit -am 'Minha feature'`)
4. Push para a branch (`git push origin minha-feature`)
5. Abra um Pull Request

---

## Documentação e Atualizações
- Documentação técnica: [DOC.md](./DOC.md)
- Histórico e guia de atualização: [UPDATE.md](./UPDATE.md)

---

## Dados da Empresa
- **Nome:** [Nome da Empresa]
- **CNPJ:** [00.000.000/0000-00]
- **Site:** [https://www.suaempresa.com.br](https://www.suaempresa.com.br)

---

## Licença
Este projeto é privado e protegido por direitos autorais. Para uso comercial, entre em contato com a empresa responsável.

---

## Contato
Dúvidas, sugestões ou suporte: [contato@suaempresa.com.br](mailto:contato@suaempresa.com.br)

---

> Desenvolvido com dedicação para automação e gestão profissional de Instagram.

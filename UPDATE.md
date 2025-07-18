# UPDATE.md — Histórico e Guia de Atualização do dash_insta

---

## Histórico de Atualizações

- **[2025-07-16]** Estrutura de testes automatizados adicionada (PHPUnit, pasta tests/)
- **[2025-07-16]** Guia detalhado para migração multi-conta (ver seção abaixo)
- **[2025-07-16]** Organização e versionamento de dependências, scripts e arquivos auxiliares
- **[2025-07-16]** Criação e atualização dos arquivos de documentação: README.md, DOC.md, UPINSTA.md
- **[2025-07-16]** Nova modelagem de dados: cadastro de clientes e associação de contas do Instagram a clientes

---

## Guia de Atualização para Multi-Conta e Multi-Cliente

### 1. Modelagem de Dados
- Crie as tabelas principais no SQLite:
```sql
CREATE TABLE clientes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nome TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  empresa TEXT,
  cpf TEXT,
  cnpj TEXT,
  nome_projeto TEXT,
  contato TEXT,
  data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contas_instagram (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  cliente_id INTEGER NOT NULL,
  username TEXT NOT NULL,
  senha TEXT NOT NULL,
  status TEXT DEFAULT 'ativa',
  data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
  ultima_atividade DATETIME,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);
```
- Cada cliente pode ter várias contas do Instagram associadas.
- O cadastro de clientes é fundamental para controle individualizado, relatórios e gestão de projetos.
- Consulte o DOC.md (seção 5) para detalhes de banco e possíveis extensões.

### 2. Refatoração de Configurações
- Remova variáveis de ambiente fixas para usuário/senha.
- Adapte o backend para buscar credenciais no banco conforme a conta selecionada.
- Cada conta deve ter seu próprio arquivo de sessão (ex: `session_{username}.json`).

### 3. Interface do Dashboard
- Implemente tela de gerenciamento de clientes e suas contas (listar, adicionar, editar, remover, selecionar ativa).
- Todas as ações do dashboard devem ser referentes ao cliente e à conta escolhidos.

### 4. Backend PHP
- Adapte funções para receber o identificador do cliente e da conta.
- Separe logs e métricas por conta e por cliente (ex: `logs/{cliente_id}/{username}/`).

### 5. Bot Python
- Permita múltiplas instâncias do cliente Instagram, cada uma com suas credenciais e sessão.
- Scripts devem receber o username/id da conta como parâmetro.

### 6. Segurança
- Armazene senhas com hash seguro (bcrypt ou superior).
- Implemente limite de até 30 contas cadastradas por cliente, se necessário.

### 7. Testes e Validação
- Crie testes para garantir isolamento entre clientes e contas.
- Teste login, logs, métricas e comandos para múltiplos clientes e contas simultâneas.

### 8. Documentação
- Mantenha README.md, DOC.md, UPDATE.md e UPINSTA.md atualizados conforme alterações.

---

## Referências
- Para detalhes técnicos e fluxos, consulte o DOC.md.
- Para instalação e uso, veja o README.md.

---

> Siga cada etapa cuidadosamente para garantir uma transição suave e segura para o novo modelo de gerenciamento de múltiplos clientes e contas do Instagram. 
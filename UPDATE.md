# Atualização do Dashboard para Gerenciamento de até 30 Contas do Instagram

Este guia detalha todos os passos necessários para transformar o dashboard atual, que gerencia uma única conta, em um sistema multi-contas capaz de gerenciar até 30 perfis do Instagram.

---

## 1. Modelagem de Dados

### 1.1. Crie a tabela de contas no banco de dados
Adicione uma tabela para armazenar as credenciais e informações das contas:

```sql
CREATE TABLE contas_instagram (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  senha VARCHAR(255) NOT NULL, -- Armazene criptografada
  status VARCHAR(20) DEFAULT 'ativa',
  data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
  ultima_atividade DATETIME
);
```

---

## 2. Refatoração das Configurações

### 2.1. Remova variáveis de ambiente fixas para usuário/senha
- Elimine IG_USERNAME e IG_PASSWORD do .env e do código.
- As credenciais devem ser buscadas no banco de dados conforme a conta selecionada.

### 2.2. Sessões separadas
- Cada conta deve ter seu próprio arquivo de sessão (ex: `session_{username}.json`).

---

## 3. Interface do Dashboard

### 3.1. Tela de Gerenciamento de Contas
- Crie uma página para:
  - Listar contas cadastradas (até 30).
  - Adicionar nova conta (formulário de usuário/senha).
  - Editar/remover contas.
  - Selecionar conta ativa.

### 3.2. Fluxo de Seleção
- Ao selecionar uma conta, todas as ações do dashboard (logs, métricas, comandos) devem ser referentes à conta escolhida.

---

## 4. Backend PHP

### 4.1. Adapte funções para múltiplas contas
- Todas as funções que interagem com o bot, logs ou métricas devem receber o identificador da conta (id ou username).
- Exemplo: `startBot($conta_id)`, `getBotStats($conta_id)`.

### 4.2. Logs e métricas separados
- Armazene arquivos de log e métricas em subpastas por conta (ex: `logs/{username}/`).

---

## 5. Bot Python

### 5.1. Inicialização multi-conta
- Permita inicializar múltiplas instâncias do cliente Instagram, cada uma com suas credenciais e sessão.
- Scripts devem receber o username/id da conta como parâmetro.
- Exemplo de chamada:
  ```bash
  python3 main.py --conta fatima.escritora
  ```

### 5.2. Sessões separadas
- Cada instância deve usar seu próprio arquivo de sessão.

---

## 6. Segurança

### 6.1. Armazenamento seguro de senhas
- Utilize hash seguro (ex: bcrypt) para armazenar senhas no banco.
- Nunca armazene senhas em texto puro.

### 6.2. Limite de contas
- Implemente validação para não permitir mais de 30 contas cadastradas.

---

## 7. Ajustes Gerais

### 7.1. Adapte o frontend para alternar entre contas
- Exiba claramente qual conta está ativa.
- Permita alternar rapidamente entre contas.

### 7.2. Testes
- Crie testes para garantir que cada conta opera de forma isolada.
- Teste o login, logs, métricas e comandos para múltiplas contas simultâneas.

---

## 8. Migração de Dados (se necessário)
- Se já houver dados de uma conta, migre para o novo formato/tabela.
- Atualize scripts e arquivos antigos para a nova estrutura.

---

## 9. Documentação
- Atualize o README e demais documentos para refletir o novo funcionamento multi-conta.

---

## 10. Checklist Final
- [ ] Tabela de contas criada
- [ ] Backend adaptado para múltiplas contas
- [ ] Bot Python aceitando múltiplas instâncias
- [ ] Interface de gerenciamento de contas implementada
- [ ] Segurança revisada
- [ ] Testes realizados
- [ ] Documentação atualizada

---

Siga cada etapa cuidadosamente para garantir uma transição suave e segura para o novo modelo de gerenciamento de múltiplas contas do Instagram. 
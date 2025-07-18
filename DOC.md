# Documentação Técnica — dash_insta

---

## Índice
1. Visão Geral do Projeto
2. Estrutura de Pastas e Arquivos
3. Fluxo de Funcionamento
4. Configuração do Ambiente
5. Banco de Dados
6. Funcionalidades do Dashboard
7. Integração com o Bot Python
8. Testes Automatizados
9. Segurança
10. FAQ e Solução de Problemas
11. Contato e Suporte

---

## 1. Visão Geral do Projeto
O **dash_insta** é um dashboard web para automação, monitoramento e controle de contas do Instagram, integrando backend em PHP, automação Python e interface web moderna. Suporta múltiplas contas (ver UPDATE.md), métricas detalhadas, logs, controle de ações e gerenciamento seguro.

---

## 2. Estrutura de Pastas e Arquivos

```
dash_insta/
├── api/                # Endpoints PHP para controle e logs
├── assets/             # Recursos estáticos (imagens, CSS, JS)
├── data/               # Schemas SQL, arquivos de dados
├── includes/           # Header, footer e componentes comuns
├── metricas/           # Scripts de métricas (PHP e Python)
├── tests/              # Testes automatizados (PHPUnit)
├── vendor/             # Dependências PHP (Composer)
├── config.php          # Configurações principais do dashboard
├── dashboard_config.php# Configurações de modo demo/produção
├── index.php           # Página principal do dashboard
├── login.php           # Tela de login
├── logs.php            # Visualização de logs
├── metricas.php        # Página de métricas
├── settings.php        # Configurações avançadas
├── README.md           # Guia rápido e institucional
├── UPDATE.md           # Histórico e guia de atualização
├── DOC.md              # Documentação técnica detalhada
└── ...
```

---

## 3. Fluxo de Funcionamento
1. Usuário acessa o dashboard via navegador e faz login.
2. Seleciona ou cadastra uma conta do Instagram (multi-conta).
3. O dashboard exibe métricas, logs e status do bot para a conta ativa.
4. Usuário pode iniciar/parar o bot, visualizar logs, ajustar configurações e analisar estatísticas.
5. O backend PHP se comunica com o bot Python para executar ações automatizadas.

---

## 4. Configuração do Ambiente
- Siga o passo a passo do README.md para instalação de dependências, configuração do banco e variáveis de ambiente.
- Certifique-se de que o bot Python está corretamente configurado em `insta/config/settings.json`.
- Para multi-conta, crie a tabela de contas conforme UPDATE.md.

---

## 5. Banco de Dados
- Utiliza MySQL/MariaDB.
- Schema inicial em `data/schema.sql`.
- Para multi-conta, utilize:
```sql
CREATE TABLE contas_instagram (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  status VARCHAR(20) DEFAULT 'ativa',
  data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
  ultima_atividade DATETIME
);
```
- Outras tabelas podem ser criadas para logs, métricas e configurações.

---

## 6. Funcionalidades do Dashboard
- **Login seguro** com timeout de sessão
- **Gerenciamento de múltiplas contas**
- **Visualização de métricas**: seguidores, comentários, mensagens, etc.
- **Logs detalhados**: ações do bot, erros, atividades
- **Controle do bot**: iniciar, parar, reiniciar, testar ações
- **Configurações avançadas**: limites, horários, filtros, hashtags
- **Exportação de dados**: logs e métricas
- **Interface responsiva**

---

## 7. Integração com o Bot Python
- O backend PHP executa comandos no bot Python via shell (ex: start, stop, test_follow).
- O bot Python utiliza a biblioteca instagrapi para automação do Instagram.
- Cada conta tem sua própria sessão e arquivos de log.
- Logs e métricas são sincronizados entre bot e dashboard.

---

## 8. Testes Automatizados
- Testes PHP estão em `tests/` e usam PHPUnit.
- Para rodar os testes:
```bash
./vendor/bin/phpunit
```
- Recomenda-se criar testes para todas as funções críticas do backend.

---

## 9. Segurança
- Senhas armazenadas de forma segura (use hash forte para multi-conta)
- Inputs validados e sanitizados
- Sessões com timeout e logout automático
- Proteção contra XSS e SQL Injection
- Logs não expõem dados sensíveis

---

## 10. FAQ e Solução de Problemas
**1. O dashboard não conecta ao banco de dados**
- Verifique as credenciais em `config.php`.
- Certifique-se de que o MySQL/MariaDB está rodando.

**2. O bot não executa comandos**
- Verifique permissões de execução dos scripts Python.
- Confira se as dependências Python estão instaladas.

**3. Erros de login**
- Confira a senha definida em `config.php`.
- Limpe cookies e tente novamente.

**4. Problemas com multi-conta**
- Certifique-se de que a tabela de contas foi criada.
- Verifique se cada conta tem sua própria sessão e logs.

---

## 11. Contato e Suporte
- Para dúvidas técnicas, sugestões ou suporte, utilize o e-mail: [contato@suaempresa.com.br](mailto:contato@suaempresa.com.br)
- Para questões comerciais, acesse: [https://www.suaempresa.com.br](https://www.suaempresa.com.br)

---

> Documentação elaborada para garantir clareza, segurança e facilidade de uso no dash_insta. Mantenha este arquivo atualizado conforme evoluções do projeto.

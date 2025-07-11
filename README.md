# 📊 Dashboard - Instagram Bot @fatima.escritora

Dashboard web em PHP com Tailwind CSS e DaisyUI para gerenciar o bot do Instagram da escritora Fátima Ribeiro Espíndola.

## 🚀 Funcionalidades

### 🔐 Sistema de Login
- Autenticação segura com senha
- Sessões com timeout automático
- Interface responsiva e moderna

### 📈 Dashboard Principal
- **Métricas em tempo real**: Follows, comentários, mensagens
- **Status do bot**: Online/Offline com indicador visual
- **Controles**: Iniciar, parar e reiniciar o bot
- **Logs recentes**: Visualização das últimas atividades
- **Ações rápidas**: Testes de funcionalidades

### 📋 Gerenciamento de Logs
- **Visualização completa**: Todos os tipos de logs (seguidores, comentários, mensagens)
- **Filtros avançados**: Por tipo, nível e busca textual
- **Estatísticas**: Contadores por categoria
- **Export/Download**: Backup dos logs em JSON
- **Atualização automática**: Refresh a cada 30 segundos

### 📊 Estatísticas e Análises
- **Gráficos interativos**: Chart.js para visualização de dados
- **Métricas de performance**: Taxas de sucesso e engajamento
- **Análise por origem**: Seguidores por fonte (hashtags, perfis, etc.)
- **Horários de atividade**: Identificação dos melhores momentos
- **Hashtags mais utilizadas**: Ranking de performance
- **Relatórios de crescimento**: Indicadores visuais

### ⚙️ Configurações Avançadas
- **Limites e cotas**: Configuração de máximos diários
- **Horários de funcionamento**: Definição de períodos ativos
- **Delays e intervalos**: Controle de tempo entre ações
- **Hashtags alvo**: Gerenciamento das tags principais
- **Filtros de segurança**: Proteção contra conteúdo inadequado
- **Mensagens personalizadas**: Customização de textos e emojis
- **Import/Export**: Backup e restauração de configurações

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, JavaScript ES6+
- **CSS Framework**: Tailwind CSS 3.x
- **UI Components**: DaisyUI 4.x
- **Ícones**: Font Awesome 6.x
- **Gráficos**: Chart.js 4.x
- **Banco de dados**: MySQL (opcional)

## 📁 Estrutura de Arquivos

```
dashboard/
├── index.php              # Dashboard principal
├── login.php              # Página de login
├── logs.php               # Visualização de logs
├── stats.php              # Estatísticas e gráficos
├── settings.php           # Configurações do sistema
├── config.php             # Configurações globais
├── includes/
│   ├── header.php         # Cabeçalho comum
│   └── footer.php         # Rodapé comum
├── api/
│   ├── status.php         # Status do bot (JSON)
│   ├── control.php        # Controle do bot (start/stop)
│   └── logs.php           # API de logs (JSON)
└── assets/               # Recursos estáticos (futuro)
```

## 🔧 Instalação e Configuração

### 1. Requisitos
- PHP 7.4 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: `json`, `session`, `mysqli` (opcional)

### 2. Configuração
1. **Edite o arquivo `config.php`**:
   ```php
   // Senha de acesso ao dashboard
   define('DASHBOARD_PASSWORD', 'sua_senha_aqui');
   
   // Caminhos do projeto
   define('BOT_PATH', '/caminho/para/o/bot');
   ```

2. **Configure permissões**:
   ```bash
   chmod 755 dashboard/
   chmod 644 dashboard/*.php
   ```

3. **Acesse o dashboard**:
   ```
   http://seu-servidor/dashboard/
   ```

### 3. Credenciais de Acesso
- **Usuário**: Admin (sem username)
- **Senha**: Definida em `DASHBOARD_PASSWORD` no config.php

## 🎨 Interface e Design

### Tema e Cores
- **Tema principal**: Light mode com DaisyUI
- **Cores primárias**: Azul (#3B82F6) e roxo (#8B5CF6)
- **Gradientes**: Utilizados nos cards de métricas
- **Responsivo**: Mobile-first design

### Componentes UI
- **Cards**: Informações organizadas em cartões
- **Badges**: Status e categorias
- **Modals**: Formulários e detalhes
- **Tables**: Listagem de dados
- **Charts**: Gráficos interativos
- **Toasts**: Notificações temporárias

## 📊 Funcionalidades Detalhadas

### Dashboard Principal
- **Métricas em tempo real**: Atualização automática via AJAX
- **Indicadores visuais**: Barras de progresso e status coloridos
- **Controles do bot**: Botões para gerenciar execução
- **Preview de logs**: Últimas atividades em tempo real

### Sistema de Logs
- **Tipos suportados**: seguidores, comentários, mensagens, curtidas
- **Filtros avançados**: Por nível (INFO, ERROR, WARNING)
- **Busca textual**: Pesquisa em tempo real nos logs
- **Paginação**: Navegação eficiente em grandes volumes
- **Export**: Download em formato JSON

### Análises e Relatórios
- **Gráfico de linha**: Atividades ao longo da semana
- **Gráfico circular**: Distribuição de tipos de ação
- **Métricas de performance**: Indicadores visuais circulares
- **Tabelas de origem**: Análise de fontes de seguidores
- **Rankings**: Hashtags e horários mais efetivos

### Configurações
- **Validação em tempo real**: Campos com limites automáticos
- **Backup/Restore**: Sistema completo de configurações
- **Interfaces por abas**: Organização clara das opções
- **Toggles visuais**: Ativação/desativação de recursos

## 🔒 Segurança

### Autenticação
- **Sistema de sessões**: Timeout automático
- **Verificação de acesso**: Middleware em todas as páginas
- **Logout automático**: Após período de inatividade

### Proteção de Dados
- **Sanitização**: Todos os inputs são filtrados
- **Escape HTML**: Prevenção de XSS
- **Validação**: Tipos e formatos de dados
- **Logs seguros**: Não exposição de dados sensíveis

## 🚀 Uso e Operação

### Fluxo de Trabalho
1. **Login**: Acesso com senha
2. **Monitoramento**: Dashboard com métricas
3. **Controle**: Start/stop do bot
4. **Análise**: Visualização de logs e estatísticas
5. **Configuração**: Ajuste de parâmetros
6. **Manutenção**: Backup e otimização

### Monitoramento
- **Status visual**: Indicadores de funcionamento
- **Alertas**: Notificações de problemas
- **Métricas**: Acompanhamento de performance
- **Logs**: Rastreamento detalhado de ações

## 📱 Responsividade

### Breakpoints
- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

### Adaptações
- **Menu mobile**: Hamburger menu
- **Cards**: Stack vertical em telas pequenas
- **Tabelas**: Scroll horizontal
- **Gráficos**: Redimensionamento automático

## 🎯 Próximas Melhorias

### Funcionalidades Planejadas
- [ ] **Notificações push**: Alertas em tempo real
- [ ] **Relatórios PDF**: Export de análises
- [ ] **Multi-usuário**: Sistema de permissões
- [ ] **API REST**: Integração externa
- [ ] **Dashboard mobile**: App dedicado
- [ ] **Backup automático**: Configurações e dados
- [ ] **Alertas por email**: Notificações importantes
- [ ] **Webhooks**: Integração com outros sistemas

### Melhorias Técnicas
- [ ] **Cache**: Otimização de performance
- [ ] **Websockets**: Atualizações em tempo real
- [ ] **Logs estruturados**: Formato JSON
- [ ] **Métricas avançadas**: Mais KPIs
- [ ] **Testes automatizados**: Qualidade de código

## 📞 Suporte

**Desenvolvido por**: Tria Inova Simples (I.S.)  
**CNPJ**: 60.967.428/0001-30  
**Local**: Imperatriz - MA  
**Email**: contato@triacore.pro  
**Telefone**: (99) 98234-9856

---

**Uso exclusivo**: Este dashboard é propriedade da escritora Fátima Ribeiro Espíndola.  
**Distribuição não autorizada é proibida**.

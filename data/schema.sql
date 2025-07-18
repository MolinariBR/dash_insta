-- SQLite3 schema para dash_insta
-- Tabela de usuários do painel
CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    senha TEXT NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de campanhas (Facebook, Google, Instagram)
CREATE TABLE IF NOT EXISTS campanhas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    plataforma TEXT NOT NULL,
    status TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de métricas das campanhas
CREATE TABLE IF NOT EXISTS metricas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campanha_id INTEGER,
    data DATE NOT NULL,
    cpc REAL,
    vendas INTEGER,
    resultado REAL,
    FOREIGN KEY (campanha_id) REFERENCES campanhas(id)
);

-- Tabela de alertas
CREATE TABLE IF NOT EXISTS alertas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campanha_id INTEGER,
    tipo TEXT,
    mensagem TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campanha_id) REFERENCES campanhas(id)
);

-- Tabela de logs do sistema
CREATE TABLE IF NOT EXISTS logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    modulo TEXT,
    nivel TEXT,
    mensagem TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de configurações (chaves, tokens, limites)
CREATE TABLE IF NOT EXISTS configuracoes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    chave TEXT UNIQUE NOT NULL,
    valor TEXT NOT NULL,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

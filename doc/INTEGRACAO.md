# INTEGRACAO.md — Integração Backend, Robô Python e IA

## Visão Geral
Este documento detalha os fluxos de comunicação, endpoints REST e formatos de dados para integração entre o backend PHP, o robô Python e o futuro módulo de IA.

---

## 1. Arquitetura de Comunicação

- **Backend PHP**: expõe endpoints REST para cadastro, comandos, métricas e logs.
- **Robô Python**: consome endpoints do backend e pode expor endpoints para status/controle.
- **IA (futura)**: expõe API REST para análise semântica, sugestões e respostas inteligentes.

---

## 2. Endpoints REST Sugeridos

### Backend PHP
- `POST /api/comando` — Envia comando para o robô
- `GET /api/status` — Status do robô, contas, métricas
- `GET /api/logs` — Logs por conta/perfil
- `POST /api/feedback` — Recebe feedback/resultados do robô ou IA
- `GET /api/perfis` — Lista perfis/contas gerenciadas

### Robô Python (opcional)
- `POST /robo/status` — Robô envia status para o backend
- `POST /robo/logs` — Robô envia logs/métricas para o backend

### IA (futura)
- `POST /ia/analisar` — Recebe texto/conteúdo para análise semântica
- `POST /ia/sugerir` — Recebe contexto e retorna sugestão/resposta inteligente

---

## 3. Exemplos de Payloads (JSON)

### Comando para o robô
```json
{
  "conta_id": 123,
  "acao": "iniciar",
  "parametros": {
    "hashtag": "#exemplo"
  }
}
```

### Status do robô
```json
{
  "conta_id": 123,
  "status": "executando",
  "ultima_atividade": "2025-07-18T14:00:00Z"
}
```

### Requisição para IA
```json
{
  "texto": "Mensagem recebida do usuário",
  "contexto": {
    "perfil": "empresa",
    "tipo": "comentario"
  }
}
```

### Resposta da IA
```json
{
  "resposta": "Obrigado pelo seu comentário!",
  "confianca": 0.92
}
```

---

## 4. Autenticação
- Recomenda-se uso de token (Bearer) ou API key nos headers das requisições.
- Exemplo:
  ```http
  Authorization: Bearer <token>
  ```

---

## 5. Fluxo de Integração

1. **Usuário aciona ação no dashboard** (ex: iniciar robô para uma conta)
2. **Backend envia comando via /api/comando**
3. **Robô consome comando, executa ação e envia status/logs de volta**
4. **(Opcional) Backend ou robô envia texto para IA via /ia/analisar ou /ia/sugerir**
5. **IA responde com análise/sugestão, que pode ser exibida no dashboard ou usada pelo robô**

---

## 6. Mock de Endpoint da IA (para testes)

Exemplo de FastAPI (Python):
```python
from fastapi import FastAPI
from pydantic import BaseModel

app = FastAPI()

class AnaliseRequest(BaseModel):
    texto: str
    contexto: dict

@app.post("/ia/analisar")
def analisar(req: AnaliseRequest):
    return {"resposta": "Simulação de resposta IA", "confianca": 0.85}
```

---

## 7. Observações
- Todos os endpoints devem retornar JSON padronizado.
- Documente sempre novos endpoints e payloads.
- Para integração real, alinhe autenticação e versionamento de API.

---

**Última atualização:** 2025-07-18 
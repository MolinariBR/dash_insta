# EXEMPLOS_API.md — Exemplos de Uso dos Endpoints REST

## 1. Enviar Comando para o Robô (/api/comando)

### curl
```bash
curl -X POST http://localhost:8080/api/comando \
  -H 'Content-Type: application/json' \
  -d '{"conta_id":1,"acao":"iniciar","parametros":{}}'
```

### Python requests
```python
import requests
resp = requests.post('http://localhost:8080/api/comando', json={
    'conta_id': 1,
    'acao': 'iniciar',
    'parametros': {}
})
print(resp.json())
```

---

## 2. Status do Sistema (/api/status)

### curl
```bash
curl http://localhost:8080/api/status
```

### Python requests
```python
import requests
resp = requests.get('http://localhost:8080/api/status')
print(resp.json())
```

---

## 3. Listar Perfis (/api/perfis)

### curl
```bash
curl http://localhost:8080/api/perfis
```

---

## 4. Buscar Logs (/api/logs)

### curl
```bash
curl 'http://localhost:8080/api/logs?type=seguidores&limit=5'
```

---

## 5. Enviar Feedback (/api/feedback)

### curl
```bash
curl -X POST http://localhost:8080/api/feedback \
  -H 'Content-Type: application/json' \
  -d '{"conta_id":1,"tipo":"acao","mensagem":"Teste feedback","extra":{"foo":"bar"}}'
```

### Python requests
```python
import requests
resp = requests.post('http://localhost:8080/api/feedback', json={
    'conta_id': 1,
    'tipo': 'acao',
    'mensagem': 'Teste feedback',
    'extra': {'foo': 'bar'}
})
print(resp.json())
```

---

**Última atualização:** 2025-07-18 
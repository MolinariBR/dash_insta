# MONITORAMENTO.md — Monitoramento e Healthcheck dash_insta

## 1. Endpoints de Healthcheck
- **Backend:**
  - `GET /api/status` — Verifica status das contas, robôs e métricas
  - `GET /api/logs?type=main&limit=10` — Verifica logs recentes
- **Bot Python:**
  - (Opcional) Expor endpoint `/robo/status` ou enviar heartbeat para backend
- **IA (futura):**
  - `POST /ia/analisar` — Testar resposta do mock/IA

---

## 2. Scripts de Healthcheck (exemplo bash)

### Backend
```bash
curl -sf http://localhost:8080/api/status | grep '"success":true' || echo "[ALERTA] Backend offline"
```

### Bot Python (checa processo)
```bash
pgrep -f 'python3 main.py' > /dev/null || echo "[ALERTA] Bot Python offline"
```

### Logs recentes
```bash
tail -n 10 logs/main.log
```

---

## 3. Monitoramento Externo
- Configure UptimeRobot, Healthchecks.io ou Pingdom para monitorar `/api/status` e `/api/logs`.
- Configure alertas por email/WhatsApp para falhas críticas.

---

## 4. Boas Práticas
- Monitore uso de disco das pastas `logs/` e `data/`.
- Faça backup regular dos dados.
- Revise logs de auditoria e feedback para identificar falhas ou tentativas de ataque.
- (Opcional) Integre logs críticos com Sentry, Grafana, Prometheus ou ELK Stack.

---

**Última atualização:** 2025-07-18 
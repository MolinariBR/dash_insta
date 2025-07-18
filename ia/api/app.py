from fastapi import FastAPI
from pydantic import BaseModel

app = FastAPI()

class AnaliseRequest(BaseModel):
    texto: str
    contexto: dict

@app.post("/ia/analisar")
def analisar(req: AnaliseRequest):
    return {"resposta": "Simulação de resposta IA", "confianca": 0.85} 
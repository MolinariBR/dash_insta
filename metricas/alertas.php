<?php
// Exemplo de dados de campanhas (substitua pela integração real)
$campanhas = [
    ["nome" => "Campanha 1", "cpc" => 2.50, "vendas" => 0, "resultado" => 0.8],
    ["nome" => "Campanha 2", "cpc" => 1.20, "vendas" => 5, "resultado" => 1.2],
];

// Regras de alerta
$LIMITES = [
    "cpc" => 2.00,           // Limite máximo de CPC
    "vendas" => 1,           // Mínimo de vendas
    "resultado" => 1.0       // Resultado mínimo
];

// Função para verificar alertas
function definir_alertas($campanhas, $LIMITES) {
    $alertas = [];
    foreach ($campanhas as $c) {
        if ($c["cpc"] > $LIMITES["cpc"]) {
            $alertas[] = "CPC alto na {$c['nome']}: R$ {$c['cpc']}";
        }
        if ($c["vendas"] < $LIMITES["vendas"]) {
            $alertas[] = "Poucas vendas na {$c['nome']}: {$c['vendas']}";
        }
        if ($c["resultado"] < $LIMITES["resultado"]) {
            $alertas[] = "Resultado baixo na {$c['nome']}: {$c['resultado']}";
        }
    }
    return $alertas;
}

// Função para enviar alerta por email (opcional, sem saída direta)
function enviar_alerta_email($alertas, $destinatario) {
    if (empty($alertas)) return;
    $assunto = 'Alertas de Desempenho das Campanhas';
    $mensagem = implode("\n", $alertas);
    // mail($destinatario, $assunto, $mensagem); // Descomente para enviar
}

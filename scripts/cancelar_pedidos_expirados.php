<?php
// RN02 — Cancelamento automático de pedidos com pagamento pendente (PIX/Boleto)
// após 24h sem confirmação. Reexecutável via agendador (Task Scheduler / cron).
// Uso: php scripts/cancelar_pedidos_expirados.php

require __DIR__ . '/../config/db.php';

try {
    $pdo->beginTransaction();

    // Seleciona os pedidos elegíveis (pendentes, pagamento pendente PIX/Boleto expirado)
    $sel = $pdo->prepare(
        "SELECT p.id
         FROM pedidos p
         JOIN pagamentos pg ON pg.pedido_id = p.id
         WHERE p.status = 'pendente'
           AND pg.status = 'pendente'
           AND pg.metodo IN ('pix', 'boleto')
           AND pg.expira_em IS NOT NULL
           AND pg.expira_em < NOW()"
    );
    $sel->execute();
    $ids = array_map('intval', $sel->fetchAll(PDO::FETCH_COLUMN));

    if (empty($ids)) {
        $pdo->rollBack();
        echo "Nenhum pedido expirado encontrado.\n";
        exit(0);
    }

    $in = implode(',', array_map('intval', $ids));

    // Restaura estoque das variações dos itens
    // (agrega por variação: UPDATE...FROM atualiza cada linha-alvo no máximo uma vez,
    // então sem GROUP BY uma variação com vários itens perderia unidades)
    $rest = $pdo->prepare(
        "UPDATE produto_variacoes v
         SET estoque = v.estoque + agg.qtd
         FROM (
             SELECT pi.variacao_id, SUM(pi.quantidade) AS qtd
             FROM pedido_itens pi
             WHERE pi.pedido_id IN ($in) AND pi.variacao_id IS NOT NULL
             GROUP BY pi.variacao_id
         ) agg
         WHERE agg.variacao_id = v.id"
    );
    $rest->execute();

    // Cancela pedidos e pagamentos
    $updP = $pdo->prepare("UPDATE pedidos SET status = 'cancelado' WHERE id IN ($in)");
    $updP->execute();
    $updPg = $pdo->prepare("UPDATE pagamentos SET status = 'cancelado' WHERE pedido_id IN ($in) AND status = 'pendente'");
    $updPg->execute();

    $pdo->commit();
    echo 'Pedidos cancelados: ' . count($ids) . " (IDs: " . implode(', ', $ids) . ")\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'Erro: ' . $e->getMessage() . "\n");
    exit(1);
}

<?php
// RF06 — Histórico de pedidos do cliente logado.
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../config/db.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE . '/login.php?erro=acesso');
    exit;
}
$uid = (int)$_SESSION['usuario_id'];

$stmt = $pdo->prepare(
    'SELECT p.id, p.status, p.subtotal, p.frete, p.total, p.criado_em, p.recebido_em,
            pg.metodo, pg.status AS pagamento_status, pg.expira_em
     FROM pedidos p
     LEFT JOIN pagamentos pg ON pg.pedido_id = p.id
     WHERE p.usuario_id = :u
     ORDER BY p.criado_em DESC, p.id DESC'
);
$stmt->execute([':u' => $uid]);
$pedidos = $stmt->fetchAll();

$itensStmt = $pdo->prepare(
    'SELECT pi.quantidade, pi.preco_unitario, p.nome, v.cor_tom
     FROM pedido_itens pi
     JOIN produtos p ON p.id = pi.produto_id
     LEFT JOIN produto_variacoes v ON v.id = pi.variacao_id
     WHERE pi.pedido_id = :pid'
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meus Pedidos — Pink Glow</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-[#FFFDFE] text-[#4A1D2F] p-6">
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold text-pink-700">Meus pedidos</h1>
  <p class="text-sm text-pink-900/60 mb-6"><a href="<?= BASE ?>/" class="text-pink-600 underline">← Voltar à vitrine</a> · <a href="<?= BASE ?>/devolucoes.php" class="text-pink-600 underline">Minhas devoluções</a></p>

  <?php if (!empty($_SESSION['flash_ok'])): ?>
    <p class="mb-4 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 text-sm"><?= htmlspecialchars($_SESSION['flash_ok']) ?></p>
    <?php unset($_SESSION['flash_ok']); ?>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_erro'])): ?>
    <p class="mb-4 px-4 py-2 rounded-lg bg-rose-50 text-rose-700 text-sm"><?= htmlspecialchars($_SESSION['flash_erro']) ?></p>
    <?php unset($_SESSION['flash_erro']); ?>
  <?php endif; ?>

  <?php if (empty($pedidos)): ?>
    <p class="text-pink-900/60">Você ainda não fez nenhum pedido.</p>
  <?php endif; ?>

  <?php foreach ($pedidos as $p): ?>
  <div class="bg-white border border-pink-100 rounded-2xl p-4 mb-4">
    <div class="flex justify-between text-sm">
      <a class="font-bold text-pink-700 underline" href="<?= BASE ?>/checkout/?pedido=<?= (int)$p['id'] ?>">Pedido #<?= (int)$p['id'] ?></a>
      <span class="text-pink-900/60"><?= htmlspecialchars($p['criado_em']) ?></span>
    </div>
    <p class="text-sm mt-1">Status: <strong><?= htmlspecialchars($p['status']) ?></strong> · Pagamento: <strong><?= htmlspecialchars($p['metodo'] ?? '—') ?></strong> (<?= htmlspecialchars($p['pagamento_status'] ?? '—') ?>)<?php if ($p['expira_em']): ?> · expira em <?= htmlspecialchars($p['expira_em']) ?><?php endif; ?></p>
    <ul class="mt-2 text-sm divide-y divide-pink-50">
      <?php
        $itensStmt->execute([':pid' => $p['id']]);
        foreach ($itensStmt->fetchAll() as $it): ?>
      <li class="py-1 flex justify-between"><span><?= htmlspecialchars($it['nome']) ?> <?= $it['cor_tom'] ? '· ' . htmlspecialchars($it['cor_tom']) : '' ?> × <?= (int)$it['quantidade'] ?></span><span>R$ <?= number_format((float)$it['preco_unitario'] * (int)$it['quantidade'], 2, ',', '.') ?></span></li>
      <?php endforeach; ?>
    </ul>
    <p class="text-right font-bold text-pink-700 mt-2">Total: R$ <?= number_format((float)$p['total'], 2, ',', '.') ?></p>
    <?php
      $recebidoTs = !empty($p['recebido_em']) ? strtotime($p['recebido_em']) : null;
      $elegivelDevolucao = ($p['status'] === 'entregue' || $recebidoTs)
          && $recebidoTs && (time() - $recebidoTs) <= 7 * 86400;
    ?>
    <?php if ($elegivelDevolucao): ?>
    <form method="post" action="<?= BASE ?>/devolucao.php" class="mt-3 border-t border-pink-50 pt-3 text-sm">
      <input type="hidden" name="pedido_id" value="<?= (int)$p['id'] ?>">
      <label class="block">Motivo da devolução
        <input name="motivo" required class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2" placeholder="Ex.: arrependimento">
      </label>
      <label class="mt-2 flex items-center gap-2">
        <input type="checkbox" name="produto_lacrado" value="1"> Produto lacrado
      </label>
      <button type="submit" class="mt-2 rounded-xl bg-pink-600 text-white px-4 py-2">Solicitar devolução</button>
    </form>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
</body></html>

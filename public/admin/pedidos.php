<?php
// Pedidos — listagem com filtro por status (RF07).
require __DIR__ . '/_layout.php';
require __DIR__ . '/../../config/db.php';

$status = $_GET['status'] ?? '';
$where = [];
$params = [];
if ($status !== '') { $where[] = 'p.status = :status'; $params[':status'] = $status; }
$sql = "
    SELECT p.id, p.status, p.total, p.criado_em, u.nome AS cliente, u.email,
           (SELECT COUNT(*) FROM pedido_itens i WHERE i.pedido_id = p.id) AS itens
    FROM pedidos p JOIN usuarios u ON u.id = p.usuario_id
    " . ($where ? 'WHERE ' . implode(' AND ', $where) : '') . "
    ORDER BY p.id DESC
";
$st = $pdo->prepare($sql);
$st->execute($params);
$pedidos = $st->fetchAll();

$todosStatus = $pdo->query("SELECT DISTINCT status FROM pedidos ORDER BY status")->fetchAll(PDO::FETCH_COLUMN);

admin_ini('Pedidos');
?>
<form method="get" class="mb-4 flex items-center gap-3">
<label class="text-sm font-semibold">Status:
<select name="status" class="ml-2 rounded-xl border border-pink-200 px-3 py-2" onchange="this.form.submit()">
<option value="">Todos</option>
<?php foreach ($todosStatus as $s): ?><option value="<?= htmlspecialchars($s) ?>"<?= $status === $s ? ' selected' : '' ?>><?= htmlspecialchars($s) ?></option><?php endforeach; ?>
</select>
</label>
<noscript><button class="rounded-full bg-primary text-white px-4 py-2 text-sm" type="submit">Filtrar</button></noscript>
</form>
<div class="overflow-x-auto bg-white rounded-2xl border border-pink-100 shadow-sm">
<table class="w-full text-sm">
<thead><tr class="bg-pink-50 text-left text-brand-900"><th class="px-4 py-3">ID</th><th class="px-4 py-3">Cliente</th><th class="px-4 py-3">Itens</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Criado em</th></tr></thead>
<tbody>
<?php foreach ($pedidos as $p): ?>
<tr class="border-t border-pink-50">
<td class="px-4 py-3">#<?= (int)$p['id'] ?></td>
<td class="px-4 py-3"><?= htmlspecialchars($p['cliente']) ?><br><span class="text-xs text-pink-400"><?= htmlspecialchars($p['email']) ?></span></td>
<td class="px-4 py-3"><?= (int)$p['itens'] ?></td>
<td class="px-4 py-3">R$ <?= number_format((float)$p['total'], 2, ',', '.') ?></td>
<td class="px-4 py-3"><span class="rounded-full bg-pink-50 px-3 py-0.5 text-xs font-semibold text-brand-700"><?= htmlspecialchars($p['status']) ?></span></td>
<td class="px-4 py-3"><?= htmlspecialchars($p['criado_em']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (empty($pedidos)): ?><tr><td class="px-4 py-6 text-center text-pink-400" colspan="6">Nenhum pedido encontrado.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
<?php admin_fim();

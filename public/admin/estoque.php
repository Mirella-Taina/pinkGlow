<?php
// Controle de estoque por variação — consulta, ajuste e inclusão (RF07).
require __DIR__ . '/_layout.php';
require __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $acao = $_POST['acao'] ?? '';
        if ($acao === 'atualizar') {
            $st = $pdo->prepare('UPDATE produto_variacoes SET estoque = :est WHERE id = :id');
            $st->execute([':est' => max(0, (int)$_POST['estoque']), ':id' => (int)$_POST['id']]);
            $_SESSION['flash_ok'] = 'Estoque atualizado.';
        } elseif ($acao === 'adicionar') {
            $st = $pdo->prepare('INSERT INTO produto_variacoes (produto_id, cor_tom, sku, estoque, preco_extra) VALUES (:pid, :tom, :sku, :est, :extra)');
            $st->execute([
                ':pid' => (int)$_POST['produto_id'],
                ':tom' => trim($_POST['cor_tom']),
                ':sku' => trim($_POST['sku'] ?? '') ?: null,
                ':est' => max(0, (int)$_POST['estoque']),
                ':extra' => (float)str_replace(',', '.', $_POST['preco_extra'] ?? '0'),
            ]);
            $_SESSION['flash_ok'] = 'Variação adicionada ao estoque.';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_erro'] = 'Erro: ' . $e->getMessage();
    }
    header('Location: ' . BASE . '/admin/estoque.php');
    exit;
}

$variacoes = $pdo->query("
    SELECT v.id, v.cor_tom, v.sku, v.estoque, v.preco_extra, p.nome AS produto
    FROM produto_variacoes v JOIN produtos p ON p.id = v.produto_id
    ORDER BY p.nome, v.id
")->fetchAll();
$produtos = $pdo->query('SELECT id, nome FROM produtos ORDER BY nome')->fetchAll();

admin_ini('Estoque');
?>
<form method="post" class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 max-w-2xl space-y-3 mb-6">
  <input type="hidden" name="acao" value="adicionar"/>
  <h3 class="font-serif text-lg text-brand-900">Adicionar variação</h3>
  <div class="grid grid-cols-2 gap-4">
    <label class="text-sm font-semibold">Produto
      <select name="produto_id" required class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2">
        <?php foreach ($produtos as $p): ?><option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option><?php endforeach; ?>
      </select></label>
    <label class="text-sm font-semibold">Tom/Cor
      <input name="cor_tom" required class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2" placeholder="Ex.: Nude 02"></label>
    <label class="text-sm font-semibold">SKU
      <input name="sku" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
    <label class="text-sm font-semibold">Estoque
      <input name="estoque" type="number" min="0" value="0" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
    <label class="text-sm font-semibold">Preço extra (R$)
      <input name="preco_extra" inputmode="decimal" value="0" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
  </div>
  <button class="rounded-full bg-primary text-white px-6 py-2 font-semibold hover:bg-primary-container" type="submit">Adicionar</button>
</form>
<div class="overflow-x-auto bg-white rounded-2xl border border-pink-100 shadow-sm">
<table class="w-full text-sm">
<thead><tr class="bg-pink-50 text-left text-brand-900"><th class="px-4 py-3">ID</th><th class="px-4 py-3">Produto</th><th class="px-4 py-3">Tom/Cor</th><th class="px-4 py-3">SKU</th><th class="px-4 py-3">Preço extra</th><th class="px-4 py-3">Estoque</th><th class="px-4 py-3">Ajustar</th></tr></thead>
<tbody>
<?php foreach ($variacoes as $v): ?>
<tr class="border-t border-pink-50">
<td class="px-4 py-3"><?= (int)$v['id'] ?></td>
<td class="px-4 py-3 font-semibold"><?= htmlspecialchars($v['produto']) ?></td>
<td class="px-4 py-3"><?= htmlspecialchars($v['cor_tom']) ?></td>
<td class="px-4 py-3"><?= htmlspecialchars($v['sku'] ?? '—') ?></td>
<td class="px-4 py-3">R$ <?= number_format((float)$v['preco_extra'], 2, ',', '.') ?></td>
<td class="px-4 py-3"><?= (int)$v['estoque'] ?></td>
<td class="px-4 py-3">
<form method="post" class="flex items-center gap-2">
<input type="hidden" name="acao" value="atualizar"/>
<input type="hidden" name="id" value="<?= (int)$v['id'] ?>"/>
<input type="number" min="0" name="estoque" value="<?= (int)$v['estoque'] ?>" class="w-24 rounded-xl border border-pink-200 px-2 py-1">
<button class="rounded-full bg-primary text-white px-3 py-1 text-xs font-semibold hover:bg-primary-container" type="submit">Salvar</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php admin_fim();

<?php
// Gestão de produtos — listar, editar, excluir (RF07).
require __DIR__ . '/_layout.php';
require __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir') {
    $id = (int)($_POST['id'] ?? 0);
    try {
        // Bloqueia exclusão se houver itens de pedido vinculados (mantém histórico de pedidos).
        $st = $pdo->prepare('SELECT COUNT(*) FROM pedido_itens WHERE produto_id = :id');
        $st->execute([':id' => $id]);
        $usados = (int)$st->fetchColumn();
        if ($usados > 0) {
            $_SESSION['flash_erro'] = "Não é possível excluir: produto possui $usados item(ns) em pedidos. Desative-o em editar.";
        } else {
            $pdo->prepare('DELETE FROM produto_variacoes WHERE produto_id = :id')->execute([':id' => $id]);
            $pdo->prepare('DELETE FROM produtos WHERE id = :id')->execute([':id' => $id]);
            $_SESSION['flash_ok'] = 'Produto excluído com sucesso.';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_erro'] = 'Erro ao excluir produto: ' . $e->getMessage();
    }
    header('Location: ' . BASE . '/admin/produtos.php');
    exit;
}

$produtos = $pdo->query("
    SELECT p.id, p.nome, p.marca, p.preco, p.ativo, c.nome AS categoria,
           (SELECT COALESCE(SUM(v.estoque),0) FROM produto_variacoes v WHERE v.produto_id = p.id) AS estoque
    FROM produtos p JOIN categorias c ON c.id = p.categoria_id
    ORDER BY p.id DESC
")->fetchAll();

admin_ini('Produtos');
?>
<div class="mb-4"><a class="inline-flex items-center rounded-full bg-primary text-white px-5 py-2 text-sm font-semibold hover:bg-primary-container" href="<?= BASE ?>/admin/produtos/novo.php">+ Novo produto</a></div>
<div class="overflow-x-auto bg-white rounded-2xl border border-pink-100 shadow-sm">
<table class="w-full text-sm">
<thead><tr class="bg-pink-50 text-left text-brand-900">
<th class="px-4 py-3">ID</th><th class="px-4 py-3">Nome</th><th class="px-4 py-3">Marca</th><th class="px-4 py-3">Categoria</th><th class="px-4 py-3">Preço</th><th class="px-4 py-3">Estoque</th><th class="px-4 py-3">Ativo</th><th class="px-4 py-3">Ações</th>
</tr></thead>
<tbody>
<?php foreach ($produtos as $p): ?>
<tr class="border-t border-pink-50">
<td class="px-4 py-3"><?= (int)$p['id'] ?></td>
<td class="px-4 py-3 font-semibold"><?= htmlspecialchars($p['nome']) ?></td>
<td class="px-4 py-3"><?= htmlspecialchars($p['marca'] ?? '—') ?></td>
<td class="px-4 py-3"><?= htmlspecialchars($p['categoria']) ?></td>
<td class="px-4 py-3">R$ <?= number_format((float)$p['preco'], 2, ',', '.') ?></td>
<td class="px-4 py-3"><?= (int)$p['estoque'] ?></td>
<td class="px-4 py-3"><?= $p['ativo'] ? 'Sim' : 'Não' ?></td>
<td class="px-4 py-3 flex gap-2">
  <a class="rounded-full border border-pink-200 px-3 py-1 text-xs font-semibold text-brand-700 hover:bg-pink-50" href="<?= BASE ?>/admin/produtos/editar.php?id=<?= (int)$p['id'] ?>">Editar</a>
  <form method="post" onsubmit="return confirm('Excluir o produto <?= htmlspecialchars(addslashes($p['nome'])) ?>?');">
    <input type="hidden" name="acao" value="excluir"/>
    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>"/>
    <button class="rounded-full bg-rose-50 text-rose-700 px-3 py-1 text-xs font-semibold hover:bg-rose-100" type="submit">Excluir</button>
  </form>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($produtos)): ?><tr><td class="px-4 py-6 text-center text-pink-400" colspan="8">Nenhum produto cadastrado.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
<?php admin_fim();

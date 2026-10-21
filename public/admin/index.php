<?php
// Dashboard administrativo — contagens rápidas (RF07).
require __DIR__ . '/_layout.php';
require __DIR__ . '/../../config/db.php';

$totalProdutos  = (int)$pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$totalPedidos   = (int)$pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$totalClientes  = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'cliente'")->fetchColumn();
$totalCategorias = (int)$pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
$totalCupons    = (int)$pdo->query("SELECT COUNT(*) FROM cupons")->fetchColumn();
$pedidosPendentes = (int)$pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'pendente'")->fetchColumn();

admin_ini('Dashboard');
?>
<div class="grid grid-cols-2 md:grid-cols-3 gap-4">
  <?php foreach ([
    ['Produtos', $totalProdutos], ['Pedidos', $totalPedidos], ['Clientes', $totalClientes],
    ['Categorias', $totalCategorias], ['Cupons', $totalCupons], ['Pedidos pendentes', $pedidosPendentes],
  ] as [$rotulo, $valor]): ?>
  <div class="bg-white rounded-2xl border border-pink-100 p-5 shadow-sm">
    <p class="text-xs font-semibold uppercase tracking-widest text-pink-400"><?= $rotulo ?></p>
    <p class="font-serif text-3xl text-brand-900 mt-1"><?= $valor ?></p>
  </div>
  <?php endforeach; ?>
</div>
<p class="mt-6 text-sm text-pink-700">Use o menu acima para gerenciar produtos, categorias, cupons, estoque e pedidos.</p>
<?php admin_fim();

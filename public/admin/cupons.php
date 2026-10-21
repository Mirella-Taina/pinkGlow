<?php
// CRUD de cupons — código, valor, tipo, validade (RF07).
require __DIR__ . '/_layout.php';
require __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    try {
        if ($acao === 'criar' || $acao === 'atualizar') {
            $dados = [
                ':codigo' => strtoupper(trim($_POST['codigo'])),
                ':tipo' => $_POST['tipo'] === 'percentual' ? 'percentual' : 'fixo',
                ':valor' => (float)str_replace(',', '.', $_POST['valor']),
                ':validade' => $_POST['validade'] !== '' ? $_POST['validade'] : null,
                ':ativo' => isset($_POST['ativo']) ? 'true' : 'false',
            ];
            if ($acao === 'criar') {
                $st = $pdo->prepare('INSERT INTO cupons (codigo, tipo, valor, validade, ativo) VALUES (:codigo, :tipo, :valor, :validade, :ativo)');
                $st->execute($dados);
                $_SESSION['flash_ok'] = 'Cupom criado.';
            } else {
                $dados[':id'] = (int)$_POST['id'];
                $st = $pdo->prepare('UPDATE cupons SET codigo=:codigo, tipo=:tipo, valor=:valor, validade=:validade, ativo=:ativo WHERE id=:id');
                $st->execute($dados);
                $_SESSION['flash_ok'] = 'Cupom atualizado.';
            }
        } elseif ($acao === 'excluir') {
            $st = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE cupom_id = :id');
            $st->execute([':id' => (int)$_POST['id']]);
            $emUso = (int)$st->fetchColumn();
            if ($emUso > 0) {
                $_SESSION['flash_erro'] = "Cupom utilizado em $emUso pedido(s) — não pode ser excluído. Desative-o.";
            } else {
                $pdo->prepare('DELETE FROM cupons WHERE id = :id')->execute([':id' => (int)$_POST['id']]);
                $_SESSION['flash_ok'] = 'Cupom excluído.';
            }
        }
    } catch (PDOException $e) {
        $_SESSION['flash_erro'] = 'Erro: ' . $e->getMessage();
    }
    header('Location: ' . BASE . '/admin/cupons.php');
    exit;
}

$editar = null;
if (isset($_GET['editar'])) {
    $st = $pdo->prepare('SELECT * FROM cupons WHERE id = :id');
    $st->execute([':id' => (int)$_GET['editar']]);
    $editar = $st->fetch();
}
$cupons = $pdo->query('SELECT * FROM cupons ORDER BY id DESC')->fetchAll();

admin_ini('Cupons');
?>
<form method="post" class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 max-w-xl space-y-3 mb-6">
  <input type="hidden" name="acao" value="<?= $editar ? 'atualizar' : 'criar' ?>"/>
  <input type="hidden" name="id" value="<?= (int)($editar['id'] ?? 0) ?>"/>
  <h3 class="font-serif text-lg text-brand-900"><?= $editar ? 'Editar cupom' : 'Novo cupom' ?></h3>
  <div class="grid grid-cols-2 gap-4">
    <label class="text-sm font-semibold">Código
      <input name="codigo" required value="<?= htmlspecialchars($editar['codigo'] ?? '') ?>" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2 uppercase"></label>
    <label class="text-sm font-semibold">Tipo
      <select name="tipo" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2">
        <option value="percentual"<?= ($editar['tipo'] ?? '') === 'percentual' ? ' selected' : '' ?>>Percentual (%)</option>
        <option value="fixo"<?= ($editar['tipo'] ?? '') === 'fixo' ? ' selected' : '' ?>>Valor fixo (R$)</option>
      </select></label>
    <label class="text-sm font-semibold">Valor
      <input name="valor" required inputmode="decimal" value="<?= htmlspecialchars((string)($editar['valor'] ?? '')) ?>" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
    <label class="text-sm font-semibold">Validade
      <input name="validade" type="date" value="<?= htmlspecialchars($editar['validade'] ?? '') ?>" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
  </div>
  <label class="inline-flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="ativo"<?= ($editar['ativo'] ?? true) ? ' checked' : '' ?>> Ativo</label><br>
  <button class="rounded-full bg-primary text-white px-6 py-2 font-semibold hover:bg-primary-container" type="submit"><?= $editar ? 'Salvar' : 'Criar' ?></button>
  <?php if ($editar): ?><a class="ml-2 text-sm text-pink-500" href="<?= BASE ?>/admin/cupons.php">Cancelar</a><?php endif; ?>
</form>
<div class="overflow-x-auto bg-white rounded-2xl border border-pink-100 shadow-sm">
<table class="w-full text-sm">
<thead><tr class="bg-pink-50 text-left text-brand-900"><th class="px-4 py-3">ID</th><th class="px-4 py-3">Código</th><th class="px-4 py-3">Tipo</th><th class="px-4 py-3">Valor</th><th class="px-4 py-3">Validade</th><th class="px-4 py-3">Ativo</th><th class="px-4 py-3">Ações</th></tr></thead>
<tbody>
<?php foreach ($cupons as $c): ?>
<tr class="border-t border-pink-50">
<td class="px-4 py-3"><?= (int)$c['id'] ?></td>
<td class="px-4 py-3 font-semibold"><?= htmlspecialchars($c['codigo']) ?></td>
<td class="px-4 py-3"><?= htmlspecialchars($c['tipo']) ?></td>
<td class="px-4 py-3"><?= htmlspecialchars((string)$c['valor']) ?></td>
<td class="px-4 py-3"><?= htmlspecialchars($c['validade'] ?? '—') ?></td>
<td class="px-4 py-3"><?= $c['ativo'] ? 'Sim' : 'Não' ?></td>
<td class="px-4 py-3 flex gap-2">
<a class="rounded-full border border-pink-200 px-3 py-1 text-xs font-semibold text-brand-700 hover:bg-pink-50" href="?editar=<?= (int)$c['id'] ?>">Editar</a>
<form method="post" onsubmit="return confirm('Excluir o cupom <?= htmlspecialchars(addslashes($c['codigo'])) ?>?');">
<input type="hidden" name="acao" value="excluir"/><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"/>
<button class="rounded-full bg-rose-50 text-rose-700 px-3 py-1 text-xs font-semibold hover:bg-rose-100" type="submit">Excluir</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php admin_fim();

<?php
// CRUD de categorias — RF07.
require __DIR__ . '/_layout.php';
require __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    try {
        if ($acao === 'criar') {
            $st = $pdo->prepare('INSERT INTO categorias (nome, descricao) VALUES (:nome, :desc)');
            $st->execute([':nome' => trim($_POST['nome']), ':desc' => trim($_POST['descricao'] ?? '')]);
            $_SESSION['flash_ok'] = 'Categoria criada.';
        } elseif ($acao === 'atualizar') {
            $st = $pdo->prepare('UPDATE categorias SET nome=:nome, descricao=:desc WHERE id=:id');
            $st->execute([':nome' => trim($_POST['nome']), ':desc' => trim($_POST['descricao'] ?? ''), ':id' => (int)$_POST['id']]);
            $_SESSION['flash_ok'] = 'Categoria atualizada.';
        } elseif ($acao === 'excluir') {
            $st = $pdo->prepare('SELECT COUNT(*) FROM produtos WHERE categoria_id = :id');
            $st->execute([':id' => (int)$_POST['id']]);
            $emUso = (int)$st->fetchColumn();
            if ($emUso > 0) {
                $_SESSION['flash_erro'] = "Categoria em uso por $emUso produto(s) — não pode ser excluída.";
            } else {
                $pdo->prepare('DELETE FROM categorias WHERE id = :id')->execute([':id' => (int)$_POST['id']]);
                $_SESSION['flash_ok'] = 'Categoria excluída.';
            }
        }
    } catch (PDOException $e) {
        $_SESSION['flash_erro'] = 'Erro: ' . $e->getMessage();
    }
    header('Location: ' . BASE . '/admin/categorias.php');
    exit;
}

$editar = null;
if (isset($_GET['editar'])) {
    $st = $pdo->prepare('SELECT * FROM categorias WHERE id = :id');
    $st->execute([':id' => (int)$_GET['editar']]);
    $editar = $st->fetch();
}
$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();

admin_ini('Categorias');
?>
<form method="post" class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 max-w-xl space-y-3 mb-6">
  <input type="hidden" name="acao" value="<?= $editar ? 'atualizar' : 'criar' ?>"/>
  <input type="hidden" name="id" value="<?= (int)($editar['id'] ?? 0) ?>"/>
  <h3 class="font-serif text-lg text-brand-900"><?= $editar ? 'Editar categoria' : 'Nova categoria' ?></h3>
  <label class="block text-sm font-semibold">Nome
    <input name="nome" required value="<?= htmlspecialchars($editar['nome'] ?? '') ?>" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
  <label class="block text-sm font-semibold">Descrição
    <textarea name="descricao" rows="2" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"><?= htmlspecialchars($editar['descricao'] ?? '') ?></textarea></label>
  <button class="rounded-full bg-primary text-white px-6 py-2 font-semibold hover:bg-primary-container" type="submit"><?= $editar ? 'Salvar' : 'Criar' ?></button>
  <?php if ($editar): ?><a class="ml-2 text-sm text-pink-500" href="<?= BASE ?>/admin/categorias.php">Cancelar</a><?php endif; ?>
</form>
<div class="overflow-x-auto bg-white rounded-2xl border border-pink-100 shadow-sm">
<table class="w-full text-sm">
<thead><tr class="bg-pink-50 text-left text-brand-900"><th class="px-4 py-3">ID</th><th class="px-4 py-3">Nome</th><th class="px-4 py-3">Descrição</th><th class="px-4 py-3">Ações</th></tr></thead>
<tbody>
<?php foreach ($categorias as $c): ?>
<tr class="border-t border-pink-50">
<td class="px-4 py-3"><?= (int)$c['id'] ?></td>
<td class="px-4 py-3 font-semibold"><?= htmlspecialchars($c['nome']) ?></td>
<td class="px-4 py-3"><?= htmlspecialchars($c['descricao'] ?? '') ?></td>
<td class="px-4 py-3 flex gap-2">
<a class="rounded-full border border-pink-200 px-3 py-1 text-xs font-semibold text-brand-700 hover:bg-pink-50" href="?editar=<?= (int)$c['id'] ?>">Editar</a>
<form method="post" onsubmit="return confirm('Excluir a categoria <?= htmlspecialchars(addslashes($c['nome'])) ?>?');">
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

<?php
// Edição de produto + variações — RF07.
require __DIR__ . '/../_layout.php';
require __DIR__ . '/../../../config/db.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$st = $pdo->prepare('SELECT * FROM produtos WHERE id = :id');
$st->execute([':id' => $id]);
$produto = $st->fetch();
if (!$produto) { http_response_code(404); echo 'Produto não encontrado.'; exit; }

$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        $st = $pdo->prepare('UPDATE produtos SET categoria_id=:cat, nome=:nome, marca=:marca, descricao=:desc, preco=:preco, preco_antigo=:pa, imagem_url=:img, ativo=:ativo WHERE id=:id');
        $st->execute([
            ':cat' => (int)$_POST['categoria_id'],
            ':nome' => trim($_POST['nome']),
            ':marca' => trim($_POST['marca']),
            ':desc' => trim($_POST['descricao'] ?? ''),
            ':preco' => (float)str_replace(',', '.', $_POST['preco']),
            ':pa' => $_POST['preco_antigo'] !== '' ? (float)str_replace(',', '.', $_POST['preco_antigo']) : null,
            ':img' => trim($_POST['imagem_url'] ?? ''),
            ':ativo' => isset($_POST['ativo']) ? 'true' : 'false',
            ':id' => $id,
        ]);
        // Atualiza variações existentes
        $upd = $pdo->prepare('UPDATE produto_variacoes SET cor_tom=:tom, estoque=:est, preco_extra=:extra WHERE id=:vid AND produto_id=:pid');
        foreach ($_POST['var_id'] ?? [] as $i => $vid) {
            $upd->execute([
                ':tom' => trim((string)($_POST['var_tom'][$i] ?? '')),
                ':est' => max(0, (int)($_POST['var_estoque'][$i] ?? 0)),
                ':extra' => (float)str_replace(',', '.', $_POST['var_preco_extra'][$i] ?? '0'),
                ':vid' => (int)$vid,
                ':pid' => $id,
            ]);
        }
        // Novas variações
        $ins = $pdo->prepare('INSERT INTO produto_variacoes (produto_id, cor_tom, estoque, preco_extra) VALUES (:pid, :tom, :est, :extra)');
        foreach ($_POST['tom'] ?? [] as $i => $tom) {
            $tom = trim((string)$tom);
            if ($tom === '') continue;
            $ins->execute([':pid' => $id, ':tom' => $tom, ':est' => max(0, (int)($_POST['estoque'][$i] ?? 0)), ':extra' => (float)str_replace(',', '.', $_POST['preco_extra'][$i] ?? '0')]);
        }
        $pdo->commit();
        $_SESSION['flash_ok'] = 'Produto atualizado.';
        header('Location: ' . BASE . '/admin/produtos.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['flash_erro'] = 'Erro ao atualizar: ' . $e->getMessage();
    }
}

$vst = $pdo->prepare('SELECT * FROM produto_variacoes WHERE produto_id = :id ORDER BY id');
$vst->execute([':id' => $id]);
$variacoes = $vst->fetchAll();

admin_ini('Editar Produto');
?>
<form method="post" class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 max-w-3xl space-y-4">
  <input type="hidden" name="id" value="<?= $id ?>"/>
  <label class="block text-sm font-semibold">Nome
    <input name="nome" required value="<?= htmlspecialchars($produto['nome']) ?>" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
  <label class="block text-sm font-semibold">Descrição
    <textarea name="descricao" rows="3" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea></label>
  <div class="grid grid-cols-2 gap-4">
    <label class="block text-sm font-semibold">Categoria
      <select name="categoria_id" required class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2">
        <?php foreach ($categorias as $c): ?><option value="<?= (int)$c['id'] ?>"<?= (int)$c['id'] === (int)$produto['categoria_id'] ? ' selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option><?php endforeach; ?>
      </select></label>
    <label class="block text-sm font-semibold">Marca
      <input name="marca" value="<?= htmlspecialchars($produto['marca'] ?? '') ?>" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
  </div>
  <div class="grid grid-cols-3 gap-4">
    <label class="block text-sm font-semibold">Preço (R$)
      <input name="preco" required inputmode="decimal" value="<?= htmlspecialchars((string)$produto['preco']) ?>" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
    <label class="block text-sm font-semibold">Preço antigo (R$)
      <input name="preco_antigo" inputmode="decimal" value="<?= htmlspecialchars((string)($produto['preco_antigo'] ?? '')) ?>" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
    <label class="block text-sm font-semibold">Imagem (URL)
      <input name="imagem_url" value="<?= htmlspecialchars($produto['imagem_url'] ?? '') ?>" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
  </div>
  <label class="inline-flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="ativo"<?= $produto['ativo'] ? ' checked' : '' ?>> Ativo</label>

  <h3 class="font-serif text-lg text-brand-900 pt-2">Variações existentes</h3>
  <?php foreach ($variacoes as $v): ?>
  <div class="grid grid-cols-[80px_1fr_1fr_1fr] gap-4 items-center">
    <input type="hidden" name="var_id[]" value="<?= (int)$v['id'] ?>"/>
    <span class="text-xs text-pink-400">#<?= (int)$v['id'] ?></span>
    <input name="var_tom[]" value="<?= htmlspecialchars($v['cor_tom']) ?>" class="rounded-xl border border-pink-200 px-3 py-2">
    <input name="var_estoque[]" type="number" min="0" value="<?= (int)$v['estoque'] ?>" class="rounded-xl border border-pink-200 px-3 py-2">
    <input name="var_preco_extra[]" inputmode="decimal" value="<?= htmlspecialchars((string)$v['preco_extra']) ?>" class="rounded-xl border border-pink-200 px-3 py-2">
  </div>
  <?php endforeach; ?>

  <h3 class="font-serif text-lg text-brand-900 pt-2">Adicionar variações</h3>
  <?php for ($i = 0; $i < 2; $i++): ?>
  <div class="grid grid-cols-3 gap-4">
    <input name="tom[]" placeholder="Tom" class="rounded-xl border border-pink-200 px-3 py-2">
    <input name="estoque[]" type="number" min="0" value="0" placeholder="Estoque" class="rounded-xl border border-pink-200 px-3 py-2">
    <input name="preco_extra[]" inputmode="decimal" value="0" placeholder="Preço extra" class="rounded-xl border border-pink-200 px-3 py-2">
  </div>
  <?php endfor; ?>
  <button class="rounded-full bg-primary text-white px-6 py-2 font-semibold hover:bg-primary-container" type="submit">Salvar alterações</button>
</form>
<?php admin_fim();

<?php
// Cadastro de produto com variações simples (tom + estoque + preço extra) — RF07.
require __DIR__ . '/../_layout.php';
require __DIR__ . '/../../../config/db.php';

$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        $st = $pdo->prepare('INSERT INTO produtos (categoria_id, nome, marca, descricao, preco, preco_antigo, imagem_url, ativo) VALUES (:cat, :nome, :marca, :desc, :preco, :preco_antigo, :img, :ativo) RETURNING id');
        $st->execute([
            ':cat' => (int)$_POST['categoria_id'],
            ':nome' => trim($_POST['nome']),
            ':marca' => trim($_POST['marca']),
            ':desc' => trim($_POST['descricao'] ?? ''),
            ':preco' => (float)str_replace(',', '.', $_POST['preco']),
            ':preco_antigo' => $_POST['preco_antigo'] !== '' ? (float)str_replace(',', '.', $_POST['preco_antigo']) : null,
            ':img' => trim($_POST['imagem_url'] ?? ''),
            ':ativo' => isset($_POST['ativo']) ? 'true' : 'false',
        ]);
        $produtoId = (int)$st->fetchColumn();
        $ins = $pdo->prepare('INSERT INTO produto_variacoes (produto_id, cor_tom, estoque, preco_extra) VALUES (:pid, :tom, :est, :extra)');
        foreach ($_POST['tom'] ?? [] as $i => $tom) {
            $tom = trim((string)$tom);
            if ($tom === '') continue;
            $ins->execute([
                ':pid' => $produtoId,
                ':tom' => $tom,
                ':est' => max(0, (int)($_POST['estoque'][$i] ?? 0)),
                ':extra' => (float)str_replace(',', '.', $_POST['preco_extra'][$i] ?? '0'),
            ]);
        }
        $pdo->commit();
        $_SESSION['flash_ok'] = 'Produto criado com sucesso.';
        header('Location: ' . BASE . '/admin/produtos.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['flash_erro'] = 'Erro ao criar produto: ' . $e->getMessage();
    }
}

admin_ini('Novo Produto');
?>
<form method="post" class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 max-w-3xl space-y-4">
  <label class="block text-sm font-semibold">Nome
    <input name="nome" required class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
  <label class="block text-sm font-semibold">Descrição
    <textarea name="descricao" rows="3" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></textarea></label>
  <div class="grid grid-cols-2 gap-4">
    <label class="block text-sm font-semibold">Categoria
      <select name="categoria_id" required class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2">
        <?php foreach ($categorias as $c): ?><option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option><?php endforeach; ?>
      </select></label>
    <label class="block text-sm font-semibold">Marca
      <input name="marca" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2" placeholder="Ex.: Pink Glow"></label>
  </div>
  <div class="grid grid-cols-3 gap-4">
    <label class="block text-sm font-semibold">Preço (R$)
      <input name="preco" required inputmode="decimal" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2" placeholder="39,90"></label>
    <label class="block text-sm font-semibold">Preço antigo (R$)
      <input name="preco_antigo" inputmode="decimal" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2"></label>
    <label class="block text-sm font-semibold">Imagem (URL)
      <input name="imagem_url" class="mt-1 w-full rounded-xl border border-pink-200 px-3 py-2" placeholder="/assets/img/p1.svg"></label>
  </div>
  <label class="inline-flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="ativo" checked> Ativo</label>

  <h3 class="font-serif text-lg text-brand-900 pt-2">Variações (tom / estoque / preço extra)</h3>
  <?php for ($i = 0; $i < 4; $i++): ?>
  <div class="grid grid-cols-3 gap-4">
    <input name="tom[]" placeholder="Tom (ex.: Nude 01)" class="rounded-xl border border-pink-200 px-3 py-2">
    <input name="estoque[]" type="number" min="0" value="0" placeholder="Estoque" class="rounded-xl border border-pink-200 px-3 py-2">
    <input name="preco_extra[]" inputmode="decimal" value="0" placeholder="Preço extra (R$)" class="rounded-xl border border-pink-200 px-3 py-2">
  </div>
  <?php endfor; ?>
  <button class="rounded-full bg-primary text-white px-6 py-2 font-semibold hover:bg-primary-container" type="submit">Salvar</button>
</form>
<?php admin_fim();

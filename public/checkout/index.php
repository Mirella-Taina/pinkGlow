<?php
// RF05 — Checkout: exige login, resume pedido, registra pedido + pagamento (gateway simulado).
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../../config/db.php';

// Exige login — caso contrário redireciona
if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE . '/login.php?erro=acesso');
    exit;
}
$usuario_id = (int)$_SESSION['usuario_id'];

// Página de confirmação (PRG): ?pedido=ID
if (isset($_GET['pedido'])) {
    $pid = (int)$_GET['pedido'];
    $stmt = $pdo->prepare(
        'SELECT p.*, pg.metodo, pg.status AS pagamento_status, pg.expira_em
         FROM pedidos p JOIN pagamentos pg ON pg.pedido_id = p.id
         WHERE p.id = :id
           AND (p.usuario_id = :uid
                OR EXISTS (SELECT 1 FROM usuarios u WHERE u.id = :uid2 AND u.tipo = \'admin\'))
         LIMIT 1'
    );
    $stmt->execute([':id' => $pid, ':uid' => $usuario_id, ':uid2' => $usuario_id]);
    $pedido = $stmt->fetch();
    if (!$pedido) { http_response_code(403); exit('Pedido não encontrado.'); }
    $itensStmt = $pdo->prepare(
        'SELECT pi.*, p.nome, v.cor_tom FROM pedido_itens pi
         JOIN produtos p ON p.id = pi.produto_id
         LEFT JOIN produto_variacoes v ON v.id = pi.variacao_id
         WHERE pi.pedido_id = :id'
    );
    $itensStmt->execute([':id' => $pid]);
    $itens = $itensStmt->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #<?= $pid ?> — Pink Glow</title>
    <script src="https://cdn.tailwindcss.com"></script></head>
    <body class="bg-[#FFFDFE] text-[#4A1D2F] p-6">
    <div class="max-w-2xl mx-auto bg-white rounded-3xl border border-pink-100 shadow-sm p-8">
      <h1 class="text-2xl font-bold text-pink-700">Pedido #<?= $pid ?> confirmado!</h1>
      <p class="text-sm text-pink-900/60 mt-1">Status: <strong><?= htmlspecialchars($pedido['status']) ?></strong> · Pagamento: <strong><?= htmlspecialchars($pedido['metodo']) ?></strong> (<?= htmlspecialchars($pedido['pagamento_status']) ?>)</p>
      <?php if ($pedido['expira_em']): ?>
      <p class="text-sm text-amber-700 bg-amber-50 rounded-lg px-3 py-2 mt-3">Vencimento do pagamento (RN02): <?= htmlspecialchars($pedido['expira_em']) ?></p>
      <?php endif; ?>
      <ul class="mt-4 divide-y divide-pink-50 text-sm">
        <?php foreach ($itens as $it): ?>
        <li class="py-2 flex justify-between"><span><?= htmlspecialchars($it['nome']) ?> <?= $it['cor_tom'] ? '· ' . htmlspecialchars($it['cor_tom']) : '' ?> × <?= (int)$it['quantidade'] ?></span><span>R$ <?= number_format((float)$it['preco_unitario'] * (int)$it['quantidade'], 2, ',', '.') ?></span></li>
        <?php endforeach; ?>
      </ul>
      <p class="mt-3 text-right text-xl font-bold">Total: R$ <?= number_format((float)$pedido['total'], 2, ',', '.') ?></p>

      <?php if ($pedido['metodo'] === 'pix'): ?>
      <div class="mt-6 border border-pink-200 rounded-2xl p-5 text-center">
        <h2 class="font-semibold text-pink-800">PIX — pague com o app do seu banco</h2>
        <div class="mx-auto my-4 w-40 h-40 bg-pink-50 border-2 border-dashed border-pink-300 rounded-xl flex items-center justify-center text-pink-400 text-xs">[ QR CODE PIX — placeholder ]</div>
        <p class="text-xs break-all font-mono bg-pink-50 rounded-lg p-2">00020126580014BR.GOV.BCB.PIX0136pinkglow-pix@placeholder520400005303986540<?= number_format((float)$pedido['total'], 2, '.', '') ?>5802BR</p>
        <p class="text-xs text-pink-900/60 mt-1">Código copia-e-cola (simulado)</p>
      </div>
      <?php elseif ($pedido['metodo'] === 'boleto'): ?>
      <div class="mt-6 border border-pink-200 rounded-2xl p-5">
        <h2 class="font-semibold text-pink-800">Boleto Bancário</h2>
        <p class="mt-3 font-mono text-sm bg-pink-50 rounded-lg p-3">23793.38128 60000.000003 00000.000408 1 843400001<?= str_pad((string)$pid, 4, '0', STR_PAD_LEFT) ?></p>
        <p class="text-xs text-pink-900/60 mt-1">Linha digitável (simulada)</p>
      </div>
      <?php else: ?>
      <div class="mt-6 border border-pink-200 rounded-2xl p-5">
        <h2 class="font-semibold text-pink-800">Cartão de Crédito</h2>
        <p class="text-sm text-pink-900/70 mt-1">Pagamento registrado para processamento no gateway (simulado). Você receberá a confirmação por e-mail.</p>
      </div>
      <?php endif; ?>

      <div class="mt-6 flex gap-3 text-sm">
        <a class="text-pink-600 underline" href="<?= BASE ?>/pedidos.php">Meus pedidos</a>
        <a class="text-pink-600 underline" href="<?= BASE ?>/">Voltar à vitrine</a>
      </div>
    </div>
    </body></html>
    <?php
    exit;
}

// Dados do carrinho (server-side)
$carrinho = $_SESSION['carrinho'] ?? [];
if (empty($carrinho)) {
    $_SESSION['flash_erro'] = 'Sua sacola está vazia.';
    header('Location: ' . BASE . '/carrinho/');
    exit;
}
$ids = implode(',', array_map('intval', array_keys($carrinho)));
$rows = $pdo->query(
    "SELECT v.id AS variacao_id, v.cor_tom, v.estoque, v.preco_extra, p.id AS produto_id, p.nome, p.preco
     FROM produto_variacoes v JOIN produtos p ON p.id = v.produto_id WHERE v.id IN ($ids)"
)->fetchAll();
$itens = []; $subtotal = 0.0;
foreach ($rows as $r) {
    $vid = (int)$r['variacao_id'];
    if (!isset($carrinho[$vid])) continue;
    $qtd = (int)$carrinho[$vid]['quantidade'];
    if ($qtd > (int)$r['estoque']) {
        $_SESSION['flash_erro'] = "Estoque insuficiente para '{$r['nome']}'. Revise a sacola.";
        header('Location: ' . BASE . '/carrinho/');
        exit;
    }
    $unit = (float)$r['preco'] + (float)$r['preco_extra'];
    $subtotal += $unit * $qtd;
    $itens[] = ['variacao_id' => $vid, 'produto_id' => (int)$r['produto_id'], 'nome' => $r['nome'], 'cor_tom' => $r['cor_tom'], 'qtd' => $qtd, 'unit' => $unit];
}
$frete = $subtotal >= 149.90 ? 0.0 : 19.90; // frete expressa; grátis a partir de R$149,90
$total = $subtotal + $frete;

// Endereços do usuário
$endStmt = $pdo->prepare('SELECT * FROM enderecos WHERE usuario_id = :u ORDER BY id DESC');
$endStmt->execute([':u' => $usuario_id]);
$enderecos = $endStmt->fetchAll();

$erro = '';

// Cria endereço rápido quando o cliente ainda não tem um
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'criar_endereco') {
    $cep = trim($_POST['cep'] ?? ''); $logradouro = trim($_POST['logradouro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? ''); $estado = strtoupper(trim($_POST['estado'] ?? ''));
    if ($cep === '' || $logradouro === '' || $cidade === '' || strlen($estado) !== 2) {
        $erro = 'Preencha CEP, logradouro, cidade e UF (2 letras).';
    } else {
        $ins = $pdo->prepare('INSERT INTO enderecos (usuario_id, cep, logradouro, numero, complemento, bairro, cidade, estado, criado_em) VALUES (:u, :cep, :log, :num, :comp, :bairro, :cidade, :uf, NOW())');
        $ins->execute([
            ':u' => $usuario_id, ':cep' => $cep, ':log' => $logradouro,
            ':num' => trim($_POST['numero'] ?? ''), ':comp' => trim($_POST['complemento'] ?? ''),
            ':bairro' => trim($_POST['bairro'] ?? ''), ':cidade' => $cidade, ':uf' => $estado,
        ]);
        header('Location: ' . BASE . '/checkout/');
        exit;
    }
}

// Confirmação do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'confirmar') {
    $metodo = $_POST['metodo'] ?? '';
    $endereco_id = (int)($_POST['endereco_id'] ?? 0);
    $metodos = ['cartao' => 'Cartão de Crédito', 'pix' => 'PIX', 'boleto' => 'Boleto'];

    if (!isset($metodos[$metodo])) {
        $erro = 'Selecione um método de pagamento válido.';
    } elseif ($endereco_id <= 0) {
        $erro = 'Selecione um endereço de entrega.';
    } else {
        // Garante que o endereço pertence ao usuário
        $chk = $pdo->prepare('SELECT id FROM enderecos WHERE id = :id AND usuario_id = :u LIMIT 1');
        $chk->execute([':id' => $endereco_id, ':u' => $usuario_id]);
        if (!$chk->fetch()) {
            $erro = 'Endereço inválido.';
        } else {
            try {
                $pdo->beginTransaction();
                // RN01: revalida estoque de cada item dentro da transação
                foreach ($itens as $it) {
                    $up = $pdo->prepare('UPDATE produto_variacoes SET estoque = estoque - :q WHERE id = :id AND estoque >= :q');
                    $up->execute([':q' => $it['qtd'], ':id' => $it['variacao_id']]);
                    if ($up->rowCount() === 0) {
                        throw new RuntimeException("Estoque insuficiente para '{$it['nome']}'.");
                    }
                }
                $insPed = $pdo->prepare(
                    'INSERT INTO pedidos (usuario_id, endereco_id, cupom_id, status, subtotal, desconto, frete, total, criado_em)
                     VALUES (:u, :e, NULL, :status, :sub, 0, :frete, :total, NOW()) RETURNING id'
                );
                $insPed->execute([':u' => $usuario_id, ':e' => $endereco_id, ':status' => 'pendente', ':sub' => $subtotal, ':frete' => $frete, ':total' => $total]);
                $pedido_id = (int)$insPed->fetchColumn();

                $insItem = $pdo->prepare('INSERT INTO pedido_itens (pedido_id, produto_id, variacao_id, quantidade, preco_unitario) VALUES (:p, :prod, :var, :q, :preco)');
                foreach ($itens as $it) {
                    $insItem->execute([':p' => $pedido_id, ':prod' => $it['produto_id'], ':var' => $it['variacao_id'], ':q' => $it['qtd'], ':preco' => $it['unit']]);
                }
                // RN02: PIX/Boleto expiram em 24h; cartão sem expiração de pagamento
                if (in_array($metodo, ['pix', 'boleto'], true)) {
                    $insPag = $pdo->prepare("INSERT INTO pagamentos (pedido_id, metodo, status, valor, expira_em, criado_em) VALUES (:p, :m, :s, :v, NOW() + interval '24 hours', NOW())");
                    $insPag->execute([':p' => $pedido_id, ':m' => $metodo, ':s' => 'pendente', ':v' => $total]);
                } else {
                    $insPag = $pdo->prepare('INSERT INTO pagamentos (pedido_id, metodo, status, valor, expira_em, criado_em) VALUES (:p, :m, :s, :v, NULL, NOW())');
                    $insPag->execute([':p' => $pedido_id, ':m' => $metodo, ':s' => 'pendente', ':v' => $total]);
                }

                $pdo->commit();
                unset($_SESSION['carrinho']); // limpa carrinho após registrar o pedido
                header('Location: ' . BASE . '/checkout/?pedido=' . $pedido_id);
                exit;
            } catch (Throwable $e) {
                $pdo->rollBack();
                $erro = 'Não foi possível concluir o pedido: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout — Pink Glow</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-[#FFFDFE] text-[#4A1D2F] p-6">
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold text-pink-700">Finalizar compra</h1>
  <p class="text-sm text-pink-900/60 mb-6"><a href="<?= BASE ?>/carrinho/" class="text-pink-600 underline">← Voltar à sacola</a></p>
  <?php if ($erro): ?><p class="mb-4 px-4 py-2 rounded-lg bg-rose-50 text-rose-700 text-sm"><?= htmlspecialchars($erro) ?></p><?php endif; ?>

  <h2 class="font-semibold mb-2">Resumo do pedido</h2>
  <ul class="divide-y divide-pink-50 bg-white rounded-2xl border border-pink-100 px-4">
    <?php foreach ($itens as $it): ?>
    <li class="py-3 flex justify-between text-sm"><span><?= htmlspecialchars($it['nome']) ?> · <?= htmlspecialchars($it['cor_tom']) ?> × <?= $it['qtd'] ?></span><span>R$ <?= number_format($it['unit'] * $it['qtd'], 2, ',', '.') ?></span></li>
    <?php endforeach; ?>
  </ul>
  <div class="mt-3 bg-pink-50 rounded-2xl p-4 text-sm space-y-1">
    <p class="flex justify-between"><span>Subtotal</span><span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span></p>
    <p class="flex justify-between"><span>Frete</span><span><?= $frete == 0 ? 'Grátis' : 'R$ ' . number_format($frete, 2, ',', '.') ?></span></p>
    <p class="flex justify-between font-bold text-lg text-pink-700"><span>Total</span><span>R$ <?= number_format($total, 2, ',', '.') ?></span></p>
  </div>

  <?php if (empty($enderecos)): ?>
    <h2 class="font-semibold mt-6 mb-2">Endereço de entrega</h2>
    <form method="post" class="bg-white rounded-2xl border border-pink-100 p-4 grid grid-cols-2 gap-3 text-sm">
      <input type="hidden" name="acao" value="criar_endereco"/>
      <label>CEP <input name="cep" required class="w-full border border-pink-200 rounded-lg px-2 py-1"/></label>
      <label>UF <input name="estado" maxlength="2" required class="w-full border border-pink-200 rounded-lg px-2 py-1"/></label>
      <label class="col-span-2">Logradouro <input name="logradouro" required class="w-full border border-pink-200 rounded-lg px-2 py-1"/></label>
      <label>Número <input name="numero" class="w-full border border-pink-200 rounded-lg px-2 py-1"/></label>
      <label>Bairro <input name="bairro" class="w-full border border-pink-200 rounded-lg px-2 py-1"/></label>
      <label class="col-span-2">Cidade <input name="cidade" required class="w-full border border-pink-200 rounded-lg px-2 py-1"/></label>
      <label class="col-span-2">Complemento <input name="complemento" class="w-full border border-pink-200 rounded-lg px-2 py-1"/></label>
      <button class="col-span-2 bg-pink-600 text-white rounded-full py-2 font-semibold" type="submit">Salvar endereço</button>
    </form>
  <?php else: ?>
    <form method="post" class="mt-6">
      <input type="hidden" name="acao" value="confirmar"/>
      <h2 class="font-semibold mb-2">Endereço de entrega</h2>
      <select name="endereco_id" class="w-full border border-pink-200 rounded-lg px-3 py-2 text-sm">
        <?php foreach ($enderecos as $e): ?>
        <option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars(trim($e['logradouro'] . ' ' . $e['numero'] . ', ' . $e['bairro'] . ' — ' . $e['cidade'] . '/' . $e['estado'])) ?></option>
        <?php endforeach; ?>
      </select>
      <h2 class="font-semibold mt-6 mb-2">Pagamento (gateway simulado)</h2>
      <div class="space-y-2 text-sm">
        <label class="flex items-center gap-2 bg-white border border-pink-100 rounded-xl px-3 py-2"><input type="radio" name="metodo" value="cartao" required/> Cartão de Crédito</label>
        <label class="flex items-center gap-2 bg-white border border-pink-100 rounded-xl px-3 py-2"><input type="radio" name="metodo" value="pix" required/> PIX (5% off no banner — aprovação instantânea)</label>
        <label class="flex items-center gap-2 bg-white border border-pink-100 rounded-xl px-3 py-2"><input type="radio" name="metodo" value="boleto" required/> Boleto Bancário</label>
      </div>
      <button class="mt-6 w-full bg-pink-600 text-white rounded-full py-3 font-semibold hover:bg-pink-700" type="submit">Confirmar pedido — R$ <?= number_format($total, 2, ',', '.') ?></button>
    </form>
  <?php endif; ?>
</div>
</body></html>

<?php
// RN03 — Solicitação de devolução (direito de arrependimento, 7 dias corridos, produto lacrado).
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE . '/pedidos.php');
    exit;
}
if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE . '/login.php?erro=acesso');
    exit;
}
$uid       = (int)$_SESSION['usuario_id'];
$pedido_id = (int)($_POST['pedido_id'] ?? 0);
$motivo    = trim((string)($_POST['motivo'] ?? ''));
$lacrado   = isset($_POST['produto_lacrado']) ? 1 : 0;

if ($pedido_id <= 0 || $motivo === '') {
    $_SESSION['flash_erro'] = 'Informe o pedido e o motivo da devolução.';
    header('Location: ' . BASE . '/pedidos.php');
    exit;
}
if (!$lacrado) {
    $_SESSION['flash_erro'] = 'A devolução por arrependimento exige produto lacrado.';
    header('Location: ' . BASE . '/pedidos.php');
    exit;
}

// Garante que o pedido pertence ao cliente e está elegível (entregue/recebido há até 7 dias)
$chk = $pdo->prepare(
    "SELECT id, status, recebido_em FROM pedidos
     WHERE id = :p AND usuario_id = :u"
);
$chk->execute([':p' => $pedido_id, ':u' => $uid]);
$pedido = $chk->fetch();

$elegivel = false;
if ($pedido && ($pedido['status'] === 'entregue' || !empty($pedido['recebido_em']))) {
    $recebido = $pedido['recebido_em'] ? strtotime($pedido['recebido_em']) : null;
    if ($recebido && (time() - $recebido) <= 7 * 86400) {
        $elegivel = true;
    }
}
if (!$elegivel) {
    $_SESSION['flash_erro'] = 'Pedido não elegível para devolução (somente entregues há até 7 dias).';
    header('Location: ' . BASE . '/pedidos.php');
    exit;
}

try {
    $ins = $pdo->prepare(
        "INSERT INTO devolucoes (pedido_id, usuario_id, motivo, produto_lacrado, status, criado_em)
         VALUES (:p, :u, :m, :l, 'solicitada', NOW())"
    );
    $ins->execute([':p' => $pedido_id, ':u' => $uid, ':m' => $motivo, ':l' => $lacrado]);
    $_SESSION['flash_ok'] = 'Solicitação de devolução registrada com sucesso.';
} catch (PDOException $e) {
    // O trigger trg_devolucoes_rn03 valida recebido_em e o prazo de 7 dias
    $_SESSION['flash_erro'] = $e->getMessage();
}
header('Location: ' . BASE . '/pedidos.php');
exit;

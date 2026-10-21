<?php
// Área do cliente — exige sessão ativa; exibe nome, e-mail e logout.
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../config/db.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE . '/login.php?erro=acesso');
    exit;
}

$stmt = $pdo->prepare('SELECT nome, email, tipo FROM usuarios WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$u = $stmt->fetch();
if (!$u) {
    session_destroy();
    header('Location: ' . BASE . '/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><title>Minha conta — Pink Glow</title></head>
<body>
<h1>Minha conta</h1>
<p><strong>Nome:</strong> <?= htmlspecialchars($u['nome']) ?></p>
<p><strong>E-mail:</strong> <?= htmlspecialchars($u['email']) ?></p>
<p><strong>Tipo:</strong> <?= htmlspecialchars($u['tipo']) ?></p>
<p>
  <a href="<?= BASE ?>/">Voltar à vitrine</a> |
  <a href="<?= BASE ?>/pedidos.php">Meus pedidos</a> |
  <a href="<?= BASE ?>/logout.php">Sair</a>
<?php if ($u['tipo'] === 'admin'): ?> | <a href="<?= BASE ?>/admin/">Painel administrativo</a><?php endif; ?>
</p>
</body>
</html>

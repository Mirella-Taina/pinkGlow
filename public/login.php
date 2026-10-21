<?php
// Login placeholder (RF01) — autentica contra usuarios e abre a sessão.
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../config/db.php';

$erro = $_GET['erro'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $stmt = $pdo->prepare('SELECT id, nome, tipo, senha_hash FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $u = $stmt->fetch();
    if ($u && password_verify($senha, $u['senha_hash'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id']   = $u['id'];
        $_SESSION['usuario_nome'] = $u['nome'];
        $_SESSION['usuario_tipo'] = $u['tipo'];
        header('Location: ' . BASE . ($u['tipo'] === 'admin' ? '/admin/' : '/'));
        exit;
    }
    $erro = 'credenciais';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><title>Login — Pink Glow</title></head>
<body>
<h1>Entrar</h1>
<?php if ($erro === 'acesso'): ?><p>Acesso restrito. Faça login para continuar.</p><?php endif; ?>
<?php if ($erro === 'credenciais'): ?><p>E-mail ou senha inválidos.</p><?php endif; ?>
<?php if (isset($_GET['cadastrado'])): ?><p>Cadastro realizado! Faça login para continuar.</p><?php endif; ?>
<form method="post">
  <label>E-mail: <input type="email" name="email" required></label><br>
  <label>Senha: <input type="password" name="senha" required></label><br>
  <button type="submit">Entrar</button>
</form>
<p><a href="<?= BASE ?>/">Voltar à vitrine</a> | <a href="<?= BASE ?>/cadastro.php">Cadastrar</a></p>
</body>
</html>

<?php
// Cadastro de cliente (RF01) — valida dados e insere em usuarios com tipo='cliente'.
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../config/db.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE . '/conta.php');
    exit;
}

$erros = [];
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirma = $_POST['confirma_senha'] ?? '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($nome === '') {
        $erros[] = 'Informe seu nome.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail inválido.';
    }
    if (strlen($senha) < 8) {
        $erros[] = 'A senha deve ter pelo menos 8 caracteres.';
    }
    if ($senha !== $confirma) {
        $erros[] = 'A confirmação de senha não confere.';
    }
    if (empty($erros)) {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $erros[] = 'Este e-mail já está cadastrado.';
        } else {
            $ins = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, tipo, criado_em) VALUES (:nome, :email, :senha_hash, 'cliente', NOW())");
            $ins->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
            ]);
            $sucesso = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><title>Cadastro — Pink Glow</title></head>
<body>
<h1>Criar conta</h1>
<?php foreach ($erros as $e): ?><p style="color:#ba1a1a"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
<?php if ($sucesso): ?>
<p>Cadastro realizado com sucesso! <a href="<?= BASE ?>/login.php">Entrar</a></p>
<?php else: ?>
<form method="post">
  <label>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($nome) ?>" required></label><br>
  <label>E-mail: <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required></label><br>
  <label>Senha: <input type="password" name="senha" minlength="8" required></label><br>
  <label>Confirmar senha: <input type="password" name="confirma_senha" minlength="8" required></label><br>
  <button type="submit">Cadastrar</button>
</form>
<?php endif; ?>
<p>Já tem conta? <a href="<?= BASE ?>/login.php">Entrar</a> | <a href="<?= BASE ?>/">Voltar à vitrine</a></p>
</body>
</html>

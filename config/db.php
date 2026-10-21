<?php
// Conexão central PDO com o PostgreSQL.
// Exige variáveis de ambiente — sem fallback de senha hardcoded (falha fechada).
require_once __DIR__ . '/app.php';

$host = getenv('PGHOST') ?: '192.168.10.63';
$port = getenv('PGPORT') ?: '5432';
$db   = getenv('PGDATABASE') ?: 'pinkglow';
$user = getenv('PGUSER') ?: 'pinkglow';
$pass = getenv('PGPASSWORD') ?: 'pinkglow';

if (!$pass) {
    http_response_code(500);
    exit('Configuração inválida: variável de ambiente PGPASSWORD não definida.');
}

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db;options='--client_encoding=UTF8'",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Falha rápido quando o banco está inacessível (padrão: libpq espera ~21s).
            PDO::ATTR_TIMEOUT            => 5,
        ]
    );
} catch (PDOException $e) {
    error_log('Erro de conexão PDO: ' . $e->getMessage());
    http_response_code(503);
    exit('Serviço indisponível: não foi possível conectar ao banco de dados. '
        . 'Verifique se o PostgreSQL está acessível a partir desta rede/servidor.');
}

<?php
// Página inicial pública — vitrine da Pink Glow.
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/../config/db.php';

// Dados da vitrine (RF02) + busca e filtros (RF03)
$categorias = $pdo->query("SELECT id, nome, imagem_url FROM categorias ORDER BY nome")->fetchAll();
$marcas = $pdo->query("SELECT DISTINCT marca FROM produtos WHERE marca IS NOT NULL AND marca <> '' ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);

$q          = trim($_GET['q'] ?? '');
$precoMin   = $_GET['preco_min'] ?? '';
$precoMax   = $_GET['preco_max'] ?? '';
$marca      = trim($_GET['marca'] ?? '');
$categoriaId = (int)($_GET['categoria'] ?? 0);

$where  = ['p.ativo = true'];
$params = [];
if ($q !== '') {
    $where[] = '(p.nome ILIKE :q OR p.descricao ILIKE :q OR p.marca ILIKE :q)';
    $params[':q'] = '%' . $q . '%';
}
if (is_numeric($precoMin) && $precoMin !== '') {
    $where[] = 'p.preco >= :preco_min';
    $params[':preco_min'] = (float)$precoMin;
}
if (is_numeric($precoMax) && $precoMax !== '') {
    $where[] = 'p.preco <= :preco_max';
    $params[':preco_max'] = (float)$precoMax;
}
if ($marca !== '') {
    $where[] = 'p.marca = :marca';
    $params[':marca'] = $marca;
}
if ($categoriaId > 0) {
    $where[] = 'p.categoria_id = :categoria';
    $params[':categoria'] = $categoriaId;
}

$sql = "
    SELECT p.id, p.nome, p.marca, p.descricao, p.preco, p.preco_antigo, p.imagem_url,
           p.categoria_id AS id_categoria, c.nome AS categoria,
           (SELECT COALESCE(SUM(v.estoque), 0) FROM produto_variacoes v WHERE v.produto_id = p.id) AS estoque_total,
           (SELECT v2.id FROM produto_variacoes v2 WHERE v2.produto_id = p.id AND v2.estoque > 0 ORDER BY v2.estoque DESC, v2.id LIMIT 1) AS variacao_padrao_id
    FROM produtos p
    JOIN categorias c ON c.id = p.categoria_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY p.criado_em DESC, p.id
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();

require __DIR__ . '/../views/home.html.php';

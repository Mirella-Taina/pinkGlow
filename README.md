# Pink Glow — Loja de Cosméticos

Stack: HTML/CSS/JS (sem frameworks), PHP 8 (PDO), PostgreSQL.

## Estrutura

- `config/db.php` — conexão PDO central (usa variáveis `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD`, com fallback de dev).
- `public/` — document root: `index.php` (vitrine), `produtos/`, `carrinho/`, `checkout/`, `admin/`, `assets/`.
- `assets/css/style.css`, `assets/js/app.js` — estilos e scripts (cópia servida em `public/assets/`).
- `views/` — templates PHP simples (`home.html.php`).
- `stitch_sistema_loja_de_cosm_ticos/` — telas de referência do Stitch (preservadas).

## Banco de dados

Host `192.168.10.63:5432`, banco `pinkglow`, usuário/senha `pinkglow`.
Tabelas: `usuarios`, `categorias`, `produtos`, `produto_variacoes`, `enderecos`,
`pedidos`, `pedido_itens`, `cupons`, `pagamentos` (status pendente/pago/cancelado — RN02),
`devolucoes` (RN03).

## Como rodar

```bash
php -S localhost:8000 -t public
```

Acesse http://localhost:8000 .

## RN02 — Cancelamento automático de pedidos expirados

`scripts/cancelar_pedidos_expirados.php` cancela pedidos com status `pendente` cujo
pagamento (PIX ou Boleto) continua `pendente` e já passou de `expira_em` (24h),
restaurando o estoque das variações — tudo em uma transação.

```bash
php scripts/cancelar_pedidos_expirados.php
```

Agende a execução a cada hora:

- **Linux/cron:** `0 * * * * cd /caminho/pinkGlow && php scripts/cancelar_pedidos_expirados.php >> /var/log/pinkglow_rn02.log 2>&1`
- **Windows Task Scheduler:** criar tarefa "PinkGlow RN02", gatilho a cada 1 hora, ação:
  `php.exe C:\caminho\pinkGlow\scripts\cancelar_pedidos_expirados.php`
  (definir as variáveis de ambiente `PGHOST`, `PGUSER`, `PGPASSWORD`, `PGDATABASE` na tarefa).

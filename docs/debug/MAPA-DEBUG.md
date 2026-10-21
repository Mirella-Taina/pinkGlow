# Mapa de Debug — PinkGlow

> Objetivo: acelerar o rastreio quando algo der errado (carrinho, estoque, login, checkout, pedidos).

## 1) Pontos de entrada

| Arquivo | Quando usar | O que inspecionar |
|---|---|---|
| `index.php` (raiz) | Ao abrir pela raiz do projeto | Ver se redireciona para `/public/` sem loop |
| `public/index.php` | Vitrine/carregamento inicial | Filtros `GET`, `WHERE` dinâmico, `estoque_total`, `variacao_padrao_id` |
| `config/app.php` | Problemas com links/assets | `BASE`, `url_base()`, detecção automática `/public/` |
| `config/db.php` | Falhas de conexão | `PGHOST/PGPORT/PGDATABASE/PGUSER/PGPASSWORD`, PDO exceptions, timeout 5s |

## 2) Fluxo de compra

| Arquivo | Ponto de debug | Dica |
|---|---|---|
| `public/carrinho/adicionar.php` | Adicionar item ao carrinho | Ver `$_SESSION['carrinho'][variacao_id]`, validação de estoque antes de somar |
| `public/carrinho/index.php` | Listagem/alteração (+/-/remover/atualizar) | Sempre reidrata do banco. Ver flash `$_SESSION['flash_ok']/flash_erro'`. RN01 (não ultrapassar estoque) |
| `public/checkout/` | Finalização | Ver criação de `pedidos`, `pedido_itens`, `pagamentos`. Conferir cálculo de `subtotal/desconto/frete/total`. Transações (BEGIN/COMMIT/ROLLBACK) |
| `public/pedidos.php` | Histórico do cliente | Filtros por `usuario_id`, status |

## 3) Autenticação e sessão

| Arquivo | Ponto de debug | Dica |
|---|---|---|
| `public/login.php` | Login | `password_verify` com `senha_hash`, `session_regenerate_id(true)`, redirecionamento por `usuario_tipo` |
| `public/cadastro.php` | Cadastro | Validação de email, `password_hash`, duplicados |
| `public/logout.php` | Logout | Destruição de sessão |
| `public/conta.php` | Dados da conta | `$_SESSION['usuario_id']` presente? |
| `public/admin/*` | Proteção admin | Verificar `$_SESSION['usuario_tipo'] === 'admin'`. Muitas vezes falta esse guard |

## 4) Banco e regras

| Tabela | Regra/observação | Debug |
|---|---|---|
| `produto_variacoes` | Fonte real do estoque (`estoque`). `preco_extra`. | Se carrinho "soma" errado, conferir `preco + preco_extra` |
| `produtos` | `ativo`, `preco`, `preco_antigo`. | Produtos sumindo? Ver `ativo = true` |
| `pedidos` | `status` (pendente/cancelado/entregado), valores calculados | Conferir integridade entre `pedidos` e `pedido_itens` |
| `pagamentos` | `metodo` (pix/boleto/cartao), `status`, `expira_em` | RN02: pagamentos pendentes vencidos podem virar cancelados via script |
| `devolucoes` | RN03: prazo 7 dias desde `pedidos.recebido_em` + trigger `trg_devolucoes_rn03` | Erro ao solicitar devolução? Olhar trigger/validacao |
| `cupons` | `codigo` único, `tipo`, `valor`, `validade`, `ativo` | Cupom não aplica? Conferir datas/ativo |

## 5) Checklist rápido para debug

### Carrinho travando / valores errados
- [ ] `$_SESSION['carrinho']` tem a `variacao_id` correta?
- [ ] No `carrinho/index.php`, o `SELECT` reidrata preço/estoque do banco (server-side) — está atualizando?
- [ ] RN01: ao clicar `+`, tem estoque disponível?
- [ ] `unitario = preco + preco_extra` está sendo usado?

### Login/redirect errado
- [ ] `session_start()` chamado antes de usar `$_SESSION`
- [ ] `BASE` está correto com `config/app.php`
- [ ] `usuario_tipo` vem do banco (`cliente` ou `admin`)
- [ ] Cookie/session em path/subpasta funciona?

### Estoque inconsistente
- [ ] Somar `produto_variacoes.estoque` por produto bate com `estoque_total` mostrado na vitrine?
- [ ] Ao finalizar pedido, estoque foi baixado? (se não tiver, é ponto de bug)
- [ ] Pedido já finalizado mas ainda aparece como disponível?

### Checkout falhando
- [ ] Tem endereço cadastrado e selecionado?
- [ ] Carrinho não vazio e itens válidos (estoque >= qtd)?
- [ ] Transação aberta? Erro em algum `INSERT` faz rollback?
- [ ] `pedido_itens` gravou com `produto_id`, `variacao_id`, `quantidade`, `preco_unitario` corretos?

## 6) Variáveis globais pra logar

Cole isso num `var_dump` temporário quando estiver debugando:

```php
var_dump([
  'BASE' => defined('BASE') ? BASE : null,
  'session' => $_SESSION ?? [],
  'get' => $_GET ?? [],
  'post' => $_POST ?? [],
]);
```

```php
// Ver carrinho completo
var_dump($_SESSION['carrinho'] ?? []);
```

```php
// Ver itens recalculados no carrinho
var_dump($itens ?? [], $total ?? 0);
```

> **Dica:** Remova os `var_dump` antes de commitar.

# Pink Glow — Diagrama de Entidade e Relacionamento (DER)

Banco: **PostgreSQL** (`pinkglow`) — stack PHP 8 + PDO.
Fonte: reconstruído a partir de `README.md`, `migrations/*.sql` e do código PHP em `public/`, `views/` e `scripts/`.

> **Observação:** o arquivo `SQL/sql.sql` é legado (MySQL, banco `sistema_maquiagem`) e **não** reflete o schema em produção — nele `produtos` tem uma coluna de texto `categoria`, enquanto o sistema real usa a FK `categoria_id` → `categorias.id`.

## Diagrama

```mermaid
erDiagram
    usuarios {
        int      id          PK
        varchar  nome
        varchar  email       "lógico: único (não confirmado no DDL)"
        varchar  senha_hash
        varchar  tipo        "cliente | admin"
        timestamp criado_em
    }

    categorias {
        int      id          PK
        varchar  nome        "Lábios | Olhos | Pele | Acessórios"
        text     descricao
        varchar  imagem_url  "varchar(255)"
    }

    produtos {
        int      id           PK
        int      categoria_id FK
        varchar  nome
        varchar  marca
        text     descricao
        numeric  preco
        numeric  preco_antigo
        varchar  imagem_url
        boolean  ativo
        timestamp criado_em
    }

    produto_variacoes {
        int     id          PK
        int     produto_id  FK
        varchar cor_tom
        varchar sku         "nullable"
        int     estoque
        numeric preco_extra
    }

    enderecos {
        int      id          PK
        int      usuario_id  FK
        varchar  cep
        varchar  logradouro
        varchar  numero
        varchar  complemento
        varchar  bairro
        varchar  cidade
        varchar  estado
        timestamp criado_em
    }

    pedidos {
        int      id          PK
        int      usuario_id  FK
        int      endereco_id FK
        int      cupom_id    FK "nullable"
        varchar  status      "pendente | cancelado | entregue"
        numeric  subtotal
        numeric  desconto
        numeric  frete
        numeric  total
        timestamp criado_em
        timestamp recebido_em "nullable"
    }

    pedido_itens {
        int     id             PK
        int     pedido_id      FK
        int     produto_id     FK
        int     variacao_id    FK "nullable"
        int     quantidade
        numeric preco_unitario
    }

    cupons {
        int     id       PK
        varchar codigo   "único (lógico)"
        varchar tipo     "percentual | fixo"
        numeric valor
        date    validade "nullable"
        boolean ativo
    }

    pagamentos {
        int      id        PK
        int      pedido_id FK
        varchar  metodo    "pix | boleto | cartao"
        varchar  status    "pendente | pago | cancelado"
        numeric  valor
        timestamp expira_em "nullable (PIX/Boleto +24h)"
        timestamp criado_em
    }

    devolucoes {
        int      id             PK
        int      pedido_id      FK
        int      usuario_id     FK
        text     motivo
        boolean  produto_lacrado
        varchar  status         "solicitada"
        timestamp criado_em
    }

    categorias        ||--o{ produtos          : "classifica"
    produtos          ||--o{ produto_variacoes : "possui"
    usuarios          ||--o{ enderecos         : "cadastra"
    usuarios          ||--o{ pedidos           : "realiza"
    enderecos         ||--o{ pedidos           : "entrega em"
    cupons            |o--o{ pedidos           : "aplica"
    pedidos           ||--|{ pedido_itens      : "contém"
    produtos          ||--o{ pedido_itens      : "referencia"
    produto_variacoes |o--o{ pedido_itens      : "referencia"
    pedidos           ||--o{ pagamentos        : "pago via"
    pedidos           ||--o{ devolucoes        : "gera"
    usuarios          ||--o{ devolucoes        : "solicita"
```

## Legenda dos relacionamentos

| Relação | Cardinalidade | Coluna de ligação | Confirmação |
|---|---|---|---|
| categorias → produtos | 1:N | `produtos.categoria_id` | confirmado (JOIN) |
| produtos → produto_variacoes | 1:N | `produto_variacoes.produto_id` | confirmado (JOIN) |
| usuarios → enderecos | 1:N | `enderecos.usuario_id` | confirmado (JOIN) |
| usuarios → pedidos | 1:N | `pedidos.usuario_id` | confirmado (JOIN) |
| enderecos → pedidos | 1:N | `pedidos.endereco_id` | inferido (nome/INSERT) |
| cupons → pedidos | 1:N (opcional) | `pedidos.cupom_id` (nullable) | inferido |
| pedidos → pedido_itens | 1:N | `pedido_itens.pedido_id` | confirmado (JOIN) |
| produtos → pedido_itens | 1:N | `pedido_itens.produto_id` | confirmado (JOIN) |
| produto_variacoes → pedido_itens | 1:N (opcional) | `pedido_itens.variacao_id` (nullable) | confirmado (JOIN) |
| pedidos → pagamentos | 1:N | `pagamentos.pedido_id` | confirmado (JOIN) |
| pedidos → devolucoes | 1:N | `devolucoes.pedido_id` | inferido |
| usuarios → devolucoes | 1:N | `devolucoes.usuario_id` | confirmado |

## Regras de negócio ligadas ao modelo

- **RN01** — Estoque controlado em `produto_variacoes.estoque`; disponibilidade do produto = `SUM(estoque)` das variações.
- **RN02** — `pagamentos` (PIX/Boleto) com `expira_em` +24h e `status = 'pendente'` → `pedidos.status = 'cancelado'` (script `scripts/cancelar_pedidos_expirados.php`).
- **RN03** — Devolução em `devolucoes` validada pelo trigger `trg_devolucoes_rn03` (usa `pedidos.recebido_em` e prazo de 7 dias).

## Índices e objetos

- `idx_produtos_marca` em `produtos (marca)` — `migrations/001_produtos_marca.sql`.
- Trigger `trg_devolucoes_rn03` — citado em `public/devolucao.php`.

## Ressalvas

1. **Sem DDL completo no repositório.** O único `CREATE TABLE` existente é o legado MySQL em `SQL/sql.sql`. Tipos, tamanhos, nulabilidade, `UNIQUE` (ex.: `usuarios.email`, `cupons.codigo`) e `ON DELETE` foram inferidos — não há `ADD CONSTRAINT` visível.
2. `pagamentos.id` como PK foi inferido (nunca selecionado explicitamente no código).
3. `documentação projeto.pdf` não pôde ser lido — pode conter o DDL oficial; vale conferir.

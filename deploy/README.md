# Deploy da Pink Glow — nginx + PHP-FPM em `/pinkglow/public/`

## Arquitetura de URLs

| URL | Comportamento |
|-----|---------------|
| `/` (raiz do servidor) | 301 → `/pinkglow/public/` |
| `/pinkglow/` (raiz do projeto) | 301 → `/pinkglow/public/` |
| `/pinkglow/public/` | Aplicação (vitrine) |
| `/pinkglow/public/carrinho/`, `/pinkglow/public/login.php`, ... | Rotas da aplicação |
| `/pinkglow/public/assets/...` | CSS/JS/imagens servidos direto pelo nginx |

O projeto fica em uma **pasta própria** dentro do webroot (`/var/www/html/pinkglow`), não na raiz.
A raiz do projeto redireciona para a pasta `public/` (que contém o `index.php` da aplicação).

## 1. Copiar os arquivos

```bash
sudo mkdir -p /var/www/html/pinkglow
sudo rsync -a --exclude 'stitch_sistema_loja_de_cosm_ticos' ./ /var/www/html/pinkglow/
sudo chown -R www-data:www-data /var/www/html/pinkglow
```

## 2. Configurar PHP-FPM (variáveis de ambiente)

Edite o pool do php-fpm (ex.: `/etc/php/8.2/fpm/pool.d/www.conf`) e adicione:

```ini
env[PGHOST] = 192.168.10.63
env[PGPORT] = 5432
env[PGUSER] = pinkglow
env[PGPASSWORD] = pinkglow
env[PGDATABASE] = pinkglow
env[APP_BASE] = /pinkglow/public
```

Reinicie o php-fpm: `sudo systemctl restart php8.2-fpm`

> As `migrations/*.sql` **já estão aplicadas** no banco `192.168.10.63`. Só rode novamente se for um servidor/banco novo:
> ```bash
> psql -h 192.168.10.63 -U pinkglow -d pinkglow -f migrations/001_produtos_marca.sql
> psql -h 192.168.10.63 -U pinkglow -d pinkglow -f migrations/002_marcas_demo.sql
> ```

## 3. Configurar o nginx

```bash
sudo cp deploy/nginx/pinkglow.conf /etc/nginx/sites-available/pinkglow
sudo ln -s /etc/nginx/sites-available/pinkglow /etc/nginx/sites-enabled/pinkglow
sudo nginx -t
sudo systemctl reload nginx
```

**Ajustes obrigatórios no `deploy/nginx/pinkglow.conf`:**
- **Socket do php-fpm**: linha `fastcgi_pass unix:/run/php/php-fpm.sock;` — troque pelo socket real do seu sistema (ex.: `unix:/var/run/php/php8.2-fpm.sock`, `unix:/run/php/php8.3-fpm.sock`)
- **`server_name`**: linha `server_name _;` — **em produção, coloque seu domínio real** (ex.: `loja.exemplo.com`). O `_` aceita qualquer Host header (risco de segurança).
- **`root` / caminhos**: apontam para `/var/www/html/pinkglow`. Se o seu webroot for diferente, ajuste as ocorrências de `/var/www/html/pinkglow` nas regras de `alias`.

> ℹ️ **Case-sensitive**: a URL é `/pinkglow/public/` (tudo minúsculo). Nginx no Linux diferencia maiúsculas de minúsculas.

## 4. Testar

```bash
# Raiz do servidor → redireciona
curl -I http://localhost/
# → 301 Location: /pinkglow/public/

# Raiz do projeto → redireciona
curl -I http://localhost/pinkglow/
# → 301 Location: /pinkglow/public/

curl -I http://localhost/pinkglow/public/                 # → 200
curl -I http://localhost/pinkglow/public/assets/img/p1.svg # → 200
```

No navegador: **`http://<seu-servidor>/`** redireciona para a loja em `/pinkglow/public/`.

## 5. Rodar local (sem nginx, para desenvolvimento)

```bash
export PGPASSWORD=pinkglow
php -S localhost:8140 -t public
# Com base (prefixo /pinkglow/public nos links)
APP_BASE=/pinkglow/public php -S localhost:8140 -t public
```

> Nota: com `APP_BASE=/pinkglow/public` no `php -S` embutido, os links no HTML saem com esse prefixo, mas o servidor embutido não remove o prefixo das requisições — o teste de ponta a ponta deve ser via nginx. Para validar a geração dos links, inspecione o HTML do `curl http://localhost:8140/`.

## 6. Estrutura no servidor

```
/var/www/html/                    ← webroot
└── pinkglow/                     ← projeto (pasta própria)
    ├── index.php                 ← redireciona para public/
    ├── public/                   ← aplicação (index.php, assets/)
    │   ├── index.php
    │   ├── carrinho/ checkout/ admin/
    │   ├── login.php cadastro.php conta.php
    │   ├── pedidos.php devolucao.php devolucoes.php
    │   └── assets/ (css/, js/, img/)
    ├── config/ (app.php, db.php)
    ├── views/ (home.html.php)
    ├── migrations/
    ├── scripts/ (cancelar_pedidos_expirados.php)
    └── deploy/ (nginx/pinkglow.conf, README.md)
```

## 7. Checklist pós-deploy

- [ ] `http://seu-dominio/` redireciona para `/pinkglow/public/`
- [ ] `http://seu-dominio/pinkglow/` redireciona para `/pinkglow/public/`
- [ ] A vitrine carrega com produtos e imagens (não quebradas)
- [ ] CSS e JS carregam (`/pinkglow/public/assets/...`)
- [ ] Cadastro/login de cliente funciona
- [ ] Carrinho adiciona/remove/atualiza subtotal
- [ ] Checkout PIX/Boleto/Cartão cria pedido e pagamento pendente
- [ ] `/pinkglow/public/admin/` exige login admin
- [ ] CRUD de produtos/categorias/cupons/estoque no admin
- [ ] Script `php scripts/cancelar_pedidos_expirados.php` roda sem erro (agende no cron)
- [ ] Headers de segurança (CSP, HSTS) — ver relatório de segurança do revisor

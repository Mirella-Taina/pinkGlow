-- RF03 — Produto passa a ter marca explícita para filtros da vitrine.
-- Idempotente: pode ser reaplicada sem efeitos colaterais.
ALTER TABLE produtos ADD COLUMN IF NOT EXISTS marca VARCHAR;

-- Backfill (seed): produtos sem marca recebem a marca própria da loja.
UPDATE produtos SET marca = 'Pink Glow' WHERE marca IS NULL OR marca = '';

-- Eventuais marcas demo para tornar o filtro de ?marca= testável:
-- (comentado; descomente se quiser variar as marcas do seed)
-- UPDATE produtos SET marca = 'GlowLab' WHERE id % 3 = 0;
-- UPDATE produtos SET marca = 'Beleza Viva' WHERE id % 3 = 1;

CREATE INDEX IF NOT EXISTS idx_produtos_marca ON produtos (marca);

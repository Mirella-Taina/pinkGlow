-- RF03 — Distribui marcas demo entre os produtos para o filtro ?marca= ser demonstrável.
-- Idempotente: só rebaixa produtos genéricos sem marca definida pela loja.
UPDATE produtos SET marca = CASE (id % 3)
    WHEN 0 THEN 'GlowLab'
    WHEN 1 THEN 'Beleza Viva'
    ELSE 'Pink Glow'
END WHERE marca = 'Pink Glow' AND id % 3 <> 2;

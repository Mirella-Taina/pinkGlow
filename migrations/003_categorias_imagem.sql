-- 003 — Imagens das categorias (Lábios, Olhos, Pele, Acessórios)
ALTER TABLE categorias ADD COLUMN IF NOT EXISTS imagem_url varchar(255);

UPDATE categorias SET imagem_url = '/assets/img/batom.jpg'          WHERE nome = 'Lábios';
UPDATE categorias SET imagem_url = '/assets/img/paleta-sombra.jpg'  WHERE nome = 'Olhos';
UPDATE categorias SET imagem_url = '/assets/img/modelo-maquiagem.jpg' WHERE nome = 'Pele';
UPDATE categorias SET imagem_url = '/assets/img/kit-pinceis.jpg'    WHERE nome = 'Acessórios';
UPDATE categorias SET imagem_url = '/assets/img/placeholder.svg'   WHERE imagem_url IS NULL;

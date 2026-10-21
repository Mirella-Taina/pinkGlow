CREATE DATABASE IF NOT EXISTS sistema_maquiagem;
USE sistema_maquiagem;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    marca VARCHAR(50) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    descricao TEXT
);

-- Dados de teste
INSERT INTO produtos (nome, categoria, marca, preco, descricao) VALUES
('Batom Matte Ruby Woo', 'Batom', 'MAC', 100.00, 'Batom vermelho de longa duração e acabamento matte'),
('Base Liquid Touch', 'Rosto', 'Rare Beauty', 170,00 'Base líquida de cobertura média com acabamento natural'),
('Máscara de Cílios Lash Sensational', 'Olhos', 'Maybelline', 49.90, 'Proporciona efeito leque e volume aos cílios');
--
-- PostgreSQL database dump
--

\restrict BQlWGKAd8xbfV8KDMGHpT06FUOhyBmbdfRfhe1exlxRHAmk5MRyNAROCNkJJ4Za

-- Dumped from database version 18.6 (Ubuntu 18.6-0ubuntu0.26.04.1)
-- Dumped by pg_dump version 18.6 (Ubuntu 18.6-0ubuntu0.26.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: valida_devolucao_rn03(); Type: FUNCTION; Schema: public; Owner: pinkglow
--

CREATE FUNCTION public.valida_devolucao_rn03() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    recebido TIMESTAMP;
BEGIN
    SELECT recebido_em INTO recebido FROM pedidos WHERE id = NEW.pedido_id;
    IF recebido IS NULL THEN
        RAISE EXCEPTION 'RN03: pedido % ainda não foi recebido (recebido_em nulo)', NEW.pedido_id;
    END IF;
    IF NEW.criado_em > recebido + INTERVAL '7 days' THEN
        RAISE EXCEPTION 'RN03: devolução fora do prazo de 7 dias após o recebimento';
    END IF;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.valida_devolucao_rn03() OWNER TO pinkglow;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: categorias; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.categorias (
    id integer NOT NULL,
    nome character varying(80) NOT NULL,
    descricao text,
    criado_em timestamp without time zone DEFAULT now() NOT NULL,
    imagem_url character varying(255)
);


ALTER TABLE public.categorias OWNER TO pinkglow;

--
-- Name: categorias_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.categorias ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.categorias_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: cupons; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.cupons (
    id integer NOT NULL,
    codigo character varying(30) NOT NULL,
    tipo character varying(20) NOT NULL,
    valor numeric(10,2) NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    validade date,
    criado_em timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT cupons_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['percentual'::character varying, 'fixo'::character varying])::text[]))),
    CONSTRAINT cupons_valor_check CHECK ((valor >= (0)::numeric))
);


ALTER TABLE public.cupons OWNER TO pinkglow;

--
-- Name: cupons_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.cupons ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.cupons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: devolucoes; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.devolucoes (
    id integer NOT NULL,
    pedido_id integer NOT NULL,
    usuario_id integer NOT NULL,
    motivo text NOT NULL,
    produto_lacrado boolean DEFAULT true NOT NULL,
    status character varying(20) DEFAULT 'solicitada'::character varying NOT NULL,
    criado_em timestamp without time zone DEFAULT now() NOT NULL,
    atualizado_em timestamp without time zone,
    CONSTRAINT devolucoes_status_check CHECK (((status)::text = ANY ((ARRAY['solicitada'::character varying, 'aprovada'::character varying, 'recusada'::character varying, 'concluida'::character varying])::text[])))
);


ALTER TABLE public.devolucoes OWNER TO pinkglow;

--
-- Name: devolucoes_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.devolucoes ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.devolucoes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: enderecos; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.enderecos (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    rotulo character varying(40) DEFAULT 'Casa'::character varying,
    cep character varying(9) NOT NULL,
    logradouro character varying(160) NOT NULL,
    numero character varying(20),
    complemento character varying(80),
    bairro character varying(80),
    cidade character varying(80) NOT NULL,
    estado character(2) NOT NULL,
    criado_em timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.enderecos OWNER TO pinkglow;

--
-- Name: enderecos_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.enderecos ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.enderecos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: pagamentos; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.pagamentos (
    id integer NOT NULL,
    pedido_id integer NOT NULL,
    metodo character varying(20) NOT NULL,
    status character varying(20) DEFAULT 'pendente'::character varying NOT NULL,
    valor numeric(10,2) NOT NULL,
    pago_em timestamp without time zone,
    criado_em timestamp without time zone DEFAULT now() NOT NULL,
    expira_em timestamp without time zone DEFAULT (now() + '24:00:00'::interval),
    CONSTRAINT pagamentos_metodo_check CHECK (((metodo)::text = ANY ((ARRAY['cartao'::character varying, 'pix'::character varying, 'boleto'::character varying])::text[]))),
    CONSTRAINT pagamentos_status_check CHECK (((status)::text = ANY ((ARRAY['pendente'::character varying, 'pago'::character varying, 'cancelado'::character varying])::text[]))),
    CONSTRAINT pagamentos_valor_check CHECK ((valor >= (0)::numeric))
);


ALTER TABLE public.pagamentos OWNER TO pinkglow;

--
-- Name: pagamentos_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.pagamentos ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.pagamentos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: pedido_itens; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.pedido_itens (
    id integer NOT NULL,
    pedido_id integer NOT NULL,
    produto_id integer NOT NULL,
    variacao_id integer,
    quantidade integer NOT NULL,
    preco_unitario numeric(10,2) NOT NULL,
    CONSTRAINT pedido_itens_quantidade_check CHECK ((quantidade > 0))
);


ALTER TABLE public.pedido_itens OWNER TO pinkglow;

--
-- Name: pedido_itens_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.pedido_itens ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.pedido_itens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: pedidos; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.pedidos (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    endereco_id integer NOT NULL,
    cupom_id integer,
    status character varying(20) DEFAULT 'pendente'::character varying NOT NULL,
    subtotal numeric(10,2) DEFAULT 0 NOT NULL,
    desconto numeric(10,2) DEFAULT 0 NOT NULL,
    frete numeric(10,2) DEFAULT 0 NOT NULL,
    total numeric(10,2) DEFAULT 0 NOT NULL,
    recebido_em timestamp without time zone,
    criado_em timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT pedidos_status_check CHECK (((status)::text = ANY ((ARRAY['pendente'::character varying, 'pago'::character varying, 'enviado'::character varying, 'entregue'::character varying, 'cancelado'::character varying, 'devolvido'::character varying])::text[])))
);


ALTER TABLE public.pedidos OWNER TO pinkglow;

--
-- Name: pedidos_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.pedidos ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.pedidos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: produto_variacoes; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.produto_variacoes (
    id integer NOT NULL,
    produto_id integer NOT NULL,
    cor_tom character varying(60) NOT NULL,
    sku character varying(60),
    estoque integer DEFAULT 0 NOT NULL,
    preco_extra numeric(10,2) DEFAULT 0 NOT NULL,
    criado_em timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT produto_variacoes_estoque_check CHECK ((estoque >= 0))
);


ALTER TABLE public.produto_variacoes OWNER TO pinkglow;

--
-- Name: produto_variacoes_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.produto_variacoes ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.produto_variacoes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: produtos; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.produtos (
    id integer NOT NULL,
    categoria_id integer NOT NULL,
    nome character varying(160) NOT NULL,
    marca character varying(80),
    descricao text,
    preco numeric(10,2) NOT NULL,
    preco_antigo numeric(10,2),
    imagem_url text,
    ativo boolean DEFAULT true NOT NULL,
    criado_em timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT produtos_preco_check CHECK ((preco >= (0)::numeric))
);


ALTER TABLE public.produtos OWNER TO pinkglow;

--
-- Name: produtos_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.produtos ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.produtos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: pinkglow
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nome character varying(120) NOT NULL,
    email character varying(160) NOT NULL,
    senha_hash character varying(255) NOT NULL,
    tipo character varying(20) DEFAULT 'cliente'::character varying NOT NULL,
    telefone character varying(20),
    criado_em timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT usuarios_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['cliente'::character varying, 'admin'::character varying])::text[])))
);


ALTER TABLE public.usuarios OWNER TO pinkglow;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: pinkglow
--

ALTER TABLE public.usuarios ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.usuarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Data for Name: categorias; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.categorias (id, nome, descricao, criado_em, imagem_url) FROM stdin;
1	Lábios	Categoria Lábios	2026-10-02 15:36:03.697246	/assets/img/batom.jpg
2	Olhos	Categoria Olhos	2026-10-02 15:36:03.71145	/assets/img/paleta-sombra.jpg
3	Pele	Categoria Pele	2026-10-02 15:36:03.72445	/assets/img/modelo-maquiagem.jpg
4	Acessórios	Categoria Acessórios	2026-10-02 15:36:03.73552	/assets/img/kit-pinceis.jpg
6	Corporal & Glow	Oleos hidratantes com brilho, bronzers e perfumes capilares/corporais.	2026-10-21 11:04:55.064645	\N
7	Skincare & Cuidados	Produtos para preparação, hidratação, limpeza e cuidados com a pele antes e depois da maquiagem.	2026-10-21 11:05:43.092054	\N
5	Kits & Presentes	Conjuntos de maquiagem, combinações especiais e kits presenteáveis com preços promocionais.	2026-10-21 11:04:26.462113	\N
\.


--
-- Data for Name: cupons; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.cupons (id, codigo, tipo, valor, ativo, validade, criado_em) FROM stdin;
2	123454	fixo	100.00	t	2026-10-01	2026-10-14 16:16:27.185015
3	153231	percentual	89.90	t	2026-09-04	2026-10-14 16:17:10.831186
\.


--
-- Data for Name: devolucoes; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.devolucoes (id, pedido_id, usuario_id, motivo, produto_lacrado, status, criado_em, atualizado_em) FROM stdin;
\.


--
-- Data for Name: enderecos; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.enderecos (id, usuario_id, rotulo, cep, logradouro, numero, complemento, bairro, cidade, estado, criado_em) FROM stdin;
2	6	Casa	01001-000	Rua Teste	100		Centro	Sao Paulo	SP	2026-10-02 16:20:28.839909
3	7	Casa	13478-710	rua angelo ortolan	150	Apartamento	machadinho	americana	SP	2026-10-14 14:13:31.605638
4	8	Casa	13466-560	Rua das açucenas	201	casa	cidade jardim	americana	SP	2026-10-14 16:30:56.054823
5	1	Casa	13478-710	rua angelo ortolan	150	Apartamento	machadinho	americana	SP	2026-10-21 08:12:43.955046
6	42	Casa	13467-600	Avenida de Cillo	100	Casa	Parque novo mundo	americana	SP	2026-10-21 08:29:51.783277
7	43	Casa	13478-710	rua angelo ortolan	150	Apartamento	machadinho	americana	SP	2026-10-21 10:54:48.501281
8	44	Casa	13473-729	rua antonio giordanio	302	casa	Nova Carioba	americana	SP	2026-10-21 13:48:56.764125
9	45	Casa	13467520	Jundiai 1220	23	do lado de um mercado	avenida brasil	americana	SP	2026-10-21 16:36:53.31241
10	47	Casa	543	sim				Americana	FU	2026-10-21 16:38:37.071603
\.


--
-- Data for Name: pagamentos; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.pagamentos (id, pedido_id, metodo, status, valor, pago_em, criado_em, expira_em) FROM stdin;
2	2	pix	pendente	214.30	\N	2026-10-02 16:20:41.261056	2026-10-03 16:20:41.261056
5	7	cartao	pendente	154.80	\N	2026-10-14 14:13:37.704718	\N
6	8	cartao	pendente	147.60	\N	2026-10-14 16:31:04.928194	\N
7	9	cartao	pendente	154.80	\N	2026-10-14 16:33:14.973704	\N
8	10	pix	pendente	49.80	\N	2026-10-21 08:12:50.597927	2026-10-22 08:12:50.597927
9	11	boleto	pendente	164.70	\N	2026-10-21 08:13:34.677222	2026-10-22 08:13:34.677222
10	12	cartao	pendente	154.60	\N	2026-10-21 08:29:55.988001	\N
11	13	cartao	pendente	228.60	\N	2026-10-21 10:54:52.761687	\N
12	14	cartao	pendente	57.90	\N	2026-10-21 10:59:52.657249	\N
13	15	cartao	pendente	262.70	\N	2026-10-21 13:49:05.170047	\N
14	16	cartao	pendente	207.70	\N	2026-10-21 14:50:28.634606	\N
15	17	pix	pendente	54.80	\N	2026-10-21 16:36:58.390708	2026-10-22 16:36:58.390708
16	18	pix	pendente	62.80	\N	2026-10-21 16:38:43.097704	2026-10-22 16:38:43.097704
\.


--
-- Data for Name: pedido_itens; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.pedido_itens (id, pedido_id, produto_id, variacao_id, quantidade, preco_unitario) FROM stdin;
2	2	1	2	2	44.90
3	2	7	13	5	24.90
6	7	8	16	1	134.90
7	8	4	8	1	37.90
8	8	6	12	1	59.90
9	8	7	14	1	29.90
10	9	8	16	1	134.90
11	10	7	14	1	29.90
12	11	1	2	1	44.90
13	11	5	10	1	99.90
14	12	9	50	1	42.90
15	12	10	54	1	52.90
16	12	11	58	1	38.90
17	13	26	94	1	38.90
18	13	28	99	1	26.90
19	13	29	101	1	44.90
20	13	30	103	1	39.90
21	13	36	115	1	38.00
22	13	35	111	1	40.00
23	14	36	115	1	38.00
24	15	42	125	1	99.90
25	15	44	129	1	42.90
26	15	46	134	1	119.90
27	16	59	149	1	42.90
28	16	60	151	1	30.00
29	16	61	152	1	44.90
30	16	63	156	1	89.90
31	17	55	143	1	34.90
32	18	59	150	1	42.90
\.


--
-- Data for Name: pedidos; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.pedidos (id, usuario_id, endereco_id, cupom_id, status, subtotal, desconto, frete, total, recebido_em, criado_em) FROM stdin;
2	6	2	\N	pendente	214.30	0.00	0.00	214.30	\N	2026-10-02 16:20:41.261056
7	7	3	\N	pendente	134.90	0.00	19.90	154.80	\N	2026-10-14 14:13:37.704718
8	8	4	\N	pendente	127.70	0.00	19.90	147.60	\N	2026-10-14 16:31:04.928194
9	8	4	\N	pendente	134.90	0.00	19.90	154.80	\N	2026-10-14 16:33:14.973704
10	1	5	\N	pendente	29.90	0.00	19.90	49.80	\N	2026-10-21 08:12:50.597927
11	1	5	\N	pendente	144.80	0.00	19.90	164.70	\N	2026-10-21 08:13:34.677222
12	42	6	\N	pendente	134.70	0.00	19.90	154.60	\N	2026-10-21 08:29:55.988001
13	43	7	\N	pendente	228.60	0.00	0.00	228.60	\N	2026-10-21 10:54:52.761687
14	43	7	\N	pendente	38.00	0.00	19.90	57.90	\N	2026-10-21 10:59:52.657249
15	44	8	\N	pendente	262.70	0.00	0.00	262.70	\N	2026-10-21 13:49:05.170047
16	42	6	\N	pendente	207.70	0.00	0.00	207.70	\N	2026-10-21 14:50:28.634606
17	45	9	\N	pendente	34.90	0.00	19.90	54.80	\N	2026-10-21 16:36:58.390708
18	47	10	\N	pendente	42.90	0.00	19.90	62.80	\N	2026-10-21 16:38:43.097704
\.


--
-- Data for Name: produto_variacoes; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.produto_variacoes (id, produto_id, cor_tom, sku, estoque, preco_extra, criado_em) FROM stdin;
1	1	Rosé Suave	BATOMM-1	5	0.00	2026-10-02 15:36:04.072517
4	2	Pêssego	GLOSSL-2	12	5.00	2026-10-02 15:36:04.098002
5	3	Volume Preto	MSCARA-1	5	0.00	2026-10-02 15:36:04.11309
6	3	Volume Marrom	MSCARA-2	12	5.00	2026-10-02 15:36:04.117362
7	4	Preto Intenso	DELINE-1	5	0.00	2026-10-02 15:36:04.131271
9	5	Bege Claro	BASEGL-1	5	0.00	2026-10-02 15:36:04.155002
11	6	Pêssego Glow	BLUSHC-1	5	0.00	2026-10-02 15:36:04.174196
15	8	Kit Completo	KITPIN-1	5	0.00	2026-10-02 15:36:04.210383
88	23	Médio Quente	\N	35	0.00	2026-10-21 09:43:28.109833
3	2	Nude Rosado	GLOSSL-1	5	0.00	2026-10-02 15:36:04.093387
89	23	Tan Neutro	\N	20	0.00	2026-10-21 09:43:28.109833
90	23	Escuro Profundo	\N	25	0.00	2026-10-21 09:43:28.109833
85	22	Champagne Gold	\N	45	0.00	2026-10-21 09:14:31.623858
86	22	Rose Quartz	\N	40	0.00	2026-10-21 09:14:31.623858
84	21	Intense Black	\N	65	0.00	2026-10-21 09:12:31.884707
82	20	Pretíssimo	\N	60	0.00	2026-10-21 09:11:06.161684
83	20	Marrom Deep	\N	30	0.00	2026-10-21 09:11:06.161684
80	19	Warm Terracota	\N	40	0.00	2026-10-21 09:09:03.592722
81	19	Nude Essential	\N	45	0.00	2026-10-21 09:09:03.592722
127	43	Rosa Metalizado	\N	40	0.00	2026-10-21 13:33:35.650021
95	26	Sun Pearl	\N	35	0.00	2026-10-21 09:46:55.639082
96	27	Nude Rose	\N	40	0.00	2026-10-21 10:00:32.172098
97	27	Malva Real	\N	30	0.00	2026-10-21 10:00:32.172098
98	27	Vermelho Diva	\N	35	0.00	2026-10-21 10:00:32.172098
13	7	Rosé Gold	ESPELH-1	5	0.00	2026-10-02 15:36:04.193467
100	28	Coral Peach	\N	38	0.00	2026-10-21 10:02:10.066603
17	5	nude 02	\N	2	100.00	2026-10-14 16:17:38.279599
18	5	nude 02	base bruna tavares	5	100.00	2026-10-14 16:18:07.45415
8	4	Marrom Café	DELINE-2	11	5.00	2026-10-02 15:36:04.136986
12	6	Rosa Antigo	BLUSHC-2	11	5.00	2026-10-02 15:36:04.178165
16	8	Kit Viagem	KITPIN-2	10	5.00	2026-10-02 15:36:04.218872
14	7	Champagne	ESPELH-2	10	5.00	2026-10-02 15:36:04.19693
2	1	Vermelho Clássico	BATOMM-2	11	5.00	2026-10-02 15:36:04.078128
10	5	Bege Médio	BASEGL-2	11	5.00	2026-10-02 15:36:04.158252
51	9	Pink Crystal	\N	20	0.00	2026-10-21 08:18:04.070006
52	9	Coral Glow	\N	15	0.00	2026-10-21 08:18:04.070006
53	9	Red Ruby	\N	18	0.00	2026-10-21 08:18:04.070006
55	10	Rose Gold	\N	18	0.00	2026-10-21 08:22:04.405672
56	10	Bronze Sun	\N	10	0.00	2026-10-21 08:22:04.405672
57	11	Stardust Gold	\N	20	0.00	2026-10-21 08:24:59.471784
59	11	Metallic Bronze	\N	15	0.00	2026-10-21 08:24:59.471784
60	11	Galaxy Silver	\N	12	0.00	2026-10-21 08:24:59.471784
61	10	nude 02	\N	0	0.00	2026-10-21 08:25:26.908453
50	9	Nude Rose	\N	24	0.00	2026-10-21 08:18:04.070006
54	10	Champagne Gold	\N	21	0.00	2026-10-21 08:22:04.405672
58	11	Rose Quartz	\N	24	0.00	2026-10-21 08:24:59.471784
62	12	Velvet Rose	\N	50	0.00	2026-10-21 08:33:48.293258
63	12	Nude Terracota	\N	30	0.00	2026-10-21 08:33:48.293258
64	12	Red Classic	\N	35	0.00	2026-10-21 08:33:48.293258
65	13	Crystal Shine	\N	40	0.00	2026-10-21 08:39:09.863629
66	13	Pink Diamond	\N	35	0.00	2026-10-21 08:39:09.863629
67	13	Peach Sparkle	\N	25	0.00	2026-10-21 08:39:09.863629
68	14	Prata Holográfico	\N	35	0.00	2026-10-21 08:45:08.168179
69	14	Rosa Gold	\N	30	0.00	2026-10-21 08:45:08.168179
72	16	Pink Gold	\N	40	0.00	2026-10-21 08:50:11.76788
73	16	Preto Matte	\N	35	0.00	2026-10-21 08:50:11.76788
74	17	Rosa Chiclete	\N	60	0.00	2026-10-21 08:53:21.470656
75	17	Branco Neve	\N	40	0.00	2026-10-21 08:53:21.470656
76	17	Lilás Soft	\N	45	0.00	2026-10-21 08:53:21.470656
77	17	Rosa Bebe	\N	35	0.00	2026-10-21 08:53:21.470656
78	18	Coração Pink	\N	50	0.00	2026-10-21 08:55:16.437315
79	18	Coração Lilás	\N	49	0.00	2026-10-21 08:55:16.437315
70	15	Gota Rosa	\N	50	0.00	2026-10-21 08:47:43.336248
71	15	Chanfrada Roxa	\N	50	0.00	2026-10-21 08:47:43.336248
128	43	Preto  Elegance	\N	30	0.00	2026-10-21 13:33:35.650021
111	35	Red Viny	\N	39	0.00	2026-10-21 10:48:04.086966
93	25	Incolor	\N	35	0.00	2026-10-21 09:45:52.031825
91	24	Translucido Universal	\N	50	0.00	2026-10-21 09:44:43.487351
92	24	Translucido Banana	\N	40	0.00	2026-10-21 09:44:43.487351
87	23	Claro Neutro	\N	30	0.00	2026-10-21 09:43:28.109833
102	29	Translúcido Banana	\N	40	0.00	2026-10-21 10:04:04.556016
104	30	Glow Rose Gold	\N	35	0.00	2026-10-21 10:05:34.040015
105	31	Transparente	\N	50	0.00	2026-10-21 10:07:06.087591
106	32	Cristal Diamond	\N	40	0.00	2026-10-21 10:10:34.96683
107	32	Golden Hour	\N	35	0.00	2026-10-21 10:10:34.96683
108	33	Inox Silver	\N	30	0.00	2026-10-21 10:12:01.320589
109	34	Roxo	\N	50	0.00	2026-10-21 10:13:42.982464
110	34	Rosa Bebê	\N	30	0.00	2026-10-21 10:13:42.982464
114	36	Strawberry Jelly	\N	30	0.00	2026-10-21 10:50:08.753661
112	35	Nude Glossy	\N	40	0.00	2026-10-21 10:48:04.086966
113	35	Berry Shine	\N	30	0.00	2026-10-21 10:48:04.086966
94	26	Rose Gold	\N	39	0.00	2026-10-21 09:46:55.639082
99	28	Red Cherry	\N	49	0.00	2026-10-21 10:02:10.066603
101	29	Translúcido Neutro	\N	59	0.00	2026-10-21 10:04:04.556016
103	30	Glow Dourado	\N	34	0.00	2026-10-21 10:05:34.040015
115	36	Vanilla Cream	\N	43	0.00	2026-10-21 10:50:08.753661
116	37	Gold Glow	\N	40	0.00	2026-10-21 11:09:57.763189
117	37	Rose Gold	\N	35	0.00	2026-10-21 11:09:57.763189
118	38	Sweet Vanilla	\N	50	0.00	2026-10-21 11:11:14.340833
119	38	Pink Cconut	\N	45	0.00	2026-10-21 11:11:14.340833
120	39	Bronze Perfeito	\N	30	0.00	2026-10-21 11:12:25.241008
121	39	Perla Radiante	\N	35	0.00	2026-10-21 11:12:25.241008
123	41	merengue	\N	40	0.00	2026-10-21 11:15:35.187481
124	41	frutas vermelhas	\N	45	0.00	2026-10-21 11:15:35.187481
122	40	Bronze Dourado	\N	40	0.00	2026-10-21 11:14:08.779978
126	42	Edição Dourado Sol	\N	20	0.00	2026-10-21 13:31:16.3305
130	44	Combo Nude	\N	35	0.00	2026-10-21 13:35:44.697788
131	45	Kit Primicia	\N	30	0.00	2026-10-21 13:38:43.986456
132	45	Kit Rosa Chiclete	\N	25	0.00	2026-10-21 13:38:43.986456
133	45	Kit Lilás Soft	\N	30	0.00	2026-10-21 13:38:43.986456
125	42	Edição Rose Gold	\N	29	0.00	2026-10-21 13:31:16.3305
129	44	Combo Vermelho	\N	44	0.00	2026-10-21 13:35:44.697788
134	46	Mystery Box Standard	\N	19	0.00	2026-10-21 13:40:52.068204
135	47	Padrão (30ml)	\N	40	0.00	2026-10-21 13:54:17.82068
136	48	Padrão (120ml)	\N	50	0.00	2026-10-21 13:56:21.305275
137	49	Padrão (15g)	\N	35	0.00	2026-10-21 14:00:26.148144
139	51	Padrão (200ml)	\N	60	0.00	2026-10-21 14:03:51.040113
140	52	Padrão (100g)	\N	47	0.00	2026-10-21 14:05:37.395218
141	53	Padrão (110ml)	\N	40	0.00	2026-10-21 14:11:35.015122
142	54	Padrão (10g)	\N	35	0.00	2026-10-21 14:13:44.833079
144	56	Baunilha	\N	50	0.00	2026-10-21 14:19:47.901406
145	56	Morango	\N	50	0.00	2026-10-21 14:19:47.901406
146	57	Diamond Silver	\N	35	0.00	2026-10-21 14:23:41.956557
147	57	Diamond Bronze	\N	35	0.00	2026-10-21 14:23:41.956557
148	58	Glow Champagne	\N	30	0.00	2026-10-21 14:26:40.346407
153	61	Kit Nude Chic	\N	30	0.00	2026-10-21 14:35:31.980957
154	62	Nude	\N	30	0.00	2026-10-21 14:40:02.705592
155	62	Pretíssimo	\N	39	0.00	2026-10-21 14:40:02.705592
157	64	Edição Pocket Pink	\N	45	0.00	2026-10-21 14:46:51.145741
149	59	Floral Bloom	\N	39	0.00	2026-10-21 14:28:40.014763
151	60	Padrão (100ml)	\N	49	0.00	2026-10-21 14:31:32.416029
152	61	Kit Rose Elegance	\N	34	0.00	2026-10-21 14:35:31.980957
156	63	Edição Pink Care	\N	24	0.00	2026-10-21 14:43:33.75637
143	55	Padrão (150g)	\N	44	0.00	2026-10-21 14:16:43.543662
150	59	Sweet Vanilla	\N	39	0.00	2026-10-21 14:28:40.014763
\.


--
-- Data for Name: produtos; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.produtos (id, categoria_id, nome, marca, descricao, preco, preco_antigo, imagem_url, ativo, criado_em) FROM stdin;
1	1	Batom Mate Hidratante	Beleza Viva	Acabamento mate confort com óleo de rosa mosqueta.	39.90	49.90	/assets/img/batom.jpg	t	2026-10-02 15:36:04.055353
2	1	Gloss Labial Ácido Hialurônico	Pink Glow	Brilho espelhado e hidratação por 12h.	45.90	\N	/assets/img/iluminador.webp	t	2026-10-02 15:36:04.089348
3	2	Máscara de Volume Luxe	GlowLab	Cerdas anatômicas para volume intenso.	58.00	69.90	/assets/img/mascara.png	t	2026-10-02 15:36:04.109114
4	2	Delineador Caneta Preto Intenso	Beleza Viva	Ponta fina de precisão e secagem rápida.	32.90	\N	/assets/img/paleta-sombra.jpg	t	2026-10-02 15:36:04.127402
5	3	Base Glow Segunda Pele FPS 35	Pink Glow	Cobertura leve com efeito viçoso.	94.90	\N	/assets/img/modelo-maquiagem.jpg	t	2026-10-02 15:36:04.150564
6	3	Blush Cremoso Soft Radiance	GlowLab	Rubor natural de longa duração.	54.90	68.90	/assets/img/blush.png	t	2026-10-02 15:36:04.170083
7	4	Espelho de Bolso com LED	Beleza Viva	Iluminação suave para retoques.	24.90	\N	/assets/img/kit-maquiagem.jpg	t	2026-10-02 15:36:04.189346
8	4	Kit Pincéis Profissional	Pink Glow	12 pincéis sintéticos de alta performance.	129.90	149.90	/assets/img/kit-pinceis.jpg	t	2026-10-02 15:36:04.206949
9	1	Gloss Labial Efeito Plump	Pink Glow	Glow Volume	42.90	54.90	https://encrypted-tbn1.gstatic.com/shopping?q=tbn:ANd9GcTlpSe-MB-mbv-QVwMbG7k0BlvMbDRBRsuSa__3ZM5W6bSfIf572QWmLvSRiGmXuqKzsyUUrb276lQpdx-4034BTNGCuDBwZHDHzQOPH-whq8P2h5qo32QcDVk	t	2026-10-21 08:18:04.070006
10	3	Iluminador Líquido	Pink Glow	Glow Sunkissed	52.90	68.00	https://m.media-amazon.com/images/I/71+VDoV5OFL.jpg	t	2026-10-21 08:22:04.405672
11	2	Sombra Líquida Multidimensional	Pink Glow	Cosmic Shine	38.90	49.90	https://images.tcdn.com.br/img/img_prod/918448/sombra_lquida_mousse_de_glitter_2_20260924162742_08c75f8695dc.png	t	2026-10-21 08:24:59.471784
12	1	Batom Matte Velvet Sunset	Pink Glow	Batom líquido matte de alta fixação e textura aveludada,não resseca os lábios, entrega cor intensa na primeira aplicação e possui cheirinho suave de baunilha.	39.90	49.90	https://www.dailus.com.br/cdn/shop/files/800x915---SITE---BOCA---PAULA---LIQUIDO---NUDE---APLICADOR_3ab429de-3fa1-4416-bca1-caeac92c7a3f.png?v=1754079215&width=1445	t	2026-10-21 08:33:48.293258
13	1	Gloss Labial Glossy Glow Hydra	Pink Glow	Gloss labial com efeito espelhado de brilho reluzente, enriquecido com Ácido Hialurônico e Óleo de Jojoba para manter os lábios nutridos e hidratados.	34.90	49.90	https://assets.sistemawbuy.com.br/arquivos/cad5cc7133685cdd8d0d7880d11cba96/produtos/697d189ff3f1a/cor-2-2-6a0dc5804103b_mini.jpg	t	2026-10-21 08:39:09.863629
14	4	Kit de Pincéis Essential Glow (5 Peças)	Pink Glow	Conjunto completo com 5 pincéis sintéticos de cerdas ultra macias e cabo anatômico na cor rosa metalizado, ideal para aplicação de pó, blush, iluminador e esfumado perfeito dos olhos.	59.90	79.90	https://img.ltwebstatic.com/v4/j/spmp/2025/12/25/e1/1766655870aec75219bd106e11e2dac81f6dee7ede_thumbnail_750x999.jpg	t	2026-10-21 08:45:08.168179
16	4	Curvador de Cílios com Refil de Silicone Precision	Pink Glow	Modelador e curvador de cílios em aço inoxidável com curvatura anatômica que não belisca a pálpebra. Acompanha 2 refis de silicone extra macios.	24.90	29.90	https://mebabo.cdn.magazord.com.br/img/2020/07/produto/9167/cursvexrosa.png?ims=400x400	t	2026-10-21 08:50:11.76788
17	4	Faixa de Cabelo para Skincare & Maquiagem Glow Bow	Pink Glow	Tiara/faixa atoalhada ajustável com laço removível,protege os fios de cabelo e segura a franja durante a rotina de skincare, limpeza de pele ou aplicação de maquiagem.	18.00	20.00	https://m.media-amazon.com/images/I/51SC8MBMQvS._AC_UF1000,1000_QL80_.jpg	t	2026-10-21 08:53:21.470656
18	4	Higienizador de Pincéis em Silicone Heart Clean	Pink Glow	Tapete higienizador em silicone flexível com textura multidimensional para lavar e remover o acúmulo de maquiagem das cerdas de pincéis sem danificá-los.	15.90	\N	https://http2.mlstatic.com/D_Q_NP_2X_876609-MLB117681760191_092026-P.webp	t	2026-10-21 08:55:16.437315
15	4	Esponja de Maquiagem Velvet Blend	Pink Glow	Esponja multifuncional livre de látex com acabamento aveludado, aumenta de tamanho quando molhada e proporciona cobertura uniforme com efeito airbrush na aplicação de bases e corretivos.	19.90	21.90	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRKQF5KRCbIU1xSvfyUK9yKdB2MSnP9rnGAM_7Kd-0vx-U3i3ub4JLeloZi&s=10	t	2026-10-21 08:47:43.336248
48	7	Gel de Limpeza Facial Purifying Gel Cleanser	Primicia	Gel higienizador facial de uso diário enriquecido com Ácido Salicílico e Extrato de Camomila. Limpa os poros profundamente, controla a oleosidade e remove impurezas sem ressecar a pele.	49.90	\N	https://http2.mlstatic.com/D_Q_NP_2X_638035-MLB114821291950_082026-P.webp	t	2026-10-21 13:56:21.305275
25	3	Primer Facial Poreless & Hydra	Ruby Rose	Primer em gel com infusão de Niacinamida,suaviza os poros visíveis, uniformiza a textura e prolonga a duração da maquiagem mantendo o viço natural.	49.50	\N	https://static.cdnlive.com.br/uploads/724/produto/17437059011264_zoom.jpg	t	2026-10-21 09:45:52.031825
24	3	Pó Compacto Aveludado HD Touch	Bruna Tavares	Pó facial de textura ultrafina que sela a maquiagem, controla a oleosidade e cria um efeito blur suave na pele sem estourar no flash.	46.90	\N	https://http2.mlstatic.com/D_Q_NP_2X_754946-MLB90365411727_082025-P-po-facial-bruna-tavares-bt-skinpowder.webp	t	2026-10-21 09:44:43.487351
23	3	Corretivo Líquido Velvet Cover	Mari Maria	Corretivo de média a alta cobertura com acabamento soft matte,enriquecido com Ácido Hialurônico e Extrato de Camomila, disfarça imperfeições e olheiras sem acumular nas linhas finas.	42.90	\N	https://assets.sistemawbuy.com.br/arquivos/cad5cc7133685cdd8d0d7880d11cba96/produtos/66929d4400251/corretivo-cover-up-mari-maria-15-z1qd90lkqn-66929da0a4b3b.jpg	t	2026-10-21 09:43:28.109833
22	2	Sombra Líquida Glitter Glow	Pink Glow	Sombra líquida radiante com micropartículas de brilho reluzente, modulável, seca rapidamente e fixa por até 12 horas sem craquelar.	28.90	\N	https://m.media-amazon.com/images/I/61taIQQAg9L._AC_UF1000,1000_QL80_.jpg	t	2026-10-21 09:14:31.623858
21	2	Máscara de Cílios Lash Lift & Volume	Pink Glow	Máscara com aplicador de silicone de cerdas assimétricas que curvem, definem e curvam cada cílio desde a raiz sem empelotar.	39.90	\N	https://http2.mlstatic.com/D_Q_NP_2X_938995-MLB115304748834_082026-P.webp	t	2026-10-21 09:12:31.884707
20	2	Delineador Líquido Precision Pen	Pink Glow	Delineador em caneta com ponta ultra fina de feltro para traços precisos e firmes,resistente à água, secagem rápida e tom preto de altíssima pigmentação.	32.90	\N	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQQvfhxsftj_vwuNMEzx-FCqvi4YG0VYcmawtmpv79H9g&s	t	2026-10-21 09:11:06.161684
19	2	Paleta de Sombras Sunset Vibes	Pink Glow	Paleta compacta com 9 tons altamente pigmentados, variando entre acabamentos matte aveludado e cintilantes,fórmulas de fácil esfumado que não esfarelam nem mancham.	59.90	74.90	https://i.ebayimg.com/images/g/NzcAAeSwav5qM0YZ/s-l1200.webp	t	2026-10-21 09:09:03.592722
26	3	Iluminador em Bastão Stick Glow	Pink Glow	Iluminador multifuncional cremoso em formato stick,fácil de aplicar e espalhar, entrega um brilho molhado e acetinado imediato à pele.	38.90	\N	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTp2vfdXmEO8f4X4xoUUZme_Ouy90U6KjbD_cf8IoPSsLhAEUqCX5ngLnk&s=10	t	2026-10-21 09:46:55.639082
27	1	Lápis de Boca Outline Precision	Pink Glow	Lápis labial de alta pigmentação com textura macia e cremosa que desliza facilmente,garante um contorno preciso, previne o borramento do batom e aumenta a fixação da maquiagem nos lábios.	22.90	\N	https://images.tcdn.com.br/img/img_prod/780525/lapis_duo_para_contorno_labial_matte_luisance_4063_1_b004f06a7e208150a6a5a0e4421189ec.png	t	2026-10-21 10:00:32.172098
28	1	Lip Tint Tinted Juice	Pink Glow	Tinta labial multifuncional de longa duração com fragrância suave de frutas,deixa um aspecto saudável e natural aos lábios e bochechas, sem secar ou craquelar.	26.90	\N	https://media.sephora.eu/content/dam/gdam/europe/digital/pim/published/S/SEPHORA_COLLECTION/722236/353190-media_swatch.jpg?scaleWidth=750&scaleHeight=750&scaleMode=fit	t	2026-10-21 10:02:10.066603
29	3	Pó Solto Translucido Microfinish HD	Playboy	Pó facial solto com micronização ultra fina que sela o corretivo e a base sem adicionar peso ou textura,controla o brilho excessivo ao longo do dia e proporciona efeito soft focus.	44.90	\N	https://http2.mlstatic.com/D_NQ_NP_758280-MLB107081725129_022026-O-po-solto-translucido-photo-microfinish-powder-playboy.webp	t	2026-10-21 10:04:04.556016
30	3	Bruma Fixadora & Hidratante Glow Mist	Ruby Rose	Bruma facial multifuncional que fixa a maquiagem, hidrata e devolve o viço natural da pele,enriquecida com Extrato de Aloe Vera, Camomila e partículas de brilho pérola	39.90	49.90	https://cdn.awsli.com.br/800x800/1007/1007996/produto/48612908f4c2de0e17.jpg	t	2026-10-21 10:05:34.040015
31	2	Cera Fixadora de Sobrancelhas Brow Lamination Effect	Pink Glow	Pasta/cera modeladora transparente de alta fixação para alinhar e modelar os fios da sobrancelha no estilo brow lamination. Não deixa resíduos brancos nem endurece em excesso.	27.90	\N	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRDF_hMcGALxUAJmyXNNkwJwpSoRoYkftepaZZ6BHDDLl-x_cKMgY9ufWY&s=10	t	2026-10-21 10:07:06.087591
32	2	Pigmento Iluminador Star	Pink Glow	Pigmento solto ultra brilhante com reflexos multidimensionais. Pode ser aplicado diretamente na pálpebra, no canto interno dos olhos ou como ponto de luz na maquiagem.	24.90	\N	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSZtRf-ltie4L9O4SlOo1YjT5Yi0MB46-D5f7oN9_k6BqSsyIdqwGTurZPD&s=10	t	2026-10-21 10:10:34.96683
33	4	Placa e Espátula em Aço Inox para Mistura	Pink Glow	Conjunto em aço inoxidável cirúrgico, ideal para misturar bases, corretivos, batons ou diluidores com higiene e praticidade sem desperdiçar produtos.	31.90	\N	https://cdn.awsli.com.br/600x700/2380/2380139/produto/194755448/kit-ac-03-placa-e-espatula-para-maquiagem-macrilan-1-b533ae9d0a.jpg	t	2026-10-21 10:12:01.320589
34	4	Mini Toalha Demaquilante Reutilizável Eco Magic	Pink Glow	Toalha em microfibra macia que remove toda a maquiagem utilizando apenas água morna, sem necessidade de sabonetes agressivos. Reutilizável e ecologicamente correta.	16.90	\N	https://http2.mlstatic.com/D_NQ_NP_899200-MLB115155896871_072026-O-toalha-magica-removedor-maquiagem-make-off-klass-vough.webp	t	2026-10-21 10:13:42.982464
36	1	Mascara Labial Noturna Jelly Lip Mask	Pink Glow	Tratamento labial intensivo noturno com textura gel-bálsamo. Enriquecida com Mantega de Karité e Extrato de Frutas Vermelhas, restaura, previne o ressecamento e renova a pele dos lábios enquanto você dorme.	38.00	45.90	https://http2.mlstatic.com/D_Q_NP_2X_693616-CBT117356142053_092026-P.webp	t	2026-10-21 10:50:08.753661
35	1	Batom Líquido Vinil High Shine	Bruna Tavares	Batom líquido de altíssima pigmentação com efeito vinil super espelhado que não gruda. Proporciona cor intensa na primeira camada, lábios visivelmente mais volumosos e hidratação prolongada.	40.00	\N	https://http2.mlstatic.com/D_NQ_NP_985008-MLA87123473580_072025-O.webp	t	2026-10-21 10:48:04.086966
37	6	Óleo Iluminador Corporal Body Shimmer Golden Hour	Pink Glow	Óleo seco iluminador corporal de rápida absorção que não deixa sensação pegajosa. Enriquecido com Óleo de Amêndoas Doces e micropartículas de brilho dourado, hidrata profundamente enquanto garante um bronzeado radiante.	54.90	69.90	https://http2.mlstatic.com/D_NQ_NP_716180-CBT115749634209_082026-O.webp	t	2026-10-21 11:09:57.763189
38	6	Spray Iluminador de Corpo e Cabelo Body & Hair Mist Sparkle	Pink Glow	pray perfumado multifuncional com brilho prateado e rosado para corpo e cabelos. Deixa um perfume floral suave e fixação duradoura de micropartículas cintilantes.	39.90	\N	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSXL2rPNXJsPKKfwF-Z4WWV6yLTAHGMRIaK7LzxPCZbNslc3FB-43dJrVo&s=10	t	2026-10-21 11:11:14.340833
39	6	Loção Hidratante Iluminadora Glow Lotion Soft Silk	Pink Glow	Loção corporal hidratante com efeito blur iluminador que disfarça pequenas imperfeições da pele. Deixa um toque aveludado e um brilho acetinado e natural.	42.90	\N	https://www.drogasil.com.br/_next/image?url=https%3A%2F%2Fproduct-data.raiadrogasil.io%2Fimages%2F14006821.webp&w=3840&q=40	t	2026-10-21 11:12:25.241008
41	6	Esfoliante Corporal Iluminador Sugar Scrub Pink Berry	Pink Glow	Esfoliante corporal à base de cristais de açúcar e óleos nutritivos. Remove as células mortas, ativa a circulação e prepara a pele para receber hidratação e iluminação.	36.90	\N	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTUBlUMyvR_qSchDXDJ18yMLz6rWf3hyLfZaj29FEnUlA&s	t	2026-10-21 11:15:35.187481
40	6	Gel Iluminador Corporal Sun Kissed Bronzer	Sheglam	Gel tonalizante e iluminador de secagem rápida. Dá um efeito bronzeado instantâneo "carinha de praia" sem necessidade de exposição ao sol e sai facilmente no banho.	48.00	\N	https://img.ltwebstatic.com/v4/j/dps/PS202608311260/931fccf31b43fda8b3d95a6bee631af088c0d7957df6fe00eac748f4d60a1d62.jpg	t	2026-10-21 11:14:08.779978
42	5	Kit Presentável Glow Queen (Edição Especial)	Giovanna Baby		99.90	\N	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSlBuyWtPAmcHVRCoGsa9K7EMKEiiaF-f-jHFQ4jLXjxBog9AnW2lsAr59Z&s=10	t	2026-10-21 13:31:16.3305
43	5	Kit Essencial Pincéis & Necessaire Pink Travel	Pink Glow	Conjunto para transporte e aplicação com 20 pincéis essenciais de maquiagem.	90.00	\N	https://http2.mlstatic.com/D_NQ_NP_937699-CBT108233294195_032026-O.webp	t	2026-10-21 13:33:35.650021
44	5	Kit Duo Lábios Perfeitos (Batom Matte + Lápis de Boca)	Duo Lip	Combo para lábios definidos e duradouros. Acompanha 1 Lápis de Boca 1 Batom Líquido Matte em tons perfeitamente combinados.	42.90	\N	https://images.tcdn.com.br/img/img_prod/780525/duo_lip_lapis_labial_e_batom_em_bala_vivai_7445_1_9bf4ef0671b2adbd8626d91f5a71f74a.jpeg	t	2026-10-21 13:35:44.697788
45	5	Kit Skincare Pre-Makeup Prep & Hydrate	Primicia		49.90	\N	https://http2.mlstatic.com/D_Q_NP_2X_623712-MLA99399240295_112025-P.webp	t	2026-10-21 13:38:43.986456
46	5	Caixa Surpresa Mystery Glow Box (5 Itens)	Pink Glow	Uma experiência de presente misteriosa! A caixa contém 5 produtos sortidos de tamanho regular da marca Pink Glow, garantindo um valor total superior ao pago.	119.90	160.00	https://down-br.img.susercontent.com/file/br-11134201-820le-mtmes4jfqww06a	t	2026-10-21 13:40:52.068204
47	7	Sérum Facial Hidratante	Hydra Glam	Sérum facial de textura leve e rápida absorção com alta concentração de Ácido Hialurônico,preenche as linhas finas, promove hidratação profunda e prepara a pele para uma maquiagem impecável.	49.90	59.90	https://storage.googleapis.com/b4a-shop-products/7908239719874/images/7908239719874_3.webp	t	2026-10-21 13:54:17.82068
49	7	Creme Hidratante para Olhos	Creamy	Creme nutritivo para a área dos olhos enriquecido com Cafeína e Niacinamida. Auxilia na redução de bolsas, atenua olheiras e hidrata a região para evitar que o corretivo craquele ao longo do dia	50.00	\N	https://drogariaspacheco.vteximg.com.br/arquivos/ids/2543279-1000-1000/942650-4.jpg?v=639210425113300000	t	2026-10-21 14:00:26.148144
51	7	Água Micelar Demaquilante 5 em 1 Pure Clean	Loreal	Solução micelar multifuncional que limpa, demaquila, purifica, suaviza e reequilibra a pele. Remove maquiagem à prova d'água sem esforço e sem necessidade de enxágue.	29.90	\N	https://m.media-amazon.com/images/I/81a+9alrNTL._AC_UF1000,1000_QL80_.jpg	t	2026-10-21 14:03:51.040113
52	7	Máscara Facial de Argila Rosa Detox & Glow	Pink Glow	Máscara de tratamento à base de argila rosa e óleo de rosa mosqueta. Remove toxinas, reduz o tamanho dos poros e devolve o viço e a luminosidade natural da pele.	36.90	\N	https://kakicosmetics.com/cdn/shop/files/mascara-argila-rosa-aplicado-rosto-2048x2048.webp?v=1773765500&width=1100	t	2026-10-21 14:05:37.395218
53	7	Tônico Facial	Eudora	Tônico suave sem álcool que reequilibra o ph da pele, desobstrui os poros e ajuda no controle da oleosidade sem ressecar. Prepara o rosto para receber a hidratação e a maquiagem.	32.90	\N	https://www.drogaraia.com.br/_next/image?url=https%3A%2F%2Fproduct-data.raiadrogasil.io%2Fimages%2F19194116.webp&w=3840&q=40	t	2026-10-21 14:11:35.015122
54	7	Balm Hidratante de Olhos	Pink Glow	Bastão hidratante com efeito gelado instantâneo. Desinchá a região dos olhos, atenua olheiras e evita que o corretivo acumule ou craquele nas linhas de expressão.	37.90	\N	https://m.media-amazon.com/images/I/71ndor4X8QL.jpg	t	2026-10-21 14:13:44.833079
55	7	Esfoliante Facial	Labotrat	Esfoliante facial suave extrato de melancia,remove células mortas, uniformiza a textura da pele e devolve o viço imediato.	34.90	\N	https://www.drogaraia.com.br/_next/image?url=https%3A%2F%2Fproduct-data.raiadrogasil.io%2Fimages%2F5138296.webp&w=3840&q=40	t	2026-10-21 14:16:43.543662
56	7	Manteiga Labial Repair	Pink Glow	Balm nutritivo ultra concentrado com manteiga de cacau e vitamina E. Restaura lábios ressecados, previne descamações e deixa a superfície lisa para aplicação de batons.	21.90	\N	https://http2.mlstatic.com/D_Q_NP_2X_768366-CBT116586796551_082026-P.webp	t	2026-10-21 14:19:47.901406
57	6	Iluminador Corporal em Spray	Pink Glow	Spray corporal ultra fino que borrifa uma névoa perfumada de brilho intenso e imediato. Não deixa toque gorduroso e seca rapidamente na pele.	45.90	\N	https://encrypted-tbn3.gstatic.com/shopping?q=tbn:ANd9GcRCJ83M2-_xltvtvPqedr3RlgIOTUzOgYMeU-vzipMFOn8p5OVueg1tJjiq0wvRC6tlvpmy895OwttDv6qHYAqxnxea_6yHqjzDWC1oUfaSuALftfkbRduw	t	2026-10-21 14:23:41.956557
58	6	Manteiga Corporal Iluminadora	Pink Glow	Manteiga hidratante corporal de alta nutrição com reflexos dourados e delicados,proporciona toque aveludado e brilho sofisticado por horas.	52.00	\N	https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQx62vlrV31DhcPOEn2YjCswbrbZ8-Ps2u3IO_YTWeA8vUkdH_tpwOkjco&s=10	t	2026-10-21 14:26:40.346407
59	6	Perfume Capilar & Corporal	Pink Glow	Fragrância leve com notas florais e partículas cintilantes,perfuma e doa brilho radiante tanto para os cabelos quanto para o corpo ao longo do dia.	42.90	\N	https://www.thebarboursbeauty.com.br/cdn/shop/files/Kit_Body_Splash_Perfume_de_Cabelo_My_Sweet_Delight_2.png?v=1778008121	t	2026-10-21 14:28:40.014763
60	6	Sabonete Liquido & Iluminador Golden	Golden	Sabonete líquido corporal com fórmula suave e partículas de brilho reluzente,limpa a pele delicadamente enquanto deixa um véu sutil de luminosidade e perfume agradável.	30.00	\N	https://www.cosmeticosatacado.com/cdn/shop/files/ImagemprodutoBioCosmeticos_d51e05d4-9c01-416d-8c37-0f48f0e7d681.png?v=1789060800	t	2026-10-21 14:31:32.416029
61	5	Kit  Mini Pillow Talk lip	Pink Glow	Kit presenteável contendo 1 Lápis de Boca e 1 Gloss Hydra,a combinação ideal para desenhar, preencher e dar acabamento espelhado aos lábios.	44.90	64.90	https://images.tcdn.com.br/img/img_prod/630287/kit_mini_pillow_talk_lip_charlotte_tilbery_1_20260410124236_3e835544a857.jpg	t	2026-10-21 14:35:31.980957
62	5	Kit 2 Delineador + Cera de Sobrancelha	Pink Glow	Kit essencial para destacar o olhar, acompanha 2 delineador em caneta e um com o lado da Cera para a sobrancelha.	49.90	60.80	https://http2.mlstatic.com/D_NQ_NP_890800-CBT106009568858_022026-O-kit-2-delineador-de-cejas-e-cera-para-modelar-e-ficar-firme.webp	t	2026-10-21 14:40:02.705592
63	7	Kit Spa Day	Pink Glow	O kit perfeito para um momento de autocuidado.	89.90	102.80	https://http2.mlstatic.com/D_Q_NP_2X_736081-MLB89951270869_082025-P.webp	t	2026-10-21 14:43:33.75637
64	7	Mini Kit Travel Glow (4 minis)	Pink Glow	Kit viagem completo com itens em formato travel size, perfeitos para levar na bolsa.	59.90	\N	https://acdn-us.mitiendanube.com/stores/002/400/460/products/kit-sweet-travel-skin-minis-skincare-antiacne-glow-1507deedbbd978919c17858624475728-1024-1024.jpg	t	2026-10-21 14:46:51.145741
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: pinkglow
--

COPY public.usuarios (id, nome, email, senha_hash, tipo, telefone, criado_em) FROM stdin;
2	t	t__820ad02bfed01de7d2329e12e4a11e64@x.com	x	cliente	\N	2026-10-02 15:36:44.580178
3	Cliente Teste	cliente@pinkglow.com.br	$2y$12$ufi5p0Zeq/wRotzAKAquJelG6LQ6ZDuPv8C3HWme62rO19igYqr7W	cliente	\N	2026-10-02 15:52:42.2133
6	Cliente Teste	cliente.teste@ex.com	$2y$12$GfVAwlPFZ.1c/ic3UnPOoeBmUbmEHIO0hDqzGmiNl8YxQgyLKb5cC	cliente	\N	2026-10-02 16:18:47.182577
1	Administrador	admin@pinkglow.com.br	$2y$12$//n/BW7gaxwT7DtQ/AFOq.SduCmXd3GInJj7dBlYRCO9F0caMAflC	admin	\N	2026-10-02 15:36:04.039016
7	mirella taina	mirella.marques@edu.senai.br	$2y$12$rUGbKwFpAehJIDm8gfoPnelBPWJXeQCrkjpyZlgrg.y3Obew2ugOW	cliente	\N	2026-10-14 14:11:12.087156
8	julia silva	julia.silva@gmail.com	$2y$12$N77AosyBxWUMvIxksPwua.xKS4HKY.tduOYKvSiLj1yws3Rvh9vfW	cliente	\N	2026-10-14 16:19:54.877104
41	Mirella	mirella.taina@gmail.com	$2y$12$P6iBl6HfcLTBe/lZuXaEWu8dxwIYQFiwZFl9ZtwVaCNym8UQ0f1za	cliente	\N	2026-10-21 08:06:00.449691
42	Sophia	sophia.silva@gmail.com	$2y$12$zVEp1KkS9cDgM9nfj6BcGuABIjILEDTxsyAldS2XeV.UikbsUldDK	cliente	\N	2026-10-21 08:27:36.130094
43	Nicoli santos	nicoli.santos@gmail.com	$2y$12$kG3B28OhOUlyJEP420k3l.smDoaP4Z9dvKFudeR0MuD.cer19omXG	cliente	\N	2026-10-21 10:53:36.298255
44	livia	livia.simoes@gmail.com	$2y$12$8Y5/bSfBCBFgrK2k2cNq..7RchqysC80WQK3OS3I8MFnKx02nOMD6	cliente	\N	2026-10-21 13:42:07.745685
45	Alef	alef@gmail.com	$2y$12$TXBsOFTHBkweKFlYNjmZAOgBAGLMk80DOOj.t7yxD/4Taxi4pw3s2	cliente	\N	2026-10-21 16:35:35.88838
46	MIRELLINHA KAWAI	mirella@email.com	$2y$12$bxZsoUz1x.96e4Ts6cHZQ.VfLjf3BRd91NAEhkK7.T0TWuBQbkdRe	cliente	\N	2026-10-21 16:36:59.163754
47	mimi fofa	mirellonafofa@email.com	$2y$12$V5KFX2TTWJoeINxjAi61rOUMzQ3BjtRl9EKD7UfiY2XNHrg5ov84m	cliente	\N	2026-10-21 16:37:24.635923
\.


--
-- Name: categorias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.categorias_id_seq', 7, true);


--
-- Name: cupons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.cupons_id_seq', 35, true);


--
-- Name: devolucoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.devolucoes_id_seq', 3, true);


--
-- Name: enderecos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.enderecos_id_seq', 10, true);


--
-- Name: pagamentos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.pagamentos_id_seq', 16, true);


--
-- Name: pedido_itens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.pedido_itens_id_seq', 32, true);


--
-- Name: pedidos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.pedidos_id_seq', 18, true);


--
-- Name: produto_variacoes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.produto_variacoes_id_seq', 157, true);


--
-- Name: produtos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.produtos_id_seq', 64, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pinkglow
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 47, true);


--
-- Name: categorias categorias_nome_key; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_nome_key UNIQUE (nome);


--
-- Name: categorias categorias_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_pkey PRIMARY KEY (id);


--
-- Name: cupons cupons_codigo_key; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.cupons
    ADD CONSTRAINT cupons_codigo_key UNIQUE (codigo);


--
-- Name: cupons cupons_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.cupons
    ADD CONSTRAINT cupons_pkey PRIMARY KEY (id);


--
-- Name: devolucoes devolucoes_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.devolucoes
    ADD CONSTRAINT devolucoes_pkey PRIMARY KEY (id);


--
-- Name: enderecos enderecos_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.enderecos
    ADD CONSTRAINT enderecos_pkey PRIMARY KEY (id);


--
-- Name: pagamentos pagamentos_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pagamentos
    ADD CONSTRAINT pagamentos_pkey PRIMARY KEY (id);


--
-- Name: pedido_itens pedido_itens_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pedido_itens
    ADD CONSTRAINT pedido_itens_pkey PRIMARY KEY (id);


--
-- Name: pedidos pedidos_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pedidos
    ADD CONSTRAINT pedidos_pkey PRIMARY KEY (id);


--
-- Name: produto_variacoes produto_variacoes_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.produto_variacoes
    ADD CONSTRAINT produto_variacoes_pkey PRIMARY KEY (id);


--
-- Name: produto_variacoes produto_variacoes_sku_key; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.produto_variacoes
    ADD CONSTRAINT produto_variacoes_sku_key UNIQUE (sku);


--
-- Name: produtos produtos_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.produtos
    ADD CONSTRAINT produtos_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: idx_devolucoes_pedido_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_devolucoes_pedido_id ON public.devolucoes USING btree (pedido_id);


--
-- Name: idx_devolucoes_usuario_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_devolucoes_usuario_id ON public.devolucoes USING btree (usuario_id);


--
-- Name: idx_enderecos_usuario_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_enderecos_usuario_id ON public.enderecos USING btree (usuario_id);


--
-- Name: idx_pagamentos_pedido_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_pagamentos_pedido_id ON public.pagamentos USING btree (pedido_id);


--
-- Name: idx_pedido_itens_pedido_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_pedido_itens_pedido_id ON public.pedido_itens USING btree (pedido_id);


--
-- Name: idx_pedido_itens_produto_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_pedido_itens_produto_id ON public.pedido_itens USING btree (produto_id);


--
-- Name: idx_pedido_itens_variacao_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_pedido_itens_variacao_id ON public.pedido_itens USING btree (variacao_id);


--
-- Name: idx_pedidos_cupom_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_pedidos_cupom_id ON public.pedidos USING btree (cupom_id);


--
-- Name: idx_pedidos_endereco_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_pedidos_endereco_id ON public.pedidos USING btree (endereco_id);


--
-- Name: idx_pedidos_usuario_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_pedidos_usuario_id ON public.pedidos USING btree (usuario_id);


--
-- Name: idx_produto_variacoes_produto_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_produto_variacoes_produto_id ON public.produto_variacoes USING btree (produto_id);


--
-- Name: idx_produtos_categoria_id; Type: INDEX; Schema: public; Owner: pinkglow
--

CREATE INDEX idx_produtos_categoria_id ON public.produtos USING btree (categoria_id);


--
-- Name: devolucoes trg_devolucoes_rn03; Type: TRIGGER; Schema: public; Owner: pinkglow
--

CREATE TRIGGER trg_devolucoes_rn03 BEFORE INSERT ON public.devolucoes FOR EACH ROW EXECUTE FUNCTION public.valida_devolucao_rn03();


--
-- Name: devolucoes devolucoes_pedido_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.devolucoes
    ADD CONSTRAINT devolucoes_pedido_id_fkey FOREIGN KEY (pedido_id) REFERENCES public.pedidos(id);


--
-- Name: devolucoes devolucoes_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.devolucoes
    ADD CONSTRAINT devolucoes_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id);


--
-- Name: enderecos enderecos_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.enderecos
    ADD CONSTRAINT enderecos_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: pagamentos pagamentos_pedido_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pagamentos
    ADD CONSTRAINT pagamentos_pedido_id_fkey FOREIGN KEY (pedido_id) REFERENCES public.pedidos(id) ON DELETE CASCADE;


--
-- Name: pedido_itens pedido_itens_pedido_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pedido_itens
    ADD CONSTRAINT pedido_itens_pedido_id_fkey FOREIGN KEY (pedido_id) REFERENCES public.pedidos(id) ON DELETE CASCADE;


--
-- Name: pedido_itens pedido_itens_produto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pedido_itens
    ADD CONSTRAINT pedido_itens_produto_id_fkey FOREIGN KEY (produto_id) REFERENCES public.produtos(id);


--
-- Name: pedido_itens pedido_itens_variacao_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pedido_itens
    ADD CONSTRAINT pedido_itens_variacao_id_fkey FOREIGN KEY (variacao_id) REFERENCES public.produto_variacoes(id);


--
-- Name: pedidos pedidos_cupom_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pedidos
    ADD CONSTRAINT pedidos_cupom_id_fkey FOREIGN KEY (cupom_id) REFERENCES public.cupons(id);


--
-- Name: pedidos pedidos_endereco_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pedidos
    ADD CONSTRAINT pedidos_endereco_id_fkey FOREIGN KEY (endereco_id) REFERENCES public.enderecos(id);


--
-- Name: pedidos pedidos_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.pedidos
    ADD CONSTRAINT pedidos_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id);


--
-- Name: produto_variacoes produto_variacoes_produto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.produto_variacoes
    ADD CONSTRAINT produto_variacoes_produto_id_fkey FOREIGN KEY (produto_id) REFERENCES public.produtos(id) ON DELETE CASCADE;


--
-- Name: produtos produtos_categoria_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: pinkglow
--

ALTER TABLE ONLY public.produtos
    ADD CONSTRAINT produtos_categoria_id_fkey FOREIGN KEY (categoria_id) REFERENCES public.categorias(id);


--
-- PostgreSQL database dump complete
--

\unrestrict BQlWGKAd8xbfV8KDMGHpT06FUOhyBmbdfRfhe1exlxRHAmk5MRyNAROCNkJJ4Za


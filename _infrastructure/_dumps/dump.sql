--
-- PostgreSQL database dump
--

\restrict K5uk1BsAdVtj3f6w3pbiXZddb6uby3rxs0QSvoHav6QKojabTMMqSi51lgHyJJS

-- Dumped from database version 16.15
-- Dumped by pg_dump version 16.15

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE IF EXISTS ONLY public.articles DROP CONSTRAINT IF EXISTS fk_articles_category;
DROP INDEX IF EXISTS public.idx_articles_category;
ALTER TABLE IF EXISTS ONLY public.categories DROP CONSTRAINT IF EXISTS uq_categories_slug;
ALTER TABLE IF EXISTS ONLY public.categories DROP CONSTRAINT IF EXISTS uq_categories_name;
ALTER TABLE IF EXISTS ONLY public.articles DROP CONSTRAINT IF EXISTS uq_articles_slug;
ALTER TABLE IF EXISTS ONLY public.categories DROP CONSTRAINT IF EXISTS categories_pkey;
ALTER TABLE IF EXISTS ONLY public.articles DROP CONSTRAINT IF EXISTS articles_pkey;
DROP TABLE IF EXISTS public.categories;
DROP TABLE IF EXISTS public.articles;
SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: articles; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.articles (
    id uuid NOT NULL,
    slug character varying(255) NOT NULL,
    title character varying(255) NOT NULL,
    body text NOT NULL,
    description text NOT NULL,
    status character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    published_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    category_id uuid NOT NULL
);


ALTER TABLE public.articles OWNER TO app;

--
-- Name: categories; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.categories (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    sort_order integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.categories OWNER TO app;

--
-- Data for Name: articles; Type: TABLE DATA; Schema: public; Owner: app
--

COPY public.articles (id, slug, title, body, description, status, created_at, updated_at, published_at, category_id) FROM stdin;
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: app
--

COPY public.categories (id, name, slug, sort_order, created_at, updated_at) FROM stdin;
01a14b9e-0d09-7527-8f42-280b0e3c7fc4	Сети и протоколы	networking	1	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-782f-8f42-280b0f2f177b	Надёжность и асинхронность	reliability	2	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-78c3-8f42-280b10064379	Архитектура	architecture	3	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-78f7-8f42-280b11034104	СУБД	databases	4	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-791b-8f42-280b118d4530	Инженерия и безопасность	engineering-security	5	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-793f-8f42-280b11ca1df5	PHP	php	6	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-795f-8f42-280b12638d05	Паттерны	patterns	7	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-7983-8f42-280b12f1fc7f	Laravel	laravel	8	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-79a3-8f42-280b13248a8d	Symfony	symfony	9	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-79c3-8f42-280b13c847f8	TypeScript	typescript	10	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-79e7-8f42-280b1459fa43	React	react	11	2026-10-17 23:46:51	2026-10-17 23:46:51
01a14b9e-0d09-7a07-8f42-280b15447db8	Vue	vue	12	2026-10-17 23:46:51	2026-10-17 23:46:51
\.


--
-- Name: articles articles_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.articles
    ADD CONSTRAINT articles_pkey PRIMARY KEY (id);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: articles uq_articles_slug; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.articles
    ADD CONSTRAINT uq_articles_slug UNIQUE (slug);


--
-- Name: categories uq_categories_name; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT uq_categories_name UNIQUE (name);


--
-- Name: categories uq_categories_slug; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT uq_categories_slug UNIQUE (slug);


--
-- Name: idx_articles_category; Type: INDEX; Schema: public; Owner: app
--

CREATE INDEX idx_articles_category ON public.articles USING btree (category_id);


--
-- Name: articles fk_articles_category; Type: FK CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.articles
    ADD CONSTRAINT fk_articles_category FOREIGN KEY (category_id) REFERENCES public.categories(id);


--
-- PostgreSQL database dump complete
--

\unrestrict K5uk1BsAdVtj3f6w3pbiXZddb6uby3rxs0QSvoHav6QKojabTMMqSi51lgHyJJS


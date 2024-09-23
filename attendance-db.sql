--
-- PostgreSQL database dump
--

-- Dumped from database version 16.4
-- Dumped by pg_dump version 16.4

-- Started on 2024-09-24 05:51:48

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 215 (class 1259 OID 16496)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 16502)
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- TOC entry 4903 (class 0 OID 0)
-- Dependencies: 216
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- TOC entry 217 (class 1259 OID 16503)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 16506)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 4904 (class 0 OID 0)
-- Dependencies: 218
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 228 (class 1259 OID 16567)
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 16578)
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_roles OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 16507)
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 16546)
-- Name: permissions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permissions OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 16545)
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.permissions_id_seq OWNER TO postgres;

--
-- TOC entry 4905 (class 0 OID 0)
-- Dependencies: 224
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- TOC entry 220 (class 1259 OID 16512)
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 16517)
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- TOC entry 4906 (class 0 OID 0)
-- Dependencies: 221
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- TOC entry 234 (class 1259 OID 24594)
-- Name: presences; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.presences (
    id bigint NOT NULL,
    is_remote integer NOT NULL,
    user_id integer NOT NULL,
    day date NOT NULL,
    start integer NOT NULL,
    lat_start character varying(255) NOT NULL,
    long_start character varying(255) NOT NULL,
    ip_start character varying(255) NOT NULL,
    browser_start character varying(255) NOT NULL,
    isp_start character varying(255),
    image_start character varying(255),
    start_late integer,
    "end" integer,
    lat_end character varying(255),
    long_end character varying(255),
    ip_end character varying(255),
    browser_end character varying(255),
    isp_end character varying(255),
    image_end character varying(255),
    start_marked_by_admin integer,
    end_marked_by_system integer,
    overtime integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.presences OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 24593)
-- Name: presences_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.presences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.presences_id_seq OWNER TO postgres;

--
-- TOC entry 4907 (class 0 OID 0)
-- Dependencies: 233
-- Name: presences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.presences_id_seq OWNED BY public.presences.id;


--
-- TOC entry 230 (class 1259 OID 16589)
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 16557)
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 16556)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO postgres;

--
-- TOC entry 4908 (class 0 OID 0)
-- Dependencies: 226
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- TOC entry 222 (class 1259 OID 16518)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    nik character varying(255)
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 16523)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 4909 (class 0 OID 0)
-- Dependencies: 223
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 232 (class 1259 OID 24587)
-- Name: working_hour; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.working_hour (
    id bigint NOT NULL,
    user_id integer NOT NULL,
    working_start integer NOT NULL,
    working_end integer NOT NULL,
    days character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.working_hour OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 24586)
-- Name: working_hour_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.working_hour_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.working_hour_id_seq OWNER TO postgres;

--
-- TOC entry 4910 (class 0 OID 0)
-- Dependencies: 231
-- Name: working_hour_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.working_hour_id_seq OWNED BY public.working_hour.id;


--
-- TOC entry 4685 (class 2604 OID 16524)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- TOC entry 4687 (class 2604 OID 16525)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 4690 (class 2604 OID 16549)
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- TOC entry 4688 (class 2604 OID 16526)
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- TOC entry 4693 (class 2604 OID 24597)
-- Name: presences id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.presences ALTER COLUMN id SET DEFAULT nextval('public.presences_id_seq'::regclass);


--
-- TOC entry 4691 (class 2604 OID 16560)
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- TOC entry 4689 (class 2604 OID 16527)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 4692 (class 2604 OID 24590)
-- Name: working_hour id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.working_hour ALTER COLUMN id SET DEFAULT nextval('public.working_hour_id_seq'::regclass);


--
-- TOC entry 4878 (class 0 OID 16496)
-- Dependencies: 215
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- TOC entry 4880 (class 0 OID 16503)
-- Dependencies: 217
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_reset_tokens_table	1
3	2019_08_19_000000_create_failed_jobs_table	1
4	2019_12_14_000001_create_personal_access_tokens_table	1
5	2024_09_19_143652_create_permission_tables	2
6	2024_09_21_062426_create_working_hour_table	3
7	2024_09_22_062849_precenses_migration	4
\.


--
-- TOC entry 4891 (class 0 OID 16567)
-- Dependencies: 228
-- Data for Name: model_has_permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.model_has_permissions (permission_id, model_type, model_id) FROM stdin;
\.


--
-- TOC entry 4892 (class 0 OID 16578)
-- Dependencies: 229
-- Data for Name: model_has_roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.model_has_roles (role_id, model_type, model_id) FROM stdin;
1	App\\Models\\User	1
1	App\\Models\\User	3
2	App\\Models\\User	4
1	App\\Models\\User	2
\.


--
-- TOC entry 4882 (class 0 OID 16507)
-- Dependencies: 219
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- TOC entry 4888 (class 0 OID 16546)
-- Dependencies: 225
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.permissions (id, name, guard_name, created_at, updated_at) FROM stdin;
1	read role	web	2024-09-19 15:04:52	2024-09-19 15:04:52
2	create role	web	2024-09-19 15:04:52	2024-09-19 15:04:52
3	update role	web	2024-09-19 15:04:52	2024-09-19 15:04:52
4	delete role	web	2024-09-19 15:04:52	2024-09-19 15:04:52
\.


--
-- TOC entry 4883 (class 0 OID 16512)
-- Dependencies: 220
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 4897 (class 0 OID 24594)
-- Dependencies: 234
-- Data for Name: presences; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.presences (id, is_remote, user_id, day, start, lat_start, long_start, ip_start, browser_start, isp_start, image_start, start_late, "end", lat_end, long_end, ip_end, browser_end, isp_end, image_end, start_marked_by_admin, end_marked_by_system, overtime, created_at, updated_at, deleted_at) FROM stdin;
3	1	2	2024-09-22	1726962259	-7.0582272	110.4314368	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66ef5a53ee852.jpeg	0	1726988188	-7.0661447	110.4344131	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efbf9c827d1.jpeg	\N	\N	0	2024-09-22 06:44:19	2024-09-22 13:56:28	\N
6	1	4	2024-09-22	1726988969	-7.0661447	110.4344131	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efc2a979bec.jpeg	0	1726989039	-7.0661447	110.4344131	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efc2ef4c68c.jpeg	\N	\N	1	2024-09-22 14:09:29	2024-09-22 14:10:39	\N
8	1	2	2024-09-21	1726989612	-7.0661447	110.4344131	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efc52c3e62d.jpeg	1726960812	1726989697	-7.0656628	110.4274195	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efc581f1d38.jpeg	\N	\N	0	2024-09-22 14:20:12	2024-09-22 14:21:37	\N
10	1	4	2024-09-21	1726990445	-7.0661447	110.4344131	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efc86db106a.jpeg	0	1726990507	-7.0661447	110.4344131	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efc8abcd1bc.jpeg	\N	\N	1	2024-09-22 14:34:05	2024-09-22 14:35:07	\N
11	1	4	2024-09-20	1726966680	-7.0661447	110.4344131	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efc9c01a5ae.jpeg	0	1726984800	-7.0661447	110.4344131	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efc9f2353a4.jpeg	\N	\N	1	2024-09-22 14:39:44	2024-09-22 14:40:34	\N
12	1	2	2024-09-20	1726967100	-7.0661483	110.4373922	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efca318d418.jpeg	1726938300	1726984800	-7.0661483	110.4373922	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66efca5e802a8.jpeg	\N	\N	0	2024-09-22 14:41:37	2024-09-22 14:42:22	\N
13	1	2	2024-09-23	1727053080	-7.0582272	110.4314368	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66f057a1d7100.jpeg	0	1727085480	-7.0582272	110.4314368	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66f057bed4622.jpeg	\N	\N	0	2024-09-23 00:45:05	2024-09-23 00:45:34	\N
14	1	4	2024-09-23	1727053930	-7.0582272	110.4314368	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66f05802b7ace.jpeg	1727025130	1727082730	-7.0582272	110.4314368	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36	\N	66f05813e364d.jpeg	\N	\N	0	2024-09-23 00:46:42	2024-09-23 00:46:59	\N
\.


--
-- TOC entry 4893 (class 0 OID 16589)
-- Dependencies: 230
-- Data for Name: role_has_permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.role_has_permissions (permission_id, role_id) FROM stdin;
\.


--
-- TOC entry 4890 (class 0 OID 16557)
-- Dependencies: 227
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, name, guard_name, created_at, updated_at) FROM stdin;
1	employee	web	2024-09-19 15:04:52	2024-09-19 15:04:52
2	hr	web	2024-09-19 15:04:52	2024-09-19 15:04:52
\.


--
-- TOC entry 4885 (class 0 OID 16518)
-- Dependencies: 222
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, nik) FROM stdin;
4	HR1	hr1@gmail.com	\N	$2y$12$FQ272nl1Ae5p12aIl7/YyemQ/rfKfnjVxeWYPtvnk3.KK.XcvWg52	\N	2024-09-21 04:04:28	2024-09-21 14:45:51	33123123123
2	Employee2	employee1@gmail.com	2024-09-19 15:04:52	$2y$12$r7TY5waPjYLDZSQpySXhV.MMm/9muFag2U2XMIZCX5rIscQudQ5tq	EIfbQCu5jLySskTY1Z0lKKjxXfqjS8SLIX1LU873yZSZxR93nuX7SABrEWBz	2024-09-19 15:04:52	2024-09-21 14:46:33	31231123123
\.


--
-- TOC entry 4895 (class 0 OID 24587)
-- Dependencies: 232
-- Data for Name: working_hour; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.working_hour (id, user_id, working_start, working_end, days, created_at, updated_at, deleted_at) FROM stdin;
23	4	0	0	7	2024-09-21 09:21:15	2024-09-21 09:21:15	\N
18	4	1726966800	1726995600	2	2024-09-21 09:21:15	2024-09-22 05:48:24	\N
19	4	1726966800	1726995600	3	2024-09-21 09:21:15	2024-09-22 05:48:24	\N
20	4	1726966800	1726995600	4	2024-09-21 09:21:15	2024-09-22 05:48:24	\N
21	4	1726966800	1726995600	5	2024-09-21 09:21:15	2024-09-22 05:48:24	\N
11	2	1726966800	1726995600	2	2024-09-21 09:13:07	2024-09-22 05:49:29	\N
12	2	1726966800	1726995600	3	2024-09-21 09:13:07	2024-09-22 05:49:29	\N
13	2	1726966800	1726995600	4	2024-09-21 09:13:07	2024-09-22 05:49:29	\N
14	2	1726966800	1726995600	5	2024-09-21 09:13:07	2024-09-22 05:49:29	\N
16	2	1726966800	1726984800	7	2024-09-21 09:13:07	2024-09-22 05:49:29	\N
15	2	1726966800	1726984800	6	2024-09-21 09:13:07	2024-09-22 05:49:42	\N
17	4	0	0	1	2024-09-21 09:21:15	2024-09-22 14:30:51	\N
22	4	1726966800	1726984800	6	2024-09-21 09:21:15	2024-09-22 14:30:51	\N
10	2	1726966800	1726984800	1	2024-09-21 09:13:07	2024-09-22 14:41:59	\N
\.


--
-- TOC entry 4911 (class 0 OID 0)
-- Dependencies: 216
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- TOC entry 4912 (class 0 OID 0)
-- Dependencies: 218
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 7, true);


--
-- TOC entry 4913 (class 0 OID 0)
-- Dependencies: 224
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.permissions_id_seq', 4, true);


--
-- TOC entry 4914 (class 0 OID 0)
-- Dependencies: 221
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- TOC entry 4915 (class 0 OID 0)
-- Dependencies: 233
-- Name: presences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.presences_id_seq', 14, true);


--
-- TOC entry 4916 (class 0 OID 0)
-- Dependencies: 226
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_id_seq', 2, true);


--
-- TOC entry 4917 (class 0 OID 0)
-- Dependencies: 223
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 8, true);


--
-- TOC entry 4918 (class 0 OID 0)
-- Dependencies: 231
-- Name: working_hour_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.working_hour_id_seq', 23, true);


--
-- TOC entry 4695 (class 2606 OID 16529)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 4697 (class 2606 OID 16531)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 4699 (class 2606 OID 16533)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 4721 (class 2606 OID 16577)
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- TOC entry 4724 (class 2606 OID 16588)
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- TOC entry 4701 (class 2606 OID 16535)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- TOC entry 4712 (class 2606 OID 16555)
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- TOC entry 4714 (class 2606 OID 16553)
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- TOC entry 4703 (class 2606 OID 16537)
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- TOC entry 4705 (class 2606 OID 16539)
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- TOC entry 4730 (class 2606 OID 24601)
-- Name: presences presences_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.presences
    ADD CONSTRAINT presences_pkey PRIMARY KEY (id);


--
-- TOC entry 4726 (class 2606 OID 16603)
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- TOC entry 4716 (class 2606 OID 16566)
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- TOC entry 4718 (class 2606 OID 16564)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 4708 (class 2606 OID 16541)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 4710 (class 2606 OID 16543)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 4728 (class 2606 OID 24592)
-- Name: working_hour working_hour_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.working_hour
    ADD CONSTRAINT working_hour_pkey PRIMARY KEY (id);


--
-- TOC entry 4719 (class 1259 OID 16570)
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- TOC entry 4722 (class 1259 OID 16581)
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- TOC entry 4706 (class 1259 OID 16544)
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- TOC entry 4731 (class 2606 OID 16571)
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- TOC entry 4732 (class 2606 OID 16582)
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- TOC entry 4733 (class 2606 OID 16592)
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- TOC entry 4734 (class 2606 OID 16597)
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


-- Completed on 2024-09-24 05:51:49

--
-- PostgreSQL database dump complete
--


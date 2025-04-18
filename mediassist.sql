
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
-- Name: appointment; Type: TABLE; Schema: public; Owner: neondb_owner
--

CREATE TABLE public.appointment (
    id integer NOT NULL,
    title character varying(100) NOT NULL,
    doctor character varying(100),
    location character varying(200),
    date date NOT NULL,
    "time" time without time zone NOT NULL,
    notes text,
    user_id integer NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--ALTER TABLE public.appointment OWNER TO neondb_owner;

--
-- Name: appointment_id_seq; Type: SEQUENCE; Schema: public; Owner: neondb_owner
--

CREATE SEQUENCE public.appointment_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--ALTER SEQUENCE public.appointment_id_seq OWNER TO neondb_owner;

--
-- Name: appointment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: neondb_owner
--

ALTER SEQUENCE public.appointment_id_seq OWNED BY public.appointment.id;


--
-- Name: emergency_contact; Type: TABLE; Schema: public; Owner: neondb_owner
--

CREATE TABLE public.emergency_contact (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    relationship character varying(50),
    phone character varying(20) NOT NULL,
    email character varying(120),
    notes text,
    user_id integer NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--ALTER TABLE public.emergency_contact OWNER TO neondb_owner;

--
-- Name: emergency_contact_id_seq; Type: SEQUENCE; Schema: public; Owner: neondb_owner
--

CREATE SEQUENCE public.emergency_contact_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--ALTER SEQUENCE public.emergency_contact_id_seq OWNER TO neondb_owner;

--
-- Name: emergency_contact_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: neondb_owner
--

ALTER SEQUENCE public.emergency_contact_id_seq OWNED BY public.emergency_contact.id;


--
-- Name: medication; Type: TABLE; Schema: public; Owner: neondb_owner
--

CREATE TABLE public.medication (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    dosage character varying(50) NOT NULL,
    frequency character varying(50) NOT NULL,
    "time" character varying(100) NOT NULL,
    notes text,
    user_id integer NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--ALTER TABLE public.medication OWNER TO neondb_owner;

--
-- Name: medication_id_seq; Type: SEQUENCE; Schema: public; Owner: neondb_owner
--

CREATE SEQUENCE public.medication_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--ALTER SEQUENCE public.medication_id_seq OWNER TO neondb_owner;

--
-- Name: medication_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: neondb_owner
--

ALTER SEQUENCE public.medication_id_seq OWNED BY public.medication.id;


--
-- Name: notification; Type: TABLE; Schema: public; Owner: neondb_owner
--

CREATE TABLE public.notification (
    id integer NOT NULL,
    type character varying(20) NOT NULL,
    item_id integer NOT NULL,
    scheduled_time timestamp without time zone NOT NULL,
    message text NOT NULL,
    is_read boolean,
    user_id integer NOT NULL,
    created_at timestamp without time zone
);


--ALTER TABLE public.notification OWNER TO neondb_owner;

--
-- Name: notification_id_seq; Type: SEQUENCE; Schema: public; Owner: neondb_owner
--

CREATE SEQUENCE public.notification_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--ALTER SEQUENCE public.notification_id_seq OWNER TO neondb_owner;

--
-- Name: notification_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: neondb_owner
--

ALTER SEQUENCE public.notification_id_seq OWNED BY public.notification.id;


--
-- Name: prescription; Type: TABLE; Schema: public; Owner: neondb_owner
--

CREATE TABLE public.prescription (
    id integer NOT NULL,
    title character varying(100) NOT NULL,
    doctor character varying(100),
    date date NOT NULL,
    image_data text,
    notes text,
    user_id integer NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--ALTER TABLE public.prescription OWNER TO neondb_owner;

--
-- Name: prescription_id_seq; Type: SEQUENCE; Schema: public; Owner: neondb_owner
--

CREATE SEQUENCE public.prescription_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--ALTER SEQUENCE public.prescription_id_seq OWNER TO neondb_owner;

--
-- Name: prescription_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: neondb_owner
--

ALTER SEQUENCE public.prescription_id_seq OWNED BY public.prescription.id;


--
-- Name: user; Type: TABLE; Schema: public; Owner: neondb_owner
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    username character varying(64) NOT NULL,
    email character varying(120) NOT NULL,
    password_hash character varying(256) NOT NULL,
    theme_preference character varying(10)
);


--ALTER TABLE public."user" OWNER TO neondb_owner;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: neondb_owner
--

CREATE SEQUENCE public.user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--ALTER SEQUENCE public.user_id_seq OWNER TO neondb_owner; -- This line is commented out to avoid errors during the sequence creation

--
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: neondb_owner
--

ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;


--
-- Name: appointment id; Type: DEFAULT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.appointment ALTER COLUMN id SET DEFAULT nextval('public.appointment_id_seq'::regclass);


--
-- Name: emergency_contact id; Type: DEFAULT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.emergency_contact ALTER COLUMN id SET DEFAULT nextval('public.emergency_contact_id_seq'::regclass);


--
-- Name: medication id; Type: DEFAULT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.medication ALTER COLUMN id SET DEFAULT nextval('public.medication_id_seq'::regclass);


--
-- Name: notification id; Type: DEFAULT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.notification ALTER COLUMN id SET DEFAULT nextval('public.notification_id_seq'::regclass);


--
-- Name: prescription id; Type: DEFAULT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.prescription ALTER COLUMN id SET DEFAULT nextval('public.prescription_id_seq'::regclass);


--
-- Name: user id; Type: DEFAULT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);




--
-- Name: appointment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: neondb_owner
--

SELECT pg_catalog.setval('public.appointment_id_seq', 1, false);


--
-- Name: emergency_contact_id_seq; Type: SEQUENCE SET; Schema: public; Owner: neondb_owner
--

SELECT pg_catalog.setval('public.emergency_contact_id_seq', 1, false);


--
-- Name: medication_id_seq; Type: SEQUENCE SET; Schema: public; Owner: neondb_owner
--

SELECT pg_catalog.setval('public.medication_id_seq', 1, false);


--
-- Name: notification_id_seq; Type: SEQUENCE SET; Schema: public; Owner: neondb_owner
--

SELECT pg_catalog.setval('public.notification_id_seq', 1, false);


--
-- Name: prescription_id_seq; Type: SEQUENCE SET; Schema: public; Owner: neondb_owner
--

SELECT pg_catalog.setval('public.prescription_id_seq', 1, false);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: neondb_owner
--

SELECT pg_catalog.setval('public.user_id_seq', 1, true);


--
-- Name: appointment appointment_pkey; Type: CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT appointment_pkey PRIMARY KEY (id);


--
-- Name: emergency_contact emergency_contact_pkey; Type: CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.emergency_contact
    ADD CONSTRAINT emergency_contact_pkey PRIMARY KEY (id);


--
-- Name: medication medication_pkey; Type: CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.medication
    ADD CONSTRAINT medication_pkey PRIMARY KEY (id);


--
-- Name: notification notification_pkey; Type: CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.notification
    ADD CONSTRAINT notification_pkey PRIMARY KEY (id);


--
-- Name: prescription prescription_pkey; Type: CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.prescription
    ADD CONSTRAINT prescription_pkey PRIMARY KEY (id);


--
-- Name: user user_email_key; Type: CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_email_key UNIQUE (email);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: user user_username_key; Type: CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_username_key UNIQUE (username);


--
-- Name: appointment appointment_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.appointment
    ADD CONSTRAINT appointment_user_id_fkey FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: emergency_contact emergency_contact_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.emergency_contact
    ADD CONSTRAINT emergency_contact_user_id_fkey FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: medication medication_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.medication
    ADD CONSTRAINT medication_user_id_fkey FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: notification notification_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.notification
    ADD CONSTRAINT notification_user_id_fkey FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: prescription prescription_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: neondb_owner
--

ALTER TABLE ONLY public.prescription
    ADD CONSTRAINT prescription_user_id_fkey FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: public; Owner: cloud_admin
--

--ALTER DEFAULT PRIVILEGES FOR ROLE cloud_admin IN SCHEMA public GRANT ALL ON SEQUENCES TO neon_superuser WITH GRANT OPTION;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: cloud_admin
--

--ALTER DEFAULT PRIVILEGES FOR ROLE cloud_admin IN SCHEMA public GRANT ALL ON TABLES TO neon_superuser WITH GRANT OPTION;



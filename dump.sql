--
-- PostgreSQL database dump
--

-- Dumped from database version 12.16 (Ubuntu 12.16-0ubuntu0.20.04.1)
-- Dumped by pg_dump version 12.16 (Ubuntu 12.16-0ubuntu0.20.04.1)

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

--
-- Name: universita; Type: SCHEMA; Schema: -; Owner: bdlab
--

CREATE SCHEMA universita;


ALTER SCHEMA universita OWNER TO bdlab;

--
-- Name: add_docente(character varying, character varying, character varying, character varying, date, character varying, character varying, integer); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.add_docente(username character varying, password character varying, nome character varying, cognome character varying, data_di_nascita date, sesso character varying, indirizzo character varying, primo_insegnamento integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$

DECLARE

    old_count integer;

    new_count integer;

BEGIN

    SELECT count(*) into old_count

	FROM docente;

    -- Per far si che l'inserimento funzioni, dobbiamo assegnare al docente un insegnamento privo di docente

    if (check_responsabile(primo_insegnamento) != 1) then

        return FALSE;

    END if;

    INSERT INTO docente VALUES

        (username, password, nome, cognome, data_di_nascita, sesso, indirizzo);

    UPDATE insegnamento

    SET responsabile = username

    WHERE id = primo_insegnamento;

    SELECT count(*) into new_count

	FROM docente;

    if (old_count=new_count) then

		ROLLBACK;

        return FALSE;

	else

		return TRUE;

    END if;

END;

$$;


ALTER FUNCTION universita.add_docente(username character varying, password character varying, nome character varying, cognome character varying, data_di_nascita date, sesso character varying, indirizzo character varying, primo_insegnamento integer) OWNER TO bdlab;

--
-- Name: check_delete_docente(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_delete_docente() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

	ALTER TABLE insegnamento

	DISABLE TRIGGER check_numero_insegnamenti_trigger;

	UPDATE insegnamento

	SET responsabile = NULL

	WHERE responsabile = OLD.username;

	ALTER TABLE insegnamento

	ENABLE TRIGGER check_numero_insegnamenti_trigger;

	RETURN OLD;

END;

$$;


ALTER FUNCTION universita.check_delete_docente() OWNER TO bdlab;

--
-- Name: check_delete_insegnamento(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_delete_insegnamento() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

DECLARE

	cnt integer;

BEGIN

	if EXISTS(

		SELECT *

		FROM esame

		WHERE insegnamento = OLD.id

	) OR EXISTS (

		SELECT *

		FROM esame_storico

		WHERE insegnamento = OLD.id

	) then raise exception 'Non puoi eliminare questo insegnamento';

	end if;

	SELECT count(*) into cnt

	FROM insegnamento

	WHERE responsabile = OLD.responsabile;

	if (cnt <= 1)

		then raise exception 'Il docente rimarrebbe privo di insegnamenti';

	end if;

	RETURN OLD;

END;

$$;


ALTER FUNCTION universita.check_delete_insegnamento() OWNER TO bdlab;

--
-- Name: check_disiscrizione_esame(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_disiscrizione_esame() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

	if (OLD.data::date <= NOW()::date)

		then raise exception 'Non ti puoi disiscrivere da questo esame';

	end if;

	RETURN OLD;

END;

$$;


ALTER FUNCTION universita.check_disiscrizione_esame() OWNER TO bdlab;

--
-- Name: check_durata_corso(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_durata_corso() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

	if EXISTS(

		SELECT *

		FROM insegnamento

		WHERE corso = OLD.nome AND anno > NEW.durata

	) then raise exception 'Ci sono insegnamenti appartenenti al corso non conformi alla modifica!';

	end if;

	RETURN NEW;

END;

$$;


ALTER FUNCTION universita.check_durata_corso() OWNER TO bdlab;

--
-- Name: check_durata_insegnamento(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_durata_insegnamento() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

DECLARE

	anno_max integer;

BEGIN

	SELECT durata into anno_max

	FROM corso

	WHERE nome = NEW.corso;

	if (NEW.anno < 1 OR NEW.anno > anno_max) then

		raise exception 'Anno previsto non conforme';

	end if;

	if (OLD.corso <> NEW.corso)

		then raise exception 'Non si può modificare il corso di un insegnamento';

	end if;

	return NEW;

END;

$$;


ALTER FUNCTION universita.check_durata_insegnamento() OWNER TO bdlab;

--
-- Name: check_inserimento_appello(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_inserimento_appello() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

	if (NEW.data::date < NOW()::date)

		then raise exception 'Impossibile inserire un appello precedente a oggi';

	end if;

	RETURN NEW;

END;

$$;


ALTER FUNCTION universita.check_inserimento_appello() OWNER TO bdlab;

--
-- Name: check_inserimento_studente(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_inserimento_studente() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

	if EXISTS(

		SELECT *

		FROM studente_storico

		WHERE matricola = NEW.matricola OR username = NEW.username

	) then raise exception 'Alcuni dati sono già stati usati da uno studente in passato';

	end if;

	if (NEW.corso <> OLD.corso)

		then raise exception 'Non si può modificare il corso di uno studente';

	end if;

	RETURN NEW;

END;

$$;


ALTER FUNCTION universita.check_inserimento_studente() OWNER TO bdlab;

--
-- Name: check_inserimento_voti(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_inserimento_voti() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

	if (NEW.data::date > NOW()::date)

		then raise exception 'Non si può modificare il voto di un esame non ancora svolto';

	end if;

	RETURN NEW;

END;

$$;


ALTER FUNCTION universita.check_inserimento_voti() OWNER TO bdlab;

--
-- Name: check_iscrizione_esami(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_iscrizione_esami() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

DECLARE

	laurea varchar(50);

BEGIN

	-- Selezioniamo il corso di laurea dello studente che si iscrive all'esame

	SELECT corso into laurea

	FROM studente

	WHERE matricola = NEW.studente;

	if NOT EXISTS(

		-- Controlliamo che l'insegnamento a cui lo studente si iscrive sia previsto dal suo corso di laurea

		SELECT *

		FROM insegnamento

		WHERE id = NEW.insegnamento AND corso = laurea

	) then

		raise exception 'Insegnamento non presente nel corso di laurea dello studente';

	end if;

	if NOT check_propedeuticita(NEW.insegnamento, NEW.studente) then

		raise exception 'Propedeuticità esame non rispettate';

	end if;

	if (NEW.data::date < NOW()::date)

		then raise exception 'Non è possibile iscriversi a un esame già passato';

	end if;

	return NEW;

END;

$$;


ALTER FUNCTION universita.check_iscrizione_esami() OWNER TO bdlab;

--
-- Name: check_numero_insegnamenti(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_numero_insegnamenti() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

DECLARE 

	new_count integer;

	old_count integer;

BEGIN

	SELECT count(*) into new_count

	FROM insegnamento

	WHERE responsabile = NEW.responsabile; 

	if (TG_OP = 'INSERT') then

		if (new_count>=3) then

			raise exception 'Il docente ha già 3 insegnamenti associati';

		end if;

	elsif (TG_OP = 'UPDATE') then

		SELECT count(*) into old_count

		FROM insegnamento

		WHERE responsabile = OLD.responsabile;

		if (old_count <= 1 AND OLD.responsabile IS NOT NULL) then

			raise exception 'Il vecchio docente non avrebbe più insegnamenti';

		end if;

		if ((OLD.responsabile <> NEW.responsabile OR OLD.responsabile IS NULL) AND new_count >= 3) then

			raise exception 'Il nuovo docente ha già 3 insegnamenti associati';

		end if;

	end if;

	return NEW;

END;

$$;


ALTER FUNCTION universita.check_numero_insegnamenti() OWNER TO bdlab;

--
-- Name: check_propedeuticita(integer, integer); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_propedeuticita(codice_insegnamento integer, matricola integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$

BEGIN

	if EXISTS(

		SELECT requisito

		FROM get_propedeuticita(codice_insegnamento)

		EXCEPT

		SELECT codice

		FROM get_carriera_valida(matricola)

	) then

		return FALSE;

	else

		return TRUE;

    end if;

END;

$$;


ALTER FUNCTION universita.check_propedeuticita(codice_insegnamento integer, matricola integer) OWNER TO bdlab;

--
-- Name: check_propedeuticita_corretta(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_propedeuticita_corretta() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

DECLARE

	corso_a varchar;

	corso_b varchar;

BEGIN

	-- Prendiamo i corsi degli insegnamenti e controlliamo che siano uguali

	SELECT corso into corso_a

	FROM insegnamento

	WHERE id = NEW.insegnamento;

	SELECT corso into corso_b

	FROM insegnamento

	WHERE id = NEW.requisito;

	if (corso_b <> corso_a) then

		raise exception 'I due insegnamenti appartengono a corsi diversi!';

	end if;

	-- Controllo catene di Propedeuticità

	if EXISTS(

		WITH RECURSIVE catena AS (

			SELECT *

			FROM propedeuticita

			WHERE insegnamento = NEW.requisito

			UNION

			SELECT c.insegnamento, p.requisito

			FROM propedeuticita p

			INNER JOIN catena c ON c.requisito = p.insegnamento

		)

		SELECT *

		FROM catena

		WHERE NEW.insegnamento = requisito

	) then raise exception 'Impossibile introdurre una catena di propedeuticità';

	end if;

	return NEW;

END;

$$;


ALTER FUNCTION universita.check_propedeuticita_corretta() OWNER TO bdlab;

--
-- Name: check_responsabile(integer); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_responsabile(codice integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$

-- Ritorna 0 se l'insegnamento non esiste.

-- Ritorna 1 se l'insegnamento esiste ma non ha un docente responsabile.

-- Ritorna 2 se l'insegnamento esiste e ha un docente responsabile.

DECLARE

    docente varchar;

BEGIN

    if NOT EXISTS(

    	SELECT *

    	FROM insegnamento

    	WHERE id = codice

    ) then

        return 0;

    end if;

    SELECT responsabile into docente

    FROM insegnamento

    WHERE id = codice;

    if (docente IS NULL) then

        return 1;

    else

        return 2;

    END if;

END;

$$;


ALTER FUNCTION universita.check_responsabile(codice integer) OWNER TO bdlab;

--
-- Name: check_sovrapposizione_appello(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_sovrapposizione_appello() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

-- Un appello non è conforme se esiste un altro appello dello stesso anno e dello stesso corso di quello che si sta inserendo

DECLARE

	an integer;

	co varchar;

BEGIN

	SELECT anno, corso into an, co

	FROM insegnamento

	WHERE id = NEW.insegnamento;

	if EXISTS(

		SELECT *

		FROM appello a

		INNER JOIN insegnamento i ON a.insegnamento = i.id

		WHERE i.anno = an AND i.corso = co AND a.data::date = NEW.data::date AND (NEW.data::date <> OLD.data::date OR OLD.data IS NULL)

	) then

		raise exception 'Insegnamento dello stesso corso e anno già presente in questa data';

	end if;

	RETURN NEW;

END;

$$;


ALTER FUNCTION universita.check_sovrapposizione_appello() OWNER TO bdlab;

--
-- Name: check_storici(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.check_storici() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

	raise exception 'Impossibile modificare o eliminare questi dati';

END;

$$;


ALTER FUNCTION universita.check_storici() OWNER TO bdlab;

--
-- Name: delete_studente(integer, date, character varying); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.delete_studente(codice integer, inat date, mot character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
    old_count integer;
    new_count integer;
BEGIN
	SELECT count(*) into old_count
	FROM Studente;

    DELETE FROM studente
    WHERE matricola = codice;

    ALTER TABLE studente_storico
	DISABLE TRIGGER check_storici_trigger;

    UPDATE studente_storico
    SET inattivita = inat,
    	motivazione = mot
    WHERE matricola = codice;

    ALTER TABLE studente_storico
	ENABLE TRIGGER check_storici_trigger;

    SELECT count(*) into new_count
	FROM studente;
	
    if (old_count=new_count) then
		return FALSE;
	else
		return TRUE;
    END if;

END;
$$;


ALTER FUNCTION universita.delete_studente(codice integer, inat date, mot character varying) OWNER TO bdlab;

--
-- Name: get_carriera(integer); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.get_carriera(cod integer) RETURNS TABLE(codice integer, nome character varying, voto integer, lode boolean, data timestamp without time zone)
    LANGUAGE plpgsql
    AS $$

BEGIN

	if EXISTS(

		SELECT *

		FROM studente 

		WHERE matricola = cod

	) then

		return QUERY

		SELECT i.id, i.nome, e.voto, e.lode, e.data

		FROM esame e

		INNER JOIN insegnamento i ON i.id = e.insegnamento

		WHERE e.studente = cod

		ORDER BY e.data;

	else if EXISTS(

		SELECT *

		FROM studente_storico

		WHERE matricola = cod

	) then

		return QUERY

		SELECT i.id, i.nome, es.voto, es.lode, es.data

		FROM esame_storico es

		INNER JOIN insegnamento i ON i.id = es.insegnamento

		WHERE es.studente = cod

		ORDER BY es.data;

	else

		raise exception 'Studente inesistente';

	end if;

	end if;

	return;

END;

$$;


ALTER FUNCTION universita.get_carriera(cod integer) OWNER TO bdlab;

--
-- Name: get_carriera_valida(integer); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.get_carriera_valida(cod integer) RETURNS TABLE(codice integer, nome character varying, voto integer, lode boolean, data timestamp without time zone)
    LANGUAGE plpgsql
    AS $$

BEGIN

	if EXISTS(

		SELECT *

		FROM studente 

		WHERE matricola = cod

	) then

		return QUERY

		SELECT i.id, i.nome, e.voto, e.lode, e.data

		FROM esame e

		INNER JOIN insegnamento i ON i.id = e.insegnamento

		WHERE e.studente = cod

		AND e.data=(

			SELECT max(ee.data)

			FROM esame ee

			WHERE i.id = ee.insegnamento AND ee.studente = cod

		)

		AND e.voto >= 18

		ORDER BY e.data;

	else if EXISTS(

		SELECT *

		FROM studente_storico

		WHERE matricola = cod

	) then

		return QUERY

		SELECT i.id, i.nome, es.voto, es.lode, es.data

		FROM esame_storico es

		INNER JOIN insegnamento i ON i.id = es.insegnamento

		WHERE es.studente = cod

		AND es.data=(

			SELECT max(ees.data)

			FROM esame_storico ees

			WHERE i.id = ees.insegnamento AND ees.studente = cod

		)

		AND es.voto >= 18

		ORDER BY es.data;

	else 

		raise exception 'studente inesistente';

	end if;

	end if;

	return;

END;

$$;


ALTER FUNCTION universita.get_carriera_valida(cod integer) OWNER TO bdlab;

--
-- Name: get_data(integer); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.get_data(student integer) RETURNS TABLE(cfu bigint, average double precision)
    LANGUAGE plpgsql
    AS $$

BEGIN

	RETURN QUERY

	SELECT sum(i.CFU) as CFU, sum(c.voto*i.CFU)::float/sum(i.CFU) as average

	FROM get_carriera_valida(student) c

	INNER JOIN insegnamento i ON c.codice = i.id;

	RETURN;

END;

$$;


ALTER FUNCTION universita.get_data(student integer) OWNER TO bdlab;

--
-- Name: get_info_corso(character varying); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.get_info_corso(nome_corso character varying) RETURNS TABLE(id integer, nome character varying, anno integer, responsabile text, descrizione text)
    LANGUAGE plpgsql
    AS $$

BEGIN

	return QUERY

	SELECT i.id, i.nome, i.anno, d.nome || ' ' || d.cognome AS doc, i.descrizione

	FROM insegnamento i

	LEFT JOIN docente d ON i.responsabile = d.username

	WHERE corso = nome_corso;

	return;

END;

$$;


ALTER FUNCTION universita.get_info_corso(nome_corso character varying) OWNER TO bdlab;

--
-- Name: get_new_matricola(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.get_new_matricola() RETURNS integer
    LANGUAGE plpgsql
    AS $$

DECLARE

	output integer;

BEGIN

	WITH un AS (

		SELECT matricola

		FROM studente

		UNION

		SELECT matricola

		FROM studente_storico

	)

	SELECT max(matricola)+1 into output

	FROM un;

	RETURN output;

END;

$$;


ALTER FUNCTION universita.get_new_matricola() OWNER TO bdlab;

--
-- Name: get_propedeuticita(integer); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.get_propedeuticita(codice_insegnamento integer) RETURNS TABLE(requisito integer, nome character varying)
    LANGUAGE plpgsql
    AS $$

BEGIN

	return QUERY

	SELECT i.id, i.nome

	FROM insegnamento i

	INNER JOIN propedeuticita p ON p.requisito = i.id

	WHERE p.insegnamento = codice_insegnamento;

	return;

END;

$$;


ALTER FUNCTION universita.get_propedeuticita(codice_insegnamento integer) OWNER TO bdlab;

--
-- Name: spostamento_dati_studente(); Type: FUNCTION; Schema: universita; Owner: bdlab
--

CREATE FUNCTION universita.spostamento_dati_studente() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

BEGIN

	INSERT INTO studente_storico

	(

		SELECT *, NULL, NULL

		FROM studente

		WHERE matricola = OLD.matricola

	);

	INSERT INTO esame_storico

	(

		SELECT *

		FROM esame

		WHERE studente = OLD.matricola

	);

	ALTER TABLE esame

	DISABLE TRIGGER check_disiscrizione_esame_trigger;

	DELETE FROM esame

	WHERE studente = OLD.matricola;

	ALTER TABLE esame

	ENABLE TRIGGER check_disiscrizione_esame_trigger;

	return OLD;

END;

$$;


ALTER FUNCTION universita.spostamento_dati_studente() OWNER TO bdlab;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: appello; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.appello (
    data timestamp without time zone NOT NULL,
    insegnamento integer NOT NULL,
    luogo character varying(30) NOT NULL
);


ALTER TABLE universita.appello OWNER TO bdlab;

--
-- Name: corso; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.corso (
    nome character varying(100) NOT NULL,
    durata integer NOT NULL,
    descrizione text,
    CONSTRAINT corso_durata_check CHECK (((durata = 2) OR (durata = 3)))
);


ALTER TABLE universita.corso OWNER TO bdlab;

--
-- Name: docente; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.docente (
    username character varying(200) NOT NULL,
    password character varying(100) NOT NULL,
    nome character varying(100) NOT NULL,
    cognome character varying(50) NOT NULL,
    nascita date NOT NULL,
    sesso character varying(10),
    indirizzo character varying(50),
    CONSTRAINT docente_nascita_check CHECK ((nascita <= (now())::date)),
    CONSTRAINT docente_sesso_check CHECK (((sesso)::text = ANY (ARRAY[('Femmina'::character varying)::text, ('Maschio'::character varying)::text])))
);


ALTER TABLE universita.docente OWNER TO bdlab;

--
-- Name: esame; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.esame (
    data timestamp without time zone NOT NULL,
    insegnamento integer NOT NULL,
    studente integer NOT NULL,
    voto integer,
    lode boolean,
    CONSTRAINT esame_check CHECK (((NOT lode) OR (voto = 30))),
    CONSTRAINT esame_voto_check CHECK (((voto >= 0) AND (voto <= 30)))
);


ALTER TABLE universita.esame OWNER TO bdlab;

--
-- Name: esame_storico; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.esame_storico (
    data timestamp without time zone NOT NULL,
    insegnamento integer NOT NULL,
    studente integer NOT NULL,
    voto integer,
    lode boolean,
    CONSTRAINT esame_storico_check CHECK (((NOT lode) OR (voto = 30))),
    CONSTRAINT esame_storico_voto_check CHECK (((voto >= 0) AND (voto <= 30)))
);


ALTER TABLE universita.esame_storico OWNER TO bdlab;

--
-- Name: insegnamento; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.insegnamento (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    anno integer NOT NULL,
    cfu integer NOT NULL,
    descrizione text,
    responsabile character varying(200),
    corso character varying(100) NOT NULL
);


ALTER TABLE universita.insegnamento OWNER TO bdlab;

--
-- Name: insegnamento_id_seq; Type: SEQUENCE; Schema: universita; Owner: bdlab
--

CREATE SEQUENCE universita.insegnamento_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE universita.insegnamento_id_seq OWNER TO bdlab;

--
-- Name: insegnamento_id_seq; Type: SEQUENCE OWNED BY; Schema: universita; Owner: bdlab
--

ALTER SEQUENCE universita.insegnamento_id_seq OWNED BY universita.insegnamento.id;


--
-- Name: propedeuticita; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.propedeuticita (
    insegnamento integer NOT NULL,
    requisito integer NOT NULL,
    CONSTRAINT propedeuticita_check CHECK ((insegnamento <> requisito))
);


ALTER TABLE universita.propedeuticita OWNER TO bdlab;

--
-- Name: segreteria; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.segreteria (
    username character varying(200) NOT NULL,
    password character varying(100) NOT NULL,
    nome character varying(100) NOT NULL,
    cognome character varying(50) NOT NULL,
    nascita date NOT NULL,
    sesso character varying(10),
    indirizzo character varying(50),
    CONSTRAINT segreteria_nascita_check CHECK ((nascita <= (now())::date)),
    CONSTRAINT segreteria_sesso_check CHECK (((sesso)::text = ANY (ARRAY[('Femmina'::character varying)::text, ('Maschio'::character varying)::text])))
);


ALTER TABLE universita.segreteria OWNER TO bdlab;

--
-- Name: studente; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.studente (
    matricola integer NOT NULL,
    username character varying(200) NOT NULL,
    password character varying(100) NOT NULL,
    nome character varying(100) NOT NULL,
    cognome character varying(50) NOT NULL,
    nascita date NOT NULL,
    sesso character varying(10),
    indirizzo character varying(50),
    iscrizione date DEFAULT (now())::date NOT NULL,
    corso character varying(100) NOT NULL,
    CONSTRAINT studente_iscrizione_check CHECK ((iscrizione <= (now())::date)),
    CONSTRAINT studente_nascita_check CHECK ((nascita <= (now())::date)),
    CONSTRAINT studente_sesso_check CHECK (((sesso)::text = ANY (ARRAY[('Femmina'::character varying)::text, ('Maschio'::character varying)::text])))
);


ALTER TABLE universita.studente OWNER TO bdlab;

--
-- Name: studente_storico; Type: TABLE; Schema: universita; Owner: bdlab
--

CREATE TABLE universita.studente_storico (
    matricola integer NOT NULL,
    username character varying(200) NOT NULL,
    password character varying(100) NOT NULL,
    nome character varying(100) NOT NULL,
    cognome character varying(50) NOT NULL,
    nascita date NOT NULL,
    sesso character varying(10),
    indirizzo character varying(50),
    iscrizione date NOT NULL,
    corso character varying(100) NOT NULL,
    inattivita date DEFAULT (now())::date,
    motivazione character varying(15),
    CONSTRAINT studente_storico_inattivita_check CHECK ((inattivita <= (now())::date)),
    CONSTRAINT studente_storico_iscrizione_check CHECK ((iscrizione <= (now())::date)),
    CONSTRAINT studente_storico_nascita_check CHECK ((nascita <= (now())::date)),
    CONSTRAINT studente_storico_sesso_check CHECK (((sesso)::text = ANY (ARRAY[('Femmina'::character varying)::text, ('Maschio'::character varying)::text])))
);


ALTER TABLE universita.studente_storico OWNER TO bdlab;

--
-- Name: insegnamento id; Type: DEFAULT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.insegnamento ALTER COLUMN id SET DEFAULT nextval('universita.insegnamento_id_seq'::regclass);


--
-- Data for Name: appello; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.appello VALUES ('2023-09-20 14:00:00', 2, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2022-01-20 08:30:00', 4, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2022-02-18 08:30:00', 4, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2022-01-15 09:30:00', 3, 'Online');
INSERT INTO universita.appello VALUES ('2022-06-22 09:30:00', 6, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2022-07-04 08:30:00', 7, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2022-06-21 13:30:00', 8, 'Via Golgi 19');
INSERT INTO universita.appello VALUES ('2023-01-17 08:30:00', 9, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-01-31 14:30:00', 9, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-03-23 10:30:00', 9, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-02-22 13:30:00', 10, 'Via Venezian 15');
INSERT INTO universita.appello VALUES ('2023-06-19 16:00:00', 10, 'Via Venezian 15');
INSERT INTO universita.appello VALUES ('2023-09-18 14:00:00', 10, 'Via Venezian 15');
INSERT INTO universita.appello VALUES ('2023-01-18 14:00:00', 11, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-03-01 14:00:00', 11, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-06-26 14:30:00', 12, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-07-25 14:30:00', 12, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-07-07 14:30:00', 12, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-06-22 14:30:00', 13, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-07-06 14:30:00', 13, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-09-13 13:30:00', 13, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-06-30 13:00:00', 18, 'Festa del Perdono');
INSERT INTO universita.appello VALUES ('2023-09-30 11:00:00', 18, 'Festa del Perdono');
INSERT INTO universita.appello VALUES ('2023-06-12 08:30:00', 5, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2022-02-24 14:30:00', 2, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-10-11 08:10:00', 2, 'Via Celoria 18');
INSERT INTO universita.appello VALUES ('2023-09-05 08:30:00', 12, 'Via Celoria 18');


--
-- Data for Name: corso; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.corso VALUES ('Informatica', 3, 'Gli obiettivi del corso di laurea in Informatica sono: da una parte fornire una solida conoscenza di base metodologica dei principali settori delle scienze informatiche e matematiche e dall''altra fornire una buona padronanza delle metodologie e tecnologie proprie dell''informatica, offrendo una preparazione adeguata per imparare e conoscere i diversi ambiti applicativi della disciplina e poter assimilare, comprendere e valutare l''impatto dei costanti progressi scientifici e tecnologie nell''ambito della disciplina.');
INSERT INTO universita.corso VALUES ('Comunicazione, Tecnologie e Culture digitali', 2, 'Il Corso di laurea triennale in Comunicazione, tecnologie e culture digitali prepara laureati in possesso di un''adeguata padronanza delle basi scientifiche e dei concetti essenziali delle discipline umane e sociali connesse ad una specifica competenza nell''area della comunicazione, dei media, delle tecnologie e delle culture digitali, dei sistemi di informazione e dell''industria culturale.');


--
-- Data for Name: docente; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.docente VALUES ('nicola.basilico@unimi.it', '61a6d740727bec7b8468947cd317f7f2', 'Nicola', 'Basilico', '1995-05-14', 'Maschio', 'Via Goffredo Mameli 3');
INSERT INTO universita.docente VALUES ('cecilia.cavaterra@unimi.it', 'd90f939262fe6b00fd71f188fc2afe94', 'Cecilia', 'Cavaterra', '1973-03-27', 'Femmina', 'Corso Monforte 21');
INSERT INTO universita.docente VALUES ('paolo.boldi@unimi.it', '65ae3209dcf0deacfa4f21d058621f38', 'Paolo', 'Boldi', '1983-04-18', 'Maschio', 'Via Collodi 5');
INSERT INTO universita.docente VALUES ('beatrice.palano@unimi.it', 'bacf20892597c70b4f15fb78c16c6396', 'Beatrice', 'Palano', '1980-07-06', 'Femmina', 'Via Savona 2');
INSERT INTO universita.docente VALUES ('stefano.aguzzoli@unimi.it', '7a1b068544821ef4135a145ac8ecb37b', 'Stefano', 'Aguzzoli', '1975-10-23', 'Maschio', 'Via Bergognone 39');
INSERT INTO universita.docente VALUES ('alice.garbagnati@unimi.it', 'd93b52d06db9a03b1763ecb725ab45ba', 'Alice', 'Garbagnati', '1988-12-07', 'Femmina', 'Via Stendhal 30');
INSERT INTO universita.docente VALUES ('giovanni.pighizzini@unimi.it', '451392f713c2084b5263723a92ec5ff8', 'Giovanni', 'Pighizzini', '1969-09-09', 'Maschio', 'Via Pesto 17');
INSERT INTO universita.docente VALUES ('valerio.bellandi@unimi.it', 'eadd5ecf28163e7a41ae15c2463ef85a', 'Valerio', 'Bellandi', '1992-02-18', 'Maschio', 'Via Giambellino 102');
INSERT INTO universita.docente VALUES ('massimo.santini@unimi.it', '619f3baca4770611495aa6d1b61b3749', 'Massimo', 'Santini', '1984-01-16', 'Maschio', 'Via dei Giacinti 30');
INSERT INTO universita.docente VALUES ('vincenzo.piuri@unimi.it', 'c66675c8684bf9f2fe1c1bcc8964d246', 'Vincenzo', 'Piuri', '1986-11-16', 'Maschio', 'Via Angelo Inganni 45');
INSERT INTO universita.docente VALUES ('dario.malchiodi@unimi.it', 'c3c99186e9ccc26223c0227bdcc7d523', 'Dario', 'Malchiodi', '1990-10-14', 'Maschio', 'Via Lucca 26');
INSERT INTO universita.docente VALUES ('mattia.monga@unimi.it', '23f1bf45da02024f5e6cceacfe66aed4', 'Mattia', 'Monga', '1986-09-15', 'Maschio', 'Via Pitagora 9');
INSERT INTO universita.docente VALUES ('elena.pagani@unimi.it', '2b69ac84373aa00a52723afbd5d49f73', 'Elena', 'Pagani', '1975-08-15', 'Femmina', 'Via Italia 22');


--
-- Data for Name: esame; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.esame VALUES ('2023-09-18 14:00:00', 10, 1, NULL, NULL);
INSERT INTO universita.esame VALUES ('2022-06-22 09:30:00', 6, 1, 30, false);
INSERT INTO universita.esame VALUES ('2022-07-04 08:30:00', 7, 1, 23, false);
INSERT INTO universita.esame VALUES ('2022-06-21 13:30:00', 8, 1, 30, true);
INSERT INTO universita.esame VALUES ('2022-01-20 08:30:00', 4, 1, 14, false);
INSERT INTO universita.esame VALUES ('2023-01-17 08:30:00', 9, 1, 12, false);
INSERT INTO universita.esame VALUES ('2023-01-31 14:30:00', 9, 1, 7, false);
INSERT INTO universita.esame VALUES ('2023-03-23 10:30:00', 9, 1, 22, false);
INSERT INTO universita.esame VALUES ('2023-01-18 14:00:00', 11, 1, 0, false);
INSERT INTO universita.esame VALUES ('2023-03-01 14:00:00', 11, 1, 24, false);
INSERT INTO universita.esame VALUES ('2023-02-22 13:30:00', 10, 1, 30, false);
INSERT INTO universita.esame VALUES ('2023-06-19 16:00:00', 10, 1, 16, false);
INSERT INTO universita.esame VALUES ('2023-06-22 14:30:00', 13, 1, 13, false);
INSERT INTO universita.esame VALUES ('2023-07-25 14:30:00', 12, 1, 14, false);
INSERT INTO universita.esame VALUES ('2022-01-15 09:30:00', 3, 1, 28, false);
INSERT INTO universita.esame VALUES ('2023-06-30 13:00:00', 18, 2, 30, false);
INSERT INTO universita.esame VALUES ('2022-02-18 08:30:00', 4, 1, 30, true);
INSERT INTO universita.esame VALUES ('2022-02-24 14:30:00', 2, 1, 27, false);
INSERT INTO universita.esame VALUES ('2023-09-13 13:30:00', 13, 1, NULL, NULL);
INSERT INTO universita.esame VALUES ('2023-06-26 14:30:00', 12, 1, 10, false);
INSERT INTO universita.esame VALUES ('2023-06-26 14:30:00', 12, 4, NULL, NULL);


--
-- Data for Name: esame_storico; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.esame_storico VALUES ('2022-02-18 08:30:00', 4, 3, 28, false);
INSERT INTO universita.esame_storico VALUES ('2023-06-19 16:00:00', 10, 3, 23, false);
INSERT INTO universita.esame_storico VALUES ('2023-09-18 14:00:00', 10, 3, NULL, NULL);
INSERT INTO universita.esame_storico VALUES ('2023-09-20 14:00:00', 2, 5, 24, false);
INSERT INTO universita.esame_storico VALUES ('2023-10-11 08:10:00', 2, 5, NULL, NULL);


--
-- Data for Name: insegnamento; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.insegnamento VALUES (4, 'Programmazione 1', 1, 12, 'Obiettivo dell''insegnamento e'' introdurre gli studenti alla programmazione imperativa strutturata e al problem solving in piccolo.', 'paolo.boldi@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (6, 'Linguaggi Formali e Automi', 1, 6, 'L''insegnamento si prefigge il compito di presentare i concetti della teoria dei linguaggi formali e degli automi centrali in svariati ambiti del contesto informatico attuale, abituando lo studente all''uso di metodi formali.', 'beatrice.palano@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (13, 'Statistica e Analisi dei Dati', 2, 6, 'L''insegnamento ha lo scopo di introdurre i concetti fondamentali della statistica descrittiva, del calcolo delle probabilità e della statistica inferenziale parametrica.', 'dario.malchiodi@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (3, 'Matematica del Continuo', 1, 12, 'L''obiettivo dell''insegnamento è duplice. Anzitutto, fornire agli studenti un linguaggio matematico di base, che li metta in grado di formulare correttamente un problema e di comprendere un problema formulato da altri. Inoltre, fornire gli strumenti matematici indispensabili per la soluzione di alcuni problemi specifici, che spaziano dal comportamento delle successioni a quello delle serie e delle funzioni ad una variabile.', 'cecilia.cavaterra@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (5, 'Architettura degli Elaboratori 2', 1, 6, 'L''insegnamento fornisce la conoscenza del funzionamento delle architetture digitali approfondendo in particolare la pipe-line, i multi-core e le gerarchie di memoria in modo da potere capire a fondo le problematiche legate ai sistemi operativi e all''ottimizzazione del software. Vengono forniti gli strumenti per valutare le prestazioni dei calcolatori e per ottimizzare le applicazioni.', 'nicola.basilico@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (14, 'Ingegneria del Software', 3, 12, 'L''obiettivo dell''insegnamento è fornire agli studenti la conoscenza dei modelli e degli strumenti per l''analisi, il progetto, lo sviluppo e il collaudo dei sistemi software, e di metterli in grado di progettare, sviluppare e collaudare sistemi software.', 'mattia.monga@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (8, 'Matematica del Discreto', 1, 6, 'Gli obiettivi principali dell''insegnamento sono di introdurre il linguaggio dell''algebra e le nozioni di spazio vettoriale e applicazioni lineari e di analizzare il problema della risolubilità dei sistemi di equazioni lineari (anche da un punto di vista algoritmico)', 'alice.garbagnati@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (15, 'Reti di Calcolatori', 3, 12, 'L''insegnamento di reti di calcolatori è il corso di introduzione al networking, ed ha come principale obiettivo quello di fornire i principi dei protocolli e della architettura della rete internet occupandosi di servizi e protocolli ad ogni livello dell''architettura funzionale', 'elena.pagani@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (9, 'Algoritmi e Strutture Dati', 2, 12, 'L''insegnamento ha lo scopo di introdurre i concetti fondamentali riguardanti il progetto e l''analisi di algoritmi e delle strutture dati che essi utilizzano, illustrando le principali tecniche di progettazione e alcune strutture dati fondamentali, insieme all''analisi della complessità computazionale.', 'giovanni.pighizzini@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (7, 'Logica Matematica', 1, 6, 'L''insegnamento ha lo scopo di introdurre i principi fondamentali del ragionamento razionale, tramite l''approccio formale fornito dalla logica matemaica, sia a livello proposizionale che a livello predicativo.', 'stefano.aguzzoli@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (17, 'Fondamenti di Informatica', 2, 6, 'Informatica', 'paolo.boldi@unimi.it', 'Comunicazione, Tecnologie e Culture digitali');
INSERT INTO universita.insegnamento VALUES (12, 'Basi di Dati', 2, 12, 'L''insegnamento fornisce i concetti fondamentali relativi alle basi di dati e ai sistemi per la loro gestione, con particolare riguardo ai sistemi di basi di dati relazionali. Il corso prevede i) una parte di teoria dedicata a modelli, linguaggi, metodologie di progettazione e agli aspetti di sicurezza e transazioni, e ii) una parte di laboratorio dedicata all''uso di strumenti di progettazione e gestione di basi di dati relazionali e alle principali tecnologie di basi di dati e Web.', 'valerio.bellandi@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (10, 'Sistemi Operativi', 2, 12, 'L''insegnamento si propone di fornire le conoscenze sui fondamenti teorici, gli algoritmi e le tecnologie riguardanti l''architettura complessiva e la gestione del processore, della memoria centrale, dei dispositivi di ingresso/uscita, del file system, dell''interfaccia utente e degli ambienti distribuiti nei sistemi operativi per le principali tipologie di architetture di elaborazione.', 'vincenzo.piuri@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (11, 'Programmazione 2', 2, 6, 'L''insegnamento, che si colloca nel percorso ideale iniziato dall''insegnamento di "Programmazione" e che proseguirà nell''insegnamento di "Ingegneria del software", ha l''obiettivo di presentare alcune astrazioni e concetti utili al progetto, sviluppo e manutenzione di programmi di grandi dimensioni. L''attenzione è focalizzata sul paradigma orientato agli oggetti, con particolare enfasi riguardo al processo di specificazione, modellazione dei tipi di dato e progetto.', 'massimo.santini@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (18, 'Sociologia dell''ambiente', 2, 6, 'Analisi qualitativa dell''azione sociale sul territorio e sull''ambiente', 'nicola.basilico@unimi.it', 'Comunicazione, Tecnologie e Culture digitali');
INSERT INTO universita.insegnamento VALUES (2, 'Architettura degli Elaboratori 1', 1, 6, 'L''insegnamento introduce le conoscenze dei principi che sottendono al funzionamento di un elaboratore digitale; partendo dal livello delle porte logiche si arriva, attraverso alcuni livelli di astrazione intermedi, alla progettazione di ALU firmware e di un''architettura MIPS in grado di eseguire il nucleo delle istruzioni in linguaggio macchina.', 'nicola.basilico@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (16, 'Fondamenti di Scienze Sociali', 1, 9, 'Descrizione', 'beatrice.palano@unimi.it', 'Comunicazione, Tecnologie e Culture digitali');
INSERT INTO universita.insegnamento VALUES (24, 'Tecnologie e Linguaggi per il Web', 2, 6, 'Il corso introduce le principali tecnologie del Web. In particolare, introduce elementi dell''evoluzione del Web, principi per la progettazione di applicazioni, dei pattern ricorrenti nel loro sviluppo e conoscenza delle tecnologie esistenti.', 'valerio.bellandi@unimi.it', 'Informatica');
INSERT INTO universita.insegnamento VALUES (23, 'Editoria Digitale', 2, 6, 'Lo scopo dell''insegnamento è quello di introdurre lo studente ai concetti fondamentali dell''editoria tradizionale focalizzando poi l''attenzione sui formati e sulle peculiarità dell''editoria digitale con particolare dettaglio sulle tecnologie utilizzate.', NULL, 'Informatica');


--
-- Data for Name: propedeuticita; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.propedeuticita VALUES (11, 4);
INSERT INTO universita.propedeuticita VALUES (10, 4);
INSERT INTO universita.propedeuticita VALUES (13, 3);
INSERT INTO universita.propedeuticita VALUES (12, 4);
INSERT INTO universita.propedeuticita VALUES (14, 4);
INSERT INTO universita.propedeuticita VALUES (9, 4);
INSERT INTO universita.propedeuticita VALUES (17, 16);
INSERT INTO universita.propedeuticita VALUES (15, 10);
INSERT INTO universita.propedeuticita VALUES (15, 5);


--
-- Data for Name: segreteria; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.segreteria VALUES ('admin', '189bbbb00c5f1fb7fba9ad9285f193d1', 'admin', 'admin', '2023-08-19', NULL, NULL);


--
-- Data for Name: studente; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.studente VALUES (2, 'antonio.fedele@studenti.unimi.it', '85f3c35de5e6ac28e4d31b17cbb529d3', 'Antonio', 'Fedele', '2000-11-06', 'Maschio', 'Via Trento 1 Fondi', '2023-08-25', 'Comunicazione, Tecnologie e Culture digitali');
INSERT INTO universita.studente VALUES (4, 'michelafrancesca.firrera@studenti.unimi.it', 'cfaf278e8f522c72644cee2a753d2845', 'Michela Francesca', 'Firrera', '2002-12-14', 'Femmina', 'Via Nicolò Barabino 7 Milano', '2022-09-28', 'Informatica');
INSERT INTO universita.studente VALUES (1, 'simonealessandro.casciaro@studenti.unimi.it', '807d3900c53dd395ce5f5eedd1d8e6b4', 'Simone Alessandro', 'Casciaro', '2001-09-12', 'Maschio', 'Via Fratelli Capelli 6 Marcignago 27020', '2021-09-28', 'Informatica');


--
-- Data for Name: studente_storico; Type: TABLE DATA; Schema: universita; Owner: bdlab
--

INSERT INTO universita.studente_storico VALUES (3, 'mattia.decampo@studenti.unimi.it', '27cd7bbd58a5300ee420d18cad991c2d', 'Mattia', 'De Campo', '1999-07-26', 'Maschio', 'Via XX settembre 23A', '2023-08-25', 'Informatica', '2023-08-25', 'Rinuncia');
INSERT INTO universita.studente_storico VALUES (5, 'luca.airoldi4@studenti.unimi.it', '6d9680bc454d08ab6ef271573ef4b268', 'Luca', 'Airoldi', '2002-04-20', 'Maschio', 'Viale Montenero', '2021-09-28', 'Informatica', '2023-08-29', 'Rinuncia');


--
-- Name: insegnamento_id_seq; Type: SEQUENCE SET; Schema: universita; Owner: bdlab
--

SELECT pg_catalog.setval('universita.insegnamento_id_seq', 24, true);


--
-- Name: appello appello_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.appello
    ADD CONSTRAINT appello_pkey PRIMARY KEY (data, insegnamento);


--
-- Name: corso corso_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.corso
    ADD CONSTRAINT corso_pkey PRIMARY KEY (nome);


--
-- Name: docente docente_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.docente
    ADD CONSTRAINT docente_pkey PRIMARY KEY (username);


--
-- Name: esame esame_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.esame
    ADD CONSTRAINT esame_pkey PRIMARY KEY (data, insegnamento, studente);


--
-- Name: esame_storico esame_storico_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.esame_storico
    ADD CONSTRAINT esame_storico_pkey PRIMARY KEY (data, insegnamento, studente);


--
-- Name: insegnamento insegnamento_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.insegnamento
    ADD CONSTRAINT insegnamento_pkey PRIMARY KEY (id);


--
-- Name: propedeuticita propedeuticita_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.propedeuticita
    ADD CONSTRAINT propedeuticita_pkey PRIMARY KEY (insegnamento, requisito);


--
-- Name: segreteria segreteria_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.segreteria
    ADD CONSTRAINT segreteria_pkey PRIMARY KEY (username);


--
-- Name: studente studente_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.studente
    ADD CONSTRAINT studente_pkey PRIMARY KEY (matricola);


--
-- Name: studente_storico studente_storico_pkey; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.studente_storico
    ADD CONSTRAINT studente_storico_pkey PRIMARY KEY (matricola);


--
-- Name: studente_storico studente_storico_username_key; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.studente_storico
    ADD CONSTRAINT studente_storico_username_key UNIQUE (username);


--
-- Name: studente studente_username_key; Type: CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.studente
    ADD CONSTRAINT studente_username_key UNIQUE (username);


--
-- Name: docente check_delete_docente_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_delete_docente_trigger BEFORE DELETE ON universita.docente FOR EACH ROW EXECUTE FUNCTION universita.check_delete_docente();


--
-- Name: insegnamento check_delete_insegnamento_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_delete_insegnamento_trigger BEFORE DELETE ON universita.insegnamento FOR EACH ROW EXECUTE FUNCTION universita.check_delete_insegnamento();


--
-- Name: esame check_disiscrizione_esame_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_disiscrizione_esame_trigger BEFORE DELETE ON universita.esame FOR EACH ROW EXECUTE FUNCTION universita.check_disiscrizione_esame();


--
-- Name: corso check_durata_corso_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_durata_corso_trigger BEFORE UPDATE ON universita.corso FOR EACH ROW EXECUTE FUNCTION universita.check_durata_corso();


--
-- Name: insegnamento check_durata_insegnamento_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_durata_insegnamento_trigger BEFORE INSERT OR UPDATE ON universita.insegnamento FOR EACH ROW EXECUTE FUNCTION universita.check_durata_insegnamento();


--
-- Name: appello check_inserimento_appello_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_inserimento_appello_trigger BEFORE INSERT OR UPDATE ON universita.appello FOR EACH ROW EXECUTE FUNCTION universita.check_inserimento_appello();


--
-- Name: studente check_inserimento_studente_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_inserimento_studente_trigger BEFORE INSERT OR UPDATE ON universita.studente FOR EACH ROW EXECUTE FUNCTION universita.check_inserimento_studente();


--
-- Name: esame check_inserimento_voti_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_inserimento_voti_trigger BEFORE UPDATE ON universita.esame FOR EACH ROW EXECUTE FUNCTION universita.check_inserimento_voti();


--
-- Name: esame check_iscrizione_esami_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_iscrizione_esami_trigger BEFORE INSERT ON universita.esame FOR EACH ROW EXECUTE FUNCTION universita.check_iscrizione_esami();


--
-- Name: insegnamento check_numero_insegnamenti_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_numero_insegnamenti_trigger BEFORE INSERT OR UPDATE ON universita.insegnamento FOR EACH ROW EXECUTE FUNCTION universita.check_numero_insegnamenti();


--
-- Name: propedeuticita check_propedeuticita_corretta_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_propedeuticita_corretta_trigger BEFORE INSERT OR UPDATE ON universita.propedeuticita FOR EACH ROW EXECUTE FUNCTION universita.check_propedeuticita_corretta();


--
-- Name: appello check_sovrapposizione_appello_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_sovrapposizione_appello_trigger BEFORE INSERT OR UPDATE ON universita.appello FOR EACH ROW EXECUTE FUNCTION universita.check_sovrapposizione_appello();


--
-- Name: esame_storico check_storici_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_storici_trigger BEFORE DELETE OR UPDATE ON universita.esame_storico FOR EACH ROW EXECUTE FUNCTION universita.check_storici();


--
-- Name: studente_storico check_storici_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER check_storici_trigger BEFORE DELETE OR UPDATE ON universita.studente_storico FOR EACH ROW EXECUTE FUNCTION universita.check_storici();


--
-- Name: studente spostamento_dati_studente_trigger; Type: TRIGGER; Schema: universita; Owner: bdlab
--

CREATE TRIGGER spostamento_dati_studente_trigger BEFORE DELETE ON universita.studente FOR EACH ROW EXECUTE FUNCTION universita.spostamento_dati_studente();


--
-- Name: appello appello_insegnamento_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.appello
    ADD CONSTRAINT appello_insegnamento_fkey FOREIGN KEY (insegnamento) REFERENCES universita.insegnamento(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: esame esame_data_insegnamento_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.esame
    ADD CONSTRAINT esame_data_insegnamento_fkey FOREIGN KEY (data, insegnamento) REFERENCES universita.appello(data, insegnamento) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: esame_storico esame_storico_data_insegnamento_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.esame_storico
    ADD CONSTRAINT esame_storico_data_insegnamento_fkey FOREIGN KEY (data, insegnamento) REFERENCES universita.appello(data, insegnamento) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: esame_storico esame_storico_studente_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.esame_storico
    ADD CONSTRAINT esame_storico_studente_fkey FOREIGN KEY (studente) REFERENCES universita.studente_storico(matricola) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: esame esame_studente_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.esame
    ADD CONSTRAINT esame_studente_fkey FOREIGN KEY (studente) REFERENCES universita.studente(matricola) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: insegnamento insegnamento_corso_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.insegnamento
    ADD CONSTRAINT insegnamento_corso_fkey FOREIGN KEY (corso) REFERENCES universita.corso(nome) ON UPDATE CASCADE;


--
-- Name: insegnamento insegnamento_responsabile_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.insegnamento
    ADD CONSTRAINT insegnamento_responsabile_fkey FOREIGN KEY (responsabile) REFERENCES universita.docente(username) ON UPDATE CASCADE;


--
-- Name: propedeuticita propedeuticita_insegnamento_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.propedeuticita
    ADD CONSTRAINT propedeuticita_insegnamento_fkey FOREIGN KEY (insegnamento) REFERENCES universita.insegnamento(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: propedeuticita propedeuticita_requisito_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.propedeuticita
    ADD CONSTRAINT propedeuticita_requisito_fkey FOREIGN KEY (requisito) REFERENCES universita.insegnamento(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: studente studente_corso_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.studente
    ADD CONSTRAINT studente_corso_fkey FOREIGN KEY (corso) REFERENCES universita.corso(nome) ON UPDATE CASCADE;


--
-- Name: studente_storico studente_storico_corso_fkey; Type: FK CONSTRAINT; Schema: universita; Owner: bdlab
--

ALTER TABLE ONLY universita.studente_storico
    ADD CONSTRAINT studente_storico_corso_fkey FOREIGN KEY (corso) REFERENCES universita.corso(nome) ON UPDATE CASCADE;


--
-- PostgreSQL database dump complete
--


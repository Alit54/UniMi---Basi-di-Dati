-- Funzione che produce la carriera valida di uno studente attivo (Modificare per far sì che prenda studenti inattivi?)
CREATE or REPLACE FUNCTION get_carriera(cod integer) RETURNS TABLE(codice integer, nome varchar(100), voto integer, lode boolean, data timestamp) AS $$

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
$$ language 'plpgsql';

-- Funzione che produce la carriera valida di uno studente attivo (Modificare per far sì che prenda studenti inattivi?)
CREATE or REPLACE FUNCTION get_carriera_valida(cod integer) RETURNS TABLE(codice integer, nome varchar(100), voto integer, lode boolean, data timestamp) AS $$
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
$$ language 'plpgsql';

-- Funzione che restituisce le propedeuticità di un esame
CREATE or REPLACE FUNCTION get_propedeuticita(codice_insegnamento integer) RETURNS TABLE(requisito integer, nome varchar(100)) AS $$
BEGIN
	return QUERY

	SELECT i.id, i.nome
	FROM insegnamento i
	INNER JOIN propedeuticita p ON p.requisito = i.id
	WHERE p.insegnamento = codice_insegnamento;

	return;

END;
$$ language 'plpgsql';

-- Funzione che restituisce le informazioni relative a un corso di laurea
CREATE or REPLACE FUNCTION get_info_corso(nome_corso varchar) RETURNS TABLE(id integer, nome varchar, anno integer, responsabile text, descrizione text) AS $$
BEGIN
	return QUERY

	SELECT i.id, i.nome, i.anno, d.nome || ' ' || d.cognome AS doc, i.descrizione
	FROM insegnamento i
	LEFT JOIN docente d ON i.responsabile = d.username
	WHERE corso = nome_corso;

	return;

END;
$$ language 'plpgsql';



-- Funzione che restituisce i CFU e la media di uno studente
CREATE or REPLACE FUNCTION get_data(student integer) RETURNS TABLE(CFU bigint, average float) AS $$
BEGIN

	RETURN QUERY
	
	SELECT sum(i.CFU) as CFU, sum(c.voto*i.CFU)::float/sum(i.CFU) as average
	FROM get_carriera_valida(student) c
	INNER JOIN insegnamento i ON c.codice = i.id;

	RETURN;

END;
$$ language 'plpgsql';

-- Funzione per ottenere il prossimo valore di matricola
CREATE or REPLACE FUNCTION get_new_matricola() RETURNS integer AS $$
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
$$ language 'plpgsql';
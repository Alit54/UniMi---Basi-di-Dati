-- Funzione che controlla se il vincolo dell'anno previsto rimane verificato anche in caso di update del corso
CREATE or REPLACE FUNCTION check_durata_corso() RETURNS TRIGGER AS $$
BEGIN
	
	if EXISTS(
		SELECT *
		FROM insegnamento
		WHERE corso = OLD.nome AND anno > NEW.durata
	) then raise exception 'Ci sono insegnamenti appartenenti al corso non conformi alla modifica!';
	end if;

	RETURN NEW;
END;
$$ language 'plpgsql';

-- Funzione che controlla se l'anno previsto dall'insegnamento è conforme al corso associatogli
CREATE or REPLACE FUNCTION check_durata_insegnamento() RETURNS TRIGGER AS $$
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
$$ language 'plpgsql';

-- Funzione che controlla se il docente ha al massimo 3 insegnamenti
CREATE or REPLACE FUNCTION check_numero_insegnamenti() RETURNS TRIGGER AS $$
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
$$ language 'plpgsql';

-- Funzione che controlla che l'unicità di matricola e username sia rispettata anche considerando la tabella 'studente_storico'
CREATE or REPLACE FUNCTION check_inserimento_studente() RETURNS TRIGGER AS $$
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
$$ language 'plpgsql';

-- Controlliamo che lo studente si iscriva a un esame previsto dal suo corso di laurea
CREATE or REPLACE FUNCTION check_iscrizione_esami() RETURNS TRIGGER AS $$
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
$$ language 'plpgsql';

-- Funzione che controlla se tutte le propedeuticità di un esame sono rispettate dallo studente
CREATE or REPLACE FUNCTION check_propedeuticita(codice_insegnamento integer, matricola integer) RETURNS BOOLEAN AS $$
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
$$ language 'plpgsql';


-- Funzione che controlla se la propedeuticità è valida (I due insegnamenti devono far parte dello stesso corso)
CREATE or REPLACE FUNCTION check_propedeuticita_corretta() RETURNS TRIGGER AS $$
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
$$ language 'plpgsql';

-- Funzione che controlla se un insegnamento esiste e non è assegnato ad un docente
CREATE or REPLACE FUNCTION check_responsabile(codice integer) RETURNS integer AS $$
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
$$ language 'plpgsql';

-- Funzione che controlla che l'inserimento di un appello sia conforme al regolamento
CREATE or REPLACE FUNCTION check_sovrapposizione_appello() RETURNS TRIGGER AS $$
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
$$ language 'plpgsql';

-- Funzione che rende a NULL tutti i campi 'responsabile' degli insegnamenti associati al docente eliminato
CREATE or REPLACE FUNCTION check_delete_docente() RETURNS TRIGGER AS $$
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
$$ language 'plpgsql';

-- Funzione che impedisce l'eliminazione di un insegnamento se qualche studente ha già affrontato l'esame
CREATE or REPLACE FUNCTION check_delete_insegnamento() RETURNS TRIGGER AS $$
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
$$ language 'plpgsql';

-- Funzione che impedisce l'inserimento di un appello con data precedente a oggi
CREATE or REPLACE FUNCTION check_inserimento_appello() RETURNS TRIGGER AS $$
BEGIN
	
	if (NEW.data::date < NOW()::date)
		then raise exception 'Impossibile inserire un appello precedente a oggi';
	end if;

	RETURN NEW;
END;
$$ language 'plpgsql';

-- Funzione che impedisce l'inserimento dei voti di un esame non ancora passato
CREATE or REPLACE FUNCTION check_inserimento_voti() RETURNS TRIGGER AS $$
BEGIN

	if (NEW.data::date > NOW()::date)
		then raise exception 'Non si può modificare il voto di un esame non ancora svolto';
	end if;

	RETURN NEW;
END;
$$ language 'plpgsql';

-- Funzione che impedisce la disiscrizione da un esame già svolto
CREATE or REPLACE FUNCTION check_disiscrizione_esame() RETURNS TRIGGER AS $$
BEGIN

	if (OLD.data::date <= NOW()::date)
		then raise exception 'Non ti puoi disiscrivere da questo esame';
	end if;
	
	RETURN OLD;
END;
$$ language 'plpgsql';

-- Funzione che impedisce modifiche o delete sui dati storici
CREATE or REPLACE FUNCTION check_storici() RETURNS TRIGGER AS $$
BEGIN
	raise exception 'Impossibile modificare o eliminare questi dati';
END;
$$ language 'plpgsql';
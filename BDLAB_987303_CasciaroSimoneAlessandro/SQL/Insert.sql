-- Per chiamarle con PHP, la query è: SELECT {nome funzione}();



-- Funzione per Inserire un Docente
CREATE or REPLACE FUNCTION add_docente(username varchar(200), password varchar(16), nome varchar(100), cognome varchar(50), data_di_nascita date, sesso varchar(10), indirizzo varchar(50), primo_insegnamento integer) RETURNS BOOLEAN AS $$
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
$$ language 'plpgsql';
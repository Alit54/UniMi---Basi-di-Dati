-- Funzione per Eliminare uno Studente
CREATE or REPLACE FUNCTION delete_studente(codice integer, inat date, mot varchar) RETURNS BOOLEAN AS $$
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
$$ language 'plpgsql';

-- Funzione che mantiene salvate le informazioni degli studenti eliminati dalla tabella
CREATE or REPLACE FUNCTION spostamento_dati_studente() RETURNS TRIGGER AS $$
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
$$ language 'plpgsql';
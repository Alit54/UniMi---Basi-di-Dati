-- Trigger relativo all'update del corso
CREATE  TRIGGER check_durata_corso_trigger
	BEFORE UPDATE ON corso FOR EACH ROW
	EXECUTE FUNCTION check_durata_corso();

-- Trigger relativo all'inserimento dell'anno previsto dall'insegnamento
CREATE TRIGGER check_durata_insegnamento_trigger
	BEFORE INSERT OR UPDATE ON insegnamento FOR EACH ROW
	EXECUTE FUNCTION check_durata_insegnamento();

-- Trigger relativo all'inserimento dello studente
CREATE TRIGGER check_inserimento_studente_trigger
	BEFORE INSERT OR UPDATE ON studente FOR EACH ROW
	EXECUTE FUNCTION check_inserimento_studente();

-- Trigger relativo all'iscrizione agli esami da parte degli studenti
CREATE TRIGGER check_iscrizione_esami_trigger
	BEFORE INSERT ON esame FOR EACH ROW
	EXECUTE FUNCTION check_iscrizione_esami();

-- Trigger relativo al numero di insegnamenti di un docente
CREATE TRIGGER check_numero_insegnamenti_trigger
	BEFORE INSERT OR UPDATE ON insegnamento FOR EACH ROW
	EXECUTE FUNCTION check_numero_insegnamenti();

-- Trigger relativo all'inserimento delle propedeuticità
CREATE TRIGGER check_propedeuticita_corretta_trigger
	BEFORE INSERT OR UPDATE ON propedeuticita FOR EACH ROW
	EXECUTE FUNCTION check_propedeuticita_corretta();

-- Trigger relativo all'inserimento di un appello
CREATE TRIGGER check_sovrapposizione_appello_trigger
	BEFORE INSERT OR UPDATE ON appello FOR EACH ROW
	EXECUTE FUNCTION check_sovrapposizione_appello();

-- Trigger relativo all'eliminazione dello studente
CREATE TRIGGER spostamento_dati_studente_trigger
	BEFORE DELETE ON studente FOR EACH ROW
	EXECUTE FUNCTION spostamento_dati_studente();

-- Trigger relativo all'eliminazione di un docente
CREATE TRIGGER check_delete_docente_trigger
	BEFORE DELETE ON docente FOR EACH ROW
	EXECUTE FUNCTION check_delete_docente();

-- Trigger relativo all'eliminazione di un insegnamento
CREATE TRIGGER check_delete_insegnamento_trigger
	BEFORE DELETE ON insegnamento FOR EACH ROW
	EXECUTE FUNCTION check_delete_insegnamento();

-- Trigger relativo all'inserimento di un appello
CREATE TRIGGER check_inserimento_appello_trigger
	BEFORE INSERT OR UPDATE ON appello FOR EACH ROW
	EXECUTE FUNCTION check_inserimento_appello();

-- Trigger relativo all'inserimento dei voti di un esame
CREATE TRIGGER check_inserimento_voti_trigger
	BEFORE UPDATE ON esame FOR EACH ROW
	EXECUTE FUNCTION check_inserimento_voti();

-- Trigger relativo alla disiscrizione di un esame
CREATE TRIGGER check_disiscrizione_esame_trigger
	BEFORE DELETE ON esame FOR EACH ROW
	EXECUTE FUNCTION check_disiscrizione_esame();

-- Trigger che impedisce l'update o la delete di Studenti_Storici o Esami_Storici
CREATE TRIGGER check_storici_trigger
	BEFORE UPDATE or DELETE ON studente_storico FOR EACH ROW
	EXECUTE FUNCTION check_storici();
CREATE TRIGGER check_storici_trigger
	BEFORE UPDATE or DELETE ON esame_storico FOR EACH ROW
	EXECUTE FUNCTION check_storici();
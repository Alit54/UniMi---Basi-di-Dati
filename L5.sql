-- SCHEMA
-- COUNTRY (iso3, name)
-- MOVIE (id, official_title, budget, year, lenght, plot)
-- PRODUCED (movie, country)
-- SIM (movie1, movie2, cause)
-- SHOW (movie, name, city, date, time)

-- Crea Database
create database test;

-- Crea Schema
create schema imdb;

-- Set variabile di sistema (Tutti i comandi che passo li passo a imbd. Il default è public)
set search_path to imbd;

-- Creazione Tabelle
CREATE TABLE country (
    iso3 char(3) PRIMARY KEY, -- char(3) significa che devono necessariamente essere 3 caratteri
    name varchar(20) UNIQUE NOT NULL -- varchar(20) significa che devono essere al massimo 20, possono essere meno
);

-- Inserimento Valori (multipla)
INSERT INTO country VALUES 
    ('USA', 'United States'),
    ('ITA', 'Italy'),
    ('FRA', 'France'),
    ('GBR', 'United Kingdom');

-- Visualizzazione dei valori
SELECT *
FROM country;

-- Esempi di insert interessanti
INSERT INTO country VALUES ('DEUS', 'Germany - Deutschland'); -- ERRATA ('DEUS' ha 4 caratteri)
INSERT INTO country VALUES ('PT', 'Portugal'); -- CORRETTA ('PT' ha 2 caratteri ma viene messo un blank alla fine)
INSERT INTO country VALUES ('PT ', 'Portugal'); -- ERRATA (sarebbe giusta ma avendo messo la query prima sarebbe un duplicato)
INSERT INTO country VALUES ('PTG', 'Portugal '); -- CORRETTA ('Portugal' è diverso da 'Portugal ' e quindi rispetta la condizione UNIQUE)

-- IMPORTANTE: Se io voglio che DAVVERO un campo abbia necessariamente 3 caratteri (non basta quindi scrivere char(3)), gestisco a livello di form

-- Queste Query hanno lo stesso risultato? Risposta: NO
SELECT *
FROM country
WHERE name = 'Portugal';

SELECT *
FROM country
WHERE name = 'Portugal ';

-- Creazione tabella MOVIE
CREATE TABLE movie(
    id varchar(10) PRIMARY KEY,
    official_title varchar(200) NOT NULL,
    budget numeric(12,2),
    year char(4),
    lenght integer,
    plot text
);

-- INSERT di colonne specifiche
INSERT INTO movie(id, official_title, year, lenght) VALUES
    ('0338751', 'The Aviator', '2004', 170),
    ('0088763', 'Back to the Future', '1985', 116),
    ('0084516', 'Poltergeist', '1982', 114),
    ('0083866', 'E.T. the Extra-Terrestrial', '1982', 115),
    ('0097576', 'Indiana Jones and the Last Crusade', '1989', 127);

-- Modifica di una riga già inserita
UPDATE movie
SET plot = 'Text to insert'
WHERE id = '0097576';

-- Eliminare una riga
DELETE FROM movie
WHERE id = '0338751';

-- Creazione tabella con chiave esterna
-- ON UPDATE: Cosa succede se modifico la riga nella tabella principale?
-- ON DELETE: Cosa succede se elimino la riga nella tabella principale?
-- Tag CASCADE: modifica/elimina a cascata (la riga di produced viene modificata/eliminata di conseguenza)
-- Tag NO ACTION: Non sarà possibile modificare/eliminare un country dalla tabella principale se implica qualcosa sulla tabella secondaria
CREATE TABLE produced (
    movie varchar(10) NOT NULL REFERENCES movie(id) ON UPDATE CASCADE ON DELETE CASCADE,
    country char(3) NOT NULL REFERENCES country(iso3) ON UPDATE CASCADE ON DELETE NO ACTION,
    PRIMARY KEY (movie, country)
);

-- Inserimento di valori nella tabella produced: si possono inserire soltanto movie che esistono nella tabella movie e country che esistono nella tabella country
INSERT INTO produced VALUES ('0097576', 'USA'); -- CORRETTA
INSERT INTO produced VALUES ('0038284', 'BAU'); -- ERRATA

-- Se ora volessimo modificare un movie_id nella tabella produced, non potremmo farlo dalla tabella produced in quanto violeremmo un vincolo
UPDATE produced
SET movie = 'ABABAB'
WHERE id = '0097576'; -- ERRATA

UPDATE movie
SET id = 'ABABAB'
WHERE id = '0097576'; -- CORRETTA (e modificherà anche la riga nella tabella movie grazie al "ON UPDATE CASCADE" definito alla creazione della tabella produced)

-- Modificare una tabella
ALTER TABLE produced DROP CONSTRAINT produced_movie_fkey; -- Rimuove un vincolo
ALTER TABLE produced ADD CONSTRAINT produced_movie_fkey FOREIGN KEY (movie) REFERENCES movie(id) ON UPDATE CASCADE; -- Aggiunge un vincolo

-- Altra tabella con due chiavi esterne sulla stessa tabella
CREATE TABLE sim (
    movie1 varchar(10) NOT NULL REFERENCES movie(id) ON UPDATE CASCADE ON DELETE CASCADE,
    movie2 varchar(10) NOT NULL REFERENCES movie(id) ON UPDATE CASCADE ON DELETE CASCADE,
    cause varchar(15) NOT NULL,
    PRIMARY KEY (movie1, movie2, cause)
)

-- Definire un dominio sul campo di una tabella
CREATE DOMAIN sim_causes AS varchar(15) CHECK (value IN ('genre', 'plot', 'setting', 'hist period')); -- Crea un nuovo tipo (di nome sim_causes)
ALTER TABLE sim ALTER COLUMN cause TYPE sim_causes; -- Assegno il nuovo tipo alla colonna modificando la tabella

-- Creare un vincolo ad una variabile
ALTER TABLE sim ADD COLUMN score numeric(3,2) NOT NULL;
ALTER TABLE sim ADD CONSTRAINT score_check CHECK (score > 0 AND score <= 1); -- Score dovrà rispettare il vincolo pur rimanendo di tipo numeric

-- Inserimento nella tabella sim
INSERT INTO sim VALUES ('0084516', '0083866', 'plot') -- CORRETTA (id1 valido, id2 valido e motivazione valida)
INSERT INTO sim VALUES ('0084516', '0083866', 'pollo') -- ERRATA (id1 valido, id2 valido ma motivazione invalida)

-- Tabella Cinema (Chiave esterna composta)
CREATE TABLE cinema (
    name varchar(50) NOT NULL,
    city varchar(50) NOT NULL,
    address varchar(100),
    phone varchar(50),
    PRIMARY KEY (name, city)
);

INSERT INTO cinema(name, city) VALUES 
    ('Anteo Palazzo del Cinema', 'Milan'),
    ('Colosseo', 'Rome'),
    ('UCI Cinema MilanoFiori', 'Assago');

-- ESERCIZIO: tabella che lega il movie al cinema che lo proietta ad un determinato orario
CREATE TABLE show (
    movie varchar(10) REFERENCES movie(id) ON UPDATE CASCADE ON DELETE NO ACTION,
    name varchar(50),
    city varchar(50),
    date date,
    time time,
    -- Oppure
    showtime timestamp,

    FOREIGN KEY (name, city) REFERENCES cinema(name, city) ON UPDATE CASCADE ON DELETE NO ACTION,
    PRIMARY KEY (movie, name, city, date, time);
)

-- Esempio di inserimento per un type timestamp: '2015-01-01 14:48:34:69' ovvero 'yyyy-mm-dd hh:mm:ss:xx'
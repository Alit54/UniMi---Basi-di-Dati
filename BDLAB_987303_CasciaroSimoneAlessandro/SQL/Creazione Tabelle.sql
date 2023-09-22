CREATE TABLE corso (
  nome varchar(100) PRIMARY KEY,
  durata integer NOT NULL CHECK (durata = 2 OR durata = 3),
  descrizione text
);

CREATE TABLE studente (
  matricola integer PRIMARY KEY,
  username varchar(200) UNIQUE NOT NULL,
  password varchar(100) NOT NULL,
  nome varchar(100) NOT NULL,
  cognome varchar(50) NOT NULL,
  nascita date NOT NULL CHECK(nascita <= NOW()::date),
  sesso varchar(10) CHECK(sesso IN ('Femmina', 'Maschio')),
  indirizzo varchar(50),
  iscrizione date DEFAULT NOW()::date NOT NULL CHECK(iscrizione <= NOW()::date),
  corso varchar(100) NOT NULL,
  FOREIGN KEY (corso) REFERENCES corso(nome) ON UPDATE CASCADE
);

CREATE TABLE segreteria (
  username varchar(200) PRIMARY KEY,
  password varchar(100) NOT NULL,
  nome varchar(100) NOT NULL,
  cognome varchar(50) NOT NULL,
  nascita date NOT NULL CHECK(nascita <= NOW()::date),
  sesso varchar(10) CHECK(sesso IN ('Femmina', 'Maschio')),
  indirizzo varchar(50)
);

CREATE TABLE docente (
  username varchar(200) PRIMARY KEY,
  password varchar(100) NOT NULL,
  nome varchar(100) NOT NULL,
  cognome varchar(50) NOT NULL,
  nascita date NOT NULL CHECK(nascita <= NOW()::date),
  sesso varchar(10) CHECK(sesso IN ('Femmina', 'Maschio')),
  indirizzo varchar(50)
);

CREATE TABLE studente_storico (
  matricola integer PRIMARY KEY,
  username varchar(200) UNIQUE NOT NULL,
  password varchar(100) NOT NULL,
  nome varchar(100) NOT NULL,
  cognome varchar(50) NOT NULL,
  nascita date NOT NULL CHECK(nascita <= NOW()::date),
  sesso varchar(10) CHECK(sesso IN ('Femmina', 'Maschio')),
  indirizzo varchar(50),
  iscrizione date NOT NULL CHECK(iscrizione <= NOW()::date),
  corso varchar(100) NOT NULL,
  inattivita date DEFAULT NOW()::date CHECK(inattivita <= NOW()::date),
  motivazione varchar(15),
  FOREIGN KEY (corso) REFERENCES corso(nome) ON UPDATE CASCADE
);

CREATE TABLE insegnamento (
  id serial PRIMARY KEY,
  nome varchar(100) NOT NULL,
  anno integer NOT NULL,
  CFU integer NOT NULL,
  descrizione text,
  responsabile varchar(200),
  corso varchar(100) NOT NULL,
  FOREIGN KEY (responsabile) REFERENCES docente(username) ON UPDATE CASCADE,
  FOREIGN KEY (corso) REFERENCES corso(nome) ON UPDATE CASCADE
);

CREATE TABLE appello (
  data timestamp,
  insegnamento integer,
  luogo varchar(30) NOT NULL,
  PRIMARY KEY (data, insegnamento),
  FOREIGN KEY (insegnamento) REFERENCES insegnamento(id) ON UPDATE CASCADE ON DELETE CASCADE,
  CHECK(data::date > NOW()::date)
);

CREATE TABLE esame (
  data timestamp,
  insegnamento integer,
  studente integer,
  voto integer CHECK(voto >= 0 AND voto <= 30),
  lode boolean,
  PRIMARY KEY (data, insegnamento, studente),
  FOREIGN KEY (studente) REFERENCES studente(matricola) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (data, insegnamento) REFERENCES appello(data, insegnamento) ON UPDATE CASCADE ON DELETE CASCADE,
  CHECK(NOT lode OR voto = 30)
);

CREATE TABLE esame_storico (
  data timestamp,
  insegnamento integer,
  studente integer,
  voto integer CHECK(voto >= 0 AND voto <= 30),
  lode boolean,
  PRIMARY KEY (data, insegnamento, studente),
  FOREIGN KEY (studente) REFERENCES studente_storico(matricola) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (data, insegnamento) REFERENCES appello(data, insegnamento) ON UPDATE CASCADE ON DELETE CASCADE,
  CHECK(NOT lode OR voto = 30)
);

CREATE TABLE propedeuticita (
  insegnamento integer,
  requisito integer,
  PRIMARY KEY (insegnamento, requisito),
  FOREIGN KEY (insegnamento) REFERENCES insegnamento(id) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (requisito) REFERENCES insegnamento(id) ON UPDATE CASCADE ON DELETE CASCADE,
  CHECK(insegnamento<>requisito)
);
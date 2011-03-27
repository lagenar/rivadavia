DROP DATABASE IF EXISTS Rivadavia;
CREATE DATABASE Rivadavia;
USE Rivadavia;

CREATE TABLE Autor (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(256) NOT NULL UNIQUE
) TYPE=myisam;

CREATE TABLE Libro (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(256) NOT NULL
) TYPE=myisam;

CREATE TABLE Tomo (
       codigo VARCHAR(10) PRIMARY KEY,
       editorial VARCHAR(50),
       anio_edicion INT,
       id_libro INT NOT NULL REFERENCES
       Libro (id)
) TYPE=myisam;

CREATE TABLE Libro_Autor (
       id INT AUTO_INCREMENT PRIMARY KEY,
       id_libro INT NOT NULL REFERENCES
       Libro (id),
       id_autor INT NOT NULL REFERENCES
       Autor (id)
) TYPE=myisam;

ALTER TABLE Libro ADD FULLTEXT(nombre);
ALTER TABLE Autor ADD FULLTEXT(nombre);

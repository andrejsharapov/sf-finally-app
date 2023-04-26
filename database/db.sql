/**
  * use DBeaver 23.0.3
  * MySQL
 */

-- create database
CREATE DATABASE php_finally_app;
USE php_finally_app;

SHOW TABLES;

-- create table roles
CREATE TABLE roles
(
    id   serial PRIMARY KEY,
    name VARCHAR(246) NOT null
);

CREATE INDEX id_name_index ON roles (id, name);

INSERT INTO roles (name) VALUES ('admin');
INSERT INTO roles (name) VALUES ('user');
INSERT INTO roles (name) VALUES ('master');

SELECT * FROM roles;

DELETE FROM roles;
ALTER TABLE roles AUTO_INCREMENT = 1;
DROP TABLE roles;

-- create users table (with error in password)
CREATE TABLE users
(
    id       serial PRIMARY KEY,
    name     VARCHAR(256),
    email    VARCHAR(50) NOT NULL,
    password VARCHAR(30) NOT NULL,
    date     DATE        NOT NULL
);

-- fix password error (use hash)
ALTER TABLE users
    MODIFY COLUMN password
        VARCHAR (256) NOT NULL
;

-- add new column for users roles
ALTER TABLE users
    ADD COLUMN role VARCHAR(30)
--     DROP COLUMN role
;

-- set default role
ALTER TABLE users
    ALTER role
        SET DEFAULT 'user'
;

ALTER TABLE users
    ADD COLUMN role_id VARCHAR(99)
;

INSERT INTO users (name, email, password, date, role, role_id)
VALUES (
           'admin',
           'admin@ya.ru',
           '3c9909afec25354d551dae21590bb26e38d53f2173b8d3dc3eee4c047e7ab1c1eb8b85103e3be7ba613b31bb5c9c36214dc9f14a42fd7a2fdb84856bca5c44c2',
           '2023-04-23',
           'admin',
           '1'
       );

CREATE INDEX role_email_index ON users (role, email);
CREATE INDEX role_role_id_index ON users (role, role_id);

SELECT * FROM users WHERE role_id >= 2;

DELETE FROM users WHERE id > 1;
ALTER TABLE users AUTO_INCREMENT = 2;
DROP TABLE users;

-- create offers table
CREATE TABLE offers
(
    id       serial PRIMARY KEY,
    title     VARCHAR(256) NOT NULL,
    count    VARCHAR(50) default 0,
    url VARCHAR(256) NOT NULL,
    theme VARCHAR(256) NOT NULL,
    user_id VARCHAR(256) NOT NULL,
    created     DATE        NOT NULL
);

-- add column state
ALTER TABLE offers
    ADD COLUMN state VARCHAR(1)
;

-- set default state
ALTER TABLE offers
    ALTER state
        SET DEFAULT '1'
;

CREATE INDEX title_user_id_index ON offers (title, user_id);
CREATE INDEX title_user_id_state_index ON offers (title, user_id, state);

-- add column transitions
ALTER TABLE offers
    ADD COLUMN transitions INT DEFAULT '0'
;

CREATE INDEX id_transitions_index ON offers (id, transitions);

INSERT INTO offers (title, count, url, theme, user_id, created) VALUES ('GitHub Repo', '100', 'https://github.com/andrejsharapov/sf-finally-app','github','1','2023-04-26'); 

-- ALTER TABLE offers
--     DROP COLUMN transitions;

-- UPDATE offers SET state = '0' WHERE id = 1;

SELECT * FROM offers;

DELETE FROM offers;
ALTER TABLE offers AUTO_INCREMENT = 1;
DROP TABLE offers;

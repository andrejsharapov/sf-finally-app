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

-- DELETE FROM roles;
-- ALTER TABLE roles AUTO_INCREMENT = 1;
-- DROP TABLE  roles;

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

-- DELETE FROM users WHERE id > 1;
-- ALTER TABLE users AUTO_INCREMENT = 2;
-- DROP TABLE  users;

-- create offers table
CREATE TABLE offers
(
    id 		    serial 		 PRIMARY KEY,
    title 	    VARCHAR(256) NOT NULL,
    payment     VARCHAR(50)  default 0,
    url 	    VARCHAR(256) NOT NULL,
    theme 	    VARCHAR(256) NOT NULL,
    creator_id  VARCHAR(256) NOT NULL,
    created     DATE         NOT NULL
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

CREATE INDEX title_user_id_index ON offers (title, creator_id);
CREATE INDEX title_user_id_state_index ON offers (title, creator_id, state);

-- add column transitions
ALTER TABLE offers
    ADD COLUMN transitions INT DEFAULT '0'
;

CREATE INDEX id_transitions_index ON offers (id, transitions);

INSERT INTO offers (title, payment, url, theme, creator_id, created)
VALUES (
           'GitHub Repo',
           '100',
           'https://github.com/andrejsharapov/sf-finally-app',
           'github',
           '1',
           '2023-04-26'
       );

-- ALTER TABLE offers
--     DROP COLUMN transitions;

-- add column transitions
ALTER TABLE offers
    ADD COLUMN total_cost INT DEFAULT '0'
;

-- UPDATE offers SET state = '0' WHERE id = 1;
-- ALTER TABLE offers RENAME COLUMN user_id TO creator_id;

SELECT * FROM offers;

-- DELETE FROM offers;
-- ALTER TABLE offers AUTO_INCREMENT = 1;
-- DROP TABLE  offers;

-- create follows table
CREATE TABLE follows
(
    id			serial PRIMARY KEY,
    offer_id	INT  NOT NULL,
    author_id	INT  NOT NULL,
    follower_id	INT  NOT NULL,
    date		DATE NOT NULL
);

CREATE INDEX offer_id_follower_id_index ON follows (offer_id, follower_id);
CREATE INDEX offer_id_author_id_index ON follows (offer_id, author_id);

SELECT * FROM follows;

-- DELETE FROM follows;
-- ALTER TABLE follows AUTO_INCREMENT = 1;
-- DROP TABLE  follows;

-- SELECT count(*) FROM offers AS o JOIN follows AS f ON o.id = f.offer_id where o.id = 2;
-- SELECT follower_id FROM offers AS o JOIN follows AS f ON o.id = f.offer_id where o.id = 2;

-- create moves table
CREATE TABLE moves (
    id				serial PRIMARY KEY,
    date			DATE NOT NULL,
    offer_id		INT  NOT NULL,
    master_id		INT  NOT NULL,
    payment_offer	INT  NOT NULL,
    amount			BIGINT DEFAULT 0
);

CREATE INDEX master_offer_index ON moves (master_id, offer_id);
CREATE INDEX master_offer_payment_index ON moves (master_id, offer_id, payment_offer);
CREATE INDEX offer_payment_amount_index ON moves (offer_id, payment_offer, amount);

SELECT * FROM moves;

-- SELECT SUM(payment_offer) FROM moves WHERE offer_id = 3 AND master_id = 3;

-- DELETE FROM moves;
-- ALTER TABLE moves AUTO_INCREMENT = 1;
-- DROP TABLE  moves;

SHOW TABLES;

-- select to current day (если добавить - 1, то покажет за вчерашний день)
SELECT id, date, offer_id FROM moves WHERE offer_id = 2 AND date = CURDATE(); -- - 1;

-- select to current week
SELECT id, date, offer_id FROM moves WHERE offer_id = 2 AND YEAR(date) = YEAR(NOW()) AND WEEK(date) = WEEK(NOW());

-- select to current month
SELECT id, date, offer_id FROM moves WHERE offer_id = 2 AND YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW());

-- select to current year
SELECT id, date, offer_id FROM moves WHERE offer_id = 2 AND YEAR(NOW());

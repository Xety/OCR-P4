-- -------------------------------------------------------
-- Table users
-- -------------------------------------------------------
CREATE SEQUENCE IF NOT EXISTS users_id_seq;

DROP TABLE IF EXISTS "users";
-- Table Definition
CREATE TABLE "users" (
    "id" int4 NOT NULL DEFAULT nextval('users_id_seq'::regclass),
    "name" varchar(100) NOT NULL,
    "email" varchar(255) NOT NULL,
    "password" varchar(255) NOT NULL,
    "created_at" timestamp DEFAULT now(),
    PRIMARY KEY ("id")
);
-- Indices
CREATE UNIQUE INDEX users_email_key ON users USING btree (email);

-- -------------------------------------------------------
-- Table books
-- -------------------------------------------------------
CREATE SEQUENCE IF NOT EXISTS books_id_seq;

DROP TABLE IF EXISTS "books";

CREATE TABLE "books" (
    "id" int4 NOT NULL DEFAULT nextval('books_id_seq'::regclass),
    "user_id" int4 NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    "title" varchar(255)  NOT NULL,
    "author" varchar(255)  NOT NULL,
    "description" text NOT NULL DEFAULT '',
    "photo" varchar(255),
    "is_available" boolean NOT NULL DEFAULT true,
    "created_at" timestamp DEFAULT now(),
    PRIMARY KEY ("id")
);

CREATE INDEX books_user_id_idx ON books USING btree (user_id);
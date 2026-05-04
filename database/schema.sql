-- Sequence and defined type
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
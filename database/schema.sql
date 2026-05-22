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
    "avatar" varchar(255) DEFAULT NULL,
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

-- -------------------------------------------------------
-- Table conversations
-- -------------------------------------------------------
CREATE SEQUENCE IF NOT EXISTS conversations_id_seq;

DROP TABLE IF EXISTS "conversations" CASCADE;

CREATE TABLE "conversations" (
    "id" int4 NOT NULL DEFAULT nextval('conversations_id_seq'::regclass),
    "creator_id" int4 REFERENCES users (id) ON DELETE SET NULL,
    "receiver_id" int4 REFERENCES users (id) ON DELETE SET NULL,
    "created_at" timestamp DEFAULT now(),
    PRIMARY KEY ("id"),
    CONSTRAINT unique_conversation UNIQUE (creator_id, receiver_id)
);

CREATE INDEX conversations_creator_id_idx ON conversations USING btree (creator_id);
CREATE INDEX conversations_receiver_id_idx ON conversations USING btree (receiver_id);

-- -------------------------------------------------------
-- Table conversation_messages
-- -------------------------------------------------------
CREATE SEQUENCE IF NOT EXISTS conversation_messages_id_seq;

DROP TABLE IF EXISTS "conversation_messages";

CREATE TABLE "conversation_messages" (
    "id" int4 NOT NULL DEFAULT nextval('conversation_messages_id_seq'::regclass),
    "conversation_id" int4 NOT NULL REFERENCES conversations (id) ON DELETE CASCADE,
    "sender_id" int4 REFERENCES users (id) ON DELETE SET NULL,
    "content" text NOT NULL,
    "created_at" timestamp DEFAULT now(),
    PRIMARY KEY ("id")
);

CREATE INDEX conversation_messages_conversation_id_idx ON conversation_messages USING btree (conversation_id);
-- Schema de la base de données OCR-P4
-- À exécuter manuellement : psql -U postgres -d ocr_p4 -f database/schema.sql
-- Pour les données de démonstration : php database/seed.php

CREATE TABLE IF NOT EXISTS users (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(255)  NOT NULL UNIQUE,
    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
);

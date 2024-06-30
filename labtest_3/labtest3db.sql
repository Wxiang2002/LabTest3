CREATE DATABASE IF NOT EXISTS labtest3;

USE labtest3;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);

INSERT INTO users (name, email) VALUES ('John Doe', 'john.doe@example.com');
INSERT INTO users (name, email) VALUES ('Jane Smith', 'jane.smith@example.com');
INSERT INTO users (name, email) VALUES ('Alice Johnson', 'alice.johnson@example.com');
INSERT INTO users (name, email) VALUES ('Bob Brown', 'bob.brown@example.com');

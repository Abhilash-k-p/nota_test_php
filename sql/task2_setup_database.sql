-- Create the database
CREATE
DATABASE IF NOT EXISTS nota_test_task2;

-- Use the created database
USE
nota_test_task2;

-- Create the wiki_sections table
CREATE TABLE IF NOT EXISTS wiki_sections
(
    id
    INT
    AUTO_INCREMENT
    PRIMARY
    KEY,
    date_created
    DATETIME
    NOT
    NULL DEFAULT CURRENT_TIMESTAMP,
    title
    VARCHAR
(
    230
) NOT NULL,
    url VARCHAR
(
    240
) UNIQUE NOT NULL,
    picture VARCHAR
(
    240
),
    abstract VARCHAR
(
    256
)
    );
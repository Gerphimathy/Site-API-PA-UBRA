USE ubra;

CREATE TABLE pvyihCO0TugPHJv_users
(
    id       INT                     NOT NULL    PRIMARY KEY     AUTO_INCREMENT,
    login    VARCHAR(64)    UNIQUE   NOT NULL,
    password CHAR(128)               NOT NULL,
    username VARCHAR(255)            NOT NULL,
    id_code  CHAR(10)       UNIQUE   NOT NULL
);

CREATE TABLE pvyihCO0TugPHJv_tokens
(
    token   CHAR(30),
    client  VARCHAR(255) NOT NULL,
    id_user INT          NOT NULL,
    expires DATETIME,

    FOREIGN KEY (id_user) REFERENCES pvyihCO0TugPHJv_users (id),
    PRIMARY KEY (client, id_user)
);
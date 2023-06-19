USE ubra;

CREATE TABLE pvyihCO0TugPHJv_users
(
    id       INT                     NOT NULL    PRIMARY KEY     AUTO_INCREMENT,
    login    VARCHAR(64)    UNIQUE   NOT NULL,
    password CHAR(128)               NOT NULL,
    username VARCHAR(255)            NOT NULL,
    id_code  CHAR(10)       UNIQUE   NOT NULL,
    points   INT                     NOT NULL    DEFAULT 0,
    is_admin BOOLEAN                 NOT NULL    DEFAULT FALSE
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

CREATE TABLE  pvyihCO0TugPHJv_boats
(
    id    INT          NOT NULL    PRIMARY KEY     AUTO_INCREMENT,
    name  VARCHAR(255) NOT NULL,
);

CREATE TABLE  pvyihCO0TugPHJv_skins
(
    id      INT             NOT NULL    PRIMARY KEY     AUTO_INCREMENT,
    price   INT             NOT NULL    DEFAULT 0,
    name    VARCHAR(255)    NOT NULL,
    id_boat INT             NOT NULL,

    FOREIGN KEY (id_boat) REFERENCES pvyihCO0TugPHJv_boats (id)
);

CREATE TABLE pvyihCO0TugPHJv_skin_ownership
(
    id_user INT NOT NULL,
    id_skin INT NOT NULL,

    FOREIGN KEY (id_user) REFERENCES pvyihCO0TugPHJv_users (id),
    FOREIGN KEY (id_skin) REFERENCES pvyihCO0TugPHJv_skins (id),
    PRIMARY KEY (id_user, id_skin)
);
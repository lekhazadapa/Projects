CREATE TABLE trains (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE platforms (
    name VARCHAR(256) NOT NULL PRIMARY KEY,
    length DOUBLE NOT NULL
);

CREATE TABLE carriages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    position INT UNSIGNED NOT NULL,
    train_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (train_id) REFERENCES trains(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE platform_sensors (
    uuid VARCHAR(36) NOT NULL PRIMARY KEY,
    platform_name VARCHAR(256) NOT NULL,
    position DOUBLE NOT NULL,
    height DOUBLE NOT NULL,
    FOREIGN KEY (platform_name) REFERENCES platforms(name) ON DELETE RESTRICT ON UPDATE CASCADE
);

INSERT INTO trains () VALUES (), (), ();

INSERT INTO carriages (train_id, position) VALUES (1, 1), (1, 2), (2, 1), (2, 2), (3, 1), (3, 2);

INSERT INTO platforms (name, length) VALUES ('Kista', 6), ('T-Central', 6);

INSERT INTO platform_sensors (uuid, platform_name, position, height) VALUES ('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', 'Kista', 0.25, 0), ('BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB', 'Kista', 0.5, 0), ('CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC', 'Kista', 0.75, 0), ('DDDDDDDD-DDDD-DDDD-DDDD-DDDDDDDDDDDD', 'T-Central', 0.5, 0);
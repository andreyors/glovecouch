CREATE DATABASE glovecoach;

CREATE TABLE gc_events (id INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY, x DECIMAL(10, 6) SIGNED, y DECIMAL(10, 6) SIGNED, z DECIMAL(10,6) SIGNED, ts DATETIME DEFAULT 0, tsdiff INTEGER UNSIGNED);

CREATE INDEX gc_xyz ON gc_events (x,y,z) USING BTREE;
CREATE INDEX gc_ts ON gc_events(ts) USING BTREE;
CREATE INDEX gc_tsdiff ON gc_events(tsdiff) USING BTREE;

CREATE TABLE gc_switches (name VARCHAR(255), val VARCHAR(255), val_int INTEGER);

CREATE INDEX gc_name ON gc_switches(name) USING BTREE;

INSERT INTO gc_switches SET name = 'like', val_int = 0;

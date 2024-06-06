CREATE TABLE vacancy (
     id INTEGER PRIMARY KEY AUTOINCREMENT,
     name TEXT NOT NULL,
     salary REAL NOT NULL,
     employer TEXT NOT NULL,
     opening_hours TEXT,
     place of work TEXT,
     remote_work_ability BOOLEAN NOT NULL CHECK (remote_work_ability IN (0, 1)),
     description TEXT
);

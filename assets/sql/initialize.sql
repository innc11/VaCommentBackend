CREATE TABLE IF NOT EXISTS 'comments' (
  'id'         INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  'parent'     TEXT,
  'key'        TEXT,
  'label'    TEXT,
  'nick'       TEXT,
  'mail'       TEXT,
  'website'    TEXT,
  'content'    TEXT,
  'approved'   BOOL,
  'time'       DATETIME,
  'ip'         TEXT,
  'useragent'  TEXT
);

CREATE TABLE IF NOT EXISTS 'views' (
  'id'         INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  'key'        TEXT,
  'label'    TEXT,
  'time'       DATETIME,
  'ip'         TEXT,
  'useragent'  TEXT
);
CREATE TABLE users (
  id INTEGER AUTO_INCREMENT,
  smwc_id INTEGER NOT NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  token VARCHAR(32) NOT NULL UNIQUE,
  PRIMARY KEY (id)
);
CREATE TABLE levels (
  id INTEGER AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  author INTEGER NOT NULL,
  difficulty INTEGER NOT NULL,
  verified BIT(1) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (author) REFERENCES users (id)
);
CREATE TABLE ratings (
  levelId INTEGER NOT NULL,
  rating INTEGER NOT NULL,
  userId INTEGER NOT NULL,
  FOREIGN KEY (levelId) REFERENCES levels (id),
  FOREIGN KEY (userId) REFERENCES users (id),
  # only 1 vote per user per level
  # this better work...
  CONSTRAINT UNIQUE (levelId, userId)
);
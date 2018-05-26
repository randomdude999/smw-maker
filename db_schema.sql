
CREATE TABLE users (
  id INTEGER AUTO_INCREMENT,
  smwc_id INTEGER NOT NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);
CREATE TABLE levels (
  id INTEGER AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  author INTEGER NOT NULL,
  bg_index INTEGER,
  sub_bg_index INTEGER,
  difficulty INTEGER NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (author) REFERENCES users (id)
);
CREATE TABLE ratings (
  levelId INTEGER NOT NULL,
  rating INTEGER NOT NULL,
  userId INTEGER NOT NULL,
  FOREIGN KEY (levelId) REFERENCES users (id),
  FOREIGN KEY (userId) REFERENCES users (id)
);
CREATE TABLE login_tokens (
  user_id INTEGER NOT NULL,
  token VARCHAR(32) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id),
  UNIQUE(token)
);


 
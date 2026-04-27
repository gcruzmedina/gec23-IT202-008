CREATE TABLE IT202_G26_Anime (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  anime_id varchar(50) NOT NULL,
  title varchar(255) NOT NULL,
  type varchar(50),
  status varchar(50),
  episodes int DEFAULT NULL,
  score decimal(3,2) DEFAULT NULL,
  image_url varchar(500),
  synopsis text,
  api_source varchar(50),
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_api tinyint(1) DEFAULT 1
);
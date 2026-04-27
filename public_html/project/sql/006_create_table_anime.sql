CREATE TABLE `Anime` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `episodes` int DEFAULT NULL,
  `score` decimal(3,2) DEFAULT NULL,
  `status` varchar(50),
  `image_url` varchar(500),
  `synopsis` text,
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_api` tinyint(1) DEFAULT 1
);
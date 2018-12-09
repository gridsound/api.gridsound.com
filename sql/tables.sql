SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `compositions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iduser` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `data` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `thingsNotVerified` (
  `id` int(11) NOT NULL,
  `iduser` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` set('NORMAL','EMAIL_TO_VERIFY') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NORMAL',
  `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emailpublic` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `pass` char(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lastname` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `username` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `compositions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `thingsNotVerified`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `username` (`username`);


ALTER TABLE `thingsNotVerified`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

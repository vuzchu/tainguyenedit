-- ShareIt resource-sharing platform
-- Schema converted from if0_37413994_sharedit export.
-- NOTE: real user accounts, favorites, feedback and password-reset rows from
-- the original export are intentionally NOT included here (they contain
-- personal emails and, in some legacy rows, plaintext passwords). Only
-- structure is kept for those tables; register fresh accounts after import.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'customer'),
(2, 'staff'),
(3, 'admin');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `google_id` varchar(64) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- A single admin account so you can sign in and manage the site immediately.
-- Username: admin  Password: ChangeMe123!  -- change this after first login.
INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `full_name`, `role_id`) VALUES
(1, 'admin', 'admin@shareit.local', '$2y$12$zVaG3FZLKeZAozstaCHYy.Mc83CCvgFKZ70Sz9HieQPNMRoIcZIqi', 'Site Admin', 3);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Pack Edit'),
(2, 'Project file'),
(3, 'Pack Mask'),
(6, 'After Effect Plugin'),
(12, 'Raw Cut');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `create_date` datetime DEFAULT current_timestamp(),
  `update_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `source` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`project_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_title` (`title`),
  KEY `idx_author` (`author`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `project_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL,
  CONSTRAINT `project_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `project` (`project_id`, `title`, `description`, `author`, `status`, `create_date`, `update_date`, `source`, `category_id`, `image`) VALUES
(24, 'Neptuns Edit Pack 3', '<p><strong>ðŸº Neptuns Edit Pack 3 ðŸº<br />\r\n<br />\r\n- Includes -&nbsp;</strong><br />\r\n<br />\r\n<br />\r\n-&nbsp;<strong>5x</strong>&nbsp;Blurs<br />\r\n-&nbsp;<strong>5x</strong>&nbsp;CCs<br />\r\n-&nbsp;<strong>15x</strong>&nbsp;Overlays<br />\r\n-&nbsp;<strong>2x</strong>&nbsp;Particles<br />\r\n-&nbsp;<strong>2x</strong>&nbsp;Project Files (Proud and ...)<br />\r\n-&nbsp;<strong>5x</strong>&nbsp;Random Presets<br />\r\n-&nbsp;<strong>7x</strong>&nbsp;Sound Effects (Wooshes)<br />\r\n-&nbsp;<strong>5x</strong>&nbsp;Shakes<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Twitches<br />\r\n<br />\r\n<strong>- Feature (@zrevisn) -</strong><br />\r\n<br />\r\n-&nbsp;<strong>16x</strong>&nbsp;CCs<br />\r\n-&nbsp;<strong>1x</strong>&nbsp;Shake<br />\r\n-&nbsp;<strong>1x</strong>&nbsp;Twitch<br />\r\n-&nbsp;<strong>1x</strong>&nbsp;New Project File (Sp&uuml;re Nichts)<br />\r\n<br />\r\n<strong>- Neptuns Edit Pack 1&amp;2 Stuff -</strong><br />\r\n<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Transition for Beginners (Scale...)<br />\r\n-&nbsp;<strong>2x</strong>&nbsp;Different Twixtors (working on a tutorial.)<br />\r\n-&nbsp;<strong>7x</strong>&nbsp;Warps<br />\r\n-&nbsp;<strong>5x</strong>&nbsp;Text Presets<br />\r\n<br />\r\nThe first one who finds the Easter Egg in my Pack will get something special.&nbsp;ðŸ’›</p>\r\n\r\n<p>You will get a RAR&nbsp;<em>(45MB)</em>&nbsp;file</p>\r\n', 'Neptuns', 'active', '2024-09-30 18:44:55', '2025-01-07 17:09:26', 'https://drive.google.com/file/d/1iHQI3V7cVUpXlMVqcCoiWwmFCU5fgFJQ/view?usp=sharing', 1, 'https://payhip.com/cdn-cgi/image/format=auto/https://pe56d.s3.amazonaws.com/o_1englsj978gb1q1pom31d1kd37m.png'),
(54, '『Nano Editing Pack #2』', '3 Project Files', 'Nano', 'active', '2024-09-29 23:00:11', '2024-09-30 18:58:56', 'https://drive.google.com/file/d/1qE2Rm6zBAfB0aDw2mEXiFz9ci6yYgGdf/view', 1, 'https://images-ext-1.discordapp.net/external/30zzPILgTAUh5S8DFQe8ajFi_Bayehr_7TCT2fH7Ikw/https/payhip.com/cdn-cgi/image/format%3Dauto/https%3A/pe56d.s3.amazonaws.com/o_1gehrdne81j686df1ascs1s668r.png?format=webp&quality=lossless&width=1193&height=671'),
(55, 'MRZ Editing Pack v1', '<p>Looking for a HUGE pack with all the content you will ever need to editing? You found it! The Definitive Pack for editors.<br />\r\n<br />\r\nIncludes +2.7k Files:<br />\r\n+Shake Tutorial<br />\r\n+Overlays<br />\r\n+Presets<br />\r\n+Colorings<br />\r\n+Masks<br />\r\n+Sfx<br />\r\n+Fonts<br />\r\n+Backgrounds<br />\r\n+Link for Clips, Compilations &amp; Raws<br />\r\n+4 of my Project Files<br />\r\n<br />\r\nThe best option to make your editing like Professional!</p>\r\n\r\n<p>You will get a RAR&nbsp;<em>(4GB)</em>&nbsp;file</p>\r\n', 'MRZ', 'active', '2024-09-29 23:00:11', '2024-10-19 07:22:05', 'https://drive.google.com/file/d/1XIF4NvfcH7SINqAKrEG5wC7EK5-AIRWo/view?usp=sharing', 1, 'https://images-ext-1.discordapp.net/external/RNjfFqalxGQ4jfv_XWQHk0lizGxEojl2QmEywqfGQRE/https/images.payhip.com/o_1frme33t81lr51cb815r1st993r.jpg?format=webp&width=1193&height=671'),
(56, 'Pack 2 - Legends Never Die + Crossfire + East of Eden + Moonlight', 'A cool giveaway project with free resources.', 'WAYK', 'active', '2024-09-29 23:00:11', '2024-09-29 23:50:06', 'https://drive.google.com/file/d/17BYDfThztB88DP4gAz0nhgCpED5QCOuo/view', 1, 'https://images-ext-1.discordapp.net/external/jwsTblkIE8pCNdqv7nNQcg1dAf0VLUj05Zi13QYPJRU/https/images.payhip.com/o_1g07kqaap1dei1c631gtq12d1sb9m.png?format=webp&quality=lossless&width=1193&height=671'),
(57, 'Viboh PACK 3', 'Fourth giveaway project for photography lovers.', 'Vboh', 'active', '2024-09-29 23:00:11', '2024-09-29 23:45:04', 'https://drive.google.com/drive/folders/1WOoHSr1FzjWqeMhYgBy_-WRVpOKyd8pI', 1, 'https://images-ext-1.discordapp.net/external/bUhUkIkBSExrSwd5qZ46tn8WCXyLo7NHGCZuiZ65k14/https/payhip.com/cdn-cgi/image/format%3Dauto/https%3A/pe56d.s3.amazonaws.com/o_1hjthktgq2qam9v1s79s9u1nag10.png?format=webp&quality=lossless&width=1193&height=671'),
(58, 'ALL OLD EDITS IN ONE PACK!', 'Kokivfx', 'Kokivfx', 'active', '2024-09-29 23:00:11', '2024-09-29 23:51:48', 'https://drive.google.com/drive/folders/1jgoFVUGyMsGj8Io94cjj9qU7IOzk8LW0?usp=sharing', 1, 'https://images-ext-1.discordapp.net/external/uvS-E-LjyTCgIc4q1pl6UrAwFm5QI4NqWpG82CERnDM/https/images.payhip.com/o_1fp5fuj5819d61n2a13vvbm963vr.png?format=webp&quality=lossless&width=975&height=671'),
(59, 'Pack Mask', 'pack mask 500 masking ', 'Vuzbeo', 'active', '2024-09-30 03:49:19', '2024-09-30 08:53:49', 'https://drive.google.com/drive/folders/1sOsOvLlii-nShPBU2PX522Yh6U3EE-Xy?usp=sharing', 3, 'https://payhip.com/cdn-cgi/image/format=auto,width=1500/https://pe56d.s3.amazonaws.com/o_1fdl5vnsi1q0r1v7r4rip5f20lr.png'),
(60, 'Know your Place', 'The Credit belong to Xsense project file', 'Xsense', 'active', '2024-09-30 09:23:14', '2024-10-06 05:45:23', ' https://mega.nz/file/jGwlRSiY#0QaRLhJza-cCUIvCAhlvrGarM7EDvNHVEMpz7YHWeJk', 2, 'https://i.imgur.com/QY0t899.png'),
(61, 'Neptuns Edit Pack 4', 'Neptuns Edit Pack 4', 'neptune', 'active', '2024-09-30 17:47:31', '2024-10-04 10:11:19', 'https://drive.google.com/file/d/1eq6WF75yE_CSUmdwhbYhaeRgb07bbFdi/view ', 1, 'https://i.imgur.com/z9DO3Rd.png'),
(62, 'KaromePro Pack', 'KaromePro Pack', 'KaromePro', 'active', '2024-09-30 17:51:34', '2024-10-04 18:15:41', 'https://drive.google.com/file/d/1ab_SMeHshP4KC8fIhnt_95W72R_43JlG/edit', 1, 'https://i.imgur.com/NLduelV.jpeg'),
(63, 'Danjin Pack', '<p>INCLUDES:<br />\r\n-20+ SFX<br />\r\n-6+ Overlays<br />\r\n-7 of my project files<br />\r\n-CC&#39;s, Shakes and more<br />\r\n<br />\r\nPROJECT FILES:<br />\r\n<br />\r\n-Rackz<br />\r\n-Tell em<br />\r\n-Juggernaut<br />\r\n-Too Comfortable<br />\r\n-Puffin on Zooties<br />\r\n-All The Stars<br />\r\n-Duppy<br />\r\n<br />\r\nMade on After Effects 2020</p>\r\n\r\n<p>You will get a ZIP&nbsp;<em>(1GB)</em>&nbsp;file</p>\r\n', 'Danjin', 'active', '2024-09-30 17:52:02', '2025-04-16 00:23:05', 'https://mega.nz/folder/HslWwQCJ#Lk3uxXCSRM82g8DxKgjNwA', 1, 'https://images-ext-1.discordapp.net/external/csM5HftHZdJQrLwiWnMzzb8VMzg-sesYK5TbNwdFhvE/https/images.payhip.com/o_1g9ilsv2fhft1qqro581ggk1jpnr.png?format=webp&quality=lossless&width=1193&height=671'),
(64, 'Fyemaj Pack #1', 'Fyemaj Pack #1', 'Fyemaj Pack', 'active', '2024-09-30 17:52:57', '2024-09-30 17:52:57', 'https://drive.google.com/drive/folders/18GULRKARVsvpr2La3EPRVw2e0gINYJS1?usp=sharing Payhip', 1, 'https://images-ext-1.discordapp.net/external/ft0im8eSxS2rvhKFNW60H2V3oMpKZKav3Vzte7xZO3w/https/images.payhip.com/o_1gd9atmel1ahi1cuek111r2v1418m.png?format=webp&quality=lossless&width=671&height=671'),
(65, 'ASTRESIM PACK', 'ASTRESIM PACK', 'ASTRESIM', 'active', '2024-09-30 17:59:00', '2024-09-30 17:59:00', ' https://drive.google.com/file/d/1vm2yxZO6L4yRbqu3KjpaTpRqEU0scEGJ/view', 1, 'https://images-ext-1.discordapp.net/external/tKe8PWcmwIqdWspEtfEg4NL5viYoPra3OuzWxvX2Y40/https/images.payhip.com/o_1fcgqpear1fc81h83lplgm2fofq.png?format=webp&quality=lossless&width=300&height=300'),
(66, 'Neptuns Edit Pack 7', 'Neptuns Edit Pack 7', 'Neptuns', 'active', '2024-09-30 18:36:45', '2024-09-30 18:36:45', 'https://drive.google.com/file/d/15Zwr3uKLwZVorUBXPzLmUZulCbLw_avy/view?usp=sharing', 1, 'https://payhip.com/cdn-cgi/image/format=auto/https://pe56d.s3.amazonaws.com/o_1hrh6tig3drfa0o13mjlk21jse10.png'),
(67, ' Neptuns Free Edit Pack 2', ' Neptuns Free Edit Pack 2', ' Neptuns', 'active', '2024-09-30 18:42:39', '2024-09-30 18:42:39', 'https://drive.google.com/file/d/1xT3qY2WXCD1K-ezDJ7Il8vUp1dJwLmyE/view?usp=sharing', 1, 'https://payhip.com/cdn-cgi/image/format=auto/https://pe56d.s3.amazonaws.com/o_1f9287cae1rnlndeeec8sempd10.png'),
(68, 'Neptuns Edit Pack 5', '<p><strong>ðŸ’€ Neptuns Edit Pack 5</strong>&nbsp;ðŸ’€<br />\r\n<br />\r\n-&nbsp;<strong>Includes</strong>&nbsp;-<br />\r\n<br />\r\n<br />\r\n-&nbsp;<strong>5x</strong>&nbsp;Private Glow Presets<br />\r\n-&nbsp;<strong>2x</strong>&nbsp;Private CCs<br />\r\n-&nbsp;<strong>8x</strong>&nbsp;Overlays (Fire Sparks &amp; Scanlines)<br />\r\n-&nbsp;<strong>1x</strong>&nbsp;Tutorial (How To Use)<br />\r\n-&nbsp;<strong>1x</strong>&nbsp;Project File (&quot;Notice&quot;)<br />\r\n-&nbsp;<strong>5x</strong>&nbsp;Private Particle/Form<br />\r\n-&nbsp;<strong>7x</strong>&nbsp;Neptuns Private 1 Framer</p>\r\n\r\n<p>You will get a RAR&nbsp;<em>(36MB)</em>&nbsp;file</p>\r\n', 'Neptuns', 'active', '2024-09-30 18:43:21', '2024-11-18 00:47:34', 'https://drive.google.com/file/d/1SUW8EOP9BiTSeXAVUXTRcAm_CKjr4CtP/view?usp=sharing', 1, 'https://payhip.com/cdn-cgi/image/format=auto/https://pe56d.s3.amazonaws.com/o_1fmg702ab1gndetc15asf1c16hur.png'),
(70, 'Neptuns Edit Pack 6', '<p><strong>ðŸ’Ž Neptuns Edit Pack 6 ðŸ’Ž</strong><br />\r\n<br />\r\n<strong>- Includes -</strong><br />\r\n<br />\r\n<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Private Presets (Smoke Effect, Grid Wipe &amp; more)<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Private CCs<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Overlays (Overlays that I use most of the time)<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Private Twitch &amp; Shakes (Impact Twitches)<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Neptuns Private One Framer<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Sound Effects (SFX that I use most of the time)<br />\r\n<br />\r\n<strong>- Feature of this pack -</strong><br />\r\n<br />\r\n@zrevisn<br />\r\n<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Cool Particles<br />\r\n-&nbsp;<strong>6x</strong>&nbsp;Projects Files ( coldstar, dropoff...)</p>\r\n\r\n<p>You will get a RAR&nbsp;<em>(126MB)</em>&nbsp;file</p>\r\n', 'Neptuns', 'active', '2024-09-30 18:46:48', '2024-11-18 00:46:50', 'https://drive.google.com/file/d/1gYJNEAyAzZ17Q_SuifsmdzWIyQ9Q3Jci/view?usp=sharing', 1, 'https://payhip.com/cdn-cgi/image/format=auto/https://pe56d.s3.amazonaws.com/o_1gmjspjk8l1b13s91t6uo7kdp3r.png'),
(71, 'R1XE PACK 3', '<p>WELCOME TO R1XE PACK 3!</p>\r\n\r\n<p>-----------------------------</p>\r\n\r\n<p>This pack includes:</p>\r\n\r\n<p><strong>10 Project Files:</strong></p>\r\n\r\n<p>-----------------------------</p>\r\n\r\n<p>NIGHTS LIKE THIS</p>\r\n\r\n<p>LANCEY OR LANCEY</p>\r\n\r\n<p>LOVE ON ME</p>\r\n\r\n<p>HOW U FEEL</p>\r\n\r\n<p>ASPHYXIA</p>\r\n\r\n<p>SLEEPWALKER</p>\r\n\r\n<p>ARE WE STILL FRIENDS</p>\r\n\r\n<p>I WISH YOU ROSES</p>\r\n\r\n<p><strong>2 COMMISSION EDITS:</strong></p>\r\n\r\n<p>THOT</p>\r\n\r\n<p>RAPVELLI</p>\r\n\r\n<p>-----------------------------</p>\r\n\r\n<p><strong>25+ CCS</strong></p>\r\n\r\n<p><strong>50+ FONTS</strong></p>\r\n\r\n<p><strong>70+ PRESETS</strong></p>\r\n\r\n<p><strong>350+ OVERLAYS</strong></p>\r\n\r\n<p><strong>200+ SFX</strong></p>\r\n\r\n<p>-----------------------------</p>\r\n\r\n<p><strong>After effects CC</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Plugins:</strong>&nbsp;Sapphire plugin, RSMB, Magic Bullet Looks, Trapcode Suits, BCC-Plugin, Videocopilot, Twixtor, Twitch, Optical Flares, and Element 3d&nbsp;&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>ALL PFS CONTAIN ANIME CLIPS EXCEPT&nbsp;<em>&quot;ARE WE STILL FRIENDS&quot;</em>. WAYS TO GET ANIME CLIPS ARE IN THE PACK AS WELL.</strong></p>\r\n\r\n<p>-----------------------------</p>\r\n\r\n<p>Enjoy!!</p>\r\n\r\n<p>-----------------------------</p>\r\n\r\n<p>You will get a TXT&nbsp;<em>(92B)</em>&nbsp;file</p>\r\n', 'R1XE', 'active', '2024-10-01 05:55:27', '2024-10-19 07:17:04', 'https://drive.google.com/file/d/1xgFsUxFP9f6kYXwNE2ceLDInuZX9U2EU/view?usp=sharing', 1, 'https://payhip.com/cdn-cgi/image/format=auto,width=1500/https://pe56d.s3.amazonaws.com/o_1hu5nugnflg81l6r163sdvf1jkir.jpg'),
(72, 'AeScript2021', '<p>vuz</p>\r\n', 'vuz', 'active', '2024-10-01 06:09:42', '2024-10-22 07:35:12', 'https://drive.google.com/drive/folders/1Dj5Z5gDFkXQrjj6HPp8NDBVHnmF0nmcU?usp=sharing', 6, 'https://lh3.googleusercontent.com/pw/AP1GczOk0ifUsNzUpi_bMh7HdPfu2r0OnuocOUeN3Fjy6Qr_DocY3P5RTEOornZAUAYyrK2Osn2QqzjGY9u7uTcR5Es74u9v_VIz1VgihtqhEZbiq06IDiSeOCnEO574jyhZu437YNKDLzzwqnGTWPC25zeJ=w1325-h654-s-no-gm?authuser=0'),
(73, 'Money Hisoka', '<p>Money Hisoka</p>\r\n', 'Basil', 'active', '2024-10-03 11:24:07', '2025-07-27 05:28:35', 'https://drive.google.com/file/d/1UaIWq2WekwmI6pC3pHzj1Gq48NJSdDK_/view?usp=sharing', 2, 'https://i.imgur.com/gwUYrND.png'),
(74, 'Fury Abstract Pack', 'Fury Abstract Pack', 'Fury', 'active', '2024-10-06 06:21:27', '2024-10-06 06:21:27', 'https://drive.google.com/drive/folders/1yt0oY_6RkgYJmaMzXx_709LB3fLcx52R', 1, 'https://payhip.com/cdn-cgi/image/format=auto,width=1500/https://pe56d.s3.amazonaws.com/o_1hos2kcpg1qt2rf11p3610esvfp10.png'),
(75, 'IZXA EDITING PACK 2', '<p>IZXA EDITING PACK 02</p>\r\n\r\n<p>AE (2020+)</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>IMPORTANT</p>\r\n\r\n<p>To access the pack DM @izxa_aep on Instagram with a screenshot of your receipt and your email requesting access to the link.</p>\r\n\r\n<p>You will be assured access within 24 hours after sending the receipt to @izxa_aep on Instagram.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>The Pack includes</p>\r\n\r\n<ul>\r\n	<li><strong>12 Project Files</strong></li>\r\n	<li><strong>10 Stylized Color Corrections</strong></li>\r\n	<li><strong>16 Presets</strong></li>\r\n	<li><strong>10 Shakes</strong></li>\r\n	<li><strong>200+ Cinematic Overlays 4K</strong></li>\r\n	<li><strong>200+ Textures</strong></li>\r\n	<li><strong>50+ Action Sound effects</strong></li>\r\n	<li><strong>Personal Fonts</strong></li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong><em>Presets works on After Effects (2020 or Higher)</em></strong></p>\r\n\r\n<p><strong><em>Project Files works on After Effects (2020 or Higher, some files only work in AE 2023 &amp; 2024)</em></strong></p>\r\n\r\n<p><strong><em>Project Files do not include all the resources</em></strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Plugins</strong>&nbsp;-&nbsp;Film Convert Nitrate,&nbsp;sapphire, BCC, video copilot, the universe, Looks, ae pixel Sorter, deep glow, glitchy, Modulation</p>\r\n', 'IZXA', 'active', '2024-10-06 06:23:03', '2024-11-17 06:24:14', 'https://drive.google.com/drive/folders/10FBWntEGjHKES0N7d_xqSFZdIt-kgG3D', 1, 'https://payhip.com/cdn-cgi/image/format=auto,width=1500/https://pe56d.s3.amazonaws.com/o_1hl751cnf1tk513ni1gsa1tt32u51o.jpg'),
(76, 'My Hero Academia Raw cut', '<p>My Hero Academia Raw cut</p>\r\n', 'vuz', 'active', '2024-10-06 06:27:11', '2025-12-01 04:37:14', 'https://mega.nz/folder/Ohh3zSDT#A8u8RaLVlfHiLoM2Cd057g', 12, 'https://i.ibb.co/Mx1tPqWF/4f238c5cd74e.png'),
(77, 'Jujutsu Kaisen raw cut', '<p>Jujutsu Kaisen raw cut</p>\r\n', 'vuz', 'active', '2024-10-06 06:30:32', '2025-04-14 19:32:22', 'https://mega.nz/folder/Chx0yYDB#QtjEihyylSPHLgUd6lbAtg', 12, 'https://lh3.googleusercontent.com/pw/AP1GczPlKvq9WMjtElXYs5JC7qkuuwr5ZADMDurZGrFIAiJVdIchNUe0AEezSdDxGTc9uKRHtT-Xr4B0Wxh8NXL-7qOULPP3IdkQKWjf_qF0pFddaD9jeOHt44JPImo8-FTmFMJeNi8MBcPbJu85nWfySn87=w1400-h788-s-no-gm?authuser=0'),
(78, 'Jojo Raw cut', '<p>Jojo Raw cut</p>\r\n', 'vuz', 'active', '2024-10-06 06:32:33', '2025-04-14 19:32:09', 'https://mega.nz/folder/WlQmkBKb#afVvgo79AOWb4Bo0eWBPIQ', 12, 'https://lh3.googleusercontent.com/pw/AP1GczMYXzz1w8CmH9CZ4P1YjVYy8jzgkQzbvG8hrmCc3T_I0rYw9at2oggkKJSlQs0Y8IYT18nigHuQIhjL1MYmzTF-kmm07KUMxVtGqvjQVRmBm7zTQ1Rh6OF-uW29gN6YF_egeFi6wjoHbLAKi2pO8rpm=w1280-h720-s-no-gm?authuser=0'),
(79, 'Editing Clip no dead frame', '<p>Editing Clip no dead frame</p>\r\n', 'Vuz', 'active', '2024-10-06 06:38:50', '2024-11-18 01:24:49', 'https://drive.google.com/drive/folders/17_BH8gHlrs_dIQ0hczmGvgibJsd7wuf0', 12, 'https://lh3.googleusercontent.com/pw/AP1GczMNjei3xJqkZEjEnVYyC7SpxigvTvnsiBvOX9tJMgOKGI1CvmsAo_azpkzLGiL4-HWHv2IcJrdyyeC7vvKzsiB5PFH5ioLJKM8tmgM2ZQHk2bHdBGhlHHjTmJthvRnB7CK2Oz-R3uj6adRzWCSE8lDW=w1595-h618-s-no-gm?authuser=0'),
(80, 'ARISA n1krus', 'ARISA n1krus', 'n1krus', 'active', '2024-10-06 07:08:20', '2024-10-06 07:08:20', 'https://drive.google.com/file/d/1_PbN_2zlEShyiBOfvYBXgjvtzC7QsSen/view?usp=sharing', 2, 'https://i.ytimg.com/vi/RMb2xu9Gg0k/hqdefault.jpg?sqp=-oaymwEpCNACELwBSFryq4qpAxsIARUAAIhCGAHYAQHiAQwIHBACGAYgATgBQAE=&rs=AOn4CLDJ8oZPoLz0dyNO79bjew1qck67kA'),
(81, 'KNY CONSUME TNC1', 'KNY CONSUME TNC1', 'n1krus', 'active', '2024-10-06 08:40:49', '2024-10-06 08:40:55', 'https://drive.google.com/drive/folders/1G2kpVBqcx7ekcc0Wo84UMUQJ6uh2O7Yd', 2, 'https://payhip.com/cdn-cgi/image/format=auto/https://pe56d.s3.amazonaws.com/o_1i33e70ml1di81tou1qcft3e1u8h1a.png'),
(83, 'TRAGIC [4K] fliz ', 'TRAGIC [4K] fliz ', 'fliz ', 'active', '2024-10-06 03:39:36', '2024-10-06 03:39:36', 'https://drive.google.com/file/d/1VxFdeptvb_CcBOyy9n46OKtu5Oxct8bm/view?usp=sharing', 2, 'https://i.imgur.com/LkXKwV0.png'),
(86, 'On My Own Project ', 'On My Own ~ AMV', 'Asairesan', 'active', '2024-10-06 21:13:39', '2024-10-06 22:26:28', 'https://drive.google.com/file/d/1Nl85yJx2VFKwi2Ngf9mIiDX5L4aLYM7Z/view?usp=sharing', 2, 'https://i.imgur.com/qAcq9MD.jpeg'),
(88, 'Demon slayer - In the end', 'Demon slayer - In the end -Wyrux', 'Wyrux', 'active', '2024-10-07 17:13:49', '2024-10-07 17:14:15', 'https://mega.nz/file/61oVQTAK#9zFHvJtrZjp3HXUqewKXkE1ISyi4VtHvSjf2Gvgd56I', 2, 'https://i.imgur.com/n77y0hV.png'),
(90, 'SUPERHEAVEN [4K]', 'SUPERHEAVEN [4K]', 'fliz', 'active', '2024-10-10 01:19:09', '2024-10-10 01:19:09', 'https://drive.google.com/drive/folders/1GO8AqkS8Yf4nyHZsMygYE2TNjeWX6EtC', 2, 'https://i.imgur.com/AyWMznz.png'),
(91, 'Killbok Editing Pack', '<p>Includes:</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li>7 Pfs with all the assets (overlays, pngs, footage etc.) - Get Busy, you cannot escape reality, LNLY, In The Air, Diedb4, Yams Day, Vampire Love.</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li>23 Custom Overlays made by me in variaty of softwares ( Smokes, Embers, Litghnings etc.)</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li>53 Presets - Warps, Effects, Impact Shakes, CC etc.</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li>AI//Stable Diffusion Tutorial</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li>20 Sfx</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li>Text Document With Useful links, assets etc</li>\r\n</ul>\r\n', 'Killbok', 'active', '2024-10-10 01:25:05', '2024-10-19 07:25:52', 'https://mega.nz/folder/ygRzFbiA#fmhUzRZgPb-kWAji0vfQEw', 1, 'https://i.imgur.com/4AfZDmt.jpeg'),
(92, 'MAAD City - Kyrik', 'MAAD City - Kyrik', 'Kyrik', 'active', '2024-10-10 02:00:33', '2024-10-10 02:00:33', 'https://drive.google.com/file/d/1LfXyN48rJQidv7liLyH7xR3ZnTr3t_bN/view?usp=sharing', 2, 'https://i.imgur.com/Q4EUULY.jpeg'),
(93, 'Blossom - Kyrik', 'Blossom - Kyrik', 'Kyrik', 'active', '2024-10-10 02:35:02', '2024-10-10 02:35:07', 'https://drive.google.com/file/d/10z7CKppcHFvsAuGCopqYIiTNHt3qycjR/view?usp=sharing', 2, 'https://i.imgur.com/IVVyCTR.jpeg'),
(94, 'See you again - Kyrik', 'See you again - Kyrik', 'Kyrik', 'active', '2024-10-10 02:36:19', '2024-10-10 02:36:19', 'https://drive.google.com/file/d/1s26U-i7vOUALYnk6SJNqrtYpDWJ2iDjg/view?usp=sharing', 2, 'https://i.imgur.com/UfSaPZO.jpeg'),
(95, 'SNAKE EDITING PACK 3', 'SNAKE EDITING PACK 3', 'SNAKE', 'active', '2024-10-10 02:51:53', '2024-10-12 05:49:26', 'https://drive.google.com/file/d/1mgSBZiD6RqYlNcEznvqI8PeGl7Ig2myS/view?usp=sharing', 1, 'https://i.imgur.com/K0uEK5Z.png'),
(96, 'Starboy - Gojo Satoru', 'Starboy - Gojo Satoru', 'Blubz', 'active', '2024-10-14 09:47:51', '2024-10-14 09:47:51', 'https://drive.google.com/drive/folders/1ND6jeeDzWZU1M8bZiYaRce7-UhmVo6kD', 2, 'https://i.imgur.com/dj1Ras4.jpeg'),
(97, 'Shoko Pack #1', 'Shoko Pack #1', 'Shoko', 'active', '2024-10-17 01:31:20', '2024-10-17 01:32:11', 'https://drive.google.com/file/d/1q2xfnv0H8bkClOBQmKGEQQJ8_odIyMXq/view', 1, 'https://i.imgur.com/BjTcfy0.png');
INSERT INTO `project` (`project_id`, `title`, `description`, `author`, `status`, `create_date`, `update_date`, `source`, `category_id`, `image`) VALUES
(98, 'All The Things She Said -Kyrix', 'All The Things She Said -Kyrix', 'Kyrix', 'active', '2024-10-17 01:45:58', '2024-10-17 01:45:58', 'https://drive.google.com/file/d/1T1Z68aW9wzk2mA-FmkLtuhdo4a3I0Dws/view?usp=drive_link', 2, 'https://i.imgur.com/EwdXmja.jpeg'),
(99, 'Bit of You Mograph - Kyrix', 'Bit of You Mograph', 'Kyrix', 'active', '2024-10-17 01:49:24', '2024-10-17 01:49:24', 'https://drive.google.com/file/d/1BTayM2P8GiEsKrE9c-MtE1m_CDfnE-6L/view?usp=sharing', 2, 'https://i.imgur.com/atdhBqI.jpeg'),
(100, 'KYTN PACK 4!!!', '<p>Pass unzip :S6hsLBetIBXWCN7d-stjomRkuXhWqy0xbdg1t8 QgY_uS6hsLBetIBXWCN7d-stjomRkuXhWqy0xbdg1t8</p>\r\n\r\n<p><strong>The KYTN PACK 4 Includes!</strong></p>\r\n\r\n<ul>\r\n	<li>500+ Fonts</li>\r\n	<li>2 cc&#39;s</li>\r\n	<li>10 framers</li>\r\n	<li>8 misc presets</li>\r\n	<li>12 shakes</li>\r\n	<li>500+ sfx</li>\r\n	<li>20 + textures/assets</li>\r\n</ul>\r\n\r\n<ul>\r\n	<li>BABYDOLL Edit w/ all files</li>\r\n	<li>CALL Edit w/ all files</li>\r\n	<li>WASTELAND&nbsp;Edit w/ all files</li>\r\n	<li>WHY&nbsp;Edit w/ all files</li>\r\n	<li>Promo Video Project file</li>\r\n</ul>\r\n\r\n<ul>\r\n	<li>How to twixtor like kytn using flowframes tutorial</li>\r\n	<li>How to do sound effects like kytn tutorial</li>\r\n</ul>\r\n\r\n<p><br />\r\n<strong>Project files are only accessable in after effects!</strong><br />\r\n<br />\r\n<br />\r\n<strong>Plugins I use</strong>:&nbsp;sapphire, bcc, video copilot, universe, mbl, twixtor, rsmb, aepixelsorter, deep glow, displacer pro, glitchify, pixdither, twitch, bokeh, fl, rowbye, ignite</p>\r\n\r\n<p>You will get a RAR&nbsp;<em>(2GB)</em>&nbsp;file</p>\r\n\r\n<p>&nbsp;</p>\r\n', 'KYTN', 'disable', '2024-10-17 22:48:25', '2025-09-25 19:52:58', 'https://mega.nz/file/9btRiA7D#QgY_uS6hsLBetIBXWCN7d-stjomRkuXhWqy0xbdg1t8', 1, 'https://i.imgur.com/oKB13eQ.png'),
(101, 'Blue Skies Edit', 'Blue Skies Edit ', 'Mrz', 'active', '2024-10-18 20:08:06', '2024-10-18 20:08:06', 'https://drive.google.com/file/d/1hMm6lPCC4p4E9YEv03WZrtkl0oyHcgd4/view?usp=sharing', 2, 'https://i.imgur.com/xIkQ6d9.png'),
(102, 'Corazon New Year Pack!', '<p>Pack includes:</p>\r\n\r\n<p>â–¸Link to clips (flow, waki etc);</p>\r\n\r\n<p>â–¸Overlays 100+;</p>\r\n\r\n<p>â–¸Presets (CC`s, Shakes, One Frames etc);</p>\r\n\r\n<p>â–¸Project Files: &rarr; 1. Glock In My Lap; 2. I Could Fall; 3. Mon&euml;y So Big; 4. Script Collab; 5. Stay; 6. Tek It.</p>\r\n\r\n<p>â–¸Sfx 20+.</p>\r\n\r\n<p>You will get a RAR&nbsp;<em>(46MB)</em>&nbsp;file</p>\r\n', 'Corazon', 'active', '2024-10-23 09:34:37', '2024-10-23 09:34:37', 'https://mega.nz/folder/sLlCnCja#eFSaSmolKQhlZCdWUriWqg', 1, 'https://i.imgur.com/rwL8ccI.png'),
(103, 'Nikzzi 2021 Pack', '<p>Nikzzi 2021 Pack - Includes - Top 9 Edits of the Year // x9 Preset Projects - COCOA Edit- COSMIC Edit- DONT LIKE Edit- NEW MAGIC WAND Edit- OFF THE GRID Edit- FIND HIM Edit- EXPLORE Edit- SHOGUN Edit- TOOK HER TO THE O</p>\r\n\r\n<p>EditNOTE: Some of the project ...</p>\r\n', 'Nikzzi', 'active', '2024-10-24 03:46:41', '2024-10-24 03:46:41', 'https://drive.google.com/file/d/1a17dosrn3goaoJegVLjv7PmXTRR1tr_M/view', 1, 'https://i.imgur.com/ngdgSXG.png'),
(104, 'R1XE 50K PACK - ANIME EDITING PACK', '<p>AE Pack Includes:<br />\r\n<br />\r\n3 Project Files:<br />\r\nGhost<br />\r\nUp to Infinity<br />\r\nDamage<br />\r\n.<br />\r\n350+ overlays<br />\r\nCc&#39;s<br />\r\nQuality settings<br />\r\nShakes<br />\r\nFramers<br />\r\nSfx&#39;s<br />\r\nFonts</p>\r\n\r\n<p>You will get a RAR&nbsp;<em>(3GB)</em>&nbsp;file</p>\r\n', 'R1XE', 'active', '2024-10-24 23:34:27', '2024-10-24 23:34:27', 'https://drive.google.com/file/d/1bVR26pHDfmH0xvgkcvUF4Dh8anjn5vxt/view', 1, 'https://i.imgur.com/3PRxfII.jpeg'),
(105, 'Gojo gets sealed - LET HIM COOK X IMPERIUS', '<p>Let him cook - Project file</p>\r\n\r\n<p>&bull; Project file&nbsp;</p>\r\n\r\n<p>&bull;&nbsp;Gojo gets sealed - LET HIM COOK X IMPERIUS</p>\r\n\r\n<p>&bull; Inludes All Clips, Project file, Overlays, Sfx</p>\r\n', 'sanchezae', 'active', '2024-10-27 03:24:44', '2025-11-16 03:18:57', 'https://drive.google.com/drive/u/0/folders/1xl9XmPvQJphmnxxBjPiEO-qMNnkI5Wej', 2, 'https://i.imgur.com/poTbv50.jpeg'),
(106, 'RetuurnÂ´s 100K Editing Pack', '<p>The pack includes:<br />\r\n- 3 Project files<br />\r\n&nbsp; &rarr; 1. Work Hard, Play Hard, 2. Crystallize &amp; 3. Humans&nbsp;<br />\r\n- 150+ Overlays<br />\r\n&nbsp; &rarr; Shockwaves, Particles, Light Leaks &amp; many more!<br />\r\n- 100+ Sound Effects<br />\r\n- 20+ Color Corrections (Presets)<br />\r\n- 10+ One Framer (Presets)<br />\r\n- 20+ Shakes<br />\r\n- 1 Folder with random effects<br />\r\n- How to Use Tutorial&nbsp;<br />\r\n- Link for Naruto RAW episodes</p>\r\n', 'Retuurn', 'active', '2024-10-28 05:51:32', '2024-10-28 05:51:32', 'https://mega.nz/file/SCRxHazY#hst6QTaliIvXkB_Ts97_-WGuW1FuW1VdF7HJiALDEF4', 1, 'https://i.imgur.com/cRsyM52.png'),
(107, '10K Editing Pack', '<p>TXT file with download link for<br />\r\n10K Editing Pack by Kana Senpai:<br />\r\n<br />\r\nâ˜… 6 Color Correction Presets for AE<br />\r\n<br />\r\nâ˜… 69 Visual Effect Presets for AE<br />\r\n<br />\r\nâ˜… 43 Fonts<br />\r\n<br />\r\nâ˜… 230 Video Overlays<br />\r\n<br />\r\nâ˜… 230 Pictures<br />\r\n<br />\r\nâ˜… 7 Project Files (PF Only)<br />\r\n<br />\r\nâ˜… 1100 Sound Files<br />\r\n<br />\r\nâ˜… Video Guide for FlowFrames<br />\r\n<br />\r\nâ˜… Text Guide for all the Basics of Editing<br />\r\n<br />\r\nâ˜… Text Guide about Creativity<br />\r\n<br />\r\nâ˜… Text Guide about Improvement<br />\r\n<br />\r\nâ˜… Bonus (AE PF of the Promo Video)</p>\r\n\r\n<p>You will get a TXT&nbsp;<em>(65B)</em>&nbsp;file</p>\r\n', 'Kana Senpai', 'active', '2024-10-30 19:04:15', '2024-10-30 19:04:15', 'https://drive.google.com/file/d/1gKa5-s0ILvUwvLnQckMR4P5DgRF4Se_y/view?usp=share_link', 1, 'https://i.imgur.com/aBIYCF9.jpeg'),
(108, 'Broken Sleep', '<p>This only includes the project file</p>\r\n', 'Kyrik', 'active', '2024-10-31 21:09:28', '2024-10-31 21:09:28', 'https://drive.google.com/file/d/1Jctem9GDgeH6jPYdtEkNUSNESDEgAgFS/view?usp=sharing', 2, 'https://i.imgur.com/25MFAZM.jpeg'),
(109, 'Inside Out', '<p>â–¸This includes<br />\r\n&nbsp; &nbsp; â–¸-The Footage<br />\r\n&nbsp; &nbsp; â–¸-The Project File<br />\r\n&nbsp; &nbsp; â–¸-The Audio</p>\r\n', 'Kyrik', 'active', '2024-10-31 21:11:04', '2025-04-15 00:15:11', 'https://drive.google.com/file/d/1Jctem9GDgeH6jPYdtEkNUSNESDEgAgFS/view?usp=drive_link', 2, 'https://i.imgur.com/peN9tqY.jpeg'),
(110, 'Editing pack #1 ryuutardd', '<p>â–¸<strong>My editing pack&lt;333</strong><br />\r\n<br />\r\nâ–¸it contains 10+ project files, preset ,overlay ,etc.<br />\r\n&nbsp; (work on aecc2018 and above)</p>\r\n', 'ryuutardd', 'active', '2025-04-14 19:17:32', '2025-04-15 00:14:46', 'https://mega.nz/folder/sLlCnCja#eFSaSmolKQhlZCdWUriWqg7527534612', 1, 'https://i.imgur.com/ye6lbwM.png'),
(111, 'Kohai Pack 2.0', '<p>Kohai Pack 2.0&nbsp;</p>\r\n\r\n<p><br />\r\nContains:<br />\r\n5 Project Files (24K Magic, Denial, Girlfriend, Lovin on You, Sunflower Feelings [all files] )<br />\r\n101 Overlays&nbsp;(Light Leaks, Shapes, Textures, Transitions &amp; more)<br />\r\n20 Kohai OCs<br />\r\n27 After Effects Presets (CCs, Shakes, FX)<br />\r\n1 Kohai Flow Preset<br />\r\n65 PNGs<br />\r\n10 Fonts<br />\r\n1 Tutorial PF for the presets<br />\r\n<br />\r\nIf you only want the Overlays/PNGs and the Original Content, you don&#39;t have to be an After Effects editor, but this pack is mainly for AE users.</p>\r\n\r\n<p>You will get a RAR&nbsp;<em>(705MB)</em>&nbsp;file</p>\r\n', 'Kohai', 'active', '2025-04-15 00:29:12', '2025-04-15 00:34:57', 'https://mega.nz/folder/ZEM3RAxI#ivSrdefu-qQmf64EdEBRkw', 1, 'https://i.imgur.com/6SAdgwz.png'),
(112, 'Slime Sorcery 2', '<p>Made on After Effects 2020<br />\r\n<br />\r\nIncludes:<br />\r\n-PROJECT FILE (.aep)<br />\r\n-SFX<br />\r\n-OVERLAYS<br />\r\n-CLIPS&nbsp;<br />\r\n<br />\r\nInstructions:<br />\r\nDownload Zip, Open Folder<br />\r\n<br />\r\nPlug-ins (will not provide)<br />\r\n-Signal<br />\r\n-Twixtor<br />\r\n-Sapphire<br />\r\n-MBL<br />\r\n-Deep glow</p>\r\n\r\n<p>You will get a ZIP&nbsp;<em>(97MB)</em>&nbsp;file</p>\r\n', 'Finessem', 'active', '2025-04-16 00:25:16', '2025-04-16 00:25:16', 'https://mega.nz/folder/Z1kFhJob#ZAr5lnppO7BDyX7BOu8u1A', 1, 'https://i.imgur.com/zGtxVTH.png'),
(113, 'Molob\'s 50k Editing Pack', '<p>&bull; This editing pack includes:</p>\r\n\r\n<ul>\r\n	<li>3 Project Files : 📜</li>\r\n	<li>20+ CC :🎨</li>\r\n	<li>20+ Shakes :💨</li>\r\n	<li>40+ VFX includes&nbsp;: 🌟</li>\r\n	<li>300+ Overlays&nbsp;includes&nbsp;:🌌</li>\r\n	<li>300+ Sound Effects 🔊</li>\r\n	<li>Link to Raw Episodes (All Anime) 📥</li>\r\n</ul>\r\n', 'Molob', 'active', '2025-04-25 06:39:05', '2025-04-25 06:39:05', 'https://mega.nz/file/mWgERKqa#YXY8diwIbyd9z--u7th5gGCw341TWqQ-78GPWQxt2uE', 1, 'https://i.imgur.com/IaPl5jS.png'),
(114, 'Zactun Editing Pack 2', '<p>Zactun Editing Pack 2</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>includes&nbsp;</p>\r\n\r\n<p>-Gintama PF</p>\r\n\r\n<p>-Blue Lock Edit PF</p>\r\n\r\n<p>-Netero vs Meruem PF</p>\r\n\r\n<p>-2 more RECENT pfs (all assets included)</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>-Zactun Editing Pack 1 for free</p>\r\n\r\n<p>-Overlays, Backgrounds, and a Animation Breakdown</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>the txt file will contain the link</p>\r\n\r\n<p>You will get a TXT&nbsp;<em>(112B)</em>&nbsp;file</p>\r\n', 'Zactun', 'active', '2025-04-25 06:41:36', '2025-04-25 06:41:36', 'https://drive.google.com/drive/folders/1Vb2wLH19T3oDWf7fsVjfxxpFf7muF3Bu', 1, 'https://i.imgur.com/APlrnst.png'),
(115, 'Sukuna VS Jogo 😈 - BRIGHT WHITE SKY BLUE by sanchezae', '<p>Project file by&nbsp;&nbsp;sanchezae</p>\r\n', ' sanchezae', 'active', '2025-09-08 15:22:47', '2025-11-30 05:43:57', 'https://drive.google.com/drive/folders/14MRLBzu51zZkgaxUocsQF3WewGdwUAdC', 2, 'https://i.ibb.co/5WqL6yW4/6e8d64eaaa97.png');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `favorites` (
  `favorite_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`favorite_id`),
  UNIQUE KEY `user_project` (`user_id`,`project_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `feedback_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

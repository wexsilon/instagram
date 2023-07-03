-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2023 at 10:49 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `instagram`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `author` text NOT NULL,
  `textt` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `pid` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `author`, `textt`, `created_at`, `pid`) VALUES
(1, 'lavazemj', 'وااااای چه نازه', '2023-06-30 11:58:18', 7),
(2, 'lavazemj', 'وای چقدر خوشگله', '2023-06-30 11:58:28', 8),
(3, 'lavazemj', 'منم میخوااام', '2023-06-30 11:58:38', 9),
(4, 'lavazemj', 'چطوری باید سفارش بدیم؟', '2023-06-30 11:59:03', 10),
(5, 'lavazemj', 'جالبه :)', '2023-06-30 11:59:13', 12),
(6, 'lavazemj', 'منم میخواااااااام', '2023-06-30 11:59:50', 1),
(7, 'lavazemj', 'نوش جان', '2023-06-30 12:00:00', 2),
(8, 'lavazemj', 'کی بریم سفر؟', '2023-06-30 12:00:09', 3),
(9, 'lavazemj', 'وای چه نازننن', '2023-06-30 12:00:19', 4),
(10, 'lavazemj', 'همیشه به گردش', '2023-06-30 12:00:30', 5),
(11, 'lavazemj', 'چه منظره قشنگی', '2023-06-30 12:00:46', 6),
(12, 'cuteshopp', 'چه خفن', '2023-06-30 12:04:51', 18),
(13, 'cuteshopp', 'هی بابا خفن', '2023-06-30 12:05:34', 17),
(14, 'cuteshopp', 'چه خشگله', '2023-06-30 12:05:40', 16),
(15, 'cuteshopp', 'خیلی خفنو خشگله', '2023-06-30 12:05:51', 15),
(16, 'cuteshopp', 'قشنگه و کلاسیک', '2023-06-30 12:06:04', 14),
(17, 'cuteshopp', 'عالییییییییی', '2023-06-30 12:06:10', 13),
(18, 'cuteshopp', 'ارتفاعش خیلی زیاده', '2023-06-30 12:06:36', 6),
(19, 'cuteshopp', 'چه منظره قشنگی', '2023-06-30 12:06:44', 5),
(20, 'cuteshopp', 'واقعا نازن', '2023-06-30 12:06:55', 4),
(21, 'cuteshopp', 'هعی روزگار', '2023-06-30 12:07:13', 3),
(22, 'cuteshopp', 'شاد باشید', '2023-06-30 12:07:40', 2),
(23, 'cuteshopp', 'من زا ماشینش خوشم اومد', '2023-06-30 12:07:48', 1),
(24, 'relaxshow', 'نازهههه', '2023-06-30 12:13:14', 9);

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `id` int(10) UNSIGNED NOT NULL,
  `owner` text NOT NULL,
  `reciever` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `follows`
--

INSERT INTO `follows` (`id`, `owner`, `reciever`) VALUES
(1, 'lavazemj', 'cuteshopp'),
(2, 'lavazemj', 'relaxshow'),
(3, 'cuteshopp', 'lavazemj'),
(4, 'relaxshow', 'lavazemj');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` text NOT NULL,
  `pid` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `username`, `pid`) VALUES
(1, 'relaxshow', 1),
(2, 'relaxshow', 2),
(3, 'relaxshow', 3),
(4, 'relaxshow', 4),
(5, 'relaxshow', 5),
(6, 'relaxshow', 6),
(7, 'cuteshopp', 7),
(8, 'cuteshopp', 8),
(9, 'cuteshopp', 9),
(10, 'cuteshopp', 10),
(11, 'cuteshopp', 11),
(12, 'cuteshopp', 12),
(13, 'lavazemj', 13),
(14, 'lavazemj', 14),
(15, 'lavazemj', 15),
(16, 'lavazemj', 16),
(17, 'lavazemj', 17),
(18, 'lavazemj', 18),
(19, 'lavazemj', 6),
(20, 'lavazemj', 5),
(21, 'lavazemj', 4),
(22, 'lavazemj', 1),
(23, 'lavazemj', 2),
(24, 'lavazemj', 3),
(25, 'lavazemj', 9),
(26, 'lavazemj', 8),
(27, 'lavazemj', 7),
(28, 'lavazemj', 12),
(29, 'lavazemj', 11),
(30, 'lavazemj', 10),
(31, 'cuteshopp', 18),
(32, 'cuteshopp', 17),
(33, 'cuteshopp', 16),
(34, 'cuteshopp', 15),
(35, 'cuteshopp', 14),
(36, 'cuteshopp', 13),
(37, 'cuteshopp', 6),
(38, 'cuteshopp', 5),
(39, 'cuteshopp', 4),
(40, 'cuteshopp', 3),
(41, 'cuteshopp', 2),
(42, 'cuteshopp', 1),
(43, 'relaxshow', 18),
(44, 'relaxshow', 17),
(45, 'relaxshow', 13),
(46, 'relaxshow', 14),
(47, 'relaxshow', 15),
(48, 'relaxshow', 12),
(49, 'relaxshow', 10),
(50, 'relaxshow', 11),
(51, 'relaxshow', 7),
(52, 'relaxshow', 9);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `photo` text NOT NULL,
  `comment_count` int(10) UNSIGNED DEFAULT 0,
  `like_count` int(10) UNSIGNED DEFAULT 0,
  `username` text NOT NULL,
  `caption` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `photo`, `comment_count`, `like_count`, `username`, `caption`, `created_at`) VALUES
(1, 'static/media/8c45ab66b03f4e6a8b8ecd97e5d773c4.jpg', 2, 3, 'relaxshow', 'رویاهات رو دنبال کن', '2023-06-30 11:09:54'),
(2, 'static/media/8e5bcc93978256da75a66e15e90a558c.jpg', 2, 3, 'relaxshow', 'آشپزی در طبیعت :)', '2023-06-30 11:11:42'),
(3, 'static/media/daa4472f689c0573f0b67fb2220121de.jpg', 2, 3, 'relaxshow', 'وسایل در طبیعت', '2023-06-30 11:12:40'),
(4, 'static/media/5b8b1d54a05e84492ac5eb7bf6da5d00.jpg', 2, 3, 'relaxshow', 'آهو در طبیعت', '2023-06-30 11:13:21'),
(5, 'static/media/741f2c2cb0fc0c4278f2571c043b7472.jpg', 2, 3, 'relaxshow', 'بالای کوه', '2023-06-30 11:14:13'),
(6, 'static/media/60c129d5e2c259961af5bf3e3440b972.jpg', 2, 3, 'relaxshow', 'قدم زدن در طبیعت یعنی تماشای هزار معجزه', '2023-06-30 11:16:28'),
(7, 'static/media/3a77a05df4e14ad42b32eedc9fb185f4.jpeg', 1, 3, 'cuteshopp', 'قیمت با احترام 100هزار تومن', '2023-06-30 11:24:44'),
(8, 'static/media/5ab16c2399c05c524c921460d4ce392a.jpg', 1, 2, 'cuteshopp', 'قیمت با احترام 400هزار تومن', '2023-06-30 11:27:25'),
(9, 'static/media/dccdade5c753904c9630f39586a4b0cd.jpg', 2, 3, 'cuteshopp', 'قیمت با احترام 200هزار تومن', '2023-06-30 11:28:49'),
(10, 'static/media/bd6ee307ad82217fe8cbbedc427d6807.jpg', 1, 3, 'cuteshopp', 'قیمت با احترام 50هزار تومن', '2023-06-30 11:31:41'),
(11, 'static/media/d90044b97657bba9964272a4bd4846aa.jpg', 0, 3, 'cuteshopp', 'قیمت با احترام 60هزار تومن', '2023-06-30 11:33:28'),
(12, 'static/media/dab8ce45bda37c9e3c2b9ac8b475907d.jpeg', 1, 3, 'cuteshopp', 'قیمت با احترام 70هزار تومن', '2023-06-30 11:35:01'),
(13, 'static/media/9610b36e923f069551f39c41304780ad.jpg', 1, 3, 'lavazemj', 'قیمت با احترام 3 میلیون تومن', '2023-06-30 11:40:17'),
(14, 'static/media/6b83ed55b6f1f2aaf07c19d9dc17d80d.jpg', 1, 3, 'lavazemj', 'قیمت با احترام 200هزار تومن', '2023-06-30 11:41:37'),
(15, 'static/media/304729c733d457ad243e50250419982b.jpg', 1, 3, 'lavazemj', 'قیمت با احترام 500هزار تومن', '2023-06-30 11:45:01'),
(16, 'static/media/2207c04e7100f447d06c82025d249bb9.jpg', 1, 2, 'lavazemj', 'قیمت با احترام 800هزار تومن', '2023-06-30 11:49:14'),
(17, 'static/media/09d999bc5a5ee73af343ff4c3f828529.jpg', 1, 3, 'lavazemj', 'قیمت با احترام 7 میلیون تومن', '2023-06-30 11:52:17'),
(18, 'static/media/9f672a975751cd327aade1296d906ef2.jpg', 1, 3, 'lavazemj', 'قیمت با احترام 600هزار تومن', '2023-06-30 11:54:23');

-- --------------------------------------------------------

--
-- Table structure for table `saves`
--

CREATE TABLE `saves` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` text NOT NULL,
  `pid` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saves`
--

INSERT INTO `saves` (`id`, `username`, `pid`) VALUES
(1, 'relaxshow', 1),
(2, 'cuteshopp', 7),
(3, 'lavazemj', 12),
(4, 'lavazemj', 3),
(5, 'lavazemj', 13),
(6, 'cuteshopp', 18),
(7, 'cuteshopp', 6),
(8, 'relaxshow', 17),
(9, 'relaxshow', 5),
(10, 'relaxshow', 6);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `photo` text DEFAULT 'static/media/default.jpg',
  `fullname` text NOT NULL,
  `bio` text DEFAULT NULL,
  `website` text DEFAULT NULL,
  `phone` text DEFAULT NULL,
  `email` text NOT NULL,
  `following_count` int(10) UNSIGNED DEFAULT 0,
  `follower_count` int(10) UNSIGNED DEFAULT 0,
  `post_count` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `photo`, `fullname`, `bio`, `website`, `phone`, `email`, `following_count`, `follower_count`, `post_count`) VALUES
(1, 'relaxshow', '$2y$10$wxWGguVvPyiiCer9aI5ieONEIZYkxOIoPdOrUqh6TUQIfEtBxgolS', 'static/media/23dc0b6a2cc10be9cf5789d9a7a49e3e.jpg', 'relaxiii', 'با ریلکس شو ، ریلکس شووو', NULL, NULL, 'relaxi1402@gmail.com', 1, 1, 6),
(3, 'cuteshopp', '$2y$10$6v2ilbBvRMXXP5nuFoC6i.a22UGgfGq6VkgTA2Fpy4qwbe0UxfeeC', 'static/media/e29c315e7c921d84033c1ce12311f392.png', 'cuteshoppp', NULL, NULL, NULL, 'cuteshopp@gmail.com', 1, 1, 6),
(4, 'lavazemj', '$2y$10$FWoa1Qrvqs7SM/JDbHmtWeGOqxcCQxwJIOwQUK9KOx6MheuOc/yAm', 'static/media/62719763b44a47d4643a86bfcc529e62.jpg', 'lavazemjj', 'فروش انواع لوازم جانبی لپ تاپ با کمترین قیمت', NULL, NULL, 'lavazemj@gmail.com', 2, 2, 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saves`
--
ALTER TABLE `saves`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `follows`
--
ALTER TABLE `follows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `saves`
--
ALTER TABLE `saves`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

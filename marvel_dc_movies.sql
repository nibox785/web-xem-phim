-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 01, 2025 at 07:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marvel_dc_movies`
--

-- --------------------------------------------------------

--
-- Table structure for table `actors`
--

CREATE TABLE `actors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `birth_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `actors`
--

INSERT INTO `actors` (`id`, `name`, `birth_date`) VALUES
(1, 'Robert Downey Jr.', NULL),
(2, 'Gwyneth Paltrow', NULL),
(3, 'Edward Norton', NULL),
(4, 'Liv Tyler', NULL),
(5, 'Mickey Rourke', NULL),
(6, 'Chris Hemsworth', NULL),
(7, 'Natalie Portman', NULL),
(8, 'Tom Hiddleston', NULL),
(9, 'Chris Evans', NULL),
(10, 'Hugo Weaving', NULL),
(11, 'Scarlett Johansson', NULL),
(12, 'Guy Pearce', NULL),
(13, 'Anthony Hopkins', NULL),
(14, 'Sebastian Stan', NULL),
(15, 'Chris Pratt', NULL),
(16, 'Zoe Saldana', NULL),
(17, 'James Spader', NULL),
(18, 'Paul Rudd', NULL),
(19, 'Michael Douglas', NULL),
(20, 'Elizabeth Olsen', NULL),
(21, 'Benedict Cumberbatch', NULL),
(22, 'Tilda Swinton', NULL),
(23, 'Michael Rooker', NULL),
(24, 'Tom Holland', NULL),
(25, 'Michael Keaton', NULL),
(26, 'Mark Ruffalo', NULL),
(27, 'Chadwick Boseman', NULL),
(28, 'Michael B. Jordan', NULL),
(29, 'Josh Brolin', NULL),
(30, 'Evangeline Lilly', NULL),
(31, 'Brie Larson', NULL),
(32, 'Samuel L. Jackson', NULL),
(33, 'Jake Gyllenhaal', NULL),
(34, 'Florence Pugh', NULL),
(35, 'Simu Liu', NULL),
(36, 'Tony Leung', NULL),
(37, 'Gemma Chan', NULL),
(38, 'Richard Madden', NULL),
(39, 'Zendaya', NULL),
(40, 'Rachel McAdams', NULL),
(41, 'Christian Bale', NULL),
(42, 'Natalie Portman', NULL),
(43, 'Letitia Wright', NULL),
(44, 'Tenoch Huerta', NULL),
(45, 'Jonathan Majors', NULL),
(46, 'Henry Cavill', NULL),
(47, 'Amy Adams', NULL),
(48, 'Ben Affleck', NULL),
(49, 'Jesse Eisenberg', NULL),
(50, 'Will Smith', NULL),
(51, 'Margot Robbie', NULL),
(52, 'Gal Gadot', NULL),
(53, 'Chris Pine', NULL),
(54, 'Jason Momoa', NULL),
(55, 'Patrick Wilson', NULL),
(56, 'Zachary Levi', NULL),
(57, 'Mark Strong', NULL),
(58, 'Jurnee Smollett', NULL),
(59, 'Kristen Wiig', NULL),
(60, 'Idris Elba', NULL),
(61, 'John Cena', NULL),
(62, 'Dwayne Johnson', NULL),
(63, 'Pierce Brosnan', NULL),
(64, 'Lucy Liu', NULL),
(65, 'Ezra Miller', NULL),
(66, 'Heath Ledger', NULL),
(67, 'Joaquin Phoenix', NULL),
(68, 'Robert Pattinson', NULL),
(69, 'Zoë Kravitz', NULL),
(70, 'Ryan Reynolds', NULL),
(71, 'Morena Baccarin', NULL),
(72, 'Josh Brolin', NULL),
(73, 'Hugh Jackman', NULL),
(74, 'Patrick Stewart', NULL),
(75, 'Dafne Keen', NULL),
(76, 'Anya Taylor-Joy', NULL),
(77, 'Maisie Williams', NULL),
(78, 'Shameik Moore', NULL),
(79, 'Jake Johnson', NULL),
(80, 'Ryan Potter', NULL),
(81, 'Scott Adsit', NULL),
(82, 'Kevin Conroy', NULL),
(83, 'Dana Delany', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','moderator') DEFAULT 'moderator',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `role`, `created_at`, `email`) VALUES
(1, 'admin1', '$2y$10$Ijx8MHw0SNZJX6IA31i8.OVD/8fgUbOw9kVpLG6HzLnkF3HjZMwJ.', 'moderator', '2025-04-22 06:43:55', 'admin1@gmail.com'),
(2, 'admin2', '$2y$10$3WaAOT.S0oKNNcthF18./OT7WGmwXng1J07xTikQeS2eCfDauDjwG', 'super_admin', '2025-04-22 07:22:24', 'admin1@marveldc.com');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `movie_id`, `comment_text`, `created_at`, `updated_at`, `is_approved`) VALUES
(0, 12, 32, 'hay', '2025-04-23 09:04:28', '2025-04-23 09:04:28', 1),
(0, 12, 15, 'cũng hay', '2025-04-23 09:23:06', '2025-04-23 09:23:06', 1),
(0, 12, 31, 'dc', '2025-04-23 09:23:43', '2025-04-23 09:23:43', 1),
(0, 13, 31, 'hay mà', '2025-04-23 09:25:56', '2025-04-23 09:25:56', 1),
(0, 13, 33, 'khá hay', '2025-04-23 09:49:19', '2025-04-23 09:49:19', 1),
(0, 13, 23, 'ok', '2025-04-24 03:11:49', '2025-04-24 03:11:49', 1),
(0, 12, 25, 'ok', '2025-04-30 16:14:46', '2025-04-30 16:14:46', 1),
(0, 12, 46, 'tốt', '2025-05-01 03:38:24', '2025-05-01 03:38:24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(1, 'Hành động'),
(2, 'Phiêu lưu'),
(3, 'Khoa học viễn tưởng'),
(4, 'Kinh dị'),
(5, 'Giả tưởng'),
(6, 'Tâm lý'),
(7, 'Tội phạm'),
(8, 'Hài hước'),
(9, 'Hoạt hình');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `universe_id` int(11) DEFAULT NULL,
  `release_year` int(11) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `universe_id`, `release_year`, `thumbnail`, `video_url`, `featured`, `description`) VALUES
(1, 'Iron Man', 1, 2008, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/78lPtwv72eTNqFW9COBYI0dWDJa.jpg', 'https://www.youtube.com/watch?v=8ugaeA-nMTc', 0, 'Tony Stark, một tỷ phú thiên tài công nghệ, chế tạo bộ giáp sắt để trốn thoát khi bị bắt cóc và trở thành siêu anh hùng Iron Man.'),
(2, 'The Incredible Hulk', 1, 2008, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/wa60s9NSvrH87NFCArmobnIyNnh.jpg', 'https://www.youtube.com/watch?v=xbqNb2PFKKA', 0, 'Bruce Banner sống trong bóng tối khi cố gắng kiểm soát sự biến đổi thành Hulk – sinh vật khổng lồ với sức mạnh khủng khiếp.'),
(3, 'Iron Man 2', 1, 2010, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/6WBeq4fCfn7AN0o21W9qNcRF2l9.jpg', 'https://www.youtube.com/watch?v=BoohRoVA9WQ', 0, 'Tony Stark đối mặt với áp lực chính phủ, sức khỏe suy yếu và kẻ thù mới là Whiplash – một thiên tài kỹ thuật đầy thù hận.'),
(4, 'Thor', 1, 2011, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/prSfAi1xGrhLQNxVSUFh61xQ4Qy.jpg', 'https://www.youtube.com/watch?v=JOddp-nlNvQ&t=1s', 0, 'Thần sấm Thor bị đày xuống Trái Đất và học cách trở thành người xứng đáng để lấy lại sức mạnh và cây búa Mjolnir.'),
(5, 'Captain America: The First Avenger', 1, 2011, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/vSNxAJTlD0r02V9sPYpOjqDZXUK.jpg', 'https://www.youtube.com/watch?v=JerVrbLldXw&t=1s', 0, 'Steve Rogers, một thanh niên yếu ớt, trở thành siêu chiến binh Captain America trong Thế chiến II để chống lại Hydra.'),
(6, 'The Avengers', 1, 2012, 'https://image.tmdb.org/t/p/w500/RYMX2wcKCBAr24UyPD7xwmjaTn.jpg', 'https://www.youtube.com/watch?v=eOrNdBpGMv8', 0, 'Các siêu anh hùng hợp lực để bảo vệ Trái Đất khỏi cuộc xâm lăng ngoài hành tinh do Loki dẫn đầu.'),
(7, 'Iron Man 3', 1, 2013, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/qhPtAc1TKbMPqNvcdXSOn9Bn7hZ.jpg', 'https://www.youtube.com/watch?v=Ke1Y3P9D0Bc', 0, 'Sau sự kiện New York, Tony Stark phải đối đầu với kẻ thù mới là Mandarin và vượt qua nỗi ám ảnh hậu chiến.'),
(8, 'Thor: The Dark World', 1, 2013, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/wD6g4EcmR6R3VNbuBmNOVq2qWrM.jpg', 'https://www.youtube.com/watch?v=npvJ9FTgZbM', 0, 'Thor chiến đấu chống lại Malekith – kẻ muốn đưa vũ trụ trở về bóng tối bằng sức mạnh của Aether.'),
(9, 'Captain America: The Winter Soldier', 1, 2014, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/tVFRpFw3xTedgPGqxW0AOI8Qhh0.jpg', 'https://www.youtube.com/watch?v=7SlILk2WMTI', 0, 'Steve Rogers khám phá âm mưu bên trong SHIELD và phải đối đầu với người bạn cũ bị tẩy não – Winter Soldier.'),
(10, 'Guardians of the Galaxy', 1, 2014, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/jPrJPZKJVhvyJ4DmUTrDgmFN0yG.jpg', 'https://www.youtube.com/watch?v=d96cjJhvlMA', 0, 'Một nhóm sinh vật ngoài hành tinh kỳ quặc hợp sức để ngăn chặn Ronan hủy diệt thiên hà bằng viên đá vô cực.'),
(11, 'Avengers: Age of Ultron', 1, 2015, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/4ssDuvEDkSArWEdyBl2X5EHvYKU.jpg', 'https://www.youtube.com/watch?v=tmeOjFno6Do', 1, 'Biệt đội Avengers chiến đấu với Ultron – trí tuệ nhân tạo do Tony Stark tạo ra – đang đe dọa tiêu diệt loài người.'),
(12, 'Ant-Man', 1, 2015, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/rQRnQfUl3kfp78nCWq8Ks04vnq1.jpg', 'https://www.youtube.com/watch?v=pWdKf3MneyI', 0, 'Scott Lang trở thành Ant-Man – anh hùng nhỏ bé với sức mạnh lớn – để đánh cắp công nghệ khỏi tay kẻ xấu.'),
(13, 'Captain America: Civil War', 1, 2016, 'https://image.tmdb.org/t/p/w500/rAGiXaUfPzY7CDEyNKUofk3Kw2e.jpg', 'https://www.youtube.com/watch?v=dKrVegVI0Us', 0, 'Mâu thuẫn giữa Iron Man và Captain America khiến Avengers chia rẽ, dẫn đến cuộc đối đầu nội bộ căng thẳng.'),
(14, 'Doctor Strange', 1, 2016, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/uGBVj3bEbCoZbDjjl9wTxcygko1.jpg', 'https://www.youtube.com/watch?v=HSzx-zryEgM', 1, 'Bác sĩ phẫu thuật Stephen Strange học phép thuật sau tai nạn, trở thành Pháp sư Tối thượng bảo vệ thực tại.'),
(15, 'Guardians of the Galaxy Vol. 2', 1, 2017, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/y4MBh0EjBlMuOzv9axM4qJlmhzz.jpg', 'https://www.youtube.com/watch?v=dW1BIid8Osg', 0, 'Nhóm Vệ binh đối mặt với bí mật về cha của Star-Lord – một thực thể quyền năng tên là Ego.'),
(16, 'Spider-Man: Homecoming', 1, 2017, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/c24sv2weTHPsmDa7jEMN0m2P3RT.jpg', 'https://www.youtube.com/watch?v=rk-dF1lIbIg', 1, 'Peter Parker cố cân bằng cuộc sống học sinh và vai trò người hùng khi đối đầu kẻ buôn vũ khí là Vulture.'),
(17, 'Thor: Ragnarok', 1, 2017, 'https://image.tmdb.org/t/p/w500/rzRwTcFvttcN1ZpX2xv4j3tSdJu.jpg', 'https://www.youtube.com/watch?v=ue80QwXMRHg', 0, 'Thor bị giam ở hành tinh Sakaar và phải chiến đấu trong đấu trường trước khi trở lại Asgard để ngăn tận thế.'),
(18, 'Black Panther', 1, 2018, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/op1gn2mK4ohZfhpKj92KEbwvc9C.jpg', 'https://www.youtube.com/watch?v=xjDjIWPwcPU', 0, 'T’Challa trở thành vua Wakanda và đối mặt với thử thách từ Killmonger – kẻ muốn lật đổ vương quyền.'),
(19, 'Avengers: Infinity War', 1, 2018, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/7WsyChQLEftFiDOVTGkv3hFpyyt.jpg', 'https://www.youtube.com/watch?v=6ZfuNTqbHE8', 1, 'Avengers cùng các đồng minh chiến đấu để ngăn Thanos thu thập đủ các viên đá vô cực và hủy diệt một nửa vũ trụ.'),
(20, 'Ant-Man and the Wasp', 1, 2018, 'https://image.tmdb.org/t/p/w500/rv1AWImgx386ULjcf62VYaW8zSt.jpg', 'https://www.youtube.com/watch?v=8_rTIAOohas', 0, 'Scott Lang hợp tác với Hope van Dyne để giải cứu mẹ cô khỏi Thế giới Lượng tử, đồng thời đối phó kẻ địch mới Ghost.'),
(21, 'Captain Marvel', 1, 2019, 'https://image.tmdb.org/t/p/w500/AtsgWhDnHTq68L0lLsUrCnM7TjG.jpg', 'https://www.youtube.com/watch?v=Z1BCujX3pw8', 0, 'Carol Danvers trở thành siêu anh hùng mạnh mẽ nhất vũ trụ và khám phá quá khứ cũng như vai trò của mình trong cuộc chiến Kree-Skrull.'),
(22, 'Avengers: Endgame', 1, 2019, 'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg', 'https://www.youtube.com/watch?v=TcMBFSGVi1c', 1, 'Sau khi một nửa vũ trụ bị xóa sổ, các Avengers còn sống thực hiện kế hoạch du hành thời gian để đảo ngược hành động của Thanos.'),
(23, 'Spider-Man: Far From Home', 1, 2019, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/4q2NNj4S5dG2RLF9CpXsej7yXl.jpg', 'https://www.youtube.com/watch?v=Nt9L1jCKGnE', 0, 'Peter Parker đến châu Âu trong chuyến đi học và phải đối mặt với kẻ thù mới là Mysterio, kẻ điều khiển ảo ảnh.'),
(24, 'Black Widow', 1, 2021, 'https://cdn.marvel.com/content/1x/black_widow_beauty_shot_static_4k_bd_digital_us_pur_01.png', 'https://www.youtube.com/watch?v=Fp9pNPdNwjI', 0, 'Natasha Romanoff đối mặt với quá khứ và gia đình cũ của mình khi cô tìm cách tiêu diệt chương trình Red Room.'),
(25, 'Shang-Chi and the Legend of the Ten Rings', 1, 2021, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/d08HqqeBQSwN8i8MEvpsZ8Cb438.jpg', 'https://www.youtube.com/watch?v=8YjFbMbfXaQ', 0, 'Shang-Chi buộc phải đối mặt với quá khứ và cha mình – Wenwu – người đứng đầu tổ chức Ten Rings.'),
(26, 'Eternals', 1, 2021, 'https://image.tmdb.org/t/p/w500/14QbnygCuTO0vl7CAFmPf1fgZfV.jpg', 'https://www.youtube.com/watch?v=x_me3xsvDgk', 0, 'Một nhóm sinh vật bất tử bảo vệ Trái Đất khỏi các Deviants và chuẩn bị cho mối đe dọa lớn hơn từ Celestials.'),
(27, 'Spider-Man: No Way Home', 1, 2021, 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg', 'https://www.youtube.com/watch?v=JfVOs4VSpmA', 1, 'Peter Parker vô tình mở đa vũ trụ, khiến các phản diện từ vũ trụ khác tràn vào và phải hợp tác với các phiên bản Spider-Man khác.'),
(28, 'Doctor Strange in the Multiverse of Madness', 1, 2022, 'https://image.tmdb.org/t/p/w500/9Gtg2DzBhmYamXBS1hKAhiwbBKS.jpg', 'https://www.youtube.com/watch?v=aWzlQ2N6qqg', 0, 'Doctor Strange du hành qua các chiều không gian để bảo vệ America Chavez và vũ trụ khỏi sự hỗn loạn do Wanda gây ra.'),
(29, 'Thor: Love and Thunder', 1, 2022, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/pIkRyD18kl4FhoCNQuWxWu5cBLM.jpg', 'https://www.youtube.com/watch?v=Go8nTmfrQd8', 0, 'Thor hợp tác với Jane Foster – nay là Mighty Thor – để ngăn chặn Gorr the God Butcher tiêu diệt các vị thần.'),
(30, 'Black Panther: Wakanda Forever', 1, 2022, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/sv1xJUazXeYqALzczSZ3O6nkH75.jpg', 'https://www.youtube.com/watch?v=_Z3QKkl1WyM', 0, 'Sau cái chết của T\'Challa, Wakanda phải bảo vệ vương quốc trước mối đe dọa từ Talokan và thủ lĩnh Namor.'),
(31, 'Ant-Man and the Wasp: Quantumania', 1, 2023, 'https://image.tmdb.org/t/p/w500/gnf4Cb2rms69QbCnGFJyqwBWsxv.jpg', 'https://www.youtube.com/watch?v=ZlNFpri-Y40', 0, 'Scott Lang và Hope van Dyne cùng gia đình bị cuốn vào Thế giới Lượng tử, nơi họ đối mặt với Kang the Conqueror.'),
(32, 'Guardians of the Galaxy Vol. 3', 1, 2023, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/r2J02Z2OpNTctfOSN1Ydgii51I3.jpg', 'https://www.youtube.com/watch?v=u3V5KDHRQvk', 1, 'Nhóm Vệ binh thiên hà thực hiện nhiệm vụ cuối cùng để cứu Rocket và đối đầu với The High Evolutionary.'),
(33, 'The Marvels', 1, 2023, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/9GBhzXMFjgcZ3FdR9w3bUMMTps5.jpg', 'https://www.youtube.com/watch?v=wS_qbDztgVY', 0, 'Carol Danvers, Monica Rambeau và Kamala Khan phải hợp tác khi sức mạnh của họ liên kết một cách kỳ lạ.'),
(34, 'Man of Steel', 2, 2013, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/dksTL9NXc3GqPBRHYHcy1aIwjS.jpg', 'https://www.youtube.com/watch?v=T6DJcgm3wNY', 0, 'Clark Kent khám phá nguồn gốc ngoài hành tinh và trở thành Superman để bảo vệ Trái Đất khỏi tướng Zod.'),
(35, 'Batman v Superman: Dawn of Justice', 2, 2016, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/5UsK3grJvtQrtzEgqNlDljJW96w.jpg', 'https://www.youtube.com/watch?v=0WWzgGyAH6Y', 0, 'Batman nghi ngờ Superman là mối đe dọa cho nhân loại và đối đầu với anh, trong khi Lex Luthor âm mưu tạo ra Doomsday.'),
(36, 'Suicide Squad', 2, 2016, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/sk3FZgh3sRrmr8vyhaitNobMcfh.jpg', 'https://www.youtube.com/watch?v=CmRih_VtVAs', 0, 'Chính phủ tuyển mộ nhóm ác nhân nguy hiểm để thực hiện nhiệm vụ cảm tử nhằm giảm án tù và cứu thế giới.'),
(37, 'Wonder Woman', 2, 2017, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/v4ncgZjG2Zu8ZW5al1vIZTsSjqX.jpg', 'https://www.youtube.com/watch?v=1Q8fG0TtVAY', 0, 'Diana, công chúa Amazon, rời hòn đảo Themyscira để giúp chấm dứt Thế chiến và khám phá vận mệnh thật sự của mình.'),
(38, 'Justice League', 2, 2017, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/3184unxdIHJIpXmCrkIAETUlM9R.jpg', 'https://www.youtube.com/watch?v=3cxixDgHUYw', 0, 'Batman và Wonder Woman tập hợp nhóm siêu anh hùng để chống lại mối đe dọa từ Steppenwolf và đoàn quân Parademon.'),
(39, 'Aquaman', 2, 2018, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/oIDpaHSnTMYK0Cf5RkEoQzXPpBE.jpg', 'https://www.youtube.com/watch?v=WDkg3h8PCVU', 0, 'Arthur Curry lên đường giành lại ngôi vua Atlantis và ngăn em trai Orm phát động chiến tranh với thế giới con người.'),
(40, 'Shazam!', 2, 2019, 'https://images7.alphacoders.com/104/thumbbig-1045842.webp', 'https://www.youtube.com/watch?v=go6GEIrcvFY&t', 0, 'Cậu bé Billy Batson nhận được sức mạnh biến thành người hùng trưởng thành Shazam để đối đầu với ác nhân Dr. Sivana.'),
(41, 'Birds of Prey', 2, 2020, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/gXeFSST0xqy5XNgrbuEaa5BxvDM.jpg', 'https://www.youtube.com/watch?v=kGM4uYZzfu0', 0, 'Sau khi chia tay Joker, Harley Quinn hợp lực với Black Canary, Huntress và Renee Montoya để bảo vệ cô bé Cassandra khỏi tay Black Mask.'),
(42, 'Wonder Woman 1984', 2, 2020, 'https://image.tmdb.org/t/p/w500/8UlWHLMpgZm9bx6QYh0NFoq67TZ.jpg', 'https://www.youtube.com/watch?v=XW2E2Fnh52w', 0, 'Diana Prince tái ngộ người yêu cũ trong khi đối đầu với hai kẻ thù mới: Cheetah và Max Lord trong bối cảnh thập niên 1980.'),
(43, 'Zack Snyder\'s Justice League', 2, 2021, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/tnAuB8q5vv7Ax9UAEje5Xi4BXik.jpg', 'https://www.youtube.com/watch?v=ui37YKQ9AC4', 0, 'Phiên bản đầy đủ của Justice League với tầm nhìn của Zack Snyder, hé lộ nhiều chi tiết mới về Darkseid và hành trình của các siêu anh hùng.'),
(44, 'The Suicide Squad', 2, 2021, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/q61qEyssk2ku3okWICKArlAdhBn.jpg', 'https://www.youtube.com/watch?v=Z1EbSXxrZ34', 0, 'Một nhóm ác nhân mới được cử đi tiêu diệt mối đe dọa ngoài hành tinh trên đảo Corto Maltese, dẫn đầu bởi Bloodsport và Harley Quinn.'),
(45, 'Black Adam', 2, 2022, 'https://image.tmdb.org/t/p/w500/pFlaoHTZeyNkG83vxsAJiGzfSsa.jpg', 'https://www.youtube.com/watch?v=X0tOpBuYasI', 0, 'Sau 5.000 năm bị giam cầm, Black Adam thức tỉnh với quyền năng hủy diệt và phải quyết định số phận của nhân loại.'),
(46, 'Shazam! Fury of the Gods', 2, 2023, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/A3ZbZsmsvNGdprRi2lKgGEeVLEH.jpg', 'https://www.youtube.com/watch?v=Zi88i4CpHe4', 0, 'Billy Batson và gia đình siêu anh hùng của cậu đối mặt với ba nữ thần Hy Lạp muốn đoạt lại quyền năng của các vị thần.'),
(47, 'The Flash', 2, 2023, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/yZevl2vHQgmosfwUdVNzviIfaWS.jpg', 'https://www.youtube.com/watch?v=hebWYacbdvc', 1, 'Barry Allen du hành thời gian để cứu mẹ, vô tình tạo ra đa vũ trụ và chạm trán nhiều phiên bản của Batman.'),
(48, 'Aquaman and the Lost Kingdom', 2, 2023, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/oEyIhY1WzoFHUDE7U3p1AWwyoSN.jpg', 'https://www.youtube.com/watch?v=UGc5Tzz19UY', 0, 'Aquaman hợp tác với kẻ thù cũ Orm để ngăn chặn mối đe dọa cổ xưa từ Vương quốc bị lãng quên đe dọa cả Atlantis và thế giới con người.'),
(49, 'The Dark Knight', 3, 2008, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/qJ2tW6WMUDux911r6m7haRef0WH.jpg', 'https://www.youtube.com/watch?v=EXeTwQWrcwY', 0, 'Batman đối đầu với Joker – tên tội phạm hỗn loạn gây náo loạn Gotham và thử thách giới hạn đạo đức của Kỵ sĩ Bóng đêm.'),
(50, 'Joker', 3, 2019, 'https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg', 'https://www.youtube.com/watch?v=t433PEQGErc', 1, 'Câu chuyện nguồn gốc của Arthur Fleck, một nghệ sĩ hài thất bại rơi vào điên loạn và trở thành biểu tượng hỗn loạn của Gotham.'),
(51, 'The Batman', 3, 2022, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/3w7koeOR2x71XYMJDGpygxYtScI.jpg', 'https://www.youtube.com/watch?v=mqqft2x_Aa4', 0, 'Batman điều tra chuỗi vụ án giết người do Riddler gây ra và khám phá ra sự mục nát của giới quyền lực Gotham.'),
(52, 'Deadpool', 3, 2016, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/3E53WEZJqP6aM84D8CckXx4pIHw.jpg', 'https://www.youtube.com/watch?v=Xithigfg7dA', 0, 'Wade Wilson trở thành Deadpool sau khi trải qua thí nghiệm đột biến và săn lùng kẻ đã phá hủy cuộc đời mình.'),
(53, 'Deadpool 2', 3, 2018, 'https://image.tmdb.org/t/p/w500/to0spRl1CMDvyUbOnbb4fTk3VAd.jpg', 'https://www.youtube.com/watch?v=D86RtevtfrA', 0, 'Deadpool thành lập nhóm X-Force để bảo vệ một cậu bé đột biến khỏi Cable – một chiến binh đến từ tương lai.'),
(54, 'Logan', 3, 2017, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/fnbjcRDYn6YviCcePDnGdyAkYsB.jpg', 'https://www.youtube.com/watch?v=Div0iP65aZo', 0, 'Trong tương lai u ám, Logan chăm sóc giáo sư X đang hấp hối và bảo vệ cô bé đột biến Laura khỏi thế lực truy đuổi.'),
(55, 'The New Mutants', 3, 2020, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/xiDGcXJTvu1lazFRYip6g1eLt9c.jpg', 'https://www.youtube.com/watch?v=W_vJhUAOFpI&t=6s', 0, 'Năm thanh thiếu niên đột biến bị giam giữ trong một cơ sở bí ẩn và phải đoàn kết để thoát khỏi những ảo ảnh chết người.'),
(56, 'Spider-Man: Into the Spider-Verse', 3, 2018, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/iiZZdoQBEYBv6id8su7ImL0oCbD.jpg', 'https://www.youtube.com/watch?v=ApXoWvfEYVU', 0, 'Miles Morales trở thành Spider-Man và cùng các phiên bản khác từ đa vũ trụ hợp lực ngăn chặn âm mưu của Kingpin.'),
(57, 'Big Hero 6', 3, 2014, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/2mxS4wUimwlLmI1xp6QW6NSU361.jpg', 'https://www.youtube.com/watch?v=Y4o_8zbelwY', 0, 'Thiên tài nhí Hiro Hamada hợp tác cùng robot Baymax và bạn bè để thành lập nhóm siêu anh hùng cứu thành phố khỏi âm mưu bí ẩn.'),
(58, 'Batman: Mask of the Phantasm', 3, 1993, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/hT4ehUteagUrhUOHAtmYiY7mp5l.jpg', 'https://www.youtube.com/watch?v=imtYrQEZ4H8', 0, 'Batman bị truy đuổi bởi cảnh sát trong khi điều tra kẻ giết người bí ẩn Phantasm – liên quan đến quá khứ tình yêu của anh.'),
(61, 'Inception (NEW)', 3, 2010, 'https://media.themoviedb.org/t/p/w188_and_h282_bestv2/ljsZTbVsrQSqZgWeep2B1QiDKuh.jpg', 'https://www.youtube.com/watch?v=YoHD9XEInc0&t=5s', 0, 'Cobb và nhóm của mình thực hiện nhiệm vụ phức tạp này bằng cách thâm nhập vào nhiều tầng giấc mơ, mỗi tầng giấc mơ lại có độ phức tạp và sự nguy hiểm tăng dần. Trong hành trình này, Cobb phải đối mặt với những yếu tố tâm lý sâu sắc, đặc biệt là bóng ma từ quá khứ của anh, liên quan đến vợ mình, Mal.');

-- --------------------------------------------------------

--
-- Table structure for table `movie_actors`
--

CREATE TABLE `movie_actors` (
  `movie_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie_actors`
--

-- --------------------------------------------------------

--
-- Table structure for table `movie_genres`
--

CREATE TABLE `movie_genres` (
  `movie_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie_genres`
--

INSERT INTO `movie_genres` (`movie_id`, `genre_id`) VALUES
(1, 1),
(1, 3),
(2, 1),
(2, 3),
(3, 1),
(3, 3),
(4, 1),
(4, 2),
(4, 5),
(5, 1),
(6, 1),
(6, 3),
(7, 1),
(7, 3),
(7, 6),
(8, 1),
(8, 2),
(8, 5),
(9, 1),
(9, 6),
(10, 1),
(10, 2),
(10, 3),
(10, 8),
(11, 1),
(11, 3),
(12, 1),
(12, 3),
(12, 8),
(13, 1),
(13, 6),
(14, 1),
(14, 5),
(15, 1),
(15, 2),
(15, 3),
(15, 8),
(16, 1),
(16, 8),
(17, 1),
(17, 5),
(17, 8),
(18, 1),
(18, 2),
(18, 3),
(19, 1),
(19, 3),
(20, 1),
(20, 3),
(20, 8),
(21, 1),
(21, 2),
(21, 3),
(22, 1),
(22, 3),
(23, 1),
(23, 2),
(23, 8),
(24, 1),
(24, 2),
(25, 1),
(25, 5),
(26, 1),
(26, 3),
(26, 5),
(27, 1),
(27, 3),
(28, 1),
(28, 3),
(28, 4),
(29, 1),
(29, 5),
(29, 8),
(30, 1),
(30, 2),
(30, 6),
(31, 1),
(31, 2),
(31, 3),
(32, 1),
(32, 3),
(32, 8),
(33, 1),
(33, 3),
(34, 1),
(34, 3),
(35, 1),
(35, 6),
(36, 1),
(36, 7),
(37, 1),
(37, 5),
(38, 1),
(38, 3),
(39, 1),
(39, 2),
(39, 5),
(40, 5),
(40, 8),
(41, 1),
(41, 7),
(41, 8),
(42, 1),
(42, 5),
(42, 6),
(43, 1),
(43, 3),
(44, 1),
(44, 3),
(44, 8),
(45, 1),
(45, 5),
(46, 5),
(46, 8),
(47, 1),
(47, 3),
(48, 1),
(48, 2),
(48, 5),
(49, 1),
(49, 6),
(49, 7),
(50, 6),
(50, 7),
(51, 1),
(51, 7),
(52, 1),
(52, 8),
(53, 1),
(53, 8),
(54, 1),
(54, 3),
(54, 6),
(55, 4),
(55, 5),
(56, 1),
(56, 3),
(56, 9),
(57, 3),
(57, 8),
(57, 9),
(58, 1),
(58, 7),
(58, 9),
(61, 1),
(61, 3),
(61, 6),
(61, 7);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `movie_id`, `rating`, `created_at`) VALUES
(0, 12, 32, 5, '2025-04-23 09:04:25'),
(0, 13, 31, 5, '2025-04-23 09:25:53'),
(0, 13, 33, 4, '2025-04-23 09:49:14'),
(0, 13, 32, 5, '2025-04-30 15:40:19'),
(0, 12, 46, 5, '2025-05-01 03:38:27');

-- --------------------------------------------------------

--
-- Table structure for table `universes`
--

CREATE TABLE `universes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `universes`
--

INSERT INTO `universes` (`id`, `name`) VALUES
(1, 'MCU'),
(2, 'DCEU'),
(3, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT 'default.jpg',
  `role` enum('user','admin','moderator') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`, `updated_at`, `profile_image`, `role`) VALUES
(1, 'xobin1', 'nguyenbin149@gmail.com', '$2y$10$g.NxQpCOK4bpgoLNfKCC.eJefwnL6vUpRnxN8V7Eomxnv4AjobdRa', '2025-04-21 12:24:12', '2025-04-21 12:24:12', 'default.jpg', 'user'),
(2, 'nguyenvan', 'user1@gmail.com', '$2y$10$iyXGLtv.cOvklXLwmQzfge7lvMOoqeixSO6FuzseQcLyM7LaFVeD2', '2025-04-21 12:27:30', '2025-04-30 16:15:03', 'profile_12_1746029703.jpg', 'user'),
(3, 'xobin2', 'user2@gmail.com', '$2y$10$yBVRLj1WU24w8SSpYpqRI.ZLLwqX1SC4kabl2qxSiS/CEqgDHH29u', '2025-04-21 12:44:49', '2025-04-22 08:49:06', 'profile_13_1745311746.jpg', 'user'),
(4, 'xobin3', 'user3@gmail.com', '$2y$10$W9HMKA1MsDxVKWR0yVx52OK6E1J9Nfgfgl/jB/sg4owxsOI3BWSUy', '2025-04-22 05:35:11', '2025-04-22 05:35:11', 'default.jpg', 'user'),
(5, 'levu', 'levu@gmail.com', '$2y$10$5UvYp2R.ZMCVKGSadkRBLuIkWr0HCLgxlP1vsI3UXayq.AYhwoW0S', '2025-04-30 16:16:38', '2025-04-30 16:16:38', 'default.jpg', 'user'),
(6, 'levu1', 'levu1@gmail.com', '$2y$10$qI/H8MWecGDpUC8ZVWYOE.ktgGqNJv0cGpvwjOYzdsDkO.DcG.Baq', '2025-04-30 16:19:28', '2025-04-30 16:19:28', 'default.jpg', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actors`
--
ALTER TABLE `actors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `universe_id` (`universe_id`);

--
-- Indexes for table `movie_actors`
--
ALTER TABLE `movie_actors`
  ADD PRIMARY KEY (`movie_id`,`actor_id`),
  ADD KEY `actor_id` (`actor_id`);

--
-- Indexes for table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD PRIMARY KEY (`movie_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `universes`
--
ALTER TABLE `universes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actors`
--
ALTER TABLE `actors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`universe_id`) REFERENCES `universes` (`id`);

--
-- Constraints for table `movie_actors`
--
ALTER TABLE `movie_actors`
  ADD CONSTRAINT `movie_actors_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`),
  ADD CONSTRAINT `movie_actors_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `actors` (`id`);

--
-- Constraints for table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD CONSTRAINT `movie_genres_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`),
  ADD CONSTRAINT `movie_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`);

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;


INSERT INTO `movie_actors` (`movie_id`, `actor_id`, `role`) VALUES
(1, 1, 'Tony Stark / Iron Man'),
(1, 2, 'Pepper Potts'),
(1, 11, 'James Rhodes'),
(2, 3, 'Bruce Banner / Hulk'),
(2, 4, 'Betty Ross'),
(2, 26, 'Emil Blonsky'),
(3, 1, 'Tony Stark / Iron Man'),
(3, 5, 'Ivan Vanko / Whiplash'),
(3, 11, 'Natasha Romanoff / Black Widow'),
(4, 6, 'Thor'),
(4, 7, 'Jane Foster'),
(4, 8, 'Loki'),
(5, 9, 'Steve Rogers / Captain America'),
(5, 10, 'Johann Schmidt / Red Skull'),
(5, 11, 'Peggy Carter'),
(6, 1, 'Tony Stark / Iron Man'),
(6, 6, 'Thor'),
(6, 9, 'Steve Rogers / Captain America'),
(7, 1, 'Tony Stark / Iron Man'),
(7, 2, 'Pepper Potts'),
(7, 12, 'Aldrich Killian'),
(8, 6, 'Thor'),
(8, 7, 'Jane Foster'),
(8, 13, 'Odin'),
(9, 9, 'Steve Rogers / Captain America'),
(9, 11, 'Natasha Romanoff / Black Widow'),
(9, 14, 'Bucky Barnes / Winter Soldier'),
(10, 15, 'Peter Quill / Star-Lord'),
(10, 16, 'Gamora'),
(10, 23, 'Yondu'),
(11, 1, 'Tony Stark / Iron Man'),
(11, 9, 'Steve Rogers / Captain America'),
(11, 17, 'Ultron'),
(12, 18, 'Scott Lang / Ant-Man'),
(12, 19, 'Hank Pym'),
(12, 30, 'Hope van Dyne'),
(13, 1, 'Tony Stark / Iron Man'),
(13, 9, 'Steve Rogers / Captain America'),
(13, 24, 'Peter Parker / Spider-Man'),
(14, 21, 'Stephen Strange / Doctor Strange'),
(14, 22, 'The Ancient One'),
(14, 40, 'Christine Palmer'),
(15, 15, 'Peter Quill / Star-Lord'),
(15, 16, 'Gamora'),
(15, 23, 'Yondu'),
(16, 24, 'Peter Parker / Spider-Man'),
(16, 25, 'Adrian Toomes / Vulture'),
(16, 39, 'MJ'),
(17, 6, 'Thor'),
(17, 8, 'Loki'),
(17, 26, 'Bruce Banner / Hulk'),
(18, 27, 'T\'Challa / Black Panther'),
(18, 28, 'Erik Killmonger'),
(18, 43, 'Shuri'),
(19, 1, 'Tony Stark / Iron Man'),
(19, 9, 'Steve Rogers / Captain America'),
(19, 29, 'Thanos'),
(20, 18, 'Scott Lang / Ant-Man'),
(20, 19, 'Hank Pym'),
(20, 30, 'Hope van Dyne / Wasp'),
(21, 16, 'Talos'),
(21, 31, 'Carol Danvers / Captain Marvel'),
(21, 32, 'Nick Fury'),
(22, 1, 'Tony Stark / Iron Man'),
(22, 9, 'Steve Rogers / Captain America'),
(22, 29, 'Thanos'),
(23, 24, 'Peter Parker / Spider-Man'),
(23, 33, 'Quentin Beck / Mysterio'),
(23, 39, 'MJ'),
(24, 11, 'Natasha Romanoff / Black Widow'),
(24, 32, 'Alexei Shostakov'),
(24, 34, 'Yelena Belova'),
(25, 16, 'Katy'),
(25, 35, 'Shang-Chi'),
(25, 36, 'Wenwu'),
(26, 16, 'Kingo'),
(26, 37, 'Sersi'),
(26, 38, 'Ikaris'),
(27, 21, 'Doctor Strange'),
(27, 24, 'Peter Parker / Spider-Man'),
(27, 39, 'MJ'),
(28, 20, 'Wanda Maximoff / Scarlet Witch'),
(28, 21, 'Stephen Strange / Doctor Strange'),
(28, 40, 'Christine Palmer'),
(29, 6, 'Thor'),
(29, 41, 'Gorr the God Butcher'),
(29, 42, 'Jane Foster / Mighty Thor'),
(30, 28, 'Okoye'),
(30, 43, 'Shuri / Black Panther'),
(30, 44, 'Namor'),
(31, 18, 'Scott Lang / Ant-Man'),
(31, 30, 'Hope van Dyne / Wasp'),
(31, 45, 'Kang the Conqueror'),
(32, 15, 'Peter Quill / Star-Lord'),
(32, 16, 'Gamora'),
(32, 26, 'Rocket Raccoon'),
(33, 16, 'Kamala Khan'),
(33, 31, 'Carol Danvers / Captain Marvel'),
(33, 32, 'Monica Rambeau'),
(34, 16, 'General Zod'),
(34, 46, 'Clark Kent / Superman'),
(34, 47, 'Lois Lane'),
(35, 46, 'Clark Kent / Superman'),
(35, 48, 'Bruce Wayne / Batman'),
(35, 49, 'Lex Luthor'),
(36, 16, 'Joker'),
(36, 50, 'Deadshot'),
(36, 51, 'Harley Quinn'),
(37, 16, 'Ares'),
(37, 52, 'Diana Prince / Wonder Woman'),
(37, 53, 'Steve Trevor'),
(38, 46, 'Clark Kent / Superman'),
(38, 48, 'Bruce Wayne / Batman'),
(38, 52, 'Diana Prince / Wonder Woman'),
(39, 16, 'Mera'),
(39, 54, 'Arthur Curry / Aquaman'),
(39, 55, 'Orm'),
(40, 16, 'Freddy Freeman'),
(40, 56, 'Billy Batson / Shazam'),
(40, 57, 'Dr. Sivana'),
(41, 16, 'Roman Sionis / Black Mask'),
(41, 51, 'Harley Quinn'),
(41, 58, 'Black Canary'),
(42, 52, 'Diana Prince / Wonder Woman'),
(42, 53, 'Steve Trevor'),
(42, 59, 'Barbara Minerva / Cheetah'),
(43, 46, 'Clark Kent / Superman'),
(43, 48, 'Bruce Wayne / Batman'),
(43, 52, 'Diana Prince / Wonder Woman'),
(44, 51, 'Harley Quinn'),
(44, 60, 'Bloodsport'),
(44, 61, 'Peacemaker'),
(45, 16, 'Hawkman'),
(45, 62, 'Teth-Adam / Black Adam'),
(45, 63, 'Doctor Fate'),
(46, 16, 'Freddy Freeman'),
(46, 56, 'Billy Batson / Shazam'),
(46, 64, 'Hespera'),
(47, 46, 'Superman'),
(47, 48, 'Bruce Wayne / Batman'),
(47, 65, 'Barry Allen / The Flash'),
(48, 16, 'Black Manta'),
(48, 54, 'Arthur Curry / Aquaman'),
(48, 55, 'Orm'),
(49, 16, 'Harvey Dent'),
(49, 41, 'Bruce Wayne / Batman'),
(49, 66, 'Joker'),
(50, 16, 'Sophie Dumond'),
(50, 67, 'Arthur Fleck / Joker'),
(51, 16, 'Edward Nashton / Riddler'),
(51, 68, 'Bruce Wayne / Batman'),
(51, 69, 'Selina Kyle / Catwoman'),
(52, 16, 'Ajax'),
(52, 70, 'Wade Wilson / Deadpool'),
(52, 71, 'Vanessa'),
(53, 70, 'Wade Wilson / Deadpool'),
(53, 71, 'Vanessa'),
(53, 72, 'Cable'),
(54, 73, 'Logan / Wolverine'),
(54, 74, 'Charles Xavier'),
(54, 75, 'Laura / X-23'),
(55, 16, 'Dani Moonstar'),
(55, 76, 'Illyana Rasputin / Magik'),
(55, 77, 'Rahne Sinclair / Wolfsbane'),
(56, 16, 'Wilson Fisk / Kingpin'),
(56, 78, 'Miles Morales / Spider-Man'),
(56, 79, 'Peter B. Parker / Spider-Man'),
(57, 16, 'Robert Callaghan'),
(57, 80, 'Hiro Hamada'),
(57, 81, 'Baymax'),
(58, 16, 'The Phantasm'),
(58, 82, 'Bruce Wayne / Batman'),
(58, 83, 'Andrea Beaumont');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

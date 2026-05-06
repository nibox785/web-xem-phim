-- =============================================================================
-- Migration SQL (import-ready): Schema đầy đủ cho web phim tổng hợp

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

CREATE DATABASE IF NOT EXISTS `prj_movie` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `prj_movie`;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- DROP tables (reverse dependency order)
-- ============================================================================
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `reports`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `user_preferences`;
DROP TABLE IF EXISTS `watch_history`;
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `ratings`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `subtitles`;
DROP TABLE IF EXISTS `video_sources`;
DROP TABLE IF EXISTS `episodes`;
DROP TABLE IF EXISTS `seasons`;
DROP TABLE IF EXISTS `movie_credits`;
DROP TABLE IF EXISTS `people`;
DROP TABLE IF EXISTS `movie_tags`;
DROP TABLE IF EXISTS `movie_genres`;
DROP TABLE IF EXISTS `movies`;
DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `genres`;
DROP TABLE IF EXISTS `studios`;
DROP TABLE IF EXISTS `languages`;
DROP TABLE IF EXISTS `countries`;
DROP TABLE IF EXISTS `user_tokens`;
DROP TABLE IF EXISTS `user_roles`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

-- ============================================================================
-- 1. Identity & Access
-- ============================================================================

CREATE TABLE `roles` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(50)  NOT NULL UNIQUE COMMENT 'e.g. admin, moderator, vip, member',
  `description` VARCHAR(255),
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_roles_name (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(50)  NOT NULL UNIQUE,
  `email`         VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `display_name`  VARCHAR(100),
  `profile_image` VARCHAR(512) DEFAULT 'default.jpg',
  `bio`           TEXT,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `is_verified`   TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Email verified',
  `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_username  (`username`),
  INDEX idx_users_email     (`email`),
  INDEX idx_users_is_active (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_roles` (
  `user_id` INT NOT NULL,
  `role_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_tokens` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT          NOT NULL,
  `token_hash`  CHAR(64)     NOT NULL,
  `type`        ENUM('refresh','reset_password','verify_email') NOT NULL DEFAULT 'refresh',
  `device_info` VARCHAR(255),
  `ip_address`  VARCHAR(45),
  `expires_at`  DATETIME     NOT NULL,
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_user_tokens_token_hash` (`token_hash`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX idx_user_tokens_user_id    (`user_id`),
  INDEX idx_user_tokens_expires_at (`expires_at`),
  INDEX idx_user_tokens_type       (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. Catalog (lookup tables)
-- ============================================================================

CREATE TABLE `countries` (
  `id`       INT AUTO_INCREMENT PRIMARY KEY,
  `iso_code` CHAR(2)      UNIQUE COMMENT 'ISO 3166-1 alpha-2',
  `name`     VARCHAR(100) NOT NULL UNIQUE,
  INDEX idx_countries_iso_code (`iso_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `languages` (
  `id`   INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(10)  UNIQUE COMMENT 'BCP 47, e.g. vi, en, ja',
  `name` VARCHAR(100) NOT NULL UNIQUE,
  INDEX idx_languages_code (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `studios` (
  `id`      INT AUTO_INCREMENT PRIMARY KEY,
  `name`    VARCHAR(200) NOT NULL UNIQUE,
  `country_id` INT NULL,
  `website` VARCHAR(255),
  `logo_url` VARCHAR(512),
  FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE SET NULL,
  INDEX idx_studios_name (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `genres` (
  `id`   INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  INDEX idx_genres_slug (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tags` (
  `id`   INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  INDEX idx_tags_slug (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. People (diễn viên, đạo diễn, biên kịch, v.v.)
-- ============================================================================

CREATE TABLE `people` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `name`          VARCHAR(200) NOT NULL,
  `original_name` VARCHAR(200) COMMENT 'Tên gốc (nếu là người nước ngoài)',
  `birth_date`    DATE         NULL,
  `nationality_id` INT         NULL COMMENT 'FK → countries',
  `profile_image` VARCHAR(512),
  `bio`           TEXT,
  `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`nationality_id`) REFERENCES `countries`(`id`) ON DELETE SET NULL,
  INDEX idx_people_name (`name`),
  FULLTEXT INDEX ft_people_name (`name`, `original_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. Movies
-- ============================================================================

CREATE TABLE `movies` (
  `id`               INT AUTO_INCREMENT PRIMARY KEY,
  `title`            VARCHAR(255) NOT NULL,
  `original_title`   VARCHAR(255) COMMENT 'Tên gốc',
  `slug`             VARCHAR(255) NOT NULL UNIQUE,
  `type`             ENUM('movie','series','anime','documentary','show') NOT NULL DEFAULT 'movie',
  `status`           ENUM('released','upcoming','ongoing','ended') NOT NULL DEFAULT 'released',
  `release_year`     SMALLINT,
  `duration_minutes` INT          COMMENT 'Dùng cho movie đơn lẻ',
  `total_episodes`   INT          COMMENT 'Dùng cho series/anime',
  `age_rating`       ENUM('G','PG','PG-13','R','NC-17','NR') DEFAULT 'NR',
  `imdb_id`          VARCHAR(20)  COMMENT 'tt1234567',
  `imdb_score`       DECIMAL(3,1),
  `description`      TEXT,
  `poster_url`       VARCHAR(512),
  `banner_url`       VARCHAR(512),
  `trailer_url`      VARCHAR(512),
  `studio_id`        INT          NULL,
  `country_id`       INT          NULL,
  `language_id`      INT          NULL,
  `featured`         TINYINT(1)   NOT NULL DEFAULT 0,
  `view_count`       BIGINT       NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`studio_id`)   REFERENCES `studios`(`id`)   ON DELETE SET NULL,
  FOREIGN KEY (`country_id`)  REFERENCES `countries`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`language_id`) REFERENCES `languages`(`id`) ON DELETE SET NULL,
  INDEX idx_movies_slug         (`slug`),
  INDEX idx_movies_type         (`type`),
  INDEX idx_movies_status       (`status`),
  INDEX idx_movies_release_year (`release_year`),
  INDEX idx_movies_featured     (`featured`),
  INDEX idx_movies_view_count   (`view_count`),
  INDEX idx_movies_created_at   (`created_at`),
  FULLTEXT INDEX ft_movies_search (`title`, `original_title`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `movie_genres` (
  `movie_id` INT NOT NULL,
  `genre_id` INT NOT NULL,
  PRIMARY KEY (`movie_id`, `genre_id`),
  FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`genre_id`) REFERENCES `genres`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `movie_tags` (
  `movie_id` INT NOT NULL,
  `tag_id`   INT NOT NULL,
  PRIMARY KEY (`movie_id`, `tag_id`),
  FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`)   REFERENCES `tags`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. Credits (thay thế movie_casts — gộp diễn viên, đạo diễn, biên kịch...)
-- ============================================================================

CREATE TABLE `movie_credits` (
  `movie_id`       INT          NOT NULL,
  `person_id`      INT          NOT NULL,
  `role`           ENUM('director','writer','producer','actor','composer','editor','cinematographer') NOT NULL,
  `character_name` VARCHAR(255) COMMENT 'Chỉ điền khi role = actor',
  `credit_order`   INT          NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  PRIMARY KEY (`movie_id`, `person_id`, `role`),
  FOREIGN KEY (`movie_id`)  REFERENCES `movies`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`person_id`) REFERENCES `people`(`id`) ON DELETE CASCADE,
  INDEX idx_movie_credits_role         (`role`),
  INDEX idx_movie_credits_credit_order (`credit_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. Series → Seasons → Episodes
-- ============================================================================

CREATE TABLE `seasons` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `movie_id`      INT          NOT NULL,
  `season_number` INT          NOT NULL,
  `title`         VARCHAR(255),
  `description`   TEXT,
  `poster_url`    VARCHAR(512),
  `release_year`  SMALLINT,
  `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uq_season_per_movie` (`movie_id`, `season_number`),
  INDEX idx_seasons_movie_id      (`movie_id`),
  INDEX idx_seasons_season_number (`season_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `episodes` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `season_id`      INT           NOT NULL,
  `episode_number` INT           NOT NULL,
  `title`          VARCHAR(255)  NOT NULL,
  `description`    TEXT,
  `runtime`        INT           COMMENT 'Thời lượng tính bằng giây',
  `thumbnail_url`  VARCHAR(512),
  `air_date`       DATE,
  `created_at`     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`season_id`) REFERENCES `seasons`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uq_episode_per_season` (`season_id`, `episode_number`),
  INDEX idx_episodes_season_id      (`season_id`),
  INDEX idx_episodes_episode_number (`episode_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. Video Sources (multi-quality, multi-provider)
-- ============================================================================

CREATE TABLE `video_sources` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `movie_id`    INT          NULL COMMENT 'NULL nếu là episode',
  `episode_id`  INT          NULL COMMENT 'NULL nếu là movie đơn',
  `quality`     ENUM('360p','480p','720p','1080p','4K') NOT NULL DEFAULT '720p',
  `source_url`  VARCHAR(1024) NOT NULL,
  `provider`    VARCHAR(100)  COMMENT 'gdrive, streamtape, okru, ...',
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`movie_id`)   REFERENCES `movies`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`episode_id`) REFERENCES `episodes`(`id`) ON DELETE CASCADE,
  INDEX idx_video_sources_movie_id   (`movie_id`),
  INDEX idx_video_sources_episode_id (`episode_id`),
  INDEX idx_video_sources_quality    (`quality`),
  INDEX idx_video_sources_is_active  (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. Subtitles
-- ============================================================================

CREATE TABLE `subtitles` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `movie_id`     INT         NULL,
  `episode_id`   INT         NULL,
  `language_id`  INT         NOT NULL,
  `subtitle_url` VARCHAR(512) NOT NULL,
  `format`       ENUM('srt','vtt','ass') NOT NULL DEFAULT 'vtt',
  `is_default`   TINYINT(1)  NOT NULL DEFAULT 0,
  `created_at`   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`movie_id`)    REFERENCES `movies`(`id`)    ON DELETE CASCADE,
  FOREIGN KEY (`episode_id`)  REFERENCES `episodes`(`id`)  ON DELETE CASCADE,
  FOREIGN KEY (`language_id`) REFERENCES `languages`(`id`) ON DELETE CASCADE,
  INDEX idx_subtitles_movie_id   (`movie_id`),
  INDEX idx_subtitles_episode_id (`episode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. Engagement
-- ============================================================================

CREATE TABLE `comments` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT          NULL,
  `movie_id`     INT          NULL,
  `episode_id`   INT          NULL,
  `parent_id`    INT          NULL COMMENT 'NULL = top-level, != NULL = reply',
  `comment_text` TEXT         NOT NULL,
  `is_approved`  TINYINT(1)   NOT NULL DEFAULT 0,
  `is_pinned`    TINYINT(1)   NOT NULL DEFAULT 0,
  `like_count`   INT          NOT NULL DEFAULT 0,
  `created_at`   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)    ON DELETE SET NULL,
  FOREIGN KEY (`movie_id`)   REFERENCES `movies`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`episode_id`) REFERENCES `episodes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_id`)  REFERENCES `comments`(`id`) ON DELETE CASCADE,
  INDEX idx_comments_movie_id    (`movie_id`),
  INDEX idx_comments_episode_id  (`episode_id`),
  INDEX idx_comments_user_id     (`user_id`),
  INDEX idx_comments_parent_id   (`parent_id`),
  INDEX idx_comments_is_approved (`is_approved`),
  INDEX idx_comments_created_at  (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ratings` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT      NOT NULL,
  `movie_id`   INT      NOT NULL,
  `score`      TINYINT  NOT NULL CHECK (`score` BETWEEN 1 AND 10),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_user_movie_rating` (`user_id`, `movie_id`),
  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE CASCADE,
  FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`) ON DELETE CASCADE,
  INDEX idx_ratings_movie_id (`movie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `favorites` (
  `user_id`  INT       NOT NULL,
  `movie_id` INT       NOT NULL,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `movie_id`),
  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE CASCADE,
  FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`) ON DELETE CASCADE,
  INDEX idx_favorites_movie_id (`movie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `watch_history` (
  `id`               INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`          INT          NOT NULL,
  `movie_id`         INT          NULL,
  `episode_id`       INT          NULL,
  `position_seconds` INT          NOT NULL DEFAULT 0 COMMENT 'Vị trí dừng để resume',
  `completed`        TINYINT(1)   NOT NULL DEFAULT 0,
  `watched_at`       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)    ON DELETE CASCADE,
  FOREIGN KEY (`movie_id`)   REFERENCES `movies`(`id`)   ON DELETE SET NULL,
  FOREIGN KEY (`episode_id`) REFERENCES `episodes`(`id`) ON DELETE SET NULL,
  -- Mỗi user chỉ có 1 record trên mỗi movie/episode, update thay vì insert mới
  UNIQUE KEY `uq_user_movie_history`   (`user_id`, `movie_id`),
  UNIQUE KEY `uq_user_episode_history` (`user_id`, `episode_id`),
  INDEX idx_watch_history_user_id    (`user_id`),
  INDEX idx_watch_history_watched_at (`watched_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. User Preferences
-- ============================================================================

CREATE TABLE `user_preferences` (
  `user_id`              INT PRIMARY KEY,
  `preferred_language_id` INT  NULL,
  `preferred_quality`    ENUM('360p','480p','720p','1080p','4K') DEFAULT '720p',
  `autoplay`             TINYINT(1) NOT NULL DEFAULT 1,
  `autoplay_next`        TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Tự động phát tập tiếp theo',
  `subtitle_default_lang` INT NULL COMMENT 'FK → languages',
  `email_notifications`  TINYINT(1) NOT NULL DEFAULT 1,
  `updated_at`           TIMESTAMP  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`)               REFERENCES `users`(`id`)     ON DELETE CASCADE,
  FOREIGN KEY (`preferred_language_id`) REFERENCES `languages`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`subtitle_default_lang`) REFERENCES `languages`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. Notifications
-- ============================================================================

CREATE TABLE `notifications` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT          NOT NULL,
  `type`       VARCHAR(50)  NOT NULL COMMENT 'new_episode, comment_reply, system, ...',
  `title`      VARCHAR(255) NOT NULL,
  `body`       TEXT,
  `link`       VARCHAR(512),
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX idx_notifications_user_id   (`user_id`),
  INDEX idx_notifications_is_read   (`is_read`),
  INDEX idx_notifications_created_at (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. Reports (báo cáo vi phạm)
-- ============================================================================

CREATE TABLE `reports` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `reporter_id` INT          NULL COMMENT 'NULL nếu báo cáo ẩn danh',
  `target_type` ENUM('movie','episode','comment','user') NOT NULL,
  `target_id`   INT          NOT NULL,
  `reason`      ENUM('spam','inappropriate','broken_link','copyright','other') NOT NULL,
  `detail`      TEXT,
  `status`      ENUM('pending','reviewed','resolved','rejected') NOT NULL DEFAULT 'pending',
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` DATETIME     NULL,
  FOREIGN KEY (`reporter_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX idx_reports_target       (`target_type`, `target_id`),
  INDEX idx_reports_status       (`status`),
  INDEX idx_reports_created_at   (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 13. Audit Logs (ghi log hành động admin/mod)
-- ============================================================================

CREATE TABLE `audit_logs` (
  `id`          BIGINT AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT          NULL COMMENT 'Admin/mod thực hiện hành động',
  `action`      VARCHAR(100) NOT NULL COMMENT 'e.g. movie.create, user.ban',
  `target_type` VARCHAR(50)  COMMENT 'e.g. movie, user, comment',
  `target_id`   INT,
  `payload`     JSON         COMMENT 'Dữ liệu trước/sau thay đổi',
  `ip_address`  VARCHAR(45),
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX idx_audit_logs_user_id    (`user_id`),
  INDEX idx_audit_logs_action     (`action`),
  INDEX idx_audit_logs_target     (`target_type`, `target_id`),
  INDEX idx_audit_logs_created_at (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
-- ============================================================================
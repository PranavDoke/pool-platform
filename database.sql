-- ============================================
-- Real-Time Live Poll Platform - Database SQL
-- Complete database setup with sample data
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS live_poll_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE live_poll_platform;

-- ============================================
-- Table: users
-- ============================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: password_reset_tokens
-- ============================================
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: sessions
-- ============================================
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: polls
-- ============================================
CREATE TABLE polls (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) DEFAULT 1,
    start_date TIMESTAMP NULL,
    end_date TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_active (is_active),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: poll_options
-- ============================================
CREATE TABLE poll_options (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    poll_id BIGINT UNSIGNED NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    INDEX idx_poll_id (poll_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: votes (IP-based voting)
-- ============================================
CREATE TABLE votes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    poll_id BIGINT UNSIGNED NOT NULL,
    poll_option_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    FOREIGN KEY (poll_option_id) REFERENCES poll_options(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_poll_ip_active (poll_id, ip_address, is_active),
    INDEX idx_poll_option (poll_option_id),
    INDEX idx_ip_address (ip_address),
    INDEX idx_voted_at (voted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: vote_history (Audit trail)
-- ============================================
CREATE TABLE vote_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vote_id BIGINT UNSIGNED NULL,
    poll_id BIGINT UNSIGNED NOT NULL,
    poll_option_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NOT NULL,
    action VARCHAR(50) NOT NULL,
    user_agent TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vote_id) REFERENCES votes(id) ON DELETE SET NULL,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    FOREIGN KEY (poll_option_id) REFERENCES poll_options(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_vote_id (vote_id),
    INDEX idx_poll_id (poll_id),
    INDEX idx_ip_address (ip_address),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Data: Users
-- ============================================
-- Password for both users: "password" (hashed with bcrypt)
INSERT INTO users (id, name, email, password, is_admin, created_at, updated_at) VALUES
(1, 'Admin User', 'admin@poll.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NANClx6nCp3K', 1, NOW(), NOW()),
(2, 'John Doe', 'user@poll.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NANClx6nCp3K', 0, NOW(), NOW());

-- ============================================
-- Sample Data: Polls
-- ============================================
INSERT INTO polls (id, user_id, title, description, is_active, created_at, updated_at) VALUES
(1, 1, 'What is your favorite programming language?', 'Vote for the programming language you enjoy working with the most.', 1, NOW(), NOW()),
(2, 2, 'Which framework do you prefer for web development?', 'Choose your preferred web development framework.', 1, NOW(), NOW()),
(3, 1, 'Best database for modern applications?', 'Vote for the database system you find most suitable for modern web applications.', 1, NOW(), NOW());

-- ============================================
-- Sample Data: Poll Options
-- ============================================
-- Poll 1 Options
INSERT INTO poll_options (poll_id, option_text, display_order, created_at, updated_at) VALUES
(1, 'JavaScript', 1, NOW(), NOW()),
(1, 'Python', 2, NOW(), NOW()),
(1, 'PHP', 3, NOW(), NOW()),
(1, 'Java', 4, NOW(), NOW());

-- Poll 2 Options
INSERT INTO poll_options (poll_id, option_text, display_order, created_at, updated_at) VALUES
(2, 'Laravel', 1, NOW(), NOW()),
(2, 'React', 2, NOW(), NOW()),
(2, 'Vue.js', 3, NOW(), NOW()),
(2, 'Angular', 4, NOW(), NOW());

-- Poll 3 Options
INSERT INTO poll_options (poll_id, option_text, display_order, created_at, updated_at) VALUES
(3, 'MySQL', 1, NOW(), NOW()),
(3, 'PostgreSQL', 2, NOW(), NOW()),
(3, 'MongoDB', 3, NOW(), NOW()),
(3, 'Redis', 4, NOW(), NOW());

-- ============================================
-- Verification Queries
-- ============================================
-- SELECT * FROM users;
-- SELECT * FROM polls;
-- SELECT * FROM poll_options;
-- SELECT * FROM votes;
-- SELECT * FROM vote_history;

-- ============================================
-- Login Credentials
-- ============================================
-- Admin: admin@poll.com / password
-- User: user@poll.com / password

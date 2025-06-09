CREATE TABLE documents (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    category_id INT DEFAULT 0,
    access_code VARCHAR(32) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL,
    status ENUM('new', 'in_review', 'archived', 'paused') DEFAULT 'new',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    responsible_user_id INT DEFAULT NULL
);

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderator', 'user') DEFAULT 'user'
);


CREATE TABLE access_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    user_id INT UNSIGNED DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    accessed_at DATETIME NOT NULL
);

ALTER TABLE access_logs
ADD CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id);

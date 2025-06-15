CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'responsible', 'user') DEFAULT 'user'
);

CREATE TABLE documents (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    category_id INT DEFAULT 0,
    access_code VARCHAR(32) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL,
    status ENUM('new', 'in_review', 'archived', 'paused') DEFAULT 'new',
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    responsible_user_id INT DEFAULT NULL
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

CREATE TABLE document_requests (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    uploaded_by_user_id INT UNSIGNED NOT NULL,
    document_type VARCHAR(255) DEFAULT NULL,
    access_code VARCHAR(32) NOT NULL,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    PRIMARY KEY (id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (uploaded_by_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE request_steps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    step_order INT NOT NULL,
    required_document VARCHAR(255) NOT NULL,
    status ENUM('pending', 'waiting_user', 'waiting_responsible', 'approved', 'rejected') DEFAULT 'pending',
    uploaded_file VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL,
    FOREIGN KEY (request_id) REFERENCES document_requests(id)
);

CREATE TABLE required_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    document_type VARCHAR(255) NOT NULL,
    required_document VARCHAR(255) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

ALTER TABLE documents ADD COLUMN document_type VARCHAR(255) DEFAULT NULL;

--ALTER TABLE document_requests ADD COLUMN document_type VARCHAR(255) DEFAULT NULL;
--ALTER TABLE document_requests ADD COLUMN access_code VARCHAR(32) NOT NULL;
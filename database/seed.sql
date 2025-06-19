-- Вмъкване на категории
INSERT INTO categories (name, responsible_user_id) VALUES
('Отдел Студенти', NULL),
('Учебен отдел – Магистри', NULL),
('Кандидат-студенти', NULL),
('Сесия', 2),
('Без категория', NULL);

-- Вмъкване на общ потребител (role: user)
INSERT INTO users (username, password, full_name, role)
VALUES ('student', '$2y$10$WtUDqLaJotlOaCTehNjOWOAAd.xlQo.CCObuV7D7Iv/8LR04y.hXm', 'Общ потребител', 'user');

-- Вмъкване на отговарящ потребител (role: responsible)
INSERT INTO users (username, password, full_name, role)
VALUES ('teacher', '$2y$10$KP.v8Hv2xAk97tCbC1r1lO4jr3qRwMMZKmFOwu/R6VwaA3b.007cW', 'Отговарящ потребител', 'responsible');

-- Вмъкване на администратор (role: admin)
INSERT INTO users (username, password, full_name, role)
VALUES ('admin', '$2y$10$GQsUsWLUZjD4AIVKV/FZhegeq1jQLNhkQxUSASdZHBDBAX2shQLTW', 'Администратор', 'admin');

INSERT INTO required_documents (category_id, document_type, required_document)
VALUES (2, 'Заявление за магистратура', 'Диплома за завършено висше образование');

INSERT INTO required_documents (category_id, document_type, required_document)
VALUES (3, 'Заявление за записване за специалност', 'Диплома за завършено средно образование');
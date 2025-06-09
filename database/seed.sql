INSERT INTO categories (name) VALUES
('Отдел Студенти'),
('Учебен отдел – Магистри'),
('Кандидат-студенти'),
('Сесия'),
('Без категория');

-- Вмъкване на общ потребител (role: user)
INSERT INTO users (username, password, full_name, role)
VALUES ('student', 'student', 'Общ потребител', 'user');

-- Вмъкване на отговарящ потребител (role: responsible)
INSERT INTO users (username, password, full_name, role)
VALUES ('teacher', 'teacher', 'Отговарящ потребител', 'responsible');

-- Вмъкване на администратор (role: admin)
INSERT INTO users (username, password, full_name, role)
VALUES ('admin', 'admin', 'Администратор', 'admin');
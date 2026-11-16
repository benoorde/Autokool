CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(5) NOT NULL UNIQUE,
  name_ru VARCHAR(100) NOT NULL,
  name_et VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS instructors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  bio_ru TEXT,
  bio_et TEXT,
  experience_years INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  instructor_id INT NULL,
  title_ru VARCHAR(150) NOT NULL,
  title_et VARCHAR(150) NOT NULL,
  description_ru TEXT,
  description_et TEXT,
  lessons_count INT NOT NULL DEFAULT 0,
  price_eur DECIMAL(8,2) NOT NULL,
  start_date DATE NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (instructor_id) REFERENCES instructors(id)
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(30),
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('student','admin') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS enrollments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  course_id INT NOT NULL,
  status ENUM('new','confirmed','cancelled') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  author_name VARCHAR(100) NOT NULL,
  rating TINYINT NOT NULL,
  text_ru TEXT,
  text_et TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

INSERT INTO categories (code, name_ru, name_et) VALUES
  ('B', 'Категория B (легковые)', 'Kategooria B (sõiduautod)'),
  ('A', 'Категория A (мотоциклы)', 'Kategooria A (mootorrattad)'),
  ('C', 'Категория C (грузовые)', 'Kategooria C (veokid)'),
  ('D', 'Категория D (автобусы)', 'Kategooria D (bussid)');

INSERT INTO instructors (full_name, bio_ru, bio_et, experience_years) VALUES
  ('Mart Tamm', 'Инструктор высшей категории, специализируется на начинающих водителях.', 'Kõrgema kategooria instruktor, spetsialiseerub algajatele juhtidele.', 15),
  ('Anna Kask', 'Терпеливый инструктор, помогает пройти экзамен с первого раза.', 'Kannatlik instruktor, aitab eksamile jõuda esimesel katsel.', 9);

INSERT INTO courses (category_id, instructor_id, title_ru, title_et, description_ru, description_et, lessons_count, price_eur, start_date) VALUES
  (1, 1, 'Базовый курс категории B', 'Kategooria B põhikursus', 'Полный курс теории и практики вождения легкового автомобиля.', 'Sõiduauto juhtimise täielik teooria- ja praktikakursus.', 30, 890.00, '2026-12-01'),
  (1, 2, 'Экспресс-курс категории B', 'Kategooria B kiirkursus', 'Ускоренная подготовка для тех, кто уже знаком с управлением.', 'Kiirendatud õpe neile, kes juba sõidavad.', 15, 490.00, '2026-12-05'),
  (2, 1, 'Курс категории A', 'Kategooria A kursus', 'Обучение управлению мотоциклом с нуля.', 'Mootorratta juhtimise õpe nullist.', 20, 690.00, '2026-12-10'),
  (3, 2, 'Курс категории C', 'Kategooria C kursus', 'Подготовка водителей грузовых автомобилей.', 'Veokijuhtide ettevalmistus.', 40, 1490.00, NULL);

INSERT INTO users (name, email, phone, password_hash, role) VALUES
  ('Administraator', 'admin@autokool.test', '+3725000000', '$2y$10$sNrkEXv.NwUY3y6.5mc2mOAcflDFYI713XvkKwJGVy4nZNa5gUgAK', 'admin');

INSERT INTO reviews (course_id, author_name, rating, text_ru, text_et) VALUES
  (1, 'Olga', 5, 'Отличный курс, сдала с первого раза!', 'Suurepärane kursus, sain esimesel katsel läbi!'),
  (1, 'Jüri', 4, 'Хорошие инструкторы и удобное расписание.', 'Head instruktorid ja mugav ajakava.');

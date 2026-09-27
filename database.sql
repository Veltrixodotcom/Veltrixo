USE sql12835544;

CREATE TABLE IF NOT EXISTS freelancers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    location VARCHAR(120) DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    skills TEXT DEFAULT NULL,
    experience_years DECIMAL(4,1) DEFAULT 0,
    portfolio_url VARCHAR(255) DEFAULT NULL,
    linkedin_url VARCHAR(255) DEFAULT NULL,
    resume_path VARCHAR(255) DEFAULT NULL,
    profile_photo VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    budget DECIMAL(12,2) DEFAULT 0,
    status ENUM('open','assigned','completed','closed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    freelancer_id INT UNSIGNED NOT NULL,
    project_id INT UNSIGNED NOT NULL,
    proposal TEXT DEFAULT NULL,
    status ENUM('pending','shortlisted','accepted','rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_application (freelancer_id, project_id),
    CONSTRAINT fk_app_freelancer FOREIGN KEY (freelancer_id) REFERENCES freelancers(id) ON DELETE CASCADE,
    CONSTRAINT fk_app_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

INSERT INTO projects (title, description, category, budget)
SELECT
    'Social Media Campaign',
    'Create a 30-day social media campaign for a growing local business.',
    'Social Media Marketing',
    15000
FROM (SELECT 1) AS temp
WHERE NOT EXISTS (
    SELECT 1
    FROM projects
    WHERE title = 'Social Media Campaign'
);

INSERT INTO projects (title, description, category, budget)
SELECT
    'Business Website',
    'Build a responsive five-page business website.',
    'Web Development',
    25000
FROM (SELECT 1) AS temp
WHERE NOT EXISTS (
    SELECT 1
    FROM projects
    WHERE title = 'Business Website'
);

INSERT INTO projects (title, description, category, budget)
SELECT
    'SEO Optimization',
    'Perform an SEO audit and implement on-page optimization.',
    'SEO',
    12000
FROM (SELECT 1) AS temp
WHERE NOT EXISTS (
    SELECT 1
    FROM projects
    WHERE title = 'SEO Optimization'
);
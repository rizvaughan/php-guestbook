-- Create database if not exists (handled by MYSQL_DATABASE env)
-- But we can add additional schema here

-- Create tables
CREATE TABLE IF NOT EXISTS guestbook.entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO guestbook.entries (message) VALUES 
('Welcome to the guestbook!'),
('Hello from Jenkins CI/CD!');

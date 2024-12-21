CREATE TABLE test (
    id INT AUTO_INCREMENT PRIMARY KEY,   -- Unique ID for each record
    name VARCHAR(100) NOT NULL,          -- Name field, maximum 100 characters
    description TEXT,                    -- Optional description field
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp of creation
);
CREATE TABLE users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    region VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,  
    password VARCHAR(255) NOT NULL
    reset_token VARCHAR(255) NULL,
    reset_token_expiry DATETIME NULL;
);

CREATE TABLE USERS (
    User_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role VARCHAR(30) DEFAULT 'Staff'
);

-- Generate a password hash with PHP password_hash() before inserting a staff user.
-- INSERT INTO USERS (Name, Email, Password, Role)
-- VALUES ('Admin', 'admin@example.com', '<password hash>', 'Admin');

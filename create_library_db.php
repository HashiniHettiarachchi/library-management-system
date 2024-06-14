<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully<br>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS library_system";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db('library_system2');

// SQL to create tables
$createTables = "
CREATE TABLE IF NOT EXISTS user (
    user_id VARCHAR(5) PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS book (
    book_id VARCHAR(5) PRIMARY KEY,
    book_name VARCHAR(100) NOT NULL,
    category_id VARCHAR(5) NOT NULL
);

CREATE TABLE IF NOT EXISTS bookcategory (
    category_id VARCHAR(5) PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    date_modified DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS member (
    member_id VARCHAR(5) PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    birthday DATE NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS bookborrower (
    borrow_id VARCHAR(5) PRIMARY KEY,
    book_id VARCHAR(5) NOT NULL,
    member_id VARCHAR(5) NOT NULL,
    borrow_status VARCHAR(100) NOT NULL,
    borrower_date_modified DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS fine (
    fine_id VARCHAR(5) PRIMARY KEY,
    member_id VARCHAR(5) NOT NULL,
    book_id VARCHAR(5) NOT NULL,
    fine_amount varchar(100) NOT NULL,
    fine_date_modified DATETIME NOT NULL
);

INSERT INTO users (user_id, first_name, last_name, username, password, email)
VALUES ('U001', 'Default', 'User', 'danu123', '$2y$10$E9WQ/NY.DS8j5ZPiw/.yaOK3MuvOqM12QyzP9S4ckP/6oW58L6xM2', 'default@example.com')
ON DUPLICATE KEY UPDATE user_id = user_id;
";

// Execute the SQL to create tables
if ($conn->multi_query($createTables)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "Tables created successfully";
} else {
    echo "Error creating tables: " . $conn->error;
}

$conn->close();
?>

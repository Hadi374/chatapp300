CREATE DATABASE IF NOT EXISTS chat;
USE chat;

CREATE TABLE IF NOT EXISTS users(
    id int PRIMARY KEY AUTO_INCREMENT,
    name varchar(30) NOT NULL,
    username varchar(10) UNIQUE,
    email varchar(255) NOT NULL,
    password char(64) NOT NULL,
    profile VARCHAR(20),
    bio VARCHAR(100),
    created_at int DEFAULT UNIX_TIMESTAMP()
);

CREATE TABLE IF NOT EXISTS messages(
    id int PRIMARY KEY AUTO_INCREMENT,
    sender_id int not null,
    chat_id int not null,
    type enum('text','file','image'),
    content_id int not null,
    view_count int default 0,
    reply_to int default null,
    is_edited boolean default false,
    created_at int default UNIX_TIMESTAMP()
);

CREATE TABLE IF NOT EXISTS text_messages(
    id INT PRIMARY KEY AUTO_INCREMENT,
    text TEXT default ""
);

CREATE TABLE IF NOT EXISTS files(
    file_id INT PRIMARY KEY AUTO_INCREMENT,
    md5_sum CHAR(32) NOT NULL,
    filename VARCHAR 255
);

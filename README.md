# Chat Application

## languages: 
HTML/CSS
JS
PHP
MySQL

## installation(developers)
first you must install xampp or similar applications.
then clone this repository in $HTDOCS folder of xampp.

then edit 'includes/config.php' based on your configurations.

then you must create the database in mysql. using 'query.sql' instructions or run that file in phpmyadmin.

## How does it works?
you can call these methods from browser or ajax method.
for example: 

for register a new user to system: 

localhost/projects/chatapp/register

Here you need to send some data like name, username, password ... to server using POST method.

POST name
POST username
POST email
POST password
POST password_verify

Here is list of all methods that you can call with Ajax:


register: register a user.
    returns user object.

    POST name
    POST username
    POST email
    POST password
    POST password_verify


login: login user to system if username and passwords be correct.
    returns user object.
    POST username
    POST password

logout: logout from system.

sendMessage: send a message to user or group
    returns message object.
    POST chat_id    
    POST text
    *POST reply_to

//getFile: downloads a file 
//    returns file not JSON object.
//    GET file_id

getMessage: return message object.
    GET message_id the id of message.

// TODO: complete this list...


<?php
// all codes of backend comes here.
//$db = new Database;

// TODO: separate public and private methods.


require_once 'includes/db.php';
require_once 'includes/utils.php';

class common {
    private $db;

    function __construct() {
        $this->db = new Database;
    }

    function hashPassword($password) {
        return hash("sha256", $password);
    }

    function validateEmail($email) {
        return mb_ereg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z]{2,3})$", $email);
    }

    function getFullUser($id) {
        $this->db->query("SELECT id,name,username,bio,profile,created_at FROM users WHERE id=:id");
        $this->db->bind(":id", $id);
        $result = $this->db->resultSet();
        if(count($result) == 1) {
            return $result[0];
        }
        failed("User not exist: " . $id);
    }

    function getUser($id) {
        $result = $this->getFullUser($id);
        return array(
            "id" => $result['id'],
            "name" => $result['name'],
            "username" => $result['username'],

        );
    }

    function getSelf() {
        if(isset($_SESSION['id']))
            return $this->getFullUser($_SESSION['id']);
        failed("Please login first.");
    }

    function register($name,  $username, $email, $password) {
        if(empty($username) || empty($name)) {
            failed("Empty name or username");
        }
        // check if username is used by another user.
        $this->db->query("SELECT * FROM users WHERE username=:username");
        $this->db->bind(":username", $username);
        $result = $this->db->resultSet();
        if(count($result) == 1) {
            failed("Username Exists. choose another username or login");
        }

        if(!$this->validateEmail($email)) {
            failed("invalid Email");
        }

        $password = $this->hashPassword($password);
        //echo $hashed_password;

        $this->db->query("INSERT INTO users(name,username,email,password) VALUES(:name,:username,:email,:password)");
        
        $this->db->bind('name', $name);
        $this->db->bind('username', $username);
        $this->db->bind('email', $email);
        $this->db->bind('password', $password);
        
        $id = $this->db->execute();
        if($id == 0) {
            failed("Cannot create account.");
        }  

        $_SESSION['id'] = $id;
        $result = $this->getSelf();
            
        return $result; //success
    }

    function login($username, $password) {
        $this->db->query("SELECT * FROM users WHERE username=:username");
        $this->db->bind(":username", $username);
        $result = $this->db->resultSet();
        
        $password = $this->hashPassword($password);
        
        if(count($result) != 1) {
            failed("Cannot find user: " . $username);
        }
        
        if($result[0]['password'] != $password) {
            failed("Invalid password!");
        } 
        
        $_SESSION['id'] = $result[0]['id'];
        return $this->getSelf(); //success("Logged In successfully!", $result);
        
    }

    function logout() {
        unset($_SESSION['id']);
        session_abort();
    }

    function uploadFile($file) {
        
        /**
         * file has md5 sum.
         * name for when downloading.
         * id: auto_increment.
         */

       if($file['error']) {
           failed("Error uploading file");
       }
      
        // limit size of file to 50mb;
        if($file['size'] >= 50 * 1024 * 1024) {
            failed("File is too big. (limit is 50MB)");
        }

        $filename = md5_file($file['tmp_name']);
       
        if(file_exists(FILE_PATH . $filename)) {
            return $filename;
        } else {
            move_uploaded_file($file['tmp_name'], FILE_PATH . $filename);
            // insert to database
            $this->db->query("INSERT INTO files (md5_sum, file_name) VALUES (:md5_sum, :file_name)");
            $this->db->bind(":md5_sum", $filename);
            $this->db->bind(":file_name", basename($file['name']));
            if($this->db->execute() == 0) {
                failed("File not uploaded, try again.");
            }
            return $filename;  
        }
    } 

    function getFile($file_id) {
        $this->db->query("SELECT * FROM files WHERE md5_sum=:file_id");
        $this->db->bind(":file_id", $file_id);
        $result = $this->db->ResultSet();
        
        if(count($result) == 1) {
            $file = $result[0]['file_name']; 
            $mimetype = mime_content_type(FILE_PATH . $file_id);
            
            header('Content-type: ' . $mimetype);
            header('Content-Disposition: attachment; filename="' . $file . '"');
            readfile(FILE_PATH . $file_id); // return file.
        } else {
            failed("file not exist.");
        }
    }

    function getFileObject($md5_of_file) {
        //return file object.
        $result = array();
        $result['file_path'] = FILE_PATH . $randomId;
        
        $result['file_id'] = $randomId;
        $result['mime_type'] = mime_content_type($result['file_path']);
        $result['file_size'] = filesize($result['file_path']);
        
        return $result;
    }

    private function newTextContent($text) {
        // insert into database.
        $this->db->query("INSERT INTO text_messages (text) VALUES (:text)");
        $this->db->bind(":text", $text);
        $lastId = $this->db->execute();
        return array('id' => $lastId, 'text' => $text);
    }
 
    function newTextMessage($text, $reply_to) {
        return array('type' => 'text', 'content' => $this->newTextContent($text), 'reply_to' => $reply_to);
    } 

    function getFileContent($file_id) {
        $this->db->query("SELECT * FROM files WHERE id=:file_id");
        $this->db->bind(":file_id", $file_id);
        $result = $this->db->ResultSet();
        if(count($result) == 1) {
            return $this->getFileObject($result[0]['md5_sum']);
        }
        failed("File not found! " . $file_id);
    }

    function getTextContent($content_id) {
        $this->db->query("SELECT * FROM text_messages WHERE id=:id");
        $this->db->bind(":id", $content_id);
        $result = $this->db->ResultSet();
        if(count($result) == 1) {
            return $result[0]['text'];
        } else {
            failed("Internal Error: cannot get content of text message");
        }
    }

    function getContent($type, $content_id) {

        switch($type) {
            case 'text':
                return $this->getTextContent($content_id);
            break;
            case 'image':
                $result = $this->getFileContent($content_id);
                $result['width'] = 0;
                $result['height'] = 0;  

                return $result;
                
            break;
            case 'file':
                return $this->getFileContent($content_id);
            break;

        }
    }

    // if chat is private: return members only if the user is a member of the chat.
    // else return members.
    function getChatMembers($chat_id) {
        $chat = $this->getChat($chat_id);
        $this->db->query("SELECT * FROM chat_members WHERE chat_id=:chat_id");
        $this->db->bind(":chat_id", $chat_id);
        $dbResult = $this->db->ResultSet();
           
        if(count($dbResult) == 0) {
            failed("this group doesn't have any member");
        }

        $result = array();
        foreach($dbResult as $item) {
            $result[] = $this->getUser($item['user_id']);
        }
       

        /** private chat */
        if($chat['is_public'] == false) {
            // find self in members.
            foreach($result as $member) {
                if($this->getSelf()['id'] == $member['user_id']) {
                    return $result;
                    break;
                }
            }
            failed("You don't have access to this chat");
        } 
        /** Public chat */
        else {
            return $result;
        }
    }

   // if recursive: we must get message that this is replied to.
    function getMessage($message_id, $recursive) {

        // check if the user has access to message.
        // x must be sender, or receiver, or a member of the message's group.
       
        $has_access = false;
        $members = array();

        // get message from database.
        $this->db->query("SELECT * FROM messages WHERE id=:message_id");
        $this->db->bind(":message_id", $message_id);
        $dbResult = $this->db->ResultSet();
 
        if(count($dbResult) == 0) {
            failed("Message not found");
        }
 
        $sender_id = $dbResult[0]['sender_id'];
        $chat_id = $dbResult[0]['chat_id'];
 
        /** check if the user is sender of group */
        if($sender_id == $this->getSelf()['id']) {
            $has_access = true;
        } 
        /** Check if the user is member of group */
        else {
            if($chat_id < 0) {
                $members = $this->getChatMembers($chat_id);
    
                foreach($members as $member) {
                    if($this->getSelf()['id'] == $member['user_id']) {
                        $has_access = true;
                        break;
                    }
                }
            } else {
                if($chat_id == $this->getSelf()['id']) {
                    $has_access = true;
                }
            }
        }  
        if(!$has_access) {
            failed("You don't have access to read this message");
        }
           
        // from 
        // chat
        // date
        // [text, photo, voice, file, ...]
        
        $result = array();
        
        $result['message_id'] = $dbResult[0]['id'];
        $result['from'] = $this->getUser($dbResult[0]['sender_id']); 
        $result['chat'] = $this->getChat($chat_id);
        $result['date'] = $dbResult[0]['created_at'];
        // reply_to
        if($recursive && isset($dbResult[0]['reply_to'])) {
            $result['reply_to'] = $this->getMessage($dbResult[0]['reply_to'], false);
        } 

        // message content
        // 'text' = 'This is text message';
        // 'file' = [ file_id=Xkjsdlkfjsj, size=123544, type='application/pdf'];
        // ....
        $result[$dbResult[0]['type']] = $this->getContent($dbResult[0]['type'], $dbResult[0]['content_id']);
        return $result;
    }

    /**
     * 
     * group: 
     *  id
     *  title
     *  description
     *  members
     *  active members ?
     *  type
     *  image
     *  is_public
     *  invite link
     *  pinned_message
     *  creator
     */

    function getGroup($chat_id) {
        $group = $this->getFullGroup($chat_id);
        return array(
            "id" => $group['id'],
            "title" => $group['title'],
            "type" => 'group'
        );
    } 

    function getFullGroup($chat_id) {
        $this->db->query("SELECT * FROM chats WHERE id=:chat_id");
        $this->db->bind(":chat_id", $chat_id);
        $result = $this->db->ResultSet();
        if(count($result) == 1) {
            // get group based on real_id;
            // add channel or other types of chat too.
            $this->db->query("SELECT * FROM groups WHERE id=:group_id");
            $this->db->bind(":group_id", $result[0]['real_id']);
            $result = $this->db->ResultSet();
            if(count($result) == 1) {
                //return $result[0];
                $res = array();
                $res['id'] = $chat_id;
                $res['title'] = $result['name'];
                $res['description'] = $result['description'];
                $res['is_public'] = $result['is_public'];
                $res['image'] = $result['image'];         // get file Url
                $res['type'] = 'group'; 
                $res['pinned_message'] = $this->getMessage($result['pinned_message'], false);
                $res['creator'] = $this->getUser($result['creator_id']);
                return $res;
            }
        }
        failed("Chat doesn't exists.");
    }
    

    function getChat($chat_id) {
        if($chat_id < 0) {
            return $this->getGroup(-$chat_id);    
        } else {
            return $this->getUser($chat_id);
        }
    }

    function sendMessage($chat_id, $message) {
        $sender_id = $this->getSelf()['id']; 

        $content_id = $message['content']['id'];
        $type = $message['type'];
        $reply_to = null;
        if(isset($message['reply_to'])) {
            $reply_to = $message['reply_to'];
        }
        $this->db->query("INSERT INTO messages (sender_id, chat_id, type, content_id, reply_to) VALUES (:sender_id, :chat_id, :type, :content_id, :reply_to)");
        $this->db->bind(":sender_id", $sender_id);
        $this->db->bind(":chat_id", $chat_id);
        $this->db->bind(":content_id", $content_id);
        $this->db->bind(":type", $type);
        $this->db->bind(":reply_to", $reply_to); 

        $dbResult = $this->db->execute();
        $message = $this->getMessage($dbResult, true);
        if($message) {
            return $message; // success
        } else {
            failed("Message not sent!");
        }  
    }
}

?>
<?php
// Function to check if a user is already connected
///////////////////////////////////////////////////////
function isUserConnected($username, $connections){
    foreach ($connections->connection as $connection) {
        if ($connection->user == $username) {
        // Check if the connection is still valid (within 5 minutes)
            $currentTime = time();
            $connectionTime = strtotime($connection->date);
            $expirationTime = $connectionTime + (5 * 60);
            if ($currentTime < $expirationTime) {
                return true; // User is already connected
            }
        }
    }
    return false; // User is not connected or the connection has expired
}

// Function to write a connection to the connection.xml file
///////////////////////////////////////////////////////
function writeConnection($username){
    // Load existing connections or create a new XML document
    if (file_exists('xmldb/connection.xml')) {
        $connections = simplexml_load_file('xmldb/connection.xml');
    } else {
        $connections = new SimpleXMLElement('<connections></connections>');
    }
    // Create a new connection entry
    $connection = $connections->addChild('connection');
    $connection->addChild('user', $username);
    $connection->addChild('date', date('Y-m-d H:i:s'));
    // Save the updated connections to connection.xml
    $connections->asXML('xmldb/connection.xml');
}

// Function to register a user in the users.xml
///////////////////////////////////////////////////////
function registerNewUser($username, $password, $user_id){
    $users = loadUsers('xmldb/users.xml');
    $exist = false;
    foreach ($users->user as $user) {
        if ($user->id == $user_id) {
            $exist = true;
        }
    }
    if (!$exist) {
        $user = $users->addChild('user');
        $user->addChild('id', $user_id);
        $user->addChild('username', $username);
        $user->addChild('password', $password);
    }
    $users->asXML('xmldb/users.xml');
}

////////////////////////////////////////////////////////////////
// Check if username and password are provided in the URL
function loginUser($username, $password){
    if (isset($_GET['username']) && isset($_GET['password'])) {
        $username = $_GET['username'];
        $password = $_GET['password'];
        // Load user.xml file
        $users = loadUsers('xmldb/users.xml');
        $verification = false;
        // Check if the user exists and the password matches
        foreach ($users->user as $user) {
            if ($user->username == $username && $user->password == $password) {
                $verification = true;
                $user_id = $user->id;
                // Check if the user is already connected
                $connections = simplexml_load_file('xmldb/connection.xml');
                if (!isUserConnected($username, $connections)) {
                    // Write the new connection to connection.xml
                    writeConnection($username);
                    echo "Connection successful for user: $username .<br>";
                    return array(true, $user_id);
                } else {
                    echo "User $username is already connected. <br>";
                    return array(true, $user_id);
                }
            } 
        }
        if (!$verification) {
            echo "Invalid username or password";
            return false;
        }
    } else {
        echo "Username and password are required in the URL";
        return false;
    }
}

// Function to register a user in the users.xml
///////////////////////////////////////////////////////
function loadUsers($user_file){
    if (file_exists($user_file)) {
        $users = simplexml_load_file($user_file);
        // echo "Funciona";
    } else {
        $users = new SimpleXMLElement('<users></users>');
    };
    $users->asXML($user_file);
    return $users;
}
?>
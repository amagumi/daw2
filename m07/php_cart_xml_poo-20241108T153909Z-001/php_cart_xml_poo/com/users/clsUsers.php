<?php
class User {
    private $users;

    public function __construct() {
        $this->users = $this->loadUsers();
    }

    private function loadUsers() {
        $file = 'xmldb/users.xml';
        if (file_exists($file)) {
            return simplexml_load_file($file);
        } else {
            return new SimpleXMLElement('<user></user>');
        }
    }

    public function register($username, $password) {
        echo "Usuario registrado <br>";
        echo "Hola " . $username . "<br>";
        echo "<br>";
    
        // Verificar si el usuario ya existe
        foreach ($this->users->user_item as $item) {
            if ((string)$item->username === $username) {
                echo "El usuario ya está registrado.<br>";
                return;
            }
        }
    
        $item = $this->users->addChild('user_item');
        $item->addChild('username', $username);
        $item->addChild('password', $password);
    
        $userInfo = $item->addChild('info_user');
        $userInfo->addChild('name', 'alex');
        $userInfo->addChild('last_name', 'bujardon');
    
        $this->saveUsers();
    }

    private function saveUsers() {
        $this->users->asXML('xmldb/users.xml');
    }

    
    
}
?>
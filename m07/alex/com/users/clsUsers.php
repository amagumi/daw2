<?php
class User {
    private $users;
///////////////////////////////////////////////////////////////////////////////////////
    public function __construct() {
        $this->users = $this->loadUsers();
    }
///////////////////////////////////////////////////////////////////////////////////////
    private function loadUsers() {
        $file = 'xmldb/users.xml';
        if (file_exists($file)) {
            return simplexml_load_file($file);
        } else {
            return new SimpleXMLElement('<user></user>');
        }
    }
///////////////////////////////////////////////////////////////////////////////////////
    public function register(string $username, string $password) {
        echo "Usuario registrado <br>";
        echo "Hola " . $username . "<br>";
        echo "<br>";
    

        foreach ($this->users->user_item as $item) { //verifica si el usuario esta registrado
            if ((string)$item->username === $username) {
                echo "El usuario ya está registrado.<br>";
                return;
            }
        }
        //si no esta registrado entonces agrega ese usuario y contraseña en users.xml
        $item = $this->users->addChild('user_item');
        $item->addChild('username', $username);
        $item->addChild('password', $password);
    
        $userInfo = $item->addChild('info_user');
        $userInfo->addChild('name', 'alex');
        $userInfo->addChild('last_name', 'bujardon');
    
        $this->saveUsers();
    }
///////////////////////////////////////////////////////////////////////////////////////
    public function login(string $username, string $password) {
        foreach ($this->users->user_item as $item) {
            if ((string)$item->username === $username) {
                if ((string)$item->password === $password) {
                    echo "Bienvenido, $username.<br>";
                    return true;
                } else {
                    echo "Contraseña incorrecta.<br>";
                    return false;
                }
            }
        }
        echo "El usuario no existe.<br>";
        return false;
    }
///////////////////////////////////////////////////////////////////////////////////////
    private function saveUsers() {
        $this->users->asXML('xmldb/users.xml');
    }
}
?>
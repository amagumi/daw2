<?php

class clsUser {
    
    private $id;
    private $username;
    private $password;
    private $login = false;
    private $xml;
    private $xml_file='xmldb/users.xml';

    function __construct($id, $username, $password, $registrar = false) {
        $this->LoadFile();
        if ($registrar) {
            $this->Register($id, $username, $password);

        } else {
            $this->LoadUser($id, $username, $password);
        }
        if ($this->login) {
            echo "Usuario logeado correctamente. <br>";
        } else {
            echo "Algún dato es incorrecto. <br>";
            // exit;
        }

    }

    function Save() {
        $this->xml->asXML($this->xml_file);
    }

    private function LoadFile () {
        if (file_exists($this->xml_file)) {
            $this->xml = simplexml_load_file($this->xml_file);
            // echo "Funciona";
        } else {
            $this->xml = new SimpleXMLElement('<users></users>');
            $this->xml->asXML($this->xml_file);
        };
    }

    private function LoadUser ($id, $username, $password) {
        if ($this->ExistUser($id)) {
            $fileUser = $this->xml->xpath("/users/user[@id='$id']/username");
            $filePassword = $this->xml->xpath("/users/user[@id='$id']/password");
            if ($fileUser[0][0] == $username && $filePassword[0][0] == $password) {
                $this->id = $id;
                $this->username = $username;
                $this->login = true;
            }
        }
    }

    private function Register ($id, $username, $password) {
        if (!$this->ExistUser($id)) {
            $newUser = $this->xml->addChild("user");
            $newUser->addAttribute("id", $id);
            $newUser->addChild("username", $username);
            $newUser->addChild("password", $password);
            
            $this->id = $id;
            $this->username = $username;
            $this->login = true;
            $this->Save();
            echo "Usuario registrado correctamente. <br>";
        } else {
            echo "El DNI del usuario que has indicado ya existe. <br>";
        }
    }

    private function ExistUser ($id) {
        foreach ($this->xml->xpath("/users/user") as $user) {
            if ($user['id'] == $id) {
                return true;
            }
        }
        return false;
    }

    public function GetId()
    {
        return $this->id;
    }

    public function GetUsername()
    {
        return $this->username;
    }

    public function GetLogin()
    {
        return $this->login;
    }
}
    

?>
<?php

class clsConnection {

    private $id;
    private $usuario;
    private $conectado = false;
    private $xml;
    private $xml_file='xmldb/connection.xml';

    public function __construct($id_user, $username) {
        $this->Load();
        $this->id = $id_user;
        $this->usuario = $username;
        if (!$this->isUserConnected($this->id)) {
            $this->writeConnection();
            $this->conectado = true;
            echo "Conexión establecida correctamente. <br>";
        } else {
            echo "La conexión ya está establecida. <br>";
        }
    }

    private function Load () {
        if (file_exists($this->xml_file)) {
            $this->xml = simplexml_load_file($this->xml_file);
            // echo "Funciona";
        } else {
            $this->xml = new SimpleXMLElement('<connections></connections>');
            $this->xml->asXML($this->xml_file);
        };
        
    }

    private function Save() {
        $this->xml->asXML($this->xml_file);
    }

    private function writeConnection(){
        $connection = $this->xml->addChild('connection');
        $connection->addChild('id', $this->id);
        $connection->addChild('user', $this->usuario);
        $connection->addChild('date', date('Y-m-d H:i:s'));
        // Save the updated connections to connection.xml
        $this->Save();
    }

    public function isUserConnected($id_user){
        foreach ($this->xml->xpath("/connections/connection") as $connection) {
            if ($connection->id == $id_user) {
                $currentTime = time();
                $connectionTime = strtotime($connection->date);
                $expirationTime = $connectionTime + (5 * 60);
                if ($currentTime < $expirationTime) {
                    $this->conectado = true;
                    return true;
                }
            }
        }
        return false;
    }

    public function GetConectado() {
        return $this->conectado;
    }
}

?>
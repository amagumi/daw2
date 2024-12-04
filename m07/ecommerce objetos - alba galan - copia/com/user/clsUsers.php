<?php
class clsUsers
{

    private array $usersArr = [];
    private string $usersFile = 'xmlDB/users.xml';


    public function __construct()
    {
        $this->usersArr = [];
        $this->_loadFromXML($this->usersFile);
    }


    private function _loadFromXML(string $usersFile): void
    {
        if (file_exists($usersFile)) {
            $usersXML = simplexml_load_file($usersFile);
            foreach ($usersXML->user as $user) {
                //recorre el xml y por cada nodo product lo convierte en objeto
                $createUser = new clsUser(
                    (int)$user->dni,
                    (string)$user->name,
                    (string)$user->country,
                    (string)$user->password
                );
                $this->addUser($createUser);
                // echo $createUser->getName() . " se ha creado" . "<br><br>";
            }
        }
    }


    public function addUser(clsUser $user): void
    {
        $this->usersArr[$user->getName()] = $user;
    }


    // get dni del user
    public function fetchDni(int $dni)
    {
        foreach ($this->usersArr as $user) { // utiliza el getter del dni de la clase clsuser
            if ($dni == $user->getDni()) {
                return $user;
            }
        } //control de errores
        return null;
    }


    public function login(int $dni)
    {
        $user = $this->fetchDni($dni);
        if ($user != null) {
            // echo "Bienvenido, " . $user->getName() . "!";
            return $user;
        } else {
            echo "Usuario no encontrado";
            return null;
        }
    }

    // mostrar todos los users
    public function showUsers(): void
    {
        $usersFile = 'xmlDB/users.xml';

        if (file_exists($usersFile)) {
            // Cargar y mostrar el XML en su formato original
            header("Content-Type: application/xml");
            readfile($usersFile);
        } else {
            echo "No hay users.";
        }
    }
}

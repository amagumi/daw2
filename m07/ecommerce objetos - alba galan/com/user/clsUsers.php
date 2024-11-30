<?php
class clsUsers
{

    private $usersArr = [];
    private $usersFile = 'xmlDB/users.xml';


    public function __construct()
    {
        $this->usersArr = [];
        $this->loadFromXML($this->usersFile);
    }


    private function loadFromXML($usersFile)
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


    public function addUser(clsUser $user)
    {
        $this->usersArr[$user->getName()] = $user;
    }


    // get dni del user
    public function fetchDni($dni)
    {
        foreach ($this->usersArr as $user) { // utiliza el getter del dni de la clase clsuser
            if ($dni == $user->getDni()) {
                return $user;
            }
        } //control de errores
        return null;
    }


    public function login($dni)
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
    public function showUsers()
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



    // UserExists()


    // Log()
    //function create user
    ////////////////////////////////////////////////////////////////////////

    // function userRegister($dni, $name, $country, $password) //el dni es el username
    // {

    //     echo "user register <br>";
    //     echo $dni;
    //     echo $name;
    //     echo $country;
    //     echo $password;

    //     $users = getUsers();

    //     $user = $users->addChild('user');
    //     $user->addChild('dni', $dni);
    //     $user->addChild('name', $name);
    //     $user->addChild('country', $country);
    //     $user->addChild('password', $password);

    //     $user_currency = $user->addChild('user_currency');
    //     $user_currency->addChild('currency', 'EU');

    //     $users->asXML('xmlDB/users.xml');
    // }

    // function getUsers()
    // {

    //     $file = 'xmlDB/users.xml';

    //     // Load existing connections or create a new XML document
    //     if (file_exists($file)) {
    //         echo "hola";
    //         $user = simplexml_load_file($file);
    //     } else {
    //         echo "adios";
    //         $user = new SimpleXMLElement('<users></users>');
    //     }

    //     return $user;
    // }



    // //function login
    // ////////////////////////////////////////////////////////////////////////

    // // crea el archivo de conexiones

    // function writeConnection($dni)
    // {
    //     // Load existing connections or create a new XML document
    //     if (file_exists('connection.xml')) {
    //         $connections = simplexml_load_file('connection.xml');
    //     } else {

    //         $connections = new SimpleXMLElement('<connections></connections>');
    //     }
    //     // Create a new connection entry
    //     $connection = $connections->addChild('connection');
    //     $connection->addChild('user', $dni);
    //     $connection->addChild('date', date('Y-m-d H:i:s'));
    //     // Save the updated connections to connection.xml
    //     $connections->asXML('connection.xml');
    // }


    // // login

    // // Check if username and password are provided in the URL
    // if (isset($_GET['dni']) && isset($_GET['password'])) {
    //     $dni = $_GET['dni'];
    //     $password = $_GET['password'];

    //     // Load user.xml file
    //     $users = simplexml_load_file('xmlDB/users.xml');
    //     // Check if the user exists and the password matches
    //     foreach ($users->user as $user) {
    //         if ($user->dni == $dni && $user->password == $password) {
    //             // Check if the user is already connected
    //             $connections = simplexml_load_file('connection.xml');
    //             if (!isUserConnected($dni, $connections)) {
    //                 // Write the new connection to connection.xml
    //                 writeConnection($dni);
    //                 echo "Connection successful for user: $dni" . "<br>";
    //                 echo '<a href="tienda.php">Ir a la tienda</a>';
    //             } else {
    //                 echo "User $dni is already connected." . "<br>";
    //                 echo '<a href="tienda.php">Ir a la tienda</a>' . "<br><br>";
    //             }
    //             exit(); // Stop execution after successful connection
    //         }
    //     }
    //     echo "Invalid dni or password";
    // } else {
    //     echo "dni and password are required in the URL" . "<br><br>";
    // }

    // // Function to check if a user is already connected
    // ///////////////////////////////////////////////////////
    // function isUserConnected($username, $connections)
    // {
    //     foreach ($connections->connection as $connection) {
    //         if ($connection->user == $username) {
    //             // Check if the connection is still valid (within 5 minutes)
    //             $currentTime = time();
    //             $connectionTime = strtotime($connection->date);
    //             $expirationTime = $connectionTime + (5 * 60);
    //             if ($currentTime < $expirationTime) {
    //                 return true; // User is already connected
    //             }
    //         }
    //     }
    //     return false; // User is not connected or the connection has expired
    // }

}

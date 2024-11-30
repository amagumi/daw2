<?php
require 'com/cart/clsCart.php';
require 'com/catalog/clsCatalog.php';
require 'com/users/clsUsers.php';
///////////////////////////////////////////////////////////////////////////////////////
$action = isset($_GET['action']) ? $_GET['action'] : 'default';
///////////////////////////////////////////////////////////////////////////////////////
$user = new User(false);
$catalog = new Catalog();
///////////////////////////////////////////////////////////////////////////////////////
switch ($action) {
    case 'register':   //?action=register&username=alex&password=12345
        $username = $_GET['username'] ?? '';
        $password = $_GET['password'] ?? '';
        if (!empty($username) && !empty($password)) {
            $user->register($username, $password);
            echo "Usuario registrado con éxito.<br>";
        } else {
            echo "Faltan datos para registrar al usuario.<br>";
        }
        break;
///////////////////////////////////////////////////////////////////////////////////////
    case 'login':   //?action=login&username=alex&password=12345
        $username = $_GET['username'] ?? '';
        $password = $_GET['password'] ?? '';
        if (!empty($username) && !empty($password) && $user->login($username, $password)) {
            echo "Inicio de sesión exitoso para $username.<br>";
        } else {
            echo "Credenciales incorrectas.<br>";
        }
        break;
///////////////////////////////////////////////////////////////////////////////////////
    case 'addProduct':   //?action=addProduct&id=1&name=PS5&price=500&stock=50
        $id = $_GET['id'] ?? '';
        $name = $_GET['name'] ?? '';
        $price = $_GET['price'] ?? 0;
        $stock = $_GET['stock'] ?? 0;

        if (!empty($id) && !empty($name) && $price > 0 && $stock > 0) {
            $catalog->addProduct($id, $name, $price, $stock);
            echo "Producto añadido al catálogo.<br>";
        } else {
            echo "Faltan datos para añadir el producto.<br>";
        }
        break;
///////////////////////////////////////////////////////////////////////////////////////
    case 'showCatalog':
        $catalog->showCatalog();
        break;
///////////////////////////////////////////////////////////////////////////////////////
    case 'addToCart':   //?action=addToCart&username=alex&password=12345&product_id=1&quantity=2
        $username = $_GET['username'] ?? '';
        $password = $_GET['password'] ?? '';
        $productId = $_GET['product_id'] ?? '';
        $quantity = $_GET['quantity'] ?? 0;

        if (!empty($username) && !empty($password) && $user->login($username, $password)) {
            $cart = new Cart($username);
            if ($catalog->updateStock($productId, $quantity)) {
                $cart->addProduct($productId, 'Nombre del Producto', $quantity, 100); // Precio ficticio
                echo "Producto añadido al carrito.<br>";
            } else {
                echo "Stock insuficiente.<br>";
            }
        } else {
            echo "Credenciales incorrectas o faltan datos.<br>";
        }
        break;
///////////////////////////////////////////////////////////////////////////////////////
    case 'showCartXML':
        $username = $_GET['username'] ?? '';
        $password = $_GET['password'] ?? '';

        if (!empty($username) && !empty($password) && $user->login($username, $password)) {
            $cart = new Cart($username);
            $cart->ShowXML();
        } else {
            echo "No se pudo iniciar sesión o faltan datos.<br>";
        }
        break;
///////////////////////////////////////////////////////////////////////////////////////
    default:
        echo "Bienvenido a la tienda. Usa las acciones disponibles para interactuar.<br>";
        break;
}


/*
//Users - Register
$user = new User();
$user->register('alex', '12345');

//Catalog
$catalog = new Catalog();
$catalog->addProduct(1, 'PlayStation 4', 250, 100);
$catalog->addProduct(2, 'PlayStation 5', 500, 100);
$catalog->addProduct(3, 'Xbox Series S', 400, 100);
$catalog->addProduct(4, 'Nintendo Switch', 320, 100);
$catalog->showCatalog();

//Cart - Login
$username = 'alex';
$password = '12345';
if ($user->login($username, $password)) {
    $cart = new Cart($username);
    $cart->addProduct(1, 'PlayStation4', 1, 250);
    $cart->showCart('DESC10');
//    $cart->ShowXML();
} else {
    echo "No es posible iniciar la sesión.<br>";
}
?>
*/

?>
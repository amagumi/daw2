<?php
require_once 'com/cart/clsCart.php';
require_once 'com/catalog/clsCatalog.php';
require_once 'com/product/clsProduct.php';
require_once 'com/user/clsUser.php';
require_once 'com/user/clsUsers.php';

$users = new clsUsers();
$catalog = new clsCatalog();


// users

//////////////////////////////////////////////////////////////

$user = $users->login(1); // se introduce el dni del usuario por parametro y se almacena en la variable user
$username = $user->getName(); // se recoge el nombre del objeto user mediante un getter
$cart = new clsCart($username, $catalog);


$action = isset($_GET['action']) ? $_GET['action'] : 'default';

$productId = isset($_GET['productId']) ? (int)$_GET['productId'] : null;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;



switch ($action) {

    default:
        echo "bienvenide <br><br>";
        break;
    case 'login': // ?action=login&userId=ID DEL USUARIO 
        $userId = isset($_GET['userId']) ? (int)$_GET['userId'] : 1;
        $user = $users->login($userId); // se introduce el ID del usuario por parámetro y se almacena en la variable user 
        $username = $user->getName(); // se recoge el nombre del objeto user mediante un getter 
        $cart = new clsCart($username, $catalog);
        echo "Usuario $username ha iniciado sesión.";
        break;
    case 'addProduct': // ?action=addProduct&productId=ID DEL PROD&quantity=CANTIDAD
        $cart->addProduct($productId, $quantity);
        break;

    case 'removeProduct':
        $cart->removeFromCart($username, 6);
        break;

    case 'showCart': // ?action=showCart
        $cart->showCart();
        break;

    case 'showCatalog': // ?action=showCatalog
        $catalog->showCatalog();
        break;
}

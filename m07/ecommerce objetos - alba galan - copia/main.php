<?php
require_once 'com/cart/clsCart.php';
require_once 'com/catalog/clsCatalog.php';
require_once 'com/product/clsProduct.php';
require_once 'com/user/clsUser.php';
require_once 'com/user/clsUsers.php';

$users = new clsUsers();
$catalog = new clsCatalog();
$users = new clsUsers();
$catalog = new clsCatalog();
$action = isset($_GET['action']) ? $_GET['action'] : 'default';
$userId = isset($_GET['userId']) ? (int)$_GET['userId'] : null;
$productId = isset($_GET['productId']) ? (int)$_GET['productId'] : null;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;


switch ($action) {

    case 'login': // ?action=login&userId=ID DEL USUARIO 
        if ($userId) {
            $user = $users->login($userId);
            $username = $user->getName();
            header("Location: index.html?userId=$userId");
            exit;
        } else {
            echo "error";
        }
        break;

    case 'showCart': // ?action=showCart
        if ($userId) {
            $user = $users->login($userId);
            if ($user) {
                $username = $user->getName();
                $cart = new clsCart($username, $catalog);
                $cart->showCart();
            }
        }
        break;

    case 'addProduct': // ?action=addProduct&productId=ID DEL PROD&quantity=CANTIDAD
        if ($userId && $productId && $quantity) {
            $user = $users->login($userId);
            if ($user) {
                $username = $user->getName();
                $cart = new clsCart($username, $catalog);
                $cart->addProduct($productId, $quantity);
            }
        }
        break;

    case 'removeProduct': // ?action=removeProduct&username=USER&productId=ID DEL PROD
        if ($userId && $productId) {
            $user = $users->login($userId);
            if ($user) {
                $username = $user->getName();
                $cart = new clsCart($username, $catalog);
                $cart->removeFromCart($username, $productId);
            }
        }
        break;

    case 'showCatalog': // ?action=showCatalog
        $catalog->showCatalog();
        break;
}



    // case 'login': // ?action=login&userId=ID DEL USUARIO 
    // $userId = isset($_GET['userId']) ? (int)$_GET['userId'] : 1;
    // $user = $users->login($userId); // se introduce el ID del usuario por parámetro y se almacena en la variable user 
    // $username = $user->getName(); // se recoge el nombre del objeto user mediante un getter 
    // $cart = new clsCart($username, $catalog);
    // echo "Usuario $username ha iniciado sesión.";
    // break;
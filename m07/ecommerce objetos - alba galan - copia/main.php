<?php
require_once 'com/cart/clsCart.php';
require_once 'com/catalog/clsCatalog.php';
require_once 'com/product/clsProduct.php';
require_once 'com/user/clsUser.php';
require_once 'com/user/clsUsers.php';

$users = new clsUsers();
$catalog = new clsCatalog();


// users

////////////////////////////////////////////////////////////

$user = $users->login(2); // se introduce el dni del usuario por parametro y se almacena en la variable user
$username = $user->getName(); // se recoge el nombre del objeto user mediante un getter
$cart = new clsCart($username, $catalog);


$action = isset($_GET['action']) ? $_GET['action'] : 'default';

$productId = isset($_GET['productId']) ? (int)$_GET['productId'] : null;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;



switch ($action) {
    
    default:
        echo "bienvenide <br><br>";
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



// añadir productos al array de productos productsArr

////////////////////////////////////////////////////////////

// $product = new clsProduct(16, "alcaparra", 1500, 0.75);
// $catalog->registerProduct($product);


// 


// añadir productos al xml catalgoo
////////////////////////////////////////////////////////////

// productRegister(15, 'piña', 1300, 2.00);








/* 
$users = new clsUsers();

// crear productos


// crear usuario
$user1 = new clsUser(123, 'juan', 'spain', 'pass1');

//añadir productos al catalogo


// añadir usuario 
$users->addUser($user1);


$users->showUsers();

*/

// $catalog->exportToXML('xmlDB/catalog.xml');


/////////////////////////////////////////////////////////////////////////////////////

//echo '<a href="tienda.php">Ir a la tienda</a>';

// addToCart(10, 4);

// removeFromCart(5);

// updateCart(12, 10);

// userRegister(3, 'asier', 'chile', '123');

// getCatalog();



// productExists(4, 20);

// viewCart();

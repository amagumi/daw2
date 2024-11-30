<?php
require 'com/cart/clsCart.php';
require 'com/catalog/clsCatalog.php';
require 'com/users/clsUsers.php';

//Users
$user = new User();
$user->register('alex', '12345');

//Catalog
$catalog = new Catalog();
$catalog->addProduct(1, 'PlayStation 4', 100, 250);
$catalog->addProduct(2, 'PlayStation 5', 100, 500);
$catalog->addProduct(3, 'Xbox Series S', 100, 400);
$catalog->addProduct(4, 'Nintendo Switch', 100, 320);
$catalog->showCatalog();

//Cart
$username = 'alex';
$cart = new Cart($username);
$cart->addProduct(1, 'PlayStation4', 1, 250);
$cart->showCart('DESC10');


?>
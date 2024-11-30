<?php

include_once("com/cart/cart.php");
include_once("com/user/users.php");
include_once("com/catalog/catalog.php");


echo "<h1>catalogo</h1>";

echo '<a href="tienda.php">Volver a la tienda</a>' . "<br><br>";
viewCatalog();

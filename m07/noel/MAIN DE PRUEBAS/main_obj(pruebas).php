<?php

    include("com/cart/clsCart.php");
    include("com/catalog/clsCatalog.php");
    include("com/utils/clsUser.php");
    include("com/utils/clsConnection.php");

    $usuario = new clsUser("49909199", "noel", "123");
    $id_user = $usuario->GetId();
    $username = $usuario->GetUsername();
    $usuarioConexion = new clsConnection($id_user, $username);
    $catalogo = new clsCatalog();
    $carrito = new clsCart($catalogo, $id_user);

    // $catalogo->Add(7,"Pruebas",54,"EUR",432);
    // $catalogo->Substract(7, 2);
    // header('Content-Type: text/xml');
    // $carrito->Show();
    // $catalogo->Show();
    
    // $carrito->Modify(3, 5, "indicar");
    // echo $catalogo->ExistProduct(2);
    // $carrito->Show();
    // $carrito->Analyze();

    $carrito->Add("4", 54);
    $carrito->Add("3", 3);
    $carrito->Add("2",1);
    // $carrito->Remove("2");
    $carrito->Show();

?>
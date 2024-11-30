<?php
include_once("com/cart/cart.php");
include_once("com/user/users.php");
include_once("com/connections/connections.php");

$prueba = connected();

if($prueba){
    $function2 = $_GET['function2'];
    $dni = $prueba[0];
    $password = $prueba[1];
    $dni = (string)$dni;
    $password = (string)$password;
    switch($function2){
        case 'AddToCart':
            $id_prod = $_GET['id_prod'];
            $quantity = $_GET['quantity'];
            AddToCart($id_prod,$quantity,$dni,$password);
            break;
        case 'RemoveFromCart':
            $id_prod = $_GET['id_prod'];
            RemoveFromCart($id_prod,$dni,$password);
            break;
        case 'ViewCart':
            $discount = $_GET['discount'];
            ViewCart($dni,$password,$discount);
            break;
        case 'UpdateCart':
            $id_prod = $_GET['id_prod'];
            $quantity = $_GET['quantity'];
            UpdateCart($id_prod,$quantity,$dni,$password);
            break;
        case 'Logout':
            LogOut();
            header("Location: " . $_SERVER['PHP_SELF']);
            break;
        default:
            echo 'Funcion no definida';
        }
}else{
    $function = $_GET['function'];

    switch($function){
        case 'Login':
            $dni = $_GET['dni'];
            $password = $_GET['password'];
            if(AddConnection($dni,$password)){
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }else{
                echo 'El usuario no existe';
            }
            break;
        case 'AddToUser':
            $dni = $_GET['dni'];
            $name = $_GET['name'];
            $last_name = $_GET['last_name'];
            $age = $_GET['age'];
            $password = $_GET['password'];
            AddToUser($dni,$name,$last_name,$age,$password);
            break;
        default:
            echo 'Funcion no definida';
    }
}

//Usuario nuevo
//localhost\MP04\UF2\ExamenPT2\main.php?function=AddToUser&dni=aureT12&name=aure&last_name=perez&age=30&password=aureliano1234
//Logarse
//http://localhost/MP04/UF2/ExamenPT2/main.php?function=Login&dni=aureT12&password=aureliano1234
//AddToCart
//http://localhost/MP04/UF2/ExamenPT2/main.php?function2=AddToCart&id_prod=1&quantity=20
//ViewCart
//http://localhost/MP04/UF2/ExamenPT2/main.php?function2=ViewCart&discount=discount12
//UpdateCart
//http://localhost/MP04/UF2/ExamenPT2/main.php?function2=UpdateCart&id_prod=1&quantity=15
//RemoveFromCart
//http://localhost/MP04/UF2/ExamenPT2/main.php?function2=RemoveFromCart&id_prod=1
//Logout
//http://localhost/MP04/UF2/ExamenPT2/main.php?function2=Logout
?>
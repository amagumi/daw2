<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
function AddToUser($dni, $name, $last_name, $age, $password){
    echo "AddToUser<br>";
    $user = _GetUser();

    foreach($user->children() as $child){
        if($child->dni == $dni){
            echo 'El usuario con este dni ya existe';
            return;
        }
    }

    if (strlen($password) >= 8){
        $item = $user->addChild('user');
        $item->addChild('dni', $dni);
        $item->addChild('name', $name);
        $item->addChild('last_name', $last_name);
        $item->addChild('age', $age);
        $item->addChild('password', $password);

        $user->asXML('xmldb/user.xml');
        echo "Usuario añadido correctamente";
        }else{
            echo 'Usuario no añadido, contraseña con pocos caracteres';
        }
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function UserExist($dni,$password){
    $xml = _GetUser();
    foreach($xml->children() as $child){
        if($child->dni == $dni && $child->password == $password){
            return true;
        }
    }
    return false;
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function LogOut(){
    $xml = simplexml_load_file('xmldb/connections.xml');
    $contador = 0;
    $contador2 = 0;

    foreach($xml->children() as $child){
            $contador = $contador + 1;
    }

    foreach($xml->children() as $child){
        $contador2 = $contador2 + 1;
        if($contador2 == $contador){
            $child->status = 'offline';
            $xml->asXML('xmldb/connections.xml');
        }
    }  

}
////////////////////////////////////////////////////////////////////////////////////////////////////
function _GetUser(){
    if(file_exists('xmldb/user.xml')){
        $user = simplexml_load_file('xmldb/user.xml');
    }else{
        $user = new SimpleXMLElement('<users></users>');
    }
    return $user;
}
?>
<?php
function connected(){
    $xml = simplexml_load_file('xmldb/connections.xml');
    $array = [];
    foreach($xml->children() as $child){
        if($child->status == 'online'){
            array_push($array,$child->dni,$child->password);
            return $array;
        }
    }
    return false;
}
////////////////////////////////////////////////////////////////////////////////////////////////////
function AddConnection($dni,$password){
    $xml = simplexml_load_file('xmldb/user.xml');
    $xml2 = simplexml_load_file('xmldb/connections.xml');

    foreach($xml->children() as $child){
        if($child->dni == $dni && $child->password == $password){
            $connection = $xml2->addChild('connection');
            $connection->addChild('dni',$child->dni);
            $connection->addChild('password',$child->password);
            $connection->addChild('status','online');
            $connection->addChild('date',date('Y-m-d H:i:s'));
            $xml2->asXML('xmldb/connections.xml');
            return true;
        }
    }
    return false;
}
?>
<?php
require_once 'com/cart/clsCart.php';

class clsUser
{

    private int $dni;
    private string $name;
    private string $country;
    private int $password;


    public function __construct(int $dni, string $name, string $country, int $password)
    {
        $this->dni = $dni;
        $this->name = $name;
        $this->country = $country;
        $this->password = $password;
    }

    //getters para obtener cada propiedad del objeto
    public function getDni()
    {
        return $this->dni;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getPassword()
    {
        return $this->password;
    }


    //setters
    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }




    public function verifyPassword($password)
    {
        return $this->password === $password;
    }
}

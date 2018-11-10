<?php
namespace AppBundle\Entity;

class UserRegistration
{
    protected $username;
    protected $email;
    protected $plainPassword;

    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }
}

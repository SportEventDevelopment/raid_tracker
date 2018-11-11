<?php

namespace AppBundle\Entity;

class Raid
{
    // private $id;
    private $nom;
    private $lieu;
    private $date;
    private $edition;
    private $equipe;

    
    // public function setId($id)
    // {
    //     $this->id = $id;
    // }

    // public function getId()
    // {
    //     return $this->id;
    // }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setLieu($lieu)
    {
        $this->lieu = $lieu;
    }

    public function getLieu()
    {
        return $this->lieu;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setEdition($edition)
    {
        $this->edition = $edition;
    }

    public function getEdition()
    {
        return $this->edition;
    }

    public function setEquipe($equipe)
    {
        $this->equipe = $equipe;
    }

    public function getEquipe()
    {
        return $this->equipe;
    }
}


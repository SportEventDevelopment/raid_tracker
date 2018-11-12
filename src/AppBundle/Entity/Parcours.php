<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parcours
 *
 * @ORM\Table(name="parcours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParcoursRepository")
 */
class Parcours
{
    private $idRaid;
    private $idParcoursPere;
    private $nom;
    private $type;
    private $etat;

    public function setIdRaid($idRaid)
    {
        $this->idRaid = $idRaid;
    }

    public function getIdRaid()
    {
        return $this->idRaid;
    }

    public function setIdParcoursPere($idParcoursPere)
    {
        $this->idParcoursPere = $idParcoursPere;
    }

    public function getIdParcoursPere()
    {
        return $this->idParcoursPere;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    public function getEtat()
    {
        return $this->etat;
    }
}


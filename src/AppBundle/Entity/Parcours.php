<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="parcours")
 */
class Parcours
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idParcours;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idRaid;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idParcoursPere;

    /**
     * @ORM\Column(type="string")
     */
    protected $nom;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $etat;

    public function getIdParcours()
    {
        return $this->idParcours;
    }

    public function getIdRaid()
    {
        return $this->idRaid;
    }

    public function getIdParcoursPere()
    {
        return $this->idUser;
    }

    public function getNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }
    
    public function getType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getEtat($etat)
    {
        $this->etat = $etat;
        return $this;
    }



    public function setIdParcours($idParcours)
    {
        $this->idParcours = $idParcours;
        return $this;
    }

    public function setIdRaid($idRaid)
    {
        $this->idRaid = $idRaid;
        return $this;
    }

    public function setIdParcoursPere($idParcoursPere)
    {
        $this->idParcoursPere = $idParcoursPere;
        return $this;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setEtat($etat)
    {
        $this->etat = $etat;
        return $this;
    }
}
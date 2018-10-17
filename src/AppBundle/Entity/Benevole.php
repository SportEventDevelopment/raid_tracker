<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="benevole")
 */
class Benevole
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idBenevole;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idRaid;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idUser;


    public function getIdBenevole()
    {
        return $this->idBenevole;
    }

    public function getIdRaid()
    {
        return $this->idRaid;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdBenevole($nom)
    {
        $this->nom = $nom;
        return $this;
    }
    
    public function setIdRaid($idRaid)
    {
        $this->idRaid = $idRaid;
        return $this;
    }

    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }
}
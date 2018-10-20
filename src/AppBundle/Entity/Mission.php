<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="mission")
 */
class Mission
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idMission;

    /**
     * @ORM\Column(type="integer")
     */
    protected $IdPoste;

    /**
     * @ORM\Column(type="string")
     */
    protected $objectif;


    public function getIdMission()
    {
        return $this->idMission;
    }

    public function getIdPoste()
    {
        return $this->idPoste;
    }

    public function getObjectif()
    {
        return $this->objectif;
    }

    public function setidMission($nom)
    {
        $this->nom = $nom;
        return $this;
    }
    
    public function setIdPoste($idPoste)
    {
        $this->idPoste = $idPoste;
        return $this;
    }

    public function setObjectif($objectif)
    {
        $this->objectif = $objectif;
        return $this;
    }
}
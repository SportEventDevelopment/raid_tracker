<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="repartition")
 */
class Repartition
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idRepartition;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idPoste;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idUser;


    public function getIdRepartition()
    {
        return $this->idRepartition;
    }

    public function getIdPoste()
    {
        return $this->idPoste;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdRepartition($idRepartition)
    {
        $this->idRepartition = $idRepartition;
        return $this;
    }
    
    public function setIdPoste($idPoste)
    {
        $this->idPoste = $idPoste;
        return $this;
    }

    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }
}
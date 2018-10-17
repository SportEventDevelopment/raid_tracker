<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="prefposte")
 */
class Prefposte
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idPrefPoste;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idUser;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idPoste;


    public function getIdPrefPoste()
    {
        return $this->getIdPrefPoste;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function getIdPoste()
    {
        return $this->idPoste;
    }

    public function setIdPrefPoste($idPrefPoste)
    {
        $this->idPrefPoste = $idPrefPoste;
        return $this;
    }
    
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function setIdPoste($idPoste)
    {
        $this->idPoste = $idPoste;
        return $this;
    }
}
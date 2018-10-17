<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="meo")
 */
class Meo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idMeo;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idUser;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idRaid;

  
    public function getIdMeo()
    {
        return $this->idMeo;
    }

    public function getIdRaid()
    {
        return $this->idRaid;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }


    public function setIdMeo($idMeo)
    {
        $this->idMeo = $idMeo;
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
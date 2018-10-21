<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="checkin")
 */
class Checkin
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idCheckin;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idRepartition;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;


    public function getIdCheckin()
    {
        return $this->idCheckin;
    }

    public function getIdRepartition()
    {
        return $this->idRepartition;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setIdCheckin($idChekcin)
    {
        $this->idChekcin = $idChekcin;
        return $this;
    }
    
    public function setIdRepartition($idRepartition)
    {
        $this->idRepartition = $idRepartition;
        return $this;
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }
}
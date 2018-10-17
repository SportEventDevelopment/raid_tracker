<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="trace")
 */
class Trace
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idTrace;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idParcours;


    public function getidTrace()
    {
        return $this->idTrace;
    }

    public function getIdParcours()
    {
        return $this->idParcours;
    }

    public function setidTrace($idTrace)
    {
        $this->idTrace = $idTrace;
        return $this;
    }
    
    public function setIdParcours($idParcours)
    {
        $this->idParcours = $idParcours;
        return $this;
    }
}
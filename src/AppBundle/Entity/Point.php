<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="point")
 */
class Point
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idPoint;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idTrace;

    /**
     * @ORM\Column(type="string")
     */
    protected $lon;


    /**
     * @ORM\Column(type="string")
     */
    protected $lat;


    /** 
     * @ORM\Column(type="integer")
     */
    protected $type;


    public function getIdPoint()
    {
        return $this->idPoint;
    }

    public function getIdTrace()
    {
        return $this->idTrace;
    }

    public function getLon()
    {
        return $this->lon;
    }

    public function getLat()
    {
        return $this->lat;
    }
    
    public function getType()
    {
        return $this->type;
    }


    public function setIdPoint($idPoint)
    {
        $this->idPoint = $idPoint;
        return $this;
    }

    public function setIdTrace($idTrace)
    {
        $this->idTrace = $idTrace;
        return $this;
    }

    public function setLon($lon)
    {
        $this->lon = $lon;
        return $this;
    }

    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="poste")
 */
class Poste
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idPoste;

    /**
     * @ORM\Column(type="integer")
     */
    protected $idPoint;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="integer")
     */
    protected $nombre;

    /**
     * @ORM\Column(type="date")
     */
    protected $heureDebut;

    /**
     * @ORM\Column(type="date")
     */
    protected $heureFin;


    public function getIdPoste()
    {
        return $this->idPoste;
    }

    public function getIdPoint()
    {
        return $this->idPoint;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getHeureDebut()
    {
        return $this->heureDebut;
    }

    public function getHeureFin()
    {
        return $this->heureFin;
    }

    public function setIdPoste($idPoste)
    {
        $this->idPoste = $idPoste;
        return $this;
    }
    
    public function setIdPoint($idPoint)
    {
        $this->idPoint = $idPoint;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function setHeureDebut($heureDebut)
    {
        $this->heureDebut = $heureDebut;
        return $this;
    }

    public function setHeureFin($heureFin)
    {
        $this->heureFin = $heureFin;
        return $this;
    }
}
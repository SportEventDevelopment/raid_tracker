<?php
namespace AppBundle\Entity;

class Trace
{
    private $id;
    private $idTrace;
    private $idParcours;

    
    public function getId()
    {
        return $this->id;
    }

    public function getIdTrace()
    {
        return $this->idTrace;
    }

    public function getIdParcours()
    {
        return $this->idParcours;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIdTrace($idTrace)
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
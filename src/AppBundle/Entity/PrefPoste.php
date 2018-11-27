<?php

namespace AppBundle\Entity;

class PrefPoste
{
    private $id;
    private $idPoste;
    private $idBenevole;

    /**
     * Set id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idBenevole
     */
    public function setIdBenevole($idBenevole)
    {
        $this->idBenevole = $idBenevole;
    }

    /**
     * Get idBenevole
     */
    public function getIdBenevole()
    {
        return $this->idBenevole;
    }

    /**
     * Set idPoste
     */
    public function setIdPoste($idPoste)
    {
        $this->idPoste = $idPoste;
    }

    /**
     * Get idPoste
     */
    public function getIdPoste()
    {
        return $this->idPoste;
    }
}

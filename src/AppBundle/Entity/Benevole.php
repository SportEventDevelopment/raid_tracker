<?php

namespace AppBundle\Entity;

class Benevole
{
    private $id;
    private $idUser;
    private $idRaid;
    
    /**
     * Set id
     *
     * @param integer $id
     *
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return Benevole
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idRaid
     *
     * @param integer $idRaid
     *
     * @return Benevole
     */
    public function setIdRaid($idRaid)
    {
        $this->idRaid = $idRaid;

        return $this;
    }

    /**
     * Get idRaid
     *
     * @return int
     */
    public function getIdRaid()
    {
        return $this->idRaid;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Benevole
 *
 * @ORM\Table(name="benevole")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BenevoleRepository")
 */
class Benevole
{


  const BENE_ZERO = 0;
  const BENE_UN = 1;

  const BENE_ZERO_STR = 'Assistant';
  const BENE_UN_STR = 'Distrib Eau';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idUser", type="integer")
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="poste", type="integer")
     */
    private $poste;

    /**
     * @var int
     *
     * @ORM\Column(name="idRaid", type="integer")
     */
    private $idRaid;


    public static $tabPoste = array(
        self::BENE_ZERO => self::BENE_ZERO_STR,
    		self::BENE_UN => self::BENE_UN_STR,
    );

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


    /**
     * Set poste
     *
     * @param integer $poste
     *
     * @return Benevole
     */
    public function setPoste($poste)
    {
        $this->poste = $poste;

        return $this;
    }

    /**
     * Get poste
     *
     * @return int
     */
    public function getPoste()
    {
        return $this->poste;
    }
}

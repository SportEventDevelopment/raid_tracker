<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrefPoste
 *
 * @ORM\Table(name="pref_poste")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrefPosteRepository")
 */
class PrefPoste
{
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
     * @var int
     *
     * @ORM\Column(name="idRaid", type="integer")
     */
    private $idRaid;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Poste", inversedBy="prefpostes")
     * @ORM\JoinColumn(name="poste_id", referencedColumnName="id")
     **/
    private $poste;

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
     * @return PrefPoste
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
     * Set idUser
     *
     * @param integer $idRaid
     *
     * @return PrefPoste
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
 * @param \AppBundle\Entity\Poste $poste
 *
 * @return PrefPoste
 */
public function setPoste(\AppBundle\Entity\Poste $poste = null)
{
    $this->poste = $poste;

    return $this;
}

/**
 * Get poste
 *
 * @return \AppBundle\Entity\Poste
 */
public function getPoste()
{
    return $this->poste;
}

/**
*
* @return string
*/
public function __toString()
{
return  $this->getPoste();
}
}

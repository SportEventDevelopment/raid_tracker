<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="raid")
 */
class Raid
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idRaid;

    /**
     * @ORM\Column(type="string")
     */
    protected $nom;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="string")
     */
    protected $lieu;

    /**
     * @ORM\Column(type="integer")
     */
    protected $edition;

    /**
     * @ORM\Column(type="string")
     */
    protected $equipeOrganisatrice;

    public function getIdRaid()
    {
        return $this->idRaid;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getLieu()
    {
        return $this->lieu;
    }

    public function getEdition()
    {
        return $this->edition;
    }

    public function getEquipeOrganisatrice()
    {
        return $this->equipeOrganisatrice;
    }

    public function setIdRaid($idRaid)
    {
        $this->idRaid = $idRaid;
        return $this;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }
    
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function setEdition($edition)
    {
        $this->edition = $edition;
        return $this;
    }

    public function setEquipeOrganisatrice($equipeOrganisatrice)
    {
        $this->equipeOrganisatrice = $equipeOrganisatrice;
        return $this;
    }
}
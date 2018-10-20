<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Raid
 *
 * @ORM\Table(name="raid")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaidRepository")
 */
class Raid
{
  //* @UniqueEntity("nom",message="Le nom du raid existe déjà."


  const SPORT_ZERO = 0;
  const SPORT_UN = 1;
  const SPORT_DEUX = 2;
  const SPORT_TROIS = 3;


	const SPORT_ZERO_STR = 'Natation';
  const SPORT_UN_STR = 'Course à pieds';
  const SPORT_DEUX_STR = 'Vélo';
  const SPORT_TROIS_STR = 'Kayak';



  public static $tabTypeSport = array(
		self::SPORT_ZERO => self::SPORT_ZERO_STR,
		self::SPORT_UN => self::SPORT_UN_STR,
    self::SPORT_DEUX => self::SPORT_DEUX_STR,
    self::SPORT_TROIS => self::SPORT_TROIS_STR,

	);

const SURFACE_ZERO = 0;
const SURFACE_UN = 1;


const SURFACE_ZERO_STR = 'Eau';
const SURFACE_UN_STR = 'Terre';

public static $tabTypeSurface = array(
  self::SURFACE_ZERO => self::SURFACE_ZERO_STR,
  self::SURFACE_UN => self::SURFACE_UN_STR,
);



    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="lieu", type="string", length=255)
     */
    private $lieu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRaid", type="date")
     */
    private $dateRaid;

    /**
     * @var string
     *
     * @ORM\Column(name="edition", type="string", length=10)
     */
    private $edition;

    /**
     * @var int
     *
     * @ORM\Column(name="type_sport", type="integer")
     */
    private $typeSport;

    /**
     * @var int
     *
     * @ORM\Column(name="nbr_sport", type="integer")
     */
    private $nbrSport;

    /**
     * @var int
     *
     * @ORM\Column(name="type_surface", type="integer")
     */
    private $typeSurface;

    /**
     * @var string
     *
     * @ORM\Column(name="equipe", type="string", length=255)
     */
    private $equipe;

    /**
      * Constructor
      */
     public function __construct()
     {
     }

    /**
   * Get chaine typeSport
   *
   * @return string
   */
  public function getChaineTypeSport()
  {
    return self::$tabTypeSport[$this->typeSport];
  }

    /**
    * Get chaine typeSurface
    *
    * @return string
    */
    public function getChaineTypeSurface()
    {
    return self::$tabTypeSurface[$this->typeSurface];
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Raid
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set lieu
     *
     * @param string $lieu
     *
     * @return Raid
     */
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu
     *
     * @return string
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * Set dateRaid
     *
     * @param \DateTime $dateRaid
     *
     * @return Raid
     */
    public function setDateRaid($dateRaid)
    {
        $this->dateRaid = $dateRaid;

        return $this;
    }

    /**
     * Get dateRaid
     *
     * @return \DateTime
     */
    public function getDateRaid()
    {
        return $this->dateRaid;
    }

    /**
     * Set edition
     *
     * @param string $edition
     *
     * @return Raid
     */
    public function setEdition($edition)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * Set typeSport
     *
     * @param integer $typeSport
     *
     * @return Raid
     */
    public function setTypeSport($typeSport)
    {
        $this->typeSport = $typeSport;

        return $this;
    }

    /**
     * Get typeSport
     *
     * @return int
     */
    public function getTypeSport()
    {
        return $this->typeSport;
    }

    /**
     * Set nbrSport
     *
     * @param integer $nbrSport
     *
     * @return Raid
     */
    public function setNbrSport($nbrSport)
    {
        $this->nbrSport = $nbrSport;

        return $this;
    }

    /**
     * Get nbrSport
     *
     * @return int
     */
    public function getNbrSport()
    {
        return $this->nbrSport;
    }

    /**
     * Set typeSurface
     *
     * @param string $typeSurface
     *
     * @return Raid
     */
    public function setTypeSurface($typeSurface)
    {
        $this->typeSurface = $typeSurface;

        return $this;
    }

    /**
     * Get typeSurface
     *
     * @return string
     */
    public function getTypeSurface()
    {
        return $this->typeSurface;
    }

    /**
     * Set equipe
     *
     * @param string $equipe
     *
     * @return Raid
     */
    public function setEquipe($equipe)
    {
        $this->equipe = $equipe;

        return $this;
    }

    /**
     * Get equipe
     *
     * @return string
     */
    public function getEquipe()
    {
        return $this->equipe;
    }
}

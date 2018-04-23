<?php

namespace ML\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="ML\BilletterieBundle\Repository\CommandeRepository")
 */
class Commande
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateVisite", type="datetime")
     */
    private $dateVisite;

    /**
     * @var int
     *
     * @ORM\Column(name="qteBillet", type="integer")
     */
    private $qteBillet;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
	 * @Assert\Email(message = "L'E-mail '{{ value }}' n'est pas valide.",
     *     checkMX = true)
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="paiement", type="integer")
     *
     */
    private $paiement;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float")
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="refCommande", type="string", length=255, unique=true)
     */
    private $refCommande;

	/**
     * @var string
     *
     * @ORM\Column(name="billet_jour", type="string", length=255)
     *
     */
    private $billetJour;
	
    /**
     * @ORM\OneToMany(targetEntity="ML\BilletterieBundle\Entity\Billet", mappedBy="commande", cascade={"merge"})
     * @Assert\Valid()
     */
    private $billets;


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
     * Set dateVisite
     *
     * @param \DateTime $dateVisite
     *
     * @return Commande
     */
    public function setDateVisite($dateVisite)
    {
        $this->dateVisite = $dateVisite;

        return $this;
    }

    /**
     * Get dateVisite
     *
     * @return \DateTime
     */
    public function getDateVisite()
    {
        return $this->dateVisite;
    }

    /**
     * Set qteBillet
     *
     * @param integer $qteBillet
     *
     * @return Commande
     */
    public function setQteBillet($qteBillet)
    {
        $this->qteBillet = $qteBillet;

        return $this;
    }

    /**
     * Get qteBillet
     *
     * @return int
     */
    public function getQteBillet()
    {
        return $this->qteBillet;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Commande
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set paiement
     *
     * @param integer $paiement
     *
     */
    public function setPaiement($paiement)
    {
        $this->paiement = $paiement;

        return $this;
    }

    /**
     * Get paiement
     *
     * @return int
     */
    public function getPaiement()
    {
        return $this->paiement;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return Commande
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set refCommande
     *
     * @param string $refCommande
     *
     * @return Commande
     */
    public function setRefCommande($refCommande)
    {
        $this->refCommande = $refCommande;

        return $this;
    }

    /**
     * Get refCommande
     *
     * @return string
     */
    public function getRefCommande()
    {
        return $this->refCommande;
    }

	/**
     * Set typeBillet
     *
     * @param string $billetJour
     *
     * @return Commande
     */
    public function setBilletJour($billetJour)
    {
        $this->billetJour = $billetJour;

        return $this;
    }

    /**
     * Get billetJour
     *
     * @return string
     */
    public function getBilletJour()
    {
        return $this->billetJour;
    }
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->billets = new ArrayCollection();
    }

    /**
     * Add billet
     *
     * @param \ML\BilletterieBundle\Entity\Billet $billet
     *
     * @return Commande
     */
    public function addBillet(Billet $billet)
    {
        $this->billets[] = $billet;

        return $this;
    }

    /**
     * Remove billet
     *
     * @param \ML\BilletterieBundle\Entity\Billet $billet
     */
    public function removeBillet(Billet $billet)
    {
        $this->billets->removeElement($billet);
    }

    /**
     * Get billets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getbillets()
    {
        return $this->billets;
    }
}

<?php
// src/ML/BilletterieBundle/Services/MLStripe.php

namespace ML\BilletterieBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use ML\BilletterieBundle\Entity\Commande;
use Stripe\Charge;
use Stripe\Error\Card;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Session\Session;

class MLStripe
{
    private $session;
    private $em;
    private $mailConfirmation;

    public function __construct(Session $session, EntityManagerInterface $em, MLMail $mailConfirmation)
    {
        $this->session = $session;
        $this->em =$em;
        $this->mailConfirmation = $mailConfirmation;
    }
	
	//------------------------------
	//Test du retour de Stripe
	//------------------------------
    public function CommandePaiement($amount, $refCommande, $stripeToken, Commande $commande, Array $tableauRecapitulatif)
    {
        Stripe::setApiKey("sk_test_lgludu4sebbQFdDsLi63RQcH");

        $token = $stripeToken;
		
		
        try {
            $charge = Charge::create(array(
                "amount" => $amount, 
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - Réservation ".$refCommande
            ));

            //Validation du paiement, envoi du mail + flush
			//---------------------------------------------
            $commande->setPaiement(1);
			
            $this->mailConfirmation->mailConfirmation($commande, $tableauRecapitulatif);
            
            $this->em->merge($commande);
			
            $this->em->flush();
			
            //Retourne le FlashBag success
			//------------------------------
            $this->session->getFlashBag()->add("success","Paiement effectué avec succès ");
			

		//Pas de validation de paiement -> Retourne le FlashBag error
		//-------------------------------------------------------------
        } catch(\Stripe\Error\Card $e) {
            $commande->setPaiement(0);
			
			$this->session->getFlashBag()->add("erreur","Une erreur s'est produite lors du paiement");
            $this->session->set('erreur',true);
			
        }
    }
}
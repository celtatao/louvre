<?php
// src/ML/BilletterieBundle/Services/MLTabBillets.php

namespace ML\BilletterieBundle\Services;

use ML\BilletterieBundle\Entity\Commande;
use Symfony\Component\HttpFoundation\Session\Session;

class MLTabBillets
{
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

	//----------------------------------------------------------
	//Elaboration du tableau récapitulatif des billets commandés
	//----------------------------------------------------------
    public function tabbillets(Commande $commande=NULL)
    {
        if(!isset($commande))
        {
            $commande = $this->session->get('commande');
        }

        $listeBillets = $commande->getBillets();

        $tableauBillets = [];

        foreach($listeBillets as $key=>$billet)
        {
            $tableauBillets[$key]=array('nom'=>$billet->getNom(),
                                              'prenom'=>$billet->getPrenom(),
                                              'type'=>$billet->getCatBillet(),
                                              'prix'=>$billet->getPrix(),
            );
        }
        return $tableauBillets;
    }
}
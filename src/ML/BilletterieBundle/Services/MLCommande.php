<?php
// src/ML/BilletterieBundle/Services/MLCommande.php

namespace ML\BilletterieBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use ML\BilletterieBundle\Entity\Commande;

class MLCommande
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

	//------------------------------
	//Enregistrement de la commande
	//------------------------------
    public function enregistrer(Commande $commande)
    {
        //récupération de la date du jour
		//-------------------------------
        $aujourdhui = new \DateTime();
        
        if($commande->getBilletJour() === 'demi-journée')
        {
            $billetJour = 'demi-journée';
        }else
        {
            $billetJour = 'journée';
        }

        //Création de la référence commande basés sur Nmr, la date et un nombre aléatoire
		//--------------------------------------------------------------------------------
        $rand = uniqid(rand());
		$refCommande = 'Nmr'. date_format($aujourdhui,'Y').
						''.date_format($aujourdhui,'m').
						''.date_format($aujourdhui,'d').
						''.$rand.'';
						
        $commande->setRefCommande($refCommande);

        $nbreBillet =0;
        $total =0;

        //Détermination du prix de chaque billet
		//--------------------------------------
        foreach($commande->getBillets() as $billet)
        {
            $naissance = $billet ->getDateNaissance();
            $age = $aujourdhui->diff($naissance)->y;

            switch ($billetJour) {
                case ($billetJour==='demi-journée') : {
                    if( $billet->getReduction() === true && $age >=12)
                    {
                        $billet->setPrix(5);
                        $billet->setCatBillet('Demi-journée tarif réduit');
                    } else {
                        switch ($age) {
							case ($age >0 && $age < 4) :
								$billet->setPrix(0);
								$billet->setCatBillet('Tarif moins de 4 ans');
								break;
                            case ($age >=4 && $age < 12) :
                                $billet->setPrix(4);
                                $billet->setCatBillet('Demi-journée tarif enfant');
                                break;
                            case ($age >=12 && $age < 60) :
                                $billet->setPrix(8);
                                $billet->setCatBillet('Demi-journée tarif normal');
                                break;
                            case ($age >=60) :
                                $billet->setPrix(6);
                                $billet->setCatBillet('Demi-journée tarif sénior');
                                break;
                        }
					}
                    }break;
					
                case($billetJour==='journée') :{
                        if ($billet->getReduction() === true && $age >=12){
                            $billet->setPrix(10);
                            $billet->setCatBillet('Tarif réduit');
                        } else
                        {
                            switch ($age) {
								case ($age >0 && $age < 4) :
									$billet->setPrix(0);
									$billet->setCatBillet('Tarif moins de 4 ans');
									break;
                                case ($age >=4 && $age <12) :
                                    $billet->setPrix(8);
                                    $billet->setCatBillet('Tarif enfant');
                                    break;
                                case ($age >=12 && $age <60) :
                                    $billet->setPrix(16);
                                    $billet->setCatBillet('Tarif normal');
                                    break;
                                case ($age >=60) :
                                    $billet->setPrix(12);
                                    $billet->setCatBillet('Tarif sénior');
                                    break;
                            }
                        }
                }break;
            }
            $billet->setCommande($commande);
            $nbreBillet++;
			
            //Calcul du prix total
			//--------------------
            $prix = $billet->getPrix();
            $total += $prix;
        }
        $commande->setQteBillet($nbreBillet);
        $commande->setTotal($total);
    }
}
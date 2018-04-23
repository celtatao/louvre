<?php
// src/ML/BilletterieBundle/Controller/BilletterieController.php

namespace ML\BilletterieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ML\BilletterieBundle\Entity\Commande;
use ML\BilletterieBundle\Form\CommandeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BilletterieController extends Controller
{
	//----------------------------------------------------------------
    // Formulaire de sélection des billets et création de la commande
    //----------------------------------------------------------------
    public function indexAction(Request $request)
    {
        //Récupération d'une éventuelle commande en cours
		//-----------------------------------------------
        if($this->get('session')->has('commande'))
        {
            $session = $this->get('session');
            $commande = $session->get('commande');
        }else
        {
            //Sinon création d'une nouvelle commande
			//--------------------------------------
            $commande = new Commande();
        }
		
		//Création du formulaire CommandeType
		//-----------------------------------
        $form = $this->get('form.factory')->create(CommandeType::class, $commande);
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $commandeService= $this->container->get('ml_billetterie.mlcommande');
            $commandeService->enregistrer($commande);
            $session = $this->get('session');
            $session->set('commande', $commande);
            $commande->setPaiement(0);
            return $this->redirectToRoute('ml_billetterie_paiement', array('id' => $commande->getId()));
        }
		//Retourne la vue
		//-----------------------------------
		return $this->render('MLBilletterieBundle:Billetterie:billetterie.html.twig', array(
            'form' => $form->createView(),
			));
    }
	
	//-----------------------------------------------
    // Contrôle des disponibilités par date de visite
    //-----------------------------------------------
    public function dispoAction(Request $req)
    {
        if($req->isXmlHttpRequest()) {
            $dateVisite = $req->get('dateVisite');
            $qteBillet = $req->get('qteBillet');
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('MLBilletterieBundle:Commande');
			
            //Récupération le nombre total de tickets réservés présents dans la base pour cette date
			//--------------------------------------------------------------------------------------
            $qteBilletBdd=$repository->getQteBilletParDate($dateVisite);
            						
			//Récupération du nombre de billets souhaités
			//-------------------------------------------
            $totalBillets=$qteBilletBdd+$qteBillet;
			
			//Vérification si on ne dépasse pas 1000 billets/jour
			//---------------------------------------------------
            if ($totalBillets > 1000) {
                $reponse = "<div class=\"col-sm-12 alert alert-danger messageCheck\" style='margin-top : 10px;'> 
                Désolé, il ne reste plus que ".(1000-$qteBilletBdd)." billets disponibles pour cette journée. 
                Merci de choisir une autre date. </div>";
                return new JsonResponse(array('reponse' => json_encode($reponse), 'limit' => true));
            } else {
                $reponse = "<div class=\"col-sm-12 alert alert-success messageCheck\" style='margin-top : 10px;'>Il reste " . (1000-$qteBilletBdd) . " billets disponibles pour cette journée, vous pouvez  poursuivre votre commande.</div>";
                return new JsonResponse(array('reponse' => json_encode($reponse), 'limit' => false));
            }
        }
        return new Response("Erreur : Erreur requête Ajax");
    }
	
	//-----------------------------------
    // Paiement de la commande par STRIPE
    //-----------------------------------
	public function paiementAction(Request $request)
    {	
				
		// Récupération de la commande enregistrée en session
		//---------------------------------------------------
        $session = $this->get('session');
        $commande = $session->get('commande');
        
		
        //Gestion de l'erreur (déjà payée ou inexistante)
		//-----------------------------------------------
        if (null === $commande || $commande->getPaiement()=== 1) {
			$this->get('session')->getFlashBag()->has('info');
			$this->addFlash("info", "La commande est inexistante ou déjà payée.");
			return $this->redirectToRoute('ml_billetterie_homepage');
		}

        //Création du tableau des billets
		//-------------------------------
        $tableauBillets = $this->container->get('ml_billetterie.mltabbillets')->tabbillets();

        //Retourne la vue du paiement
		//----------------------------
        $content = $this->get('templating')->render('MLBilletterieBundle:Paiement:index.html.twig',array(
            'tableauBillets'=>$tableauBillets,
            'commande'=>$commande,
        ));
		
		//Elaboration du service Stripe
		//-----------------------------
        if ($request->isMethod('POST'))
        {
            $paiementService= $this->container->get('ml_billetterie.mlstripe');
            $paiementService->commandePaiement($commande->getTotal()*100,$commande->getRefCommande(),$_POST['stripeToken'],$commande,$tableauBillets);
            $em = $this->getDoctrine()->getManager();
            return $this->redirectToRoute('ml_billetterie_confirmation', array('id' => $commande->getRefCommande()));
        }
        return new Response($content);
    }
	
	//-----------------------------------------------
    // Confirmation de paiement et de l'envoi du mail
    //-----------------------------------------------
	public function confirmationAction($id)
    {
        
		//En cas de non paiement retour au formuaire puis recréation Flash
		//----------------------------------------------------------------
        if($this->get('session')->getFlashBag()->has('erreur'))
        {
			$this->get('session')->getFlashBag()->clear();
			$this->addFlash("info", "Il y a eu un problème de paiement - veuillez recommencer");
			return $this->redirectToRoute('ml_billetterie_homepage');
        }
		
		//Récupération de la commande en BDD
		//----------------------------------
		$em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('MLBilletterieBundle:Commande')->findOneBy(array('refCommande'=>$id));
			
		//Création du tableau des billets avec message succès
		//---------------------------------------------------
        $tableauBillets = $this->container->get('ml_billetterie.mltabbillets')->tabbillets($commande);
		$this->addFlash("success","Paiement effectué avec succès ");
        
        //Retourne la vue confirmation
		//----------------------------
        $content = $this->get('templating')->render('MLBilletterieBundle:Confirmation:index.html.twig',array(
            'tableauBillets'=>$tableauBillets,
            'commande'=>$commande,
		$this->get('session')->clear(),
        ));
        return new Response($content);
    }
}

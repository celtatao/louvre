<?php
// src/ML/BilletterieBundle/Services/MLMail.php

namespace ML\BilletterieBundle\Services;

use ML\BilletterieBundle\Entity\Commande;

class MLMail
{
    private $mailer;
	
	//---------------------------------
	//Création objet mailer et template
	//---------------------------------
    public function __construct($mailer, $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

	//----------------------------
	//Elaboration et envoi du mail
	//----------------------------
    public function mailConfirmation(Commande $commande, $tableauBillets)
    {
        $transport = \Swift_MailTransport::newInstance();
                 
        $mailer = \Swift_Mailer::newInstance($transport);
		
		$message = \Swift_Message::newInstance()
            ->setSubject('Vos billets pour le musée du Louvre n° '. $commande->getRefCommande())
            ->setFrom(array('celtatao@celtatao.fr'=>"Billeterie du Musée du Louvre"))
            ->setTo($commande->getEmail())
            ->setCharset('utf-8')
            ->setContentType('text/html');

        $message->setBody($this->templating->render(
			'Email/index.html.twig', array(
            'commande'=>$commande,
            'tableauBillets'=> $tableauBillets)));

        $this->mailer->send($message);
		
    }
}
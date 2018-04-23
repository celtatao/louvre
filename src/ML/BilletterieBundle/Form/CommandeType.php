<?php
// src/ML/BilletterieBundle/Form/CommandeType.php

namespace ML\BilletterieBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
		
			->add('qteBillet',IntegerType::class, array(
                'attr'=> array(
					'min'=>1,
					'max'=>1000,
					'class'=>"qteBillet"
                )
            ))
			
            ->add('dateVisite',      DateType::class, array(
                'attr' => array(
					'readonly' => true,
                    'onfocus'=>"this.blur()",
                    'style'=> "background:white;",
                    'class'=>"datepicker;"),
					'widget' => 'single_text',
					'html5' => false,
					'format' => 'dd/MM/yyyy',
            ))
			
            ->add('email',     EmailType::class)
			
            ->add('billetJour', ChoiceType::class, array(
                'choices'  => array(
					'Demi-journée' => 'demi-journée',
					'Journée' => 'journée',
                ),
            ))
			
            ->add('billets', CollectionType::class, array(
                'entry_type'   => BilletType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype' => true
            ))
			
            ->add('save', SubmitType::class,  array(
                'attr' => array('class' => 'btn btn-primary btn-block btn-lg'),
                'label' =>'Réserver'
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ML\BilletterieBundle\Entity\Commande'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ml_billetteriebundle_commande';
    }


}

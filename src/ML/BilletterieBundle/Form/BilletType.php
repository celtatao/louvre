<?php
// src/ML/BilletterieBundle/Form/BilletType.php

namespace ML\BilletterieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilletType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('nom', TextType::class, array(
                'label' => 'Nom',
            ))
            ->add('prenom', TextType::class, array(
                'label' => 'Prénom',
            ))
            ->add('pays', CountryType::class, array(
                'label' => 'Pays',
                'preferred_choices' => array('FR'),
            ))
            ->add('dateNaissance', BirthdayType::class, array(
                'label' => 'Date de naissance',
                'attr' => ['class' => 'date_de_naissance'],
                'format' => 'dd-MM-yyyy',
                'placeholder' => array(
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ))
            )
            ->add('reduction', CheckboxType::class, array(
                'label'    => 'Tarif Réduit (étudiants, employés du musée ou d’un service du Ministère de la Culture et militaires)',
                'required' => false,
				'attr'     => ['class' => 'reduction-info'],
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ML\BilletterieBundle\Entity\Billet'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ml_billetteriebundle_billet';
    }


}

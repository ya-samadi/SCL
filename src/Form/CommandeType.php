<?php

namespace App\Form;

use App\Entity\Commande;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommandeType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('livraisonDate', DateType::class, $this->getConfiguration("Date de livraison souhaiter", "La date à laquelle vous seriez besoin de ce produit", ["widget" => "single_text"]))
            ->add('quantity', IntegerType::class, $this->getConfiguration('Nombre de produits', 'Le nombre de produits souhaiter'))
            ->add('comment', TextareaType::class, $this->getConfiguration("Commentaire", "Si vous avez un commentaire, n'hésitez pas à en faire part !", ["required" => false ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}

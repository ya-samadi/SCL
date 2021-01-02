<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Titre', 'Taper votre titre de produit'))
            ->add('slug', TextType::class, $this->getConfiguration('Chaine URL', 'Adresse web (automatique)', ['required' => false]))
            ->add('coverImage', UrlType::class, $this->getConfiguration('Url de l\'image principale', 'Donnez l\'addresse d\'une image'))
            ->add('description', TextareaType::class, $this->getConfiguration('Description', 'Donnez une description pour votre produit'))
            ->add('quantity', IntegerType::class, $this->getConfiguration('Nombre de produits', 'Le nombre de produits disponible au stock'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix par unit', 'Indiquez le prix que vous voulez pour chaque produit'))
            ->add('images', CollectionType::class, [
                'entry_type' => ImgaeType::class,
                'allow_add' => true,
                'allow_delete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

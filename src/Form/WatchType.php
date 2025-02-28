<?php

namespace App\Form;

use App\Entity\Watch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class WatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $watch = $builder->getData();

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la montre'
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('picture', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'mapped' => false
            ])
        ;

        // Ajouter le champ référence uniquement pour les montres de la boutique
        if (!$watch->getAuthor()) {
            $builder->add('reference', TextType::class, [
                'label' => 'Référence'
            ]);
        }

        // Ajouter le champ état uniquement pour les montres de particuliers
        if ($watch && $watch->getAuthor()) {
            $builder->add('state', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Neuf' => 'Neuf',
                    'Très bon état' => 'Très bon état',
                    'Bon état' => 'Bon état',
                    'État moyen' => 'État moyen'
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Watch::class,
        ]);
    }
}

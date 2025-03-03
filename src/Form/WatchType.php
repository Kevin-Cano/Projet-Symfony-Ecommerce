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
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class WatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $watch = $builder->getData();

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la montre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom pour votre montre',
                    ]),
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prix pour votre montre',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une description pour votre montre',
                    ]),
                ],
            ])
            ->add('bracelet', ChoiceType::class, [
                'label' => 'Type de bracelet',
                'choices' => [
                    'Cuir' => 'Cuir',
                    'Métal/Acier' => 'Métal/Acier',
                    'Caoutchouc' => 'Caoutchouc',
                    'Tissu/NATO' => 'Tissu/NATO',
                    'Céramique' => 'Céramique',
                    'Or' => 'Or',
                    'Titane' => 'Titane',
                    'Autre' => 'Autre',
                ],
                'placeholder' => 'Sélectionnez le type',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner le type de bracelet',
                    ]),
                ],
            ])
            ->add('picture', FileType::class, [
                'label' => 'Image',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, WEBP)',
                    ])
                ],
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
                    'Neuf avec étiquettes' => 'Neuf avec étiquettes',
                    'Comme neuf' => 'Comme neuf',
                    'Excellent' => 'Excellent',
                    'Bon' => 'Bon',
                    'Acceptable' => 'Acceptable',
                    'Vintage' => 'Vintage'
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

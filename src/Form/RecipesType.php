<?php

namespace App\Form;

use App\Entity\Recipes;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RecipesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                "label"=>false,
                "attr"=>[
                    "placeholder" => "Titre",
                    "class"=>"recipes_title"
                ]
            ])
            ->add('content', TextType::class,[
                "label"=>false,
                "attr"=>[
                    "placeholder" => "Contenu",
                    "class"=>"recipes_content"
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => 'Image',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => "Vous devez rentrer une image valide",
                    ])
                ],
                "attr"=>[
                    "class"=>"recipes_img"
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_value' => 'id',
                'choice_label' => function (Category $category) {
                    return $category->getName();
                },
                "attr"=>[
                    "class"=>"recipes_category"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipes::class,
        ]);
    }
}

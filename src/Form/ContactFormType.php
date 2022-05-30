<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class,[
                "label"=>false,
                "attr"=>[
                    "placeholder" => "Email",
                    "class"=>"email_contact"
                ]
            ])
            ->add('content', TextType::class, [
                "label"=>false,
                "attr"=>[
                    "placeholder" => "Message",
                    "class"=>"message_contact"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}

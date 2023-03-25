<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CreateProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add( 'category', EntityType::class,array('class'=>'App\Entity\Category','choice_label'=>"catName"))
            ->add( 'author', EntityType::class,array('class'=>'App\Entity\Author','choice_label'=>"AuthorName"))
            ->add( 'tag', EntityType::class,array('class'=>'App\Entity\Tag','choice_label'=>"TagName"))

//            ->add( 'review', EntityType::class,array('class'=>'App\Entity\Review','choice_label'=>"ReviewContent"))

            ->add('ProductName', TextType::class)
            ->add('NumberChapter', TextType::class)


            ->add('ProductDate',DateType::class,['widget'=>'single_text'])
            ->add('ProductImages',FileType::class,[
                'label'=>'Image file',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new File([
                        'maxSize'=>'1024k',
                        'mimeTypesMessage'=>'Please upload a valid image',
                    ])
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

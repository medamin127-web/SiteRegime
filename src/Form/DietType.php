<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Category;



class DietType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('diet_name', TextType::class, [
                'label' => 'Diet Name',
                'attr' => [
                    'placeholder' => 'Enter Diet Name',
                ],
            ])
            ->add('duration', TextType::class, [
                'label' => 'Duration (Per Week)',
                'attr' => [
                    'placeholder' => 'Enter Duration',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Enter Description',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'All' => 'all',
                    'Vegan' => 'vegan',
                    'No Dairy' => 'no_dairy',
                    'Gluten-Free' => 'gluten_free',
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => $options['categories'],
                'choice_label' => 'category_name',
                'choice_value' => function (Category $category = null) {
                    return $category ? $category->getId() : '';
                },
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'mapped' => false, // Tell Symfony to ignore this field in the entity
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Diet',
            'categories' => [], // Pass categories as an option
        ]);
    }
}
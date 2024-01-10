<?php
namespace App\Form;

use App\Entity\Plat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomplat', TextType::class, [
                'label' => 'Meal Name',
                'required' => true,
            ])
            ->add('nbrCalories', NumberType::class, [
                'label' => 'Number of Calories',
                'required' => true,
            ])
            ->add('ingredients', TextType::class, [
                'label' => 'Ingredients',
                'required' => true,
            ])
            ->add('cout', NumberType::class, [
                'label' => 'Cost (DT)',
                'required' => true,
                'scale' => 2,
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
            'data_class' => Plat::class,
        ]);
    }
}
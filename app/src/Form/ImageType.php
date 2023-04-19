<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public const SCALES = [
        'XS 100x100' => 100,
        'S 200x200' => 200,
        'M 250x250' => 250,
        'L 300x300' => 300,
        'XL 500x500' => 500,
    ];

    public const TYPES = [
        'jpeg' => 'jpeg',
        'png' => 'png',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename', FileType::class, [
                'label' => 'Image file',
                'mapped' => false,
                'required' => false,
                'constraints' => [new \Symfony\Component\Validator\Constraints\Image()],
            ])
            ->add('scale', ChoiceType::class, [
                'mapped' => false,
                'choices' => self::SCALES,
            ])
            ->add('type', ChoiceType::class, [
                'mapped' => false,
                'choices' => self::TYPES,
            ])
            ->add('save', SubmitType::class, ['label' => 'Save file']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}

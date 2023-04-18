<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageConvertType extends AbstractType
{

    public const SCALES = [
        '100x100 xs' => 'thumbnail_xs',
        '250x250 xs' => 'thumbnail_m',
    ];

    public const TYPES = [
        'jpeg' => 'jpeg',
        'png' => 'png',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('scale', ChoiceType::class, [
                'choices' => self::SCALES,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => self::TYPES,
            ])
            ->add('save', SubmitType::class, ['label' => 'Save file']);
    }
}

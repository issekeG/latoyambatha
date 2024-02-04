<?php

namespace App\Form;
use App\Entity\Categorie;
use App\Entity\Videos;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('title', null, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('embedVideoLink', null, [
                'label' => 'Embed video link',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', null, [
                'label' => 'Video description',
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Videos::class,
        ]);
    }
}

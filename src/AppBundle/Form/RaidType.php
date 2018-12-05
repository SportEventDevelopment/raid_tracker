<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use AppBundle\Entity\Raid;

class RaidType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array('label' => 'Nom du raid'))
            ->add('lieu', TextType::class, array('label' => 'Lieu de l\'événement'))
            ->add('date', DateTimeType::class, array(
                'label' => 'Date du raid',
                'date_widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm'
            ))
            ->add('edition', IntegerType::class, array('label' => 'Numéro d\'édition'))
            ->add('equipe', TextType::class, array('label' => 'Nom d\'équipe'))
            ->add('visibility', CheckboxType::class, array(
                'label' => 'Visible',
                'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Raid',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_raid';
    }


}

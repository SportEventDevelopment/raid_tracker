<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Benevole;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class BenevoleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        //->add('idUser')->add('poste')->add('idRaid')
        ->add('poste', ChoiceType::class, array("label" => 'Choix du poste', "invalid_message" => "Le champ est incorrect", "choices_as_values"=>true ,
        "choices"=>array_flip(Benevole::$tabPoste), "multiple" => false, "expanded" => false))

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Benevole'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_benevole';
    }


}

<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use AppBundle\Entity\Raid;



class RaidType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array("label" => 'Nom du raid'))

            ->add('lieu', TextType::class, array("label" => "Lieu de l'événement"))

            ->add('date', DateType::class, array('label' => 'Date du raid'))
                
            ->add('edition', TextType::class, array("label" => "Numéro d'édition"))

    //   ->add('typeSport', ChoiceType::class, array("label" => "Type de sport", "invalid_message" => "Le champ est incorrect", "choices_as_values"=>true ,"choices"=>array_flip(Raid::$tabTypeSport), "multiple" => false, "expanded" => true))

    //   ->add('nbrSport',null,array("label" => 'Nombre de sport', "invalid_message" => "Le champ est incorrect"))

    //   ->add('typeSurface', ChoiceType::class, array("label" => "Type de surface", "invalid_message" => "Le champ est incorrect", "choices_as_values"=>true ,"choices"=>array_flip(Raid::$tabTypeSurface), "multiple" => false, "expanded" => true))

       ->add('equipe', TextType::class, array("label" => "Nom d'équipe"))
       ;
    }




    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Raid'
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

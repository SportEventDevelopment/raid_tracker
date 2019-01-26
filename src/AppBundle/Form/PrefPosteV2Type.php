<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PrefPosteV2Type extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $postes = $options['postes_disponibles'];
       $benevoles = $options['benevoles_raid'];


        $builder
            ->add('idPoste', ChoiceType::class, array(
                'choices' => $postes,
                'label' => 'Choisir un poste',
                'choice_label' => function ($postes, $key, $value) {
                    return strtoupper($postes->type);
                })
                  )
        ->add('idBenevole', ChoiceType::class, array(
            'choices' => $benevoles,
            'label' => 'Choisir un bénévole',
            'choice_label' => function ($benevoles, $key, $value) {
                return strtoupper($benevoles->idUser->username);
            })
    )
        ;

  }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PrefPoste',
            'postes_disponibles' => null,
            'benevoles_raid' =>null,
            'csrf_protection' =>false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_prefposte';
    }


}

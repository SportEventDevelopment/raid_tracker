<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PrefPosteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $postes = $options['postes_disponibles'];

        $builder
            ->add('idPoste', ChoiceType::class, array(
                'choices' => $postes,
                'label' => 'Ma préférence de poste',
                'choice_label' => function ($postes, $key, $value) {
                    return strtoupper($postes->type);
                }
            )
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

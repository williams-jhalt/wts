<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('file', FileType::class)
                ->add('explicit', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, array('label' => 'Explicit', 'required' => false));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProductAttachment',
        ));
    }

}

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDetailType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('description', TextareaType::class, array('label' => "Description", 'required' => false, 'attr' => array('rows' => 25)))
                ->add('height', TextType::class, array('label' => "Package Height", 'required' => false))
                ->add('length', TextType::class, array('label' => "Package Length", 'required' => false))
                ->add('width', TextType::class, array('label' => "Package Width", 'required' => false))
                ->add('weight', TextType::class, array('label' => "Package Weight", 'required' => false));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProductDetail',
        ));
    }

}

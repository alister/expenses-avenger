<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('created_at', 'date', array(
            'description' => 'When the expense was reported',
            'html5' => true,
            'widget' => 'single_text',
            'input' => 'string',
            'format' => 'yyyy-MM-dd',
        ))
        ->add('amount', 'money', array(
            'description' => 'Cost of expense',
            'currency' => 'BRL',   // Brazilian 'Real' currency
        ))
        ->add('description', 'text', array(
            'description' => 'Description of expense',
        ))
        ->add('comment', 'textarea', array(
            'description' => 'Additional comment',
            'required' => false,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'AppBundle\Entity\Expense',
            'intention'          => 'expense',
            'translation_domain' => 'AppBundle',
            'csrf_protection'    => false,     // @TODO security
        ));
    }

    public function getName()
    {
        // don't put the data to be returned into an array - just flat POSTs
        return '';
    }
}

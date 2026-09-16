<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 18/06/2018
 * Time: 12:29
 */

namespace Dealer\Form;


use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\Constraints;

class CustomImageForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add("file", FileType::class, array("constraints" => array(
                new Constraints\Image(mimeTypes: array('image/gif', 'image/jpeg', 'image/png', 'image/webp'))
            )))
            ->add("parent_id", HiddenType::class, array("constraints" => array(new Constraints\NotBlank())))
        ;
    }
}
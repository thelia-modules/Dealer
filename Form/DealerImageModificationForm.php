<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 13/07/2018
 * Time: 11:46
 */

namespace Dealer\Form;


use Thelia\Form\Image\ImageModification;

class DealerImageModificationForm extends ImageModification
{
    public function getName()
    {
        return 'dealer_image_modification';
    }
}

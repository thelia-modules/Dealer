<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 18/06/2018
 * Time: 11:31
 */

namespace Dealer\Form;



class DealerImageHeaderForm extends CustomImageForm
{
    const DEALER_IMAGE_HEADER_FORM_ID = "dealer.image.header";

    public static function getName()
    {
        return "dealer_image_header";
    }
}
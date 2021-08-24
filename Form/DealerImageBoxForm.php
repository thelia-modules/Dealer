<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 18/06/2018
 * Time: 12:06
 */

namespace Dealer\Form;


class DealerImageBoxForm extends CustomImageForm
{
    const DEALER_IMAGE_BOX_FORM_ID = "dealer.image.box";

    public static function getName()
    {
        return "dealer_image_box";
    }
}
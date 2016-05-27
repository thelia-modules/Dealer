<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Dealer\Form;

use Dealer\Dealer;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ConfigQuery;

/**
 * Class Configuration
 * @package DealerGoogleTimeZone\Form
 */
class Configuration extends BaseForm {

    protected function buildForm()
    {
        $form = $this->formBuilder;

        $form->add(
            "googlemapsapi_key",
            "text",
            array(
                'data'  => Dealer::getConfigValue("googlemapsapi_key"),
                'label' => Translator::getInstance()->trans("Google Maps Api Key"),
                'label_attr' => array(
                    'for' => "googlemapsapi_key"
                ),
            )
        );

    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "dealer_configuration";
    }


} 
<?php namespace Dealer\Form\Base;

use Dealer\Form\DealerTabCreateForm as ChildDealerTabCreateForm;
use Dealer\Form\Type\DealerTabIdType;

class DealerTabUpdateForm extends ChildDealerTabCreateForm
{
    const FORM_NAME = "dealer_tab_update";

    public function buildForm()
    {
        parent::buildForm();
        $this->formBuilder->add("id", DealerTabIdType::TYPE_NAME);
    }
}

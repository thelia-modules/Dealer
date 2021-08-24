<?php

namespace Dealer\Controller;

use Dealer\Dealer;
use Dealer\Form\DealerMetaSEOForm;
use Dealer\Model\DealerMetaSeoQuery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/module/dealer/seo", name="dealer_seo")
 */
class MetaSeoController extends BaseAdminController
{
    /**
     * @Route("/update", name="_update", methods="POST")
     */
    public function updateSeo()
    {
        $form = $this->createForm(DealerMetaSEOForm::getName());
        try {
            $this->validateForm($form);

            $databasesConfiguration = [
                'dealer_id' => $form->getForm()->get('dealer_id')->getData(),
                'slug' => $form->getForm()->get('slug')->getData(),
                'meta_title' => $form->getForm()->get('meta_title')->getData(),
                'meta_description' => $form->getForm()->get('meta_description')->getData(),
                'meta_keywords' => $form->getForm()->get('meta_keywords')->getData(),
                'meta_json' => $form->getForm()->get('meta_json')->getData(),
            ];

            $dealerSeo = DealerMetaSeoQuery::create()
                ->filterByDealerId($databasesConfiguration["dealer_id"])
                ->findOneOrCreate();

            $dealerSeo
                ->setJson( $databasesConfiguration["meta_json"])
                ->setSlug($databasesConfiguration["slug"])
                ->setMetaTitle($databasesConfiguration["meta_title"])
                ->setMetaDescription($databasesConfiguration["meta_description"])
                ->setMetaKeywords($databasesConfiguration["meta_keywords"])
                ->save();

            return RedirectResponse::create(
                URL::getInstance()->absoluteUrl('admin/module/Dealer/dealer')
            );

        } catch (FormValidationException $exception) {
            if (!$form->getForm()->isValid()) {
                $this->setupFormErrorContext(
                    'Dealer meta seo manager',
                    $this->createStandardFormValidationErrorMessage($exception),
                    $form
                );
                $form->setErrorMessage(null);
            } else {
                $this->setupFormErrorContext(
                    'Dealer meta seo manager',
                    $exception->getMessage(),
                    $form
                );
            }

            $response = $this->render(
                'module-configure',
                [
                    'module_code' => Dealer::DOMAIN_NAME
                ]
            );
        }

        return $response;
    }
}
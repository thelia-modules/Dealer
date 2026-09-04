<?php

namespace Dealer\Controller;

use Dealer\Form\DealerMetaSEOForm;
use Dealer\Model\DealerMetaSeoQuery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

/**
 */
class MetaSeoController extends BaseAdminController
{
    /**
     * @Route("/update", name="_update", methods="POST")
     */
    #[Route('/admin/module/dealer/seo', name: 'dealer_seo')]
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

            // The meta fields are translated: without the edition locale they would be stored
            // under Propel's default one and read back empty by the edition screen.
            $dealerSeo
                ->setLocale($this->getCurrentEditionLocale())
                ->setJson( $databasesConfiguration["meta_json"])
                ->setSlug($databasesConfiguration["slug"])
                ->setMetaTitle($databasesConfiguration["meta_title"])
                ->setMetaDescription($databasesConfiguration["meta_description"])
                ->setMetaKeywords($databasesConfiguration["meta_keywords"])
                ->save();

            return $this->redirectToDealerEdit($databasesConfiguration["dealer_id"]);

        } catch (FormValidationException $exception) {
            $message = $form->getForm()->isValid()
                ? $exception->getMessage()
                : $this->createStandardFormValidationErrorMessage($exception);

            $request = $this->getRequest();
            if (null !== $request && $request->hasSession()) {
                $request->getSession()->getFlashBag()->add('error', $message);
            }

            return $this->redirectToDealerEdit(
                $this->getRequest()->request->all(DealerMetaSEOForm::getName())['dealer_id'] ?? null
            );
        }
    }

    private function redirectToDealerEdit($dealerId): RedirectResponse
    {
        if ($dealerId === null) {
            return new RedirectResponse(URL::getInstance()->absoluteUrl('/admin/module/Dealer/dealer'));
        }

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl('/admin/module/Dealer/dealer/edit', ['dealer_id' => $dealerId])
        );
    }
}

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
/*************************************************************************************/

namespace Dealer\Controller;

use Dealer\Controller\Base\BaseController;
use Dealer\Dealer as DealerModule;
use Dealer\Form\DealerForm;
use Dealer\Model\Dealer;
use Dealer\Model\DealerQuery;
use Propel\Runtime\Propel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Thelia\Core\Template\TemplateHelperInterface;
use Thelia\Model\CountryQuery;
use Thelia\Tools\TokenProvider;
use Thelia\Tools\URL;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class DealerController
 * @package Dealer\Controller
 */
class DealerController extends BaseController
{
    /**
     * Load an existing object from the database
     */
    protected function getExistingObject(Request $request)
    {
        return DealerQuery::create()->findPk($request->query->get("dealer_id"));
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param mixed $object
     */
    protected function hydrateObjectForm($object)
    {
        $data = array(
            "id" => $object->getId(),
            "title" => $object->getTitle(),
            "address1" => $object->getAddress1(),
            "address2" => $object->getAddress2(),
            "address3" => $object->getAddress3(),
            "zipcode" => $object->getZipcode(),
            "city" => $object->getCity(),
            "country_id" => $object->getCountryId(),
            "description" => $object->getDescription(),
            "big_description" => $object->getBigDescription(),
            "hard_open_hour" => $object->getHardOpenHour(),
            "latitude" => $object->getLatitude(),
            "longitude" => $object->getLongitude(),
        );

        return $this->getUpdateForm($data);
    }

    const CONTROLLER_ENTITY_NAME = "dealer";

    protected function getCreationForm()
    {
        return $this->createForm(DealerForm::getName());
    }

    private ?Environment $dealerTwig = null;

    #[Route('/admin/module/Dealer/dealer', name: 'dealer', methods: ['GET'])]
    public function listAction(
        Environment $twig,
        TemplateHelperInterface $templateHelper,
        #[Autowire(service: 'twig.loader.native_filesystem')]
        FilesystemLoader $loader,
    ): Response {
        $this->dealerTwig = $twig;

        // Make both the active admin theme (for base.html.twig) and this module's
        // default-twig directory resolvable by the shared Twig loader, so the page
        // can be rendered by bare name (the active parser would otherwise pick Smarty).
        $loader->addPath($templateHelper->getActiveAdminTemplate()->getAbsolutePath());
        $loader->addPath(__DIR__.'/../templates/backOffice/default-twig');

        return parent::defaultAction();
    }

    /**
     * Use to get render of list
     */
    protected function getListRenderTemplate(): Response
    {
        $request = $this->getRequest();
        $order = $request->query->get('order') ?? 'id';

        $locale = $request->getSession()?->getLang()?->getLocale() ?? 'en_US';

        $query = DealerQuery::create();

        switch ($order) {
            case 'id-reverse':
                $query->orderById(\Propel\Runtime\ActiveQuery\Criteria::DESC);
                break;
            case 'title':
                $query->useDealerI18nQuery()->orderByTitle()->endUse();
                break;
            case 'title-reverse':
                $query->useDealerI18nQuery()->orderByTitle(\Propel\Runtime\ActiveQuery\Criteria::DESC)->endUse();
                break;
            default:
                $query->orderById();
                break;
        }

        $dealers = [];
        $countries = [];

        /** @var Dealer $dealer */
        foreach ($query->find() as $dealer) {
            $countryId = $dealer->getCountryId();

            if ($countryId !== null && !isset($countries[$countryId])) {
                $country = CountryQuery::create()->findPk($countryId);

                if ($country !== null) {
                    $countries[$countryId] = $country->setLocale($locale)->getTitle();
                }
            }

            $dealers[] = [
                'id' => $dealer->getId(),
                'title' => $dealer->setLocale($locale)->getTitle(),
                'address1' => $dealer->getAddress1(),
                'zipcode' => $dealer->getZipcode(),
                'city' => $dealer->getCity(),
                'country' => $countries[$countryId] ?? '',
                'visible' => (int) $dealer->getVisible() === 1,
            ];
        }

        $createForm = $this->getTheliaFormFactory()->createForm(DealerForm::getName(), data: ['locale' => $locale]);

        return new Response(
            $this->dealerTwig->render('dealers.html.twig', [
                'dealers' => $dealers,
                'order' => $order,
                'edit_language_id' => $request->getSession()?->getLang()?->getId(),
                'edit_language_locale' => $locale,
                'create_form' => $createForm->createView()->getView(),
                'general_error' => $this->getParserContext()->get('general_error'),
            ])
        );
    }

    /**
     * Use to get Edit render
     * @return mixed
     */
    protected function getEditRenderTemplate()
    {
        return $this->render("dealer-edit");
    }

    /**
     * Use to get Create render
     * @return mixed
     */
    protected function getCreateRenderTemplate()
    {
        // TODO: Implement getCreateRenderTemplate() method.
    }

    /**
     * @return mixed
     */
    protected function getObjectId($object)
    {
        /** @var Dealer $object */
        return $object->getId();
    }

    protected function redirectToListTemplate()
    {
        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer"));
    }

    protected function redirectToEditionTemplate(Request $request)
    {
        $id = $request->query->get("dealer_id");

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer/edit",
            ["dealer_id" => $id, ]));
    }

    /**
     */
    #[Route('/toggle-online/{id}', name: '_toggle_visible', methods: ['POST'])]
    public function toggleVisibleAction($id)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, DealerModule::getModuleCode(),
                AccessManager::UPDATE)
        ) {
            return $response;
        }

        // Error (Default: false)
        $error_msg = false;

        $retour = [];
        $code = 200;


        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            $updatedObject = $this->getService()->toggleVisibilityFromId($id);

            // Check if object exist
            if (!$updatedObject) {
                throw new \LogicException(
                    Translator::getInstance()->trans("No %obj was updated.", ['%obj' => static::CONTROLLER_ENTITY_NAME])
                );
            }

            $con->commit();

            $retour["message"] = "Visibility was updated";
        } catch (\Exception $ex) {
            $con->rollBack();
            // Any other error
            $retour["message"] = "Visibility can be updated";
            $code = 500;
            $retour["error"] = $ex->getMessage();
        }

        return JsonResponse::create($retour, $code);
    }

    /**
     */
    #[Route('/admin/module/Dealer/dealer', name: 'dealer_create', methods: ['POST'])]
    public function createAction()
    {
        return parent::createAction();
    }

    /**
     */
    #[Route('/edit', name: '_tab.view', methods: ['GET'])]
    public function updateAction(ParserContext $parserContext, RequestStack $requestStack)
    {
        return parent::updateAction($parserContext, $requestStack);
    }

    /**
     */
    #[Route('/edit', name: '_tab.edit', methods: ['POST'])]
    public function processUpdateAction(RequestStack $requestStack)
    {
        return parent::processUpdateAction($requestStack);
    }

    /**
     */
    #[Route('/delete', name: '_delete', methods: ['POST'])]
    public function deleteAction(TokenProvider $tokenProvider, RequestStack $requestStack, ParserContext $parserContext)
    {
        return parent::deleteAction($tokenProvider, $requestStack, $parserContext);
    }
}

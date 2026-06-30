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
use Dealer\Form\AdminLinkForm;
use Dealer\Form\ContactForm;
use Dealer\Form\ContactInfoForm;
use Dealer\Form\ContactInfoUpdateForm;
use Dealer\Form\ContactUpdateForm;
use Dealer\Form\DealerForm;
use Dealer\Form\DealerImageBoxForm;
use Dealer\Form\DealerImageHeaderForm;
use Dealer\Form\DealerMetaSEOForm;
use Dealer\Form\DealerUpdateForm;
use Dealer\Form\GeoDealerForm;
use Dealer\Form\SchedulesCloneForm;
use Dealer\Form\SchedulesForm;
use Dealer\Form\SchedulesUpdateForm;
use Dealer\Model\DealerImage;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
#[Route('/admin/module/Dealer/dealer', name: 'dealer')]
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

    protected function getUpdateForm($data = [])
    {
        if (!is_array($data)) {
            $data = [];
        }

        return $this->createForm(DealerUpdateForm::getName(), FormType::class, $data);
    }

    public function __construct(
        private readonly Environment $dealerTwigEnv,
        private readonly TemplateHelperInterface $dealerTemplateHelper,
        #[Autowire(service: 'twig.loader.native_filesystem')]
        private readonly FilesystemLoader $dealerTwigLoader,
        private readonly EventDispatcherInterface $dealerEventDispatcher,
    ) {
    }

    /**
     * Make both the active admin theme (for base.html.twig) and this module's
     * default-twig directory resolvable by the shared Twig loader, so the page
     * can be rendered by bare name (the active parser would otherwise pick Smarty).
     */
    private function setupModuleTwig(): Environment
    {
        $this->dealerTwigLoader->addPath($this->dealerTemplateHelper->getActiveAdminTemplate()->getAbsolutePath());
        $this->dealerTwigLoader->addPath(__DIR__.'/../templates/backOffice/default-twig');

        return $this->dealerTwigEnv;
    }

    #[Route('', name: '', methods: ['GET'])]
    public function listAction(): Response
    {
        $this->setupModuleTwig();

        return parent::defaultAction();
    }

    /**
     * Use to get render of list
     */
    protected function getListRenderTemplate(): Response
    {
        $request = $this->getRequest();
        $order = $request->query->get('order') ?? 'id';

        $locale = $request->hasSession()
            ? ($request->getSession()->getLang()?->getLocale() ?? 'en_US')
            : (\Thelia\Model\LangQuery::create()->findOneByByDefault(true)?->getLocale() ?? 'en_US');

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
            $this->dealerTwigEnv->render('dealers.html.twig', [
                'dealers' => $dealers,
                'order' => $order,
                'edit_language_id' => $request->hasSession() ? $request->getSession()->getLang()?->getId() : null,
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
        $twig = $this->setupModuleTwig();

        $request = $this->getRequest();
        $dealerId = $request->query->get('dealer_id');
        $locale = $request->hasSession()
            ? ($request->getSession()->getLang()?->getLocale() ?? 'en_US')
            : (\Thelia\Model\LangQuery::create()->findOneByByDefault(true)?->getLocale() ?? 'en_US');

        $dealer = $dealerId !== null ? DealerQuery::create()->findPk($dealerId) : null;

        $updateForm = $this->getUpdateForm($this->buildDealerFormData($dealer));

        $contactCreateForm = $this->getTheliaFormFactory()->createForm(ContactForm::getName(), data: ['locale' => $locale]);
        $contactUpdateForm = $this->getTheliaFormFactory()->createForm(ContactUpdateForm::getName(), data: ['locale' => $locale]);
        $contactInfoCreateForm = $this->getTheliaFormFactory()->createForm(ContactInfoForm::getName(), data: ['locale' => $locale]);
        $contactInfoUpdateForm = $this->getTheliaFormFactory()->createForm(ContactInfoUpdateForm::getName(), data: ['locale' => $locale]);
        $schedulesCreateForm = $this->getTheliaFormFactory()->createForm(SchedulesForm::getName());
        $schedulesUpdateForm = $this->getTheliaFormFactory()->createForm(SchedulesUpdateForm::getName());
        $schedulesCloneForm = $this->getTheliaFormFactory()->createForm(SchedulesCloneForm::getName());
        $adminLinkForm = $this->getTheliaFormFactory()->createForm(AdminLinkForm::getName());
        $geoForm = $this->getTheliaFormFactory()->createForm(GeoDealerForm::getName(), data: $this->buildGeoFormData($dealer));
        $seoForm = $this->getTheliaFormFactory()->createForm(DealerMetaSEOForm::getName(), data: $this->buildSeoFormData($dealerId));
        $imageHeaderForm = $this->getTheliaFormFactory()->createForm(DealerImageHeaderForm::getName());
        $imageBoxForm = $this->getTheliaFormFactory()->createForm(DealerImageBoxForm::getName());

        return new Response(
            $twig->render('dealer-edit.html.twig', [
                'dealer_id' => $dealerId,
                'dealer' => $dealer !== null ? [
                    'id' => $dealer->getId(),
                    'title' => $dealer->setLocale($locale)->getTitle(),
                    'created_at' => $dealer->getCreatedAt(),
                    'updated_at' => $dealer->getUpdatedAt(),
                ] : null,
                'edit_language_id' => $request->hasSession() ? $request->getSession()->getLang()?->getId() : null,
                'edit_language_locale' => $locale,
                'update_form' => $updateForm->createView()->getView(),
                'general_error' => $this->getParserContext()->get('general_error'),
                // Tab data
                'contacts' => $this->buildContacts($dealerId, $locale),
                'contact_create_form' => $contactCreateForm->createView()->getView(),
                'contact_update_form' => $contactUpdateForm->createView()->getView(),
                'contact_info_create_form' => $contactInfoCreateForm->createView()->getView(),
                'contact_info_update_form' => $contactInfoUpdateForm->createView()->getView(),
                'contact_info_types' => $this->getContactInfoTypeChoices(),
                'schedules_default' => $this->buildSchedules($dealerId, $locale, defaultPeriod: true, closed: false),
                'schedules_extra' => $this->buildSchedules($dealerId, $locale, defaultPeriod: false, closed: false),
                'schedules_closed' => $this->buildSchedules($dealerId, $locale, defaultPeriod: false, closed: true),
                'schedules_create_form' => $schedulesCreateForm->createView()->getView(),
                'schedules_update_form' => $schedulesUpdateForm->createView()->getView(),
                'schedules_clone_form' => $schedulesCloneForm->createView()->getView(),
                'day_labels' => $this->getDayLabels($locale),
                'linked_admins' => $this->buildLinkedAdmins($dealerId),
                'admin_link_form' => $adminLinkForm->createView()->getView(),
                'geo_form' => $geoForm->createView()->getView(),
                'images_header' => $this->buildImages($dealerId, DealerImage::IMAGE_TYPE_HEADER, $locale),
                'images_box' => $this->buildImages($dealerId, DealerImage::IMAGE_TYPE_BOX, $locale),
                'image_header_form' => $imageHeaderForm->createView()->getView(),
                'image_box_form' => $imageBoxForm->createView()->getView(),
                'seo_form' => $seoForm->createView()->getView(),
            ])
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildGeoFormData(?Dealer $dealer): array
    {
        if ($dealer === null) {
            return [];
        }

        return [
            'id' => $dealer->getId(),
            'latitude' => $dealer->getLatitude(),
            'longitude' => $dealer->getLongitude(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSeoFormData($dealerId): array
    {
        if ($dealerId === null) {
            return [];
        }

        $seo = \Dealer\Model\DealerMetaSeoQuery::create()->findOneByDealerId($dealerId);

        return [
            'dealer_id' => $dealerId,
            'slug' => $seo?->getSlug(),
            'meta_title' => $seo?->getMetaTitle(),
            'meta_description' => $seo?->getMetaDescription(),
            'meta_keywords' => $seo?->getMetaKeywords(),
            'meta_json' => $seo?->getJson(),
        ];
    }

    /**
     * Contacts (with their contact-infos) for the Contacts tab.
     *
     * @return list<array<string, mixed>>
     */
    private function buildContacts($dealerId, string $locale): array
    {
        if ($dealerId === null) {
            return [];
        }

        $contacts = [];

        $rows = \Dealer\Model\DealerContactQuery::create()
            ->filterByDealerId($dealerId)
            ->orderByIsDefault(\Propel\Runtime\ActiveQuery\Criteria::DESC)
            ->find();

        /** @var \Dealer\Model\DealerContact $contact */
        foreach ($rows as $contact) {
            $infos = [];

            $infoRows = \Dealer\Model\DealerContactInfoQuery::create()
                ->filterByContactId($contact->getId())
                ->find();

            /** @var \Dealer\Model\DealerContactInfo $info */
            foreach ($infoRows as $info) {
                $infos[] = [
                    'id' => $info->getId(),
                    'contact_id' => $info->getContactId(),
                    'type' => $info->getContactType(),
                    'type_id' => $info->getContactTypeId(),
                    'value' => $info->setLocale($locale)->getValue(),
                ];
            }

            $contacts[] = [
                'id' => $contact->getId(),
                'label' => $contact->setLocale($locale)->getLabel(),
                'is_default' => (int) $contact->getIsDefault() === 1,
                'infos' => $infos,
            ];
        }

        return $contacts;
    }

    /**
     * @return array<int|string, string>
     */
    private function getContactInfoTypeChoices(): array
    {
        return \Dealer\Model\Map\DealerContactInfoTableMap::getValueSet(
            \Dealer\Model\Map\DealerContactInfoTableMap::COL_CONTACT_TYPE
        );
    }

    /**
     * Schedules for one of the three sections (default / extra / closed).
     *
     * @return list<array<string, mixed>>
     */
    private function buildSchedules($dealerId, string $locale, bool $defaultPeriod, bool $closed): array
    {
        if ($dealerId === null) {
            return [];
        }

        $query = \Dealer\Model\DealerShedulesQuery::create()
            ->filterByDealerId($dealerId)
            ->filterByClosed($closed ? 1 : 0);

        if ($defaultPeriod) {
            $query->filterByPeriodNull()->orderByDay()->orderByBegin();
        } else {
            $query->filterByPeriodNotNull()->orderByPeriodBegin()->orderByDay()->orderByBegin();
        }

        $days = $this->getDayLabels($locale);
        $schedules = [];

        /** @var \Dealer\Model\DealerShedules $schedule */
        foreach ($query->find() as $schedule) {
            $begin = $schedule->getBegin();
            $end = $schedule->getEnd();
            $periodBegin = $schedule->getPeriodBegin();
            $periodEnd = $schedule->getPeriodEnd();

            $schedules[] = [
                'id' => $schedule->getId(),
                'day' => $schedule->getDay(),
                'day_label' => $days[$schedule->getDay()] ?? '',
                'begin' => $begin instanceof \DateTimeInterface ? $begin->format('H:i') : null,
                'end' => $end instanceof \DateTimeInterface ? $end->format('H:i') : null,
                'period_begin' => $periodBegin instanceof \DateTimeInterface ? $periodBegin->format('Y-m-d') : null,
                'period_end' => $periodEnd instanceof \DateTimeInterface ? $periodEnd->format('Y-m-d') : null,
            ];
        }

        return $schedules;
    }

    /**
     * @return array<int, string>
     */
    private function getDayLabels(string $locale): array
    {
        $translator = Translator::getInstance();

        return [
            $translator->trans('Monday', [], DealerModule::MESSAGE_DOMAIN, $locale),
            $translator->trans('Tuesday', [], DealerModule::MESSAGE_DOMAIN, $locale),
            $translator->trans('Wednesday', [], DealerModule::MESSAGE_DOMAIN, $locale),
            $translator->trans('Thursday', [], DealerModule::MESSAGE_DOMAIN, $locale),
            $translator->trans('Friday', [], DealerModule::MESSAGE_DOMAIN, $locale),
            $translator->trans('Saturday', [], DealerModule::MESSAGE_DOMAIN, $locale),
            $translator->trans('Sunday', [], DealerModule::MESSAGE_DOMAIN, $locale),
        ];
    }

    /**
     * Admin users linked to this dealer (Users tab).
     *
     * @return list<array<string, mixed>>
     */
    private function buildLinkedAdmins($dealerId): array
    {
        if ($dealerId === null) {
            return [];
        }

        $admins = [];

        $links = \Dealer\Model\DealerAdminQuery::create()
            ->filterByDealerId($dealerId)
            ->find();

        $adminIds = [];
        /** @var \Dealer\Model\DealerAdmin $link */
        foreach ($links as $link) {
            $adminIds[] = $link->getAdminId();
        }

        if (empty($adminIds)) {
            return [];
        }

        $adminRows = \Thelia\Model\AdminQuery::create()
            ->filterById($adminIds, \Propel\Runtime\ActiveQuery\Criteria::IN)
            ->orderById()
            ->find();

        /** @var \Thelia\Model\Admin $admin */
        foreach ($adminRows as $admin) {
            $admins[] = [
                'id' => $admin->getId(),
                'firstname' => $admin->getFirstname(),
                'lastname' => $admin->getLastname(),
            ];
        }

        return $admins;
    }

    /**
     * Images of a given type (header / box) processed through the image pipeline.
     *
     * @return list<array<string, mixed>>
     */
    private function buildImages($dealerId, string $type, string $locale): array
    {
        if ($dealerId === null) {
            return [];
        }

        $images = [];

        $rows = \Dealer\Model\DealerImageQuery::create()
            ->filterByType(DealerImage::getTypeIdFromLabel($type))
            ->filterByDealerId($dealerId)
            ->find();

        /** @var DealerImage $image */
        foreach ($rows as $image) {
            $imageUrl = '';
            $originalUrl = '';

            try {
                $event = new \Thelia\Core\Event\Image\ImageEvent();
                $event->setSourceFilepath($image->getUploadDir() . '/' . $image->getFile());
                $event->setCacheSubdirectory(DealerModule::DOMAIN_NAME);
                $event->setWidth(120);
                $event->setResizeMode((string) \Thelia\Action\Image::EXACT_RATIO_WITH_BORDERS);

                $this->dealerEventDispatcher->dispatch($event, \Thelia\Core\Event\TheliaEvents::IMAGE_PROCESS);

                $imageUrl = $event->getFileUrl();
                $originalUrl = $event->getOriginalFileUrl();
            } catch (\Exception) {
                // Leave URLs empty; the template shows a placeholder.
            }

            $images[] = [
                'id' => $image->getId(),
                'image_url' => $imageUrl,
                'original_url' => $originalUrl,
                'title' => $image->setLocale($locale)->getTitle(),
            ];
        }

        return $images;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDealerFormData(?Dealer $dealer): array
    {
        if ($dealer === null) {
            return [];
        }

        return [
            'id' => $dealer->getId(),
            'title' => $dealer->getTitle(),
            'address1' => $dealer->getAddress1(),
            'address2' => $dealer->getAddress2(),
            'address3' => $dealer->getAddress3(),
            'zipcode' => $dealer->getZipcode(),
            'city' => $dealer->getCity(),
            'country_id' => $dealer->getCountryId(),
            'description' => $dealer->getDescription(),
            'big_description' => $dealer->getBigDescription(),
            'hard_open_hour' => $dealer->getHardOpenHour(),
            'access' => $dealer->getAccess(),
            'latitude' => $dealer->getLatitude(),
            'longitude' => $dealer->getLongitude(),
        ];
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
    public function toggleVisibleAction(TokenProvider $tokenProvider, RequestStack $requestStack, $id): Response
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, DealerModule::getModuleCode(),
                AccessManager::UPDATE)
        ) {
            return $response;
        }

        // Check CSRF token
        $tokenProvider->checkToken(
            (string) $requestStack->getCurrentRequest()->query->get('_token')
        );

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

        return new JsonResponse($retour, $code);
    }

    /**
     */
    #[Route('', name: '_create', methods: ['POST'])]
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

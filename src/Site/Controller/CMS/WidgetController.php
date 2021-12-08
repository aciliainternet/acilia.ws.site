<?php

namespace WS\Site\Controller\CMS;

use WS\Core\Entity\AssetImage;
use WS\Core\Library\CRUD\AbstractController;
use WS\Core\Library\CRUD\AbstractService;
use WS\Site\Entity\WidgetConfiguration;
use WS\Site\Service\WidgetService;
use WS\Site\Service\Entity\WidgetConfigurationService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/widget", name="ws_cms_site_widget_")
 */
class WidgetController extends AbstractController
{
    protected WidgetService $widgetService;

    public function __construct(WidgetConfigurationService $service, WidgetService $widgetService)
    {
        $this->service = $service;
        $this->widgetService = $widgetService;
    }

    protected function getService(): AbstractService
    {
        return $this->service;
    }

    protected function getTranslatorPrefix(): string
    {
        return 'ws_cms_site_widget';
    }

    protected function useCRUDTemplate(string $template): bool
    {
        if ($template === 'show.html.twig') {
            return true;
        }

        return false;
    }

    protected function getBatchActions(): array
    {
        return [self::DELETE_BATCH_ACTION];
    }

    /**
     * @Route("/", name="index")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request): Response
    {
        $this->addEvent(self::EVENT_INDEX_EXTRA_DATA, function () {
            return [
                'widgets' => $this->widgetService->getWidgets()
            ];
        });

        return parent::index($request);
    }

    /**
     * @Route("/create/{widgetType}", name="create")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request): Response
    {
        $widgetType = $request->get('widgetType');

        try {
            $widget = $this->widgetService->getWidget($widgetType);
        } catch (\Exception $e) {
            throw $this->createNotFoundException(sprintf($this->trans('not_found', [], $this->getTranslatorPrefix()), $widgetType));
        }

        $this->addEvent(self::EVENT_CREATE_NEW_ENTITY, function (WidgetConfiguration $entity) use ($widget) {
            $entity->setWidget($widget->getId());
        });

        $this->addEvent(self::EVENT_CREATE_CREATE_FORM, function (WidgetConfiguration $entity) use ($widget) {
            return $this->createForm(
                $this->getService()->getFormClass(),
                $entity,
                [
                    'widget' => $widget,
                    'translation_domain' => $this->getTranslatorPrefix()
                ]
            );
        });

        $this->addEvent(self::EVENT_IMAGE_HANDLE, function (WidgetConfiguration $entity, string $imageField, AssetImage $assetImage) {
            $configuration = $entity->getConfiguration();
            if ($configuration !== null) {
                if (array_key_exists($imageField, $configuration)) {
                    $configuration[$imageField] = $assetImage->getId();
                }
            }

            $entity->setConfiguration($configuration);
        });

        return parent::create($request);
    }

    /**
     * @Route ("/edit/{id}", name="edit")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, int $id): Response
    {
        $this->addEvent(self::EVENT_EDIT_CREATE_FORM, function (WidgetConfiguration $entity) {
            return $this->createForm(
                $this->getService()->getFormClass(),
                $entity,
                [
                    'widget' => $this->widgetService->getWidget($entity->getWidget()),
                    'translation_domain' => $this->getTranslatorPrefix()
                ]
            );
        });

        $this->addEvent(self::EVENT_IMAGE_HANDLE, function (WidgetConfiguration $entity, string $imageField, ?AssetImage $assetImage) {
            $configuration = $entity->getConfiguration();
            if ($configuration !== null) {
                if (array_key_exists($imageField, $configuration)) {
                    if (null !== $assetImage) {
                        $configuration[$imageField] = $assetImage->getId();
                    } else {
                        $configuration[$imageField] = null;
                    }
                }
            }

            $entity->setConfiguration($configuration);
        });

        /** @var WidgetConfigurationService */
        $service = $this->getService();
        $service->invalidateCache($id);

        return parent::edit($request, $id);
    }
}

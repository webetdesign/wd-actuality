<?php


namespace WebEtDesign\ActualityBundle\EventListener;

use App\Entity\Actuality\Actuality;
use DateTime;
use App\Entity\Actuality\Category;
use Doctrine\ORM\EntityManagerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface  $urlGenerator;
    private ParameterBagInterface  $parameterBag;
    private EntityManagerInterface $entityManager;
    private bool $useCategory;
    private bool $generateSitemap;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param ParameterBagInterface $parameterBag
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $entityManager,
    ) {
        $this->urlGenerator  = $urlGenerator;
        $this->parameterBag  = $parameterBag;
        $this->entityManager = $entityManager;
        $this->useCategory = $parameterBag->get('wd_actuality.config')['use_category'];
        $this->generateSitemap = $parameterBag->get('wd_actuality.config')['generate_sitemap'];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function populate(SitemapPopulateEvent $event): void
    {
        if ($this->generateSitemap) {
            if ($this->useCategory) {
                $this->useCategoryRegisterDynamicUrls($event->getUrlContainer());
            }else{
                $this->notUseCategoryRegisterDynamicUrls($event->getUrlContainer());
            }
        }
    }

    private function notUseCategoryRegisterDynamicUrls(UrlContainerInterface $urls)
    {
        $config = $this->parameterBag->get('wd_actuality.seo');

        foreach ($this->entityManager->getRepository(Actuality::class)->findAll() as $actuality) {
            $now = new DateTime('now');

            if (!$actuality->getPublished() || $actuality->getPublishedAt() === null || $actuality->getPublishedAt()->getTimestamp() > $now->getTimestamp()) {
                continue;
            }
            
            $context = $this->urlGenerator->getContext();
            if (isset($config['host'])) {
                $context->setHost($config['host']);
            }
            if (isset($config['scheme'])) {
                $context->setScheme($config['scheme']);
            }

            $this->urlGenerator->setContext($context);

            $url = new UrlConcrete($this->urlGenerator->generate($config['actuality_route_name'], [
                "actuality" => $actuality->getSlug()
            ],
                UrlGeneratorInterface::ABSOLUTE_URL),
                $actuality->getUpdatedAt(),
                $config['changefreq'] ?? null,
                $config['priority'] ?? null
            );
            $urls->addUrl($url, 'actuality');
        }
    }
    
    private function useCategoryRegisterDynamicUrls(UrlContainerInterface $urls)
    {
        $config = $this->parameterBag->get('wd_actuality.seo');

        /** @var Category $category */
        foreach ($this->entityManager->getRepository(Category::class)->findAll() as $category) {
            $context = $this->urlGenerator->getContext();
            if (isset($config['host'])) {
                $context->setHost($config['host']);
            }
            if (isset($config['scheme'])) {
                $context->setScheme($config['scheme']);
            }
            
            $this->urlGenerator->setContext($context);

            $url = new UrlConcrete($this->urlGenerator->generate($config['category_route_name'], [
                "category" => $category->getSlug()
            ],
                UrlGeneratorInterface::ABSOLUTE_PATH),
                $category->getUpdatedAt(),
                $config['changefreq'] ?? null,
                $config['priority'] ?? null
            );
            $urls->addUrl($url, 'actuality');

            foreach ($category->getActualities() as $actuality) {
                $now = new DateTime('now');

                if (!$actuality->getPublished() || $actuality->getPublishedAt() === null || $actuality->getPublishedAt()->getTimestamp() > $now->getTimestamp()) {
                    continue;
                }
                $context = $this->urlGenerator->getContext();
                if (isset($config['host'])) {
                    $context->setHost($config['host']);
                }
                if (isset($config['scheme'])) {
                    $context->setScheme($config['scheme']);
                }

                $this->urlGenerator->setContext($context);

                $url = new UrlConcrete($this->urlGenerator->generate($config['actuality_route_name'], [
                    "category" => $category->getSlug(),
                    "actuality" => $actuality->getSlug()
                ],
                    UrlGeneratorInterface::ABSOLUTE_URL),
                    $actuality->getUpdatedAt(),
                    $config['changefreq'] ?? null,
                    $config['priority'] ?? null
                );
                $urls->addUrl($url, 'actuality');
            }
        }
    }
}

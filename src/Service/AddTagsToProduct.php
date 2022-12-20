<?php declare(strict_types=1);

namespace Swag\ShopfrontTags\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddTagsToProduct implements EventSubscriberInterface
{
    private EntityRepositoryInterface $tagRepository;

    public function __construct(EntityRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'getAllTags'
        ];
    }

    public function getAllTags(ProductPageLoadedEvent $event): void
    {
        $tagIds = $event->getPage()->getProduct()->getTagIds();
        $criteria = new Criteria();

        // Exposes precisely those tags that have been assigned to the current product
        if ($tagIds !== null) {
            $criteria->addFilter(new EqualsAnyFilter('id', $tagIds));
            $tags = $this->tagRepository->search($criteria, $event->getContext());

            $event->getPage()->addExtension('tags', $tags);
        }
    }
}

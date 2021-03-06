<?php

namespace WS\Site\Repository;

use Doctrine\ORM\QueryBuilder;
use WS\Core\Entity\Domain;
use WS\Core\Library\Domain\DomainRepositoryTrait;
use WS\Core\Library\Publishing\PublishingRepositoryTrait;
use WS\Site\Entity\WidgetConfiguration;
use WS\Core\Library\CRUD\AbstractRepository;

/**
 * @method WidgetConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method WidgetConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method WidgetConfiguration[]    findAll()
 * @method WidgetConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WidgetConfigurationRepository extends AbstractRepository
{
    use DomainRepositoryTrait;
    use PublishingRepositoryTrait;

    public function getEntityClass()
    {
        return WidgetConfiguration::class;
    }

    public function getFilterFields()
    {
        return ['name'];
    }

    /**
     * @param Domain $domain
     * @param array|null $filters
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return QueryBuilder
     */
    public function getAvailableQuery(Domain $domain, array $filters = null, array $orderBy = null, int $limit = null, int $offset = null)
    {
        $alias = 'wc';
        $qb = $this->createQueryBuilder($alias);

        $this->setDomainRestriction($alias, $qb, $domain);

        $this->setPublishingRestriction($alias, $qb);

        if ($orderBy && count($orderBy)) {
            foreach ($orderBy as $field => $dir) {
                $qb->orderBy(sprintf('%s.%s', $alias, $field), $dir);
            }
        }

        if (isset($limit) && isset($offset)) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);
        }

        return $qb;
    }
}

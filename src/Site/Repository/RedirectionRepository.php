<?php

namespace WS\Site\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use WS\Core\Entity\Domain;
use WS\Core\Library\CRUD\AbstractRepository;
use WS\Site\Entity\Redirection;

/**
 * @method Redirection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Redirection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Redirection[]    findAll()
 * @method Redirection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RedirectionRepository extends AbstractRepository
{
    public function getEntityClass(): string
    {
        return Redirection::class;
    }

    public function getFilterFields(): array
    {
        return ['origin', 'destination'];
    }

    public function findExactRedirection(string $url, Domain $domain): ?Redirection
    {
        $alias = 'r';
        $qb = $this->createQueryBuilder($alias)
            ->where(sprintf('%s.exactMatch = true', $alias))
            ->andWhere(sprintf('%s.origin = :origin', $alias))
            ->andWhere(sprintf('%s.domain IS NULL OR %s.domain = :domain', $alias, $alias))
            ->setParameter('origin', $url)
            ->setParameter('domain', $domain)
            ->setMaxResults(1);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findRegexRedirection(string $url, Domain $domain): ?Redirection
    {
        $sql = 'SELECT redirection_id, redirection_origin, redirection_destination'
            . ' FROM ws_site_redirection r'
            . ' WHERE ? REGEXP CONCAT(\'^\', redirection_origin) '
            . ' AND (redirection_domain IS NULL OR ? = redirection_domain)'
            . ' AND redirection_exact_match = 0'
            . ' LIMIT 1';

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('WS\\Site\\Entity\\Redirection', 'r');
        $rsm->addFieldResult('r', 'redirection_id', 'id');
        $rsm->addFieldResult('r', 'redirection_origin', 'origin');
        $rsm->addFieldResult('r', 'redirection_destination', 'destination');

        try {
            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $query->setParameter(1, $url);
            $query->setParameter(2, $domain->getId());

            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}

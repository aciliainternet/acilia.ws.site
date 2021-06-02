<?php

namespace WS\Site\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;
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
    public function getEntityClass()
    {
        return Redirection::class;
    }

    public function getFilterFields()
    {
        return ['origin', 'destination'];
    }

    public function findValidRedirection(string $url, string $host, bool $exactMatch)
    {
        $sql = 'SELECT redirection_id, redirection_origin, redirection_destination'
            . ' FROM ws_site_redirection r';
        if ($exactMatch === true) {
            $sql .= ' WHERE ? = redirection_origin';
        } else {
            $sql .= ' WHERE ? REGEXP CONCAT(\'^\', redirection_origin) ';
        }
        $sql .= ' AND ( redirection_domain is null OR ? = redirection_domain)'
            . ' LIMIT 1';

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('WS\\Site\\Entity\\Redirection', 'r');
        $rsm->addFieldResult('r', 'redirection_id', 'id');
        $rsm->addFieldResult('r', 'redirection_origin', 'origin');
        $rsm->addFieldResult('r', 'redirection_destination', 'destination');

        try {
            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $query->setParameter(1, $url);
            $query->setParameter(2, $host);

            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}

<?php

namespace WS\Site\Service\Entity;

use WS\Core\Entity\Domain;
use WS\Site\Entity\Redirection;
use WS\Site\Form\CMS\RedirectionType;
use WS\Core\Library\CRUD\AbstractService;
use WS\Site\Repository\RedirectionRepository;

class RedirectionService extends AbstractService
{
    public function getEntityClass(): string
    {
        return Redirection::class;
    }

    public function getFormClass(): ?string
    {
        return RedirectionType::class;
    }

    public function getSortFields(): array
    {
        return ['origin'];
    }

    public function getListFields(): array
    {
        return [
            ['name' => 'domain', 'sortable' => true],
            ['name' => 'origin', 'sortable' => true],
            ['name' => 'destination', 'sortable' => true],
            ['name' => 'exactMatch', 'filter' => 'ws_redirection_exact_match_format'],
            ['name' => 'createdAt', 'sortable' => false, 'width' => 200, 'isDate' => true],
        ];
    }

    public function getExactRedirection(string $url, Domain $domain): ?Redirection
    {
        /** @var RedirectionRepository */
        $repository = $this->repository;

        return $repository->findExactRedirection($url, $domain);
    }

    public function getRegexRedirection(string $url, Domain $domain): ?Redirection
    {
        /** @var RedirectionRepository */
        $repository = $this->repository;

        return $repository->findRegexRedirection($url, $domain);
    }
}

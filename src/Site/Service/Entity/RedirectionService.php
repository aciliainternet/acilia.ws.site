<?php

namespace WS\Site\Service\Entity;

use WS\Site\Entity\Redirection;
use WS\Site\Form\CMS\RedirectionType;
use WS\Core\Library\CRUD\AbstractService;

class RedirectionService extends AbstractService
{
    public function getEntityClass() : string
    {
        return Redirection::class;
    }

    public function getFormClass(): ?string
    {
        return RedirectionType::class;
    }

    public function getSortFields() : array
    {
        return ['origin'];
    }

    public function getListFields(): array
    {
        return [
            ['name' => 'domain', 'sortable' => true],
            ['name' => 'origin', 'sortable' => true],
            ['name' => 'destination', 'sortable' => true],
            ['name' => 'createdAt', 'sortable' => false, 'width' => 200, 'isDate' => true],
        ];
    }

    public function getValidRedirection(string $url, string $host, bool $exactMatch)
    {
        return $this->repository->findValidRedirection($url, $host, $exactMatch);
    }
}

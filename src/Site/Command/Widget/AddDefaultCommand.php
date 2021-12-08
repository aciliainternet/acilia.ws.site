<?php

namespace WS\Site\Command\Widget;

use Doctrine\ORM\EntityManagerInterface;
use WS\Core\Entity\Domain;
use WS\Core\Service\DomainService;
use WS\Site\Entity\WidgetConfiguration;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddDefaultCommand extends Command
{
    protected EntityManagerInterface $em;
    protected DomainService $domainService;

    public function __construct(EntityManagerInterface $em, DomainService $domainService)
    {
        $this->em = $em;
        $this->domainService = $domainService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ws:widget:add-default')
            ->setDescription('Add default widgets into WideStand')
            ->addArgument('domain', InputArgument::REQUIRED, 'The id of the domain')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $domain = $this->domainService->get($input->getArgument('domain'));
            if (! $domain instanceof Domain) {
                throw new \Exception('Invalid Domain ID provided');
            }

            $widget = new WidgetConfiguration();
            $widget
                ->setWidget('cloud_tag')
                ->setCode('cloud_tag')
                ->setConfiguration([])
                ->setDomain($domain)
            ;

            $this->em->persist($widget);
            $this->em->flush();

            $io->success('You have created default widgets');
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}

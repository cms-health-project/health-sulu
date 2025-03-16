<?php

namespace CmsHealthProject\Command;

use CmsHealthProject\Service\HealthProviderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand('cmshealthproject:health-check')]
class HealthCheckCommand extends Command
{
    private HealthProviderService $healthProviderService;

    public function __construct(HealthProviderService $healthProviderService, ?string $name = null)
    {
        $this->healthProviderService = $healthProviderService;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $checks = $this->healthProviderService->getHealthData([]);
        } catch (Throwable $e) {
            $output->writeln($e->getMessage());

            return self::FAILURE;
        }

        $output->writeln(json_encode($checks, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}

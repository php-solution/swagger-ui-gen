<?php

namespace PhpSolution\SwaggerUIGen\Bundle\Command;

use PhpSolution\SwaggerUIGen\Component\DataProvider\DataProviderInterface;
use PhpSolution\SwaggerUIGen\Component\SwaggerProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SwaggerSpecCommand
 */
class SwaggerSpecCommand extends ContainerAwareCommand
{
    /**
     * @var DataProviderInterface
     */
    private $configProvider;
    /**
     * @var SwaggerProvider
     */
    private $swaggerProvider;

    /**
     * @param string                $name
     * @param DataProviderInterface $configProvider
     * @param SwaggerProvider       $swaggerProvider
     */
    public function __construct(string $name = null, DataProviderInterface $configProvider, SwaggerProvider $swaggerProvider)
    {
        parent::__construct($name);
        $this->configProvider = $configProvider;
        $this->swaggerProvider = $swaggerProvider;
    }

    /**
     * Configure command
     */
    public function configure(): void
    {
        $defaultPath = __DIR__ . '/../../../../../../public/assets/swagger/data.json';
        $this
            ->setName('swagger-gen:generate-spec')
            ->setDescription('Command for generate json specification for swagger ui')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The destination of generated spec', $defaultPath)
            ->addOption('json_encode_options', null, InputOption::VALUE_OPTIONAL);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $swaggerSpec = $this->swaggerProvider->getSwaggerData($this->configProvider);
        $jsonData = json_encode($swaggerSpec, $input->getOption('json_encode_options'));

        file_put_contents($input->getOption('path'), $jsonData);
    }
}
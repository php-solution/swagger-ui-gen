<?php

namespace PhpSolution\SwaggerUIGen\Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SwaggerSpecCommand
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\Command
 */
class SwaggerSpecCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    public function configure(): void
    {
        $defaultPath = __DIR__ . '/../../../../../../web/assets/swagger/data.json';
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
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $configProvider = $container->get('swagger_uigen.data_provider');
        $swaggerSpec = $container->get('swagger_uigen.swagger_provider')->getSwaggerData($configProvider);
        $jsonData = json_encode($swaggerSpec, $input->getOption('json_encode_options'));

        file_put_contents($input->getOption('path'), $jsonData);
    }
}
<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer UI for Magento 2 (System)
 */

namespace Amasty\ImageOptimizerUi\Console\Command;

use Amasty\ImageOptimizerUi\Console\Command\Operation\Optimize;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OptimizeCommand extends ConsoleCommand
{
    public const IMAGE_SETTING_ID = 'settings_id';
    public const JOBS_AMOUNT = 'jobs';

    /**
     * @var Optimize
     */
    private $optimizeCommand;

    public function __construct(
        Optimize $optimizeCommand,
        $name = null
    ) {
        $this->optimizeCommand = $optimizeCommand;

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $options = [
            new InputOption(
                self::IMAGE_SETTING_ID,
                '-i',
                InputOption::VALUE_OPTIONAL,
                'Image Settings Id'
            ),
            new InputOption(
                self::JOBS_AMOUNT,
                '-j',
                InputOption::VALUE_OPTIONAL,
                'Enable parallel processing using the specified number of jobs.'
            ),
        ];

        $this->setName('amasty:optimizer:optimize')
            ->setDescription('Run image optimization script.')
            ->setDefinition($options);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        return $this->optimizeCommand->execute($input, $output);
    }
}

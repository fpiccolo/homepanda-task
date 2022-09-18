<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class TableHelper
{
    /**
     * @codeCoverageIgnore
     */
    public function instantiateTable(OutputInterface $output): Table
    {
        return new Table($output);
    }
}
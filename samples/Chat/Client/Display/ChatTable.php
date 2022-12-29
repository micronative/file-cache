<?php

namespace Samples\Chat\Client\Display;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class ChatTable
{
    private Table $table;

    public function __construct()
    {
        $this->table = new Table(new ConsoleOutput());
    }

    /**
     * @param array $header
     * @param array $data
     * @return void
     */
    public function render(array $header, array $data): void
    {
        $this->table->setHeaders($header)->setRows($data);
        $this->table->render();
    }

}
<?php declare(strict_types=1);

namespace MCampbell508\Git\Flamethrower\Console\Commands;

use MCampbell508\Git\Flamethrower\Console\CLImate;
use MCampbell508\Git\Flamethrower\Console\Command;

class Menu extends Command
{
    public function __construct(CLImate $CLImate, ?string $name = null)
    {
        parent::__construct($CLImate, $name);
    }

    protected function configure(){
        $this->setName("menu")
            ->setDescription("View and executed git commands via a menu");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
}
<?php declare(strict_types=1);

namespace MCampbell508\Git\Flamethrower\Console;

class Command extends SymfonyCommand
{
    protected $CLImate;

    public function __construct(CLImate $CLImate, ?string $name = null)
    {
        parent::__construct($name);
        $this->CLImate = $CLImate;
    }
}

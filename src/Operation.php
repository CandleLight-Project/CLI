<?php


namespace CandleLight\Artisan;

/**
 * Basic Operation Class for usage within Commands
 * @package CandleLight\Artisan
 */
class Operation{

    private $name;
    private $description;
    private $exec;

    /**
     * Operation constructor.
     * @param string $name Operation name
     * @param string $description Operation description
     * @param callable $exec Action behind this operation
     */
    public function __construct(string $name, string $description, callable $exec){
        $this->name = $name;
        $this->description = $description;
        $this->exec = $exec;
    }

    /**
     * Executes the Operation
     * @param array $args
     */
    public function run(array $args): void{
        $args = (isset($args['args'])) ? $args['args'] : [];
        ($this->exec)($this, $args);
    }

    /**
     * Returns the Operations name
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * Returns the Operations Description
     * @return string
     */
    public function getDescription(): string{
        return $this->description;
    }

    /**
     * Prints this Operations Help-String
     * @param Command $command Command this Operation is registered to
     */
    public function printHelp(Command $command){
        printf(Client::STRING_OPERATION, $command->getName(), $this->getName());
        printf(Client::STRING_DESCRIPTION, $this->getDescription());
    }
}
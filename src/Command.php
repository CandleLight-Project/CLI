<?php


namespace CandleLight\Artisan;

/**
 * Basic Command Wrapper for usage within the Artisan Client
 * @package CandleLight\Artisan
 */
class Command{

    private $name;
    private $description;
    private $function;

    /** @var Operation[] */
    private $operations = [];

    /**
     * Command constructor.
     * @param string $name Command name
     * @param string $description Command description
     * @param callable $function Function which configures the command
     */
    public function __construct(string $name, string $description, callable $function){
        $this->name = $name;
        $this->description = $description;
        $this->function = $function;
    }

    /**
     * Prepares the command and all its operations
     * by invoking the callable given in the constructor
     */
    public function prepare(): void{
        ($this->function)($this);
    }

    /**
     * Adds a new Operation to the Command
     * @param Operation $operation
     * @throws \Exception
     */
    public function addOperation(Operation $operation): void{
        if (isset($this->operations[$operation->getName()])) {
            throw new \Exception('op already defined');
        }
        $this->operations[$operation->getName()] = $operation;
    }

    /**
     * Executes the Command and the matching operation based on the given artisan config array
     * @param array $args
     */
    public function exec(array $args): void{
        if (!isset($args['operation']) || !isset($this->operations[$args['operation']])) {
            $this->printHelp();
            return;
        }
        $op = $this->operations[$args['operation']];
        $op->run($args);
    }

    /**
     * Returns the Commands Name
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * Returns the Commands Description
     * @return string
     */
    public function getDescription(): string{
        return $this->description;
    }

    /**
     * Returns the list of all registered Operations
     * @return Operation[]
     */
    public function getOperations(): array{
        return $this->operations;
    }

    /**
     * Prints the Commands full help-string
     */
    public function printHelp(): void{
        printf(Client::STRING_COMMAND, $this->getName());
        printf(Client::STRING_DESCRIPTION, $this->getDescription());
        foreach ($this->operations as $operation) {
            $operation->printHelp($this);
        }
    }
}
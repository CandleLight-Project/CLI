<?php


namespace CandleLight\Artisan;

/**
 * Main Artisan Client
 * @package CandleLight\Artisan
 */
class Artisan{

    const STRING_COMMAND = '%s:<operation>' . PHP_EOL;
    const STRING_DESCRIPTION = '    %s' . PHP_EOL;
    const STRING_OPERATION = '%s:%s' . PHP_EOL;

    /** @var Command[] */
    private $commands = [];

    /** @var Command[] */
    private $stagedCommands = [];

    /**
     * Adds a Command to the Artisan Client
     * @param Command $command
     */
    public function addCommand(Command $command){
        array_push($this->stagedCommands, $command);
    }

    /**Registers a Commmand in the Artisan Client
     * @param Command $command
     * @throws \Exception
     */
    private function registerCommand(Command $command){
        if (isset($this->commands[$command->getName()])) {
            throw new \Exception('command already defined');
        }
        $this->commands[$command->getName()] = $command;
    }

    /**
     * Starts executing the Artisan Client based on the given argv array
     * @param array $argv
     */
    public function dispatch(array $argv): void{
        try {
            $this->prepareCommands();
            $this->execute(self::cleanArgv($argv));
        } catch (\Exception $e) {
            echo $e->getMessage();
            return;
        }
    }

    /**
     * Prepares and registers all commands in the Artisan Client
     * @throws \Exception
     */
    private function prepareCommands(): void{
        foreach ($this->stagedCommands as $command) {
            $this->registerCommand($command);
            $command->prepare();
        }
    }

    /**
     * Prints the Help-String
     */
    private function printHelp(){
        foreach ($this->commands as $command) {
            echo PHP_EOL;
            $command->printHelp();
        }
    }

    /**
     * Prepares the argv array and builds the config string for the artisan dispatcher
     * @param array $argv
     * @return array
     */
    private static function cleanArgv(array $argv){
        if (!isset($argv[1])) {
            return [];
        }
        $cmd = explode(':', $argv[1]);
        $data = [
            'command' => $cmd[0]
        ];
        if (isset($cmd[1])) {
            $data['operation'] = $cmd[1];
        }
        if (isset($argv[2])) {
            $data['args'] = array_slice($argv, 2);
        }
        return $data;
    }

    /**
     * Prompt the user with the given message
     * @param string $message Message to display to the user
     * @param callable|null $validation predicate which needs to be fulfilled for the input to be accepted
     * @return string user input string
     */
    public static function prompt(string $message, callable $validation = null): string{
        if (is_null($validation)) {
            $validation = function (){
                return true;
            };
        }

        do {
            $data = readline(trim($message) . ' ');
        } while (!($validation)($data));

        readline_add_history($data);
        return $data;
    }

    /**
     * Asks the User a yes/no question and allows y and n as an answer
     * @param string $message Message to display to the user
     * @param callable|null $yes function to execute on accept
     * @param callable|null $no function to execute on decline
     * @return bool
     */
    public static function confirm(string $message, callable $yes = null, callable $no = null): bool{
        $response = self::prompt($message . '[y|n]');
        $response = strtolower($response);
        if ($response === 'y') {
            if (!is_null($yes)) {
                ($yes)();
            }
            return true;
        }
        if (!is_null($no)) {
            ($no)();
        }
        return false;
    }

    /**
     * Executes the command based on the given artisan config array
     * @param array $args
     */
    private function execute(array $args): void{
        if (empty($args) || !isset($this->commands[$args['command']])) {
            $this->printHelp();
            return;
        }
        $this->commands[$args['command']]->exec($args);
    }
}
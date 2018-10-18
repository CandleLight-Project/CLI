<?php


namespace CandleLight\Artisan;

/**
 * Basic Template Copy Helper
 * @package CandleLight\Artisan
 */
class Template{

    private $from;
    private $to;
    private $fields;
    private $content;

    /**
     * Template constructor.
     * @param string $from path to the template file
     * @param string $to path to the target file
     * @param array $fields key value pair to search and replace
     * @throws \Exception
     */
    public function __construct(string $from, string $to, array $fields = []){
        $this->from = $from;
        $this->to = $to;
        $this->fields = $fields;
        if (!file_exists($this->from)) {
            throw new \Exception('File does not exist ' . $this->from);
        }
    }

    /**
     * Fetches  the  template data, replaces the tokens and saves the result in the target file
     * @throws \Exception
     */
    public function process(): void{
        if (file_exists($this->to) && !Client::confirm('The Template-File already exists. Do you want to replace it?')) {
            throw new \Exception('Target file does exist already');
        }
        $this->content = file_get_contents($this->from);
        $content = self::replaceData($this->content, $this->fields);
        file_put_contents($this->to, $content);
    }

    /**
     * Replaces mustache-tagged keys with the given value
     * @param string $data string to filter
     * @param array $fields key value pair to search and replace
     * @return string
     */
    public static function replaceData(string $data, array $fields): string{
        foreach ($fields as $key => $replace) {
            $data = preg_replace('/\{\{\s*' . $key . '\s*\}\}/', $replace, $data);
        }
        return $data;
    }
}
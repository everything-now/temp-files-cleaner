<?php

class CommandExecutor 
{
    /**
     * Command arguments
     *
     * @var array
     */
    public $args = [
        'rootPath'     => null,
        'delDirectory' => false,
        'extension'    => 'bak'
    ];

    /**
     * Create a new command executor instance.
     *
     * @return void
     */
    function __construct()
    {
        $opts = $this->validateInput();

        $this->declareArgs($opts);
    }

    /**
     * Validation of input options
     *
     * @return mixed
     */
    private function validateInput()
    {
        $opts = getopt('p:h:d::', ['ext:']);

        if(isset($opts['h']) || !isset($opts['p'])){
            return $this->outputHelp();
        
        } elseif (!is_dir($opts['p'])){
            return $this->outputError('Wrong directory path');
        }

        return $opts;
    }

    /**
     * Arguments declaration
     *
     * @param array $opts
     * @return array
     */
    private function declareArgs(array $opts)
    {
        $this->args['rootPath'] = $opts['p'];
        
        $this->args['delDirectory'] = isset($opts['d']);

        if(isset($opts['ext'])){
            $this->args['extension'] = $opts['ext'];
        }

        return $this->args;
    }

    /**
     * Show text end exit command
     *
     * @param string $text
     * @return void
     */
    private function output(string $text)
    {
        exit($text);
    }

    /**
     * Show result text end exit command
     *
     * @param Cleaner $result
     * @return void
     */
    public function outputResult(Cleaner $result)
    {
        $red     = "\e[91m";
        $green   = "\e[92m";
        $default = "\e[39m";

        $text  = "Operation completed. \n"; 
        $text  .= "$green $result->countDeletedFiles $default - deleted files \n"; 
        $text  .= "$green $result->countDeletedDirectories $default - deleted directories \n";
        
        if($result->countErrors){
            $text  .= "$red $result->countErrors $default - errors \n"; 
        }

        return $this->output($text);
    }

    /**
     * Output text error end exit command
     *
     * @param string $text
     * @return void exit
     */
    public function outputError(string $text)
    {
        $red = "\e[91m";

        return $this->output($red . $text);
    }

    /**
     * Formating help text and exit
     *
     * @return void exit
     */
    private function outputHelp()
    {
        $yellow  = "\e[93m";
        $green   = "\e[92m";
        $default = "\e[39m";

        $text  = "$yellow Description: $default \n"; 
        $text .= "    Run command to remove your temporary .bak files or anything else \n\n";
        
        $text .= "$yellow Usage: $default \n"; 
        $text .= "    php clean $green <options> $default \n\n";

        $text .= "$yellow Options: $default \n"; 
        $text .= "    $green -p $default       Directory path \n";
        $text .= "    $green -d $default       Delete the directory if it is empty \n";
        $text .= "    $green -h $default       Display this text and exit immediately \n";
        $text .= "    $green --ext $default    Set extension (default .bak) \n";
        
        return $this->output($text);
    }
}
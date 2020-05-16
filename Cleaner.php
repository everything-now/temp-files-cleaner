<?php

class Cleaner
{
    public $countDeletedFiles = 0;
    public $countDeletedDirectories = 0;
    public $countErrors = 0;
    private $delDirectory;
    private $extension;

    /**
     * Create a new cleaner instance.
     *
     * @param array $args
     * @return void
     */
    function __construct(array $args)
    {
        $this->delDirectory = $args['delDirectory'];
        
        $this->extension = $args['extension'];

        $this->scanDirectory($args['rootPath']);
    }

    /**
     * Directory cleaning and scanning for subdirectories
     *
     * @param string $path
     * @return void
     */
    private function scanDirectory(string $path)
    {
        $subDirectories = glob($path . '/*' , GLOB_ONLYDIR);

        foreach($subDirectories as $subDirectoryPath){
            $this->scanDirectory($subDirectoryPath);
        }

        $this->cleanDirectory($path);
    }

    /**
     * Deleted files from directory by extension and directory itself
     *
     * @param [type] $path
     * @return void
     */
    private function cleanDirectory(string $path)
    {
        $ext = $this->extension[0] == '.' ? $this->extension : '.' . $this->extension;

        $files = glob($path . '/*' . $ext);

        foreach($files as $file){
            
            if($ext != '.bak' || $this->canDeleteDotBakFile($file)){
                $this->deleteFile($file);
            }
        }

        if($this->delDirectory && empty(glob($path . '/*'))){
            $this->deleteDirectory($path);
        }
    }

    /**
     * Delete determined file and increase operation result
     *
     * @param string $file
     * @return bool
     */
    private function deleteFile(string $file)
    {
        if(is_writable($file) && unlink($file)){
            $this->countDeletedFiles++;

            return true;
        }
        $this->countErrors++;

        return false;
    }

    /**
     * Delete determined directory and increase operation result
     *
     * @param string $file
     * @return bool
     */
    private function deleteDirectory(string $path)
    {
        if(is_writable($path) && rmdir($path)){
            $this->countDeletedDirectories++;

            return true;
        }
        $this->countErrors++;

        return false;
    }

    /**
     * Check if exists other files with the same filename
     *
     * @param string $file
     * @return boolean
     */
    private function canDeleteDotBakFile(string $file)
    {
        $sameNameFiles = glob(str_replace('.bak', '', $file) . '.*[!bak]');
                
        return empty($sameNameFiles);
    }

}
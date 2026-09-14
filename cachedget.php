<?php
namespace lordmatt\tools\cachedget;

/**
 * This library is designed to do one thing rather well ie. fetch a thing from a
 * website and then cache it so the next request is local.  This makes use of an
 * injected file fetcher so you can swap out the curl version for something that
 * suits your project better.
 * 
 * @version 1.0.0
 * @author Lord Matt <https://lordmatt.co.uk>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU GPL3
 * @requires PHP 8.0+
 *
 * @author lordmatt
 */
class cachedget {
    
    private $file_prefix = 'cache_';
    private $directory ='.';
    private $max_cache_age = 60*60*34*7;
    private $file_getter; // implements i_file_getter
    
    public function __construct(string $directory='.') {
        $this->directory = $directory;
    }
    
    
    public function set_max_cache_age(int $inseconds):cachedget{
        $this->max_cache_age = $inseconds;
        return $this;
    }
    
    /**
     * Inject a class that implements i_file_getter for the actual getting of 
     * the file.
     * 
     * @param i_file_getter $getter
     * @return cachedget
     */
    public function set_getter(i_file_getter $getter):cachedget{
        $this->file_getter = $getter;
        return $this;
    }
    
    /**
     * The full path to the working cache directory without a trailing slash at
     * the end.
     * @param string $directory
     * @return cachedget
     */
    public function set_working_directory(string $directory):cachedget{
        $this->directory = $directory;
        return $this;
    }
    /**
     * 
     * @return string
     */
    public function get_working_directory(): string{
        return $this->directory;
    }
    
    /**
     * The file prefix is used at the start of generated file names
     * @param string $new_prefix
     * @return cachedget
     */
    public function set_prefix(string $new_prefix):cachedget{
        $this->file_prefix = $new_prefix;
        return $this;
    }
    
    /**
     * The file prefix is used at the start of generated file names
     * @return string
     */
    public function get_prefix(): string{
        return $this->file_prefix;
    }
    
    /**
     * Fetch the remote file contents
     * @param string $url
     * @return string|mixed
     */
    public function get(string $url){
        $filename = $this->filename($url);
        if($this->is_cached($filename)){
            return file_get_contents($filename);
        }
        if(!isset($this->file_getter)){
            $this->file_getter = new curl_getter();
        }
        $file = $this->file_getter->get_remote($url);
        $this->cache_file($filename, $file);
        return $file;
    }
    
    private function filename(string $url): string{
        return $this->file_prefix . sha1($url);
    }
    
    private function cache_file(string $filename, string $file):int|bool{
        return file_put_contents($filename,$file);
    }
    
    private function is_cached($file){
        if (file_exists($file)){
            if (time()-filemtime($file) > $this->max_cache_age) {
                unlink($file); // too old
                return false;
            } else {
                return true;
            } 
        }
        return false; // file not found
    }
    
}



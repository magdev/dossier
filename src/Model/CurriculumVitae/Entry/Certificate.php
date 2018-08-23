<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 magdev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    magdev
 * @copyright 2018 Marco Grätsch
 * @package   magdev/dossier
 * @license   http://opensource.org/licenses/MIT MIT License
 */
 
namespace Magdev\Dossier\Model\CurriculumVitae\Entry;

/**
 * Certificate Model
 * 
 * @author magdev
 */
class Certificate 
{
    /**
     * Store the file object
     * 
     * @var \SplFileObject
     */
    protected $file = null;
    
    /**
     * Store the type of this certificate
     * 
     * @var string
     */
    protected $type = '';
    
    
    /**
     * Constructor
     * 
     * @param string $path
     * @param string $type
     */
    public function __construct(string $path, string $type = '')
    {
        $this->file = new \SplFileObject($path);
        $this->setType($type);
    }
    
    
    /**
     * Get the file object
     * 
     * @return \SplFileObject
     */
    public function getFileObject(): \SplFileObject
    {
        return $this->file;
    }
    
    
    /**
     * Set the type og the certificate
     * 
     * @param string $type
     * @return \Magdev\Dossier\Model\CurriculumVitae\Entry\Certificate
     */
    public function setType(string $type): Certificate
    {
        $this->type = $type;
        return $this;
    }
    
    
    /**
     * Get the type of the certificate
     * 
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    
    /**
     * Get the file contents as Data-URI
     * 
     * @TODO Refactor to use UriHelperService
     * @deprecated Use UriHelperService instead
     * @return string
     */
    public function getDataUri(): string
    {
        $fi = new \finfo();
        $mimetype = $fi->file($this->file->getRealPath(), FILEINFO_MIME);
        
        return 'data:'.$mimetype.'; base64,'.base64_encode(file_get_contents($this->file->getRealPath()));
    }
}
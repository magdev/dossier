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
 
namespace Magdev\Dossier\Model\Traits;

use Magdev\Dossier\Model\AbstractModel;
use Magdev\Dossier\Model\Base\BaseModel;
use Magdev\Dossier\Model\Base\PhotoInterface;

/**
 * Trait to add a photo to a model
 * 
 * @author magdev
 */
trait PhotoTrait
{
    /**
     * Photo object
     * @var \SplFileInfo
     */
    protected $photo = null;
    
    /**
     * Size of the Photo
     * @var int
     */
    protected $photoSize = -1;
    
    
    
    /**
     * Get the photo
     * 
     * @return \SplFileInfo
     */
    public function getPhoto(): \SplFileInfo
    {
        return $this->photo;
    }
    
    
    /**
     * Check if the model has a photo
     * 
     * @return bool
     */
    public function hasPhoto(): bool
    {
        return $this->photo instanceof \SplFileInfo;
    }
    
    
    /**
     * Get the file contents as Data-URI
     *
     * @TODO Refactor to use UriHelperService
     * @deprecated Use UriHelperService instead
     * @return string
     */
    public function getPhotoDataUri(): string
    {
        if ($this->hasPhoto()) {
            $fi = new \finfo();
            $mimetype = $fi->file($this->photo->getRealPath(), FILEINFO_MIME);
            
            return 'data:'.$mimetype.'; base64,'.base64_encode(file_get_contents($this->photo->getRealPath()));
        } 
        return '';
    }
    
    
    /**
     * Get the size of the photo
     * 
     * @return int
     */
    public function getPhotoSize(): int
    {
        if ($this->photoSize == -1) {
            if ($this->hasPhoto()) {
                if ($size = getimagesize($this->photo->getRealPath())) {
                    $this->photoSize = $size[0];
                }
            }
        }
        return $this->photoSize;
    }
    
    
    /**
     * Set the photo
     *
     * @param string $path
     * @return \Magdev\Dossier\Model\Base\PhotoInterface
     */
    public function setPhoto(string $path): PhotoInterface
    {
        $this->photo = new \SplFileInfo(PROJECT_ROOT.'/'.$path);
        return $this;
    }
}


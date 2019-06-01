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
 
namespace Magdev\Dossier\Model;

use Magdev\Dossier\Model\Base\BaseModel;
use Magdev\Dossier\Model\Traits\ToggableTrait;
use Mni\FrontYAML\Document;

/**
 * Model for projects page
 *
 * @author magdev
 */
class Project extends BaseModel
{
    use ToggableTrait;
    
    /**
     * Project name
     * @var string
     */
    protected $name = '';
    
    /**
     * Public URLs
     * @var array
     */
    protected $urls = array();
    
    /**
     * Short Description
     * @var string
     */
    protected $shortDescription = '';
    
    /**
     * Project stack
     * @var string
     */
    protected $stack = '';
    

    public function getStack(): string
    {
        return $this->stack;
    }


    public function getName(): string
    {
        return $this->name;
    }

    
    public function hasUrls(): bool
    {
        return sizeof($this->urls) > 0;
    }


    public function getUrls(): array
    {
        return $this->urls;
    }


    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }
}

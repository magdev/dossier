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
 
namespace Magdev\Dossier\Model\Base;

use Mni\FrontYAML\Document;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Magdev\Dossier\Model\Traits\VarnameConverterTrait;

/**
 * Abstract model for Markdown files
 * 
 * @author magdev
 */
abstract class BaseModel
{
    use VarnameConverterTrait;
    
    /**
     * FrontYAML Document
     * @var \Mni\FrontYAML\Document
     */
    protected $document = null;
    
    /**
     * Additional data 
     * @var \ArrayObject
     */
    protected $additionalData = null;
    
    /**
     * Set some properties to ignore while analysing
     * @var \ArrayObject
     */
    protected $ignoredProperties = null;
    
    
    /**
     * Constructor
     * 
     * @param \Mni\FrontYAML\Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->additionalData = new \ArrayObject();
        $this->ignoredProperties = new \ArrayObject(array(
            'ignoreProperties',
            'document'
        ));
        $this->load();
    }
    
    
    /**
     * Get the document object
     * 
     * @return \Mni\FrontYAML\Document
     */
    public function getDocument(): Document
    {
        return $this->document;
    }
    
    
    /**
     * Get the parsed Markdown part of the file
     * 
     * @return string
     */
    public function getContent(): string
    {
        return $this->document->getContent();
    }
    
    
    /**
     * Get the YAML content of the file
     * 
     * @return array
     */
    public function getYAML(): array
    {
        $yaml = $this->document->getYAML();
        if (is_null($yaml)) {
            return array();
        }
        return $yaml;
    }
    
    
    /**
     * Wrapper to access the page content as description
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getContent();
    }
    
    
    /**
     * Get additional data
     * 
     * @return \ArrayObject
     */
    public function getAdditionalData(): \ArrayObject
    {
        return $this->additionalData;
    }
    
    
    /**
     * Check if model has additional data
     *
     * @return bool
     */
    public function hasAdditionalData(): bool
    {
        return $this->additionalData->count() > 0 ? true : false;
    }
    
    
    /**
     * Get an array with all data values
     * 
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
    
    
    /**
     * Add an ignored property
     * 
     * @param string $prop
     * @return BaseModel
     */
    protected function addIgnoredProperty(string $prop): BaseModel
    {
        if (!in_array($prop, $this->ignoredProperties->getArrayCopy())) {
            $this->ignoredProperties->append($prop);
        }
        return $this;
    }
    
    
    /**
     * Add multiple ignored prperties
     * 
     * @param array $props
     * @return BaseModel
     */
    protected function addIgnoredProperties(array $props): BaseModel
    {
        foreach ($props as $prop) {
            $this->addIgnoredProperty($prop);
        }
        return $this;
    }
    
    
    /**
     * Add additional data
     * 
     * @param string $key
     * @param mixed $value
     * @return \Magdev\Dossier\Model\Base\BaseModel
     */
    protected function addAdditionalData(string $key, $value): BaseModel
    {
        $this->additionalData->append(array('key' => $key, 'value' => $value));
        return $this;
    }
    
    
    /**
     * Set a property
     * 
     * @param string $key
     * @param mixed $value
     * @return \Magdev\Dossier\Model\Base\BaseModel
     */
    protected function setProperty(string $key, $value): BaseModel
    {
        $prop = $this->convertCase($key);
        if (property_exists($this, $prop)) {
            $this->{$prop} = $value;
        } else if ($this instanceof BaseModel) {
            $this->addAdditionalData($prop, $value);
        } else {
            throw new InvalidArgumentException('Unknow property: '.__CLASS__.'::'.$prop);
        }
        return $this;
    }
    
    
    /**
     * Load YAML data into model properties
     * 
     * @return \Magdev\Dossier\Model\Base\BaseModel
     */
    protected function load(): BaseModel
    {
        $yaml = $this->getYAML();
        return $this->loadArray($yaml);
    }
    
    
    /**
     * Laod data from array
     * 
     * @param array $data
     * @return \Magdev\Dossier\Model\Base\BaseModel
     */
    protected function loadArray(array $data): BaseModel
    {
        foreach ($data as $key => $value) {
            $this->setProperty($key, $value);
        }
        return $this;
    }
}
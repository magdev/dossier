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

use Mni\FrontYAML\Document;
use Magdev\Dossier\Model\Base\BaseModel;
use Magdev\Dossier\Model\Base\ModelExportableInterface;
use Magdev\Dossier\Model\Base\PhotoInterface;
use Magdev\Dossier\Model\Traits\PhotoTrait;
use Magdev\Dossier\Analyzer\Base\AnalyzableInterface;

/**
 * Model for introduction page
 * 
 * @author magdev
 */
class Intro extends BaseModel implements PhotoInterface, AnalyzableInterface
{
    const SHOW_TOP = 'top';
    const SHOW_BOTTOM = 'bottom';
    
    use PhotoTrait;
    
    /**
     * Quotes
     * @var \ArrayObject
     */
    protected $quotes = null;
    
    /**
     * Headline for intro page
     * @var string
     */
    protected $headline = '';
    
    /**
     * Place to show the quotes
     * 
     * @var string
     */
    protected $showQuotes = self::SHOW_TOP;
    

    /**
     * Get the headline
     * 
     * @return string
     */
    public function getHeadline(): string
    {
        return $this->headline;
    }



    /**
     * Constructor
     * 
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->quotes = new \ArrayObject();
        parent::__construct($document);
    }
    
    
    /**
     * Get the quotes
     * 
     * @return \ArrayObject
     */
    public function getQuotes(): \ArrayObject
    {
        return $this->quotes;
    }
    
    
    /**
     * Get the place where to show the quotes
     * 
     * @return string
     */
    public function getShowQuotes(): string
    {
        return $this->showQuotes;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Model\Base\BaseModel::loadArray()
     */
    protected function loadArray(array $data): BaseModel
    {
        foreach ($data as $key => $value) {
            if ($key == 'quotes') {
                foreach ($value as $quote) {
                    $q = new Intro\Quote($quote);
                    $this->quotes->append($q);
                }
            } else if ($key == 'photo') {
                $this->setPhoto($value);
            }  else {
                $this->setProperty($key, $value);
            }
        }
        return $this;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Magdev\Dossier\Analyzer\Base\AnalyzableInterface::getAnalyzerData()
     */
    public function getAnalyzerData(): array
    {
        $props = get_object_vars($this);
        foreach ($this->ignoredProperties as $prop) {
            unset($props[$prop]);
        }
        $props['text'] = $this->getContent();
        return $props;
    }
    
}

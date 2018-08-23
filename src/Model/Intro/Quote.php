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
 
namespace Magdev\Dossier\Model\Intro;


use Magdev\Dossier\Model\AbstractModel;

/**
 * Quote Model
 * 
 * @author magdev
 */
final class Quote
{
    /**
     * Quote text
     * @var string
     */
    protected $quoteText = '';
    
    /**
     * Author of the quote
     * @var string
     */
    protected $quoteAuthor = '';
    
    
    /**
     * Constructor
     * 
     * @param array $quote
     */
    public function __construct(array $quote)
    {
        $this->quoteText = $quote['text'];
        if (isset($quote['author'])) {
            $this->quoteAuthor = $quote['author'];
        }
    }
    
    
    /**
     * Get the quote text
     * 
     * @return string
     */
    public function getQuoteText(): string
    {
        return $this->quoteText;
    }
    

    /**
     * Get the author of the quote
     * 
     * @return string
     */
    public function getQuoteAuthor(): string
    {
        return $this->quoteAuthor;
    }
    
}

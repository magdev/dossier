<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2019 magdev
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
 * @copyright 2019 Marco Grätsch
 * @package   magdev/dossier
 * @license   http://opensource.org/licenses/MIT MIT License
 */
 
namespace Magdev\DossierTests\Util;

use PHPUnit\Framework\TestCase;
use Magdev\Dossier\Util\DataCollector;

class DataCollectorTest extends TestCase
{
    /**
     * @var array
     */
    private $initData = ['foo'];
    
    
    /**
     * @group core
     * @covers \Magdev\Dossier\Util\DataCollector::addData()
     * @covers \Magdev\Dossier\Util\DataCollector::hasData()
     * @covers \Magdev\Dossier\Util\DataCollector::setData()
     * @covers \Magdev\Dossier\Util\DataCollector::getData()
     */
    public function testSetGetData()
    {
        $data = new DataCollector($this->initData);
        $this->assertEquals(true, $data->hasData());
        
        $data->setData(1, 'bar');
        $this->assertEquals(['foo', 'bar'], $data->getData());
    }
    
    
    /**
     * @group core
     * @covers \Magdev\Dossier\Util\DataCollector::merge()
     */
    public function testMergeData()
    {
        $data = new DataCollector($this->initData);
        $data2 = new DataCollector(['bar']);
        
        $data->merge($data2);
        $this->assertEquals(['foo', 'bar'], $data->getData());
    }
}

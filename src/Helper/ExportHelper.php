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
 
namespace Magdev\Dossier\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * Export helper class
 * 
 * @author magdev
 */
class ExportHelper extends Helper
{
    const JSON = 'json';
    const YAML = 'yaml';
    const PHP  = 'php';
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Helper\HelperInterface::getName()
     */
    public function getName()
    {
        return 'export';
    }
    
    /**
     * Convert an assoziatve array to a tablerow-style array
     * 
     * @param array $source
     * @return array
     */
    public function arrayToTableRow(array $source): array
    {
        return array(array_values($source));
    }
    
    
    /**
     * Convert an array of assoziatve arrays to a tablerows-style array
     * 
     * @param array $source
     * @return array
     */
    public function arrayToTableRows(array $source): array
    {
        $rows = array();
        foreach ($source as $values) {
            $rows[] = $this->arrayToTableRow($values);
        }
        return $rows;
    }
    
    
    /**
     * Convert tablerow-style array to an assoziatve array
     *
     * @param array $source
     * @return array
     */
    public function tableRowToArray(array $source, array $keys): array
    {
        return array_combine($keys, array_shift($source));
    }
    
    
    /**
     * Convert an array of tablerow-style arrays to an array of assoziatve arrays
     *
     * @param array $source
     * @return array
     */
    public function tableRowsToArray(array $source, array $keys): array
    {
        $array = array();
        foreach ($source as $row) {
            $array[] = $this->tableRowToArray($row, $keys);
        }
        return $array;
    }
    
    
    /**
     * Check if an array is a tablerow-style array
     * 
     * @param array $array
     * @return bool
     */
    public function isTableRowStyle(array $source): bool
    {
        return isset($source[0]) === true;
    }
    
    
    /**
     * Export data to a given format
     * 
     * @param array $source
     * @param string $type (json|yaml)
     * @param string $file
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     * @return bool
     */
    public function export(array $source, string $type, string $file): bool
    {
        $code = $this->convert($source, $type);
        
        if ($bytes = file_put_contents($file, $code) === false) {
            throw new RuntimeException('Error while writing target: '.$file);
        }
        return true;
    }
    
    
    /**
     * Convert data to a given format
     * 
     * @param array $source
     * @param string $type
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     * @return string
     */
    public function convert(array $source, string $type): string
    {
        switch ($type) {
            case self::JSON: return $this->toJSON($source);
            case self::YAML: return $this->toYAML($source);
            case self::PHP: return $this->toPHP($source);
            
            default:
                throw new RuntimeException('Invalid export type: '.strtoupper($type));
        }
    }
    
    
    /**
     * Convert an assoziatve array to JSON
     * 
     * @param array $source
     * @param bool $pretty
     * @return string
     */
    public function toJSON(array $source, bool $pretty = true): string
    {
        if ($pretty) {
            return json_encode($source, JSON_FORCE_OBJECT|JSON_PRETTY_PRINT);
        }
        return json_encode($source, JSON_FORCE_OBJECT);
    }
    
    
    /**
     * Convert an assoziatve array to YAML
     *
     * @param array $source
     * @param int $depth
     * @param int $indent
     * @return string
     */
    public function toYAML(array $source, int $depth = 3, int $indent = 2): string
    {
        return Yaml::dump($source, $depth, $indent);
    }
    
    
    /**
     * Convert values to PHP sourcecode
     * 
     * @param mixed $source
     * @param bool $includeReturn
     * @param bool $includeTags
     * @return string
     */
    public function toPHP($source, bool $includeReturn = false, bool $includeTags = false): string
    {
        $code = '';
        if ($includeTags) {
            $code .= '<?php'.PHP_EOL;
        }
        if ($includeReturn) {
            $code .= 'return ';
        }
        $code .= var_export($source, true);
        if ($includeReturn || $includeTags) {
            $code .= ';'.PHP_EOL;
        }
        return $code;
    }
}
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
 
namespace Magdev\Dossier\Form\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Droath\ConsoleForm\FormDiscovery as BaseFormDiscovery;
use Droath\ConsoleForm\FormInterface;
use Magdev\Dossier\Form\Base\FormBuilderInterface;

class FormDiscovery extends BaseFormDiscovery
{
    /**
     * Container
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container = null;
    
    
    /**
     * Constructor 
     * 
     * @param string $depth
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container, $depth = '< 3')
    {
        parent::__construct($depth);
        $this->container = $container;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormDiscovery::doDiscovery()
     */
    protected function doDiscovery(array $directories, $base_namespace)
    {
        $forms = [];
        $filter = $this->filterByNamespace($base_namespace);
        $config = $this->container->get('config');
        $translator = $this->container->get('translator');
        
        foreach ($this->searchFiles($directories, $filter) as $file) {
            $ext = $file->getExtension();
            $classname = $base_namespace . '\\' . $file->getBasename(".$ext");
            
            if (!class_exists($classname)) {
                throw new \Exception('Missing class found during form discovery. '.$classname);
            }
            $instance = new $classname();
            /* @var $instance \Magdev\Dossier\Form\Base\FormBuilderInterface */
            
            if (!$instance instanceof FormInterface) {
                throw new \Exception(sprintf('Form class (%s) is missing \Droath\ConsoleForm\Form\FormInterface.', $classname));
            }
            
            if ($instance instanceof FormBuilderInterface) {
                $forms[$instance->getName()] = $instance->setConfig($config)
                    ->setTranslator($translator)
                    ->buildForm();
            } else {
                $forms[$instance->getName()] = $instance->buildForm();
            }
        }
        
        return $forms;
    }
}


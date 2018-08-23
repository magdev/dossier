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
 
namespace Magdev\Dossier;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Droath\ConsoleForm\FormHelper;
use Magdev\Dossier\Form\Extension\FormDiscovery;
use Magdev\Dossier\Helper\ExportHelper;
use Magdev\Dossier\Helper\OutputHelper;
use Magdev\Dossier\Analyzer\DossierStatusAnalyzer;
use Magdev\Dossier\Analyzer\ModelStatusAnalyzer;
use Magdev\Dossier\Helper\SectionManagerHelper;

/**
 * ContainerAware Application
 * 
 * @author magdev
 */
class Application extends BaseApplication
{
    /**
     * Store the di-container
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container = null;
    
    
    /**
     * Constructor
     * 
     * @param string $name
     * @param string $version
     */
    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator(DOSSIER_ROOT.'/app/conf'));
        $loader->load('services.yaml');
        
        $formDiscovery = new FormDiscovery($this->container);
        $forms = $formDiscovery->discover(DOSSIER_ROOT.'/src/Form', '\Magdev\Dossier\Form');
        
        $helperSet = $this->getHelperSet();
        $helperSet->set(new FormHelper($forms));
        $helperSet->set(new ExportHelper());
        $helperSet->set(new OutputHelper($this->getName(), $this->getVersion()));
        $helperSet->set(new SectionManagerHelper($this->container->get('config')));
        
        $this->container->get('analyzer')
            ->addAnalyzer(new ModelStatusAnalyzer());
    }
    
    
    /**
     * Get the service-contianer
     * 
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }
    
    
    /**
     * Get a service by its name
     * 
     * @param string $name
     * @param int $invalidBehavior
     * @return object|\Symfony\Component\DependencyInjection\Container|mixed|void|unknown
     */
    public function getService(string $name, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->getContainer()->get($name, $invalidBehavior);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Application::getLongVersion()
     */
    public function getLongVersion()
    {
        $code = '<fg=magenta;options=bold>'.base64_decode(DOSSIER_LOGO).PHP_EOL;
        $header = $this->getService('translator')->trans('app.header');
        
        if ('UNKNOWN' !== $this->getName()) {
            if ('UNKNOWN' !== $this->getVersion()) {
                $code .= ' '.$this->getName().' '.$this->getVersion().PHP_EOL;
            } else {
                $code .= ' '.$this->getName().PHP_EOL;
            }
        }
        $code .= ' '.$header.'</>';
        
        return $code;
    }
}

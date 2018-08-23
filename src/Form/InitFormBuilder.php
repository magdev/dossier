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
 
namespace Magdev\Dossier\Form;

use Magdev\Dossier\Form\Extension\Form;
use Magdev\Dossier\Form\Field\SelectField;
use Magdev\Dossier\Form\Field\TextField;
use Magdev\Dossier\Form\Field\BooleanField;

/**
 * IntroFormBuilder
 *
 * @author magdev
 */
class InitFormBuilder extends Base\BaseFormBuilder implements Base\FormBuilderInterface
{
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormInterface::getName()
     */
    public function getName()
    {
        return 'form.init';
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormInterface::buildForm()
     */
    public function buildForm()
    {
        $form = new Form();
        $form->setLabelPrefix($this->config->get('form.label.prefix'))
            ->setLabelLength($this->config->get('form.label.length'));
        
        $form->addField((new BooleanField('use_userstyles', $this->translator->trans('form.init.use_userstyles')))
            ->setSubform(function($subform, $value) {
                if ($value === true) {
                    $subform->addFields(array(
                        (new SelectField('userstyle_type', $this->translator->trans('form.init.userstyle_type')))
                            ->setOptions(array('SCSS', 'LESS'))
                            ->setDefault('SCSS')
                            ->setRequired(true)
                    ));
                }
            }));
        
        return $form;
    }
}


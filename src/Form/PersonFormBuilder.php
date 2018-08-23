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

use Droath\ConsoleForm\FieldGroup;
use Magdev\Dossier\Form\Extension\Form;
use Magdev\Dossier\Form\Field\SelectField;
use Magdev\Dossier\Form\Field\TextField;
use Magdev\Dossier\Form\Field\BooleanField;

/**
 * IntroFormBuilder
 * 
 * @author magdev
 */
class PersonFormBuilder extends Base\BaseFormBuilder implements Base\FormBuilderInterface
{
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormInterface::getName()
     */
    public function getName()
    {
        return 'form.person';
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Droath\ConsoleForm\FormInterface::buildForm()
     */
    public function buildForm()
    {
        $form = new Form();
        $form->setLabelPrefix($this->config->get('form.label.prefix'))
            ->setLabelLength($this->config->get('form.label.length'))
            ->addFormLoadCallback($this->getUnflattenCallback())
            ->addFormResultsCallback($this->getFlattenCallback());
        
        $form->addField(new TextField('firstame', $this->translator->trans('form.person.firstname')));
        $form->addField(new TextField('lastame', $this->translator->trans('form.person.lastname')));
        $form->addField(new TextField('tagline', $this->translator->trans('form.person.tagline'), false));
        $form->addField(new TextField('nationality', $this->translator->trans('form.person.nationality'), false));
        $form->addField(new TextField('birthdate', $this->translator->trans('form.person.birthdate'), false));
        $form->addField((new TextField('birthplace', $this->translator->trans('form.person.birthplace'), false))
            ->setCondition('birthdate', '', '!='));
        $form->addField(new TextField('residence', $this->translator->trans('form.person.residence'), false));
        $form->addField(new TextField('status', $this->translator->trans('form.person.status'), false));
        $form->addField(new TextField('work_license', $this->translator->trans('form.person.work_license'), false));
        $form->addField((new BooleanField('addlang', $this->translator->trans('form.person.add_languages')))
            ->setSubform(function($subform, $value) {
                if ($value === true) {
                    $subform->addField((new FieldGroup('languages'))
                        ->addFields(array(
                            (new TextField('language', $this->translator->trans('form.person.language'), false)),
                            (new SelectField('level', $this->translator->trans('form.person.level')))
                                ->setOptions($this->config->get('language.levels'))
                                ->setDefault($this->config->get('language.default_level'))
                                ->setRequired(true)
                                ->setCondition('language', '', '!=')
                        ))->setLoopUntil(function($result) {
                            if (!isset($result['language']) || empty($result['language'])) {
                                return false;
                            }
                            return true;
                        }));
                }
            }));
        $this->addFlatGroupField($form, 'skills', false, 'person')
            ->addFlatGroupField($form, 'projects', false, 'person')
            ->addFlatGroupField($form, 'links', false, 'person')
            ->addFlatGroupField($form, 'interests', false, 'person');
        $form->addField(new TextField('text', $this->translator->trans('form.person.text'), false));
        return $form;
    }
}

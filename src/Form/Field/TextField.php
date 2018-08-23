<?php

namespace Magdev\Dossier\Form\Field;

use Droath\ConsoleForm\Field\FieldInterface;
use Droath\ConsoleForm\Exception\FormException;
use Magdev\Dossier\Form\Extension\Field;

/**
 * Define the text field as input via vim.
 */
class TextField extends Field implements FieldInterface
{
    /**
     * {@inheritdoc}
     */
    public function dataType()
    {
        return 'string';
    }

    /**
     * {@inheritdoc}
     */
    public function questionClassArgs()
    {
        return [$this->formattedLabel(), $this->default];
    }

    /**
     * {@inheritdoc}
     */
    public function questionClass()
    {
        return '\Symfony\Component\Console\Question\Question';
    }
}

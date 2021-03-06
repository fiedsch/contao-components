<?php
/**
 * contao components extension for Contao Open Source CMS
 *
 * Copyright (c) 2016 fiedsch@ja-eh.at
 *
 * @package fiedsch-components
 * @author  fiedsch <fiedsch@ja-eh.at>
 * @license MIT
 */

namespace Contao;

/**
 * Class WidgetJSON
 * Provide a widget for JSON data.
 */
class WidgetJSON extends TextArea
{

    /**
     * Initialize the object
     * @param array
     */
    public function __construct($arrAttributes=null)
    {
        parent::__construct($arrAttributes);
        $this->rte = ''; // RTE (tinyMCE) does not make sense here
    }

    /**
     * Validate input and set value
     */
    public function validate()
    {
        $varValue = $this->getPost($this->strName);
        if (empty($varValue)) {
            // the empty string is not a valid jSON string. So we set it to
            // the string representation of an empty JSON object.
            \Input::setPost($this->strName, '{}');
        }
        parent::validate();
    }

    /**
     * @param mixed $varInput
     */
    public function validator($varInput)
    {
        if (null === json_decode($varInput)) {
            $this->addError($GLOBALS['TL_LANG']['MSC']['json_widget_invalid_json']);
        } else {
            // revert the effect of prettyPrintJson() in generate()
            $varInput = $this->minifyJson($varInput);
        }
        return parent::validator($varInput);
    }

    /**
     * Generate the widget and return it as string
     *
     * @return string
     */
    public function generate()
    {
        $this->varValue = $this->prettyPrintJson($this->varValue);
        return parent::generate();
    }

    /**
     * Pretty print a JSON string
     *
     * @param $jsonString
     * @return string
     */
    protected function prettyPrintJson($jsonString)
    {
        $decoded = json_decode($jsonString);
        if (null === $decoded) {
            return $jsonString;
        }
        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Make a compact version of a (potentially) pretty printed JSON string
     *
     * @param $jsonString
     * @return string
     */
    protected function minifyJson($jsonString)
    {
        $decoded = json_decode($jsonString);
        if (null === $decoded) {
            return $jsonString;
        }
        return json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

}
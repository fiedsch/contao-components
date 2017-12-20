<?php
/**
 * contao components extension for Contao Open Source CMS
 *
 * Copyright (c) 2017 fiedsch@ja-eh.at
 *
 * @package fiedsch-components
 * @author  fiedsch <fiedsch@ja-eh.at>
 * @license MIT
 */

namespace Fiedsch;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\DumpException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;


class YamlConfigHelper
{

    /**
     * @var array
     */
    protected $configData;

    /**
     * @var ExpressionLanguage
     */
    protected $language;

    /**
     * Read configuration from a YAML file and store the data. If the file does not exist
     * it will be created with the data supplied in the second argument.
     *
     * @param string $pathToConfig relative path from TL_ROOT
     * @param array $defaults the defaults to use in case the config file does not exist
     */
    public function __construct($pathToConfig, $defaults = [])
    {
        $this->assertFolder($pathToConfig);
        $this->assertFile($pathToConfig, $defaults);
        try {
            $this->configData = Yaml::parse(file_get_contents(TL_ROOT . '/' . $pathToConfig));
        } catch (ParseException $e) {
            \System::log("failed to parse config file " . $pathToConfig . ' ' . $e->getMessage(), __METHOD__, TL_ERROR);
            $this->configData = $defaults;
        }
        $this->language = new ExpressionLanguage();
    }

    /**
     * @param string $pathToConfig
     */
    protected function assertFolder($pathToConfig)
    {
        // TODO: (note: the folder will be created in assertFile() when creating the file!)
        // create the folder(s) if needed
        // protect wit a .htaccess file
        // $folder = ... ; // derive from $pathToConfig
        // new \Folder($folder)->protect();
        // \System::log("protected folder $folder with a  .htaccess file", __METHOD__, TL_ERROR);
    }

    /**
     * @param string $pathToConfig
     * @param array $defaults the defaults to use in case the config file does not exist
     */
    protected function assertFile($pathToConfig, $defaults)
    {
        if (!file_exists(TL_ROOT . '/' . $pathToConfig)) {
            $configFile = new \File($pathToConfig);
            try {
                \File::putContent($pathToConfig, Yaml::dump($defaults));
                \System::log("created missing config file " . $configFile->path, __METHOD__, TL_ERROR);
            } catch (DumpException $e) {
                \System::log("failed to create missing config file " . $pathToConfig . ' ' . $e->getMessage(), __METHOD__, TL_ERROR);
            }
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->configData;
    }

    /**
     * @param string $path the "path"  described with "ExpressionLanguage" (starting with 'config.').
     * @param mixed $fallback will be returned no data is found at $path
     * @param string $rootElementName the name to be used in $path as the "root element" of the data
     * @return mixed
     */
    public function getEntry($path, $fallback = null, $rootElementName = 'data')
    {
        $result = null;
        try {
            $result = $this->language->evaluate($path, [$rootElementName => $this->getConfigObject()]);
        } catch (SyntaxError $e) {
            // silenty ignored.
        } catch (\RuntimeException $e) {
            // silenty ignored.
        }
        if (null === $result) {
            return $fallback;
        }
        return $result;
    }

    /**
     * Return the configData as Object (stdClass) so we can use "dot notation"
     * in paths when calling getEntry():
     * getEntry("foo.bar.baz") (vs. getEntry("foo[bar][baz]"))
     *
     * @return mixed
     */
    protected function getConfigObject()
    {
        return json_decode(json_encode($this->configData), false);
    }

}
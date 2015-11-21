<?php
namespace Zrashwani\NewsScrapper;

use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\CssSelector\Exception\ParseException;

/**
 * Set of useful functions for using CSS and XPath selector.
 * inspired from CodeCeption locator
 */
class Selector
{
    
    /**
     * @param $selector
     * @return bool
     */
    public static function isCSS($selector)
    {
        try {
            CssSelector::toXPath($selector);
        } catch (ParseException $e) {
            return false;
        }
        return true;
    }
    

    /**
     * Checks that locator is an XPath
     *
     * @param $selector
     * @return bool
     */
    public static function isXPath($selector)
    {
        libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0', 'UTF-8');
        $xpath = new \DOMXPath($document);
        return $xpath->evaluate($selector, $document) !== false;
    }
}

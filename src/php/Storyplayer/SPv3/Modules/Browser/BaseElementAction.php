<?php

/**
 * Copyright (c) 2011-present Mediasift Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   Storyplayer/Modules/Browser
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */

namespace Storyplayer\SPv3\Modules\Browser;

use Storyplayer\SPv3\Modules\Exceptions;

/**
 * Base class for all the *ElementAction helper classes
 *
 * The main thing this base class offers is the process for converting a
 * faked element type (such as 'buttonLabelled') into something specific
 * that we can go and find
 *
 * @category  Libraries
 * @package   Storyplayer/Browser
 * @author    Stuart Herbert <stuart.herbert@datasift.com>
 * @copyright 2011-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://datasift.github.io/storyplayer
 */
class BaseElementAction
{
    use VisibleElementFinder;

    const SINGLE_TARGET = 1;
    const PLURAL_TARGET = 2;

    protected $countTypes = array(
        "any"       => "any",
        "several"   => "several",
        "no"        => 0,
        "zero"      => 0,
        "one"       => 1,
        "a"         => 1,
        "an"        => 2,
        "two"       => 2,
        "three"     => 3,
        "four"      => 4,
        "five"      => 5,
        "six"       => 6,
        "seven"     => 7,
        "eight"     => 8,
        "nine"      => 9,
        "ten"       => 10,
        "eleven"    => 11,
        "twelve"    => 12,
        "thirteen"  => 13,
        "fourteen"  => 14,
        "fifteen"   => 15,
        "sixteen"   => 16,
        "seventeen" => 17,
        "eighteen"  => 18,
        "nineteen"  => 19,
        "twenty"    => 20,
    );

    protected $indexTypes = array(
        "first"       => 0,
        "second"      => 1,
        "third"       => 2,
        "fourth"      => 3,
        "fifth"       => 4,
        "sixth"       => 5,
        "seventh"     => 6,
        "eighth"      => 7,
        "ninth"       => 8,
        "tenth"       => 9,
        "eleventh"    => 10,
        "twelfth"     => 11,
        "thirteenth"  => 12,
        "fourteenth"  => 13,
        "fifteenth"   => 14,
        "sixteenth"   => 15,
        "seventeenth" => 16,
        "eighteenth"  => 17,
        "nineteenth"  => 18,
        "twentieth"   => 19,
    );

    protected $tagTypes = array(
        'button'        => array('input', 'button'),
        'buttons'       => array('input','button'),
        'cell'          => 'td',
        'cells'         => 'td',
        'heading'       => array('h1', 'h2', 'h3', 'h4', 'h5','h6'),
        'headings'      => array('h1', 'h2', 'h3', 'h4', 'h5','h6'),
        'link'          => 'a',
        'links'         => 'a',
        'orderedlist'   => 'ol',
        'unorderedlist' => 'ul'
    );

    protected $targetTypes = array(
        'box'           => self::SINGLE_TARGET,
        'boxes'         => self::PLURAL_TARGET,
        'button'        => self::SINGLE_TARGET,
        'buttons'       => self::PLURAL_TARGET,
        'cell'          => self::SINGLE_TARGET,
        'cells'         => self::PLURAL_TARGET,
        'dropdown'      => self::SINGLE_TARGET,
        'dropdowns'     => self::PLURAL_TARGET,
        'element'       => self::SINGLE_TARGET,
        'elements'      => self::PLURAL_TARGET,
        'field'         => self::SINGLE_TARGET,
        'fields'        => self::PLURAL_TARGET,
        'heading'       => self::SINGLE_TARGET,
        'headings'      => self::PLURAL_TARGET,
        'link'          => self::SINGLE_TARGET,
        'links'         => self::PLURAL_TARGET,
        'orderedlist'   => self::SINGLE_TARGET,
        'span'          => self::SINGLE_TARGET,
        'unorderedlist' => self::SINGLE_TARGET
    );

    protected $searchTypes = array (
        'id'            => 'ById',
        'label'         => 'ByLabel',
        'labelled'      => 'ByLabel',
        'named'         => 'ByName',
        'name'          => 'ByName',
        'text'          => 'ByText',
        'class'         => 'ByClass',
        'placeholder'   => 'ByPlaceholder',
        'title'         => 'ByTitle',
        'labelidortext' => 'ByLabelIdText',
    );

    protected $baseElement;

    public function __construct($baseElement)
    {
        $this->baseElement = $baseElement;
    }

    /**
     * @param  string $methodName
     *         camelCase string to parse
     * @return array<string>
     *         $methodName broken into individual words, all lower-cased
     */
    protected function convertMethodNameToWords($methodName)
    {
        // turn the method name into an array of words
        $words = explode(' ', strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1 $2", $methodName)));

        // all done
        return $words;
    }

    /**
     * @param  array<string> $words
     *         a list of words to examine
     * @return int|null
     */
    protected function determineCountType($words)
    {
        foreach ($words as $word) {
            if (isset($this->countTypes[$word])) {
                return $this->countTypes[$word];
            }
        }

        // if we do not recognise the word, tell the caller
        return null;
    }

    /**
     * @param  array<string> $words
     *         a list of words to examine
     * @return int|null
     */
    protected function determineIndexType($words)
    {
        foreach ($words as $word) {
            if (isset($this->indexTypes[$word])) {
                return $this->indexTypes[$word];
            }
        }

        // if we do not recognise the word, we want the first match
        return 0;
    }

    /**
     * @param  array<string> $words
     *         a list of words to examine
     * @return string|null
     */
    protected function determineSearchType($words)
    {
        foreach ($words as $word) {
            if (isset($this->searchTypes[$word])) {
                return $this->searchTypes[$word];
            }
        }

        // if we do not recognise the word, tell the caller
        return null;
    }

    /**
     * @param  array<string> $words
     *         a list of words to examine
     * @return string
     */
    protected function determineTargetType($words)
    {
        foreach ($words as $word) {
            if (isset($this->targetTypes[$word])) {
                return $word;
            }
        }

        // if we do not recognise the word, substitute a suitable default
        return 'field';
    }

    /**
     * @param  string $targetType
     * @return string
     */
    protected function determineTagType($targetType)
    {
        // do we have a specific tag to look for?
        if (isset($this->tagTypes[$targetType])) {
            return $this->tagTypes[$targetType];
        }

        // no, so return the default to feed into xpath
        return '*';
    }

    /**
     * @param  string $targetType
     * @return bool
     */
    protected function isPluralTarget($targetType)
    {
        // is this a valid target type?
        if (!isset($this->targetTypes[$targetType])) {
            throw new UnknownDomElementTypeException($targetType);
        }

        // is this a plural target?
        if ($this->targetTypes[$targetType] == self::PLURAL_TARGET) {
            return true;
        }

        // no, it is not
        return false;
    }

    protected function retrieveElement($methodName, $methodArgs)
    {
        // we need to know which element they want
        $words = $this->convertMethodNameToWords($methodName);
        $indexType  = $this->determineIndexType($words);

        // get all the elements that match
        $elements = $this->retrieveElements($methodName, $methodArgs);

        // reduce the list down to a single matching element
        $element = $this->returnNthVisibleElement($indexType, $elements);

        // all done
        return $element;
    }

    /**
     * @param  string $methodName
     * @param  array<mixed> $methodArgs
     * @return array
     */
    protected function retrieveElements($methodName, $methodArgs)
    {
        $words = $this->convertMethodNameToWords($methodName);

        $targetType = $this->determineTargetType($words);

        // what are we searching for?
        $searchTerm = $methodArgs[0];

        $searchType = $this->determineSearchType($words);
        if ($searchType === null) {             // we do not understand how to find the target field
            throw Exceptions::newActionFailedException(__CLASS__ . '::' . $methodName, "could not work out how to find the target to action upon");
        }

        // what tag(s) do we want to narrow our search to?
        $tag = $this->determineTagType($targetType);

        // how are we searching for matching elements?
        $searchMethod = 'getElements' . $searchType;

        // let's go find our element
        $searchObject = new DomElementSearch($this->baseElement);
        $elements = $searchObject->$searchMethod($searchTerm, $tag);

        // all done
        return $elements;
    }
}

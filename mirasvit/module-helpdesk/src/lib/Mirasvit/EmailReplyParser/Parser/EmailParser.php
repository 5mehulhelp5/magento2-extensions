<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.3.6
 * @copyright Copyright (C) 2025 Mirasvit (https://mirasvit.com/)
 */


// @codingStandardsIgnoreFile
/**
 * This file is part of the EmailReplyParser package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class EmailParser
{
    const SIG_REGEX = '/(^--|^__|\w-$)|(^(\w+\s*){1,3} ym morf tneS$)/s';

    const QUOTE_REGEX = '/(>+)$/s';

    /**
     * @var array
     */
    private $quoteHeadersRegex = array(
        '/^(On\s(.+)wrote:)$/ms', // On DATE, NAME <EMAIL> wrote:
    );

    /**
     * @var array
     */
    private $fragments = array();

    /**
     * Parse a text which represents an email and splits it into fragments.
     *
     * @param string $text A text.
     *
     * @return Email
     */
    public function parse($text)
    {
        $text = str_replace("\r\n", "\n", $text);

        foreach ($this->quoteHeadersRegex as $regex) {
            if (preg_match($regex, $text, $matches)) {
                $text = str_replace($matches[1], str_replace("\n", ' ', $matches[1]), $text);
            }
        }

        $fragment = null;
        foreach (explode("\n", strrev($text)) as $line) {
            $line = rtrim($line, "\n");

            if (!$this->isSignature($line)) {
                $line = ltrim($line);
            }

            if ($fragment && empty($line)) {
                $last = end($fragment->lines);

                if ($this->isSignature($last)) {
                    $fragment->isSignature = true;
                    $this->addFragment($fragment);

                    $fragment = null;
                } elseif ($this->isQuoteHeader($last)) {
                    $fragment->isQuoted = true;
                    $this->addFragment($fragment);

                    $fragment = null;
                }
            }

            $isQuoted = $this->isQuote($line);

            if (null === $fragment || !$this->isFragmentLine($fragment, $line, $isQuoted)) {
                if ($fragment) {
                    $this->addFragment($fragment);
                }

                $fragment = new FragmentDTO();
                $fragment->isQuoted = $isQuoted;
            }

            $fragment->lines[] = $line;
        }

        if ($fragment) {
            $this->addFragment($fragment);
        }

        return $this->createEmail($this->fragments);
    }

    /**
     * @return array
     */
    public function getQuoteHeadersRegex()
    {
        return $this->quoteHeadersRegex;
    }

    /**
     * @param array $quoteHeadersRegex
     *
     * @return EmailParser
     */
    public function setQuoteHeadersRegex(array $quoteHeadersRegex)
    {
        $this->quoteHeadersRegex = $quoteHeadersRegex;

        return $this;
    }

    /**
     * @param FragmentDTO[]
     *
     * @return Email
     */
    protected function createEmail(array $fragmentDTOs)
    {
        $fragments = array();
        foreach (array_reverse($fragmentDTOs) as $fragment) {
            $fragments[] = new Fragment(
                preg_replace("/^\n/", '', strrev(implode("\n", $fragment->lines))),
                $fragment->isHidden,
                $fragment->isSignature,
                $fragment->isQuoted
            );
        }

        return new Email($fragments);
    }

    /**
     * @param string $line
     * @return bool
     */
    private function isQuoteHeader($line)
    {
        foreach ($this->quoteHeadersRegex as $regex) {
            if (preg_match($regex, strrev($line))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $line
     * @return bool
     */
    private function isSignature($line)
    {
        return preg_match(self::SIG_REGEX, $line) ? true : false;
    }

    /**
     * @param string $line
     * @return bool
     */
    private function isQuote($line)
    {
        return preg_match(self::QUOTE_REGEX, $line) ? true : false;
    }

    /**
     * @param FragmentDTO $fragment
     * @return bool
     */
    private function isEmpty(FragmentDTO $fragment)
    {
        return '' === implode('', $fragment->lines);
    }

    /**
     * @param FragmentDTO $fragment
     * @param string $line
     * @param bool $isQuoted
     * @return bool
     */
    private function isFragmentLine(FragmentDTO $fragment, $line, $isQuoted)
    {
        return $fragment->isQuoted === $isQuoted ||
            ($fragment->isQuoted && ($this->isQuoteHeader($line) || empty($line)));
    }

    /**
     * @param FragmentDTO $fragment
     */
    private function addFragment(FragmentDTO $fragment)
    {
        if ($fragment->isQuoted || $fragment->isSignature || $this->isEmpty($fragment)) {
            $fragment->isHidden = true;
        }

        $this->fragments[] = $fragment;
    }
}

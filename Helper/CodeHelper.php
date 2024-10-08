<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NyroDev\PhpTemplateBundle\Helper;

use finfo;
use Symfony\Component\ErrorHandler\ErrorRenderer\FileLinkFormatter;
use Symfony\Component\Templating\Helper\Helper;

use function count;
use function extension_loaded;
use function is_array;
use function is_int;
use function is_string;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal since Symfony 4.2
 */
class CodeHelper extends Helper
{
    private $fileLinkFormat;
    protected $projectDir;

    /**
     * @param string|FileLinkFormatter $fileLinkFormat The format for links to source files
     * @param string                   $projectDir     The project root directory
     * @param string                   $charset        The charset
     */
    public function __construct(
        string|FileLinkFormatter $fileLinkFormat,
        string $projectDir,
        string $charset,
    ) {
        $this->fileLinkFormat = $fileLinkFormat ?: ini_get('xdebug.file_link_format') ?: get_cfg_var('xdebug.file_link_format');
        $this->projectDir = str_replace('\\', '/', $projectDir).'/';
        $this->setCharset($charset);
    }

    /**
     * Formats an array as a string.
     *
     * @param array $args The argument array
     */
    public function formatArgsAsText(array $args): string
    {
        return strip_tags($this->formatArgs($args));
    }

    public function abbrClass(string $class): string
    {
        $parts = explode('\\', $class);
        $short = array_pop($parts);

        return sprintf('<abbr title="%s">%s</abbr>', $class, $short);
    }

    public function abbrMethod(string $method): string
    {
        if (false !== strpos($method, '::')) {
            list($class, $method) = explode('::', $method, 2);
            $result = sprintf('%s::%s()', $this->abbrClass($class), $method);
        } elseif ('Closure' === $method) {
            $result = sprintf('<abbr title="%s">%1$s</abbr>', $method);
        } else {
            $result = sprintf('<abbr title="%s">%1$s</abbr>()', $method);
        }

        return $result;
    }

    /**
     * Formats an array as a string.
     *
     * @param array $args The argument array
     */
    public function formatArgs(array $args): string
    {
        $result = [];
        foreach ($args as $key => $item) {
            if ('object' === $item[0]) {
                $parts = explode('\\', $item[1]);
                $short = array_pop($parts);
                $formattedValue = sprintf('<em>object</em>(<abbr title="%s">%s</abbr>)', $item[1], $short);
            } elseif ('array' === $item[0]) {
                $formattedValue = sprintf('<em>array</em>(%s)', is_array($item[1]) ? $this->formatArgs($item[1]) : $item[1]);
            } elseif ('string' === $item[0]) {
                $formattedValue = sprintf("'%s'", htmlspecialchars($item[1], ENT_QUOTES, $this->getCharset()));
            } elseif ('null' === $item[0]) {
                $formattedValue = '<em>null</em>';
            } elseif ('boolean' === $item[0]) {
                $formattedValue = '<em>'.strtolower(var_export($item[1], true)).'</em>';
            } elseif ('resource' === $item[0]) {
                $formattedValue = '<em>resource</em>';
            } else {
                $formattedValue = str_replace("\n", '', var_export(htmlspecialchars((string) $item[1], ENT_QUOTES, $this->getCharset()), true));
            }

            $result[] = is_int($key) ? $formattedValue : sprintf("'%s' => %s", $key, $formattedValue);
        }

        return implode(', ', $result);
    }

    /**
     * Returns an excerpt of a code file around the given line number.
     *
     * @param string $file A file path
     * @param int    $line The selected line number
     *
     * @return string|null An HTML string
     */
    public function fileExcerpt(string $file, int $line): ?string
    {
        if (is_readable($file)) {
            if (extension_loaded('fileinfo')) {
                $finfo = new finfo();

                // Check if the file is an application/octet-stream (eg. Phar file) because highlight_file cannot parse these files
                if ('application/octet-stream' === $finfo->file($file, FILEINFO_MIME_TYPE)) {
                    return '';
                }
            }

            // highlight_file could throw warnings
            // see https://bugs.php.net/25725
            $code = @highlight_file($file, true);
            // remove main code/span tags
            $code = preg_replace('#^<code.*?>\s*<span.*?>(.*)</span>\s*</code>#s', '\\1', $code);
            $content = explode('<br />', $code);

            $lines = [];
            for ($i = max($line - 3, 1), $max = min($line + 3, count($content)); $i <= $max; ++$i) {
                $lines[] = '<li'.($i == $line ? ' class="selected"' : '').'><code>'.self::fixCodeMarkup($content[$i - 1]).'</code></li>';
            }

            return '<ol start="'.max($line - 3, 1).'">'.implode("\n", $lines).'</ol>';
        }

        return null;
    }

    /**
     * Formats a file path.
     *
     * @param string $file An absolute file path
     * @param int    $line The line number
     * @param string $text Use this text for the link rather than the file path
     */
    public function formatFile(string $file, int $line, ?string $text = null): string
    {
        $flags = ENT_QUOTES | ENT_SUBSTITUTE;

        if (null === $text) {
            $file = trim($file);
            $fileStr = $file;
            if (0 === strpos($fileStr, $this->projectDir)) {
                $fileStr = str_replace(['\\', $this->projectDir], ['/', ''], $fileStr);
                $fileStr = htmlspecialchars($fileStr, $flags, $this->charset);
                $fileStr = sprintf('<abbr title="%s">kernel.project_dir</abbr>/%s', htmlspecialchars($this->projectDir, $flags, $this->charset), $fileStr);
            }

            $text = sprintf('%s at line %d', $fileStr, $line);
        }

        if (false !== $link = $this->getFileLink($file, $line)) {
            return sprintf('<a href="%s" title="Click to open this file" class="file_link">%s</a>', htmlspecialchars($link, $flags, $this->charset), $text);
        }

        return $text;
    }

    /**
     * Returns the link for a given file/line pair.
     *
     * @param string $file An absolute file path
     * @param int    $line The line number
     *
     * @return string A link of false
     */
    public function getFileLink(string $file, int $line): string
    {
        if ($fmt = $this->fileLinkFormat) {
            return is_string($fmt) ? strtr($fmt, ['%f' => $file, '%l' => $line]) : $fmt->format($file, $line);
        }

        return false;
    }

    public function formatFileFromText(string $text): string
    {
        return preg_replace_callback('/in ("|&quot;)?(.+?)\1(?: +(?:on|at))? +line (\d+)/s', function ($match) {
            return 'in '.$this->formatFile($match[2], $match[3]);
        }, $text);
    }

    public function getName(): string
    {
        return 'code';
    }

    protected static function fixCodeMarkup(int $line): string
    {
        // </span> ending tag from previous line
        $opening = strpos($line, '<span');
        $closing = strpos($line, '</span>');
        if (false !== $closing && (false === $opening || $closing < $opening)) {
            $line = substr_replace($line, '', $closing, 7);
        }

        // missing </span> tag at the end of line
        $opening = strpos($line, '<span');
        $closing = strpos($line, '</span>');
        if (false !== $opening && (false === $closing || $closing > $opening)) {
            $line .= '</span>';
        }

        return $line;
    }
}

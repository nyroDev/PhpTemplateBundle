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

use Symfony\Component\Asset\Packages;
use Symfony\Component\Templating\Helper\Helper;

/**
 * AssetsHelper helps manage asset URLs.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AssetsHelper extends Helper
{
    public function __construct(
        private readonly Packages $packages,
    ) {
    }

    /**
     * Returns the public url/path of an asset.
     *
     * If the package used to generate the path is an instance of
     * UrlPackage, you will always get a URL and not a path.
     *
     * @param string $path        A public path
     * @param string $packageName The name of the asset package to use
     *
     * @return string The public path of the asset
     */
    public function getUrl(string $path, ?string $packageName = null): string
    {
        return $this->packages->getUrl($path, $packageName);
    }

    /**
     * Returns the version of an asset.
     *
     * @param string $path        A public path
     * @param string $packageName The name of the asset package to use
     *
     * @return string The asset version
     */
    public function getVersion(string $path, ?string $packageName = null): string
    {
        return $this->packages->getVersion($path, $packageName);
    }

    public function getName(): string
    {
        return 'assets';
    }
}

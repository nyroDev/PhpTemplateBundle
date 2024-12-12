<?php

namespace NyroDev\PhpTemplateBundle\Helper;

use Exception;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollection;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;

class TagRendererHelper extends Helper
{
    private array $injectedAssets = [];

    public function getName()
    {
        return 'nyrodev_tagRenderer';
    }

    public function __construct(
        private readonly Packages $assetsPackages,
        private readonly EntrypointLookupCollection $entrypointLookupCollection,
    ) {
    }

    public function setInjectedAssets(array $injectedAssets): void
    {
        $this->injectedAssets = $injectedAssets;
    }

    public function reset(string $entrypointName = '_default'): void
    {
        $this->getEntrypointLookup($entrypointName)->reset();
    }

    public function renderWebpackScriptTags(string $entryName, ?string $moreAttrs = null, ?string $packageName = null, string $entrypointName = '_default'): string
    {
        $scriptTags = [];
        foreach ($this->getScriptFiles($entryName, $packageName, $entrypointName) as $filename) {
            $scriptTags[] = sprintf(
                '<script src="%s"'.($moreAttrs ? ' '.$moreAttrs : null).'></script>',
                $filename
            );
        }

        return
            $this->renderWebpackLinkTags($entryName, null, $packageName, $entrypointName)
            .implode('', $scriptTags);
    }

    public function getScriptFiles(string $entryName, ?string $packageName = null, string $entrypointName = '_default'): array
    {
        $scriptFiles = [];
        foreach ($this->getEntrypointLookup($entrypointName)->getJavaScriptFiles($entryName) as $filename) {
            $scriptFiles[] = htmlentities($this->getAssetPath($filename, $packageName));
        }

        if (isset($this->injectedAssets[$entryName], $this->injectedAssets[$entryName]['js'])) {
            foreach($this->injectedAssets[$entryName]['js'] as $filename) {
                $scriptFiles[] = htmlentities($this->getAssetPath($filename, $packageName));
            }
        }

        return $scriptFiles;
    }

    public function renderWebpackLinkTags(string $entryName, ?string $moreAttrs = null, ?string $packageName = null, string $entrypointName = '_default'): string
    {
        $linkTags = [];
        foreach ($this->getLinkFiles($entryName, $packageName, $entrypointName) as $filename) {
            $linkTags[] = sprintf(
                '<link rel="stylesheet" href="%s"'.($moreAttrs ? ' '.$moreAttrs : null).' />',
                $filename
            );
        }

        return implode('', $linkTags);
    }

    public function getLinkFiles(string $entryName, ?string $packageName = null, string $entrypointName = '_default'): array
    {
        $linkFiles = [];
        foreach ($this->getEntrypointLookup($entrypointName)->getCssFiles($entryName) as $filename) {
            $linkFiles[] = htmlentities($this->getAssetPath($filename, $packageName));
        }

        if (isset($this->injectedAssets[$entryName], $this->injectedAssets[$entryName]['css'])) {
            foreach($this->injectedAssets[$entryName]['css'] as $filename) {
                $linkFiles[] = htmlentities($this->getAssetPath($filename, $packageName));
            }
        }

        return $linkFiles;
    }

    public function getAssetPath(string $assetPath, ?string $packageName = null): string
    {
        if (null === $this->assetsPackages) {
            throw new Exception('To render the script or link tags, run "composer require symfony/asset".');
        }

        return $this->assetsPackages->getUrl(
            $assetPath,
            $packageName
        );
    }

    private function getEntrypointLookup(string $buildName): EntrypointLookupInterface
    {
        return $this->entrypointLookupCollection->getEntrypointLookup($buildName);
    }
}

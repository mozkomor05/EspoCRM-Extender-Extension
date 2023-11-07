<?php

namespace Espo\Modules\Extender\Core\Utils;

use DOMDocument;
use Espo\Core\Di;
use Espo\Core\Utils\Json;

class ClientManager extends \Espo\Core\Utils\ClientManager implements Di\MetadataAware, Di\ConfigAware, Di\PreferencesAware
{
    use Di\MetadataSetter;
    use Di\ConfigSetter;
    use Di\PreferencesSetter;

    public function render(?string $runScript = null, ?string $htmlFilePath = null, array $vars = []): string
    {
        $html = parent::render($runScript, $htmlFilePath, $vars);
        $version = $this->config->get('version');
        $oldLoader = version_compare($version, '8.0.0', '<');
        $scriptPath = sprintf(
            'client/custom/modules/extender/src/%s?r=%s',
            $oldLoader ? 'extender.old.js' : 'extender.js',
            $oldLoader ? $this->getCacheTimestamp() : $this->getAppTimestamp()
        );

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $firstScriptTag = $dom->getElementsByTagName('script')->item(0);

        $script = $dom->createElement('script');
        $script->setAttribute('type', 'text/javascript');
        $script->setAttribute('src', $scriptPath);

        $extensionMap = $this->metadata->get(['app', 'client', 'viewExtensions'], []);
        $paramsTag = $dom->createElement('script', Json::encode($extensionMap));
        $paramsTag->setAttribute('type', 'application/json');
        $paramsTag->setAttribute('data-name', 'extension-views');

        $parentNode = $firstScriptTag->parentNode;

        $parentNode->insertBefore($script, $oldLoader ? $firstScriptTag : $firstScriptTag->nextSibling);
        $parentNode->insertBefore($paramsTag, $script);

        return $dom->saveHTML();
    }

    private function useCache(): bool
    {
        return (bool)$this->config->get('useCache');
    }

    private function getCacheTimestamp(): int
    {
        if (!$this->useCache()) {
            return time();
        }

        return $this->config->get('cacheTimestamp', 0);
    }

    private function getAppTimestamp(): int
    {
        if (!$this->useCache()) {
            return time();
        }

        return $this->config->get('appTimestamp', 0);
    }
}

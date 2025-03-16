<?php

namespace CmsHealthProject\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('health_sulu');
        $rootNode    = $treeBuilder->getRootNode();
        $rootNode->children()->scalarNode('access_token')->defaultValue(null)->end();

        return $treeBuilder;
    }
}

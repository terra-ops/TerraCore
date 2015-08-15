<?php

namespace TerraCore;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use TerraCore\Environment\EnvironmentInterface;

/**
 * Class Config.
 */
abstract class ConfigBase implements ConfigInterface {
  /**
   * Configuration values array.
   *
   * @var array
   */
  protected $config = array();

  /**
   * {@inheritdoc}
   */
  public function __construct()
  {
    try {
      $processor = new Processor();
      $configs = func_get_args();
      $this->config = $processor->processConfiguration($this, $configs);
    } catch (\Exception $e) {
      throw new \Exception('There is an error with your configuration: '.$e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigTreeBuilder()
  {
    $tree_builder = new TreeBuilder();
    $root_node = $tree_builder->root('project');
    $root_node
      ->children()
      ->scalarNode('git')
      ->defaultValue('/usr/bin/git')
      ->end()
      ->scalarNode('apps_basepath')
      ->defaultValue($_SERVER['HOME'].'/Apps')
      ->end()
      ->arrayNode('projects')
        ->prototype('array')
        ->children()
          ->scalarNode('name')
          ->isRequired(true)
          ->end()
          ->scalarNode('description')
          ->isRequired(false)
          ->end()
          ->scalarNode('repo')
          ->isRequired(true)
          ->end()
          ->scalarNode('host')
          ->defaultValue('localhost')
          ->isRequired(true)
          ->end()
          ->arrayNode('environments')
            ->prototype('array')
            ->isRequired(false)
            ->children()
              ->scalarNode('name')
              ->isRequired(true)
              ->end()
              ->scalarNode('path')
              ->isRequired(true)
              ->end()
              ->scalarNode('version')
              ->isRequired(true)
              ->end()
              ->scalarNode('url')
              ->isRequired(false)
              ->end()
              ->scalarNode('document_root')
              ->isRequired(true)
              ->end()
    ;

    return $tree_builder;
  }

  /**
   * {@inheritdoc}
   */
  public function get($key, $name = null)
  {
    if ($name) {
      return array_key_exists($name, $this->config[$key]) ? $this->config[$key][$name] : null;
    } else {
      return $this->has($key) ? $this->config[$key] : null;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function has($key)
  {
    return array_key_exists($key, $this->config);
  }

  /**
   * {@inheritdoc}
   */
  public function set($key, $val)
  {
    return $this->config[$key] = $val;
  }

  /**
   * {@inheritdoc}
   */
  public function all()
  {
    return $this->config;
  }

  /**
   * {@inheritdoc}
   */
  public function add($key, $names, $val)
  {
    if (is_array($names)) {
      $array_piece = &$this->config[$key];
      foreach ($names as $name_key) {
        $array_piece = &$array_piece[$name_key];
      }

      return $array_piece = $val;
    } else {
      return $this->config[$key][$names] = $val;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function remove($key, $name)
  {
    if (isset($this->config[$key][$name])) {
      unset($this->config[$key][$name]);

      return true;
    } else {
      return false;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function saveEnvironment(EnvironmentInterface $environment) {
    $this->config['projects'][$environment->getProject()->getName()]['environments'][$environment->getName()] = [
      'name' => $environment->getName(),
      'path' => $environment->getPath(),
      'version' => $environment->getVersion(),
      'url' => $environment->getUrl(),
      'document_root' => $environment->getDocumentRoot(),
    ];
    $this->save();
  }

  /**
   * {@inheritdoc}
   */
  abstract public function save();

}

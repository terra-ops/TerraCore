<?php
/**
 * @file
 * Contains \TerraCore\ConfigInterface.php
 */
namespace TerraCore;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use TerraCore\Environment\EnvironmentInterface;

/**
 * Class Config.
 */
interface ConfigInterface extends ConfigurationInterface {
  /**
   * Get a config param value.
   *
   * @param string $key
   *                    Key of the param to get.
   *
   * @return mixed|null
   *                    Value of the config param, or NULL if not present.
   */
  public function get($key, $name = NULL);

  /**
   * Check if config param is present.
   *
   * @param string $key
   *                    Key of the param to check.
   *
   * @return bool
   *              TRUE if key exists.
   */
  public function has($key);

  /**
   * Set a config param value.
   *
   * @param string $key
   *                    Key of the param to get.
   * @param mixed $val
   *                    Value of the param to set.
   *
   * @return bool
   */
  public function set($key, $val);

  /**
   * Get all config values.
   *
   * @return array
   *               All config galues.
   */
  public function all();

  /**
   * Add a config param value to a config array.
   *
   * @param string $key
   *                            Key of the group to set to.
   * @param string|array $names
   *                            Name of the new object to set.
   * @param mixed $val
   *                            Value of the new object to set.
   *
   * @return bool
   */
  public function add($key, $names, $val);

  /**
   * Remove a config param from a config array.
   *
   * @param $key
   * @param $name
   *
   * @return bool
   */
  public function remove($key, $name);

  /**
   * Saves an environment to the config class.
   *
   * Don't forget to call ::save() afterwards to save to file.
   *
   * @param \TerraCore\Environment\EnvironmentInterface $environment
   */
  public function saveEnvironment(EnvironmentInterface $environment);

  /**
   * Saves the config class to file.
   * @return bool
   */
  public function save();
}
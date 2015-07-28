<?php
/**
 * @file
 * Contains TerraCore\Stack\StackInterface.php
 */

namespace TerraCore\Stack;


interface StackInterface {

  /**
   * @return \TerraCore\Environment\EnvironmentInterface
   */
  public function getEnvironment();

  /**
   * @return \Psr\Log\LoggerInterface
   */
  public function getLogger();

  public function getConfigPath();

  public function getConfig();

  public function getUrl();

  public function getPort();

  public function getScale();

  public function generateConfigFile();

  /**
   * @return \TerraCore\Environment\Factory\EnvironmentFactoryInterface
   */
  public function getEnvironmentFactory();

}

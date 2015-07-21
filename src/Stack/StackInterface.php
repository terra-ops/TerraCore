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

  public function getDockerComposePath();

  public function getDockerComposeArray();

  public function getUrl();

  public function getPort();

  public function getScale();

  public function generateDockerComposeFile();

}

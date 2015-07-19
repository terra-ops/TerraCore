<?php
/**
 * @file
 * Contains TerraCore\Stack\StackInterface.php
 */

namespace TerraCore\Stack;


interface StackInterface {

  public function getDockerComposePath();

  public function getDockerComposeArray();

  public function getPort();

  public function getHost();

  public function getScale();

}

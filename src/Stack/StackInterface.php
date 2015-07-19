<?php
/**
 * @file
 * Contains TerraCore\Stack\StackInterface.php
 */

namespace TerraCore\Stack;


interface StackInterface {

  public function getDockerComposePath();

  public function getDockerComposeArray();

  public function getUrl();

  public function getPort();

  public function getScale();

}

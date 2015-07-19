<?php
/**
 * @file
 * Contains \TerraCore\Stack\TerraDrupalStack.php
 */

namespace TerraCore\Stack;


use TerraCore\Project\ProjectInterface;

class TerraDrupalStack implements StackInterface {

  public function __construct(ProjectInterface $project) {
    $this->project = $project;
  }

  public function getDockerComposePath() {

  }

  public function getDockerComposeArray() {
    // TODO: Implement getDockerComposeArray() method.
  }

  public function getPort() {
    // TODO: Implement getPort() method.
  }

  public function getHost() {
    // TODO: Implement getHost() method.
  }

  public function getScale() {
    // TODO: Implement getScale() method.
  }

}

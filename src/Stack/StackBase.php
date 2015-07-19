<?php
/**
 * @file
 * Contains \TerraCore\Stack\StackBase.php
 */

namespace TerraCore\Stack;

use TerraCore\Environment\EnvironmentInterface;

abstract class StackBase implements StackInterface {

  /**
   * @var \TerraCore\Environment\EnvironmentInterface
   */
  protected $environment;

  public function __construct(EnvironmentInterface $environment) {
    $this->environment = $environment;
  }

  protected function mergeDockerComposeProjectOverrides(array $compose) {
    $project = $this->environment->getProject();

    // Add "overrides" to docker-compose.
    foreach ($project->getDockerCompose('overrides') as $service => $info) {
      $compose[$service] = array_merge_recursive($compose[$service], $info);
    }
    return $compose;
  }

}

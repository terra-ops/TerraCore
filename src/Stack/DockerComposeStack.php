<?php
/**
 * @file
 * Contains \TerraCore\Stack\DockerComposeStack.php
 */

namespace TerraCore\Stack;


abstract class DockerComposeStack extends StackBase {

  protected function mergeDockerComposeProjectOverrides(array $compose) {
    $project = $this->environment->getProject();

    // Add "overrides" to docker-compose.
    foreach ($project->getDockerCompose('overrides') as $service => $info) {
      $compose[$service] = array_merge_recursive($compose[$service], $info);
    }
    return $compose;
  }

}

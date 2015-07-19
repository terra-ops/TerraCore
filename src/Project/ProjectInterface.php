<?php
/**
 * @file
 * Contains \TerraCore\Project\ProjectInterface.php
 */

namespace TerraCore\Project;


interface ProjectInterface {

  /**
   * The name of this project.
   *
   * @return string
   */
  public function getName();

  /**
   * The description of this project.
   *
   * @return string
   */
  public function getDescription();

  /**
   * @todo seems like this should be a getTerra() method since an app is really
   * just the .terra.yml file + associated helper files and could be stored in
   * any fashion, not just in git repositories.
   *
   * @return mixed
   */
  public function getRepo();

  /**
   * Get all Environments for this project.
   *
   * @return \TerraCore\Environment\EnvironmentInterface[]
   */
  public function getEnvironments();

  /**
   * Get a particular environment
   *
   * @param string $environment
   *
   * @return \TerraCore\Environment\EnvironmentInterface
   */
  public function getEnvironment($environment);

  /**
   * Allows projects to specificy StackInterface level changes and additions.
   *
   * @param string $key
   *
   * @return []
   */
  public function getDockerCompose($key);

}

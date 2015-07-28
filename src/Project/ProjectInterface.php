<?php
/**
 * @file
 * Contains \TerraCore\Project\ProjectInterface.php
 */

namespace TerraCore\Project;


use Psr\Log\LoggerInterface;
use TerraCore\Environment\EnvironmentInterface;

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

  public function build(LoggerInterface $logger, $path);

  public function enable(LoggerInterface $logger);

  public function deploy($version, EnvironmentInterface $environment, LoggerInterface $logger);

  /**
   * @param $hook
   *
   * @return \Symfony\Component\Process\Process|NULL
   */
  public function invokeHook($hook);

}

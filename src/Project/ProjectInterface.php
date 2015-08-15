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
   * @return array
   */
  public static function getInterfaceQuestions();

  /**
   * The name of this project.
   *
   * @return string
   */
  public function getName();

  /**
   * The type of project.
   *
   * @return string
   */
  public function getType();

  /**
   * The description of this project.
   *
   * @return string
   */
  public function getDescription();

  /**
   * The string location of the project.
   *
   * In the case of a git project this would be the git repository url, in the
   * case of a local project it would be the directory location.
   *
   * @return string
   */
  public function getLocation();

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
   * @param string $type
   *   The type of stack modification. Each stack might have completely
   *   different instructions to create the stack. Projects can support various
   *   types of stacks i.e. docker-compose, kubernetes, etc.
   *
   * @param string $key
   *
   * @return []
   */
  public function getStackModifications($type, $key);

  /**
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function build(LoggerInterface $logger, $path);

  /**
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function enable(LoggerInterface $logger);

  /**
   * @param string $version
   *
   * @param \TerraCore\Environment\EnvironmentInterface $environment
   *
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function deploy($version, EnvironmentInterface $environment, LoggerInterface $logger);

  /**
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function disable(LoggerInterface $logger);

  /**
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function duplicate(LoggerInterface $logger);

  /**
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function destroy(LoggerInterface $logger);

  /**
   * @param $hook
   *
   * @return \Symfony\Component\Process\Process|NULL
   */
  public function invokeHook($hook);

}

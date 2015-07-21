<?php
/**
 * @file
 * Contains \TerraCore\Project\GitProject.php
 */

namespace TerraCore\Project;


use GitWrapper\GitException;
use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Psr\Log\LoggerInterface;
use TerraCore\Environment\Environment;

class GitProject implements ProjectInterface {

  /**
   * @var string
   */
  protected $name;

  /**
   * @var string
   */
  protected $description;

  /**
   * @var array
   */
  protected $rawEnvironments;

  /**
   * @var \TerraCore\Environment\EnvironmentInterface[]
   */
  protected $environments = [];

  /**
   * @var array
   */
  protected $overrides;

  function __construct($name, $description, array $environments = [], array $overrides = []) {
    $this->name = $name;
    $this->description = $description;
    $this->rawEnvironments = $environments;
    $this->overrides = $overrides;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getRepo() {
    // TODO: Implement getRepo() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironments() {
    if (array_diff_key($this->rawEnvironments, $this->environments)) {
      foreach ($this->rawEnvironments as $key => $values) {
        if (!isset($this->environments[$key])) {
          $this->environments[$key] = new Environment($values['name'], $values['path'], $values['document_root'], $values['host'], $values['url'], $values['version'], $this);
        }
      }
    }
    return $this->environments;
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment($environment) {
    if (!isset($this->rawEnvironments[$environment])) {
      throw new \Exception(sprintf("No such environment for project \"%s\"", $this->getName()));
    }
    if (!isset($this->environments[$environment])) {
      $values = $this->rawEnvironments[$environment];
      $this->environments[$environment] = new Environment($values['name'], $values['path'], $values['document_root'], $values['host'], $values['url'], $values['version'], $this);
    }
    return $this->environments[$environment];
  }

  /**
   * {@inheritdoc}
   */
  public function getDockerCompose($key) {
    return $this->overrides[$key];
  }

  public function build(LoggerInterface $logger, $path) {

    // Check if clone already exists at this path. If so we can safely skip.
    if (file_exists($path)) {
      $wrapper = new GitWrapper();

      try {
        $working_copy = new GitWorkingCopy($wrapper, $path);
        $output = $working_copy->remote('-v');
      } catch (GitException $e) {
        throw new \Exception('Path already exists.');
      }

      // if repo exists in the remotes already, this working copy is ok.
      if (strpos(strtolower($output), strtolower($this->getRepo())) !== false) {
        return true;
      } else {
        throw new \Exception('Git clone already exists at that path, but it is not for this app.');
      }
    }

    try {
      mkdir($path, 0755, TRUE);
      chdir($path);
      $wrapper = new GitWrapper();
      $wrapper->streamOutput();
      $wrapper->cloneRepository($this->getRepo(), $path);
    } catch (\GitWrapper\GitException $e) {
      return false;
    }

    chdir($path);
    $logger->info($wrapper->git('branch'));
    $logger->info($wrapper->git('status'));
  }

  public function enable(LoggerInterface $logger) {
    // TODO: Implement enable() method.
  }

  public function deploy(LoggerInterface $logger) {
    // TODO: Implement deploy() method.
  }

}

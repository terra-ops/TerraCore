<?php
/**
 * @file
 * Contains \TerraCore\Project\GitProject.php
 */

namespace TerraCore\Project;


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

}

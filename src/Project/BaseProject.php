<?php
/**
 * @file
 * Contains \TerraCore\Project\BaseProject.php
 */

namespace TerraCore\Project;


use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use TerraCore\Environment\Environment;
use TerraCore\Environment\EnvironmentInterface;

class BaseProject implements ProjectInterface {

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
  protected $hooks;

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

  function __construct($name, $description, array $hooks = [], array $environments = [], array $overrides = []) {
    $this->name = $name;
    $this->description = $description;
    $this->hooks = $hooks;
    $this->rawEnvironments = $environments;
    $this->overrides = $overrides;
  }

  public static function getInterfaceQuestions() {
    $host_default = getenv('DOCKER_HOST') ? parse_url(getenv('DOCKER_HOST'), PHP_URL_HOST) : php_uname('n');

    // Allow local.computer
    if ($host_default == '192.168.99.100') {
      $host_default = 'local.computer';
    }
    return [
      ['System name of your project? ', ''],
      ['Description? ', ''],
      ['Host? [' . $host_default . '] ', $host_default],
    ];
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
  public function build(LoggerInterface $logger, $path) {}

  /**
   * {@inheritdoc}
   */
  public function enable(LoggerInterface $logger) {}

  /**
   * {@inheritdoc}
   */
  public function deploy($version, EnvironmentInterface $environment, LoggerInterface $logger) {}

  /**
   * @inheritDoc
   */
  public function disable(LoggerInterface $logger) {}

  /**
   * @inheritDoc
   */
  public function duplicate(LoggerInterface $logger) {}

  /**
   * @inheritDoc
   */
  public function destroy(LoggerInterface $logger) {}

  /**
   * {@inheritdoc}
   */
  public function invokeHook($hook) {
    // Run the build hooks
    if (!empty($this->hooks[$hook])) {
      $process = new Process($this->hooks[$hook]);
      $process->setTimeout(NULL);
      $process->run(function ($type, $buffer) {
        if (Process::ERR === $type) {
          echo $buffer;
        } else {
          echo $buffer;
        }
      });
      return $process;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getStackModifications($type, $key) {
    return $this->overrides[$type][$key];
  }

}

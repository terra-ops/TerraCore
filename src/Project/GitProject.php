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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use TerraCore\Environment\Environment;
use TerraCore\Environment\EnvironmentInterface;

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

  public function deploy($version, EnvironmentInterface $environment, LoggerInterface $logger) {
    // Checkout the branch
    $wrapper = new GitWrapper();
    $wrapper->streamOutput();
    $git = new GitWorkingCopy($wrapper, $environment->getPath());
    $git->checkout($version);
    $git->pull();
    // Reload config so any changes get picked up.
    $this->reload($environment);
  }

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
   * Reload this project instance with any changes from the .terra.yml file.
   *
   * @param \TerraCore\Environment\EnvironmentInterface $environment
   */
  protected function reload(EnvironmentInterface $environment) {
    // Look for .terra.yml
    $fs = new Filesystem();
    if ($fs->exists($environment->getPath().'/.terra.yml')) {
      // Process any string replacements.
      $environment_config_string = file_get_contents($environment->getPath().'/.terra.yml');
      $config = Yaml::parse(strtr($environment_config_string, array(
        '{{alias}}' => "@{$this->getName()}.{$environment->getName()}",
      )));
      $this->name = isset($config['name']) ? $config['name'] : $this->name;
      $this->description = isset($config['description']) ? $config['description'] : $this->description;
      $this->hooks = isset($config['hooks']) ? $config['hooks'] : $this->hooks;
      $this->overrides = isset($config['overrides']) ? $config['overrides'] : $this->overrides;
    }
  }

}

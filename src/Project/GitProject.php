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
use Symfony\Component\Yaml\Yaml;
use TerraCore\Environment\EnvironmentInterface;

class GitProject extends BaseProject {

  protected $type = 'git';

  /**
   * {@inheritdoc}
   */
  public static function getInterfaceQuestions() {
    $questions = [];
    $questions[] = ['Source code repository URL? ', ''];
    $questions += parent::getInterfaceQuestions();
    return $questions;
  }

  /**
   * @param \Psr\Log\LoggerInterface $logger
   * @param $path
   *
   * @return bool
   * @throws \Exception
   */
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
      if (strpos(strtolower($output), strtolower($this->getLocation())) !== false) {
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
      $wrapper->cloneRepository($this->getLocation(), $path);
    } catch (\GitWrapper\GitException $e) {
      return false;
    }

    chdir($path);
    $logger->info($wrapper->git('branch'));
    $logger->info($wrapper->git('status'));
    return true;
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

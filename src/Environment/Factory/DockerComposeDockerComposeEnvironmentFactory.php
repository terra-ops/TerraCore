<?php
/**
 * @file
 * Contains \TerraCore\Environment\Factory\EnvironmentFactory.php
 */

namespace TerraCore\Environment\Factory;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use TerraCore\Stack\StackInterface;

class DockerComposeEnvironmentFactory implements EnvironmentFactoryInterface {

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @var \TerraCore\Project\ProjectInterface
   */
  protected $project;

  /**
   * @var \TerraCore\Environment\EnvironmentInterface
   */
  protected $environment;

  /**
   * @var \TerraCore\Stack\StackInterface
   */
  protected $stack;

  public function __construct(LoggerInterface $logger, StackInterface $stack) {
    $this->logger = $logger;
    $this->environment = $stack->getEnvironment();
    $this->project = $this->environment->getProject();
    $this->stack = $stack;
  }

  public function build() {
    $this->project->build($this->logger, $this->environment->getPath());
    // Run the build hooks
    $process = $this->project->invokeHook('build');
    $this->logProcessOutput($process);

    $this->stack->generateConfigFile();
    return $this->logger;
  }

  public function deploy($version) {
    $this->project->deploy($version, $this->environment, $this->logger);

    // Run the deploy hooks, if there are any.
    $process = $this->project->invokeHook('deploy');
    $this->logProcessOutput($process);
    return $this->logger;
  }

  public function enable() {
    $process = new Process('docker-compose up -d', $this->stack->getConfigPath());
    $process->setTimeout(null);
    $process->run(function ($type, $buffer) {
      if (Process::ERR === $type) {
        echo 'DOCKER > '.$buffer;
      } else {
        echo 'DOCKER > '.$buffer;
      }
    });
    $this->logProcessOutput($process);

    $this->project->enable($this->logger);
    // Run the enable hooks
    $process = $this->project->invokeHook('enable');
    $this->logProcessOutput($process);
    return $this->logger;
  }

  public function disable() {
    // TODO: Implement disable() method.
  }

  public function duplicate() {
    // TODO: Implement duplicate() method.
  }

  public function destroy() {
    // Run docker-compose kill
    $this->logger->info("Running 'docker-compose kill' in {$this->stack->getConfigPath()}");
    $process = new Process('docker-compose kill', $this->stack->getConfigPath());
    $process->setTimeout(null);
    $process->run(function ($type, $buffer) {
      if (Process::ERR === $type) {
        echo 'DOCKER > '.$buffer;
      } else {
        echo 'DOCKER > '.$buffer;
      }
    });
    $this->logProcessOutput($process);

    // Run docker-compose rm
    echo "\n";
    echo "Running 'docker-compose rm -f' in ".$this->stack->getConfigPath()."\n";
    $process = new Process('docker-compose rm -f', $this->stack->getConfigPath());
    $process->setTimeout(null);
    $process->run(function ($type, $buffer) {
      if (Process::ERR === $type) {
        echo 'DOCKER > '.$buffer;
      } else {
        echo 'DOCKER > '.$buffer;
      }
    });
    $this->logProcessOutput($process);

    try {
      $fp = new Filesystem();
      $fp->remove($this->stack->getConfigPath());
      $this->logger->info("Configuration directory successfully removed.");
    }
    catch (IOException $e) {
      $this->logger->error("Configuration directory removal failure.");
      $this->logger->error($e->getMessage());
    }
    return $this->logger;
  }

  public function scale() {
    // TODO: Implement scale() method.
  }

  protected function logProcessOutput(Process $process) {
    if ($process->isSuccessful()) {
      $this->logger->info($process->getOutput());
      return TRUE;
    }
    $this->logger->error($process->getErrorOutput());
    return FALSE;
  }
}

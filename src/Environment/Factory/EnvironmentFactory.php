<?php
/**
 * @file
 * Contains \TerraCore\Environment\Factory\EnvironmentFactory.php
 */

namespace TerraCore\Environment\Factory;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use TerraCore\Environment\EnvironmentInterface;
use TerraCore\Stack\StackInterface;

class EnvironmentFactory implements EnvironmentFactoryInterface {

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

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
    $this->stack = $stack;
  }

  public function build() {
    $project = $this->environment->getProject();
    $project->build($this->logger, $this->environment->getPath());


    // Run the build hooks
    if (!empty($this->config['hooks']['build'])) {
      chdir($this->getSourcePath());
      $process = new Process($this->config['hooks']['build']);
      $process->setTimeout(6000);
      $process->run(function ($type, $buffer) {
        if (Process::ERR === $type) {
          echo $buffer;
        } else {
          echo $buffer;
        }
      });
      $this->logProcessOutput($process);
    }

    return $this->stack->generateDockerComposeFile();
  }

  public function deploy() {
    // TODO: Implement deploy() method.
  }

  public function enable() {
    $process = new Process('docker-compose up -d', $this->stack->getDockerComposePath());
    $process->setTimeout(null);
    $process->run(function ($type, $buffer) {
      if (Process::ERR === $type) {
        echo 'DOCKER > '.$buffer;
      } else {
        echo 'DOCKER > '.$buffer;
      }
    });
    return $this->logProcessOutput($process);
  }

  public function disable() {
    // TODO: Implement disable() method.
  }

  public function duplicate() {
    // TODO: Implement duplicate() method.
  }

  public function destroy() {
    // Run docker-compose kill
    echo "\n";
    echo "Running 'docker-compose kill' in ".$this->stack->getDockerComposePath()."\n";
    $process = new Process('docker-compose kill', $this->stack->getDockerComposePath());
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
    echo "Running 'docker-compose rm -f' in ".$this->stack->getDockerComposePath()."\n";
    $process = new Process('docker-compose rm -f', $this->stack->getDockerComposePath());
    $process->setTimeout(null);
    $process->run(function ($type, $buffer) {
      if (Process::ERR === $type) {
        echo 'DOCKER > '.$buffer;
      } else {
        echo 'DOCKER > '.$buffer;
      }
    });
    $this->logProcessOutput($process);

    $fp = new Filesystem();
    $fp->remove($this->stack->getDockerComposePath());
  }

  public function scale() {
    // TODO: Implement scale() method.
  }

  protected function logProcessOutput(Process $process) {
    if ($process->isSuccessful()) {
      $this->logger->info($process->getOutput());
      return true;
    }
    $this->logger->info($process->getErrorOutput());
  }
}

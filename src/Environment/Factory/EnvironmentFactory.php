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

  public function __construct(LoggerInterface $logger, EnvironmentInterface $environment, StackInterface $stack) {
    $this->logger = $logger;
    $this->environment = $environment;
    $this->stack = $stack;
  }

  public function build() {
    $path = is_null($path) ? $this->environment->path : $path;

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
      if (strpos(strtolower($output), strtolower($this->app->repo)) !== false) {
        return true;
      } else {
        throw new Exception('Git clone already exists at that path, but it is not for this app.');
      }
    }

    try {
      mkdir($path, 0755, TRUE);
      chdir($path);
      $wrapper = new GitWrapper();
      $wrapper->streamOutput();
      $wrapper->cloneRepository($this->app->repo . '.git', $path);
    } catch (\GitWrapper\GitException $e) {
      return false;
    }

    chdir($path);
    $wrapper->git('branch');
    $wrapper->git('status');
    $this->loadConfig();

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
    }

    return $this->writeConfig();
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
    return $this->logProcessOutput();
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

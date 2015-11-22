<?php
/**
 * @file
 * Contains \TerraCore\Stack\StackBase.php
 */

namespace TerraCore\Stack;

use Psr\Log\LoggerInterface;
use TerraCore\Environment\EnvironmentInterface;

abstract class StackBase implements StackInterface {

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @var \TerraCore\Environment\EnvironmentInterface
   */
  protected $environment;

  public function __construct(LoggerInterface $logger, EnvironmentInterface $environment) {
    $this->logger = $logger;
    $this->environment = $environment;
  }

  public function getEnvironment() {
    return $this->environment;
  }

  public function getLogger() {
    return $this->logger;
  }

}

<?php
/**
 * @file
 * Contains \TerraCore\Environment\Factory\EnvironmentFactoryInterface.php
 */

namespace TerraCore\Environment\Factory;


interface EnvironmentFactoryInterface {

  /**
   * @return \Psr\Log\LoggerInterface
   */
  public function build();

  /**
   * @param string $version
   *
   * @return \Psr\Log\LoggerInterface
   */
  public function deploy($version);

  /**
   * @return \Psr\Log\LoggerInterface
   */
  public function enable();

  /**
   * @return \Psr\Log\LoggerInterface
   */
  public function disable();

  /**
   * @return \Psr\Log\LoggerInterface
   */
  public function duplicate();

  /**
   * @return \Psr\Log\LoggerInterface
   */
  public function destroy();

  /**
   * @return \Psr\Log\LoggerInterface
   */
  public function scale();

}

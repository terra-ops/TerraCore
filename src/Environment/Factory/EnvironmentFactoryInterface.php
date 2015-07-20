<?php
/**
 * @file
 * Contains \TerraCore\Environment\Factory\EnvironmentFactoryInterface.php
 */

namespace TerraCore\Environment\Factory;


interface EnvironmentFactoryInterface {

  public function build();

  public function deploy();

  public function enable();

  public function disable();

  public function duplicate();

  public function destroy();

  public function scale();

}

<?php
/**
 * @file
 * Contains \TerraCore\Environment\EnvironmentFactoryInterface.php
 */

namespace TerraCore\Environment;


interface EnvironmentFactoryInterface {

  public function build();

  public function deploy();

  public function enable();

  public function disable();

  public function duplicate();

  public function destroy();

  public function scale();

}

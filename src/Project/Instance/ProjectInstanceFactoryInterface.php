<?php
/**
 * @file
 * Contains \TerraCore\Project\Instance\ProjectInstanceFactoryInterface.php
 */

namespace TerraCore\Project\Instance;


interface ProjectInstanceFactoryInterface {

  public function build();

  public function deploy();

  public function enable();

  public function disable();

  public function duplicate();

  public function destroy();

  public function scale();

}

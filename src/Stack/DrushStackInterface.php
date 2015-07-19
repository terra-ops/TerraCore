<?php
/**
 * @file
 * Contains \TerraCore\Stack\DrushStackInterface.php
 */

namespace TerraCore\Stack;


interface DrushStackInterface extends StackInterface {

  public function getDrushPort();

  public function writeDrushAlias();

}
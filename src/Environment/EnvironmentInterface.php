<?php
/**
 * @file
 * Contains \TerraCore\Environment\EnvironmentInterface.php
 */

namespace TerraCore\Environment;


interface EnvironmentInterface {

  public function getName();

  public function getPath();

  public function getDocumentRoot();

  public function getHost();

  public function getUrl();

  public function getVersion();

  /**
   * @return \TerraCore\Project\ProjectInterface
   */
  public function getProject();

}

<?php
/**
 * @file
 * Contains \TerraCore\Environment\Environment.php
 */

namespace TerraCore\Environment;


use TerraCore\Project\ProjectInterface;

class Environment implements EnvironmentInterface {

  /**
   * @inheritDoc
   */
  function __construct($name, $path, $doc_root, $host, $url, $version, ProjectInterface $project) {
    // TODO: Implement __construct() method.
  }

  public function getName() {
    // TODO: Implement getName() method.
  }

  public function getPath() {
    // TODO: Implement getPath() method.
  }

  public function getDocumentRoot() {
    // TODO: Implement getDocumentRoot() method.
  }

  public function getHost() {
    // TODO: Implement getHost() method.
  }

  public function getUrl() {
    // TODO: Implement getUrl() method.
  }

  public function getVersion() {
    // TODO: Implement getVersion() method.
  }

  /**
   * @inheritDoc
   */
  public function getProject() {
    // TODO: Implement getProject() method.
  }

}

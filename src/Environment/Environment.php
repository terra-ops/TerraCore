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
    $this->name = $name;
    $this->path = $path;
    $this->docRoot = $doc_root;
    $this->host = $host;
    $this->url = $url;
    $this->version = $version;
    $this->project = $project;
  }

  public function getName() {
    return $this->name;
  }

  public function getPath() {
    return $this->path;
  }

  public function getDocumentRoot() {
    return $this->docRoot;
  }

  public function getHost() {
    return $this->host;
  }

  public function getUrl() {
    return $this->url;
  }

  public function getVersion() {
    return $this->version;
  }

  /**
   * @inheritDoc
   */
  public function getProject() {
    return $this->project;
  }

}

<?php
/**
 * @file
 * Contains \TerraCore\Stack\TerraDrupalStack.php
 */

namespace TerraCore\Stack;


use Symfony\Component\Process\Process;

class TerraDrupalStack extends StackBase {

  public function getDockerComposePath() {
    return getenv('HOME').'/.terra/environments/'.$this->environment->getProject()->getName().'/'.$this->environment->getProject()->getName().'-'.$this->environment->getName();
  }

  public function getDockerComposeArray() {
    // Look for this users SSH public key
    // @TODO: Move ssh_authorized_keys to terra config.  Ask the user on first run.
    $ssh_key_path = $_SERVER['HOME'].'/.ssh/id_rsa.pub';
    $ssh_authorized_keys = '';
    if (file_exists($ssh_key_path)) {
      $ssh_authorized_keys = file_get_contents($ssh_key_path);
    }

    $compose = array();
    $compose['load'] = array(
      'image' => 'tutum/haproxy',
      'environment' => array(
        'VIRTUAL_HOST' => $this->getUrl(),
      ),
      'links' => array(
        'app',
      ),
      'expose' => array(
        '80/tcp',
      ),
      'ports' => array(
        ':80',
      ),
      'restart' => 'always',
    );
    $compose['app'] = array(
      'image' => 'terra/drupal',
      'tty' => true,
      'stdin_open' => true,
      'links' => array(
        'database',
      ),
      'volumes' => array(
        "{$this->environment->getDocumentRoot()}:/usr/share/nginx/html",
      ),
      'environment' => array(
        'HOST_UID' => posix_getuid(),
        'HOST_GID' => posix_getgid(),
      ),
      'expose' => array(
        '80/tcp',
      ),
      'restart' => 'always',
    );
    $compose['database'] = array(
      'image' => 'mariadb',
      'tty' => true,
      'stdin_open' => true,
      'environment' => array(
        'MYSQL_ROOT_PASSWORD' => 'RANDOMIZEPLEASE',
        'MYSQL_DATABASE' => 'drupal',
        'MYSQL_USER' => 'drupal',
        'MYSQL_PASSWORD' => 'drupal',
      ),
      'restart' => 'always',
    );
    $compose['drush'] = array(
      'image' => 'terra/drush',
      'tty' => true,
      'stdin_open' => true,
      'links' => array(
        'database',
      ),
      'ports' => array(
        ':22',
      ),
      'volumes' => array(
        "{$this->environment->getDocumentRoot()}:/var/www/html",
        "{$this->environment->getPath()}:/source",
      ),
      'environment' => array(
        'AUTHORIZED_KEYS' => $ssh_authorized_keys,
      ),
      'restart' => 'always',
    );

    $project = $this->environment->getProject();
    // Add "app_services": Additional containers linked to the app container.
    foreach ($project->getDockerCompose('app_services') as $service => $info) {
      $compose['app']['links'][] = $service;

      // Look for volume paths to change
      foreach ($info['volumes'] as &$volume) {
        $volume = strtr($volume, array(
          '{APP_PATH}' => $this->environment->getPath(),
        ));
      }

      $compose[$service] = $info;
    }

    return $this->mergeDockerComposeProjectOverrides($compose);
  }

  public function getUrl() {
    return $this->environment->getProject()->getName().'.'.$this->environment->getName().'.'.gethostname();
  }

  public function getPort() {
    $process = new Process('docker-compose port load 80', $this->getDockerComposePath());
    $process->run();
    if (!$process->isSuccessful()) {
      return false;
    } else {
      $output_array = explode(':', trim($process->getOutput()));
      return array_pop($output_array);
    }
  }

  public function getScale() {
    // Get current scale of app service
    $process = new Process('docker-compose ps -q app', $this->getDockerComposePath());
    $process->run();
    if (!$process->isSuccessful()) {
      return false;
    }
    $container_list = trim($process->getOutput());
    $lines = explode(PHP_EOL, $container_list);
    $app_scale = count($lines);
    return $app_scale;
  }

}

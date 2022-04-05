<?php

namespace Drupal\lynx_client\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Form\FormState;

class LynxClientController extends ControllerBase {

  public function __construct() {}

  public function view($id) {
    $url = LYNX_CONTENT_API . "/node/" . $id . "?_format=json";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    $return = json_decode(curl_exec($ch), true);

    if (empty($return['message'])){
      // set Page title
      $route = \Drupal::routeMatch()->getRouteObject(); 
      $route->setDefault('_title', $return['title'][0]['value']);

      // set fields
      $date = date('D, m/d/Y - H:i', strtotime($return['changed'][0]['value']));
      $body = $return['body'][0]['value'];
      $img = $return['field_image'][0];
      $link = $return['field_url'][0];
        

      $build = [
        '#theme' => 'lynx_client_content',
        '#title' => $return['title'][0]['value'],
        '#date' => $date,
        '#body' => $body,
        '#img' => $img,
        '#link' => $link
      ];
      return $build;
    } else {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }
  }

}
<?php
namespace Drupal\lynx_client\Plugin\views\query;

use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Lynx Client views query plugin which wraps calls to the Server API in order to
 * expose the results to views.
 *
 * @ViewsQuery(
 *   id = "lynxclient",
 *   title = @Translation("Lynx Client"),
 *   help = @Translation("Query against the Server API.")
 * )
 */
class LynxClient extends QueryPluginBase {

    /**
     * LynxClient constructor.
     *
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition) {
      parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
      return new static(
        $configuration,
        $plugin_id,
        $plugin_definition
      );
    }

    public function ensureTable($table, $relationship = NULL) {
      return '';
    }

    public function addField($table, $field, $alias = '', $params = array()) {
      return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ViewExecutable $view) {
        // CURL request to API Endpoint
        // Shove data into fields
        $url = LYNX_CONTENT_API . "/content-api";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $return = json_decode(curl_exec($ch), true);
        $index = 0;
        foreach ($return as $key => $val) {
            $row['id'] = $val['nid'][0]['value'];
            $row['title'] = $val['title'][0]['value'];
            $row['changed'] = strtotime($val['changed'][0]['value']);
            $row['index'] = $index++;
            $view->result[] = new ResultRow($row);
        }
    }
}
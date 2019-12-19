<?php

namespace Drupal\relations_audit\Controller;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\relations_audit\Commands\StructureRelations;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class StructureRelationsController.
 */
class StructureRelationsController extends ControllerBase {

  /**
   * StructureRelations definition.
   *
   * @var StructureRelations
   */
  protected $relationsAuditStructure;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->relationsAuditStructure = $container->get('relations_audit.structure');
    return $instance;
  }

  /**
   * Endpoint method for /api/structure-relations
   *
   * @param Request $request
   * @return CacheableJsonResponse
   *   Return Hello string.
   */
  public function get(Request $request) {
    $data = $this->relationsAuditStructure->getDataArray($request->query->all());
    $response = new CacheableJsonResponse($data);
    $response->getCacheableMetadata()->addCacheContexts(['url.query_args']);
    return $response;
  }

}

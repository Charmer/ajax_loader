<?php
/**
 * Created by PhpStorm.
 * User: Timur
 * Date: 23.03.2016
 * Time: 11:09
 */
/**
 * @file
 *
 */

namespace Drupal\ajax_loader\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\Core\Entity\Entity;
use Drupal\node\Entity\Node;



class Ajax_loaderController extends ControllerBase
{

    /**
     * {@inheritdoc}

     */
    public function load()
    {
        $response = new JsonResponse();
        $alias = $_GET['alias'];
        $bundle = $_GET['bundle'];
        $node = Node::load(end(explode("/",\Drupal::service("path.alias_manager")->getPathByAlias($alias))));
        $entity_type_id = 'node';
        $bundleFields[$entity_type_id]['title']['type'] = 'text';
        $bundleFields[$entity_type_id]['title']['label'] = 'Title';
        $bundleFields[$entity_type_id]['title']['value'] = $node->getTitle();
        foreach (\Drupal::entityManager()->getFieldDefinitions($entity_type_id, $bundle) as $field_name => $field_definition) {
            if (!empty($field_definition->getTargetBundle())) {
                $bundleFields[$entity_type_id][$field_name]['type'] = $field_definition->getType();
                $bundleFields[$entity_type_id][$field_name]['label'] = $field_definition->getLabel();
                $bundleFields[$entity_type_id][$field_name]['name'] = $field_definition->getName();
                if($bundleFields[$entity_type_id][$field_name]['type'] == 'image')
                {
                    foreach ($node->get($bundleFields[$entity_type_id][$field_name]['name']) as $image)
                    {
                        $bundleFields[$entity_type_id][$field_name]['value'][] = $image->entity->url();
                    }
                }else{
                    $bundleFields[$entity_type_id][$field_name]['value'] = $node->get($bundleFields[$entity_type_id][$field_name]['name'])->getValue();                    
                }
            }
        }
        $response->setData($bundleFields);
        return $response;
    }
}
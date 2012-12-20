<?php

namespace AlbumRest;

return array(
    'controllers' => array(
        'invokables' => array(
            'AlbumRest\Controller\AlbumRest' => 'AlbumRest\Controller\AlbumRestController',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'odm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),
 
    'router' => array(
        'routes' => array(
            'album-rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/album-rest[/:id]',
                    'constraints' => array(
                    'id'          => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'AlbumRest\Controller\AlbumRest',
                    ),
                ),
            ),
        ),
    ),
 
    'view_manager' => array(
        'template_path_stack' => array(
            'album-rest' => __DIR__ . '/../view',
        ),
    ),
);
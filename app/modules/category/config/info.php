<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 11/10/13
 * Time: 11:40 AM
 */
return array(
    'name' => 'category',
    'language' => array('en', 'fa'),
    'sections' => array(
        'frontEnd' => array(
            'controllers' => array(
                'main' => array(
                    'index',
                    'view'
                )
            )
        ),
        'backEnd' => array(
            'controllers' => array(
                'main' => array(
                    'index',
                    'create',
                    'update',
                    'delete',
                    'arrange',
                )
            )
        ),
    ),
    'relations' => array(
        'category' => "array(

);"),
);
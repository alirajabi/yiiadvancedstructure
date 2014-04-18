<?php
/**
 * Created by PhpStorm.
 * User: Ali Rajabi - www.alirajabi.com
 * Date: 11/10/13
 * Time: 11:40 AM
 */
return array(
    'name' => 'default',
    'language' => array('en', 'fa'),
    'sections' => array(
        'frontEnd' => array(
            'controllers' => array(
                'default' => array(
                    'index',
                    'login',
                    'logout',
                    'contact'
                )
            )
        ),
        'backEnd' => array(
            'controllers' => array(
                'default' => array(
                    'index',
                    'login',
                    'logout',
                    'contact'
                )
            )
        )
    )
);
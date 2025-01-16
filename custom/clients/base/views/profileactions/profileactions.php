<?php

include('clients/base/views/profileactions/profileactions.php');
$viewdefs['base']['view']['profileactions'][] =
[
    'route' => '#sg_LogsAggregator',
    'label' => 'LBL_SUGARGATOR_LOG_VIEW',
    'css_class' => 'administration',
    'acl_action' => 'admin',
    'icon' => 'sicon-list-view',
];


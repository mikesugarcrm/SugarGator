<?php
$dictionary['sg_LogsAggregator']['indices'][] = array(
    'name' => 'idx_date_entered_deleted',
    'type' => 'index',
    'fields' => array(
        'date_entered',
        'deleted'
    )
);

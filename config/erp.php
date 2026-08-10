<?php

return [
    'base_currency' => strtoupper(env('ERP_BASE_CURRENCY', 'UZS')),
    'work_days' => array_map(
        'intval',
        array_filter(explode(',', env('ERP_WORK_DAYS', '1,2,3,4,5')), 'strlen')
    ),
    'default_page_size' => 20,
    'maximum_page_size' => 100,
];

<?php
$types = array(
    'NO2' => array('type_name' => 'NO<sub>2>/sub>', 'unit' => 'µg/m<sup>3</sup>', 'low_medium' => 175, 'medium_high' => 200, 'spatial_validity'=>10, 'start_decrease'=>3600, 'fully_gone'=>86400)
);

file_put_contents(gzdeflate(json_encode($types)), 'data/types.data');

exit('Done');

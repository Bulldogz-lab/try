<?php

include_once __DIR__ . '/db.php'; 

$unitsSql = "
    SELECT
        u.unit_id,
        u.unit_number,
        u.unit_name,
        u.unit_type,
        u.floor,
        u.rent_amount,
        u.status,
        u.description,
        p.property_name,
        p.city,
        p.address,
        (
            SELECT ui.image_path
            FROM   unit_images ui
            WHERE  ui.unit_id = u.unit_id
            ORDER BY ui.sort_order ASC, ui.image_id ASC
            LIMIT 1
        ) AS image_path
    FROM  units u
    LEFT JOIN properties p ON p.property_id = u.property_id
    ORDER BY u.unit_id ASC
";

$unitsResult = mysqli_query($conn, $unitsSql);
$units = [];
while ($row = mysqli_fetch_assoc($unitsResult)) {
    $units[] = $row;
}


$amenitiesSql = "
    SELECT ua.unit_id, a.name AS amenity_name, a.icon AS amenity_icon
    FROM   unit_amenities ua
    JOIN   amenities a ON a.amenity_id = ua.amenity_id
    ORDER  BY ua.unit_id
";
$amenResult   = mysqli_query($conn, $amenitiesSql);
$amenitiesMap = [];
while ($row = mysqli_fetch_assoc($amenResult)) {
    $amenitiesMap[$row['unit_id']][] = [
        'name' => $row['amenity_name'],
        'icon' => $row['amenity_icon'],
    ];
}
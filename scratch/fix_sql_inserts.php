<?php
$file = 'c:/xampp4/htdocs/EventManagementSystem/event_management_system.sql';
$content = file_get_contents($file);

// Fix the INSERT INTO line if it was already modified by me (which it was)
$content = str_replace(
    "INSERT INTO `bookings` (`id`, `event_id`, `custom_request_id`, `event_snapshot`, `client_id`, `package_tier`, `package_snapshot`, `event_date`, `guest_count`, `full_name`, `email`, `phone`, `checkin_time`, `total_amount`, `status`, `payment_status`, `created_at`) VALUES",
    "INSERT INTO `bookings` (`id`, `event_id`, `custom_request_id`, `event_snapshot`, `client_id`, `package_tier`, `package_snapshot`, `event_date`, `guest_count`, `full_name`, `email`, `phone`, `checkin_time`, `total_amount`, `status`, `payment_status`, `created_at`) VALUES",
    $content
);

// We need to insert NULL, NULL after the second value (event_id) and NULL after the 6th value (package_tier)
// The lines look like: (1, 2, 3, 'premium', '2026-03-30', 5000, 'Roz Chaudhary', ...)
// After fix: (1, 2, NULL, NULL, 3, 'premium', NULL, '2026-03-30', 5000, 'Roz Chaudhary', ...)

$lines = explode("\n", $content);
$inBookingsInsert = false;
foreach ($lines as &$line) {
    if (strpos($line, "INSERT INTO `bookings`") !== false) {
        $inBookingsInsert = true;
        continue;
    }
    if ($inBookingsInsert) {
        if (trim($line) === '' || strpos($line, ");") !== false || strpos($line, "--") === 0) {
            if (strpos($line, ");") !== false) {
                 $line = preg_replace_callback('/\((.*?)\)/', function($m) {
                    $parts = explode(',', $m[1]);
                    if (count($parts) == 14) {
                        // id, event_id, [NULL, NULL], client_id, package_tier, [NULL], event_date, guest_count, full_name, email, phone, checkin_time, total_amount, status, payment_status, created_at
                        // Wait, original was 14 columns?
                        // 1:id, 2:event_id, 3:client_id, 4:package_tier, 5:event_date, 6:guest_count, 7:full_name, 8:email, 9:phone, 10:checkin_time, 11:total_amount, 12:status, 13:payment_status, 14:created_at
                        array_splice($parts, 2, 0, [' NULL', ' NULL']); // Add after event_id
                        array_splice($parts, 6, 0, [' NULL']); // Add after package_tier
                        return '(' . implode(',', $parts) . ')';
                    }
                    return $m[0];
                }, $line);
            }
            if (strpos($line, ";") !== false) $inBookingsInsert = false;
            continue;
        }
        
        $line = preg_replace_callback('/\((.*?)\),?$/', function($m) {
            $valStr = $m[1];
            // Split by comma, but be careful with commas inside strings (though these are simple)
            // A better way is to use str_getcsv if possible, but it's for CSV.
            // Let's try a simple split since we know the structure.
            $parts = explode(',', $valStr);
            if (count($parts) == 14) {
                array_splice($parts, 2, 0, [' NULL', ' NULL']);
                array_splice($parts, 6, 0, [' NULL']);
                return '(' . implode(',', $parts) . ')' . (strpos($m[0], ',') !== false ? ',' : '');
            }
            return $m[0];
        }, $line);
    }
}

file_put_contents($file, implode("\n", $lines));
echo "SQL file updated successfully.\n";

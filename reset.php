<?php

/**
 * Reset Script — Vanigan Membership
 *
 * Deletes all member data from MongoDB and their images from Cloudinary.
 *
 * Usage: php reset.php
 *        php reset.php --force   (skip confirmation)
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\MongoService;

// ── Check for --force flag ────────────────────────────────────────────
$force = in_array('--force', $argv ?? []);

// ── Connect to MongoDB ────────────────────────────────────────────────
$mongo = app(MongoService::class);

// ── Get all members ───────────────────────────────────────────────────
$result = $mongo->getAllMembers(1, 1000);
$members = $result['members'];
$total = $result['total'];

if ($total === 0) {
    echo "\n  No members found in MongoDB. Nothing to reset.\n\n";
    exit(0);
}

echo "\n";
echo "  ┌─────────────────────────────────────────────┐\n";
echo "  │         VANIGAN RESET SCRIPT                 │\n";
echo "  └─────────────────────────────────────────────┘\n\n";
echo "  Found {$total} member(s) in MongoDB:\n\n";

foreach ($members as $i => $m) {
    $name = $m['name'] ?? 'N/A';
    $id = $m['unique_id'] ?? 'N/A';
    $epic = $m['epic_no'] ?? 'N/A';
    echo "    " . ($i + 1) . ". {$name} | {$id} | EPIC: {$epic}\n";
}

echo "\n";
echo "  This will DELETE:\n";
echo "    - All {$total} member(s) from MongoDB\n";
echo "    - Their photos from Cloudinary (vanigan/member_photos/)\n";
echo "    - Their ID card images from Cloudinary (vanigan/cards/)\n";
echo "\n";

// ── Confirmation ──────────────────────────────────────────────────────
if (!$force) {
    echo "  Type 'RESET' to confirm: ";
    $input = trim(fgets(STDIN));
    if ($input !== 'RESET') {
        echo "\n  Aborted.\n\n";
        exit(0);
    }
}

echo "\n";

// ── Delete from Cloudinary ────────────────────────────────────────────
$cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));
$deletedCloudinary = 0;

foreach ($members as $m) {
    $uniqueId = $m['unique_id'] ?? '';
    $epicNo = $m['epic_no'] ?? '';
    $photoUrl = $m['photo_url'] ?? '';

    // Delete member photo from Cloudinary
    if ($photoUrl && $epicNo) {
        // Extract public_id from URL: vanigan/member_photos/EPICNO_timestamp
        if (preg_match('/vanigan\/member_photos\/([^\.]+)/', $photoUrl, $matches)) {
            $publicId = 'vanigan/member_photos/' . $matches[1];
            try {
                $cloudinary->uploadApi()->destroy($publicId);
                echo "  [OK] Deleted photo: {$publicId}\n";
                $deletedCloudinary++;
            } catch (Exception $e) {
                echo "  [!!] Failed to delete photo {$publicId}: " . $e->getMessage() . "\n";
            }
        }
    }

    // Delete card images from Cloudinary (front & back)
    if ($uniqueId) {
        foreach (['front', 'back'] as $side) {
            $publicId = "vanigan/cards/{$uniqueId}/{$side}";
            try {
                $cloudinary->uploadApi()->destroy($publicId);
                echo "  [OK] Deleted card: {$publicId}\n";
                $deletedCloudinary++;
            } catch (Exception $e) {
                echo "  [!!] Failed to delete card {$publicId}: " . $e->getMessage() . "\n";
            }
        }
    }
}

// ── Delete all members from MongoDB ───────────────────────────────────
echo "\n";
$deleteResult = $mongo->deleteAllMembers();
$deletedCount = $deleteResult['deletedCount'] ?? 0;

echo "  ┌─────────────────────────────────────────────┐\n";
echo "  │  RESET COMPLETE                              │\n";
echo "  │                                              │\n";
echo "  │  MongoDB:    {$deletedCount} member(s) deleted" . str_repeat(' ', max(0, 15 - strlen((string)$deletedCount))) . "│\n";
echo "  │  Cloudinary: {$deletedCloudinary} file(s) deleted" . str_repeat(' ', max(0, 15 - strlen((string)$deletedCloudinary))) . "│\n";
echo "  └─────────────────────────────────────────────┘\n\n";

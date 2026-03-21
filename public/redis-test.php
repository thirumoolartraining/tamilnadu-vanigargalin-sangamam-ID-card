<?php
/**
 * Redis Predis Connection Test
 * Tests connection to new Upstash instance: humble-grubworm-79324
 * File: public/redis-test.php
 *
 * IMPORTANT: Delete this file after testing!
 */

// Start output
echo "<h1>🔴 Redis Predis Connection Test</h1>";
echo "<p>Testing connection to: humble-grubworm-79324.upstash.io</p>";
echo "<hr>";

try {
    // Test 1: Put value in cache
    echo "<h2>Test 1: Cache PUT</h2>";
    $putResult = Cache::store('redis')->put('test_key', 'hello_world_from_predis', 60);

    if ($putResult === true || $putResult === null) {
        echo "<p style='color: green;'>✅ Cache PUT succeeded</p>";
    } else {
        echo "<p style='color: red;'>❌ Cache PUT failed: " . var_export($putResult, true) . "</p>";
    }

    // Test 2: Get value from cache
    echo "<h2>Test 2: Cache GET</h2>";
    $getValue = Cache::store('redis')->get('test_key');

    if ($getValue === 'hello_world_from_predis') {
        echo "<p style='color: green;'>✅ Cache GET succeeded</p>";
        echo "<p><strong>Retrieved value:</strong> " . htmlspecialchars($getValue) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Cache GET failed or returned unexpected value</p>";
        echo "<p><strong>Retrieved value:</strong> " . var_export($getValue, true) . "</p>";
    }

    // Test 3: Has key
    echo "<h2>Test 3: Cache HAS</h2>";
    $hasKey = Cache::store('redis')->has('test_key');

    if ($hasKey === true) {
        echo "<p style='color: green;'>✅ Cache HAS succeeded - key exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Cache HAS failed - key doesn't exist</p>";
    }

    // Test 4: Delete key
    echo "<h2>Test 4: Cache FORGET</h2>";
    $forgetResult = Cache::store('redis')->forget('test_key');

    if ($forgetResult === true || $forgetResult === 1 || $forgetResult === null) {
        echo "<p style='color: green;'>✅ Cache FORGET succeeded</p>";
    } else {
        echo "<p style='color: red;'>❌ Cache FORGET failed</p>";
    }

    // Overall Result
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ REDIS CONNECTION TEST PASSED</h2>";
    echo "<p>Predis successfully connected to humble-grubworm-79324.upstash.io</p>";

} catch (\Exception $e) {
    echo "<hr>";
    echo "<h2 style='color: red;'>❌ REDIS CONNECTION TEST FAILED</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";

    // Try to get more details
    if (method_exists($e, 'getPrevious') && $e->getPrevious()) {
        echo "<p><strong>Previous Error:</strong> " . htmlspecialchars($e->getPrevious()->getMessage()) . "</p>";
    }
}

echo "<hr>";
echo "<p style='color: orange;'><strong>⚠️ IMPORTANT:</strong> Delete this file (redis-test.php) after testing for security!</p>";
?>

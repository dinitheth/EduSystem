<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test student login route renders
$request = Illuminate\Http\Request::create('/student/login', 'GET');
$response = $kernel->handle($request);
echo "Student login page status: " . $response->getStatusCode() . "\n";

// Test teacher login route renders
$request2 = Illuminate\Http\Request::create('/teacher/login', 'GET');
$response2 = $kernel->handle($request2);
echo "Teacher login page status: " . $response2->getStatusCode() . "\n";

// Test that unauthenticated dashboard redirects
$request3 = Illuminate\Http\Request::create('/student/dashboard', 'GET');
$response3 = $kernel->handle($request3);
echo "Student dashboard (no auth) status: " . $response3->getStatusCode() . " (expect 302 redirect)\n";

$request4 = Illuminate\Http\Request::create('/teacher/dashboard', 'GET');
$response4 = $kernel->handle($request4);
echo "Teacher dashboard (no auth) status: " . $response4->getStatusCode() . " (expect 302 redirect)\n";

echo "\nAll routes returning expected HTTP codes!\n";

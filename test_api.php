<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
echo "User: {$user->name} ID: {$user->id}\n";

$token = Str::random(60);
$user->api_token = $token;
$user->save();
echo "Token: {$token}\n";

echo "\n--- Testing API ---\n";
$client = new GuzzleHttp\Client();

try {
    $response = $client->post('https://chatmego.com/api/chat/send/1', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'message' => 'Test message from API',
            'type' => 'text',
        ],
    ]);
    
    $body = $response->getBody()->getContents();
    echo "Response: {$body}\n";
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    if ($e->getResponse()) {
        echo "Response body: {$e->getResponse()->getBody()->getContents()}\n";
    }
}

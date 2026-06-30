<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user1 = App\Models\User::find(1);
$user2 = App\Models\User::find(2);

echo "User1: {$user1->name} ID: {$user1->id}\n";
echo "User2: {$user2->name} ID: {$user2->id}\n";

echo "\n--- Testing send message ---\n";
$message = App\Models\Message::create([
    'from_user_id' => 1,
    'to_user_id' => 2,
    'message' => 'Test message from API',
    'type' => 'text',
]);

echo "Message created: ID={$message->id}\n";

echo "\n--- Testing fetch messages ---\n";
$messages = App\Models\Message::where(function ($query) {
    $query->where('from_user_id', 1)->where('to_user_id', 2);
})->orWhere(function ($query) {
    $query->where('from_user_id', 2)->where('to_user_id', 1);
})->orderBy('created_at', 'asc')->get();

echo "Total messages: " . $messages->count() . "\n";
foreach ($messages as $msg) {
    echo "  ID:{$msg->id} From:{$msg->from_user_id} To:{$msg->to_user_id} '{$msg->message}'\n";
}

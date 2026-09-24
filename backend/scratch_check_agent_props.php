<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(4);
$ctrl = app(App\Http\Controllers\Api\V1\Agent\AgentDashboardController::class);
$req = Illuminate\Http\Request::create('/api/v1/agent/properties', 'GET');
$req->setUserResolver(fn() => $user);
$res = $ctrl->properties($req);
echo json_encode($res->getData(), JSON_PRETTY_PRINT);

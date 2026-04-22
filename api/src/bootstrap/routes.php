<?php

return [
    ['GET', '/api/health', [SystemApiController::class, 'health'], []],
    ['GET', '/api/protected-ping', [SystemApiController::class, 'protectedPing'], [SessionAuthMiddleware::class]],
];

<?php

return [
    ['GET', '/api/health', [SystemApiController::class, 'health'], []],
    ['GET', '/api/protected-ping', [SystemApiController::class, 'protectedPing'], [SessionAuthMiddleware::class]],
    ['GET', '/api/v1/health', [SystemApiController::class, 'health'], []],

    // Auth v1
    ['POST', '/api/v1/auth/register', [AuthApiController::class, 'register'], []],
    ['POST', '/api/v1/auth/login', [AuthApiController::class, 'login'], []],
    ['POST', '/api/v1/auth/forgot-password', [AuthApiController::class, 'forgotPassword'], []],
    ['POST', '/api/v1/auth/verify-otp', [AuthApiController::class, 'verifyOtp'], []],
    ['POST', '/api/v1/auth/reset-password', [AuthApiController::class, 'resetPassword'], []],
    ['POST', '/api/v1/auth/logout', [AuthApiController::class, 'logout'], []],
    ['GET', '/api/v1/auth/me', [AuthApiController::class, 'me'], [JwtAuthMiddleware::class]],
    ['POST', '/api/v1/auth/profile', [AuthApiController::class, 'updateProfile'], [JwtAuthMiddleware::class]],
    ['POST', '/api/v1/auth/profile/picture', [AuthApiController::class, 'updateProfilePicture'], [JwtAuthMiddleware::class]],
    ['DELETE', '/api/v1/auth/profile/picture', [AuthApiController::class, 'deleteProfilePicture'], [JwtAuthMiddleware::class]],

    // Events
    ['GET', '/api/v1/events', [EventApiController::class, 'index'], [JwtAuthMiddleware::class]],
    ['GET', '/api/v1/events/{id}', [EventApiController::class, 'show'], [JwtAuthMiddleware::class]],
    ['POST', '/api/v1/events', [EventApiController::class, 'store'], [JwtAuthMiddleware::class]],
    ['PUT', '/api/v1/events/{id}', [EventApiController::class, 'update'], [JwtAuthMiddleware::class]],
    ['DELETE', '/api/v1/events/{id}', [EventApiController::class, 'delete'], [JwtAuthMiddleware::class]],

    // Bookings
    ['GET', '/api/v1/bookings', [BookingApiController::class, 'index'], [JwtAuthMiddleware::class]],
    ['GET', '/api/v1/bookings/{id}', [BookingApiController::class, 'show'], [JwtAuthMiddleware::class]],
    ['POST', '/api/v1/bookings', [BookingApiController::class, 'store'], [JwtAuthMiddleware::class]],
    ['PATCH', '/api/v1/bookings/{id}/cancel', [BookingApiController::class, 'cancel'], [JwtAuthMiddleware::class]],
    ['PATCH', '/api/v1/bookings/{id}/approve', [BookingApiController::class, 'approve'], [JwtAuthMiddleware::class]],
    ['PATCH', '/api/v1/bookings/{id}/mark-paid', [BookingApiController::class, 'markPaid'], [JwtAuthMiddleware::class]],

    // Dashboards
    ['GET', '/api/v1/dashboard/admin', [DashboardApiController::class, 'admin'], [JwtAuthMiddleware::class]],
    ['GET', '/api/v1/dashboard/organizer', [DashboardApiController::class, 'organizer'], [JwtAuthMiddleware::class]],
    ['GET', '/api/v1/dashboard/client', [DashboardApiController::class, 'client'], [JwtAuthMiddleware::class]],

    // Payments
    ['POST', '/api/v1/payments/checkout', [PaymentApiController::class, 'checkout'], [JwtAuthMiddleware::class]],
    ['POST', '/api/v1/payments/confirm', [PaymentApiController::class, 'confirm'], [JwtAuthMiddleware::class]],
    ['GET', '/api/v1/payments/{bookingId}/summary', [PaymentApiController::class, 'summary'], [JwtAuthMiddleware::class]],
    ['GET', '/api/v1/payments/{bookingId}/history', [PaymentApiController::class, 'history'], [JwtAuthMiddleware::class]],

    // Notifications
    ['GET', '/api/v1/notifications/latest', [NotificationApiController::class, 'latest'], [JwtAuthMiddleware::class]],
    ['GET', '/api/v1/notifications', [NotificationApiController::class, 'index'], [JwtAuthMiddleware::class]],
    ['GET', '/api/v1/notifications/counts', [NotificationApiController::class, 'counts'], [JwtAuthMiddleware::class]],
    ['PATCH', '/api/v1/notifications/{id}/read', [NotificationApiController::class, 'read'], [JwtAuthMiddleware::class]],
    ['PATCH', '/api/v1/notifications/{id}/unread', [NotificationApiController::class, 'unread'], [JwtAuthMiddleware::class]],
    ['PATCH', '/api/v1/notifications/mark-all-read', [NotificationApiController::class, 'markAllRead'], [JwtAuthMiddleware::class]],
    ['PATCH', '/api/v1/notifications/mark-all-unread', [NotificationApiController::class, 'markAllUnread'], [JwtAuthMiddleware::class]],
    ['DELETE', '/api/v1/notifications/{id}', [NotificationApiController::class, 'deleteOne'], [JwtAuthMiddleware::class]],
    ['DELETE', '/api/v1/notifications', [NotificationApiController::class, 'deleteAll'], [JwtAuthMiddleware::class]],

    // Admin User Management
    ['GET', '/api/v1/admin/users', [UserApiController::class, 'index'], [JwtAuthMiddleware::class]],
    ['POST', '/api/v1/admin/users/update-role', [UserApiController::class, 'updateRole'], [JwtAuthMiddleware::class]],
    ['POST', '/api/v1/admin/users/toggle-block', [UserApiController::class, 'toggleBlock'], [JwtAuthMiddleware::class]],

    // Feedback
    ['GET', '/api/v1/feedback', [FeedbackApiController::class, 'list'], []],
    ['GET', '/api/v1/feedback/my', [FeedbackApiController::class, 'myFeedback'], [JwtAuthMiddleware::class]],
    ['POST', '/api/v1/feedback', [FeedbackApiController::class, 'create'], [JwtAuthMiddleware::class]],
    ['POST', '/api/v1/feedback/reply', [FeedbackApiController::class, 'reply'], [JwtAuthMiddleware::class]],
    ['PATCH', '/api/v1/feedback', [FeedbackApiController::class, 'update'], [JwtAuthMiddleware::class]],
    ['PATCH', '/api/v1/feedback/reply', [FeedbackApiController::class, 'updateReply'], [JwtAuthMiddleware::class]],
    ['GET', '/api/v1/feedback/stats', [FeedbackApiController::class, 'stats'], []],
];

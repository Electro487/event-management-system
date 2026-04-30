<?php
class HomeController
{
    public function index()
    {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        /* If logged in, redirect to dashboard
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
            $role = $_SESSION['user_role'];
            if ($role === 'client') {
                $redirect = '/EventManagementSystem/public/client/events';
            } else {
                $redirect = '/EventManagementSystem/public/' . $role . '/dashboard';
            }
            header('Location: ' . $redirect);
            exit;
        } */

        // Fetch review stats
        require_once dirname(__DIR__) . '/config/database.php';
        try {
            $db = new Database();
            $pdo = $db->getConnection();
            $stmt = $pdo->query("SELECT COUNT(*) as total_reviews, AVG(rating) as avg_rating FROM feedbacks");
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalReviews = $stats['total_reviews'] ? (int)$stats['total_reviews'] : 0;
            $avgRating = $stats['avg_rating'] ? round((float)$stats['avg_rating'], 1) : 0.0;
        } catch (Exception $e) {
            $totalReviews = 0;
            $avgRating = 0.0;
        }

        // Show the public landing page (from the home folder)
        require_once dirname(__DIR__) . '/views/home/index.php';
    }

    public function homePage()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Redirect to login if not logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /EventManagementSystem/public/login');
            exit;
        }

        // If logged in, redirect to respective home/dashboard
        if (isset($_SESSION['user_role'])) {
            $role = $_SESSION['user_role'];
            if ($role === 'client') {
                header('Location: /EventManagementSystem/public/client/home');
                exit;
            } else {
                header('Location: /EventManagementSystem/public/' . $role . '/dashboard');
                exit;
            }
        }

        require_once dirname(__DIR__) . '/views/home/home.php';
    }
}

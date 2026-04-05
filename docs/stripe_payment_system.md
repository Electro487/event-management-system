# Stripe Payment System Documentation (50/50 Advance Policy)

This document provides a comprehensive guide for developers to understand, set up, and troubleshoot the Stripe payment integration in the Event Management System.

## 1. Overview
The system follows a **50/50 payment policy**:
- **Advance Payment**: 50% of the total amount must be paid online via Stripe to secure the booking.
- **Remaining Balance**: The remaining 50% is handled offline (e.g., in cash directly to the organizer) upon service delivery.

---

## 2. File Structure & Responsibilities

### **Backend Logic**
- **`app/controllers/PaymentController.php`**: 
  - `checkout()`: Creates a Stripe Checkout Session for the 50% advance.
  - `success()`: Verifies the payment, records it in the DB, and sends notifications.
  - `cancel()`: Handles payment cancellation before completion.
- **`app/models/Payment.php`**: Manages recording payments with `stripe_session_id`.
- **`app/models/Booking.php`**: Handles updating booking payment status to `partially_paid`.
- **`app/models/Notification.php`**: Alerts both the client and organizer about payment status.

### **UI & Frontend**
- **`app/views/client/my_bookings.php`**:
  - Displays the payment breakdown (50% Advance vs. 50% Balance).
  - Contains the "Pay Now" button which redirects to the Stripe Checkout page.
- **`app/views/client/payment/success.php`**: The "Thank You" page shown after a successful transaction.

### **Configuration**
- **`app/config/config.php`**: Defines global constants for Stripe Publishable and Secret Keys.
- **`.env.example`**: Lists the required environment variables.

---

## 3. Setup Instructions (For New Developers)

If you have just pulled the code from the repository, follow these steps:

### **Step 1: Install Dependencies**
You must install the Stripe PHP SDK via Composer.
```bash
composer install
```

### **Step 2: Database Update**
Ensure your local database reflects the latest column additions.
1. Open your database management tool (e.g., phpMyAdmin).
2. Run/Import the latest **`database/schema.sql`**.
   - *Key Change*: Added `stripe_session_id` to `payments` table and `partially_paid` to `bookings.payment_status`.

### **Step 3: Configure Environment Variables**
1. Copy `.env.example` to a new file named `.env`.
2. Visit the [Stripe Dashboard](https://dashboard.stripe.com/test/apikeys) (Test Mode) to get your keys.
3. Fill in your `.env` file:
```env
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
```

---

## 4. Technical Payment Flow

1. **Initiation**: User clicks "Pay 50% Advance" on the My Bookings page.
2. **Checkout Creation**: `PaymentController::checkout` uses the Stripe PHP SDK to create a session:
   - Currency: `npr` (Nepalese Rupee).
   - Amount: `(total_amount * 0.50 * 100)` (converted to paisa).
3. **Stripe Redirect**: The user is redirected to Stripe's hosted checkout page.
4. **Success Redirect**: Upon successful payment, Stripe redirects to `/client/payment/success?session_id={CHECKOUT_SESSION_ID}`.
5. **Verification & DB Update**:
   - The controller retrieves the session using the `session_id`.
   - Payment record is inserted into `payments` table.
   - Booking status is updated to `partially_paid`.
6. **Alerts**: Automated notifications are created for both the client and the organizer using the `Notification` model.

---

## 5. Troubleshooting
- **Errors during Checkout**: Ensure the `URL_ROOT` in `config.php` is correct (e.g., `http://localhost/EventManagementSystem/public`).
- **Database Errors**: Check if the `stripe_session_id` column exists in the `payments` table.
- **Library Not Found**: Ensure the `/vendor` folder exists and `composer install` was successful.

---
*Created on: 2026-04-05*

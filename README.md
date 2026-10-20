# Bus Ticket Management System (PHP + MySQL)

A beginner-friendly academic project using plain PHP, PDO, MySQL, HTML and CSS.

## Features
- Customer registration and login
- Bus search by origin, destination and travel date
- Bus trip listing with fare and seat availability
- Booking of 1–6 seats
- Booking reference and booking history
- Cancellation of future confirmed bookings
- Admin-only form to add bus trips and view existing trips
- Prepared SQL statements, password hashing, output escaping, CSRF tokens and transactions with row locking

## Requirements
- PHP 8.1+ with PDO MySQL extension
- MySQL 8+ or a compatible MariaDB version
- Apache (XAMPP/WAMP/LAMP) or PHP's local development server

## Setup (XAMPP)
1. Extract the `bus_ticket_management` folder into `C:\\xampp\\htdocs\\`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin, select **Import**, and import `database/schema.sql`.
4. Edit `config/database.php` and set the correct database username/password.
5. Visit `http://localhost/bus_ticket_management/`.

Alternatively, from the project directory run:
```bash
php -S 127.0.0.1:8000
```
Then open `http://127.0.0.1:8000/`. Ensure MySQL is running and the schema has been imported.

## Create an administrator
1. Register a normal account through the website.
2. In phpMyAdmin, open `bus_ticket_db` → SQL and run:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```
3. Log out and log back in. The **Manage Buses** menu should appear.
Do not expose an admin-creation page publicly.

## Main files
- `index.php` — search/list trips
- `register.php`, `login.php`, `logout.php` — authentication
- `book.php` — seat booking and availability check
- `my_bookings.php`, `cancel_booking.php` — booking history and cancellation
- `admin_buses.php` — admin trip creation
- `config/database.php` — PDO connection
- `includes/functions.php` — authentication helpers, CSRF and output escaping
- `database/schema.sql` — database schema and sample trips

## Core workflow
1. A customer registers and logs in.
2. Customer searches trips by route/date.
3. Customer selects a trip and seats.
4. The booking transaction locks the bus row, verifies remaining seats, inserts the booking, updates booked seats and commits.
5. Cancellation changes booking status and releases seats in a transaction.

## Important limitations
This is an educational starter project, not a complete commercial booking platform. It does not include online payment, seat-by-seat seat maps, email/SMS notifications, refunds, boarding-point management, operator APIs, or production deployment configuration. Before production use, add HTTPS, secure cookie settings, rate limiting, audit logging, automated tests, backups, payment-webhook verification, and a more complete cancellation/refund policy.

## Suggested test cases
- Register with valid details; attempt duplicate email.
- Login with correct and incorrect passwords.
- Search for Ahmedabad → Vadodara on the sample trip date.
- Book 1 seat and verify remaining seats decrease by one.
- Try booking more seats than available.
- Cancel a future booking and verify seats are released.
- Attempt to access `admin_buses.php` as a customer.
- Submit a form with a missing/invalid CSRF token.

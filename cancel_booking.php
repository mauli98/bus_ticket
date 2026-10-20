<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: my_bookings.php'); exit; }
verify_csrf();
$bookingId = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT bk.*, b.travel_date FROM bookings bk JOIN buses b ON b.id=bk.bus_id WHERE bk.id=? AND bk.user_id=? FOR UPDATE");
    $stmt->execute([$bookingId ?: 0, $_SESSION['user_id']]); $booking = $stmt->fetch();
    if (!$booking || $booking['status'] !== 'confirmed' || $booking['travel_date'] < date('Y-m-d')) {
        throw new RuntimeException('This booking cannot be cancelled.');
    }
    $stmt = $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
    $stmt->execute([$booking['id']]);
    $stmt = $pdo->prepare('UPDATE buses SET booked_seats = GREATEST(0, booked_seats - ?) WHERE id=?');
    $stmt->execute([$booking['seats'], $booking['bus_id']]);
    $pdo->commit();
    header('Location: my_bookings.php?cancelled=1'); exit;
} catch (Throwable $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(400);
    exit($ex instanceof RuntimeException ? e($ex->getMessage()) : 'Could not cancel booking.');
}

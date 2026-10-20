<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$page_title = 'Book Ticket'; $error = '';
$busId = filter_input(INPUT_GET, 'bus_id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $busId = filter_input(INPUT_POST, 'bus_id', FILTER_VALIDATE_INT);
    $seats = filter_input(INPUT_POST, 'seats', FILTER_VALIDATE_INT);
    if (!$busId || !$seats || $seats < 1 || $seats > 6) {
        $error = 'Choose between 1 and 6 seats.';
    } else {
        try {
            $pdo->beginTransaction();
            // Lock the bus row to prevent two requests from overselling the same seats.
            $stmt = $pdo->prepare("SELECT * FROM buses WHERE id = ? AND status = 'active' FOR UPDATE");
            $stmt->execute([$busId]); $bus = $stmt->fetch();
            if (!$bus || ((int)$bus['total_seats'] - (int)$bus['booked_seats']) < $seats || $bus['travel_date'] < date('Y-m-d')) {
                throw new RuntimeException('This bus does not have enough seats or is no longer bookable.');
            }
            $bookingRef = 'BT' . strtoupper(bin2hex(random_bytes(4)));
            $total = (float)$bus['fare'] * $seats;
            $stmt = $pdo->prepare("INSERT INTO bookings (booking_ref,user_id,bus_id,seats,total_amount,status) VALUES (?,?,?,?,?,'confirmed')");
            $stmt->execute([$bookingRef, $_SESSION['user_id'], $busId, $seats, $total]);
            $stmt = $pdo->prepare('UPDATE buses SET booked_seats = booked_seats + ? WHERE id = ?');
            $stmt->execute([$seats, $busId]);
            $pdo->commit();
            header('Location: my_bookings.php?booked=1'); exit;
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = $ex instanceof RuntimeException ? $ex->getMessage() : 'Booking failed. Please try again.';
        }
    }
}
$stmt = $pdo->prepare("SELECT *, (total_seats-booked_seats) AS available_seats FROM buses WHERE id = ? AND status='active'");
$stmt->execute([$busId ?: 0]); $bus = $stmt->fetch();
require __DIR__ . '/includes/header.php';
?>
<h1>Book ticket</h1>
<?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
<?php if (!$bus): ?><div class="card">Bus not found or inactive. <a href="index.php">Return to search</a>.</div>
<?php else: ?><div class="card">
<h2><?= e($bus['bus_name']) ?></h2>
<p><?= e($bus['from_city']) ?> → <?= e($bus['to_city']) ?></p>
<p><?= e($bus['travel_date']) ?> at <?= e(substr($bus['departure_time'],0,5)) ?> · ₹<?= number_format((float)$bus['fare'],2) ?> per seat</p>
<p>Seats available: <?= (int)$bus['available_seats'] ?></p>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<input type="hidden" name="bus_id" value="<?= (int)$bus['id'] ?>">
<label>Number of seats (1–6)</label><input type="number" name="seats" min="1" max="6" value="1" required>
<button type="submit">Confirm booking</button></form></div><?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>

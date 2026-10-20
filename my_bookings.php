<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login(); $page_title = 'My Bookings';
$stmt = $pdo->prepare("SELECT bk.*, b.bus_name,b.from_city,b.to_city,b.travel_date,b.departure_time
 FROM bookings bk JOIN buses b ON b.id=bk.bus_id WHERE bk.user_id=? ORDER BY bk.created_at DESC");
$stmt->execute([$_SESSION['user_id']]); $bookings = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<h1>My bookings</h1>
<?php if (isset($_GET['booked'])): ?><p class="notice">Booking confirmed successfully.</p><?php endif; ?>
<?php if (isset($_GET['cancelled'])): ?><p class="notice">Booking cancelled successfully.</p><?php endif; ?>
<div class="card"><table><thead><tr><th>Reference</th><th>Bus / route</th><th>Travel</th><th>Seats</th><th>Total</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach ($bookings as $b): ?><tr>
<td><?= e($b['booking_ref']) ?></td><td><?= e($b['bus_name']) ?><br><?= e($b['from_city']) ?> → <?= e($b['to_city']) ?></td>
<td><?= e($b['travel_date']) ?><br><?= e(substr($b['departure_time'],0,5)) ?></td><td><?= (int)$b['seats'] ?></td>
<td>₹<?= number_format((float)$b['total_amount'],2) ?></td><td><?= e(ucfirst($b['status'])) ?></td>
<td><?php if ($b['status']==='confirmed' && $b['travel_date'] >= date('Y-m-d')): ?>
<form method="post" action="cancel_booking.php" onsubmit="return confirm('Cancel this booking?')">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
<button class="btn danger" type="submit">Cancel</button></form><?php else: ?>—<?php endif; ?></td>
</tr><?php endforeach; ?>
</tbody></table></div>
<?php if (!$bookings): ?><p>You have not made any bookings yet.</p><?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>

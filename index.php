<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Find a Bus';
$from = trim($_GET['from'] ?? '');
$to = trim($_GET['to'] ?? '');
$date = trim($_GET['date'] ?? '');
$sql = "SELECT b.*, (b.total_seats - b.booked_seats) AS available_seats
        FROM buses b WHERE b.status = 'active'";
$params = [];
if ($from !== '') { $sql .= " AND b.from_city LIKE ?"; $params[] = "%{$from}%"; }
if ($to !== '') { $sql .= " AND b.to_city LIKE ?"; $params[] = "%{$to}%"; }
if ($date !== '') { $sql .= " AND b.travel_date = ?"; $params[] = $date; }
$sql .= " ORDER BY b.travel_date, b.departure_time";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$buses = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<h1>Find and book bus tickets</h1>
<p class="muted">Search routes, check availability, and reserve seats.</p>
<div class="card"><form method="get" class="grid">
<div><label>From city</label><input name="from" value="<?= e($from) ?>" placeholder="Ahmedabad"></div>
<div><label>To city</label><input name="to" value="<?= e($to) ?>" placeholder="Vadodara"></div>
<div><label>Travel date</label><input type="date" name="date" value="<?= e($date) ?>"></div>
<div style="align-self:end"><button type="submit">Search buses</button> <a class="btn secondary" href="index.php">Reset</a></div>
</form></div>
<?php if (!$buses): ?><div class="card">No buses found. Try another route or date.</div><?php else: ?>
<div class="card"><table><thead><tr><th>Bus</th><th>Route</th><th>Date / departure</th><th>Fare</th><th>Seats left</th><th>Action</th></tr></thead><tbody>
<?php foreach ($buses as $bus): ?><tr>
<td><?= e($bus['bus_name']) ?><br><small><?= e($bus['bus_type']) ?></small></td>
<td><?= e($bus['from_city']) ?> → <?= e($bus['to_city']) ?></td>
<td><?= e($bus['travel_date']) ?><br><?= e(substr($bus['departure_time'],0,5)) ?></td>
<td>₹<?= number_format((float)$bus['fare'], 2) ?></td><td><?= (int)$bus['available_seats'] ?></td>
<td><?php if ((int)$bus['available_seats'] > 0): ?><a class="btn" href="book.php?bus_id=<?= (int)$bus['id'] ?>">Book</a><?php else: ?>Full<?php endif; ?></td>
</tr><?php endforeach; ?></tbody></table></div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>

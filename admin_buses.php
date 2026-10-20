<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_admin(); $page_title = 'Manage Buses'; $message = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['bus_name'] ?? '');
    $type = trim($_POST['bus_type'] ?? 'AC');
    $from = trim($_POST['from_city'] ?? '');
    $to = trim($_POST['to_city'] ?? '');
    $date = $_POST['travel_date'] ?? '';
    $time = $_POST['departure_time'] ?? '';
    $fare = filter_input(INPUT_POST, 'fare', FILTER_VALIDATE_FLOAT);
    $seats = filter_input(INPUT_POST, 'total_seats', FILTER_VALIDATE_INT);
    if ($name==='' || $from==='' || $to==='' || $from===$to || !$date || !$time || $date < date('Y-m-d') || !$fare || $fare<=0 || !$seats || $seats<1 || $seats>100) {
        $error = 'Enter valid bus details. Fare must be positive and seats must be 1–100.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO buses (bus_name,bus_type,from_city,to_city,travel_date,departure_time,fare,total_seats,booked_seats,status) VALUES (?,?,?,?,?,?,?,?,0,'active')");
        $stmt->execute([$name,$type,$from,$to,$date,$time,$fare,$seats]);
        $message = 'Bus added successfully.';
    }
}
$buses = $pdo->query('SELECT *, (total_seats-booked_seats) AS available_seats FROM buses ORDER BY travel_date DESC,id DESC')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<h1>Admin · Manage buses</h1>
<?php if ($message): ?><p class="notice"><?= e($message) ?></p><?php endif; ?>
<?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
<div class="card"><h2>Add bus trip</h2>
<form method="post" class="grid"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div><label>Bus name / number</label><input name="bus_name" required></div>
<div><label>Bus type</label><select name="bus_type"><option>AC</option><option>Non-AC</option><option>Sleeper</option><option>AC Sleeper</option></select></div>
<div><label>From city</label><input name="from_city" required></div><div><label>To city</label><input name="to_city" required></div>
<div><label>Travel date</label><input type="date" name="travel_date" min="<?= date('Y-m-d') ?>" required></div>
<div><label>Departure time</label><input type="time" name="departure_time" required></div>
<div><label>Fare per seat (INR)</label><input type="number" name="fare" min="1" step="0.01" required></div>
<div><label>Total seats</label><input type="number" name="total_seats" min="1" max="100" required></div>
<div style="align-self:end"><button type="submit">Add bus</button></div></form></div>
<div class="card"><h2>Existing buses</h2><table><thead><tr><th>Bus</th><th>Route</th><th>Travel date</th><th>Fare</th><th>Availability</th><th>Status</th></tr></thead><tbody>
<?php foreach ($buses as $b): ?><tr><td><?= e($b['bus_name']) ?> (<?= e($b['bus_type']) ?>)</td><td><?= e($b['from_city']) ?> → <?= e($b['to_city']) ?></td><td><?= e($b['travel_date']) ?> <?= e(substr($b['departure_time'],0,5)) ?></td><td>₹<?= number_format((float)$b['fare'],2) ?></td><td><?= (int)$b['available_seats'] ?>/<?= (int)$b['total_seats'] ?></td><td><?= e($b['status']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

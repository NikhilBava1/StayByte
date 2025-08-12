<?php
header('Content-Type: application/json; charset=UTF-8');

// Read input (JSON or form-encoded)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) { $data = $_POST; }
$question = isset($data['q']) ? trim((string)$data['q']) : '';

// Basic guard
if ($question === '') {
  echo json_encode([ 'answer' => 'Please type your question about rooms, meals, or bookings.' ]);
  exit;
}

require_once __DIR__ . '/config/db.php'; // Provides $conn

// Helpers
function contains_any($haystack, $needles) {
  $haystack = mb_strtolower($haystack);
  foreach ($needles as $n) {
    if ($n !== '' && mb_strpos($haystack, mb_strtolower($n)) !== false) return true;
  }
  return false;
}

function safe_val($arr, $key, $fallback = null) {
  return isset($arr[$key]) ? $arr[$key] : $fallback;
}

function money_fmt($num) {
  if (!is_numeric($num)) return $num;
  return number_format((float)$num, 2);
}

// Preload a few aggregates from DB (best-effort; ignore errors gracefully)
$roomStats = [ 'num' => null, 'min' => null, 'max' => null, 'minGuests' => null, 'maxGuests' => null ];
$bedSizes = [];
$mealStats = [ 'num' => null, 'min' => null, 'max' => null, 'veg' => null, 'nonveg' => null ];
$topMeals = [];

// Rooms: count, price range, guest capacity range, bed sizes
if ($res = @mysqli_query($conn, "SELECT COUNT(*) AS n, MIN(price) AS pmin, MAX(price) AS pmax, MIN(guest_capacity) AS gmin, MAX(guest_capacity) AS gmax FROM rooms")) {
  $row = mysqli_fetch_assoc($res);
  $roomStats['num'] = safe_val($row, 'n');
  $roomStats['min'] = safe_val($row, 'pmin');
  $roomStats['max'] = safe_val($row, 'pmax');
  $roomStats['minGuests'] = safe_val($row, 'gmin');
  $roomStats['maxGuests'] = safe_val($row, 'gmax');
}
if ($res = @mysqli_query($conn, "SELECT DISTINCT bed_size FROM rooms WHERE bed_size IS NOT NULL AND bed_size <> '' LIMIT 6")) {
  while ($r = mysqli_fetch_assoc($res)) { $bedSizes[] = $r['bed_size']; }
}

// Meals: count, price range, veg/non-veg counts, top meals
if ($res = @mysqli_query($conn, "SELECT COUNT(*) AS n, MIN(price) AS pmin, MAX(price) AS pmax FROM meals")) {
  $row = mysqli_fetch_assoc($res);
  $mealStats['num'] = safe_val($row, 'n');
  $mealStats['min'] = safe_val($row, 'pmin');
  $mealStats['max'] = safe_val($row, 'pmax');
}
if ($res = @mysqli_query($conn, "SELECT meal_type, COUNT(*) AS c FROM meals GROUP BY meal_type")) {
  while ($r = mysqli_fetch_assoc($res)) {
    $type = strtolower($r['meal_type']);
    if ($type === 'veg') $mealStats['veg'] = $r['c'];
    if ($type === 'non-veg' || $type === 'non veg' || $type === 'nonveg') $mealStats['nonveg'] = $r['c'];
  }
}
if ($res = @mysqli_query($conn, "SELECT meal_title FROM meals ORDER BY rating DESC, price ASC LIMIT 3")) {
  while ($r = mysqli_fetch_assoc($res)) { $topMeals[] = $r['meal_title']; }
}

$t = mb_strtolower($question);
$answer = null;

// Intent: room price range
if ($answer === null && contains_any($t, ['room', 'rooms']) && contains_any($t, ['price','cost','rate','charge'])) {
  if ($roomStats['min'] !== null && $roomStats['max'] !== null) {
    $answer = 'Room prices range from $' . money_fmt($roomStats['min']) . ' to $' . money_fmt($roomStats['max']) . '. Browse the Rooms section and open a room to see full details.';
  } else {
    $answer = 'Room prices are listed on each room card. Please scroll to the Rooms section and click “Check Details”.';
  }
}

// Intent: room availability / capacity
if ($answer === null && contains_any($t, ['room','rooms']) && contains_any($t, ['available','availability','vacancy','free','capacity'])) {
  if ($roomStats['num'] !== null) {
    $cap = '';
    if ($roomStats['minGuests'] !== null && $roomStats['maxGuests'] !== null) {
      $cap = ' Guest capacity varies from ' . (int)$roomStats['minGuests'] . ' to ' . (int)$roomStats['maxGuests'] . '.';
    }
    $sizes = count($bedSizes) ? (' Common bed sizes: ' . implode(', ', array_slice($bedSizes, 0, 4)) . '.') : '';
    $answer = 'We currently list ' . (int)$roomStats['num'] . ' rooms.' . $cap . $sizes . ' Open a room to check its latest availability.';
  } else {
    $answer = 'Availability is shown on the room detail pages. Please open a room to see the latest status.';
  }
}

// Intent: bed size question
if ($answer === null && contains_any($t, ['bed','bed size'])) {
  if (count($bedSizes)) {
    $answer = 'Available bed sizes include: ' . implode(', ', array_slice($bedSizes, 0, 6)) . '. See each room page for exact details.';
  } else {
    $answer = 'Bed sizes are listed on each room card and room detail page.';
  }
}

// Intent: meals overview / price / veg
if ($answer === null && contains_any($t, ['meal','meals','food','menu','breakfast','lunch','dinner'])) {
  if ($mealStats['num'] !== null) {
    $range = ($mealStats['min'] !== null && $mealStats['max'] !== null)
      ? (' Prices range from $' . money_fmt($mealStats['min']) . ' to $' . money_fmt($mealStats['max']) . '.')
      : '';
    $kinds = [];
    if ($mealStats['veg']) $kinds[] = $mealStats['veg'] . ' Veg';
    if ($mealStats['nonveg']) $kinds[] = $mealStats['nonveg'] . ' Non‑Veg';
    $kindText = count($kinds) ? (' We offer ' . implode(' and ', $kinds) . ' options.') : '';
    $tops = count($topMeals) ? (' Popular choices: ' . implode(', ', $topMeals) . '.') : '';
    $answer = 'We currently offer ' . (int)$mealStats['num'] . ' meals.' . $range . $kindText . $tops;
  } else {
    $answer = 'We provide Veg and Non‑Veg meals. See the Meals section for today’s offerings and prices.';
  }
}

// Intent: booking
if ($answer === null && contains_any($t, ['book','booking','reserve','reservation'])) {
  $answer = 'To book a room, open its details from the homepage and proceed to booking. For assistance, visit contact.php.';
}

// Intent: check-in/out
if ($answer === null && contains_any($t, ['check in','check-in','checkin','check out','check-out','checkout'])) {
  $answer = 'Check‑in starts at 2:00 PM and check‑out is until 11:00 AM.';
}

// Intent: login/register
if ($answer === null && contains_any($t, ['login','sign in','signin'])) {
  $answer = 'You can login at login.php. If you do not have an account, please register first.';
}
if ($answer === null && contains_any($t, ['register','sign up','signup','create account'])) {
  $answer = 'Create your account at register.php, then login to manage your bookings.';
}

// Intent: contact/help
if ($answer === null && contains_any($t, ['contact','support','help'])) {
  $answer = 'Please reach us via contact.php. We are happy to help!';
}

// Default
if ($answer === null) {
  $answer = 'I can help with rooms, meals, bookings, check‑in/out, login/registration, and contact. Try asking: “What are room prices?” or “Do you have Veg meals?”.';
}

echo json_encode([ 'answer' => $answer ]);
?>


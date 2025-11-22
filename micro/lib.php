<?php
require_once __DIR__ . '/config.php';

function read_data() {
    if (!file_exists(DATA_FILE)) return [];
    $json = @file_get_contents(DATA_FILE);
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}

function write_data($arr) {
    if (!is_dir(DATA_DIR)) { @mkdir(DATA_DIR, 0755, true); }
    $tmp = DATA_FILE . '.tmp';
    $fp = @fopen($tmp, 'wb');
    if (!$fp) return false;
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, json_encode(array_values($arr), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
    // Atomic replace
    return @rename($tmp, DATA_FILE);
}

function parse_time_ms($str) {
    $s = trim((string)$str);
    if ($s === '') return null;
    $s = str_replace(',', '.', $s);

    // Digits-only fallback (length 5–7: MMssmmm)
    if (preg_match('/^\d{5,7}$/', $s)) {
        $ms  = intval(substr($s, -3));
        $sec = intval(substr($s, -5, 2));
        $min_raw = substr($s, 0, strlen($s) - 5);
        if ($min_raw === '') $min_raw = '0';
        $min = intval($min_raw);
        if ($sec < 0 || $sec > 59) return false;
        return ($min * 60 + $sec) * 1000 + $ms;
    }

    // Standard "MM:ss:mmm" or "MM:ss.mmm"
    if (!preg_match('/^(\d{1,2}):([0-5]?\d)[:\.](\d{1,3})$/', $s, $m)) {
        return false;
    }
    $min = intval($m[1]);
    $sec = intval($m[2]);
    $ms  = intval(str_pad(substr($m[3], 0, 3), 3, '0'));
    return ($min * 60 + $sec) * 1000 + $ms;
}

function format_time_colon($ms) {
    if ($ms === null || $ms === '') return '';
    $total = intval($ms);
    $min = intdiv($total, 60000);
    $rem = $total % 60000;
    $sec = intdiv($rem, 1000);
    $milli = $rem % 1000;
    return sprintf('%02d:%02d:%03d', $min, $sec, $milli);
}

function sanitize_text($s) {
    return htmlspecialchars(trim((string)$s), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function next_id($arr) {
    $max = 0;
    foreach ($arr as $r) {
        if (isset($r['id']) && is_numeric($r['id']) && $r['id'] > $max) $max = $r['id'];
    }
    return $max + 1;
}

/**
 * Check if user is logged in for micro admin
 * Now uses main CMS is_admin() function
 */
function is_logged_in() {
    return is_admin();
}

// Input constraints
function normalize_name($s, $max=64) {
    $s = trim((string)$s);
    $s = preg_replace('/\s+/', ' ', $s);
    $s = mb_substr($s, 0, $max);
    return $s;
}
function normalize_startno($s, $max=32) {
    $s = trim((string)$s);
    $s = mb_substr($s, 0, $max);
    // allow letters, digits, space, -, _ and /
    $s = preg_replace('/[^A-Za-z0-9\-\_\/ ]+/', '', $s);
    return $s;
}
?>

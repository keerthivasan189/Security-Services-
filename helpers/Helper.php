<?php
class Helper {
    public static function money(float $amount): string {
        return '₹' . number_format($amount, 2);
    }

    public static function moneyWords(float $amount): string {
        $amount = (int) round($amount);
        $ones = ['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE',
                 'TEN','ELEVEN','TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN',
                 'SEVENTEEN','EIGHTEEN','NINETEEN'];
        $tens = ['','','TWENTY','THIRTY','FORTY','FIFTY','SIXTY','SEVENTY','EIGHTY','NINETY'];

        if ($amount === 0) return 'ZERO RUPEES ONLY';

        $convert = function(int $n) use (&$convert, $ones, $tens): string {
            if ($n < 20)     return $ones[$n] . ' ';
            if ($n < 100)    return $tens[(int)($n/10)] . ' ' . ($n%10 ? $ones[$n%10].' ' : '');
            if ($n < 1000)   return $ones[(int)($n/100)] . ' HUNDRED ' . ($n%100 ? $convert($n%100) : '');
            if ($n < 100000) return $convert((int)($n/1000)) . 'THOUSAND ' . ($n%1000 ? $convert($n%1000) : '');
            if ($n < 10000000) return $convert((int)($n/100000)) . 'LAKH ' . ($n%100000 ? $convert($n%100000) : '');
            return $convert((int)($n/10000000)) . 'CRORE ' . ($n%10000000 ? $convert($n%10000000) : '');
        };

        return trim($convert($amount)) . ' RUPEES ONLY';
    }

    public static function sanitize(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function redirect(string $path): void {
        header('Location: ' . BASE_URL . '/index.php?url=' . ltrim($path, '/'));
        exit;
    }

    public static function uploadFile(string $inputName, string $folder): ?string {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $allowed = ['jpg','jpeg','png','pdf','webp'];
        $ext     = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) return null;

        $dir      = BASE_PATH . '/uploads/' . $folder . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $filename = uniqid('', true) . '.' . $ext;
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $dir . $filename);
        return $filename;
    }

    public static function daysInMonth(int $month, int $year): int {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    public static function monthName(string $ym): string {
        return date('F Y', strtotime($ym . '-01'));
    }

    public static function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function post(string $key, $default = ''): string {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    public static function get(string $key, $default = ''): string {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }
}

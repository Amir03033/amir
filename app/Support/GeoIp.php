<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/** Country lookup against a local copy of DB-IP Lite (CC BY 4.0). No visitor IP ever leaves the server. */
class GeoIp
{
    /** 16-byte address (IPv4 mapped into IPv6) as 32 hex chars, so string order equals numeric order. */
    public static function key(string $ip): ?string
    {
        $packed = @inet_pton($ip);

        if ($packed === false) {
            return null;
        }

        if (strlen($packed) === 4) {
            $packed = str_repeat("\0", 10)."\xff\xff".$packed;
        }

        return bin2hex($packed);
    }

    public static function country(?string $ip): ?string
    {
        $key = $ip ? static::key($ip) : null;

        if (! $key) {
            return null;
        }

        $row = DB::table('geoip_ranges')->where('ip_from', '<=', $key)->orderByDesc('ip_from')->first();

        return $row && $row->ip_to >= $key && $row->country !== 'ZZ' ? $row->country : null;
    }

    /** Drop the host part: IPv4 keeps /24, IPv6 keeps /48. Enough for abuse checks, not enough to identify a person. */
    public static function anonymize(?string $ip): ?string
    {
        $packed = $ip ? @inet_pton($ip) : false;

        if ($packed === false) {
            return null;
        }

        $keep = strlen($packed) === 4 ? 3 : 6;

        return inet_ntop(substr($packed, 0, $keep).str_repeat("\0", strlen($packed) - $keep));
    }
}

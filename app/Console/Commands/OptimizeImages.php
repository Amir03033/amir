<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/** Shrinks uploaded images that are larger than the site ever shows them. Run once after deploy. */
class OptimizeImages extends Command
{
    protected $signature = 'media:optimize {--max=1600 : Longest side in pixels} {--dry-run : Only report what would change}';

    protected $description = 'Resize oversized uploads in storage/app/public (projects, blogs, site) in place';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $max = (int) $this->option('max');
        $saved = 0;

        foreach (['projects', 'blogs', 'site'] as $dir) {
            foreach ($disk->files($dir) as $file) {
                $path = $disk->path($file);
                [$w, $h, $type] = @getimagesize($path) ?: [0, 0, 0];

                if (! in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true) || max($w, $h) <= $max) {
                    continue;
                }

                $scale = $max / max($w, $h);
                $nw = (int) round($w * $scale);
                $nh = (int) round($h * $scale);
                $before = filesize($path);

                if ($this->option('dry-run')) {
                    $this->line("would resize {$file}: {$w}x{$h} → {$nw}x{$nh}");
                    continue;
                }

                $src = match ($type) {
                    IMAGETYPE_JPEG => imagecreatefromjpeg($path),
                    IMAGETYPE_PNG => imagecreatefrompng($path),
                    IMAGETYPE_WEBP => imagecreatefromwebp($path),
                };
                $dst = imagecreatetruecolor($nw, $nh);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

                match ($type) {
                    IMAGETYPE_JPEG => imagejpeg($dst, $path, 82),
                    IMAGETYPE_PNG => imagepng($dst, $path, 8),
                    IMAGETYPE_WEBP => imagewebp($dst, $path, 82),
                };
                imagedestroy($src);
                imagedestroy($dst);

                clearstatcache(true, $path);
                $saved += $before - filesize($path);
                $this->line(sprintf('%-50s %dx%d → %dx%d  %s → %s', $file, $w, $h, $nw, $nh, $this->kb($before), $this->kb(filesize($path))));
            }
        }

        $this->info('Saved '.$this->kb($saved).'.');

        return self::SUCCESS;
    }

    private function kb(int $bytes): string
    {
        return round($bytes / 1024).' KB';
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Share image (1200×630) per project or blog post, drawn with GD in the site's style.
 * Generated once per record version and cached in storage/app/public/og.
 */
class OgImageController extends Controller
{
    private const W = 1200;

    private const H = 630;

    public function __invoke(Request $request, string $type, int $id)
    {
        $locale = $request->query('lang') === 'en' ? 'en' : 'nl';
        app()->setLocale($locale);

        $record = $type === 'project' ? Project::findOrFail($id) : Blog::published()->findOrFail($id);
        $file = "og/{$type}-{$id}-{$locale}-{$record->updated_at?->timestamp}.png";
        $disk = Storage::disk('public');

        if (! $disk->exists($file)) {
            $disk->makeDirectory('og');
            $disk->put($file, $this->render($record, $type));
        }

        return response()->file($disk->path($file), [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    private function render(Project|Blog $record, string $type): string
    {
        $img = imagecreatetruecolor(self::W, self::H);
        $c = fn (int $r, int $g, int $b, int $a = 0) => imagecolorallocatealpha($img, $r, $g, $b, $a);

        imagefill($img, 0, 0, $c(9, 9, 11));

        // Cover on the right, fading into the background.
        if ($type === 'project' && ($cover = $this->coverImage($record))) {
            $x = 560;
            $w = self::W - $x;
            $scale = max($w / imagesx($cover), self::H / imagesy($cover));
            imagecopyresampled($img, $cover, $x, 0, 0, 0, (int) ceil(imagesx($cover) * $scale), (int) ceil(imagesy($cover) * $scale), imagesx($cover), imagesy($cover));
            imagedestroy($cover);
            for ($i = 0; $i < 360; $i++) {
                imagefilledrectangle($img, $x + $i, 0, $x + $i, self::H, $c(9, 9, 11, (int) min(127, 127 * $i / 360)));
            }
        }

        // Dot grid.
        for ($x = 24; $x < self::W; $x += 32) {
            for ($y = 24; $y < self::H; $y += 32) {
                imagesetpixel($img, $x, $y, $c(60, 60, 68));
            }
        }

        $bold = base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf');
        $regular = base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf');
        $accent = $c(110, 156, 247);
        $text = $c(244, 244, 245);
        $muted = $c(150, 150, 160);
        $green = $c(74, 222, 128);

        // Brand pulse mark.
        imagesetthickness($img, 4);
        $pts = [[72, 92], [92, 92], [104, 62], [120, 122], [132, 92], [156, 92]];
        for ($i = 0; $i < count($pts) - 1; $i++) {
            imageline($img, $pts[$i][0], $pts[$i][1], $pts[$i + 1][0], $pts[$i + 1][1], $accent);
        }

        $title = $record->getTranslation('title');
        $kicker = $type === 'project' ? __('messages.og.case') : __('messages.og.note');
        $sub = $type === 'project' ? implode(' · ', array_slice($record->tagList(), 0, 4)) : $record->publishedDate()->format('Y-m-d');

        if (is_file($bold)) {
            imagettftext($img, 20, 0, 176, 102, $text, $bold, config('site.name'));
            imagettftext($img, 18, 0, 72, 210, $accent, $bold, mb_strtoupper($kicker));

            $y = 290;
            foreach ($this->wrap($title, $bold, 52, 520) as $line) {
                imagettftext($img, 52, 0, 72, $y, $text, $bold, $line);
                $y += 70;
            }

            imagettftext($img, 20, 0, 72, min($y + 20, 500), $muted, $regular, $sub);
            imagefilledellipse($img, 80, 560, 14, 14, $green);
            imagettftext($img, 18, 0, 100, 567, $muted, $regular, parse_url(config('app.url'), PHP_URL_HOST) ?: 'portfolio');
        } else {
            imagestring($img, 5, 72, 280, $title, $text);
        }

        ob_start();
        imagepng($img, null, 8);
        imagedestroy($img);

        return (string) ob_get_clean();
    }

    /** @return list<string> at most 3 lines */
    private function wrap(string $text, string $font, int $size, int $max): array
    {
        $lines = [''];
        foreach (preg_split('/\s+/', trim($text)) as $word) {
            $try = trim(end($lines).' '.$word);
            $box = imagettfbbox($size, 0, $font, $try);
            if ($box[2] - $box[0] > $max && end($lines) !== '') {
                $lines[] = $word;
            } else {
                $lines[count($lines) - 1] = $try;
            }
        }

        if (count($lines) > 3) {
            $lines = array_slice($lines, 0, 3);
            $lines[2] = rtrim($lines[2], '.,').'…';
        }

        return $lines;
    }

    private function coverImage(Project $project): ?\GdImage
    {
        $path = $project->image
            ? storage_path('app/public/'.$project->image)
            : public_path(ltrim((string) parse_url($project->coverUrl(), PHP_URL_PATH), '/'));

        if (! is_file($path)) {
            return null;
        }

        return match (@getimagesize($path)[2] ?? null) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path) ?: null,
            IMAGETYPE_PNG => @imagecreatefrompng($path) ?: null,
            IMAGETYPE_WEBP => @imagecreatefromwebp($path) ?: null,
            default => null,
        };
    }
}

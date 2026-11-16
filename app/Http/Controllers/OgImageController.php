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

        $record = $type === 'project' ? Project::published()->findOrFail($id) : Blog::published()->findOrFail($id);
        $file = "og/v4-{$type}-{$id}-{$locale}-{$record->updated_at?->timestamp}.png";
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

    public const PAGES = ['home', 'work', 'certificates', 'ask', 'roadmap', 'notes', 'cv', 'status', 'privacy', 'work-with-me'];

    /** Share images for the static pages, in the same style, cached per page/language/version. */
    public function page(Request $request, string $key)
    {
        $locale = $request->query('lang') === 'en' ? 'en' : 'nl';
        app()->setLocale($locale);

        $file = "og/v4-page-{$key}-{$locale}-".md5(config('seo.name').config("seo.description.{$locale}").__("seo.pages.{$key}.title")).'.png';
        $disk = Storage::disk('public');

        if (! $disk->exists($file)) {
            $disk->makeDirectory('og');
            [$title, $sub] = $key === 'home'
                ? [config('seo.name'), config("seo.description.{$locale}")]
                : [preg_replace('/\s+[–-]\s+(Amir Jebbari|amirs\.life)$/u', '', __("seo.pages.{$key}.title")), __("seo.pages.{$key}.description")];
            $disk->put($file, $this->card(__("seo.og.kicker.{$key}"), $title, $sub));
        }

        return response()->file($disk->path($file), ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=604800']);
    }

    private function render(Project|Blog $record, string $type): string
    {
        $kicker = $type === 'project' ? __('messages.og.case') : __('messages.og.note');
        $sub = $type === 'project'
            ? ($record->oneLiner() ?: implode(' · ', array_slice($record->tagList(), 0, 4)))
            : $record->publishedDate()->translatedFormat('j F Y');

        return $this->card($kicker, $record->getTranslation('title'), $sub, $type === 'project' ? $this->coverImage($record) : null);
    }

    private function card(string $kicker, string $title, string $sub, ?\GdImage $cover = null): string
    {
        $img = imagecreatetruecolor(self::W, self::H);
        imagealphablending($img, true);
        $c = fn (int $r, int $g, int $b, int $a = 0) => imagecolorallocatealpha($img, $r, $g, $b, $a);

        // amirs.life: warm paper, ink type, one burgundy detail. No gradients, no glow.
        $paper = $c(247, 245, 241);
        $ink = $c(22, 21, 18);
        $muted = $c(107, 101, 91);
        $line = $c(227, 222, 213);
        $accent = $c(127, 29, 63);
        imagefill($img, 0, 0, $paper);

        $semi = resource_path('fonts/InstrumentSans-600.ttf');
        $regular = resource_path('fonts/InstrumentSans-400.ttf');

        $textRight = 1128;
        if ($cover) {
            // Screenshot as a clean crop on the right; no fake browser chrome.
            [$x, $y, $w, $h] = [640, 72, 488, 486];
            $scale = max($w / imagesx($cover), $h / imagesy($cover));
            $sw = (int) ceil($w / $scale);
            $sh = (int) ceil($h / $scale);
            imagecopyresampled($img, $cover, $x, $y, 0, 0, $w, $h, $sw, $sh);
            imagedestroy($cover);
            imagerectangle($img, $x, $y, $x + $w - 1, $y + $h - 1, $line);
            $textRight = 584;
        }

        // Wordmark: the monogram (a geometric "a" in a burgundy rounded square, as in the nav) and the name in ink.
        $this->monogram($img, 72, 85, 30, $accent, $paper);
        imagettftext($img, 22, 0, 114, 109, $ink, $semi, 'amirs.life');

        imagettftext($img, 18, 0, 72, 236, $muted, $regular, $kicker);

        $y = 316;
        foreach ($this->wrap($title, $semi, 56, $textRight - 72) as $l) {
            imagettftext($img, 56, 0, 70, $y, $ink, $semi, $l);
            $y += 68;
        }

        foreach (array_slice($this->wrap($sub, $regular, 22, $textRight - 72), 0, 2) as $l) {
            imagettftext($img, 22, 0, 72, min($y + 18, 540), $muted, $regular, $l);
            $y += 34;
        }

        imagettftext($img, 16, 0, 72, 572, $muted, $regular, config('seo.name').' · '.config('seo.role.'.app()->getLocale()).' · amirs.life');

        ob_start();
        imagepng($img, null, 8);
        imagedestroy($img);

        return (string) ob_get_clean();
    }

    /** The nav mark in GD: rounded square (radius 5.5/20 of the size) with a ring and a stem, scaled from the 20×20 SVG. */
    private function monogram(\GdImage $img, int $x, int $y, int $size, int $fill, int $ink): void
    {
        $u = $size / 20;
        $r = (int) round(5.5 * $u);
        imagefilledrectangle($img, $x + $r, $y, $x + $size - $r, $y + $size, $fill);
        imagefilledrectangle($img, $x, $y + $r, $x + $size, $y + $size - $r, $fill);
        foreach ([[$x + $r, $y + $r], [$x + $size - $r, $y + $r], [$x + $r, $y + $size - $r], [$x + $size - $r, $y + $size - $r]] as [$cx, $cy]) {
            imagefilledellipse($img, $cx, $cy, 2 * $r, 2 * $r, $fill);
        }
        $cx = (int) round($x + 9.4 * $u);
        $cy = (int) round($y + 10.5 * $u);
        $outer = (int) round((3.3 + 1) * 2 * $u);
        $inner = (int) round((3.3 - 1) * 2 * $u);
        imagefilledellipse($img, $cx, $cy, $outer, $outer, $ink);
        imagefilledellipse($img, $cx, $cy, $inner, $inner, $fill);
        $sx = (int) round($x + 12.7 * $u);
        $half = (int) max(1, round($u));
        $top = (int) round($y + 6.9 * $u);
        $bottom = (int) round($y + 14.1 * $u);
        imagefilledrectangle($img, $sx - $half, $top, $sx + $half, $bottom, $ink);
        imagefilledellipse($img, $sx, $top, 2 * $half + 1, 2 * $half + 1, $ink);
        imagefilledellipse($img, $sx, $bottom, 2 * $half + 1, 2 * $half + 1, $ink);
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
        $url = $project->coverUrl();
        if (! $url) {
            return null;
        }
        $path = $project->image
            ? storage_path('app/public/'.$project->image)
            : public_path(ltrim((string) parse_url($url, PHP_URL_PATH), '/'));
        // Legacy ServerScope shots live in storage too.
        if (! is_file($path) && str_contains($url, '/storage/')) {
            $path = storage_path('app/public/'.substr((string) parse_url($url, PHP_URL_PATH), strlen('/storage/')));
        }

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

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
        $file = "og/v7-{$type}-{$id}-{$locale}-{$record->updated_at?->timestamp}.png";
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

        $file = "og/v7-page-{$key}-{$locale}-".md5(config('seo.name').config("seo.description.{$locale}").__("seo.pages.{$key}.title")).'.png';
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
        } else {
            // No screenshot: the logo large on the right, so a shared link is recognisable at a glance.
            $this->mark($img, 848, 175, 280);
            $textRight = 780;
        }

        // Wordmark: the heartbeat mark (as in the nav) and the name in ink.
        $this->mark($img, 72, 85, 30);
        imagettftext($img, 22, 0, 114, 109, $ink, $semi, 'amirs.life');

        imagettftext($img, 18, 0, 72, 236, $muted, $regular, $kicker);

        // Title: 56 px, or 44 px when it needs more than two lines; the subtitle only where it fits above the footer.
        $size = 56;
        $lines = $this->wrap($title, $semi, $size, $textRight - 72);
        if (count($lines) > 2) {
            $size = 44;
            $lines = $this->wrap($title, $semi, $size, $textRight - 72);
        }
        $y = 316;
        foreach ($lines as $l) {
            imagettftext($img, $size, 0, 70, $y, $ink, $semi, $l);
            $y += (int) round($size * 1.2);
        }

        $y += 18;
        $subLines = $this->wrap($sub, $regular, 22, $textRight - 72);
        $room = max(0, min(2, (int) floor((530 - $y) / 34) + 1));
        $shown = array_slice($subLines, 0, $room);
        if ($shown && count($subLines) > count($shown)) {
            $shown[count($shown) - 1] = rtrim($shown[count($shown) - 1], ' ,.;:–-').'…';
        }
        foreach ($shown as $l) {
            imagettftext($img, 22, 0, 72, $y, $muted, $regular, $l);
            $y += 34;
        }

        imagettftext($img, 16, 0, 72, 572, $muted, $regular, config('seo.name').' · '.config('seo.role.'.app()->getLocale()).' · amirs.life');

        ob_start();
        imagepng($img, null, 8);
        imagedestroy($img);

        return (string) ob_get_clean();
    }

    /**
     * The logo in GD: a heartbeat line in a burgundy rounded square, scaled from the 20×20 nav SVG.
     * Drawn at 4× on its own canvas and scaled down, so the edges are smooth.
     */
    private function mark(\GdImage $img, int $x, int $y, int $size): void
    {
        $k = 4;
        $big = $size * $k;
        $m = imagecreatetruecolor($big, $big);
        imagealphablending($m, false);
        imagesavealpha($m, true);
        imagefill($m, 0, 0, imagecolorallocatealpha($m, 0, 0, 0, 127));
        imagealphablending($m, true);
        $fill = imagecolorallocate($m, 127, 29, 63);
        $ink = imagecolorallocate($m, 247, 245, 241);

        $u = $big / 20;
        $r = (int) round(5.5 * $u);
        imagefilledrectangle($m, $r, 0, $big - $r - 1, $big - 1, $fill);
        imagefilledrectangle($m, 0, $r, $big - 1, $big - $r - 1, $fill);
        foreach ([[$r, $r], [$big - $r - 1, $r], [$r, $big - $r - 1], [$big - $r - 1, $big - $r - 1]] as [$cx, $cy]) {
            imagefilledellipse($m, $cx, $cy, 2 * $r, 2 * $r, $fill);
        }

        // M3.5 10.5 h3 l1.6-4 2.6 8 1.8-5.2 1 1.2 h3
        $points = [[3.5, 10.5], [6.5, 10.5], [8.1, 6.5], [10.7, 14.5], [12.5, 9.3], [13.5, 10.5], [16.5, 10.5]];
        // A round-capped stroke: stamp discs along each segment (smooth joins, no square ends).
        $w = max(2, (int) round(1.7 * $u));
        for ($i = 0; $i < count($points) - 1; $i++) {
            [$x1, $y1] = $points[$i];
            [$x2, $y2] = $points[$i + 1];
            $steps = (int) max(1, ceil(hypot(($x2 - $x1) * $u, ($y2 - $y1) * $u)));
            for ($t = 0; $t <= $steps; $t++) {
                $px = ($x1 + ($x2 - $x1) * $t / $steps) * $u;
                $py = ($y1 + ($y2 - $y1) * $t / $steps) * $u;
                imagefilledellipse($m, (int) round($px), (int) round($py), $w, $w, $ink);
            }
        }

        imagecopyresampled($img, $m, $x, $y, 0, 0, $size, $size, $big, $big);
        imagedestroy($m);
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

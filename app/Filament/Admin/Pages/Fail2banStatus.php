<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class Fail2banStatus extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Fail2ban';
    protected static ?string $navigationGroup = 'Beveiliging';
    protected static ?string $title = 'Fail2ban status';
    protected static string $view = 'filament.admin.pages.fail2ban-status';

    // Whitelist: alleen deze namen komen ooit in het shell-commando
    private const JAILS = ['sshd', 'plesk-panel', 'proftpd'];

    public array $jails = [];
    public ?string $updatedAt = null;

    public function mount(): void
    {
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $result = [];

        foreach (self::JAILS as $jail) {
            $result[$jail] = $this->readJail($jail);
        }

        $this->jails = $result;
        $this->updatedAt = now()->format('H:i:s');
    }

    private function readJail(string $jail): array
    {
        if (! in_array($jail, self::JAILS, true)) {
            return ['error' => 'Jail niet toegestaan'];
        }

        $output = [];
        $code = 0;
        exec('sudo -n /usr/bin/fail2ban-client status ' . escapeshellarg($jail) . ' 2>&1', $output, $code);
        $text = implode("\n", $output);

        if ($code !== 0) {
            return ['error' => trim($text) ?: "Exit code {$code}"];
        }

        $grab = fn (string $label) => preg_match('/' . $label . ':\s*(.*)$/mi', $text, $m) ? trim($m[1]) : null;

        $ips = $grab('Banned IP list');

        return [
            'current_failed' => (int) $grab('Currently failed'),
            'total_failed'   => (int) $grab('Total failed'),
            'current_banned' => (int) $grab('Currently banned'),
            'total_banned'   => (int) $grab('Total banned'),
            'banned_ips'     => $ips ? preg_split('/\s+/', $ips) : [],
        ];
    }
}
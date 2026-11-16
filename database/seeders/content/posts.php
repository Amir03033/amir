<?php

/*
|--------------------------------------------------------------------------
| Blog posts, seeded as DRAFTS (BlogDraftsSeeder).
|--------------------------------------------------------------------------
| Post 1 is based on the fail2ban jails this site reads in Filament (sshd, plesk-panel, proftpd);
| post 2 on app/Http/Middleware/TrackVisit.php and app/Support/GeoIp.php in this repo; post 3 on the
| ServerScope repo (README, agent/README, docs/AUDIT.md). Examples use documentation placeholders only
| (203.0.113.0/24, 198.51.100.0/24, server.example.com): no real IPs, usernames or paths.
| Everything not confirmed carries [VERIFY: …] and blocks publishing until removed.
*/

return [
    // ------------------------------------------------------------------------------------------
    'fail2ban-vps' => [
        'slug' => 'how-i-hardened-my-vps-with-fail2ban',
        'category' => 'Security',
        'tags' => ['fail2ban', 'Plesk', 'Ubuntu', 'Security', 'nmap'],
        'title' => [
            'nl' => 'Hoe ik mijn VPS beveiligde met fail2ban',
            'en' => 'How I hardened my VPS with fail2ban',
        ],
        'excerpt' => [
            'nl' => 'Een Ubuntu-server met Plesk krijgt de hele dag inlogpogingen. Zo zette ik fail2ban op voor SSH, FTP en het Plesk-paneel, testte ik een ban echt, en controleerde ik met nmap wat er openstaat.',
            'en' => 'An Ubuntu server with Plesk gets login attempts all day. This is how I set up fail2ban for SSH, FTP and the Plesk panel, tested a ban for real, and checked with nmap what is open.',
        ],
        'meta_title' => [
            'nl' => 'Mijn VPS beveiligen met fail2ban – Amir Jebbari',
            'en' => 'Hardening my VPS with fail2ban – Amir Jebbari',
        ],
        'meta_description' => [
            'nl' => 'fail2ban op Ubuntu met Plesk: jails voor sshd, proftpd en het Plesk-paneel, een eigen filter, een ban testen, je eigen IP uitzonderen en poorten checken.',
            'en' => 'fail2ban on Ubuntu with Plesk: jails for sshd, proftpd and the Plesk panel, a custom filter, testing a ban, whitelisting your own IP and checking ports.',
        ],
        'content' => [
            'nl' => <<<'MD'
Een paar minuten nadat een nieuwe server online staat, beginnen de inlogpogingen. Niet van mensen die het op jou gemunt hebben, maar van scripts die het hele internet afgaan met lijsten van gebruikersnamen en wachtwoorden. Op mijn VPS met Ubuntu en Plesk zag ik dat terug in de logbestanden van SSH, FTP en het Plesk-paneel. [VERIFY: hoe snel zag jij de eerste pogingen, en om hoeveel ging het per dag?]

Sterke wachtwoorden en SSH-sleutels zijn de basis. Maar ook een mislukte poging kost rekenkracht en vult je logs. fail2ban lost dat op: het leest logbestanden, telt mislukte pogingen per IP-adres en blokkeert een adres een tijdje als het te vaak misgaat.

## Wat ik wilde afschermen

Drie ingangen kregen de meeste pogingen:

- **SSH** (`sshd`), de ingang voor beheer op de commandoregel;
- **FTP** (`proftpd`), de FTP-server die Plesk gebruikt;
- **het Plesk-paneel** zelf, de webinterface voor de hele server.

Voor de eerste twee levert fail2ban kant-en-klare filters mee. Voor het Plesk-paneel schreef ik een eigen jail. [VERIFY: gebruik je de Fail2Ban-functie in Plesk (Extra en instellingen → IP-adressen blokkeren), of alleen de configuratie op de commandoregel?]

## De jails

Je past nooit de standaardbestanden aan; die worden bij een update overschreven. Eigen instellingen komen in `/etc/fail2ban/jail.local` of in een apart bestand onder `/etc/fail2ban/jail.d/`. Een vereenvoudigde versie van mijn opzet:

```ini
[DEFAULT]
# Eigen adressen nooit blokkeren (zie verderop)
ignoreip = 127.0.0.1/8 ::1 203.0.113.10
bantime  = 1h
findtime = 10m
maxretry = 5

[sshd]
enabled = true

[proftpd]
enabled = true

[plesk-panel]
enabled  = true
port     = 8443
filter   = plesk-panel
logpath  = /var/log/plesk/panel.log
maxretry = 5
```

De waarden voor `bantime`, `findtime` en `maxretry` zijn een voorbeeld. [VERIFY: welke waarden gebruik je zelf, en klopt het logpad van het Plesk-paneel op jouw server?] Vijf pogingen binnen tien minuten is streng genoeg voor scripts, en ruim genoeg voor iemand die een keer een typfout maakt.

## Een eigen filter voor het Plesk-paneel

Een filter is niets meer dan een reguliere expressie die een mislukte poging in het logbestand herkent. Het belangrijkste stuk is `<HOST>`: daar vult fail2ban het IP-adres in.

```ini
# /etc/fail2ban/filter.d/plesk-panel.conf
[Definition]
failregex = ^.*[Ll]ogin failed.* from <HOST>.*$
ignoreregex =
```

Deze regel is een vereenvoudiging. [VERIFY: zet hier de failregex die je echt gebruikt; de exacte tekst van een mislukte login verschilt per Plesk-versie.] Test een filter altijd tegen het echte logbestand voordat je de jail aanzet:

```sh
sudo fail2ban-regex /var/log/plesk/panel.log /etc/fail2ban/filter.d/plesk-panel.conf
```

`fail2ban-regex` laat zien hoeveel regels overeenkomen. Staat daar nul, terwijl je weet dat er mislukte pogingen in het log staan, dan klopt de expressie niet. Andersom: komt elke regel overeen, dan blokkeer je straks ook geslaagde logins.

## Je eigen IP-adres uitzonderen

Het eerste wat ik mezelf bijna aandeed: een paar keer een verkeerd wachtwoord, en mijn eigen adres stond op de lijst. Zet daarom je vaste IP-adressen in `ignoreip`. Werk je vanaf wisselende adressen, dan kun je je uitsluiten via een VPN met een vast adres. [VERIFY: hoe los jij dit op: vast IP thuis/kantoor, een VPN, of beide?]

Kom je toch zelf op de lijst, dan haal je je adres er zo weer af, via de console van je hostingpartij als SSH niet meer werkt:

```sh
sudo fail2ban-client set sshd unbanip 203.0.113.10
```

## Een ban echt testen

Een configuratie die laadt, is nog geen configuratie die werkt. Ik test een jail op twee manieren.

Eerst handmatig, met een adres uit een testbereik:

```sh
sudo fail2ban-client set sshd banip 198.51.100.23
sudo fail2ban-client status sshd
sudo fail2ban-client set sshd unbanip 198.51.100.23
```

`status` hoort het adres te tonen onder "Banned IP list". Daarna echt: vanaf een andere verbinding, bijvoorbeeld een telefoon op mobiel internet, een paar keer verkeerd inloggen. Na de ingestelde pogingen moet de verbinding weigeren. Vergeet niet het adres daarna weer vrij te geven.

Na een herstart controleer ik altijd of alle jails draaien:

```sh
sudo fail2ban-client status
```

Op deze site lees ik dezelfde drie jails uit in mijn beheeromgeving, zodat ik zie hoeveel adressen er geblokkeerd zijn zonder in te loggen op de server.

## Controleren wat er openstaat

fail2ban beschermt diensten die open moeten staan. Wat helemaal niet open hoeft te staan, kun je beter sluiten. Ik controleer dat van buitenaf, vanaf een andere machine, met nmap:

```sh
nmap -Pn -p- server.example.com
```

`-p-` scant alle 65.535 poorten, `-Pn` slaat de ping over, die veel servers niet beantwoorden. Wat ik verwacht: 22 (SSH), 80 en 443 (web), 21 (FTP, als je het gebruikt) en 8443 (Plesk). Zag ik iets anders, zoals een database-poort, dan ging die dicht in de firewall. [VERIFY: welke poorten stonden er bij jou open die er niet hoorden?] Scan alleen je eigen servers.

## Wat ik ervan leerde

- **Test filters tegen echte logs.** Een filter dat niets matcht, geeft een vals gevoel van veiligheid.
- **Zet jezelf eerst op de uitzonderingslijst.** Het scheelt een avond via de noodconsole.
- **fail2ban is een laag, geen oplossing.** SSH-sleutels in plaats van wachtwoorden, geen root-login via SSH, en updates bijhouden blijven net zo belangrijk.

Na een paar weken stonden er dagelijks [VERIFY: aantal] adressen op de lijst, en werden de logbestanden een stuk rustiger.
MD,
            'en' => <<<'MD'
A few minutes after a new server goes online, the login attempts start. Not from people who are after you, but from scripts that crawl the whole internet with lists of usernames and passwords. On my VPS with Ubuntu and Plesk I saw it in the logs of SSH, FTP and the Plesk panel. [VERIFY: how soon did you see the first attempts, and roughly how many per day?]

Strong passwords and SSH keys are the basis. But even a failed attempt costs resources and fills your logs. fail2ban solves that: it reads log files, counts failed attempts per IP address and blocks an address for a while when it fails too often.

## What I wanted to protect

Three entry points got most of the attempts:

- **SSH** (`sshd`), the way in for command-line administration;
- **FTP** (`proftpd`), the FTP server Plesk uses;
- **the Plesk panel** itself, the web interface for the whole server.

fail2ban ships with filters for the first two. For the Plesk panel I wrote my own jail. [VERIFY: do you use the Fail2Ban feature in Plesk (Tools & Settings → IP Address Banning), or only the command-line configuration?]

## The jails

Never edit the default files; updates overwrite them. Your own settings go in `/etc/fail2ban/jail.local` or a separate file under `/etc/fail2ban/jail.d/`. A simplified version of my setup:

```ini
[DEFAULT]
# Never block my own addresses (see below)
ignoreip = 127.0.0.1/8 ::1 203.0.113.10
bantime  = 1h
findtime = 10m
maxretry = 5

[sshd]
enabled = true

[proftpd]
enabled = true

[plesk-panel]
enabled  = true
port     = 8443
filter   = plesk-panel
logpath  = /var/log/plesk/panel.log
maxretry = 5
```

The values for `bantime`, `findtime` and `maxretry` are an example. [VERIFY: which values do you use, and is the Plesk panel log path right on your server?] Five attempts within ten minutes is strict enough for scripts, and loose enough for someone who makes a typo once.

## A custom filter for the Plesk panel

A filter is nothing more than a regular expression that recognises a failed attempt in the log. The key part is `<HOST>`: that's where fail2ban fills in the IP address.

```ini
# /etc/fail2ban/filter.d/plesk-panel.conf
[Definition]
failregex = ^.*[Ll]ogin failed.* from <HOST>.*$
ignoreregex =
```

This line is a simplification. [VERIFY: put the failregex you actually use here; the exact text of a failed login differs between Plesk versions.] Always test a filter against the real log before enabling the jail:

```sh
sudo fail2ban-regex /var/log/plesk/panel.log /etc/fail2ban/filter.d/plesk-panel.conf
```

`fail2ban-regex` shows how many lines match. If it says zero while you know there are failed attempts in the log, the expression is wrong. The other way round: if every line matches, you'll also block successful logins.

## Whitelisting your own IP

The first thing I nearly did to myself: a few wrong passwords, and my own address was on the list. So put your fixed IP addresses in `ignoreip`. If you work from changing addresses, a VPN with a fixed address helps. [VERIFY: how do you handle this: a fixed IP at home/office, a VPN, or both?]

If you do end up on the list, you can remove yourself, through your host's console if SSH no longer works:

```sh
sudo fail2ban-client set sshd unbanip 203.0.113.10
```

## Testing a ban for real

A configuration that loads isn't the same as a configuration that works. I test a jail in two ways.

First by hand, with an address from a test range:

```sh
sudo fail2ban-client set sshd banip 198.51.100.23
sudo fail2ban-client status sshd
sudo fail2ban-client set sshd unbanip 198.51.100.23
```

`status` should show the address under "Banned IP list". Then for real: from another connection, for example a phone on mobile data, log in wrongly a few times. After the configured number of attempts the connection should be refused. Don't forget to unban the address afterwards.

After a reboot I always check that all jails are running:

```sh
sudo fail2ban-client status
```

On this site I read the same three jails in my admin panel, so I can see how many addresses are banned without logging in to the server.

## Checking what is open

fail2ban protects services that have to be open. Whatever doesn't need to be open is better closed. I check that from the outside, from another machine, with nmap:

```sh
nmap -Pn -p- server.example.com
```

`-p-` scans all 65,535 ports, `-Pn` skips the ping that many servers don't answer. What I expect: 22 (SSH), 80 and 443 (web), 21 (FTP, if you use it) and 8443 (Plesk). If I saw something else, such as a database port, it got closed in the firewall. [VERIFY: which ports were open on your server that shouldn't have been?] Only scan your own servers.

## What I learned

- **Test filters against real logs.** A filter that matches nothing gives a false sense of security.
- **Put yourself on the ignore list first.** It saves an evening in the emergency console.
- **fail2ban is a layer, not a solution.** SSH keys instead of passwords, no root login over SSH, and keeping up with updates matter just as much.

After a few weeks there were [VERIFY: number] addresses on the list each day, and the logs became a lot quieter.
MD,
        ],
    ],

    // ------------------------------------------------------------------------------------------
    'bots-visitor-stats' => [
        'slug' => 'keeping-bots-out-of-my-visitor-stats-in-laravel',
        'category' => 'Laravel',
        'tags' => ['Laravel', 'Middleware', 'Privacy', 'Analytics', 'Bots'],
        'title' => [
            'nl' => 'Bots uit mijn bezoekersstatistieken houden in Laravel',
            'en' => 'Keeping bots out of my visitor stats in Laravel',
        ],
        'excerpt' => [
            'nl' => 'Mijn eigen bezoekersstatistieken telden scanners en bots als bezoekers. Zo filter ik ze nu in een Laravel-middleware, zonder trackers van derden en zonder volledige IP-adressen op te slaan.',
            'en' => 'My own visitor stats counted scanners and bots as visitors. This is how I filter them in a Laravel middleware now, without third-party trackers and without storing full IP addresses.',
        ],
        'meta_title' => [
            'nl' => 'Bots uit je bezoekersstatistieken in Laravel – Amir Jebbari',
            'en' => 'Keeping bots out of visitor stats in Laravel – Amir Jebbari',
        ],
        'meta_description' => [
            'nl' => 'Een Laravel-middleware die bezoeken telt zonder bots: crawler-detect, een eigen blocklist, verdachte paden zoals .env en gehashte IP-adressen.',
            'en' => 'A Laravel middleware that counts visits without bots: crawler-detect, a custom blocklist, suspicious paths like .env and wp-config.php, and hashed IPs.',
        ],
        'content' => [
            'nl' => <<<'MD'
Op amirs.life gebruik ik geen Google Analytics of andere tracker van derden. Ik wilde wel weten hoeveel mensen langskomen en welke pagina's ze lezen. Dus schreef ik een kleine middleware in Laravel die elk paginabezoek opslaat. Na een paar dagen klopte er iets niet: in drie dagen ongeveer 690 bezoeken, waarvan zo'n 210 van bots en scanners. [VERIFY: kloppen 690 bezoeken en ongeveer 210 bots/scanners in 3 dagen, en wanneer was dat?]

Dat is bijna een derde van alle "bezoekers". Zonder filter zegt zo'n grafiek dus vooral iets over het internet, niet over je site.

## De middleware

Een middleware draait bij elk verzoek. Dat maakt hem de natuurlijke plek om een bezoek te registreren. De opzet is simpel: eerst alles uitsluiten wat geen echt bezoek is, dan pas opslaan.

```php
public function handle(Request $request, Closure $next)
{
    $userAgent = (string) $request->userAgent();

    if ($this->isBot($userAgent) || $this->isScan($request->path())) {
        return $next($request); // wel doorlaten, niet tellen
    }

    if ($request->isMethod('GET') && ! $request->is('admin*', 'livewire*', 'storage/*', 'build/*')) {
        Visit::create([/* … */]);
    }

    return $next($request);
}
```

Het belangrijkste detail staat in het commentaar: bots worden **niet geblokkeerd**, alleen niet geteld. Zoekmachines moeten de site gewoon kunnen lezen.

Ik tel alleen GET-verzoeken naar echte pagina's. Formulieren, de beheeromgeving, Livewire-verzoeken en bestanden zoals CSS en afbeeldingen tellen niet mee.

## Laag 1: bekende crawlers

Voor de bekende bots gebruik ik het pakket `jaybizzle/crawler-detect`. Dat herkent honderden crawlers aan hun user-agent: Googlebot, Bingbot, de crawlers van AI-diensten, monitoringtools.

```php
use Jaybizzle\CrawlerDetect\CrawlerDetect;

if ((new CrawlerDetect)->isCrawler($userAgent)) {
    return $next($request);
}
```

Dat vangt de nette bots: die zeggen zelf wie ze zijn.

## Laag 2: een eigen blocklist

Scripts en scanners zeggen dat vaak niet, maar verraden zich toch aan hun user-agent. Wat in mijn logs opviel, kwam in een eigen lijst:

```php
protected array $blockedUserAgentPatterns = [
    'curl/',
    'Go-http-client',
    'python-requests',
    'Scrapy',
    'PostmanRuntime',
    'HeadlessChrome',
];
```

Een gewone browser stuurt geen `python-requests` mee. Wat zo'n user-agent heeft, is vrijwel altijd een script. Deze lijst groeit langzaam mee met wat ik in de logs zie.

## Laag 3: verdachte paden

De derde groep doet zich voor als browser, maar vraagt pagina's op die op mijn site niet bestaan: `/wp-login.php`, `/wp-config.php`, `/.env`, `/.git/config`. Dat zijn scanners die zoeken naar een slecht beveiligde WordPress-installatie of een uitgelekt configuratiebestand.

```php
protected array $suspiciousPathPatterns = [
    'wp-config', '.env', 'wp-login', 'wp-admin', '.git', 'phpinfo', 'config.php', '.php.bak',
];
```

Een verzoek naar een van die paden telt nooit als bezoek, ook als de user-agent er normaal uitziet. Mijn site is geen WordPress en serveert geen `.env`, dus die verzoeken krijgen gewoon een 404.

## Privacy: geen volledige IP-adressen bewaren

Om unieke bezoekers te tellen heb je iets nodig dat een bezoeker herkent. Het volledige IP-adres opslaan wilde ik niet. Ik sla twee dingen op:

- een **onomkeerbare hash** van het IP-adres, gecombineerd met de geheime sleutel van de applicatie, zodat je hem zonder die sleutel niet kunt terugrekenen of vergelijken met een andere site;
- het IP-adres **zonder het laatste deel** (bijvoorbeeld `203.0.113.0`), voor grove statistiek.

```php
'ip_hash' => hash('sha256', $request->ip() . config('app.key')),
```

## Het land bepalen

Ik wilde ook weten uit welk land bezoekers komen, zonder het IP-adres naar een externe dienst te sturen. Daarom staat er een kopie van de gratis DB-IP Lite-database op de server zelf, die elke maand wordt bijgewerkt. Het opzoeken gebeurt lokaal, binnen de middleware. [VERIFY: je noemde een queued job voor het land; in deze versie van de code gebeurt het direct met de lokale database. Welke versie draait er bij jou? Pas de tekst aan.]

## Opruimen

Losse bezoeken bewaar ik niet eeuwig. Elke nacht worden ze samengevat tot dagtotalen; daarna worden losse bezoeken na 90 dagen verwijderd. De dagtotalen blijven, maar die zijn niet naar een persoon te herleiden.

## Je eigen bezoeken niet meetellen

Een bron van ruis die ik eerst over het hoofd zag: ikzelf. Elke keer dat ik iets aanpaste en de pagina vernieuwde, telde dat als bezoek. De beheeromgeving tel ik daarom helemaal niet mee, en een verzoek naar `/amir` of naar Livewire slaat de middleware over. Voor het testen van de filters gebruik ik `curl` met een eigen user-agent:

```sh
curl -A "python-requests/2.31" https://amirs.life/
curl -A "Mozilla/5.0" https://amirs.life/wp-login.php
```

Beide verzoeken horen geen nieuw bezoek op te leveren. Het eerste valt op de blocklist, het tweede op de verdachte paden. Zo'n test kost een minuut en laat meteen zien of een nieuwe regel werkt. Ik heb er ook automatische tests voor, zodat een latere wijziging het filter niet stilletjes kapotmaakt.

## Wat het opleverde

Na de drie lagen telt de grafiek vooral mensen. [VERIFY: hoeveel procent van de bezoeken valt er nu af als bot of scanner, als je dat wilt noemen?] Een paar dingen die ik leerde:

- **Blokkeer niet, filter.** De doelen zijn verschillend: zoekmachines moeten erin, maar niet in je cijfers.
- **Kijk naar je logs.** De beste regels voor de blocklist kwamen niet uit een lijst online, maar uit wat er echt op mijn server binnenkwam.
- **Privacy kost weinig.** Een hash en een ingekort IP-adres zijn genoeg voor eerlijke statistiek.

Er blijft altijd een kleine groep bots die zich goed vermomt. Voor een persoonlijke site vind ik dat geen probleem: het gaat om de trend, niet om het laatste bezoek.
MD,
            'en' => <<<'MD'
On amirs.life I don't use Google Analytics or any other third-party tracker. I did want to know how many people visit and which pages they read. So I wrote a small Laravel middleware that stores every page visit. After a few days something was off: about 690 visits in three days, of which around 210 came from bots and scanners. [VERIFY: are 690 visits and about 210 bots/scanners in 3 days right, and when was that?]

That's almost a third of all "visitors". Without a filter, a chart like that mostly tells you something about the internet, not about your site.

## The middleware

A middleware runs on every request, which makes it the natural place to record a visit. The setup is simple: first rule out everything that isn't a real visit, only then store it.

```php
public function handle(Request $request, Closure $next)
{
    $userAgent = (string) $request->userAgent();

    if ($this->isBot($userAgent) || $this->isScan($request->path())) {
        return $next($request); // let it through, don't count it
    }

    if ($request->isMethod('GET') && ! $request->is('admin*', 'livewire*', 'storage/*', 'build/*')) {
        Visit::create([/* … */]);
    }

    return $next($request);
}
```

The most important detail is in the comment: bots are **not blocked**, just not counted. Search engines must still be able to read the site.

I only count GET requests to real pages. Forms, the admin panel, Livewire requests and files such as CSS and images don't count.

## Layer 1: known crawlers

For the well-known bots I use the `jaybizzle/crawler-detect` package. It recognises hundreds of crawlers by their user agent: Googlebot, Bingbot, the crawlers of AI services, monitoring tools.

```php
use Jaybizzle\CrawlerDetect\CrawlerDetect;

if ((new CrawlerDetect)->isCrawler($userAgent)) {
    return $next($request);
}
```

That catches the polite bots: they say who they are.

## Layer 2: my own blocklist

Scripts and scanners often don't say that, but still give themselves away through their user agent. What stood out in my logs went into a list of its own:

```php
protected array $blockedUserAgentPatterns = [
    'curl/',
    'Go-http-client',
    'python-requests',
    'Scrapy',
    'PostmanRuntime',
    'HeadlessChrome',
];
```

A normal browser doesn't send `python-requests`. Anything with a user agent like that is almost always a script. The list grows slowly with what I see in the logs.

## Layer 3: suspicious paths

The third group pretends to be a browser but asks for pages that don't exist on my site: `/wp-login.php`, `/wp-config.php`, `/.env`, `/.git/config`. Those are scanners looking for a badly secured WordPress install or a leaked configuration file.

```php
protected array $suspiciousPathPatterns = [
    'wp-config', '.env', 'wp-login', 'wp-admin', '.git', 'phpinfo', 'config.php', '.php.bak',
];
```

A request to one of those paths never counts as a visit, even if the user agent looks normal. My site isn't WordPress and doesn't serve `.env`, so those requests simply get a 404.

## Privacy: no full IP addresses stored

To count unique visitors you need something that recognises a visitor. I didn't want to store full IP addresses. I store two things:

- a **one-way hash** of the IP address, combined with the application's secret key, so without that key it can't be traced back or compared with another site;
- the IP address **without its last part** (for example `203.0.113.0`), for rough statistics.

```php
'ip_hash' => hash('sha256', $request->ip() . config('app.key')),
```

## Finding the country

I also wanted to know which country visitors come from, without sending the IP address to an outside service. So there's a copy of the free DB-IP Lite database on the server itself, updated every month. The lookup happens locally, inside the middleware. [VERIFY: you mentioned a queued job for the country; in this version of the code it happens directly against the local database. Which version runs on your server? Adjust the text.]

## Cleaning up

I don't keep individual visits forever. Every night they are summarised into daily totals; after that, individual visits are deleted after 90 days. The daily totals stay, but they can't be traced back to a person.

## Not counting your own visits

One source of noise I overlooked at first: me. Every time I changed something and refreshed the page, it counted as a visit. So the admin panel doesn't count at all, and the middleware skips requests to `/amir` and to Livewire. To test the filters I use `curl` with a custom user agent:

```sh
curl -A "python-requests/2.31" https://amirs.life/
curl -A "Mozilla/5.0" https://amirs.life/wp-login.php
```

Neither request should add a visit. The first hits the blocklist, the second the suspicious paths. A test like that takes a minute and shows right away whether a new rule works. There are automated tests for it as well, so a later change can't quietly break the filter.

## What it gave me

With the three layers in place, the chart mostly counts people. [VERIFY: what share of visits is now filtered out as bots or scanners, if you want to mention it?] A few things I learned:

- **Filter, don't block.** The goals differ: search engines should get in, but not into your numbers.
- **Look at your logs.** The best rules for the blocklist didn't come from a list online, but from what actually reached my server.
- **Privacy is cheap.** A hash and a shortened IP address are enough for honest statistics.

There will always be a small group of bots that disguise themselves well. For a personal site I don't mind: it's about the trend, not the last visit.
MD,
        ],
    ],

    // ------------------------------------------------------------------------------------------
    'serverscope-lessons' => [
        'slug' => 'what-building-serverscope-taught-me',
        'category' => 'ServerScope',
        'tags' => ['ServerScope', 'Monitoring', 'Laravel', 'Python', 'Lessons'],
        'title' => [
            'nl' => 'Wat het bouwen van ServerScope me leerde',
            'en' => 'What building ServerScope taught me',
        ],
        'excerpt' => [
            'nl' => 'ServerScope is mijn monitoringplatform voor servers, websites en domeinen. Hoe agents metingen naar het dashboard sturen, wat er lastig was, en wat ik de volgende keer anders zou doen.',
            'en' => 'ServerScope is my monitoring platform for servers, websites and domains. How agents report to the dashboard, what was hard, and what I would do differently next time.',
        ],
        'meta_title' => [
            'nl' => 'Wat het bouwen van ServerScope me leerde – Amir Jebbari',
            'en' => 'What building ServerScope taught me – Amir Jebbari',
        ],
        'meta_description' => [
            'nl' => 'Lessen uit het bouwen van ServerScope: een agent die alleen leest, veilig koppelen en updaten, incidenten in plaats van meldingen, en testen op MySQL.',
            'en' => 'Lessons from building ServerScope: an agent that only reads, safe enrollment and updates, incidents instead of alerts, and testing on MySQL.',
        ],
        'content' => [
            'nl' => <<<'MD'
ServerScope begon met een simpele vraag: hoe weet ik dat er iets mis is met een server, voordat een klant het merkt? [VERIFY: is dit hoe het begon, en wanneer was dat?] Inmiddels is het een platform voor servers, websites, domeinen en geplande taken, met een openbare demo op serverscope.nl en documentatie in twee talen. Dit zijn de dingen die ik onderweg leerde.

## Wat het doet

Er zijn twee kanten. Van buitenaf controleert ServerScope websites en domeinen: doet de site het, klopt het certificaat, verloopt het domein binnenkort. Van binnenuit meet een **agent** op de server zelf hoe het gaat: processor, geheugen, schijven, netwerk en diensten. Geplande taken, zoals een nachtelijke back-up, melden zich met een ping. Komt die ping niet, dan klopt er iets niet.

Gaat er iets mis, dan wordt dat een **incident** met een tijdlijn en een oorzaakanalyse. Meldingen gaan per e-mail, push of naar Slack, Teams, Telegram of Discord.

Daaromheen zitten de dingen die een team nodig heeft om er echt mee te werken: teams met rollen, tweestapsverificatie, openbare statuspagina's en badges die je op je eigen site kunt zetten, en een REST-API met webhooks, zodat andere systemen kunnen reageren als er iets gebeurt. Het dashboard kun je per persoon indelen, en ServerScope werkt ook als app op je telefoon, met pushmeldingen.

## Hoe een agent meldt

De agent is een klein Python-programma (er is ook een Bash-versie voor machines zonder Python). Elke 60 seconden verzamelt hij metingen en stuurt die via HTTPS naar het dashboard, met een eigen token per server. Het dashboard bewaart van dat token alleen een hash.

Belangrijke keuze: **de agent stuurt, het dashboard vraagt niets.** Er hoeft geen poort open op de server, en de agent voert geen opdrachten uit. Alleen uitgaand verkeer op poort 443. Dat maakt hem makkelijk te installeren achter een firewall, en weinig interessant voor een aanvaller.

Komt er een tijd geen meting binnen, dan merkt het dashboard dat zelf: de server is dan offline of de agent draait niet. Een stilte is ook een signaal.

## Het lastigste stuk: installeren en updaten

Ik dacht dat het meeste werk in grafieken zou zitten. Het zat in vertrouwen.

Een installatiescript dat als root draait, moet je kunnen vertrouwen. Daarom koppel je een server met een **eenmalige token** die een uur geldig is en maar één keer werkt. Het script vraagt erom, in plaats van dat hij in het commando staat. Zo komt hij niet in de shellgeschiedenis of in de lijst met processen terecht.

Updates van de agent zijn **ondertekend**. Het script stopt als er nog geen ondertekende release is, of als de server eerder een andere sleutel heeft gezien. Dat voelt streng, maar het alternatief is een update-kanaal waarmee je, als het misgaat, al je servers tegelijk kwijt bent.

## Incidenten in plaats van meldingen

De eerste versie stuurde een melding bij elke mislukte controle. Dat werd snel te veel: één storing gaf tien e-mails. [VERIFY: klopt dit voorbeeld met hoe het bij jou ging?] Nu opent een mislukte controle een incident, dat open blijft tot het probleem weg is en dan vanzelf sluit. Eén probleem, één incident, met alles wat er gebeurde op een tijdlijn. Tijdens gepland onderhoud worden meldingen onderdrukt, maar het herstel wordt wel bijgehouden.

## Wat ik anders zou doen

**Testen op MySQL vanaf dag één.** De tests draaiden lang op SQLite, omdat dat snel en makkelijk is. Het auditdocument noemt vier oudere tests die op MySQL 8 niet werkten. Die verschillen zie je lokaal niet, en in productie wel. In latere projecten, zoals BarberCuts, draaien de tests daarom op MySQL.

**Eerder nadenken over hoeveel data je bewaart.** Een meting per minuut per server lijkt weinig, tot je het vermenigvuldigt met servers en maanden. Het samenvatten van oude metingen kwam pas in een latere fase. [VERIFY: wil je hier een getal of ervaring noemen over de groei van de database?]

**Kleiner beginnen en eerder laten zien.** [VERIFY: is dit een les die je herkent? Bijvoorbeeld: eerder een eerste versie aan iemand laten gebruiken. Pas aan of schrap.]

## Wat het me opleverde

ServerScope groeide snel. Volgens het auditdocument ging het van 61 tests bij de eerste audit naar ruim 680 tests een paar fases later. Wat hielp: na elke fase opschrijven wat er af is, per onderdeel, met "compleet", "gedeeltelijk" of "ontbreekt". Zo wist ik bij elke volgende stap waar ik stond.

En ik leerde dat software draaien iets anders is dan software schrijven. Achtergrondtaken, meldingen, documentatie en een installatie die niemand hoeft uit te leggen: daar zit het meeste werk. Dezelfde lessen gebruik ik nu in werk voor anderen via Jebbari.
MD,
            'en' => <<<'MD'
ServerScope started with a simple question: how do I know something is wrong with a server before a customer notices? [VERIFY: is this how it started, and when was that?] By now it's a platform for servers, websites, domains and scheduled jobs, with a public demo at serverscope.nl and documentation in two languages. These are the things I learned along the way.

## What it does

There are two sides. From the outside, ServerScope checks websites and domains: is the site up, is the certificate right, is the domain about to expire. From the inside, an **agent** on the server itself measures how it's doing: CPU, memory, disks, network and services. Scheduled jobs, such as a nightly backup, check in with a ping. If the ping doesn't arrive, something is wrong.

When something fails, it becomes an **incident** with a timeline and a root cause analysis. Alerts go out by email, push, or to Slack, Teams, Telegram or Discord.

Around that sit the things a team needs to actually work with it: teams with roles, two-factor authentication, public status pages and badges you can put on your own site, and a REST API with webhooks, so other systems can react when something happens. Everyone can arrange their own dashboard, and ServerScope also works as an app on your phone, with push notifications.

## How an agent reports

The agent is a small Python program (there's also a Bash version for machines without Python). Every 60 seconds it collects metrics and sends them to the dashboard over HTTPS, with its own token per server. The dashboard only stores a hash of that token.

An important choice: **the agent sends, the dashboard asks nothing.** No port has to be open on the server, and the agent runs no commands. Only outbound traffic on port 443. That makes it easy to install behind a firewall, and of little interest to an attacker.

If no measurement arrives for a while, the dashboard notices by itself: the server is offline or the agent isn't running. Silence is a signal too.

## The hardest part: installing and updating

I thought most of the work would be in charts. It was in trust.

An install script that runs as root has to be trustworthy. That's why you connect a server with a **one-time token** that is valid for an hour and works once. The script asks for it, instead of it being part of the command, so it never ends up in the shell history or the process list.

Agent updates are **signed**. The script stops when there's no signed release yet, or when the server has seen a different key before. That feels strict, but the alternative is an update channel that, if it goes wrong, costs you all your servers at once.

## Incidents instead of alerts

The first version sent an alert for every failed check. That quickly became too much: one outage meant ten emails. [VERIFY: does this example match what happened for you?] Now a failed check opens an incident that stays open until the problem is gone and then closes by itself. One problem, one incident, with everything that happened on a timeline. During planned maintenance alerts are suppressed, but recovery is still tracked.

## What I would do differently

**Test on MySQL from day one.** For a long time the tests ran on SQLite, because it's quick and easy. The audit document lists four older tests that didn't work on MySQL 8. You don't see those differences locally, but you do in production. In later projects, such as BarberCuts, the tests run on MySQL.

**Think earlier about how much data you keep.** One measurement per minute per server sounds small, until you multiply it by servers and months. Summarising old measurements only came in a later phase. [VERIFY: do you want to mention a number or experience about the database growing?]

**Start smaller and show it sooner.** [VERIFY: is this a lesson you recognise? For example: letting someone use a first version earlier. Adjust or remove.]

## What it gave me

ServerScope grew fast. According to the audit document it went from 61 tests at the first audit to more than 680 tests a few phases later. What helped: after every phase, writing down what's finished, per feature, as "complete", "partial" or "missing". That way I knew where I stood at every next step.

And I learned that running software is different from writing it. Background jobs, alerts, documentation and an installation nobody has to explain: that's where most of the work is. I use the same lessons now in work for others through Jebbari.
MD,
        ],
    ],
];

<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Blog;
use App\Models\ContactMessage;
use App\Models\Idea;
use App\Models\Question;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $published = Blog::published()->count();
        $drafts = Blog::count() - $published;
        $unread = ContactMessage::whereNull('read_at')->count();

        return [
            Stat::make('Projecten', Project::count())
                ->description(Project::where('featured', true)->count().' uitgelicht')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->url(route('filament.Admin.resources.projects.index')),
            Stat::make('Blogs online', $published)
                ->description($drafts ? "{$drafts} concept/gepland (niet zichtbaar)" : 'Geen concepten')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($drafts ? 'warning' : 'success')
                ->url(route('filament.Admin.resources.blogs.index')),
            Stat::make('Ongelezen berichten', $unread)
                ->description(ContactMessage::count().' berichten totaal')
                ->descriptionIcon('heroicon-m-inbox')
                ->color($unread ? 'warning' : 'gray')
                ->url(route('filament.Admin.resources.contact-messages.index')),
            Stat::make('Open vragen', $open = Question::whereNull('answer')->count())
                ->description(Question::published()->count().' openbaar beantwoord')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($open ? 'warning' : 'gray')
                ->url(route('filament.Admin.resources.questions.index')),
            Stat::make('Nieuwe ideeën', $pending = Idea::where('is_public', false)->where('status', 'new')->count())
                ->description(Idea::published()->count().' op de roadmap')
                ->descriptionIcon('heroicon-m-light-bulb')
                ->color($pending ? 'warning' : 'gray')
                ->url(route('filament.Admin.resources.ideas.index')),
        ];
    }
}

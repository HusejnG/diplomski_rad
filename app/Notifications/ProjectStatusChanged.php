<?php

namespace App\Notifications;

use App\Models\ProjectStatusChange;
use App\Models\SolarProject;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email kupcu kada projektant pomjeri njegovu narudžbu u novi status.
 */
class ProjectStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public SolarProject $project,
        public ProjectStatusChange $change,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->project;

        $mail = (new MailMessage)
            ->subject(config('app.name').': '.$project->name.' - '.$this->change->to_status_label)
            ->greeting('Poštovani/a '.($project->contact_name ?: $notifiable->name).',')
            ->line($this->message());

        if ($this->change->note) {
            $mail->line('Napomena projektanta: '.$this->change->note);
        }

        return $mail->action('Pogledaj projekat', route('projects.show', $project));
    }

    private function message(): string
    {
        $project = $this->project;

        return match ($this->change->to_status) {
            SolarProject::STATUS_UNDER_REVIEW => 'Projektant je preuzeo vašu narudžbu "'.$project->name.'" i pregleda predloženi sistem.',
            SolarProject::STATUS_APPROVED => 'Vaš solarni sistem "'.$project->name.'" je odobren. Uskoro ćemo vas kontaktirati radi termina ugradnje.',
            SolarProject::STATUS_REJECTED => 'Nažalost, vaš zahtjev "'.$project->name.'" nije odobren.',
            SolarProject::STATUS_SCHEDULED => 'Ugradnja sistema "'.$project->name.'" je zakazana za '.$project->installation_scheduled_at?->format('d.m.Y').'.',
            SolarProject::STATUS_COMPLETED => 'Ugradnja sistema "'.$project->name.'" je završena. Hvala vam na povjerenju!',
            default => 'Status projekta "'.$project->name.'" je promijenjen u: '.$this->change->to_status_label.'.',
        };
    }
}

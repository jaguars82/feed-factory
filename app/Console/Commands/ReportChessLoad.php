<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\ChessUpload;

class ReportChessLoad extends Command
{
    protected $signature = 'chess:load-report';
    protected $description = 'Send a report with information about the last chess update session';
    protected $recipients = [
        'info@teacher-site.ru',
        //'project_manager@grvrn.ru',
        'jaguars82@yandex.ru',
        //'forteachersite@gmail.com',
    ];

    public function handle()
    {
        $this->info('Executing the report...');
        
        // Get the last update session
        $chessUpload = ChessUpload::orderBy('update_session_at', 'desc')->first();
        
        if (!$chessUpload) {
            $this->error('No chess upload data found.');
            return Command::FAILURE;
        }

        // Counting data
        $updatedDevelopers = $chessUpload->updated_developers;

        // Group updated chesses by provider_id
        $groupedUpdatedChesses = [];
        foreach ($chessUpload->updated_chess_info as $chess) {
            $providerId = $chess['provider_id'] ?? null;
            if ($providerId !== null) {
                $groupedUpdatedChesses[$providerId][] = $chess;
            }
        }

        // Group not updated chesses by provider_id
        $groupedNotUpdatedChesses = [];
        foreach ($chessUpload->not_updated_chess_info as $chess) {
            $providerId = $chess['provider_id'] ?? null;
            if ($providerId !== null) {
                $groupedNotUpdatedChesses[$providerId][] = $chess;
            }
        }

        $updatedCounts = [];
        $notUpdatedCounts = [];
        
        foreach ($updatedDevelopers as $developer) {
            $developerId = $developer['id'];
        
            $updatedCounts[$developerId] = count($groupedUpdatedChesses[$developerId] ?? []);
            $notUpdatedCounts[$developerId] = count($groupedNotUpdatedChesses[$developerId] ?? []);
        }

        $attachments = $chessUpload->all_attachments_by_developer;
        $updatedAttachments = $chessUpload->updated_attachments_by_developer;
        $notUpdatedAttachments = $chessUpload->not_updated_attachments_by_developer;
        $newAttachments = $chessUpload->new_attachments_by_developer;
        $removedAttachments = $chessUpload->removed_attachments_by_developer;

        // Form the body of the message
        $sessionId = $chessUpload->id;
        $sessionDate = Carbon::parse($chessUpload->update_session_at)->format('d.m.Y г., H:i');

        $messageBody = "<h2>Сводная информация об обновлении шахматок #{$sessionId} от {$sessionDate}</h2>";

        $messageBody .= "<h3>Застройщики, шахматки которых обновлялись</h3>";
        $messageBody .= "<table border='1' cellpadding='5'><tr><th>Застройщик</th><th>Обновленные шахматки</th><th>Необновленные шахматки</th></tr>";
        foreach ($updatedDevelopers as $developer) {
            $messageBody .= "<tr><td>{$developer['name']}</td><td>{$updatedCounts[$developer['id']]}</td><td>{$notUpdatedCounts[$developer['id']]}</td></tr>";
        }
        $messageBody .= "</table><br />";
        
        $messageBody .= "<h3>Вновь поступившие файлы</h3>";
        $messageBody .= "<table border='1' cellpadding='5'><tr><th>Email-папка</th><th>Файлов</th><th>Обновлено</th><th>Не задействовано</th><th>Дельта</th></tr>";
        foreach ($attachments as $folder => $files) {
            $updated = count($updatedAttachments[$folder] ?? []);
            $notUpdated = count($notUpdatedAttachments[$folder] ?? []);
            $new = count($newAttachments[$folder] ?? []);
            $removed = count($removedAttachments[$folder] ?? []);
            $messageBody .= "<tr><td>{$folder}</td><td>" . count($files) . "</td><td>{$updated}</td><td>{$notUpdated}</td><td>{$new} / {$removed}</td></tr>";
        }
        $messageBody .= "</table><br />";
        
        $messageBody .= "<h3>Застройщики, не задействованные в текущем сеансе обновления</h3>";
        $messageBody .= "<table border='1' cellpadding='5'><tr><th>Застройщик</th><th>Активные шахматки</th></tr>";
        $updatedDevelopersNames = array_map(function($item) {
            return $item['name'];
        }, $chessUpload->updated_developers);
        foreach ($chessUpload->not_updated_developers as $developer) {
            $developerName = $developer['name'] ?? 'Unknown';
            if (!in_array($developerName, $updatedDevelopersNames)) {
                $count = count($groupedNotUpdatedChesses[$developer['id']] ?? []);
                $messageBody .= "<tr><td>{$developerName}</td><td>{$count}</td></tr>";
            }
        }
        $messageBody .= "</table><br />";

        /*$adminPanelUrl = config('admin_panel_url');

        $messageBody .= "<p>С более подробной информацией о данном сеансе обновления, включая списки файлов-вложений и шахматок, можно ознакомиться по ссылке: <a terget=\"_blank\" href=\"{$adminPanelUrl}/stats/chess-upload/{$sessionId}/view\">{$adminPanelUrl}/stats/chess-upload/{$sessionId}/view</a></p>";

        $messageBody .= "<p>Список всех сеансов обновления доступен по ссылке: <a terget=\"_blank\" href=\"{$adminPanelUrl}/stats/chess-upload\">{$adminPanelUrl}/stats/chess-upload</a></p>";
        */

        $messageBody .= "<p>С более подробной информацией о данном сеансе обновления, включая списки файлов-вложений и шахматок, можно ознакомиться по ссылке: <a terget=\"_blank\" href=\"http://houston.grch.ru/stats/chess-upload/{$sessionId}/view\">http://houston.grch.ru/stats/chess-upload/{$sessionId}/view</a></p>";

        $messageBody .= "<p>Список всех сеансов обновления доступен по ссылке: <a terget=\"_blank\" href=\"http://houston.grch.ru/stats/chess-upload\">http://houston.grch.ru/stats/chess-upload</a></p>";
        
        // Send the message
        Mail::send([], [], function ($message) use ($messageBody, $sessionId, $sessionDate) {
            $message->to($this->recipients)
                    ->subject("Обновление шахматок (Сессия #{$sessionId} от {$sessionDate})")
                    ->html($messageBody);
        });
        
        return Command::SUCCESS;
    }
}

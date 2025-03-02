<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use App\Models\Chess;
use App\Models\Provider;
use App\Models\ChessUpload;
use Accent;
use CityCenter1C;
use DSK;
use GCh;
use EuroStroy;
use EuroStroy2;
use VDK;
use Vybor;
use Krays;
use Legos;
use Razvitie;
use SU35;

class LoadChessfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chess:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load chess files from email\'s attachments';

    /**
     * List of the mailbox folders to pass by
     */
    protected $ignoreMailboxFolders = [
        'drafts',
        'sent',
        'outbox',
        'spam',
        'trash',
    ];

    /**
     * Sets of symbols to ignore a chess-file that contains some of them in its name
     */
    /* protected $ignoreIfContains = [
        'КЛАДОВКИ', 'Кладовки', 'кладовки',
        'ПАРКОВКА', 'Парковка', 'парковка',
        'ПАРКОВКИ', 'Парковки', 'парковки',
        'ПАРКИНГ', 'Паркинг', 'паркинг',
        'ГАРАЖ-СТОЯНКА', 'Гараж-стоянка', 'гараж-стоянка',
        'ЗЕМЕЛЬНЫЙ УЧАСТОК', 'Земельный участок', 'земельный участок',
        'АВТОСТОЯНКА', 'Автостоянка', 'автостоянка',
        '(вторая очередь)'
    ]; */

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $successfullyUpdated = [];

        $uploadedAttachments = [];
        $extraAttachments = [];

        $activeChesses = Chess::where('is_active', 1)->get();
        $chessData = [];
        foreach ($activeChesses as $chess) {
            if (!empty($chess->scheme)) {
                $scheme = $this->chessScheme($chess->scheme);
                $currName = !empty($chess->attachment_filename) 
                    ? $scheme->filterChessFilename(pathinfo($chess->attachment_filename, PATHINFO_FILENAME)) 
                    : '';
                $currPath = $chess->file_chess_path ?? '';
                $chessData[] = [
                    'name' => $currName,
                    'path' => $currPath,
                    'scheme' => $scheme,
                    'info' => [
                        'id' => $chess->id,
                        'provider_id' => $chess->provider_id,
                        'complex_feed_name' => $chess->complex_feed_name,
                        'building_feed_name' => $chess->building_feed_name,
                        'name' => $chess->name,
                        'scheme' => $chess->scheme,
                        'file_chess_path' => $chess->file_chess_path,
                        'attachment_filename' => $chess->attachment_filename,
                        'filtered_filename' => $currName,
                        'created_at' => $chess->created_at,
                        'updated_at' => $chess->updated_at,
                    ]
                ];
            }
        }

        $cm = new ClientManager([]);
        $client = $cm->make([
            'host' => env('LOADER_MAIL_HOST', 'imap.yandex.ru'),
            'port' => env('LOADER_MAIL_PORT', 993),
            'encryption' => 'ssl',
            'validate_cert' => true,
            'username' => env('LOADER_MAIL_USERNAME', 'loader@grvrn.ru'),
            'password' => env('LOADER_MAIL_PASSWORD'),
            'protocol' => 'imap'
        ]);
        $client->connect();
        $folders = $client->getFolders();

        foreach ($folders as $folder) {
            if (in_array(mb_strtolower($folder->name), $this->ignoreMailboxFolders, true)) continue;
            $messages = $folder
                ->messages()
                //->since(strtotime("-1 day"))
                ->since(strtotime("-2 weeks"))
                ->unseen()
                ->get();
            // Sort massages by date (descending)
            $sortedMessages = $messages->sortByDesc(fn($message) => strtotime($message->getDate()->toString()));

            foreach ($sortedMessages as $message) {
                $attachments = $message->getAttachments();
                if (count($attachments) < 1) continue;
                $senderEmail = $message->getFrom()->first()->mail;

                foreach ($attachments as $attachment) {
                    /*if($folder->name == "ДСК") {
                        $this->info($attachment->name);
                    }*/
                    foreach ($chessData as $index => $chess) {
                        $loadAttachmentForCurrChess = false;
                        /* foreach($this->ignoreIfContains as $ignoreMarker) {
                            if (strpos($attachment->name, $ignoreMarker)) continue 2;
                        } */

                        if ($chess['name'] === $chess['scheme']->filterChessFilename(pathinfo($attachment->name, PATHINFO_FILENAME))) {
                            $pathParts = explode('/', $chess['path']);
                            $attachment->save(storage_path('app/' . $pathParts[0] . '/'), $pathParts[1]);
                            $loadAttachmentForCurrChess = true;
                            $chess['info']['attachment_actual_filename'] = $attachment->name;
                            $successfullyUpdated[] = $chess['info'];
                            unset($chessData[$index]);
                        }
                        if ($loadAttachmentForCurrChess) {
                            $this->addAttachmentToList($uploadedAttachments, $folder->name, $senderEmail, $attachment->name);
                        } else {
                            $this->addAttachmentToList($extraAttachments, $folder->name, $senderEmail, $attachment->name);
                        }
                    }
                }
                $message->setFlag('Seen');
            }
        }

        
        // remove values from the unused attachments structured list if they are stored in the uploaded attachments list
        foreach ($uploadedAttachments as $developer => $attachmentsBySender) {
            if (!isset($extraAttachments[$developer])) continue;
            foreach ($attachmentsBySender as $email => $attachmentNames) {
                if (!isset($extraAttachments[$developer][$email])) continue;
                foreach ($attachmentNames as $attachmentName) {
                    if (($key = array_search($attachmentName, $extraAttachments[$developer][$email], true)) !== false) {
                        unset($extraAttachments[$developer][$email][$key]);
                    }
                }
            }
        } 

        // Merge the uploaded & extra attachments to a new array to get an array of all the attachments
        $allAttachments = $this->mergeAttachmentArrays($extraAttachments, $uploadedAttachments);

        // Prepare data for db and save
        $allAttachmentsByDeveloper = array_map(fn($emails) => $emails ? array_merge(...array_values($emails)) : [], $allAttachments);
        $updatedAttachmentsByDeveloper = array_map(fn($emails) => $emails ? array_merge(...array_values($emails)) : [], $uploadedAttachments);
        $notUpdatedAttachmentsByDeveloper = array_map(fn($emails) => $emails ? array_merge(...array_values($emails)) : [], $extraAttachments);

        $updatedProviderIds = array_unique(array_column($successfullyUpdated, 'provider_id'));
        $notUpdatedProviderIds = array_unique(array_column(array_column($chessData, 'info'), 'provider_id'));

        $updatedDevelopers = Provider::whereIn('id', $updatedProviderIds)->get(['id', 'name'])->toArray();
        $notUpdatedDevelopers = Provider::whereIn('id', $notUpdatedProviderIds)->get(['id', 'name'])->toArray();

        // Calculate new attachments and deleted ones relative to the last update session of a developer
        $previousUpdateSessionsByDeveloper = [];
        $newAttachmentsByDeveloper = [];
        $removedAttachmentsByDeveloper = [];
        foreach ($allAttachmentsByDeveloper as $developer => $attachments) {
            $previousUpdateOfDeveloper = ChessUpload::whereRaw("JSON_CONTAINS_PATH(all_attachments_by_developer, 'one', ?)", ['$."' . $developer . '"'])
            ->latest('created_at')
            ->first();

            if ($previousUpdateOfDeveloper) {
                $previousUpdateSessionsByDeveloper[$developer] = $previousUpdateOfDeveloper->id;

                $newAttachmentsByDeveloper[$developer] = array_diff($notUpdatedAttachmentsByDeveloper[$developer], $previousUpdateOfDeveloper->all_attachments_by_developer[$developer]);

                $removedAttachmentsByDeveloper[$developer] = array_diff($previousUpdateOfDeveloper->all_attachments_by_developer[$developer], $attachments);
            }
        }

        ChessUpload::create([
            'chess_idies_list' => $activeChesses->pluck('id')->toArray(),
            'updated_chess_idies' => array_column($successfullyUpdated, 'id'),
            'not_updated_chess_idies' => array_column(array_column($chessData, 'info'), 'id'),
            'updated_chess_info' => $successfullyUpdated,
            'not_updated_chess_info' => array_column($chessData, 'info'),
            'all_attachments_by_developer' => $allAttachmentsByDeveloper,
            'all_attachments_by_developer_and_email' => $allAttachments,
            'updated_attachments_by_developer' => $updatedAttachmentsByDeveloper,
            'updated_attachments_by_developer_and_email' => $uploadedAttachments,
            'not_updated_attachments_by_developer' => $notUpdatedAttachmentsByDeveloper,
            'not_updated_attachments_by_developer_and_email' => $extraAttachments,
            'new_attachments_by_developer' => $newAttachmentsByDeveloper,
            'removed_attachments_by_developer' => $removedAttachmentsByDeveloper,
            'previous_sessions_by_developer' => $previousUpdateSessionsByDeveloper,    
            'updated_developers' => $updatedDevelopers,
            'not_updated_developers' => $notUpdatedDevelopers,
            'update_session_at' => now(),
        ]);

        // Report the result of the update-session
        if (count($allAttachmentsByDeveloper) > 0) {
            $this->call('chess:load-report');
            $this->call('feeds:update');
        }
        
        return Command::SUCCESS;
    }

    private function chessScheme($scheme)
    {
        return new $scheme();
    }

    /**
     * Place an attachement to the structured list
     * The list is grouped by a mailbox folder name and then by sender's email
     */
    private function addAttachmentToList(&$attachmentsArray, $folderName, $senderEmail, $attachmentName)
    {
        if (!isset($attachmentsArray[$folderName])) {
            $attachmentsArray[$folderName] = [];
        }
        if (!isset($attachmentsArray[$folderName][$senderEmail])) {
            $attachmentsArray[$folderName][$senderEmail] = [];
        }
        if (!in_array($attachmentName, $attachmentsArray[$folderName][$senderEmail], true)) {
            $attachmentsArray[$folderName][$senderEmail][] = $attachmentName;
        }
    }

    /** Merge structured by folder and email arrays of attachments */
    private function mergeAttachmentArrays(array $array1, array $array2): array
    {
        foreach ($array2 as $folder => $senders) {
            foreach ($senders as $sender => $attachements) {
                // If the folder exists, check the sender
                if (isset($array1[$folder][$sender])) {
                    // add attachment to existing array
                    $array1[$folder][$sender] = array_merge($array1[$folder][$sender], $attachements);
                } else {
                    // Add the sender and his attachments
                    $array1[$folder][$sender] = $attachements;
                }
            }
        }
        return $array1;
    }
    
}
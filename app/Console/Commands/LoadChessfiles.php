<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use App\Models\Chess;

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $activeChesses = Chess::all()->where('is_active', 1);
        $chessNames = array();
        foreach ($activeChesses as $chess) {
            $chessNames[$chess->attachment_filename] = $chess->file_chess_path;
        }

        $cm = new ClientManager($options = []);
        $client = $cm->make([
            'host'          => 'imap.yandex.ru',
            'port'          => 993,
            'encryption'    => 'ssl',
            'validate_cert' => true,
            'username'      => 'loader@grvrn.ru',
            'password'      => 'vpjwtkknhavbkjma',
            'protocol'      => 'imap'
        ]);

        // Connect to the IMAP Server
        $client->connect();

        // Get all Mailboxes
        /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
        $folders = $client->getFolders();

        // Loop through every Mailbox
        /** @var \Webklex\PHPIMAP\Folder $folder */
        foreach($folders as $folder){

            // Get all Messages of the current Mailbox $folder
            /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
            $messages = $folder->messages()->since(strtotime("-1 week"))->unseen()->get();

            /** @var \Webklex\PHPIMAP\Message $message */
            foreach($messages as $message){
                $attachments = $message->getAttachments();
                foreach($attachments as $attachment) {
                    if (array_key_exists($attachment->name, $chessNames)) {
                        $pathParts = explode('/', $chessNames[$attachment->name]);
                        $attachment->save($path = storage_path('app/'.$pathParts[0].'/'), $filename = $pathParts[1]);
                    }
                }
                // Mark message seen
                // $message->setFlag('Seen');
            }
        }

        return Command::SUCCESS;
    }

}

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
        $chessPaths = array();
        foreach ($activeChesses as $chess) {
            $currName = !empty($chess->attachment_filename) ? $chess->attachment_filename : '';
            $currPath = !empty($chess->file_chess_path) ? $chess->file_chess_path : '';
            array_push($chessNames, $currName);
            array_push($chessPaths, $currPath);
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
                    if ($ind = array_search($attachment->name, $chessNames)) {
                        $pathParts = explode('/', $chessPaths[$ind]);
                        $attachment->save($path = storage_path('app/'.$pathParts[0].'/'), $filename = $pathParts[1]);
                        unset($chessNames[$ind]);
                        unset($chessPaths[$ind]);
                    }
                }
                // Mark message seen
                $message->setFlag('Seen');
            }
        }

        return Command::SUCCESS;
    }

}

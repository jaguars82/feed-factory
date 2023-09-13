<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use App\Models\Chess;
use Accent;
use CityCenter1C;
use EuroStroy;
use VDK;
use Krays;
use Razvitie;

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
            $scheme = $this->chessScheme($chess->scheme);
            //$currName = !empty($chess->attachment_filename) ? $chess->attachment_filename : '';
            $currName = !empty($chess->attachment_filename) ? $scheme->filterChessFilename(substr($chess->attachment_filename, 0, strrpos($chess->attachment_filename, '.'))) : ''; // chess (attachment) filename without the extention and dynamic (changing) parts of the name (date etc.)
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
            'password'      => 'arssxrquffmfsfyx',
            'protocol'      => 'imap'
        ]);

        // Connect to the IMAP Server
        $client->connect();

        // Get all Mailboxes
        /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
        $folders = $client->getFolders();

        //var_dump($chessNames); die;

        // Loop through every Mailbox
        /** @var \Webklex\PHPIMAP\Folder $folder */
        foreach($folders as $folder){

            // Get all Messages of the current Mailbox $folder
            /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
            $messages = $folder->messages()
                ->since(strtotime("-1 week"))
                //->unseen()
                ->get();

            /** @var \Webklex\PHPIMAP\Message $message */
            foreach($messages as $message){
                $attachments = $message->getAttachments();
                foreach($attachments as $attachment) {
                    //echo $attachment->name; echo PHP_EOL;
                    /*if ($ind = array_search($attachment->name, $chessNames)) {
                        $pathParts = explode('/', $chessPaths[$ind]);
                        $attachment->save($path = storage_path('app/'.$pathParts[0].'/'), $filename = $pathParts[1]);
                        unset($chessNames[$ind]);
                        unset($chessPaths[$ind]);
                    }*/
                    foreach($chessNames as $index => $chessName) {
                        if(str_starts_with($attachment->name, $chessName)) {
                            $pathParts = explode('/', $chessPaths[$index]);
                            $attachment->save($path = storage_path('app/'.$pathParts[0].'/'), $filename = $pathParts[1]);
                            unset($chessNames[$index]);
                            unset($chessPaths[$index]);  
                        }
                    }
                }
                // Mark message seen
                $message->setFlag('Seen');
            }
        }

        return Command::SUCCESS;
    }

    /**
     * class with chess sheme params (offsets, filters)
     */
    private function chessScheme($scheme)
    {
        $createScheme = new $scheme();
        return $createScheme;
    }

}

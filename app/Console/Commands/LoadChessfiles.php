<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use App\Models\Chess;
use Accent;
use CityCenter1C;
use DSK;
use GCh;
use EuroStroy;
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
     * sets of symbols
     * to ignore a chess-file that contains some if them in its name
     */
    /* protected $ignoreIfContains = [
        'КЛАДОВКИ',
        'Кладовки',
        'кладовки',
        'ПАРКОВКА',
        'Парковка',
        'парковка',
        'ПАРКОВКИ',
        'Парковки',
        'парковки',
        'ПАРКИНГ',
        'Паркинг',
        'паркинг',
        'ГАРАЖ-СТОЯНКА',
        'Гараж-стоянка',
        'гараж-стоянка',
        'ЗЕМЕЛЬНЫЙ УЧАСТОК',
        'Земельный участок',
        'земельный участок',
        'АВТОСТОЯНКА',
        'Автостоянка',
        'автостоянка',
        // special cases
        '(вторая очередь)' // - to force downloading 'Городские сады 1' instead of 'Городские сады 1 (вторая очередь)' for 'ВДК' developer
    ]; */

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Generate sets for all the active chesses
        $activeChesses = Chess::all()->where('is_active', 1);
        $chessNames = array(); // Set of active chesses names
        $chessPaths = array(); // Set of active chesses paths
        $chessSchemes = array(); // Set of active chesses scheme classes
        foreach ($activeChesses as $chess) {
            if (!empty($chess->scheme)) {
                $scheme = $this->chessScheme($chess->scheme);
                //$currName = !empty($chess->attachment_filename) ? $chess->attachment_filename : '';
                $currName = !empty($chess->attachment_filename) ? $scheme->filterChessFilename(substr($chess->attachment_filename, 0, strrpos($chess->attachment_filename, '.'))) : ''; // chess (attachment) filename without the extention and dynamic (changing) parts of the name (date etc.)
                $currPath = !empty($chess->file_chess_path) ? $chess->file_chess_path : '';
                array_push($chessNames, $currName);
                array_push($chessPaths, $currPath);
                array_push($chessSchemes, $scheme);
            }
        }

        // Connect to the mail service
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

        // Traverse through every Mailbox
        /** @var \Webklex\PHPIMAP\Folder $folder */
        foreach($folders as $folder){

            // Get all Messages of the current Mailbox $folder
            /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
            $messages = $folder->messages()
                //->since(strtotime("-1 week"))
                ->since(strtotime("-2 weeks"))
                //->since(strtotime("-1 month"))
                ->unseen()
                ->get();

            /** @var \Webklex\PHPIMAP\Message $message */
            foreach($messages as $message){
                $attachments = $message->getAttachments();
                foreach($attachments as $attachment) {
                    foreach($chessNames as $index => $chessName) {
                        /** here we ignore files that contain some substrings determined in $this->ignoreIfContains property */
                        /* foreach($this->ignoreIfContains as $ignoreMarker) {
                            if (strpos($attachment->name, $ignoreMarker)) {
                                continue 2;
                            }
                        } */
                        
                        if ($chessName === $chessSchemes[$index]->filterChessFilename(substr($attachment->name, 0, strrpos($attachment->name, '.')))) {
                        //if(str_starts_with($attachment->name, $chessName)) {
                            $pathParts = explode('/', $chessPaths[$index]);
                            $attachment->save($path = storage_path('app/'.$pathParts[0].'/'), $filename = $pathParts[1]);
                            unset($chessNames[$index]);
                            unset($chessPaths[$index]);  
                            unset($chessSchemes[$index]);  
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

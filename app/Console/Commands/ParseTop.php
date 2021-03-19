<?php

namespace App\Console\Commands;

use App\Film;
use DiDom\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ParseTop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:imdbTop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $document = new Document('https://www.imdb.com/chart/top/', true);

        $titles = $document->find('.titleColumn > a');

        foreach ($titles as $title) {
            $href = trim($title->attr('href'));

            $document = new Document('https://www.imdb.com/'.$href, true);

            $films = new Film();

            $titles = $document->find('h1');
            $release_date = $document->find('a[title=See more release dates]');
            $subtext = $document->first('.subtext');
            $rating = $document->find('.ratingValue');
            $director = $document->find('.credit_summary_item > a');
            $cover_image = $document->find('.poster > a > img');

            $url = trim($cover_image[0]->attr('src'));
            $image = file_get_contents($url);
            $name = substr($url, strrpos($url, '/') + 1);

            Storage::put($name, $image);

            $films->title = trim($titles[0]->text());
            $films->category = trim($subtext->child(7)->text());
            $films->release_date = trim($release_date[0]->text());
            $films->rating = trim($rating[0]->text());
            $films->director = trim($director[0]->text());
            $films->cover_image = Storage::path($name);
            $films->top_chart = 1;

            $films->save();
            print trim($titles[0]->text()) . "\n";
        };
    }
}

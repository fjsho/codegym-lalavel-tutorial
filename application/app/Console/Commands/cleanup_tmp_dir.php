<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class cleanup_tmp_dir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:cleanup_tmp_dir';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up the public/tmp directory';

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
        if($all_files = Storage::disk('public')->allFiles('tmp/')){
            Storage::disk('public')->delete($all_files);
        }
        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateNotificationUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:update-urls {--old-url=http://lms.test} {--new-url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update notification URLs from old domain to new domain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oldUrl = $this->option('old-url');
        $newUrl = $this->option('new-url') ?: config('app.url');

        $this->info("Updating notification URLs...");
        $this->info("Old URL: {$oldUrl}");
        $this->info("New URL: {$newUrl}");

        // Get all unread notifications
        $notifications = DB::table('notifications')
            ->whereNull('read_at')
            ->get();

        $updated = 0;

        foreach ($notifications as $notification) {
            $data = json_decode($notification->data, true);
            
            // Check if action_url exists and contains the old URL
            if (isset($data['action_url']) && str_contains($data['action_url'], $oldUrl)) {
                // Replace the old URL with the new URL
                $data['action_url'] = str_replace($oldUrl, $newUrl, $data['action_url']);
                
                // Update the notification
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update([
                        'data' => json_encode($data),
                        'updated_at' => now(),
                    ]);
                
                $updated++;
            }
        }

        $this->info("Updated {$updated} notification(s) successfully!");
        
        return Command::SUCCESS;
    }
}

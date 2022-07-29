<?php

namespace App\Modules\Forge\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Forge\Forge;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class SyncCheckFailedProductsJob.
 */
class CreateSiteJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $siteDomain = "";

        $token = env('LARAVEL_FORGE_TOKEN');
        $serverId = env('LARAVEL_FORGE_SERVER_ID');

        $this->installSiteOnForge($token, $serverId, $siteDomain);
    }


    /**
     * @param string $token
     * @param int $serverId
     * @param string $siteDomain
     */
    private function installSiteOnForge(string $token, int $serverId, string $siteDomain): void
    {
        $siteUsername = \Illuminate\Support\Str::camel($siteDomain);
        $githubRepository = "CodingCabProduction/management.products.api";
        $githubBranch = "master";
        $phpVersion = "php74";

        $forge = new Forge($token);

        try {
            info('Creating site');
            $site = $forge->createSite($serverId, [
                "domain" => $siteDomain,
                "project_type" => "php",
                "isolated" => true,
                "username" => $siteUsername,
                "directory" => '/public',
                "php_version" => $phpVersion,
            ]);
            info('Site created', $site->attributes);

            info('Installing git repository');
            $site->installGitRepository([
                "provider" => "github",
                "repository" => $githubRepository,
                "branch" => $githubBranch,
                "composer" => true
            ]);
            info('Git repository installed', $site->attributes);

            info('Installing LetEncrypt certificate');
            $forge->setTimeout(120)->obtainLetsEncryptCertificate($serverId, $site->id, [
                "type" => "new",
                "domains" => [$siteDomain],
            ]);
            info('LetEncrypt certificate installed', $site->attributes);

            info('Creating job worker');
            $forge->createWorker($serverId, $site->id, [
                "connection" => "database",
                "timeout" => 3600,
                "sleep" => 60,
                "tries" => 1,
                "processes" => 1,
                "stopwaitsecs" => 600,
                "daemon" => false,
                "force" => false,
                "php_version" => $phpVersion,
                "queue" => "default"
            ]);
            info('Job worker created', $site->attributes);
        } catch (\Exception $e) {
            $site->delete();
            ray($e);
        }
    }
}

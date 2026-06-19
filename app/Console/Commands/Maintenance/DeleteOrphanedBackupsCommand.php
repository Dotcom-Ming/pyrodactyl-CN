<?php

namespace Pterodactyl\Console\Commands\Maintenance;

use Illuminate\Console\Command;
use Pterodactyl\Models\Backup;
use Illuminate\Database\Eloquent\Builder;

class DeleteOrphanedBackupsCommand extends Command
{
    protected $signature = 'p:maintenance:delete-orphaned-backups {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Delete backups that reference non-existent servers (orphaned backups), including soft-deleted backups.';

    /**
     * DeleteOrphanedBackupsCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        // Find backups that reference non-existent servers including 
        // soft-deleted backups since they might be orphaned too
        $orphanedBackups = Backup::withTrashed()
            ->whereDoesntHave('server')
            ->get();

        if ($orphanedBackups->isEmpty()) {
            $this->info(trans('command/messages.maintenance.no_orphaned_backups'));
            return;
        }

        $count = $orphanedBackups->count();
        $totalSize = $orphanedBackups->sum('bytes');
        
        if ($isDryRun) {
            $this->warn(trans('command/messages.maintenance.orphaned_backups_dry_run', ['count' => $count, 'size' => $this->formatBytes($totalSize)]));
            
            $this->table(
                [
                    trans('command/messages.maintenance.table_id'),
                    trans('command/messages.maintenance.table_uuid'),
                    trans('command/messages.maintenance.table_name'),
                    trans('command/messages.maintenance.table_server_id'),
                    trans('command/messages.maintenance.table_disk'),
                    trans('command/messages.maintenance.table_size'),
                    trans('command/messages.maintenance.table_status'),
                    trans('command/messages.maintenance.table_created_at'),
                ],
                $orphanedBackups->map(function (Backup $backup) {
                    return [
                        $backup->id,
                        $backup->uuid,
                        $backup->name,
                        $backup->server_id,
                        $backup->disk,
                        $this->formatBytes($backup->bytes),
                        $backup->trashed() ? trans('command/messages.maintenance.soft_deleted') : trans('command/messages.maintenance.active'),
                        $backup->created_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray()
            );
            
            $this->info(trans('command/messages.maintenance.run_without_dry_run'));
            return;
        }

        if (!$this->confirm(trans('command/messages.maintenance.delete_orphaned_confirm', ['count' => $count, 'size' => $this->formatBytes($totalSize)]))) {
            $this->info(trans('command/messages.maintenance.operation_cancelled'));
            return;
        }

        $this->warn(trans('command/messages.maintenance.delete_orphaned_start', ['count' => $count, 'size' => $this->formatBytes($totalSize)]));

        $deletedCount = 0;
        $failedCount = 0;

        foreach ($orphanedBackups as $backup) {
            try {
                // If backup is already soft-deleted, force delete it completely
                if ($backup->trashed()) {
                    $backup->forceDelete();
                    $deletedCount++;
                    $this->info(trans('command/messages.maintenance.force_deleted_backup', ['uuid' => $backup->uuid, 'name' => $backup->name, 'size' => $this->formatBytes($backup->bytes)]));
                } else {
                    // Delete the orphaned backup from the database
                    $backup->forceDelete();
                    $deletedCount++;
                    $this->info(trans('command/messages.maintenance.deleted_backup', ['uuid' => $backup->uuid, 'name' => $backup->name, 'size' => $this->formatBytes($backup->bytes)]));
                }
            } catch (\Exception $exception) {
                $failedCount++;
                $this->error(trans('command/messages.maintenance.delete_backup_failed', ['uuid' => $backup->uuid, 'error' => $exception->getMessage()]));
                
                // If we can't delete from storage, at least remove the database record
                try {
                    if ($backup->trashed()) {
                        $backup->forceDelete();
                        $this->warn(trans('command/messages.maintenance.force_deleted_record', ['uuid' => $backup->uuid]));
                    } else {
                        $backup->delete();
                        $this->warn(trans('command/messages.maintenance.removed_database_record', ['uuid' => $backup->uuid]));
                    }
                } catch (\Exception $dbException) {
                    $this->error(trans('command/messages.maintenance.remove_database_record_failed', ['uuid' => $backup->uuid, 'error' => $dbException->getMessage()]));
                }
            }
        }

        $this->info(trans('command/messages.maintenance.cleanup_completed', ['deleted' => $deletedCount, 'failed' => $failedCount]));
    }

    /**
     * Format bytes into human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = 1024;
        $exponent = floor(log($bytes) / log($base));
        $exponent = min($exponent, count($units) - 1);

        $value = $bytes / pow($base, $exponent);
        $unit = $units[$exponent];

        return sprintf('%.2f %s', $value, $unit);
    }
}

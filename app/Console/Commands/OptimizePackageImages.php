<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageOptimizationService;

class OptimizePackageImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize-packages {--force : Force optimization even if already optimized}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all package images for faster loading';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting package image optimization...');
        $this->newLine();
        
        $service = new ImageOptimizationService();
        
        $results = $service->optimizeAllPackageImages();
        
        $this->newLine();
        $this->info('=== OPTIMIZATION RESULTS ===');
        $this->line("Total Packages: {$results['total']}");
        $this->line("Optimized: {$results['optimized']}");
        $this->line("Failed: {$results['failed']}");
        $this->line("Skipped: {$results['skipped']}");
        
        if ($results['optimized'] > 0) {
            $this->newLine();
            $this->info('✅ Image optimization complete!');
            $this->line('Images are now compressed and will load faster.');
        } else {
            $this->newLine();
            $this->warn('No images were optimized.');
        }
        
        return Command::SUCCESS;
    }
}

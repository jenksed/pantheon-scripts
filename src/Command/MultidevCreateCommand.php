<?php

namespace PantheonCli\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'multidev:create',
    description: 'Create multiple multidev environments for a Pantheon site'
)]
class MultidevCreateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('product', InputArgument::REQUIRED, 'The site slug/product name')
            ->addOption(
                'envs',
                'e',
                InputOption::VALUE_REQUIRED,
                'Space-separated list of environment names to create',
                ''
            )
            ->addOption(
                'format',
                'f',
                InputOption::VALUE_REQUIRED,
                'Output format (table, json)',
                'table'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Validate Terminus is available
        if (!$this->isTerminusAvailable()) {
            $io->error('Terminus CLI not found. Please install and authenticate with Terminus.');
            return Command::FAILURE;
        }

        $product = $input->getArgument('product');
        $envsString = $input->getOption('envs');
        $format = $input->getOption('format');

        // Parse environments
        if (empty($envsString)) {
            $envsString = $io->ask('Enter space-separated environment names to create');
        }
        
        $envs = array_filter(explode(' ', trim($envsString)));
        
        if (empty($envs)) {
            $io->error('No environments specified');
            return Command::FAILURE;
        }

        $io->title("Creating multidev environments for site: {$product}");
        
        $results = [];
        
        foreach ($envs as $env) {
            $io->section("Creating environment: {$env}");
            
            try {
                // Create the multidev environment
                $this->createMultidev($product, $env, $io);
                
                // Get connection info
                $connectionInfo = $this->getConnectionInfo($product, $env);
                
                $results[] = [
                    'environment' => $env,
                    'site' => $product,
                    'status' => 'created',
                    'sftp_host' => $connectionInfo['sftp_host'] ?? 'N/A',
                    'sftp_port' => $connectionInfo['sftp_port'] ?? 'N/A',
                    'sftp_username' => $connectionInfo['sftp_username'] ?? 'N/A',
                    'working_directory' => '~/code'
                ];
                
                $io->success("Environment {$env} created successfully");
                
            } catch (ProcessFailedException $e) {
                $results[] = [
                    'environment' => $env,
                    'site' => $product,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                
                $io->error("Failed to create environment {$env}: " . $e->getMessage());
            }
        }

        // Output results
        $this->outputResults($results, $format, $io);
        
        $io->success('MultiDev creation process completed');
        
        return Command::SUCCESS;
    }

    private function isTerminusAvailable(): bool
    {
        $process = new Process(['which', 'terminus']);
        $process->run();
        
        return $process->isSuccessful();
    }

    private function createMultidev(string $product, string $env, SymfonyStyle $io): void
    {
        $process = new Process([
            'terminus',
            'multidev:create',
            "{$product}.live",
            $env
        ]);
        
        $process->setTimeout(300); // 5 minutes timeout
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        $io->writeln($process->getOutput());
    }

    private function getConnectionInfo(string $product, string $env): array
    {
        $process = new Process([
            'terminus',
            'connection:info',
            "{$product}.{$env}",
            '--format=json'
        ]);
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            return [];
        }
        
        $data = json_decode($process->getOutput(), true);
        
        // Parse SFTP command to extract connection details
        $sftpCommand = $data['sftp_command'] ?? '';
        
        $connectionInfo = [];
        
        if (preg_match('/sftp -o Port=(\d+) (.+)@(.+)/', $sftpCommand, $matches)) {
            $connectionInfo['sftp_port'] = $matches[1];
            $connectionInfo['sftp_username'] = $matches[2];
            $connectionInfo['sftp_host'] = $matches[3];
        }
        
        return $connectionInfo;
    }

    private function outputResults(array $results, string $format, SymfonyStyle $io): void
    {
        if ($format === 'json') {
            $io->writeln(json_encode($results, JSON_PRETTY_PRINT));
            return;
        }
        
        // Table format
        $tableData = [];
        foreach ($results as $result) {
            $tableData[] = [
                $result['environment'],
                $result['site'],
                $result['status'],
                $result['sftp_host'] ?? 'N/A',
                $result['sftp_port'] ?? 'N/A',
                $result['sftp_username'] ?? 'N/A'
            ];
        }
        
        $io->table(
            ['Environment', 'Site', 'Status', 'SFTP Host', 'SFTP Port', 'SFTP Username'],
            $tableData
        );
    }
}
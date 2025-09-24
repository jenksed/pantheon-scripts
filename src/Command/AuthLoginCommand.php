<?php

namespace PantheonCli\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'auth:login',
    description: 'Authenticate with Pantheon using machine token'
)]
class AuthLoginCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption(
                'token',
                't',
                InputOption::VALUE_REQUIRED,
                'Pantheon machine token (or set PANTHEON_MACHINE_TOKEN env var)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Check if Terminus is available
        if (!$this->isTerminusAvailable()) {
            $io->error('Terminus CLI not found. Please install Terminus first.');
            return Command::FAILURE;
        }

        // Get token from option or environment
        $token = $input->getOption('token') ?: $_ENV['PANTHEON_MACHINE_TOKEN'] ?? null;
        
        if (!$token) {
            $token = $io->askHidden('Enter your Pantheon machine token');
        }
        
        if (!$token) {
            $io->error('No machine token provided');
            return Command::FAILURE;
        }

        $io->info('Authenticating with Pantheon...');
        
        try {
            $this->authenticateWithTerminus($token);
            
            // Verify authentication
            $userInfo = $this->getCurrentUser();
            
            $io->success("Successfully authenticated as: {$userInfo['email']}");
            
            return Command::SUCCESS;
            
        } catch (ProcessFailedException $e) {
            $io->error('Authentication failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function isTerminusAvailable(): bool
    {
        $process = new Process(['which', 'terminus']);
        $process->run();
        
        return $process->isSuccessful();
    }

    private function authenticateWithTerminus(string $token): void
    {
        $process = new Process([
            'terminus',
            'auth:login',
            '--machine-token=' . $token
        ]);
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    private function getCurrentUser(): array
    {
        $process = new Process([
            'terminus',
            'auth:whoami',
            '--format=json'
        ]);
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        return json_decode($process->getOutput(), true);
    }
}
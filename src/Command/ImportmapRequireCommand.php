<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'importmap:require',
    description: 'Ajouter une dépendance à l\'importmap en préservant la structure personnalisée',
    aliases: ['importmap:req']
)]
class ImportmapRequireCommand extends Command
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('package', InputArgument::REQUIRED, 'Le package à ajouter (ex: three, axios@1.6.0)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $packageSpec = $input->getArgument('package');

        // Parser le package (ex: "three" ou "axios@1.6.0")
        $parts = explode('@', $packageSpec);
        $packageName = $parts[0];
        $version = $parts[1] ?? null;

        $io->title("🎯 Ajout de $packageName avec structure préservée");

        // Lire l'importmap actuel
        $importmapPath = $this->projectDir . '/importmap.php';
        
        if (!file_exists($importmapPath)) {
            $io->error('Fichier importmap.php introuvable');
            return Command::FAILURE;
        }

        // Configuration par défaut pour les packages courants
        $defaultConfigs = [
            'three' => ['version' => $version ?? '0.178.0'],
            'axios' => ['version' => $version ?? '1.6.8'],
            'vue' => ['version' => $version ?? '3.4.0'],
            'react' => ['version' => $version ?? '18.2.0'],
            'lodash' => ['version' => $version ?? '4.17.21'],
        ];

        $config = $defaultConfigs[$packageName] ?? ['version' => $version ?? '1.0.0'];

        // Ajouter le package à notre structure
        $this->addPackageToStructure($packageName, $config, $io);

        $io->success("✅ Package $packageName ajouté avec succès !");
        $io->note('Structure importmap.php préservée');

        return Command::SUCCESS;
    }

    private function addPackageToStructure(string $packageName, array $config, SymfonyStyle $io): void
    {
        $importmapPath = $this->projectDir . '/importmap.php';
        $content = file_get_contents($importmapPath);
        
        // Trouver la section $additionalDependencies
        $pattern = '/(\/\/ Dépendances ajoutées via importmap:require\s*\$additionalDependencies\s*=\s*\[)(.*?)(\];)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            $existingDeps = trim($matches[2]);
            
            // Formater la nouvelle dépendance
            $formattedDep = $this->formatDependency($packageName, $config);
            
            // Vérifier si le package existe déjà
            if (strpos($existingDeps, "'$packageName'") !== false) {
                $io->note("Package $packageName déjà présent, mise à jour...");
                // Remplacer l'existant
                $newDeps = preg_replace(
                    '/[\'"]' . preg_quote($packageName, '/') . '[\'"] => \[.*?\],/s',
                    $formattedDep,
                    $existingDeps
                );
            } else {
                // Ajouter le nouveau package
                $newDeps = $existingDeps . ($existingDeps ? "\n    " : "\n    ") . $formattedDep;
            }
            
            $newContent = str_replace(
                $matches[0],
                $matches[1] . $newDeps . "\n" . $matches[3],
                $content
            );
            
            file_put_contents($importmapPath, $newContent);
            $io->text("✓ Package ajouté à la section additionalDependencies");
        } else {
            $io->error('Structure importmap non reconnue. Vérifiez le fichier importmap.php');
        }
    }

    private function formatDependency(string $packageName, array $config): string
    {
        $configLines = [];
        foreach ($config as $key => $value) {
            if (is_string($value)) {
                $configLines[] = "        '$key' => '$value'";
            } elseif (is_bool($value)) {
                $configLines[] = "        '$key' => " . ($value ? 'true' : 'false');
            } else {
                $configLines[] = "        '$key' => '$value'";
            }
        }
        
        return "'$packageName' => [\n" . implode(",\n", $configLines) . "\n    ],";
    }
}

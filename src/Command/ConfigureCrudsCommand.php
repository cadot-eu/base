<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use ReflectionClass;
use ReflectionMethod;

#[AsCommand(
    name: 'app:configure-cruds',
    description: 'Configure la méthode cruds() pour une entité de manière interactive',
)]
class ConfigureCrudsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private array $availableTypes = [];
    private string $projectDir;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->projectDir = $parameterBag->get('kernel.project_dir');
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('🔧 Configuration interactive de la méthode cruds()');

        // 1. Découvrir les types disponibles depuis les templates Twig
        $this->discoverAvailableTypes($io);

        // 2. Lister et choisir une entité
        $entities = $this->getAvailableEntities();
        if (empty($entities)) {
            $io->error('Aucune entité trouvée dans le dossier src/Entity/');
            return Command::FAILURE;
        }

        $entityChoice = $io->choice('Choisissez une entité à configurer:', array_keys($entities));
        $entityData = $entities[$entityChoice];

        $io->section("Configuration de l'entité: {$entityChoice}");

        // 3. Analyser l'entité
        $reflection = new ReflectionClass($entityData['class']);
        $fields = $this->analyzeEntityFields($reflection);
        $associations = $this->analyzeEntityAssociations($reflection);
        $currentCruds = $this->getCurrentCruds($reflection);

        // 4. Configuration interactive avec système de menus
        $newCruds = $this->showMainMenu($io, $fields, $associations, $currentCruds, $entityChoice);

        // 5. Sauvegarder
        $this->saveCrudsMethod($entityData['file'], $newCruds, $io);

        $io->success('Configuration de la méthode cruds() terminée avec succès !');
        return Command::SUCCESS;
    }

    private function showMainMenu(SymfonyStyle $io, array $fields, array $associations, array $currentCruds, string $entityName): array
    {
        $cruds = $currentCruds;
        
        $first = true;
        do {
            $io->section("🏠 Menu principal - Entité: {$entityName}");

            $menuChoices = [
                'global' => '🌐 Configuration globale (Ordre, Actions globales, boutons au-dessus du tableau, drag-drop ...)',
                'id' => '🆔 Configuration ID (InfoIdCrud, Actions par ligne)',
                'fields' => '📝 Configuration des champs',
                'preview' => '👁️ Aperçu de la configuration actuelle',
                'save' => '💾 Sauvegarder et quitter',
                'quit' => '❌ Quitter sans sauvegarder'
            ];

            // Par défaut, "save" au premier affichage, sinon on garde le dernier choix
            static $lastChoice = null;
            $default = $first ? 'save' : ($lastChoice ?? 'global');
            $first = false;

            $choice = $io->choice('Que voulez-vous configurer ?', $menuChoices, $default);
            $lastChoice = $choice;

            switch ($choice) {
                case 'global':
                    $cruds = $this->configureGlobalMenu($io, $cruds);
                    break;
                case 'id':
                    $cruds = $this->configureIdMenu($io, $cruds, $fields);
                    break;
                case 'fields':
                    $cruds = $this->configureFieldsMenu($io, $fields, $associations, $cruds);
                    break;
                case 'preview':
                    $this->showSummary($io, $cruds);
                    $io->writeln('');
                    break;
                case 'save':
                    if ($this->confirmSave($io, $cruds)) {
                        return $cruds;
                    }
                    break;
                case 'quit':
                    if ($io->confirm('Êtes-vous sûr de vouloir quitter sans sauvegarder ?', false)) {
                        $io->info('Configuration annulée.');
                        return $currentCruds;
                    }
                    break;
            }
        } while (true);
    }

    private function configureGlobalMenu(SymfonyStyle $io, array $cruds): array
    {
        $first = true;
        do {
            $io->section('🌐 Configuration globale');

            $currentOrder = isset($cruds['Ordre']) ? 'Activé (' . ($cruds['Ordre']['propriete'] ?? 'ordre') . ')' : 'Désactivé';
            $currentActions = isset($cruds['ActionsTableauEntite']) ? 'Configuré (' . count($cruds['ActionsTableauEntite']) . ' actions)' : 'Aucune action';

            $globalChoices = [
                'order' => "🔄 Tri par glisser-déposer - {$currentOrder}",
                'actions' => "⚡ Actions globales - {$currentActions}",
                'back' => '🔙 Retour au menu principal'
            ];

            $choice = $io->choice('Configuration globale:', $globalChoices, 'back');

            switch ($choice) {
                case 'order':
                    $cruds = $this->configureOrder($io, $cruds);
                    break;
                case 'actions':
                    $cruds = $this->configureGlobalActionsMenu($io, $cruds);
                    break;
                case 'back':
                    return $cruds;
            }
        } while (true);
    }

    private function configureIdMenu(SymfonyStyle $io, array $cruds, array $fields): array
    {
        $first = true;
        do {
            $io->section('🆔 Configuration ID');

            $currentInfo = isset($cruds['id']['InfoIdCrud']) ? 'Configuré (' . count($cruds['id']['InfoIdCrud']) . ' infos)' : 'Aucune info';
            $currentActions = isset($cruds['id']['Actions']) ? 'Configuré (' . count($cruds['id']['Actions']) . ' actions)' : 'Aucune action';

            $idChoices = [
                'info' => "ℹ️ Tooltip d'informations - {$currentInfo}",
                'actions' => "🎯 Actions par ligne - {$currentActions}",
                'back' => '🔙 Retour au menu principal'
            ];

            $choice = $io->choice('Configuration ID:', $idChoices, 'back');

            switch ($choice) {
                case 'info':
                    $cruds['id']['InfoIdCrud'] = $this->configureIdInfo($io, $fields);
                    break;
                case 'actions':
                    $cruds['id']['Actions'] = $this->configureLineActions($io, $cruds['id']['Actions'] ?? []);
                    break;
                case 'back':
                    return $cruds;
            }
        } while (true);
    }

    private function configureFieldsMenu(SymfonyStyle $io, array $fields, array $associations, array $cruds): array
    {
        $first = true;
        do {
            $io->section('📝 Configuration des champs');

            $allFields = array_merge(array_keys($fields), array_keys($associations));
            $fieldChoices = [];

            foreach ($allFields as $fieldName) {
                if ($fieldName === 'id') continue;

                $type = '';
                if (isset($fields[$fieldName])) {
                    $type = "({$fields[$fieldName]['type']})";
                } elseif (isset($associations[$fieldName])) {
                    $assocType = $this->getAssociationTypeLabel($associations[$fieldName]['type']);
                    $type = "({$assocType})";
                }

                $status = isset($cruds[$fieldName]) ? '✅' : '⚪';
                $fieldChoices[$fieldName] = "{$status} {$fieldName} {$type}";
            }

            $fieldChoices['back'] = '🔙 Retour au menu principal';

            $choice = $io->choice('Choisissez un champ à configurer:', $fieldChoices, 'back');

            if ($choice === 'back') {
                return $cruds;
            }

            $cruds = $this->configureFieldMenu($io, $choice, $fields, $associations, $cruds);

        } while (true);
    }

    private function configureFieldMenu(SymfonyStyle $io, string $fieldName, array $fields, array $associations, array $cruds): array
    {
        do {
            $io->section("⚙️ Configuration du champ: {$fieldName}");
            
            $currentConfig = $cruds[$fieldName] ?? [];
            $this->displayCurrentConfig($io, $fieldName, $currentConfig);
            
            $fieldChoices = [
                'edition' => '✏️ Édition en ligne',
                'tooltip' => '💬 Tooltip d\'aide',
                'label' => '🏷️ Label personnalisé',
                'affichage' => '👁️ Mode d\'affichage',
                'reset' => '🗑️ Réinitialiser la configuration',
                'back' => '🔙 Retour à la liste des champs'
            ];
            
            $choice = $io->choice("Configuration de '{$fieldName}':", $fieldChoices);
            
            switch ($choice) {
                case 'edition':
                    $currentEdition = $currentConfig['Edition'] ?? false;
                    $cruds[$fieldName]['Edition'] = $io->confirm('Permettre l\'édition en ligne ?', $currentEdition);
                    break;
                case 'tooltip':
                    $currentTooltip = $currentConfig['tooltip'] ?? '';
                    $tooltip = $io->ask('Tooltip d\'aide (laissez vide pour aucun)', $currentTooltip);
                    $cruds[$fieldName]['tooltip'] = $tooltip ?: null;
                    break;
                case 'label':
                    $currentLabel = $currentConfig['label'] ?? '';
                    $label = $io->ask('Label personnalisé (laissez vide pour le nom du champ)', $currentLabel);
                    $cruds[$fieldName]['label'] = $label ?: null;
                    break;
                case 'affichage':
                    $currentAffichage = $currentConfig['affichage'] ?? '';
                    $affichageChoices = ['', 'tooltip', 'select', 'checkbox'];
                    $affichage = $io->choice('Mode d\'affichage spécial', $affichageChoices, $currentAffichage ?: '');
                    if ($affichage) {
                        $cruds[$fieldName]['affichage'] = $affichage;
                    } else {
                        unset($cruds[$fieldName]['affichage']);
                    }
                    break;
                case 'reset':
                    if ($io->confirm("Réinitialiser la configuration de '{$fieldName}' ?", false)) {
                        unset($cruds[$fieldName]);
                        $io->success('Configuration réinitialisée.');
                    }
                    break;
                case 'back':
                    return $cruds;
            }
        } while (true);
    }

    private function configureOrder(SymfonyStyle $io, array $cruds): array
    {
        $currentOrder = $cruds['Ordre']['propriete'] ?? 'ordre';
        
        if ($io->confirm('Voulez-vous activer le tri par glisser-déposer ?', isset($cruds['Ordre']))) {
            $orderField = $io->ask('Nom du champ pour l\'ordre', $currentOrder);
            $cruds['Ordre'] = ['propriete' => $orderField];
            $io->success('Tri par glisser-déposer configuré.');
        } else {
            unset($cruds['Ordre']);
            $io->success('Tri par glisser-déposer désactivé.');
        }
        
        return $cruds;
    }

    private function configureGlobalActionsMenu(SymfonyStyle $io, array $cruds): array
    {
        $actions = $cruds['ActionsTableauEntite'] ?? [];
        
        do {
            $io->section('⚡ Actions globales');
            
            if (empty($actions)) {
                $io->writeln('<comment>Aucune action configurée</comment>');
            } else {
                $io->writeln('<info>Actions configurées:</info>');
                foreach ($actions as $name => $action) {
                    $io->writeln("  • {$name}: {$action['url']}");
                }
            }
            
            $actionChoices = [
                'add' => '➕ Ajouter une action',
                'remove' => '➖ Supprimer une action',
                'clear' => '🗑️ Supprimer toutes les actions',
                'back' => '🔙 Retour'
            ];
            
            $choice = $io->choice('Actions globales:', $actionChoices);
            
            switch ($choice) {
                case 'add':
                    $actions = $this->addGlobalAction($io, $actions);
                    break;
                case 'remove':
                    if (!empty($actions)) {
                        $actions = $this->removeGlobalAction($io, $actions);
                    } else {
                        $io->warning('Aucune action à supprimer.');
                    }
                    break;
                case 'clear':
                    if ($io->confirm('Supprimer toutes les actions ?', false)) {
                        $actions = [];
                        $io->success('Toutes les actions ont été supprimées.');
                    }
                    break;
                case 'back':
                    $cruds['ActionsTableauEntite'] = $actions;
                    return $cruds;
            }
        } while (true);
    }

    private function addGlobalAction(SymfonyStyle $io, array $actions): array
    {
        $actionName = $io->ask('Nom de l\'action');
        if (!$actionName) return $actions;
        
        $url = $io->ask('URL de l\'action (utilisez {{entity}} pour le nom d\'entité)');
        $icon = $io->ask('Classe CSS de l\'icône (ex: bi bi-download)', '');
        $texte = $io->ask('Texte du bouton', $actionName);
        $target = $io->choice('Target du lien', ['_self', '_blank', 'modal'], '_self');
        $turbo = $io->confirm('Utiliser Turbo ?', false);

        $actions[$actionName] = [
            'url' => $url,
            'icon' => $icon,
            'texte' => $texte
        ];

        if ($target !== '_self') {
            $actions[$actionName]['target'] = $target;
        }
        if (!$turbo) {
            $actions[$actionName]['turbo'] = false;
        }
        
        $io->success("Action '{$actionName}' ajoutée.");
        return $actions;
    }

    private function removeGlobalAction(SymfonyStyle $io, array $actions): array
    {
        $actionChoice = $io->choice('Quelle action supprimer ?', array_keys($actions));
        unset($actions[$actionChoice]);
        $io->success("Action '{$actionChoice}' supprimée.");
        return $actions;
    }

    private function getAssociationTypeLabel(int $type): string
    {
        return match($type) {
            1 => 'OneToOne',
            2 => 'ManyToOne',
            4 => 'OneToMany',
            8 => 'ManyToMany',
            default => 'Association'
        };
    }

    private function confirmSave(SymfonyStyle $io, array $cruds): bool
    {
        $this->showSummary($io, $cruds);
        return $io->confirm('Voulez-vous enregistrer cette configuration ?', true);
    }

    private function discoverAvailableTypes(SymfonyStyle $io): void
    {
        $partialDir = $this->projectDir . '/templates/dashboard/partial';
        
        if (!is_dir($partialDir)) {
            $io->warning("Répertoire des templates partiels non trouvé: {$partialDir}");
            return;
        }

        $finder = new Finder();
        $finder->files()->in($partialDir)->name('*.html.twig');

        foreach ($finder as $file) {
            $typeName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $this->availableTypes[] = $typeName;
        }

        $io->note('Types d\'affichage disponibles: ' . implode(', ', $this->availableTypes));
    }

    private function getAvailableEntities(): array
    {
        $entityDir = $this->projectDir . '/src/Entity';
        $entities = [];

        if (!is_dir($entityDir)) {
            return $entities;
        }

        $finder = new Finder();
        $finder->files()->in($entityDir)->name('*.php');

        foreach ($finder as $file) {
            $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $fullClassName = "App\\Entity\\{$className}";
            
            if (class_exists($fullClassName)) {
                $entities[$className] = [
                    'class' => $fullClassName,
                    'file' => $file->getRealPath()
                ];
            }
        }

        return $entities;
    }

    private function analyzeEntityFields(ReflectionClass $reflection): array
    {
        $metadata = $this->entityManager->getClassMetadata($reflection->getName());
        $fields = [];

        foreach ($metadata->getFieldNames() as $fieldName) {
            $fieldMapping = $metadata->getFieldMapping($fieldName);
            $fields[$fieldName] = [
                'type' => $fieldMapping['type'],
                'nullable' => $fieldMapping['nullable'] ?? false,
                'length' => $fieldMapping['length'] ?? null
            ];
        }

        return $fields;
    }

    private function analyzeEntityAssociations(ReflectionClass $reflection): array
    {
        $metadata = $this->entityManager->getClassMetadata($reflection->getName());
        $associations = [];

        foreach ($metadata->getAssociationNames() as $assocName) {
            $assocMapping = $metadata->getAssociationMapping($assocName);
            $associations[$assocName] = [
                'type' => $assocMapping['type'],
                'targetEntity' => $assocMapping['targetEntity'],
                'mappedBy' => $assocMapping['mappedBy'] ?? null,
                'inversedBy' => $assocMapping['inversedBy'] ?? null
            ];
        }

        return $associations;
    }

    private function getCurrentCruds(ReflectionClass $reflection): array
    {
        if (!$reflection->hasMethod('cruds')) {
            return [];
        }

        try {
            $instance = $reflection->newInstance();
            return $instance->cruds();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function configureGlobalActions(SymfonyStyle $io): array
    {
        $actions = [];
        
        do {
            $actionName = $io->ask('Nom de l\'action');
            if (!$actionName) break;
            
            $url = $io->ask('URL de l\'action (utilisez {{entity}} pour le nom d\'entité)');
            $icon = $io->ask('Classe CSS de l\'icône (ex: bi bi-download)', '');
            $texte = $io->ask('Texte du bouton', $actionName);
            $target = $io->choice('Target du lien', ['_self', '_blank', 'modal'], '_self');
            $turbo = $io->confirm('Utiliser Turbo ?', false);

            $actions[$actionName] = [
                'url' => $url,
                'icon' => $icon,
                'texte' => $texte
            ];

            if ($target !== '_self') {
                $actions[$actionName]['target'] = $target;
            }
            if (!$turbo) {
                $actions[$actionName]['turbo'] = false;
            }

        } while ($io->confirm('Ajouter une autre action ?', false));

        return $actions;
    }

    private function configureIdInfo(SymfonyStyle $io, array $fields): array
    {
        $info = [];
        
        $io->writeln('Champs disponibles: ' . implode(', ', array_keys($fields)));
        
        do {
            $label = $io->ask('Label de l\'information');
            if (!$label) break;
            
            $value = $io->ask('Valeur PHP (ex: $this->getNom())', '$this->getId()');
            $info[$label] = $value;
            
        } while ($io->confirm('Ajouter une autre information ?', false));

        return $info;
    }

    private function configureLineActions(SymfonyStyle $io, array $existingActions = []): array
    {
        $actions = $existingActions;
        do {
            $io->section('🎯 Actions par ligne');
            if (empty($actions)) {
                $io->writeln('<comment>Aucune action configurée</comment>');
            } else {
                $io->writeln('<info>Actions existantes :</info>');
                foreach ($actions as $name => $params) {
                    $io->writeln("  • {$name}: " . json_encode($params));
                }
            }

            $choices = array_keys($actions);
            $choices[] = 'Créer une nouvelle action';
            $choices[] = 'Retour';

            $choice = $io->choice('Sélectionnez une action à modifier, ou créez-en une nouvelle :', $choices, count($choices)-2);

            if ($choice === 'Retour') {
                return $actions;
            }

            if ($choice === 'Créer une nouvelle action') {
                $actionName = $io->ask('Nom de la nouvelle action');
                if (!$actionName) {
                    continue;
                }
                $url = $io->ask('URL de l\'action (utilisez {{ligne.id}} pour l\'ID)');
                $icon = $io->ask('Classe CSS de l\'icône (ex: bi bi-download)', '');
                $texte = $io->ask('Texte du bouton (utiliser comme tooltip si pas d\'icone)', $actionName);
                $target = $io->choice('Target du lien', ['_self', '_blank', 'modal'], '_self');
                $turbo = $io->confirm('Utiliser Turbo ?', false);
                $actions[$actionName] = [
                    'url' => $url,
                    'icon' => $icon,
                    'texte' => $texte
                ];
                if ($target !== '_self') {
                    $actions[$actionName]['target'] = $target;
                }
                if (!$turbo) {
                    $actions[$actionName]['turbo'] = false;
                }
                $io->success("Action '{$actionName}' ajoutée.");
                continue;
            }

            // Sous-menu pour une action existante
            $subChoices = [
                'edit' => '✏️ Modifier cette action',
                'delete' => '🗑️ Supprimer cette action',
                'back' => '🔙 Retour'
            ];
            $subChoice = $io->choice("Que voulez-vous faire avec l'action '{$choice}' ?", $subChoices, 'edit');

            if ($subChoice === 'edit') {
                $params = $actions[$choice];
                $io->writeln("Modification de l'action : {$choice}");
                $url = $io->ask('URL de l\'action', $params['url'] ?? '');
                $icon = $io->ask('Classe CSS de l\'icône', $params['icon'] ?? '');
                $texte = $io->ask('Texte du bouton', $params['texte'] ?? $choice);
                $target = $io->choice('Target du lien', ['_self', '_blank', 'modal'], $params['target'] ?? '_self');
                $turbo = $io->confirm('Utiliser Turbo ?', !isset($params['turbo']) || $params['turbo'] !== false);

                $actions[$choice] = [
                    'url' => $url,
                    'icon' => $icon,
                    'texte' => $texte
                ];
                if ($target !== '_self') {
                    $actions[$choice]['target'] = $target;
                }
                if (!$turbo) {
                    $actions[$choice]['turbo'] = false;
                }
                $io->success("Action '{$choice}' modifiée.");
            } elseif ($subChoice === 'delete') {
                unset($actions[$choice]);
                $io->success("Action '{$choice}' supprimée.");
            } // sinon retour, rien à faire

        } while (true);
    }

    private function displayCurrentConfig(SymfonyStyle $io, string $fieldName, array $config): void
    {
        if (empty($config)) {
            $io->writeln('<comment>Aucune configuration actuelle</comment>');
            return;
        }

        $io->writeln('<comment>Configuration actuelle:</comment>');
        foreach ($config as $key => $value) {
            $valueStr = is_bool($value) ? ($value ? 'true' : 'false') : (string)$value;
            $io->writeln("  {$key}: {$valueStr}");
        }
    }

    private function showSummary(SymfonyStyle $io, array $cruds): void
    {
        $io->section('📋 Récapitulatif de la configuration');

        foreach ($cruds as $key => $config) {
            $io->writeln("<info>{$key}:</info>");
            if (is_array($config)) {
                foreach ($config as $subKey => $subValue) {
                    if (is_array($subValue)) {
                        $io->writeln("  {$subKey}: " . json_encode($subValue, JSON_PRETTY_PRINT));
                    } else {
                        $valueStr = is_bool($subValue) ? ($subValue ? 'true' : 'false') : (string)$subValue;
                        $io->writeln("  {$subKey}: {$valueStr}");
                    }
                }
            }
            $io->writeln('');
        }
    }

    private function saveCrudsMethod(string $filePath, array $cruds, SymfonyStyle $io): void
    {
        $content = file_get_contents($filePath);
        
        // Supprimer l'ancienne méthode cruds si elle existe
        $content = $this->removeCrudsMethod($content);
        
        // Générer la nouvelle méthode
        $crudMethod = $this->generateCrudsMethod($cruds);
        
        // Ajouter la nouvelle méthode avant la dernière accolade
        $content = preg_replace('/\}(\s*)$/', $crudMethod . "\n}\n", $content);
        
        if (file_put_contents($filePath, $content)) {
            $io->success("Méthode cruds() sauvegardée dans {$filePath}");
        } else {
            $io->error("Erreur lors de la sauvegarde");
        }
    }

    private function removeCrudsMethod(string $content): string
    {
        // Pattern pour supprimer la méthode cruds() existante
        $pattern = '/\s*public function cruds\(\).*?\n\s*\}/s';
        return preg_replace($pattern, '', $content);
    }

    private function generateCrudsMethod(array $cruds): string
    {
        $method = "\n    public function cruds(): array\n    {\n        return [\n";
        
        foreach ($cruds as $key => $config) {
            $method .= "            '{$key}' => ";
            $method .= $this->arrayToPhpString($config, 3);
            $method .= ",\n";
        }
        
        $method .= "        ];\n    }\n";
        
        return $method;
    }

    private function arrayToPhpString($value, int $indent = 0): string
    {
        $spaces = str_repeat('    ', $indent);
        
        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }
            
            $result = "[\n";
            foreach ($value as $k => $v) {
                $result .= $spaces . "    '{$k}' => ";
                $result .= $this->arrayToPhpString($v, $indent + 1);
                $result .= ",\n";
            }
            $result .= $spaces . "]";
            return $result;
        }
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        if (is_null($value)) {
            return 'null';
        }
        
        if (is_string($value)) {
            return "'" . addslashes($value) . "'";
        }
        
        return (string)$value;
    }
}

<?php

/**
 * @file
 * PHP script to generate all logos mosaics.
 * Can only be run from CLI.
 * Usage:
 * Open a terminal, access the root of epg repository and run:
 * php utilities/generate-all-logos-mosaics.php
 *
 * Based on the original script from the tv-logos project.
 * @see https://github.com/tv-logo/tv-logos
 *
 * Adapted by Rootzpower for use in the EPG project.
 * @see https://github.com/Rootzpower/epg
 *
 * Tested with PHP 8.4 (cli).
 * ⚠️ Script comes with no warranty, use at your own risk.
 */

error_reporting(E_ALL);

if (PHP_SAPI !== 'cli') {
    die("This script must be ran from the command line.");
}

$settings = array(
    'countriesFolders' => array(
        __DIR__ . '/../logos',
    ),
    'outputFilename' => '0_all_logos_mosaic.md',
    'cols' => 6,
);

/**
 * List all files of a directory.
 *
 * @param string $dir Directory to scan.
 *
 * @return array<string>
 */
function listAllFiles(string $dir): array
{
    $array = array_diff(scandir($dir), array('.', '..'));

    foreach ($array as &$item) {
        $item = $dir . DIRECTORY_SEPARATOR . $item;
    }
    unset($item);

    foreach ($array as $item) {
        if (is_dir($item)) {
            $array = array_merge($array, listAllFiles($item));
        }
    }

    return $array;
}

/**
 * Organize logos and sort them ASC.
 *
 * @param array<string> $logos List of logos.
 * @param string $source Path to folder.
 *
 * @return array<string, array<string, string>>
 */
function organizeContent(array $logos, string $source): array
{
    $output = array();

    foreach ($logos as $file) {
        $filename = basename($file);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($ext === 'png') {
            $key = preg_replace('/\.png$/i', '', $filename);
            $output['logos'][$key] = $filename;
        }
    }

    if (isset($output['logos'])) {
        ksort($output['logos']);
    }

    return $output;
}

/**
 * Create MD mosaic files.
 *
 * @param array<string, array<string, string>> $logos List of logos.
 * @param string $source Path to folder.
 *
 * @return void
 */
function createMDFiles(array $logos, string $source): void
{
    global $settings;

    foreach ($logos as $files) {
        $outputFile = $source . DIRECTORY_SEPARATOR . $settings['outputFilename'];

        echo "Generating $outputFile\n";

        $outputContent = "# Logos\n\n";

        $table = "";
        $matrix = array();
        $list = "";
        $i = 0;

        foreach ($files as $fileKey => $file) {
            $displayKey = $fileKey;
            // Remover sufixo de país ex: -pt, -es, -fr
            $displayKey = preg_replace('/-[a-z]{2}$/', '', $displayKey);
            // Evitar conflito com palavra reservada "space"
            $displayKey = preg_replace('/^space$/', 'space-channel', $displayKey);

            $matrix[intdiv($i, $settings['cols'])][] = $displayKey;
            $list .= "[$displayKey]:$file\n";
            $i++;
        }

        // Linhas da tabela
        for ($j = 0; $j < count($matrix); $j++) {
            for ($i = 0; $i < $settings['cols']; $i++) {
                $table .= "| ![" . ($matrix[$j][$i] ?? "space") . "] ";
            }
            $table .= "|\n";

            // Header só na primeira linha
            if ($j === 0) {
                for ($i = 0; $i < $settings['cols']; $i++) {
                    $table .= "|:---:";
                }
                $table .= "|\n";
            }
        }

        // Linha extra de espaço no final
        for ($i = 0; $i < $settings['cols']; $i++) {
            $table .= "| ![space] ";
        }
        $table .= "|\n";

        $outputContent .= "$table\n";
        $outputContent .= "\n";
        $outputContent .= "$list\n"
        $outputContent .= "[space]:../utilities/space-1500.png \"Space\"\n";
        $outputContent .= "\n";

        file_put_contents($outputFile, $outputContent);
    }
}

/**
 * Generate all logos mosaics MD files.
 *
 * @return void
 */
function generateAllLogosMosaics(): void
{
    global $settings;

    foreach ($settings['countriesFolders'] as $source) {
        $logos = listAllFiles($source);
        $logos = organizeContent($logos, $source);
        createMDFiles($logos, $source);
    }
}

generateAllLogosMosaics();

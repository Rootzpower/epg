<?php

/**
 * @file
 * PHP script responsible for generating all logo mosaics.
 * This script must be executed exclusively from the command-line interface (CLI).
 *
 * Usage:
 * Open a terminal, navigate to the root of the EPG repository, and run:
 * php utilities/generate-all-logos-mosaics.php
 *
 * Based on the original script from the tv-logos project.
 * @see https://github.com/tv-logo/tv-logos
 *
 * Tested with PHP 8.4 (CLI).
 * ⚠️ This script is provided without warranty. Use at your own risk.
 */

error_reporting(E_ALL);

if (PHP_SAPI !== 'cli') {
    die("This script must be run from the command line.");
}

$settings = array(
    'countriesFolders' => array(
        __DIR__ . '/../logos',
    ),
    'outputFilename' => '0_logos_mosaic.md',
    'cols' => 6,
);

/**
 * Recursively lists all files within a directory.
 *
 * @param string $dir
 *   Base directory to scan.
 *
 * @return array
 *   A flat array containing full paths to all discovered files.
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
 * Filters and organizes logo files into a structured array.
 *
 * @param array $logos
 *   List of file paths.
 *
 * @return array
 *   Associative array indexed by logo name (without extension).
 */
function organizeContent(array $logos): array
{
    $output = array();

    foreach ($logos as $file) {
        $filename = basename($file);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, ['png'])) {
            $key = preg_replace('/\.png$/i', '', $filename);
            $output['logos'][$key] = $filename;
        }
    }

    return $output;
}

/**
 * Generates Markdown mosaic files containing logos.
 *
 * @param array $logos
 *   Structured array of logo filenames.
 * @param string $source
 *   Directory where the output file will be created.
 */
function createMDFiles(array $logos, string $source): void
{
    global $settings;

    foreach ($logos as $files) {
        $outputFile = $source . DIRECTORY_SEPARATOR . $settings['outputFilename'];
        echo "Generating $outputFile\n";

        $outputContent  = "# Logos\n\n";
        $outputContent .= "*For optimal visibility of transparent logos, enable dark mode.*\n\n";

        $table = "";
        $matrix = array();
        $i = 0;

        // Build a matrix of logo keys based on the configured number of columns.
        foreach ($files as $fileKey => $file) {
            $matrix[intdiv($i, $settings['cols'])][] = $fileKey;
            $i++;
        }

        for ($j = 0; $j < count($matrix); $j++) {

            // Image row — renders each logo inside a styled container.
            for ($i = 0; $i < $settings['cols']; $i++) {
                $logo = $matrix[$j][$i] ?? null;

                $table .= '| <div align="center" style="background:#756f6f; padding:10px; border-radius:8px;">';

                if ($logo !== null) {
                    $table .= '<img src="' . $logo . '.png" width="120">';
                } else {
                    $table .= '&nbsp;';
                }

                $table .= '</div> ';

                if ($i === $settings['cols'] - 1) {
                    $table .= "|\n";
                }
            }

            // Table header — defines column alignment (generated only once).
            if ($j === 0) {
                for ($i = 0; $i < $settings['cols']; $i++) {
                    $table .= "|:---:";
                    if ($i === $settings['cols'] - 1) {
                        $table .= "|\n";
                    }
                }
            }
        }

        $outputContent .= "$table\n";
        file_put_contents($outputFile, $outputContent);
    }
}

/**
 * Main execution function.
 * Iterates through all configured logo directories and generates mosaics.
 */
function generateAllLogosMosaics(): void
{
    global $settings;

    foreach ($settings['countriesFolders'] as $source) {
        $logos = listAllFiles($source);
        $logos = organizeContent($logos);
        createMDFiles($logos, $source);
    }
}

generateAllLogosMosaics();
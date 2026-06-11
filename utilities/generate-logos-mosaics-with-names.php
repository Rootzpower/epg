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
    'outputFilename' => '0_logos_mosaic_with_names.md',
    'cols' => 5,
);
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
function organizeContent(array $logos): array
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
    return $output;
}
function createMDFiles(array $logos, string $source): void
{
    global $settings;
    foreach ($logos as $files) {
        $outputFile = $source . DIRECTORY_SEPARATOR . $settings['outputFilename'];
        echo "Generating $outputFile\n";
        $outputContent = "# Logos with names\n\n";
        $outputContent .= "*To properly view some transparent logos, enable dark mode.*\n\n";
        $table = "";
        $matrix = array();
        $i = 0;
        foreach ($files as $fileKey => $file) {
            $matrix[intdiv($i, $settings['cols'])][] = $fileKey;
            $i++;
        }
        for ($j = 0; $j < count($matrix); $j++) {
            // Linha das imagens
            for ($i = 0; $i < $settings['cols']; $i++) {
                $logo = $matrix[$j][$i] ?? "";
                $table .= '| <div align="center">';
                if ($logo !== "") {
                    $table .= '<img src="' . $logo . '.png" width="120">';
                }
                $table .= '</div> ';
            }
            $table .= "|\n";
            // Header da tabela (só na primeira linha)
            if ($j === 0) {
                for ($i = 0; $i < $settings['cols']; $i++) {
                    $table .= "|:---:";
                }
                $table .= "|\n";
            }
            // Linha dos nomes
            for ($i = 0; $i < $settings['cols']; $i++) {
                $logo = $matrix[$j][$i] ?? "";
                $table .= '| <div align="center">' . ($logo !== "" ? $logo : '') . '</div> ';
            }
            $table .= "|\n";
        }
        $outputContent .= "$table\n";
        file_put_contents($outputFile, $outputContent);
    }
}
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
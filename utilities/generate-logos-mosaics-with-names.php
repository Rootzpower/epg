<?php
declare(strict_types=1);

/**
 * @file
 * PHP script responsible for generating all logo mosaics.
 * This script must be executed exclusively from the command-line interface (CLI).
 *
 * Usage:
 * php utilities/generate-all-logos-mosaics.php
 */

error_reporting(E_ALL);

if (PHP_SAPI !== 'cli') {
    die("This script must be run from the command line.");
}

$settings = [
    'countriesFolders' => [
        __DIR__ . '/../logos',
    ],
    'outputFilename' => '0_logos-mosaic-with-names.md',
    'cols' => 6,
];

/**
 * Recursively lists all files within a directory.
 */
function listAllFiles(string $dir): array
{
    $array = array_diff(scandir($dir), ['.', '..']);

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
 */
function organizeContent(array $logos): array
{
    $output = [];

    foreach ($logos as $file) {
        $filename = basename($file);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($ext === 'png') {
            $key = preg_replace('/\.png$/i', '', $filename);
            $output['logos'][$key] = $filename;
        }
    }

    // Garantir ordem alfabética
    if (isset($output['logos'])) {
        ksort($output['logos']);
    }

    return $output;
}

/**
 * Generates Markdown mosaic files containing logos and their names.
 */
function createMDFiles(array $logos, string $source, array $settings): void
{
    foreach ($logos as $files) {
        $outputFile = $source . DIRECTORY_SEPARATOR . $settings['outputFilename'];
        echo "Generating $outputFile\n";

        $outputContent  = "# Logos with names\n\n";
        $outputContent .= "*For optimal visibility of transparent logos, enable dark mode.*\n\n";

        $table = "";
        $matrix = [];
        $i = 0;

        // Build a matrix of logo keys based on the configured number of columns.
        foreach ($files as $fileKey => $file) {
            $matrix[intdiv($i, $settings['cols'])][] = $fileKey;
            $i++;
        }

        $rows = count($matrix);

        for ($j = 0; $j < $rows; $j++) {

            // Image row
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

            // Table header (only once)
            if ($j === 0) {
                for ($i = 0; $i < $settings['cols']; $i++) {
                    $table .= "|:---:";
                    if ($i === $settings['cols'] - 1) {
                        $table .= "|\n";
                    }
                }
            }

            // Name row (altura fixa + fonte pequena + overflow controlado)
            for ($i = 0; $i < $settings['cols']; $i++) {
                $logo = $matrix[$j][$i] ?? null;

                $table .= '| <div align="center" style="height:22px; line-height:22px; font-size:12px; overflow:hidden; white-space:nowrap;">';

                if ($logo !== null) {
                    $table .= $logo;
                } else {
                    $table .= '&nbsp;';
                }

                $table .= '</div> ';

                if ($i === $settings['cols'] - 1) {
                    $table .= "|\n";
                }
            }
        }

        $outputContent .= "$table\n";
        file_put_contents($outputFile, $outputContent);
    }
}

/**
 * Main execution function.
 */
function generateAllLogosMosaics(array $settings): void
{
    foreach ($settings['countriesFolders'] as $source) {
        $logos = listAllFiles($source);
        $logos = organizeContent($logos);

        if (empty($logos['logos'])) {
            echo "No logos found in $source\n";
            continue;
        }

        createMDFiles($logos, $source, $settings);
    }
}

generateAllLogosMosaics($settings);

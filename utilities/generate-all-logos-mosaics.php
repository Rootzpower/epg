<?php
/*
Based on the original script from the tv-logos project.
@see https://github.com/tv-logo/tv-logos
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

    return $output;
}

function createMDFiles(array $logos, string $source): void
{
    global $settings;

    foreach ($logos as $files) {
        $outputFile = $source . DIRECTORY_SEPARATOR . $settings['outputFilename'];

        echo "Generating $outputFile\n";

        $outputContent = "# Logos\n\n";

        $table = "";
        $matrix = array();
        $i = 0;

        foreach ($files as $fileKey => $file) {
            $matrix[intdiv($i, $settings['cols'])][] = $fileKey;
            $i++;
        }

        for ($j = 0; $j < count($matrix); $j++) {

            // 1️⃣ Linha das imagens
            for ($i = 0; $i < $settings['cols']; $i++) {
                $logo = $matrix[$j][$i] ?? "";

                $table .= '| <div align="center" style="background:#756f6f; padding:10px; border-radius:8px;">';

                if ($logo !== "") {
                    $table .= '<img src="' . $logo . '.png" style="height:80px; width:auto;">';
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

            // 2️⃣ Linha dos nomes
            for ($i = 0; $i < $settings['cols']; $i++) {
                $logo = $matrix[$j][$i] ?? "";
                $table .= '| <div align="center"><strong><code style="font-size:12px;">' . ($logo !== "" ? $logo : '') . '</code></strong></div> ';
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
        $logos = organizeContent($logos, $source);
        createMDFiles($logos, $source);
    }
}

generateAllLogosMosaics();
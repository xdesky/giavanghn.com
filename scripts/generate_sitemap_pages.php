<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$config = include $root . '/config/gold_sitemap.php';
$pagesRoot = $root . '/resources/views/pages';

if (!is_dir($pagesRoot)) {
    mkdir($pagesRoot, 0777, true);
}

$flatten = function (array $nodes, string $prefix = '') use (&$flatten): array {
    $result = [];

    foreach ($nodes as $slug => $meta) {
        $fullPath = $prefix === '' ? $slug : $prefix . '/' . $slug;

        $entry = [
            'path' => $fullPath,
            'title' => $meta['title'] ?? ucwords(str_replace('-', ' ', $slug)),
            'description' => $meta['description'] ?? 'Noi dung dang duoc cap nhat theo sitemap.',
            'children' => [],
        ];

        if (isset($meta['children']) && is_array($meta['children'])) {
            foreach ($meta['children'] as $childSlug => $childMeta) {
                $entry['children'][] = [
                    'path' => $fullPath . '/' . $childSlug,
                    'title' => $childMeta['title'] ?? ucwords(str_replace('-', ' ', $childSlug)),
                ];
            }
        }

        $result[$fullPath] = $entry;

        if (isset($meta['children']) && is_array($meta['children'])) {
            $result = array_merge($result, $flatten($meta['children'], $fullPath));
        }
    }

    return $result;
};

$flat = $flatten($config);

foreach ($flat as $entry) {
    $target = $pagesRoot . '/' . $entry['path'] . '.blade.php';
    $dir = dirname($target);

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $childrenExport = var_export($entry['children'], true);

    $content = "<?php\n";
    $content .= "\$title = '" . addslashes($entry['title']) . "';\n";
    $content .= "\$description = '" . addslashes($entry['description']) . "';\n";
    $content .= "\$children = " . $childrenExport . ";\n";
    $content .= "?>\n";
    $content .= "@include('gold.page-shell')\n";

    file_put_contents($target, $content);
}

echo 'Generated ' . count($flat) . " pages\n";

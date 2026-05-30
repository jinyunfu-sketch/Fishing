<?php
// 正式域名
$domain = "https://fishing.chinafriendpro.com";
// 语言目录，留空代表主目录(默认英语)
$dirs = ['', 'ar', 'de', 'es', 'fr', 'id', 'it', 'jp', 'kr', 'pt', 'ru'];

// 初始化【主索引地图】
$index = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

foreach ($dirs as $d) {
    $lang = $d ?: 'en'; // 根目录命名为 sitemap-en.xml
    $file_name = "sitemap-{$lang}.xml";
    $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // 扫描该语言目录下的html
    $files = (array)glob(__DIR__ . ($d ? "/$d" : "") . "/*.html");
    if (empty($files[0])) continue; // 没有文件则跳过

    foreach ($files as $file) {
        $loc = $domain . ($d ? "/$d/" : "/") . basename($file);
        $pri = strpos(basename($file), 'index.html') !== false ? ($d ? '0.9' : '1.0') : '0.8';
        $xml .= "<url><loc>{$loc}</loc><lastmod>".date('Y-m-d', filemtime($file))."</lastmod><priority>{$pri}</priority></url>";
    }
    $xml .= '</urlset>';
    
    // 1. 生成【单语言子地图】
    file_put_contents(__DIR__ . '/' . $file_name, $xml);
    
    // 2. 将子地图写入【主索引】
    $index .= "<sitemap><loc>{$domain}/{$file_name}</loc><lastmod>".date('Y-m-d')."</lastmod></sitemap>";
}

// 3. 生成【主索引地图】文件
file_put_contents(__DIR__ . '/sitemap.xml', $index . '</sitemapindex>');

echo "<h3>🎉 搞定！已生成以下文件：</h3>";
echo "1. 主索引：<a href='sitemap.xml' target='_blank'>sitemap.xml</a> (提交给谷歌就提交这一个即可)<br>";
echo "2. 各语言子文件：sitemap-en.xml, sitemap-ar.xml, sitemap-de.xml 等等。<br><br>";
echo "<b>下一步：把刚生成的这些 .xml 文件，全部丢到你 Cloudflare 正式站的根目录即可！</b>";
?>
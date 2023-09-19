<?php

$snips = array(
    'commerce.render_quantity_usergroup_price' => 'Extended version of the commerce.render_quantity_price snippet, that renders usergroup-specific prices.',
);

$snippets = array();
$idx = 0;

foreach ($snips as $name => $description) {
    $idx++;
    $snippets[$idx] = $modx->newObject('modSnippet');
    $snippets[$idx]->fromArray(array(
       'name' => $name,
       'description' => $description . ' (Part of Commerce_QuantityUserGroup)',
       'snippet' => getSnippetContent($sources['snippets'] . strtolower($name) . '.snippet.php')
    ));
}

return $snippets;

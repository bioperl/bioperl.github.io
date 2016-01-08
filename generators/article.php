<?php
date_default_timezone_set('Asia/Kolkata');
$now = date('Y-m-d');

echo 'This utility helps generating article file with minimal input.' . PHP_EOL . PHP_EOL .
        'Article date in which article has been published.' . PHP_EOL;
$publishYearSuggested = date('Y');
$publishYear = readline('Enter publish year (' . $publishYearSuggested . '): ');
if ($publishYear == "") {
    $publishYear = $publishYearSuggested;
}

$publishMonthSuggested = date('m');
$publishMonth = readline('Enter publish month (' . $publishMonthSuggested . '): ');
if ($publishMonth == "") {
    $publishMonth = $publishMonthSuggested;
}

$publishDateSuggested = date('d');
$publishDate = readline('Enter publish date (' . $publishDateSuggested . '): ');
if ($publishDate == "") {
    $publishDate = $publishDateSuggested;
}

$publishTime = mktime(0,0,0,$publishMonth,$publishDate,$publishYear);

echo PHP_EOL . PHP_EOL .
        'Title will be displayed on home page and monthly magazine page so must be human readable ' .
        'but also short.' . PHP_EOL;
$title = trim(readline('Human readable title: '));


echo PHP_EOL . PHP_EOL .
        'Page name, is similar to title but it will be part of URL, so capital letters ' .
        'and space are not allowed. Based on Human readable title, a suggested url name is ' .
        'given. Press enter to accept generated url anme or enter new url name.' . PHP_EOL;

$suggestedUrlName = str_replace(' ', '-', strtolower($title));
$urlName = readline('Url name (' . $suggestedUrlName . '): ');
if ($urlName == '') {
    $urlName = $suggestedUrlName;
}

$date = date('Y-m-d h:i:s', $publishTime);
$category = date('FY', $publishTime);

echo PHP_EOL . PHP_EOL;
$tags = readline('Enter tags for article, separated by comma: ');
$tagsArray = explode(',', $tags);
$formattedTags = '';
foreach ($tagsArray as $tag) {
    $formattedTags .= PHP_EOL . '  - ' . $tag;
}

echo PHP_EOL . PHP_EOL . 'Enter author datials:' . PHP_EOL;
$authorName = readline('  Enter name (Mandatory): ');
$authorTwitter = readline('  Enter author\'s twitter handle (Optional): ');
$authorGithub = readline('  Enter author\'s github account (Optional): ');
$authorUrl = readline('  Enter original url of article: ');

$author = "author:" . PHP_EOL . "    name: " . $authorName . PHP_EOL;
if (strlen($authorTwitter) > 0) {
    $author .= "    twitter: " . $authorTwitter . PHP_EOL;
}
if (strlen($authorGithub) > 0) {
    $author .= "    github: " . $authorGithub . PHP_EOL;
}
$author .= "    url: " . $authorUrl;

$delimiter = readline('Enter delimiter for description: ');
$description = '';
$first = false;

echo PHP_EOL . PHP_EOL .
        'Enter description' . PHP_EOL .
        '=====================================================' . PHP_EOL;

$line = readline(' = ');
while ($line != $delimiter) {
    if ($first) {
        $first = false;
    } else {
        $description .= PHP_EOL;
    }
    $description .= $line;
    $line = readline(' = ');
}

$page = <<< page_eol
---
layout: post
title: "$title"
date: $date
categories: $category
tags:$formattedTags
$author
---
$description
page_eol;

echo PHP_EOL . '---------' . PHP_EOL .
        'Generated page' . PHP_EOL .
        '---------' . PHP_EOL . $page . PHP_EOL . '---------' . PHP_EOL;

define('DS', DIRECTORY_SEPARATOR);
$folder = dirname(__DIR__) . DS . '_posts' . DS . 'm' . date('ym', $publishTime) . DS;
$file = date('Y-m-d', $publishTime) . '-' . $urlName . '.md';
echo 'Above contents will be written to file "' . $folder . $file .'"';
$writeConfirm = readline("Confirm (yes/no): ");
echo PHP_EOL;

if (strtolower(trim($writeConfirm)) != 'yes') {
    echo PHP_EOL . 'File write aborted by user.';
    exit();
}

//Check folder existance. If not, attempt to create it.
if (!file_exists($folder)) {
    //Check if _posts folder exist
    if (!file_exists(dirname($folder))) {
        echo '"_posts" folder do not exist. Exiting.';
        exit();
    }
    // Create folder
    $folderCreated = mkdir($folder);
    if (!$folderCreated) {
        echo 'Couldn\'t create folder - ' . $folder;
        exit;
    }
}

// Reaching here means folder exist. Write file now.
// Check if file exist
if (file_exists($folder . $file)) {
    // Ahh file already exist, ask user to overrite it.
    $overwrite = readline("File '" . $folder . $file . "' already exist. Over write it? (yes/no) : ");
    if (strtolower(trim($overwrite)) != 'yes') {
        echo PHP_EOL . "Writing aborted by user. Exiting.";
    }

    //File exist but user wish to over write. Continue write operation.
}

if (file_put_contents($folder.$file, $page)) {
    echo "File written successfully";
} else {
    echo "OOOooopsssss. Couldn't write file :(";
}

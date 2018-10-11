<?php
namespace FlouTest;
require "Flou.php";
use Flou;

$current_dir = dirname(__FILE__);

$image1 = (new Flou\Image())
            ->setBasePath("$current_dir/test/fixtures/image1.jpg");

echo "Hello\n";

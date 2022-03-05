<?php

$color1 = "#FDE02F"; # "#F2CB05";
$color2 = "#F7B209"; # "#D98E04";

function inject_svg_defs($svg) {
    global $color1, $color2;

    $defs = '<defs>
    <linearGradient id="yellow-gradient" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop style="stop-color:' . $color1 . ';stop-opacity:1;" offset="0%" />
      <stop style="stop-color:' . $color2 . ';stop-opacity:1;" offset="100%" />
    </linearGradient>
  </defs>';

    $lines = explode("\n", $svg);
    array_splice($lines, 1, 0, $defs); // insert after first line
    return join("\n", $lines);
}

function replace_color($svg, $from, $to) {
    return str_ireplace($from, $to, $svg);
}

function mod_openmoji($source) {
    $im = new Imagick();
    $im->readImageBlob($source);

    $sourceWidth = 72;

    if ($im->getImageHeight() != $sourceWidth && $im->getImageWidth() != $sourceWidth) {
        throw new Exception("input size is invalid");
    }

    $im->trimImage(0);

    // check if image can be better fitted
    for ($i = 8*3; $i >= 8; $i -= 2) {
        if ($im->getImageWidth() <= $sourceWidth-$i && $im->getImageHeight() <= $sourceWidth-$i) {

            $source = str_replace('viewBox="0 0 '.$sourceWidth.' '.$sourceWidth.'"', 'viewBox="'.($i/2).' '.($i/2).' '.($sourceWidth-$i).' '.($sourceWidth-$i).'"', $source);
            break;
        }
    }

    // inject special gradients
    $source = inject_svg_defs($source);

    // replace colors
    $source = replace_color($source, "#fcea2b", "url(#yellow-gradient)");

    // TODO replace main emoji circle

    return $source;
}

if ($argc > 1) {
    $color1 = $argv[1];
    $color2 = $argv[2];
}

print "using gradient colors $color1 - $color2\n\n";

// $selection = explode(",", "1F600,1F601,1F602,1F923,1F603,1F604,1F605,1F606,1F609,1F60A,1F60B,1F60E,1F60D,1F618,1F970,1F617,1F619,1F61A,263A,263A,1F642,1F917,1F929,1F914,1F928,1F610,1F611,1F636,1F644,1F60F,1F623,1F625,1F62E,1F910,1F62F,1F62A,1F62B,1F634,1F60C,1F61B,1F61C,1F61D,1F924,1F612,1F613,1F614,1F615,1F643,1F911,1F632,2639,2639,1F641,1F616,1F61E,1F61F,1F624,1F622,1F62D,1F626,1F627,1F628,1F629,1F92F,1F62C,1F630,1F631,1F975,1F976,1F633,1F92A,1F635,1F621,1F620,1F92C,1F637,1F912,1F915,1F922,1F92E,1F927,1F607,1F920,1F973,1F974,1F97A,1F925,1F92B,1F92D,1F9D0,1F913,1F608,1F47F,1F921,1F479,1F47A,1F480,2620,2620,1F47B,1F47D,1F47E,1F916,1F4A9,1F63A,1F638,1F639,1F63B,1F63C,1F63D,1F640,1F63F,1F63E,1F648,1F649,1F64A,1F3FB,1F3FC,1F3FD,1F3FE,1F3FF,1F476");
$files = glob("svg/*.svg");

foreach ($files as $file) {
    $input_file = $file; // "svg/".$file.".svg"; - when editing selection
    $output_file = "yellow/". basename($input_file);

    // check override
    if (file_exists("overrides/" . basename($input_file))) {
        $input_file = "overrides/" . basename($input_file);
    }

    print "improving " . $input_file . "\n";

    $source = file_get_contents($input_file);
    $mod = mod_openmoji($source);
    file_put_contents($output_file, $mod);
}

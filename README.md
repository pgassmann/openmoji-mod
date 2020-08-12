# openmoji-mod

Quick and easy customization of [OpenMoji](https://openmoji.org/) emoji pack. 
Script will by default remove excessive padding around original emojis and replace yellow color with custom one.

## Usage
PHP CLI and imagick extension is required (`apt install php-cli php-imagick`).
Place OpenMoji original images to svg directory. Then simply run:
```
php improve-openmoji.php 
```
or specify gradient color 
```
php improve-openmoji.php "#ffffff" "#dddddd"
```

## Sample output
![Preview](preview.png)

# word-count
PHP port of [iarna/word-count](https://github.com/iarna/word-count). This *should* provide an accurate wordcount taking into account apostrophes and hyphens and stuff. See iarna's project for disclaimers on what languages this method may not work well with. 

## Usage

`composer require sylae/word-count`

```php
<?php

use Sylae\Wordcount;

// optional, it'll load itself on the first count()...
Wordcount::loadLetters();

$string = "This string has five words!";

$count = Wordcount::count($string); // int(5)
```

## Contributing

I mean if you want to cool i guess <3

1. PSR-2.
2. Write tests.
3. Yeet me a PR.

Currently the biggest priority is getting `Wordcount::getLetters()` down to a nicer initial runtime. Barring that, being able to export/import a serialized string cached by the application would be great. Something like...

```php
<?php

file_put_contents("tmp/letters.json", Wordcount::dumpLetters());

// then in a later execution...
Wordcount::loadLetters(file_get_contents("tmp/letters.json") ?? null);

```

I'm also lowkey pretty sure `count()` could be improved with fancy regex magic, but I'm a dumb bitch so idk.

## Versioning

semver.

## License

MIT.

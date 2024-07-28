<?php

use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Parser;

require_once __DIR__ . '/../vendor/autoload.php';

$files = [
    __DIR__ . '/../src/Node.php',
    __DIR__ . '/../src/Node/Document.php',
    __DIR__ . '/../src/Node/Element.php',
    __DIR__ . '/../src/Node/Content.php',
    __DIR__ . '/../src/Node/Attribute.php',
    __DIR__ . '/../src/Node/CharData.php',
    __DIR__ . '/../src/Node/Misc.php',
    __DIR__ . '/../src/Node/Prolog.php',
    __DIR__ . '/../src/Node/Reference.php',
    __DIR__ . '/../src/Parser.php',
    __DIR__ . '/../src/Lexer.php',
    __DIR__ . '/../src/Token.php',
];

$parser = new Parser();

echo '# API Documentation' . PHP_EOL;

foreach ($files as $file) {
    $ast = $parser->parseSourceFile(file_get_contents($file));

    foreach ($ast->getDescendantNodes() as $descendant) {
        if ($descendant instanceof ClassDeclaration) {
            $className = $descendant->name->getText($descendant->getFileContents());
            echo "## " . $className . PHP_EOL;

            foreach ($descendant->classMembers->classMemberDeclarations as $member) {
                if ($member instanceof MethodDeclaration) {
                    if (!$member->isPublic()) {
                        continue;
                    }

                    $signature = $member->getSignatureFormatted();
                    $description = $member->getDescriptionFormatted();

                    echo "### " . $className . "::" . $member->getName() . PHP_EOL;
                    echo $description . PHP_EOL;
                    echo "```php\n$signature\n```" . PHP_EOL;
                }
            }
        }
    }
}

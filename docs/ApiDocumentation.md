# API Documentation
## Node
### Node::addToken

```php
public function addToken ( ?Token $token )
```
### Node::addTokens

```php
public function addTokens ( ...$tokens )
```
### Node::addChild

```php
public function addChild ( ?Node $child )
```
### Node::addChildren

```php
public function addChildren ( ...$children )
```
### Node::getParent

```php
public function getParent ( ) : ? Node
```
### Node::getFirstChildNode

```php
public function getFirstChildNode ( ...$types )
```
### Node::getFirstToken

```php
public function getFirstToken ( ...$kinds )
```
### Node::getChildNodesOfType

```php
public function getChildNodesOfType ( ...$types )
```
### Node::getChildTokensOfType

```php
public function getChildTokensOfType ( ...$kinds )
```
### Node::walkDescendantNodesAndTokens

```php
public function walkDescendantNodesAndTokens ( callable $callback )
```
### Node::walkDescendantNodes

```php
public function walkDescendantNodes ( callable $callback )
```
### Node::getRoot

```php
public function getRoot ( ) : Node
```
### Node::getChildNodesAndTokens

```php
public function getChildNodesAndTokens ( )
```
### Node::getChildNodes

```php
public function getChildNodes ( )
```
### Node::getChildTokens

```php
public function getChildTokens ( )
```
### Node::jsonSerialize
{@inheritDoc}
```php
public function jsonSerialize ( )
```
## Document
### Document::getProlog

```php
public function getProlog ( ) : ? Prolog
```
### Document::getRootElement

```php
public function getRootElement ( ) : ? Element
```
### Document::getMisc

```php
public function getMisc ( ) : array
```
## Element
### Element::getName

```php
public function getName ( ) : ? string
```
### Element::getAttributes

```php
public function getAttributes ( ) : array
```
### Element::getContent

```php
public function getContent ( ) : ? Content
```
## Content
### Content::getElements

```php
public function getElements ( ) : array
```
### Content::getReferences

```php
public function getReferences ( ) : array
```
### Content::getCharData

```php
public function getCharData ( ) : array
```
### Content::getCData

```php
public function getCData ( ) : array
```
### Content::getComments

```php
public function getComments ( ) : array
```
## Attribute
### Attribute::getName

```php
public function getName ( ) : ? string
```
### Attribute::getValue

```php
public function getValue ( ) : ? string
```
## CharData
### CharData::getText

```php
public function getText ( ) : ? string
```
## Misc
## Prolog
### Prolog::getAttributes

```php
public function getAttributes ( ) : array
```
## Reference
### Reference::getEntityRef

```php
public function getEntityRef ( ) : ? string
```
### Reference::getCharRef

```php
public function getCharRef ( ) : ? string
```
## Parser
### Parser::__construct

```php
public function __construct ( )
```
### Parser::parse
Parses an XML string into a Document node
```php
public function parse ( string $xml ) : Node\Document
```
## Lexer
### Lexer::__construct

```php
public function __construct ( )
```
### Lexer::tokenize
Splits raw XML string into tokens
```php
public function tokenize ( string $input )
```
## Token
### Token::__construct

```php
public function __construct ( int $kind, string $fullValue, string $value, int $offset )
```
### Token::jsonSerialize
{@inheritDoc}
```php
public function jsonSerialize ( )
```

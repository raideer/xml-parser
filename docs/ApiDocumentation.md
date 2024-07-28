# API Documentation
## Node
### Node::addToken
Adds a token to the node
```php
public function addToken ( ?Token $token )
```
### Node::addTokens
Adds multiple tokens to node
```php
public function addTokens ( ...$tokens )
```
### Node::addNode
Adds a child node
```php
public function addNode ( ?Node $child )
```
### Node::addNodes
Adds multiple child nodes
```php
public function addNodes ( ...$children )
```
### Node::getParent
Returns the parent node
```php
public function getParent ( ) : ? Node
```
### Node::getFirstChildNode
Returns the first child node of a given type
```php
public function getFirstChildNode ( ...$types )
```
### Node::getFirstToken
Returns the first token of a given kind See: TokenKind class
```php
public function getFirstToken ( ...$kinds )
```
### Node::getChildNodesOfType
Returns all child nodes of a given type
```php
public function getChildNodesOfType ( ...$types )
```
### Node::getChildTokensOfType
Returns all child tokens of a given kind See: TokenKind class
```php
public function getChildTokensOfType ( ...$kinds )
```
### Node::walkDescendantNodesAndTokens
Walks through all descendant nodes and tokens
```php
public function walkDescendantNodesAndTokens ( callable $callback )
```
### Node::walkDescendantNodes
Walks through all descendant nodes
```php
public function walkDescendantNodes ( callable $callback )
```
### Node::getRoot
Returns the root node. Will return self if node has no parent
```php
public function getRoot ( ) : Node
```
### Node::getChildNodesAndTokens
Returns all child nodes and tokens
```php
public function getChildNodesAndTokens ( )
```
### Node::getChildNodes
Returns all child nodes
```php
public function getChildNodes ( )
```
### Node::getChildTokens
Returns all child tokens
```php
public function getChildTokens ( )
```
### Node::jsonSerialize
JSON serialize node for debugging purposes {@inheritDoc}
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
public function __construct ( int $kind,
        string $fullValue,
        string $value,
        int $offset,
        int $fullOffset, )
```
### Token::jsonSerialize
JSON serialize token for debugging purposes {@inheritDoc}
```php
public function jsonSerialize ( )
```

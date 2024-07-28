# API Documentation
## Node
### Node::addChild
Adds a child node
```php
public function addChild ( ?NodeInterface $child )
```
### Node::addChildren
Adds multiple child nodes
```php
public function addChildren ( ...$children )
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
### Node::walkDescendantTokens
Walks through all descendant tokens
```php
public function walkDescendantTokens ( callable $callback )
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
### Node::getTokenAtOffset

```php
public function getTokenAtOffset ( int $offset )
```
### Node::jsonSerialize
JSON serialize node for debugging purposes
```php
public function jsonSerialize ( ) : mixed
```
## Document
### Document::getProlog
Returns the prolog node
```php
public function getProlog ( ) : ? Prolog
```
### Document::getRootElement
Returns the root element node
```php
public function getRootElement ( ) : ? Element
```
### Document::getMisc
Returns the misc node
```php
public function getMisc ( ) : array
```
## Element
### Element::getName
Returns the element name as string. Returns null if NAME token is not found
```php
public function getName ( ) : ? string
```
### Element::getAttributes
Returns all element attribute nodes
```php
public function getAttributes ( ) : array
```
### Element::getContent
Returns the content node
```php
public function getContent ( ) : ? Content
```
## Content
### Content::getElements
Returns all child element nodes
```php
public function getElements ( ) : array
```
### Content::getReferences
Returns all child reference nodes
```php
public function getReferences ( ) : array
```
### Content::getCharData
Returns all child charData nodes
```php
public function getCharData ( ) : array
```
### Content::getCData
Returns all CData strings
```php
public function getCData ( ) : array
```
### Content::getComments
Returns all comment strings
```php
public function getComments ( ) : array
```
## Attribute
### Attribute::getName
Returns the attribute name as string. Returns null if NAME token is not found
```php
public function getName ( ) : ? string
```
### Attribute::getValue
Returns the attribute value as string (without quotes) Returns null if STRING token is not found
```php
public function getValue ( ) : ? string
```
### Attribute::getFullValue
Returns the attribute value as string (with quotes) Returns null if STRING token is not found
```php
public function getFullValue ( ) : ? string
```
## CharData
### CharData::getText
Returns the text content of the node. Does not include whitesspace
```php
public function getText ( ) : ? string
```
## Misc
## Prolog
### Prolog::getAttributes
Returns all child attribute nodes
```php
public function getAttributes ( ) : array
```
## Reference
### Reference::getEntityRef
Returns the entity reference as string.
```php
public function getEntityRef ( ) : ? string
```
### Reference::getCharRef
Returns the character reference as string.
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
JSON serialize token for debugging purposes
```php
public function jsonSerialize ( ) : mixed
```

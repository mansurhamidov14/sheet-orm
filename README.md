<p align="center">
  <img src="https://github.com/user-attachments/assets/c51c3a01-8a26-4f47-9046-83404509eb95" height="100px">
</p>

# Sheetmap

**Sheetmap** is a PHP library that maps spreadsheet data (Excel, CSV, etc.) into PHP objects using PHP 8 attributes, doc annotations and flexible programmatic mappings. It aims to make converting tabular data into typed PHP models easy, testable and extensible.

---

# Example spreadsheet (visual)

| A            | B             | C           | D           | E            |
|--------------|---------------|-------------|-------------|--------------|
| **Fullname** | **Birthdate** | **isAdmin** | **Salary**  | **Password** |
| John Doe     | 1990-05-10    | TRUE        | 5500.75     | secret1      |
| Jane Smith   | 1985-08-22    | FALSE       | 7200.00     | secret2      |

---

# Quick install

```bash
composer require twelver313/sheetmap
```

---

# Usage overview

Below is a single example demonstrating annotations, custom formatters, dynamic mappings and loading. After the code block you'll find a breakdown explaining each piece.


## Defining and mapping a model with attributes (PHP8+)
```php
<?php

use Twelver313\SheetORM\Attributes\Sheet;
use Twelver313\SheetORM\Attributes\Column;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\ValueFormatter;

// Optional: describe the sheet (attribute is optional; defaults exist)
#[Sheet(name: 'UsersWorksheet', startRow: 5, endRow: 76)]
// alternatively you can reference by index:
#[Sheet(index: 2, startRow: 5, endRow: 76)]
class User
{
    #[Column(title: 'Fullname', type: ValueFormatter::TYPE_STRING)]
    protected $fullName;

    /** @var \DateTime */
    #[Column(letter: 'B', type: ValueFormatter::TYPE_DATE)]
    protected $birthDate;

    #[Column(letter: 'C', type: ValueFormatter::TYPE_BOOL)]
    protected $isAdmin;

    #[Column(title: 'Salary', type: ValueFormatter::TYPE_FLOAT)]
    protected $salary;
}

// create mapper
$spreadsheetMapper = new SpreadsheetMapper();

// load the spreadsheet into an array of model instances
$data = $spreadsheetMapper
    ->load(User::class)
    ->fromFile('path/to/your/spreadsheet.xlsx')
    ->getData();

```

## Defining and mapping a model using doc annotators
```php
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\Attributes\Sheet;
use Twelver313\SheetORM\Attributes\Column;

/**
 * @Sheet(name="UsersWorksheet", startRow=5, endRow=76)
 * or alternatively you can reference by index
 * @Sheet(index=2, startRow=5, endRow=76)
 */
class User
{
    /** @Column(title="Fullname", type="string") */
    protected $fullName;

    /** 
     * @var \DateTime
     * @Column(letter="B", type="date")
     */
    protected $birthDate;

    /** @Column(letter="C", type="bool") */
    protected $isAdmin;

    /** @Column(title="Salary", type="float") */
    protected $salary;
}

```

## Defining a model and mapping dynamically
```php
use Twelver313\SheetORM\ModelMapping;
use Twelver313\SheetORM\SheetConfig;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\ValueFormatter;

class User
{
    protected $fullName;
    /** @var \DateTime */
    protected $birthDate;
    protected $isAdmin;
    protected $salary;
}

// create mapper
$spreadsheetMapper = new SpreadsheetMapper();

// map properties dynamically
$spreadsheetMapper->map(User::class, function (ModelMapping $mapping) {
    $mapping->field('fullName')->title('Fullname')->type(ValueFormatter::TYPE_STRING);
    $mapping->field('birthDate')->column('B')->type(ValueFomatter::TYPE_DATE);
    $mapping->field('isAdmin')->column('C')->type(ValueFormatter::TYPE_BOOL);
    $mapping->field('salary')->title('Salary')->type(ValueFormatter::TYPE_FLOAT);
});

// Defining sheet config dynamically
$sheetConfig = new SheetConfig([
    'name'     => 'UsersSheet',
    'startRow' => 2,
    'endRow'   => 55,
]);

// load the spreadsheet into an array of model instances
$data = $spreadsheetMapper
    ->load(User::class)
    ->fromFile('path/to/your/spreadsheet.xlsx', $sheetConfig)
    ->getData();
```

## Defining an array schema for mapping an array
```php
use Twelver313\SheetORM\ArrayMapping;
use Twelver313\SheetORM\ArraySchema;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\ValueFormatter;

$arraySchema = new ArraySchema('boardGames', [
  'endRow' => 4
]);

$arraySchema->mapKeys(function (ArrayMapping $mapping) {
  $mapping->field('id')->title('row_id')->type(ValueFormatter::TYPE_INT);
  $mapping->field('game')->title('boardgame');
});

$spreadsheetMapper = new SpreadsheetMapper();

$data = $spreadsheetMapper
  ->loadAsArray($arraySchema)
  ->fromFile(__DIR__ . '/boardgames.csv')
  ->getData();
```

## Parsing sheet header
Sometimes you may need to parse your worksheet header to map your model dynamically from user input
```php
use Twelver313\SheetORM\SpreadsheetMapper;

class User {}

$spreadsheetMapper = new SpreadsheetMapper();
$sheetHeader = $spreadsheetMapper
    ->load(User::class)
    ->fromFile('path/to/your/spreadsheet.xls')
    ->getSheetHeader();

// Sheet header represents an array of sheet columns mapped by titles ['Fullname' => 'A', 'Email' => 'B']
```

## Defining custom value formatter by registering new type
```php
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\ValueFormatter;

class User
{
    ...

    #[Column(title: 'Password', type: 'md5')]
    $password;

    #[Column(title: 'birthDate', type: 'formattedDate')]
    $birthDate
}

$spreadsheetMapper = new SpreadsheetMapper();

// register a custom value formatter named "md5"
$spreadsheetMapper->valueFormatter->register('md5', function (Cell $cell) {
    return md5($cell->getCalculatedValue());
});
$spreadsheetMapper->valueFormatter->register('formattedDate', function (Cell $cell, ValueFormatter $formatter) {
    $initialValue = $formatter->formatDateTime($cell);
    return $initialValue?->format('d.m.Y');
})
```

## Header/template validation

### Using predefined validation strategies

#### a) With PHP8+ attributes
```php

use Twelver313\SheetORM\Attributes\SheetValidation;
use Twelver313\SheetORM\Validation\ValidateByHeaderSize;
use Twelver313\SheetORM\Validation\ValidateByHeaderTitles;

#[SheetValidation(
  strategy: ValidateByHeaderTitles::class,
  params: [
    'titles' => ['First Name', 'Last Name', 'Email'],
    'flags' => HeaderValidationFlags::IGNORE_CASE | HeaderValidationFlags::IGNORE_ORDER
  ]
)]
#[SheetValidation(strategy: ValidateByHeaderSize::class, params: ['exact' => 5])]
#[SheetValidation(
    strategy: ValidateByHeaderSize::class,
    params: ['min' => 2, 'max' => 10],
    message: 'Minimum {params.min} and maximum {params.max} required. Provided {context.headerSize}' 
)]
class Student {}
```

#### b) With doc annotators
```php

use Twelver313\SheetORM\Attributes\SheetValidation;

/**
 * @SheetValidation(
 *  strategy="Twelver313\SheetORM\Validation\ValidateByHeaderTitles",
 *  params={
 *    "titles"={"First name", "Last name", "Email"},
 *    "flags"=9
 *  }
 * )
 * @SheetValidation(strategy="Twelver313\SheetORM\Validation\ValidateByHeaderSize", params={"exact"=5})
 * @SheetValidation(
 *   strategy="Twelver313\SheetORM\Validation\ValidateByHeaderSize",
 *   params={"min"=2, "max"=10},
 *   message="Minimum {params.min} and maximum {params.max} required. Provided {context.headerSize}"
 * )
 */
class Student {}
```


### Overriding/customizing error messages with predefined validation strategies
You may need to override message logic especially if you are using PHP<8 and want to print out messages using translation methods/functions
```php

use Twelver313\SheetORM\Validation\ValidateByHeaderSize;
use Twelver313\SheetORM\Validation\SheetValidationContext;

class CustomisedValidateByHeaderSize extends ValidateByHeaderSize
{
    protected function message(array $params, SheetValidationContext $context): string
    {
        return 'Your own message depending on validation params and validation context';
    }
}
```

### Defining and providing new validation strategy
```php
use Twelver313\SheetORM\Attributes\SheetValidation;
use Twelver313\SheetORM\Validation\SheetValidationContext;
use Twelver313\SheetORM\Validation\SheetValidationStrategy;

class MyCustomValidationStrategy extends SheetValidationStrategy
{
    protected function validate(array $params, SheetValidationContext $context): bool
    {
        if ($context->getHeaderSize() != $params['expectedHeaderSize']) {
            return false;
        } else {
            return true;
        }
    }

    protected function message(array $params, SheetValidationContext $context): string
    {
        return sprintf(
            'We were expecting %s columns, you have provided %s',
            $params['expectedHeaderSize'],
            $context->getHeaderSize()
        );
    }
}

#[SheetValidation(strategy: MyCustomValidationStrategy::class, params: ['expectedHeaderSize' => 12])]
class Student {}

```

### Handling validation
#### a) Programmatic-check
```php

use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\Attributes\SheetValidation;
use MyCustomValidationStrategy;

#[SheetValidation(strategy: MyCustomValidationStrategy::class, params: ['expectedHeaderSize' => 12])]
class Student {}

$spreadsheetMapper = new SpreadsheetMapper();

// load the spreadsheet into an array of model instances
$handler = $spreadsheetMapper
    ->load(Student::class)
    ->fromFile('path/to/your/spreadsheet.xlsx');

if ($handler->isValidSheet()) {
    $data = $hander->getData();
    // Your data handling code here
} else {
    $errors = $handler->getErrors();
    // $errors will be an array of string
    // Do what ever you want with your errors
}

// Or you may want to handle it with try/catch

```

#### b) Exception-based
```php

use Twelver313\SheetORM\SpreadsheetMapper;
use Twelver313\SheetORM\Attributes\SheetValidation;
use MyCustomValidationStrategy;

#[SheetValidation(strategy: MyCustomValidationStrategy::class, params: ['expectedHeaderSize' => 12])]
class Student {}

try {
    $spreadsheetMapper = new SpreadsheetMapper();
    $data = $spreadsheetMapper
        ->load(Student::class)
        ->fromFile('path/to/your/spreadsheet.xlxs')
        ->getData();
    // Handle your $data
} catch (\Twelver313\SheetORM\Exceptions\InvalidSheetTemplateException $e) {
    // Handle your error
}
```


---

# API (quick reference)

- `SpreadsheetMapper` — main entry: register formatters, map classes, load files.
- `ValueFormatter` — register and resolve formatters (`register(name, callable)`).
- `ModelMapping` — runtime mapping builder (`property()->column()->title()->type()`).
- `SheetConfig` — runtime sheet selection and row bounds.
- Attributes: `#[Sheet]`, `#[Column]`, and optional `#[SheetValidation]`.

(See phpdoc in code for full signatures.)

---

# Contributing
PRs welcome. Please include unit tests for new behaviors (formatters, validation flags, repeating groups).

---

# License
MIT

---

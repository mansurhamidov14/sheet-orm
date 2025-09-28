# Sheetmap

**Sheetmap** is a PHP library that maps spreadsheet data (Excel, CSV, etc.) into PHP objects using PHP 8 attributes and flexible programmatic mappings. It aims to make converting tabular data into typed PHP models easy, testable and extensible.

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
# and phpoffice/phpspreadsheet is required (composer should pull it)
```

---

# Usage overview

Below is a single example demonstrating annotations, custom formatters, dynamic mappings and loading. After the code block you'll find a breakdown explaining each piece.


## Defining and mapping a model with annotators
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Twelver313\Sheetmap\Sheet;
use Twelver313\Sheetmap\Column;
use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\ValueFormatter;

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

    // custom type using a registered formatter
    #[Column(title: 'Password', type: 'md5')]
    protected $password;
}

// create mapper
$spreadsheetMapper = new SpreadsheetMapper();

// load the spreadsheet into an array of model instances
$data = $spreadsheetMapper
    ->load(User::class)
    ->fromFile('path/to/your/spreadsheet.xlsx')
    ->getData();

```

## Defining a model and mapping dynamically
```php

require_once __DIR__ . '/vendor/autoload.php';

use Twelver313\Sheetmap\ModelMapping;
use Twelver313\Sheetmap\SheetConfig;
use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\ValueFormatter;

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
    $mapping->property('fullName')->title('Fullname')->type(ValueFormatter::TYPE_STRING);
    $mapping->property('birthDate')->column('B')->type(ValueFomatter::TYPE_DATE);
    $mapping->property('isAdmin')->column('C')->type(ValueFormatter::TYPE_BOOL);
    $mapping->property('salary')->title('Salary')->type(ValueFormatter::TYPE_FLOAT);
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

## You may need to parse your worksheet header to map you model dynamically from user input
```php
use Twelver313\Sheetmap\SpreadsheetMapper;

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
use Twelver313\Sheetmap\SpreadsheetMapper;

class User
{
    ...

    #[Column(title: 'Password', type: 'md5')]
    $password;
}

$spreadsheetMapper = new SpreadsheetMapper();

// register a custom value formatter named "md5"
$spreadsheetMapper->valueFormatter->register('md5', function (Cell $cell) {
    return md5($cell->getCalculatedValue());
});
```

## Header/template validation

### Using predefined validation strategies
```php

use Twelver313\Sheetmap\Validation;
use Twelver313\Sheetmap\Validation\ValidateByHeaderSize;
use Twelver313\Sheetmap\Validation\ValidateByHeaderTitles;

// You can provide multiple validation attributes
#[Validation(
  strategy: ValidateByHeaderTitles::class,
  params: [
    'expected' => ['First Name', 'Last Name', 'Email'],
    'flags' => HeaderValidationFlags::IGNORE_CASE | HeaderValidationFlags::IGNORE_ORDER
  ]
)]
#[Validation(strategy: ValidateByHeaderSize::class, params: ['exact' => 5])]
#[Validation(
    strategy: ValidateByHeaderSize::class,
    params: ['min' => 2, 'max' => 10],
    message: 'Minimum {params.min} and maximum {params.max} required. Provided {context.headerSize}' 
)]
class Student {}
```

### Overriding/customizing error messages with predefined validation strategies
```php

use Twelver313\Sheetmap\Validation\ValidateByHeaderSize;
use Twelver313\Sheetmap\Validation\ValidationContext;

class CustomisedValidateByHeaderSize extends ValidateByHeaderSize
{
    protected function message(array $params, ValidationContext $context): string
    {
        return 'Your own message depending on validation params and validation context';
    }
}
```

### Defining and providing new validation strategy
```php
use Twelver313\Sheetmap\Validation;
use Twelver313\Sheetmap\Validation\ValidationContext;
use Twelver313\Sheetmap\Validation\ValidationStrategy;

class MyCustomValidationStrategy extends ValidationStrategy
{
    protected function validate(array $params, ValidationContext $context): bool
    {
        if ($context->getHeaderSize() != $params['expectedHeaderSize']) {
            return false;
        } else {
            return true;
        }
    }

    protected function message(array $params, ValidationContext $context): string
    {
        return sprintf(
            'We were expecting %s columns, you have provided %s',
            $params['expectedHeaderSize'],
            $context->getHeaderSize()
        );
    }
}

#[Validation(strategy: MyCustomValidationStrategy::class, params: ['expectedHeaderSize' => 12])]
class Student {}

```

### Handling validation
#### a) Programmatic-check
```php

use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\Validation;
use MyCustomValidationStrategy;

#[Validation(strategy: MyCustomValidationStrategy::class, params: ['expectedHeaderSize' => 12])]
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

use Twelver313\Sheetmap\SpreadsheetMapper;
use Twelver313\Sheetmap\Validation;
use MyCustomValidationStrategy;

#[Validation(strategy: MyCustomValidationStrategy::class, params: ['expectedHeaderSize' => 12])]
class Student {}

try {
    $spreadsheetMapper = new SpreadsheetMapper();
    $data = $spreadsheetMapper
        ->load(Student::class)
        ->fromFile('path/to/your/spreadsheet.xlxs')
        ->getData();
    // Handle your $data
} catch (\Twelver313\Sheetmap\Exceptions\InvalidSheetTemplateException $e) {
    // Handle your error
}
```


---

# API (quick reference)

- `SpreadsheetMapper` — main entry: register formatters, map classes, load files.
- `ValueFormatter` — register and resolve formatters (`register(name, callable)`).
- `ModelMapping` — runtime mapping builder (`property()->column()->title()->type()`).
- `SheetConfig` — runtime sheet selection and row bounds.
- Attributes: `#[Sheet]`, `#[Column]`, and optional `#[Validation]`.

(See phpdoc in code for full signatures.)

---

# Contributing
PRs welcome. Please include unit tests for new behaviors (formatters, validation flags, repeating groups).

---

# License
MIT

---

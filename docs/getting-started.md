# Getting Started

To start using **Sheet ORM**, install it via Composer:

```bash
composer require twelver313/sheet-orm
```

Then define your model:

```php
use Twelver313\SheetOrm\Attributes\Sheet;
use Twelver313\SheetOrm\Attributes\Column;

#[Sheet("Users")]
class User {
  #[Column("A")] public string $name;
  #[Column("B")] public string $email;
}
```

Load your sheet:

```php
$mapper = new Twelver313\SheetOrm\SpreadsheetMapper();
$users = $mapper->load("users.xlsx", User::class);
```

Now each row in the sheet maps to an instance of `User`.

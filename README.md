# PHPrimitives - Example App

A minimal Symfony API demonstrating that **you never have to touch a raw scalar again** - not in
your controllers, not in your entities, not in your DTOs. From the moment data enters your
application to the moment it leaves, every value is a typed domain primitive.

## The proof

**Input** - the request payload is deserialized directly into typed primitives. No `string $email`
anywhere:
```php
class CreateUserDto
{
    public function __construct(
        public Email $email,
    ) {}
}
```

**Transport** - the controller works exclusively with primitives. No casting, no validation, no
`filter_var`:
```php
public function __invoke(#[MapRequestPayload] CreateUserDto $data): JsonResponse
{
    $user = new User($data->email);
    $this->users->save($user);
    return new JsonResponse($user);
}
```

**Persistence** - the entity stores a primitive, not a string. Doctrine handles the conversion:
```php
#[ORM\Column(type: 'email', length: 255)]
public readonly Email $email;
```

**Output** - primitives serialize themselves. `json_encode` just works:
```php
return new JsonResponse($user); // Email serializes to its string value automatically
```

If the incoming value is invalid, `Email::construct()` throws before your code ever runs.
If it reaches your controller, it's already valid - **guaranteed by the type system**.

---

## How it works
```
POST /users  {"email": "john@example.com"}
       │
       ▼
CreateUserDto        ← PrimitiveDenormalizer calls Email::construct()
  └── Email $email   ← throws InvalidBackingValue if invalid; 422 before your code runs
       │
       ▼
User entity          ← accepts Email directly, no re-validation needed
       │
       ▼
EmailType (Doctrine) ← calls deconstruct() on write, construct() on read
       │
       ▼
PostgreSQL           ← plain VARCHAR column; primitives live at every other layer
```

## Running locally

**Prerequisites:** Docker (or a local PostgreSQL instance) and Composer.
```bash
composer install
docker compose up -d
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
cd public && php -S localhost:8080
```

## Try it
```bash
# Create a user
curl -X POST http://localhost:8080/users \
  -H "Content-Type: application/json" \
  -d '{"email": "john@example.com", "name": "John Doe"}'

# List users
curl http://localhost:8080/users
```

## Error Handling

Invalid primitive values are automatically rejected with a `422 Unprocessable Content`. No controller-level error handling is needed - `#[MapRequestPayload]` and `PrimitiveDenormalizer` handle this together.

### Example
```bash
# Invalid input - fails directly
curl -X POST http://localhost:8080/users \
  -H "Content-Type: application/json" \
  -d '{"email": "not-an-email", "name": ""}'
```
```json
{
    "type": "https://symfony.com/errors/validation",
    "title": "Validation Failed",
    "status": 422,
    "detail": "email: This value should be of type string.\nname: This value should be of type string.",
    "violations": [
        {
            "propertyPath": "email",
            "title": "This value should be of type string.",
            "template": "This value should be of type {{ type }}.",
            "parameters": {
                "{{ type }}": "string"
            },
            "hint": "Invalid email address: not-an-email"
        },
        {
            "propertyPath": "name",
            "title": "This value should be of type string.",
            "template": "This value should be of type {{ type }}.",
            "parameters": {
                "{{ type }}": "string"
            },
            "hint": "Name must be at least 5 characters long."
        }
    ]
}
```

The violation message is forwarded from the `InvalidArgumentException` thrown in your primitive's `validate()` method. No glue code required.
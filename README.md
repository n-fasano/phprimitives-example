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
  -d '{"email": "john@example.com"}'

# List users
curl http://localhost:8080/users

# Invalid input - fails directly
curl -X POST http://localhost:8080/users \
  -H "Content-Type: application/json" \
  -d '{"email": "not-an-email"}'
```

## Error handling

When an invalid value is submitted, an exception can be thrown during deserialization -
before your controller code runs. Out of the box this produces a 500, so you'll want to map it
to something more appropriate.

**Quick option** - map it to a 422 in `config/packages/framework.yaml`:
```yaml
framework:
    exceptions:
        Fasano\PHPrimitives\Exception\InvalidBackingValue: # or your custom exception class
            status_code: 422
```

**Custom response** - if you need control over the response format (JSON body, XML, a redirect,
etc.), register a `kernel.exception` listener:
```php
#[AsEventListener(event: KernelEvents::EXCEPTION)]
class InvalidBackingValueListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof InvalidBackingValue) {
            return;
        }

        $event->setResponse(/* whatever makes sense for your app */);
    }
}
```
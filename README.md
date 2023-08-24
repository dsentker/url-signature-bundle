# URL Signature Bundle
**A Symfony >=4 bundle for the [url-signature](https://github.com/dsentker/url-signature) library.**

This bundle allows you to build URLs with a signature in query string to prevent the modification of URL parts form a user. For a more detailed description, view the README from [url-signature](https://github.com/dsentker/url-signature) library .

**Features:**
* URL generation in Twig Templates
* URL generation and URL validation with a controller helper trait
* URL generation and URL validation with Dependency Injection in your controllers
* URL validation in your controller with Annotation 

## Installation
Use composer to install this bundle:
```composer
require dsentker/url-signature-bundle
```
If you use Symfony Flex, you do not have to do anything anymore. Otherwise you have to include the bundle in your `<root>/config/bundles.php` like this:

```php
<?php
return [
    // ...
    Shift\UrlSignatureBundle\ShiftUrlSignatureBundle::class => ['all' => true],
];
```

## Usage
### Create signed URLs in your Twig Template
This bundle comes with a twig extension to create an url from any route name: `signed_url()` (and, as alias, `signed_path()`) works just like the symfony / twig function `path()` which you have certainly used a hundredfold. `signed_path` expects a route name as first argument and, optionally, query data as array:
```twig
<!-- Generating a link -->
<a href="{{ path('member_detail', { id: user.id }) }}">A Link </a>

<!-- A link with a hash signature -->
<a href="{{ signed_url('member_detail', { id: user.id }) }}">A Link with a signature</a>
```
Both links lead to the same target, but the link created via `signed_url(...)` has a hash in the query string. This hash can be validated in the destination controller. 

To set an expiry date for a URL, pass the date as the 3rd parameter:
 ```html
<a href="{{ signed_url('member_detail', { id: user.id }, '+10 minutes') }}">A Link with a signature, expires in 10 minutes</a>
```
The expiration value can be 
* a relative string (parsable with [date()](http://php.net/manual/de/function.date.php) function )
* a \DateTime object
* a timestamp as integer

If the hash value is checked AFTER the expiration time, it is invalid.

### Sign URLs in your controller
Use dependency injection to get an instance of `Shift\UrlSignatureBundle\Utils\UrlSignatureBuilder`:

```php
use Shift\UrlSignatureBundle\Utils\UrlSignatureBuilder;

class ExampleController extends AbstractController
{
    /**
     * @Route("/member/detail/{id}", name="member_detail")
     */
    public function index(User $user, UrlSignatureBuilder $builder) {
        
        // Just like the Twig function, the UrlSignatureBuilder offers in the third 
        // parameter to set an expiration date.
        $hashedUrl = $builder->signUrlFromPath('example_path', ['param1' => 'value1'], '+10 minutes');
        
        // You can also create a signature for a regular URL (without referring to a route path)
        $hashedUrl = $builder->signUrl('https://example.com/foo', '+10 minutes');
        
    }
```

### Verify URLs
This bundle offers several ways to check the signature of the URL in your controller.

#### Verify a signature with dependency injection (recommended)
Inject an `Shift\UrlSignatureBundle\Utils\RequestValidator` instance to your action:

```php
use Shift\UrlSignatureBundle\Utils\RequestValidator;

class ExampleController extends AbstractController
{
    /**
     * @Route("/member/detail/{id}", name="member_detail")
     */
    public function index(User $user, RequestValidator $signatureValidator) {
        
        if(!$signatureValidator->isValid()) {
            // is Signature missing or invalid? Show an alert, redirect or do something you like    
        }

        // Alternatively, you can use this method. It throws an exception if the hash value
        // is missing or not valid.
        $signatureValidator->verify();

        // There is no need to also inject the request object to your 
        // action method as it is provided by RequestValidator instance.
        $request = $signatureValidator->getRequest();

    }
``` 

#### Verify a signature with an Annotation
Annotate your controller action like the following example:

```php
use Shift\UrlSignatureBundle\Annotation\RequiresSignatureVerification;

class ExampleController extends AbstractController
{
    /**
     * @RequiresSignatureVerification()
     *
     * @Route("/member/detail/{id}", name="member_detail")
     */
    public function index(User $user) {
        // ...
    }
}
```

If the annotation is present, an Event Listener checks the incoming request URL. If the signature is missing (or invalid), an `\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException` is thrown **before** your action is called. Make sure to give the user an useful response if an AccessDeniedException is thrown (This applies regardless of the use of this bundle, of course).

### Build hashed URLs and verify signatures with a trait
This bundle comes with a trait to make the access to the Builder and RequestValidator easier::

```php
use Shift\UrlSignatureBundle\Controller\UrlSignatureTrait;

class SingleActionController extends AbstractController
{
    
    use UrlSignatureTrait;

    /**
     * @Route("/member/detail/{id}", name="member_detail")
     */
    public function index(User $user) {

        /** @var Shift\UrlSignatureBundle\Utils\UrlSignatureBuilder $builder */
        $builder = $this->getBuilder();
        
        /** @var Shift\UrlSignatureBundle\Utils\RequestValidator $validator */
        $validator = $this->getValidator();

    }
}
```

_**Note:** The trait has its own constructor. If your controller already has a constructor, you should not use this trait. Read more [at StackOverflow about "constructor in traits"](https://stackoverflow.com/questions/12478124/how-to-overload-class-constructor-within-traits-in-php-5-4)._

## Advanced Usage

### Customize the configuration
Configuration is already done with the help of the Service Container. To create a signature, a secret is needed. By configuration, this secret is equivalent to the value of [your APP_SECRET from the .env file](https://symfony.com/doc/current/reference/configuration/framework.html#secret) in your project root.  

As you know, you can override parameters and dependencies in your `config/services.yaml`. Here is an example:
```yaml
parameters:
    shift_url_signature.hash_algo: 'MD5'
    shift_url_signature.query_signature_name: '_hash'
``` 

Look at the [service container configuration file](https://github.com/dsentker/url-signature-bundle/blob/master/Shift/UrlSignatureBundle/Resources/config/packages/shift_url_signature.yaml) in this repository to see what you want to adjust.

Here is an complete example for the configuration of the hash configuration:

```yaml
shift_url_signature.configuration.default:
        class:  UrlSignature\HashConfiguration
        shared: true
        arguments: ['%shift_url_signature.secret%']
        calls:
            -   method: setAlgorithm
                arguments:
                    - '%shift_url_signature.hash_algo%'
            -   method: setHashMask
                arguments:
                    - !php/const UrlSignature\HashConfiguration::FLAG_HASH_SCHEME
                    - !php/const UrlSignature\HashConfiguration::FLAG_HASH_HOST
                    - !php/const UrlSignature\HashConfiguration::FLAG_HASH_PORT
                    - !php/const UrlSignature\HashConfiguration::FLAG_HASH_PATH
                    - !php/const UrlSignature\HashConfiguration::FLAG_HASH_QUERY
            -   method: setSignatureUrlKey
                arguments: ['%shift_url_signature.query_signature_name%']
            -   method: setTimeoutUrlKey
                arguments: ['%shift_url_signature.query_expires_name%']
``` 
Do not be surprised at the weird looking arguments for the `setHashMask` method - I did not find a better solution to set a bitmask in a services.yaml.

## Submitting bugs and feature requests
Bugs and feature request are tracked on GitHub.

## TODO
- [ ] Create more tests. I look forward to every support.
- [ ] Restructure this bundle for the new directory structure coming with Symfony >= 5.0

## Testing
```shell
./vendor/bin/phpunit Shift/UrlSignatureBundle/Tests/
```

Or, if you use Windows:
```shell
 .\vendor\bin\phpunit.bat Shift/UrlSignatureBundle/Tests/ --configuration phpunit.xml
 ```
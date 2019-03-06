# URL Signature Bundle
**A Symfony4 bundle for the [url-signature](https://github.com/dsentker/url-signature) library.**

This bundle allows you to build urls with a signature un query string to prevent the modification of URL parts form a user. For a more detailed description, view the README from [url-signature](https://github.com/dsentker/url-signature) library .

Features:
* URL generation in .twig-Files
* URL generation and URL validation with a controller helper trait
* URL generation and URL validation with action-based Dependency Injection in your controllers
* URL validation with Annotation support 

## Installation
The best way to install this bundle is via composer in your Symfony 4 Framework:
```composer
require dsentker/url-signature-bundle
```
...which results in:
```composer log
Package operations: 1 install, 0 updates, 0 removals
  - Installing dsentker/url-signature-bundle (0.0.1): Downloading (100%)
[...]
Symfony operations: 1 recipe
  - Configuring dsentker/url-signature-bundle (>=0.0.1): From auto-generated recipe
Executing script cache:clear [OK]
Executing script assets:install public [OK]
```

As you can see, Symfony flex is able to install this bundle automatically, so there is nothing more to do for you :-)
Looking for setup instructions without Symfony Flex? Look at the bottom of this file.

## Usage
### Sign URLs in your template / twig files
This bundle comes with a twig function to create an url from any route name: `signed_url()` (and, as alias, `signed_path()`) works just like the symfony / twig function `path()` which you have certainly used a hundredfold. signed_path expects a route name and, optionally, query data as array:
```twig
<!-- Generating a regular link -->
<a href="{{ path('member_detail', { id: user.id }) }}">A Link </a>

<!-- A link with a hash signature -->
<a href="{{ signed_url('member_detail', { id: user.id }) }}">A Link with a signature</a>
```

To set an expire date for an URL, pass the date as the 3rd parameter:
 ```html
<a href="{{ signed_url('member_detail', { id: user.id }, '+10 minutes') }}">A Link with a signature, expires in 10 minutes</a>
```
The expiration value can be 
* a relative string (parsable with [date()](http://php.net/manual/de/function.date.php) function )
* a \DateTime object
* a timestamp as integer

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

#### Verify a signature with dependency injection
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

        // Will throw an Exception if the signature is missing or invalid
        $signatureValidator->verify();


        // There is no need to also inject the request object to your 
        // action method as it is provided by RequestValidator instance.
        $request = $signatureValidator->getRequest();

    }
``` 

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

_**Note:** The trait has a constructor. If your controller already has a constructor, you should not use this trait. Read more [at SO about "constructor in traits"](https://stackoverflow.com/questions/12478124/how-to-overload-class-constructor-within-traits-in-php-5-4)._

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

### Manual Installation
Download this repository, wire it with your autoloading mechanism and include the bundle in your `<root>/config/bundles.php` like this:

```php
<?php
return [
    // ...
    Shift\UrlSignatureBundle\ShiftUrlSignatureBundle::class => ['all' => true],
];
```

## Credits
Based on the ideas by [psecio](https://github.com/psecio), the project was forked by [dsentker](https://github.com/dsentker) (thats me 😁) to upgrade the code for PHP 7.x applications. The adjustments then resulted in a separate library and, additionally, in this symfony 4 bundle.

## Submitting bugs and feature requests
Bugs and feature request are tracked on GitHub.

## TODO
- [ ] Create tests. I look forward to every support. 

## Testing
- TBD.
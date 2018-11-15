#Transformer Bus


[![Build Status](https://travis-ci.org/Crell/Transformer.svg?branch=master)](https://travis-ci.org/Crell/Transformer)
[![Code Climate](https://codeclimate.com/repos/54b33edc69568018cd0078a3/badges/dad83f7a6a6f5bdcd2f3/gpa.svg)](https://codeclimate.com/repos/54b33edc69568018cd0078a3/feed)
[![Test Coverage](https://codeclimate.com/repos/54b33edc69568018cd0078a3/badges/dad83f7a6a6f5bdcd2f3/coverage.svg)](https://codeclimate.com/repos/54b33edc69568018cd0078a3/feed)

This is a simple library that simplifies building of transformation pipelines
based on typed PHP objects.

## "What does that mean?"

Consider this scenario: You have some process or set of processes, which in the
end should result in an object of some type.  However, your various processes
may output objects of a variety of different types depending on their use case.
But you want all of those to eventually end up as that final type.

What this library lets you do is create "transformers" that know how to convert
from one object to another.  You can then trivially string them together, and 
give the main transformer bus any object that it knows how to handle. It will
then produce an object of the final type.

As a concrete example, say you want to produce a Response object for your framework
(Symfony, Zend, whatever), but your controller could return a number of possible
domain objects specific to your application. Say, an object out of your model,
or an HTML representation of that domain model object, or a complete HTML page
that contains that HTML representation of the domain model.  

* Rendering a domain model object to HTML is something you know how to do, but 
you don't always want to show it as the body of its own page.
* Given an HTML fragment, you know how to render that as the body of an HTML page.
* Given an HTML page, you know how to wrap that up as a response.

Each of those is a separate task that transforms data from one representation 
to another.

Each of those steps is a *transformation*, and is carried out by a *transformer*.
A transformer is simply a PHP callable that takes an object of one type and 
returns an object of another.

## Usage

Using the example above, we could wire it up something like this.  (This example 
uses PHP 5.5 syntax but the library supports PHP 5.4.)

First we have our various domain model classes:

```php
// A product from our domain.
class Product {}

// A Customer from our domain.
class Customer {}

// An HttpResponse object from our framework.
class Response {}

// Contains an HTML string and metadata.
class HtmlBody {} 

// Contains an HTML string and metadata.
class HtmlPage {} 
```

Now we setup transformers for all of them, and wire them into a bus: 

```php
// Create a new tranformer bus, which will process until it finds a Response.
$bus = new TransformerBus(Response::class);

// Register a transformer for Product objects.
$bus->setTransformer(Product::class, function(Product $p) {
  $fragment = new HtmlBody();
  // Do some business logic here.
  return $fragment;
});

// Register a transformer for Customer objects. Note that it's totally OK
// for multiple objects to get transformed to the same type.
$bus->setTransformer(Customer::class, function(Customer $p) {
  $fragment = new HtmlBody();
  // Do some business logic here.
  return $fragment;
});

// Register a transformer for HtmlBody objects, this one as a function.
function makePage(HtmlBody $p) {
  $page = new HtmlPage();
  // Do some business logic here.
  return $page;
}
$bus->setTransformer(HtmlBody::class, 'makePage');

// Register a transformer for HtmlPage objects. Any PHP callable works.
class PageTransformer {
  public function transform(HtmlPage $h) {
    $response = new Response();
    // Do some business logic here.
    return $response;
  }
}
$t = new PageTransformer();
$bus->setTransformer(HtmlBody::class, [$t, 'transform']);
```

Now we can use that bus like so:

```php
$p = getProductFromSomewhere();
$response = $bus->transform($p);

$c = getCustomerFromSomewhere();
$response = $bus->transform($c);

$h = getHtmlBodyFromSomewhere();
$response = $bus->transform($h);

$h = getHtmlPageFromSomewhere();
$response = $bus->transform($h);

// This is effectively a no-op.
$r = getResponseFromSomewhere();
$response = $bus->transform($r);
```

In each case, only those transformers that are appropriate will be executed but
we will always reliably end up with a $response in the end.  That means that,
in a web framework, our controller can return *any* of Product, Customer, HtmlBody,
HtmlPage, or Response, and we'll reliably get a Response at the end.

There are no doubt many other use cases, but that's the one I had in mind when
writing this library originally.

## Reflective Bus

An extra implementation is available, `ReflectiveTransformerBus`. It works 
the same way as TransformerBus, but you may register transformers like so:

```php
$bus->setAutomaticTransformer(function(Product $p) {
  $fragment = new HtmlBody();
  // Do some business logic here.
  return $fragment.
});
```

And it will use reflection to register that callable for Product classes without
having to be told explicitly.  That's more convenient but has a small overhead
for the reflection process.

## Installation

The preferred method of installation is via Composer with the following command:

    composer require crell/transformer

See the [Composer documentation][2] for more details.

## See also

For users of the Symfony framework, another variant is available that will
accept Symfony "extended callables", so you can register services as transformers
and they won't be loaded until/unless used.  See <a href="https://github.com/Crell/TransformerBundle">TransformerBundle</a>.

## License

The LGPL License, version 3 or, at your option, any later version. Please see [License File](LICENSE.md) for more information.
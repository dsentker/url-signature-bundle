# CHANGELOG #
## 1.3.0
**2023-08-06**
* Updates for Symfony 6

## 1.2.0
**2022-03-24**
* Support PHP 8

## 1.1.0
**2019-12-03**
* Updates for Symfony 5
* Replacing Client with KernelBrowser in tests
* Let SignedPathExtension not extend from RoutingExtension (marked final in Symfony 5)

## 1.0.3
**2019-11-15**
This version removes the use of the Symfony\Component\HttpKernel\Event\FilterControllerEvent class which is deprecated since Symfony 4.3. Also, small fixes are done:
- Version bump symfony/framework-bundle to ^4.1.2 (CVE-2019-10909)
- Code style fixes
- Removed deprecated method in SignedPathExtension
- Fixing typos and misleading statements in README

## 1.0.2
**2019-10-28**
* Added Return type declarations
* Updated README
* Honor url-signature v1.0.9 / Version bump

## 1.0.1
**2019-09-06**
* Code cleanup
* Added test for combining route parameter with query parameters.
* Version Bump

## 1.0.0
**2019-08-01**
* Some Code style fixed (PSR)
* Removed unnecessary docblocks
* Added return type declarations for some methods
* Repository marked as "stable" after the bundle has proven itself in production websites

## 0.0.3
**2019-03-11**
* Updated to url-signature v1.0.6
* Added some tests

## 0.0.2
Created a readme file for a quick overview
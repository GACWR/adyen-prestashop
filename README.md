# Adyen Payment plugin for PrestaShop
Use Adyen's plugin for PrestaShop to offer frictionless payments online, in-app, and in-store.

## Integration
The plugin integrates card component (Secured Fields) using Adyen Checkout for all card payments. Local payment methods are integrated with Checkout Payment Methods.

### Supported payment methods
- Credit and debit cards (non 3D secure, 3D secure 1, 3D secure 2 native)
- Tokenized card payment methods (One click/Stored payment methods)
- Alipay
- Apple Pay
- Bank Transfer
- Billdesk
- Blik
- Dotpay
- Electronic Payment Standard (EPS)
- Finnish e-banking
- GCash
- Google Pay
- iDeal
- JCB
- Klarna
- MobilePay
- MOLPay online banking
- MoMo Wallet
- PayMaya E-Wallet
- Paypal
- Paysafecard
- POLi
- Qiwi
- RatePAY
- SEPA Direct Debit
- Swish
- Trustly
- TWINT
- UnionPay
- YooMoney

If a payment method of your choice is not included in the list above, you can reach out to us so we can add support for the requested method.

To learn more about enabling payment methods in your merchant account please visit our [docs page](https://docs.adyen.com/payment-methods#add-payment-methods-to-your-account).
## Requirements
This plugin supports PrestaShop version 1.6.1 or 1.7

## Contributing
We strongly encourage you to join us in contributing to this repository so everyone can benefit from:
* New features and functionality
* Resolved bug fixes and issues
* Any general improvements

Read our [**contribution guidelines**](CONTRIBUTING.md) to find out how.

## Installation and configuration
Please use the [official documentation](https://docs.adyen.com/plugins/prestashop) of the plugin 

## Deprecation strategy
Whenever a not private function or property is tagged deprecated, please be aware that in the next major release it will be permanently removed.

## Support
If you have a feature request, or spotted a bug or a technical problem, create a GitHub issue. For other questions, contact our [support team](https://support.adyen.com/hc/en-us/requests/new?ticket_form_id=360000705420).

## API Library
This module is using the Adyen APIs Library for PHP for all (API) connections to Adyen.
<a href="https://github.com/Adyen/adyen-php-api-library" target="_blank">This library can be found here</a>

## License
MIT license. For more information, see the LICENSE file.

# paymob

paymob payment gateway https://paymob.com

<p>paymob payment gateway API documentation https://docs.paymob.com/docs/accept-standard-redirect</p>

### Usage Laravel

## step 1

install package

```bash
composer require islam-alsayed/paymob
```

## step 2

add ServiceProvider in config/app.php

```php
// in providers
IslamAlsayed\PayMob\PayMobServiceProvider::class,

// in aliases
'PayMob' => IslamAlsayed\PayMob\Facades\PayMob::class,
```

## step 3

in .env file add your data
// you get it from your control panel

```bash
PAYMOB_USERNAME="Your_Username"
PAYMOB_PASSWORD="Your_Password"
PAYMOB_INTEGRATION_ID="Integration_Id"
PAYMOB_HMAC="HMAC"
```

## step 4

run command

```bash
php artisan vendor:publish --provider="IslamAlsayed\PayMob\PayMobServiceProvider"
```

## step 5

create PayMobController like this

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use IslamAlsayed\PayMob\PayMob;
use App\Traits\SendSmsAndEmail;

class PayMobController extends Controller
{
    use SendSmsAndEmail;

    public static function pay($newOrder)
    {
        $auth = PayMob::AuthenticationRequest();

        $order = PayMob::OrderRegistrationAPI([
            'auth_token' => $auth->token,
            'amount_cents' => $newOrder->total * 100,           // put your price
            'currency' => 'EGP',
            'delivery_needed' => false,                         // another option true
            'merchant_order_id' => $newOrder->id,               // put order id from your database must be unique id
            'items' => []                                       // all items information or leave it empty
        ]);

        $PaymentKey = PayMob::PaymentKeyRequest([
            'auth_token' => $auth->token,
            'amount_cents' => $newOrder->total * 100,           // put your price
            'currency' => 'EGP',                                // fake
            'order_id' => $order->id,
            "billing_data" => [                                 // put your client information
                "apartment" => "803",                           // fake
                "email" => $newOrder->customer->email,
                "floor" => "42",                                // fake
                "first_name" => $newOrder->customer->name,
                "street" => "Ethan Land",                       // fake
                "building" => "8028",                           // fake
                "phone_number" => $newOrder->customer->phone,
                "shipping_method" => "PKG",                     // fake
                "postal_code" => "01898",                       // fake
                "city" => "Jaskolskiburgh",                     // fake
                "country" => "CR",                              // fake
                "last_name" => "Nicolas",                       // fake
                "state" => "Utah"                               // fake
            ]
        ]);

        return $PaymentKey->token;
    }

    public function checkout_processed(Request $request)
    {
        $request_hmac = $request->hmac;
        $calc_hmac = PayMob::calcHMAC($request);

        if ($request_hmac == $calc_hmac) {
            $order_id = $request->obj['order']['merchant_order_id'];
            $amount_cents = $request->obj['amount_cents'];
            $transaction_id = $request->obj['id'];

            $order = Order::findOrFail($order_id);

            if ($request->obj['success'] == true && ($order->total * 100) == $amount_cents) {
                $order->update([
                    'payment_type' => 'online',
                    'payment_status' => 'paid',
                    'transaction_id' => $transaction_id
                ]);

                $this->SendSmsAndEmail($order);
            } else {
                $order->update([
                    'payment_type' => 'online',
                    'payment_status' => 'unpaid',
                    'transaction_id' => $transaction_id
                ]);
            }
        }
    }
}

```

## step 6

create OrderController like this

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $order = Order::create($request->all());
        $PaymentKey = PayMobController::pay($order);
        return view('paymob_iframe')->with(['token' => $PaymentKey]);
    }
}
```

## step 7

create View paymob.blade.php and use your iframe like this
// you get it from your control panel

```html
<iframe
    width="100%"
    height="800"
    src="https://accept.paymob.com/api/acceptance/iframes/your_iframe_id?payment_token={{$token}}"
>
</iframe>
```

## step 8

create Routes like this

in api.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayMobController;

Route::post('checkout/processed', [PayMobController::class, 'checkout_processed']);
```

in web.php

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('checkout');
});

Route::post('/checkout', [OrderController::class, 'store'])->name('checkout');

Route::get('/checkout/response', function (Request $request) {
    if ($request->success == false) {
        return view('pay_failed');
    }

    return view('pay_success');
});
```

## step 9

create View checkout.blade.php

```html
<form action="{{ route('checkout') }}" method="POST">
    @csrf
    <button>Go to Checkout</button>
</form>
```

## step 10

create View pay_failed.blade.php

```html
<body>
    <h2>Payment Failed</h2>
    <p>
        Unfortunately, your payment could not be processed. Please try again
        later.
    </p>
</body>
```

## step 11

create View pay_success.blade.php

```html
<body>
    <h2>Payment Successful!</h2>
    <p>Your payment has been successfully processed. Thank you!</p>
</body>
```

# Refund Transaction

```php
Route::post('/refund', function () {
    $auth = PayMob::AuthenticationRequest();
    return PayMob::refundTransaction(
        $auth->token,
        $transaction_id,
        $amount_cents // amount in cent 100 EGP = 100 * 100 cent
    );
});
```

# Void Transaction

```php
Route::post('/void', function () {
    $auth = PayMob::AuthenticationRequest();
    return  PayMob::voidTransaction(
        $auth->token,
        $transaction_id,
    );
});
```
If you have any problems, contact me:
eslamalsayed8133@gmail.com

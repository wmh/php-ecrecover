# php-ecrecover

This project is showing you how to sign from client-side(browser, javascript) and verify from server-side(php).

Check out this repo to see JS Signature Example: https://github.com/danfinlay/js-eth-personal-sign-examples

## eth_sign & verify

* JS Sign

```js
var msg = '0xca5e3f850877ea106134e2a2c8d7c0018a9d412b59abe623b2d1a432f0d163a8'
var from = web3.eth.accounts[0]
web3.eth.sign(from, msg, function (err, result) {
if (err) return console.error(err)
console.log('SIGNED:' + result)
})
```

* PHP Verify

```php
$hex = '0xca5e3f850877ea106134e2a2c8d7c0018a9d412b59abe623b2d1a432f0d163a8';
$signed = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';
echo ecRecover($hex, $signed), "\n";
```

## personal_sign

* JS Sign

```js
var text = 'Hello!!';
var msg = ethUtil.bufferToHex(new Buffer(text, 'utf8'))
var from = web3.eth.accounts[0]
var params = [msg, from]
var method = 'personal_sign'

web3.currentProvider.sendAsync({
    method,
    params,
    from,
}, function (err, result) {
    if (err) return console.error(err)
    if (result.error) return console.error(result.error)
    console.log('PERSONAL SIGNED:' + JSON.stringify(result.result))
});
```

* PHP Verify

```php
$msg = 'Hello!!';
$signed = '0x7b87a3c4dd63bee43d4c880391bb0aaaf210c12356406152a30edc424b9c4de62cc64a266b6faf22691a36489651f1cbf7dee1f028ba24b7d48e5552eac4c93f1b';
echo personal_ecRecover($msg, $signed), "\n";
```

## sign typed data

* JS Sign

```js
const msgParams = [
    {
        type: 'string',
        name: 'Message',
        value: 'Hi, Alice!'
    },
    {
        type: 'uint32',
        name: 'A number',
        value: '1337'
    }
]
var from = web3.eth.accounts[0]
var params = [msgParams, from]
var method = 'eth_signTypedData'

web3.currentProvider.sendAsync({
    method,
    params,
    from,
}, function (err, result) {
    if (err) return console.dir(err)
    if (result.error) {
      alert(result.error.message)
    }
    if (result.error) return console.error(result)
    console.log('PERSONAL SIGNED:' + JSON.stringify(result.result))
}
```

* PHP Verify

```php
$presha_str = hex2bin(substr(keccak256('string Messageuint32 A number'), 2) . substr(keccak256('Hi, Alice!'. pack('N', 1337)), 2));
$hex = keccak256($presha_str);
$signed = '0x5147f94643843d709bf7c374fb8d619b27da739413f7ab8de5c788a6b7d2d10e53c4789d8a0398dee6c9f6cb69e094fa801cc00fa4d19f3b71b03a7a4b7cfee11c';
echo ecRecover($hex, $signed), "\n";
```

## References

* https://thomasclowes.com/verifying-an-ethereum-signature-on-the-server-php-2/
* https://medium.com/metamask/the-new-secure-way-to-sign-data-in-your-browser-6af9dd2a1527
* https://github.com/ethereum/EIPs/pull/712
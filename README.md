# wp-cli-presets

Some personal presets I use locally when spinning up new sites to make the process a little quicker.

Works well alongside [wp-cli-valet-command](https://github.com/aaemnnosttv/wp-cli-valet-command).

## Examples

```
cd ~/Sites

wp valet new test-site

cd test-site

wp presets apply
```

Or to additionally clone WooCommerce, Subscriptions, Stripe etc.

```
cd ~/Sites

wp valet new test-site

cd test-site

wp presets apply --type=woo
```
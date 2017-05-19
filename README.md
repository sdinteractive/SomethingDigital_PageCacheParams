# SomethingDigital_PageCacheParams

This module prevents Magento EE's FPC from seeing certain parameters in the request.

It does this by removing the parameters early on in request processing.  This is meant to be used only with parameters used for JavaScript tracking, such as Google Analytics and AdWords parameters.

[This script](https://gist.github.com/mpchadwick/6c2313eda1ec6d42c8b97ed70fc5a55f) may be useful for helping you identify which parameters to blacklist.

## Changing the ignored parameters

Add a new config file in `app/etc/`.  It cannot go in a module subdirectory. This new config in `app/etc/` will work in addition to the default config file provided.

```xml
<?xml version="1.0"?>
<config>
    <global>
        <sd_pagecacheparams>
            <blacklist>
                <!-- Use ignore with any value to remove a node. -->
                <utm_source ignore="true" />
                <!-- Add additional nodes. -->
                <tracking_parameter_one />
            </blacklist>
        </sd_pagecacheparams>
    </global>
</config>
```

Please note: files under `app/etc/` are processed in alphabetical order.

##Exclude route from parameter removal process

In a new config file found in `app/etc/`. You may add an optional `<exclude_list>` that can be used to exempt or exclude a path/URI from the PageCacheParam process. Any URI that contains the string in the `path` attribute will be excluded from the PageCacheParams processor.
Additionally default exclusions can be overwritten using the `ignore` attribute.

```xml
<?xml version="1.0"?>
<config>
    <global>
        <sd_pagecacheparams>
        ...
            <exclude_list>
                <!-- The name "sd_exclude_cart_page" is in this case arbitary -->
                <!-- The "route" attribute will be used to ignore URLs by PageCacheParams -->
                <sd_exclude_cart_page path="/checkout/cart" />
                <!-- Use ignore with any value to remove a node. -->
                <listrak_remarketing ignore="true" />
            </exclude_list>
        ...
        </sd_pagecacheparams>
    </global>
<config>
```

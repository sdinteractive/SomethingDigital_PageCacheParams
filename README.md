# SomethingDigital_PageCacheParams

This module prevents Magento EE's FPC from seeing certain parameters in the request.

It does this by removing the parameters early on in request processing.  This is meant to be used only with parameters used for JavaScript tracking, such as Google Analytics and AdWords parameters.

[This script](https://gist.github.com/mpchadwick/6c2313eda1ec6d42c8b97ed70fc5a55f) may be useful for helping you identify which parameters to blacklist.

## Changing the ignored parameters

Add a new config file in `app/etc/`.  It cannot go in a module subdirectory.

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

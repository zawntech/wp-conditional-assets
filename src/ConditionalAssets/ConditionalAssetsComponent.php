<?php
namespace ConditionalAssets\ConditionalAssets;

class ConditionalAssetsComponent
{
    public function __construct() {
        new ConditionalAssetPostType;
        new ConditionalAssetOptionsMetaBox;
        // new ConditionalAssetPostTypeListTableFilter;
        new CssOptionsMetaBox;
        new JsOptionsMetaBox;
    }
}
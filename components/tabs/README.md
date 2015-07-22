# Tabs

## Markup structure

    <div class="ev-tabs ev-component">
        <ul class="ev-tabs-nav ev-vertical ev-align-$alignment" role="tablist">
            <li><a id="ev-tab-1" role="tab" aria-controls="ev-tab-panel-1" class="ev-tab-trigger" href="#"></a></li>
            <li><a id="ev-tab-2" role="tab" aria-controls="ev-tab-panel-2" class="ev-tab-trigger" href="#"></a></li>
            <li><a id="ev-tab-3" role="tab" aria-controls="ev-tab-panel-3" class="ev-tab-trigger" href="#"></a></li>
        </ul>
        
        <div class="ev-tab-container">
            <div aria-labelledby="ev-tab-1" id="ev-tab-panel-1" class="ev-tab" role="tabpanel"></div>
            <div aria-labelledby="ev-tab-2" id="ev-tab-panel-2" class="ev-tab"></div>
            <div aria-labelledby="ev-tab-3" id="ev-tab-panel-3" class="ev-tab"></div>
        </div>
    </div>

## Notes

* A `ev-active` class is applied to tab triggers as well as tab panels in order to trigger their appearance.
* The `ev-align-$alignment` CSS class is applied to the tabs navigation in order to tweak its appearance. Possible value for the alignment CSS class are:
    - `ev-align-left`
    - `ev-align-right`
    - `ev-align-center`
* The `ev-vertical` CSS class is applied to the tabs navigation in order to tweak the component's appearance.